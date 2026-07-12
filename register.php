<?php
session_start();
include("conexion.php");

if(isset($_POST['registrar'])){
    $usuario = $_POST['usuario'];
    $password = $_POST['password'];

    $check = "SELECT * FROM usuarios WHERE usuario='$usuario'";
    $resultado = mysqli_query($conexion, $check);

    if(mysqli_num_rows($resultado) > 0){
        $error = "El usuario ya existe";
    } else {
        $sql = "INSERT INTO usuarios (usuario, password, rol) 
        VALUES ('$usuario', '$password', 'usuario')";

        mysqli_query($conexion, $sql);

        $mensaje = "Cuenta creada correctamente";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Registro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Crear cuenta</h2>

        <?php if(isset($error)) echo "<p class='text-danger'>$error</p>"; ?>
        <?php if(isset($mensaje)) echo "<p class='text-success'>$mensaje</p>"; ?>

        <form method="POST">
            <input type="text" name="usuario" placeholder="Usuario" class="form-control mb-2" required>
            <input type="password" name="password" placeholder="Contraseña" class="form-control mb-2" required>
            <button name="registrar" class="btn btn-success w-100">Registrarse</button>
        </form>

        <a href="index.php" class="btn btn-link mt-3">Volver al login</a>
    </div>
</body>
</html>