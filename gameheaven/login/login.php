<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/style.css">
    <title>Inicio de Sesión</title>
</head>
<body>
    <img src="../images/Captura_de_pantalla_2024-04-09_113755-removebg-preview.png" alt="logo">
    <div class="wrapper">
        <h1>Inicio de Sesión</h1>
        <form method="post" action="<?=htmlspecialchars($_SERVER['PHP_SELF'])?>">
            <label for="usuario"></label>
            <input type="text" id="usuario" name="usuario" placeholder="Usuario o correo" required><br><br>

            <label for="contra"></label>
            <input type="password" id="contra" name="contra" placeholder="Contraseña" required><br><br>

            <input type="submit" value="Acceder">
        </form>
        <div class="miembro">
            ¿Todavía no eres miembro? <a href="../registro.php">Regístrate</a>
        </div>
    </div>
    
    <?php
    require_once("usuarios.php");
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST["usuario"]) && isset($_POST["contra"])) {
            $usuario = trim(htmlspecialchars($_POST["usuario"]));
            $contra = trim(htmlspecialchars($_POST["contra"]));

            if (empty($usuario) || empty($contra)) {
                $errors [] = "Debes introducir todos los campos";
            }

            if (!empty($errors)) {
                foreach ($errors as $e) {
                    echo $e;
                }
            } else {
                $usuarioObj = new Usuario();
                $usuarioObj->verificar($usuario, $contra);
            }
        } else {
            echo "Error: Los índices 'usuario' y 'contra' no existen en el array \$_POST.";
        }
    }
    ?>
</body>
</html>
