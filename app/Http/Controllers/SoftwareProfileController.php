<?php
namespace App\Http\Controllers;
use App\Models\SoftwareProfile;
use App\Models\SoftwareItem;
use Illuminate\Http\Request;

class SoftwareProfileController extends Controller
{
    public function index()
    {
        $profiles = SoftwareProfile::withCount('softwareItems')->latest()->paginate(10);
        return view('software-profiles.index', compact('profiles'));
    }

    public function create()
    {
        $softwareItems = SoftwareItem::orderBy('name')->get();
        return view('software-profiles.create', compact('softwareItems'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:software_profiles',
            'description' => 'nullable|string',
            'software' => 'required|array', // Ensures the software checklist is submitted
            'software.*' => 'exists:software_items,id', // Ensures every ID is a valid software item
        ]);

        $profile = SoftwareProfile::create($request->only('name', 'description'));
        $profile->softwareItems()->attach($request->software); // Attach the selected software

        return redirect()->route('software-profiles.index')->with('success', 'Software Profile created successfully.');
    }

    public function edit(SoftwareProfile $softwareProfile)
    {
        $softwareItems = SoftwareItem::orderBy('name')->get();
        // Get the IDs of the software already in this profile for pre-checking the boxes
        $assignedSoftwareIds = $softwareProfile->softwareItems()->pluck('software_items.id')->toArray();
        
        return view('software-profiles.edit', compact('softwareProfile', 'softwareItems', 'assignedSoftwareIds'));
    }

    public function update(Request $request, SoftwareProfile $softwareProfile)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:software_profiles,name,' . $softwareProfile->id,
            'description' => 'nullable|string',
            'software' => 'required|array',
        ]);

        $softwareProfile->update($request->only('name', 'description'));
        // Sync is the perfect method for updating a many-to-many relationship
        $softwareProfile->softwareItems()->sync($request->software);

        return redirect()->route('software-profiles.index')->with('success', 'Software Profile updated successfully.');
    }

    public function destroy(SoftwareProfile $softwareProfile)
    {
        $softwareProfile->delete();
        return redirect()->route('software-profiles.index')->with('success', 'Software Profile deleted successfully.');
    }
}