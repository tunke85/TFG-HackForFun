<?php
    # Habilitar mensajes de error
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require '../conexion.php';

    $error = "";
    $erroruser = "";
    $errorroot = "";
    $correcto = '';

    session_start();
    if (!isset($_SESSION['id'])) {
        header("Location: http://localhost/");
    }

    $userSession = explode(' ', $_SESSION['id']);
    $select = $conexion->execute_query("SELECT username FROM users WHERE '$userSession[0]' = username OR '$userSession[0]' = email;");
    $userEmail = $select->fetch_assoc();
    $user = $userEmail['username'];

    $selectmach = $conexion->execute_query("SELECT name FROM machines WHERE name = 'behind-the-web';");
    $machineName = $selectmach->fetch_assoc();

    $selectmachid = $conexion->execute_query("SELECT machineid FROM machines WHERE name = 'behind-the-web';");
    $serverId = $selectmachid->fetch_assoc();

    $serverConfig = [
        'machineName' => $machineName['name'],
        'serverId' => $serverId['machineid'] // Podrías obtener esto de tu base de datos
    ];

    echo '<script>window.serverConfig = ' . json_encode($serverConfig) . ';</script>';

    $selectuser = $conexion->execute_query("SELECT userflag FROM machines WHERE name = 'behind-the-web';");
    $userflag = $selectuser->fetch_assoc();

    $selectroot = $conexion->execute_query("SELECT rootflag FROM machines WHERE name = 'behind-the-web';");
    $rootflag = $selectroot->fetch_assoc();

    $correctouser = '';
    $correctoroot = '';

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $root = $_POST['root'];
        $user = $_POST['user'];

        if ( empty($root) || empty($user) ){ # Validando campos vacíos
            $error = "Los dos campos están vacios.";
        } elseif (!empty($root)){
            if ($root == $rootflag) {
                $correctoroot = "root";
            } elseif ($root != $rootflag) {
                $correctoroot = "noroot";
            }
        } elseif (!empty($user)){
            if ($user == $userflag) {
                $correctouser = "user";
            } elseif ($user != $userflag) {
                $correctouser = "nouser";
            }
        }else {
            if ($root == $rootflag) {
                $correctoroot = "root";
            } elseif ($root != $rootflag) {
                $correctoroot = "noroot";
            } elseif ($user == $userflag) {
                $correctouser = "user";
            } elseif ($user != $userflag) {
                $correctouser = "nouser";
            }
        }
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Laboratorio | <?php echo htmlspecialchars($user); ?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta charset="UTF-8">
        <link rel="stylesheet" href="../../css/behind-the-web.css">
        <link rel="icon" href="../../icono/logo.ico" type="image/x-icon">
    </head>
    <body>
        <header>
            <div id="inicio" onclick="window.location.href='../logout.php';">
                <img src="../../icono/logo.jpeg"/>
                <h3>HackForFun</h3>
            </div>
            <div id="panel" style="text-align: right;">
                <span>Bienvenido, <?php echo htmlspecialchars($user); ?></span>
                <a id="logout" href="../logout.php">Cerrar sesión</a>
            </div>
        </header>
        <div class="docker">
            <h3>Behind The Web</h3>
            <p>
                Esta máquina está creada con el objetivo de mejorar y/o aprender de técnicas de hacking web.
                Certificados: <b>eJPT</b> y <b>eWPT</b><br><br>
            </p>
            <div id="server-controls">
                <!-- Botón Principal -->
                <button id="mainActionBtn" class="btn btn-primary">
                    <span id="actionSpinner" class="spinner"></span>
                    <span id="actionText">Cargando...</span>
                </button>
                
                <!-- Botón Reiniciar -->
                <button id="rebootBtn" class="btn btn-warning" style="margin-left: 10px;">
                    <span id="rebootSpinner" class="spinner"></span>
                    Reiniciar
                </button>
            </div>

            <div id="serverStatus" style="margin-top: 15px; padding: 10px; background: #f8f9fa; border-radius: 4px;">
                Estado: Verificando...
            </div>
            <script>
                // Estado global del servidor
                let serverState = {
                    status: null,
                    ip: null
                };

                // Configuración
                const currentMachine = window.serverConfig.machineName;
                const serverId = window.serverConfig.serverId; // Si necesitas enviarlo también
                const API_BASE_URL = 'http://localhost:3000';

                // Elementos del DOM
                const mainBtn = document.getElementById('mainActionBtn');
                const rebootBtn = document.getElementById('rebootBtn');
                const statusDiv = document.getElementById('serverStatus');
                const actionSpinner = document.getElementById('actionSpinner');
                const actionText = document.getElementById('actionText');
                const rebootSpinner = document.getElementById('rebootSpinner');

                // Inicialización
                function init() {
                    // Configurar spinners
                    actionSpinner.style.display = 'none';
                    rebootSpinner.style.display = 'none';
                    
                    // Asignar eventos
                    mainBtn.addEventListener('click', handleMainAction);
                    rebootBtn.addEventListener('click', handleRebootAction);
                    
                    // Verificar estado inicial
                    checkServerStatus();
                    
                    // Verificar estado periódicamente (cada 30 segundos)
                    setInterval(checkServerStatus, 30000);
                }

                // Manejador del botón principal
                async function handleMainAction() {
                    const action = serverState.status === 'Running' ? 'stop' : 'start';
                    await handleAction(action);
                }

                // Manejador del botón de reinicio
                async function handleRebootAction() {
                    if (confirm('¿Estás seguro de reiniciar la máquina? Esto puede tomar unos minutos.')) {
                        await handleAction('reboot');
                    }
                }

                // Función para manejar cualquier acción
                async function handleAction(action) {
                    const isReboot = action === 'reboot';
                    const spinner = isReboot ? rebootSpinner : actionSpinner;
                    const btn = isReboot ? rebootBtn : mainBtn;
                    
                    btn.disabled = true;
                    spinner.style.display = 'inline-block';
                    statusDiv.style.color = 'inherit';
                    statusDiv.innerHTML = `Procesando ${getActionName(action)}...`;
                    
                    try {
                        const response = await fetch(`${API_BASE_URL}/server-action`, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ action, machine: currentMachine })
                        });
                        
                        if (!response.ok) {
                            throw new Error(`Error HTTP: ${response.status}`);
                        }
                        
                        const data = await response.json();
                        
                        if (!data.success) {
                            throw new Error(data.error || 'Error en la respuesta del servidor');
                        }
                        
                        // Esperar cambio de estado
                        await waitForStatusChange(action);
                        
                    } catch (error) {
                        console.error('Error:', error);
                        statusDiv.innerHTML = `❌ Error: ${error.message}`;
                        statusDiv.style.color = 'red';
                    } finally {
                        btn.disabled = false;
                        spinner.style.display = 'none';
                        await checkServerStatus();
                    }
                }

                // Obtener nombre descriptivo de la acción
                function getActionName(action) {
                    const actions = {
                        'start': 'inicio',
                        'stop': 'detención',
                        'reboot': 'reinicio'
                    };
                    return actions[action] || action;
                }

                // Esperar cambio de estado del servidor
                async function waitForStatusChange(action) {
                    const targetStatus = action === 'start' ? 'Running' : 
                                    action === 'stop' ? 'Stopped' : 'Running';
                    
                    let attempts = 0;
                    const maxAttempts = 30; // 30 segundos máximo de espera
                    
                    while (attempts < maxAttempts) {
                        await new Promise(resolve => setTimeout(resolve, 1000));
                        await checkServerStatus();
                        
                        if (serverState.status === targetStatus) {
                            return;
                        }
                        
                        attempts++;
                        statusDiv.innerHTML = `Procesando... (${attempts}s)`;
                    }
                    
                    throw new Error('La acción está tardando más de lo esperado');
                }

                // Verificar estado del servidor
                async function checkServerStatus() {
                    try {
                        const response = await fetch(`${API_BASE_URL}/server-status?machine=${currentMachine}`);
                        
                        if (!response.ok) {
                            throw new Error(`Error HTTP: ${response.status}`);
                        }
                        
                        const data = await response.json();
                        
                        if (data.success) {
                            serverState = {
                                status: data.status,
                                ip: data.ip
                            };
                            updateUI();
                        } else {
                            throw new Error(data.error || 'Error en la respuesta del servidor');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        statusDiv.innerHTML = '⚠️ Error al conectar con el servicio de control';
                        statusDiv.style.color = 'orange';
                        actionText.textContent = 'Error';
                        
                        // Reintentar después de 5 segundos
                        setTimeout(checkServerStatus, 5000);
                    }
                }

                // Actualizar la interfaz de usuario
                function updateUI() {
                    // Actualizar botón principal
                    if (serverState.status === 'Running') {
                        actionText.textContent = 'Detener Máquina';
                        mainBtn.classList.remove('btn-primary');
                        mainBtn.classList.add('btn-danger');
                        rebootBtn.disabled = false;
                    } else if (serverState.status === 'Stopped') {
                        actionText.textContent = 'Iniciar Máquina';
                        mainBtn.classList.remove('btn-danger');
                        mainBtn.classList.add('btn-primary');
                        rebootBtn.disabled = true;
                    }
                    
                    // Actualizar estado
                    statusDiv.innerHTML = serverState.ip 
                        ? `Estado: <strong>${serverState.status}</strong> (IP: ${serverState.ip})`
                        : `Estado: <strong>${serverState.status || 'Desconocido'}</strong>`;
                    
                    statusDiv.style.color = serverState.status === 'Running' ? 'green' : 
                                        serverState.status === 'Stopped' ? 'red' : 'inherit';
                    
                    mainBtn.disabled = false;
                }

                // Inicializar cuando el DOM esté listo
                document.addEventListener('DOMContentLoaded', init);
            </script>
            <p>
                <a href="behind-the-web/ofensive.pdf">Solución Ofensiva</a> | <a href="behind-the-web/defensive.pdf">Solución Defensiva</a>
            </p>
        </div>
        <div id="vpn">
            <h3>Descarga de VPN</h3>
            <p>Descarga la vpn para poder realizar la máquina del laboratorio<br><br><br></p>
            <a href="../descargar-vpn.php?usuario=<?php echo htmlspecialchars($user);?>" class="boton">Descargar</a>
            <br><br>
        </div>
        <div class="docker">
            <?php if (!empty($error)): ?>
                <div style="color: red;"><?php echo $error; ?></div> 
            <?php endif; ?> <br>
            <form action="" method="post">
                User flag:<br>
                <?php if (!empty($erroruser)): ?>
                    <div style="color: red; padding: 2px 2px 2px 2px;"><?php echo $erroruser; ?></div> 
                <?php endif; ?> 
                <input type="text" name="user" id="user" <?php if ($correctouser == 'Correcto') echo  "readonly value='Flag correcta' style='color: green;';";?>>
                <input type="submit" value="Comprobar" class="boton" <?php if ($correcto == 'Correcto') echo "disabled";?>>
                <br><br>
                Root flag:<br>
                <?php if (!empty($errorroot)): ?>
                    <div style="color: red; padding: 2px 2px 2px 2px;"><?php echo $errorroot; ?></div> 
                <?php endif; ?> 
                <input type="text" name="root" id="root" <?php if ($correctoroot == 'Correcto') echo  "readonly value='Flag correcta' style='color: green;';";?>>
                <input type="submit" value="Comprobar" class="boton">
                <br>
            </form>
        </div>
        <footer>
            <div>
                <div id="logo" onclick="window.location.href='../logout.php';">
                    <img src="../../icono/logo.jpeg"/>
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