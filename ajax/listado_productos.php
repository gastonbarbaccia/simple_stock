<?php
session_start();
if (!isset($_SESSION['user_login_status']) || $_SESSION['user_login_status'] != 1) {
	http_response_code(403);
	echo json_encode(["error" => "No autorizado"]);
	exit;
}

require_once("../config/db.php");
require_once("../config/conexion.php");

$query = mysqli_query($con, "SELECT products.*, categorias.nombre_categoria
	FROM products
	INNER JOIN categorias ON products.id_categoria = categorias.id_categoria");

$data = [];

while ($row = mysqli_fetch_assoc($query)) {
    $row['precio_producto_cons_final'] = number_format($row['precio_producto_cons_final'], 2);
    $row['precio_producto_reventa']    = number_format($row['precio_producto_reventa'], 2);

    $row['imagen_producto'] = isset($row['imagen_producto']) ? trim($row['imagen_producto']) : '';

    $data[] = $row;
}


header('Content-Type: application/json');

echo json_encode($data);
