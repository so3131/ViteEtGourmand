<?php
// ImageHelper - Gestion des images

class ImageHelper
{
    const UPLOAD_DIR = ROOT_PATH . '/public/assets/images/uploads/';
    const MAX_SIZE = 5 * 1024 * 1024; // 5MB
    const ALLOWED_TYPES = ['image/jpeg', 'image/png', 'image/gif'];

    /**
     * Uploader une image
     */
    public static function uploadImage($file)
    {
        if (!isset($file) || !isset($file['tmp_name'])) {
            return false;
        }

        if ($file['size'] > self::MAX_SIZE) {
            return false;
        }

        if (!in_array($file['type'], self::ALLOWED_TYPES)) {
            return false;
        }

        if (!is_dir(self::UPLOAD_DIR)) {
            mkdir(self::UPLOAD_DIR, 0755, true);
        }

        $filename = uniqid() . '_' . basename($file['name']);
        $filepath = self::UPLOAD_DIR . $filename;

        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            return 'assets/images/uploads/' . $filename;
        }

        return false;
    }

    /**
     * Supprimer une image
     */
    public static function deleteImage($imagePath)
    {
        $fullPath = ROOT_PATH . '/public/' . $imagePath;
        if (file_exists($fullPath)) {
            return unlink($fullPath);
        }
        return false;
    }
}
