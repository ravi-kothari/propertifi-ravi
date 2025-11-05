# Propertifi v2 - Backend Implementation Guide

## Overview
This document provides step-by-step instructions for AI agents to implement the Laravel backend for Propertifi v2. Each section includes specific prompts, code examples, and validation steps.

---

## Phase 1: Foundation & Legal Resource Center

### Task 1.1: Database Schema Setup for Legal Resources

**Objective**: Create database models for the legal resource center

**AI Agent Prompt**:
```
Create Laravel migrations and models for a legal resource center with the following requirements:
1. StateLawContent model with fields: state_code, topic_slug, title, summary, content, official_link, last_updated, meta_description, is_published
2. LegalTopic model with fields: name, slug, description, sort_order, is_active
3. StateProfile model with fields: state_code, name, slug, overview, abbreviation

Include proper relationships, indexes for SEO optimization, and validation rules.
```

**Implementation Steps**:

1. **Create migrations**:
```bash
php artisan make:migration create_legal_topics_table
php artisan make:migration create_state_profiles_table  
php artisan make:migration create_state_law_contents_table
```

2. **Migration files content**:

**LegalTopic Migration**:
```php
Schema::create('legal_topics', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('slug')->unique();
    $table->text('description')->nullable();
    $table->integer('sort_order')->default(0);
    $table->boolean('is_active')->default(true);
    $table->timestamps();
    
    $table->index(['is_active', 'sort_order']);
});
```

**StateProfile Migration**:
```php
Schema::create('state_profiles', function (Blueprint $table) {
    $table->id();
    $table->string('state_code', 2)->unique();
    $table->string('name');
    $table->string('slug')->unique();
    $table->text('overview')->nullable();
    $table->string('abbreviation', 2);
    $table->timestamps();
    
    $table->index('slug');
});
```

**StateLawContent Migration**:
```php
Schema::create('state_law_contents', function (Blueprint $table) {
    $table->id();
    $table->string('state_code', 2);
    $table->string('topic_slug');
    $table->string('title');
    $table->text('summary');
    $table->longText('content');
    $table->string('official_link')->nullable();
    $table->string('meta_description');
    $table->boolean('is_published')->default(true);
    $table->timestamp('last_updated');
    $table->timestamps();
    
    $table->unique(['state_code', 'topic_slug']);
    $table->index(['state_code', 'is_published']);
    $table->index(['topic_slug', 'is_published']);
    $table->foreign('state_code')->references('state_code')->on('state_profiles');
});
```

3. **Create Models with relationships**:

**Models to create**:
- `app/Models/LegalTopic.php`
- `app/Models/StateProfile.php` 
- `app/Models/StateLawContent.php`

**Validation**: Run migrations and verify tables are created with proper indexes.

---

### Task 1.2: Admin Controllers for Legal Content Management

**Objective**: Create admin interface for managing legal content

**AI Agent Prompt**:
```
Create Laravel admin controllers with full CRUD operations for managing legal content:
1. Admin/StateLawController - manage state law content with bulk import/export
2. Admin/LegalTopicController - manage legal topics
3. Admin/StateProfileController - manage state profiles

Include proper validation, authorization middleware, and Blade views with Bootstrap styling.
```

**Implementation Steps**:

1. **Create Controllers**:
```bash
php artisan make:controller Admin/StateLawController
php artisan make:controller Admin/LegalTopicController  
php artisan make:controller Admin/StateProfileController
```

2. **StateLawController Implementation**:
```php
class StateLawController extends Controller
{
    public function index(Request $request)
    {
        $query = StateLawContent::with(['stateProfile', 'legalTopic']);
        
        if ($request->state_code) {
            $query->where('state_code', $request->state_code);
        }
        
        if ($request->topic_slug) {
            $query->where('topic_slug', $request->topic_slug);
        }
        
        $contents = $query->paginate(20);
        $states = StateProfile::orderBy('name')->get();
        $topics = LegalTopic::where('is_active', true)->orderBy('sort_order')->get();
        
        return view('admin.state-laws.index', compact('contents', 'states', 'topics'));
    }
    
    // Additional CRUD methods...
}
```

