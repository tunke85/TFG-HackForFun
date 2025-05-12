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
                        <li><a href="">Entorno de Trabajo</a></li><br>
                        <li><a href="../conceptos basicos/">Conceptos Básicos</a></li><br>
                        <li><a href="../herramientas usadas/">Herramientas usadas</a></li><br>
                    </ul>
                </li>           
            </ul>
        </div>
        <div class="docker">
            <h3>Entorno de trabajo</h3><br>
            <h4>1. Requisitos Previos</h4>
            <ul>
                <li><strong>Hardware mínimo recomendado:</strong>
                    <ul>
                        <li>Procesador: CPU de 2 núcleos (64-bit)</li>
                        <li>Memoria RAM: 4GB (8GB recomendado)</li>
                        <li>Almacenamiento: 32GB mínimo (SSD recomendado)</li>
                        <li>Tarjeta gráfica: Compatible con OpenGL</li>
                    </ul>
                </li>
                <li><strong>Preparación:</strong>
                    <ul>
                        <li>USB de al menos 8GB</li>
                        <li>Conexión a Internet estable</li>
                        <li>Software: Balena Etcher o Rufus</li>
                    </ul>
                </li>
            </ul>
            
            <h4>2. Descargar la Imagen ISO</h4>
            <ol>
                <li>Visita el sitio oficial: <a href="https://parrotsec.org/download/" target="_blank">parrotsec.org/download</a></li>
                <li>Elige la edición adecuada:
                    <ul>
                        <li><strong>Security Edition:</strong> Versión completa con todas las herramientas</li>
                        <li><strong>Home Edition:</strong> Versión ligera para uso diario</li>
                        <li><strong>Architect:</strong> Para instalación personalizada</li>
                    </ul>
                </li>
                <li>Descarga la imagen ISO (aproximadamente 4GB)</li>
                <li>Verifica la integridad con el checksum SHA256</li>
            </ol>
            
            <h4>3. Crear USB Booteable</h4>
            <ol>
                <li><strong>Windows:</strong>
                    <ol>
                        <li>Usa Rufus (<a href="https://rufus.ie/" target="_blank">rufus.ie</a>)</li>
                        <li>Selecciona tu USB</li>
                        <li>Elige la imagen ISO descargada</li>
                        <li>Formato: GPT para UEFI</li>
                        <li>Inicia el proceso (tarda ~10 minutos)</li>
                    </ol>
                </li>
                <li><strong>Linux/macOS:</strong>
                    <ol>
                        <li>Usa Balena Etcher (<a href="https://www.balena.io/etcher/" target="_blank">balena.io/etcher</a>)</li>
                        <li>Selecciona imagen ISO y unidad USB</li>
                        <li>Haz clic en "Flash!"</li>
                    </ol>
                </li>
            </ol>
            
            <h4>4. Instalación Paso a Paso</h4>
            <ol>
                <li><strong>Arrancar desde USB:</strong>
                    <ul>
                        <li>Reinicia el equipo</li>
                        <li>Accede a la BIOS (F2, F12, DEL o ESC según tu placa)</li>
                        <li>Habilita arranque UEFI y desactiva Secure Boot</li>
                        <li>Selecciona el USB como dispositivo de arranque</li>
                    </ul>
                </li>
                <li><strong>Menú de inicio:</strong>
                    <ul>
                        <li>Elige "Install" (modo gráfico)</li>
                        <li>O "Install (Text Mode)" para equipos antiguos</li>
                    </ul>
                </li>
                <li><strong>Configuración básica:</strong>
                    <ol>
                        <li>Idioma: Español</li>
                        <li>Zona horaria</li>
                        <li>Distribución de teclado</li>
                    </ol>
                </li>
                <li><strong>Particionado de disco:</strong>
                    <ul>
                        <li><strong>Opción simple:</strong> "Borrar disco e instalar Parrot"</li>
                        <li><strong>Avanzado:</strong> Configura manualmente:
                            <ul>
                                <li>/ (root): 30GB mínimo ext4</li>
                                <li>swap: Igual a tu RAM (opcional en equipos con 8GB+ RAM)</li>
                                <li>/home: Resto del espacio (para tus archivos)</li>
                                <li>/boot: 500MB (para el gestor de arranque)</li>
                            </ul>
                        </li>
                    </ul>
                </li>
                <li><strong>Usuario y contraseña:</strong>
                    <ul>
                        <li>Crea un usuario (evita usar "root" como nombre)</li>
                        <li>Contraseña segura (mínimo 12 caracteres)</li>
                    </ul>
                </li>
                <li><strong>Selección de software:</strong>
                    <ul>
                        <li>Elige "Parrot Security" para todas las herramientas</li>
                        <li>O "Parrot Home" para versión ligera</li>
                    </ul>
                </li>
                <li><strong>Instalación:</strong> Proceso automático (~20-40 minutos)</li>
                <li><strong>Reiniciar:</strong> Retira el USB cuando se indique</li>
            </ol>
            
            <h4>5. Primeras Configuraciones Post-Instalación</h4>
            <ol>
                <li><strong>Actualizaciones:</strong>
                    <pre><code>sudo parrot-upgrade</code></pre>
                </li>
                <li><strong>Drivers adicionales:</strong>
                    <pre><code>sudo apt install firmware-linux firmware-linux-nonfree</code></pre>
                </li>
                <li><strong>Configura red:</strong>
                    <ul>
                        <li>WiFi: Usa el menú de red en la barra superior</li>
                        <li>VPN: Configura OpenVPN o WireGuard si es necesario</li>
                    </ul>
                </li>
                <li><strong>Herramientas esenciales:</strong>
                    <pre><code>sudo apt install terminator keepassxc git</code></pre>
                </li>
            </ol>
            
            <h4>6. Recomendaciones para Entorno de Trabajo</h4>
            <ul>
                <li><strong>Personalización:</strong>
                    <ul>
                        <li>Configura atajos de teclado en Menú > Preferencias > Teclado</li>
                        <li>Instala temas y iconos desde "Parrot Tweaks"</li>
                    </ul>
                </li>
                <li><strong>Seguridad básica:</strong>
                    <ul>
                        <li>Configura firewall: <code>sudo ufw enable</code></li>
                        <li>Instala herramientas adicionales: <code>sudo apt install lynis rkhunter</code></li>
                    </ul>
                </li>
                <li><strong>Entornos virtuales:</strong>
                    <ul>
                        <li>VirtualBox: <code>sudo apt install virtualbox</code></li>
                        <li>Docker: <code>sudo apt install docker.io</code></li>
                    </ul>
                </li>
            </ul>
            
            <h4>7. Solución de Problemas Comunes</h4>
            <ul>
                <li><strong>No detecta Wi-Fi:</strong>
                    <pre><code>sudo apt install firmware-iwlwifi
        sudo modprobe -r iwlwifi
        sudo modprobe iwlwifi</code></pre>
                </li>
                <li><strong>Problemas con NVIDIA:</strong>
                    <pre><code>sudo apt install nvidia-driver</code></pre>
                </li>
                <li><strong>Error GRUB:</strong> Reparar con USB live y ejecutar:
                    <pre><code>sudo grub-install /dev/sda
        sudo update-grub</code></pre>
                </li>
            </ul>
        </div>
        <br>
        <center>
            <a class="boton" href="../../">Volver</a>
        </center>
        <br><br><br>
    </body>
</html>