<?php
session_start();
include 'BBDD.php';

if(!isset($_SESSION['id_usuario'])){
    echo json_encode(['success'=>false,'message' =>'Tienes que iniciar la sesión']);
    exit;
}

$data=json_decode(file_get_contents('php://input'),true);
$id=$data['id'];
$action=$data['action'];

if($action==='increase'){
    $query="UPDATE carrito SET cantidad=cantidad+1 WHERE id=?";
}elseif($action==='decrease'){
    $query="UPDATE carrito SET cantidad=cantidad-1 WHERE id=? AND cantidad>1";
}else{
    echo json_encode(['success'=>false,'message'=>'No es valido']);
    exit;
}

$stmt=$conex1->prepare($query);
$stmt->bind_param("i",$id);
$stmt->execute();

$response=['success'=>$stmt->affected_rows>0];
echo json_encode($response);
?>
