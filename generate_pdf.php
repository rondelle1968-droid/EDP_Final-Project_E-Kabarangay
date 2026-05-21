<?php
require_once 'config.php';
require_once 'fpdf186/fpdf.php'; // Updated to match your folder name

// Session check
if (!isset($_SESSION['account_id']) || $_SESSION['is_admin'] != 1) {
    die("Unauthorized access.");
}

$id = $_GET['id'] ?? null;

if (!$id) {
    die("Request ID missing.");
}

// Fetch request details
$sql = "SELECT d.*, r.first_name, r.middle_name, r.last_name, 
        CONCAT(a.street, ', ', a.barangay, ', ', a.municipality) as full_address 
        FROM document_requests d
        JOIN residents r ON d.resident_id = r.id
        LEFT JOIN address a ON r.id = a.resident_id AND a.address_type = 'current'
        WHERE d.id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);
$req = $stmt->fetch();

if (!$req) {
    die("Request not found.");
}

// Format Name
$middleInitial = !empty($req['middle_name']) ? substr($req['middle_name'], 0, 1) . '. ' : '';
$fullName = strtoupper($req['first_name'] . ' ' . $middleInitial . $req['last_name']);
$date = date('jS \d\a\y \o\f F, Y');

class PDF extends FPDF {
    function Header() {
        // Logo
        if (file_exists('BHPS logo.png')) {
            $this->Image('BHPS logo.png', 20, 10, 25);
        }
        $this->SetFont('Arial', '', 11);
        $this->Cell(0, 5, 'Republic of the Philippines', 0, 1, 'C');
        $this->Cell(0, 5, 'Province of Occidental Mindoro', 0, 1, 'C');
        $this->Cell(0, 5, 'Municipality of Mamburao', 0, 1, 'C');
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 5, 'BARANGAY TRES', 0, 1, 'C'); 
        $this->Ln(2);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(0, 5, 'OFFICE OF THE BARANGAY CHAIRMAN', 0, 1, 'C');
        $this->Ln(5);
        $this->Line(20, 42, 190, 42);
        $this->Ln(10);
    }

    function Footer() {
        $this->SetY(-40);
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, 'HON. GLENDA V. TACDOL', 0, 1, 'R'); 
        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 5, 'Punong Barangay', 0, 1, 'R');
    }
}

function print_pdf($req, $fullName, $date) {
    $pdf = new PDF();
    $pdf->AddPage();
    $pdf->SetMargins(25, 50, 25);
    
    
    switch ($req['document_type']) {
        case 'Barangay Clearance':
            $pdf->SetFont('Arial', 'B', 20);
            $pdf->Cell(0, 20, 'BARANGAY CLEARANCE', 0, 1, 'C');
            $pdf->Ln(10);
            $pdf->SetFont('Arial', '', 12);
            $content = "TO WHOM IT MAY CONCERN:\n\nThis is to certify that $fullName, of legal age, is a bona fide resident of this Barangay with postal address at " . $req['full_address'] . ".\n\nThis is to certify further that the above-mentioned name has NO DEROGATORY RECORD filed in this office as of this date.\n\nThis clearance is being issued upon the request of the interested party for: " . strtoupper($req['purpose']) . ".\n\nIssued this $date.";
            $pdf->MultiCell(0, 8, $content);
            break;

        case 'Barangay Certificate':
            $pdf->SetFont('Arial', 'B', 20);
            $pdf->Cell(0, 20, 'BARANGAY CERTIFICATION', 0, 1, 'C');
            $pdf->Ln(10);
            $pdf->SetFont('Arial', '', 12);
            $content = "TO WHOM IT MAY CONCERN:\n\nThis is to certify that $fullName is a known resident of this Barangay and is known to be of good moral character.\n\nThis certification is issued upon the request of the above-named person for " . $req['purpose'] . " and for whatever legal purpose it may serve.\n\nIssued this $date.";
            $pdf->MultiCell(0, 8, $content);
            break;

        case 'Certificate of Indigency':
            $pdf->SetFont('Arial', 'B', 20);
            $pdf->Cell(0, 20, 'CERTIFICATE OF INDIGENCY', 0, 1, 'C');
            $pdf->Ln(10);
            $pdf->SetFont('Arial', '', 12);
            $content = "TO WHOM IT MAY CONCERN:\n\nThis is to certify that $fullName is a permanent resident of this Barangay and is one of those who belong to a low-income family.\n\nThis certification is issued for the purpose of " . $req['purpose'] . " .\n\nIssued this $date.";
            $pdf->MultiCell(0, 8, $content);
            break;

        case 'Certificate of Residency':
            $pdf->SetFont('Arial', 'B', 20);
            $pdf->Cell(0, 20, 'CERTIFICATE OF RESIDENCY', 0, 1, 'C');
            $pdf->Ln(10);
            $pdf->SetFont('Arial', '', 12);
            $content = "TO WHOM IT MAY CONCERN:\n\nThis is to certify that $fullName is a permanent resident of " . $req['full_address'] . ".\n\nThis certification is issued upon the request of the interested party for the purpose of: " . $req['purpose'] . ".\n\nGiven this $date.";
            $pdf->MultiCell(0, 8, $content);
            break;

        default:
            $pdf->SetFont('Arial', 'B', 18);
            $pdf->Cell(0, 20, strtoupper($req['document_type']), 0, 1, 'C');
            $pdf->Ln(10);
            $pdf->SetFont('Arial', '', 12);
            $pdf->Cell(0, 10, "Requested by: " . $fullName, 0, 1);
            $pdf->Cell(0, 10, "Purpose: " . $req['purpose'], 0, 1);
            break;
    }
    
    $pdf->Output('I', $req['document_type'] . '_' . $req['last_name'] . '.pdf');
}

print_pdf($req, $fullName, $date);
?>