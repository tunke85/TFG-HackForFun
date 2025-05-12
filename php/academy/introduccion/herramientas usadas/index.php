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
                        <li><a href="../conceptos basicos/">Conceptos Básicos</a></li><br>
                        <li><a href="">Herramientas usadas</a></li><br>
                    </ul>
                </li>           
            </ul>
        </div>
        <div class="docker">
            <h3>Herramientas Usadas</h3>
            <h4>1. Herramientas de Análisis de Red</h4>
    
            <h5>Wireshark</h5>
            <p>Analizador de protocolos de red que permite ver todo el tráfico que pasa por tu conexión. Ideal para entender cómo se comunican los dispositivos en una red.</p>
            
            <h5>Nmap</h5>
            <p>Escáner de redes que descubre dispositivos conectados y servicios que están ejecutando. Perfecto para mapear redes y encontrar posibles puntos de entrada.</p>
            
            <h4>2. Herramientas de Pruebas de Vulnerabilidades</h4>
            
            <h5>Metasploit Framework</h5>
            <p>Plataforma para desarrollar y ejecutar exploits contra sistemas remotos. Usado tanto por hackers éticos como maliciosos para encontrar vulnerabilidades.</p>
            
            <h5>Burp Suite Community</h5>
            <p>Herramienta para probar la seguridad de aplicaciones web. Permite interceptar y modificar peticiones HTTP para encontrar fallos de seguridad.</p>
            
            <h4>3. Herramientas de Análisis de Malware</h4>
            
            <h5>VirusTotal</h5>
            <p>Servicio online que analiza archivos sospechosos con múltiples motores antivirus. Muy útil para verificar si un archivo es malicioso.</p>
            
            <h5>Process Hacker</h5>
            <p>Monitor avanzado de procesos para Windows que permite ver en detalle qué programas están ejecutándose y qué recursos están utilizando.</p>
            
            <h4>4. Herramientas de Criptografía</h4>
            
            <h5>GnuPG (GPG)</h5>
            <p>Implementación gratuita de PGP para cifrar y firmar digitalmente archivos y correos electrónicos. Esencial para proteger comunicaciones sensibles.</p>
            
            <h5>Hashcat</h5>
            <p>Herramienta avanzada para recuperación de contraseñas mediante fuerza bruta. Usada para probar la fortaleza de contraseñas.</p>
            
            <h4>5. Distribuciones Linux para Ciberseguridad</h4>
            
            <h5>Kali Linux</h5>
            <p>Sistema operativo especializado que incluye cientos de herramientas de seguridad preinstaladas. La favorita de muchos profesionales y estudiantes.</p>
            
            <h5>Parrot OS</h5>
            <p>Alternativa a Kali Linux con enfoque en privacidad, desarrollo y pruebas de penetración. Más ligera y con mejor soporte para entornos de escritorio.</p>
            
            <h4>6. Herramientas para Seguridad en la Nube</h4>
            
            <h5>Prowler</h5>
            <p>Herramienta para evaluar la seguridad de entornos AWS contra estándares de seguridad como CIS Benchmark.</p>
            
            <h5>CloudSploit</h5>
            <p>Escáner de seguridad para detectar configuraciones erróneas y riesgos en entornos de AWS, Azure y Google Cloud.</p>
            
            <h4>7. Herramientas para Análisis Forense</h4>
            
            <h5>Autopsy</h5>
            <p>Interfaz gráfica para The Sleuth Kit, permite analizar discos duros en busca de evidencias digitales. Ideal para principiantes en forense digital.</p>
            
            <h5>FTK Imager</h5>
            <p>Herramienta para crear imágenes forenses de dispositivos de almacenamiento sin alterar la evidencia original.</p>
            
            <h4>8. Entornos de Práctica</h4>
            
            <h5>TryHackMe</h5>
            <p>Plataforma online con máquinas virtuales y retos guiados para aprender ciberseguridad de forma práctica.</p>
            
            <h5>Hack The Box</h5>
            <p>Plataforma con máquinas vulnerables para practicar habilidades de hacking ético. Tiene opciones gratuitas y de pago.</p>
            
            <h4>9. Herramientas para Seguridad en Redes Wi-Fi</h4>
            
            <h5>Aircrack-ng</h5>
            <p>Suite de herramientas para evaluar la seguridad de redes Wi-Fi, incluyendo capacidades para recuperación de contraseñas WEP/WPA.</p>
            
            <h5>Wifite</h5>
            <p>Herramienta automatizada para probar la seguridad de redes inalámbricas, ideal para principiantes en seguridad Wi-Fi.</p>
            
            <h4>10. Gestores de Contraseñas</h4>
            
            <h5>Bitwarden</h5>
            <p>Gestor de contraseñas de código abierto con versiones gratuitas y premium. Almacena de forma segura todas tus credenciales.</p>
            
            <h5>KeePass</h5>
            <p>Gestor de contraseñas local que almacena tus credenciales en una base de datos cifrada. No requiere conexión a internet.</p>
        </div>
        <br>
        <center>
            <a class="boton" href="../../">Volver</a>
        </center>
        <br><br><br>
    </body>
</html>