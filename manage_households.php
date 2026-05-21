<?php
require_once 'config.php';

if (!isset($_SESSION['account_id']) || $_SESSION['is_admin'] != 1) {
    header("Location: login.php");
    exit;
}

// POST ACTIONS
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 1. Create Household
    if (isset($_POST['create_household'])) {
        $head_id = $_POST['hh_head_id'];
        
        // BACKEND CHECK: Verify resident is not already in a household
        $check = $pdo->prepare("SELECT household_id FROM residents WHERE id = ?");
        $check->execute([$head_id]);
        $resident = $check->fetch();

        if ($resident && is_null($resident['household_id'])) {
            $stmt = $pdo->prepare("INSERT INTO households (household_head_id) VALUES (?)");
            $stmt->execute([$head_id]);
            $hh_id = $pdo->lastInsertId();
            $pdo->prepare("UPDATE residents SET household_id = ? WHERE id = ?")->execute([$hh_id, $head_id]);
        } else {
            // Validation error: Resident already belongs to a household
            $_SESSION['error'] = "This resident is already a member of another household.";
        }
    }

    // 2. Create NEW Family Group
    if (isset($_POST['add_family'])) {
        $hh_id = $_POST['hh_id'];
        $member_ids = $_POST['member_ids'] ?? [];
        $stmt = $pdo->prepare("INSERT INTO family_groups (household_id) VALUES (?)");
        $stmt->execute([$hh_id]);
        $family_id = $pdo->lastInsertId();
        if (!empty($member_ids)) {
            foreach ($member_ids as $rid) {
                // Validation: Only update if they don't already have a family group
                $pdo->prepare("UPDATE residents SET household_id = ?, family_group_id = ? WHERE id = ? AND family_group_id IS NULL")
                    ->execute([$hh_id, $family_id, $rid]);
            }
        }
    }

    // Add Member to EXISTING Family
    if (isset($_POST['add_member_only'])) {
        $hh_id = $_POST['hh_id'];
        $fam_id = $_POST['family_id'];
        $member_ids = $_POST['member_ids'] ?? [];
        if (!empty($member_ids)) {
            foreach ($member_ids as $rid) {
                // Validation: Check if resident already belongs to a family
                $check = $pdo->prepare("SELECT family_group_id FROM residents WHERE id = ?");
                $check->execute([$rid]);
                $res = $check->fetch();
                
                if ($res && is_null($res['family_group_id'])) {
                    $pdo->prepare("UPDATE residents SET household_id = ?, family_group_id = ? WHERE id = ?")
                        ->execute([$hh_id, $fam_id, $rid]);
                }
            }
        }
    }

    
    if (isset($_POST['remove_member'])) {
        $rid = $_POST['resident_id'];
        $fam_id = $_POST['family_id'];
        
        
        $stmt = $pdo->prepare("SELECT family_head_id FROM family_groups WHERE id = ?");
        $stmt->execute([$fam_id]);
        $fam = $stmt->fetch();
        
        if ($fam && $fam['family_head_id'] == $rid) {
            // Reset the family head 
            $pdo->prepare("UPDATE family_groups SET family_head_id = NULL WHERE id = ?")->execute([$fam_id]);
        }
        
        // Remove from family and household
        $pdo->prepare("UPDATE residents SET household_id = NULL, family_group_id = NULL WHERE id = ?")->execute([$rid]);
    }

    // 3. Set Family Head
    if (isset($_POST['set_fam_head'])) {
        $pdo->prepare("UPDATE family_groups SET family_head_id = ? WHERE id = ?")
            ->execute([$_POST['resident_id'], $_POST['family_id']]);
    }

    // 4. Delete Household
    if (isset($_POST['delete_household'])) {
        $hh_id = $_POST['hh_id'];
        $pdo->prepare("UPDATE residents SET household_id = NULL, family_group_id = NULL WHERE household_id = ?")->execute([$hh_id]);
        $pdo->prepare("DELETE FROM households WHERE id = ?")->execute([$hh_id]);
    }

    // 5. Delete Family
    if (isset($_POST['delete_family'])) {
        $fam_id = $_POST['family_id'];
        $pdo->prepare("UPDATE residents SET family_group_id = NULL WHERE family_group_id = ?")->execute([$fam_id]);
        $pdo->prepare("DELETE FROM family_groups WHERE id = ?")->execute([$fam_id]);
    }

    header("Location: manage_households.php");
    exit;
}

