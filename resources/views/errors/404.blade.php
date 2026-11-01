<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>403 - unauthorized</title>
    <style>
        body {
            background-color: #f8fafc;
            color: #2d3748;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            text-align: center;
            padding-top: 100px;
        }
        h1 {
            font-size: 72px;
            margin-bottom: 10px;
        }
        p {
            font-size: 20px;
            color: #6b7280;
        }
        a {
            color: #3490dc;
            text-decoration: none;
            font-weight: bold;
            margin-top: 20px;
            display: inline-block;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <h1>403</h1>
    <p>You are not authorized to view this page.</p>
    <a href="{{ url('/') }}">← Go back to Home</a>
</body>
</html>
