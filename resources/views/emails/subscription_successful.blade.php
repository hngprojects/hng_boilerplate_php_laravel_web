<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscription Successful</title>
</head>
<body>
    <h1>Thank you for subscribing to {{ $subscriptionPlan->name }}!</h1>
    <p>Your subscription was successful. You have subscribed to the {{ $subscriptionPlan->name }} plan for {{ $subscriptionPlan->duration }}.</p>
</body>
</html>
