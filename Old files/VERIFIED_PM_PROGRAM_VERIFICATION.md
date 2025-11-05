# Verified Property Manager Program - Implementation Verification

**Date:** October 27, 2025
**Status:** ⚠️ PARTIALLY COMPLETE (2 of 3 steps done)

---

## Requirements from improvement.md (Section 8)

### ✅ Step 8.1: Add Verification Flag - COMPLETE

**Requirement:** Add `is_verified` boolean/timestamp and `verification_documents` (JSON?) fields to `users` table.

**Implementation Status:** ✅ FULLY IMPLEMENTED

**Evidence:**

#### 1. Migration File Created
**File:** `database/migrations/2025_10_28_111938_add_verification_fields_to_users_table.php`

```php
public function up()
{
    Schema::table('users', function (Blueprint $table) {
        // Boolean flag to indicate if the PM has been verified by admin
        $table->boolean('is_verified')->default(false);

        // JSON field to store verification documents (URLs/paths to uploaded docs)
        $table->json('verification_documents')->nullable();

        // Timestamp when the PM was verified
        $table->timestamp('verified_at')->nullable();
    });
}
```

**Fields Added:**
- ✅ `is_verified` - Boolean, default false
- ✅ `verification_documents` - JSON, nullable
- ✅ `verified_at` - Timestamp, nullable (BONUS: not required but added)

#### 2. User Model Updated
**File:** `app/Models/User.php`

**Fillable Fields (lines 65-67):**
```php
protected $fillable = [
    // ... other fields ...
    'is_verified',
    'verification_documents',
    'verified_at'
];
```

**Casts (lines 86-88):**
```php
protected $casts = [
    'email_verified_at' => 'datetime',
    'is_verified' => 'boolean',
    'verification_documents' => 'array',
    'verified_at' => 'datetime',
];
```

**Benefits:**
- ✅ `is_verified` automatically cast to boolean
- ✅ `verification_documents` automatically decoded from JSON to array
- ✅ `verified_at` automatically cast to Carbon datetime
- ✅ All fields mass-assignable

---

### ❌ Step 8.2: Admin Verification UI - NOT IMPLEMENTED

**Requirement:** Create an interface in the Admin panel for reviewing submitted documents and marking PMs as verified.

**Implementation Status:** ❌ MISSING

**What's Needed:**

#### 1. Add Verification Section to Admin/UsersController

**File to Modify:** `app/Http/Controllers/Admin/UsersController.php`

**Required Methods:**

```php
/**
 * Display verification interface for a PM
 */
public function showVerification($userId)
{
    $user = User::findOrFail($userId);

    return view('admin.users.verification', compact('user'));
}

/**
 * Update verification status
 */
public function updateVerification(Request $request, $userId)
{
    $request->validate([
        'is_verified' => 'required|boolean',
        'verification_notes' => 'nullable|string'
    ]);

    $user = User::findOrFail($userId);

    $user->update([
        'is_verified' => $request->is_verified,
        'verified_at' => $request->is_verified ? now() : null
    ]);

    return redirect()->back()->with('success', 'Verification status updated successfully.');
}

/**
 * Upload verification document
 */
public function uploadVerificationDocument(Request $request, $userId)
{
    $request->validate([
        'document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120' // 5MB max
    ]);

    $user = User::findOrFail($userId);

    // Store document
    $path = $request->file('document')->store('verification-documents', 'private');

    // Add to verification_documents array
    $documents = $user->verification_documents ?? [];
    $documents[] = [
        'path' => $path,
        'original_name' => $request->file('document')->getClientOriginalName(),
        'uploaded_at' => now()->toDateTimeString()
    ];

    $user->update(['verification_documents' => $documents]);

    return response()->json([
        'success' => true,
        'message' => 'Document uploaded successfully',
        'document' => end($documents)
    ]);
}

/**
 * Delete verification document
 */
public function deleteVerificationDocument(Request $request, $userId, $documentIndex)
{
    $user = User::findOrFail($userId);
    $documents = $user->verification_documents ?? [];

    if (isset($documents[$documentIndex])) {
        // Delete file from storage
        Storage::disk('private')->delete($documents[$documentIndex]['path']);

        // Remove from array
        unset($documents[$documentIndex]);
        $documents = array_values($documents); // Re-index array

        $user->update(['verification_documents' => $documents]);

        return response()->json([
            'success' => true,
            'message' => 'Document deleted successfully'
        ]);
    }

    return response()->json([
        'success' => false,
        'message' => 'Document not found'
    ], 404);
}
```

