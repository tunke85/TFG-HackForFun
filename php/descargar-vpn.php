<?php
if (isset($_GET['usuario'])) {
    $usuario = htmlspecialchars($_GET['usuario']);
    $nombre_archivo = "clienteCFT-" . $usuario . ".ovpn";

    $file = '/var/www/vpn-configs/cliente1.ovpn';

    if (file_exists($file)) {
        // Configura las cabeceras para forzar la descarga con el nombre personalizado
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $nombre_archivo . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        readfile($file);
        exit;
    } else {
        echo "El archivo de configuración no está disponible.";
    }
} else {
    
}
?>