<!DOCTYPE html>
<html>
<head>
    <title>Magic Link Login</title>
</head>
<body>
    <h1>Magic Link Login</h1>
    <p>Click the link below to log in:</p>
    <a href="{{ url('/api/v1/auth/magic-link/verify/' . $token) }}">Login with Magic Link</a>
    <p>This link will expire in 30 minutes.</p>
</body>
</html>