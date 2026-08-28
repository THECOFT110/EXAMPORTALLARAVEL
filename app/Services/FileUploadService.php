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
     * Upload student photo
     */
    public function uploadStudentPhoto(UploadedFile $file, string $userId): string
    {
        // Always stored as JPEG since the image is re-encoded below
        $filename = 'photo_'.$userId.'_'.time().'.jpg';
        $path = 'uploads/students/photos/'.date('Y/m');

        // Resize and optimize image (aspect-preserving, never upscales)
        $manager = new ImageManager(GdDriver::class);
        $encoded = $manager->decodePath($file->getPathname())
            ->scaleDown(400, 500)
            ->encodeUsingFormat(Format::JPEG, 80);

        Storage::disk('public')->put($path.'/'.$filename, (string) $encoded);

        return Storage::url($path.'/'.$filename);
    }

    /**
     * Upload document (CNIC, certificates, etc.) with safe MIME inspection
     */
    public function uploadDocument(UploadedFile $file, string $userId, string $type): string
    {
        $allowedMimes = [
            'application/pdf' => 'pdf',
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
        ];

        $mimeType = $file->getMimeType();
        $safeExtension = $allowedMimes[$mimeType] ?? $file->guessExtension() ?? 'bin';

        if (! array_key_exists($mimeType, $allowedMimes)) {
            throw new \InvalidArgumentException('Unsupported or unsafe file format.');
        }

        $filename = preg_replace('/[^a-zA-Z0-9_-]/', '', $type).'_'.$userId.'_'.time().'_'.\Illuminate\Support\Str::random(6).'.'.$safeExtension;
        $path = 'uploads/students/documents/'.date('Y/m');

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
