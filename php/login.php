<?php
    # Habilitar mensajes de error
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    header('Content-Type: application/json'); // Asegura que la respuesta sea JSON

    require 'conexion.php';

    $response = ['success' => false, 'error' => ''];

    try {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = trim($_POST['correo'] ?? '');
            $password = $_POST['password'] ?? '';

            // Validación de campos vacíos
            if (empty($email) || empty($password)) {
                throw new Exception("Tienes que completar todos los campos.");
            }
            
            // Consulta preparada segura
            $hashedPassword = hash('sha256', $password);
            $stmt = $conexion->prepare("SELECT id, username, email FROM users WHERE (email = ? OR username = ?) AND password = ?");
            $stmt->bind_param("sss", $email, $email, $hashedPassword);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                session_start();
                $user = $result->fetch_assoc();
                
                // Mejorar la sesión almacenando datos útiles
                $_SESSION['id'] = $user['id'];

                $response['success'] = true;
                $response['redirect'] = '/php/panel.php';
            } else {
                throw new Exception("Usuario o contraseña no válidos");
            }
        } else {
            throw new Exception("Método de solicitud no válido");
        }
    } catch (Exception $e) {
        $response['error'] = $e->getMessage();
    }

    echo json_encode($response);
    exit;
?>