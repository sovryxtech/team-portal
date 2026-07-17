<?php
declare(strict_types=1);

/**
 * PDF ID Card Generation Endpoint using DomPDF
 */

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../src/Controller/EmployeeController.php';
require_once __DIR__ . '/../src/Service/QRCodeGenerator.php';
require_once __DIR__ . '/../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// Enforce authentication
if (!auth_check()) {
    http_response_code(403);
    die("Access Denied. Please log in first.");
}

$currentUser = auth_user();
$employeeId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($employeeId <= 0) {
    die("Invalid Employee ID.");
}

// Security: Employees can only download their own ID, Admins can download any
if ($_SESSION['role_name'] === 'Employee' && (int)$currentUser['employee_id'] !== $employeeId) {
    http_response_code(403);
    die("Access Denied. You are not authorized to view this ID card.");
}

$empController = new \Src\Controller\EmployeeController();
$details = $empController->getDetails($employeeId);

if (!$details) {
    die("Employee profile record not found.");
}

// Generate QR Code linking directly to the public verify endpoint
$qrGenerator = new \Src\Service\QRCodeGenerator();
$verificationUrl = get_base_url() . '/verify.php?id=' . $details['employee_custom_id'];
$qrDataUri = $qrGenerator->generateDataUri($verificationUrl);

// Helper to convert images to base64 Data URIs for absolute DomPDF reliability
function get_image_data_uri(string|null $relativePath): string {
    if ($relativePath === null) return '';
    $absolutePath = __DIR__ . '/../' . $relativePath;
    if (file_exists($absolutePath) && is_file($absolutePath)) {
        $mime = mime_content_type($absolutePath);
        $data = file_get_contents($absolutePath);
        return 'data:' . $mime . ';base64,' . base64_encode($data);
    }
    return '';
}

$photoDataUri = get_image_data_uri($details['profile_photo']);

// HTML template optimized for DomPDF rendering
$html = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        @page {
            margin: 0;
        }
        body {
            font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
            color: #1E293B;
            -webkit-print-color-adjust: exact;
        }
        .card {
            width: 240pt;
            height: 380pt;
            position: relative;
            background-color: #ffffff;
            border: 1px solid #E2E8F0;
            box-sizing: border-box;
            overflow: hidden;
        }
        .header {
            height: 95pt;
            background: #0B2545;
            text-align: center;
            padding-top: 15pt;
            color: #ffffff;
        }
        .header h5 {
            margin: 0;
            font-size: 14pt;
            font-weight: bold;
            letter-spacing: 1px;
        }
        .header h5 span {
            color: #F79F1F;
        }
        .header .subtitle {
            font-size: 6pt;
            color: #94A3B8;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .photo-wrapper {
            position: absolute;
            top: 60pt;
            left: 50%;
            margin-left: -40pt;
            width: 80pt;
            height: 80pt;
        }
        .photo {
            width: 80pt;
            height: 80pt;
            border-radius: 50%;
            border: 3pt solid #ffffff;
            background-color: #F8FAFC;
        }
        .photo-placeholder {
            width: 80pt;
            height: 80pt;
            border-radius: 50%;
            border: 3pt solid #ffffff;
            background-color: #0B2545;
            color: #ffffff;
            text-align: center;
            line-height: 80pt;
            font-size: 24pt;
            font-weight: bold;
        }
        .status-badge {
            position: absolute;
            top: 10pt;
            right: 10pt;
            background-color: #10B981;
            color: #ffffff;
            font-size: 6pt;
            padding: 3pt 8pt;
            border-radius: 50pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        .body {
            padding-top: 55pt;
            text-align: center;
        }
        .body h4 {
            margin: 0;
            font-size: 13pt;
            color: #0B2545;
            font-weight: bold;
        }
        .body .designation {
            color: #F79F1F;
            font-weight: bold;
            font-size: 8pt;
            margin: 2pt 0 10pt 0;
            text-transform: uppercase;
        }
        .details {
            padding: 0 20pt;
            text-align: left;
            font-size: 7.5pt;
            line-height: 1.5;
        }
        .details-row {
            margin-bottom: 2pt;
        }
        .details-label {
            color: #64748B;
            width: 75pt;
            display: inline-block;
        }
        .details-value {
            color: #0B2545;
            font-weight: bold;
            display: inline-block;
        }
        .qr-section {
            position: absolute;
            bottom: 15pt;
            left: 0;
            right: 0;
            text-align: center;
        }
        .qr-image {
            width: 65pt;
            height: 65pt;
            border: 1px solid #E2E8F0;
            padding: 2pt;
            background: #ffffff;
            border-radius: 4pt;
        }
        .qr-text {
            font-size: 5pt;
            color: #94A3B8;
            margin-top: 3pt;
        }
    </style>
</head>
<body>
    <div class="card">
        <!-- Status Badge -->
        <div class="status-badge">' . htmlspecialchars($details['employment_status']) . '</div>

        <!-- Header -->
        <div class="header">
            <h5>SOVRYX<span>TECH</span></h5>
            <div class="subtitle">Digital Identity Card</div>
        </div>

        <!-- Photo -->
        <div class="photo-wrapper">';
        if (!empty($photoDataUri)) {
            $html .= '<img src="' . $photoDataUri . '" class="photo">';
        } else {
            $html .= '<div class="photo-placeholder">' . strtoupper(substr($details['full_name'], 0, 2)) . '</div>';
        }
        $html .= '</div>

        <!-- Body Details -->
        <div class="body">
            <h4>' . htmlspecialchars($details['full_name']) . '</h4>
            <div class="designation">' . htmlspecialchars($details['designation_title']) . '</div>
            
            <div class="details">
                <div class="details-row">
                    <span class="details-label">Employee ID:</span>
                    <span class="details-value">' . htmlspecialchars($details['employee_custom_id']) . '</span>
                </div>
                <div class="details-row">
                    <span class="details-label">Department:</span>
                    <span class="details-value">' . htmlspecialchars($details['department_name']) . '</span>
                </div>
                <div class="details-row">
                    <span class="details-label">Office Branch:</span>
                    <span class="details-value">' . htmlspecialchars($details['branch_name']) . '</span>
                </div>
                <div class="details-row">
                    <span class="details-label">Issue Date:</span>
                    <span class="details-value">' . htmlspecialchars($details['joining_date']) . '</span>
                </div>
            </div>
        </div>

        <!-- QR Verification Code -->
        <div class="qr-section">
            <img src="' . $qrDataUri . '" class="qr-image">
            <div class="qr-text">Scan badge to verify authenticity</div>
        </div>
    </div>
</body>
</html>
';

try {
    // Configure Dompdf Options
    $options = new Options();
    $options->set('isHtml5ParserEnabled', true);
    
    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html);
    
    // Set custom size for ID Card (PVC size: 240pt x 380pt)
    $dompdf->setPaper([0, 0, 240, 380], 'portrait');
    
    $dompdf->render();
    
    // Stream PDF file back to user
    $filename = "ID_Card_" . str_replace('-', '_', $details['employee_custom_id']) . ".pdf";
    $dompdf->stream($filename, ["Attachment" => true]);
    
} catch (\Exception $e) {
    error_log("DomPDF rendering failed: " . $e->getMessage());
    die("PDF Generation failed: " . htmlspecialchars($e->getMessage()));
}
