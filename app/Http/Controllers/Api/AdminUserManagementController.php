<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Roles;
use App\Models\UserPreferences;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class AdminUserManagementController extends Controller
{
    /**
     * Get all users with filtering and pagination.
     *
     * GET /api/admin/users
     */
    public function index(Request $request)
    {
        $query = User::with(['role', 'preferences.tier']);

        // Filter by type
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        // Filter by verification status
        if ($request->has('is_verified')) {
            $query->where('is_verified', filter_var($request->is_verified, FILTER_VALIDATE_BOOLEAN));
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by role
        if ($request->has('role_id')) {
            $query->where('role_id', $request->role_id);
        }

        // Search by name or email
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%");
            });
        }

        // Sort
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $perPage = $request->input('per_page', 15);
        $users = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $users,
        ]);
    }

    /**
     * Get a single user by ID.
     *
     * GET /api/admin/users/{id}
     */
    public function show($id)
    {
        $user = User::with(['role', 'preferences.tier', 'assignedLeads'])
            ->findOrFail($id);

        // Get user statistics
        $stats = [
            'total_leads' => $user->assignedLeads()->count(),
            'viewed_leads' => $user->assignedLeads()->whereNotNull('viewed_at')->count(),
            'responded_leads' => $user->assignedLeads()->where('status', 'responded')->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'user' => $user,
                'statistics' => $stats,
            ],
        ]);
    }

    /**
     * Create a new user.
     *
     * POST /api/admin/users
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => ['required', Rule::in(['admin', 'pm', 'owner', 'AccountManager'])],
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'mobile' => 'nullable|string|max:20',
            'company_name' => 'nullable|string|max:255',
            'role_id' => 'nullable|exists:roles,id',
            'status' => 'nullable|integer|in:0,1,2,3',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        DB::beginTransaction();

        try {
            $user = User::create([
                'type' => $request->type,
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'mobile' => $request->mobile,
                'company_name' => $request->company_name,
                'role_id' => $request->role_id,
                'status' => $request->input('status', 1),
                'is_verified' => false,
            ]);

            // If creating a PM, create default preferences
            if ($request->type === User::TYPE_PM) {
                UserPreferences::create([
                    'user_id' => $user->id,
                    'tier_id' => 1, // Default to free tier
                    'is_active' => true,
                    'email_notifications' => true,
                    'sms_notifications' => false,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'User created successfully.',
                'data' => $user->load('role'),
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to create user.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update an existing user.
     *
     * PUT /api/admin/users/{id}
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'type' => ['nullable', Rule::in(['admin', 'pm', 'owner', 'AccountManager'])],
            'name' => 'nullable|string|max:255',
            'email' => ['nullable', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8',
            'mobile' => 'nullable|string|max:20',
            'company_name' => 'nullable|string|max:255',
            'role_id' => 'nullable|exists:roles,id',
            'status' => 'nullable|integer|in:0,1,2,3',
            'is_verified' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        DB::beginTransaction();

        try {
            $updateData = $request->only([
                'type', 'name', 'email', 'mobile', 'company_name',
                'role_id', 'status', 'is_verified'
            ]);

            // Only update password if provided
            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            // Update verified_at timestamp if verification status changes
            if ($request->has('is_verified') && $request->is_verified && !$user->is_verified) {
                $updateData['verified_at'] = now();
            }

            $user->update($updateData);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'User updated successfully.',
                'data' => $user->load('role'),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to update user.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete a user.
     *
     * DELETE /api/admin/users/{id}
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Prevent deleting yourself
        if ($user->id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot delete your own account.',
            ], 403);
        }

        // Soft delete (set status to 3)
        $user->update(['status' => 3, 'is_delete' => 1]);

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully.',
        ]);
    }

    /**
     * Verify/Approve a property manager.
     *
     * POST /api/admin/users/{id}/verify
     */
    public function verify(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'is_verified' => 'required|boolean',
            'verification_notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $user->update([
            'is_verified' => $request->is_verified,
            'verified_at' => $request->is_verified ? now() : null,
        ]);

        return response()->json([
            'success' => true,
            'message' => $request->is_verified
                ? 'User verified successfully.'
                : 'User verification revoked.',
            'data' => $user,
        ]);
    }

    /**
     * Bulk update user statuses.
     *
     * POST /api/admin/users/bulk-update
     */
    public function bulkUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'action' => ['required', Rule::in(['activate', 'deactivate', 'verify', 'unverify', 'delete'])],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $userIds = $request->user_ids;
        $action = $request->action;

        // Prevent bulk deleting yourself
        if ($action === 'delete' && in_array(auth()->id(), $userIds)) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot delete your own account.',
            ], 403);
        }

        DB::beginTransaction();

        try {
            switch ($action) {
                case 'activate':
                    User::whereIn('id', $userIds)->update(['status' => 1]);
                    $message = 'Users activated successfully.';
                    break;

                case 'deactivate':
                    User::whereIn('id', $userIds)->update(['status' => 0]);
                    $message = 'Users deactivated successfully.';
                    break;

                case 'verify':
                    User::whereIn('id', $userIds)->update([
                        'is_verified' => true,
                        'verified_at' => now(),
                    ]);
                    $message = 'Users verified successfully.';
                    break;

                case 'unverify':
                    User::whereIn('id', $userIds)->update([
                        'is_verified' => false,
                        'verified_at' => null,
                    ]);
                    $message = 'Users unverified successfully.';
                    break;

                case 'delete':
                    User::whereIn('id', $userIds)->update([
                        'status' => 3,
                        'is_delete' => 1,
                    ]);
                    $message = 'Users deleted successfully.';
                    break;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $message,
                'affected_count' => count($userIds),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Bulk update failed.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Assign role to user.
     *
     * POST /api/admin/users/{id}/assign-role
     */
    public function assignRole(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'role_id' => 'required|exists:roles,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $user->update(['role_id' => $request->role_id]);

        return response()->json([
            'success' => true,
            'message' => 'Role assigned successfully.',
            'data' => $user->load('role'),
        ]);
    }

    /**
     * Impersonate a user (for debugging/support).
     *
     * POST /api/admin/users/{id}/impersonate
     */
    public function impersonate($id)
    {
        $user = User::findOrFail($id);

        // Generate a token for the impersonated user
        $token = $user->createToken('impersonation-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => "Now impersonating {$user->name}",
            'data' => [
                'user' => $user,
                'token' => $token,
                'impersonated_by' => auth()->user()->name,
            ],
        ]);
    }
}
