<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Lab;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Auth;
use App\Models\ActivityLog;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index()
    {
        $users = User::latest()->paginate(10);
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $roles = ['Admin', 'Custodian/Technician'];
        return view('users.create', compact('roles'));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'string', 'in:Admin,Custodian/Technician'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        log_activity(
            'CREATED', 
            $user, 
            "Created new user: {$user->name} ({$user->role})" 
        );

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        $roles = ['Admin', 'Custodian/Technician'];

        $labs = Lab::orderBy('lab_name')->get(); 
    
        $assignedLabIds = $user->labs()->pluck('labs.id')->toArray();

        return view('users.edit', compact('user', 'roles', 'labs', 'assignedLabIds'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username,' . $user->id],
            'role' => ['required', 'string', 'in:Admin,Custodian/Technician'],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'labs' => 'nullable|array', 
            'labs.*' => 'exists:labs,id', 
        ]);

        $user->name = $request->name;
        $user->username = $request->username;
        $user->role = $request->role;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        
        $user->save();

        if ($user->role === 'Custodian/Technician') {
            $user->labs()->sync($request->input('labs', []));
        } else {
            $user->labs()->detach();
        }

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        // Prevent admin from deleting themselves
        if ($user->id == Auth::id()) {
            return redirect()->route('users.index')->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }

    public function activity(User $user)
    {
        // Fetch all activity logs for this specific user, newest first.
        // We'll paginate the results to handle users with long histories.
        $activities = ActivityLog::where('user_id', $user->id)
                                ->latest()
                                ->paginate(25);

        return view('users.activity', compact('user', 'activities'));
    }

    /**
     * Display the specified user. We redirect to the edit page instead.
     */
    public function show(User $user)
    {
        return redirect()->route('users.edit', $user->id);
    }
}