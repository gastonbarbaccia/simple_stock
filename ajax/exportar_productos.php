<?php

include("is_logged.php"); 
require_once("../config/db.php");
require_once("../config/conexion.php");

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=productos.csv');


$output = fopen('php://output', 'w');


fputcsv($output, ['codigo', 'nombre', 'categoria', 'precio', 'stock']);

// Consulta con JOIN para obtener la categoría
$query = "
    SELECT 
        p.codigo_producto AS codigo, 
        p.nombre_producto AS nombre, 
        IFNULL(c.nombre_categoria, 'sin categoría') AS categoria, 
        p.precio_producto_cons_final AS precio, 
        p.stock,
    FROM products p
    LEFT JOIN categorias c ON p.id_categoria = c.id_categoria
";

$resultado = mysqli_query($con, $query);

while ($fila = mysqli_fetch_assoc($resultado)) {
    fputcsv($output, [
        $fila['codigo'],
        $fila['nombre'],
        $fila['categoria'],
        number_format($fila['precio'], 2, '.', ''),
        $fila['stock'],
    ]);
}

fclose($output);
exit;
?>
