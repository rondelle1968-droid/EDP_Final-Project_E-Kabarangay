<?php
require_once 'config.php';

if (!isset($_SESSION['account_id'])) {
    header("Location: login.php");
    exit;
}

$accountId = $_SESSION['account_id'];

// Get Resident Profile
$stmt = $pdo->prepare("SELECT r.id, r.first_name, r.last_name, r.id_picture FROM residents r WHERE r.account_id = ?");
$stmt->execute([$accountId]);
$resident = $stmt->fetch();
$residentId = $resident['id'];

// Handle Deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['delete_id'])) {
        $id = $_POST['delete_id'];
        $stmt = $pdo->prepare("DELETE FROM archived_requests WHERE id = ? AND resident_id = ?");
        $stmt->execute([$id, $residentId]);
        header("Location: archived_requests.php?deleted=1");
        exit;
    }
}

// Fetch Archived Requests
$stmt = $pdo->prepare("SELECT * FROM archived_requests WHERE resident_id = ? ORDER BY archived_at DESC");
$stmt->execute([$residentId]);
$archivedRequests = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Archived Requests | E-Kabarangay</title>
    <link rel="stylesheet" href="dashboard-style.css">
    <link rel="stylesheet" href="requests-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header class="dashboard-header">
        <div class="user-welcome">
            <div class="profile-img-container">
                <img src="Upload/id_pictures/<?= htmlspecialchars($resident['id_picture'] ?? 'default.png') ?>" alt="Profile">
            </div>
            <div class="welcome-text">
                <p>Archived History</p>
                <h1><?= htmlspecialchars($resident['first_name'] . ' ' . $resident['last_name']) ?></h1>
            </div>
        </div>
        <div class="header-actions">
            <a href="requests.php" class="btn-request" style="background:#fff; color:#000; text-decoration:none;">Back to Active Requests</a>
        </div>
    </header>

    <div class="requests-container">
        <section class="request-section">
            <div class="section-header">
                <h2><i class="fas fa-box-archive"></i> Archive History</h2>
            </div>
            <div class="table-card">
                <table>
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Document</th>
                            <th>Purpose</th>
                            <th>Status</th>
                            <th>Date Requested</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($archivedRequests)): ?>
                            <tr><td colspan="6" style="text-align:center;">No archived records found.</td></tr>
                        <?php else: $i = 1; foreach($archivedRequests as $arc): ?>
                            <tr>
                                <td><?= $i++ ?></td>
                                <td><strong><?= htmlspecialchars($arc['document_type']) ?></strong></td>
                                <td><?= htmlspecialchars($arc['purpose']) ?></td>
                                <td><span class="status-badge status-<?= strtolower($arc['status']) ?>"><?= $arc['status'] ?></span></td>
                                <td><?= date('M d, Y', strtotime($arc['date_requested'])) ?></td>
                                <td>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="delete_id" value="<?= $arc['id'] ?>">
                                        <button type="submit" class="btn-delete" onclick="return confirm('Permanently delete this record?')">Delete Permanently</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</body>
</html>