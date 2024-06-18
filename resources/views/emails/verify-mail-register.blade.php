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
        <h1>Introduction</h1>
        <p>Hello {{ $user->name }}</p>
        <p>This is your account verification email, please activate your account to log in.</p>
        <p>Note, the account activation email is only valid for 60 hours.</p>
        <a href="{{ $url }}" class="button">ACTIVE ACCOUNT</a>
        <p>Thanks,<br>{{ config('app.name') }}</p>
    </div>
</body>
</html>
