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
function get_image_data_uri($relativePath): string {
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
$verticalDesignation = implode("<br>", mb_str_split(strtoupper($details['designation_title'])));
$addressOrBranch = htmlspecialchars($details['address'] ?? $details['branch_name']);
$contactNumber = htmlspecialchars($details['phone'] ?? 'N/A');
$logoDataUri = get_image_data_uri('images/LOGO.png');

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
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
            color: #000;
        }
        .card {
            width: 240pt;
            height: 380pt;
            position: relative;
            background-color: #ffffff;
            border: 1px solid #ccc;
            box-sizing: border-box;
            overflow: hidden;
            text-align: left;
        }
        
        .right-banner {
            position: absolute;
            right: 0;
            top: 0;
            width: 45pt;
            height: 380pt;
            background-color: #a98f3b;
            text-align: center;
        }

        .right-banner-text {
            color: #ffffff;
            font-size: 18pt;
            font-weight: bold;
            margin-top: 30pt;
            line-height: 1.2;
        }

        .header {
            padding: 10pt;
            position: relative;
            z-index: 1;
        }
        .header img {
            height: 30pt;
            margin-left: -5pt;
        }

        .photo-container {
            text-align: center;
            margin-top: 5pt;
            padding-right: 45pt;
        }
        .photo {
            width: 90pt;
            height: 90pt;
            border-radius: 50%;
            border: 3pt solid #a98f3b;
            background: white;
        }
        .photo-placeholder {
            width: 90pt;
            height: 90pt;
            border-radius: 50%;
            border: 3pt solid #a98f3b;
            background-color: #0B2545;
            color: #ffffff;
            text-align: center;
            line-height: 90pt;
            font-size: 24pt;
            font-weight: bold;
            margin: 0 auto;
        }

        .name {
            text-align: center;
            font-family: Georgia, "Times New Roman", Times, serif;
            font-size: 15pt;
            font-weight: 800;
            font-variant: small-caps;
            margin-top: 10pt;
            margin-bottom: 10pt;
            padding-right: 45pt;
        }

        .details-box {
            padding-left: 15pt;
            font-size: 9pt;
            line-height: 1.4;
            width: 180pt;
        }

        .qr-container {
            padding-left: 15pt;
            margin-top: 5pt;
        }
        .qr-container img {
            width: 60pt;
            height: 60pt;
        }

        .signature {
            padding-left: 15pt;
            font-size: 7pt;
            font-weight: bold;
            margin-top: 5pt;
        }
        .signature-img {
            font-family: serif;
            font-style: italic;
            font-size: 14pt;
            border-bottom: 1px solid #000;
            width: 80pt;
            text-align: center;
            margin-bottom: 2pt;
        }

        .footer {
            position: absolute;
            bottom: 10pt;
            left: 0;
            width: 195pt;
            text-align: center;
            font-size: 7pt;
            font-weight: bold;
        }
        
        .watermark {
            position: absolute;
            top: 50%;
            left: 100pt;
            transform: translate(-50%, -50%);
            opacity: 0.05;
            z-index: 0;
        }
        .watermark img {
            width: 150pt;
            /* DomPDF filter grayscale might not work, but we set opacity */
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="watermark">
            <img src="' . $logoDataUri . '" alt="">
        </div>
        
        <div class="header">
            <img src="' . $logoDataUri . '" alt="">
        </div>
        
        <div class="right-banner">
            <div class="right-banner-text">' . $verticalDesignation . '</div>
        </div>

        <div class="photo-container">';
        
        if (!empty($photoDataUri)) {
            $html .= '<img src="' . $photoDataUri . '" class="photo">';
        } else {
            $html .= '<div class="photo-placeholder">' . strtoupper(substr($details['full_name'], 0, 2)) . '</div>';
        }
        
        $html .= '
        </div>

        <div class="name">' . htmlspecialchars($details['full_name']) . '</div>
        
        <div class="details-box">
            <div><strong>ID NO:</strong> ' . htmlspecialchars($details['employee_custom_id']) . '</div>
            <div><strong>Address:</strong> ' . $addressOrBranch . '</div>
            <div><strong>Contact:</strong> ' . $contactNumber . '</div>
            <div><strong>Email:</strong> ' . htmlspecialchars($details['email']) . '</div>
        </div>

        <div class="qr-container">
            <img src="' . $qrDataUri . '">
        </div>

        <div class="signature">
            <div class="signature-img">Approved</div>
            <div>AUTHORIZED BY CEO</div>
        </div>

        <div class="footer">
            ' . $addressOrBranch . ', Nepal<br>
            Email: sovryx.tech@gmail.com
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
