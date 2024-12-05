<?php
session_start();

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate username and password (you may replace this with a database query)
    $valid_username = "admin";
    $valid_password = "admin123";

    // Retrieve username and password from form
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check if username and password match
    if ($username === $valid_username && $password === $valid_password) {
        // Admin credentials are valid, set session variables or redirect to admin dashboard
        $_SESSION['admin_logged_in'] = true;
        header("Location: admin.php");
        exit();
    } else {
        // Invalid credentials, show error message
        echo "<p style='color:red; text-align:center;'>Invalid username or password. Please try again.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Autogate System</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            height: 100vh;
            margin: 0;
            background: url('residentialarea.jpg') no-repeat center center fixed;
            background-size: cover;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .title {
            font-size: 1.3em;
            margin-bottom: 25px;
            color: #fff;
            text-align: center;
            width: 80%;
            text-shadow: -2px -2px 0 #333, 2px -2px 0 #333, -2px 2px 0 #333, 2px 2px 0 #333;
        }

        .container {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            margin-bottom: 60px;
            width: 100%;
            max-width: 400px;
            padding: 20px;
            background-color: rgba(44, 44, 44, 0.9);
            border-radius: 15px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
        }

        .container .login {
            width: 100%;
            padding: 40px 30px;
            color: #fff;
            text-align: center;
        }

        .login input[type="text"],
        .login input[type="password"] {
            width: 90%;
            padding: 14px;
            margin: 10px 0;
            border-radius: 15px;
            border: none;
            background-color: #3c3c3c;
            color: #fff;
            font-size: 0.9em;
        }

        .login input[type="submit"] {
            width: 90%;
            padding: 14px;
            border-radius: 15px;
            border: none;
            font-weight: bold;
            background: linear-gradient(144deg, #af40ff, #5b42f3 50%, #00ddeb);
            color: #fff;
            margin-top: 20px;
            cursor: pointer;
            transition: background 0.4s ease;
        }

        .login input[type="submit"]:hover {
            background: linear-gradient(144deg, #1e1e1e, 20%, #1e1e1e 50%, #1e1e1e);
        }

        .login h1 {
            font-size: 1.8em;
            margin-bottom: 30px;
            margin-top: -20px;
            color: #fff;
            letter-spacing: 1px;
        }

        @media (max-width: 600px) {
            .container {
                max-width: 90%;
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="title">
        <h2>RESIDENTS MANAGEMENT SYSTEM</h2>
    </div>
    <div class="container">
        <div class="login">
            <h1>Admin Login</h1>
            <form action="login.php" method="post">
                <input placeholder="Username" id="username" name="username" type="text" required>
                <input placeholder="Password" id="password" name="password" type="password" required>
                <input value="Login" class="btn" type="submit">
            </form>
        </div>
    </div>
</body>
</html>
