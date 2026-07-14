<?php
function compresserImage($path, $largeurMax) {
    // On ne compresse pas, on lit simplement le fichier binaire
    return file_get_contents($path);
}