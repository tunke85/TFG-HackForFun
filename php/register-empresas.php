<?php
    # Habilitar mensajes de error
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require 'conexion.php';

    $error = "";

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $nombreemp = trim($_POST['nombreemp']);
        $areaprof = trim($_POST['areaprof']);
        $dir = trim($_POST['dir']);
        $email = trim($_POST['email']);
        $numerotrabj = trim($_POST['numerotrabj']);

        if (empty($nombreemp) || empty($areaprof) || empty($dir) || empty($email) || empty($numerotrabj)) {
            $error = "Tienes que completar todos los campos.";
        }

        $select = $conexion->execute_query("SELECT * FROM empresas WHERE 'nombreemp' = 'nombre';");

        if ($select->num_rows > 0) {
            $fila = $select->fetch_assoc();

            if ($nombreemp == $fila['nombre']) {
                $error = "El nombre de la empresa ya está registrado.";
            }
        }

        $insert = $conexion->execute_query("INSERT INTO empresas
                                            (nombre, areaprof, dir, numerotrabj)
                                            VALUES ('$nombreemp', '$areaprof', '$dir', '$email', '$numerotrabj');");

        if (isset($insert)) {
            echo "<script>alert('Empresa ". $nombreemp ." registrada correctamente.');</script>";
            echo "<script>window.location.href = 'empresa.php?nombre=". $nombreemp . "';<script>";
        }
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <title>HackForFun | Empresas</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta charset="UTF-8">
        <link rel="stylesheet" href="../css/register-empresas.css">
        <link rel="icon" href="../icono/logo.ico" type="image/x-icon">
    </head>
    <body>
        <header>
            <div id="inicio" onclick="window.location.href='../index.html';">
                <img src="../icono/logo.jpeg"/>
                <h3>HackForFun</h3>
            </div>
        </header>
        <form action="" method="post">
            <h3 style="text-align: center; color: #FFFFFF;">Registro Empresas</h3>
            <hr>
            <?php if (!empty($error)): ?>
                <div style="color: red;"><?php echo $error; ?></div> 
            <?php endif; ?> 
            <!-- Permite que se muestren los mensajes de error definidos en los condicionales -->
            <br>
            Nombre de la Empresa: <br>
            <input type="text" name="nombreemp" id="nombreemp"><br><br>
            Area profesional: <br>
            <input type="text" name="areaprof" id="areaprof"><br><br>
            Dirección: <br>
            <input type="text" name="dir" id="dir"><br><br>
            Email: <br>
            <input type="text" name="correo" id="correo" value="<?php echo htmlspecialchars($email ?? ''); ?>"><br><br>
            Numero de trabajadores: <br>
            <input type="text" name="numerotrabj" id="numerotrabj"><br><br>
            <input class="boton" type="submit" value="Registrarse"> 
        </form>
    </body>
</html>