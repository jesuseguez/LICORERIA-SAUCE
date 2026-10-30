<?php
session_start();
require_once "admin/conexion.php";

// 🔒 Verificación de rol
if (empty($_SESSION['active'])) {
    // Sin sesión → login
    $_SESSION['redirect_after_login'] = 'comprar.php';
    header('Location: login.php');
    exit;
}

$rol = $_SESSION['rol'] ?? 'admin';

// Admin → panel
if ($rol === 'admin') {
    header('Location: admin/index.php');
    exit;
}

// Proveedor → panel proveedor
if ($rol === 'proveedor') {
    header('Location: proveedor.php');
    exit;
}

// Solo clientes pueden ver la tienda
if ($rol !== 'cliente') {
    session_destroy();
    header('Location: login.php');
    exit;
}

// Obtener productos de la BD
$query = mysqli_query($conexion, "SELECT * FROM producto WHERE existencia > 0 ORDER BY categoria, descripcion");
$productos = [];
while ($row = mysqli_fetch_assoc($query)) {
    $productos[] = $row;
}

// Agrupar por categoría
$categorias = [];
foreach ($productos as $p) {
    $cat = $p['categoria'] ?? 'General';
    $categorias[$cat][] = $p;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🍷 Tienda | SUACÉ - Sabores Exóticos</title>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        :root { --gold: #d4af37; --gold-dark: #b8941f; --dark: #0a0e1a; }
        body { font-family: 'Poppins', sans-serif; background: #f5f6fa; min-height: 100vh; }
        
        .header-cliente {
            background: linear-gradient(135deg, #0a0e1a 0%, #1a1f35 100%);
            padding: 15px 0;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .header-cliente .container {
            max-width: 1400px; margin: 0 auto; padding: 0 20px;
            display: flex; justify-content: space-between; align-items: center;
            flex-wrap: wrap; gap: 15px;
        }
        .header-cliente .logo {
            color: var(--gold); font-family: 'Cinzel', serif;
            font-size: 22px; font-weight: 700; text-decoration: none;
            display: flex; align-items: center; gap: 10px;
        }
        .header-cliente .logo:hover { color: var(--gold); text-decoration: none; }
        .header-cliente .logo i { font-size: 26px; }
        
        .nav-cliente { display: flex; align-items: center; gap: 18px; flex-wrap: wrap; }
        .nav-cliente a {
            color: rgba(255,255,255,0.85); text-decoration: none;
            padding: 8px 14px; font-size: 14px; font-weight: 500;
            border-radius: 20px; transition: all 0.3s;
            display: inline-flex; align-items: center; gap: 6px;
        }
        .nav-cliente a:hover { color: var(--gold); background: rgba(212, 175, 55, 0.1); }
        .nav-cliente a.activo { color: var(--gold); background: rgba(212, 175, 55, 0.15); }
        
        .user-dropdown { position: relative; }
        .user-btn {
            display: flex; align-items: center; gap: 8px;
            background: rgba(212, 175, 55, 0.15);
            border: 1px solid rgba(212, 175, 55, 0.3);
            color: #fff; padding: 8px 16px; border-radius: 25px;
            cursor: pointer; font-size: 13px;
        }
        .user-btn:hover { background: rgba(212, 175, 55, 0.25); color: var(--gold); }
        .user-menu {
            position: absolute; top: calc(100% + 8px); right: 0;
            background: #fff; border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.15);
            min-width: 200px; overflow: hidden;
            opacity: 0; visibility: hidden; transform: translateY(-10px);
            transition: all 0.3s; z-index: 200;
        }
        .user-dropdown:hover .user-menu { opacity: 1; visibility: visible; transform: translateY(0); }
        .user-menu a {
            display: flex; align-items: center; gap: 10px;
            padding: 12px 18px; color: #333; font-size: 13px;
            text-decoration: none; border-radius: 0;
        }
        .user-menu a:hover { background: #fdfaf0; color: var(--gold); }
        .user-menu .divider { height: 1px; background: #eee; margin: 4px 0; }
        
        .btn-carrito {
            background: linear-gradient(135deg, var(--gold) 0%, var(--gold-dark) 100%);
            color: var(--dark) !important;
            padding: 9px 18px !important;
            border-radius: 25px !important;
            font-weight: 600;
            display: inline-flex; align-items: center; gap: 8px;
            position: relative;
            box-shadow: 0 4px 15px rgba(212, 175, 55, 0.4);
        }
        .btn-carrito:hover { color: var(--dark) !important; transform: translateY(-2px); text-decoration: none; }
        .badge-carrito {
            position: absolute; top: -6px; right: -6px;
            background: #dc3545; color: #fff;
            border-radius: 50%; width: 20px; height: 20px;
            display: flex; align-items: center; justify-content: center;
            font-size: 10px; font-weight: 700; border: 2px solid var(--dark);
        }
        
        .main-cliente { padding: 30px 20px; max-width: 1400px; margin: 0 auto; }
        
        .filtros-tienda {
            background: #fff; padding: 20px; border-radius: 12px;
            margin-bottom: 25px; box-shadow: 0 2px 12px rgba(0,0,0,0.06);
            display: flex; flex-wrap: wrap; gap: 10px; justify-content: center;
        }
        .btn-filtro {
            padding: 8px 20px; border-radius: 25px;
            background: #f0f0f0; color: #333; border: none;
            font-size: 13px; font-weight: 500; cursor: pointer;
            transition: all 0.3s; font-family: 'Poppins', sans-serif;
        }
        .btn-filtro:hover, .btn-filtro.activo {
            background: linear-gradient(135deg, var(--gold) 0%, var(--gold-dark) 100%);
            color: var(--dark);
            box-shadow: 0 4px 12px rgba(212, 175, 55, 0.35);
        }
        
        .grid-productos {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            gap: 20px;
        }
        .card-producto {
            background: #fff; border-radius: 15px; padding: 20px;
            text-align: center; transition: all 0.3s;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
            display: flex; flex-direction: column;
        }
        .card-producto:hover { transform: translateY(-5px); box-shadow: 0 12px 30px rgba(0,0,0,0.12); }
        .card-producto img {
            width: 130px; height: 150px; object-fit: contain;
            margin: 0 auto 15px; display: block;
        }
        .card-producto .nombre {
            font-weight: 600; font-size: 14px; color: #1a1a1a;
            min-height: 40px; display: flex; align-items: center;
            justify-content: center; margin-bottom: 8px;
        }
        .card-producto .stock { font-size: 12px; color: #28a745; margin-bottom: 10px; }
        .card-producto .precio {
            font-size: 18px; font-weight: 700;
            color: var(--gold); margin: 10px 0;
        }
        
        .selector-cantidad {
            display: flex; align-items: center; justify-content: center;
            gap: 0; margin: 10px auto; width: 130px;
            border: 2px solid #e0e0e0; border-radius: 25px;
            overflow: hidden; background: #fafafa;
        }
        .btn-cant {
            background: #fff; border: none; width: 35px; height: 35px;
            cursor: pointer; font-size: 16px; color: #333;
            font-weight: 700; transition: all 0.2s;
            display: flex; align-items: center; justify-content: center;
        }
        .btn-cant:hover { background: var(--gold); color: #fff; }
        .input-cant {
            width: 60px; height: 35px; border: none;
            text-align: center; font-size: 14px; font-weight: 700;
            background: #fff; color: #1a1a1a;
            font-family: 'Poppins', sans-serif; outline: none;
        }
        .input-cant::-webkit-outer-spin-button,
        .input-cant::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
        .input-cant[type=number] { -moz-appearance: textfield; }
        
        .btn-agregar {
            background: linear-gradient(135deg, var(--gold) 0%, var(--gold-dark) 100%);
            color: var(--dark); border: none; padding: 10px;
            border-radius: 8px; font-weight: 600; font-size: 13px;
            width: 100%; cursor: pointer; transition: all 0.3s;
            display: flex; align-items: center; justify-content: center;
            gap: 6px; font-family: 'Poppins', sans-serif;
            margin-top: auto;
        }
        .btn-agregar:hover { transform: scale(1.03); box-shadow: 0 6px 20px rgba(212, 175, 55, 0.5); }
        
        .overlay-carrito {
            position: fixed; inset: 0; background: rgba(0,0,0,0.5);
            z-index: 9998; display: none;
        }
        .overlay-carrito.abierto { display: block; }
        
        .modal-carrito {
            position: fixed; right: 0; top: 0; width: 420px;
            max-width: 100%; height: 100vh; background: #fff;
            z-index: 9999; transform: translateX(100%);
            transition: transform 0.3s;
            box-shadow: -5px 0 30px rgba(0,0,0,0.2);
            display: flex; flex-direction: column;
        }
        .modal-carrito.abierto { transform: translateX(0); }
        .modal-carrito .header-modal {
            padding: 20px;
            background: linear-gradient(135deg, #0a0e1a 0%, #1a1f35 100%);
            color: #fff; display: flex; justify-content: space-between; align-items: center;
        }
        .modal-carrito .header-modal h5 { margin: 0; font-family: 'Cinzel', serif; letter-spacing: 1px; }
        .modal-carrito .header-modal button { background: transparent; border: none; color: #fff; font-size: 20px; cursor: pointer; }
        .modal-carrito .body-modal { flex: 1; overflow-y: auto; padding: 20px; }
        .modal-carrito .footer-modal { padding: 20px; border-top: 1px solid #eee; }
        
        .item-carrito {
            display: flex; gap: 12px; padding: 15px 0;
            border-bottom: 1px solid #f0f0f0; align-items: center;
        }
        .item-carrito img { width: 55px; height: 55px; object-fit: contain; }
        .item-carrito .info { flex: 1; }
        .item-carrito .info .nom { font-size: 13px; font-weight: 600; margin-bottom: 3px; }
        .item-carrito .info .pre { font-size: 12px; color: var(--gold); font-weight: 600; }
        
        .cant-controles { display: flex; align-items: center; gap: 5px; margin-top: 6px; }
        .cant-btn {
            width: 26px; height: 26px; border-radius: 6px;
            border: 1px solid #ddd; background: #fff;
            cursor: pointer; font-weight: 700; color: #333;
            font-size: 12px; transition: all 0.2s;
            display: flex; align-items: center; justify-content: center;
        }
        .cant-btn:hover { background: var(--gold); color: #fff; border-color: var(--gold); }
        .cant-num {
            min-width: 30px; text-align: center;
            font-weight: 700; font-size: 14px; color: #1a1a1a;
        }
        .item-carrito .btn-eliminar {
            background: #dc3545; color: #fff; border: none;
            border-radius: 50%; width: 28px; height: 28px;
            cursor: pointer; font-size: 11px; transition: all 0.2s;
        }
        .item-carrito .btn-eliminar:hover { background: #b02a37; transform: scale(1.1); }
        
        .btn-checkout {
            background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);
            color: #fff; border: none; padding: 14px;
            border-radius: 10px; font-weight: 600; width: 100%;
            font-size: 15px; text-transform: uppercase;
            letter-spacing: 1px; cursor: pointer;
            font-family: 'Poppins', sans-serif; transition: all 0.3s;
        }
        .btn-checkout:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(40, 167, 69, 0.4); }
        
        @media (max-width: 768px) {
            .header-cliente .container { justify-content: center; }
            .nav-cliente { width: 100%; justify-content: center; }
            .nav-cliente a { font-size: 12px; padding: 6px 10px; }
            .modal-carrito { width: 100%; }
        }
    </style>
</head>
<body>

<header class="header-cliente">
    <div class="container">
        <a href="comprar.php" class="logo">
            <i class="fas fa-wine-bottle"></i> SUACE
        </a>
        <nav class="nav-cliente">
            <a href="comprar.php" class="activo">
                <i class="fas fa-store"></i> Tienda
            </a>
            <a href="mis_pedidos.php">
                <i class="fas fa-receipt"></i> Mis Pedidos
            </a>
            <a href="#" onclick="abrirCarrito(event)" class="btn-carrito">
                <i class="fas fa-shopping-cart"></i> Carrito
                <span class="badge-carrito" id="cart-badge-global">0</span>
            </a>
            <div class="user-dropdown">
                <div class="user-btn">
                    <i class="fas fa-user-circle"></i>
                    <span><?php echo htmlspecialchars($_SESSION['nombre'] ?? 'Cliente'); ?></span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="user-menu">
                    <a href="mis_pedidos.php"><i class="fas fa-receipt"></i> Mis Pedidos</a>
                    <a href="mi_perfil.php"><i class="fas fa-user-edit"></i> Mi Perfil</a>
                    <div class="divider"></div>
                    <a href="salir_cliente.php" style="color:#dc3545;"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
                </div>
            </div>
        </nav>
    </div>
</header>

<div class="main-cliente">
    
    <div class="filtros-tienda">
        <button class="btn-filtro activo" data-cat="all">Todos</button>
        <?php foreach (array_keys($categorias) as $cat): ?>
            <button class="btn-filtro" data-cat="<?php echo htmlspecialchars($cat); ?>">
                <?php echo htmlspecialchars($cat); ?>
            </button>
        <?php endforeach; ?>
    </div>
    
    <div class="grid-productos">
        <?php foreach ($productos as $p): ?>
            <div class="card-producto producto-item" data-cat="<?php echo htmlspecialchars($p['categoria'] ?? 'General'); ?>">
                <img src="<?php echo htmlspecialchars($p['imagen'] ?? 'image/logo2.png'); ?>" 
                     onerror="this.src='image/logo2.png'"
                     alt="<?php echo htmlspecialchars($p['descripcion']); ?>">
                <div class="nombre"><?php echo htmlspecialchars($p['descripcion']); ?></div>
                <div class="stock">
                    <i class="fas fa-check-circle"></i> Stock: <?php echo $p['existencia']; ?>
                </div>
                <div class="precio">Bs. <?php echo number_format($p['precio'], 2); ?></div>
                
                <div class="selector-cantidad">
                    <button type="button" class="btn-cant" onclick="cambiarCantidadCard(this, -1)">−</button>
                    <input type="number" class="input-cant" value="1" min="1" max="<?php echo $p['existencia']; ?>" 
                           id="cant_<?php echo $p['codproducto']; ?>">
                    <button type="button" class="btn-cant" onclick="cambiarCantidadCard(this, 1)">+</button>
                </div>
                
                <button class="btn-agregar" 
                        onclick="agregarAlCarritoConCantidad(
                            <?php echo $p['codproducto']; ?>, 
                            '<?php echo addslashes($p['descripcion']); ?>', 
                            <?php echo $p['precio']; ?>, 
                            '<?php echo addslashes($p['imagen'] ?? 'image/logo2.png'); ?>',
                            'cant_<?php echo $p['codproducto']; ?>',
                            <?php echo $p['existencia']; ?>
                        )">
                    <i class="fas fa-cart-plus"></i> Añadir al Carrito
                </button>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<div class="overlay-carrito" id="overlay-carrito" onclick="cerrarCarrito()"></div>

<div class="modal-carrito" id="modal-carrito">
    <div class="header-modal">
        <h5><i class="fas fa-shopping-cart"></i> Tu Carrito</h5>
        <button onclick="cerrarCarrito()"><i class="fas fa-times"></i></button>
    </div>
    <div class="body-modal" id="carrito-body">
        <p style="text-align:center;color:#999;">Tu carrito está vacío</p>
    </div>
    <div class="footer-modal">
        <div style="display:flex;justify-content:space-between;margin-bottom:15px;">
            <strong>Total:</strong>
            <strong style="color:var(--gold);font-size:20px;" id="carrito-total">Bs. 0.00</strong>
        </div>
        <button class="btn-checkout" onclick="irACheckout()">
            <i class="fas fa-credit-card"></i> Realizar Compra
        </button>
    </div>
</div>

<script>
let carrito = JSON.parse(localStorage.getItem('carrito_suace')) || [];

function guardarCarrito() {
    localStorage.setItem('carrito_suace', JSON.stringify(carrito));
    actualizarCarrito();
}

function cambiarCantidadCard(btn, delta) {
    const input = btn.parentElement.querySelector('.input-cant');
    let valor = parseInt(input.value) || 1;
    valor += delta;
    const min = parseInt(input.min) || 1;
    const max = parseInt(input.max) || 999;
    if (valor < min) valor = min;
    if (valor > max) valor = max;
    input.value = valor;
}

function agregarAlCarritoConCantidad(id, nombre, precio, imagen, inputId, stockMax) {
    const input = document.getElementById(inputId);
    let cantidad = parseInt(input.value) || 1;
    
    if (cantidad < 1) cantidad = 1;
    if (cantidad > stockMax) {
        alert('⚠️ Solo hay ' + stockMax + ' unidades disponibles');
        cantidad = stockMax;
    }
    
    const existe = carrito.find(p => p.id === id);
    if (existe) {
        existe.cantidad += cantidad;
        if (existe.cantidad > stockMax) existe.cantidad = stockMax;
    } else {
        carrito.push({ id, nombre, precio, imagen, cantidad });
    }
    
    guardarCarrito();
    abrirCarrito();
    input.value = 1;
}

function cambiarCantidadCarrito(id, delta) {
    const item = carrito.find(p => p.id === id);
    if (!item) return;
    item.cantidad += delta;
    if (item.cantidad <= 0) {
        eliminarDelCarrito(id);
        return;
    }
    guardarCarrito();
}

function eliminarDelCarrito(id) {
    carrito = carrito.filter(p => p.id !== id);
    guardarCarrito();
}

function actualizarCarrito() {
    const body = document.getElementById('carrito-body');
    const badge = document.getElementById('cart-badge-global');
    const totalEl = document.getElementById('carrito-total');
    
    let total = 0;
    let cantidadTotal = 0;
    
    if (carrito.length === 0) {
        body.innerHTML = '<p style="text-align:center;color:#999;">Tu carrito está vacío</p>';
    } else {
        body.innerHTML = carrito.map(item => {
            const subtotal = item.precio * item.cantidad;
            total += subtotal;
            cantidadTotal += item.cantidad;
            return `
                <div class="item-carrito">
                    <img src="${item.imagen}" onerror="this.src='image/logo2.png'">
                    <div class="info">
                        <div class="nom">${item.nombre}</div>
                        <div class="pre">Bs. ${item.precio.toFixed(2)} c/u</div>
                        <div class="cant-controles">
                            <button class="cant-btn" onclick="cambiarCantidadCarrito(${item.id}, -1)">−</button>
                            <span class="cant-num">${item.cantidad}</span>
                            <button class="cant-btn" onclick="cambiarCantidadCarrito(${item.id}, 1)">+</button>
                            <span style="margin-left:10px;font-size:12px;color:#666;">= Bs. ${subtotal.toFixed(2)}</span>
                        </div>
                    </div>
                    <button class="btn-eliminar" onclick="eliminarDelCarrito(${item.id})">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
        }).join('');
    }
    
    if (badge) badge.textContent = cantidadTotal;
    if (totalEl) totalEl.textContent = 'Bs. ' + total.toFixed(2);
}

function abrirCarrito(event) {
    if (event) event.preventDefault();
    document.getElementById('modal-carrito').classList.add('abierto');
    document.getElementById('overlay-carrito').classList.add('abierto');
}

function cerrarCarrito() {
    document.getElementById('modal-carrito').classList.remove('abierto');
    document.getElementById('overlay-carrito').classList.remove('abierto');
}

function irACheckout() {
    if (carrito.length === 0) {
        alert('El carrito está vacío. Agrega al menos un producto.');
        return;
    }
    window.location.href = 'checkout.php';
}

document.querySelectorAll('.btn-filtro').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.btn-filtro').forEach(b => b.classList.remove('activo'));
        this.classList.add('activo');
        const cat = this.dataset.cat;
        document.querySelectorAll('.producto-item').forEach(item => {
            item.style.display = (cat === 'all' || item.dataset.cat === cat) ? '' : 'none';
        });
    });
});

actualizarCarrito();
</script>

</body>
</html>