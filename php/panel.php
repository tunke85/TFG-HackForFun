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
        <title>Panel de Usuario | <?php echo "$user"; ?></title>
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
        <div id="docker">
            <p id="welcome">
                <b style="font-size: x-large;">Bienvenido <?php echo "$user"; ?>, </b><br>
                Adentrate en el mundo de la ciberseguridad y mejora activamente en tus técnicas.
            </p>
            <div id="academybox">
                <h2>HackForFun Academy</h2>
                <p>
                    Empieza o mejora tu formación en tácticas de Red Team y Blue Team.<br><br>
                    En HackForFun Academy, podrás aprender a través de nuestros learning paht y conseguir certificados profesionales.
                </p>
                <a href="academy.php" class="boton">Empieza a aprender</a>
            </div>
            <div id="labbox">
                <h2>HackForFun Labs</h2>
                <p>
                    Mejora tus técnicas de intrusión y protección en nuestrás máquinas de Capture The Flag.<br><br>
                    En HackForFun Labs, podrás mejorar tus técnicas de "pentesting" haciendo uso de writeups y mejorar las técnicas defensivas con nuestro modelo inovador de aprendizaje.
                </p>
                <a href="labs.php" class="boton">Empieza a aprender</a>
            </div>
        </div>
    </body>
</html>