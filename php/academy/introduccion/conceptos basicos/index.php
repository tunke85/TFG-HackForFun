<?php
    # Habilitar mensajes de error
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require '../../../conexion.php';

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
    <title>Academia | <?php echo "$user"; ?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta charset="UTF-8">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Roboto+Condensed:wght@300;400;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="../../../../css/academy.css">
        <link rel="icon" href="../../../../icono/logo_hack4fun_H_whiteblue.ico" type="image/x-icon">
    </head>
    <body>
        <header>
            <div id="inicio" onclick="window.location.href='../../../logout.php';">
                <img src="../../../../icono/logo_hack4fun_bluewhite.png"/>
            </div>
            <div id="panel" style="text-align: right;">
                <span>Bienvenido, <?php echo "$user"; ?></span>
                <a id="logout" href="../../../logout.php">Cerrar sesión</a>
            </div>
        </header>
        <div id="menu">
            <div id="inicio" onclick="window.location.href='../logout.php';">
                <img src="../../../../icono/logo_hack4fun_bluewhite.png"/>
            </div><br><br>
            <ul>
                <li><a href="../../../panel.php">Panel de control</a></li><br>
                <li><a href="../../">Academia</a><br><br>
                    <ul>
                        <li><a href="../entorno de trabajo/">Entorno de Trabajo</a></li><br>
                        <li><a href="">Conceptos Básicos</a></li><br>
                        <li><a href="../herramientas usadas/">Herramientas usadas</a></li><br>
                    </ul>
                </li>           
            </ul>
        </div>
        <div class="docker">
            <h3>Conceptos Básicos</h3>
            <p></p>
        </div>
    </body>
</html>