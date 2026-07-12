<?php
session_start();
include("conexion.php");

$id = $_POST['id'];
$cantidad = $_POST['cantidad'];

$sql = "SELECT * FROM productos WHERE id = $id";
$resultado = mysqli_query($conexion, $sql);
$producto = mysqli_fetch_assoc($resultado);

if(!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

$_SESSION['carrito'][$id] = [
    "id"=>$producto['id'],
    "nombre"=>$producto['nombre'],
    "precio"=>$producto['precio'],
    "cantidad"=>$cantidad
];


header("Location: index.php");
exit();