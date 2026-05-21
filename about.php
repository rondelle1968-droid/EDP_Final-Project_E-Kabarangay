<?php
// about.php - About Us page for E-Kabarangay
require_once 'config.php';

if (!isset($_SESSION['account_id'])) {
    header("Location: login.php");
    exit;
}

$accountId = $_SESSION['account_id'];

// Fetch resident basic info and notification count
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

// Unread notifications count
$notifCountStmt = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE resident_id = ? AND is_read = 0");
$notifCountStmt->execute([$user['id']]);
$unreadCount = $notifCountStmt->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us | E-Kabarangay</title>
    <link rel="stylesheet" href="dashboard-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        
        .banner-nav .nav-item.active {
            background-color: rgba(0, 0, 0, 0.3) !important;
            border-bottom: 3px solid white;
            font-weight: bold;
        }

        
        .about-container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }

        .about-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            margin-bottom: 30px;
        }

        .about-title {
            font-size: 2rem;
            color: var(--text-blue);
            margin-bottom: 20px;
            border-left: 6px solid var(--dash-blue);
            padding-left: 20px;
        }

        .about-subtitle {
            font-size: 1.2rem;
            color: #444;
            margin-bottom: 15px;
            font-weight: 600;
        }

        .about-text {
            font-size: 1rem;
            line-height: 1.7;
            color: #333;
            text-align: justify;
        }

        .mission-vision-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin: 30px 0;
        }

        .mv-card {
            background: #f8faff;
            padding: 25px;
            border-radius: 16px;
            border-top: 5px solid var(--dash-blue);
            transition: transform 0.3s;
        }

        .mv-card:hover {
            transform: translateY(-5px);
        }

        .mv-card h3 {
            color: var(--text-blue);
            font-size: 1.5rem;
            margin-bottom: 15px;
        }

        .mv-card p {
            color: #555;
            line-height: 1.6;
        }

        .why-choose-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            margin-top: 20px;
        }

        .feature-card {
            background: white;
            border-radius: 16px;
            padding: 25px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
            border: 1px solid #eef2f7;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.1);
        }

        .feature-icon {
            font-size: 2.5rem;
            color: var(--dash-blue);
            margin-bottom: 15px;
        }

        .feature-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--text-blue);
            margin-bottom: 10px;
        }

        .feature-desc {
            font-size: 0.95rem;
            color: #666;
            line-height: 1.5;
        }

        @media (max-width: 768px) {
            .mission-vision-grid {
                grid-template-columns: 1fr;
            }
            .about-card {
                padding: 25px;
            }
            .about-title {
                font-size: 1.6rem;
            }
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

    <main class="about-container">
        <div class="about-card">
            <h2 class="about-title">About E-Kabarangay</h2>
            <p class="about-text">
                E-Kabarangay is an innovative digital platform developed to modernize barangay governance, enhance resident engagement, and streamline the delivery of public services. Born from the need for efficient, transparent, and accessible local administration, this system integrates resident profiling, document request management, health monitoring, announcements, and community feedback into one unified ecosystem.
                <br><br>
                By bridging the gap between the barangay hall and its constituents, E-Kabarangay empowers citizens to access essential services anytime, anywhere — reducing long queues, minimizing paperwork, and fostering a culture of responsive governance.
            </p>
        </div>

        <div class="mission-vision-grid">
            <div class="mv-card">
                <h3><i class="fas fa-bullseye" style="margin-right: 10px;"></i> Our Mission</h3>
                <p>To empower barangay communities through digital transformation by providing a secure, efficient, and user‑centric platform that simplifies administrative processes, promotes health and well‑being, and strengthens the bond between residents and local government — ensuring that every Filipino feels heard, served, and valued.</p>
            </div>
            <div class="mv-card">
                <h3><i class="fas fa-eye" style="margin-right: 10px;"></i> Our Vision</h3>
                <p>A connected, transparent, and progressive barangay where every resident can actively participate in governance, access services seamlessly, and contribute to the development of a digitally‑enabled, inclusive community — setting the standard for e‑governance at the grassroots level.</p>
            </div>
        </div>

        <div class="about-card">
            <h2 class="about-title">Why Choose E-Kabarangay?</h2>
            <div class="why-choose-grid">
                <div class="feature-card">
                    <div class="feature-icon"><i class="fas fa-shield-alt"></i></div>
                    <h3 class="feature-title">Security & Privacy</h3>
                    <p class="feature-desc">Your personal data is protected with industry‑standard encryption and strict access controls. We adhere to data privacy laws, ensuring that your information is used only for official barangay transactions.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><i class="fas fa-mobile-alt"></i></div>
                    <h3 class="feature-title">Accessibility</h3>
                    <p class="feature-desc">Available 24/7 from any device — desktop, tablet, or smartphone. Residents can request documents, view announcements, and check health records without visiting the barangay hall.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><i class="fas fa-chart-line"></i></div>
                    <h3 class="feature-title">Transparency</h3>
                    <p class="feature-desc">Real‑time tracking of document requests, public announcements, and barangay ordinances. Residents receive notifications at every step, fostering trust and accountability.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><i class="fas fa-users"></i></div>
                    <h3 class="feature-title">User‑Oriented</h3>
                    <p class="feature-desc">Designed with the community in mind — intuitive interface, clear navigation, and responsive support. Every feature addresses real needs of residents and barangay staff.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><i class="fas fa-rocket"></i></div>
                    <h3 class="feature-title">Efficiency</h3>
                    <p class="feature-desc">Automated workflows reduce manual processing time. From registration to document approval, E-Kabarangay cuts red tape and speeds up service delivery.</p>
                </div>
            </div>
        </div>

        <div class="about-card" style="text-align: center;">
            <h2 class="about-title" style="border-left: none; text-align: center;">Join the Digital Transformation</h2>
            <p class="about-text" style="text-align: center; max-width: 700px; margin: 0 auto;">
                E-Kabarangay is more than just a system — it's a movement towards smarter, more responsive local governance. Whether you are a resident needing a barangay clearance, a health worker monitoring maternal care, or an administrator managing requests, our platform is here to serve you.
            </p>
            <div style="margin-top: 30px;">
                <a href="dashboard.php" class="btn-request" style="background: var(--dash-blue); color: white; text-decoration: none; padding: 12px 30px;">Back to Dashboard</a>
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