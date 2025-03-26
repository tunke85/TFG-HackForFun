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
    $user = $userEmail['username'];

    $currentMachine = 'gV8ql2YxqL9n91mP';
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Laboratorio | <?php echo htmlspecialchars($user); ?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta charset="UTF-8">
        <link rel="stylesheet" href="../../css/behind-the-web.css">
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
                const currentMachine = 'behind-the-web';
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
            <p>Descarga la vpn para poder realizar la máquina del laboratorio</p>
            <a href="../descargar-vpn.php?usuario=. <?php $user?> ." class="boton"></a>
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