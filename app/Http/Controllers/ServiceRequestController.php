<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ServiceRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\ActivityLog;

class ServiceRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = ServiceRequest::with('requester', 'technician')->latest();

        if (Auth::user()->role == 'Custodian') {
            $query->where('requester_id', Auth::id());
        }

        $serviceRequests = $query->paginate(15);
        return view('service-requests.index', compact('serviceRequests'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $labsQuery = \App\Models\Lab::query();
        if (Auth::user()->role !== 'Admin') {
            $labsQuery->whereHas('users', fn($q) => $q->where('user_id', Auth::id()));
        }
        $labs = $labsQuery->get();
        
        return view('service-requests.create', compact('labs'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'requesting_office' => 'required|string|max:255',
            'request_type' => 'required|in:Procurement,Repair,Condemnation,Other',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'equipment_details' => 'nullable|string',
        ]);

        // Get all the validated data from the request
        $data = $request->all();
        // Manually add the data the system should control
        $data['requester_id'] = Auth::id();
        $data['status'] = 'Submitted'; // <-- Set the default status automatically

        // Create the request
        $serviceRequest = ServiceRequest::create($data);

        // Log this activity
        log_activity(
            'REQUEST_SUBMITTED',
            $serviceRequest,
            "Submitted request '{$serviceRequest->title}' for {$serviceRequest->requesting_office}."
        );

        return redirect()->route('service-requests.index')->with('success', 'Service Request submitted successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ServiceRequest $serviceRequest)
    {
        // Get all users who are Technicians or Admins to populate the "Assign To" dropdown
        $technicians = User::whereIn('role', ['Admin', 'Custodian/Technician'])->orderBy('name')->get();

        // Get the full history for this specific service request
        $history = ActivityLog::where('subject_type', get_class($serviceRequest))
                            ->where('subject_id', $serviceRequest->id)
                            ->with('user')
                            ->latest()
                            ->get();

        return view('service-requests.show', compact('serviceRequest', 'technicians', 'history'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ServiceRequest $serviceRequest)
    {
        $request->validate([
            'technician_id' => 'nullable|exists:users,id',
            // Make sure all possible statuses are in this list
            'status' => 'required|in:Submitted,In Review,In Progress,Completed,On Hold,Rejected',
            'classification' => 'required|in:Simple,Complex,Unclassified',
            'action_taken' => 'nullable|string',
            'recommendation' => 'nullable|string',
            // This is the key validation rule
            'rejection_reason' => 'required_if:status,Rejected|nullable|string',
        ]);

        $originalStatus = $serviceRequest->status;
        $newStatus = $request->status;

        // Use fill() to stage the changes
        $serviceRequest->fill($request->all());

        // Use a variable for the log message
        $logMessage = "Updated details for request '{$serviceRequest->title}'.";

        if ($newStatus !== $originalStatus) {
            $logMessage = "Changed status from '{$originalStatus}' to '{$newStatus}'.";
            if ($newStatus == 'In Progress' && is_null($serviceRequest->started_at)) {
                $serviceRequest->started_at = now();
                $logMessage = "Started work on request '{$serviceRequest->title}'.";
            }
            if ($newStatus == 'Rejected') {
                $logMessage = "Request '{$serviceRequest->title}' was rejected. Reason: " . $request->rejection_reason;
            }
            if ($newStatus == 'Completed') {
                $serviceRequest->completed_at = now();
                $logMessage = "Technician marked work as complete for request '{$serviceRequest->title}'.";
            }
        }
        
        // Save all staged changes
        $serviceRequest->save();
        
        // Create a single, definitive log entry
        log_activity('UPDATED', $serviceRequest, $logMessage);

        return redirect()->route('service-requests.show', $serviceRequest->id)
                        ->with('success', 'Service Request updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function complete(Request $request, ServiceRequest $serviceRequest)
    {
        // Only the original requester (Custodian) or an Admin can complete it
        if (Auth::id() !== $serviceRequest->requester_id && Auth::user()->role !== 'Admin') {
            abort(403, 'UNAUTHORIZED ACTION');
        }

        $request->validate([
            'status_after_service' => 'required|string|max:255',
            'client_verifier_name' => 'required|string|max:255',
        ]);

        // Update the record with the final details
        $serviceRequest->update([
            'status' => 'Completed',
            'status_after_service' => $request->status_after_service,
            'client_verifier_name' => $request->client_verifier_name,
            'completed_at' => now(),
        ]);

        // Log the two final activities
        log_activity(
            'CLIENT_VERIFIED',
            $serviceRequest,
            "Service verified by client: {$request->client_verifier_name}."
        );
        log_activity(
            'REQUEST_COMPLETED',
            $serviceRequest,
            "Request '{$serviceRequest->title}' was completed."
        );

        return redirect()->route('service-requests.show', $serviceRequest->id)
                        ->with('success', 'Service request has been successfully verified and closed.');
    }

    public function verify(Request $request, ServiceRequest $serviceRequest)
    {
        // Only the original requester (Custodian) or an Admin can verify.
        if (Auth::id() !== $serviceRequest->requester_id && Auth::user()->role !== 'Admin') {
            abort(403, 'UNAUTHORIZED ACTION');
        }

        // The technician must have marked their work as 'Completed' first.
        if ($serviceRequest->status !== 'Completed') {
            return back()->with('error', 'This request must be marked as Completed by a technician before it can be verified.');
        }

        $request->validate([
            'status_after_service' => 'required|string|max:255',
            'client_verifier_name' => 'required|string|max:255',
        ]);

        // Update the verification fields. The status remains 'Completed'.
        $serviceRequest->update([
            'status_after_service' => $request->status_after_service,
            'client_verifier_name' => $request->client_verifier_name,
        ]);

        log_activity('CLIENT_VERIFIED', $serviceRequest, "Service verified by client: {$request->client_verifier_name}.");

        return redirect()->route('service-requests.show', $serviceRequest->id)
                        ->with('success', 'Service Request has been successfully verified.');
    }
}
