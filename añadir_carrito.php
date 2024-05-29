<?php
session_start();
include 'BBDD.php';

header('Content-Type: application/json');

if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(['success' => false, 'message' => 'Debes iniciar sesión']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$id_videojuego = $data['id_videojuego'];
$id_usuario = $_SESSION['id_usuario'];

// Verificar el stock
$query = "SELECT stock FROM videojuegos WHERE id_videojuego = ?";
$stmt = $conex1->prepare($query);
$stmt->bind_param("i", $id_videojuego);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$stock = $row['stock'];

if ($stock > 0) {
    //Iniciar una transacción
    $conex1->begin_transaction();
    try {
        // Añadir al carrito
        $query = "INSERT INTO carrito (id_usuario, id_videojuego, cantidad) VALUES (?, ?, 1)
                  ON DUPLICATE KEY UPDATE cantidad = cantidad + 1";
        $stmt = $conex1->prepare($query);
        $stmt->bind_param("ii", $id_usuario, $id_videojuego);
        $stmt->execute();

        //Commit de la transacción
        $conex1->commit();

        $response = ['success' => true];
    } catch (Exception $e) {
        //Rollback en caso de error
        $conex1->rollback();
        $response = ['success' => false, 'message' => 'Error al añadir el producto al carrito'];
    }
} else {
    $response = ['success' => false, 'message' => 'No hay suficiente stock'];
}

echo json_encode($response);
?>
