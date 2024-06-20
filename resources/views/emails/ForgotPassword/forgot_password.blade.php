<!DOCTYPE html>
<html>
<head>
    <title>Email Verification</title>
    <style>
        .button {
            display: inline-block;
            padding: 10px 20px;
            font-size: 16px;
            color: #fff;
            background-color: #28a745;
            text-decoration: none;
            border-radius: 5px;
        }
        .button:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <div>
        <h1>Email Verification</h1>
        <p>Hello {{ $user->name }}</p>
        <p>This is your password reset verification email.</p>
        <p>Your verification code is: <strong>{{ $verificationCode }}</strong></p>
        <p>Note, the password reset code is only valid for 60 hours.</p>
        <p>Thanks,<br>{{ config('app.name') }}</p>
    </div>
</body>
</html>
