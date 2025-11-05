<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Roles;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AdminRoleManagementController extends Controller
{
    /**
     * Get all roles with optional filtering.
     *
     * GET /api/admin/roles
     */
    public function index(Request $request)
    {
        $query = Roles::withCount('users');

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by is_admin
        if ($request->has('is_admin')) {
            $query->where('is_admin', filter_var($request->is_admin, FILTER_VALIDATE_BOOLEAN));
        }

        // Search by name or title
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%");
            });
        }

        // Sort
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $perPage = $request->input('per_page', 15);
        $roles = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $roles,
        ]);
    }

    /**
     * Get a single role by ID.
     *
     * GET /api/admin/roles/{id}
     */
    public function show($id)
    {
        $role = Roles::withCount('users')->findOrFail($id);

        // Get sample users with this role
        $sampleUsers = User::where('role_id', $id)
            ->select('id', 'name', 'email', 'type')
            ->limit(5)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'role' => $role,
                'sample_users' => $sampleUsers,
            ],
        ]);
    }

    /**
     * Create a new role.
     *
     * POST /api/admin/roles
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:roles,name',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'permissions' => 'required|array',
            'permissions.*' => 'string',
            'status' => 'nullable|integer|in:0,1',
            'is_admin' => 'nullable|boolean',
            'is_default' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        // Validate permissions against available permissions
        $availablePermissions = $this->getPermissionsList();
        $invalidPermissions = array_diff($request->permissions, $availablePermissions);

        if (!empty($invalidPermissions)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid permissions detected.',
                'invalid_permissions' => $invalidPermissions,
            ], 422);
        }

        DB::beginTransaction();

        try {
            // If this role is set as default, unset all other defaults
            if ($request->input('is_default', false)) {
                Roles::where('is_default', true)->update(['is_default' => false]);
            }

            $role = Roles::create([
                'name' => $request->name,
                'title' => $request->title,
                'description' => $request->description,
                'permissions' => $request->permissions,
                'status' => $request->input('status', 1),
                'is_admin' => $request->input('is_admin', false),
                'is_default' => $request->input('is_default', false),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Role created successfully.',
                'data' => $role,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to create role.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update an existing role.
     *
     * PUT /api/admin/roles/{id}
     */
    public function update(Request $request, $id)
    {
        $role = Roles::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:255|unique:roles,name,' . $id,
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string',
            'status' => 'nullable|integer|in:0,1',
            'is_admin' => 'nullable|boolean',
            'is_default' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        // Validate permissions if provided
        if ($request->has('permissions')) {
            $availablePermissions = $this->getPermissionsList();
            $invalidPermissions = array_diff($request->permissions, $availablePermissions);

            if (!empty($invalidPermissions)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid permissions detected.',
                    'invalid_permissions' => $invalidPermissions,
                ], 422);
            }
        }

        DB::beginTransaction();

        try {
            // If this role is set as default, unset all other defaults
            if ($request->has('is_default') && $request->is_default) {
                Roles::where('is_default', true)
                    ->where('id', '!=', $id)
                    ->update(['is_default' => false]);
            }

            $updateData = $request->only([
                'name', 'title', 'description', 'permissions',
                'status', 'is_admin', 'is_default'
            ]);

            $role->update($updateData);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Role updated successfully.',
                'data' => $role->fresh(),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to update role.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete a role.
     *
     * DELETE /api/admin/roles/{id}
     */
    public function destroy($id)
    {
        $role = Roles::findOrFail($id);

        // Prevent deleting role if users are assigned
        $userCount = $role->users()->count();
        if ($userCount > 0) {
            return response()->json([
                'success' => false,
                'message' => "Cannot delete role. {$userCount} user(s) are assigned to this role.",
                'user_count' => $userCount,
            ], 400);
        }

        // Prevent deleting default role
        if ($role->is_default) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete the default role.',
            ], 400);
        }

        $role->delete();

        return response()->json([
            'success' => true,
            'message' => 'Role deleted successfully.',
        ]);
    }

    /**
     * Clone an existing role.
     *
     * POST /api/admin/roles/{id}/clone
     */
    public function clone(Request $request, $id)
    {
        $originalRole = Roles::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:roles,name',
            'title' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        DB::beginTransaction();

        try {
            $newRole = Roles::create([
                'name' => $request->name,
                'title' => $request->input('title', $originalRole->title . ' (Copy)'),
                'description' => $originalRole->description,
                'permissions' => $originalRole->permissions,
                'status' => $originalRole->status,
                'is_admin' => $originalRole->is_admin,
                'is_default' => false, // Never clone as default
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Role cloned successfully.',
                'data' => $newRole,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to clone role.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get available permissions.
     *
     * GET /api/admin/permissions/available
     */
    public function getAvailablePermissions()
    {
        $categories = config('permissions.categories');
        $allPermissions = $this->getPermissionsList();

        return response()->json([
            'success' => true,
            'data' => [
                'all_permissions' => $allPermissions,
                'categories' => $categories,
            ],
        ]);
    }

    /**
     * Get the complete list of available permissions.
     *
     * @return array
     */
    private function getPermissionsList()
    {
        $permissions = [];
        $categories = config('permissions.categories');

        foreach ($categories as $category) {
            foreach ($category['permissions'] as $key => $permission) {
                $permissions[] = $key;
            }
        }

        return $permissions;
    }
}
