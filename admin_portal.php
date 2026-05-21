<?php
require_once 'config.php';

// Session check
if (!isset($_SESSION['account_id']) || $_SESSION['is_admin'] != 1) {
    header("Location: login.php");
    exit;
}

// Fetch Approved Master List 
$stmt = $pdo->query("SELECT id, first_name, last_name, dob, sex, civil_status, contact_no, pregnancy_status, breastfeeding_type FROM residents WHERE status = 'Approved' ORDER BY last_name ASC");
$residents = $stmt->fetchAll();

// Count Pending for the Navigation Badge
$pending_count = $pdo->query("SELECT COUNT(*) FROM residents WHERE status = 'Pending'")->fetchColumn();

// Statistics Initialization
$total_residents = count($residents);
$total_households = $pdo->query("SELECT COUNT(*) FROM households")->fetchColumn();
$total_families = $pdo->query("SELECT COUNT(*) FROM family_groups")->fetchColumn();

$total_infants_stat = 0; 
$total_youth = 0;
$total_seniors = 0;

// Age Brackets for Chart Logic
$age_brackets = [
    '0-3' => 0, 
    '4-14' => 0, 
    '15-30' => 0, 
    '31-59' => 0, 
    '60-100' => 0
];
$gender_counts = ['Male' => 0, 'Female' => 0];

foreach ($residents as $r) {
    // 1. Age Calculation para sa mga rehistradong residente
    $age = 0;
    if (!empty($r['dob'])) {
        $birthDate = new DateTime($r['dob']);
        $today = new DateTime('today');
        $age = $birthDate->diff($today)->y;
    }

    // 2. INFANT LOGIC: Kung Delivered o Breastfeeding, ibig sabihin may infant silang kasama
    $has_infant = ($r['pregnancy_status'] == 'Delivered' || (!empty($r['breastfeeding_type']) && $r['breastfeeding_type'] !== 'None'));
    
    if ($has_infant) {
        $total_infants_stat++; // Para sa Stat Card sa taas
    }

    // 3. STAT CARDS logic para sa Youth at Seniors
    if ($age >= 15 && $age <= 30) { $total_youth++; }
    if ($age >= 60) { $total_seniors++; }

    // 4. CHART BRACKETS (Excluding 0-3 muna dahil kukunin ito sa total_infants_stat)
    if ($age >= 4 && $age <= 14) {
        $age_brackets['4-14']++;
    } elseif ($age >= 15 && $age <= 30) {
        $age_brackets['15-30']++;
    } elseif ($age >= 31 && $age <= 59) {
        $age_brackets['31-59']++;
    } elseif ($age >= 60) {
        $age_brackets['60-100']++;
    }

    // Gender Counts
    if ($r['sex'] == 'Male') $gender_counts['Male']++;
    if ($r['sex'] == 'Female') $gender_counts['Female']++;
}

// 5. FINAL SYNC: Ang 0-3 bracket sa chart ay kapareho na ng Infant Stat Card
$age_brackets['0-3'] = $total_infants_stat;



// 1. Residents per Street (Modified to exclusively target Current Address)
$street_stats = $pdo->query("SELECT street, COUNT(*) as count FROM address a JOIN residents r ON a.resident_id = r.id WHERE r.status = 'Approved' AND a.address_type = 'current' GROUP BY street")->fetchAll(PDO::FETCH_ASSOC);

// 2. Document Request Pie Chart
$doc_types = ['Barangay Clearance', 'Barangay Certificate', 'Certificate of Indigency', 'Certificate of Residency'];
$doc_stats = [];
foreach($doc_types as $type) {
    $stmt_doc = $pdo->prepare("SELECT COUNT(*) FROM document_requests WHERE document_type = ?");
    $stmt_doc->execute([$type]);
    $doc_stats[$type] = $stmt_doc->fetchColumn();
}

// Employment & Education
$employment_stats = $pdo->query("SELECT employment_status as label, COUNT(*) as value FROM residents WHERE status = 'Approved' GROUP BY employment_status HAVING value > 0")->fetchAll(PDO::FETCH_ASSOC);
$education_stats = $pdo->query("SELECT educational_attainment as label, COUNT(*) as value FROM residents WHERE status = 'Approved' GROUP BY educational_attainment HAVING value > 0")->fetchAll(PDO::FETCH_ASSOC);

