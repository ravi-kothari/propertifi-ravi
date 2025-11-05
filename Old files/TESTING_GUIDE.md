# Propertifi - Complete Local Testing Guide

**End-to-end testing guide for all implemented backend features**

---

## Quick Start (TL;DR)

```bash
# 1. Setup database
mysql -u root -p -e "CREATE DATABASE propertifi"

# 2. Configure environment
cd /Users/ravi/Documents/gemini_projects/propertifi/propertifi-backend
cp .env.example .env
# Edit .env with your database credentials

# 3. Install & migrate
composer install
php artisan key:generate
php artisan migrate

# 4. Start server
php artisan serve
# Server runs at http://localhost:8000

# 5. Test API
curl http://localhost:8000/api/property-managers
```

---

## Part 1: Environment Setup

### Step 1: Database Configuration

**Create Database:**
```bash
mysql -u root -p
CREATE DATABASE propertifi CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
exit;
```

**Configure `.env`:**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=propertifi
DB_USERNAME=root
DB_PASSWORD=your_password_here
```

### Step 2: Install Dependencies & Migrate

```bash
composer install
php artisan key:generate
php artisan migrate
```

### Step 3: Start Laravel Server

```bash
php artisan serve
# Server: http://localhost:8000
```

---

## Part 2: Create Test Data

### Create Test Users

Open a new terminal and run:

```bash
php artisan tinker
```

**Create Admin:**
```php
App\Models\User::create([
    'type' => 'Admin',
    'name' => 'Admin User',
    'email' => 'admin@test.com',
    'password' => bcrypt('password123'),
    'status' => 1
]);
```

**Create Verified PM:**
```php
$pm1 = App\Models\User::create([
    'type' => 'User',
    'name' => 'Premium PM',
    'company_name' => 'Premium PM',
    'email' => 'pm1@test.com',
    'password' => bcrypt('password123'),
    'mobile' => '1234567890',
    'city' => 'Los Angeles',
    'state' => 'CA',
    'zipcode' => '90001',
    'slug' => 'premium-pm-los-angeles',
    'status' => 1,
    'credits' => 1000,
    'is_verified' => true,
    'verified_at' => now()
]);
```

**Create Owner:**
```php
App\Models\Owner::create([
    'name' => 'Test Owner',
    'email' => 'owner@test.com',
    'password' => bcrypt('password123')
]);

exit;
```

---

## Part 3: Test API Endpoints

### Test 1: Property Managers API (Verified Badge)

```bash
# Get all PMs
curl http://localhost:8000/api/property-managers

# Get only verified PMs
curl http://localhost:8000/api/property-managers?verified=true
```

**Expected Response:**
```json
{
  "success": true,
  "data": {
    "data": [
      {
        "id": 1,
        "name": "Premium PM",
        "is_verified": true,
        "verified_at": "2025-10-27...",
        ...
      }
    ]
  }
}
```

### Test 2: Owner Authentication

**Register:**
```bash
curl -X POST http://localhost:8000/api/owner/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "New Owner",
    "email": "newowner@test.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'
```

**Login:**
```bash
curl -X POST http://localhost:8000/api/owner/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "owner@test.com",
    "password": "password123"
  }'
```

Save the token from the response!

**Get Dashboard:**
```bash
curl http://localhost:8000/api/owner/dashboard \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

### Test 3: Submit Lead

```bash
curl -X POST http://localhost:8000/api/home-page-lead \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@test.com",
    "phone": "1234567890",
    "address": "123 Main St",
    "city": "Los Angeles",
    "state": "CA",
    "zipcode": "90001",
    "category": "multi_family"
  }'
```

### Test 4: Calculators

```bash
curl -X POST http://localhost:8000/api/calculator/roi \
  -H "Content-Type: application/json" \
  -d '{
    "property_value": 500000,
    "monthly_rent": 3000,
    "monthly_expenses": 1500
  }'
```

---

## Part 4: Test Admin Verification UI

### Access Admin Panel

1. **Login to Admin:** `http://localhost:8000/admin`
   - Email: `admin@test.com`
   - Password: `password123`

2. **Go to Verification Page:**
   - URL: `http://localhost:8000/admin/users/1/verification`
   - (Replace `1` with actual PM user ID)

3. **Test Features:**
   - Upload a document (PDF/JPG/PNG)
   - Toggle verification status
   - Download document
   - Delete document

---

## Part 5: Test Lead Distribution

### Create Test Data

```bash
php artisan tinker
```

**Create Lead:**
```php
$lead = App\Models\Lead::create([
    'name' => 'Test Lead',
    'email' => 'lead@test.com',
    'phone' => '5555555555',
    'city' => 'Los Angeles',
    'state' => 'CA',
    'zipcode' => '90001',
    'category' => 'multi_family',
    'units' => 25,
    'unique_id' => 'LEAD-' . time()
]);

echo "Created lead ID: {$lead->id}\n";
exit;
```

### Run Distribution

```bash
# Run command
php artisan LeadDistribute:check

# Watch logs
tail -f storage/logs/laravel.log | grep "Lead"
```

