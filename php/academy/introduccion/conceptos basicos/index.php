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
            <h4>1. ¿Qué es la Ciberseguridad?</h4>
            <p>La ciberseguridad es como una seguridad digital que protege computadoras, redes, teléfonos y datos de hackers, virus y otros peligros en internet. Su objetivo es:</p>
            <ul>
                <li>Evitar robos de información (como contraseñas o datos bancarios).</li>
                <li>Proteger sistemas para que no sean hackeados o dañados.</li>
                <li>Garantizar que los servicios en línea (como bancos o redes sociales) estén siempre disponibles.</li>
            </ul>

            <h4>2. Los 3 Pilares de la Seguridad (CIA)</h4>
            
            <h5>Confidencialidad</h5>
            <p>"Que solo los autorizados lean la información"</p>
            <p>Ejemplo: Tu correo electrónico debe estar protegido con contraseña para que solo tú puedas leerlo.</p>
            <p>¿Cómo se protege? Con contraseñas fuertes, cifrado y permisos de acceso.</p>
            
            <h5>Integridad</h5>
            <p>"Que nadie modifique los datos sin permiso"</p>
            <p>Ejemplo: Si alguien cambia tu saldo bancario sin tu consentimiento, se pierde la integridad.</p>
            <p>¿Cómo se protege? Con firmas digitales y sistemas que detectan cambios no autorizados.</p>
            
            <h5>Disponibilidad</h5>
            <p>"Que los sistemas funcionen cuando los necesites"</p>
            <p>Ejemplo: Si un hacker tumba la página de tu banco y no puedes entrar, falla la disponibilidad.</p>
            <p>¿Cómo se protege? Con copias de seguridad (backups) y protección contra ataques DDoS.</p>

            <h4>3. Amenazas más Comunes</h4>
            
            <h5>Malware (Software Malicioso)</h5>
            <p>Programas dañinos que infectan dispositivos.</p>
            <ul>
                <li>Virus: Se pega a archivos legítimos y se propaga (como un virus real).</li>
                <li>Ransomware: Bloquea tu computadora y pide dinero para liberarla.</li>
                <li>Spyware: Espía lo que haces (como tus contraseñas).</li>
            </ul>
            
            <h5>Phishing (Suplantación de Identidad)</h5>
            <p>Engaña a las personas para robarles datos.</p>
            <p>Ejemplo: Recibes un correo falso de "tu banco" pidiendo que ingreses tu contraseña.</p>
            <p>Cómo reconocerlo: Errores ortográficos, enlaces sospechosos, urgencia ("¡actúa ya!").</p>
            
            <h5>Ataque DDoS (Ataque de Negación de Servicio)</h5>
            <p>"Tumbar un sitio web saturándolo con tráfico falso"</p>
            <p>Ejemplo: Imagina que miles de robots visitan una tienda en línea a la vez, haciendo que nadie más pueda entrar.</p>
            <p>Objetivo: Dejar servicios inaccesibles (ej. páginas de bancos, juegos online).</p>
            
            <h5>Ingeniería Social (Manipulación Psicológica)</h5>
            <p>Hackear personas en lugar de computadoras.</p>
            <p>Ejemplo 1: Alguien te llama fingiendo ser de "soporte técnico" para que le des acceso a tu PC.</p>
            <p>Ejemplo 2: Un USB "perdido" infectado que alguien conecta por curiosidad.</p>

            <h4>4. ¿Cómo Protegerse? Medidas Básicas</h4>
            
            <h5>Contraseñas Seguras</h5>
            <ul>
                <li>Usa mínimo 12 caracteres con letras, números y símbolos (ej. Café$Seguro123).</li>
                <li>No reutilices contraseñas (si hackean una, no caerán todas).</li>
                <li>Usa un gestor de contraseñas como Bitwarden o LastPass.</li>
            </ul>
            
            <h5>Antivirus y Actualizaciones</h5>
            <ul>
                <li>Instala un antivirus (Windows Defender, Avast, Bitdefender).</li>
                <li>Actualiza siempre tu sistema y apps (las actualizaciones arreglan agujeros de seguridad).</li>
            </ul>
            
            <h5>Wi-Fi Seguro</h5>
            <ul>
                <li>No uses redes públicas para bancos o compras (pueden espiarte).</li>
                <li>En casa, usa WPA3 (el cifrado más seguro para Wi-Fi).</li>
            </ul>
            
            <h5>Copias de Seguridad (Backup)</h5>
            <ul>
                <li>Guarda copias de tus archivos importantes en la nube (Google Drive, iCloud) y en un disco externo.</li>
                <li>Así si un ransomware cifra tu PC, podrás recuperar tus datos.</li>
            </ul>

            <h4>5. ¿Qué Hacer si Te Hackean?</h4>
            <ol>
                <li>Desconéctate de internet (para evitar más daños).</li>
                <li>Cambia todas tus contraseñas desde otro dispositivo seguro.</li>
                <li>Escanea tu PC con antivirus.</li>
                <li>Reporta el incidente (al banco, plataforma afectada o autoridades si es grave).</li>
            </ol>

            <h4>Resumen Final</h4>
            <ul>
                <li>Ciberseguridad = Proteger sistemas y datos de ataques.</li>
                <li>Triada CIA: Confidencialidad (solo accesible para autorizados), Integridad (datos sin cambios no autorizados), Disponibilidad (que todo funcione cuando se necesite).</li>
                <li>Amenazas principales: Malware, phishing, DDoS e ingeniería social.</li>
                <li>Protección básica: Contraseñas fuertes, antivirus, actualizaciones y backups.</li>
            </ul>
        </div>
        <br>
        <center>
            <a class="boton" href="../../">Volver</a>
        </center>
        <br><br><br>
    </body>
</html>