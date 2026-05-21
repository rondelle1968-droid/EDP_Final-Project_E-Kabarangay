<?php
require_once 'config.php';

if (!isset($_SESSION['account_id'])) {
    header("Location: login.php");
    exit;
}

$accountId = $_SESSION['account_id'];
$stmt = $pdo->prepare("
    SELECT a.username, a.email, r.first_name, r.last_name, r.id_picture 
    FROM accounts a 
    JOIN residents r ON a.id = r.account_id 
    WHERE a.id = ?
");
$stmt->execute([$accountId]);
$user = $stmt->fetch();

$successMessage = '';
if (isset($_GET['updated']) && isset($_SESSION['update_success'])) {
    $successMessage = $_SESSION['update_success'];
    unset($_SESSION['update_success']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">  
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile | E-Kabarangay</title>
    <link rel="stylesheet" href="profile-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>

        .success-message {
            background-color: #28a745 !important;
            color: #ffffff !important;
            padding: 15px !important;
            border-radius: 10px !important;
            margin-bottom: 20px !important;
            text-align: center !important;
            font-weight: bold !important;
            display: block !important;
            transition: opacity 0.5s ease;
            box-shadow: 0 4px 6px rgba(0,0,0,0.2);
            border: none !important;
        }
    </style>
</head>
<body>
    <div class="profile-page">
        <header class="profile-header">
            <div class="logo-title">
                <img src="BHPS logo.png" alt="Logo" class="profile-logo">
                <h1>PROFILE</h1>
            </div>
        </header>

        <main class="profile-content">
            <div class="profile-card">
                <?php if ($successMessage): ?>
                    <div id="success-alert" class="success-message">
                        <?= htmlspecialchars($successMessage) ?>
                    </div>
                <?php endif; ?>
                
                <div class="avatar-container">

                    <img src="Upload/id_pictures/<?= htmlspecialchars($user['id_picture']) ?>" alt="User Avatar" class="user-avatar">
                </div>
                <h2 class="user-name"><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></h2>
                <p class="user-email"><?= htmlspecialchars($user['email']) ?></p>

                <div class="action-list">
                    <a href="Update_Personal_Profile.php" class="action-item">
                        <span>Update Personal Profile</span>
                        <i class="fas fa-chevron-right"></i>
                    </a>
                    <a href="change_password.php" class="action-item">
                        <span>Change Password</span>
                        <i class="fas fa-chevron-right"></i>
                    </a>
                    <a href="change_id_picture.php" class="action-item">
                        <span>Change ID Picture</span>
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </div>

                <div class="logout-container">
                    <a href="logout.php" class="logout-btn">Log out</a>
                </div>
            </div>
        </main>

        <a href="dashboard.php" class="back-btn">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const alertBox = document.getElementById('success-alert');
            if (alertBox) {
                setTimeout(function() {
                    alertBox.style.opacity = '0';
                    setTimeout(function() {
                        alertBox.style.display = 'none';
                    }, 500); 
                }, 2000); 
            }
        });
    </script>
</body>
</html>