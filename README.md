<p align="center">
    <img src="http://assets.brunofalcao.me/larapush/github_logo.jpg" alt="Larapush logo" width="300">
</p>

## About Larapush

Larapush is a Laravel library that will allow you to push your codebase using a single artisan command (artisan push), in a secure and transacional way.

### Features




- All communications between your local dev environment and your web server will be made using OAuth Client Access Grants via Laravel Passport. It's not possible for other local computer to hack your codebase without knowing your Oauth keys!
- You can run pre-scripts and post-scripts (Artisan commands, invokable custom classes, or specific custom methods).
- You can specify what codebase you want. What files and folders.
- All upload versions are kept inside your storage folder with a unique transaction folder code (if you want later to restore them).
- Confirmation prompts in case you're uploading to sensitive environments (like production).
