<?php
session_start();
include 'BBDD.php';
include 'header.php';

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login/login.php");
    exit;
}

$id_usuario = $_SESSION['id_usuario'];

$query = "SELECT c.id, v.nombre, v.precio, c.cantidad 
          FROM carrito c 
          JOIN videojuegos v ON c.id_videojuego = v.id_videojuego 
          WHERE c.id_usuario = ?";
$stmt = $conex1->prepare($query);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();

$total = 0;
?>
<link rel="stylesheet" href="styles/carrito.css">
<h2 class="cart-title">Carrito de Compras</h2>
<table class="cart-table">
    <tr class="cart-header">
        <th class="cart-header-item">Producto</th>
        <th class="cart-header-item">Precio</th>
        <th class="cart-header-item">Cantidad</th>
        <th class="cart-header-item">Total</th>
        <th class="cart-header-item">Acciones</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()) : ?>
    <tr class="cart-row">
        <td class="cart-item"><?= $row['nombre'] ?></td>
        <td class="cart-price"><?= $row['precio'] ?></td>
        <td class="cart-quantity"><?= $row['cantidad'] ?></td>
        <td class="cart-total"><?= $row['precio'] * $row['cantidad'] ?></td>
        <td class="cart-actions">
            <button class="cart-button" onclick="actualizarCarrito(<?= $row['id'] ?>, 'increase')">+</button>
            <button class="cart-button" onclick="actualizarCarrito(<?= $row['id'] ?>, 'decrease')">-</button>
            <button class="cart-button" onclick="borrarDelCarrito(<?= $row['id'] ?>)">Eliminar</button>
        </td>
    </tr>
    <?php $total += $row['precio'] * $row['cantidad']; endwhile; ?>
</table>
<p class="cart-total-price">Total: <?= $total ?></p>
<div class="checkout-container">
    <a class="checkout-button" href="checkout.php">Finalizar Compra</a>
</div>



<script>
function actualizarCarrito(id, action) {
    fetch('actualizar_carrito.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ id: id, action: action })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error al actualizar el carrito');
        }
    });
}

function borrarDelCarrito(id) {
    fetch('borrar_carrito.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ id: id })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error al eliminar el producto');
        }
    });
}
</script>
</body>
</html>
