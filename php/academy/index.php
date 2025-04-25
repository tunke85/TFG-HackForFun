<?php
    # Habilitar mensajes de error
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require '../conexion.php';

    $error = "";

    session_start();
    if (!isset($_SESSION['id'])) {
        header("Location: https://hackforfun.io/");
    }

    $userSession = explode(' ', $_SESSION['id']);
    $select = $conexion->execute_query("SELECT username FROM users WHERE '$userSession[0]' = username OR '$userSession[0]' = email");
    $userEmail = $select->fetch_assoc();
    $user= $userEmail['username']
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
        <link rel="stylesheet" href="../../css/academy.css">
        <link rel="icon" href="../../icono/logo_hack4fun_H_whiteblue.ico" type="image/x-icon">
    </head>
    <body>
        <header>
            <div id="inicio" onclick="window.location.href='../logout.php';">
                <img src="../../icono/logo_hack4fun_bluewhite.png"/>
            </div>
            <div id="panel" style="text-align: right;">
                <span>Bienvenido, <?php echo "$user"; ?></span>
                <a id="logout" href="logout.php">Cerrar sesión</a>
            </div>
        </header>
        <div id="menu">
            <div id="inicio" onclick="window.location.href='../logout.php';">
                <img src="../../icono/logo_hack4fun_bluewhite.png"/>
            </div><br><br>
            <ul>
                <li><a href="../panel.php">Panel de control</a></li><br>
                <li><a href="">Academia</a><br><br>
                    <ul>
                        <li><a href="entorno.php">Entorno de Trabajo</a></li><br>
                        <li><a href="conceptos.php">Conceptos Básicos</a></li><br>
                        <li><a href="herramientas.php">Herramientas usadas</a></li><br>
                    </ul>
                </li>           
            </ul>
        </div>
        <div class="docker">
            <p id="welcome">
                <b style="font-size: x-large;">Bienvenido <?php echo "$user"; ?>, </b><br>
                En la academia aprenderas los conceptos de la ciberseguridad.
            </p>
            <div class="desplegable-container">
                <!-- Desplegable 1 -->
                <div class="desplegable-item">
                    <div class="desplegable-titulo">
                        Introducción a la Ciberseguridad
                        <span class="desplegable-icono">▼</span>
                    </div>
                    <div class="desplegable-contenido">
                        <ul>
                            <li><a href="entorno.php">Entorno de Trabajo</a></li><br>
                            <li><a href="conceptos.php">Conceptos Básicos</a></li><br>
                            <li><a href="herramientas.php">Herramientas usadas</a></li>
                        </ul>
                    </div>
                </div>
                <!-- Desplegable 2 -->
                <div class="desplegable-item">
                    <div class="desplegable-titulo">
                        Hacking introductorio -> (eJPT)
                        <span class="desplegable-icono">▼</span>
                    </div>
                    <div class="desplegable-contenido">
                        Cooming Soon...
                    </div>
                </div>
                <!-- Desplegable 3 -->
                <div class="desplegable-item">
                    <div class="desplegable-titulo">
                        Hacking Web -> (eWPT)
                        <span class="desplegable-icono">▼</span>
                    </div>
                    <div class="desplegable-contenido">
                        Cooming Soon...
                    </div>
                </div>
            </div>
        </div>
        <script>
            document.querySelectorAll('.desplegable-titulo').forEach(titulo => {
                titulo.addEventListener('click', () => {
                    const item = titulo.parentElement;
                    const contenido = titulo.nextElementSibling;

                    // Cierra otros desplegables abiertos (opcional)
                    document.querySelectorAll('.desplegable-item.abierto').forEach(abierto => {
                        if (abierto !== item) {
                            abierto.classList.remove('abierto');
                        }
                    });

                    // Abre/cierra el actual
                    item.classList.toggle('abierto');
                });
            });
        </script>       
    </body>
</html>