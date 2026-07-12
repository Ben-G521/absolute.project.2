<?php
session_start();
include("conexion.php");

if(!isset($_SESSION['usuario'])){
    header("Location: index.php");
    exit();
}

$esAdmin = ($_SESSION['rol'] == "admin");
$usuario = $_SESSION['usuario'];
$usuario_id = $_SESSION['id'] ?? 0;


// Cerrar sesión
if(isset($_GET['logout'])){
    session_destroy();
    header("Location: index.php");
    exit();
}

// ==========================
// MARCAR COMO ENTREGADO
// ==========================
if(isset($_GET['entregar']) && $esAdmin){

    $id = (int)$_GET['entregar'];

    mysqli_query($conexion,"
        UPDATE pedidos
            SET estados='E'
            WHERE id=$id
            ");

    header("Location: mis_pedidos.php");
    exit();
}

// ==========================
// ANULAR PEDIDO
// ==========================

if(isset($_GET['cancelar'])){

    $id = (int)$_GET['cancelar'];

    if($esAdmin){

        mysqli_query($conexion,"
            UPDATE pedidos
                SET estados='AA'
                WHERE id=$id
                ");

    }else{

        mysqli_query($conexion,"
            UPDATE pedidos
                SET estados='AU'
                WHERE id=$id
                AND usuario_id=$usuario_id
                AND estados='P'
                ");

    }

    header("Location: mis_pedidos.php");
    exit();
}

// ==========================
// FILTROS
// ==========================
$estado = "P";
$orden = $_GET['orden'] ?? "DESC";
$buscarUsuario = $_GET['usuario'] ?? "";

// Lista de usuarios para el filtro
if($esAdmin){
    $usuarios = mysqli_query($conexion,"
        SELECT usuario
        FROM usuarios
        ORDER BY usuario
        ");
}

// Consulta base
$sql = "
SELECT
p.id,
p.total,
p.fecha,
p.estados,
u.usuario
FROM pedidos p
INNER JOIN usuarios u
ON p.usuario_id = u.id
WHERE p.estados='P'
";

// Usuario normal
if(!$esAdmin){
    $sql .= " AND p.usuario_id = $usuario_id";
}

// Filtro por usuario
if($esAdmin && !empty($buscarUsuario)){
    $sql .= " AND u.usuario = '$buscarUsuario'";
}



// Orden
$sql .= " ORDER BY p.fecha $orden";

$pedidos = mysqli_query($conexion, $sql);

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
    <title><?php echo $esAdmin ? "Panel de Pedidos" : "Mis Pedidos"; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

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
    <h2><?php echo $esAdmin ? "Panel de Pedidos" : "Mis Pedidos"; ?></h2>




    <form method="GET" class="mb-3 d-flex align-items-center gap-2">



        <select name="orden" class="form-select" style="width:200px;">
            <option value="DESC" <?php if($orden=="DESC") echo "selected"; ?>>Más nuevos</option>
            <option value="ASC" <?php if($orden=="ASC") echo "selected"; ?>>Más viejos</option>
        </select>

        <?php if($esAdmin){ ?>

            <select name="usuario" class="form-select" style="width:220px;">
                <option value="">Todos los usuarios</option>

                <?php while($u=mysqli_fetch_assoc($usuarios)){ ?>

                    <option value="<?php echo $u['usuario']; ?>"
                        <?php if($buscarUsuario==$u['usuario']) echo "selected"; ?>>
                        <?php echo $u['usuario']; ?>
                    </option>

                <?php } ?>

            </select>

        <?php } ?>

        <button class="btn btn-primary">
            Filtrar
        </button>

        <a href="historial_pedidos.php" 
        class="btn btn-secondary ms-auto"
        data-bs-toggle="tooltip"
        data-bs-placement="bottom"
        title="Ver el historial de pedidos entregados y cancelados">
        Historial
    </a>

</form>

<?php if(mysqli_num_rows($pedidos)==0){ ?>

    <p>No hay pedidos.</p>

<?php }else{ ?>

    <?php while($pedido=mysqli_fetch_assoc($pedidos)){ ?>

        <div class="card mb-3">

            <div class="card-body">

                <h5>

                    Orden:
                    <?php echo "#PED-" . str_pad($pedido['id'], 5, "0", STR_PAD_LEFT); ?>

                </h5>

                <?php if($esAdmin){ ?>

                    <p>

                        <strong>Usuario:</strong>

                        <?php echo $pedido['usuario']; ?>

                    </p>

                <?php } ?>

                <p>

                    <strong>Fecha:</strong>

                    <?php echo $pedido['fecha']; ?>

                </p>

                <p>

                    <strong>Total:</strong>

                    $<?php echo $pedido['total']; ?>

                </p>

                <p>

                    <strong>Estado:</strong>

                    <?php
                    echo "<span class='badge bg-warning text-dark'>
                    Pendiente
                    </span>";

                    ?>

                </p>

                <h6>Productos</h6>

                <ul class="list-group">

                    <?php

                    $id_pedido=$pedido['id'];

                    $detalles=mysqli_query($conexion,"
                        SELECT
                        dp.cantidad,
                        dp.precio,
                        pr.nombre
                        FROM detalle_pedidos dp
                        INNER JOIN productos pr
                        ON dp.producto_id=pr.id
                        WHERE dp.pedido_id=$id_pedido
                        ");

                    while($item=mysqli_fetch_assoc($detalles)){

                        ?>

                        <li class="list-group-item d-flex justify-content-between">

                            <?php echo $item['nombre']; ?>

                            x<?php echo $item['cantidad']; ?>

                            <span>

                                $<?php echo $item['precio']*$item['cantidad']; ?>

                            </span>

                        </li>

                    <?php } ?>

                </ul>

                <?php if($pedido['estados']=="P"){ ?>

                    <a href="mis_pedidos.php?cancelar=<?php echo $pedido['id']; ?>"
                        class="btn btn-danger mt-2"
                        data-bs-toggle="tooltip"
                        data-bs-placement="top"
                        title="Cancelar este pedido antes de que sea procesado"
                        onclick="return confirm('¿Anular este pedido?')">

                        <?php echo $esAdmin ? "Anular pedido" : "Cancelar pedido"; ?>

                    </a>

                <?php } ?>

                <?php if($esAdmin && $pedido['estados']=="P"){ ?>

                    <a href="mis_pedidos.php?entregar=<?php echo $pedido['id']; ?>"
                        class="btn btn-success mt-2"
                        onclick="return confirm('¿Marcar como entregado?')">

                        Marcar como entregado

                    </a>

                <?php } ?>

            </div>

        </div>

    <?php } ?>

<?php } ?>


<a href="index.php" 
class="btn btn-info me-2"
data-bs-toggle="tooltip"
data-bs-placement="bottom"
title="Consultar el estado e historial de tus pedidos">
Volver al inicio
</a>
</div>
<br>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        [...tooltipTriggerList].map(el => new bootstrap.Tooltip(el));
    });
</script>

</body>
</html>