# Phase 3 Quick Reference Guide

## New Database Fields

### UserPreferences Table
```php
'property_types'           // JSON array - e.g., ['single_family', 'multi_family']
'min_units'                // Integer - minimum units (e.g., 1)
'max_units'                // Integer - maximum units (e.g., 100)
'preferred_delivery_speed' // Integer - minutes (overrides 'timings')
```

### Users Table
```php
'is_verified'              // Boolean - true if admin verified
'verification_documents'   // JSON array - ['url1.pdf', 'url2.pdf']
'verified_at'              // Timestamp - when verified
```

## Model Usage Examples

### Creating PM with Preferences
```php
use App\Models\User;
use App\Models\UserPreferences;

// Create PM
$pm = User::create([
    'name' => 'John Property Manager',
    'email' => 'john@example.com',
    'type' => 'property_manager',
    // ... other fields
]);

// Create preferences
$preferences = UserPreferences::create([
    'user_id' => $pm->id,
    'pricing_id' => 1,
    'tier_id' => 2,
    'timings' => 15,
    'property_types' => ['single_family', 'multi_family'], // Auto-casted to JSON
    'min_units' => 5,
    'max_units' => 100,
    'preferred_delivery_speed' => 10, // 10 minutes
    'status' => 1,
]);
```

### Verifying a PM
```php
use App\Models\User;

$pm = User::find($pmId);
$pm->update([
    'is_verified' => true,
    'verification_documents' => [
        'storage/documents/license.pdf',
        'storage/documents/insurance.pdf'
    ],
    'verified_at' => now(),
]);
```

### Using Relationships
```php
// Get PM's preferences
$pm = User::with('preferences')->find($pmId);
$propertyTypes = $pm->preferences->property_types; // Already an array

// Get PM's assigned leads
$pm = User::with('assignedLeads')->find($pmId);
$leadCount = $pm->assignedLeads->count();

// Get user from preference
$preference = UserPreferences::with('user', 'tier')->find($prefId);
$pmName = $preference->user->name;
$tierPrice = $preference->tier->price;
```

## Lead Distribution Logic Flow

```
1. Fetch all leads
2. For each lead:
   a. Find PMs matching:
      - Zipcode
      - Pricing category
      - Active status
   b. Filter by preferences:
      - Property type match
      - Unit count within range
   c. Sort PMs:
      - Verified PMs first
      - Then by tier (higher tier = priority)
   d. Distribute to each matching PM:
      - Check delivery time
      - Verify not already assigned
      - Check credits (if paid tier)
      - Assign and deduct credits
      - Log results
```

## Testing Lead Distribution

### Manual Test
```bash
# Run the command manually
php artisan LeadDistribute:check

# Check logs
tail -f storage/logs/laravel.log | grep "Lead"
```

### Test Scenarios

#### Scenario 1: Property Type Filtering
```php
// Create lead
$lead = Lead::create([
    'category' => 'single_family',
    'zipcode' => '12345',
    'units' => 1,
    // ...
]);

// PM with matching preference (will receive lead)
UserPreferences::create([
    'property_types' => ['single_family', 'multi_family'],
    // ...
]);

// PM with non-matching preference (will NOT receive lead)
UserPreferences::create([
    'property_types' => ['commercial_property'],
    // ...
]);
```

#### Scenario 2: Unit Count Filtering
```php
// Lead with 50 units
$lead = Lead::create([
    'units' => 50,
    // ...
]);

// PM 1: min=10, max=100 (MATCH - will receive)
// PM 2: min=60, max=200 (NO MATCH - units too low)
// PM 3: min=1, max=40 (NO MATCH - units too high)
```

#### Scenario 3: Verification Priority
```php
// Two PMs in same tier, same preferences
// PM 1: is_verified = true  (receives lead FIRST)
// PM 2: is_verified = false (receives lead SECOND)
```

#### Scenario 4: Credit Check
```php
// Tier price: $50
// PM with $100 credits (WILL receive lead, balance becomes $50)
// PM with $30 credits (SKIPPED - insufficient credits)
```

