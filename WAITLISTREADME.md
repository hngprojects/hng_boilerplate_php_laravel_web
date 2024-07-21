## Description

Create a backend API endpoint to receive and store user information submitted through the waitlist signup form on the website.

## Acceptance Criteria

- The endpoint should be accessible at `/api/v1/waitlist`.
- The endpoint should accept `POST` requests to receive user information.
- Given a `POST` request with user data, the system should validate and sanitize the incoming data.

- Validation rules should include:

  - Email format validation.
  - Required fields: email, name.
  - The name should be a non-empty string.

- Given valid user data, the system should securely store the information in the database.

    ![image]()

- Upon successful signup, the system should send a confirmation email to the user.

- Return a `429 Too Many Requests` status with an appropriate error message if the rate limit is exceeded.
- The system should handle errors gracefully and return appropriate status codes.

Request Body

```json
{
  "email": "johndoe@gmail.com",
  "full_name": "John Doe"
}
```
Error Response

```json
{
  "message": "Invalid email format",
  "status_code": 400,
  "error": "Bad Request"
}
```

## Purpose

To allow potential users to join a waitlist for our product/service by submitting their information through a form on the website.

## Requirements

 - Develop a RESTful API endpoint to handle POST requests
 - Validate and sanitize incoming user data
 - Store user information securely in the database
 - Implement rate limiting to prevent abuse
 - Send a confirmation email to the user upon successful signup
 - Handle errors gracefully and return appropriate status codes

## Expected Outcome

A secure and efficient API endpoint that can receive user information, store it in the database and confirm the user's addition to the waitlist.

## Testing

- Test that the endpoint returns a 201 status code with a success message on valid submissions.
- Test that invalid data triggers a 400 Bad Request response with an appropriate error message.
- Test that a confirmation email is sent upon successful signup.
- Test that the rate-limiting works correctly and returns the appropriate status code when exceeded.

* add original repo to remote
git remote add upstream https://github.com/hngprojects/hng_boilerplate_php_laravel_web.git
* fetch from original
git fetch upstream
git checkout dev
git merge upstream/dev
