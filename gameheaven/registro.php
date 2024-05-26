<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/style.css">
    <title>Registro</title>
    <script src="js/registro.js"></script>
</head>
<body>
    <img src="images/Captura_de_pantalla_2024-04-09_113755-removebg-preview.png" alt="logo">
    <div class="wrapper">
        <h1>Registro de Usuario</h1>
        <form id="formulario" method="post" action="<?=htmlspecialchars($_SERVER['PHP_SELF'])?>" onsubmit="return validarFormulario()">
        <p class="noVisible" id="mnombre">Campo obligatorio</p>    
        <label for="nombre"></label><br>
            <input type="text" id="nombre" name="nombre" required placeholder="Nombre" onfocus="ocultarError('mnombre')"><br><br>
            
            <p class="noVisible" id="memail">Campo obligatorio</p>
            <label for="email"></label><br>
            <input type="email" id="email" name="email" required placeholder="Email" onfocus="ocultarError('memail')"><br><br>
            
            <p class="noVisible" id="mcontrasena">Campo obligatorio</p>
            <label for="contrasena"></label><br>
            <input type="password" id="contrasena" name="contrasena" required placeholder="Contraseña" onfocus="ocultarError('mcontrasena')"><br><br>
            
            <p class="noVisible" id="mconfirmar_contrasena">Campo obligatorio</p>
            <label for="confirmar_contrasena"></label><br>
            <input type="password" id="confirmar_contrasena" name="confirmar_contrasena" required placeholder="Confirmar contraseña" onfocus="ocultarError('mconfirmar_contrasena')"><br><br>
            
            <p class="noVisible" id="mtelefono">Campo obligatorio</p>
            <label for="telefono"></label><br>
            <input type="tel" id="telefono" name="telefono" required placeholder="Teléfono" onfocus="ocultarError('mtelefono')"><br><br>
            
            <p class="noVisible" id="mfecha">Campo obligatorio</p>
            <label for="fecha_nacimiento"></label><br>
            <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" required placeholder="Fecha de nacimiento" onfocus="ocultarError('mfecha')"><br><br>
            
            <p class="noVisible" id="mdireccion">Campo obligatorio</p>
            <label for="direccion"></label><br>
            <textarea id="direccion" name="direccion" rows="4" cols="35" required placeholder="Dirección" onfocus="ocultarError('mdireccion')"></textarea><br><br>
            

            <input type="submit" value="Registrar" id="registrar">
        </form>
        <div class="miembro">
            ¿Ya tienes cuenta? <a href="login/login.php">Inicia sesión</a>
        </div>
    </div>
    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $nombre = isset($_POST["nombre"]) ? trim(htmlspecialchars($_POST["nombre"])) : "";
        $contraseña = isset($_POST["contrasena"]) ? trim(htmlspecialchars($_POST["contrasena"])) : "";
        $email = isset($_POST["email"]) ? trim(htmlspecialchars($_POST["email"])) : "";
        $telefono = isset($_POST["telefono"]) ? trim(htmlspecialchars($_POST["telefono"])) : "";
        $fecha_nacimiento = isset($_POST["fecha_nacimiento"]) ? trim(htmlspecialchars($_POST["fecha_nacimiento"])) : "";
        $direccion = isset($_POST["direccion"]) ? trim(htmlspecialchars($_POST["direccion"])) : "";
    
        $errors = array();
    
        if (empty($nombre)) {
            $errors[] = 'Es necesario ingresar el nombre. <br>';
        }
    
        if (empty($contraseña)) {
            $errors[] = 'Es necesario ingresar la contraseña. <br>';
        }
    
        if (empty($email)) {
            $errors[] = 'Es necesario ingresar el correo electrónico. <br>';
        }
    
        if (empty($telefono)) {
            $errors[] = 'Es necesario ingresar el teléfono. <br>';
        }
    
        if (empty($fecha_nacimiento)) {
            $errors[] = 'Es necesario ingresar la fecha de nacimiento. <br>';
        }
    
        if (empty($direccion)) {
            $errors[] = 'Es necesario ingresar la dirección. <br>';
        }
    
        if (!empty($errors)) {
            foreach ($errors as $e) {
                echo $e;
            }
        } else {
            include("BBDD.php");
            
            $nombre = $conex1->real_escape_string($nombre);
            $contraseña = $conex1->real_escape_string($contraseña);
            $email = $conex1->real_escape_string($email);
            $telefono = $conex1->real_escape_string($telefono);
            $fecha_nacimiento = $conex1->real_escape_string($fecha_nacimiento);
            $direccion = $conex1->real_escape_string($direccion);
    
            $query = "INSERT INTO usuarios (nombre, contrasena, email, telefono, fecha_nacimiento, direccion) VALUES ('$nombre', '$contraseña', '$email', '$telefono', '$fecha_nacimiento', '$direccion')";
            $resultado = $conex1->query($query);
    
            if ($resultado) {
                header("Location: login/login.php");
                exit();
            } else {
                echo "Error al insertar en la base de datos: " . $conex1->error;
            }
    
            $conex1->close();
        }
    }
    ?>
    <script src="js/registro.js"></script>
</body>
</html>
