<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Welcome to Our Newsletter!</title>
</head>
<body>
    <h1>Welcome to Our Newsletter!</h1>

    <p>Hello {{ $user->name }},</p>

    <p>Thank you for subscribing to our newsletter! We're excited to have you on board and keep you updated with the latest news and updates.</p>

    <p>If you have any questions, feel free to reply to this email.</p>

    <p>Thanks,<br>{{ config('app.name') }}</p>
</body>
</html>
