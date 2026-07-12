<?php
session_start();
include("conexion.php");

if(isset($_POST['login'])){
	$user = $_POST['usuario'];
	$pass = $_POST['password'];

	$sql = "SELECT * FROM usuarios WHERE usuario='$user' AND password='$pass'";
	$resultado = mysqli_query($conexion, $sql);

	if(mysqli_num_rows($resultado) > 0){
		$datos = mysqli_fetch_assoc($resultado);
		$_SESSION['usuario'] = $datos['usuario'];
		$_SESSION['rol'] = $datos['rol'];
	} else {
		$error = "Usuario o contraseña incorrectos";
	}
}

if(isset($_GET['logout'])){
	session_destroy();
	header("Location: index.php");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
	<style>
		.producto-card {
			transition: transform 0.2s ease, box-shadow 0.2s ease;
		}

		.producto-card:hover {
			transform: scale(1.03);
			box-shadow: 0 10px 25px rgba(0,0,0,0.2);
		}
	</style>

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


	<meta charset="UTF-8">
	<title>Restaurante</title>
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

	<?php if(!isset($_SESSION['usuario'])){ ?>

		<h2>Iniciar sesión</h2>

		<?php if(isset($error)) echo "<p class='text-danger'>$error</p>"; ?>

		<form method="POST" action="login.php">
			<input type="text" name="usuario" placeholder="Usuario" class="form-control mb-2" required>
			<input type="password" name="password" placeholder="Contraseña" class="form-control mb-2" required>
			<button type="submit" class="btn btn-primary">Ingresar</button>
			<a href="register.php" class="btn btn-primary text-white">Crear cuenta</a>
		</form>

	<?php } else { ?>

		<br>

		<style>
			.contenido {
				background: rgba(255, 255, 255, 0.9);
				padding: 20px;
				border-radius: 15px;
			}
		</style>

		<div class="contenido mt-4">

			<section id="about" class="about">
				<div class="container-fluid">

					<div class="row">

						<div class="col-lg-5 align-items-stretch video-box" style='background-image: url("img/about.jpg");'>
							<a href="" class="venobox play-btn mb-4" data-vbtype="" data-autoplay="true"></a>
						</div>

						<div class="col-lg-7 d-flex flex-column justify-content-center align-items-stretch">

							<div class="content">
								<h3>¡Te damos la más cordial bienvenida a nuestro restaurante!</h3>
								<p>
									¡Bienvenidos al restaurante! En nuestro restaurante nos esforzamos por ofrecer un ambiente acogedor y agradable para nuestros clientes. Nuestro equipo está formado por expertos en gastronomía, que trabajan arduamente para crear platillos deliciosos y únicos que satisfagan tus papilas gustativas.
								</p>
								<p class="fst-italic">
									Somos un lugar acogedor y lleno de sabor, donde podrás disfrutar de la mejor gastronomía. Aquí te presento tres razones por las que deberías visitarnos:
								</p>
								<ul>
									<li><i class="bx bx-check-double"></i> Ofrecemos una amplia variedad de platillos, desde deliciosas entradas hasta especialidades exóticas, para satisfacer los gustos más exigentes.</li>
									<li><i class="bx bx-check-double"></i> Nuestro servicio es excepcional y nuestro equipo siempre está dispuesto a atender a nuestros clientes de la mejor manera posible.</li>
									<li><i class="bx bx-check-double"></i> Nos preocupamos por la calidad de nuestros ingredientes y siempre utilizamos los más frescos y naturales en nuestros platillos.</li>
								</ul>
								<p>
									Así que, si estás buscando un lugar para disfrutar de la mejor comida en un ambiente acogedor y agradable, no dudes en visitarnos. ¡Te aseguramos que será una experiencia inolvidable para ti y tus seres queridos!
								</p>
							</div>

						</div>

					</div>

				</div>
			</section>

			<br><br><br>

			<h2 class="text-center mb-4">Nuestro Menú</h2>

			<style>
				.producto-card {
					transition: transform 0.2s ease, box-shadow 0.2s ease;
					border-radius: 15px;
					overflow: hidden;
				}

				.producto-card:hover {
					transform: scale(1.03);
					box-shadow: 0px 8px 20px rgba(0,0,0,0.2);
				}

				.btn-agregar {
					font-weight: bold;
					border-radius: 10px;
				}

				.cantidad-input {
					max-width: 80px;
					text-align: center;
				}
			</style>

			<div class="row g-4">
				<?php
				$sql = "SELECT * FROM productos";
				$resultado = mysqli_query($conexion, $sql);

				while($fila = mysqli_fetch_assoc($resultado)) { ?>

					<div class="col-md-4 col-lg-3">
						<div class="card producto-card h-100">

							<img src="<?php echo $fila['imagen']; ?>" 
							class="card-img-top"
							style="height:200px; object-fit:cover;">

							<div class="card-body d-flex flex-column text-center">

								<h5 class="card-title"><?php echo $fila['nombre']; ?></h5>

								<p class="text-success fw-bold fs-4 mb-3">
									$<?php echo $fila['precio']; ?>
								</p>

								<form action="agregar_carrito.php" method="POST" class="mt-auto">
									<input type="hidden" name="id" value="<?php echo $fila['id']; ?>">

									<div class="d-flex justify-content-center align-items-center mb-2 gap-2">
										<input type="number" name="cantidad" value="1" min="1" 
										class="form-control cantidad-input">
									</div>

									<button class="btn btn-success w-100 btn-agregar">
										🛒 Agregar al carrito
									</button>
								</form>

							</div>
						</div>
					</div>

				<?php } ?>
			</div>

		<?php } ?>
	</div>
</div>
<br><br><br>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
	document.addEventListener('DOMContentLoaded', function () {
		const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
		[...tooltipTriggerList].map(el => new bootstrap.Tooltip(el));
	});
</script>
</body>
</html>
