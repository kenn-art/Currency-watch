<?php
session_start();
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $input = trim($_POST['email_or_username']); // Accepts either email or username
    $password = trim($_POST['password']);

    if (!empty($input) && !empty($password)) {
        $query = "SELECT * FROM users WHERE email = ? OR username = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$input, $input]); // Check both email and username
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];

            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Invalid username/email or password.";
        }
    } else {
        $error = "Please fill in all fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body {
            background-color: #121212;
            color: white;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background: #1e1e1e;
            padding: 20px;
            border-radius: 10px;
            width: 300px;
            text-align: center;
            box-shadow: 0px 0px 10px rgba(255, 255, 255, 0.1);
        }
        input, button {
            width: 90%;
            padding: 10px;
            margin: 10px 0;
            border: none;
            border-radius: 5px;
            font-size: 16px;
        }
        input {
            background: #333;
            color: white;
        }
        button {
            background: #ff4c4c;
            color: white;
            cursor: pointer;
        }
        button:hover {
            background: #e84141;
        }
        a {
            color: #ff4c4c;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Login</h2>

        <?php if (isset($error)) echo "<p style='color: red;'>$error</p>"; ?>

        <form method="POST">
            <input type="text" name="email_or_username" placeholder="Email or Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="login">Login</button>
        </form>

        <p>Don't have an account? <a href="register.php">Register here</a></p>
    </div>
</body>
</html>
