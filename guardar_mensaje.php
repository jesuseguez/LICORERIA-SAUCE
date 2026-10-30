<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'mensaje' => 'Método no permitido']);
    exit;
}

require_once "admin/conexion.php";

$nombre = mysqli_real_escape_string($conexion, trim($_POST['nombre'] ?? ''));
$edad = intval($_POST['edad'] ?? 0);
$telefono = mysqli_real_escape_string($conexion, trim($_POST['telefono'] ?? ''));
$correo = mysqli_real_escape_string($conexion, trim($_POST['correo'] ?? ''));
$direccion = mysqli_real_escape_string($conexion, trim($_POST['direccion'] ?? ''));
$mensaje = mysqli_real_escape_string($conexion, trim($_POST['mensaje'] ?? ''));

if (empty($nombre) || empty($telefono)) {
    echo json_encode(['success' => false, 'mensaje' => 'Nombre y teléfono son obligatorios']);
    exit;
}

$insert = mysqli_query($conexion, "INSERT INTO mensajes (nombre, edad, telefono, correo, direccion, mensaje) 
                                    VALUES ('$nombre', $edad, '$telefono', '$correo', '$direccion', '$mensaje')");

if ($insert) {
    echo json_encode(['success' => true, 'id' => mysqli_insert_id($conexion)]);
} else {
    echo json_encode(['success' => false, 'mensaje' => 'Error: ' . mysqli_error($conexion)]);
}
?>