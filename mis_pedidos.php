<?php
session_start();
require_once "admin/conexion.php";

if (empty($_SESSION['active']) || ($_SESSION['rol'] ?? '') !== 'cliente') {
    header('Location: login.php');
    exit;
}

$idUser = $_SESSION['idUser'];
$pedidos = mysqli_query($conexion, "SELECT * FROM pedidos WHERE id_usuario = $idUser ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🍷 Mis Pedidos | SUACÉ</title>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        :root {
            --gold: #d4af37;
            --gold-dark: #b8941f;
            --dark: #0a0e1a;
        }
        body {
            font-family: 'Poppins', sans-serif;
            background: #f5f6fa;
            min-height: 100vh;
        }
        
        /* HEADER */
        .header-cliente {
            background: linear-gradient(135deg, #0a0e1a 0%, #1a1f35 100%);
            padding: 15px 0;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .header-cliente .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }
        .header-cliente .logo {
            color: var(--gold);
            font-family: 'Cinzel', serif;
            font-size: 22px;
            font-weight: 700;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .header-cliente .logo:hover { color: var(--gold); text-decoration: none; }
        .header-cliente .logo i { font-size: 26px; }
        
        .nav-cliente {
            display: flex;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
        }
        .nav-cliente a {
            color: rgba(255,255,255,0.85);
            text-decoration: none;
            padding: 8px 14px;
            font-size: 14px;
            font-weight: 500;
            border-radius: 20px;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .nav-cliente a:hover {
            color: var(--gold);
            background: rgba(212, 175, 55, 0.1);
        }
        .nav-cliente a.activo {
            color: var(--gold);
            background: rgba(212, 175, 55, 0.15);
        }
        .nav-cliente a.salir {
            color: #ff6b6b;
        }
        
        /* CONTENIDO */
        .main-cliente {
            padding: 30px 20px;
            max-width: 1400px;
            margin: 0 auto;
        }
        .titulo-seccion {
            font-family: 'Cinzel', serif;
            color: #0a0e1a;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        /* TABLA */
        .card-pedidos {
            background: #fff;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        thead tr {
            background: #f8f9fa;
        }
        th {
            padding: 14px;
            font-size: 13px;
            text-align: left;
            color: #333;
            font-weight: 600;
            border-bottom: 2px solid #eee;
        }
        td {
            padding: 14px;
            font-size: 14px;
            border-bottom: 1px solid #f0f0f0;
        }
        tbody tr:hover {
            background: #fdfaf0;
        }
        .badge-estado {
            padding: 5px 12px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            color: #fff;
        }
        .badge-pendiente { background: #ffc107; color: #333; }
        .badge-confirmado { background: #17a2b8; }
        .badge-enviado { background: #28a745; }
        .badge-cancelado { background: #dc3545; }
        
        .btn-pdf {
            background: #dc3545;
            color: #fff;
            padding: 7px 14px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 12px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: all 0.3s;
        }
        .btn-pdf:hover {
            background: #b02a37;
            color: #fff;
            text-decoration: none;
            transform: translateY(-2px);
        }
        
        .vacio {
            text-align: center;
            padding: 60px 20px;
        }
        .vacio i {
            font-size: 70px;
            color: #ddd;
            margin-bottom: 20px;
        }
        .vacio h4 {
            color: #666;
            margin-bottom: 10px;
        }
        .vacio p {
            color: #999;
            margin-bottom: 20px;
        }
        .btn-ir-tienda {
            background: linear-gradient(135deg, var(--gold) 0%, var(--gold-dark) 100%);
            color: #0a0e1a;
            padding: 12px 30px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
        }
        .btn-ir-tienda:hover {
            color: #0a0e1a;
            text-decoration: none;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(212, 175, 55, 0.4);
        }
    </style>
</head>
<body>

<!-- HEADER EMBEBIDO -->
<header class="header-cliente">
    <div class="container">
        <a href="comprar.php" class="logo">
            <i class="fas fa-wine-bottle"></i>
            SUACE
        </a>
        
        <nav class="nav-cliente">
            <a href="comprar.php">
                <i class="fas fa-store"></i> Tienda
            </a>
            <a href="mis_pedidos.php" class="activo">
                <i class="fas fa-receipt"></i> Mis Pedidos
            </a>
            <a href="salir_cliente.php" class="salir">
                <i class="fas fa-sign-out-alt"></i> Salir
            </a>
        </nav>
    </div>
</header>

<!-- CONTENIDO -->
<div class="main-cliente">
    <h2 class="titulo-seccion">
        <i class="fas fa-receipt" style="color:#d4af37;"></i>
        Mis Pedidos
    </h2>
    
    <div class="card-pedidos">
        <?php if (mysqli_num_rows($pedidos) == 0): ?>
            <div class="vacio">
                <i class="fas fa-shopping-bag"></i>
                <h4>Aún no tienes pedidos</h4>
                <p>Explora nuestra tienda y haz tu primera compra</p>
                <a href="comprar.php" class="btn-ir-tienda">
                    <i class="fas fa-store"></i> Ir a la Tienda
                </a>
            </div>
        <?php else: ?>
            <div style="overflow-x:auto;">
                <table>
                    <thead>
                        <tr>
                            <th>#Pedido</th>
                            <th>Fecha</th>
                            <th>Método</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th>Boleta</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($p = mysqli_fetch_assoc($pedidos)): 
                            $estado = $p['estado'] ?? 'pendiente';
                            $clase = 'badge-' . $estado;
                        ?>
                            <tr>
                                <td><strong>#<?php echo $p['id']; ?></strong></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($p['fecha'])); ?></td>
                                <td><span style="background:#6c757d;color:#fff;padding:4px 10px;border-radius:10px;font-size:11px;"><?php echo $p['metodo_pago']; ?></span></td>
                                <td><strong style="color:#d4af37;">Bs. <?php echo number_format($p['total'], 2); ?></strong></td>
                                <td><span class="badge-estado <?php echo $clase; ?>"><?php echo strtoupper($estado); ?></span></td>
                                <td>
                                    <a href="boleta_pdf.php?id=<?php echo $p['id']; ?>" target="_blank" class="btn-pdf">
                                        <i class="fas fa-file-pdf"></i> PDF
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>