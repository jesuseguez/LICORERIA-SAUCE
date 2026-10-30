<?php
session_start();
if (!empty($_SESSION['active'])) {
    header('Location: login.php');
    exit;
}

$alert = '';
$tipo = '';

if (!empty($_POST)) {
    require_once "admin/conexion.php";
    
    $nombre = mysqli_real_escape_string($conexion, $_POST['nombre']);
    $correo = mysqli_real_escape_string($conexion, $_POST['correo']);
    $telefono = mysqli_real_escape_string($conexion, $_POST['telefono']);
    $direccion = mysqli_real_escape_string($conexion, $_POST['direccion']);
    $usuario = mysqli_real_escape_string($conexion, $_POST['usuario']);
    $clave = md5(mysqli_real_escape_string($conexion, $_POST['clave']));
    
    if (empty($nombre) || empty($correo) || empty($usuario) || empty($_POST['clave'])) {
        $alert = 'Todos los campos obligatorios deben ser llenados';
        $tipo = 'error';
    } else {
        // Verificar si el usuario ya existe
        $verificar = mysqli_query($conexion, "SELECT * FROM usuario WHERE usuario = '$usuario' OR correo = '$correo'");
        
        if (mysqli_num_rows($verificar) > 0) {
            $alert = 'El usuario o correo ya está registrado';
            $tipo = 'error';
        } else {
            $insert = mysqli_query($conexion, "INSERT INTO usuario (nombre, correo, telefono, direccion, usuario, clave, rol) 
                                              VALUES ('$nombre', '$correo', '$telefono', '$direccion', '$usuario', '$clave', 'cliente')");
            
            if ($insert) {
                $alert = '¡Cuenta creada exitosamente! Ya puedes iniciar sesión';
                $tipo = 'success';
            } else {
                $alert = 'Error al crear la cuenta: ' . mysqli_error($conexion);
                $tipo = 'error';
            }
        }
    }
    mysqli_close($conexion);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>📝 Registro | SUACÉ</title>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        :root {
            --gold: #d4af37;
            --gold-dark: #b8941f;
            --dark: #0a0e1a;
            --text-light: #f5f5f5;
            --text-gray: #a8b0c2;
            --red-wine: #722f37;
        }
        body {
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #0a0e1a 0%, #1a1f35 50%, #0a0e1a 100%);
            padding: 20px;
            position: relative;
            overflow-x: hidden;
        }
        body::before {
            content: '';
            position: absolute;
            top: -50%; left: -50%;
            width: 200%; height: 200%;
            background: radial-gradient(circle at 20% 30%, rgba(212, 175, 55, 0.08) 0%, transparent 40%),
                        radial-gradient(circle at 80% 70%, rgba(114, 47, 55, 0.15) 0%, transparent 40%);
            animation: rotateBg 30s linear infinite;
        }
        @keyframes rotateBg { to { transform: rotate(360deg); } }

        .register-wrapper {
            position: relative; z-index: 10;
            width: 900px; max-width: 100%;
            background: rgba(19, 24, 38, 0.9);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            border: 1px solid rgba(212, 175, 55, 0.2);
            box-shadow: 0 30px 80px rgba(0, 0, 0, 0.7);
            overflow: hidden;
            animation: fadeInUp 0.8s ease;
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .register-header {
            padding: 35px;
            background: linear-gradient(160deg, #1a1f35 0%, #0a0e1a 100%);
            border-bottom: 1px solid rgba(212, 175, 55, 0.15);
            text-align: center;
        }
        .register-header h1 {
            font-family: 'Cinzel', serif;
            color: var(--gold); font-size: 28px;
            letter-spacing: 3px; margin-bottom: 8px;
        }
        .register-header p { color: var(--text-gray); font-size: 14px; }

        .register-body { padding: 40px; }

        .alert-box {
            padding: 14px 18px;
            border-radius: 10px;
            font-size: 14px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .alert-error {
            background: rgba(220, 38, 38, 0.15);
            border: 1px solid rgba(220, 38, 38, 0.4);
            color: #fca5a5;
        }
        .alert-success {
            background: rgba(34, 197, 94, 0.15);
            border: 1px solid rgba(34, 197, 94, 0.4);
            color: #86efac;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
        }
        @media (max-width: 700px) {
            .form-grid { grid-template-columns: 1fr; }
        }

        .input-group { position: relative; }
        .input-group.full-width { grid-column: 1 / -1; }
        .input-group .icon {
            position: absolute; left: 16px; top: 50%;
            transform: translateY(-50%); color: var(--gold);
            font-size: 15px; z-index: 2;
        }
        .input-group input {
            width: 100%;
            padding: 14px 16px 14px 45px;
            background: rgba(10, 14, 26, 0.6);
            border: 1.5px solid rgba(212, 175, 55, 0.15);
            border-radius: 10px;
            color: var(--text-light);
            font-size: 14px; font-family: 'Poppins', sans-serif;
            transition: all 0.3s;
            outline: none;
        }
        .input-group input::placeholder { color: #6b7280; font-weight: 300; }
        .input-group input:focus {
            border-color: var(--gold);
            background: rgba(10, 14, 26, 0.9);
            box-shadow: 0 0 0 4px rgba(212, 175, 55, 0.1);
        }

        .btn-register {
            grid-column: 1 / -1;
            padding: 15px;
            background: linear-gradient(135deg, var(--gold) 0%, var(--gold-dark) 100%);
            border: none; border-radius: 10px;
            color: #0a0e1a; font-size: 15px;
            font-weight: 600; font-family: 'Poppins', sans-serif;
            letter-spacing: 2px; text-transform: uppercase;
            cursor: pointer; transition: all 0.3s;
            display: flex; align-items: center; justify-content: center;
            gap: 10px;
            box-shadow: 0 8px 25px rgba(212, 175, 55, 0.35);
            margin-top: 10px;
        }
        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 35px rgba(212, 175, 55, 0.5);
        }

        .login-link {
            text-align: center;
            margin-top: 25px;
            color: var(--text-gray);
            font-size: 14px;
        }
        .login-link a {
            color: var(--gold); text-decoration: none;
            font-weight: 500;
        }
        .login-link a:hover { text-decoration: underline; }
    </style>
</head>
<body>

    <div class="register-wrapper">
        <div class="register-header">
            <h1>🍷 CREAR CUENTA</h1>
            <p>Únete a SUACÉ - Sabores Exóticos de mi Tierra</p>
        </div>

        <div class="register-body">
            
            <?php if (!empty($alert)): ?>
                <div class="alert-box <?php echo $tipo == 'success' ? 'alert-success' : 'alert-error'; ?>">
                    <i class="fas <?php echo $tipo == 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle'; ?>"></i>
                    <span><?php echo $alert; ?></span>
                </div>
            <?php endif; ?>

            <form action="" method="POST" autocomplete="off">
                <div class="form-grid">
                    
                    <div class="input-group full-width">
                        <input type="text" name="nombre" placeholder="Nombre completo *" required>
                        <i class="fas fa-user icon"></i>
                    </div>

                    <div class="input-group">
                        <input type="email" name="correo" placeholder="Correo electrónico *" required>
                        <i class="fas fa-envelope icon"></i>
                    </div>

                    <div class="input-group">
                        <input type="text" name="telefono" placeholder="Teléfono">
                        <i class="fas fa-phone icon"></i>
                    </div>

                    <div class="input-group full-width">
                        <input type="text" name="direccion" placeholder="Dirección">
                        <i class="fas fa-map-marker-alt icon"></i>
                    </div>

                    <div class="input-group">
                        <input type="text" name="usuario" placeholder="Usuario *" required>
                        <i class="fas fa-at icon"></i>
                    </div>

                    <div class="input-group">
                        <input type="password" name="clave" placeholder="Contraseña *" required>
                        <i class="fas fa-lock icon"></i>
                    </div>

                    <button type="submit" class="btn-register">
                        <i class="fas fa-user-plus"></i>
                        <span>Registrarme</span>
                    </button>
                </div>
            </form>

            <div class="login-link">
                ¿Ya tienes cuenta? <a href="login.php">Inicia sesión aquí</a>
            </div>
        </div>
    </div>

</body>
</html>