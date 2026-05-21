<?php
require_once 'config.php';

if (!isset($_SESSION['account_id'])) {
    header("Location: login.php");
    exit;
}

$accountId = $_SESSION['account_id'];


$stmt = $pdo->prepare("
    SELECT 
        a.username,
        r.id,
        r.first_name,
        r.last_name,
        r.id_picture
    FROM accounts a
    JOIN residents r ON a.id = r.account_id
    WHERE a.id = ?
");
$stmt->execute([$accountId]);
$user = $stmt->fetch();

if (!$user) {
    die("Profile not found.");
}

$current_page = basename($_SERVER['PHP_SELF']);

// Fetch Unread Notification Count
$notifCountStmt = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE resident_id = ? AND is_read = 0");
$notifCountStmt->execute([$user['id']]);
$unreadCount = $notifCountStmt->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barangay Hotline | E-Kabarangay</title>
    <link rel="stylesheet" href="dashboard-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        
        .banner-nav .nav-item.active {
            background-color: rgba(0, 0, 0, 0.3) !important;
            border-bottom: 3px solid white;
            font-weight: bold;
        }

        
        .hotline-container {
            max-width: 800px;
            margin: 30px auto;
            padding: 0 20px;
        }

        .hotline-card {
            background: white;
            border-radius: 10px;
            padding: 40px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }

        .main-heading {
            font-size: 1.6rem;
            color: #004085; 
            font-weight: bold;
            margin-top: 0;
            margin-bottom: 30px;
            letter-spacing: 0.5px;
        }

        .section-divider {
            border: 0;
            height: 1px;
            background: #e0e0e0;
            margin: 30px 0;
        }

        .hotline-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 25px;
        }

        @media (max-width: 600px) {
            .hotline-grid {
                grid-template-columns: 1fr;
            }
        }

        .hotline-item {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .agency-name {
            font-weight: bold;
            color: #0056b3; 
            font-size: 1.05rem;
            margin: 0;
        }

        .phone-number {
            color: #007bff; 
            font-size: 0.95rem;
            margin: 0;
            font-family: monospace, sans-serif;
        }

        .meta-section h3 {
            font-size: 1.1rem;
            color: #004085;
            margin-top: 0;
            margin-bottom: 10px;
        }

        .meta-text {
            color: #333;
            font-size: 1rem;
            margin: 0;
        }
    </style>
</head>
<body>
    <header class="dashboard-header">
        <div class="user-welcome">
            <div class="profile-img-container">
                <img src="Upload/id_pictures/<?= htmlspecialchars($user['id_picture']) ?>" alt="Profile">
                <a href="profile.php" class="edit-icon" title="Update Profile">✎</a>
            </div>
            <div class="welcome-text">
                <p>Welcome back,</p>
                <h1><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></h1>
            </div>
        </div>
        
        <div class="header-actions">
            <a href="requests.php" class="btn-request" style="text-decoration: none;">Request</a>
            <a href="notifications.php" class="notification-bell" style="text-decoration: none; color: white;">
                <span class="bell-icon">🔔</span>
                <?php if ($unreadCount > 0): ?>
                    <span class="badge"><?= $unreadCount ?></span>
                <?php endif; ?>
            </a>
            <div class="ph-time">
                <p>Philippine Standard Time:</p>
                <span id="current-time"></span>
            </div>
        </div>
    </header>

    <section class="banner-section">
        <div class="banner-content">
            <div class="banner-text">
                <h1>BARANGAY ONLINE SERVICES</h1>
                <p>Your Barangay's digital partner!</p>
            </div>
            <img src="BHPS logo.png" alt="Logo" class="banner-logo">
        </div>
        <nav class="banner-nav">
            <a href="dashboard.php" class="nav-item <?= ($current_page == 'dashboard.php') ? 'active' : ''; ?>">Dashboard</a>
            <a href="ordinance.php" class="nav-item <?= ($current_page == 'ordinance.php') ? 'active' : ''; ?>">Ordinances</a>
            <a href="barangay_hotline.php" class="nav-item <?= ($current_page == 'barangay_hotline.php') ? 'active' : ''; ?>">Barangay Hotline</a>
            <a href="about.php" class="nav-item <?= ($current_page == 'about.php') ? 'active' : ''; ?>">About Us</a>
        </nav>
    </section>

    <main class="hotline-container">
        <div class="hotline-card">
            <h2 class="main-heading">CONTACT INFORMATION</h2>
            
            <div class="hotline-grid">
                <div class="hotline-item">
                    <p class="agency-name">Municipal Disaster Risk Reduction and Management Office (MDRRMO)</p>
                    <p class="phone-number">0977-337-9723</p>
                </div>
                <div class="hotline-item">
                    <p class="agency-name">Bureau of Fire Protection (BFP)</p>
                    <p class="phone-number">0915-832-4654</p>
                </div>
                <div class="hotline-item">
                    <p class="agency-name">Armed Forces of the Philippines (AFP)</p>
                    <p class="phone-number">0916-630-6292</p>
                </div>
                <div class="hotline-item">
                    <p class="agency-name">Philippine National Police (PNP)</p>
                    <p class="phone-number">0998-598-5839</p>
                </div>
                <div class="hotline-item">
                    <p class="agency-name">Occidental Mindoro Provincial Hospital (OMPH)</p>
                    <p class="phone-number">0917-568-9417</p>
                </div>
                <div class="hotline-item">
                    <p class="agency-name">Municipal Health Center</p>
                    <p class="phone-number">0956-478-1617</p>
                </div>
            </div>

            <hr class="section-divider">

            <div class="meta-section">
                <h3>Email Address</h3>
                <p class="meta-text" style="color: #0056b3; font-weight: 500;">ekabarangay@gmail.com</p>
            </div>

            <hr class="section-divider">

            <div class="meta-section">
                <h3>E-Kabarangay Office Address</h3>
                <p class="meta-text">Barangay Hall, Barangay 3, Mamburao, Occidental Mindoro.</p>
            </div>
        </div>
    </main>

    <script>
        function updateTime() {
            const now = new Date();
            document.getElementById('current-time').innerText = now.toLocaleString('en-US', { 
                timeZone: 'Asia/Manila',
                weekday: 'long', year: 'numeric', month: 'long', day: 'numeric',
                hour: '2-digit', minute: '2-digit', second: '2-digit'
            });
        }
        setInterval(updateTime, 1000);
        updateTime();
    </script>
</body>
</html>