## Admin Controller Routes

### State Law Content
```php
// List all laws
GET /admin/laws

// Create form
GET /admin/laws/create

// Store new law
POST /admin/laws

// Show law
GET /admin/laws/{law}

// Edit form
GET /admin/laws/{law}/edit

// Update law
PUT /admin/laws/{law}

// Delete law
DELETE /admin/laws/{law}
```

### Document Templates
```php
// List all templates
GET /admin/templates

// Create form
GET /admin/templates/create

// Store new template (with file upload)
POST /admin/templates
// Form data: title, description, file, is_free, requires_signup, status

// Show template
GET /admin/templates/{template}

// Edit form
GET /admin/templates/{template}/edit

// Update template (optional new file)
PUT /admin/templates/{template}

// Delete template (also deletes file)
DELETE /admin/templates/{template}
```

## Debugging Tips

### Check Migration Status
```bash
php artisan migrate:status
```

### View Generated SQL
```bash
php artisan migrate --pretend
```

### Check Logs
```bash
# All logs
tail -f storage/logs/laravel.log

# Lead distribution only
tail -f storage/logs/laravel.log | grep "Lead Distribution"

# Insufficient credits
tail -f storage/logs/laravel.log | grep "insufficient credits"
```

### Database Queries
```sql
-- Check PM preferences
SELECT u.name, up.property_types, up.min_units, up.max_units 
FROM users u 
JOIN user_preferences up ON u.id = up.user_id;

-- Check verified PMs
SELECT name, is_verified, verified_at 
FROM users 
WHERE is_verified = 1;

-- Check lead assignments
SELECT u.name, COUNT(ul.id) as lead_count 
FROM users u 
LEFT JOIN user_leads ul ON u.id = ul.user_id 
GROUP BY u.id;

-- Check PM credits
SELECT name, credits 
FROM users 
WHERE type = 'property_manager' 
ORDER BY credits DESC;
```

## Common Issues & Solutions

### Issue: Migrations fail
**Solution:** Check if tables exist first
```bash
php artisan migrate:status
php artisan migrate:rollback --step=1
php artisan migrate
```

### Issue: Property types not filtering
**Solution:** Ensure property_types is stored as JSON array
```php
// Correct
'property_types' => ['single_family', 'multi_family']

// Incorrect
'property_types' => 'single_family,multi_family'
```

### Issue: Verified PMs not getting priority
**Solution:** Check sorting logic and is_verified field
```php
// Debug: Check PM verification status
$users = DB::table('users')->select('id', 'name', 'is_verified')->get();
dd($users);
```

### Issue: Credits not deducting
**Solution:** Verify tier price and credit balance
```php
// Debug before lead assignment
Log::info('Credit check', [
    'user_id' => $user->user_id,
    'current_credits' => $user->credits,
    'tier_price' => $tierData->price,
    'has_enough' => $user->credits >= $tierData->price
]);
```

## Performance Optimization

### Add Indexes (if needed)
```sql
-- For lead distribution queries
ALTER TABLE user_preferences ADD INDEX idx_status (status);
ALTER TABLE users ADD INDEX idx_is_verified (is_verified);
ALTER TABLE user_zipcodes ADD INDEX idx_zipcode_status (zipcode, status);
ALTER TABLE leads ADD INDEX idx_category (category);
```

### Query Optimization
```php
// Instead of this (N+1 problem)
$users = User::all();
foreach($users as $user) {
    $prefs = $user->preferences; // Extra query per user
}

// Do this (eager loading)
$users = User::with('preferences')->get();
foreach($users as $user) {
    $prefs = $user->preferences; // No extra queries
}
```

## Cron Schedule

Add to `app/Console/Kernel.php`:
```php
protected function schedule(Schedule $schedule)
{
    // Run lead distribution every 5 minutes
    $schedule->command('LeadDistribute:check')
             ->everyFiveMinutes()
             ->withoutOverlapping()
             ->runInBackground();
}
```

Verify cron is running:
```bash
php artisan schedule:list
```
