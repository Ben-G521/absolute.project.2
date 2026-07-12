<?php
session_start();


include("conexion.php");

if(!isset($_SESSION['usuario'])){
    header("Location: index.php");
    exit();
}

if(isset($_GET['logout'])){
    session_destroy();
    header("Location: index.php");
}

$ticket = false;
$carrito_ticket = [];

if(isset($_POST['comprar']) && !empty($_SESSION['carrito'])){
    $ticket = true;
    $carrito_ticket = $_SESSION['carrito'];

    $usuario_id = $_SESSION['id'];
    $total_ticket = 0;

    foreach($carrito_ticket as $item){
        $precio = $item['precio'] ?? 0;
        $total_ticket += $precio * $item['cantidad'];
    }

    mysqli_query($conexion,"INSERT INTO pedidos (usuario_id, total, estados) VALUES ($usuario_id, '$total_ticket', 'P')");
    $pedido_id = mysqli_insert_id($conexion);

    foreach($carrito_ticket as $item){
        $producto_id = $item['id'];
        $precio = $item['precio'];
        $cantidad = $item['cantidad'];

        mysqli_query($conexion,"INSERT INTO detalle_pedidos (pedido_id, producto_id, precio, cantidad) VALUES ($pedido_id, $producto_id, '$precio', '$cantidad')
            ");
    }

    $_SESSION['carrito'] = [];
}

$carrito = $_SESSION['carrito'] ?? [];

if(isset($_POST['actualizar'])){
    $id = $_POST['id'];
    $cantidad = $_POST['cantidad'];

    if($cantidad > 0){
        $_SESSION['carrito'][$id]['cantidad'] = $cantidad;
    }

    header("Location: carrito.php");
    exit();
}

$total = 0;
?>

<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            background-image: url('img/fondo.jpg');
            background-size: cover;        /* ocupa toda la pantalla */
            background-position: center;   /* centrada */
            background-repeat: no-repeat;  /* no se repite */
            background-attachment: fixed; /* efecto fijo (más moderno) */
        }
    </style>
    <style>
        .contenido {
            background: rgba(255, 255, 255, 0.9);
            padding: 20px;
            border-radius: 15px;
        }
    </style>
    <title>Carrito</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<nav class="navbar navbar-dark bg-dark">
    <div class="container d-flex justify-content-between">

        <span class="navbar-brand" 
        data-bs-toggle="tooltip" 
        data-bs-placement="bottom" 
        title="Nombre del restaurante">
        Mi Restaurante
    </span>

    <?php if(isset($_SESSION['usuario'])){ ?>
        <div>

            <a href="index.php" 
            class="btn btn-primary me-2"
            data-bs-toggle="tooltip"
            data-bs-placement="bottom"
            title="Volver a la página principal">
            Inicio
        </a>


        <a href="carrito.php" 
        class="btn btn-warning me-2"
        data-bs-toggle="tooltip"
        data-bs-placement="bottom"
        title="Ver los productos que agregaste al carrito">
        Carrito
    </a>


    <?php if($_SESSION['rol'] == 'admin'){ ?>

        <a href="mis_pedidos.php" 
        class="btn btn-info me-2"
        data-bs-toggle="tooltip"
        data-bs-placement="bottom"
        title="Administrar y consultar todos los pedidos">
        Panel de Pedidos
    </a>

<?php } else { ?>

    <a href="mis_pedidos.php" 
    class="btn btn-info me-2"
    data-bs-toggle="tooltip"
    data-bs-placement="bottom"
    title="Consultar el estado e historial de tus pedidos">
    Mis pedidos
</a>

<?php } ?>


<?php if($_SESSION['rol'] == 'admin'){ ?>

    <a href="admin.php" 
    class="btn btn-info me-2"
    data-bs-toggle="tooltip"
    data-bs-placement="bottom"
    title="Acceder al panel de administración">
    Panel Admin
</a>

<?php } ?>


<span class="text-white me-2"
data-bs-toggle="tooltip"
data-bs-placement="bottom"
title="Usuario actualmente conectado">
Hola, <?php echo $_SESSION['usuario']; ?>
</span>


<a href="?logout=true" 
class="btn btn-danger"
data-bs-toggle="tooltip"
data-bs-placement="bottom"
title="Cerrar tu sesión actual">
Cerrar sesión
</a>

</div>
<?php } ?>

