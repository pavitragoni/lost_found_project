<?php
session_start();
include 'db_connect.php';

// Simple admin protection
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Handle claim status update
if (isset($_GET['action']) && isset($_GET['claim_id'])) {
    $claim_id = mysqli_real_escape_string($conn, $_GET['claim_id']);
    $action   = $_GET['action'] == 'approve' ? 'Approved' : 'Rejected';
    mysqli_query($conn, "UPDATE Claim_Request SET Status='$action' WHERE Claim_ID='$claim_id'");
    header("Location: admin.php");
    exit();
}

// Fetch all users
$users = mysqli_query($conn, "SELECT * FROM User ORDER BY Created_At DESC");

// Fetch all lost items
$lost_items = mysqli_query($conn, "SELECT li.*, u.Name AS Reporter FROM Lost_Item li JOIN User u ON li.User_ID = u.User_ID ORDER BY li.Created_At DESC");

// Fetch all found items
$found_items = mysqli_query($conn, "SELECT fi.*, u.Name AS Reporter FROM Found_Item fi JOIN User u ON fi.User_ID = u.User_ID ORDER BY fi.Created_At DESC");

// Fetch all claims
$claims = mysqli_query($conn, "
    SELECT cr.*, u.Name AS Claimant, li.Item_Name AS Lost_Name, fi.Item_Name AS Found_Name
    FROM Claim_Request cr
    JOIN User u ON cr.User_ID = u.User_ID
    JOIN Lost_Item li ON cr.Lost_ID = li.Lost_ID
    JOIN Found_Item fi ON cr.Found_ID = fi.Found_ID
    ORDER BY cr.Created_At DESC
");

// Count stats
$total_users  = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM User"));
$total_lost   = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM Lost_Item"));
$total_found  = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM Found_Item"));
$total_claims = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM Claim_Request"));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel — Lost & Found</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f0f4f8; }

        /* Navbar */
        .navbar {
            background: #1a202c;
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
            margin-left: 10px;
        }
        .navbar a.logout { background: #e53e3e; }

        /* Container */
        .container { max-width: 1100px; margin: 30px auto; padding: 0 20px; }

        /* Stats */
        .stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        .stat-card .number { font-size: 36px; font-weight: bold; margin-bottom: 6px; }
        .stat-card .label  { font-size: 13px; color: #718096; }
        .stat-card.users  .number { color: #4299e1; }
        .stat-card.lost   .number { color: #e53e3e; }
        .stat-card.found  .number { color: #38a169; }
        .stat-card.claims .number { color: #d69e2e; }

        /* Section */
        .section {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            margin-bottom: 24px;
            overflow: hidden;
        }
        .section-header {
            padding: 16px 20px;
            font-size: 16px;
            font-weight: bold;
            color: white;
        }
        .header-blue   { background: #4299e1; }
        .header-red    { background: #e53e3e; }
        .header-green  { background: #38a169; }
        .header-yellow { background: #d69e2e; }

        /* Table */
        table { width: 100%; border-collapse: collapse; }
        th {
            background: #f7fafc;
            padding: 12px 16px;
            text-align: left;
            font-size: 13px;
            color: #4a5568;
            border-bottom: 1px solid #e2e8f0;
        }
        td {
            padding: 12px 16px;
            font-size: 13px;
            color: #4a5568;
            border-bottom: 1px solid #e2e8f0;
        }
        tr:last-child td { border-bottom: none; }
        tr:hover { background: #f7fafc; }

        /* Status */
        .status-pending  { color: #d69e2e; font-weight: bold; }
        .status-approved { color: #38a169; font-weight: bold; }
        .status-rejected { color: #e53e3e; font-weight: bold; }

        /* Action buttons */
        .btn-approve {
            padding: 5px 10px;
            background: #38a169;
            color: white;
            border-radius: 5px;
            font-size: 12px;
            text-decoration: none;
            margin-right: 4px;
        }
        .btn-reject {
            padding: 5px 10px;
            background: #e53e3e;
            color: white;
            border-radius: 5px;
            font-size: 12px;
            text-decoration: none;
        }
        .btn-approve:hover { background: #2f855a; }
        .btn-reject:hover  { background: #c53030; }
        .no-data { padding: 20px; text-align: center; color: #a0aec0; font-size: 14px; }
    </style>
</head>
<body>

<!-- Navbar -->
<div class="navbar">
    <h1>⚙️ Admin Panel — Lost & Found</h1>
    <div>
        <a href="home.php">Home</a>
        <a href="logout.php" class="logout">Logout</a>
    </div>
</div>

<div class="container">

    <!-- Stats -->
    <div class="stats">
        <div class="stat-card users">
            <div class="number"><?php echo $total_users; ?></div>
            <div class="label">Total Users</div>
        </div>
        <div class="stat-card lost">
            <div class="number"><?php echo $total_lost; ?></div>
            <div class="label">Lost Items</div>
        </div>
        <div class="stat-card found">
            <div class="number"><?php echo $total_found; ?></div>
            <div class="label">Found Items</div>
        </div>
        <div class="stat-card claims">
            <div class="number"><?php echo $total_claims; ?></div>
            <div class="label">Claim Requests</div>
        </div>
    </div>

    <!-- Users -->
    <div class="section">
        <div class="section-header header-blue">👤 All Users</div>
        <?php if (mysqli_num_rows($users) > 0): ?>
        <table>
            <tr>
                <th>#</th><th>Name</th><th>Email</th><th>Phone</th><th>Registered</th>
            </tr>
            <?php $i = 1; while ($user = mysqli_fetch_assoc($users)): ?>
            <tr>
                <td><?php echo $i++; ?></td>
                <td><?php echo htmlspecialchars($user['Name']); ?></td>
                <td><?php echo htmlspecialchars($user['Email']); ?></td>
                <td><?php echo htmlspecialchars($user['Phone']); ?></td>
                <td><?php echo date('d M Y', strtotime($user['Created_At'])); ?></td>
            </tr>
            <?php endwhile; ?>
        </table>
        <?php else: ?>
            <div class="no-data">No users registered yet.</div>
        <?php endif; ?>
    </div>

    <!-- Lost Items -->
    <div class="section">
        <div class="section-header header-red">🚨 All Lost Items</div>
        <?php if (mysqli_num_rows($lost_items) > 0): ?>
        <table>
            <tr>
                <th>#</th><th>Item</th><th>Description</th><th>Location</th><th>Date</th><th>Reported By</th>
            </tr>
            <?php $i = 1; while ($item = mysqli_fetch_assoc($lost_items)): ?>
            <tr>
                <td><?php echo $i++; ?></td>
                <td><?php echo htmlspecialchars($item['Item_Name']); ?></td>
                <td><?php echo htmlspecialchars($item['Description']); ?></td>
                <td><?php echo htmlspecialchars($item['Location']); ?></td>
                <td><?php echo $item['Lost_Date']; ?></td>
                <td><?php echo htmlspecialchars($item['Reporter']); ?></td>
            </tr>
            <?php endwhile; ?>
        </table>
        <?php else: ?>
            <div class="no-data">No lost items reported yet.</div>
        <?php endif; ?>
    </div>

    <!-- Found Items -->
    <div class="section">
        <div class="section-header header-green">✅ All Found Items</div>
        <?php if (mysqli_num_rows($found_items) > 0): ?>
        <table>
            <tr>
                <th>#</th><th>Item</th><th>Description</th><th>Location</th><th>Date</th><th>Reported By</th>
            </tr>
            <?php $i = 1; while ($item = mysqli_fetch_assoc($found_items)): ?>
            <tr>
                <td><?php echo $i++; ?></td>
                <td><?php echo htmlspecialchars($item['Item_Name']); ?></td>
                <td><?php echo htmlspecialchars($item['Description']); ?></td>
                <td><?php echo htmlspecialchars($item['Location']); ?></td>
                <td><?php echo $item['Found_Date']; ?></td>
                <td><?php echo htmlspecialchars($item['Reporter']); ?></td>
            </tr>
            <?php endwhile; ?>
        </table>
        <?php else: ?>
            <div class="no-data">No found items reported yet.</div>
        <?php endif; ?>
    </div>

    <!-- Claim Requests -->
    <div class="section">
        <div class="section-header header-yellow">📋 All Claim Requests</div>
        <?php if (mysqli_num_rows($claims) > 0): ?>
        <table>
            <tr>
                <th>#</th><th>Claimant</th><th>Lost Item</th><th>Found Item</th><th>Status</th><th>Date</th><th>Action</th>
            </tr>
            <?php $i = 1; while ($claim = mysqli_fetch_assoc($claims)): ?>
            <tr>
                <td><?php echo $i++; ?></td>
                <td><?php echo htmlspecialchars($claim['Claimant']); ?></td>
                <td><?php echo htmlspecialchars($claim['Lost_Name']); ?></td>
                <td><?php echo htmlspecialchars($claim['Found_Name']); ?></td>
                <td class="status-<?php echo strtolower($claim['Status']); ?>">
                    <?php echo $claim['Status']; ?>
                </td>
                <td><?php echo date('d M Y', strtotime($claim['Created_At'])); ?></td>
                <td>
                    <?php if ($claim['Status'] == 'Pending'): ?>
                        <a href="admin.php?action=approve&claim_id=<?php echo $claim['Claim_ID']; ?>" class="btn-approve">Approve</a>
                        <a href="admin.php?action=reject&claim_id=<?php echo $claim['Claim_ID']; ?>"  class="btn-reject">Reject</a>
                    <?php else: ?>
                        —
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
        <?php else: ?>
            <div class="no-data">No claim requests yet.</div>
        <?php endif; ?>
    </div>

</div>
</body>
</html>