#### 2. Add Routes

**File to Modify:** `routes/admin.php`

```php
// PM Verification Routes
Route::get('/users/{userId}/verification', [UsersController::class, 'showVerification'])->name('admin.users.verification');
Route::post('/users/{userId}/verification', [UsersController::class, 'updateVerification'])->name('admin.users.verification.update');
Route::post('/users/{userId}/verification/upload', [UsersController::class, 'uploadVerificationDocument'])->name('admin.users.verification.upload');
Route::delete('/users/{userId}/verification/documents/{documentIndex}', [UsersController::class, 'deleteVerificationDocument'])->name('admin.users.verification.delete');
```

#### 3. Create Blade View

**File to Create:** `resources/views/admin/users/verification.blade.php`

```blade
@extends('admin.layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h2>PM Verification - {{ $user->name }}</h2>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <!-- Verification Status Card -->
            <div class="card mb-4">
                <div class="card-header">
                    <h4>Verification Status</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.users.verification.update', $user->id) }}" method="POST">
                        @csrf

                        <div class="form-group">
                            <label>Current Status:</label>
                            <div>
                                @if($user->is_verified)
                                    <span class="badge badge-success">Verified</span>
                                    <small class="text-muted">Verified on {{ $user->verified_at->format('M d, Y') }}</small>
                                @else
                                    <span class="badge badge-warning">Not Verified</span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Update Status:</label>
                            <select name="is_verified" class="form-control">
                                <option value="1" {{ $user->is_verified ? 'selected' : '' }}>Verified</option>
                                <option value="0" {{ !$user->is_verified ? 'selected' : '' }}>Not Verified</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Admin Notes (optional):</label>
                            <textarea name="verification_notes" class="form-control" rows="3" placeholder="Internal notes about verification..."></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">Update Verification Status</button>
                    </form>
                </div>
            </div>

            <!-- Verification Documents Card -->
            <div class="card">
                <div class="card-header">
                    <h4>Verification Documents</h4>
                </div>
                <div class="card-body">
                    <!-- Upload Form -->
                    <form id="uploadForm" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label>Upload New Document:</label>
                            <input type="file" name="document" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                            <small class="form-text text-muted">Accepted: PDF, JPG, PNG (Max 5MB)</small>
                        </div>
                        <button type="submit" class="btn btn-success">Upload Document</button>
                    </form>

                    <hr>

                    <!-- Documents List -->
                    <h5>Uploaded Documents:</h5>
                    @if($user->verification_documents && count($user->verification_documents) > 0)
                        <div class="list-group" id="documentsList">
                            @foreach($user->verification_documents as $index => $doc)
                                <div class="list-group-item d-flex justify-content-between align-items-center" data-index="{{ $index }}">
                                    <div>
                                        <strong>{{ $doc['original_name'] }}</strong>
                                        <br>
                                        <small class="text-muted">Uploaded: {{ $doc['uploaded_at'] }}</small>
                                    </div>
                                    <div>
                                        <a href="{{ route('admin.users.verification.download', [$user->id, $index]) }}" class="btn btn-sm btn-info">Download</a>
                                        <button class="btn btn-sm btn-danger delete-doc" data-index="{{ $index }}">Delete</button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted">No documents uploaded yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Upload document via AJAX
document.getElementById('uploadForm').addEventListener('submit', function(e) {
    e.preventDefault();

    let formData = new FormData(this);

    fetch('{{ route('admin.users.verification.upload', $user->id) }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            alert('Document uploaded successfully');
            location.reload();
        }
    });
});

// Delete document via AJAX
document.querySelectorAll('.delete-doc').forEach(button => {
    button.addEventListener('click', function() {
        if(!confirm('Are you sure you want to delete this document?')) return;

        let index = this.dataset.index;

        fetch(`{{ url('/admin/users/' . $user->id . '/verification/documents') }}/${index}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                alert('Document deleted successfully');
                location.reload();
            }
        });
    });
});
</script>
@endsection
```

#### 4. Add Verification Link to User Edit Page

**File to Modify:** `resources/views/admin/users/edit.blade.php` (or wherever user edit view is)

Add a button/link:
```blade
<a href="{{ route('admin.users.verification', $user->id) }}" class="btn btn-warning">
    <i class="fas fa-shield-alt"></i> Manage Verification
