<?php
require_once 'config.php';

if (!isset($_SESSION['account_id']) || $_SESSION['is_admin'] != 1) {
    header("Location: login.php");
    exit;
}

$current_user_id = $_SESSION['account_id'];

// Handle Form Submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Post a Comment OR Reply
    if (isset($_POST['submit_comment'])) {
        $announcement_id = $_POST['announcement_id'];
        $comment_text = $_POST['comment_text'];
        $account_id = $current_user_id;
        $parent_id = !empty($_POST['parent_id']) ? $_POST['parent_id'] : null;

        $stmt = $pdo->prepare("INSERT INTO comments (announcement_id, account_id, comment_text, parent_id, sender_type) VALUES (?, ?, ?, ?, 'Admin')");
        $stmt->execute([$announcement_id, $account_id, $comment_text, $parent_id]);
        
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            $last_id = $pdo->lastInsertId();
            $stmt = $pdo->prepare("SELECT c.*, a.username FROM comments c JOIN accounts a ON c.account_id = a.id WHERE c.id = ?");
            $stmt->execute([$last_id]);
            $new_comment = $stmt->fetch(PDO::FETCH_ASSOC);
            header('Content-Type: application/json');
            echo json_encode($new_comment);
            exit;
        }
        header("Location: announcements.php" . (isset($_GET['view']) ? "?view=".$_GET['view'] : ""));
        exit;
    }

    // Edit Comment (Permission check included)
    if (isset($_POST['edit_comment'])) {
        $comment_id = $_POST['comment_id'];
        $comment_text = $_POST['comment_text'];
        $stmt = $pdo->prepare("UPDATE comments SET comment_text = ? WHERE id = ? AND account_id = ?");
        $stmt->execute([$comment_text, $comment_id, $current_user_id]);
        echo json_encode(['status' => 'success']);
        exit;
    }

    // Delete Comment (Permission check included)
    if (isset($_POST['delete_comment'])) {
        $comment_id = $_POST['comment_id'];
        $stmt = $pdo->prepare("DELETE FROM comments WHERE (id = ? OR parent_id = ?) AND (account_id = ? OR EXISTS (SELECT 1 FROM comments WHERE id = ? AND account_id = ?))");
        $stmt->execute([$comment_id, $comment_id, $current_user_id, $comment_id, $current_user_id]);
        echo json_encode(['status' => 'success']);
        exit;
    }

    // Publish or Update Announcement
    if (isset($_POST['action']) && ($_POST['action'] == 'publish' || $_POST['action'] == 'update')) {
        $title = $_POST['title'];
        $description = $_POST['description'];
        $media_file = $_POST['existing_media'] ?? '';

        if (!empty($_FILES['media']['name'])) {
            $target_dir = "Upload/announcement/";
            if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
            $media_file = $target_dir . time() . "_" . basename($_FILES["media"]["name"]);
            move_uploaded_file($_FILES["media"]["tmp_name"], $media_file);
        }

        if ($_POST['action'] == 'publish') {
            $stmt = $pdo->prepare("INSERT INTO announcements (title, description, media_file, status) VALUES (?, ?, ?, 'active')");
            $stmt->execute([$title, $description, $media_file]);
        } else {
            $stmt = $pdo->prepare("UPDATE announcements SET title = ?, description = ?, media_file = ? WHERE announcement_id = ?");
            $stmt->execute([$title, $description, $media_file, $_POST['id']]);
        }
        header("Location: announcements.php"); 
        exit;
    }

    // Archive / Restore Actions
    if (isset($_POST['change_status'])) {
        $new_status = $_POST['status'];
        $stmt = $pdo->prepare("UPDATE announcements SET status = ? WHERE announcement_id = ?");
        $stmt->execute([$new_status, $_POST['id']]);
        
        if ($new_status == 'archived') {
            header("Location: announcements.php"); 
        } else {
            header("Location: announcements.php?view=archived");
        }
        exit;
    }

    // Permanent Delete
    if (isset($_POST['delete_permanent'])) {
        $stmt = $pdo->prepare("DELETE FROM announcements WHERE announcement_id = ?");
        $stmt->execute([$_POST['id']]);
        header("Location: announcements.php?view=archived"); 
        exit;
    }
}

$view = $_GET['view'] ?? 'active';
// Fetch announcements
$stmt = $pdo->prepare("SELECT * FROM announcements WHERE status = ? ORDER BY created_at DESC");
$stmt->execute([$view]);
$announcements = $stmt->fetchAll();

// Fetch comments
$comments_stmt = $pdo->query("SELECT c.*, a.username FROM comments c JOIN accounts a ON c.account_id = a.id ORDER BY c.created_at ASC");
$flat_comments = $comments_stmt->fetchAll(PDO::FETCH_ASSOC);

