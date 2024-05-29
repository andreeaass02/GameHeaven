<?php
session_start();
require 'BBDD.php';

if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_videojuego = $_POST['id_videojuego'];
    $codigo = $_POST['codigo'];

    // Iniciar una transacción
    $conex1->begin_transaction();
    try {
        // Verificar si el código ya existe
        $query = "SELECT COUNT(*) FROM codigos WHERE codigo = ?";
        $stmt = $conex1->prepare($query);
        $stmt->bind_param("s", $codigo);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close(); // Liberar el resultado

        if ($count > 0) {
            echo "Error: El código '$codigo' ya existe.";
            echo "<a href='admin.php'>Volver</a>";
            $conex1->rollback();
            exit;
        }

        // Añadir el código
        $query = "INSERT INTO codigos (id_videojuego, codigo) VALUES (?, ?)";
        $stmt = $conex1->prepare($query);
        $stmt->bind_param("is", $id_videojuego, $codigo);
        $stmt->execute();

        // Actualizar el stock
        $query = "UPDATE videojuegos SET stock = stock + 1 WHERE id_videojuego = ?";
        $stmt = $conex1->prepare($query);
        $stmt->bind_param("i", $id_videojuego);
        $stmt->execute();

        // Commit de la transacción
        $conex1->commit();

        echo "Código añadido y stock actualizado correctamente.";
    } catch (Exception $e) {
        // Rollback en caso de error
        $conex1->rollback();
        echo "Error al añadir el código: " . $e->getMessage();
    }
}
?>
<a href="admin.php">Volver</a>
