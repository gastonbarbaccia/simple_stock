<?php
include('is_logged.php'); 

$errors = [];
$messages = [];

if (empty($_POST['mod_id'])) {
    $errors[] = "ID vacío";
} elseif (empty($_POST['mod_codigo'])) {
    $errors[] = "Código vacío";
} elseif (empty($_POST['mod_nombre'])) {
    $errors[] = "Nombre del producto vacío";
} elseif ($_POST['mod_categoria'] == "") {
    $errors[] = "Selecciona la categoría del producto";
} elseif (empty($_POST['mod_precio'])) {
    $errors[] = "Precio de venta vacío";
} else {
    require_once("../config/db.php");
    require_once("../config/conexion.php");

    
    $id_producto = intval($_POST['mod_id']);
    $codigo = mysqli_real_escape_string($con, strip_tags($_POST["mod_codigo"], ENT_QUOTES));
    $nombre = mysqli_real_escape_string($con, strip_tags($_POST["mod_nombre"], ENT_QUOTES));
    $categoria = intval($_POST['mod_categoria']);
    $stock = intval($_POST['mod_stock']);
    $precio_venta = floatval($_POST['mod_precio']);
    $imagen = mysqli_real_escape_string($con, strip_tags($_POST["imagen_actual"])); // Imagen actual

    // Si se sube una nueva imagen
    if (isset($_FILES['mod_imagen']) && $_FILES['mod_imagen']['error'] == UPLOAD_ERR_OK) {
        $archivo_tmp = $_FILES['mod_imagen']['tmp_name'];
        $nombre_original = basename($_FILES['mod_imagen']['name']);
        $ext = strtolower(pathinfo($nombre_original, PATHINFO_EXTENSION));
        $ext_permitidas = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($ext, $ext_permitidas)) {
            $nombre_nuevo = uniqid("img_") . "." . $ext;
            $ruta_destino = "../img/" . $nombre_nuevo;

            if (move_uploaded_file($archivo_tmp, $ruta_destino)) {
                $imagen = "img/" . $nombre_nuevo;
            } else {
                $errors[] = "Error al mover la imagen al servidor.";
            }
        } else {
            $errors[] = "Tipo de imagen no permitido. Usa JPG, PNG o GIF.";
        }
    }

    if (empty($errors)) {
        $sql = "UPDATE products SET 
                    codigo_producto = '$codigo',
                    nombre_producto = '$nombre',
                    id_categoria = '$categoria',
                    precio_producto_cons_final = '$precio_venta',
                    stock = '$stock',
                    imagen = '$imagen'
                WHERE id_producto = '$id_producto'";

        $query_update = mysqli_query($con, $sql);

        if ($query_update) {
            $messages[] = "Producto actualizado correctamente.";
        } else {
            $errors[] = "Error al actualizar: " . mysqli_error($con);
        }
    }
}

// Muestra errores o mensajes
if (!empty($errors)) {
    echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button><strong>Error!</strong><br>';
    foreach ($errors as $error) {
        echo $error . "<br>";
    }
    echo '</div>';
}

if (!empty($messages)) {
    echo '<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button><strong>Éxito!</strong><br>';
    foreach ($messages as $message) {
        echo $message . "<br>";
    }
    echo '</div>';
}
?>
