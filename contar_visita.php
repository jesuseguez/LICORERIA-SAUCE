<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once "admin/conexion.php";

// Obtener IP del visitante (para evitar contar múltiples veces en la misma sesión)
$ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
session_start();

// Evitar contar la misma visita 2 veces en la misma sesión
$yaContado = $_SESSION['visita_contada_' . date('Y-m-d')] ?? false;

if (!$yaContado) {
    // Incrementar contador del día actual
    mysqli_query($conexion, "INSERT INTO visitas (fecha, total) VALUES (CURDATE(), 1) 
                             ON DUPLICATE KEY UPDATE total = total + 1");
    
    // Marcar la sesión como contada
    $_SESSION['visita_contada_' . date('Y-m-d')] = true;
}

// Obtener el total actual
$result = mysqli_query($conexion, "SELECT total FROM visitas WHERE fecha = CURDATE()");
$row = mysqli_fetch_assoc($result);

// Obtener el total histórico (suma de todos los días)
$resultTotal = mysqli_query($conexion, "SELECT SUM(total) AS total_historico FROM visitas");
$rowTotal = mysqli_fetch_assoc($resultTotal);

echo json_encode([
    'total_hoy' => intval($row['total'] ?? 0),
    'total_historico' => intval($rowTotal['total_historico'] ?? 0)
]);
?>