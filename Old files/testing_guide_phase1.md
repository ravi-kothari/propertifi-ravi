# Testing Guide: Phase 1.1 - Multi-Step & Conditional Lead Form (Backend)

## 1. Introduction

This document provides a comprehensive guide for testing the backend implementation of the multi-step and conditional lead form. The feature includes a database structure for storing questions, public API endpoints to fetch questions for the frontend, and a full suite of admin CRUD endpoints for managing these questions.

**Key Features Tested:**
-   Database schema for questions.
-   Seeding of initial questions.
-   Public API for retrieving questions by step.
-   Admin API for Create, Read, Update, Delete, and Reorder operations on questions.
-   Conditional logic (a question's visibility based on another's answer).
-   Validation and error handling.

## 2. Prerequisites and Setup

Before starting, ensure your environment is correctly configured.

### 2.1. Database Configuration

1.  Make sure you have a copy of the `.env` file from `.env.example`.
2.  Verify that your database credentials are correct in the `.env` file:
    ```ini
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=propertifi
    DB_USERNAME=root
    DB_PASSWORD=
    ```

### 2.2. Run Database Migrations

The new `questions` table needs to be created. Run the following command from the `propertifi-backend` directory:

```bash
php artisan migrate
```

**Expected Output:**
You should see the `2025_10_28_063238_create_questions_table` migration being run successfully.

### 2.3. Run Database Seeder

The system includes a seeder with 16 initial questions across 5 steps. Run the seeder to populate the database:

```bash
php artisan db:seed --class=QuestionSeeder
```

**Expected Output:**
You should see a confirmation that the `QuestionSeeder` was run successfully.

## 3. Database Verification Tests

After seeding, verify the data directly in the database.

1.  **Check Total Question Count:**
    ```sql
    SELECT COUNT(*) FROM questions;
    ```
    *Expected Result:* `16`

2.  **Check Question Count Per Step:**
    ```sql
    SELECT step, COUNT(*) as question_count FROM questions GROUP BY step;
    ```
    *Expected Result:* A list of steps (1 through 5) and the count of questions for each.

3.  **Inspect a Single Question:**
    ```sql
    SELECT * FROM questions WHERE id = 1;
    ```
    *Expected Result:* You should see the full data for the first question, including properly formatted JSON in the `options` and `validation_rules` columns.

## 4. Public API Testing

These endpoints are for the frontend to consume.

### 4.1. GET `/api/questions` - Get All Questions

This endpoint should return all active questions, grouped by their `step`.

```bash
curl -X GET http://localhost:8000/api/questions
```

**Expected Result:**
A JSON object where keys are step numbers (`"1"`, `"2"`, etc.) and values are arrays of question objects for that step. The questions within each step should be sorted by their `order_in_step`.

### 4.2. GET `/api/questions/step/{step}` - Get Questions for a Specific Step

Test fetching questions for each step individually.

**Step 1:**
```bash
curl -X GET http://localhost:8000/api/questions/step/1
```
*Expected Result:* An array of question objects belonging to step 1.

**Step 3:**
```bash
curl -X GET http://localhost:8000/api/questions/step/3
```
*Expected Result:* An array of question objects belonging to step 3.

**Invalid Step:**
```bash
curl -X GET http://localhost:8000/api/questions/step/99
```
*Expected Result:* An empty JSON array `[]`.

## 5. Admin API Testing

These endpoints require authentication/authorization in a real application, but for this phase, they are open for testing.

### 5.1. GET `/admin/form-questions` - List All Questions

```bash
curl -X GET http://localhost:8000/admin/form-questions
```
*Expected Result:* A JSON array of all 16 question objects.

**With Filters:**
Test the filtering functionality.

*Filter by step:*
```bash
curl -X GET "http://localhost:8000/admin/form-questions?step=2"
```
*Expected Result:* An array of questions where `step` is 2.