**Expected Output:**
```
Lead Distribution Started
Lead distributed to PM: {"lead_id":1,"user_id":1}
Lead Distribution Completed
```

### Verify Assignment

```bash
php artisan tinker
```

```php
$assignments = DB::table('user_leads')->get();
foreach($assignments as $a) {
    $pm = App\Models\User::find($a->user_id);
    echo "Lead {$a->lead_id} -> PM: {$pm->name} (Verified: {$pm->is_verified})\n";
}
exit;
```

---

## Part 6: Test with Postman

### Import This Collection

Create requests in Postman:

**1. GET Property Managers**
```
GET http://localhost:8000/api/property-managers
```

**2. POST Owner Register**
```
POST http://localhost:8000/api/owner/register
Body: {"name":"Test","email":"test@test.com","password":"password123","password_confirmation":"password123"}
```

**3. POST Owner Login**
```
POST http://localhost:8000/api/owner/login
Body: {"email":"test@test.com","password":"password123"}
```

**4. GET Owner Dashboard** (needs token from login)
```
GET http://localhost:8000/api/owner/dashboard
Headers: Authorization: Bearer {token}
```

**5. POST Submit Lead**
```
POST http://localhost:8000/api/home-page-lead
Body: {"name":"Test","email":"t@t.com","phone":"1234567890","city":"Los Angeles","state":"CA","zipcode":"90001"}
```

---

## Part 7: Verification Scenarios

### Scenario 1: Verified PM Priority

```bash
php artisan tinker
```

```php
// PM1: Verified, Tier 3
$pm1 = App\Models\User::find(1);
$pm1->update(['is_verified' => true, 'tier_id' => 3]);

// PM2: Not verified, Tier 5
$pm2 = App\Models\User::find(2);
$pm2->update(['is_verified' => false, 'tier_id' => 5]);

// Create lead
$lead = App\Models\Lead::create([
    'name' => 'Test',
    'email' => 't@t.com',
    'phone' => '5555555555',
    'city' => 'Los Angeles',
    'state' => 'CA',
    'zipcode' => '90001',
    'category' => 'single_family',
    'unique_id' => 'LEAD-' . time()
]);

exit;
```

```bash
php artisan LeadDistribute:check
```

**Expected:** PM1 (verified) gets lead despite lower tier.

---

## Part 8: Database Inspection

```bash
mysql -u root -p propertifi
```

**Check Verified PMs:**
```sql
SELECT id, name, is_verified, verified_at, credits
FROM users
WHERE type = 'User';
```

**Check Lead Assignments:**
```sql
SELECT ul.*, u.name, u.is_verified
FROM user_leads ul
JOIN users u ON u.id = ul.user_id;
```

**Check Preferences:**
```sql
SELECT up.user_id, u.name, up.property_types, up.min_units, up.max_units
FROM user_preferences up
JOIN users u ON u.id = up.user_id;
```

---

## Part 9: Frontend Testing (Next.js)

```bash
cd /Users/ravi/Documents/gemini_projects/propertifi/propertifi-frontend/nextjs-app

# Install
npm install

# Configure
echo "NEXT_PUBLIC_API_URL=http://localhost:8000/api" > .env.local

# Start
npm run dev
```

Visit `http://localhost:3000` and test:
- PM listings show verified badges
- Lead form submission
- Calculators work
- Dashboard shows data

---

## Troubleshooting

### Issue: Migration Fails

```bash
# Reset database
php artisan migrate:fresh

# Check status
php artisan migrate:status
```

### Issue: API Returns 500

```bash
# Check logs
tail -f storage/logs/laravel.log

# Clear cache
php artisan cache:clear
php artisan config:clear
```

### Issue: Lead Distribution Not Working

```bash
php artisan tinker
```

```php
// Debug
echo "PMs: " . App\Models\User::where('type', 'User')->count() . "\n";
echo "Leads: " . App\Models\Lead::count() . "\n";
echo "Zipcodes: " . DB::table('user_zipcodes')->count() . "\n";
exit;
```

---

## Success Checklist

- ✅ Server starts without errors
- ✅ Migrations complete successfully
- ✅ API endpoints return JSON
- ✅ PM API includes `is_verified` field
- ✅ Owner can register/login
- ✅ Lead distribution runs
- ✅ Admin verification UI works
- ✅ Frontend connects to API

---

## Quick Test Commands

```bash
# Start everything
php artisan serve                    # Backend (terminal 1)
cd nextjs-app && npm run dev         # Frontend (terminal 2)
tail -f storage/logs/laravel.log     # Logs (terminal 3)

# Test API
curl http://localhost:8000/api/property-managers
curl http://localhost:8000/api/questions

# Run distribution
php artisan LeadDistribute:check

# Check database
mysql -u root -p propertifi -e "SELECT COUNT(*) FROM user_leads"
```

---

## Test Credentials

```
Admin:
  Email: admin@test.com
  Password: password123
  URL: http://localhost:8000/admin

Owner:
  Email: owner@test.com
  Password: password123

PM:
  Email: pm1@test.com
  Password: password123
```

---

**Ready to test! 🚀**

Start with the Quick Start section and work through each part.
