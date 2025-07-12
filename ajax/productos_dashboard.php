<?php
session_start();
if (!isset($_SESSION['user_login_status']) || $_SESSION['user_login_status'] != 1) {
	http_response_code(403);
	echo json_encode(["error" => "No autorizado"]);
	exit;
}

require_once("../config/db.php");
require_once("../config/conexion.php");

$query = mysqli_query($con, "SELECT 
  DATE_FORMAT(h.fecha, '%Y-%m-%d') AS fecha,
  CONCAT(u.firstname, ' ', u.lastname) AS usuario,
  p.codigo_producto AS codigo,
  h.referencia,
  p.nombre_producto,
  h.tipo_precio,
  h.cantidad,
  CASE 
    WHEN LOWER(h.tipo_precio) LIKE '%reventa%' THEN p.precio_producto_reventa
    WHEN LOWER(h.tipo_precio) LIKE '%consumidor final%' THEN p.precio_producto_cons_final
    ELSE NULL
  END AS precio_unitario
FROM historial h
JOIN users u ON h.user_id = u.user_id
JOIN products p ON h.id_producto = p.id_producto
WHERE h.referencia LIKE '%venta%'
ORDER BY h.fecha DESC;
");

$data = [];

while ($row = mysqli_fetch_assoc($query)) {
	$row['precio_unitario']  = number_format($row['precio_unitario'], 2);
	$data[] = $row;
}

header('Content-Type: application/json');
echo json_encode($data);
