<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 Forbidden</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #e3e9f18f;
            color: #333;
            text-align: center;
            padding: 120px;
        }
        .container {
            max-width: 700px;
            margin: 0 auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h2 {
            font-size: 42px;
            margin: 0;
            color: #e74c3c;
        }
        p {
            font-size: 20px;
            font-weight: 600;
            margin: 20px 0;
        }
        span {
            font-size: 14px;
            margin: 16px 0;
        }
        a {
            text-decoration: none;
            color: #3498db;
            font-weight: bold;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Access Denied</h2>
        <p>You do not have permission to access this page/event. </p>
        <span>Please contact your administrator if you believe this is an error.</span>
        {{-- <h4><a href="{{ url('/dashboard') }}">Return to Dashboard</a></h4> --}}
    </div>
</body>
</html>
