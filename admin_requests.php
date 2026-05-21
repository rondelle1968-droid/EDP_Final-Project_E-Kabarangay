<?php
require_once 'config.php';

// Session check
if (!isset($_SESSION['account_id']) || $_SESSION['is_admin'] != 1) {
    header("Location: login.php");
    exit;
}

// Handle Actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['request_id'] ?? null;
    $action = $_POST['action'] ?? '';
    $reason = $_POST['decline_reason'] ?? '';

    if ($id && $action) {
        if ($action == 'approve' || $action == 'decline') {
            $status = ($action == 'approve') ? 'Approved' : 'Declined';
            
            
            $req = $pdo->prepare("SELECT resident_id, document_type FROM document_requests WHERE id = ?");
            $req->execute([$id]);
            $requestData = $req->fetch(PDO::FETCH_ASSOC);

            if ($requestData && isset($requestData['resident_id'])) {
                // Update Status in the active requests table
                $stmt = $pdo->prepare("UPDATE document_requests SET status = ? WHERE id = ?");
                $stmt->execute([$status, $id]);

                // Determine message based on approval status
                if ($status == 'Approved') {
                    $msg = "Your request for " . $requestData['document_type'] . " has been approved. You may now proceed to the Barangay Hall to pick up your requested documents.";
                } else {
                    $msg = "Your request for " . $requestData['document_type'] . " has been declined. Reason: " . $reason;
                }
                
                // Save notification into database
                $notif = $pdo->prepare("INSERT INTO notifications (resident_id, message, is_read) VALUES (?, ?, 0)");
                $notif->execute([$requestData['resident_id'], $msg]);
            }
        } elseif ($action == 'archive') {
            $pdo->prepare("INSERT INTO admin_archived_request (resident_id, document_type, purpose, status, date_requested) 
                           SELECT resident_id, document_type, purpose, status, date_requested FROM document_requests WHERE id = ?")->execute([$id]);
            $pdo->prepare("DELETE FROM document_requests WHERE id = ?")->execute([$id]);
        }
        header("Location: admin_requests.php");
        exit;
    }
}

// Count Pending for the Navigation Badge
$pending_count = $pdo->query("SELECT COUNT(*) FROM residents WHERE status = 'Pending'")->fetchColumn();

// Search Logic
$search = $_GET['search'] ?? '';
$searchQuery = "%%";
if(!empty($search)) $searchQuery = "%$search%";

// Fetch Active Requests using clean positional parameters
$sql = "SELECT d.*, r.first_name, r.last_name, 
        CONCAT(a.street, ', ', a.barangay, ', ', a.municipality) as full_address 
        FROM document_requests d
        JOIN residents r ON d.resident_id = r.id
        LEFT JOIN address a ON r.id = a.resident_id AND a.address_type = 'current'
        WHERE (r.first_name LIKE ? OR r.last_name LIKE ? OR d.document_type LIKE ? OR d.purpose LIKE ?)
        ORDER BY d.date_requested DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$searchQuery, $searchQuery, $searchQuery, $searchQuery]);
