<?php
// terms.php - Terms of Use for E-Kabarangay
// No login required – accessible from registration and footer links
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms of Use | E-Kabarangay</title>
    <link rel="stylesheet" href="dashboard-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #1547A1;
            font-family: 'Segoe UI', Arial, sans-serif;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
            box-sizing: border-box;
        }
        .legal-container {
            width: 100%;
            max-width: 850px;
            background: #FFFFFF;
            border-radius: 25px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            overflow: hidden;
            position: relative;
            margin-bottom: 80px; /* Space for the absolute positioned back button */
        }
        .legal-header {
            background: #0056b3;
            padding: 25px 35px;
            color: white;
            display: flex;
            align-items: center;
            gap: 20px;
            border-bottom: 4px solid #ffc107;
        }
        .legal-header img {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background: white;
            padding: 5px;
        }
        .legal-header h1 {
            margin: 0;
            font-size: 2rem;
            font-weight: 800;
        }
        .legal-content {
            padding: 40px 45px;
            line-height: 1.7;
            color: #2c3e4f;
        }
        .legal-content h2 {
            color: #004085;
            margin-top: 1.8rem;
            margin-bottom: 1rem;
            font-size: 1.6rem;
            border-left: 5px solid #ffc107;
            padding-left: 15px;
        }
        .legal-content h3 {
            color: #1545a2;
            margin-top: 1.2rem;
            margin-bottom: 0.5rem;
            font-size: 1.2rem;
        }
        .legal-content p, .legal-content li {
            font-size: 1rem;
            margin-bottom: 0.8rem;
            text-align: justify;
        }
        .legal-content ul {
            margin: 10px 0 20px 25px;
        }
        .legal-footer {
            background: #f8f9fc;
            padding: 20px 45px;
            text-align: center;
            border-top: 1px solid #e9ecef;
            display: flex;
            justify-content: flex-end;
            align-items: center;
        }
        .back-section {
            position: absolute;
            bottom: -70px;
            left: 0;
        }
        .btn-back {
            background-color: #CCCCCC;
            color: black;
            text-decoration: none;
            padding: 12px 40px;
            border-radius: 15px;
            font-weight: 800;
            font-size: 1.3rem;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: transform 0.2s;
        }
        .btn-back:hover {
            transform: scale(1.03);
        }
        .last-updated {
            font-size: 0.85rem;
            color: #6c757d;
        }
        @media (max-width: 768px) {
            .legal-content { padding: 25px; }
            .legal-header { flex-direction: column; text-align: center; }
        }
    </style>
</head>
<body>
<div class="legal-container">
    <div class="legal-header">
        <img src="BHPS logo.png" alt="E-Kabarangay Logo">
        <h1>TERMS OF USE</h1>
    </div>
    <div class="legal-content">
        <p><strong>Effective Date:</strong> January 1, 2025</p>
        <p>Welcome to <strong>E-Kabarangay</strong> (the "System"), an online platform operated by Barangay 3, Mamburao, Occidental Mindoro. By accessing or using this System, you agree to be bound by these Terms of Use. If you do not agree, please do not use the System.</p>

        <h2>1. Eligibility</h2>
        <p>You must be a resident of Barangay 3, Mamburao, Occidental Mindoro, or otherwise have official business with the barangay. By registering, you confirm that all information provided is truthful and complete. Minors (under 18) must have consent from a parent or legal guardian.</p>

        <h2>2. Account Registration & Security</h2>
        <p>You are responsible for maintaining the confidentiality of your login credentials. Notify the barangay immediately of any unauthorized use. The barangay reserves the right to suspend or terminate accounts that violate these terms.</p>

        <h2>3. Permitted Use</h2>
        <p>The E-Kabarangay System is provided for lawful purposes only, including:</p>
        <ul>
            <li>Submitting document requests (Barangay Clearance, Certificates, etc.)</li>
            <li>Viewing barangay announcements, ordinances, and hotlines</li>
            <li>Updating your personal, address, and household information</li>
            <li>Receiving notifications regarding requests and community updates</li>
        </ul>

        <h2>4. Prohibited Activities</h2>
        <p>You shall NOT:</p>
        <ul>
            <li>Provide false or misleading information during registration or in any request.</li>
            <li>Use the System to harass, abuse, defame, or threaten any person.</li>
            <li>Attempt to gain unauthorized access to other users' accounts or administrative functions.</li>
            <li>Upload malicious code, viruses, or any material that may disrupt the System.</li>
            <li>Request documents for illegal purposes or misrepresent the purpose of a request.</li>
        </ul>
        <p>Violation may lead to permanent deactivation of your account and legal action under Philippine law.</p>

        <h2>5. Document Requests & Approval</h2>
        <p>All document requests are subject to review and approval by the Barangay Administration. Submission does not guarantee approval. The barangay may deny requests that lack proper purpose, contain false information, or violate local ordinances. Approved documents must be claimed in person at the Barangay Hall unless otherwise arranged.</p>

        <h2>6. Privacy & Data Protection</h2>
        <p>Your privacy is important. Please review our <a href="privacy.php" style="color: #1545a2; font-weight: bold;">Privacy Policy</a> to understand how we collect, use, and protect your personal information. By using the System, you consent to such data processing as described.</p>

        <h2>7. Intellectual Property</h2>
        <p>All content, logos, software, and design of E-Kabarangay are property of Barangay 3, Mamburao. You may not copy, reproduce, or reverse-engineer any part without written permission.</p>

        <h2>8. Limitation of Liability</h2>
        <p>The System is provided "as is" without warranties of uninterrupted or error-free operation. The barangay shall not be liable for any indirect, incidental, or consequential damages arising from use of the System, including delays, data loss, or system downtime. In no event shall liability exceed PHP 1,000.00.</p>

        <h2>9. Modifications to Terms</h2>
        <p>We may update these Terms from time to time. Continued use of the System after changes constitutes acceptance of the revised Terms. Material changes will be notified via announcement or email.</p>

        <h2>10. Governing Law & Dispute Resolution</h2>
        <p>These Terms are governed by the laws of the Republic of the Philippines. Any dispute shall first be brought to the attention of the Barangay Captain for amicable settlement. If unresolved, the exclusive venue for litigation shall be the proper courts of Mamburao, Occidental Mindoro.</p>

        <h2>11. Contact Information</h2>
        <p>For questions or concerns regarding these Terms, please contact:<br>
        <i class="fas fa-envelope"></i> <strong>ekabarangay@gmail.com</strong><br>
        <i class="fas fa-phone-alt"></i> Barangay Hotline: (available on Barangay Hotline page)<br>
        Barangay Hall, Barangay 3, Mamburao, Occidental Mindoro.</p>
    </div>
    <div class="legal-footer">
        <div class="last-updated"><i class="far fa-calendar-alt"></i> Last Revised: April 10, 2025</div>
    </div>
    <div class="back-section">
        <a href="javascript:history.back()" class="btn-back"><i class="fas fa-arrow-left"></i> Back</a>
    </div>
</div>
</body>
</html>