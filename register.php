<?php
session_start();
include 'db_connect.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name     = mysqli_real_escape_string($conn, $_POST['name']);
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $phone    = mysqli_real_escape_string($conn, $_POST['phone']);

    $check = mysqli_query($conn, "SELECT * FROM User WHERE Email='$email'");
    if (mysqli_num_rows($check) > 0) {
        $message = "Email already registered!";
    } else {
        $sql = "INSERT INTO User (Name, Email, Password, Phone) 
                VALUES ('$name', '$email', '$password', '$phone')";
        if (mysqli_query($conn, $sql)) {
            header("Location: login.php?registered=1");
            exit();
        } else {
            $message = "Error: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register — Lost & Found</title>
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
            max-width: 420px;
        }
        h2 { text-align: center; color: #2d3748; margin-bottom: 24px; font-size: 24px; }
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
        .login-link { text-align: center; margin-top: 16px; font-size: 14px; color: #718096; }
        .login-link a { color: #4299e1; text-decoration: none; }
    </style>
</head>
<body>
<div class="card">
    <span class="emoji">🔍</span>
    <h2>Lost & Found System</h2>
    <?php if ($message): ?>
        <p class="msg"><?php echo $message; ?></p>
    <?php endif; ?>
    <form method="POST">
        <input type="text"     name="name"     placeholder="Full Name"     required>
        <input type="email"    name="email"    placeholder="Email Address" required>
        <input type="password" name="password" placeholder="Password"      required>
        <input type="text"     name="phone"    placeholder="Phone Number"  required>
        <button type="submit">Register</button>
    </form>
    <div class="login-link">Already have an account? <a href="login.php">Login here</a></div>
</div>
</body>
</html>