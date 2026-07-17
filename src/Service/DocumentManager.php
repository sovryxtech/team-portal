<?php
declare(strict_types=1);

namespace Src\Service;

/**
 * Secure File Upload Manager
 */
class DocumentManager {
    private array $config;

    public function __construct() {
        $this->config = require __DIR__ . '/../../config/app.php';
    }

    /**
     * Upload profile photo securely
     */
    public function uploadProfilePhoto(array $file): string {
        return $this->upload($file, 'profiles', ['jpg', 'jpeg', 'png'], ['image/jpeg', 'image/png']);
    }

    /**
     * Upload registration documents securely
     */
    public function uploadDocument(array $file): string {
        return $this->upload($file, 'documents');
    }

    /**
     * Core upload execution and validation
     */
    private function upload(array $file, string $subFolder, array $customExts = null, array $customMimes = null): string {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new \RuntimeException($this->getUploadErrorMessage($file['error']));
        }

        // Validate File Size
        $maxSize = $this->config['max_upload_size'] ?? (5 * 1024 * 1024);
        if ($file['size'] > $maxSize) {
            throw new \RuntimeException("File size exceeds limit of " . ($maxSize / (1024 * 1024)) . "MB.");
        }

        // Validate File Extension
        $filename = basename($file['name']);
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $allowedExts = $customExts ?? $this->config['allowed_extensions'] ?? [];
        if (!in_array($ext, $allowedExts, true)) {
            throw new \RuntimeException("Invalid file extension: .{$ext}. Allowed extensions: " . implode(', ', $allowedExts));
        }

        // Validate MIME-Type using finfo
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        if (!$finfo) {
            throw new \RuntimeException("Could not open Fileinfo resource.");
        }
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        $allowedMimes = $customMimes ?? $this->config['allowed_mime_types'] ?? [];
        if (!in_array($mime, $allowedMimes, true)) {
            throw new \RuntimeException("Invalid file format. Detected MIME type: {$mime}");
        }

        // Generate Random Safe Filename
        $safeName = $subFolder . '_' . uniqid('', true) . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
        $uploadDir = __DIR__ . '/../../uploads/' . $subFolder . '/';
        
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $destPath = $uploadDir . $safeName;

        if (!move_uploaded_file($file['tmp_name'], $destPath)) {
            throw new \RuntimeException("Failed to move uploaded file to target destination.");
        }

        // Return path relative to project root
        return 'uploads/' . $subFolder . '/' . $safeName;
    }

    /**
     * Map PHP upload errors to readable messages
     */
    private function getUploadErrorMessage(int $code): string {
        return match ($code) {
            UPLOAD_ERR_INI_SIZE   => 'The uploaded file exceeds the upload_max_filesize directive in php.ini.',
            UPLOAD_ERR_FORM_SIZE  => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.',
            UPLOAD_ERR_PARTIAL    => 'The uploaded file was only partially uploaded.',
            UPLOAD_ERR_NO_FILE    => 'No file was uploaded.',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder.',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
            UPLOAD_ERR_EXTENSION  => 'A PHP extension stopped the file upload.',
            default               => 'Unknown upload error.',
        };
    }
}
