<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Email</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            width: 100%;
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header {
            background-color: #007bff;
            color: #ffffff;
            padding: 10px 20px;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
            text-align: center;
        }
        .content {
            padding: 20px;
            line-height: 1.6;
            color: #333333;
        }
        .content p {
            margin-bottom: 15px;
        }
        .content strong {
            color: #0056b3;
        }
        .footer {
            text-align: center;
            padding: 10px;
            font-size: 0.9em;
            color: #777777;
            border-bottom-left-radius: 8px;
            border-bottom-right-radius: 8px;
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Laravel Queue Test Email</h2>
        </div>
        <div class="content">
            <p>Hello <strong>{{ $userTestName }}</strong>,</p>
            <p>This is a test email sent from a Laravel application using a queued job.</p>
            <p><strong>Your Message:</strong></p>
            <p><em>{{ $testMessage }}</em></p>
            <p>The email was dispatched to the queue and processed successfully.</p>
            <p><strong>Sent at:</strong> {{ $sentTestAt }}</p>
            <p>If you received this, it means:</p>
            <ul>
                <li>The Laravel Mailable class is working.</li>
                <li>The Job class is dispatching correctly.</li>
                <li>The queue worker (Redis) is processing jobs.</li>
                <li>MailHog is capturing emails.</li>
            </ul>
            <p>Thank you for testing!</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} Laravel Test Application</p>
        </div>
    </div>
</body>
</html>
