<?php
// privacy.php - Privacy Policy for E-Kabarangay
// No login required – accessible from registration and footer links
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Policy | E-Kabarangay</title>
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
            border-bottom: 4px solid #28a745;
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
            border-left: 5px solid #28a745;
            padding-left: 15px;
        }
        .legal-content h3 {
            color: #1545a2;
            margin-top: 1.2rem;
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
        <h1>PRIVACY POLICY</h1>
    </div>
    <div class="legal-content">
        <p><strong>Effective Date:</strong> January 1, 2025</p>
        <p>This Privacy Policy explains how <strong>Barangay 3, Mamburao, Occidental Mindoro</strong> ("we", "us", "our") collects, uses, stores, and protects your personal data when you use the <strong>E-Kabarangay</strong> System (the "System"). We are committed to complying with the Republic Act No. 10173, otherwise known as the <strong>Data Privacy Act of 2012</strong> of the Philippines.</p>

        <h2>1. Information We Collect</h2>
        <p>To provide barangay services efficiently, we collect the following categories of personal information:</p>
        <ul>
            <li><strong>Personal Profile:</strong> Full name, date of birth, sex, civil status, place of birth, religion, contact number, email address, username.</li>
            <li><strong>Address Details:</strong> Current and permanent address (street, barangay, municipality, province), residence type (owned/boarding).</li>
            <li><strong>Household & Sanitation:</strong> Toilet type, water source, use of iodized salt, iron-fortified rice.</li>
            <li><strong>Maternal & Child Health:</strong> Pregnancy status, family planning method, breastfeeding type (for female residents with infants).</li>
            <li><strong>Employment & Education:</strong> Occupation/employment status, educational attainment.</li>
            <li><strong>Government ID Picture:</strong> Uploaded ID photo for identity verification.</li>
            <li><strong>Document Requests:</strong> Type of document requested, purpose, status, and timestamps.</li>
            <li><strong>System Usage Data:</strong> Announcement views, comments, notification logs, and IP addresses for security.</li>
        </ul>

        <h2>2. How We Collect Data</h2>
        <p>We collect information directly from you when you:</p>
        <ul>
            <li>Register for an account.</li>
            <li>Submit or update your resident profile.</li>
            <li>Request barangay documents.</li>
            <li>Post comments on announcements.</li>
            <li>Communicate with the barangay through the System.</li>
        </ul>

        <h2>3. Legal Basis & Purposes of Processing</h2>
        <p>Your personal data is processed based on your consent (given during registration) and for the legitimate interests of the barangay, including:</p>
        <ul>
            <li>Managing resident records and master list.</li>
            <li>Processing and approving document requests (Barangay Clearance, Certificates, etc.).</li>
            <li>Delivering notifications about request status, announcements, and ordinances.</li>
            <li>Health profiling (e.g., maternal health, nutrition programs).</li>
            <li>Compliance with national and local government reporting requirements.</li>
            <li>Improving barangay services and responding to inquiries.</li>
        </ul>

        <h2>4. Data Sharing & Disclosure</h2>
        <p>We do <strong>not</strong> sell your personal data. We may share your information only in the following circumstances:</p>
        <ul>
            <li><strong>Within Barangay Officials & Staff:</strong> Authorized personnel (e.g., Barangay Captain, Secretary, Health Worker) may access data to perform official functions.</li>
            <li><strong>Government Agencies:</strong> When required by law (e.g., Department of the Interior and Local Government, Philippine Statistics Authority, Municipal Health Office) for census, health programs, or legal mandates.</li>
            <li><strong>Service Providers:</strong> Third-party hosting or technical support under strict confidentiality agreements.</li>
            <li><strong>Legal Obligations:</strong> To comply with a subpoena, court order, or enforceable government request.</li>
        </ul>

        <h2>5. Data Security & Retention</h2>
        <p>We implement reasonable physical, technical, and organizational security measures to protect against unauthorized access, alteration, disclosure, or destruction. These include encrypted passwords, access controls, and regular backups.</p>
        <p>Your personal data will be retained for as long as your account is active or as necessary to provide services. After account deactivation or upon request, data may be anonymized or archived in accordance with Philippine retention laws (e.g., local government recordkeeping). ID pictures and sensitive information are stored securely.</p>

        <h2>6. Your Rights Under the Data Privacy Act</h2>
        <p>You have the following rights, which you may exercise by contacting our Data Protection Officer (see Section 10):</p>
        <ul>
            <li><strong>Right to be informed</strong> – of the collection and processing of your data.</li>
            <li><strong>Right to access</strong> – request a copy of your personal data.</li>
            <li><strong>Right to rectification</strong> – correct inaccurate or incomplete data.</li>
            <li><strong>Right to erasure/blocking</strong> – request deletion of your data when no longer necessary or when consent is withdrawn.</li>
            <li><strong>Right to object</strong> – to processing for legitimate reasons.</li>
            <li><strong>Right to data portability</strong> – obtain a copy of your data in a structured format.</li>
        </ul>
        <p>To exercise these rights, please submit a written request. We will respond within 15 working days as required by law.</p>

        <h2>7. Cookies & Tracking Technologies</h2>
        <p>E-Kabarangay uses session cookies to maintain your login state and preferences. These do not collect personal data beyond the session. You may disable cookies in your browser, but that may affect functionality.</p>

        <h2>8. Links to Other Websites</h2>
        <p>The System may contain links to external government websites (e.g., DILG). We are not responsible for their privacy practices. We encourage you to read their privacy policies.</p>

        <h2>9. Changes to This Privacy Policy</h2>
        <p>We may update this Privacy Policy periodically. We will notify you of material changes via announcement on the dashboard or email. The "Effective Date" at the top indicates the latest revision. Your continued use of the System constitutes acceptance of the updated policy.</p>

        <h2>10. Data Protection Officer & Contact Information</h2>
        <p>If you have questions, concerns, or wish to exercise your privacy rights, please contact:</p>
        <p>
        <strong>Barangay Secretary / Data Protection Officer</strong><br>
        Barangay 3 Hall, Mamburao, Occidental Mindoro<br>
        Email: <strong>ekabarangay@gmail.com</strong><br>
        Phone: (available via Barangay Hotline page)<br>
        Office Hours: Monday – Friday, 8:00 AM – 5:00 PM
        </p>

        <h2>11. Consent</h2>
        <p>By registering and using E-Kabarangay, you freely give your consent to the collection and processing of your personal data as described in this Privacy Policy. You may withdraw your consent by requesting account deactivation; however, withdrawal may affect your ability to use certain services (e.g., document requests).</p>
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