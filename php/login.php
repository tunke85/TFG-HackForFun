<?php
    # Habilitar mensajes de error
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    header('Content-Type: application/json'); // Asegura que la respuesta sea JSON

    require 'conexion.php';

    $response = ['success' => false, 'error' => ''];

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $email = $_POST['correo'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $response['error'] = "Tienes que completar todos los campos.";
            echo json_encode($response);
            exit;
        }
        
        $hashedPassword = hash('sha256', $password);
        $sql = $conexion->execute_query("SELECT * FROM users WHERE ('$email' = email OR '$email' = username) AND password = ?", [$hashedPassword]);

        if ($sql->num_rows > 0) {
            session_start();
            $_SESSION['id'] = $email . " " . $hashedPassword;
            $response['success'] = true;
            $response['redirect'] = '../../php/panel.php';
        } else {
            $response['error'] = "Usuario o contraseña no válidos";
        }
    }

    echo json_encode($response);
    exit;
?>