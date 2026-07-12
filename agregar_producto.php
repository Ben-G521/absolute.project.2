<?php
if(isset($_GET['logout'])){
    session_destroy();
    header("Location: index.php");
    exit();
}session_start();

include("conexion.php");

if(!isset($_SESSION['usuario']) || $_SESSION['rol'] != 'admin'){
    die("Acceso denegado");
}

if(isset($_POST['guardar'])){
    $nombre = $_POST['nombre'];
    $precio = $_POST['precio'];
    $imagen = $_POST['imagen'];

    $sql = "INSERT INTO productos (nombre, precio, imagen) 
    VALUES ('$nombre', '$precio', '$imagen')";

    mysqli_query($conexion, $sql);

    header("Location: admin.php");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Agregar Producto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

    <nav class="navbar navbar-dark bg-dark">
        <div class="container d-flex justify-content-between">
            <span class="navbar-brand">Agregar Producto</span>

            <div>
                <a href="index.php" class="btn btn-primary me-2">Inicio</a>
                <a href="admin.php" class="btn btn-secondary me-2">Panel Admin</a>
                <a href="mis_pedidos.php" class="btn btn-info me-2">Pedidos</a>
                <a href="?logout=true" class="btn btn-danger">Cerrar sesión</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h2>Agregar Producto</h2>
        
        <form method="POST" class="card p-4 shadow-sm">
            <div class="mb-3">
                <label class="form-label">Nombre</label>
                <input type="text" name="nombre" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Precio</label>
                <input type="number" name="precio" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Imagen (URL o ruta)</label>
                <input type="text" name="imagen" class="form-control">
            </div>

            <button name="guardar" class="btn btn-success">Guardar producto</button>
        </form>
    </div>
</body>
</html>