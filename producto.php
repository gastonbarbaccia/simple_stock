<?php
session_start();
if (!isset($_SESSION['user_login_status']) || $_SESSION['user_login_status'] != 1) {
    header("location: login.php");
    exit;
}

require_once("config/db.php");
require_once("config/conexion.php");
include("funciones.php");

$active_productos = "active";
$active_clientes = "";
$active_usuarios = "";
$title = "Producto | Simple Stock";

if (isset($_POST['reference']) && isset($_POST['quantity'])) {
    $quantity = intval($_POST['quantity']);
    $reference = mysqli_real_escape_string($con, strip_tags($_POST["reference"], ENT_QUOTES));
    $id_producto = intval($_GET['id']);
    $user_id = $_SESSION['user_id'];
    $firstname = $_SESSION['firstname'];
    $nota = "$firstname agregó $quantity producto(s) al inventario";
    date_default_timezone_set('America/Argentina/Buenos_Aires');
    $fecha = date("Y-m-d H:i:s");
    $tipo_precio = $_POST['reference_remove_2'];
    guardar_historial($id_producto, $user_id, $fecha, $nota, $reference, $tipo_precio, $quantity);
    $update = agregar_stock($id_producto, $quantity);
    $message = $update == 1 ? 1 : null;
    $error = $update != 1 ? 1 : null;
}

if (isset($_POST['reference_remove']) && isset($_POST['quantity_remove'])) {
    $quantity = intval($_POST['quantity_remove']);
    $reference = mysqli_real_escape_string($con, strip_tags($_POST["reference_remove"], ENT_QUOTES));
    $id_producto = intval($_GET['id']);
    $user_id = $_SESSION['user_id'];
    $firstname = $_SESSION['firstname'];
    $nota = "$firstname eliminó $quantity producto(s) del inventario";
    date_default_timezone_set('America/Argentina/Buenos_Aires');
    $fecha = date("Y-m-d H:i:s");
    $tipo_precio = $_POST['reference_remove_2'];
    guardar_historial($id_producto, $user_id, $fecha, $nota, $reference, $tipo_precio, $quantity);
    $update = eliminar_stock($id_producto, $quantity);
    $message = $update == 1 ? 1 : null;
    $error = $update != 1 ? 1 : null;
}

