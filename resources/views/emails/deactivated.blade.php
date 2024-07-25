<!DOCTYPE html>
<html>
<head>
    <title>Account Deactivation Confirmation</title>
</head>
<body>
    <h1>Account Deactivated</h1>
    <p>Hello {{ $user->name }},</p>
    <p>Your account has been successfully deactivated.</p>
    @if($user->deactivation_reason)
        <p>Reason: {{ $user->deactivation_reason }}</p>
    @endif
    <p>Thank you for being with us.</p>
</body>
</html>