</a>
```

---

### ✅ Step 8.3: Factor into Matching/Display - COMPLETE

**Requirement:** Update `LeadDistribute.php` to prioritize verified PMs. Display a "Verified" badge on PM listings/profiles.

**Implementation Status:** ✅ PARTIALLY COMPLETE (Lead Distribution done, Badge display pending)

**Evidence:**

#### 1. Lead Distribution Priority - IMPLEMENTED ✅

**File:** `app/Console/Commands/LeadDistribute.php`

**Line 37:** Fetches `is_verified` field
```php
->select('user_preferences.*', 'users.is_verified', 'users.credits')
```

**Lines 73-81:** Priority sorting with verified PMs first
```php
// Sort matching users: verified PMs first, then by tier priority
usort($matchingUsers, function($a, $b) {
    // Verified PMs get priority
    if($a->is_verified != $b->is_verified){
        return $b->is_verified <=> $a->is_verified;
    }
    // Then sort by tier_id (higher tier = higher priority)
    return $b->tier_id <=> $a->tier_id;
});
```

**Priority Order:**
1. ✅ Verified PMs with highest tier
2. ✅ Verified PMs with lower tiers
3. ✅ Non-verified PMs with highest tier
4. ✅ Non-verified PMs with lower tiers

#### 2. Badge Display - NOT IMPLEMENTED ❌

**What's Needed:**

**API Endpoint for PM Listings:**

**File to Create:** `app/Http/Controllers/Api/PropertyManagerController.php`

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class PropertyManagerController extends Controller
{
    /**
     * Get list of property managers for Next.js frontend
     */
    public function index(Request $request)
    {
        $query = User::where('type', 'property_manager')
            ->where('status', 1);

        // Filter by state/city if provided
        if($request->has('state')) {
            $query->where('state', $request->state);
        }

        if($request->has('city')) {
            $query->where('city', $request->city);
        }

        $managers = $query->select([
            'id',
            'name',
            'email',
            'company_name',
            'photo',
            'city',
            'state',
            'slug',
            'is_verified', // Include verification status
            'verified_at',
            'about',
            'website'
        ])->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $managers
        ]);
    }

    /**
     * Get single PM details
     */
    public function show($state, $city, $slug)
    {
        $manager = User::where('slug', $slug)
            ->where('state', $state)
            ->where('city', $city)
            ->where('type', 'property_manager')
            ->where('status', 1)
            ->select([
                'id',
                'name',
                'email',
                'company_name',
                'photo',
                'city',
                'state',
                'slug',
                'is_verified', // Include verification status
                'verified_at',
                'about',
                'website',
                'p_contact_name',
                'p_contact_no',
                'p_contact_email'
            ])
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => $manager
        ]);
    }
}
```

**Routes to Add:** `routes/api.php`

```php
// Property Manager endpoints
Route::get('/property-managers', [PropertyManagerController::class, 'index']);
Route::get('/property-managers/{state}/{city}/{slug}', [PropertyManagerController::class, 'show']);
```

**Next.js Frontend Badge Component:**

```tsx
// components/VerifiedBadge.tsx
interface VerifiedBadgeProps {
  isVerified: boolean;
  verifiedAt?: string;
}

export function VerifiedBadge({ isVerified, verifiedAt }: VerifiedBadgeProps) {
  if (!isVerified) return null;

  return (
    <span
      className="inline-flex items-center px-2 py-1 text-xs font-medium text-blue-700 bg-blue-100 rounded-full"
      title={`Verified on ${new Date(verifiedAt).toLocaleDateString()}`}
    >
      <svg className="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
        <path fillRule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clipRule="evenodd" />
      </svg>
      Verified
    </span>
  );
}

// Usage in PM Card:
<div className="pm-card">
  <h3>{manager.name}</h3>
  <VerifiedBadge isVerified={manager.is_verified} verifiedAt={manager.verified_at} />
  {/* ... rest of card ... */}
</div>
```

