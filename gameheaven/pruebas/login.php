<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Document</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Play:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>
    

    <?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["usuario"]) && isset($_POST["contra"])) {
    $usuario = htmlspecialchars(trim($_POST["usuario"]));
    $contra = htmlspecialchars(trim($_POST["contra"]));
    $errors = array();

    if (empty($usuario) || empty($contra)) {
        $errors[] = "Debes introducir todos los campos";
    }

    if (!empty($errors)) {
        foreach ($errors as $e) {
            echo $e;
        }
    } else {
        include("BBDD.php");
        $query = "SELECT nombre, contrasena FROM usuarios WHERE (nombre=? OR email=?) AND contrasena=?";
        $stmt = $conex1->prepare($query);
        $stmt->bind_param("sss", $usuario, $usuario, $contra);

        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            session_start();
            $_SESSION["usuario"] = $usuario;
            header("Location: mostrar.php");
            exit();
        } else {
            echo "Los datos introducidos no son correctos";
        }

        $stmt->close();
        $conex1->close();
    }
}


?>
<img src="images/Captura_de_pantalla_2024-04-09_113755-removebg-preview.png" alt="logo">
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
            ¿Todavia no eres miembro? <a href="registro.php">Registrate</a>
        </div>
    </div>
</body>
</html>