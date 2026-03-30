<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $item_name   = mysqli_real_escape_string($conn, $_POST['item_name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $lost_date   = mysqli_real_escape_string($conn, $_POST['lost_date']);
    $location    = mysqli_real_escape_string($conn, $_POST['location']);
    $user_id     = $_SESSION['user_id'];

    $sql = "INSERT INTO Lost_Item (Item_Name, Description, Lost_Date, Location, User_ID)
            VALUES ('$item_name', '$description', '$lost_date', '$location', '$user_id')";

    if (mysqli_query($conn, $sql)) {
        header("Location: home.php");
        exit();
    } else {
        $message = "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Lost Item</title>
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
            max-width: 480px;
        }
        .emoji { font-size: 36px; text-align: center; display: block; margin-bottom: 8px; }
        h2 { text-align: center; color: #2d3748; margin-bottom: 24px; }
        label { font-size: 14px; color: #4a5568; margin-bottom: 6px; display: block; }
        input, textarea {
            width: 100%;
            padding: 12px;
            margin-bottom: 16px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 15px;
            outline: none;
            font-family: Arial, sans-serif;
        }
        input:focus, textarea:focus { border-color: #e53e3e; }
        textarea { height: 100px; resize: vertical; }
        button {
            width: 100%;
            padding: 13px;
            background: #e53e3e;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
        }
        button:hover { background: #c53030; }
        .msg { color: red; text-align: center; margin-bottom: 12px; font-size: 14px; }
        .back-link { text-align: center; margin-top: 16px; font-size: 14px; }
        .back-link a { color: #4299e1; text-decoration: none; }
    </style>
</head>
<body>
<div class="card">
    <span class="emoji">🚨</span>
    <h2>Report Lost Item</h2>
    <?php if ($message): ?>
        <p class="msg"><?php echo $message; ?></p>
    <?php endif; ?>
    <form method="POST">
        <label>Item Name</label>
        <input type="text" name="item_name" placeholder="e.g. Blue Backpack" required>

        <label>Description</label>
        <textarea name="description" placeholder="Describe the item in detail..."></textarea>

        <label>Date Lost</label>
        <input type="date" name="lost_date" required>

        <label>Location</label>
        <input type="text" name="location" placeholder="e.g. Library, Block A" required>

        <button type="submit">Submit Lost Item</button>
    </form>
    <div class="back-link"><a href="home.php">← Back to Home</a></div>
</div>
</body>
</html>