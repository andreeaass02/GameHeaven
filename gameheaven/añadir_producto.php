<?php
session_start();
require 'BBDD.php';

if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $genero = $_POST['genero'];
    $plataforma = $_POST['plataforma'];
    $precio = $_POST['precio'];

    $stock = 0;

    $query = "INSERT INTO videojuegos (nombre, descripcion, genero, plataforma, precio) 
              VALUES (?, ?, ?, ?, ?)";
    $stmt = $conex1->prepare($query);
    $stmt->bind_param("ssssd", $nombre, $descripcion, $genero, $plataforma, $precio);

    if ($stmt->execute()) {
        echo "Producto añadido correctamente.";
    } else {
        echo "Error al añadir el producto: " . $stmt->error;
    }
}
?>
<a href="admin.php">Volver</a>
