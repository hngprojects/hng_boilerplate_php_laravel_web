# Contribution Guide

Thank you for considering contributing to [App Name]! We welcome all types of contributions, including bug reports, feature requests, documentation improvements, and code enhancements.

## Contribution Rules

1. **Follow the Code of Conduct:** Please read and adhere to the [Code of Conduct](CODE_OF_CONDUCT.md).

2. **Style Guide:** Ensure your code follows the project's coding style. This includes proper indentation, variable naming conventions, and inline comments where necessary.

3. **Testing:** Make sure your changes are well-tested. Write unit tests for new functionality and run all tests to ensure nothing is broken.

4. **Documentation:** Update or add documentation for any changes made. This includes comments in the code, updating README files, or any other relevant documentation.

5. **Commit Messages:** Write clear, concise commit messages that accurately describe the changes made.

6. **Review Feedback:** Be open to feedback and be ready to make additional changes to your PR based on the review.

---

# Pull Request Template

## Description

Created an API endpoint to handle the deletion of a blog. This endpoint validates the blog ID and update the specified blog status column to 0 to indicate it has been soft deleted on the database upon successful validation.

Fixes # (issue) [FEAT] Blog Post Deletion Endpoint - Backend #6

## Endpoints

/api/v1/blog/{blog_id}

## Type of Change

- [ ] Bug fix (non-breaking change which fixes an issue)
- [X] New feature (non-breaking change which adds functionality)
- [ ] Breaking change (fix or feature that would cause existing functionality to not work as expected)
- [ ] This change requires a documentation update

## Checklist

- [X] My code follows the style guidelines of this project
- [X] I have performed a self-review of my own code
- [X] I have commented my code, particularly in hard-to-understand areas
- [X] I have made corresponding changes to the documentation
- [X] My changes generate no new warnings
- [X] I have added tests that prove my fix is effective or that my feature works
- [X] New and existing unit tests pass locally with my changes
- [X] Any dependent changes have been merged and published in downstream modules

## Screenshots (if appropriate)

## Additional Context

Add any other context or screenshots about the pull request here.
