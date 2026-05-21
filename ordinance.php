<?php
require_once 'config.php';

if (!isset($_SESSION['account_id'])) {
    header("Location: login.php");
    exit;
}

$accountId = $_SESSION['account_id'];

$stmt = $pdo->prepare("
    SELECT a.username, a.email, r.*
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


$ordinances = [
    [
        "title" => "Barangay Ordinance No. 01",
        "subtitle" => "An Ordinance Prohibiting the Drinking of Liquor and other Intoxicating Beverages in Public Places Within Barangay 3, Mamburao, Occidental Mindoro.",
        "images" => ["BO_1_1-4.jpg", "BO_1_2-4.jpg", "BO_1_3-4.jpg", "BO_1_4-4.jpg"]
    ],
    [
        "title" => "Barangay Ordinance No. 02",
        "subtitle" => "An Ordinance Prohibiting Topless or Half-Naked Appearance in Public Places within Barangay 3, Mamburao, Occidental Mindoro.",
        "images" => ["BO_2_1-4.jpg", "BO_2_2-4.jpg", "BO_2_3-4.jpg", "BO_2_4-4.jpg"]
    ],
    [
        "title" => "Barangay Ordinance No. 03",
        "subtitle" => "An Ordinance Regulating the Use of Videoke Machines and Other Amplified Audio Devices within Barangay 3, Mamburao, Occidental Mindoro.",
        "images" => ["BO_3_1-4.jpg", "BO_3_2-4.jpg", "BO_3_3-4.jpg", "BO_3_4-4.jpg"]
    ],
    [
        "title" => "Barangay Ordinance No. 04",
        "subtitle" => "An Ordinance Instituting Curfew Hours for Minors in Barangay 3, Mamburao, Occidental Mindoro.",
        "images" => ["BO_4_1-4.jpg", "BO_4_2-4.jpg", "BO_4_3-4.jpg", "BO_4_4-4.jpg"]
    ],
    [
        "title" => "Barangay Ordinance No. 05",
        "subtitle" => "An Ordinance Prohibiting The Sale, Purchase, and Possession of Liquor and Cigarettes by Minors in Barangay 3, Mamburao, Occidental Mindoro.",
        "images" => ["BO_5_1-3.jpg", "BO_5_2-3.jpg", "BO_5_3-3.jpg"]
    ]
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barangay Ordinances | E-Kabarangay</title>
    <link rel="stylesheet" href="dashboard-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .ordinance-main-container {
            padding: 30px 50px;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .page-title-section {
            margin-bottom: 25px;
            border-bottom: 3px solid var(--dash-blue);
            padding-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .page-title-section h2 {
            margin: 0;
            color: var(--text-blue);
            font-size: 1.8rem;
        }

        .btn-back {
            background-color: var(--dash-blue);
            color: white;
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 5px;
            font-weight: bold;
            font-size: 0.9rem;
            transition: background 0.2s;
        }

        .btn-back:hover {
            background-color: var(--text-blue);
        }

        .ordinance-grid {
            display: flex;
            flex-direction: column;
            gap: 30px;
        }

        .ordinance-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            border-left: 5px solid var(--dash-blue);
        }

        .ordinance-header {
            margin-bottom: 15px;
        }

        .ordinance-card h3 {
            margin: 0 0 8px 0;
            color: var(--text-blue);
            font-size: 1.4rem;
        }

        .ordinance-subtitle {
            margin: 0;
            color: #444;
            font-size: 1rem;
            line-height: 1.5;
            font-weight: 500;
        }

        .image-gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }

        .gallery-item {
            position: relative;
            aspect-ratio: 3 / 4;
            overflow: hidden;
            border-radius: 8px;
            border: 1px solid #ddd;
            cursor: pointer;
            box-shadow: 0 2px 6px rgba(0,0,0,0.06);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .gallery-item:hover {
            transform: scale(1.04);
            box-shadow: 0 5px 12px rgba(0,0,0,0.15);
        }

        .gallery-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .lightbox-modal {
            display: none;
            position: fixed;
            z-index: 3000;
            left: 0; top: 0;
            width: 100%; height: 100%;
            background: rgba(0,0,0,0.9);
            justify-content: center;
            align-items: center;
        }

        .lightbox-content {
            max-width: 90%;
            max-height: 85%;
            border-radius: 4px;
            box-shadow: 0 4px 25px rgba(255,255,255,0.15);
        }

        .lightbox-close {
            position: absolute;
            top: 20px;
            right: 30px;
            color: white;
            font-size: 40px;
            font-weight: bold;
            cursor: pointer;
            user-select: none;
        }


        .banner-nav .nav-item.active {
            background-color: rgba(0, 0, 0, 0.3) !important;
            border-bottom: 3px solid white;
            font-weight: bold;
        }

        @media(max-width: 768px) {
            .ordinance-main-container { padding: 15px; }
            .image-gallery-grid { grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); }
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

    <main class="ordinance-main-container">
        <div class="page-title-section">
            <h2>BARANGAY ORDINANCES</h2>
            <a href="dashboard.php" class="btn-back"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
        </div>

        <div class="ordinance-grid">
            <?php foreach ($ordinances as $ord): ?>
                <div class="ordinance-card">
                    <div class="ordinance-header">
                        <h3><?= htmlspecialchars($ord['title']) ?></h3>
                        <p class="ordinance-subtitle"><?= htmlspecialchars($ord['subtitle']) ?></p>
                    </div>
                    
                    <div class="image-gallery-grid">
                        <?php foreach ($ord['images'] as $imgFile): ?>
                            <div class="gallery-item" onclick="openLightbox('Upload/ordinance/<?= $imgFile ?>')">
                                <img src="Upload/ordinance/<?= htmlspecialchars($imgFile) ?>" alt="Ordinance Document Page">
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </main>

    <div id="lightboxModal" class="lightbox-modal" onclick="closeLightbox()">
        <span class="lightbox-close" onclick="closeLightbox()">&times;</span>
        <img class="lightbox-content" id="lightboxTargetImg" alt="Enlarged Document View">
    </div>

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

        function openLightbox(src) {
            const modal = document.getElementById('lightboxModal');
            const targetImg = document.getElementById('lightboxTargetImg');
            targetImg.src = src;
            modal.style.display = 'flex';
        }

        function closeLightbox() {
            document.getElementById('lightboxModal').style.display = 'none';
        }
    </script>
</body>
</html>