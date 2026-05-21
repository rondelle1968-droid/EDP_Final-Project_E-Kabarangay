<?php
require_once 'config.php';

// Session check
if (!isset($_SESSION['account_id']) || $_SESSION['is_admin'] != 1) {
    header("Location: login.php");
    exit;
}

// Handle Permanent Deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'delete_perm') {
    $id = $_POST['request_id'] ?? null;
    if ($id) {
        $stmt = $pdo->prepare("DELETE FROM admin_archived_request WHERE id = ?");
        $stmt->execute([$id]);
        header("Location: admin_archived_request.php");
        exit;
    }
}

// Fetch Archived Requests
$archSql = "SELECT d.*, r.first_name, r.last_name, 
            CONCAT(a.street, ', ', a.barangay, ', ', a.municipality) as full_address 
            FROM admin_archived_request d
            JOIN residents r ON d.resident_id = r.id
            LEFT JOIN address a ON r.id = a.resident_id AND a.address_type = 'current'
            ORDER BY d.archived_at DESC";
$archivedRequests = $pdo->query($archSql)->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Archived Requests | Admin</title>
    <link rel="stylesheet" href="admin_portal-style.css">
    <link rel="stylesheet" href="admin_request-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header class="admin-header">
        <div class="logo-section">
            <img src="BHPS logo.png" class="portal-logo" alt="Logo">
            <h1>ARCHIVED REQUESTS</h1>
        </div>
        <a href="admin_requests.php" class="btn-logout" style="background: var(--admin-blue);">Back to Requests</a>
    </header>

    <main class="request-main-container">
        <div class="unified-card">
            <div class="list-header-row">
                <h2 style="color: #666;">ARCHIVED DOCUMENT RECORDS</h2>
            </div>

            <div class="table-wrapper">
                <table class="admin-request-table">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Document Type</th>
                            <th>Purpose</th>
                            <th>Name</th>
                            <th>Address</th>
                            <th>Date Requested</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($archivedRequests)): ?>
                            <tr><td colspan="8" class="empty-msg">No archived records found.</td></tr>
                        <?php else: $no = 1; foreach($archivedRequests as $arc): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><strong><?= htmlspecialchars($arc['document_type']) ?></strong></td>
                                <td><small><?= htmlspecialchars($arc['purpose'] ?? 'N/A') ?></small></td>
                                <td><?= htmlspecialchars($arc['first_name'] . ' ' . $arc['last_name']) ?></td>
                                <td><small><?= htmlspecialchars($arc['full_address'] ?? 'N/A') ?></small></td>
                                <td><?= date('M d, Y', strtotime($arc['date_requested'])) ?></td>
                                <td><span class="status-pill status-<?= strtolower($arc['status']) ?>"><?= $arc['status'] ?></span></td>
                                <td>
                                    <form method="POST" onsubmit="return confirm('Permanently delete this archive?')">
                                        <input type="hidden" name="request_id" value="<?= $arc['id'] ?>">
                                        <button type="submit" name="action" value="delete_perm" class="btn-action" style="color: #dc3545; background: none; border: none; cursor: pointer;" title="Delete Permanently">
                                            <i class="fas fa-trash-can"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>