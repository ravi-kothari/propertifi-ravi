# Propertifi - Improvement Plan (v2)

This document outlines potential improvements and new features for the Propertifi application, inspired by competitor analysis and focused on adding value for both Property Owners and Property Managers. Steps are designed to be manageable for implementation assistance from AI tools.

## Phase 1: Foundational Enhancements & Owner Value

### 1. Enhance Lead Generation Form (Property Owner Facing)

* **Goal:** Improve the initial property owner experience.
* **Step 1.1: Implement Multi-Step Form UI:** Convert the existing lead form into a user-friendly multi-step process on the frontend.
    * *(AI Task: Generate Vue/React components for a multi-step form based on `Questions` model/API endpoint.)*
* **Step 1.2: Add Input Validation (API):** Strengthen server-side validation rules in `Api/HomePageController` for the lead submission endpoint.
    * *(AI Task: Analyze `storeLead` method in `Api/HomePageController`, suggest stricter Laravel validation rules based on `Leads` model and potential edge cases.)*
* **Step 1.3: Add Conditional Logic for Questions:** Modify backend/API (`Questions` model/`HomePageController`) and frontend to show/hide questions based on previous answers (e.g., if "Property Type" is "Multi-family," ask different follow-up questions).
    * *(AI Task: Add `depends_on_question_id`, `depends_on_answer` fields to `Questions` model/migration. Update `HomePageController` logic. Update frontend form components.)*

### 2. Implement Structured Legal Resource Center (Public Facing)

* **Goal:** Become an authoritative source for landlord-tenant laws, driving organic traffic. Inspired by `ipropertymanagement.com`.
* **Step 2.1: Data Model & Admin Interface:** Create a new `StateLawContent` model (fields: `state_code`, `topic_slug`, `title`, `summary`, `content`, `official_link`, `last_updated`). Build a dedicated Admin interface (`Admin/StateLawController`) for managing this structured content.
    * *(AI Task: Generate migration and model for `StateLawContent`. Generate `Admin/StateLawController` with CRUD methods and associated Blade views.)*
* **Step 2.2: Public Routes & Views:** Create public routes for `/laws` (state listing/map) and `/laws/{state_slug}` (detailed state law page). 
    * *(AI Task: Create `PublicLawController`. Add routes in `web.php`. Generate Blade views: `laws/index.blade.php` (state list/map) and `laws/show.blade.php` (state detail page).)*
* **Step 2.3: Implement State/Topic Structure:** Design the `laws/show.blade.php` view to display content organized by topic slugs (e.g., Security Deposits, Eviction) consistently across states. Add filtering/navigation within the state page.
    * *(AI Task: Generate Blade structure for `laws/show.blade.php` using `@foreach` loops based on topics fetched from `StateLawContent` for a given state.)*

### 3. Implement Document Template Library (Public Facing)

* **Goal:** Provide downloadable legal forms as a lead magnet and user resource. Inspired by `ipropertymanagement.com`.
* **Step 3.1: Data Model & Admin Interface:** Create `DocumentTemplate` model (fields: `title`, `description`, `state_code` ('Federal' or state), `category`, `file_path`, `is_free`, `requires_signup`). Add Admin CRUD (`Admin/DocumentTemplateController`) for uploading and managing templates.
    * *(AI Task: Generate migration and model for `DocumentTemplate`. Generate `Admin/DocumentTemplateController` with CRUD methods and file upload handling, plus Blade views.)*
* **Step 3.2: Public Routes & Views:** Create public routes for `/templates` (listing/filtering) and download functionality.
    * *(AI Task: Create `PublicTemplateController`. Add routes in `web.php`. Generate `templates/index.blade.php` view with filtering options (state, category). Implement secure download logic in the controller.)*
* **Step 3.3: Implement Gating (Optional):** Add logic to require email signup or user login (for the future Owner Dashboard) to download non-free templates.
    * *(AI Task: Add middleware or controller logic to check `requires_signup` flag and user authentication status before allowing download.)*

### 4. Implement Real Estate Calculators (Public Facing)

