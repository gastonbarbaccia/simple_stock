<?php
include('is_logged.php');

if (isset($_FILES['archivo_csv']) && $_FILES['archivo_csv']['error'] == 0) {
    require_once("../config/db.php");
    require_once("../config/conexion.php");

    $file_tmp = $_FILES['archivo_csv']['tmp_name'];

    if (($handle = fopen($file_tmp, "r")) !== FALSE) {
        $row = 0;
        $errors = [];
        $success_count = 0;

        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $row++;

            if ($row == 1) {
                continue; 
            }

            if (count($data) < 5) {
                $errors[] = "Fila $row: formato incorrecto, faltan columnas.";
                continue;
            }

            $codigo = mysqli_real_escape_string($con, trim($data[0]));
            $nombre = mysqli_real_escape_string($con, trim($data[1]));
            $categoria_nombre = trim($data[2]);
            $precio = floatval($data[3]);
            $stock = intval($data[4]);

            
            $query_cat = mysqli_query($con, "SELECT id_categoria FROM categorias WHERE nombre_categoria = '$categoria_nombre' LIMIT 1");
            if (mysqli_num_rows($query_cat) == 1) {
                $row_cat = mysqli_fetch_assoc($query_cat);
                $id_categoria = intval($row_cat['id_categoria']);
            } else {
                $id_categoria = 0;
                $errors[] = "Fila $row: categoría '$categoria_nombre' no encontrada. Se asignó categoría 0.";
            }

            
            if ($codigo == "" || $nombre == "" || $precio <= 0 || $stock < 0) {
                $errors[] = "Fila $row: datos inválidos.";
                continue;
            }

            
            $check = mysqli_query($con, "SELECT id_producto FROM products WHERE codigo_producto = '$codigo'");
            if (mysqli_num_rows($check) > 0) {
                $errors[] = "Fila $row: El código '$codigo' ya existe.";
                continue;
            }

            $date_added = date("Y-m-d H:i:s");

            $sql = "INSERT INTO products (codigo_producto, nombre_producto, date_added, precio_producto_cons_final, stock, id_categoria, imagen) 
                    VALUES ('$codigo', '$nombre', '$date_added', '$precio', '$stock', '$id_categoria', 'img/stock.png')";

            $insert = mysqli_query($con, $sql);

            if ($insert) {
                $success_count++;
            } else {
                $errors[] = "Fila $row: Error en la base de datos - " . mysqli_error($con);
            }
        }
        fclose($handle);

        
        echo "<div style='padding:20px'>";
        if ($success_count > 0) {
            echo "<div class='alert alert-success'> Importación exitosa: $success_count producto(s) agregado(s).</div>";
        }
        if (!empty($errors)) {
            echo "<div class='alert alert-danger'><strong> Errores encontrados:</strong><br>";
            foreach ($errors as $error) {
                echo $error . "<br>";
            }
            echo "</div>";
        
        if ($success_count == 0 && empty($errors)) {
            echo '<div class="alert alert-danger">No se pudo procesar el archivo CSV o estaba vacío.</div>';
        }
    }}}
?>