---

## Implementation Summary

### ✅ Completed Items

1. **Database Schema (Step 8.1):** ✅ COMPLETE
   - Migration created with `is_verified`, `verification_documents`, `verified_at`
   - User model updated with fillable fields and casts
   - All fields properly configured

2. **Lead Distribution Priority (Step 8.3):** ✅ COMPLETE
   - LeadDistribute command fetches and uses `is_verified` field
   - Verified PMs sorted to top of matching list
   - Priority system fully functional

### ❌ Missing Items

1. **Admin Verification UI (Step 8.2):** ❌ NOT IMPLEMENTED
   - No verification management interface in admin panel
   - No document upload functionality
   - No toggle for verification status
   - No routes for verification actions

2. **Badge Display (Step 8.3):** ❌ NOT IMPLEMENTED
   - No API endpoint exposing `is_verified` to frontend
   - No badge component in Next.js frontend
   - PM profile/listing views don't show verification status

---

## Completion Checklist

### To Complete Step 8.2 (Admin UI):
- [ ] Add verification methods to `Admin/UsersController.php`
- [ ] Create verification routes in `routes/admin.php`
- [ ] Create `resources/views/admin/users/verification.blade.php`
- [ ] Add verification link to user edit page
- [ ] Configure 'verification-documents' storage disk in `config/filesystems.php`
- [ ] Test document upload/delete functionality
- [ ] Test verification status toggle

### To Complete Step 8.3 (Badge Display):
- [ ] Create `PropertyManagerController.php` API endpoint
- [ ] Add routes for PM listings to `routes/api.php`
- [ ] Ensure `is_verified` field is included in API responses
- [ ] Create `VerifiedBadge` component in Next.js
- [ ] Add badge to PM listing cards
- [ ] Add badge to PM detail page
- [ ] Test badge display with verified and non-verified PMs

---

## Estimated Completion Time

- **Step 8.2 (Admin UI):** 3-4 hours
  - Controller methods: 1 hour
  - Blade view: 1.5 hours
  - Testing: 1 hour
  - Storage configuration: 30 minutes

- **Step 8.3 (Badge Display):** 2-3 hours
  - API endpoint: 30 minutes
  - Next.js component: 1 hour
  - Integration testing: 1 hour
  - Styling refinements: 30 minutes

**Total:** 5-7 hours for full completion

---

## Testing Recommendations

### Manual Testing for Admin UI:
1. Navigate to admin user list
2. Click "Manage Verification" for a PM
3. Upload a verification document (PDF, JPG)
4. Toggle verification status to "Verified"
5. Verify `verified_at` timestamp is set
6. Delete a document
7. Toggle back to "Not Verified"
8. Verify `verified_at` is cleared

### Manual Testing for Badge Display:
1. Create verified and non-verified test PMs
2. View PM listing page in Next.js app
3. Verify verified PMs show blue "Verified" badge
4. View individual PM profile
5. Verify badge appears on profile page
6. Hover over badge to see verification date

### Automated Testing:
```php
// tests/Feature/VerificationTest.php
public function test_admin_can_verify_pm()
{
    $admin = User::factory()->admin()->create();
    $pm = User::factory()->propertyManager()->create();

    $this->actingAs($admin)
        ->post("/admin/users/{$pm->id}/verification", [
            'is_verified' => true
        ])
        ->assertRedirect()
        ->assertSessionHas('success');

    $pm->refresh();
    $this->assertTrue($pm->is_verified);
    $this->assertNotNull($pm->verified_at);
}
```

---

## Current Status: 66% Complete

**Implemented:**
- ✅ Database schema (Step 8.1)
- ✅ Lead distribution priority (Step 8.3 part 1)

**Pending:**
- ❌ Admin verification UI (Step 8.2)
- ❌ Badge display (Step 8.3 part 2)