* **Goal:** Attract property owners/investors with useful tools.
* **Step 4.1: Create Tools Controller & Routes:** Create `ToolsController` and add routes in `web.php` for calculators (e.g., `/tools/rental-roi`, `/tools/rent-vs-buy`).
    * *(AI Task: Generate `ToolsController` with methods for each calculator. Add routes to `web.php`.)*
* **Step 4.2: Build Calculator Views & Logic:** Create Blade views for each calculator with input forms. Implement calculations using JavaScript.
    * *(AI Task: Generate Blade view for a Rental ROI calculator (`rental-roi.blade.php`) with form inputs (price, rent, expenses) and JavaScript for calculation display.)*
    * *(AI Task: Repeat for Rent vs. Buy and Investment Mortgage calculators.)*

---

## Phase 2: User Dashboards & Platform Maturity

### 5. Implement Property Owner Dashboard

* **Goal:** Create a logged-in experience for property owners to foster engagement and provide personalized value.
* **Step 5.1: Owner Authentication:** Set up authentication for property owners (perhaps triggered after lead submission). Create a distinct "owner" guard or role.
    * *(AI Task: Configure a new auth guard in `config/auth.php`. Create necessary controllers/routes for owner registration/login.)*
* **Step 5.2: Basic Dashboard View:** Create a dashboard view (`owner/dashboard.blade.php`) showing the owner's submitted lead history (`Leads` model relationship).
    * *(AI Task: Create `OwnerDashboardController`. Add route. Generate Blade view showing leads linked to the authenticated owner.)*
* **Step 5.3: Manager Match Visibility:** Display anonymized status of manager matching for their leads (e.g., "Matched with 3 managers," "2 managers viewed").
    * *(AI Task: Modify `OwnerDashboardController` to fetch related `UserLeads` data. Update dashboard view to display counts/basic status.)*
* **Step 5.4: Implement Bookmarking:** Allow owners to save/bookmark specific `StateLawContent` pages and `DocumentTemplate` entries. Display these bookmarks in their dashboard.
    * *(AI Task: Create `OwnerBookmark` model/migration (owner_id, bookmarkable_id, bookmarkable_type). Add bookmark/unbookmark buttons/endpoints. Fetch and display bookmarks in owner dashboard.)*
* **Step 5.5: Saved Calculator Results:** Add functionality for owners to save the inputs/results from the calculators to their dashboard.
    * *(AI Task: Create `SavedCalculation` model/migration (owner_id, calculator_type, inputs_json, results_json). Add "Save" button to calculators that posts data via AJAX. Fetch and display saved calculations in owner dashboard.)*

### 6. Enhance Property Manager (User) Dashboard

* **Goal:** Provide PMs with tools to manage leads effectively and see value.
* **Step 6.1: Dedicated PM Dashboard:** Create a separate dashboard view (`pm/dashboard.blade.php`) distinct from the Admin dashboard.
    * *(AI Task: Create `PmDashboardController`. Add route protected by PM role/middleware. Generate basic Blade view.)*
* **Step 6.2: Lead Status Management:** Allow PMs to update the status of leads assigned to them (`UserLeads` table needs a `status` column: e.g., 'New', 'Contacted', 'Not a Fit', 'Client Won').
    * *(AI Task: Add `status` enum column to `UserLeads` migration. Add dropdown/buttons in PM lead list view to update status via AJAX endpoint.)*
