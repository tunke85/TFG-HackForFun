<?php
    # Habilitar mensajes de error
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require '../conexion.php';

    $mensaje = '';
    $respuestauser = '';
    $respuestaroot = '';
    $is_correctuser = false;
    $is_correctroot = false;

    session_start();
    if (!isset($_SESSION['id'])) {
        header("Location: http://localhost/");
    }

    $userSession = explode(' ', $_SESSION['id']);
    $select = $conexion->execute_query("SELECT username FROM users WHERE '$userSession[0]' = username OR '$userSession[0]' = email;");
    $userEmail = $select->fetch_assoc();
    $user = $userEmail['username'] ?? '';

    $machineName = $conexion->execute_query("SELECT name FROM machines WHERE name = 'control'")->fetch_assoc();
    $serverId = $conexion->execute_query("SELECT machineid FROM machines WHERE name = 'control'")->fetch_assoc();

    $serverConfig = [
        'machineName' => $machineName['name'],
        'serverId' => $serverId['machineid'] 
    ];

    echo '<script>window.serverConfig = ' . json_encode($serverConfig) . ';</script>';

    $stmt1 = $conexion->prepare("SELECT userflag, rootflag FROM labprogress WHERE iduser = (SELECT id FROM users WHERE username = ?)");
    $stmt1->bind_param("s", $user);
    $stmt1->execute();
    $result1 = $stmt1->get_result();

    $stmt2 = $conexion->prepare("SELECT userflag, rootflag FROM machines WHERE name = ? ");
    $stmt2->bind_param("s", $serverConfig['machineName']);
    $stmt2->execute();
    $result2 = $stmt2->get_result();

    if ($result1->num_rows > 0) {
        $row1 = $result1->fetch_assoc();
        $is_correctuser = (bool)($row1['userflag'] ?? false);
        $is_correctroot = (bool)($row1['rootflag'] ?? false);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && (!$is_correctuser || !$is_correctroot)) {
        $new_respuestauser = trim($_POST['respuestauser'] ?? '');
        $new_respuestaroot = trim($_POST['respuestaroot'] ?? '');
        
        if ($result2->num_rows > 0) {
            $row2 = $result2->fetch_assoc();
            $clave_correctauser = $row2['userflag'] ?? '';
            $clave_correctaroot = $row2['rootflag'] ?? '';
            
            $update_needed = false;
            
            // Validación respuesta user
            if (!$is_correctuser && !empty($_POST['respuestauser'])) {
                $respuestauser = $new_respuestauser;
                $is_correctuser = ($new_respuestauser === $clave_correctauser);
                $update_needed = $is_correctuser;
            }
            
            // Validación respuesta root
            if (!$is_correctroot && !empty($_POST['respuestaroot'])) {
                $respuestaroot = $new_respuestaroot;
                $is_correctroot = ($new_respuestaroot === $clave_correctaroot);
                $update_needed = $update_needed || $is_correctroot;
            }
            
            // Actualización en base de datos
            if ($update_needed) {
                $comprobacion_stmt = $conexion->prepare("SELECT * FROM labprogress WHERE iduser = (SELECT id FROM users WHERE username = ?) AND idmachine = (SELECT id FROM machines WHERE name = ?)");
                $comprobacion_stmt->bind_param("ss", $user, $serverConfig['machineName']);
                $comprobacion_stmt->execute();
                $rest_comprobacion_stmt = $comprobacion_stmt->get_result();

                if ($rest_comprobacion_stmt->num_rows == 0) {
                    $insert_stmt = $conexion->prepare("INSERT INTO labprogress (idmachine, iduser) SELECT (SELECT id FROM machines WHERE name = ? LIMIT 1),(SELECT id FROM users WHERE username = ? LIMIT 1)");
                    $insert_stmt->bind_param("ss", $serverConfig['machineName'], $user);
                    $insert_stmt->execute();
                }

                $update_stmt = $conexion->prepare("UPDATE labprogress SET userflag = ?, rootflag = ? WHERE iduser = (SELECT id FROM users WHERE username = ?) AND idmachine = (SELECT id FROM machines WHERE name = ?)");
                $update_stmt->bind_param("iiss", $is_correctuser, $is_correctroot, $user, $serverConfig['machineName']);

                if ($update_stmt->execute()) {
                    if ($is_correctuser && $is_correctroot) {
                        $mensaje = "<div class='exito'>¡Todas las respuestas son correctas!</div>";
                    } else {
                        $mensaje = "<div class='info'>Progreso guardado correctamente.</div>";
                    }
                } else {
                    $mensaje = "<div class='error'>Error al guardar el progreso</div>";
                }
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
            <h3>Control</h3>
            <p>
                Esta máquina está creada con el objetivo de mejorar y/o aprender de técnicas de hacking de sistemas.
                Certificados: <b>eJPT</b> y <b>OSCP</b><br><br>
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
                    ip: null,
                    isAccessible: false,
                    isProcessing: false // Nuevo estado para operaciones en curso
                };

                // Configuración desde PHP
                const currentMachine = window.serverConfig.machineName;
                const serverId = window.serverConfig.serverId;
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
                    actionSpinner.style.display = 'none';
                    rebootSpinner.style.display = 'none';
                    
                    mainBtn.addEventListener('click', handleMainAction);
                    rebootBtn.addEventListener('click', handleRebootAction);
                    
                    checkServerStatus();
                    setInterval(checkServerStatus, 30000);
                }

                // Verificar estado del servidor (mejorado)
                async function checkServerStatus() {
                    if (serverState.isProcessing) return; // No verificar durante operaciones
                    
                    try {
                        const response = await fetch(
                            `${API_BASE_URL}/server-status?machine=${currentMachine}&serverId=${serverId}&rand=${Math.random()}`
                        );
                        
                        if (!response.ok) throw new Error(`Error HTTP: ${response.status}`);
                        
                        const data = await response.json();
                        if (!data.success) throw new Error(data.error || 'Error en la respuesta');

                        serverState = {
                            ...serverState,
                            status: data.status,
                            ip: data.ip,
                            isAccessible: data.isAccessible || false
                        };
                        
                        updateUI();
                    } catch (error) {
                        console.error('Error al verificar estado:', error);
                        if (!serverState.isProcessing) {
                            statusDiv.innerHTML = '⚠️ Error al conectar con el servicio';
                            statusDiv.style.color = 'orange';
                        }
                    }
                }

                // Manejadores de acciones
                async function handleMainAction() {
                    const action = serverState.status === 'Running' ? 'stop' : 'start';
                    await handleAction(action, actionSpinner, actionText);
                }

                async function handleRebootAction() {
                    if (confirm('¿Estás seguro de reiniciar la máquina? Esto puede tomar unos minutos.')) {
                        await handleAction('reboot', rebootSpinner, rebootBtn.querySelector('span'));
                    }
                }

                // Función principal para manejar acciones (mejorada)
                async function handleAction(action, spinner, textElement) {
                    serverState.isProcessing = true;
                    const originalText = textElement.textContent;
                    const btn = spinner === actionSpinner ? mainBtn : rebootBtn;
                    
                    btn.disabled = true;
                    spinner.style.display = 'inline-block';
                    statusDiv.style.color = 'inherit';
                    
                    // Mensajes específicos para cada acción
                    const actionMessages = {
                        start: 'Desarchivando máquina...',
                        stop: 'Archivando máquina...',
                        reboot: 'Reiniciando máquina...'
                    };
                    
                    statusDiv.innerHTML = actionMessages[action] || 'Procesando...';
                    
                    try {
                        const response = await fetch(`${API_BASE_URL}/server-action`, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ action, machine: currentMachine, serverId })
                        });
                        
                        if (!response.ok) throw new Error(`Error HTTP: ${response.status}`);
                        
                        const data = await response.json();
                        if (!data.success) throw new Error(data.error || 'Error en la respuesta');

                        // Esperar confirmación del cambio de estado
                        await waitForStatusChange(action);
                        
                    } catch (error) {
                        console.error(`Error en ${action}:`, error);
                        statusDiv.innerHTML = `❌ Error: ${error.message}`;
                        statusDiv.style.color = 'red';
                    } finally {
                        serverState.isProcessing = false;
                        btn.disabled = false;
                        spinner.style.display = 'none';
                        await checkServerStatus();
                    }
                }

                // Esperar cambio de estado (mejorado)
                async function waitForStatusChange(action) {
                    const targetStatus = action === 'start' ? 'Running' : 
                                    action === 'stop' ? 'Archived' : 'Running';
                    
                    let attempts = 0;
                    const maxAttempts = 30; // 30 segundos máximo
                    
                    while (attempts < maxAttempts) {
                        statusDiv.innerHTML = `${getActionName(action)}... (${attempts}s)`;
                        await new Promise(resolve => setTimeout(resolve, 1000));
                        
                        try {
                            await checkServerStatus();
                            if (serverState.status === targetStatus) {
                                if (action === 'start') {
                                    // Esperar adicionalmente a que sea accesible
                                    await waitForAccessibility();
                                }
                                return;
                            }
                        } catch (error) {
                            console.error('Error durante espera:', error);
                        }
                        
                        attempts++;
                    }
                    
                    throw new Error(`La acción está tardando más de lo esperado (${maxAttempts}s)`);
                }

                // Esperar a que el servidor sea accesible
                async function waitForAccessibility() {
                    let attempts = 0;
                    const maxAttempts = 30;
                    
                    while (attempts < maxAttempts && !serverState.isAccessible) {
                        statusDiv.innerHTML = `Iniciando... (${attempts}s)`;
                        await new Promise(resolve => setTimeout(resolve, 1000));
                        await checkServerStatus();
                        attempts++;
                    }
                    
                    if (!serverState.isAccessible) {
                        throw new Error('El servidor no respondió después de iniciar');
                    }
                }

                // Actualizar interfaz (mejorada)
                function updateUI() {
                    if (serverState.isProcessing) return;

                    // Botón principal
                    if (serverState.status === 'Running') {
                        actionText.textContent = 'Detener Máquina';
                        mainBtn.classList.remove('btn-primary');
                        mainBtn.classList.add('btn-danger');
                        rebootBtn.disabled = false;
                        
                        const accStatus = serverState.isAccessible ? 
                            '<span style="color:green"> (Accesible)</span>' : 
                            '<span style="color:orange"> (Iniciando...)</span>';
                        
                        statusDiv.innerHTML = serverState.ip ? 
                            `Estado: <strong>En ejecución</strong>${accStatus} - IP: ${serverState.ip}` : 
                            `Estado: <strong>En ejecución</strong>${accStatus}`;
                            
                    } else if (serverState.status === 'Archived') {
                        actionText.textContent = 'Iniciar Máquina';
                        mainBtn.classList.remove('btn-danger');
                        mainBtn.classList.add('btn-primary');
                        rebootBtn.disabled = true;
                        statusDiv.innerHTML = 'Estado: <strong>Archivada</strong>';
                    } else {
                        actionText.textContent = 'Iniciar Máquina';
                        mainBtn.classList.remove('btn-danger');
                        mainBtn.classList.add('btn-primary');
                        rebootBtn.disabled = true;
                        statusDiv.innerHTML = 'Estado: <strong>Detenida</strong>';
                    }
                }

                // Helper para nombres de acciones
                function getActionName(action) {
                    const actions = {
                        'start': 'Iniciando',
                        'stop': 'Apagando',
                        'reboot': 'Reiniciando'
                    };
                    return actions[action] || action;
                }

                // Iniciar cuando el DOM esté listo
                document.addEventListener('DOMContentLoaded', init);
            </script>
            <p>
                <a href="control/ofensive.pdf">Solución Ofensiva</a> | <a href="control/defensive.pdf">Solución Defensiva</a>
            </p>
        </div>
        <div id="vpn">
            <h3>Descarga de VPN</h3>
            <p>Descarga la vpn para poder realizar la máquina del laboratorio<br><br><br></p>
            <a href="../descargar-vpn.php?usuario=<?php echo htmlspecialchars($user);?>" class="boton">Descargar</a>
            <br><br>
        </div>
        <div class="docker">
            <?php echo $mensaje; ?>
            <form method="post" id="formRespuestas">
                <div class="campo <?php echo $is_correctuser ? 'campo-bloqueado' : 'campo-activo'; ?>">
                    <label>User Flag:</label>
                    <input type="text" name="respuestauser" 
                        value="<?php echo htmlspecialchars($respuestauser); ?>" 
                        <?php echo $is_correctuser ? 'readonly' : ''; ?>>
                    <?php if ($is_correctuser): ?>
                        <span class="exito">✓ User flag correcta</span>
                    <?php elseif (!empty($respuestauser)): ?>
                        <span class="error">✗ User flag incorrecta</span>
                    <?php endif; ?>
                </div>
                <div class="campo <?php echo $is_correctroot ? 'campo-bloqueado' : 'campo-activo'; ?>">
                    <label>Root Flag::</label>
                    <input type="text" name="respuestaroot" 
                        value="<?php echo htmlspecialchars($respuestaroot); ?>" 
                        <?php echo $is_correctroot ? 'readonly' : ''; ?>>
                    <?php if ($is_correctroot): ?>
                        <span class="exito">✓ Root flag correcta</span>
                    <?php elseif (!empty($respuestaroot)): ?>
                        <span class="error">✗ Root flag incorrecta</span>
                    <?php endif; ?>
                </div>
                <br>
                <button class="boton" type="submit" <?php echo ($is_correctuser && $is_correctroot) ? 'disabled' : ''; ?>>
                    <?php echo ($is_correctuser || $is_correctroot) ? 'Enviar' : 'Enviar'; ?>
                </button>
            </form>
            <script>
                // Validación básica del formulario
                document.getElementById('formRespuestas').addEventListener('submit', function(e) {
                    const respuesta1 = this.elements.respuestauser.value.trim();
                    const respuesta2 = this.elements.respuestaroot.value.trim();
                    const is_correct1 = <?php echo $is_correctuser ? 'true' : 'false'; ?>;
                    const is_correct2 = <?php echo $is_correctroot ? 'true' : 'false'; ?>;

                    // Si ambos campos están vacíos y no hay respuestas previas
                    if (!respuesta1 && !respuesta2 && !is_correct1 && !is_correct2) {
                        e.preventDefault();
                        alert('Debes ingresar al menos una respuesta');
                        return;
                    }

                    // Si intenta enviar un campo vacío que no estaba completado antes
                    if ((!respuesta1 && !is_correct1) || (!respuesta2 && !is_correct2)) {
                        e.preventDefault();
                        alert('Completa todos los campos requeridos');
                    }
                });
            </script>
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