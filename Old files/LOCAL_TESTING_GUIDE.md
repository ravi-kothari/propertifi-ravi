# Local Testing Guide: Phase 1.1 - Multi-Step Questions API

This guide provides a step-by-step process for setting up and testing the Phase 1.1 Multi-Step Questions API on your local macOS laptop.

## Prerequisites

Before you begin, ensure you have the following installed:

- **PHP**: Version 8.0 or higher (you have 8.4.14 ✅)
- **MySQL**: Version 5.7 or higher
- **Composer**: The latest version (already installed ✅)

To check the versions of your installed software, run:

```bash
php -v
mysql --version
composer --version
```

## Step 1: Environment Setup

First, set up your local environment configuration file.

### 1.1 Copy the `.env.example` file:

```bash
cd /Users/ravi/Documents/gemini_projects/propertifi/propertifi-backend
cp .env.example .env
```

### 1.2 Generate the application key:

```bash
php artisan key:generate
```

**Expected output:**
```
Application key set successfully.
```

## Step 2: Database Setup

Create a MySQL database for the application.

### 2.1 Start MySQL (if not already running):

If you're using Homebrew MySQL:
```bash
brew services start mysql
```

Or if using MAMP, start MAMP and ensure MySQL is running.

### 2.2 Create the database:

**Option A: Using MySQL CLI:**

```bash
mysql -u root -p
```

Then run:
```sql
CREATE DATABASE propertifi;
EXIT;
```

**Option B: Using phpMyAdmin:**
- Navigate to `http://localhost/phpMyAdmin`
- Click "New" to create a database
- Name it `propertifi`
- Click "Create"

### 2.3 Update your `.env` file:

Open `.env` and update these database settings:

```ini
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=propertifi
DB_USERNAME=root
DB_PASSWORD=your_mysql_password
```

**Important:** Replace `your_mysql_password` with your actual MySQL password. If you don't have a password, leave it empty.

## Step 3: Run Migrations and Seeders

Now, create the database tables and populate them with initial data.

### 3.1 Run the migrations:

```bash
php artisan migrate --force
```

**Expected output:**
```
Migration table created successfully.
Migrating: 2025_10_28_063238_create_questions_table
Migrated:  2025_10_28_063238_create_questions_table (XX.XXms)
```

### 3.2 Run the QuestionSeeder:

```bash
php artisan db:seed --class=QuestionSeeder
```

**Expected output:**
```
Seeding: Database\Seeders\QuestionSeeder
Successfully seeded 16 questions across 5 steps.
Seeded:  Database\Seeders\QuestionSeeder (XX.XXms)
```

## Step 4: Start Laravel Development Server

Start the Laravel development server to make the API accessible.

### 4.1 Start the server:

```bash
php artisan serve
```

### 4.2 Verify it's running:

**Expected output:**
```
Starting Laravel development server: http://127.0.0.1:8000
[Mon Oct 27 18:00:00 2025] PHP 8.4.14 Development Server (http://127.0.0.1:8000) started
```

✅ Your API is now running at `http://localhost:8000`

**Keep this terminal window open!** The server must be running for the API to work.

## Step 5: Test with Browser (Simple GET Requests)

Open a new terminal window for testing (keep the server running in the first window).

You can test simple GET requests directly in your browser:

### 5.1 Get all questions:
Open in browser: [http://localhost:8000/api/questions](http://localhost:8000/api/questions)

**What you should see:**
JSON response with questions grouped by step numbers (1, 2, 3, 4, 5).

### 5.2 Get questions for step 1:
Open in browser: [http://localhost:8000/api/questions/step/1](http://localhost:8000/api/questions/step/1)

**What you should see:**
JSON array with 1 question (the property type question).

### 5.3 Get questions for step 2:
Open in browser: [http://localhost:8000/api/questions/step/2](http://localhost:8000/api/questions/step/2)

**What you should see:**
JSON array with 4 questions (address, city, state, ZIP).

## Step 6: Test with curl (Terminal Commands)

Open a new terminal window and run these commands:

### 6.1 GET all questions:

```bash
curl -X GET http://localhost:8000/api/questions
```

**Expected response:**
```json
{
  "success": true,
  "data": {
    "1": [...],
    "2": [...],
    "3": [...],
    "4": [...],
    "5": [...]
  }
}
```

### 6.2 GET questions by step:

```bash
curl -X GET http://localhost:8000/api/questions/step/1
```

**Expected response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "question_text": "What type of property do you manage?",
      "input_type": "radio",
      "options": [
        {"value": "residential", "label": "Residential"},
        {"value": "commercial", "label": "Commercial"},
        {"value": "mixed", "label": "Mixed Use"},
        {"value": "hoa", "label": "HOA/Community Association"}
      ],
      "step": 1,
      "is_required": true,
      ...
    }
  ]
}
```

### 6.3 POST create a new question (Admin API):

```bash
curl -X POST http://localhost:8000/admin/form-questions \
  -H "Content-Type: application/json" \
  -d '{
    "question_text": "Do you have parking available?",
    "input_type": "radio",
    "options": [
      {"value": "yes", "label": "Yes"},
      {"value": "no", "label": "No"}
    ],
    "step": 3,
    "order_in_step": 10,
    "is_required": false,
    "is_active": true
  }'
```

**Expected response:**
```json
{
  "success": true,
  "message": "Question created successfully",
  "data": {
    "id": 17,
    "question_text": "Do you have parking available?",
    ...
  }
}
```

### 6.4 PUT update a question:

```bash
curl -X PUT http://localhost:8000/admin/form-questions/17 \
  -H "Content-Type: application/json" \
  -d '{
    "question_text": "Is parking available on the property?",
    "is_required": true
  }'
```

**Expected response:**
```json
{
  "success": true,
  "message": "Question updated successfully",
  "data": {
    "id": 17,
    "question_text": "Is parking available on the property?",
    "is_required": true,
    ...
  }
}
```

### 6.5 DELETE a question:

```bash
curl -X DELETE http://localhost:8000/admin/form-questions/17
```

**Expected response:**
```json
{
  "success": true,
  "message": "Question deleted successfully"
}
```

## Step 7: Test with Database Queries

Verify the data directly in your MySQL database.

### 7.1 Log in to MySQL:

```bash
mysql -u root -p propertifi
```

### 7.2 Verify the number of questions:

```sql
SELECT COUNT(*) FROM questions;
```

**Expected result:** `16`

### 7.3 Check questions per step:

```sql
SELECT step, COUNT(*) as count FROM questions GROUP BY step;
```

**Expected result:**
```
+------+-------+
| step | count |
+------+-------+
|    1 |     1 |
|    2 |     4 |
|    3 |     3 |
|    4 |     4 |
|    5 |     3 |
+------+-------+
```

### 7.4 View all questions for step 1:

```sql
SELECT id, question_text, input_type, step FROM questions WHERE step = 1;
```

### 7.5 Check conditional questions (if any):

```sql
SELECT id, question_text, depends_on_question_id, depends_on_answer
FROM questions
WHERE depends_on_question_id IS NOT NULL;
```

### 7.6 Exit MySQL:

```sql
EXIT;
```

## Step 8: Advanced Testing (Optional)

For a better testing experience, you can use GUI tools:

### Using Postman:
1. Download from [postman.com](https://www.postman.com/downloads/)
2. Create a new request
3. Set method to GET
4. Enter URL: `http://localhost:8000/api/questions`
5. Click Send

