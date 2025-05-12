<?php
    # Habilitar mensajes de error
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    header('Content-Type: application/json'); // Añadir cabecera JSON

    require 'conexion.php';

    $response = ['success' => false, 'error' => ''];

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $nombre = trim($_POST['nombre'] ?? '');
        $apellidos = trim($_POST['apellidos'] ?? '');
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['correo'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $passwordcheck = trim($_POST['passwordcheck'] ?? '');

        // Validación de campos vacíos
        if (empty($nombre) || empty($apellidos) || empty($username) || empty($email) || empty($password) || empty($passwordcheck)) {
            $response['error'] = "Tienes que completar todos los campos.";
            echo json_encode($response);
            exit;
        }

        // Validación de contraseñas
        if ($password !== $passwordcheck) {
            $response['error'] = "Las contraseñas no coinciden";
            echo json_encode($response);
            exit;
        }

        // Hash de contraseñas
        $hashedPassword = hash('sha256', $password);

        try {
            // Verificar si el usuario o email ya existen (usando consultas preparadas)
            $stmt = $conexion->prepare("SELECT username, email FROM users WHERE username = ? OR email = ?");
            $stmt->bind_param("ss", $username, $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();
                
                if ($username === $user['username'] && $email === $user['email']) {
                    $response['error'] = "El nombre de usuario y el correo electrónico ya están en uso";
                } elseif ($email === $user['email']) {
                    $response['error'] = "El correo electrónico ya está en uso";
                } elseif ($username === $user['username']) {
                    $response['error'] = "El nombre de usuario ya está en uso";
                }
                
                echo json_encode($response);
                exit;
            }

            // Insertar nuevo usuario (usando consultas preparadas)
            $insert = $conexion->prepare("INSERT INTO users (username, nombre, apellidos, email, password) VALUES (?, ?, ?, ?, ?)");
            $insert->bind_param("sssss", $username, $nombre, $apellidos, $email, $hashedPassword);
            
            if ($insert->execute()) {
                $response['success'] = true;
                $response['redirect'] = '../index.html';
                $response['message'] = "Usuario $username creado correctamente. Redirigiendo...";
            } else {
                $response['error'] = "Error al registrar el usuario: " . $conexion->error;
            }
        } catch (Exception $e) {
            $response['error'] = "Error en la base de datos: " . $e->getMessage();
        }
    }

    echo json_encode($response);
    exit;
?>