<?php
include('is_logged.php'); 

$errors = [];
$messages = [];

if (empty($_POST['codigo'])) {
    $errors[] = "Código vacío";
} elseif (empty($_POST['nombre'])) {
    $errors[] = "Nombre del producto vacío";
} elseif ($_POST['stock'] == "") {
    $errors[] = "Stock del producto vacío";
} elseif (empty($_POST['precio'])) {
    $errors[] = "Precio de venta vacío";
} elseif (empty($_POST['precio2'])) {
    $errors[] = "Precio de reventa vacío";
} elseif (
    !empty($_POST['codigo']) &&
    !empty($_POST['nombre']) &&
    $_POST['stock'] !== "" &&
    !empty($_POST['precio']) &&
    !empty($_POST['precio2'])
) {
    require_once("../config/db.php");
    require_once("../config/conexion.php");
    include("../funciones.php");

    $codigo = mysqli_real_escape_string($con, strip_tags($_POST["codigo"], ENT_QUOTES));
    $nombre = mysqli_real_escape_string($con, strip_tags($_POST["nombre"], ENT_QUOTES));
    $stock = intval($_POST['stock']);
    $id_categoria = intval($_POST['categoria']);
    $precio_venta = floatval($_POST['precio']);
    $precio_venta2 = floatval($_POST['precio2']);
    date_default_timezone_set('America/Argentina/Buenos_Aires');
    $date_added = date("Y-m-d H:i:s");

    $imagen_db = "img/stock.png"; // Imagen por defecto

    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['imagen']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (in_array($ext, $allowed)) {
            $nuevo_nombre = uniqid() . '.' . $ext;
            $ruta = "../img/" . $nuevo_nombre;

            if (move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta)) {
                $imagen_db = "img/" . $nuevo_nombre;
            } else {
                $errors[] = "Error al subir la imagen.";
            }
        } else {
            $errors[] = "Tipo de archivo no permitido. Solo jpg, jpeg, png y gif.";
        }
    }

    if (empty($errors)) {
        $sql = "INSERT INTO products (codigo_producto, nombre_producto, date_added, precio_producto_cons_final, precio_producto_reventa, stock, id_categoria, imagen)
                VALUES ('$codigo', '$nombre', '$date_added', '$precio_venta', '$precio_venta2', '$stock', '$id_categoria', '$imagen_db')";
        $query_new_insert = mysqli_query($con, $sql);

        if ($query_new_insert) {
            $messages[] = "Producto ha sido ingresado satisfactoriamente.";
            $id_producto = get_row('products', 'id_producto', 'codigo_producto', $codigo);
            $user_id = $_SESSION['user_id'];
            $firstname = $_SESSION['firstname'];
            $nota = "$firstname agregó $stock producto(s) al inventario";
            $tipo_precio = floatval($precio_venta);  

            echo guardar_historial($id_producto, $user_id, $date_added, $nota, $codigo, $tipo_precio, $stock);

        } else {
            $errors[] = "Lo siento, algo ha salido mal. Intenta nuevamente." . mysqli_error($con);
        }
    }
} else {
    $errors[] = "Error desconocido.";
}

// Mensajes
if (!empty($errors)) {
    ?>
    <div class="alert alert-danger" role="alert">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <strong>Error!</strong><br>
        <?php
        foreach ($errors as $error) {
            echo $error . "<br>";
        }
        ?>
    </div>
    <?php
}

if (!empty($messages)) {
    ?>
    <div class="alert alert-success" role="alert">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <strong>¡Bien hecho!</strong><br>
        <?php
        foreach ($messages as $message) {
            echo $message . "<br>";
        }
        ?>
    </div>
    <?php
}
?>
