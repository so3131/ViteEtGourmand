<?php
require_once __DIR__ . '/../app/config/Database.php';
require_once __DIR__ . '/../app/config/constants.php';require_once __DIR__ . '/../app/helpers/ImageHelper.php';

$db = (new Database())->connect();

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    $stmt = $db->prepare("SELECT photo FROM vg_plat WHERE plat_id = :id");
    $stmt->execute(['id' => $id]);
    $plat = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($plat && !empty($plat['photo'])) {
        // Détecte l'extension du fichier pour déterminer le type MIME
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->buffer($plat['photo']);
        
        header("Content-Type: " . $mimeType);
        header("Cache-Control: max-age=2592000"); // Mis en cache ~ 30 jours
        echo $plat['photo'];
        exit; 
    }
}


header("HTTP/1.0 404 Not Found");
echo "Image non trouvée.";
exit;