*Filter by active status:*
```bash
curl -X GET "http://localhost:8000/admin/form-questions?is_active=1"
```
*Expected Result:* An array of all questions where `is_active` is true.

### 5.2. POST `/admin/form-questions` - Create Question

```bash
curl -X POST http://localhost:8000/admin/form-questions \
-H "Content-Type: application/json" \
-d '{
    "question_text": "What is your estimated budget?",
    "input_type": "select",
    "options": ["$100,000 - $200,000", "$200,000 - $300,000", "$300,000+"],
    "help_text": "Select the range that best fits your budget.",
    "placeholder": "Choose your budget",
    "step": 1,
    "order_in_step": 10,
    "is_required": true,
    "validation_rules": ["required"],
    "is_active": true
}'
```
*Expected Result:* A JSON object of the newly created question with an ID (e.g., 17). Verify this new question appears in the database.

### 5.3. GET `/admin/form-questions/{id}` - Show Question

```bash
curl -X GET http://localhost:8000/admin/form-questions/17
```
*Expected Result:* The full JSON object for the question with ID 17.

### 5.4. PUT `/admin/form-questions/{id}` - Update Question

```bash
curl -X PUT http://localhost:8000/admin/form-questions/17 \
-H "Content-Type: application/json" \
-d '{
    "question_text": "What is your estimated home budget?",
    "step": 2,
    "is_required": false
}'
```
*Expected Result:* The updated JSON object for question 17. Verify the `question_text`, `step`, and `is_required` fields have changed in the database.

### 5.5. DELETE `/admin/form-questions/{id}` - Delete Question

```bash
curl -X DELETE http://localhost:8000/admin/form-questions/17
```
*Expected Result:* A success message. Verify the question with ID 17 is no longer in the database.

### 5.6. POST `/admin/form-questions/reorder` - Reorder Questions

This test assumes there are at least 3 questions in step 1.

```bash
curl -X POST http://localhost:8000/admin/form-questions/reorder \
-H "Content-Type: application/json" \
-d '{
    "step": 1,
    "question_orders": [
        {"id": 1, "order_in_step": 2},
        {"id": 2, "order_in_step": 0},
        {"id": 3, "order_in_step": 1}
    ]
}'
```
*Expected Result:* A success message. Verify in the database that for `step=1`, the questions have been reordered according to the new `order_in_step` values.

## 6. Conditional Logic Testing

This test ensures a question only appears if a specific answer is given for a previous question.

1.  **Create the Parent Question:**
    Find a question to act as the parent, for example, the "Property Type" question (let's assume its ID is 1 and it's in step 1).

2.  **Create the Dependent Question:**
    Create a new question that *depends on* the answer to question 1 being "commercial".

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
    *Expected Result:* A new question is created (e.g., ID 18) with `depends_on_question_id=1` and `depends_on_answer="commercial"`.

3.  **Test the Public API:**
    Fetch the questions for step 1.

    ```bash
    curl -X GET http://localhost:8000/api/questions/step/1
    ```
    *Expected Result:* The response should contain question 1, and the new dependent question (ID 18) with its `parent_question` field populated. The frontend will be responsible for showing it based on the user's selection.

## 7. Validation and Error Testing

1.  **Create Question with Missing Required Fields:**
    ```bash
    curl -X POST http://localhost:8000/admin/form-questions \
    -H "Content-Type: application/json" \
    -d '{"input_type": "text"}'
    ```
    *Expected Result:* A 422 Unprocessable Entity response with JSON detailing the missing fields (`question_text`, `step`, `order_in_step`).

2.  **Create Question with Invalid `input_type`:**
    ```bash
    curl -X POST http://localhost:8000/admin/form-questions \
    -H "Content-Type: application/json" \
    -d '{
        "question_text": "Invalid Type Test",
        "input_type": "invalid_enum_value",
        "step": 1,
        "order_in_step": 0
    }'
    ```
    *Expected Result:* A 422 response indicating the `input_type` is invalid.

