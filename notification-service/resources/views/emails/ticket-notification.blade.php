<!DOCTYPE html>
<html>
<head>
    <title>Notification: {{ ucfirst($type) }}</title>
</head>
<body>
    <h1>New Ticket Notification</h1>
    <p><strong>Type:</strong> {{ $type }}</p>
    <p><strong>Message:</strong> {{ $messageText }}</p>
    <p><strong>Reference ID:</strong> {{ $referenceId }}</p>
    <hr>
    <p>Sent by Notification Service</p>
</body>
</html>
