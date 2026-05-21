<?php
require_once 'config.php';

if (!isset($_SESSION['account_id']) || $_SESSION['is_admin'] != 1) {
    header("Location: login.php");
    exit;
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $res_id = $_POST['resident_id'];
    $action = $_POST['action'];
    $new_status = ($action == 'approve') ? 'Approved' : 'Rejected';
    
    $message = ($action == 'approve') 
        ? "Your registration has been approved. Welcome to E-Kabarangay!" 
        : "Your registration was rejected. Please contact the barangay office for details.";
    
    $update = $pdo->prepare("UPDATE residents SET status = ? WHERE id = ?");
    $update->execute([$new_status, $res_id]);

    $notif = $pdo->prepare("INSERT INTO notifications (resident_id, message) VALUES (?, ?)");
    $notif->execute([$res_id, $message]);

    header("Location: resident_validation.php");
    exit;
}

// Fetch Pending Residents
$stmt = $pdo->query("SELECT * FROM residents WHERE status = 'Pending' ORDER BY id DESC");
$pending = $stmt->fetchAll();


$pending_count = $pdo->query("SELECT COUNT(*) FROM residents WHERE status = 'Pending'")->fetchColumn();


$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Resident Validation | Admin</title>
    <link rel="stylesheet" href="admin_portal-style.css">
    <link rel="stylesheet" href="resident_validation-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header class="admin-header">
        <div class="logo-section">
            <img src="BHPS logo.png" class="portal-logo" alt="Logo">
            <h1>RESIDENT VALIDATION</h1>
        </div>
        <a href="logout.php" class="btn-logout">Log out</a>
    </header>

    <nav class="admin-nav">
        <a href="admin_portal.php" class="nav-btn <?= ($current_page == 'admin_portal.php') ? 'active' : '' ?>">Dashboard</a>
        <a href="master_list.php" class="nav-btn <?= ($current_page == 'master_list.php') ? 'active' : '' ?>">Master List</a>
        <a href="announcements.php" class="nav-btn <?= ($current_page == 'announcements.php') ? 'active' : '' ?>">Announcement</a>
        <a href="admin_requests.php" class="nav-btn <?= ($current_page == 'admin_requests.php') ? 'active' : '' ?>">Request</a>
        <a href="resident_validation.php" class="nav-btn <?= ($current_page == 'resident_validation.php') ? 'active' : '' ?>">
            Resident Validation <?php if($pending_count > 0): ?><span class="badge"><?= $pending_count ?></span><?php endif; ?>
        </a>
    </nav>

    <main class="validation-container">
        <h2 class="section-header">Pending Registration</h2>
        
        <div class="validation-grid">
            <?php foreach($pending as $r): ?>
            <div class="validation-card">
                <div class="id-container-2x2">
                    <img src="Upload/id_pictures/<?= htmlspecialchars($r['id_picture']) ?>" alt="ID">
                </div>
                
                <div class="resident-summary">
                    <h2 class="full-name"><?= htmlspecialchars($r['first_name'] . ' ' . $r['middle_name'] . ' ' . $r['last_name']) ?></h2>
                    <p><strong>DOB:</strong> <?= date('d/m/Y', strtotime($r['dob'])) ?></p>
                    <p><strong>Sex:</strong> <?= $r['sex'] ?></p>
                    <p><strong>Contact:</strong> <?= htmlspecialchars($r['contact_no']) ?></p>
                </div>

                <div class="card-actions">
                    <form method="POST" style="display: flex; gap: 8px; width: 100%;">
                        <input type="hidden" name="resident_id" value="<?= $r['id'] ?>">
                        <button type="submit" name="action" value="approve" class="btn-approve">Approve</button>
                        <button type="submit" name="action" value="reject" class="btn-reject">Reject</button>
                        <a href="view_resident.php?id=<?= $r['id'] ?>&from=resident_validation.php" class="btn-view">
                            <i class="fas fa-eye"></i>
                        </a>
                    </form>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </main>
</body>
</html>