3. **Create Blade Views**:
- `resources/views/admin/state-laws/index.blade.php`
- `resources/views/admin/state-laws/create.blade.php`
- `resources/views/admin/state-laws/edit.blade.php`

**Validation**: Test CRUD operations through admin interface.

---

### Task 1.3: Public API Endpoints for Legal Content

**Objective**: Create API endpoints for frontend consumption

**AI Agent Prompt**:
```
Create RESTful API endpoints for the legal resource center:
1. GET /api/states - list all states with law content count
2. GET /api/states/{state}/laws - get all law topics for a state
3. GET /api/states/{state}/laws/{topic} - get specific law content
4. GET /api/legal-topics - get all legal topics
5. Add proper caching, pagination, and SEO-friendly response structure

Include API documentation and rate limiting.
```

**Implementation Steps**:

1. **Create API Controller**:
```bash
php artisan make:controller Api/LegalContentController
```

2. **API Routes** (`routes/api.php`):
```php
Route::prefix('legal')->group(function () {
    Route::get('states', [LegalContentController::class, 'states']);
    Route::get('states/{state}/laws', [LegalContentController::class, 'stateLaws']);
    Route::get('states/{state}/laws/{topic}', [LegalContentController::class, 'specificLaw']);
    Route::get('topics', [LegalContentController::class, 'topics']);
});
```

3. **Controller Implementation**:
```php
class LegalContentController extends Controller
{
    public function states()
    {
        $states = StateProfile::withCount(['lawContents' => function ($query) {
            $query->where('is_published', true);
        }])
        ->orderBy('name')
        ->get()
        ->map(function ($state) {
            return [
                'code' => $state->state_code,
                'name' => $state->name,
                'slug' => $state->slug,
                'law_count' => $state->law_contents_count,
                'overview' => $state->overview
            ];
        });
        
        return response()->json([
            'data' => $states,
            'meta' => [
                'total_states' => $states->count(),
                'generated_at' => now()->toISOString()
            ]
        ]);
    }
    
    // Additional methods...
}
```

**Validation**: Test API endpoints and verify response structure.

---

## Phase 2: Document Template Library

### Task 2.1: Document Template System

**Objective**: Create document template management system

**AI Agent Prompt**:
```
Create a document template library system with:
1. DocumentTemplate model with fields: title, description, state_code, category, file_path, is_free, requires_signup, download_count, tags
2. DocumentCategory model for organizing templates
3. File upload handling with validation for PDF, DOC, DOCX files
4. Download tracking and access control
5. Admin interface for template management

Include proper file storage, validation, and security measures.
```

**Implementation Steps**:

1. **Create Migrations**:
```bash
php artisan make:migration create_document_categories_table
php artisan make:migration create_document_templates_table
```

2. **DocumentTemplate Migration**:
```php
Schema::create('document_templates', function (Blueprint $table) {
    $table->id();
    $table->string('title');
    $table->text('description');
    $table->string('state_code', 2)->nullable(); // null for federal
    $table->string('category_slug');
    $table->string('file_path');
    $table->string('file_size_mb', 10);
    $table->boolean('is_free')->default(true);
    $table->boolean('requires_signup')->default(false);
    $table->integer('download_count')->default(0);
    $table->json('tags')->nullable();
    $table->boolean('is_active')->default(true);
    $table->timestamps();
    
    $table->index(['state_code', 'category_slug', 'is_active']);
    $table->index(['is_free', 'is_active']);
});
```

3. **Create Controllers**:
```bash
php artisan make:controller Admin/DocumentTemplateController
php artisan make:controller Api/DocumentTemplateController
```

**Validation**: Test file upload and download functionality.

---

### Task 2.2: Secure Download System

**Objective**: Implement secure file downloads with access control

**AI Agent Prompt**:
```
Create a secure download system for document templates:
1. Download tracking (user tracking, anonymous downloads)
2. Access control based on requires_signup flag
3. Secure file serving (no direct links to storage)
4. Download analytics and reporting
5. Rate limiting for downloads

Include proper security headers and file validation.
```

**Implementation Steps**:

