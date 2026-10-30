<?php
session_start();
require_once "admin/conexion.php";

// Verificar login
if (empty($_SESSION['active'])) {
    header('Location: login.php');
    exit;
}

// Verificar que FPDF exista
if (!file_exists(__DIR__ . '/fpdf/fpdf.php')) {
    die('
        <div style="font-family:Arial;padding:40px;max-width:600px;margin:50px auto;background:#fff3cd;border:1px solid #ffc107;border-radius:10px;">
            <h2>⚠️ Falta la librería FPDF</h2>
            <p>Para generar el PDF necesitas descargar FPDF:</p>
            <ol>
                <li>Ve a: <a href="http://www.fpdf.org/en/download.php" target="_blank">http://www.fpdf.org/en/download.php</a></li>
                <li>Descarga el ZIP</li>
                <li>Extrae y copia la carpeta <strong>fpdf</strong> a:<br>
                <code>' . __DIR__ . '/fpdf/</code></li>
            </ol>
        </div>
    ');
}

require_once "fpdf/fpdf.php";

$idPedido = intval($_GET['id'] ?? 0);
$idUser = $_SESSION['idUser'];

if ($idPedido == 0) {
    die('ID de pedido inválido');
}

// Traer datos del pedido
$pedidoQ = mysqli_query($conexion, "SELECT p.*, c.nombre AS cliente_nombre, c.telefono, c.direccion 
                                    FROM pedidos p 
                                    LEFT JOIN cliente c ON p.id_cliente = c.idcliente 
                                    WHERE p.id = $idPedido AND p.id_usuario = $idUser");

if (!$pedidoQ || mysqli_num_rows($pedidoQ) == 0) {
    die('Pedido no encontrado o no tienes permisos para verlo');
}

$pedido = mysqli_fetch_assoc($pedidoQ);

// Config de la empresa
$configQ = mysqli_query($conexion, "SELECT * FROM configuracion LIMIT 1");
$config = mysqli_fetch_assoc($configQ);

// Detalle del pedido
$detalleQ = mysqli_query($conexion, "SELECT d.*, p.descripcion 
                                     FROM detalle_pedido d 
                                     INNER JOIN producto p ON d.id_producto = p.codproducto 
                                     WHERE d.id_pedido = $idPedido");

// ============================================
// GENERAR PDF
// ============================================
class PDF extends FPDF {
    function Header() {
        // Fondo negro superior
        $this->SetFillColor(10, 14, 26);
        $this->Rect(0, 0, 210, 40, 'F');
        
        // Título dorado
        $this->SetTextColor(212, 175, 55);
        $this->SetFont('Arial', 'B', 20);
        $this->SetY(12);
        $this->Cell(0, 10, 'SUACE - SABORES EXOTICOS', 0, 1, 'C');
        
        // Subtítulo blanco
        $this->SetTextColor(255, 255, 255);
        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 6, utf8_decode('Boleta de Compra'), 0, 1, 'C');
        
        $this->Ln(10);
    }
    
    function Footer() {
        $this->SetY(-20);
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(128);
        $this->Cell(0, 5, utf8_decode('Gracias por su compra - SUACE Sabores Exóticos de mi Tierra'), 0, 0, 'C');
        $this->Ln(4);
        $this->Cell(0, 5, utf8_decode('Página ') . $this->PageNo(), 0, 0, 'C');
    }
}

$pdf = new PDF('P', 'mm', 'A4');
$pdf->AddPage();
$pdf->SetMargins(15, 15, 15);

// ============ DATOS DE LA EMPRESA ============
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('Arial', '', 9);
$pdf->SetY(45);
$pdf->Cell(0, 5, utf8_decode('Teléfono: ') . ($config['telefono'] ?? 'N/A'), 0, 1);
$pdf->Cell(0, 5, utf8_decode('Email: ') . ($config['email'] ?? 'N/A'), 0, 1);
$pdf->Cell(0, 5, utf8_decode('Dirección: ') . utf8_decode($config['direccion'] ?? 'N/A'), 0, 1);

// ============ INFORMACIÓN DEL PEDIDO ============
$pdf->Ln(5);
$pdf->SetFillColor(240, 240, 240);
$pdf->SetFont('Arial', 'B', 12);
$pdf->SetTextColor(10, 14, 26);
$pdf->Cell(0, 8, utf8_decode('INFORMACIÓN DEL PEDIDO #' . $idPedido), 0, 1);
$pdf->SetFont('Arial', '', 10);

$pdf->Cell(45, 6, 'Fecha:', 0, 0);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 6, $pedido['fecha'], 0, 1);
$pdf->SetFont('Arial', '', 10);

$pdf->Cell(45, 6, utf8_decode('Método de pago:'), 0, 0);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 6, $pedido['metodo_pago'], 0, 1);
$pdf->SetFont('Arial', '', 10);

$pdf->Cell(45, 6, utf8_decode('Estado:'), 0, 0);
$pdf->SetFont('Arial', 'B', 10);
$estado = $pedido['estado'] ?? 'pendiente';
$pdf->Cell(0, 6, strtoupper($estado), 0, 1);
$pdf->SetFont('Arial', '', 10);

// ============ DATOS DEL CLIENTE ============
$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 12);
$pdf->SetTextColor(10, 14, 26);
$pdf->Cell(0, 8, utf8_decode('DATOS DEL CLIENTE'), 0, 1);
$pdf->SetFont('Arial', '', 10);

