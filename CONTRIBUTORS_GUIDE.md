## Description

- Implement an Api endpoint to update a job post detail, incase of any omission in job details.


## Related Issue (Link to Github issue)
[Related issue to frontend](https://github.com/hngprojects/hng_boilerplate_nestjs/issues/158)
[Related issue for Backend](https://github.com/hngprojects/hng_boilerplate_php_laravel_web/issues/29)


## Endpoints
- api/v1/jobs/:id

## Motivation and Context
Enable updating of a posted job details, incase of any error(typo, etc)
​

## How Has This Been Tested?

- The endpoint is accessible at api/v1/jobs/:id
- The endpoint accept HTTP PATCH requests.
- The endpoint check that the user has the appropriate permissions.
- The endpoint validate all data passed in the body of the request
- Checks if the Requests to the endpoint include a valid authentication token in the Authorization header. Authorization: Bearer
- Given a request with a valid job post ID, when the user is authenticated and authorized, the system update the user with a status code of 200
- Given a request with an invalid or non-existent user ID, when the user is authenticated and authorised, the system return a 404 Not Found status.


## Unit tests
```
    All test cases passed.
```
- Successful response with valid id
- invalid job post id
- Miissing job post id
- invalid request body
- Appropriate status code


## Screenshots (if appropriate)

![alt text](image.png)


## Type of Change

- [ ] Bug fix (non-breaking change which fixes an issue)
- [ ] New feature (non-breaking change which adds functionality)
- [ ] Breaking change (fix or feature that would cause existing functionality to not work as expected)
- [ ] This change requires a documentation update

## Checklist

- [ ] My code follows the style guidelines of this project
- [ ] I have performed a self-review of my own code
- [ ] I have commented my code, particularly in hard-to-understand areas
- [ ] I have made corresponding changes to the documentation
- [ ] My changes generate no new warnings
- [ ] I have added tests that prove my fix is effective or that my feature works
- [ ] New and existing unit tests pass locally with my changes
- [ ] Any dependent changes have been merged and published in downstream modules

