<?php
    # Habilitar mensajes de error
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require '../conexion.php';

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
        <title>Cooming Soon</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta charset="UTF-8">
        <link rel="stylesheet" href="../css/cooming-soon.css">
    </head>
    <body>
        <header>
            <div id="inicio" onclick="window.location.href='../logout.php';">
                <img src="../imagenes/logo.jpeg"/>
                <h3>HackForFun</h3>
            </div>
            <div id="panel" style="text-align: right;">
                <span>Bienvenido, <?php echo "$user"; ?></span>
                <a id="logout" href="../logout.php">Cerrar sesión</a>
            </div>
        </header>
        <div id="docker">
            <h2>Comming Soon...</h2>
            <p>
                Sí te gusta la idea de este proyecto de trabajo de final de grado <br>
                puedes apoyar este proyecto con pequeñas donaciones o ayudando directamente en este proyecto.<br><br>
                Cualquier apoyo o contribución puedes escribir a <a href="mailto:alejandro.blanco.cebollero@gmail.com">alejandro.blanco.cebollero@gmail.com</a>.<br><br>
            </p>
            <a class="boton" href="../labs.php">Volver</a>
        </div>
        <footer>
            <div>
                <div id="logo" onclick="window.location.href='../logout.php';">
                    <img src="../imagenes/logo.jpeg"/>
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