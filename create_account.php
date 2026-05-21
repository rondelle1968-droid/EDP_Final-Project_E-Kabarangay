<?php
require_once 'config.php';

// Prevent direct access to step two if profile variables are empty
if (!isset($_SESSION['temp_reg_data'])) {
    header("Location: register_details.php");
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];
    $terms = isset($_POST['terms']);
    
    if (!$terms) {
        $error = "You must agree to the Terms of Use and Privacy Policy.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } elseif (strlen($username) < 3) {
        $error = "Username must be at least 3 characters.";
    } elseif ($password !== $confirm) {
        $error = "Passwords do not match.";
    } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $password)) {
        $error = "Password does not meet the security policy.";
    } else {
        // Check if username or email already exists in accounts table
        $stmt = $pdo->prepare("SELECT id FROM accounts WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->fetch()) {
            $error = "Username or Email already exists.";
        } else {
            $temp_data = $_SESSION['temp_reg_data'];
            $temp_picture = $_SESSION['temp_id_picture'];
            $hash = password_hash($password, PASSWORD_DEFAULT);
            
            try {
                $pdo->beginTransaction();
                
                // Insert into accounts table with email
                $stmt = $pdo->prepare("INSERT INTO accounts (email, username, password_hash) VALUES (?, ?, ?)");
                $stmt->execute([$email, $username, $hash]);
                $accountId = $pdo->lastInsertId();
                
                // Move temporary ID picture
                $upload_dir = 'Upload/id_pictures/'; 
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }

                $final_pic = "user_{$accountId}_" . time() . ".png";
                $final_path = $upload_dir . $final_pic;
                
                if (file_exists($temp_picture)) {
                    rename($temp_picture, $final_path);
                } else {
                    throw new Exception("Temporary ID upload image missing.");
                }
                
                // Prepare maternal health values with defaults
                $family_planning = $temp_data['family_planning'] ?? 'None';
                $pregnancy_status = $temp_data['pregnancy_status'] ?? 'None';
                $breastfeeding_type = $temp_data['breastfeeding_type'] ?? null;
                
                if (($temp_data['sex'] ?? '') === 'Male') {
                    $family_planning = 'None';
                    $pregnancy_status = 'None';
                    $breastfeeding_type = null;
                }
                
                // Insert into residents table
                $stmt2 = $pdo->prepare("INSERT INTO residents (
                    account_id, first_name, middle_name, last_name, civil_status, sex, dob, place_of_birth, 
                    contact_no, religion, employment_status, educational_attainment, 
                    family_planning, pregnancy_status, breastfeeding_type, id_picture
                ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
                
                $stmt2->execute([
                    $accountId,
                    $temp_data['first_name'],
                    $temp_data['middle_name'] ?? null,
                    $temp_data['last_name'],
                    $temp_data['civil_status'],
                    $temp_data['sex'],
                    $temp_data['dob'],
                    $temp_data['place_of_birth'],
                    $temp_data['contact_no'],
                    $temp_data['religion'],
                    $temp_data['employment_status'],
                    $temp_data['educational_attainment'],
                    $family_planning,
                    $pregnancy_status,
                    $breastfeeding_type,
                    $final_pic
                ]);
                
                $residentId = $pdo->lastInsertId();
                
                // Insert current address
                $stmt3 = $pdo->prepare("INSERT INTO address (
                    resident_id, address_type, street, barangay, municipality, province, residence_type
                ) VALUES (?, 'current', ?, ?, ?, ?, ?)");
                $stmt3->execute([
                    $residentId,
                    $temp_data['current_street'],
                    $temp_data['current_barangay'],
                    $temp_data['current_municipality'],
                    $temp_data['current_province'],
                    $temp_data['current_residence_type']
                ]);
                
                // Insert permanent address
                $stmt4 = $pdo->prepare("INSERT INTO address (
                    resident_id, address_type, street, barangay, municipality, province
                ) VALUES (?, 'permanent', ?, ?, ?, ?)");
                $stmt4->execute([
                    $residentId,
                    $temp_data['permanent_street'],
                    $temp_data['permanent_barangay'],
                    $temp_data['permanent_municipality'],
                    $temp_data['permanent_province']
                ]);
                
                // Insert household info
                $stmt5 = $pdo->prepare("INSERT INTO household_info (
                    resident_id, toilet_type, water_source, iodized_salt, iron_fortified_rice
                ) VALUES (?, ?, ?, ?, ?)");
                $stmt5->execute([
                    $residentId,
                    $temp_data['toilet_type'],
                    $temp_data['water_source'],
                    $temp_data['iodized_salt'],
                    $temp_data['iron_fortified_rice']
                ]);
                
                $pdo->commit();
                
                unset($_SESSION['temp_reg_data'], $_SESSION['temp_id_picture']);
                
                header("Location: login.php?registered=1");
                exit;
                
            } catch(Exception $e) {
                $pdo->rollBack();
                $error = "Registration failed: " . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account | BHPS</title>
    <link rel="stylesheet" href="create_account-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
<div class="main-container">
    <div class="top-header">
        <img src="BHPS logo.png" alt="Logo" class="main-logo">
        <h1>CREATE ACCOUNT</h1>
    </div>

    <div class="form-card">
        <?php if($error): ?>
            <div class="alert"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" id="registrationForm">
            <div class="input-group">
                <label>Email:</label>
                <input type="email" name="email" placeholder="ex. JuanDelaCruz01@gmail.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
            </div>

            <div class="input-group">
                <label>Username:</label>
                <input type="text" name="username" placeholder="ex. JuanDelaCruz01" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required>
            </div>

            <div class="input-group password-wrapper">
                <label>Password:</label>
                <div class="input-with-icon">
                    <input type="password" name="password" id="password" required>
                    <i class="fas fa-eye-slash toggle-password" data-target="password"></i>
                </div>
            </div>

            <div class="input-group password-wrapper">
                <label>Confirm Password:</label>
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

            <div class="terms-group">
                <input type="checkbox" name="terms" id="terms" required>
                <label for="terms">I agree to the <a href="terms.php" target="_blank">Terms of Use</a> and <a href="privacy.php" target="_blank">Privacy Policy</a>.</label>
            </div>

            <div class="action-buttons">
                <button type="submit" class="btn-register">Register</button>
            </div>
        </form>
        
        <div class="back-section">
            <a href="register_details.php" class="btn-back"><i class="fas fa-arrow-left"></i> Back</a>
        </div>
    </div>
</div>
<script src="create_account-script.js"></script>
</body>
</html>