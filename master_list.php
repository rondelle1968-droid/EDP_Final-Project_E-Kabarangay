<?php
require_once 'config.php';

if (!isset($_SESSION['account_id']) || $_SESSION['is_admin'] != 1) {
    header("Location: login.php");
    exit;
}

// Fetch Pending Count for Badge
$pending_count = $pdo->query("SELECT COUNT(*) FROM residents WHERE status = 'Pending'")->fetchColumn();

// Filter Logic
$age_filter = $_GET['age_group'] ?? '';
$job_filter = $_GET['occupation'] ?? '';
$edu_filter = $_GET['education'] ?? '';
$res_filter = $_GET['res_type'] ?? '';
$street_filter = $_GET['street'] ?? '';
$toilet_filter = $_GET['toilet'] ?? '';
$water_filter = $_GET['water'] ?? '';
$salt_filter = $_GET['salt'] ?? '';
$rice_filter = $_GET['rice'] ?? '';
$civil_filter = $_GET['civil_status'] ?? '';

$query = "SELECT r.*, a.street, a.residence_type, h.toilet_type, h.water_source, h.iodized_salt, h.iron_fortified_rice 
          FROM residents r
          LEFT JOIN address a ON r.id = a.resident_id AND a.address_type = 'current'
          LEFT JOIN household_info h ON r.id = h.resident_id
          WHERE r.status = 'Approved' 
          AND r.id NOT IN (SELECT resident_id FROM archived_residents)";

$params = [];

if ($age_filter == 'Infants') { $query .= " AND TIMESTAMPDIFF(YEAR, r.dob, CURDATE()) <= 1"; }
elseif ($age_filter == 'Youth') { $query .= " AND TIMESTAMPDIFF(YEAR, r.dob, CURDATE()) BETWEEN 15 AND 30"; }
elseif ($age_filter == 'Seniors') { $query .= " AND TIMESTAMPDIFF(YEAR, r.dob, CURDATE()) >= 60"; }

if ($civil_filter) { $query .= " AND r.civil_status = ?"; $params[] = $civil_filter; }
if ($job_filter) { $query .= " AND r.employment_status = ?"; $params[] = $job_filter; }
if ($edu_filter) { $query .= " AND r.educational_attainment = ?"; $params[] = $edu_filter; }
if ($res_filter) { $query .= " AND a.residence_type = ?"; $params[] = ($res_filter == 'Rental' ? 'Boarding House' : 'Own House'); }
if ($street_filter) { $query .= " AND a.street = ?"; $params[] = $street_filter; }
if ($toilet_filter) { $query .= " AND h.toilet_type = ?"; $params[] = $toilet_filter; }
if ($water_filter) { $query .= " AND h.water_source = ?"; $params[] = $water_filter; }
if ($salt_filter) { $query .= " AND h.iodized_salt = ?"; $params[] = ($salt_filter == 'User' ? 'Yes' : 'No'); }
if ($rice_filter) { $query .= " AND h.iron_fortified_rice = ?"; $params[] = ($rice_filter == 'User' ? 'Yes' : 'No'); }

$query .= " ORDER BY r.last_name ASC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$residents = $stmt->fetchAll();

