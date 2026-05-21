<?php
require_once 'config.php';

if (!isset($_SESSION['account_id'])) {
    header("Location: login.php");
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    $accountId = $_SESSION['account_id'];

    if ($new_password !== $confirm_password) {
        $error = "New passwords do not match.";
    } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $new_password)) {
        $error = "New password does not meet security requirements.";
    } else {
        $stmt = $pdo->prepare("SELECT password_hash FROM accounts WHERE id = ?");
        $stmt->execute([$accountId]);
        $user = $stmt->fetch();

        if ($user && password_verify($current_password, $user['password_hash'])) {
            $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
            $update = $pdo->prepare("UPDATE accounts SET password_hash = ? WHERE id = ?");
            $update->execute([$new_hash, $accountId]);
            
            $_SESSION['update_success'] = "Password updated successfully.";
            header("Location: profile.php?updated=1");
            exit;
        } else {
            $error = "Incorrect current password.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password | BHPS</title>
    <link rel="stylesheet" href="change_password-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
<div class="main-container">
    <div class="top-header">
        <img src="BHPS logo.png" alt="Logo" class="main-logo">
        <h1>CHANGE PASSWORD</h1>
    </div>

    <div class="form-card">
        <?php if($error): ?>
            <div class="alert"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" id="changePasswordForm">
            <div class="input-group password-wrapper">
                <label>Old Password:</label>
                <div class="input-with-icon">
                    <input type="password" name="current_password" id="current_password" required>
                    <i class="fas fa-eye-slash toggle-password" data-target="current_password"></i>
                </div>
            </div>

            <div class="input-group password-wrapper">
                <label>New Password:</label>
                <div class="input-with-icon">
                    <input type="password" name="new_password" id="password" required>
                    <i class="fas fa-eye-slash toggle-password" data-target="password"></i>
                </div>
            </div>

            <div class="input-group password-wrapper">
                <label>Confirm New Password:</label>
                <div class="input-with-icon">
                    <input type="password" name="confirm_password" id="confirm_password" required>
                    <i class="fas fa-eye-slash toggle-password" data-target="confirm_password"></i>
                </div>
                <small id="confirm-error" class="match-error hidden">Password should match.</small>
            </div>

            <div class="password-requirements">
                <div class="req-item" id="req-strength"><i class="far fa-times-circle"></i> Password Strength: <span id="strength-label">Weak</span></div>
                <div class="req-item" id="req-length"><i class="far fa-times-circle"></i> At least 8 characters</div>
                <div class="req-item" id="req-digit"><i class="far fa-times-circle"></i> Contains a digit</div>
                <div class="req-item" id="req-lower"><i class="far fa-times-circle"></i> Contains a lowercase letter</div>
                <div class="req-item" id="req-upper"><i class="far fa-times-circle"></i> Contains at least 1 uppercase letter</div>
                <div class="req-item" id="req-special"><i class="far fa-times-circle"></i> Contains at least 1 special character</div>
            </div>

            <div class="action-buttons">
                <button type="submit" class="btn-change">Change Password</button>
            </div>
        </form>
        
        <div class="back-section">
            <a href="profile.php" class="btn-back"><i class="fas fa-arrow-left"></i> Back</a>
        </div>
    </div>
</div>
<script src="change_password-script.js"></script>
</body>
</html>