</div>
</nav>
<div class="container mt-4 contenido">
    <h2>Carrito de Compras</h2>
    <?php if($ticket){ ?>
        <div class="card mt-4">
            <div class="card-body">
                <h3>🧾 Ticket de compra</h3>

                <div class="alert alert-secondary">
                    <strong>N° de Pedido:</strong>
                    <?php echo "P-".str_pad($pedido_id, 6, "0", STR_PAD_LEFT); ?><br>

                    <strong>Cliente:</strong>
                    <?php echo $_SESSION['usuario']; ?><br>

                    <strong>Fecha:</strong>
                    <?php echo date("d/m/Y H:i"); ?>
                </div>


                <ul class="list-group mb-3">
                    <?php 
                    foreach($carrito_ticket as $item){
                        $precio = $item['precio'] ?? 0;
                        $subtotal = $precio * $item['cantidad'];
                        ?>
                        <li class="list-group-item d-flex justify-content-between">
                            <?php echo $item['nombre']; ?> x<?php echo $item['cantidad']; ?>
                            <span>$<?php echo $subtotal; ?></span>
                        </li>
                    <?php } ?>
                </ul>

                <h4>Total a pagar: $<?php echo $total_ticket; ?></h4>

                <a href="https://link.mercadopago.com.ar/absolutehorastp" target="_blank" rel="noopener noreferrer" class="text-decoration-none">
                    <div class="alert alert-info mt-3">
                        Pagar aquí
                        <br>
                        <strong>Ir a Mercado Pago</strong>
                    </div>
                </a>

                <a href="index.php" class="btn btn-info me-2" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Consultar el estado e historial de tus pedidos"> Volver al inicio</a>
                <a href="mis_pedidos.php" class="btn btn-info me-2" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Consultar el estado e historial de tus pedidos">Ir a tus pedidos</a>

            </div>
        </div>
    <?php } ?>

    <?php if(empty($carrito)){ ?>
        <p>No hay productos en el carrito</p>
    <?php } else { ?>

        <table class="table">
            <tr>
                <th>Producto</th>
                <th>Precio</th>
                <th>Cantidad</th>
                <th>Subtotal</th>
                <th>Acción</th>
            </tr>

            <?php foreach($carrito as $id => $item) { 
                $precio = $item['precio'] ?? 0;
                $subtotal = $precio * $item['cantidad'];
                $total += $subtotal;
                ?>
                <tr>
                    <td><?php echo $item['nombre']; ?></td>

                    <td>$<?php echo $precio; ?></td>

                    <td>
                        <form method="POST" class="d-flex">
                            <input type="hidden" name="id" value="<?php echo $id; ?>">

                            <input type="number" name="cantidad" value="<?php echo $item['cantidad']; ?>" min="1" class="form-control"onchange="this.form.submit()">

                            <input type="hidden" name="actualizar" value="1">
                        </form>
                    </td>

                    <td>$<?php echo $subtotal; ?></td>

                    <td>
                        <a href="eliminar.php?id=<?php echo $producto['id']; ?>" class="btn btn-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="Eliminar este producto del carrito">Eliminar</a>
                    </td>
                </tr>
            <?php } ?>

        </table>

        <h4>Total: $<?php echo $total; ?></h4>

        <form method="POST">


            <button name="comprar" type="submit" 
            class="btn btn-success"
            data-bs-toggle="tooltip"
            data-bs-placement="top"
            title="Agregar este producto al carrito">
            Comprar
        </button>
    </form>
<?php } ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        [...tooltipTriggerList].map(el => new bootstrap.Tooltip(el));
    });
</script>

</body>
</html>