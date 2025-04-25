<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = User::whereHas('roles', function($query) {
            $query->whereIn('name', ['employee', 'new_hire']);
        })->with('roles', 'department')->paginate(10);

        return view('admin.employees.index', compact('employees'));
    }

    public function create()
    {
        $roles = Role::whereIn('name', ['employee', 'new_hire'])->get();
        $departments = Department::all();

        return view('admin.employees.create', compact('roles', 'departments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'department_id' => 'nullable|exists:departments,id',
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,id',
            'position' => 'nullable|string|max:255',
            'hire_date' => 'nullable|date',
            'photo' => 'nullable|image|max:1024',
            'status' => 'required|in:active,pending,blocked',
        ]);

        $user = new User();
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->password = Hash::make($validated['password']);
        $user->department_id = $validated['department_id'] ?? null;
        $user->position = $validated['position'] ?? null;
        $user->hire_date = $validated['hire_date'] ?? null;
        $user->status = $validated['status'];

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('profile-photos', 'public');
            $user->photo = $path;
        }

        $user->save();

        // Assign roles
        $user->roles()->attach($request->roles);

        return redirect()->route('admin.employees.index')
            ->with('success', 'Dipendente creato con successo!');
    }

    public function show(User $employee)
    {
        $employee->load(['roles', 'department', 'programs', 'courses', 'checklistItems']);

        // Get progress statistics
        $coursesCompleted = $employee->courses()->wherePivot('status', 'completed')->count();
        $totalAssignedCourses = $employee->courses()->count();

        $checklistsCompleted = $employee->checklistItems()->wherePivot('is_completed', 1)->count();
        $totalChecklistItems = $employee->checklistItems()->count();

        // Calculate overall progress percentage
        $overallPercentage = 0;
        if (($totalAssignedCourses + $totalChecklistItems) > 0) {
            $overallPercentage = round((($coursesCompleted + $checklistsCompleted) / ($totalAssignedCourses + $totalChecklistItems)) * 100);
        }

        return view('admin.employees.show', compact(
            'employee',
            'coursesCompleted',
            'totalAssignedCourses',
            'checklistsCompleted',
            'totalChecklistItems',
            'overallPercentage'
        ));
    }

    public function edit(User $employee)
    {
        $roles = Role::whereIn('name', ['employee', 'new_hire'])->get();
        $departments = Department::all();

        $employee->load('roles');

        return view('admin.employees.edit', compact('employee', 'roles', 'departments'));
    }

    public function update(Request $request, User $employee)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $employee->id,
            'password' => 'nullable|string|min:8|confirmed',
            'department_id' => 'nullable|exists:departments,id',
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,id',
            'position' => 'nullable|string|max:255',
            'hire_date' => 'nullable|date',
            'photo' => 'nullable|image|max:1024',
            'status' => 'required|in:active,pending,blocked',
        ]);

        $employee->name = $validated['name'];
        $employee->email = $validated['email'];

        if (!empty($validated['password'])) {
            $employee->password = Hash::make($validated['password']);
        }

        $employee->department_id = $validated['department_id'] ?? null;
        $employee->position = $validated['position'] ?? null;
        $employee->hire_date = $validated['hire_date'] ?? null;
        $employee->status = $validated['status'];

        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($employee->photo) {
                Storage::disk('public')->delete($employee->photo);
            }

            $path = $request->file('photo')->store('profile-photos', 'public');
            $employee->photo = $path;
        }

        $employee->save();

        // Sync roles
        $employee->roles()->sync($request->roles);

        return redirect()->route('admin.employees.index')
            ->with('success', 'Dipendente aggiornato con successo!');
    }

    public function destroy(User $employee)
    {
        // Delete profile photo if exists
        if ($employee->photo) {
            Storage::disk('public')->delete($employee->photo);
        }

        $employee->delete();

        return redirect()->route('admin.employees.index')
            ->with('success', 'Dipendente eliminato con successo!');
    }
}