1. **Create Download Controller**:
```php
class DocumentDownloadController extends Controller
{
    public function download(Request $request, DocumentTemplate $template)
    {
        // Access control logic
        if ($template->requires_signup && !auth()->check()) {
            return response()->json(['error' => 'Login required'], 401);
        }
        
        // Rate limiting
        if (RateLimiter::tooManyAttempts('download:' . $request->ip(), 10)) {
            return response()->json(['error' => 'Too many downloads'], 429);
        }
        
        // Track download
        $template->increment('download_count');
        
        // Serve file securely
        return Storage::download($template->file_path, $template->title . '.pdf');
    }
}
```

**Validation**: Test download restrictions and tracking.

---

## Phase 3: Enhanced Lead Management

### Task 3.1: Advanced Lead Preferences

**Objective**: Expand user preference system for better lead matching

**AI Agent Prompt**:
```
Enhance the lead matching system with:
1. Add property_types (JSON), min_units, max_units, budget_range to UserPreferences
2. Create PreferenceProperty model for complex property criteria
3. Update lead capture form to collect additional property details
4. Modify LeadDistribute command to use new matching criteria
5. Add preference scoring algorithm

Include migration for existing data and backward compatibility.
```

**Implementation Steps**:

1. **Update UserPreferences Migration**:
```php
Schema::table('user_preferences', function (Blueprint $table) {
    $table->json('property_types')->nullable(); // ['single-family', 'multi-family', 'commercial']
    $table->integer('min_units')->nullable();
    $table->integer('max_units')->nullable();
    $table->json('budget_range')->nullable(); // ['min' => 1000, 'max' => 5000]
    $table->decimal('max_distance_miles', 8, 2)->nullable();
    $table->json('excluded_zipcodes')->nullable();
});
```

2. **Update Lead Model**:
```php
Schema::table('leads', function (Blueprint $table) {
    $table->string('property_type')->nullable();
    $table->integer('unit_count')->nullable();
    $table->decimal('monthly_rent', 10, 2)->nullable();
    $table->json('property_details')->nullable();
});
```

3. **Enhanced LeadDistribute Command**:
```php
class LeadDistribute extends Command
{
    public function handle()
    {
        $unprocessedLeads = Lead::where('distributed_at', null)->get();
        
        foreach ($unprocessedLeads as $lead) {
            $matchingUsers = $this->findMatchingUsers($lead);
            $this->distributeToUsers($lead, $matchingUsers);
        }
    }
    
    private function findMatchingUsers(Lead $lead)
    {
        return User::whereHas('preferences', function ($query) use ($lead) {
            // Zipcode matching
            $query->whereHas('zipcodes', function ($zipQuery) use ($lead) {
                $zipQuery->where('zipcode', $lead->zipcode);
            });
            
            // Property type matching
            if ($lead->property_type) {
                $query->whereJsonContains('property_types', $lead->property_type)
                      ->orWhereNull('property_types');
            }
            
            // Unit count matching
            if ($lead->unit_count) {
                $query->where(function ($unitQuery) use ($lead) {
                    $unitQuery->where('min_units', '<=', $lead->unit_count)
                             ->orWhereNull('min_units');
                })
                ->where(function ($unitQuery) use ($lead) {
                    $unitQuery->where('max_units', '>=', $lead->unit_count)
                             ->orWhereNull('max_units');
                });
            }
        })
        ->where('is_active', true)
        ->get();
    }
}
```

**Validation**: Test lead distribution with new criteria.

---

### Task 3.2: Lead Status Management

**Objective**: Add lead lifecycle tracking for property managers

**AI Agent Prompt**:
```
Create a comprehensive lead status tracking system:
1. Add status field to UserLeads table with enum values
2. Create LeadStatusHistory model for tracking status changes
3. Add lead notes functionality for property managers
4. Create API endpoints for status updates
5. Add dashboard analytics based on lead status

Include proper validation and business logic for status transitions.
```

**Implementation Steps**:

1. **Update UserLeads Migration**:
```php
Schema::table('user_leads', function (Blueprint $table) {
    $table->enum('status', ['new', 'contacted', 'qualified', 'not_interested', 'client_won', 'client_lost'])
          ->default('new');
    $table->text('notes')->nullable();
    $table->timestamp('last_status_update')->nullable();
    $table->timestamp('first_contact_at')->nullable();
});
```

