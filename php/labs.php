<?php
    # Habilitar mensajes de error
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require 'conexion.php';

    $error = "";

    session_start();
    if (!isset($_SESSION['id'])) {
        header("Location: https://hackforfun.io/");
    }

    $stmt = $conexion->prepare("SELECT username FROM users WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['id']);
    $stmt->execute();
    $result = $stmt->get_result();

    $userData = $result->fetch_assoc();
    $user = $userData['username'];

?>

<!DOCTYPE html>
<html>
    <head>
        <title>Laboratorio | <?php echo "$user"; ?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta charset="UTF-8">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Roboto+Condensed:wght@300;400;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="../css/labs.css">
        <link rel="icon" href="../icono/logo_hack4fun_H_whiteblue.ico" type="image/x-icon">
    </head>
    <body>
        <header>
            <div id="inicio" onclick="window.location.href='logout.php';">
                <img src="../icono/logo_hack4fun_bluewhite.png"/>
            </div>
            <div id="panel" style="text-align: right;">
                <span>Bienvenido, <?php echo "$user"; ?></span>
                <a id="logout" href="logout.php">Cerrar sesión</a>
            </div>
        </header>
        <div id="easy">
            <div class="top">
                <h4>Behind The Web</h4>
                <h5 class="easy">Easy</h5>
                <p></p>
                <a class="boton" href="machines/behind-the-web.php">Hackeame</a>
            </div>
            <div class="top">
                <h4>Users Leak</h4>
                <h5 class="easy">Easy</h5>
                <p></p>
                <a class="boton" href="machines/cooming-soon.php">Hackeame</a>
            </div>
            <div class="top">
                <h4>Control</h4>
                <h5 class="easy">Easy</h5>
                <p></p>
                <a class="boton" href="machines/cooming-soon.php">Hackeame</a>
            </div>
        </div>
        <div id="rest">
            <div class="bot">
                <h4>Villain</h4>
                <h5 class="medium">Medium</h5>
                <p></p>
                <a class="boton" href="machines/cooming-soon.php">Hackeame</a>
            </div>
            <div class="bot">
                <h4>Guardian Angel</h4>
                <h5 class="medium">Medium</h5>
                <p></p>
                <a class="boton" href="machines/cooming-soon.php">Hackeame</a>
            </div>
            <div class="bot">
                <h4>Kerberos</h4>
                <h5 class="hard">Hard</h5>
                <p></p>
                <a class="boton" href="machines/cooming-soon.php">Hackeame</a>
            </div>
        </div>
    </body>
</html>