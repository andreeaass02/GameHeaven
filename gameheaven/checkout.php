<?php
session_start();
require 'BBDD.php';
//require 'vendor/autoload.php'; // Incluye el autoload de Composer

//use PHPMailer\PHPMailer\PHPMailer;
//use PHPMailer\PHPMailer\Exception;

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit;
}

$id_usuario = $_SESSION['id_usuario'];

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

// Aquí va PayPal

$payment_successful = true;

if ($payment_successful) {
    // Iniciar una transacción
    $conex1->begin_transaction();
    try {
        // Guardar el pedido en la base de datos
        $query = "INSERT INTO pedidos (id_usuario, fecha_pedido, total) VALUES (?, NOW(), ?)";
        $stmt = $conex1->prepare($query);
        $stmt->bind_param("id", $id_usuario, $total);
        $stmt->execute();
        $id_pedido = $stmt->insert_id;

        // Guardar los detalles del pedido y obtener los códigos de los juegos
        foreach ($items as $item) {
            for ($i = 0; $i < $item['cantidad']; $i++) {
                // Obtener un código disponible
                $query = "SELECT id_codigo, codigo FROM codigos WHERE id_videojuego = ? LIMIT 1";
                $stmt = $conex1->prepare($query);
                $stmt->bind_param("i", $item['id_videojuego']);
                $stmt->execute();
                $codigo_result = $stmt->get_result();
                $codigo = $codigo_result->fetch_assoc();

                // Insertar detalle del pedido
                $query = "INSERT INTO detalles_pedido (id_pedido, id_codigo, cantidad, precio) VALUES (?, ?, 1, ?)";
                $stmt = $conex1->prepare($query);
                $stmt->bind_param("iid", $id_pedido, $codigo['id_codigo'], $item['precio']);
                $stmt->execute();

                /*
                // Enviar correo al usuario con el código del juego
                $to = $_SESSION['email'];
                $subject = "Compra realizada con éxito";
                $message = "Gracias por tu compra. Aquí está tu código del juego: " . $codigo['codigo'];
                $headers = "From: no-reply@tuweb.com";

                $mail = new PHPMailer(true);
                try {
                    // Configuración del servidor SMTP
                    $mail->isSMTP();
                    $mail->Host = 'smtp.tu-servidor.com'; // Servidor SMTP de tu proveedor
                    $mail->SMTPAuth = true;
                    $mail->Username = 'tu-email@tu-dominio.com'; // Tu correo
                    $mail->Password = 'tu-contraseña'; // Tu contraseña
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587; // Puerto SMTP

                    // Remitente y destinatario
                    $mail->setFrom('no-reply@tuweb.com', 'GameHeaven');
                    $mail->addAddress($to);

                    // Contenido del correo
                    $mail->isHTML(true);
                    $mail->Subject = $subject;
                    $mail->Body    = $message;

                    $mail->send();
                } catch (Exception $e) {
                    echo "El mensaje no pudo ser enviado. Error de Mailer: {$mail->ErrorInfo}";
                }
                */

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

        echo "Compra finalizada con éxito.";
        echo "<a href='index.php'>Inicio</a>";
    } catch (Exception $e) {
        // Rollback en caso de error
        $conex1->rollback();
        echo "Error en el proceso de compra. Por favor, inténtalo de nuevo.";
        echo "<a href='index.php'>Inicio</a>";
    }
}
?>