2. **Create LeadStatusHistory Model**:
```php
Schema::create('lead_status_histories', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_lead_id')->constrained()->onDelete('cascade');
    $table->string('old_status')->nullable();
    $table->string('new_status');
    $table->text('notes')->nullable();
    $table->foreignId('user_id')->constrained();
    $table->timestamps();
    
    $table->index(['user_lead_id', 'created_at']);
});
```

**Validation**: Test status updates and history tracking.

---

## Phase 4: User Dashboard Enhancements

### Task 4.1: Property Owner Dashboard Backend

**Objective**: Create backend for property owner dashboard

**AI Agent Prompt**:
```
Create a property owner dashboard system:
1. PropertyOwner model extending User with additional fields
2. Owner authentication system (separate from property managers)
3. Lead history and matching status API endpoints
4. Bookmark system for legal content and templates
5. Saved calculator results storage

Include proper authentication guards and role management.
```

**Implementation Steps**:

1. **Create PropertyOwner Model**:
```php
class PropertyOwner extends Model
{
    protected $fillable = [
        'user_id', 'company_name', 'phone', 'address', 
        'properties_owned', 'years_experience'
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function leads()
    {
        return $this->hasMany(Lead::class, 'email', 'email');
    }
}
```

2. **Create Bookmark System**:
```php
Schema::create('owner_bookmarks', function (Blueprint $table) {
    $table->id();
    $table->foreignId('property_owner_id')->constrained();
    $table->morphs('bookmarkable'); // polymorphic relation
    $table->timestamps();
    
    $table->unique(['property_owner_id', 'bookmarkable_id', 'bookmarkable_type']);
});
```

**Validation**: Test owner authentication and dashboard data retrieval.

---

### Task 4.2: Property Manager Dashboard Enhancement

**Objective**: Enhance PM dashboard with analytics and lead management

**AI Agent Prompt**:
```
Enhance the property manager dashboard:
1. Lead performance analytics (conversion rates, response times)
2. Monthly/weekly performance reports
3. Lead pipeline visualization data
4. Revenue tracking integration
5. Competitive analysis (anonymized market data)

Include caching for performance and scheduled report generation.
```

**Implementation Steps**:

1. **Create Analytics Service**:
```php
class LeadAnalyticsService
{
    public function getUserStats(User $user, $period = '30days')
    {
        $startDate = Carbon::now()->subDays($period === '30days' ? 30 : 7);
        
        return [
            'total_leads' => $user->userLeads()->where('created_at', '>=', $startDate)->count(),
            'contacted_leads' => $user->userLeads()->where('status', 'contacted')
                                    ->where('created_at', '>=', $startDate)->count(),
            'conversion_rate' => $this->calculateConversionRate($user, $startDate),
            'avg_response_time' => $this->calculateAvgResponseTime($user, $startDate),
            'won_deals' => $user->userLeads()->where('status', 'client_won')
                              ->where('created_at', '>=', $startDate)->count(),
        ];
    }
}
```

**Validation**: Test analytics calculations and dashboard performance.

---

## Phase 5: Advanced Features

### Task 5.1: Verification System

**Objective**: Implement property manager verification system

**AI Agent Prompt**:
```
Create a comprehensive verification system:
1. Add verification fields to users table (is_verified, verification_documents, verification_date)
2. Create VerificationRequest model for tracking verification process
3. Document upload and review system
4. Admin interface for verification management
5. Verification badges and prioritization in lead distribution

Include file validation, secure storage, and audit trail.
```

**Implementation Steps**:

1. **Add Verification Fields**:
```php
Schema::table('users', function (Blueprint $table) {
    $table->boolean('is_verified')->default(false);
    $table->json('verification_documents')->nullable();
    $table->timestamp('verification_date')->nullable();
    $table->text('verification_notes')->nullable();
    $table->enum('verification_status', ['pending', 'approved', 'rejected', 'expired'])
          ->default('pending');
});
```

