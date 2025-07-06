<?php
session_start();
if (!isset($_SESSION['user_login_status']) || $_SESSION['user_login_status'] != 1) {
	header("location: login.php");
	exit;
}

require_once("config/db.php"); // Config DB
require_once("config/conexion.php"); // Conexión a DB

$active_listado = "active";
$title = "Inventario | Simple Stock";
?>
<!DOCTYPE html>
<html lang="es">

<head>
	<meta charset="UTF-8">
	<title><?php echo $title; ?></title>
	<?php include("head.php"); ?>


	<!-- jQuery -->
	<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

	<!-- DataTables CSS -->
	<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

	<!-- DataTables JS -->
	<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

	<!-- Estilos opcionales -->
	<style>
		table.dataTable {
			width: 100% !important;
		}

		.contenedor {
			padding: 0 40px;
			/* Margen interno a ambos lados */
		}
	</style>

	<!-- Inicialización de DataTables -->
	<script>
		$(document).ready(function() {
			$('#miTabla').DataTable({
				language: {
					url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
				}
			});
		});
	</script>
</head>

<body>

	<?php include("navbar.php"); ?>
	<h2 class="panel-heading" style="background-color: #dff0d8;color:#3c763d">Listado de Productos</h2>
	<div class="contenedor">

		<table id="miTabla" class="table table-hover">
			<thead>
				<tr>
					<th>Código</th>
					<th>Nombre</th>
					<th>Precio</th>
					<th>Cantidad</th>
					<th>Categoría</th>
				</tr>
			</thead>
			<tbody>
				<?php
				$query = mysqli_query($con, "SELECT products.*, categorias.nombre_categoria
					FROM products
					INNER JOIN categorias ON products.id_categoria = categorias.id_categoria;
					");
				while ($row = mysqli_fetch_array($query)) {
					
					if ($row['stock'] == 0) {
						$stockDisplay = '<span style="color: red;"><b>Sin stock</b></span>';
					} else {
						$stockDisplay = $row['stock'];
					}

					echo "<tr>
					<td>{$row['codigo_producto']}</td>
					<td>{$row['nombre_producto']}</td>
					<td>$ {$row['precio_producto']}</td>
					<td>{$stockDisplay}</td>
					<td>{$row['nombre_categoria']}</td>
				</tr>";
				}
				?>
			</tbody>
		</table>
	</div>
	<div class="navbar navbar-default navbar-fixed-bottom">
		<div class="container">
			<p class="navbar-text pull-left">&copy <?php echo date('Y'); ?> - Gastón Barbaccia.
				<a href="#" target="_blank" style="color: #ecf0f1">DEVCODE</a>
			</p>
		</div>
	</div>
</body>

</html>