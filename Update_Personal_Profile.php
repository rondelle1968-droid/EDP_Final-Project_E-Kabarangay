<?php
require_once 'config.php';

if (!isset($_SESSION['account_id'])) {
    header("Location: login.php");
    exit;
}

$accountId = $_SESSION['account_id'];

// Fetch existing resident data (including related tables)
$stmt = $pdo->prepare("
    SELECT 
        r.*,
        cur.street AS cur_street, cur.barangay AS cur_barangay, 
        cur.municipality AS cur_municipality, cur.province AS cur_province,
        cur.residence_type AS cur_residence_type,
        perm.street AS perm_street, perm.barangay AS perm_barangay,
        perm.municipality AS perm_municipality, perm.province AS perm_province,
        h.toilet_type, h.water_source, h.iodized_salt, h.iron_fortified_rice,
        a.email
    FROM residents r
    JOIN accounts a ON r.account_id = a.id
    LEFT JOIN address cur ON r.id = cur.resident_id AND cur.address_type = 'current'
    LEFT JOIN address perm ON r.id = perm.resident_id AND perm.address_type = 'permanent'
    LEFT JOIN household_info h ON r.id = h.resident_id
    WHERE r.account_id = ?
");
$stmt->execute([$accountId]);
$resident = $stmt->fetch();

if (!$resident) {
    die("Resident profile not found.");
}

// Define option arrays (same as register_details.php)
$civil_statuses = ['Single', 'Married', 'Separated', 'Divorced', 'Widowed', 'Live-in'];
$employment_opts = ['Employed', 'Unemployed', 'Self-employed', 'Student', 'Retired', 'Homemaker', 'Part-time'];
$education_opts = ['Elementary', 'High school', 'Senior high school', 'College / Undergraduate', 'College Graduate', 'No formal education', 'Vocational / Technical'];
$streets = ['Del Pilar st.', 'National Road', 'Rizal st.', 'P. Viana st.', 'San Jose st.', 'Mercene st.'];
$barangays = ['Poblacion 1', 'Poblacion 2', 'Poblacion 3', 'Poblacion 4', 'Poblacion 5', 'Poblacion 6', 'Poblacion 7', 'Poblacion 8', 'Payompon', 'Balansay', 'Fatima', 'Talabaan', 'Tayamaan', 'San Luis', 'Tangkalan'];
$municipalities = ['Mamburao'];
$provinces = ['Occidental Mindoro'];

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Required fields (excluding id_picture)
    $required_fields = ['first_name', 'last_name', 'civil_status', 'sex', 'dob', 'place_of_birth', 'contact_no', 'religion', 'employment_status', 'educational_attainment', 'current_residence_type', 'current_street', 'current_barangay', 'current_municipality', 'current_province', 'permanent_street', 'permanent_barangay', 'permanent_municipality', 'permanent_province', 'toilet_type', 'water_source', 'iodized_salt', 'iron_fortified_rice'];
    
    foreach ($required_fields as $field) {
        if (empty(trim($_POST[$field] ?? ''))) {
            $errors[$field] = "Required.";
        }
    }

    // Contact number validation
    $contact = trim($_POST['contact_no'] ?? '');
    if (!preg_match('/^09[0-9]{9}$/', $contact)) {
        $errors['contact_no'] = "Must start with 09 and be 11 digits.";
    }

    // Maternal Health Validation (only for Female)
    if (($_POST['sex'] ?? '') === 'Female') {
        if (empty($_POST['family_planning'])) $errors['family_planning'] = "Required.";
        if (empty($_POST['pregnancy_status'])) $errors['pregnancy_status'] = "Required.";
        
        if (($_POST['pregnancy_status'] ?? '') === 'Delivered and Breastfeeding') {
            if (empty($_POST['breastfeeding_type'])) $errors['breastfeeding_type'] = "Required.";
        }
    }

    if (empty($errors)) {
        try {
            $pdo->beginTransaction();

            // 1. Get resident ID
            $residentId = $resident['id'];

            // 2. Update residents table
            $residentData = [
                'first_name' => $_POST['first_name'],
                'middle_name' => $_POST['middle_name'] ?? null,
                'last_name' => $_POST['last_name'],
                'civil_status' => $_POST['civil_status'],
                'sex' => $_POST['sex'],
                'dob' => $_POST['dob'],
                'place_of_birth' => $_POST['place_of_birth'],
                'contact_no' => $_POST['contact_no'],
                'religion' => $_POST['religion'],
                'employment_status' => $_POST['employment_status'],
                'educational_attainment' => $_POST['educational_attainment'],
                'family_planning' => ($_POST['sex'] === 'Female') ? $_POST['family_planning'] : null,
                'pregnancy_status' => ($_POST['sex'] === 'Female') ? $_POST['pregnancy_status'] : null,
                'breastfeeding_type' => (($_POST['sex'] === 'Female') && ($_POST['pregnancy_status'] ?? '') === 'Delivered and Breastfeeding') ? $_POST['breastfeeding_type'] : null,
                'account_id' => $accountId
            ];
            
            $updateResident = $pdo->prepare("
                UPDATE residents SET 
                    first_name = :first_name,
                    middle_name = :middle_name,
                    last_name = :last_name,
                    civil_status = :civil_status,
                    sex = :sex,
                    dob = :dob,
                    place_of_birth = :place_of_birth,
                    contact_no = :contact_no,
                    religion = :religion,
                    employment_status = :employment_status,
                    educational_attainment = :educational_attainment,
                    family_planning = :family_planning,
                    pregnancy_status = :pregnancy_status,
                    breastfeeding_type = :breastfeeding_type
                WHERE account_id = :account_id
            ");
            $updateResident->execute($residentData);

            // 3. Update current address
            $checkCurAddr = $pdo->prepare("SELECT id FROM address WHERE resident_id = ? AND address_type = 'current'");
            $checkCurAddr->execute([$residentId]);
            $curExists = $checkCurAddr->fetch();
            
            if ($curExists) {
                $updateCur = $pdo->prepare("
                    UPDATE address SET 
                        street = :street, 
                        barangay = :barangay, 
                        municipality = :municipality,
                        province = :province, 
                        residence_type = :residence_type
                    WHERE resident_id = :resident_id AND address_type = 'current'
                ");
                $updateCur->execute([
                    'street' => $_POST['current_street'],
                    'barangay' => $_POST['current_barangay'],
                    'municipality' => $_POST['current_municipality'],
                    'province' => $_POST['current_province'],
                    'residence_type' => $_POST['current_residence_type'],
                    'resident_id' => $residentId
                ]);
            } else {
                $insertCur = $pdo->prepare("
                    INSERT INTO address (resident_id, street, barangay, municipality, province, residence_type, address_type)
                    VALUES (:resident_id, :street, :barangay, :municipality, :province, :residence_type, 'current')
                ");
                $insertCur->execute([
                    'resident_id' => $residentId,
                    'street' => $_POST['current_street'],
                    'barangay' => $_POST['current_barangay'],
                    'municipality' => $_POST['current_municipality'],
                    'province' => $_POST['current_province'],
                    'residence_type' => $_POST['current_residence_type']
                ]);
            }

            // 4. Update permanent address
            $checkPermAddr = $pdo->prepare("SELECT id FROM address WHERE resident_id = ? AND address_type = 'permanent'");
            $checkPermAddr->execute([$residentId]);
            $permExists = $checkPermAddr->fetch();
            
            if ($permExists) {
                $updatePerm = $pdo->prepare("
                    UPDATE address SET 
                        street = :street, 
                        barangay = :barangay, 
                        municipality = :municipality, 
                        province = :province
                    WHERE resident_id = :resident_id AND address_type = 'permanent'
                ");
                $updatePerm->execute([
                    'street' => $_POST['permanent_street'],
                    'barangay' => $_POST['permanent_barangay'],
                    'municipality' => $_POST['permanent_municipality'],
                    'province' => $_POST['permanent_province'],
                    'resident_id' => $residentId
                ]);
            } else {
                $insertPerm = $pdo->prepare("
                    INSERT INTO address (resident_id, street, barangay, municipality, province, address_type)
                    VALUES (:resident_id, :street, :barangay, :municipality, :province, 'permanent')
                ");
                $insertPerm->execute([
                    'resident_id' => $residentId,
                    'street' => $_POST['permanent_street'],
                    'barangay' => $_POST['permanent_barangay'],
                    'municipality' => $_POST['permanent_municipality'],
                    'province' => $_POST['permanent_province']
                ]);
            }

            // 5. Update household_info
            $checkHH = $pdo->prepare("SELECT id FROM household_info WHERE resident_id = ?");
            $checkHH->execute([$residentId]);
            $hhExists = $checkHH->fetch();
            
            if ($hhExists) {
                $updateHH = $pdo->prepare("
                    UPDATE household_info SET 
                        toilet_type = :toilet_type, 
                        water_source = :water_source,
                        iodized_salt = :iodized_salt, 
                        iron_fortified_rice = :iron_fortified_rice
                    WHERE resident_id = :resident_id
                ");
                $updateHH->execute([
                    'toilet_type' => $_POST['toilet_type'],
                    'water_source' => $_POST['water_source'],
                    'iodized_salt' => $_POST['iodized_salt'],
                    'iron_fortified_rice' => $_POST['iron_fortified_rice'],
                    'resident_id' => $residentId
                ]);
            } else {
                $insertHH = $pdo->prepare("
                    INSERT INTO household_info (resident_id, toilet_type, water_source, iodized_salt, iron_fortified_rice)
                    VALUES (:resident_id, :toilet_type, :water_source, :iodized_salt, :iron_fortified_rice)
                ");
                $insertHH->execute([
                    'resident_id' => $residentId,
                    'toilet_type' => $_POST['toilet_type'],
                    'water_source' => $_POST['water_source'],
                    'iodized_salt' => $_POST['iodized_salt'],
                    'iron_fortified_rice' => $_POST['iron_fortified_rice']
                ]);
            }

            $pdo->commit();
            $_SESSION['update_success'] = "Profile updated successfully!";
            header("Location: dashboard.php?updated=1");
            exit;

        } catch (Exception $e) {
            $pdo->rollBack();
            $errors['general'] = "Update failed: " . $e->getMessage();
        }
    }
}

// Merge existing data with POST for repopulation
$formData = $_POST;
if (empty($formData) && !$errors) {
    $formData = $resident;
    // Map address fields
    $formData['current_street'] = $resident['cur_street'];
    $formData['current_barangay'] = $resident['cur_barangay'];
    $formData['current_municipality'] = $resident['cur_municipality'];
    $formData['current_province'] = $resident['cur_province'];
    $formData['current_residence_type'] = $resident['cur_residence_type'];
    $formData['permanent_street'] = $resident['perm_street'];
    $formData['permanent_barangay'] = $resident['perm_barangay'];
    $formData['permanent_municipality'] = $resident['perm_municipality'];
    $formData['permanent_province'] = $resident['perm_province'];
    $formData['toilet_type'] = $resident['toilet_type'];
    $formData['water_source'] = $resident['water_source'];
    $formData['iodized_salt'] = $resident['iodized_salt'];
    $formData['iron_fortified_rice'] = $resident['iron_fortified_rice'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Personal Profile | E-Kabarangay</title>
    <link rel="stylesheet" href="Update_Personal_Profile-style.css">
    <script src="Update_Personal_Profile-script.js" defer></script>
</head>
<body>
<div class="main-wrapper">
    <div class="logo-header">
        <img src="BHPS logo.png" alt="Logo">
        <span>UPDATE PROFILE</span>
    </div>

    <div class="registration-card">
        <?php if (isset($errors['general'])): ?>
            <div class="error-message"><?= htmlspecialchars($errors['general']) ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <h2 class="section-title">Personal Profile</h2>
            <div class="form-grid">
                <div class="input-group">
                    <label>First Name: <?= (isset($errors['first_name'])) ? '<span style="color:red">*</span>' : '' ?></label>
                    <input type="text" name="first_name" value="<?= htmlspecialchars($formData['first_name'] ?? '') ?>">
                </div>
                <div class="input-group">
                    <label>Middle Name:</label>
                    <input type="text" name="middle_name" value="<?= htmlspecialchars($formData['middle_name'] ?? '') ?>">
                </div>
                <div class="input-group">
                    <label>Last Name: <?= (isset($errors['last_name'])) ? '<span style="color:red">*</span>' : '' ?></label>
                    <input type="text" name="last_name" value="<?= htmlspecialchars($formData['last_name'] ?? '') ?>">
                </div>
                <div class="input-group">
                    <label>Civil Status: <?= (isset($errors['civil_status'])) ? '<span style="color:red">*</span>' : '' ?></label>
                    <select name="civil_status">
                        <option value="">Select Status</option>
                        <?php foreach($civil_statuses as $opt): ?>
                            <option <?= (($formData['civil_status'] ?? '') == $opt) ? 'selected' : '' ?>><?= $opt ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="input-group">
                    <label>Sex: <?= (isset($errors['sex'])) ? '<span style="color:red">*</span>' : '' ?></label>
                    <div class="pill-group">
                        <input type="radio" name="sex" value="Male" id="sex_male" <?= (($formData['sex'] ?? '') == 'Male') ? 'checked' : '' ?>>
                        <label for="sex_male">Male</label>
                        <input type="radio" name="sex" value="Female" id="sex_female" <?= (($formData['sex'] ?? '') == 'Female') ? 'checked' : '' ?>>
                        <label for="sex_female">Female</label>
                    </div>
                </div>
                <div class="input-group">
                    <label>Date of Birth: <?= (isset($errors['dob'])) ? '<span style="color:red">*</span>' : '' ?></label>
                    <input type="date" name="dob" id="dob" value="<?= htmlspecialchars($formData['dob'] ?? '') ?>">
                </div>
                <div class="input-group">
                    <label>Age:</label>
                    <input type="text" name="age" id="age" readonly placeholder="Auto calculated">
                </div>
                <div class="input-group">
                    <label>Place of Birth: <?= (isset($errors['place_of_birth'])) ? '<span style="color:red">*</span>' : '' ?></label>
                    <input type="text" name="place_of_birth" value="<?= htmlspecialchars($formData['place_of_birth'] ?? '') ?>">
                </div>
                <div class="input-group">
                    <label>Contact No.:</label>
                    <input type="tel" name="contact_no" id="contact_no" placeholder="09XXXXXXXXX" maxlength="11" value="<?= htmlspecialchars($formData['contact_no'] ?? '') ?>">
                    <?php if(isset($errors['contact_no'])): ?><small class="error-msg"><?= $errors['contact_no'] ?></small><?php endif; ?>
                </div>
                <div class="input-group">
                    <label>Religion: <?= (isset($errors['religion'])) ? '<span style="color:red">*</span>' : '' ?></label>
                    <input type="text" name="religion" value="<?= htmlspecialchars($formData['religion'] ?? '') ?>">
                </div>
                <div class="input-group">
                    <label>Occupation: <?= (isset($errors['employment_status'])) ? '<span style="color:red">*</span>' : '' ?></label>
                    <select name="employment_status">
                        <option value="">Select Status</option>
                        <?php foreach($employment_opts as $opt): ?>
                            <option <?= (($formData['employment_status'] ?? '') == $opt) ? 'selected' : '' ?>><?= $opt ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="input-group">
                    <label>Educational Attainment: <?= (isset($errors['educational_attainment'])) ? '<span style="color:red">*</span>' : '' ?></label>
                    <select name="educational_attainment">
                        <option value="">Select Status</option>
                        <?php foreach($education_opts as $opt): ?>
                            <option <?= (($formData['educational_attainment'] ?? '') == $opt) ? 'selected' : '' ?>><?= $opt ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="address-section section-box">
                <div class="section-label">Address</div>
                <h3>Current Address</h3>
                <div class="inline-row">
                    <label>Residence Type: <?= (isset($errors['current_residence_type'])) ? '<span style="color:red">*</span>' : '' ?></label>
                    <div class="pill-group">
                        <input type="radio" name="current_residence_type" value="Boarding House" id="res_boarding" <?= (($formData['current_residence_type'] ?? '') == 'Boarding House') ? 'checked' : '' ?>>
                        <label for="res_boarding">Boarding House</label>
                        <input type="radio" name="current_residence_type" value="Own House" id="res_own" <?= (($formData['current_residence_type'] ?? '') == 'Own House') ? 'checked' : '' ?>>
                        <label for="res_own">Own House</label>
                    </div>
                </div>
                <div class="form-grid">
                    <div class="input-group">
                        <select name="current_street">
                            <option value="">Select Street</option>
                            <?php foreach($streets as $st): ?>
                                <option <?= (($formData['current_street'] ?? '') == $st) ? 'selected' : '' ?>><?= $st ?></option>
                            <?php endforeach; ?>
                        </select>
                        <small class="sub-label">Street <?= (isset($errors['current_street'])) ? '<span style="color:red">*</span>' : '' ?></small>
                    </div>
                    <div class="input-group">
                        <select name="current_barangay">
                            <option value="">Select Barangay</option>
                            <?php foreach($barangays as $br): ?>
                                <option <?= (($formData['current_barangay'] ?? '') == $br) ? 'selected' : '' ?>><?= $br ?></option>
                            <?php endforeach; ?>
                        </select>
                        <small class="sub-label">Barangay <?= (isset($errors['current_barangay'])) ? '<span style="color:red">*</span>' : '' ?></small>
                    </div>
                    <div class="input-group">
                        <select name="current_municipality">
                            <?php foreach($municipalities as $mu): ?>
                                <option <?= (($formData['current_municipality'] ?? '') == $mu) ? 'selected' : '' ?>><?= $mu ?></option>
                            <?php endforeach; ?>
                        </select>
                        <small class="sub-label">Municipality</small>
                    </div>
                    <div class="input-group">
                        <select name="current_province">
                            <?php foreach($provinces as $pr): ?>
                                <option <?= (($formData['current_province'] ?? '') == $pr) ? 'selected' : '' ?>><?= $pr ?></option>
                            <?php endforeach; ?>
                        </select>
                        <small class="sub-label">Province</small>
                    </div>
                </div>

                <h3>Permanent Address</h3>
                <div class="form-grid">
                    <div class="input-group">
                        <select name="permanent_street">
                            <option value="">Select Street</option>
                            <?php foreach($streets as $st): ?>
                                <option <?= (($formData['permanent_street'] ?? '') == $st) ? 'selected' : '' ?>><?= $st ?></option>
                            <?php endforeach; ?>
                        </select>
                        <small class="sub-label">Street <?= (isset($errors['permanent_street'])) ? '<span style="color:red">*</span>' : '' ?></small>
                    </div>
                    <div class="input-group">
                        <select name="permanent_barangay">
                            <option value="">Select Barangay</option>
                            <?php foreach($barangays as $br): ?>
                                <option <?= (($formData['permanent_barangay'] ?? '') == $br) ? 'selected' : '' ?>><?= $br ?></option>
                            <?php endforeach; ?>
                        </select>
                        <small class="sub-label">Barangay <?= (isset($errors['permanent_barangay'])) ? '<span style="color:red">*</span>' : '' ?></small>
                    </div>
                    <div class="input-group">
                        <select name="permanent_municipality">
                            <?php foreach($municipalities as $mu): ?>
                                <option <?= (($formData['permanent_municipality'] ?? '') == $mu) ? 'selected' : '' ?>><?= $mu ?></option>
                            <?php endforeach; ?>
                        </select>
                        <small class="sub-label">Municipality</small>
                    </div>
                    <div class="input-group">
                        <select name="permanent_province">
                            <?php foreach($provinces as $pr): ?>
                                <option <?= (($formData['permanent_province'] ?? '') == $pr) ? 'selected' : '' ?>><?= $pr ?></option>
                            <?php endforeach; ?>
                        </select>
                        <small class="sub-label">Province</small>
                    </div>
                </div>
            </div>

            <div id="maternal-health-section" class="maternal-section section-box" <?= (($formData['sex'] ?? '') !== 'Female') ? 'style="display: none;"' : '' ?>>
                <div class="section-label">Pregnancy Status</div>
                <div class="input-row">
                    <label>Family Planning: <?= (isset($errors['family_planning'])) ? '<span style="color:red">*</span>' : '' ?></label>
                    <div class="pill-group">
                        <input type="radio" name="family_planning" value="Contraceptive Method" id="fp_contra" <?= (($formData['family_planning'] ?? '') == 'Contraceptive Method') ? 'checked' : '' ?>>
                        <label for="fp_contra">Contraceptive Method</label>
                        <input type="radio" name="family_planning" value="None" id="fp_none" <?= (($formData['family_planning'] ?? '') == 'None') ? 'checked' : '' ?>>
                        <label for="fp_none">None</label>
                    </div>
                </div>
                <div class="input-row">
                    <label>Pregnancy Status: <?= (isset($errors['pregnancy_status'])) ? '<span style="color:red">*</span>' : '' ?></label>
                    <div class="pill-group">
                        <input type="radio" name="pregnancy_status" value="Pregnant" id="ps_preg" <?= (($formData['pregnancy_status'] ?? '') == 'Pregnant') ? 'checked' : '' ?>>
                        <label for="ps_preg">Pregnant</label>
                        <input type="radio" name="pregnancy_status" value="Delivered and Breastfeeding" id="ps_breast" <?= (($formData['pregnancy_status'] ?? '') == 'Delivered and Breastfeeding') ? 'checked' : '' ?>>
                        <label for="ps_breast">Delivered / Breast feeding</label>
                        <input type="radio" name="pregnancy_status" value="None" id="ps_none" <?= (($formData['pregnancy_status'] ?? '') == 'None') ? 'checked' : '' ?>>
                        <label for="ps_none">None</label>
                    </div>
                </div>
                <div id="breastfeeding-group" class="input-row" style="display: <?= (($formData['pregnancy_status'] ?? '') == 'Delivered and Breastfeeding') ? 'block' : 'none'; ?>">
                    <label>Breastfeeding Type (0-12mos Infant): <?= (isset($errors['breastfeeding_type'])) ? '<span style="color:red">*</span>' : '' ?></label>
                    <div class="pill-group">
                        <input type="radio" name="breastfeeding_type" value="Exclusive breastfeeding" id="bf_ex" <?= (($formData['breastfeeding_type'] ?? '') == 'Exclusive breastfeeding') ? 'checked' : '' ?>>
                        <label for="bf_ex">Exclusive Breastfeeding</label>
                        <input type="radio" name="breastfeeding_type" value="Formula feeding" id="bf_for" <?= (($formData['breastfeeding_type'] ?? '') == 'Formula feeding') ? 'checked' : '' ?>>
                        <label for="bf_for">Formula Feeding</label>
                        <input type="radio" name="breastfeeding_type" value="Mixed feeding" id="bf_mix" <?= (($formData['breastfeeding_type'] ?? '') == 'Mixed feeding') ? 'checked' : '' ?>>
                        <label for="bf_mix">Mixed Feeding</label>
                    </div>
                </div>
            </div>

            <div class="household-section section-box">
                <div class="section-label">Household Information</div>
                <div class="form-grid">
                    <div class="input-group">
                        <label>Toilet Type: <?= (isset($errors['toilet_type'])) ? '<span style="color:red">*</span>' : '' ?></label>
                        <div class="pill-group">
                            <input type="radio" name="toilet_type" value="Water sealed" id="tt_water" <?= (strcasecmp($formData['toilet_type'] ?? '', 'Water sealed') == 0) ? 'checked' : '' ?>>
                            <label for="tt_water">Water Sealed</label>
                            <input type="radio" name="toilet_type" value="Open pit" id="tt_pit" <?= (strcasecmp($formData['toilet_type'] ?? '', 'Open pit') == 0) ? 'checked' : '' ?>>
                            <label for="tt_pit">Open Pit</label>
                        </div>
                    </div>
                    <div class="input-group">
                        <label>Water Source: <?= (isset($errors['water_source'])) ? '<span style="color:red">*</span>' : '' ?></label>
                        <div class="pill-group">
                            <input type="radio" name="water_source" value="Community piped" id="ws_piped" <?= (strcasecmp($formData['water_source'] ?? '', 'Community piped') == 0) ? 'checked' : '' ?>>
                            <label for="ws_piped">Community Piped</label>
                            <input type="radio" name="water_source" value="Well" id="ws_well" <?= (strcasecmp($formData['water_source'] ?? '', 'Well') == 0) ? 'checked' : '' ?>>
                            <label for="ws_well">Well</label>
                            <input type="radio" name="water_source" value="Spring" id="ws_spring" <?= (strcasecmp($formData['water_source'] ?? '', 'Spring') == 0) ? 'checked' : '' ?>>
                            <label for="ws_spring">Spring</label>
                        </div>
                    </div>
                    <div class="input-group">
                        <label>Using Iodized Salt: <?= (isset($errors['iodized_salt'])) ? '<span style="color:red">*</span>' : '' ?></label>
                        <div class="pill-group">
                            <input type="radio" name="iodized_salt" value="Yes" id="salt_y" <?= (strcasecmp($formData['iodized_salt'] ?? '', 'Yes') == 0) ? 'checked' : '' ?>>
                            <label for="salt_y">Yes</label>
                            <input type="radio" name="iodized_salt" value="No" id="salt_n" <?= (strcasecmp($formData['iodized_salt'] ?? '', 'No') == 0) ? 'checked' : '' ?>>
                            <label for="salt_n">No</label>
                        </div>
                    </div>
                    <div class="input-group">
                        <label>Using Iron Fortified Rice: <?= (isset($errors['iron_fortified_rice'])) ? '<span style="color:red">*</span>' : '' ?></label>
                        <div class="pill-group">
                            <input type="radio" name="iron_fortified_rice" value="Yes" id="rice_y" <?= (strcasecmp($formData['iron_fortified_rice'] ?? '', 'Yes') == 0) ? 'checked' : '' ?>>
                            <label for="rice_y">Yes</label>
                            <input type="radio" name="iron_fortified_rice" value="No" id="rice_n" <?= (strcasecmp($formData['iron_fortified_rice'] ?? '', 'No') == 0) ? 'checked' : '' ?>>
                            <label for="rice_n">No</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="footer-actions">
                <a href="dashboard.php" class="btn btn-back">← Back to Dashboard</a>
                <button type="submit" class="btn btn-save">Save Changes</button>
            </div>
        </form>
    </div>
</div>
</body>
</html>