* **Step 6.3: Basic Performance Stats:** Display simple metrics on the PM dashboard (Total Leads, Contact Rate, Win Rate based on the status above).
    * *(AI Task: Add logic to `PmDashboardController` to calculate these stats based on the authenticated PM's `UserLeads`. Display stats in the dashboard view.)*
* **Step 6.4: Lead Notes:** Add a simple notes field (`notes` text column on `UserLeads`) for PMs to track interactions per lead.
    * *(AI Task: Add `notes` column to `UserLeads` migration. Add textarea and save functionality (AJAX) to the PM's lead view.)*

### 7. Improve Lead Matching & Distribution

* **Goal:** Increase lead quality and fairness of distribution.
* **Step 7.1: Add More Preference Factors:** Expand `UserPreferences` model (e.g., `property_types` (JSON array), `min_units`, `max_units`). Update the PM preference setting UI. Modify lead questions (`Questions` model) to capture this info.
    * *(AI Task: Generate migrations for `UserPreferences`. Update `Admin/PreferencesController` and views. Update `Admin/QuestionsController`. Update `Api/HomePageController`.)*
* **Step 7.2: Refine Matching Algorithm (`LeadDistribute.php`):** Update the command to use the new preference factors in the matching query.
    * *(AI Task: Analyze `LeadDistribute.php`. Suggest Eloquent query modifications to incorporate new `UserPreferences` fields against `Leads` data.)*
* **Step 7.3: Implement Lead Cap/Budget:** Add logic in `LeadDistribute.php` to check PM's plan (`Tiers`?) or credit balance (`Credits`?) before assigning leads.
    * *(AI Task: Modify `LeadDistribute.php` query/logic to factor in subscription limits or decrement credits.)*
* **Step 7.4: Tiered Exclusivity:** Modify `LeadDistribute.php` to check PM tier. Send leads to top-tier PMs first, potentially adding a delay before sending to lower tiers (requires `UserLeads` timestamp or similar).
    * *(AI Task: Add logic to `LeadDistribute.php` to query users by tier, potentially use Laravel's job queuing with delays for distribution to lower tiers.)*

### 8. Implement "Verified" Property Manager Program

* **Goal:** Build trust with owners and create a premium offering for PMs.
* **Step 8.1: Add Verification Flag:** Add `is_verified` boolean/timestamp and `verification_documents` (JSON?) fields to `users` table.
    * *(AI Task: Generate migration to add fields to `users` table.)*
* **Step 8.2: Admin Verification UI:** Create an interface in the Admin panel for reviewing submitted documents and marking PMs as verified.
    * *(AI Task: Add section to `Admin/UsersController` edit view for document upload/display. Add action to toggle `is_verified` status.)*
* **Step 8.3: Factor into Matching/Display:** Update `LeadDistribute.php` to prioritize verified PMs. Display a "Verified" badge on PM listings/profiles (requires PM profile feature).
    * *(AI Task: Modify `LeadDistribute.php` query. Add logic to display badge in relevant views.)*

---

## Phase 3: Technical Debt & Long-Term Growth

### 9. Technical Debt & Refinements

* **Step 9.1: Implement Unit/Integration Tests:** Add PHPUnit tests for critical flows (Lead Distribution, Payment, Owner/PM Auth).
    * *(AI Task: Generate basic feature tests for `LeadDistribute` command, `StripePaymentController`, and login/registration controllers.)*
* **Step 9.2: API Security Review:** Ensure all API endpoints (`api.php`, and any new ones for dashboards) use Sanctum or appropriate authentication/authorization.
    * *(AI Task: Review `routes/api.php`. Add `auth:sanctum` middleware where needed. Check controller methods for authorization logic.)*
* **Step 9.3: Query Optimization:** Use tools like Laravel Telescope or Debugbar to identify slow queries, especially in dashboards and `LeadDistribute`. Add database indexes.
    * *(AI Task: Analyze key Eloquent queries. Suggest adding `with()` for eager loading. Suggest relevant database indexes based on `where` clauses.)*
* **Step 9.4: Frontend Asset Bundling:** Ensure proper use of Laravel Mix for compiling and versioning CSS/JS.
    * *(AI Task: Review `webpack.mix.js`. Ensure `.version()` is used for cache busting.)*
* **Step 9.5: Update Dependencies:** Regularly run `composer update` & `npm update`.

### 10. Integrations (Longer Term)

* **Step 10.1: Property Management Software (Display):** Add `pms_used` field to `User` profile.
* **Step 10.2: Legal Document Affiliate Links:** Integrate links in resource/template sections.
* **Step 10.3 (Advanced): Tenant Screening Integration (API):** Partner and integrate API.
* **Step 10.4 (Advanced): Accounting Software Integration (API):** Partner with Stessa, QuickBooks etc. for data export/import.