// Data Retrieval
$all_residents = $pdo->query("SELECT id, first_name, last_name, family_group_id, household_id FROM residents WHERE status = 'Approved' ORDER BY last_name ASC")->fetchAll(PDO::FETCH_ASSOC);
$households = $pdo->query("SELECT h.*, r.first_name, r.last_name FROM households h LEFT JOIN residents r ON h.household_head_id = r.id ORDER BY h.id ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Household and Family | Admin</title>
    <link rel="stylesheet" href="admin_portal-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .unified-card { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); margin-top: 10px; }
        
        .flex-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
        
        .btn-m { padding: 8px 15px; font-size: 0.85rem; border-radius: 6px; cursor: pointer; border: none; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s; text-decoration: none; }
        .btn-primary { background: var(--admin-blue); color: white; }
        .btn-success { background: var(--nav-green); color: white; }
        
        .action-group { display: flex; align-items: center; gap: 10px; }
        .btn-icon { background: transparent; border: none; cursor: pointer; font-size: 1rem; transition: color 0.2s; outline: none; padding: 5px; }
        .btn-trash { color: #b2bec3; }
        .btn-trash:hover { color: #e74c3c; }
        .btn-manage { color: var(--admin-blue); }
        .btn-manage:hover { color: #074b83; }

        .hh-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(380px, 1fr)); gap: 20px; }
        .hh-card { background: white; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.06); border-top: 5px solid var(--admin-blue); }
        .hh-header { padding: 15px 20px; border-bottom: 1px solid #f1f1f1; display: flex; justify-content: space-between; align-items: center; }
        .hh-header h3 { margin: 0; font-size: 1.1rem; color: #2d3436; }
        
        .hh-body { padding: 15px 20px; }

        .fam-box { background: #fdfdfd; border-radius: 10px; margin-top: 15px; padding: 12px; border: 1px solid #eee; position: relative; }
        .fam-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; padding-bottom: 8px; border-bottom: 1px dashed #ddd; }
        .fam-id { font-weight: 800; color: #636e72; font-size: 0.8rem; letter-spacing: 0.5px; }
        
        .member-row { display: flex; justify-content: space-between; align-items: center; padding: 6px 0; font-size: 0.85rem; border-bottom: 1px solid #f8f9fa; }
        .member-row:last-child { border-bottom: none; }

        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); backdrop-filter: blur(3px); overflow-y: auto; }
        .modal-content { 
            background: white; 
            margin: 2vh auto; 
            padding: 30px; 
            width: 450px; 
            max-width: 90%; 
            max-height: 95vh; 
            overflow-y: auto; 
            border-radius: 15px; 
            box-shadow: 0 20px 40px rgba(0,0,0,0.15); 
            position: relative;
        }
        
        .search-wrapper { position: relative; margin-top: 10px; }
        .search-input { width: 100%; padding: 12px; border: 1px solid #e0e0e0; border-radius: 8px; box-sizing: border-box; font-size: 0.85rem; }
        .search-results { max-height: 200px; overflow-y: auto; border: 1px solid #edf2f7; margin-top: 5px; border-radius: 8px; background: #fff; }
        .search-item { padding: 10px 15px; cursor: pointer; border-bottom: 1px solid #f7fafc; font-size: 0.9rem; }
        .search-item:hover { background: #ebf8ff; color: var(--admin-blue); }

        .tag-badge { background: #e1f0ff; color: var(--admin-blue); padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; }
        .head-tag { background: var(--nav-green); color: white; }
        .already-assigned { color: #ccc; cursor: not-allowed; }
        
        .remove-member-btn { color: #b2bec3; font-size: 0.8rem; margin-left: 8px; }
        .remove-member-btn:hover { color: #e74c3c; }
    </style>
</head>
<body>
    <header class="admin-header">
        <div class="logo-section">
            <img src="BHPS logo.png" class="portal-logo">
            <h1>HOUSEHOLD MANAGEMENT</h1>
        </div>
        <a href="master_list.php" class="btn-logout" style="background: var(--admin-blue); text-decoration: none;">Back to Masterlist</a>
    </header>

    <main class="main-content-layout" style="display: block; padding: 0 40px 40px;">
        <div class="unified-card">
            <div class="flex-header">
                <h2 style="color: #2d3436; font-size: 1.2rem;">Resident Organization</h2>
                <button class="btn-m btn-primary" onclick="openModal('createHHModal')"><i class="fas fa-plus-circle"></i> Create Household</button>
            </div>

            <div class="hh-grid">
                <?php 
                $hhDisplayId = 1;
                foreach ($households as $hh): 
                ?>
                    <div class="hh-card">
                        <div class="hh-header">
                            <div>
                                <h3>Household #<?= str_pad($hhDisplayId++, 2, '0', STR_PAD_LEFT) ?></h3>
                                <span style="font-size: 0.85rem; font-weight: 600; color: var(--admin-blue);"><?= htmlspecialchars($hh['first_name'].' '.$hh['last_name']) ?> <small>(Head)</small></span>
                            </div>
                            <form method="POST" onsubmit="return confirm('Delete this Household?');">
                                <input type="hidden" name="hh_id" value="<?= $hh['id'] ?>">
                                <button name="delete_household" class="btn-icon btn-trash"><i class="fas fa-trash-alt"></i></button>
                            </form>
                        </div>
                        
                        <div class="hh-body">
                            <button class="btn-m btn-success" style="width: 100%; justify-content: center; margin-bottom: 10px;" onclick="openAddFamily(<?= $hh['id'] ?>)">
                                <i class="fas fa-users"></i> Add New Family
                            </button>

                            <?php 
                            $fams = $pdo->prepare("SELECT f.*, r.first_name, r.last_name FROM family_groups f LEFT JOIN residents r ON f.family_head_id = r.id WHERE f.household_id = ? ORDER BY f.id ASC");
                            $fams->execute([$hh['id']]);
                            $famDisplayId = 1;
                            while($f = $fams->fetch()):
                            ?>
                                <div class="fam-box">
                                    <div class="fam-header">
                                        <span class="fam-id">FAMILY #<?= str_pad($famDisplayId++, 2, '0', STR_PAD_LEFT) ?></span>
                                        <div class="action-group">
                                            <button class="btn-icon btn-manage" onclick="openAddMember(<?= $hh['id'] ?>, <?= $f['id'] ?>)" title="Add Family Member">
                                                <i class="fas fa-user-plus"></i>
                                            </button>
                                            <form method="POST" onsubmit="return confirm('Remove this Family group?');" style="display:inline;">
                                                <input type="hidden" name="family_id" value="<?= $f['id'] ?>">
                                                <button name="delete_family" class="btn-icon btn-trash" style="font-size: 0.9rem;"><i class="fas fa-trash"></i></button>
                                            </form>
                                        </div>
                                    </div>
                                    
                                    <div style="font-size: 0.85rem; margin-bottom: 10px;">
                                        <strong>Family Head:</strong> <?= $f['first_name'] ? ($f['first_name'].' '.$f['last_name']) : '<span style="color:#e74c3c">Not Assigned</span>' ?>
                                    </div>

                                    <div class="member-list">
                                        <?php 
                                        $fm = $pdo->prepare("SELECT id, first_name, last_name FROM residents WHERE family_group_id = ?");
                                        $fm->execute([$f['id']]);
                                        while($mem = $fm->fetch()):
                                        ?>
                                            <div class="member-row">
                                                <span>
                                                    <?= $mem['first_name'].' '.$mem['last_name'] ?>
                                                    <form method="POST" style="display:inline;" onsubmit="return confirm('Remove this member from the family?');">
                                                        <input type="hidden" name="resident_id" value="<?= $mem['id'] ?>">
                                                        <input type="hidden" name="family_id" value="<?= $f['id'] ?>">
                                                        <button name="remove_member" type="submit" class="btn-icon remove-member-btn" title="Remove Member"><i class="fas fa-user-minus"></i></button>
                                                    </form>
                                                </span>
                                                <div>
                                                    <?php if($mem['id'] == $f['family_head_id']): ?>
                                                        <span class="tag-badge head-tag">HEAD</span>
                                                    <?php else: ?>
                                                        <form method="POST" style="display:inline;">
                                                            <input type="hidden" name="family_id" value="<?= $f['id'] ?>">
                                                            <input type="hidden" name="resident_id" value="<?= $mem['id'] ?>">
                                                            <button name="set_fam_head" class="btn-m" style="padding: 2px 8px; font-size: 0.7rem; border: 1px solid #ddd; background: #fff;">Set Head</button>
                                                        </form>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php endwhile; ?>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </main>

    <div id="createHHModal" class="modal">
        <div class="modal-content">
            <h2 style="margin-bottom:5px; font-size: 1.3rem;">Create Household</h2>
            <p style="font-size: 0.85rem; color: #636e72; margin-bottom: 15px;">Select an unassigned resident as the Household Head.</p>
            <form method="POST">
                <div class="search-wrapper">
                    <input type="text" class="search-input" placeholder="Search unassigned residents..." onkeyup="filterList(this, 'hh-res-list')">
                    <div id="hh-res-list" class="search-results">
                        <?php foreach($all_residents as $r): ?>
                            <?php if(is_null($r['household_id'])): ?>
                                <div class="search-item" onclick="pickHH(<?= $r['id'] ?>, '<?= addslashes($r['first_name'].' '.$r['last_name']) ?>')"><?= htmlspecialchars($r['first_name'].' '.$r['last_name']) ?></div>
                            <?php else: ?>
                                <div class="search-item already-assigned" title="Already in a household"><?= htmlspecialchars($r['first_name'].' '.$r['last_name']) ?> (Assigned)</div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div id="hh-sel" style="margin: 15px 0; min-height: 20px;"></div>
                <input type="hidden" name="hh_head_id" id="hh_head_input" required>
                
                <button type="submit" name="create_household" class="btn-m btn-primary" style="width:100%; justify-content:center;">Confirm & Save</button>
                <button type="button" onclick="closeModal('createHHModal')" style="width:100%; background:none; border:none; color:#636e72; margin-top:10px; cursor:pointer;">Cancel</button>
            </form>
        </div>
    </div>

    <div id="addFamModal" class="modal">
        <div class="modal-content">
            <h2 style="font-size: 1.3rem;">Create New Family Group</h2>
            <form method="POST" id="famForm">
                <input type="hidden" name="hh_id" id="target_hh">
                <p style="font-size: 0.85rem; color: #636e72; margin-bottom: 15px;">Search and add initial members for this NEW family.</p>
                
                <div class="search-wrapper">
                    <input type="text" class="search-input" placeholder="Search unassigned residents..." onkeyup="filterList(this, 'fam-res-list')">
                    <div id="fam-res-list" class="search-results">
                        <?php foreach($all_residents as $r): ?>
                            <?php if(is_null($r['family_group_id'])): ?>
                            <div class="search-item" onclick="addMem(<?= $r['id'] ?>, '<?= addslashes($r['first_name'].' '.$r['last_name']) ?>', 'famForm', 'tags-box')"><?= htmlspecialchars($r['first_name'].' '.$r['last_name']) ?></div>
                            <?php else: ?>
                            <div class="search-item already-assigned" title="Already in a family"><?= htmlspecialchars($r['first_name'].' '.$r['last_name']) ?> (Assigned)</div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div id="tags-box" style="margin: 20px 0; border: 2px dashed #edf2f7; padding: 12px; border-radius: 10px; min-height: 50px;"></div>

                <button type="submit" name="add_family" class="btn-m btn-success" style="width:100%; justify-content:center;">Save New Family</button>
                <button type="button" onclick="closeModal('addFamModal')" style="width:100%; background:none; border:none; color:#636e72; margin-top:10px; cursor:pointer;">Cancel</button>
            </form>
        </div>
    </div>

    <div id="addMemModal" class="modal">
        <div class="modal-content">
            <h2 style="font-size: 1.3rem;">Add Family Members</h2>
            <form method="POST" id="memForm">
                <input type="hidden" name="hh_id" id="mem_target_hh">
                <input type="hidden" name="family_id" id="mem_target_fam">
                <p style="font-size: 0.85rem; color: #636e72; margin-bottom: 15px;">Add residents to this existing family unit.</p>
                
                <div class="search-wrapper">
                    <input type="text" class="search-input" placeholder="Search unassigned residents..." onkeyup="filterList(this, 'mem-res-list')">
                    <div id="mem-res-list" class="search-results">
                        <?php foreach($all_residents as $r): ?>
                            <?php if(is_null($r['family_group_id'])): ?>
                            <div class="search-item" onclick="addMem(<?= $r['id'] ?>, '<?= addslashes($r['first_name'].' '.$r['last_name']) ?>', 'memForm', 'mem-tags-box')"><?= htmlspecialchars($r['first_name'].' '.$r['last_name']) ?></div>
                            <?php else: ?>
                            <div class="search-item already-assigned" title="Already in a family"><?= htmlspecialchars($r['first_name'].' '.$r['last_name']) ?> (Assigned)</div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div id="mem-tags-box" style="margin: 20px 0; border: 2px dashed #edf2f7; padding: 12px; border-radius: 10px; min-height: 50px;"></div>

                <button type="submit" name="add_member_only" class="btn-m btn-primary" style="width:100%; justify-content:center;">Add Members</button>
                <button type="button" onclick="closeModal('addMemModal')" style="width:100%; background:none; border:none; color:#636e72; margin-top:10px; cursor:pointer;">Cancel</button>
            </form>
        </div>
    </div>

    <script>
    function openModal(id) { document.getElementById(id).style.display = 'block'; }
    function closeModal(id) { document.getElementById(id).style.display = 'none'; }
    
    function filterList(input, listId) {
        let val = input.value.toLowerCase();
        let items = document.getElementById(listId).getElementsByClassName('search-item');
        for (let item of items) {
            item.style.display = item.textContent.toLowerCase().includes(val) ? '' : 'none';
        }
    }

    function pickHH(id, name) {
        document.getElementById('hh_head_input').value = id;
        document.getElementById('hh-sel').innerHTML = `<span class="tag-badge head-tag">Selected Head: ${name}</span>`;
    }

    function openAddFamily(hhId) {
        document.getElementById('target_hh').value = hhId;
        document.getElementById('tags-box').innerHTML = '';
        document.querySelectorAll('.f-hidden').forEach(el => el.remove());
        openModal('addFamModal');
    }

    function openAddMember(hhId, famId) {
        document.getElementById('mem_target_hh').value = hhId;
        document.getElementById('mem_target_fam').value = famId;
        document.getElementById('mem-tags-box').innerHTML = '';
        document.querySelectorAll('.f-hidden').forEach(el => el.remove());
        openModal('addMemModal');
    }

    function addMem(id, name, formId, tagBoxId) {
        if(document.getElementById('f-id-'+id)) return;
        
        let tag = document.createElement('span');
        tag.className = 'tag-badge';
        tag.style.margin = '4px';
        tag.innerHTML = `${name} <i class="fas fa-times" style="cursor:pointer; margin-left:8px; color:#e74c3c" onclick="this.parentElement.remove(); document.getElementById('f-id-${id}').remove();"></i>`;
        document.getElementById(tagBoxId).appendChild(tag);
        
        let hid = document.createElement('input');
        hid.type = 'hidden'; hid.name = 'member_ids[]'; hid.value = id; hid.id = 'f-id-'+id; hid.className = 'f-hidden';
        document.getElementById(formId).appendChild(hid);
    }

    window.onclick = function(e) { if(e.target.className === 'modal') closeModal(e.target.id); }
    </script>
</body>
</html>