<?php
    # Habilitar mensajes de error
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require 'conexion.php';

    $error = "";

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $nombre = trim($_POST['nombre']);;
        $apellidos = trim($_POST['apellidos']);
        $username = trim($_POST['username']);
        $email = trim($_POST['correo']);
        $password = hash('sha256', trim($_POST['password']));
        $passwordcheck = hash('sha256', trim($_POST['passwordcheck']));

        if (empty($nombre) || empty($apellidos) || empty($username) ||empty($email) || empty($password) || empty($passwordcheck)) {
            $error = "Tienes que completar todos los campos.";
        } # Validando campos vacíos
        
        if ($password !== $passwordcheck) {
            $error = "Las contraseñas no son las mismas";
        }

        $select = $conexion->execute_query("SELECT * FROM users WHERE '$username' = 'username';");

        if ($select->num_rows > 0) {
            $fila = $select->fetch_assoc();

            if ($username == $fila['username'] && $email == $fila['email']) {
                $error = "El nombre de usuario y el correo electrónico ya está en uso";

            } elseif ($email == $fila['email']) {
                $error = "El correo electrónico ya está en uso";

            } elseif ($username == $fila['username']) {
                $error = "El nombre de usuario ya está en uso";
            }
        }

        $insert = $conexion->execute_query("INSERT INTO users (username, nombre, apellidos, email, password) 
                                        VALUES ('$username', '$nombre', '$apellidos', '$email', '$password');");
        # Ejecución de sentencia

        if (isset($insert)) {
            echo "<script>alert('Usuario ". $username ." creado correctamente');</script>";
            echo "<script>window.location.href = 'login.php';</script>";
        } 
    }
?>
<!DOCTYPE html>
<html>
<head>
    <title>HackForFun | Sign In</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../css/register.css">
    <link rel="icon" href="../icono/logo_hack4fun_H_whiteblue.ico" type="image/x-icon">
</head>
<body>
    <header>
        <div id="inicio" onclick="window.location.href='../index.html';">
            <img src="../icono/logo_hack4fun_bluewhite.png"/>
        </div>
    </header>
    <form action="" method="post">
        <h3 style="text-align: center; color: #FFFFFF;">Registro</h3>
        <hr>
        <?php if (!empty($error)): ?>
            <div style="color: red;"><?php echo $error; ?></div> 
        <?php endif; ?> 
        <!-- Permite que se muestren los mensajes de error definidos en los condicionales -->
        <br>
        Nombre: <br>
        <input type="text" name="nombre" id="nombre"><br><br>
        Apellidos: <br>
        <input type="text" name="apellidos" id="apellidos"><br><br>
        Nombre de usuario: <br>
        <input type="text" name="username" id="username"><br><br>
        Email: <br>
        <input type="text" name="correo" id="correo" value="<?php echo htmlspecialchars($email ?? ''); ?>"><br><br>
        Contraseña: <br>
        <input type="password" name="password" id="password"><br><br>
        Repetir Contraseña: <br>
        <input type="password" name="passwordcheck" id="passwordcheck"><br><br>
        <input class="boton" type="submit" value="Registrarse"> 
    </form>
</body>
</html>