<?php
require_once 'config.php';

if (!isset($_SESSION['account_id'])) {
    header("Location: login.php");
    exit;
}

$accountId = $_SESSION['account_id'];
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['id_picture'])) {
    $file = $_FILES['id_picture'];
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
    
    if ($file['error'] === 0) {
        if (in_array($file['type'], $allowedTypes)) {
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $newFileName = "ID_" . $accountId . "_" . time() . "." . $extension;
            
            
            $uploadPath = 'Upload/id_pictures/' . $newFileName;

            
            if (!is_dir('Upload/id_pictures/')) {
                mkdir('Upload/id_pictures/', 0777, true);
            }

            if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                
                $stmt = $pdo->prepare("UPDATE residents SET id_picture = ? WHERE account_id = ?");
                if ($stmt->execute([$newFileName, $accountId])) {
                    $_SESSION['update_success'] = "ID Picture Updated Successfully!";
                    header("Location: profile.php?updated=1");
                    exit;
                } else {
                    $error = "Database update failed.";
                }
            } else {
                $error = "Failed to move uploaded file.";
            }
        } else {
            $error = "Invalid file type. Only JPG, JPEG, and PNG are allowed.";
        }
    } else {
        $error = "An error occurred during upload.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change ID Picture | E-Kabarangay</title>
    <link rel="stylesheet" href="change_id_picture-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="change-id-page">
        <header class="page-header">
            <div class="logo-title">
                <img src="BHPS logo.png" alt="Logo" class="page-logo">
                <h1>CHANGE ID PICTURE</h1>
            </div>
        </header>

        <main class="content-wrapper">
            <form action="change_id_picture.php" method="POST" enctype="multipart/form-data" class="upload-card">
                <h3 class="upload-label">Upload your ID picture:</h3>
                
                <div class="drop-zone" onclick="document.getElementById('fileInput').click()">
                    <div class="drop-zone-content">
                        <i class="fas fa-upload upload-icon"></i>
                        <span class="upload-text">Upload ID</span>
                        <p class="support-text">Supports: JPG, JPEG, and PNG</p>
                        <p id="fileNameDisplay" style="margin-top: 10px; color: #1545a2; font-weight: bold;"></p>
                    </div>
                    <input type="file" name="id_picture" id="fileInput" hidden accept=".jpg,.jpeg,.png" required onchange="displayFileName()">
                </div>

                <?php if($error): ?>
                    <p style="color: #ffcc00; margin-bottom: 10px;"><?= $error ?></p>
                <?php endif; ?>

                <button type="submit" class="submit-btn">Change ID Picture</button>

                <div class="footer-actions">
                    <a href="profile.php" class="back-link">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
            </form>
        </main>
    </div>

    <script>
        function displayFileName() {
            const input = document.getElementById('fileInput');
            const display = document.getElementById('fileNameDisplay');
            if (input.files.length > 0) {
                display.innerText = "Selected: " + input.files[0].name;
            }
        }
    </script>
</body>
</html>