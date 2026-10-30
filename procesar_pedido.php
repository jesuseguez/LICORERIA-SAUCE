<?php
session_start();
header('Content-Type: application/json');

if (empty($_SESSION['active'])) {
    echo json_encode(['success' => false, 'mensaje' => 'Debes iniciar sesión']);
    exit;
}

require_once "admin/conexion.php";

$input = json_decode(file_get_contents('php://input'), true);

if (!$input || empty($input['carrito'])) {
    echo json_encode(['success' => false, 'mensaje' => 'Datos inválidos']);
    exit;
}

$idUser = $_SESSION['idUser'];
$carrito = $input['carrito'];
$total = floatval($input['total']);
$metodo = $input['metodo_pago'] ?? 'QR';
$notas = $input['notas'] ?? '';

// Buscar/crear cliente con ese nombre
$nombre = mysqli_real_escape_string($conexion, $input['nombre']);
$telefono = mysqli_real_escape_string($conexion, $input['telefono']);
$direccion = mysqli_real_escape_string($conexion, $input['direccion']);

$buscarCliente = mysqli_query($conexion, "SELECT idcliente FROM cliente WHERE telefono = '$telefono' LIMIT 1");
if (mysqli_num_rows($buscarCliente) > 0) {
    $idCliente = mysqli_fetch_assoc($buscarCliente)['idcliente'];
    mysqli_query($conexion, "UPDATE cliente SET nombre = '$nombre', direccion = '$direccion' WHERE idcliente = $idCliente");
} else {
    mysqli_query($conexion, "INSERT INTO cliente (nombre, telefono, direccion) VALUES ('$nombre', '$telefono', '$direccion')");
    $idCliente = mysqli_insert_id($conexion);
}

// Insertar pedido
$metodoSafe = in_array($metodo, ['QR', 'Transferencia', 'Efectivo']) ? $metodo : 'QR';
$notasSafe = mysqli_real_escape_string($conexion, $notas);

$insertPedido = mysqli_query($conexion, "INSERT INTO pedidos (id_usuario, id_cliente, total, metodo_pago, notas) 
                                        VALUES ($idUser, $idCliente, $total, '$metodoSafe', '$notasSafe')");

if (!$insertPedido) {
    echo json_encode(['success' => false, 'mensaje' => 'Error: ' . mysqli_error($conexion)]);
    exit;
}

$idPedido = mysqli_insert_id($conexion);

// Insertar detalle + actualizar stock
foreach ($carrito as $item) {
    $idProd = intval($item['id']);
    $cant = intval($item['cantidad']);
    $precio = floatval($item['precio']);
    $subtotal = $precio * $cant;
    
    mysqli_query($conexion, "INSERT INTO detalle_pedido (id_pedido, id_producto, cantidad, precio, subtotal) 
                            VALUES ($idPedido, $idProd, $cant, '$precio', '$subtotal')");
    
    mysqli_query($conexion, "UPDATE producto SET existencia = existencia - $cant WHERE codproducto = $idProd AND existencia >= $cant");
}

echo json_encode(['success' => true, 'id_pedido' => $idPedido]);
?>