<?php
session_start();
include 'BBDD.php';

if(!isset($_SESSION['id_usuario'])){
    echo json_encode(['success'=>false,'message'=>'Tienes que iniciar la sesión']);
    exit;
}

$data=json_decode(file_get_contents('php://input'),true);
$id=$data['id'];

$query = "DELETE FROM carrito WHERE id = ?";
$stmt = $conex1->prepare($query);
$stmt->bind_param("i",$id);
$stmt->execute();

$response=['success'=>$stmt->affected_rows>0];
echo json_encode($response);
?>
