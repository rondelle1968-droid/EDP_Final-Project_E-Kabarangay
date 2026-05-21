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
        a.email,
        r.*,
        cur.street AS current_street,
        cur.barangay AS current_barangay,
        cur.municipality AS current_municipality,
        cur.province AS current_province,
        cur.residence_type AS current_residence_type,
        perm.street AS permanent_street,
        perm.barangay AS permanent_barangay,
        perm.municipality AS permanent_municipality,
        perm.province AS permanent_province,
        h.toilet_type,
        h.water_source,
        h.iodized_salt,
        h.iron_fortified_rice
    FROM accounts a
    JOIN residents r ON a.id = r.account_id
    LEFT JOIN address cur ON r.id = cur.resident_id AND cur.address_type = 'current'
    LEFT JOIN address perm ON r.id = perm.resident_id AND perm.address_type = 'permanent'
    LEFT JOIN household_info h ON r.id = h.resident_id
    WHERE a.id = ?
");
$stmt->execute([$accountId]);
$user = $stmt->fetch();

if (!$user) {
    die("Profile not found.");
}

$current_page = basename($_SERVER['PHP_SELF']);

// Handle Comment Actions (AJAX)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Post Comment
    if (isset($_POST['submit_comment'])) {
        $announcement_id = $_POST['announcement_id'];
        $comment_text = $_POST['comment_text'];
        $parent_id = !empty($_POST['parent_id']) ? $_POST['parent_id'] : null;

        $stmt = $pdo->prepare("INSERT INTO comments (announcement_id, account_id, comment_text, parent_id, sender_type) VALUES (?, ?, ?, ?, 'Resident')");
        $stmt->execute([$announcement_id, $accountId, $comment_text, $parent_id]);
        
        $last_id = $pdo->lastInsertId();
        $stmt = $pdo->prepare("SELECT c.*, a.username FROM comments c JOIN accounts a ON c.account_id = a.id WHERE c.id = ?");
        $stmt->execute([$last_id]);
        $new_comment = $stmt->fetch(PDO::FETCH_ASSOC);
        header('Content-Type: application/json');
        echo json_encode($new_comment);
        exit;
    }

    // Edit Comment (Permission check)
    if (isset($_POST['edit_comment'])) {
        $comment_id = $_POST['comment_id'];
        $comment_text = $_POST['comment_text'];
        $stmt = $pdo->prepare("UPDATE comments SET comment_text = ? WHERE id = ? AND account_id = ?");
        $stmt->execute([$comment_text, $comment_id, $accountId]);
        echo json_encode(['status' => 'success']);
        exit;
    }

    // Delete Comment (Permission check)
    if (isset($_POST['delete_comment'])) {
        $comment_id = $_POST['comment_id'];
        $stmt = $pdo->prepare("DELETE FROM comments WHERE (id = ? OR parent_id = ?) AND (account_id = ? OR EXISTS (SELECT 1 FROM comments WHERE id = ? AND account_id = ?))");
        $stmt->execute([$comment_id, $comment_id, $accountId, $comment_id, $accountId]);
        echo json_encode(['status' => 'success']);
        exit;
    }
}

// Fetch Unread Notification Count
$notifCountStmt = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE resident_id = ? AND is_read = 0");
$notifCountStmt->execute([$user['id']]);
$unreadCount = $notifCountStmt->fetchColumn();

// Fetch Active Announcements
$announcementsStmt = $pdo->query("SELECT * FROM announcements WHERE status = 'Active' ORDER BY created_at DESC");
$announcements = $announcementsStmt->fetchAll();

// Fetch all comments and organize them by announcement
$comments_stmt = $pdo->query("SELECT c.*, a.username FROM comments c JOIN accounts a ON c.account_id = a.id ORDER BY c.created_at ASC");
$flat_comments = $comments_stmt->fetchAll(PDO::FETCH_ASSOC);
$all_comments = [];
foreach ($flat_comments as $c) {
    $all_comments[$c['announcement_id']][] = $c;
}

