<?php
require_once 'config.php';

if (!isset($_GET['id'])) {
    header("Location: admin_portal.php");
    exit;
}

// Determine where to go back to
$back_url = isset($_GET['from']) ? $_GET['from'] : 'resident_validation.php';

$stmt = $pdo->prepare("
    SELECT r.*, a.email, 
           cur.street AS cur_s, cur.barangay AS cur_b, cur.municipality AS cur_m, cur.province AS cur_p, cur.residence_type,
           perm.street AS per_s, perm.barangay AS per_b, perm.municipality AS per_m, perm.province AS per_p,
           h.toilet_type, h.water_source, h.iodized_salt, h.iron_fortified_rice
    FROM residents r
    JOIN accounts a ON r.account_id = a.id
    LEFT JOIN address cur ON r.id = cur.resident_id AND cur.address_type = 'current'
    LEFT JOIN address perm ON r.id = perm.resident_id AND perm.address_type = 'permanent'
    LEFT JOIN household_info h ON r.id = h.resident_id
    WHERE r.id = ?
");
$stmt->execute([$_GET['id']]);
$r = $stmt->fetch();

if (!$r) {
    header("Location: $back_url");
    exit;
}

$age = date_diff(date_create($r['dob']), date_create('today'))->y;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Resident Details</title>
    <link rel="stylesheet" href="admin_portal-style.css">
    <link rel="stylesheet" href="dashboard-style.css">
    <style>
        .details-modal {
            max-width: 900px;
            margin: 40px auto;
            background: #f8f9fa;
            border-radius: 15px;
            padding: 30px;
        }
        .back-btn {
            background: #ccc;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            color: black;
            display: inline-block;
            margin-top: 20px;
        }
    </style>
</head>
<body style="background: #1545a2;">
    <div class="details-modal">
        <h1 style="color: #1545a2; margin-bottom: 20px;">Resident Personal Details</h1>
        
        <div class="dashboard-container" style="display: grid; grid-template-columns: 1fr 1fr; padding: 0;">
            <div class="info-column">
                <div class="info-card">
                    <h2 class="card-title blue-border">PERSONAL INFORMATION</h2>
                    <div class="info-grid">
                        <div class="info-item"><label>First Name</label><p><?= htmlspecialchars($r['first_name']) ?></p></div>
                        <div class="info-item"><label>Sex</label><p><?= $r['sex'] ?></p></div>
                        <div class="info-item"><label>Middle Name</label><p><?= htmlspecialchars($r['middle_name']) ?></p></div>
                        <div class="info-item"><label>Age</label><p><?= $age ?></p></div>
                        <div class="info-item"><label>Last Name</label><p><?= htmlspecialchars($r['last_name']) ?></p></div>
                        <div class="info-item"><label>Date of Birth</label><p><?= date('d/m/Y', strtotime($r['dob'])) ?></p></div>
                        <div class="info-item"><label>Civil Status</label><p><?= $r['civil_status'] ?></p></div>
                        <div class="info-item"><label>Place of Birth</label><p><?= htmlspecialchars($r['place_of_birth']) ?></p></div>
                        <div class="info-item"><label>Employment</label><p><?= $r['employment_status'] ?></p></div>
                        <div class="info-item"><label>Religion</label><p><?= htmlspecialchars($r['religion']) ?></p></div>
                        <div class="info-item"><label>Contact</label><p class="highlight"><?= htmlspecialchars($r['contact_no']) ?></p></div>
                        <div class="info-item"><label>Email Address</label><p class="highlight"><?= htmlspecialchars($r['email']) ?></p></div>
                    </div>
                </div>

                <div class="info-card">
                    <h2 class="card-title blue-border">ADDRESS</h2>
                    <label>Current Address</label>
                    <p><strong><?= htmlspecialchars($r['cur_s'] . ', ' . $r['cur_b'] . ', ' . $r['cur_m'] . ', ' . $r['cur_p']) ?></strong></p>
                    <p class="highlighted-type"><?= $r['residence_type'] ?></p>
                    <br>
                    <label>Permanent Address</label>
                    <p><strong><?= htmlspecialchars($r['per_s'] . ', ' . $r['per_b'] . ', ' . $r['per_m'] . ', ' . $r['per_p']) ?></strong></p>
                </div>
            </div>

            <div class="info-column">
                <div class="info-card">
                    <h2 class="card-title yellow-border">HOUSEHOLD INFORMATION</h2>
                    <div class="list-row"><span>Toilet Type: </span><strong><?= $r['toilet_type'] ?></strong></div>
                    <div class="list-row"><span>Water Source: </span><strong><?= $r['water_source'] ?></strong></div>
                    <div class="list-row"><span>Using Iodized Salt: </span><strong><?= $r['iodized_salt'] ?></strong></div>
                    <div class="list-row"><span>Using Iron Fortified Rice: </span><strong><?= $r['iron_fortified_rice'] ?></strong></div>
                </div>

                <div class="info-card">
                    <h2 class="card-title red-border">HEALTH STATUS</h2>
                    <div class="info-item"><label>Maternal Status</label><p><?= $r['pregnancy_status'] ?? 'None' ?></p></div>
                    <div class="info-item"><label>Breastfeeding Type</label><p><?= $r['breastfeeding_type'] ?? '-' ?></p></div>
                </div>
            </div>
        </div>

        <a href="<?= htmlspecialchars($back_url) ?>" class="back-btn">← Back</a>
    </div>
</body>
</html>