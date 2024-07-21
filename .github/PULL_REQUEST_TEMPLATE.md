<!--- Provide a general summary of your changes in the Title above -->
​
## Description
<!--- Describe your changes in detail -->


I have implemented a TestimonialController with a store method to handle the submission of user testimonials.
This method processes data received from TestimonialRequest, and saves it to the database.
The controller ensures that only authenticated users can submit testimonials, returning a 401 error
if an unauthorized user attempts to access the method.


### Created an API endpoint to handle the deletion of an organization. This endpoint validate the organization ID and update the specified organization deleted attribute to true in the database upon successful validation.

Created an API endpoint to handle the deletion of an organization. This endpoint validate the organization ID and update the specified organization deleted attribute to true in the database upon successful validation.

## Related Issue (Link to Github issue)
<!--- This project only accepts pull requests related to open issues -->
<!--- If suggesting a new feature or change, please discuss it in an issue first -->
<!--- If fixing a bug, there should be an issue describing it with steps to reproduce -->
<!--- Please link to the issue here: -->
Implemented Organization Deletion
​
## Endpoints
/api/v1/organizations/{org_id}

## Motivation and Context
<!--- Why is this change required? What problem does it solve? -->
It provides a backend service to handle the deletion of organizations, ensuring the organization ID is valid and setting the oganization deleted attribute to true in the database.
​
## How Has This Been Tested?
<!--- Please describe in detail how you tested your changes. -->
<!--- Include details of your testing environment, and the tests you ran to -->
<!--- see how your change affects other areas of the code, etc. -->
the Api has been tested using php units testing and it passed the 3 test it under went.
​
## Screenshots (if appropriate - Postman, etc):
​
## Types of changes
<!--- What types of changes does your code introduce? Put an `x` in all the boxes that apply: -->
- [ ] Bug fix (non-breaking change which fixes an issue) -->
- [x] New feature (non-breaking change which adds functionality)
- [ ] Breaking change (fix or feature that would cause existing functionality to change)
​
## Checklist:
<!--- Go over all the following points, and put an `x` in all the boxes that apply. -->
<!--- If you're unsure about any of these, don't hesitate to ask. We're here to help! -->
- [ ] My code follows the code style of this project.
- [ ] My change requires a change to the documentation.
- [ ] I have updated the documentation accordingly.
- [ ] I have read the **CONTRIBUTING** document.
- [ ] I have added tests to cover my changes.
- [ ] All new and existing tests passed.
