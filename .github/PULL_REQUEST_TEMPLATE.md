Here's how you can fill out the pull request template based on the information provided:

---

<!--- Provide a general summary of your changes in the Title above -->
### Add API Endpoint for User Login

## Description
Implemented a new API endpoint for user login. This endpoint accepts user credentials (email and password) and returns a JWT token upon successful authentication. It also handles invalid login attempts and provides appropriate error messages.

## Related Issue (Link to linear ticket)
<!--- This project only accepts pull requests related to open issues -->
<!--- If suggesting a new feature or change, please discuss it in an issue first -->
<!--- If fixing a bug, there should be an issue describing it with steps to reproduce -->
<!--- Please link to the issue here: -->
[Issue #11: Create API Endpoint for User Login](https://github.com/hngprojects/hng_boilerplate_php_laravel_web/issues/11)

## Motivation and Context
This change is required to provide a secure method for users to authenticate themselves and access protected resources. The endpoint ensures proper validation of user credentials and generates a JWT token for authenticated sessions.

## How Has This Been Tested?
- **Unit Tests:** Verified that valid credentials return a JWT token, and invalid credentials or missing parameters return appropriate error responses.
- **Integration Tests:** Ensured correct interaction with the database and proper generation of JWT tokens.
- **Testing Environment:** Laravel application running with a MySQL database.

## Screenshots (if appropriate - Postman, etc):
<!-- Add screenshots from Postman or other tools demonstrating the endpoint in action. -->

## Types of changes
<!--- What types of changes does your code introduce? Put an `x` in all the boxes that apply: -->
- [x] Bug fix (non-breaking change which fixes an issue)
- [x] New feature (non-breaking change which adds functionality)
- [ ] Breaking change (fix or feature that would cause existing functionality to change)

## Checklist:
<!--- Go over all the following points, and put an `x` in all the boxes that apply. -->
<!--- If you're unsure about any of these, don't hesitate to ask. We're here to help! -->
- [x] My code follows the code style of this project.
- [x] My change requires a change to the documentation.
- [x] I have updated the documentation accordingly.
- [x] I have read the **CONTRIBUTING** document.
- [x] I have added tests to cover my changes.
- [x] All new and existing tests passed.

---

Feel free to adjust any of the details to better fit the specifics of your changes.