<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
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
        $encoded = $manager->read($file->getPathname())
            ->scaleDown(400, 500)
            ->toJpeg(80);

        Storage::disk('public')->put($path.'/'.$filename, $encoded->toString());

        return Storage::url($path.'/'.$filename);
    }

    /**
     * Upload document (CNIC, certificates, etc.)
     */
    public function uploadDocument(UploadedFile $file, string $userId, string $type): string
    {
        $filename = $type.'_'.$userId.'_'.time().'.'.$file->getClientOriginalExtension();
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
     * Validate file size and type
     */
    public function validateFile(UploadedFile $file, array $allowedTypes, int $maxSizeKB = 5120): array
    {
        $errors = [];

        // Check file size
        if ($file->getSize() > ($maxSizeKB * 1024)) {
            $errors[] = 'File size must not exceed '.($maxSizeKB / 1024).'MB';
        }

        // Check file type
        $extension = strtolower($file->getClientOriginalExtension());
        if (! in_array($extension, $allowedTypes)) {
            $errors[] = 'File type must be one of: '.implode(', ', $allowedTypes);
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
