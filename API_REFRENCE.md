# [App Name] Integration API Documentation

## Overview

[Description]

## Base URL

- Live URL: `https://api-php.boilerplate.hng.tech`
- Staging URL: `https://staging.api-php.boilerplate.hng.tech`

## [Section]

### [Endpoint]

#### [SubTitle]

- **Endpoint:** `/api/v1/organizations/{org_id}/users/{user_id}`
- **Method:** DELETE
- **Description:** This endpoint remove member from an organization


**Success Response:**

- **Code:** 200
- **Content:**

```json
{
  "status": "success",
  "message": "user deleted successfully",
  "status_code": 200
}
```

**Error Responses:**

- **Code:** [Status-Code]
- **Content:**

```json
{
  "status": "forbidden",
  "message": "user not found",
  "status_code": 404
}
```

## Versioning

This API is versioned to ensure backward compatibility and easy maintenance. The current version is [version].
