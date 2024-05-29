<?php
session_start();
require 'BBDD.php';

if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(['count' => 0]);
    exit;
}

$id_usuario = $_SESSION['id_usuario'];

$query = "SELECT SUM(cantidad) AS total FROM carrito WHERE id_usuario = ?";
$stmt = $conex1->prepare($query);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$stmt->bind_result($total);
$stmt->fetch();
$stmt->close();

$total = $total ? $total : 0;

echo json_encode(['count' => $total]);
?>
