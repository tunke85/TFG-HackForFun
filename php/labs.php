<?php
    # Habilitar mensajes de error
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require 'conexion.php';

    $error = "";

    session_start();
    if (!isset($_SESSION['id'])) {
        header("Location: http://localhost/");
    }

    $userSession = explode(' ', $_SESSION['id']);
    $select = $conexion->execute_query("SELECT username FROM users WHERE '$userSession[0]' = username OR '$userSession[0]' = email");
    $userEmail = $select->fetch_assoc();
    $user= $userEmail['username']

?>

<!DOCTYPE html>
<html>
    <head>
        <title>Laboratorio | <?php echo "$user"; ?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta charset="UTF-8">
        <link rel="stylesheet" href="css/panel.css">
    </head>
    <body>
        <header>
            <div style="text-align: right;">
                <span>Bienvenido, <?php echo "$user"; ?></span>
                <a id="logout" href="logout.php">Cerrar sesión</a>
            </div>
        </header>
        
    </body>
</html>