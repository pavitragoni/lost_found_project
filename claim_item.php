<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$message = "";
$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $lost_id  = mysqli_real_escape_string($conn, $_POST['lost_id']);
    $found_id = mysqli_real_escape_string($conn, $_POST['found_id']);

    $sql = "INSERT INTO Claim_Request (Lost_ID, Found_ID, User_ID, Status)
            VALUES ('$lost_id', '$found_id', '$user_id', 'Pending')";

    if (mysqli_query($conn, $sql)) {
        $message = "success";
    } else {
        $message = "Error: " . mysqli_error($conn);
    }
}

// Fetch lost items
$lost_items  = mysqli_query($conn, "SELECT * FROM Lost_Item ORDER BY Created_At DESC");

// Fetch found items
$found_items = mysqli_query($conn, "SELECT * FROM Found_Item ORDER BY Created_At DESC");

// Fetch all claims
$claims = mysqli_query($conn, "
    SELECT cr.*, li.Item_Name AS Lost_Name, fi.Item_Name AS Found_Name
    FROM Claim_Request cr
    JOIN Lost_Item li ON cr.Lost_ID = li.Lost_ID
    JOIN Found_Item fi ON cr.Found_ID = fi.Found_ID
    WHERE cr.User_ID = '$user_id'
    ORDER BY cr.Created_At DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Claim Item — Lost & Found</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f0f4f8; }
        .navbar {
            background: #2d3748;
            padding: 14px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: white;
        }
        .navbar h1 { font-size: 20px; }
        .navbar a {
            color: white;
            text-decoration: none;
            background: #4299e1;
            padding: 8px 14px;
            border-radius: 6px;
            font-size: 14px;
        }
        .navbar a:hover { background: #3182ce; }
        .container { max-width: 800px; margin: 30px auto; padding: 0 20px; }
        .card {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            margin-bottom: 24px;
        }
        h2 { color: #2d3748; margin-bottom: 20px; font-size: 20px; }
        label { font-size: 14px; color: #4a5568; margin-bottom: 6px; display: block; }
        select {
            width: 100%;
            padding: 12px;
            margin-bottom: 16px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 15px;
            outline: none;
            background: white;
        }
        select:focus { border-color: #4299e1; }
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
        .success { color: green; text-align: center; margin-bottom: 12px; font-size: 14px; }
        .msg { color: red; text-align: center; margin-bottom: 12px; font-size: 14px; }
        .back-link { text-align: center; margin-top: 16px; font-size: 14px; }
        .back-link a { color: #4299e1; text-decoration: none; }

        /* Claims table */
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th {
            background: #2d3748;
            color: white;
            padding: 12px;
            text-align: left;
            font-size: 14px;
        }
        td { padding: 12px; border-bottom: 1px solid #e2e8f0; font-size: 14px; color: #4a5568; }
        tr:last-child td { border-bottom: none; }
        .status-pending  { color: #d69e2e; font-weight: bold; }
        .status-approved { color: #38a169; font-weight: bold; }
        .status-rejected { color: #e53e3e; font-weight: bold; }
        .no-claims { text-align: center; color: #a0aec0; padding: 20px; font-size: 14px; }
    </style>
</head>
<body>

<!-- Navbar -->
<div class="navbar">
    <h1>🔍 Lost & Found System</h1>
    <a href="home.php">← Back to Home</a>
</div>

<div class="container">

    <!-- Claim Form -->
    <div class="card">
        <h2>📋 Submit Claim Request</h2>
        <?php if ($message == "success"): ?>
            <p class="success">✅ Claim submitted successfully! Status: Pending</p>
        <?php elseif ($message): ?>
            <p class="msg"><?php echo $message; ?></p>
        <?php endif; ?>
        <form method="POST">
            <label>Select Lost Item</label>
            <select name="lost_id" required>
                <option value="">-- Select Lost Item --</option>
                <?php while ($item = mysqli_fetch_assoc($lost_items)): ?>
                    <option value="<?php echo $item['Lost_ID']; ?>">
                        <?php echo htmlspecialchars($item['Item_Name']); ?> — <?php echo htmlspecialchars($item['Location']); ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label>Select Found Item</label>
            <select name="found_id" required>
                <option value="">-- Select Found Item --</option>
                <?php while ($item = mysqli_fetch_assoc($found_items)): ?>
                    <option value="<?php echo $item['Found_ID']; ?>">
                        <?php echo htmlspecialchars($item['Item_Name']); ?> — <?php echo htmlspecialchars($item['Location']); ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <button type="submit">Submit Claim</button>
        </form>
    </div>

    <!-- My Claims -->
    <div class="card">
        <h2>📝 My Claim Requests</h2>
        <?php if (mysqli_num_rows($claims) > 0): ?>
        <table>
            <tr>
                <th>#</th>
                <th>Lost Item</th>
                <th>Found Item</th>
                <th>Status</th>
                <th>Date</th>
            </tr>
            <?php $i = 1; while ($claim = mysqli_fetch_assoc($claims)): ?>
            <tr>
                <td><?php echo $i++; ?></td>
                <td><?php echo htmlspecialchars($claim['Lost_Name']); ?></td>
                <td><?php echo htmlspecialchars($claim['Found_Name']); ?></td>
                <td class="status-<?php echo strtolower($claim['Status']); ?>">
                    <?php echo $claim['Status']; ?>
                </td>
                <td><?php echo date('d M Y', strtotime($claim['Created_At'])); ?></td>
            </tr>
            <?php endwhile; ?>
        </table>
        <?php else: ?>
            <div class="no-claims">No claims submitted yet.</div>
        <?php endif; ?>
    </div>

</div>
</body>
</html>