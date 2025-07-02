<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Forbidden</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin-top: 20%;
            color: #333;
        }
        h1 {
            font-size: 3em;
            color: #e74c3c;
        }
        p {
            font-size: 1.5em;
        }
        a {
            text-decoration: none;
            color: #3498db;
            font-size: 1.2em;
        }
    </style>
</head>
<body>
    <h1>403 - Forbidden</h1>
    <p>You do not have permission to access this page.</p>
    <a href="{{ url('/') }}">Return to Homepage</a>
</body>
</html>