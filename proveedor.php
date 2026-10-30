<?php
session_start();
require_once "admin/conexion.php";

// Verificar que sea proveedor
if (empty($_SESSION['active']) || ($_SESSION['rol'] ?? '') !== 'proveedor') {
    // Si es admin, al panel
    if (($_SESSION['rol'] ?? '') === 'admin') {
        header('Location: admin/index.php');
        exit;
    }
    // Si es cliente, a la tienda
    if (($_SESSION['rol'] ?? '') === 'cliente') {
        header('Location: comprar.php');
        exit;
    }
    // Si no tiene sesión, al login
    $_SESSION['redirect_after_login'] = 'proveedor.php';
    header('Location: login.php');
    exit;
}

$idUser = $_SESSION['idUser'];
$userQ = mysqli_query($conexion, "SELECT * FROM usuario WHERE idusuario = $idUser");
$usuario = mysqli_fetch_assoc($userQ);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🚚 Panel Proveedor | SUACÉ</title>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        :root { --gold: #d4af37; --dark: #0a0e1a; }
        body {
            font-family: 'Poppins', sans-serif;
            background: #f5f6fa;
            min-height: 100vh;
        }
        
        .header-proveedor {
            background: linear-gradient(135deg, #6a1b9a 0%, #9c27b0 100%);
            padding: 15px 0;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .header-proveedor .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header-proveedor .logo {
            color: #fff;
            font-family: 'Cinzel', serif;
            font-size: 22px;
            font-weight: 700;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .header-proveedor .logo i { color: var(--gold); font-size: 26px; }
        
        .nav-proveedor {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .nav-proveedor a {
            color: rgba(255,255,255,0.9);
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 500;
            transition: all 0.3s;
        }
        .nav-proveedor a:hover {
            background: rgba(255,255,255,0.15);
            color: #fff;
        }
        .badge-user {
            background: rgba(255,255,255,0.2);
            padding: 8px 16px;
            border-radius: 20px;
            color: #fff;
            font-size: 13px;
            font-weight: 600;
        }
        
        .main {
            max-width: 1400px;
            margin: 0 auto;
            padding: 30px 20px;
        }
        
        .bienvenida {
            background: linear-gradient(135deg, #1e1e2f 0%, #2a2a40 100%);
            color: #fff;
            padding: 40px;
            border-radius: 20px;
            margin-bottom: 30px;
            position: relative;
            overflow: hidden;
        }
        .bienvenida::before {
            content: '';
            position: absolute;
            right: -50px; top: -50px;
            width: 250px; height: 250px;
            background: radial-gradient(circle, rgba(212, 175, 55, 0.3), transparent);
            border-radius: 50%;
        }
        .bienvenida h1 {
            font-family: 'Cinzel', serif;
            color: var(--gold);
            font-size: 32px;
            margin-bottom: 10px;
            position: relative;
            z-index: 2;
        }
        .bienvenida p {
            font-size: 15px;
            opacity: 0.9;
            position: relative;
            z-index: 2;
        }
        .bienvenida .icono {
            position: absolute;
            right: 40px; top: 50%;
            transform: translateY(-50%);
            font-size: 120px;
            color: var(--gold);
            opacity: 0.15;
        }
        
        .grid-opciones {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
        }
        .card-opcion {
            background: #fff;
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
            transition: all 0.3s;
            text-decoration: none;
            color: #333;
            display: block;
        }
        .card-opcion:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.12);
            text-decoration: none;
            color: #333;
        }
        .card-opcion .icono {
            width: 80px; height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, #d4af37 0%, #b8941f 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            color: #fff;
            font-size: 32px;
        }
        .card-opcion h3 {
            font-size: 18px;
            margin-bottom: 10px;
            color: #1e1e2f;
            font-weight: 600;
        }
        .card-opcion p {
            font-size: 13px;
            color: #888;
        }
        .card-salir {
            background: linear-gradient(135deg, #f44336 0%, #c62828 100%);
            color: #fff;
            max-width: 300px;
            margin: 30px auto 0;
        }
        .card-salir .icono {
            background: rgba(255,255,255,0.2);
        }
        .card-salir h3, .card-salir p { color: #fff; }
    </style>
</head>
<body>

<!-- HEADER -->
<header class="header-proveedor">
    <div class="container">
        <a href="proveedor.php" class="logo">
            <i class="fas fa-truck"></i>
            SUACE - Proveedor
        </a>
        <nav class="nav-proveedor">
            <span class="badge-user">
                <i class="fas fa-user-circle"></i>
                <?php echo htmlspecialchars($usuario['nombre']); ?>
            </span>
            <a href="salir_cliente.php">
                <i class="fas fa-sign-out-alt"></i> Salir
            </a>
        </nav>
    </div>
</header>

<!-- MAIN -->
<div class="main">

    <!-- BIENVENIDA -->
    <div class="bienvenida">
        <i class="fas fa-boxes icono"></i>
        <h1>Panel de Proveedor</h1>
        <p>
            Bienvenido, <strong><?php echo htmlspecialchars($usuario['nombre']); ?></strong>. 
            Desde aquí puedes gestionar tus productos y pedidos.
        </p>
    </div>

    <!-- OPCIONES -->
    <div class="grid-opciones">
        
        <a href="admin/src/productos.php" class="card-opcion">
            <div class="icono"><i class="fas fa-box"></i></div>
            <h3>Mis Productos</h3>
            <p>Ver y gestionar tu catálogo de productos</p>
        </a>
        
        <a href="admin/src/pedidos.php" class="card-opcion">
            <div class="icono"><i class="fas fa-shopping-bag"></i></div>
            <h3>Pedidos Recibidos</h3>
            <p>Ver pedidos pendientes y en proceso</p>
        </a>
        
        <a href="comprar.php" class="card-opcion">
            <div class="icono"><i class="fas fa-store"></i></div>
            <h3>Ver Tienda</h3>
            <p>Ver la tienda pública</p>
        </a>
        
    </div>
    
    <a href="salir_cliente.php" class="card-opcion card-salir">
        <div class="icono"><i class="fas fa-sign-out-alt"></i></div>
        <h3>Cerrar Sesión</h3>
        <p>Salir del panel de proveedor</p>
    </a>

</div>

</body>
</html>