// Health & Sanitation
$preg_val = $pdo->query("SELECT COUNT(*) FROM residents WHERE pregnancy_status = 'Pregnant' AND status = 'Approved'")->fetchColumn();
$salt_val = $pdo->query("SELECT COUNT(*) FROM household_info h JOIN residents r ON h.resident_id = r.id WHERE h.iodized_salt = 'Yes' AND r.status = 'Approved'")->fetchColumn();
$rice_val = $pdo->query("SELECT COUNT(*) FROM household_info h JOIN residents r ON h.resident_id = r.id WHERE h.iron_fortified_rice = 'Yes' AND r.status = 'Approved'")->fetchColumn();
$bf_stats = $pdo->query("SELECT breastfeeding_type as label, COUNT(*) as value FROM residents WHERE status = 'Approved' AND breastfeeding_type != 'None' GROUP BY breastfeeding_type HAVING value > 0")->fetchAll(PDO::FETCH_ASSOC);
$toilet_stats = $pdo->query("SELECT toilet_type as label, COUNT(*) as value FROM household_info h JOIN residents r ON h.resident_id = r.id WHERE r.status = 'Approved' GROUP BY toilet_type HAVING value > 0")->fetchAll(PDO::FETCH_ASSOC);
$water_stats = $pdo->query("SELECT water_source as label, COUNT(*) as value FROM household_info h JOIN residents r ON h.resident_id = r.id WHERE r.status = 'Approved' GROUP BY water_source HAVING value > 0")->fetchAll(PDO::FETCH_ASSOC);

