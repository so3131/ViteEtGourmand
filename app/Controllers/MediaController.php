<?php

namespace App\Controllers;

class MediaController
{
    //function pour servir les images de plats depuis le dossier storage/uploads/plats
    public static function servePlatImage(\PDO $db, ?string $file): void
    {
        if (!$file || !preg_match('/^[a-zA-Z0-9\-]+\.(jpg|jpeg|png|webp)$/', $file)) {
            self::serveDefault();
            return;
        }

        $filePath = ROOT_PATH . '/storage/uploads/plats/' . $file;

        if (!file_exists($filePath)) {
            self::serveDefault();
            return;
        }

        $mimeTypes = [
            'jpg'  => 'image/jpeg',
            'png'  => 'image/png',
            'webp' => 'image/webp',
        ];
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);

        header('Content-Type: ' . $mimeTypes[$extension]);
        header('Content-Length: ' . filesize($filePath));
        header('Cache-Control: public, max-age=31536000, immutable');

        readfile($filePath);
        exit();
    }
    //function pour servir l'image par défaut si le fichier demandé n'existe pas
    private static function serveDefault(): void
    {
        $defaultPath = ROOT_PATH . '/public/assets/img/plats/default.webp';
        header('Content-Type: image/webp');
        header('Cache-Control: public, max-age=86400');
        readfile($defaultPath);
        exit();
    }
}