<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Cache-Control: no-cache, must-revalidate');

// Función para obtener el precio del dólar paralelo desde Binance P2P
function obtenerDolarBinance() {
    $url = 'https://p2p.binance.com/bapi/c2c/v2/friendly/c2c/adv/search';
    
    $datos = json_encode([
        'page' => 1,
        'rows' => 5,
        'payTypes' => [],
        'asset' => 'USDT',
        'fiat' => 'BOB',
        'tradeType' => 'SELL'
    ]);
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $datos);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode !== 200 || !$response) {
        return null;
    }
    
    $data = json_decode($response, true);
    
    if (!isset($data['data']) || empty($data['data'])) {
        return null;
    }
    
    // Tomar el primer precio disponible
    $precio = floatval($data['data'][0]['adv']['price'] ?? 0);
    
    return $precio > 0 ? $precio : null;
}

// Función alternativa desde Bcb.gob.bo o similar
function obtenerDolarRespaldo() {
    // Precio de respaldo si Binance falla (ajustar manualmente)
    return 10.82;
}

// Obtener el precio
$precio = obtenerDolarBinance();
$fuente = 'Binance P2P';
$esTiempoReal = true;

if (!$precio) {
    $precio = obtenerDolarRespaldo();
    $fuente = 'Respaldo';
    $esTiempoReal = false;
}

echo json_encode([
    'success' => true,
    'precio' => round($precio, 2),
    'fuente' => $fuente,
    'tiempo_real' => $esTiempoReal,
    'fecha' => date('Y-m-d H:i:s')
]);
?>