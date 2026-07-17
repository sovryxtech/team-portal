<?php
declare(strict_types=1);

namespace Src\Service;

use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;

/**
 * QR Code Generator wrapping Endroid QR Code
 */
class QRCodeGenerator {
    /**
     * Generate base64 Data URI for inline HTML rendering
     */
    public function generateDataUri(string $text, int $size = 180, int $margin = 10): string {
        try {
            $writer = new PngWriter();
            
            // Create QR code using Endroid constructor
            $qrCode = new QrCode(
                $text,
                new Encoding('UTF-8'),
                ErrorCorrectionLevel::Low,
                $size,
                $margin,
                RoundBlockSizeMode::Margin,
                new Color(11, 37, 69),
                new Color(255, 255, 255)
            );
                
            $result = $writer->write($qrCode);
            return $result->getDataUri();
        } catch (\Exception $e) {
            error_log("QR Code Generation failed: " . $e->getMessage());
            // Fallback: Return a simple blank or error state image or online generator URL
            return 'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="150" height="150"><rect width="150" height="150" fill="%23eee"/><text x="10" y="80" font-family="sans-serif" font-size="12" fill="%23cc0000">QR Generation Error</text></svg>';
        }
    }
}
