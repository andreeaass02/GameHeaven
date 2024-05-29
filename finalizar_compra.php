<?php
session_start();
require 'BBDD.php';
require 'fpdf.php';

header('Content-Type: application/json');

if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(['success' => false, 'message' => 'Debes iniciar sesión']);
    exit;
}

$id_usuario = $_SESSION['id_usuario'];

try {
    // Obtener el carrito del usuario
    $query = "SELECT c.id, v.id_videojuego, v.precio, c.cantidad 
              FROM carrito c 
              JOIN videojuegos v ON c.id_videojuego = v.id_videojuego 
              WHERE c.id_usuario = ?";
    $stmt = $conex1->prepare($query);
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $result = $stmt->get_result();

    // Calcular el total
    $total = 0;
    $items = [];
    while ($row = $result->fetch_assoc()) {
        $total += $row['precio'] * $row['cantidad'];
        $items[] = $row;
    }

    // Iniciar una transacción
    $conex1->begin_transaction();

    // Guardar el pedido en la base de datos
    $query = "INSERT INTO pedidos (id_usuario, fecha_pedido, total) VALUES (?, NOW(), ?)";
    $stmt = $conex1->prepare($query);
    $stmt->bind_param("id", $id_usuario, $total);
    $stmt->execute();
    $id_pedido = $stmt->insert_id;

    $codes = []; // Array para almacenar los códigos comprados

    // Guardar los detalles del pedido y obtener los códigos de los juegos
    foreach ($items as $item) {
        // Verificar stock disponible antes de procesar la compra
        $query = "SELECT stock FROM videojuegos WHERE id_videojuego = ?";
        $stmt = $conex1->prepare($query);
        $stmt->bind_param("i", $item['id_videojuego']);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stock = $row['stock'];

        if ($stock < $item['cantidad']) {
            throw new Exception('No hay suficiente stock para completar la compra.');
        }

        for ($i = 0; $i < $item['cantidad']; $i++) {
            // Obtener un código disponible
            $query = "SELECT id_codigo, codigo FROM codigos WHERE id_videojuego = ? LIMIT 1";
            $stmt = $conex1->prepare($query);
            $stmt->bind_param("i", $item['id_videojuego']);
            $stmt->execute();
            $codigo_result = $stmt->get_result();
            $codigo = $codigo_result->fetch_assoc();

            if (!$codigo) {
                throw new Exception('No hay códigos disponibles para completar la compra.');
            }

            // Insertar detalle del pedido
            $query = "INSERT INTO detalles_pedido (id_pedido, id_codigo, cantidad, precio) VALUES (?, ?, 1, ?)";
            $stmt = $conex1->prepare($query);
            $stmt->bind_param("iid", $id_pedido, $codigo['id_codigo'], $item['precio']);
            $stmt->execute();

            // Almacenar el código en el array
            $codes[] = $codigo['codigo'];

            // Eliminar el código usado
            $query = "DELETE FROM codigos WHERE id_codigo = ?";
            $stmt = $conex1->prepare($query);
            $stmt->bind_param("i", $codigo['id_codigo']);
            $stmt->execute();
        }

        // Actualizar el stock del juego
        $query = "UPDATE videojuegos SET stock = stock - ? WHERE id_videojuego = ?";
        $stmt = $conex1->prepare($query);
        $stmt->bind_param("ii", $item['cantidad'], $item['id_videojuego']);
        $stmt->execute();
    }

    // Vaciar el carrito
    $query = "DELETE FROM carrito WHERE id_usuario = ?";
    $stmt = $conex1->prepare($query);
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();

    // Commit de la transacción
    $conex1->commit();

    // Generar el PDF
    class PDF extends FPDF
    {
        function Header()
        {
            $this->SetFont('Arial', 'B', 12);
            $this->Cell(0, 10, 'Codigos de Compra', 0, 1, 'C');
            $this->Ln(10);
        }

        function Footer()
        {
            $this->SetY(-15);
            $this->SetFont('Arial', 'I', 8);
            $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
        }

        function ChapterTitle($num, $label)
        {
            $this->SetFont('Arial', 'B', 12);
            $this->Cell(0, 10, "Codigo $num: $label", 0, 1);
        }
    }

    $pdf = new PDF();
    $pdf->AddPage();

    foreach ($codes as $index => $code) {
        $pdf->ChapterTitle($index + 1, $code);
    }

    $file_name = "codigos_compra_{$id_usuario}.pdf";
    $pdf->Output('F', $file_name);

    // Enviar el nombre del archivo PDF generado al cliente
    echo json_encode(['success' => true, 'pdf' => $file_name]);
} catch (Exception $e) {
    // Rollback en caso de error
    $conex1->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
