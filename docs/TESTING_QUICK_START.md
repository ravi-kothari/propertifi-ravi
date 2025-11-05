# Backend Testing - Quick Start Guide

**Status:** Ready for Testing
**Time Required:** 15-30 minutes

---

## 🚀 Quick Start (3 Steps)

### 1. Start Backend Server

```bash
cd /Users/ravi/Documents/gemini_projects/propertifi/propertifi-backend
php artisan serve
```

Server will start at: `http://localhost:8000`

---

### 2. Test Authentication

**Register a PM user:**
```bash
curl -X POST http://localhost:8000/api/v2/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test Property Manager",
    "email": "pm@test.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'
```

**Expected Response:**
```json
{
  "access_token": "1|aBcDeFgHiJkLmNoPqRsTuVwXyZ",
  "token_type": "Bearer",
  "user": {
    "id": 1,
    "name": "Test Property Manager",
    "email": "pm@test.com",
    ...
  }
}
```

**SAVE THE TOKEN!** You'll need it for all authenticated requests.

---

### 3. Test Lead Response Submission

**Set your token:**
```bash
export TOKEN="YOUR_TOKEN_FROM_STEP_2"
```

**Submit a contact info response:**
```bash
curl -X POST http://localhost:8000/api/v2/leads/1/responses \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "response_type": "contact_info",
    "message": "I would love to help manage your property!",
    "contact_info": {
      "phone": "5551234567",
      "email": "pm@test.com",
      "preferred_time": "morning",
      "notes": "Available Monday-Friday 9am-5pm"
    }
  }'
```

**Expected Response:**
```json
{
  "success": true,
  "message": "Response submitted successfully",
  "data": {
    "id": 1,
    "lead_id": 1,
    "property_manager_id": 1,
    "response_type": "contact_info",
    "message": "I would love to help...",
    "contact_info": {
      "phone": "5551234567",
      "email": "pm@test.com",
      "notes": "Available Monday-Friday 9am-5pm"
    },
    "created_at": "2025-01-15T...",
    "updated_at": "2025-01-15T..."
  }
}
```

---

## 📋 Complete Test Suite

### Test 1: Authentication Endpoints ✅

**1a. Register:**
```bash
curl -X POST http://localhost:8000/api/v2/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test PM",
    "email": "pm@test.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'
```

**1b. Login:**
```bash
curl -X POST http://localhost:8000/api/v2/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "pm@test.com",
    "password": "password123"
  }'
```

**1c. Get User Profile:**
```bash
curl http://localhost:8000/api/v2/auth/user \
  -H "Authorization: Bearer $TOKEN"
```

**1d. Logout:**
```bash
curl -X POST http://localhost:8000/api/v2/auth/logout \
  -H "Authorization: Bearer $TOKEN"
```

---

### Test 2: Lead Response - Contact Info ✅

```bash
curl -X POST http://localhost:8000/api/v2/leads/1/responses \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "response_type": "contact_info",
    "message": "I would love to help manage your property!",
    "contact_info": {
      "phone": "5551234567",
      "email": "pm@test.com",
      "preferred_time": "morning",
      "notes": "Available Monday-Friday 9am-5pm"
    }
  }'
```

---

### Test 3: Lead Response - Availability 📅

```bash
curl -X POST http://localhost:8000/api/v2/leads/1/responses \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "response_type": "availability",
    "message": "I am available to view the property!",
    "availability": {
      "date": "2025-02-01",
      "time": "10:00",
      "location": "Property address",
      "notes": "Please bring keys and property documents"
    }
  }'
```

---

### Test 4: Lead Response - Price Quote 💰

```bash
curl -X POST http://localhost:8000/api/v2/leads/1/responses \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "response_type": "price_quote",
    "message": "Here is my competitive management quote",
    "price_quote": {
      "amount": 2000,
      "frequency": "monthly",
      "details": "Full-service property management including tenant screening, rent collection, maintenance coordination, and monthly reports",
      "includes": [
        "Tenant screening and placement",
        "Rent collection and disbursement",
        "24/7 maintenance coordination",
        "Monthly financial reports",
        "Annual property inspections"
      ],
      "valid_until": "2025-02-15"
    }
  }'
```

---

### Test 5: Lead Response - Decline 👋

```bash
curl -X POST http://localhost:8000/api/v2/leads/1/responses \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "response_type": "decline",
    "message": "Thank you for considering me, but I am currently at full capacity and cannot take on new properties at this time."
  }'
```

---

### Test 6: Get Response History 📜

```bash
curl http://localhost:8000/api/v2/leads/1/responses \
  -H "Authorization: Bearer $TOKEN"
```

**Expected:** Array of all responses submitted by the authenticated PM for lead #1

---

## 🐛 Troubleshooting

### Problem: "Lead not found" (404)

**Cause:** No lead with ID 1 in database

**Solution:** Create a test lead first, or use the lead distribution system to assign leads to your PM:

```bash
# Create a lead (if endpoint exists)
curl -X POST http://localhost:8000/api/home-page-lead \
  -H "Content-Type: application/json" \
  -d '{
    "property_type": "Single Family",
    "address": "123 Main St",
    "city": "Los Angeles",
    "state": "CA",
    "zipcode": "90001",
    "price": 500000,
    "number_of_units": 1
  }'
```

---

### Problem: "You do not have access to this lead" (403)

**Cause:** PM is not assigned to this lead (no UserLead record)

**Solution:** The lead must be distributed to the PM first via the lead distribution system. Check:

```bash
# Get PM's leads
curl http://localhost:8000/api/v2/property-managers/1/leads \
  -H "Authorization: Bearer $TOKEN"
```

If empty, you need to:
1. Create a UserLead record manually, or
2. Use the lead distribution endpoint to assign leads

---

### Problem: "Unauthenticated" (401)

**Cause:** Missing or invalid token

**Solutions:**
- Make sure you included the `Authorization: Bearer $TOKEN` header
- Check that your token hasn't expired
- Re-login to get a fresh token

---

### Problem: "Validation failed" (422)

**Cause:** Invalid request data

**Solution:** Check the `errors` object in the response for specific field errors:

```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "message": ["The message field is required."],
    "price_quote.amount": ["The amount must be a positive number."]
  }
}
```

Fix the indicated fields and resubmit.

---

## 📊 Expected Database State

After running all tests successfully, you should have:

**users table:**
- 1 user record (PM with email pm@test.com)

**lead_responses table:**
- 4 response records (contact_info, availability, price_quote, decline)

**user_leads table:**
- 1 record with status = 'responded'

---

## ✅ Success Criteria

All tests pass when:
- [x] Can register new user
- [x] Can login and receive token
- [x] Can get user profile with token
- [x] Can submit contact_info response (201 Created)
- [x] Can submit availability response (201 Created)
- [x] Can submit price_quote response (201 Created)
- [x] Can submit decline response (201 Created)
- [x] Can retrieve response history
- [x] Responses are transformed correctly (nested objects)
- [x] Unauthorized requests return 403
- [x] Invalid data returns 422 with error details

---

## 🔄 Reset Database (If Needed)

```bash
php artisan migrate:fresh --seed
```

**WARNING:** This will delete all data!

---

## 📱 Frontend Testing

Once backend tests pass, test with the frontend:

```bash
# Terminal 1: Backend
cd /Users/ravi/Documents/gemini_projects/propertifi/propertifi-backend
php artisan serve

# Terminal 2: Frontend
cd /Users/ravi/Documents/gemini_projects/propertifi/propertifi-frontend/nextjs-app
npm run dev
```

Navigate to: `http://localhost:3000/property-manager`

---

**Good luck with testing! 🚀**