$activeRequests = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get current page name for navigation highlighting
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Management | Admin</title>
    <link rel="stylesheet" href="admin_portal-style.css">
    <link rel="stylesheet" href="admin_request-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header class="admin-header">
        <div class="logo-section">
            <img src="BHPS logo.png" class="portal-logo" alt="Logo">
            <h1>DOCUMENT REQUEST MANAGEMENT</h1>
        </div>
        <a href="logout.php" class="btn-logout">Log out</a>
    </header>

    <nav class="admin-nav">
        <a href="admin_portal.php" class="nav-btn <?= ($current_page == 'admin_portal.php') ? 'active' : '' ?>">Dashboard</a>
        <a href="master_list.php" class="nav-btn <?= ($current_page == 'master_list.php') ? 'active' : '' ?>">Master List</a>
        <a href="announcements.php" class="nav-btn <?= ($current_page == 'announcements.php') ? 'active' : '' ?>">Announcement</a>
        <a href="admin_requests.php" class="nav-btn <?= ($current_page == 'admin_requests.php') ? 'active' : '' ?>">Requests</a>
        <a href="resident_validation.php" class="nav-btn <?= ($current_page == 'resident_validation.php') ? 'active' : '' ?>">
            Resident Validation <?php if($pending_count > 0): ?><span class="badge"><?= $pending_count ?></span><?php endif; ?>
        </a>
    </nav>

    <main class="request-main-container">
        <div class="unified-card">
            <section class="request-section">
                <div class="list-header-row">
                    <h2>BARANGAY DOCUMENT REQUESTS</h2>
                    <div class="controls-group">
                        <div class="search-container">
                            <form action="" method="GET" class="admin-search-form">
                                <i class="fas fa-search"></i>
                                <input type="text" name="search" placeholder="Search requests..." value="<?= htmlspecialchars($search) ?>">
                            </form>
                        </div>
                        <a href="admin_archived_request.php" class="btn-archive-toggle" style="text-decoration:none;"><i class="fas fa-archive"></i> Archived Requests</a>
                    </div>
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
                            <?php if(empty($activeRequests)): ?>
                                <tr><td colspan="8" class="empty-msg">No active requests found.</td></tr>
                            <?php else: $no = 1; foreach($activeRequests as $req): ?>
                                <tr class="resident-row">
                                    <td><?= $no++ ?></td>
                                    <td><strong><?= htmlspecialchars($req['document_type']) ?></strong></td>
                                    <td><small><?= htmlspecialchars($req['purpose'] ?? 'N/A') ?></small></td>
                                    <td class="name-cell"><?= htmlspecialchars($req['first_name'] . ' ' . $req['last_name']) ?></td>
                                    <td><small><?= htmlspecialchars($req['full_address'] ?? 'N/A') ?></small></td>
                                    <td><?= date('M d, Y', strtotime($req['date_requested'])) ?></td>
                                    <td><span class="status-pill status-<?= strtolower($req['status']) ?>"><?= $req['status'] ?></span></td>
                                    <td class="action-btns">
                                        <div style="display:inline-flex; gap: 8px;">
                                            <?php if($req['status'] == 'Pending'): ?>
                                                <form method="POST" style="display:inline;">
                                                    <input type="hidden" name="request_id" value="<?= $req['id'] ?>">
                                                    <button type="submit" name="action" value="approve" class="btn-action-icon btn-approve" title="Approve"><i class="fas fa-check"></i></button>
                                                </form>
                                                <button type="button" class="btn-action-icon btn-decline" title="Decline" onclick="openDeclineModal(<?= $req['id'] ?>)"><i class="fas fa-xmark"></i></button>
                                            <?php endif; ?>
                                            
                                            <button type="button" class="btn-action-icon btn-print" onclick="window.open('generate_pdf.php?id=<?= $req['id'] ?>', '_blank')" title="Print"><i class="fas fa-print"></i></button>
                                            
                                            <form method="POST" style="display:inline;">
                                                <input type="hidden" name="request_id" value="<?= $req['id'] ?>">
                                                <button type="submit" name="action" value="archive" class="btn-action-icon btn-archive-act" title="Archive"><i class="fas fa-box-archive"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </main>

    <div id="declineModal" class="admin-modal-overlay" style="display:none;">
        <div class="admin-modal-card">
            <div class="admin-modal-header">
                <h3>Decline Request</h3>
                <span class="admin-close-modal" onclick="closeDeclineModal()">&times;</span>
            </div>
            <form id="declineForm" method="POST">
                <input type="hidden" name="request_id" id="decline_request_id">
                <input type="hidden" name="action" value="decline">
                <div class="admin-form-group">
                    <label>Reason for Decline</label>
                    <textarea name="decline_reason" required placeholder="Provide a reason for declining..."></textarea>
                </div>
                <div class="admin-modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeDeclineModal()">Cancel</button>
                    <button type="submit" class="btn-confirm-decline">Decline</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openDeclineModal(requestId) {
            document.getElementById('decline_request_id').value = requestId;
            document.getElementById('declineModal').style.display = 'flex';
        }

        function closeDeclineModal() {
            document.getElementById('declineModal').style.display = 'none';
            document.getElementById('declineForm').reset();
        }

        window.onclick = function(event) {
            const modal = document.getElementById('declineModal');
            if (event.target == modal) {
                closeDeclineModal();
            }
        }
    </script>
</body>
</html>