<?php
    # Habilitar mensajes de error
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require 'conexion.php';

    $error = "";

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $email = $_POST['correo'];
        $password = hash('sha256', $_POST['password']);

        if (empty($email) || empty($password)) {
            $error = "Tienes que completar todos los campos.";
        } # Validando campos vacíos
        
        $sql = $conexion->execute_query("SELECT * FROM users WHERE ('$email' = email OR '$email' = username) AND '$password' = password;");
        # Ejecución de sentencia

        if ($sql->num_rows > 0) {
            $session= $email . " " . hash('sha256', $password); # Variable session compuesta de email y contraseña haseada
            session_start();
            $_SESSION['id'] = $session;
            header("Location: panel.php");
        } else {
            $error= "Usuario o contraseña no validos";
        }
    }
?>

<!DOCTYPE html>
<html>
<head>
    <title>HackForFun | Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
    <header>
        <div id="inicio" onclick="window.location.href='../index.html';">
            <img src="imagenes/logo.jpeg"/>
            <h3>HackForFun</h3>
        </div>
    </header>
    <form action="" method="post">
        <?php if (!empty($error)): ?>
            <div style="color: red;"><?php echo $error; ?></div> 
        <?php endif; ?> 
        <!-- Permite que se muestren los mensajes de error definidos en los condicionales -->
        <br>
        Email o nombre de usuario: <br>
        <input type="text" name="correo" id="correo" value="<?php echo htmlspecialchars($email ?? ''); ?>"><br><br>
        Contraseña: <br>
        <input type="password" name="password" id="password"><br><br>
        <input class="boton" type="submit" value="Iniciar sesión">
        <input class="boton" type="button" onclick="window.location.href='register.php';" value="Registrarse">
    </form>
    <footer>
        <div>
            <div id="logo" onclick="window.location.href='../index.html';">
                <img src="imagenes/logo.jpeg"/>
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
