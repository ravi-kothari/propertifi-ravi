# Authentication API Documentation

This document provides detailed documentation for the authentication API endpoints of Propertifi.

## Base URL

All API endpoints are prefixed with `/api/v2/auth`.

---

## 1. Register

- **Endpoint:** `POST /register`
- **Description:** Registers a new user account.
- **Authentication:** Not required.

### Request Body

| Field | Type | Description | Required |
|---|---|---|---|
| `name` | string | The user's full name. | Yes |
| `email` | string | The user's email address. | Yes |
| `password` | string | The user's desired password. | Yes |
| `password_confirmation` | string | Confirmation of the password. | Yes |

**Example:**
```json
{
  "name": "John Doe",
  "email": "john.doe@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

### Responses

**Success (201 Created)**
```json
{
  "access_token": "1|aBcDeFgHiJkLmNoPqRsTuVwXyZ",
  "token_type": "Bearer",
  "user": {
    "name": "John Doe",
    "email": "john.doe@example.com",
    "updated_at": "2025-10-31T12:00:00.000000Z",
    "created_at": "2025-10-31T12:00:00.000000Z",
    "id": 1
  }
}
```

**Error (422 Unprocessable Entity)** - Validation Error
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "email": [
      "The email has already been taken."
    ]
  }
}
```

---

## 2. Login

- **Endpoint:** `POST /login`
- **Description:** Authenticates a user and returns a session token.
- **Authentication:** Not required.

### Request Body

| Field | Type | Description | Required |
|---|---|---|---|
| `email` | string | The user's email address. | Yes |
| `password` | string | The user's password. | Yes |

**Example:**
```json
{
  "email": "john.doe@example.com",
  "password": "password123"
}
```

### Responses

**Success (200 OK)**
```json
{
  "access_token": "1|aBcDeFgHiJkLmNoPqRsTuVwXyZ",
  "token_type": "Bearer",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john.doe@example.com",
    "email_verified_at": "2025-10-31T12:05:00.000000Z",
    "created_at": "2025-10-31T12:00:00.000000Z",
    "updated_at": "2025-10-31T12:00:00.000000Z"
  }
}
```

**Error (422 Unprocessable Entity)** - Invalid Credentials
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "email": [
      "These credentials do not match our records."
    ]
  }
}
```

**Error (403 Forbidden)** - Email Not Verified
```json
{
  "message": "Please verify your email address."
}
```

---

## 3. Logout

- **Endpoint:** `POST /logout`
- **Description:** Logs out the currently authenticated user.
- **Authentication:** Required (Bearer Token).

### Headers
```
Authorization: Bearer {token}
```

### Responses

**Success (200 OK)**
```json
{
  "message": "Logged out"
}
```

**Error (401 Unauthorized)**
```json
{
  "message": "Unauthenticated."
}
```

---

## 4. Get User Profile

- **Endpoint:** `GET /user`
- **Description:** Retrieves the profile of the currently authenticated user.
- **Authentication:** Required (Bearer Token).

### Headers
```
Authorization: Bearer {token}
```

### Responses

**Success (200 OK)**
```json
{
  "id": 1,
  "name": "John Doe",
  "email": "john.doe@example.com",
  "email_verified_at": "2025-10-31T12:05:00.000000Z",
  "created_at": "2025-10-31T12:00:00.000000Z",
  "updated_at": "2025-10-31T12:00:00.000000Z"
}
```

**Error (401 Unauthorized)**
```json
{
  "message": "Unauthenticated."
}
```

---

## 5. Forgot Password

- **Endpoint:** `POST /forgot-password`
- **Description:** Sends a password reset link to the user's email.
- **Authentication:** Not required.

### Request Body

| Field | Type | Description | Required |
|---|---|---|---|
| `email` | string | The user's email address. | Yes |

**Example:**
```json
{
  "email": "john.doe@example.com"
}
```

### Responses

**Success (200 OK)**
```json
{
  "message": "We have emailed your password reset link."
}
```

**Error (404 Not Found)** - User Not Found
```json
{
  "message": "We can't find a user with that email address."
}
```

---

## 6. Reset Password

- **Endpoint:** `POST /reset-password`
- **Description:** Resets the user's password using the token from the password reset link.
- **Authentication:** Not required.

### Request Body

| Field | Type | Description | Required |
|---|---|---|---|
| `token` | string | The password reset token. | Yes |
| `email` | string | The user's email address. | Yes |
| `password` | string | The new password. | Yes |
| `password_confirmation` | string | Confirmation of the new password. | Yes |

**Example:**
```json
{
  "token": "reset-token-from-email",
  "email": "john.doe@example.com",
  "password": "newpassword123",
  "password_confirmation": "newpassword123"
}
```

### Responses

**Success (200 OK)**
```json
{
  "message": "Your password has been reset."
}
```

**Error (400 Bad Request)** - Invalid Token
```json
{
  "message": "This password reset token is invalid."
}
```

---

## 7. Verify Email

- **Endpoint:** `GET /verify-email/{id}/{hash}`
- **Description:** Verifies the user's email address.
- **Authentication:** Not required.

### URL Parameters

| Parameter | Type | Description |
|---|---|---|
| `id` | integer| The ID of the user to verify. |
| `hash` | string | The verification hash. |

**Example:**
```
GET /api/v2/auth/verify-email/1/abc123def456
```

### Responses

**Success (200 OK)**
```json
{
  "message": "Email successfully verified."
}
```

**Error (200 OK)** - Already Verified
```json
{
  "message": "Email already verified."
}
```

**Error (400 Bad Request)** - Invalid Link
```json
{
  "message": "Invalid verification link."
}
```

---

## 8. Resend Verification Email

- **Endpoint:** `POST /resend-verification`
- **Description:** Resends the email verification link to the authenticated user.
- **Authentication:** Required (Bearer Token).

### Headers
```
Authorization: Bearer {token}
```

### Responses

**Success (200 OK)**
```json
{
  "message": "Verification link sent."
}
```

**Error (401 Unauthorized)**
```json
{
  "message": "Unauthenticated."
}
```

**Error (400 Bad Request)** - Already Verified
```json
{
  "message": "Email already verified."
}
```

---

## Authentication

Protected endpoints require a Bearer token in the Authorization header:

```
Authorization: Bearer {your-access-token}
```

The token is returned from the `/register` and `/login` endpoints.

## Error Handling

All endpoints return appropriate HTTP status codes:

- `200` - Success
- `201` - Resource created
- `400` - Bad request
- `401` - Unauthorized (missing or invalid token)
- `403` - Forbidden (action not allowed)
- `404` - Resource not found
- `422` - Validation error

Validation errors return detailed information about which fields failed validation.
