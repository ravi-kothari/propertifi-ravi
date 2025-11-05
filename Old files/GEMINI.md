
# Propertifi Backend

This is the backend for the Propertifi application. It is a Laravel-based RESTful API.

## Getting Started

1.  **Install dependencies:**
    ```bash
    composer install
    ```
2.  **Create a copy of the `.env.example` file and name it `.env`:**
    ```bash
    cp .env.example .env
    ```
3.  **Generate an application key:**
    ```bash
    php artisan key:generate
    ```
4.  **Configure your database credentials in the `.env` file.**
5.  **Run database migrations:**
    ```bash
    php artisan migrate
    ```
6.  **Start the development server:**
    ```bash
    php artisan serve
    ```
    The backend will be available at `http://localhost:8000`.

## API Endpoints

API routes are defined in `routes/api.php`. The API is not authenticated.
