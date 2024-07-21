<!--- Provide a general summary of your changes in the Title above -->
​
## Description
<!--- Describe your changes in detail -->
I have implemented a TestimonialController with a store method to handle the submission of user testimonials.
This method processes data received from TestimonialRequest, and saves it to the database..
The controller ensures that only authenticated users can submit testimonials, returning a 401 error
if an unauthorized user attempts to access the method.

​
## Related Issue (Link to Github issue)
<!--- This project only accepts pull requests related to open issues -->
<!--- If suggesting a new feature or change, please discuss it in an issue first -->
<!--- If fixing a bug, there should be an issue describing it with steps to reproduce -->
<!--- Please link to the issue here: -->
- Link to approved github issue:
  https://github.com/hngprojects/hng_boilerplate_nestjs/issues/206
- Link to issue on PHP repo:
  https://github.com/hngprojects/hng_boilerplate_php_laravel_web/issues/39
  
## Motivation and Context
<!--- Why is this change required? What problem does it solve? -->
This feature allows users to provide feedback about their experience with our service. It helps in showcasing user satisfaction and improving our public image.
​
## How Has This Been Tested?
<!--- Please describe in detail how you tested your changes. -->
<!--- Include details of your testing environment, and the tests you ran to -->
Testing was performed using Postman. Although authentication was not implemented at the time of testing, I verified the functionality in a separate project with authentication and confirmed it worked as expected.

Environment: Postman
- Test 1: Verified that only authenticated users can create testimonials.
- Test 2: Ensured that testimonials are correctly saved in the database.
<!--- see how your change affects other areas of the code, etc. -->
​
## Screenshots (if appropriate - Postman, etc):
![Picture shows a test for the testimonial endpoint. A JSON body and a JSON response](/home/abdiel/hng_boilerplate_php_laravel_web/public/create-testimonials.png "Test environment")

​
## Types of changes
<!--- What types of changes does your code introduce? Put an `x` in all the boxes that apply: -->
- [ ] Bug fix (non-breaking change which fixes an issue)
- [x] New feature (non-breaking change which adds functionality)
- [ ] Breaking change (fix or feature that would cause existing functionality to change)
  ​
## Checklist:
<!--- Go over all the following points, and put an `x` in all the boxes that apply. -->
<!--- If you're unsure about any of these, don't hesitate to ask. We're here to help! -->
- [x] My code follows the code style of this project.
- [ ] My change requires a change to the documentation.
- [ ] I have updated the documentation accordingly.
- [x] I have read the **CONTRIBUTING** document.
- [x] I have added tests to cover my changes.
- [x] All new and existing tests passed.