if (isset($_GET['id'])) {
    $id_producto = intval($_GET['id']);
    $query = mysqli_query($con, "SELECT * FROM products WHERE id_producto='$id_producto'");
    $row = mysqli_fetch_array($query);
} else {
    die("Producto no existe");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <?php include("head.php"); ?>
</head>
<body>
<?php
include("navbar.php");
include("modal/agregar_stock.php");
include("modal/eliminar_stock.php");
include("modal/editar_productos.php");
?>

<div class="container">
    <!-- Modal para ampliar imagen -->
    <div class="modal fade" id="imagenModal" tabindex="-1" role="dialog" aria-labelledby="imagenModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <img id="imagen-ampliada" src="" class="img-responsive img-thumbnail" style="margin: 0 auto; max-height: 80vh;">
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-4 col-sm-offset-2 text-center">
                            <?php $imagen_producto = !empty($row['imagen']) ? $row['imagen'] : 'img/stock.png'; ?>
                            <a href="#" data-imagen="<?php echo $imagen_producto; ?>" data-toggle="modal" data-target="#imagenModal">
                                <img class="item-img img-responsive" src="<?php echo $imagen_producto; ?>" alt="Imagen del producto" style="max-height:200px;">
                            </a>
                            <br>
                            <a href="#" class="btn btn-danger" onclick="eliminar('<?php echo $row['id_producto'];?>')" title="Eliminar"> <i class="glyphicon glyphicon-trash"></i> Eliminar </a>
                            <a href="#myModal2" data-toggle="modal"
                               data-codigo='<?php echo $row['codigo_producto'];?>'
                               data-nombre='<?php echo $row['nombre_producto'];?>'
                               data-categoria='<?php echo $row['id_categoria']?>'
                               data-precio='<?php echo $row['precio_producto_cons_final']?>'
                               data-precio2='<?php echo $row['precio_producto_reventa']?>'
                               data-stock='<?php echo $row['stock'];?>'
                               data-id='<?php echo $row['id_producto'];?>'
                               class="btn btn-info" title="Editar">
                                <i class="glyphicon glyphicon-pencil"></i> Editar
                            </a>
                        </div>

                        <div class="col-sm-4 text-left">
                            <div class="row margin-btm-20">
                                <div class="col-sm-12">
                                    <span class="item-title"><?php echo $row['nombre_producto']; ?></span>
                                </div>
                                <div class="col-sm-12 margin-btm-10">
                                    <span class="item-number">Código de producto <?php echo $row['codigo_producto']; ?></span>
                                </div>
                                <div class="col-sm-12">
                                    <span class="current-stock">Stock disponible</span>
                                </div>
                                <div class="col-sm-12 margin-btm-10">
                                    <span class="item-quantity"><?php echo number_format($row['stock']); ?></span>
                                </div>
                                <div class="col-sm-12">
                                    <span class="current-stock">Precio consumidor final</span>
                                </div>
                                <div class="col-sm-12">
                                    <span class="item-price">$ <?php echo number_format($row['precio_producto_cons_final'], 2); ?></span>
                                </div>
                                <div class="col-sm-12">
                                    <span class="current-stock">Precio reventa</span>
                                </div>
                                <div class="col-sm-12">
                                    <span class="item-price">$ <?php echo number_format($row['precio_producto_reventa'], 2); ?></span>
                                </div>
                                <div class="col-sm-6 col-xs-6 col-md-4">
                                    <a href="#" data-toggle="modal" data-target="#add-stock"><img width="100px" src="img/stock-in.png"></a>
                                </div>
                                <div class="col-sm-6 col-xs-6 col-md-4">
                                    <a href="#" data-toggle="modal" data-target="#remove-stock"><img width="100px" src="img/stock-out.png"></a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <br>
                    <div class="row">
                        <div class="col-sm-8 col-sm-offset-2 text-left">
                            <div class="row">
                                <?php if (isset($message)): ?>
                                    <div class="alert alert-success alert-dismissible" role="alert">
                                        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                                        <strong>Aviso!</strong> Datos procesados exitosamente.
                                    </div>
                                <?php endif; ?>
                                <?php if (isset($error)): ?>
                                    <div class="alert alert-danger alert-dismissible" role="alert">
                                        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                                        <strong>Error!</strong> No se pudo procesar los datos.
                                    </div>
                                <?php endif; ?>

                                <table class='table table-bordered'>
                                    <tr>
                                        <th class='text-center' colspan="6">HISTORIAL DE INVENTARIO</th>
                                    </tr>
                                    <tr>
                                        <td>Fecha</td>
                                        <td>Hora</td>
                                        <td>Descripción</td>
                                        <td>Referencia</td>
                                        <td>Precio</td>
                                        <td class='text-center'>Total</td>
                                    </tr>
                                    <?php
                                    $query = mysqli_query($con, "SELECT * FROM historial WHERE id_producto='$id_producto'");
                                    while ($row = mysqli_fetch_array($query)):
                                        ?>
                                        <tr>
                                            <td><?php echo date('d/m/Y', strtotime($row['fecha'])); ?></td>
                                            <td><?php echo date('H:i:s', strtotime($row['fecha'])); ?></td>
                                            <td><?php echo $row['nota']; ?></td>
                                            <td><?php echo $row['referencia']; ?></td>
                                            <td>$ <?php echo $row['tipo_precio']; ?></td>
                                            <td class='text-center'><?php echo number_format($row['cantidad'], 2); ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php include("footer.php"); ?>
        <script type="text/javascript" src="js/productos.js"></script>

        <script>
            $("#editar_producto").submit(function(event) {
                event.preventDefault();
                $('#actualizar_datos').attr("disabled", true);
                let form = $('#editar_producto')[0];
                let formData = new FormData(form);
                $.ajax({
                    type: "POST",
                    url: "ajax/editar_producto.php",
                    data: formData,
                    processData: false,
                    contentType: false,
                    beforeSend: function () {
                        $("#resultados_ajax2").html("Mensaje: Cargando...");
                    },
                    success: function (datos) {
                        $("#resultados_ajax2").html(datos);
                        $('#actualizar_datos').attr("disabled", false);
                        setTimeout(function () {
                            $(".alert").fadeTo(500, 0).slideUp(500, function () {
                                $(this).remove();
                                location.reload();
                            });
                        }, 3000);
                    }
                });
            });

            $('#myModal2').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var modal = $(this);
                modal.find('.modal-body #mod_codigo').val(button.data('codigo'));
                modal.find('.modal-body #mod_nombre').val(button.data('nombre'));
                modal.find('.modal-body #mod_categoria').val(button.data('categoria'));
                modal.find('.modal-body #mod_precio').val(button.data('precio'));
                modal.find('.modal-body #mod_precio_2').val(button.data('precio2'));
                modal.find('.modal-body #mod_stock').val(button.data('stock'));
                modal.find('.modal-body #mod_id').val(button.data('id'));
            });

            function eliminar(id) {
                if (confirm("Realmente deseas eliminar el producto")) {
                    location.replace('stock.php?delete=' + id);
                }
            }

            $(document).on('click', '[data-toggle="modal"][data-imagen]', function (e) {
                e.preventDefault();
                var imagen = $(this).data('imagen');
                $('#imagen-ampliada').attr('src', imagen);
                $('#imagenModal').modal('show');
            });
        </script>
    </div>
</body>
</html>
