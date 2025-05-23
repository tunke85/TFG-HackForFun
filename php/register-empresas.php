<?php
# Habilitar mensajes de error
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json'); // Añadir cabecera JSON

require 'conexion.php';

$response = ['success' => false, 'error' => ''];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombreemp = trim($_POST['nombreemp'] ?? '');
    $areaprof = trim($_POST['areaprof'] ?? '');
    $dir = trim($_POST['dir'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $numerotrabj = trim($_POST['numerotrabj'] ?? '');

    // Validación de campos vacíos
    if (empty($nombreemp) || empty($areaprof) || empty($dir) || empty($email) || empty($numerotrabj)) {
        $response['error'] = "Debes completar todos los campos.";
        echo json_encode($response);
        exit;
    }

    // Validación de número de trabajadores (debe ser numérico)
    if (!is_numeric($numerotrabj)) {
        $response['error'] = "El número de trabajadores debe ser un valor numérico";
        echo json_encode($response);
        exit;
    }

    try {
        // Verificar si la empresa ya existe (usando consultas preparadas)
        $stmt = $conexion->prepare("SELECT nombre FROM empresas WHERE nombre = ? OR email = ?");
        $stmt->bind_param("ss", $nombreemp, $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $empresa = $result->fetch_assoc();
            
            if ($nombreemp === $empresa['nombre']) {
                $response['error'] = "El nombre de la empresa ya está registrado";
            } else {
                $response['error'] = "El correo electrónico ya está registrado";
            }
            
            echo json_encode($response);
            exit;
        }

        // Insertar nueva empresa (usando consultas preparadas)
        $insert = $conexion->prepare("INSERT INTO empresas (nombre, areaprof, dir, email, numerotrabj) VALUES (?, ?, ?, ?, ?)");
        $insert->bind_param("ssssi", $nombreemp, $areaprof, $dir, $email, $numerotrabj);
        
        if ($insert->execute()) {
            $response['success'] = true;
            $response['message'] = "Empresa $nombreemp registrada correctamente";
            $response['redirect'] = '../php/empresa.php?nombre=' . urlencode($nombreemp);
        } else {
            $response['error'] = "Error al registrar la empresa: " . $conexion->error;
        }
    } catch (Exception $e) {
        $response['error'] = "Error en la base de datos: " . $e->getMessage();
    }
}

echo json_encode($response);
exit;
?>
