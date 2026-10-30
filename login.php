<?php
session_start();

// Si ya está logueado, redirigir según su rol
if (!empty($_SESSION['active'])) {
    if (($_SESSION['rol'] ?? 'admin') == 'admin') {
        header('Location: admin/index.php');
        exit;
    } elseif (($_SESSION['rol'] ?? '') == 'proveedor') {
        header('Location: proveedor.php');
        exit;
    } elseif (($_SESSION['rol'] ?? '') == 'cliente') {
        if (!empty($_SESSION['redirect_after_login'])) {
            $redirect = $_SESSION['redirect_after_login'];
            unset($_SESSION['redirect_after_login']);
            header("Location: $redirect");
            exit;
        } else {
            header('Location: comprar.php');
            exit;
        }
    }
}

$alert = '';

if (!empty($_POST)) {
    if (empty($_POST['usuario']) || empty($_POST['clave'])) {
        $alert = 'Ingrese usuario y contraseña';
    } else {
        require_once "admin/conexion.php";
        
        $user = mysqli_real_escape_string($conexion, $_POST['usuario']);
        $clave = md5(mysqli_real_escape_string($conexion, $_POST['clave']));
        
        $query = mysqli_query($conexion, "SELECT * FROM usuario WHERE usuario = '$user' AND clave = '$clave'");
        $resultado = mysqli_num_rows($query);
        
        if ($resultado > 0) {
            $dato = mysqli_fetch_array($query);
            $_SESSION['active'] = true;
            $_SESSION['idUser'] = $dato['idusuario'];
            $_SESSION['nombre'] = $dato['nombre'];
            $_SESSION['user'] = $dato['usuario'];
            $_SESSION['rol'] = $dato['rol'] ?? 'admin';
            
            mysqli_close($conexion);
            
            // ✅ REDIRECCIÓN SEGÚN ROL
            if ($_SESSION['rol'] == 'admin') {
                header('Location: admin/index.php');
                exit;
            } 
            elseif ($_SESSION['rol'] == 'proveedor') {
                header('Location: proveedor.php');
                exit;
            } 
            elseif ($_SESSION['rol'] == 'cliente') {
                if (!empty($_SESSION['redirect_after_login'])) {
                    $redirect = $_SESSION['redirect_after_login'];
                    unset($_SESSION['redirect_after_login']);
                    header("Location: $redirect");
                    exit;
                } else {
                    header('Location: comprar.php');
                    exit;
                }
            } 
            else {
                // Rol desconocido → cerrar sesión
                session_destroy();
                header('Location: login.php');
                exit;
            }
        } else {
            $alert = 'Usuario o contraseña incorrectos';
            session_destroy();
            session_start();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🍷 Iniciar Sesión | SUACÉ</title>
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
        }
        body {
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #0a0e1a 0%, #1a1f35 50%, #0a0e1a 100%);
            overflow: hidden;
            position: relative;
            padding: 20px;
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
        
        .login-wrapper {
            position: relative; z-index: 10;
            display: flex; width: 950px; max-width: 100%;
            min-height: 600px;
            background: rgba(19, 24, 38, 0.85);
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

        .brand-panel {
            flex: 1;
            background: linear-gradient(160deg, #1a1f35 0%, #0a0e1a 100%);
            padding: 60px 40px;
            display: flex; flex-direction: column;
            justify-content: center; align-items: center;
            text-align: center;
            border-right: 1px solid rgba(212, 175, 55, 0.15);
        }
        .logo-circle {
            width: 130px; height: 130px; border-radius: 50%;
            background: linear-gradient(145deg, #1c2233, #0a0e1a);
            border: 2px solid var(--gold);
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 30px;
            box-shadow: 0 0 40px rgba(212, 175, 55, 0.3);
        }
        .logo-circle i {
            font-size: 55px; color: var(--gold);
            text-shadow: 0 0 20px rgba(212, 175, 55, 0.6);
        }
        .brand-title {
            font-family: 'Cinzel', serif; font-size: 32px;
            font-weight: 700; color: var(--gold);
            letter-spacing: 3px; margin-bottom: 10px;
        }
        .brand-subtitle {
            font-size: 13px; color: var(--text-gray);
            letter-spacing: 4px; text-transform: uppercase; margin-bottom: 25px;
        }
        .brand-slogan {
            font-size: 14px; color: var(--text-gray);
            font-style: italic; line-height: 1.6; max-width: 250px;
        }

        .login-panel {
            flex: 1; padding: 60px 50px;
            display: flex; flex-direction: column; justify-content: center;
        }
        .login-header { margin-bottom: 30px; }
        .login-header h2 {
            font-family: 'Cinzel', serif; font-size: 28px;
            color: var(--text-light); font-weight: 500;
            letter-spacing: 2px; margin-bottom: 8px;
        }
        .login-header p { color: var(--text-gray); font-size: 14px; }
        .login-header .gold-line {
            width: 50px; height: 2px;
            background: linear-gradient(90deg, var(--gold), transparent);
            margin-top: 15px;
        }

        .alert-custom {
            background: rgba(220, 38, 38, 0.15);
            border: 1px solid rgba(220, 38, 38, 0.4);
            color: #fca5a5; padding: 12px 16px;
            border-radius: 10px; font-size: 13px;
            margin-bottom: 20px;
            display: flex; align-items: center; gap: 10px;
        }

        .input-group { position: relative; margin-bottom: 20px; }
        .input-group .icon {
            position: absolute; left: 18px; top: 50%;
            transform: translateY(-50%); color: var(--gold);
            font-size: 16px; z-index: 2;
        }
        .input-group input {
            width: 100%;
            padding: 16px 50px 16px 50px;
            background: rgba(10, 14, 26, 0.6);
            border: 1.5px solid rgba(212, 175, 55, 0.15);
            border-radius: 12px;
            color: var(--text-light);
            font-size: 15px; font-family: 'Poppins', sans-serif;
            transition: all 0.3s;
            outline: none;
        }
        .input-group input::placeholder { color: #6b7280; }
        .input-group input:focus {
            border-color: var(--gold);
            box-shadow: 0 0 0 4px rgba(212, 175, 55, 0.1);
        }
        .toggle-pass {
            position: absolute; right: 18px; top: 50%;
            transform: translateY(-50%);
            color: var(--text-gray); cursor: pointer;
            font-size: 15px;
        }
        .toggle-pass:hover { color: var(--gold); }

        .btn-login {
            width: 100%; padding: 16px;
            background: linear-gradient(135deg, var(--gold) 0%, var(--gold-dark) 100%);
            border: none; border-radius: 12px;
            color: #0a0e1a; font-size: 15px;
            font-weight: 600; font-family: 'Poppins', sans-serif;
            letter-spacing: 2px; text-transform: uppercase;
            cursor: pointer; transition: all 0.3s;
            display: flex; align-items: center; justify-content: center;
            gap: 10px;
            box-shadow: 0 8px 25px rgba(212, 175, 55, 0.35);
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 35px rgba(212, 175, 55, 0.5);
        }

        .login-footer {
            margin-top: 25px; text-align: center;
            color: var(--text-gray); font-size: 13px;
        }
        .login-footer a {
            color: var(--gold); text-decoration: none; font-weight: 500;
        }
        .login-footer a:hover { text-decoration: underline; }

        .back-home {
            display: block; text-align: center;
            margin-top: 15px; color: var(--text-gray);
            text-decoration: none; font-size: 13px;
        }
        .back-home:hover { color: var(--gold); }

        .role-info {
            display: flex; margin-top: 20px;
            padding: 12px; background: rgba(212, 175, 55, 0.08);
            border-radius: 10px; border-left: 3px solid var(--gold);
            font-size: 12px; color: var(--text-gray);
        }
        .role-info i { color: var(--gold); margin-right: 5px; }

        @media (max-width: 850px) {
            .login-wrapper { flex-direction: column; min-height: auto; }
            .brand-panel { padding: 40px 30px; border-right: none; border-bottom: 1px solid rgba(212, 175, 55, 0.15); }
            .logo-circle { width: 100px; height: 100px; }
            .logo-circle i { font-size: 42px; }
            .brand-title { font-size: 24px; }
            .login-panel { padding: 40px 30px; }
        }
    </style>
</head>
<body>

    <div class="login-wrapper">
        
        <div class="brand-panel">
            <div class="logo-circle">
                <i class="fas fa-wine-bottle"></i>
            </div>
            <h1 class="brand-title">SUACÉ</h1>
            <p class="brand-subtitle">Sabores Exóticos</p>
            <p class="brand-slogan">
                "El mejor sabor de mi tierra, directo a tu mesa"
            </p>
        </div>

        <div class="login-panel">
            
            <div class="login-header">
                <h2>Iniciar Sesión</h2>
                <p>Accede a tu cuenta para continuar</p>
                <div class="gold-line"></div>
            </div>

            <?php if (!empty($alert)): ?>
                <div class="alert-custom">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span><?php echo $alert; ?></span>
                </div>
            <?php endif; ?>

            <form action="" method="POST" autocomplete="off">
                
                <div class="input-group">
                    <input type="text" name="usuario" id="usuario" placeholder="Usuario" required autofocus>
                    <i class="fas fa-user icon"></i>
                </div>

                <div class="input-group">
                    <input type="password" name="clave" id="clave" placeholder="Contraseña" required>
                    <i class="fas fa-lock icon"></i>
                    <i class="fas fa-eye toggle-pass" onclick="togglePassword()"></i>
                </div>

                <button type="submit" class="btn-login">
                    <i class="fas fa-sign-in-alt"></i>
                    <span>Iniciar Sesión</span>
                </button>
            </form>

            <div class="login-footer">
                <p>¿No tienes cuenta? <a href="registro.php">Regístrate aquí</a></p>
                <a href="home.html" class="back-home">
                    <i class="fas fa-arrow-left"></i> Volver al inicio
                </a>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            var input = document.getElementById('clave');
            var icon = document.querySelector('.toggle-pass');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>

</body>
</html>