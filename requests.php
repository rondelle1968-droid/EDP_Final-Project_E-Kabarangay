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

if (!$resident) {
    die("Error: Resident profile not found.");
}

$residentId = $resident['id'];

// Handle Form Submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['submit_request'])) {
        $doc = $_POST['document_type'];
        $purpose = $_POST['purpose'];
        $stmt = $pdo->prepare("INSERT INTO document_requests (resident_id, document_type, purpose) VALUES (?, ?, ?)");
        $stmt->execute([$residentId, $doc, $purpose]);
        header("Location: requests.php?success=1");
        exit;
    }

    if (isset($_POST['archive_id'])) {
        $id = $_POST['archive_id'];
        $stmt = $pdo->prepare("INSERT INTO archived_requests (resident_id, document_type, purpose, status, date_requested) 
                               SELECT resident_id, document_type, purpose, status, date_requested FROM document_requests WHERE id = ? AND resident_id = ?");
        $stmt->execute([$id, $residentId]);
        $stmt = $pdo->prepare("DELETE FROM document_requests WHERE id = ? AND resident_id = ?");
        $stmt->execute([$id, $residentId]);
        header("Location: requests.php?archived=1");
        exit;
    }
}

// Fetch Active Requests
$stmt = $pdo->prepare("SELECT * FROM document_requests WHERE resident_id = ? ORDER BY date_requested DESC");
$stmt->execute([$residentId]);
$myRequests = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document Requests | E-Kabarangay</title>
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
                <p>Document Services</p>
                <h1><?= htmlspecialchars($resident['first_name'] . ' ' . $resident['last_name']) ?></h1>
            </div>
        </div>
        <div class="header-actions">
            <a href="dashboard.php" class="btn-request" style="background:#fff; color:#000; text-decoration:none;">Back to Dashboard</a>
        </div>
    </header>

    <div class="requests-container">
        <section class="request-section">
            <div class="section-header">
                <h2><i class="fas fa-file-alt"></i> My Requests</h2>
                <div class="button-group">
                    <button class="btn-main" onclick="openModal()">Request Document</button>
                    <a href="archived_requests.php" class="btn-main" style="text-decoration:none; background-color: #6c757d;">Archived Request</a>
                </div>
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
                        <?php if(empty($myRequests)): ?>
                            <tr><td colspan="6" style="text-align:center;">No active requests found.</td></tr>
                        <?php else: $i = 1; foreach($myRequests as $req): ?>
                            <tr>
                                <td><?= $i++ ?></td>
                                <td><strong><?= htmlspecialchars($req['document_type']) ?></strong></td>
                                <td><?= htmlspecialchars($req['purpose']) ?></td>
                                <td><span class="status-badge status-<?= strtolower($req['status']) ?>"><?= $req['status'] ?></span></td>
                                <td><?= date('M d, Y', strtotime($req['date_requested'])) ?></td>
                                <td>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="archive_id" value="<?= $req['id'] ?>">
                                        <button type="submit" class="btn-archive" onclick="return confirm('Move this to archives?')">Archive</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <div id="requestModal" class="modal-overlay">
        <div class="modal-card">
            <div class="modal-header">
                <h3>New Document Request</h3>
                <span class="close-modal" onclick="closeModal()">&times;</span>
            </div>
            <form method="POST">
                <div class="form-group">
                    <label>Select Document</label>
                    <select name="document_type" required>
                        <option value="" disabled selected>-- Choose Document --</option>
                        <option value="Barangay Clearance">Barangay Clearance</option>
                        <option value="Barangay Certificate">Barangay Certificate</option>
                        <option value="Certificate of Indigency">Certificate of Indigency</option>
                        <option value="Certificate of Residency">Certificate of Residency</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Purpose</label>
                    <textarea name="purpose" required></textarea>
                </div>
                <button type="submit" name="submit_request" class="btn-submit">Submit Request</button>
            </form>
        </div>
    </div>

    <script>
        function openModal() { document.getElementById('requestModal').style.display = 'flex'; }
        function closeModal() { document.getElementById('requestModal').style.display = 'none'; }
    </script>
</body>
</html>