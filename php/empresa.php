<?php
    # Habilitar mensajes de error
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require 'conexion.php';

    $error = "";

    $nombre = $_GET['nombre'];
?>

<!DOCTYPE html>
<html>
    <head>
        <title>HackForFun | Empresas</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta charset="UTF-8">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Roboto+Condensed:wght@300;400;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="../css/style.css">
        <link rel="icon" href="../icono/logo_hack4fun_H_whiteblue.ico" type="image/x-icon">
    </head>
    <body>
        <nav>
            <div id="inicio" onclick="window.location.href='../index.html';">
                <img src="../icono/logo_hack4fun_bluewhite.png"/>
            </div>
            <div id="nav">
                <a href="../html/aprendizaje.html">Aprendizaje</a>
                <a href="../html/servicios.html">Servicios</a>
                <a href="../html/precios.html">Precios</a>
                <a href="../html/contacto.html">Contacto</a>
            </div>
            <div id="botones">
                <a id="empresas" href="register-empresas.php">Empresas</a>
                <a class="boton" href="register.php">Registrarse</a>
                <a class="boton" href="login.php">Iniciar sesión</a>
            </div>
        </nav>
        <div class="docker">
            <h3>Muchas gracias <?php echo "$nombre";?></h3>
            <p>
                Muchas gracias <?php echo "$nombre";?> por acceder al registro previo para comenzar a hacer uso de nuestros servicios.
                Actualmente, estamos evaluando nuestro impacto en las empresas y a lo largo de unos días, nos comunicaremos con vosotros vía email.
                Un saludo y ante cualquier cosa escriba a: alejandro.blanco.cebollero@gmail.com. 
            </p>
        </div>
        <footer>
            <div>
                <div id="logo" onclick="window.location.href='../index.html';">
                    <img src="../icono/logo_hack4fun_bluewhite.png"/>
                </div>
            </div>
            <div id="menu-footer">
                <h3><a href="../html/aprendizaje.html">Aprendizaje</a></h3><br>
                <h3><a href="../html/servicios.html">Servicios</a></h3><br>
                <h3><a href="../html/precios.html">Precios</a></h3><br>
                <h3><a href="../html/contacto.html">Contacto</a></h3><br>
                <h3><a href="register-empresas.php">Empresas</a></h3><br>
            </div>
            <div id="info-footer">
                <h3><a href="../legalidad/PrivacidadYDatos.html">Política de Privacidad y Datos</a></h3><br>
                <h3><a href="../legalidad/TerminosYCondiciones.html">Términos y condiciones</a></h3><br>
                <h3>HackForFun S.L.</h3>
                <h4>Calle Francisco Bores, 3</h4>
                <h4>Trabajo Final de Grado Superior de ASIR</h4>
                <h4>IES Calderón de la Barca, Pinto</h4>
            </div>
        </footer>
    </body>
</html>