$dob = new DateTime($user['dob']);
$today = new DateTime();
$age = $today->diff($dob)->y;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resident Dashboard | E-Kabarangay</title>
    <link rel="stylesheet" href="dashboard-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .toast-notification {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: #28a745;
            color: white;
            padding: 15px 25px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            font-weight: bold;
            z-index: 9999;
            opacity: 1;
            transition: opacity 0.5s ease;
            pointer-events: none;
        }
        .toast-notification.hide { opacity: 0; }
        

        .comment-actions { gap: 8px; display: flex; margin-top: 5px; }
        .action-btn { background: none; border: none; font-size: 0.75rem; color: #0056b3; cursor: pointer; padding: 0; }
        .action-btn.delete { color: #cc0000; }
        .sender-tag { font-size: 0.65rem; background: #e4e6eb; padding: 2px 6px; border-radius: 4px; margin-left: 5px; color: #65676b; vertical-align: middle; }
        

        .banner-nav .nav-item.active {
            background-color: rgba(0, 0, 0, 0.3) !important;
            border-bottom: 3px solid white;
            font-weight: bold;
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

    <?php if (isset($_GET['updated']) && isset($_SESSION['update_success'])): ?>
        <div id="updateToast" class="toast-notification">
            <?= htmlspecialchars($_SESSION['update_success']); unset($_SESSION['update_success']); ?>
        </div>
        <script>
            setTimeout(function() {
                const toast = document.getElementById('updateToast');
                if (toast) {
                    toast.classList.add('hide');
                    setTimeout(function() { if (toast && toast.parentNode) toast.remove(); }, 500);
                }
            }, 2000);
        </script>
    <?php endif; ?>

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

    <main class="dashboard-container">
        <div class="info-column">
            <div class="info-card">
                <h2 class="card-title blue-border">PERSONAL INFORMATION</h2>
                <div class="info-grid">
                    <div class="info-item"><label>First Name</label><p><?= htmlspecialchars($user['first_name']) ?></p></div>
                    <div class="info-item"><label>Sex</label><p><?= htmlspecialchars($user['sex']) ?></p></div>
                    <div class="info-item"><label>Middle Name</label><p><?= htmlspecialchars($user['middle_name']) ?></p></div>
                    <div class="info-item"><label>Age</label><p><?= $age ?></p></div>
                    <div class="info-item"><label>Last Name</label><p><?= htmlspecialchars($user['last_name']) ?></p></div>
                    <div class="info-item"><label>Date of Birth</label><p><?= date('m/d/Y', strtotime($user['dob'])) ?></p></div>
                    <div class="info-item"><label>Civil Status</label><p><?= htmlspecialchars($user['civil_status']) ?></p></div>
                    <div class="info-item"><label>Place of Birth</label><p><?= htmlspecialchars($user['place_of_birth']) ?></p></div>
                    <div class="info-item"><label>Employment</label><p><?= htmlspecialchars($user['employment_status']) ?></p></div>
                    <div class="info-item"><label>Religion</label><p><?= htmlspecialchars($user['religion']) ?></p></div>
                    <div class="info-item"><label>Contact</label><p class="highlight"><?= htmlspecialchars($user['contact_no']) ?></p></div>
                    <div class="info-item"><label>Email</label><p class="highlight"><?= htmlspecialchars($user['email']) ?></p></div>
                </div>
            </div>

            <?php if ($user['sex'] === 'Female'): ?>
            <div class="info-card">
                <h2 class="card-title red-border">MATERNAL HEALTH STATUS</h2>
                <div class="info-grid">
                    <div class="info-item"><label>Family Planning</label><p><?= htmlspecialchars($user['family_planning'] ?? 'Not specified') ?></p></div>
                    <div class="info-item"><label>Pregnancy Status</label><p><?= htmlspecialchars($user['pregnancy_status'] ?? 'Not specified') ?></p></div>
                    <?php if ($user['pregnancy_status'] === 'Delivered and Breastfeeding'): ?>
                    <div class="info-item"><label>Breastfeeding Type</label><p><?= htmlspecialchars($user['breastfeeding_type'] ?? 'Not specified') ?></p></div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <div class="info-card">
                <h2 class="card-title blue-border">ADDRESS</h2>
                <div class="address-section">
                    <label>Current Address</label>
                    <p><?= htmlspecialchars($user['current_street'] . ', ' . $user['current_barangay'] . ', ' . $user['current_municipality'] . ', ' . $user['current_province']) ?></p>
                    <p class="residence-type highlighted-type"><?= htmlspecialchars($user['current_residence_type']) ?></p>
                    <label style="margin-top:15px; display:block;">Permanent Address</label>
                    <p><?= htmlspecialchars($user['permanent_street'] . ', ' . $user['permanent_barangay'] . ', ' . $user['permanent_municipality'] . ', ' . $user['permanent_province']) ?></p>
                </div>
            </div>

            <div class="info-card">
                <h2 class="card-title yellow-border">HOUSEHOLD INFORMATION</h2>
                <div class="list-info">
                    <div class="list-row"><span>Toilet Type</span><strong><?= htmlspecialchars($user['toilet_type']) ?></strong></div>
                    <div class="list-row"><span>Water Source</span><strong><?= htmlspecialchars($user['water_source']) ?></strong></div>
                    <div class="list-row"><span>Using Iodized Salt</span><strong><?= htmlspecialchars($user['iodized_salt']) ?></strong></div>
                    <div class="list-row"><span>Using Iron Fortified Rice</span><strong><?= htmlspecialchars($user['iron_fortified_rice']) ?></strong></div>
                </div>
            </div>
        </div>

        <div class="announcements-column">
            <h2 class="card-title">ANNOUNCEMENTS</h2>
            <div class="announcement-list">
                <?php if (empty($announcements)): ?>
                    <p style="text-align: center; color: #888; padding: 20px;">No active announcements.</p>
                <?php endif; ?>
                <?php foreach($announcements as $a): ?>
                    <div class="post-card" onclick="viewDetails(<?= htmlspecialchars(json_encode($a)) ?>, <?= htmlspecialchars(json_encode($all_comments[$a['announcement_id']] ?? [])) ?>)">
                        <div class="post-header">
                            <h3 class="post-title"><?= htmlspecialchars($a['title']) ?></h3>
                            <span class="post-date"><?= date('M d, Y', strtotime($a['created_at'])) ?></span>
                        </div>
                        <p class="post-preview"><?= htmlspecialchars(substr($a['description'], 0, 100)) ?>...</p>
                        <?php if($a['media_file']): ?>
                            <div class="post-media-preview">
                                <?php if(preg_match('/\.(mp4|webm)$/i', $a['media_file'])): ?>
                                    <video src="<?= $a['media_file'] ?>"></video>
                                <?php else: ?>
                                    <img src="<?= $a['media_file'] ?>" alt="Preview">
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </main>

    <div id="detailModal" class="modal">
        <div class="modal-content fb-layout">
            <span class="close" onclick="closeDetailModal()">&times;</span>
            <div class="fb-left">
                <div id="detailMedia"></div>
            </div>
            <div class="fb-right-unified">
                <div class="scrollable-content">
                    <div class="fb-header-section">
                        <div class="post-date" id="detailTime"></div>
                        <div class="expandable-container">
                            <h2 id="detailTitle" class="title-text" style="color: var(--text-blue); margin: 10px 0;"></h2>
                            <button id="seeMoreTitle" class="see-more-link" onclick="toggleText('detailTitle', 'seeMoreTitle', 40)">See More</button>
                        </div>
                        <div class="expandable-container">
                            <p id="detailDesc" class="desc-text" style="white-space: pre-wrap; font-size: 0.95rem;"></p>
                            <button id="seeMoreDesc" class="see-more-link" onclick="toggleText('detailDesc', 'seeMoreDesc', 150)">See More</button>
                        </div>
                    </div>
                    <div class="comments-section">
                        <h3 class="comments-count">Comments</h3>
                        <div id="commentsList"></div>
                    </div>
                </div>
                <div class="comment-input-area">
                    <div id="replyIndicator" style="display:none; padding: 5px 15px; background: #f0f2f5; font-size: 0.85rem;">
                        Replying to <span id="replyingToName"></span>
                        <button onclick="cancelReply()" style="border:none; background:none; color:red; cursor:pointer; margin-left:10px;">Cancel</button>
                    </div>
                    <form id="commentForm" class="comment-box">
                        <input type="hidden" name="submit_comment" value="1">
                        <input type="hidden" name="announcement_id" id="commentAnnId">
                        <input type="hidden" name="parent_id" id="commentParentId">
                        <textarea name="comment_text" id="commentText" placeholder="Write a comment..." required></textarea>
                        <button type="submit" class="btn-send-icon"><i class="fas fa-paper-plane"></i></button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        const currentUserId = <?= $accountId ?>;

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

        function viewDetails(ann, comments) {
            document.getElementById('detailTitle').classList.remove('expanded');
            document.getElementById('detailDesc').classList.remove('expanded');
            
            document.getElementById('detailTitle').innerText = ann.title;
            document.getElementById('detailDesc').innerText = ann.description;
            document.getElementById('detailTime').innerText = 'Posted on: ' + new Date(ann.created_at).toLocaleString();
            document.getElementById('commentAnnId').value = ann.announcement_id;

            const mediaDiv = document.getElementById('detailMedia');
            mediaDiv.innerHTML = '';
            if (ann.media_file) {
                if (ann.media_file.match(/\.(mp4|webm)$/i)) {
                    mediaDiv.innerHTML = `<video src="${ann.media_file}" controls style="max-width:100%; max-height:100%;"></video>`;
                } else {
                    mediaDiv.innerHTML = `<img src="${ann.media_file}" style="max-width:100%; max-height:100%;">`;
                }
            }
            renderComments(comments);
            document.getElementById('detailModal').style.display = 'flex';
            
            checkTextLength('detailTitle', 'seeMoreTitle', 40);
            checkTextLength('detailDesc', 'seeMoreDesc', 150);
        }

        function renderComments(comments) {
            const list = document.getElementById('commentsList');
            list.innerHTML = '';
            if (comments.length === 0) {
                list.innerHTML = '<p class="no-comments">No comments yet.</p>';
                return;
            }

            const parents = comments.filter(c => !c.parent_id);
            const children = comments.filter(c => c.parent_id);

            parents.forEach(p => {
                list.innerHTML += createCommentHTML(p);
                const rContainer = document.createElement('div');
                rContainer.className = 'replies-container';
                rContainer.id = `replies-${p.id}`;
                rContainer.style = "margin-left:35px; border-left: 2px solid #ddd; padding-left:10px;";
                list.appendChild(rContainer);
                children.filter(c => c.parent_id == p.id).forEach(c => {
                    rContainer.innerHTML += createCommentHTML(c, true);
                });
            });
        }

        function createCommentHTML(c, isReply = false) {
            const isOwner = (parseInt(c.account_id) === currentUserId);
            const actions = isOwner ? `
                <div class="comment-actions">
                    <button onclick="startEditComment(${c.id}, \`${c.comment_text.replace(/'/g, "\\'")}\`)" class="action-btn">Edit</button>
                    <button onclick="deleteComment(${c.id})" class="action-btn delete">Delete</button>
                </div>
            ` : '';

            return `
                <div class="comment-item" id="comment-${c.id}" style="margin-bottom:12px;">
                    <div style="background:#f0f2f5; padding:8px 12px; border-radius:15px; display:inline-block; max-width:90%;">
                        <strong style="font-size:0.85rem;">${c.username} <span class="sender-tag">${c.sender_type}</span></strong>
                        <div id="text-${c.id}" style="font-size:0.9rem;">${c.comment_text}</div>
                    </div>
                    <div style="font-size:0.75rem; color:#666; margin-top:2px; margin-left:10px;">
                        ${new Date(c.created_at).toLocaleString()}
                        ${!isReply ? `• <button style="border:none; background:none; font-weight:bold; color:#666; cursor:pointer;" onclick="replyTo(${c.id}, '${c.username}')">Reply</button>` : ''}
                    </div>
                    ${actions}
                </div>
            `;
        }

        function replyTo(id, name) {
            document.getElementById('commentParentId').value = id;
            document.getElementById('replyIndicator').style.display = 'block';
            document.getElementById('replyingToName').innerText = name;
            document.getElementById('commentText').focus();
        }

        function cancelReply() {
            document.getElementById('commentParentId').value = '';
            document.getElementById('replyIndicator').style.display = 'none';
        }

        function startEditComment(id, oldText) {
            const newText = prompt("Edit your comment:", oldText);
            if (newText && newText !== oldText) {
                const formData = new FormData();
                formData.append('edit_comment', '1');
                formData.append('comment_id', id);
                formData.append('comment_text', newText);
                fetch('dashboard.php', { method: 'POST', body: formData })
                .then(() => { document.getElementById(`text-${id}`).innerText = newText; });
            }
        }

        function deleteComment(id) {
            if (confirm("Delete this comment?")) {
                const formData = new FormData();
                formData.append('delete_comment', '1');
                formData.append('comment_id', id);
                fetch('dashboard.php', { method: 'POST', body: formData })
                .then(() => { 
                    document.getElementById(`comment-${id}`).remove();
                    const replies = document.getElementById(`replies-${id}`);
                    if (replies) replies.remove();
                });
            }
        }

        function closeDetailModal() { document.getElementById('detailModal').style.display = 'none'; cancelReply(); }

        document.getElementById('commentForm').onsubmit = function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const parentId = document.getElementById('commentParentId').value;

            fetch('dashboard.php', { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                const noComments = document.querySelector('.no-comments');
                if (noComments) noComments.remove();
                if (parentId) {
                    document.getElementById(`replies-${parentId}`).innerHTML += createCommentHTML(data, true);
                } else {
                    document.getElementById('commentsList').innerHTML += createCommentHTML(data) + `<div class="replies-container" id=\"replies-${data.id}\" style=\"margin-left:35px; border-left: 2px solid #ddd; padding-left:10px;\"></div>`;
                }
                this.reset();
                cancelReply();
            });
        };

        function toggleText(id, btnId, limit) {
            const textElement = document.getElementById(id);
            const btn = document.getElementById(btnId);
            const isExpanded = textElement.classList.toggle('expanded');
            btn.innerText = isExpanded ? 'See Less' : 'See More';
        }

        function checkTextLength(id, btnId, limit) {
            const text = document.getElementById(id).innerText;
            const btn = document.getElementById(btnId);
            if (text.length <= limit) {
                btn.style.display = 'none';
            } else {
                btn.style.display = 'inline';
                btn.innerText = 'See More';
            }
        }

        window.onclick = function(event) { if (event.target == document.getElementById('detailModal')) closeDetailModal(); }
    </script>
</body>
</html>