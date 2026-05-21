<?php
require_once 'config.php';

$errors = [];
$civil_statuses = ['Single', 'Married', 'Separated', 'Divorced', 'Widowed', 'Live-in'];
$employment_opts = ['Employed', 'Unemployed', 'Self-employed', 'Student', 'Retired', 'Homemaker', 'Part-time'];
$education_opts = ['Elementary', 'High school', 'Senior high school', 'College / Undergraduate', 'College Graduate', 'No formal education', 'Vocational / Technical'];
$streets = ['Del Pilar st.', 'National Road', 'Rizal st.', 'P. Viana st.', 'San Jose st.', 'Mercene st.'];
$barangays = ['Poblacion 3'];
$municipalities = ['Mamburao'];
$provinces = ['Occidental Mindoro'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $required_fields = ['first_name', 'last_name', 'civil_status', 'sex', 'dob', 'place_of_birth', 'contact_no', 'religion', 'employment_status', 'educational_attainment', 'current_residence_type', 'current_street', 'current_barangay', 'current_municipality', 'current_province', 'permanent_street', 'permanent_barangay', 'permanent_municipality', 'permanent_province', 'toilet_type', 'water_source', 'iodized_salt', 'iron_fortified_rice'];
    
    foreach ($required_fields as $field) {
        if (empty(trim($_POST[$field] ?? ''))) {
            $errors[$field] = "Required.";
        }
    }

    $contact = trim($_POST['contact_no'] ?? '');
    if (!preg_match('/^09[0-9]{9}$/', $contact)) {
        $errors['contact_no'] = "Must start with 09 and be 11 digits.";
    }

    // Maternal Health Validation
    if (($_POST['sex'] ?? '') === 'Female') {
        if (empty($_POST['family_planning'])) $errors['family_planning'] = "Required.";
        if (empty($_POST['pregnancy_status'])) $errors['pregnancy_status'] = "Required.";
        
        if (($_POST['pregnancy_status'] ?? '') === 'Delivered and Breastfeeding') {
            if (empty($_POST['breastfeeding_type'])) $errors['breastfeeding_type'] = "Required.";
        }
    }
    
    if (!isset($_FILES['id_picture']) || $_FILES['id_picture']['error'] !== UPLOAD_ERR_OK) {
        $errors['id_picture'] = "ID picture required.";
    }

    if (empty($errors)) {
        $temp_dir = isset($temp_dir) ? $temp_dir : sys_get_temp_dir() . DIRECTORY_SEPARATOR;
        if (!is_dir($temp_dir)) {
            mkdir($temp_dir, 0777, true);
        }

        $ext = pathinfo($_FILES['id_picture']['name'], PATHINFO_EXTENSION);
        $temp_name = bin2hex(random_bytes(16)) . '.' . $ext;
        $temp_path = $temp_dir . $temp_name;
        
        if (move_uploaded_file($_FILES['id_picture']['tmp_name'], $temp_path)) {
            $_SESSION['temp_reg_data'] = $_POST;
            $_SESSION['temp_id_picture'] = $temp_path;
            header("Location: create_account.php");
            exit;
        } else {
            $errors['id_picture'] = "Failed to process uploaded file temporary path.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Registration | BHPS</title>
    <link rel="stylesheet" href="register_details-style.css">
    <script src="register_details-script.js" defer></script>
</head>
<body>
<div class="main-wrapper">
    <div class="logo-header">
        <img src="BHPS logo.png" alt="Logo">
        <span>REGISTRATION</span>
    </div>

    <div class="registration-card">
        <form method="POST" enctype="multipart/form-data">
            <h2 class="section-title">Personal Profile</h2>
            <div class="form-grid">
                <div class="input-group">
                    <label>First Name: <?= (isset($errors['first_name'])) ? '<span style="color:red">*</span>' : '' ?></label>
                    <input type="text" name="first_name" value="<?= htmlspecialchars($_POST['first_name'] ?? '') ?>">
                </div>
                <div class="input-group">
                    <label>Middle Name:</label>
                    <input type="text" name="middle_name" value="<?= htmlspecialchars($_POST['middle_name'] ?? '') ?>">
                </div>
                <div class="input-group">
                    <label>Last Name: <?= (isset($errors['last_name'])) ? '<span style="color:red">*</span>' : '' ?></label>
                    <input type="text" name="last_name" value="<?= htmlspecialchars($_POST['last_name'] ?? '') ?>">
                </div>
                <div class="input-group">
                    <label>Civil Status: <?= (isset($errors['civil_status'])) ? '<span style="color:red">*</span>' : '' ?></label>
                    <select name="civil_status">
                        <option value="">Select Status</option>
                        <?php foreach($civil_statuses as $opt): ?>
                            <option <?= (($_POST['civil_status'] ?? '') == $opt) ? 'selected' : '' ?>><?= $opt ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="input-group">
                    <label>Sex: <?= (isset($errors['sex'])) ? '<span style="color:red">*</span>' : '' ?></label>
                    <div class="pill-group">
                        <input type="radio" name="sex" value="Male" id="sex_male" <?= (($_POST['sex'] ?? '') == 'Male') ? 'checked' : '' ?>>
                        <label for="sex_male">Male</label>
                        <input type="radio" name="sex" value="Female" id="sex_female" <?= (($_POST['sex'] ?? '') == 'Female') ? 'checked' : '' ?>>
                        <label for="sex_female">Female</label>
                    </div>
                </div>
                <div class="input-group">
                    <label>Date of Birth: <?= (isset($errors['dob'])) ? '<span style="color:red">*</span>' : '' ?></label>
                    <input type="date" name="dob" id="dob" value="<?= htmlspecialchars($_POST['dob'] ?? '') ?>">
                </div>
                <div class="input-group">
                    <label>Age:</label>
                    <input type="text" name="age" id="age" readonly placeholder="Auto calculated" value="<?= htmlspecialchars($_POST['age'] ?? '') ?>">
                </div>
                <div class="input-group">
                    <label>Place of Birth: <?= (isset($errors['place_of_birth'])) ? '<span style="color:red">*</span>' : '' ?></label>
                    <input type="text" name="place_of_birth" value="<?= htmlspecialchars($_POST['place_of_birth'] ?? '') ?>">
                </div>
                <div class="input-group">
                    <label>Contact No.:</label>
                    <input type="tel" name="contact_no" id="contact_no" placeholder="09XXXXXXXXX" maxlength="11" value="<?= htmlspecialchars($_POST['contact_no'] ?? '') ?>">
                    <?php if(isset($errors['contact_no'])): ?><small class="error-msg"><?= $errors['contact_no'] ?></small><?php endif; ?>
                </div>
                <div class="input-group">
                    <label>Religion: <?= (isset($errors['religion'])) ? '<span style="color:red">*</span>' : '' ?></label>
                    <input type="text" name="religion" value="<?= htmlspecialchars($_POST['religion'] ?? '') ?>">
                </div>
                <div class="input-group">
                    <label>Occupation: <?= (isset($errors['employment_status'])) ? '<span style="color:red">*</span>' : '' ?></label>
                    <select name="employment_status">
                        <option value="">Select Status</option>
                        <?php foreach($employment_opts as $opt): ?>
                            <option <?= (($_POST['employment_status'] ?? '') == $opt) ? 'selected' : '' ?>><?= $opt ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="input-group">
                    <label>Educational Attainment: <?= (isset($errors['educational_attainment'])) ? '<span style="color:red">*</span>' : '' ?></label>
                    <select name="educational_attainment">
                        <option value="">Select Status</option>
                        <?php foreach($education_opts as $opt): ?>
                            <option <?= (($_POST['educational_attainment'] ?? '') == $opt) ? 'selected' : '' ?>><?= $opt ?></option>
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
                        <input type="radio" name="current_residence_type" value="Boarding House" id="res_boarding" <?= (($_POST['current_residence_type'] ?? '') == 'Boarding House') ? 'checked' : '' ?>>
                        <label for="res_boarding">Boarding House</label>
                        <input type="radio" name="current_residence_type" value="Own House" id="res_own" <?= (($_POST['current_residence_type'] ?? '') == 'Own House') ? 'checked' : '' ?>>
                        <label for="res_own">Own House</label>
                    </div>
                </div>
                <div class="form-grid">
                    <div class="input-group">
                        <select name="current_street">
                            <option value="">Select Street</option>
                            <?php foreach($streets as $st): ?>
                                <option <?= (($_POST['current_street'] ?? '') == $st) ? 'selected' : '' ?>><?= $st ?></option>
                            <?php endforeach; ?>
                        </select>
                        <small class="sub-label">Street <?= (isset($errors['current_street'])) ? '<span style="color:red">*</span>' : '' ?></small>
                    </div>
                    <div class="input-group">
                        <select name="current_barangay">
                            <option value="">Select Barangay</option>
                            <?php foreach($barangays as $br): ?>
                                <option <?= (($_POST['current_barangay'] ?? '') == $br) ? 'selected' : '' ?>><?= $br ?></option>
                            <?php endforeach; ?>
                        </select>
                        <small class="sub-label">Barangay <?= (isset($errors['current_barangay'])) ? '<span style="color:red">*</span>' : '' ?></small>
                    </div>
                    <div class="input-group">
                        <select name="current_municipality"><?php foreach($municipalities as $mu) echo "<option>$mu</option>"; ?></select>
                        <small class="sub-label">Municipality</small>
                    </div>
                    <div class="input-group">
                        <select name="current_province"><?php foreach($provinces as $pr) echo "<option>$pr</option>"; ?></select>
                        <small class="sub-label">Province</small>
                    </div>
                </div>

                <h3>Permanent Address</h3>
                <div class="form-grid">
                    <div class="input-group">
                        <select name="permanent_street">
                            <option value="">Select Street</option>
                            <?php foreach($streets as $st): ?>
                                <option <?= (($_POST['permanent_street'] ?? '') == $st) ? 'selected' : '' ?>><?= $st ?></option>
                            <?php endforeach; ?>
                        </select>
                        <small class="sub-label">Street <?= (isset($errors['permanent_street'])) ? '<span style="color:red">*</span>' : '' ?></small>
                    </div>
                    <div class="input-group">
                        <select name="permanent_barangay">
                            <option value="">Select Barangay</option>
                            <?php foreach($barangays as $br): ?>
                                <option <?= (($_POST['permanent_barangay'] ?? '') == $br) ? 'selected' : '' ?>><?= $br ?></option>
                            <?php endforeach; ?>
                        </select>
                        <small class="sub-label">Barangay <?= (isset($errors['permanent_barangay'])) ? '<span style="color:red">*</span>' : '' ?></small>
                    </div>
                    <div class="input-group">
                        <select name="permanent_municipality"><?php foreach($municipalities as $mu) echo "<option>$mu</option>"; ?></select>
                        <small class="sub-label">Municipality</small>
                    </div>
                    <div class="input-group">
                        <select name="permanent_province"><?php foreach($provinces as $pr) echo "<option>$pr</option>"; ?></select>
                        <small class="sub-label">Province</small>
                    </div>
                </div>
            </div>

            <div id="maternal-health-section" class="maternal-section section-box" style="display: none;">
                <div class="section-label">Maternal Health Status</div>
                <div class="input-row">
                    <label>Family Planning: <?= (isset($errors['family_planning'])) ? '<span style="color:red">*</span>' : '' ?></label>
                    <div class="pill-group">
                        <input type="radio" name="family_planning" value="Contraceptive Method" id="fp_contra" <?= (($_POST['family_planning'] ?? '') == 'Contraceptive Method') ? 'checked' : '' ?>>
                        <label for="fp_contra">Contraceptive Method</label>
                        <input type="radio" name="family_planning" value="None" id="fp_none" <?= (($_POST['family_planning'] ?? '') == 'None') ? 'checked' : '' ?>>
                        <label for="fp_none">None</label>
                    </div>
                </div>
                <div class="input-row">
                    <label>Pregnancy Status: <?= (isset($errors['pregnancy_status'])) ? '<span style="color:red">*</span>' : '' ?></label>
                    <div class="pill-group">
                        <input type="radio" name="pregnancy_status" value="Pregnant" id="ps_preg" <?= (($_POST['pregnancy_status'] ?? '') == 'Pregnant') ? 'checked' : '' ?>>
                        <label for="ps_preg">Pregnant</label>
                        <input type="radio" name="pregnancy_status" value="Delivered and Breastfeeding" id="ps_breast" <?= (($_POST['pregnancy_status'] ?? '') == 'Delivered and Breastfeeding') ? 'checked' : '' ?>>
                        <label for="ps_breast">Delivered / Breastfeeding</label>
                        <input type="radio" name="pregnancy_status" value="None" id="ps_none" <?= (($_POST['pregnancy_status'] ?? '') == 'None') ? 'checked' : '' ?>>
                        <label for="ps_none">None</label>
                    </div>
                </div>
                <div id="breastfeeding-group" class="input-row" style="display: none;">
                    <label>Breastfeeding Type (0-12mos Infant): <?= (isset($errors['breastfeeding_type'])) ? '<span style="color:red">*</span>' : '' ?></label>
                    <div class="pill-group">
                        <input type="radio" name="breastfeeding_type" value="Exclusive breastfeeding" id="bf_ex" <?= (($_POST['breastfeeding_type'] ?? '') == 'Exclusive breastfeeding') ? 'checked' : '' ?>>
                        <label for="bf_ex">Exclusive Breastfeeding</label>
                        <input type="radio" name="breastfeeding_type" value="Formula feeding" id="bf_for" <?= (($_POST['breastfeeding_type'] ?? '') == 'Formula feeding') ? 'checked' : '' ?>>
                        <label for="bf_for">Formula Feeding</label>
                        <input type="radio" name="breastfeeding_type" value="Mixed feeding" id="bf_mix" <?= (($_POST['breastfeeding_type'] ?? '') == 'Mixed feeding') ? 'checked' : '' ?>>
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
                            <input type="radio" name="toilet_type" value="Water sealed" id="tt_water" <?= (($_POST['toilet_type'] ?? '') == 'Water sealed') ? 'checked' : '' ?>>
                            <label for="tt_water">Water Sealed</label>
                            <input type="radio" name="toilet_type" value="Open pit" id="tt_pit" <?= (($_POST['toilet_type'] ?? '') == 'Open pit') ? 'checked' : '' ?>>
                            <label for="tt_pit">Open Pit</label>
                        </div>
                    </div>
                    <div class="input-group">
                        <label>Water Source: <?= (isset($errors['water_source'])) ? '<span style="color:red">*</span>' : '' ?></label>
                        <div class="pill-group">
                            <input type="radio" name="water_source" value="Community piped" id="ws_piped" <?= (($_POST['water_source'] ?? '') == 'Community piped') ? 'checked' : '' ?>>
                            <label for="ws_piped">Community Piped</label>
                            <input type="radio" name="water_source" value="Well" id="ws_well" <?= (($_POST['water_source'] ?? '') == 'Well') ? 'checked' : '' ?>>
                            <label for="ws_well">Well</label>
                            <input type="radio" name="water_source" value="Spring" id="ws_spring" <?= (($_POST['water_source'] ?? '') == 'Spring') ? 'checked' : '' ?>>
                            <label for="ws_spring">Spring</label>
                        </div>
                    </div>
                    <div class="input-group">
                        <label>Using Iodized Salt: <?= (isset($errors['iodized_salt'])) ? '<span style="color:red">*</span>' : '' ?></label>
                        <div class="pill-group">
                            <input type="radio" name="iodized_salt" value="Yes" id="salt_y" <?= (($_POST['iodized_salt'] ?? '') == 'Yes') ? 'checked' : '' ?>>
                            <label for="salt_y">Yes</label>
                            <input type="radio" name="iodized_salt" value="No" id="salt_n" <?= (($_POST['iodized_salt'] ?? '') == 'No') ? 'checked' : '' ?>>
                            <label for="salt_n">No</label>
                        </div>
                    </div>
                    <div class="input-group">
                        <label>Using Iron Fortified Rice: <?= (isset($errors['iron_fortified_rice'])) ? '<span style="color:red">*</span>' : '' ?></label>
                        <div class="pill-group">
                            <input type="radio" name="iron_fortified_rice" value="Yes" id="rice_y" <?= (($_POST['iron_fortified_rice'] ?? '') == 'Yes') ? 'checked' : '' ?>>
                            <label for="rice_y">Yes</label>
                            <input type="radio" name="iron_fortified_rice" value="No" id="rice_n" <?= (($_POST['iron_fortified_rice'] ?? '') == 'No') ? 'checked' : '' ?>>
                            <label for="rice_n">No</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="upload-section section-box" id="upload-container">
                <label>Upload your ID picture: <?= (isset($errors['id_picture'])) ? '<span style="color:red">*</span>' : '' ?></label>
                <div class="upload-box">
                    <input type="file" name="id_picture" id="id_picture" accept=".jpg,.jpeg,.png">
                    <label for="id_picture" id="upload-label">
                        <span class="upload-icon">⬆</span> <span id="upload-text">Upload ID</span>
                        <small id="upload-status">Supports: JPG, JPEG, and PNG</small>
                    </label>
                </div>
            </div>

            <div class="footer-actions">
                <a href="login.php" class="btn btn-back">← Back</a>
                <button type="submit" class="btn btn-next">Next →</button>
            </div>
        </form>
    </div>
</div>
</body>
</html>