3.  **Update Question with Non-existent `depends_on_question_id`:**
    ```bash
    curl -X PUT http://localhost:8000/admin/form-questions/1 \
    -H "Content-Type: application/json" \
    -d '{"depends_on_question_id": 9999}'
    ```
    *Expected Result:* A 422 response indicating the selected `depends_on_question_id` does not exist.

4.  **Create Circular Dependency:**
    ```bash
    curl -X PUT http://localhost:8000/admin/form-questions/1 \
    -H "Content-Type: application/json" \
    -d '{"depends_on_question_id": 1}'
    ```
    *Expected Result:* A 422 response with message "A question cannot depend on itself".

## 8. Edge Cases

1.  **Delete a Parent Question:** Try to delete a question that another question depends on (e.g., delete question ID 1 from the conditional logic test). The database schema has a foreign key constraint set to `on delete set null`, so the `depends_on_question_id` of the child should become `NULL`. Verify this behavior.

2.  **Delete Question with Dependencies:** Try to delete a question that has dependent questions. The API should prevent deletion and return an error message indicating how many questions depend on it.

3.  **Reorder with Dependencies:** Reorder the questions in step 1 from the conditional logic test. The logic should not break.

4.  **Empty Options:** Create a `select` or `multiselect` question with an empty `options` array. The API should handle this gracefully.

5.  **Large Text:** Create a question with very long `question_text` and `help_text` to ensure there are no database column length issues.

## 9. Performance Testing Tips

While full-scale load testing isn't required, it's good to be mindful of performance.

-   The `GET /api/questions` endpoint is the most critical for frontend performance. Use a tool like Apache Bench (`ab`) to get a baseline response time.
    ```bash
    # Example: 100 requests, 10 concurrent
    ab -n 100 -c 10 http://localhost:8000/api/questions
    ```
-   If response times are slow, consider adding database indexes to the `step`, `is_active`, and `order_in_step` columns on the `questions` table (indexes are already included in the migration).

## 10. Final Checklist

-   [ ] Environment setup complete (`.env`, migrations, seeder).
-   [ ] Database verification passed (16 questions created).
-   [ ] Public API `GET /api/questions` works as expected.
-   [ ] Public API `GET /api/questions/step/{step}` works for all steps (1-5).
-   [ ] Admin API `GET` (list and filter) works.
-   [ ] Admin API `POST` (create) works with valid data.
-   [ ] Admin API `GET` (show) works for specific question.
-   [ ] Admin API `PUT` (update) works.
-   [ ] Admin API `DELETE` works.
-   [ ] Admin API `POST` (reorder) works.
-   [ ] Conditional logic is correctly stored and associated.
-   [ ] Validation for required fields and invalid data is working.
-   [ ] Edge cases (deleting parents, circular dependencies, large text) are handled gracefully.
-   [ ] Error responses return proper HTTP status codes (422 for validation, 404 for not found, 500 for server errors).

## 11. Integration with Next.js Frontend

Once backend testing is complete, the Next.js frontend can integrate with these endpoints:

1. **Fetch questions for multi-step form:**
   - Use `GET /api/questions` to get all questions grouped by step
   - Or use `GET /api/questions/step/{step}` to fetch questions progressively as user navigates

2. **Display conditional questions:**
   - Frontend should check `depends_on_question_id` and `depends_on_answer` fields
   - Show/hide questions based on user's answers to parent questions

3. **Submit completed form:**
   - Use the existing `POST /api/home-page-lead` endpoint with all answers

## 12. Notes for Developers

- All API responses follow the format: `{"success": true/false, "data": {...}, "message": "..."}`
- The `options` field is stored as JSON and supports both array and object formats
- The `validation_rules` field stores Laravel validation rules as JSON array
- Questions are automatically ordered by `step` and `order_in_step` in all API responses
- The `parent_question` field in API responses provides denormalized data for easier frontend consumption
