<?php
session_start();
include 'db_connect.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $sql    = "SELECT * FROM User WHERE Email='$email'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        if (password_verify($password, $user['Password'])) {
            $_SESSION['user_id']   = $user['User_ID'];
            $_SESSION['user_name'] = $user['Name'];
            header("Location: home.php");
            exit();
        } else {
            $message = "Wrong password!";
        }
    } else {
        $message = "Email not found!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Lost & Found</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: Arial, sans-serif;
            background: #f0f4f8;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .card {
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
        }
        h2 { text-align: center; color: #2d3748; margin-bottom: 24px; }
        .emoji { font-size: 36px; text-align: center; display: block; margin-bottom: 8px; }
        input {
            width: 100%;
            padding: 12px;
            margin-bottom: 16px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 15px;
            outline: none;
        }
        input:focus { border-color: #4299e1; }
        button {
            width: 100%;
            padding: 13px;
            background: #4299e1;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
        }
        button:hover { background: #3182ce; }
        .msg { color: red; text-align: center; margin-bottom: 12px; font-size: 14px; }
        .success { color: green; text-align: center; margin-bottom: 12px; font-size: 14px; }
        .reg-link { text-align: center; margin-top: 16px; font-size: 14px; color: #718096; }
        .reg-link a { color: #4299e1; text-decoration: none; }
    </style>
</head>
<body>
<div class="card">
    <span class="emoji">🔐</span>
    <h2>Login</h2>
    <?php if (isset($_GET['registered'])): ?>
        <p class="success">Registration successful! Please login.</p>
    <?php endif; ?>
    <?php if ($message): ?>
        <p class="msg"><?php echo $message; ?></p>
    <?php endif; ?>
    <form method="POST">
        <input type="email"    name="email"    placeholder="Email Address" required>
        <input type="password" name="password" placeholder="Password"      required>
        <button type="submit">Login</button>
    </form>
    <div class="reg-link">Don't have an account? <a href="register.php">Register here</a></div>
</div>
</body>
</html>