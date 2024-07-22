Implement endpoint to update notification settings
​
## Description
Implement endpoint to update notification settings. 
Created a table and migrations to allow authenticated users update notifications setting​s.
## Related Issue ([Link to Github issue](https://github.com/hngprojects/hng_boilerplate_php_laravel_web/issues/40))
Endpoint accessible at domain.com/api/v1/notification-settings/{:user_id}

<!--- This project only accepts pull requests related to open issues -->
<!--- If suggesting a new feature or change, please discuss it in an issue first -->
<!--- If fixing a bug, there should be an issue describing it with steps to reproduce -->
([Link to Github Issue]https://github.com/hngprojects/hng_boilerplate_php_laravel_web/issues/40)
​


## How to install and run project
Run the commands below one after the other to setup the project
```
composer install //Run Composer and install

php artisan jwt:generate

cp .env.example .env
php artisan key:generate

php artisan migrate ///Migrates Databases

php artisan test // If test is successful, Tests/Feature/NotificationSettingTest shout return 'PASS' on the terminal

php artisan serve //starts the server and makes project run localy
```


## Motivation and Contex

<!--- Why is this change required? What problem does it solve? -->
​
## How Has This Been Tested?
<!--- Please describe in detail how you tested your changes. -->
<!--- Include details of your testing environment, and the tests you ran to -->
<!--- see how your change affects other areas of the code, etc. -->
​
## Screenshots (if appropriate - Postman, etc):
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
