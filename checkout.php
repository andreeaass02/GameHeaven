<?php
session_start();
require 'BBDD.php';

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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <script src="https://www.paypal.com/sdk/js?client-id=AW50Svb8QaZqpw0F9DhNd2YM4saaE6brjb5DYpIHK-yRMdTwA90SDkHtRL1jItUEpD6JDFrxJvLzmYmK&currency=USD"></script>
    <script>
        function handlePayment() {
            paypal.Buttons({
                createOrder: function(data, actions) {
                    return actions.order.create({
                        purchase_units: [{
                            amount: {
                                value: '<?= $total ?>' // Usar el total calculado
                            }
                        }]
                    });
                },
                onApprove: function(data, actions) {
                    return actions.order.capture().then(function(details) {
                        // Llamar a la función para completar la compra en el servidor
                        fetch('finalizar_compra.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({ orderID: data.orderID })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert('Compra completada con éxito');
                                window.location.href = 'confirmation.php?pdf=' + encodeURIComponent(data.pdf);
                            } else {
                                alert('Error al completar la compra');
                            }
                        });
                    });
                }
            }).render('#paypal-button-container');
        }
    </script>
</head>
<body onload="handlePayment()">
    <h1>Checkout</h1>
    <p>Total: $<?= number_format($total, 2) ?></p>
    <div id="paypal-button-container"></div>
</body>
</html>

