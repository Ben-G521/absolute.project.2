<?php
session_start();

if(isset($_GET['logout'])){
    session_destroy();
    header("Location: index.php");
    exit();
}

include("conexion.php");

if(!isset($_SESSION['usuario']) || $_SESSION['rol'] != 'admin'){
    die("Acceso denegado");
}

$resultado = mysqli_query($conexion, "SELECT * FROM productos");
$usuarios = mysqli_query($conexion,"
    SELECT usuario
    FROM usuarios
    ORDER BY usuario
    ");

$usuarioFiltro = $_GET['usuario'] ?? "";
$orden = $_GET['orden'] ?? "fecha_desc";
$desde = $_GET['desde'] ?? "";
$hasta = $_GET['hasta'] ?? "";

// Obtener todos los pedidos pagados junto con el nombre del usuario
$sql = "
SELECT
p.id,
u.usuario,
p.total,
p.fecha
FROM pedidos p
INNER JOIN usuarios u
ON p.usuario_id=u.id
WHERE p.estados='E'
";

if($usuarioFiltro!=""){
    $sql .= " AND u.usuario='$usuarioFiltro'";
}

if($desde!=""){
    $sql .= " AND DATE(p.fecha)>='$desde'";
}

if($hasta!=""){
    $sql .= " AND DATE(p.fecha)<='$hasta'";
}

switch($orden){

    case "fecha_asc":
    $sql.=" ORDER BY p.fecha ASC";
    break;

    case "total_desc":
    $sql.=" ORDER BY p.total DESC";
    break;

    case "total_asc":
    $sql.=" ORDER BY p.total ASC";
    break;

    default:
    $sql.=" ORDER BY p.fecha DESC";
}


$ventas = mysqli_query($conexion, $sql);

if(!$ventas){
    die("Error en consulta de ventas: " . mysqli_error($conexion));
}

$total_general = 0;
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
    <title>Panel de Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

    <nav class="navbar navbar-dark bg-dark">
        <div class="container d-flex justify-content-between">
            <span class="navbar-brand">Mi Restaurante</span>

            <?php if(isset($_SESSION['usuario'])){ ?>
                <div>
                    <a href="index.php" class="btn btn-primary me-2">Inicio</a>
                    <a href="carrito.php" class="btn btn-warning me-2">Carrito</a>
                    <a href="mis_pedidos.php" class="btn btn-info me-2">
                        <?php echo ($_SESSION['rol'] == 'admin') ? 'Panel de Pedidos' : 'Mis pedidos'; ?>
                    </a>



                    
                    <?php if($_SESSION['rol'] == 'admin'){ ?>
                        <a href="admin.php" class="btn btn-info me-2">Panel Admin</a>
                    <?php } ?>
                    <span class="text-white me-2">Hola, <?php echo $_SESSION['usuario']; ?></span>
                    <a href="?logout=true" class="btn btn-danger">Cerrar sesión</a>
                </div>
            <?php } ?>
        </div>
    </nav>

    <div class="container mt-4 contenido">
        <h2>Gestión de Productos</h2>
        <a href="agregar_producto.php" class="btn btn-success mb-3">Agregar producto</a>
        <table class="table table-bordered">
            <tr>
                <th>Producto</th>
                <th>Acciones</th>
            </tr>

            <?php while($fila = mysqli_fetch_assoc($resultado)){ ?>
                <tr>
                    <td><?php echo $fila['nombre']; ?></td>
                    <td>
                        <a href="eliminar_producto.php?id=<?php echo $fila['id']; ?>" class="btn btn-danger btn-sm">
                            Eliminar
                        </a>
                    </td>
                </tr>
            <?php } ?>
        </table>
    </div>


    <div class="container mt-4 contenido">

        <h2>Ventas realizadas</h2>

        <form method="GET" class="row g-2 mb-4">

            <div class="col-md-3">
                <select name="usuario" class="form-select">

                    <option value="">Todos los usuarios</option>

                    <?php while($u=mysqli_fetch_assoc($usuarios)){ ?>

                        <option value="<?php echo $u['usuario']; ?>"
                            <?php if($usuarioFiltro==$u['usuario']) echo "selected"; ?>>
                            <?php echo $u['usuario']; ?>
                        </option>

                    <?php } ?>

                </select>
            </div>


            <div class="col-md-3">

                <select name="orden" class="form-select">

                    <option value="fecha_desc" <?php if($orden=="fecha_desc") echo "selected"; ?>>Más recientes</option>

                    <option value="fecha_asc" <?php if($orden=="fecha_asc") echo "selected"; ?>>Más antiguas</option>

                    <option value="total_desc" <?php if($orden=="total_desc") echo "selected"; ?>>Mayor ganancia</option>

                    <option value="total_asc" <?php if($orden=="total_asc") echo "selected"; ?>>Menor ganancia</option>

                </select>

            </div>

            <div class="col-md-2">
                <button class="btn btn-primary w-100"> Filtrar </button>

            </div>

        </form>

        <?php if(mysqli_num_rows($ventas)==0){ ?>

            <div class="alert alert-warning">
                No hay ventas registradas.
            </div>

        <?php }else{ ?>

            <table class="table table-bordered table-striped">

                <thead class="table-dark">
                    <tr>
                        <th>Código de pedido</th>
                        <th>Cliente</th>
                        <th>Productos</th>
                        <th>Total</th>
                        <th>Fecha</th>
                    </tr>
                </thead>

                <tbody>

                    <?php while($v=mysqli_fetch_assoc($ventas)){

                        $total_general += $v['total'];

                        ?>
                        <?php

                        $productos = mysqli_query($conexion,"
                            SELECT
                            pr.nombre,
                            dp.cantidad
                            FROM detalle_pedidos dp
                            INNER JOIN productos pr
                            ON dp.producto_id = pr.id
                            WHERE dp.pedido_id = ".$v['id']."
                            ");

                            ?>


                            <tr>

                                <td>
                                    <?php echo "#PED-" . str_pad($v['id'], 5, "0", STR_PAD_LEFT); ?>
                                </td>

                                <td>
                                    <?php echo $v['usuario']; ?>
                                </td>

                                <td>

                                    <ul>

                                        <?php while($p=mysqli_fetch_assoc($productos)){ ?>

                                            <li>
                                                <?php echo $p['nombre']; ?> 
                                                x<?php echo $p['cantidad']; ?>
                                            </li>

                                        <?php } ?>

                                    </ul>

                                </td>

                                <td>$<?php echo number_format($v['total'],2); ?></td>

                                <td>
                                    <?php echo $v['fecha']; ?>
                                </td>

                            </tr>
                        <?php } ?>

                    </tbody>

                </table>

                <div class="alert alert-success">
                    <h4>Total recaudado: $<?php echo number_format($total_general,2); ?></h4>
                </div>

            <?php } ?>

        </div>


    </body>
    </html>