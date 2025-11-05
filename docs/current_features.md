# Propertifi - Current Features

This document outlines the features currently implemented in the Propertifi application based on code structure analysis.

#

## Core Lead Generation & Distribution

* **Lead Capture:** API endpoint (`/api/home-page-lead`) to receive lead data (property owner details and answers to questions).
* **Lead Storage:** Stores lead information, including responses to questions (`Leads`, `Responses` models).
* **Question Management:** Admin interface to create/manage questions asked during lead capture (`QuestionsController`, `Questions` model).
* **Lead Distribution Logic:** Automated command (`LeadDistribute`) to match and distribute leads to property managers based on criteria (likely preferences/zip codes).
* **Lead History Tracking:** Records which leads were sent to which users (`UserLeads` model).

## Property Manager (User) Features

* **User Registration & Authentication:** Standard user signup and login (`AdminController` handles login/logout, likely uses Laravel's built-in auth).
* **Profile Management:** Users can update their profile information.
* **Lead Preference Management:** Users can define preferences (e.g., geographic areas via zip codes, potentially other criteria) to filter leads they receive (`PreferencesController`, `UserPreferences`, `UserZipcodes`).
* **Lead Viewing:** Users can likely view the leads assigned to them.
* **Account Activation/Verification:** Email verification and account activation process (`EmailvarificationMail`, `AccountActiveMail`).

## Pricing & Payment

* **Pricing Plans/Tiers:** Admin can define different pricing structures (`PricingController`, `TiersController`).
* **Zip Code Pricing:** Specific pricing likely associated with zip code coverage (`Pricings` model).
* **Stripe Integration:** Handles payments/subscriptions via Stripe (`StripePaymentController`, `PaymentController`).
* **Credit System (Potential):** A system for users to purchase or use credits, possibly for leads (`CreditsController`, `Credits` model).
* **Payment History:** Admin can view payment transactions.

## Admin Panel Features

* **Dashboard:** Overview of key metrics (e.g., leads, users, revenue).
* **User Management:** CRUD operations for users (Property Managers), including viewing details, editing profiles, and managing status. Potential sub-roles like Agents and Account Managers.
* **Lead Management:** View submitted leads, see distribution history, potentially manually assign leads.
* **Pricing Management:** Define and manage pricing plans, tiers, and zip code coverage costs.
* **Preference Management:** View/manage user preferences.
* **Questionnaire Management:** CRUD operations for the lead generation questions.
* **Content Management:**
    * Blogs & Categories (CRUD)
    * FAQs (CRUD)
    * Testimonials (CRUD)
    * Inner/Static Pages (CRUD)
* **Location Management:** Manage Cities/Zip Codes (`CitiesController`, `Zipcodes` model).
* **Settings Management:** Configure application settings (`ProfileController` -> `settings`).
* **Role Management:** Define user roles and permissions (`RolesController`, `AgentRolesController`).
* **Contact Form Submissions:** View messages submitted through a contact form (`ContactController`).
* **Newsletter Subscribers:** Manage newsletter signups (`NewsletterController`).
* **Data Export:** Export functionality for leads, users, payments, etc., likely to CSV.

## Technical Features

* **API for Frontend:** Basic API for lead submission.
* **Scheduled Tasks:** Cron job defined for lead distribution (`Kernel.php`, `LeadDistribute.php`).
* **Email Notifications:** Various emails for user actions (signup, reset password, lead notifications, etc.).
* **Helper Functions:** Custom helper functions (`Helper.php`).
* **Database Migrations:** Standard Laravel migrations for database schema management.



## The application is a monolith with a distinct separation between the backend logic, the admin panel, and the public-facing website, which appears to be a separate frontend application.

1. Backend

Framework: Laravel (PHP)

Evidence: The entire project structure (app/Http/Controllers, app/Models, app/Console), the presence of composer.json, the artisan script, routes/web.php and routes/api.php files, and specific Laravel classes like app/Console/Commands/LeadDistribute.php.

Database: Inferred (Likely MySQL or PostgreSQL)

Evidence: This is the standard database configuration for most Laravel applications. The specific driver is defined in config/database.php.

Task Scheduling: Laravel Task Scheduler (Cron)

Evidence: app/Console/Kernel.php is used to define scheduled tasks like LeadDistribute.

Queue System: Laravel Queues

Evidence: config/queue.php suggests the application is set up to handle asynchronous jobs (like lead distribution or sending emails).

2. Frontend

Public-Facing Website

Framework: Angular

Evidence: This was inferred from the user-provided deployment.md file (not included in this file list, but provided in conversation) which mentioned "Angular files" and "Angular routes" for the existing site. This Angular app communicates with the Laravel backend via the API.

Admin Panel

Templating: Laravel Blade

Evidence: The entire resources/views/admin/ directory consists of .blade.php files.

Styling: Bootstrap

Evidence: The file public/admin/css/bootstrap.css is present.

JavaScript: jQuery

Evidence: The file public/admin/js/jquery-3.6.0.min.js is a core part of the admin assets. The public/admin/vendors/ directory also contains numerous jQuery plugins (e.g., sweetalert2, toastify, ckeditor).

3. Tooling & Services

Asset Bundling: Laravel Mix (Webpack)

Evidence: The presence of the webpack.mix.js file at the root level indicates Laravel Mix is used to compile and bundle frontend assets (CSS, JS).

Payment Processing: Stripe

Evidence: The file app/Http/Controllers/Admin/StripePaymentController.php shows a direct integration for handling payments via Stripe.

Email: Laravel Mail

Evidence: The extensive app/Mail/ directory (e.g., LeadDistributeMail.php, SignupMail.php) and Blade templates in resources/views/mail/ show a standard Laravel mail implementation.