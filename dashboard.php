<?php
session_start();
if (!isset($_SESSION['user_login_status']) || $_SESSION['user_login_status'] != 1) {
	header("location: login.php");
	exit;
}

if ($_SESSION['user_name'] == 'cliente') {
	// if the user is a client, redirect to the list page
	header("location: list.php");
}

require_once("config/db.php"); // Config DB
require_once("config/conexion.php"); // Conexión a DB

$active_dashboard = "active";
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

		#miTabla td:nth-child(10) {
			white-space: normal;
			word-wrap: break-word;
			overflow-wrap: break-word;
		}

		#miTabla th:nth-child(10) {
			white-space: normal;
		}
	</style>
	</style>
</head>

<body>

	<div class="main-content">
		<?php include("navbar.php"); ?>
		<h2 class="panel-heading" style="background-color: #dff0d8;color:#3c763d">Ventas realizadas</h2>
		<div class="contenedor table-responsive">


			<div style="display: flex; flex-wrap: wrap; gap: 20px; margin-bottom: 10px;">
				<label>Desde:<br>
					<input class="form-control" type="date" id="fecha_inicio" />
				</label>

				<label>Hasta:<br>
					<input class="form-control" type="date" id="fecha_fin" />
				</label>

				<label>Usuario:<br>
					<select class="form-control" id="usuario_filtro">
						<option value="">Todos</option>
						<?php
						$query = mysqli_query($con, "SELECT firstname, lastname FROM users GROUP BY firstname, lastname");
						while ($row = mysqli_fetch_array($query)) {
							$nombreCompleto = $row['firstname'] . ' ' . $row['lastname'];
							echo '<option value="' . htmlspecialchars(strtolower($nombreCompleto)) . '">' . htmlspecialchars($nombreCompleto) . '</option>';
						}
						?>
					</select>
				</label>

				<label>Tipo Precio:<br>
					<select class="form-control" id="tipo_precio_filtro" name="tipo_precio_filtro">
						<option value="">Todos</option>
						<option value="consumidor Final">Consumidor Final</option>
						<option value="reventa">Reventa</option>
					</select>
				</label>

				<label>Otros motivos:<br>
					<select class="form-control" id="tipo_precio_filtro2" name="tipo_precio_filtro2">
						<option value="">Todos</option>
						<option value="otros motivos">Otros motivos</option>
					</select>
				</label>

				<label>Total:<br>
					<input style="color:green" class="form-control" type="text" id="totalFiltrado" readonly />
				</label>
			</div>

			<div class="table-responsive">
				<table id="miTabla" class="table table-hover" style="table-layout: fixed;">
					<thead>
						<tr>
							<th>Fecha</th>
							<th>Usuario</th>
							<th>Código</th>
							<th>Producto</th>
							<th>Tipo Precio</th>
							<th>Precio</th>
							<th>Cantidad</th>
							<th>Total</th>
							<th>Referencia</th>
							<th>Detalle</th>
						</tr>
					</thead>
					<tbody></tbody>
				</table>
			</div>
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
			// Inicializo DataTable y guardo referencia
			var table = $('#miTabla').DataTable({
				autoWidth: false,
				"ajax": {
					"url": "ajax/productos_dashboard.php",
					"dataSrc": ""
				},
				"columns": [{
						"data": "fecha"
					},
					{
						"data": "usuario"
					},
					{
						"data": "codigo"
					},
					{
						"data": "nombre_producto"
					},
					{
						"data": "tipo_precio",
						render: function(data) {
							if (!data) return '';
							const texto = data.toString().toLowerCase();
							if (texto.includes('reventa')) return 'Reventa';
							if (texto.includes('consumidor final')) return 'Consumidor Final';
							return '';
						}
					},
					{
						"data": "precio_unitario",
						render: function(data) {
							return '$ ' + data;
						}
					},
					{
						"data": "cantidad"
					},
					{
						"data": null,
						render: function(data) {
							const cantidad = parseFloat(data.cantidad);
							let precioStr = data.precio_unitario ? data.precio_unitario.toString() : '';
							precioStr = precioStr.replace(/,/g, '');
							const precio = parseFloat(precioStr);
							if (isNaN(cantidad) || isNaN(precio)) return '';
							const total = cantidad * precio;
							return '$ ' + total.toLocaleString('en-US', {
								minimumFractionDigits: 2,
								maximumFractionDigits: 2
							});
						}
					},
					{
						"data": "referencia"
					},
					{
						"data": "detalle"
					}
				],
				columnDefs: [{
						width: "100px",
						targets: 0
					}, // Fecha
					{
						width: "150px",
						targets: 1
					}, // Usuario
					{
						width: "90px",
						targets: 2
					}, // Código
					{
						width: "250px",
						targets: 3
					}, // Producto
					{
						width: "120px",
						targets: 4
					}, // Tipo Precio
					{
						width: "80px",
						targets: 5
					}, // Precio
					{
						width: "80px",
						targets: 6
					}, // Cantidad
					{
						width: "90px",
						targets: 7
					}, // Total
					{
						width: "150px",
						targets: 8
					}, // Referencia
					{
						width: "150px",
						targets: 9
					} // Detalle
				],
				"language": {
					"url": "https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
				}
			});

			// Filtro personalizado para fecha, usuario y tipo precio
			$.fn.dataTable.ext.search.push(function(settings, data) {
				const fechaInicio = $('#fecha_inicio').val();
				const fechaFin = $('#fecha_fin').val();
				const usuarioFiltro = $('#usuario_filtro').val().toLowerCase();
				const tipoPrecioFiltro = $('#tipo_precio_filtro').val().toLowerCase();
				const motivoFiltro = $('#tipo_precio_filtro2').val().toLowerCase();

				const fecha = data[0]; // Fecha
				const usuario = data[1].toLowerCase(); // Usuario
				const tipoPrecio = data[4].toLowerCase(); // Tipo Precio
				const referencia = data[8].toLowerCase(); // Referencia (donde está el motivo)

				const fechaRow = new Date(fecha);

				// Filtros por fecha
				if (fechaInicio && fechaRow < new Date(fechaInicio)) return false;
				if (fechaFin && fechaRow > new Date(fechaFin)) return false;

				// Filtro por usuario
				if (usuarioFiltro && usuario !== usuarioFiltro) return false;

				// Filtro por tipo de precio (columna 4)
				if (tipoPrecioFiltro && tipoPrecio !== tipoPrecioFiltro) return false;

				// Filtro por "Otros motivos" en la referencia (columna 8)
				if (motivoFiltro && !referencia.includes(motivoFiltro)) return false;

				return true;
			});

			// Función para actualizar el total filtrado
			function actualizarTotal() {
				let total = 0;
				table.rows({
					filter: 'applied'
				}).every(function() {
					const data = this.data();

					// data[7] no funciona porque usás objetos, usá la propiedad correcta
					let cantidad = parseFloat(data.cantidad);
					let precioStr = data.precio_unitario ? data.precio_unitario.toString().replace(/,/g, '') : '';
					let precio = parseFloat(precioStr);

					if (!isNaN(cantidad) && !isNaN(precio)) {
						total += cantidad * precio;
					}
				});

				console.log('Total calculado:', total);

				$('#totalFiltrado').val('$ ' + total.toLocaleString('en-US', {
					minimumFractionDigits: 2,
					maximumFractionDigits: 2
				}));
			}

			// Actualizar total cada vez que se redibuja la tabla (filtro, paginación, etc)
			table.on('draw', actualizarTotal);

			// Redibujar tabla al cambiar filtros
			$('#fecha_inicio, #fecha_fin, #usuario_filtro, #tipo_precio_filtro, #tipo_precio_filtro2').on('change', function() {
				table.draw();
			});

			// Calcular total al cargar la tabla
			actualizarTotal();
		});
	</script>

</body>

</html>