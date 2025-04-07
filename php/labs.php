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
        <link rel="stylesheet" href="../css/labs.css">
        <link rel="icon" href="../icono/logo.ico" type="image/x-icon">
    </head>
    <body>
        <header>
            <div id="inicio" onclick="window.location.href='logout.php';">
                <img src="../icono/logo.jpeg"/>
                <h3>HackForFun</h3>
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
                <a class="boton" href="machines/users-leaks.php">Hackeame</a>
            </div>
            <div class="top">
                <h4>Control</h4>
                <h5 class="easy">Easy</h5>
                <p></p>
                <a class="boton" href="machines/control.php">Hackeame</a>
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
        <footer>
            <div>
                <div id="logo" onclick="window.location.href='logout.php';">
                    <img src="../icono/logo.jpeg"/>
                    <h3>HackForFun</h3>
                </div>
            </div>
            <div>
                <h3>Aprendizaje</h3>
            </div>
            <div>
                <p></p>
            </div>
        </footer>
    </body>
</html>