$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Portal | E-Kabarangay</title>
    <link rel="stylesheet" href="admin_portal-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <header class="admin-header">
        <div class="logo-section">
            <img src="BHPS logo.png" class="portal-logo" alt="Logo">
            <h1>ADMIN PORTAL</h1>
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

    <div class="stats-row">
        <div class="stat-card blue-accent"><h3>RESIDENTS</h3><p><?= $total_residents ?></p></div>
        <div class="stat-card yellow-accent"><h3>HOUSEHOLDS</h3><p><?= $total_households ?></p></div>
        <div class="stat-card red-accent"><h3>FAMILIES</h3><p><?= $total_families ?></p></div>
        <div class="stat-card blue-accent"><h3>INFANTS</h3><p><?= $total_infants_stat ?></p></div>
        <div class="stat-card yellow-accent"><h3>YOUTHS</h3><p><?= $total_youth ?></p></div>
        <div class="stat-card red-accent"><h3>SENIORS</h3><p><?= $total_seniors ?></p></div>
    </div>

    <main class="main-content-layout">
        <aside class="summary-sidebar">
            <?php if(!empty($employment_stats) || !empty($education_stats)): ?>
            <div class="summary-card">
                <h4 class="blue-border">EMPLOYMENT & EDUCATION</h4>
                <?php if(!empty($employment_stats)): ?>
                <p class="sub-label">Employment Status</p>
                <?php foreach($employment_stats as $stat): ?>
                    <div class="sum-item"><span><?= htmlspecialchars($stat['label']) ?></span><strong><?= $stat['value'] ?></strong></div>
                <?php endforeach; ?>
                <?php endif; ?>
                <?php if(!empty($education_stats)): ?>
                <br>
                <p class="sub-label">Educational Attainment</p>
                <?php foreach($education_stats as $stat): ?>
                    <div class="sum-item"><span><?= htmlspecialchars($stat['label']) ?></span><strong><?= $stat['value'] ?></strong></div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <?php if($preg_val > 0 || $salt_val > 0 || $rice_val > 0 || !empty($bf_stats)): ?>
            <div class="summary-card">
                <h4 class="blue-border">HEALTH PROFILING</h4>
                <?php if($preg_val > 0): ?><div class="sum-item"><span>Pregnant</span><strong><?= $preg_val ?></strong></div><?php endif; ?>
                <?php if($salt_val > 0): ?><div class="sum-item"><span>Iodized Salt User</span><strong><?= $salt_val ?></strong></div><?php endif; ?>
                <?php if($rice_val > 0): ?><div class="sum-item"><span>Iron Fortified Rice User</span><strong><?= $rice_val ?></strong></div><?php endif; ?>
                <?php if(!empty($bf_stats)): ?>
                <hr>
                <p class="sub-label">BREASTFEEDING TYPE</p>
                <?php foreach($bf_stats as $stat): ?>
                    <div class="sum-item"><span><?= htmlspecialchars($stat['label']) ?></span><strong><?= $stat['value'] ?></strong></div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <?php if(!empty($toilet_stats) || !empty($water_stats)): ?>
            <div class="summary-card">
                <h4 class="blue-border">ENVIRONMENTAL SANITATION</h4>
                <?php if(!empty($toilet_stats)): ?>
                <p class="sub-label">Toilet Types</p>
                <?php foreach($toilet_stats as $stat): ?>
                    <div class="sum-item"><span><?= htmlspecialchars($stat['label']) ?></span><strong><?= $stat['value'] ?></strong></div>
                <?php endforeach; ?>
                <?php endif; ?>
                <?php if(!empty($water_stats)): ?>
                <br>
                <p class="sub-label">Water Sources</p>
                <?php foreach($water_stats as $stat): ?>
                    <div class="sum-item"><span><?= htmlspecialchars($stat['label']) ?></span><strong><?= $stat['value'] ?></strong></div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </aside>

        <section class="visualization-container">
            <div class="chart-grid">
                <div class="chart-card">
                    <h3>Residents per Street</h3>
                    <canvas id="streetChart"></canvas>
                </div>
                <div class="chart-card">
                    <h3>Age Distribution</h3>
                    <canvas id="ageChart"></canvas>
                </div>

                <div class="chart-card">
                    <h3>Gender Distribution</h3>
                    <div class="pie-wrapper">
                        <canvas id="genderChart"></canvas>
                    </div>
                </div>
                <div class="chart-card">
                    <h3>Document Requests</h3>
                    <div class="pie-wrapper">
                        <canvas id="requestChart"></canvas>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <script>
        const palette = {
            blue: '#1545a2',
            lightBlue: '#3498db',
            green: '#2ecc71',
            yellow: '#f1c40f',
            red: '#e74c3c',
            purple: '#9b59b6',
            teal: '#1abc9c',
            pink: '#fd79a8'
        };

        const pieOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { boxWidth: 12, padding: 15, font: { size: 11 } } },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let sum = context.dataset.data.reduce((a, b) => a + b, 0);
                            let value = context.raw;
                            let percentage = sum > 0 ? ((value / sum) * 100).toFixed(1) + "%" : "0%";
                            return ` ${context.label}: ${value} (${percentage})`;
                        }
                    }
                }
            }
        };

        // 1. Street Chart
        new Chart(document.getElementById('streetChart'), {
            type: 'bar',
            data: {
                labels: <?= json_encode(array_column($street_stats, 'street')) ?>,
                datasets: [{
                    label: 'Residents',
                    data: <?= json_encode(array_column($street_stats, 'count')) ?>,
                    backgroundColor: palette.blue,
                    borderRadius: 5
                }]
            },
            options: { 
                responsive: true, 
                maintainAspectRatio: false,
                scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } 
            }
        });

        // 2. Age Distribution (SYNCED WITH INFANT STAT CARD)
        new Chart(document.getElementById('ageChart'), {
            type: 'bar',
            data: {
                labels: <?= json_encode(array_keys($age_brackets)) ?>,
                datasets: [{
                    label: 'Population Count',
                    data: <?= json_encode(array_values($age_brackets)) ?>,
                    backgroundColor: palette.green,
                    borderRadius: 5
                }]
            },
            options: { 
                responsive: true, 
                maintainAspectRatio: false,
                scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } 
            }
        });

        // 3. Gender Distribution
        new Chart(document.getElementById('genderChart'), {
            type: 'pie',
            data: {
                labels: ['Male', 'Female'],
                datasets: [{
                    data: [<?= $gender_counts['Male'] ?>, <?= $gender_counts['Female'] ?>],
                    backgroundColor: [palette.lightBlue, palette.pink]
                }]
            },
            options: pieOptions
        });

        // 4. Document Requests
        new Chart(document.getElementById('requestChart'), {
            type: 'pie',
            data: {
                labels: <?= json_encode(array_keys($doc_stats)) ?>,
                datasets: [{
                    data: <?= json_encode(array_values($doc_stats)) ?>,
                    backgroundColor: [palette.yellow, palette.teal, palette.purple, palette.red]
                }]
            },
            options: pieOptions
        });
    </script>
</body>
</html>