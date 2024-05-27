<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmación de Compra</title>
</head>
<body>
    <h1>Compra Completada</h1>
    <p>Gracias por tu compra. Tu pedido ha sido procesado con éxito.</p>
    <a href="index.php">Volver al Inicio</a>
</body>
</html>
