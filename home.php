<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_name = $_SESSION['user_name'];

// Fetch lost items
$lost_items = mysqli_query($conn, "SELECT * FROM Lost_Item ORDER BY Created_At DESC");

// Fetch found items
$found_items = mysqli_query($conn, "SELECT * FROM Found_Item ORDER BY Created_At DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home — Lost & Found</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f0f4f8; }

        /* Navbar */
        .navbar {
            background: #2d3748;
            padding: 14px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: white;
        }
        .navbar h1 { font-size: 20px; }
        .navbar span { font-size: 14px; color: #a0aec0; }
        .nav-links a {
            color: white;
            text-decoration: none;
            margin-left: 20px;
            font-size: 14px;
            background: #4299e1;
            padding: 8px 14px;
            border-radius: 6px;
        }
        .nav-links a:hover { background: #3182ce; }
        .nav-links a.logout { background: #e53e3e; }
        .nav-links a.logout:hover { background: #c53030; }

        /* Container */
        .container { max-width: 1100px; margin: 30px auto; padding: 0 20px; }

        /* Welcome */
        .welcome {
            background: white;
            padding: 20px 24px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        .welcome h2 { color: #2d3748; font-size: 20px; }
        .welcome p  { color: #718096; font-size: 14px; margin-top: 4px; }

        /* Grid */
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; }

        /* Section */
        .section {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            overflow: hidden;
        }
        .section-header {
            padding: 16px 20px;
            font-size: 16px;
            font-weight: bold;
            color: white;
        }
        .lost-header  { background: #e53e3e; }
        .found-header { background: #38a169; }

        /* Item card */
        .item {
            padding: 14px 20px;
            border-bottom: 1px solid #e2e8f0;
        }
        .item:last-child { border-bottom: none; }
        .item h4 { color: #2d3748; font-size: 15px; }
        .item p  { color: #718096; font-size: 13px; margin-top: 4px; }
        .item .meta { font-size: 12px; color: #a0aec0; margin-top: 6px; }
        .no-items { padding: 20px; text-align: center; color: #a0aec0; font-size: 14px; }

        /* Claim button */
        .claim-btn {
            display: inline-block;
            margin-top: 8px;
            padding: 5px 12px;
            background: #4299e1;
            color: white;
            border-radius: 5px;
            font-size: 12px;
            text-decoration: none;
        }
        .claim-btn:hover { background: #3182ce; }
    </style>
</head>
<body>

<!-- Navbar -->
<div class="navbar">
    <h1>🔍 Lost & Found System</h1>
    <div>
        <span>Welcome, <?php echo htmlspecialchars($user_name); ?>!</span>
        <div class="nav-links" style="display:inline">
            <a href="report_lost.php">Report Lost</a>
            <a href="report_found.php">Report Found</a>
            <a href="claim_item.php">Claim Item</a>
            <a href="logout.php" class="logout">Logout</a>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="container">

    <!-- Welcome Box -->
    <div class="welcome">
        <h2>Dashboard</h2>
        <p>View all lost and found items. Report a lost item or claim a found item.</p>
    </div>

    <!-- Grid -->
    <div class="grid">

        <!-- Lost Items -->
        <div class="section">
            <div class="section-header lost-header">🚨 Lost Items</div>
            <?php if (mysqli_num_rows($lost_items) > 0): ?>
                <?php while ($item = mysqli_fetch_assoc($lost_items)): ?>
                <div class="item">
                    <h4><?php echo htmlspecialchars($item['Item_Name']); ?></h4>
                    <p><?php echo htmlspecialchars($item['Description']); ?></p>
                    <div class="meta">
                        📍 <?php echo htmlspecialchars($item['Location']); ?> &nbsp;|&nbsp;
                        📅 <?php echo $item['Lost_Date']; ?>
                    </div>
                    <a href="claim_item.php?lost_id=<?php echo $item['Lost_ID']; ?>" class="claim-btn">Claim This</a>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="no-items">No lost items reported yet.</div>
            <?php endif; ?>
        </div>

        <!-- Found Items -->
        <div class="section">
            <div class="section-header found-header">✅ Found Items</div>
            <?php if (mysqli_num_rows($found_items) > 0): ?>
                <?php while ($item = mysqli_fetch_assoc($found_items)): ?>
                <div class="item">
                    <h4><?php echo htmlspecialchars($item['Item_Name']); ?></h4>
                    <p><?php echo htmlspecialchars($item['Description']); ?></p>
                    <div class="meta">
                        📍 <?php echo htmlspecialchars($item['Location']); ?> &nbsp;|&nbsp;
                        📅 <?php echo $item['Found_Date']; ?>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="no-items">No found items reported yet.</div>
            <?php endif; ?>
        </div>

    </div>
</div>

</body>
</html>