<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Module;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::withCount('users')->with('modules')->get();
        return view('roles.index', compact('roles'));
    }

    public function create()
    {
        $modules = Module::orderBy('order')->get();
        return view('roles.create', compact('modules'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'modules' => 'nullable|array',
            'modules.*' => 'exists:modules,id',
        ]);

        $role = Role::create(['name' => $validated['name']]);

        if (!empty($validated['modules'])) {
            $role->modules()->sync($validated['modules']);
        }

        return redirect()->route('roles.index')->with('success', 'Role created successfully.');
    }

    public function edit(Role $role)
    {
        $modules = Module::orderBy('order')->get();
        $assignedModuleIds = $role->modules->pluck('id')->toArray();
        return view('roles.edit', compact('role', 'modules', 'assignedModuleIds'));
    }

    public function update(Request $request, Role $role)
    {
        if ($role->is_admin) {
            return redirect()->route('roles.index')->with('error', 'Cannot modify the Admin role.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', \Illuminate\Validation\Rule::unique('roles')->ignore($role->id)],
            'modules' => 'nullable|array',
            'modules.*' => 'exists:modules,id',
        ]);

        $role->update(['name' => $validated['name']]);
        $role->modules()->sync($validated['modules'] ?? []);

        return redirect()->route('roles.index')->with('success', 'Role updated successfully.');
    }

    public function destroy(Role $role)
    {
        if ($role->is_admin) {
            return redirect()->route('roles.index')->with('error', 'Cannot delete the Admin role.');
        }

        if ($role->users()->count() > 0) {
            return redirect()->route('roles.index')->with('error', 'Cannot delete a role with assigned users. Reassign users first.');
        }

        $role->modules()->detach();
        $role->delete();

        return redirect()->route('roles.index')->with('success', 'Role deleted successfully.');
    }
}
