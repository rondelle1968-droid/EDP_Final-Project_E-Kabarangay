<?php
require_once 'config.php';

if (!isset($_SESSION['account_id']) || $_SESSION['is_admin'] != 1) {
    header("Location: login.php");
    exit;
}

$stmt = $pdo->query("SELECT * FROM archived_residents ORDER BY archived_at DESC");
$archives = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resident Archive | E-Kabarangay</title>
    <link rel="stylesheet" href="admin_portal-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .back-link { background: #ff7675; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: bold; font-size: 0.9rem; transition: background 0.3s; }
        .back-link:hover { background: #d63031; }
        .archive-card { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); margin-top: 20px; }
        .resident-table { font-size: 0.9rem; width: 100%; border-collapse: collapse; }
        .resident-table th, .resident-table td { padding: 10px; text-align: left; border-bottom: 1px solid #eee; }
        
        
        .blue-icon { color: #007bff; cursor: pointer; font-size: 1.1rem; text-decoration: none; }
        .blue-icon:hover { color: #0056b3; }
        .red-icon { color: #dc3545; cursor: pointer; font-size: 1.1rem; border: none; background: none; padding: 0; }
        .red-icon:hover { color: #a71d2a; }
        
        .actions-cell { display: flex; gap: 15px; align-items: center; }
    </style>
</head>
<body>
    <header class="admin-header">
        <div class="logo-section">
            <img src="BHPS logo.png" class="portal-logo" alt="Logo">
            <h1>RESIDENT ARCHIVE</h1>
        </div>
        <a href="master_list.php" class="back-link">Back to Master List</a>
    </header>

    <main class="main-content-layout" style="display: block; padding: 20px 40px;">
        <div class="archive-card">
            <table class="resident-table">
                <thead>
                    <tr>
                        <th>RESIDENT NAME</th>
                        <th>ARCHIVED DATE</th>
                        <th>ACTION</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($archives as $a): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($a['first_name'] . ' ' . $a['last_name']) ?></strong></td>
                        <td><?= date('M d, Y h:i A', strtotime($a['archived_at'])) ?></td>
                        <td class="actions-cell">
                            <a href="view_resident.php?id=<?= $a['resident_id'] ?>&from=archive_page.php" class="blue-icon">
                                <i class="fas fa-eye" title="View Profile"></i>
                            </a>
                            
                            <button onclick="deletePermanent(<?= $a['archive_id'] ?>, '<?= addslashes($a['first_name'] . ' ' . $a['last_name']) ?>')" class="red-icon">
                                <i class="fas fa-trash" title="Permanently Delete"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if(empty($archives)): ?>
                        <tr><td colspan="3" style="text-align:center; padding: 20px;">No archived records found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <script>
    function deletePermanent(archiveId, name) {
        if(confirm(`WARNING: Are you sure you want to PERMANENTLY delete ${name}? This action cannot be undone.`)) {
            fetch('delete_archive.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `archive_id=${archiveId}`
            })
            .then(res => res.json())
            .then(data => {
                if(data.status === 'success') {
                    location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Could not delete record.'));
                }
            })
            .catch(err => {
                console.error(err);
                alert('An error occurred while processing the request.');
            });
        }
    }
    </script>
</body>
</html>