### Using Insomnia:
1. Download from [insomnia.rest](https://insomnia.rest/download)
2. Create a new HTTP Request
3. Enter URL and click Send

### Using TablePlus (for database):
1. Download from [tableplus.com](https://tableplus.com/)
2. Connect to MySQL
3. Browse the `questions` table visually

## Step 9: Test Complete Flow

Here's a complete test scenario from start to finish:

### 9.1 Fetch all questions (as frontend would):

```bash
curl http://localhost:8000/api/questions
```

### 9.2 Create a conditional question:

```bash
curl -X POST http://localhost:8000/admin/form-questions \
  -H "Content-Type: application/json" \
  -d '{
    "question_text": "What type of commercial property?",
    "input_type": "text",
    "step": 1,
    "order_in_step": 10,
    "is_required": true,
    "is_active": true,
    "depends_on_question_id": 1,
    "depends_on_answer": "commercial"
  }'
```

### 9.3 Verify the conditional question appears:

```bash
curl http://localhost:8000/api/questions/step/1
```

You should see the new question with its `parent_question` field populated.

### 9.4 Reorder questions in step 2:

```bash
curl -X POST http://localhost:8000/admin/form-questions/reorder \
  -H "Content-Type: application/json" \
  -d '{
    "step": 2,
    "question_orders": [
      {"id": 4, "order_in_step": 0},
      {"id": 3, "order_in_step": 1},
      {"id": 2, "order_in_step": 2},
      {"id": 5, "order_in_step": 3}
    ]
  }'
```

## Troubleshooting

### Issue: Port already in use

**Error:** `Failed to listen on 127.0.0.1:8000`

**Solution:**
```bash
php artisan serve --port=8080
```

Then use `http://localhost:8080` instead of `8000` in all URLs.

### Issue: Database connection refused

**Error:** `SQLSTATE[HY000] [2002] Connection refused`

**Solutions:**
1. Make sure MySQL is running:
   ```bash
   brew services list
   ```
2. Check your `.env` credentials are correct
3. Try connecting manually:
   ```bash
   mysql -u root -p
   ```

### Issue: Migration fails

**Error:** `Base table or view already exists`

**Solution:**
Reset the database:
```bash
php artisan migrate:fresh --seed
```

⚠️ This will delete all data and re-create tables!

### Issue: Seeder fails

**Error:** `Class "QuestionSeeder" not found`

**Solution:**
```bash
composer dump-autoload
php artisan db:seed --class=QuestionSeeder
```

### Issue: 404 Not Found on API calls

**Possible causes:**
1. Server not running - check the terminal where you ran `php artisan serve`
2. Wrong URL - make sure you're using `http://localhost:8000` (not `8080` or other port)
3. Wrong route - check `routes/api.php` for correct endpoints

### Issue: CORS errors (if testing from frontend)

**Solution:**
The Laravel CORS package is already installed. Make sure `config/cors.php` allows localhost:3000 (for Next.js).

## Quick Reference

### Important URLs:

- **Base API URL:** `http://localhost:8000`
- **Get all questions:** `http://localhost:8000/api/questions`
- **Get step 1 questions:** `http://localhost:8000/api/questions/step/1`
- **Admin questions list:** `http://localhost:8000/admin/form-questions`

### Common Commands Cheat Sheet:

```bash
# Start server
php artisan serve

# Run migrations
php artisan migrate

# Run seeder
php artisan db:seed --class=QuestionSeeder

# Reset database (⚠️ deletes data)
php artisan migrate:fresh --seed

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# View routes
php artisan route:list | grep question
```

### Database Quick Queries:

```sql
-- Count all questions
SELECT COUNT(*) FROM questions;

-- Questions by step
SELECT step, COUNT(*) FROM questions GROUP BY step;

-- View step 1 questions
SELECT * FROM questions WHERE step = 1;

-- Check conditional questions
SELECT id, question_text, depends_on_question_id
FROM questions
WHERE depends_on_question_id IS NOT NULL;
```

## Success Checklist

Run through this checklist to ensure everything is working:

- [ ] Database created and connected
- [ ] Migration ran successfully (16 questions in DB)
- [ ] Laravel server is running on port 8000
- [ ] Browser shows JSON at `http://localhost:8000/api/questions`
- [ ] Can fetch questions by step (1-5)
- [ ] Can create a new question via curl
- [ ] Can update an existing question
- [ ] Can delete a question
- [ ] Database queries show correct data
- [ ] No errors in Laravel server logs

## Next Steps

Once local testing is complete:

1. ✅ Backend API is ready
2. 🔄 Integrate with Next.js frontend at `http://localhost:3000`
3. 🔄 Update frontend to fetch questions from `/api/questions`
4. 🔄 Implement conditional question display logic in frontend
5. 🔄 Test end-to-end form submission

---

**Need Help?**

Check the main testing guide: `testing_guide_phase1.md`

For Laravel documentation: [laravel.com/docs/8.x](https://laravel.com/docs/8.x)
