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

	<!-- DataTables CSS -->
	<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

	<!-- Estilos opcionales -->
	<style>
		html,
		body {
			height: 100%;
			margin: 0;
			display: flex;
			flex-direction: column;
		}

		.main-content {
			flex: 1;
			display: flex;
			flex-direction: column;
		}

		.contenedor {
			flex: 1;
			padding: 0 40px;
		}

		.footer {
			margin-bottom: 0px;
			margin-top: 10px;
		}
		
		.thumbnail-img {
			border-radius: 4px;
			transition: transform 0.2s;
		}

		.thumbnail-img:hover {
			transform: scale(1.1);
		}

	</style>
</head>

<body>

	<div class="main-content">
		<?php include("navbar.php"); ?>
		<h2 class="panel-heading" style="background-color: #dff0d8;color:#3c763d">Listado de Productos</h2>
		<div class="contenedor table-responsive">

			<table id="miTabla" class="table table-hover">
				<thead>
					<tr>
						<th>Código</th>
						<th>Nombre</th>
						<th>Imagen</th> 
						<th>Precio Cons. Final</th>
						<th>Precio Reventa</th>
						<th>Cantidad</th>
						<th>Categoría</th>
					</tr>
				</thead>
				<tbody></tbody>
			</table>
		</div>
	</div>

	<!-- Footer -->
	<div class="navbar navbar-default footer">
		<div class="container">
			<p class="navbar-text pull-left">&copy <?php echo date('Y'); ?> - Gastón Barbaccia.
				<a href="#" target="_blank" style="color: #ecf0f1">DEVCODE</a>
			</p>
		</div>
	</div>

	<!-- jQuery compatible con Bootstrap 3 -->
	<script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>

	<!-- Bootstrap 3 -->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>

	<!-- DataTables JS -->
	<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

	<!-- Inicialización de DataTables con AJAX -->
	<script>
		$(document).ready(function() {
			$('#miTabla').DataTable({
				"ajax": {
					"url": "ajax/listado_productos.php",
					"dataSrc": ""
				},
				"columns": [{
						"data": "codigo_producto"
					},
					{
						"data": "nombre_producto"
					},
					{
						"data": "imagen",
						"render": function(data) {
							if (data && data.trim() !== "") {
							return `<img src="${data.trim()}" alt="Producto" class="thumbnail-img" onclick="expandImage(this)" style="cursor:pointer; max-height:60px;">`;
							} else {
							return `<img src="img/stock.png" alt="Producto sin imagen" class="thumbnail-img" style="max-height:60px;">`;
							}
					}
					},
					{
						"data": "precio_producto_cons_final",
						"render": function(data) {
							return '$ ' + data;
						}
					},
					{
						"data": "precio_producto_reventa",
						"render": function(data) {
							return '$ ' + data;
						}
					},
					{
						"data": "stock",
						"render": function(data) {
							return (parseInt(data) <= 0) ?
								'<span style="color: red;"><b>Sin stock</b></span>' :
								data;
						}
					},
					{
						"data": "nombre_categoria"
					}
				],
				"language": {
					"url": "https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
				}
			});
		});
	</script>
			<!-- ver imagen ampliada -->
		<div id="imageModal" class="modal fade" tabindex="-1" role="dialog">
		<div class="modal-dialog modal-dialog-centered" role="document">
			<div class="modal-content">
			<div class="modal-body text-center">
				<img id="modalImage" src="" alt="Imagen ampliada del producto" class="img-responsive" style="width: 100%;">

			</div>
			</div>
		</div>
		</div>

		<script>
			function expandImage(img) {
				const src = img.getAttribute('src');
				$('#modalImage').attr('src', src);
				$('#imageModal').modal('show');
			}
		</script>

</body>

</html>