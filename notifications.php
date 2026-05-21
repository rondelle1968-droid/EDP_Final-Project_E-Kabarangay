<?php
require_once 'config.php';

if (!isset($_SESSION['account_id'])) {
    header("Location: login.php");
    exit;
}

$accountId = $_SESSION['account_id'];

// Get Resident ID
$stmt = $pdo->prepare("SELECT id FROM residents WHERE account_id = ?");
$stmt->execute([$accountId]);
$resident = $stmt->fetch();

if (!$resident) {
    die("Resident record not found.");
}

$resId = $resident['id'];

// Mark all as read when visiting this page
$pdo->prepare("UPDATE notifications SET is_read = 1 WHERE resident_id = ?")->execute([$resId]);

// Fetch Notifications
$notifStmt = $pdo->prepare("SELECT * FROM notifications WHERE resident_id = ? ORDER BY created_at DESC");
$notifStmt->execute([$resId]);
$notifications = $notifStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Notifications | E-Kabarangay</title>
    <link rel="stylesheet" href="dashboard-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .notif-container { max-width: 800px; margin: 30px auto; padding: 20px; }
        .notif-item { 
            background: white; 
            padding: 15px; 
            border-radius: 8px; 
            margin-bottom: 10px; 
            border-left: 5px solid var(--dash-blue);
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .notif-date { font-size: 0.8rem; color: #888; margin-top: 5px; }
        .back-link { display: inline-block; margin-bottom: 20px; text-decoration: none; color: var(--dash-blue); font-weight: bold; }
    </style>
</head>
<body style="background-color: #f4f7f6;">
    <div class="notif-container">
        <a href="dashboard.php" class="back-link"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
        <h2 class="card-title blue-border">NOTIFICATIONS</h2>
        
        <?php if (empty($notifications)): ?>
            <p style="text-align: center; color: #666; margin-top: 30px;">No notifications yet.</p>
        <?php else: ?>
            <?php foreach($notifications as $n): ?>
                <div class="notif-item">
                    <p><?= htmlspecialchars($n['message']) ?></p>
                    <div class="notif-date"><?= date('F j, Y, g:i a', strtotime($n['created_at'])) ?></div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>