// Get current page name for navigation highlighting
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Master List | E-Kabarangay</title>
    <link rel="stylesheet" href="admin_portal-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .unified-card { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); margin-top: 10px; }
        .filter-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 10px; margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px solid #eee; }
        .filter-grid select { padding: 6px 10px; border: 1px solid #ddd; border-radius: 6px; width: 100%; outline: none; font-size: 0.85rem; }
        .search-container { position: relative; margin-bottom: 15px; max-width: 450px; }
        .search-container input { width: 100%; padding: 8px 35px; border-radius: 20px; border: 1px solid #e0e0e0; font-size: 0.85rem; }
        .search-container i { position: absolute; left: 12px; top: 10px; color: #aaa; font-size: 0.85rem; }
        .action-bar { display: flex; justify-content: flex-end; align-items: center; margin-bottom: 15px; gap: 10px; }
        .btn-archive-link { background: #ff7675; color: white; padding: 8px 15px; border-radius: 6px; text-decoration: none; font-weight: 600; display: flex; align-items: center; gap: 8px; font-size: 0.85rem; transition: background 0.3s; }
        .btn-assign { background: #0984e3; color: white; padding: 8px 15px; border-radius: 6px; text-decoration: none; font-weight: 600; display: flex; align-items: center; gap: 8px; font-size: 0.85rem; }
        
        
        .filter-buttons { display: flex; gap: 10px; align-items: center; }
        .filter-buttons .nav-btn { padding: 6px 15px; font-size: 0.8rem; border-radius: 6px; border: none; color: white; font-weight: bold; cursor: pointer; }
        
        .resident-table { font-size: 0.85rem; width: 100%; border-collapse: collapse; }
        .resident-table th, .resident-table td { padding: 8px; text-align: left; border-bottom: 1px solid #f0f0f0; }
    </style>
</head>
<body>
    <header class="admin-header">
        <div class="logo-section">
            <img src="BHPS logo.png" class="portal-logo" alt="Logo">
            <h1>MASTER LIST</h1>
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

    <main class="main-content-layout" style="display: block; padding: 0 40px 40px;">
        <div class="unified-card">
            <div class="action-bar">
                <a href="manage_households.php" class="btn-assign"><i class="fas fa-house-user"></i> Assign Household and Family</a>
                <a href="archive_page.php" class="btn-archive-link"><i class="fas fa-archive"></i> View Resident Archive</a>
            </div>

            <div class="search-container">
                <i class="fas fa-search"></i>
                <input type="text" id="liveSearch" placeholder="Search name">
            </div>

            <form method="GET">
                <div class="filter-grid">
                    <select name="age_group">
                        <option value="">All Age</option>
                        <option value="Youth" <?= $age_filter=='Youth'?'selected':'' ?>>Youth (15-30 yrs)</option>
                        <option value="Infants" <?= $age_filter=='Infants'?'selected':'' ?>>Infants</option>
                        <option value="Seniors" <?= $age_filter=='Seniors'?'selected':'' ?>>Seniors</option>
                    </select>
                    <select name="civil_status">
                        <option value="">Civil Status</option>
                        <?php foreach(['Single', 'Married', 'Separated', 'Divorced', 'Widowed', 'Live-in'] as $c): ?>
                            <option value="<?= $c ?>" <?= $civil_filter==$c?'selected':'' ?>><?= $c ?></option>
                        <?php endforeach; ?>
                    </select>
                    <select name="occupation">
                        <option value="">Occupation</option>
                        <?php foreach(['Employed','Unemployed','Self-employed','Student','Retired','Homemaker','Part-time'] as $o): ?>
                            <option value="<?= $o ?>" <?= $job_filter==$o?'selected':'' ?>><?= $o ?></option>
                        <?php endforeach; ?>
                    </select>
                    <select name="education">
                        <option value="">Educational Attainment</option>
                        <?php foreach(['Elementary','High school','Senior high school','College / Undergraduate','College Graduate','No Formal Education','Vocational / Technical'] as $e): ?>
                            <option value="<?= $e ?>" <?= $edu_filter==$e?'selected':'' ?>><?= $e ?></option>
                        <?php endforeach; ?>
                    </select>
                    <select name="res_type">
                        <option value="">Resident type</option>
                        <option value="Own house" <?= $res_filter=='Own house'?'selected':'' ?>>Own house</option>
                        <option value="Rental" <?= $res_filter=='Rental'?'selected':'' ?>>Rental</option>
                    </select>
                    <select name="street">
                        <option value="">Street</option>
                        <?php foreach(['Del Pilar St.','National Road','Rizal St.','P. Viana St.','San Jose St.','Mercene St.'] as $s): ?>
                            <option value="<?= $s ?>" <?= $street_filter==$s?'selected':'' ?>><?= $s ?></option>
                        <?php endforeach; ?>
                    </select>
                    <select name="toilet">
                        <option value="">Toilet type</option>
                        <option value="Water Sealed" <?= $toilet_filter=='Water Sealed'?'selected':'' ?>>Water Sealed</option>
                        <option value="Open pit" <?= $toilet_filter=='Open pit'?'selected':'' ?>>Open pit</option>
                    </select>
                    <select name="water">
                        <option value="">Water source</option>
                        <option value="Community Piped" <?= $water_filter=='Community Piped'?'selected':'' ?>>Community Piped</option>
                        <option value="Well" <?= $water_filter=='Well'?'selected':'' ?>>Well</option>
                        <option value="Spring" <?= $water_filter=='Spring'?'selected':'' ?>>Spring</option>
                    </select>
                    <select name="salt">
                        <option value="">Iodized salt</option>
                        <option value="User" <?= $salt_filter=='User'?'selected':'' ?>>User</option>
                        <option value="Non-user" <?= $salt_filter=='Non-user'?'selected':'' ?>>Non-user</option>
                    </select>
                    <select name="rice">
                        <option value="">Iron fortified rice</option>
                        <option value="User" <?= $rice_filter=='User'?'selected':'' ?>>User</option>
                        <option value="Non-user" <?= $rice_filter=='Non-user'?'selected':'' ?>>Non-user</option>
                    </select>
                </div>
                <div class="filter-buttons">
                    <button type="submit" class="nav-btn" style="background: var(--nav-green);">Apply Filters</button>
                    <a href="master_list.php" class="nav-btn" style="background:#6c757d; text-decoration:none; display: inline-block;">Clear All</a>
                </div>
            </form>

            <div style="margin-top: 20px;">
                <table class="resident-table" id="masterTable">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>FULL NAME</th>
                            <th>AGE / SEX</th>
                            <th>STATUS</th>
                            <th>ACTION</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $counter = 1; // Start counter for auto-indexing
                        foreach($residents as $r): 
                            $age = date_diff(date_create($r['dob']), date_create('today'))->y;
                        ?>
                        <tr class="resident-row">
                            <td class="index-cell"><?= $counter++ ?></td>
                            <td class="name-cell">
                                <strong><?= htmlspecialchars($r['first_name'] . ' ' . $r['last_name']) ?></strong><br>
                                <small><?= htmlspecialchars($r['contact_no']) ?></small>
                            </td>
                            <td><?= $age ?> yrs / <?= $r['sex'] ?></td>
                            <td><span class="status-text"><?= strtoupper($r['civil_status']) ?></span></td>
                            <td class="actions">
                                <a href="view_resident.php?id=<?= $r['id'] ?>&from=master_list.php"><i class="fas fa-eye blue-icon"></i></a>
                                <button onclick="archiveResident(<?= $r['id'] ?>, '<?= addslashes($r['first_name']) ?>', '<?= addslashes($r['last_name']) ?>')" style="border:none; background:none; cursor:pointer; padding: 0 5px;">
                                    <i class="fas fa-archive" style="color: #6c757d;"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <script>
    document.getElementById('liveSearch').addEventListener('input', function() {
        let filter = this.value.toLowerCase();
        let rows = document.querySelectorAll('.resident-row');
        let visibleCount = 1;
        
        rows.forEach(row => {
            let text = row.querySelector('.name-cell').textContent.toLowerCase();
            if (text.includes(filter)) {
                row.style.display = '';
                // Update the index number visually during search
                row.querySelector('.index-cell').textContent = visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });
    });

    function archiveResident(id, fname, lname) {
        if(confirm(`Move ${fname} ${lname} to archive?`)) {
            fetch('archive_action.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `id=${id}&fname=${encodeURIComponent(fname)}&lname=${encodeURIComponent(lname)}`
            })
            .then(res => res.json())
            .then(data => { if(data.status === 'success') location.reload(); });
        }
    }
    </script>
</body>
</html>