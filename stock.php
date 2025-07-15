<?php
session_start();
if (!isset($_SESSION['user_login_status']) || $_SESSION['user_login_status'] != 1) {
	header("location: login.php");
	exit;
}

require_once("config/db.php");
require_once("config/conexion.php");

$active_productos = "active";
$title = "Inventario | Simple Stock";
?>
<!DOCTYPE html>
<html lang="es">

<head>
	<?php include("head.php"); ?>
</head>

<body>
	<?php include("navbar.php"); ?>

	<div class="container">
		<div class="panel panel-success">
			<div class="panel-heading">
				<div class="btn-group pull-right">
					<button type='button' class="btn btn-success" data-toggle="modal" data-target="#nuevoProducto">
						<span class="glyphicon glyphicon-plus"></span> Agregar
					</button>

					<div class="btn-group ml-2">
						<button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown">
							Descargar
						</button>
						<div class="dropdown-menu">
							<a class="dropdown-item" href="ajax/exportar_productos.php">📄 CSV</a><br>
							<a class="dropdown-item" href="ajax/exportar_productos_excel.php">📊 Excel</a>
						</div>
					</div>
				</div>
				<h4><i class='glyphicon glyphicon-search'></i> Consultar inventario</h4>
			</div>
			<div class="panel-body">
				<?php
				include("modal/registro_productos.php");
				include("modal/editar_productos.php");
				?>
				<form class="form-horizontal" role="form" id="datos">
					<div class="row">
						<div class='col-md-4'>
							<label>Filtrar por código o nombre</label>
							<input type="text" class="form-control" id="q" placeholder="Código o nombre del producto" onkeyup='load(1);'>
						</div>
						<div class='col-md-4'>
							<label>Filtrar por categoría</label>
							<select class='form-control' name='id_categoria' id='id_categoria' onchange="load(1);">
								<option value="">Selecciona una categoría</option>
								<?php 
								$query_categoria = mysqli_query($con, "SELECT * FROM categorias ORDER BY nombre_categoria");
								while ($rw = mysqli_fetch_array($query_categoria)) {
									echo "<option value='{$rw['id_categoria']}'>{$rw['nombre_categoria']}</option>";
								}
								?>
							</select>
						</div>
						<div class='col-md-12 text-center'>
							<span id="loader"></span>
						</div>
					</div>
					<hr>
					<div class='row-fluid'>
						<div id="resultados"></div>
					</div>
					<div class='row'>
						<div class='outer_div'></div>
					</div>
				</form>
			</div>
		</div>
	</div>

	<?php include("footer.php"); ?>
	<script type="text/javascript" src="js/productos.js"></script>

	<script>
		function eliminar(id) {
			var q = $("#q").val();
			var id_categoria = $("#id_categoria").val();
			if (confirm("¿Seguro que deseas eliminar este producto?")) {
				$.ajax({
					type: "GET",
					url: "./ajax/buscar_productos.php",
					data: { id: id, q: q, id_categoria: id_categoria },
					beforeSend: function() {
						$("#resultados").html("Mensaje: Cargando...");
					},
					success: function(datos) {
						$("#resultados").html(datos);
						load(1);
					}
				});
			}
		}

		$(document).ready(function() {
			<?php if (isset($_GET['delete'])) { ?>
				eliminar(<?php echo intval($_GET['delete']); ?>);
			<?php } ?>

			// Alta de nuevo producto con imagen (FormData)
			$("#guardar_producto").submit(function(event) {
				event.preventDefault();
				$('#guardar_datos').attr("disabled", true);
				var form = $('#guardar_producto')[0];
				var formData = new FormData(form);
				$.ajax({
					type: "POST",
					url: "ajax/nuevo_producto.php",
					data: formData,
					processData: false,
					contentType: false,
					beforeSend: function() {
						$("#resultados_ajax_productos").html("Cargando...");
					},
					success: function(datos) {
						$("#resultados_ajax_productos").html(datos);
						$('#guardar_datos').attr("disabled", false);
						$('#nuevoProducto').modal('hide');
						$('#guardar_producto')[0].reset();
						load(1);
					},
					error: function() {
						$("#resultados_ajax_productos").html("Error al enviar el formulario.");
						$('#guardar_datos').attr("disabled", false);
					}
				});
			});
		});
	</script>
</body>
</html>