$all_comments = [];
foreach ($flat_comments as $c) {
    $all_comments[$c['announcement_id']][] = $c;
}

// Fetch dynamic state data
$pending_count = $pdo->query("SELECT COUNT(*) FROM residents WHERE status = 'Pending'")->fetchColumn();
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Announcements | Admin</title>
    <link rel="stylesheet" href="admin_portal-style.css">
    <link rel="stylesheet" href="announcements-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header class="admin-header">
        <div class="logo-section">
            <img src="BHPS logo.png" class="portal-logo" alt="Logo">
            <h1>ANNOUNCEMENT</h1>
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

    <main class="announcement-container">
        <div class="action-bar">
            <button class="btn-primary" onclick="openModal()"><i class="fas fa-plus"></i> Post Announcement</button>
            <div class="toggle-view">
                <a href="announcements.php" class="view-link <?= $view == 'active' ? 'active' : '' ?>">All Announcements</a>
                <a href="announcements.php?view=archived" class="view-link <?= $view == 'archived' ? 'active' : '' ?>">View Archived Announcement</a>
            </div>
        </div>

        <h2 class="section-title"><?= $view == 'active' ? 'All Announcements' : 'Archived Announcements' ?></h2>

        <div class="announcement-grid">
            <?php if (empty($announcements)): ?>
                <p style="text-align: center; grid-column: 1/-1; color: #888; padding: 50px;">No announcements to display.</p>
            <?php endif; ?>

            <?php foreach ($announcements as $a): ?>
            <div class="announcement-card" onclick="viewDetails(<?= htmlspecialchars(json_encode($a)) ?>, <?= htmlspecialchars(json_encode($all_comments[$a['announcement_id']] ?? [])) ?>)">
                <?php if (!empty($a['media_file']) && file_exists($a['media_file'])): ?>
                    <div class="card-media">
                        <?php if (preg_match('/\.(mp4|webm)$/i', $a['media_file'])): ?>
                            <video src="<?= $a['media_file'] ?>"></video>
                        <?php else: ?>
                            <img src="<?= $a['media_file'] ?>" alt="Media">
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                <div class="card-content">
                    <div class="post-meta">
                        <i class="far fa-clock"></i> Posted on: <?= date('M d, Y | h:i A', strtotime($a['created_at'])) ?>
                    </div>
                    <h3><?= htmlspecialchars($a['title']) ?></h3>
                    <p><?= nl2br(htmlspecialchars(substr($a['description'], 0, 100))) ?><?= strlen($a['description']) > 100 ? '...' : '' ?></p>
                    <div class="card-actions" onclick="event.stopPropagation()">
                        <?php if ($view == 'active'): ?>
                            <button onclick='editAnnouncement(<?= json_encode($a) ?>)' class="edit-btn"><i class="fas fa-edit"></i> Edit</button>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="id" value="<?= $a['announcement_id'] ?>">
                                <input type="hidden" name="status" value="archived">
                                <button type="submit" name="change_status" class="archive-btn"><i class="fas fa-archive"></i> Archive</button>
                            </form>
                        <?php else: ?>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="id" value="<?= $a['announcement_id'] ?>">
                                <input type="hidden" name="status" value="active">
                                <button type="submit" name="change_status" class="restore-btn"><i class="fas fa-undo"></i> Restore</button>
                            </form>
                            <form method="POST" style="display:inline;" onsubmit="return confirm('Permanently delete this announcement?')">
                                <input type="hidden" name="id" value="<?= $a['announcement_id'] ?>">
                                <button type="submit" name="delete_permanent" class="delete-btn"><i class="fas fa-trash"></i> Delete Permanently</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </main>

    <div id="announcementModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2 id="modalTitle">Post Announcement</h2>
            <form action="announcements.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" id="formAction" value="publish">
                <input type="hidden" name="id" id="announcementId">
                <input type="hidden" name="existing_media" id="existingMedia">
                
                <div class="input-group">
                    <label>Title</label>
                    <input type="text" name="title" id="inputTitle" required placeholder="Enter announcement title...">
                </div>
                <div class="input-group">
                    <label>Description</label>
                    <textarea name="description" id="inputDesc" rows="5" required placeholder="Enter description..."></textarea>
                </div>
                <div class="input-group">
                    <label>Upload Image or Video</label>
                    <input type="file" name="media" accept="image/*,video/*">
                </div>
                <button type="submit" class="publish-btn" id="submitBtn">Publish</button>
            </form>
        </div>
    </div>

    <div id="detailModal" class="modal detail-modal">
        <div class="modal-content fb-layout">
            <span class="close" onclick="closeDetailModal()">&times;</span>
            <div class="fb-left">
                <div id="detailMedia"></div>
            </div>
            <div class="fb-right-unified">
                <div class="scrollable-content">
                    <div class="fb-header-section">
                        <div class="post-meta" id="detailTime"></div>
                        <div class="expandable-container">
                            <h2 id="detailTitle" class="title-text"></h2>
                            <button id="seeMoreTitle" class="see-more-link" onclick="toggleText('detailTitle', 'seeMoreTitle', 40)">See More</button>
                        </div>
                        <div class="expandable-container">
                            <p id="detailDesc" class="desc-text"></p>
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
        const currentUserId = <?= $current_user_id ?>;

        function openModal() { document.getElementById('announcementModal').style.display = 'flex'; }
        function closeModal() { 
            document.getElementById('announcementModal').style.display = 'none'; 
            document.getElementById('formAction').value = 'publish';
            document.getElementById('modalTitle').innerText = 'Post Announcement';
            document.getElementById('submitBtn').innerText = 'Publish';
            document.getElementById('announcementId').value = '';
            document.getElementById('inputTitle').value = '';
            document.getElementById('inputDesc').value = '';
            document.getElementById('existingMedia').value = '';
        }

        function viewDetails(announcement, comments) {
            document.getElementById('detailTitle').innerText = announcement.title;
            document.getElementById('detailDesc').innerText = announcement.description;
            document.getElementById('detailTime').innerHTML = `<i class="far fa-clock"></i> Posted on: ${new Date(announcement.created_at).toLocaleString()}`;
            document.getElementById('commentAnnId').value = announcement.announcement_id;

            const mediaDiv = document.getElementById('detailMedia');
            mediaDiv.innerHTML = '';
            if (announcement.media_file) {
                if (announcement.media_file.match(/\.(mp4|webm)$/i)) {
                    mediaDiv.innerHTML = `<video src="${announcement.media_file}" controls style="max-width:100%; max-height:100%;"></video>`;
                } else {
                    mediaDiv.innerHTML = `<img src="${announcement.media_file}" style="max-width:100%; max-height:100%;">`;
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
                const repliesContainer = document.createElement('div');
                repliesContainer.className = 'replies-container';
                repliesContainer.id = `replies-${p.id}`;
                list.appendChild(repliesContainer);

                children.filter(c => c.parent_id == p.id).forEach(c => {
                    repliesContainer.innerHTML += createCommentHTML(c, true);
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
                <div class="comment-item ${isReply ? 'reply-item' : ''}" id="comment-${c.id}">
                    <div class="comment-bubble">
                        <div class="comment-user">${c.username} <span class="sender-tag">${c.sender_type}</span></div>
                        <div class="comment-text" id="text-${c.id}">${c.comment_text}</div>
                    </div>
                    <div class="comment-meta">
                        ${new Date(c.created_at).toLocaleString()}
                        ${!isReply ? `• <button class="reply-btn" onclick="replyTo(${c.id}, '${c.username}')">Reply</button>` : ''}
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
                fetch('announcements.php', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(() => { document.getElementById(`text-${id}`).innerText = newText; });
            }
        }

        function deleteComment(id) {
            if (confirm("Delete this comment?")) {
                const formData = new FormData();
                formData.append('delete_comment', '1');
                formData.append('comment_id', id);
                fetch('announcements.php', { method: 'POST', body: formData })
                .then(res => res.json())
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

            fetch('announcements.php', {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.json())
            .then(data => {
                const noComments = document.querySelector('.no-comments');
                if (noComments) noComments.remove();

                const newHtml = createCommentHTML(data, !!parentId);
                if (parentId) {
                    document.getElementById(`replies-${parentId}`).innerHTML += newHtml;
                } else {
                    const list = document.getElementById('commentsList');
                    list.innerHTML += newHtml + `<div class="replies-container" id="replies-${data.id}"></div>`;
                }
                
                this.reset();
                cancelReply();
            });
        };

        function editAnnouncement(data) {
            openModal();
            document.getElementById('formAction').value = 'update';
            document.getElementById('modalTitle').innerText = 'Edit Announcement';
            document.getElementById('submitBtn').innerText = 'Update';
            document.getElementById('announcementId').value = data.announcement_id;
            document.getElementById('inputTitle').value = data.title;
            document.getElementById('inputDesc').value = data.description;
            document.getElementById('existingMedia').value = data.media_file;
        }

        window.onclick = function(event) {
            if (event.target == document.getElementById('announcementModal')) closeModal();
            if (event.target == document.getElementById('detailModal')) closeDetailModal();
        }

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
            }
        }
    </script>
</body>
</html>