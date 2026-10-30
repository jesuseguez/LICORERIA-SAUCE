<?php
session_start();

// 🔒 Requerir login
if (empty($_SESSION['active'])) {
    $_SESSION['redirect_after_login'] = 'checkout.php';
    header('Location: login.php');
    exit;
}

require_once "admin/conexion.php";

// Traer datos del usuario
$idUser = $_SESSION['idUser'];
$userQ = mysqli_query($conexion, "SELECT * FROM usuario WHERE idusuario = $idUser");
$usuario = mysqli_fetch_assoc($userQ);

// Traer config (WhatsApp)
$configQ = mysqli_query($conexion, "SELECT * FROM configuracion LIMIT 1");
$config = mysqli_fetch_assoc($configQ);
$telefonoAdmin = $config['telefono'] ?? '59171621436';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Checkout | SUACE</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        :root { --gold: #d4af37; --dark: #0a0e1a; }
        body { font-family: 'Poppins', sans-serif; background: #f5f6fa; }
        
        .header-checkout {
            background: linear-gradient(135deg, #0a0e1a 0%, #1a1f35 100%);
            padding: 15px 0;
            color: #fff;
        }
        
        .header-checkout a { color: var(--gold); text-decoration: none; }
        
        .card-checkout {
            background: #fff;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        }
        
        .card-checkout h4 {
            color: var(--dark);
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 2px solid #f0f0f0;
        }
        
        .opcion-pago {
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            padding: 18px;
            margin-bottom: 12px;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .opcion-pago:hover {
            border-color: var(--gold);
            background: #fdfaf0;
        }
        
        .opcion-pago.seleccionado {
            border-color: var(--gold);
            background: #fdfaf0;
            box-shadow: 0 4px 15px rgba(212, 175, 55, 0.2);
        }
        
        .opcion-pago input[type="radio"] {
            width: 20px;
            height: 20px;
            accent-color: var(--gold);
        }
        
        .opcion-pago .icono {
            font-size: 30px;
            color: var(--gold);
        }
        
        .opcion-pago .texto strong {
            display: block;
            font-size: 15px;
        }
        
        .opcion-pago .texto small {
            color: #666;
            font-size: 12px;
        }
        
        .qr-container {
            text-align: center;
            padding: 20px;
            background: #f9f9f9;
            border-radius: 12px;
            margin-top: 15px;
            display: none;
        }
        
        .qr-container.visible { display: block; }
        
        .qr-container img {
            width: 220px;
            height: 220px;
            border: 3px solid var(--gold);
            border-radius: 12px;
            padding: 8px;
            background: #fff;
        }
        
        .datos-banco {
            background: #f9f9f9;
            border-radius: 12px;
            padding: 18px;
            margin-top: 15px;
            display: none;
        }
        
        .datos-banco.visible { display: block; }
        
        .datos-banco .fila {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px dashed #ddd;
            font-size: 14px;
        }
        
        .datos-banco .fila:last-child { border-bottom: none; }
        
        .btn-comprar {
            background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);
            color: #fff;
            border: none;
            padding: 16px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 16px;
            width: 100%;
            text-transform: uppercase;
            letter-spacing: 1px;
            cursor: pointer;
            box-shadow: 0 8px 25px rgba(40, 167, 69, 0.35);
        }
        
        .btn-comprar:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 35px rgba(40, 167, 69, 0.5);
        }
        
        .resumen-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            font-size: 14px;
        }
        
        .resumen-total {
            display: flex;
            justify-content: space-between;
            padding: 15px 0;
            border-top: 2px solid var(--gold);
            margin-top: 10px;
            font-size: 20px;
            font-weight: 700;
            color: var(--gold);
        }
    </style>
</head>
<body>

<div class="header-checkout">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <a href="comprar.php"><i class="fas fa-arrow-left"></i> Volver a la tienda</a>
            <h5 class="mb-0"><i class="fas fa-lock"></i> Checkout Seguro</h5>
            <span><?php echo htmlspecialchars($usuario['nombre']); ?></span>
        </div>
    </div>
</div>

<div class="container my-4">
    <div class="row">
        <div class="col-lg-7">
            <div class="card-checkout">
                <h4><i class="fas fa-user"></i> Datos del Cliente</h4>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Nombre</label>
                        <input type="text" id="nombre" class="form-control" value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Teléfono</label>
                        <input type="text" id="telefono" class="form-control" value="<?php echo htmlspecialchars($usuario['telefono'] ?? ''); ?>" required>
                    </div>
                    <div class="col-12 mb-3">
                        <label>Dirección de entrega</label>
                        <input type="text" id="direccion" class="form-control" value="<?php echo htmlspecialchars($usuario['direccion'] ?? ''); ?>" required>
                    </div>
                    <div class="col-12">
                        <label>Notas adicionales (opcional)</label>
                        <textarea id="notas" class="form-control" rows="2" placeholder="Ej: Entregar después de las 18:00"></textarea>
                    </div>
                </div>
            </div>
            
            <div class="card-checkout">
                <h4><i class="fas fa-credit-card"></i> Método de Pago</h4>
                
                <label class="opcion-pago seleccionado" onclick="seleccionarMetodo('QR', this)">
                    <input type="radio" name="metodo_pago" value="QR" checked>
                    <div class="icono"><i class="fas fa-qrcode"></i></div>
                    <div class="texto">
                        <strong>Pago por QR</strong>
                        <small>Escanea el código QR desde tu app bancaria</small>
                    </div>
                </label>
                
                <label class="opcion-pago" onclick="seleccionarMetodo('Transferencia', this)">
                    <input type="radio" name="metodo_pago" value="Transferencia">
                    <div class="icono"><i class="fas fa-university"></i></div>
                    <div class="texto">
                        <strong>Transferencia Bancaria</strong>
                        <small>Transfiere a nuestra cuenta</small>
                    </div>
                </label>
                
                <div class="qr-container visible" id="qr-container">
                    <p><strong>Escanea este código QR</strong></p>
                    <img src="image/qr_pago.png" alt="QR de Pago" onerror="this.style.display='none'; this.insertAdjacentHTML('afterend','<p class=\'text-muted\'>Configura tu QR en: image/qr_pago.png</p>')">
                    <p class="text-muted mt-2">Monto a pagar: <strong id="monto-qr" style="color:#d4af37;">Bs. 0.00</strong></p>
                </div>
                
                <div class="datos-banco" id="banco-container">
                    <h6><i class="fas fa-info-circle"></i> Datos Bancarios</h6>
                    <div class="fila"><span>Banco:</span><strong>Banco Unión S.A.</strong></div>
                    <div class="fila"><span>Titular:</span><strong>SUACE - Sabores Exóticos</strong></div>
                    <div class="fila"><span>Cuenta:</span><strong>1234567890</strong></div>
                    <div class="fila"><span>NIT:</span><strong>12345678901</strong></div>
                    <div class="fila"><span>Monto:</span><strong id="monto-banco" style="color:#d4af37;">Bs. 0.00</strong></div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-5">
            <div class="card-checkout">
                <h4><i class="fas fa-receipt"></i> Resumen del Pedido</h4>
                <div id="resumen-productos">
                    <!-- Se llena con JS -->
                </div>
                <div class="resumen-total">
                    <span>TOTAL:</span>
                    <span id="resumen-total">Bs. 0.00</span>
                </div>
            </div>
            
            <div class="card-checkout">
                <h4><i class="fas fa-headset"></i> ¿Dudas con tu pedido?</h4>
                <p style="font-size:13px;color:#666;margin-bottom:15px;">
                    Contáctate con el administrador por WhatsApp para resolver cualquier consulta.
                </p>
                <a href="https://wa.me/<?php echo $telefonoAdmin; ?>?text=Hola,%20necesito%20ayuda%20con%20mi%20pedido%20en%20SUACE" 
                   target="_blank"
                   style="background: #25D366; color:#fff; padding:12px; border-radius:10px; display:flex; align-items:center; justify-content:center; gap:10px; text-decoration:none; font-weight:600;">
                    <i class="fab fa-whatsapp" style="font-size:22px;"></i>
                    Contactar por WhatsApp
                </a>
            </div>
            
            <button class="btn-comprar" onclick="realizarCompra()">
                <i class="fas fa-check-circle"></i> Confirmar Compra
            </button>
        </div>
    </div>
</div>

<script>
let carrito = JSON.parse(localStorage.getItem('carrito_suace')) || [];
let metodoSeleccionado = 'QR';

function cargarResumen() {
    let total = 0;
    const div = document.getElementById('resumen-productos');
    
    if (carrito.length === 0) {
        div.innerHTML = '<p class="text-muted text-center">Carrito vacío</p>';
        return;
    }
    
    div.innerHTML = carrito.map(item => {
        const subtotal = item.precio * item.cantidad;
        total += subtotal;
        return `
            <div class="resumen-item">
                <span>${item.nombre} <small class="text-muted">x${item.cantidad}</small></span>
                <strong>Bs. ${subtotal.toFixed(2)}</strong>
            </div>
        `;
    }).join('');
    
    document.getElementById('resumen-total').textContent = 'Bs. ' + total.toFixed(2);
    document.getElementById('monto-qr').textContent = 'Bs. ' + total.toFixed(2);
    document.getElementById('monto-banco').textContent = 'Bs. ' + total.toFixed(2);
}

function seleccionarMetodo(metodo, elemento) {
    metodoSeleccionado = metodo;
    document.querySelectorAll('.opcion-pago').forEach(el => el.classList.remove('seleccionado'));
    elemento.classList.add('seleccionado');
    
    document.getElementById('qr-container').classList.toggle('visible', metodo === 'QR');
    document.getElementById('banco-container').classList.toggle('visible', metodo === 'Transferencia');
}

function realizarCompra() {
    const nombre = document.getElementById('nombre').value.trim();
    const telefono = document.getElementById('telefono').value.trim();
    const direccion = document.getElementById('direccion').value.trim();
    const notas = document.getElementById('notas').value.trim();
    
    if (!nombre || !telefono || !direccion) {
        Swal.fire('Atención', 'Completa todos los campos obligatorios', 'warning');
        return;
    }
    
    if (carrito.length === 0) {
        Swal.fire('Error', 'Tu carrito está vacío', 'error');
        return;
    }
    
    const total = carrito.reduce((sum, item) => sum + (item.precio * item.cantidad), 0);
    
    Swal.fire({
        title: '¿Confirmar compra?',
        html: `Total: <b style="color:#d4af37;font-size:22px;">Bs. ${total.toFixed(2)}</b><br>
               <small>Método: ${metodoSeleccionado}</small>`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '✅ Sí, confirmar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (!result.isConfirmed) return;
        
        const datos = {
            carrito: carrito,
            nombre: nombre,
            telefono: telefono,
            direccion: direccion,
            notas: notas,
            metodo_pago: metodoSeleccionado,
            total: total
        };
        
        $.ajax({
            url: 'procesar_pedido.php',
            type: 'POST',
            data: JSON.stringify(datos),
            contentType: 'application/json',
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    // Guardar datos para WhatsApp
                    sessionStorage.setItem('pedido_id', res.id_pedido);
                    sessionStorage.setItem('pedido_total', total.toFixed(2));
                    
                    Swal.fire({
                        title: '¡Pedido Registrado! 🎉',
                        html: `Tu pedido #<b>${res.id_pedido}</b> ha sido creado.<br>
                               Total: <b>Bs. ${total.toFixed(2)}</b>`,
                        icon: 'success',
                        showCancelButton: true,
                        confirmButtonText: '<i class="fas fa-file-pdf"></i> Descargar Boleta',
                        cancelButtonText: '<i class="fab fa-whatsapp"></i> Contactar Admin',
                        confirmButtonColor: '#d4af37',
                        cancelButtonColor: '#25D366'
                    }).then((r) => {
                        if (r.isConfirmed) {
                            window.open('boleta_pdf.php?id=' + res.id_pedido, '_blank');
                        } else {
                            window.open('https://wa.me/<?php echo $telefonoAdmin; ?>?text=' + 
                                encodeURIComponent('Hola, acabo de hacer el pedido #' + res.id_pedido + ' por Bs. ' + total.toFixed(2) + '. Adjunto mi comprobante de pago.'), '_blank');
                        }
                        localStorage.removeItem('carrito_suace');
                        setTimeout(() => window.location.href = 'comprar.php', 1500);
                    });
                } else {
                    Swal.fire('Error', res.mensaje || 'No se pudo procesar el pedido', 'error');
                }
            },
            error: function(xhr) {
                console.error(xhr.responseText);
                Swal.fire('Error', 'Error de conexión', 'error');
            }
        });
    });
}

cargarResumen();
</script>

</body>
</html>