<?php
session_start();
include("conexion.php");

$user = $_POST['usuario'];
$pass = $_POST['password'];

$sql = "SELECT * FROM usuarios WHERE usuario='$user' AND password='$pass'";
$resultado = mysqli_query($conexion, $sql);

if(mysqli_num_rows($resultado) > 0){

    $datos = mysqli_fetch_assoc($resultado);

    $_SESSION['id'] = $datos['id'];
    $_SESSION['usuario'] = $datos['usuario'];
    $_SESSION['rol'] = $datos['rol'];

    if($datos['rol'] == 'admin'){
        header("Location: admin.php");
    }else{
        header("Location: index.php");
    }

    exit();

}else{

    echo "Error de login";

}