2. **Create VerificationRequest Model**:
```php
Schema::create('verification_requests', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained();
    $table->json('submitted_documents');
    $table->enum('status', ['pending', 'under_review', 'approved', 'rejected']);
    $table->text('admin_notes')->nullable();
    $table->foreignId('reviewed_by')->nullable()->constrained('users');
    $table->timestamp('submitted_at');
    $table->timestamp('reviewed_at')->nullable();
    $table->timestamps();
});
```

**Validation**: Test verification workflow and document handling.

---

### Task 5.2: City Landing Pages System

**Objective**: Create dynamic city-specific landing pages for SEO

**AI Agent Prompt**:
```
Create a system for generating city-specific landing pages:
1. City model with demographics, market data, and SEO content
2. PropertyManagerLocation model linking PMs to cities
3. Dynamic content generation for city pages
4. Local market statistics integration
5. City-specific legal resources and templates

Include SEO optimization, schema markup, and performance caching.
```

**Implementation Steps**:

1. **Create City Model**:
```php
Schema::create('cities', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('slug')->unique();
    $table->string('state_code', 2);
    $table->string('county')->nullable();
    $table->decimal('latitude', 10, 8)->nullable();
    $table->decimal('longitude', 11, 8)->nullable();
    $table->integer('population')->nullable();
    $table->json('market_data')->nullable(); // median rent, vacancy rates, etc.
    $table->text('description')->nullable();
    $table->string('meta_title')->nullable();
    $table->text('meta_description')->nullable();
    $table->boolean('is_active')->default(true);
    $table->timestamps();
    
    $table->index(['state_code', 'is_active']);
    $table->index('slug');
});
```

2. **Create API for City Pages**:
```php
class CityController extends Controller
{
    public function show($state, $city)
    {
        $cityData = City::where('slug', $city)
                       ->where('state_code', strtoupper($state))
                       ->with(['propertyManagers', 'localLaws'])
                       ->firstOrFail();
        
        return response()->json([
            'city' => $cityData,
            'property_managers' => $cityData->propertyManagers()->verified()->get(),
            'local_laws' => $cityData->localLaws,
            'market_stats' => $cityData->market_data,
            'seo' => [
                'title' => $cityData->meta_title,
                'description' => $cityData->meta_description,
                'canonical' => route('city.show', [$state, $city])
            ]
        ]);
    }
}
```

**Validation**: Test city page generation and SEO metadata.

---

## Testing & Quality Assurance

### Task 6.1: Comprehensive Testing Suite

**AI Agent Prompt**:
```
Create a comprehensive testing suite:
1. Unit tests for models and services
2. Feature tests for API endpoints
3. Integration tests for lead distribution
4. Performance tests for dashboard queries
5. Security tests for file uploads and downloads

Include test database seeding and factory definitions.
```

**Implementation Steps**:

1. **Create Model Factories**:
```php
// database/factories/StateLawContentFactory.php
class StateLawContentFactory extends Factory
{
    public function definition()
    {
        return [
            'state_code' => $this->faker->stateAbbr(),
            'topic_slug' => $this->faker->slug(),
            'title' => $this->faker->sentence(),
            'summary' => $this->faker->paragraph(),
            'content' => $this->faker->text(5000),
            'meta_description' => $this->faker->text(160),
            'last_updated' => $this->faker->dateTimeThisYear(),
        ];
    }
}
```

2. **Create Feature Tests**:
```php
class LegalContentApiTest extends TestCase
{
    public function test_can_fetch_states_list()
    {
        StateProfile::factory()->count(3)->create();
        
        $response = $this->getJson('/api/legal/states');
        
        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => ['code', 'name', 'slug', 'law_count']
                    ]
                ]);
    }
}
```

**Validation**: Run all tests and ensure 100% pass rate.

---

## Deployment & Performance

### Task 7.1: Optimization and Caching

**AI Agent Prompt**:
```
Implement performance optimizations:
1. Database query optimization with proper indexes
2. Redis caching for frequently accessed data
3. API response caching with cache invalidation
4. Database connection pooling
5. Background job processing for heavy operations

Include monitoring and alerting for performance metrics.
```

**Implementation Steps**:

