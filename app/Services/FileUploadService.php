<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Format;
use Intervention\Image\ImageManager;

class FileUploadService
{
    /**
     * Allowed document MIME types and extensions
     */
    protected const ALLOWED_DOC_MIMES = [
        'application/pdf' => 'pdf',
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];

    /**
     * Detect actual MIME type using PHP Fileinfo magic byte analysis
     */
    public function detectActualMimeType(UploadedFile $file): string
    {
        $realPath = $file->getRealPath();
        if ($realPath && function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            if ($finfo) {
                $mime = finfo_file($finfo, $realPath);
                finfo_close($finfo);
                if (! empty($mime)) {
                    return $mime;
                }
            }
        }

        return $file->getMimeType() ?? 'application/octet-stream';
    }

    /**
     * Upload student photo
     */
    public function uploadStudentPhoto(UploadedFile $file, string $userId): string
    {
        $maxPhotoSize = config('app.photo_max_size_kb', 2048) * 1024;
        if ($file->getSize() > $maxPhotoSize) {
            throw new \InvalidArgumentException('Student photo exceeds maximum allowed size of 2MB.');
        }

        $actualMime = $this->detectActualMimeType($file);
        $allowedPhotoMimes = ['image/jpeg', 'image/png', 'image/webp'];
        if (! in_array($actualMime, $allowedPhotoMimes, true)) {
            throw new \InvalidArgumentException('Invalid student photo format. Only JPG, PNG, and WebP images are permitted.');
        }

        $maxWidth = config('app.photo_max_width', 400);
        $maxHeight = config('app.photo_max_height', 500);
        $jpegQuality = config('app.photo_jpeg_quality', 80);

        // Always stored as JPEG with unpredictable cryptographic random filename
        $filename = 'photo_' . bin2hex(random_bytes(16)) . '.jpg';
        $path = 'uploads/students/photos/' . date('Y/m');

        // Resize and optimize image (aspect-preserving, never upscales)
        $manager = new ImageManager(GdDriver::class);
        $encoded = $manager->decodePath($file->getPathname())
            ->scaleDown($maxWidth, $maxHeight)
            ->encodeUsingFormat(Format::JPEG, $jpegQuality);

        Storage::disk('public')->put($path.'/'.$filename, (string) $encoded);

        return Storage::url($path.'/'.$filename);
    }

    /**
     * Upload document (CNIC, certificates, etc.) with safe magic bytes MIME inspection
     */
    public function uploadDocument(UploadedFile $file, string $userId, string $type): string
    {
        $maxDocSize = config('app.doc_max_size_kb', 5120) * 1024;
        if ($file->getSize() > $maxDocSize) {
            throw new \InvalidArgumentException('Document exceeds maximum allowed size of 5MB.');
        }

        $actualMime = $this->detectActualMimeType($file);

        if (! array_key_exists($actualMime, self::ALLOWED_DOC_MIMES)) {
            throw new \InvalidArgumentException('Unsupported or unsafe file format detected via content inspection.');
        }

        $safeExtension = self::ALLOWED_DOC_MIMES[$actualMime];
        $safeType = preg_replace('/[^a-zA-Z0-9_-]/', '', $type);
        $prefix = ! empty($safeType) ? $safeType . '_' : 'doc_';

        // Cryptographically secure random filename
        $filename = $prefix . bin2hex(random_bytes(16)) . '.' . $safeExtension;
        $path = 'uploads/students/documents/' . date('Y/m');

        $file->storeAs($path, $filename, 'public');

        return Storage::url($path.'/'.$filename);
    }

    /**
     * Upload multiple documents
     */
    public function uploadMultipleDocuments(array $files, string $userId, string $type): array
    {
        $uploadedFiles = [];

        foreach ($files as $file) {
            $uploadedFiles[] = $this->uploadDocument($file, $userId, $type);
        }

        return $uploadedFiles;
    }

    /**
     * Delete file
     */
    public function deleteFile(string $filePath): bool
    {
        // Convert URL to storage path
        $path = str_replace('/storage/', '', parse_url($filePath, PHP_URL_PATH));

        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->delete($path);
        }

        return false;
    }

    /**
     * Validate file size and MIME magic bytes
     */
    public function validateFile(UploadedFile $file, array $allowedExtensions = ['pdf', 'jpg', 'jpeg', 'png'], int $maxSizeKB = 5120): array
    {
        $errors = [];

        // Check file size
        if ($file->getSize() > ($maxSizeKB * 1024)) {
            $errors[] = 'File size must not exceed '.($maxSizeKB / 1024).'MB';
        }

        // Validate via MIME inspection (magic bytes)
        $mime = $file->getMimeType();
        $guessedExt = strtolower($file->guessExtension() ?? '');

        $validMimes = [
            'application/pdf' => ['pdf'],
            'image/jpeg' => ['jpg', 'jpeg'],
            'image/png' => ['png'],
            'image/webp' => ['webp'],
        ];

        $isValidMime = false;
        foreach ($allowedExtensions as $ext) {
            $normExt = strtolower($ext);
            if ($guessedExt === $normExt || ($normExt === 'jpg' && $guessedExt === 'jpeg') || ($normExt === 'jpeg' && $guessedExt === 'jpg')) {
                $isValidMime = true;
                break;
            }
        }

        if (! $isValidMime) {
            $errors[] = 'Invalid file type. Allowed formats: '.implode(', ', $allowedExtensions);
        }

        return $errors;
    }

    /**
     * Get file size in human readable format
     */
    public function getFileSize(string $filePath): string
    {
        $path = str_replace('/storage/', '', parse_url($filePath, PHP_URL_PATH));

        if (Storage::disk('public')->exists($path)) {
            $bytes = Storage::disk('public')->size($path);

            if ($bytes >= 1073741824) {
                return number_format($bytes / 1073741824, 2).' GB';
            } elseif ($bytes >= 1048576) {
                return number_format($bytes / 1048576, 2).' MB';
            } elseif ($bytes >= 1024) {
                return number_format($bytes / 1024, 2).' KB';
            } else {
                return $bytes.' bytes';
            }
        }

        return 'Unknown';
    }
}