$pdf->Cell(45, 6, utf8_decode('Nombre:'), 0, 0);
$pdf->Cell(0, 6, utf8_decode($pedido['cliente_nombre'] ?? $_SESSION['nombre']), 0, 1);

$pdf->Cell(45, 6, utf8_decode('Teléfono:'), 0, 0);
$pdf->Cell(0, 6, $pedido['telefono'] ?? 'N/A', 0, 1);

$pdf->Cell(45, 6, utf8_decode('Dirección:'), 0, 0);
$pdf->Cell(0, 6, utf8_decode($pedido['direccion'] ?? 'N/A'), 0, 1);

// ============ TABLA DE PRODUCTOS ============
$pdf->Ln(8);
$pdf->SetFillColor(212, 175, 55);
$pdf->SetTextColor(10, 14, 26);
$pdf->SetFont('Arial', 'B', 10);

$pdf->Cell(18, 9, 'Cant.', 1, 0, 'C', true);
$pdf->Cell(105, 9, utf8_decode('Descripción'), 1, 0, 'C', true);
$pdf->Cell(32, 9, 'Precio', 1, 0, 'C', true);
$pdf->Cell(32, 9, 'Subtotal', 1, 1, 'C', true);

$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('Arial', '', 10);

$subtotalGeneral = 0;
while ($d = mysqli_fetch_assoc($detalleQ)) {
    $subtotalGeneral += floatval($d['subtotal']);
    
    $pdf->Cell(18, 8, $d['cantidad'], 1, 0, 'C');
    $pdf->Cell(105, 8, utf8_decode(substr($d['descripcion'], 0, 55)), 1, 0);
    $pdf->Cell(32, 8, 'Bs. ' . number_format($d['precio'], 2), 1, 0, 'R');
    $pdf->Cell(32, 8, 'Bs. ' . number_format($d['subtotal'], 2), 1, 1, 'R');
}

// ============ TOTAL ============
$pdf->SetFont('Arial', 'B', 12);
$pdf->SetFillColor(212, 175, 55);
$pdf->SetTextColor(10, 14, 26);
$pdf->Cell(123, 10, utf8_decode('TOTAL A PAGAR:'), 1, 0, 'R', true);
$pdf->Cell(64, 10, 'Bs. ' . number_format($pedido['total'], 2), 1, 1, 'R', true);

// ============ NOTAS ============
if (!empty($pedido['notas'])) {
    $pdf->Ln(5);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetTextColor(10, 14, 26);
    $pdf->Cell(0, 6, utf8_decode('NOTAS:'), 0, 1);
    $pdf->SetFont('Arial', '', 9);
    $pdf->SetTextColor(80, 80, 80);
    $pdf->MultiCell(0, 5, utf8_decode($pedido['notas']));
}

// ============ INSTRUCCIONES DE PAGO ============
$pdf->Ln(8);
$pdf->SetFillColor(255, 250, 235);
$pdf->SetDrawColor(212, 175, 55);
$pdf->SetLineWidth(0.5);
$pdf->SetTextColor(10, 14, 26);
$pdf->SetFont('Arial', 'B', 10);

$pdf->Cell(0, 8, utf8_decode('INSTRUCCIONES DE PAGO - ' . strtoupper($pedido['metodo_pago'])), 1, 1, 'C', true);

$pdf->SetFont('Arial', '', 9);
$pdf->SetFillColor(255, 255, 255);
$pdf->SetTextColor(60, 60, 60);

if ($pedido['metodo_pago'] == 'QR') {
    $pdf->MultiCell(0, 5, utf8_decode(
        "1. Escanee el código QR con su aplicación bancaria.\n" .
        "2. Realice el pago por el monto indicado: Bs. " . number_format($pedido['total'], 2) . "\n" .
        "3. Envíe el comprobante por WhatsApp al administrador.\n" .
        "4. Espere la confirmación de su pedido.\n\n" .
        "Importante: Su pedido será procesado una vez confirmado el pago."
    ), 1, 1);
} else {
    $pdf->MultiCell(0, 5, utf8_decode(
        "1. Realice la transferencia a la siguiente cuenta:\n" .
        "   Banco: Banco Unión S.A.\n" .
        "   Titular: SUACE - Sabores Exóticos\n" .
        "   Cuenta: 1234567890\n" .
        "   NIT: 12345678901\n\n" .
        "2. Monto a transferir: Bs. " . number_format($pedido['total'], 2) . "\n" .
        "3. Envíe el comprobante por WhatsApp al administrador.\n" .
        "4. Espere la confirmación de su pedido."
    ), 1, 1);
}

// ============ PIE ============
$pdf->Ln(8);
$pdf->SetFont('Arial', 'I', 9);
$pdf->SetTextColor(128, 128, 128);
$pdf->MultiCell(0, 5, utf8_decode(
    'Este documento es una constancia de su pedido. ' .
    'Presente este comprobante al momento de recibir su producto. ' .
    'Para cualquier consulta, contáctese con el administrador vía WhatsApp.'
), 0, 'C');

// ============ SALIDA ============
$pdf->Output('I', 'Boleta_SUACE_' . str_pad($idPedido, 6, '0', STR_PAD_LEFT) . '.pdf');
?>