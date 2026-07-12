<?php
session_start();
include("conexion.php");

if(!isset($_SESSION['usuario']) || $_SESSION['rol'] != 'admin'){
    die("Acceso denegado");
}

if(isset($_GET['id'])){
    $id = $_GET['id'];

    $sql = "DELETE FROM productos WHERE id = $id";
    mysqli_query($conexion, $sql);
}

header("Location: admin.php");
?>