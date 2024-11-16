<!DOCTYPE html>
<html>
<head>
    <title>Welcome Student</title>
</head>
<body>
<h1>Welcome, {{ $username }}!</h1>
<p>We are excited to have you on board. Here are your login credentials:</p>
<ul>
    <li><strong>Email:</strong> {{ $email }}</li>
    <li><strong>Password:</strong> {{ $password }}</li>
</ul>
<p>For security reasons, please change your password after logging in.</p>
<p><a href="{{ url('/admin/login') }}">Click here to log in</a></p>
</body>
</html>