1. **Cache Configuration**:
```php
// config/cache.php - Add cache tags support
'stores' => [
    'redis' => [
        'driver' => 'redis',
        'connection' => 'cache',
        'lock_connection' => 'default',
    ],
],

// Add cache service
class CacheService
{
    public function rememberStateLaws($stateCode, $hours = 24)
    {
        return Cache::tags(['state-laws', "state-{$stateCode}"])
                   ->remember("state-laws-{$stateCode}", $hours * 3600, function () use ($stateCode) {
                       return StateLawContent::where('state_code', $stateCode)
                                           ->where('is_published', true)
                                           ->with('legalTopic')
                                           ->get();
                   });
    }
}
```

2. **Database Indexes**:
```php
// Add performance indexes
Schema::table('user_leads', function (Blueprint $table) {
    $table->index(['user_id', 'status', 'created_at']);
    $table->index(['lead_id', 'created_at']);
});

Schema::table('state_law_contents', function (Blueprint $table) {
    $table->index(['state_code', 'topic_slug', 'is_published']);
    $table->fulltext(['title', 'summary', 'content']);
});
```

**Validation**: Run performance tests and verify query optimization.

---

## Security Implementation

### Task 8.1: Security Hardening

**AI Agent Prompt**:
```
Implement comprehensive security measures:
1. API rate limiting and DDoS protection
2. File upload security and virus scanning
3. SQL injection prevention and input validation
4. CSRF protection for all forms
5. Secure file serving with access controls

Include security headers and audit logging.
```

**Implementation Steps**:

1. **Rate Limiting**:
```php
// app/Http/Kernel.php
protected $routeMiddleware = [
    'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
    'auth.api' => \App\Http\Middleware\AuthenticateApi::class,
];

// Apply to routes
Route::middleware(['throttle:60,1'])->group(function () {
    Route::apiResource('legal/states', LegalContentController::class);
});
```

2. **File Security**:
```php
class SecureFileUploadService
{
    public function validateAndStore(UploadedFile $file, $directory)
    {
        // Validate file type
        $allowedMimes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        
        if (!in_array($file->getMimeType(), $allowedMimes)) {
            throw new ValidationException('Invalid file type');
        }
        
        // Scan for viruses (if ClamAV is available)
        if (class_exists('\Xenolope\Quahog\Client')) {
            $scanner = new \Xenolope\Quahog\Client('unix:///var/run/clamav/clamd.ctl');
            $result = $scanner->scanFile($file->getRealPath());
            
            if ($result['status'] !== 'OK') {
                throw new ValidationException('File failed security scan');
            }
        }
        
        // Store with secure filename
        $filename = Str::random(40) . '.' . $file->getClientOriginalExtension();
        return $file->storeAs($directory, $filename, 'private');
    }
}
```

**Validation**: Run security tests and penetration testing.

---

## Final Checklist

### Pre-Production Verification

**AI Agent Prompt**:
```
Create a comprehensive pre-production checklist:
1. All database migrations run successfully
2. All API endpoints return proper responses
3. File upload/download functionality works
4. Email notifications are properly configured
5. Caching is working and can be invalidated
6. Background jobs are processing
7. Error logging is configured
8. Performance benchmarks meet requirements

Include automated testing scripts for verification.
```

**Implementation Steps**:

1. **Health Check Endpoint**:
```php
class HealthCheckController extends Controller
{
    public function check()
    {
        $checks = [
            'database' => $this->checkDatabase(),
            'cache' => $this->checkCache(),
            'storage' => $this->checkStorage(),
            'queue' => $this->checkQueue(),
            'mail' => $this->checkMail(),
        ];
        
        $allHealthy = collect($checks)->every(fn($check) => $check['status'] === 'ok');
        
        return response()->json([
            'status' => $allHealthy ? 'healthy' : 'unhealthy',
            'checks' => $checks,
            'timestamp' => now()->toISOString()
        ], $allHealthy ? 200 : 503);
    }
}
```

**Final Validation**: All systems operational and performance targets met.

---

This implementation guide provides comprehensive, step-by-step instructions for AI agents to build the Propertifi v2 backend. Each task includes specific prompts, code examples, and validation steps to ensure proper implementation and tracking of progress.
