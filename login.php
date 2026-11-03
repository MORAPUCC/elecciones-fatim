<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso Docente - Sistema Electoral Escolar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { 
            background: linear-gradient(135deg, #eef2f7 0%, #dbe5f1 100%); 
            font-family: 'Segoe UI', sans-serif; 
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 20px;
        }

        .card-login { 
            border-radius: 20px; 
            border: none; 
            box-shadow: 0 12px 35px rgba(0, 0, 0, 0.1); 
            width: 100%; 
            max-width: 480px;
            background: #ffffff;
            overflow: hidden;
        }

        .header-login {
            background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
            color: white;
            padding: 35px 25px 25px 25px;
            text-align: center;
        }

        .icon-circle {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.2);
            border: 2px solid rgba(255, 255, 255, 0.4);
            backdrop-filter: blur(5px);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px auto;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .form-control, .input-group-text {
            border-radius: 10px;
            padding: 12px 15px;
        }

        .input-group-text {
            background-color: #f8f9fa;
            color: #0d6efd;
            border-right: none;
        }

        .form-control {
            border-left: none;
            font-size: 0.95rem;
        }

        .form-control:focus {
            box-shadow: none;
            border-color: #ced4da;
        }

        .input-group:focus-within {
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
            border-radius: 10px;
        }

        .input-group:focus-within .input-group-text,
        .input-group:focus-within .form-control {
            border-color: #0d6efd;
        }

        .btn-login {
            background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-size: 1rem;
            letter-spacing: 0.5px;
            transition: all 0.25s ease;
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.25);
        }

        .btn-login:hover {
            background: linear-gradient(135deg, #0b5ed7 0%, #0a58ca 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(13, 110, 253, 0.35);
        }

        .btn-volver {
            color: #6c757d;
            font-weight: 600;
            transition: color 0.2s ease;
        }

        .btn-volver:hover {
            color: #0d6efd;
        }
    </style>
</head>
<body>

<div class="card card-login">
    
    <div class="header-login">
        <div class="icon-circle">
            <i class="fas fa-user-shield fa-2x text-white"></i>
        </div>
        <h3 class="fw-bold mb-1">Panel Docente</h3>
        <p class="mb-0 text-white-50 small">Sistema Digital de Elecciones Escolares 2026</p>
    </div>

    <div class="p-4 p-md-5">

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger py-2.5 px-3 small text-center rounded-3 mb-4 d-flex align-items-center justify-content-center gap-2" role="alert">
                <i class="fas fa-exclamation-circle fa-lg"></i>
                <div>
                    <?php if ($_GET['error'] === 'empty'): ?>
                        Por favor ingrese su usuario y contraseña.
                    <?php else: ?>
                        Usuario o contraseña incorrectos.
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <form action="procesar_login.php" method="POST">
            
            <div class="mb-4">
                <label class="form-label fw-semibold small text-secondary">Nombre de Usuario</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                    <input type="text" name="usuario" class="form-control" placeholder="Ingrese su usuario" required autocomplete="off">
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold small text-secondary">Contraseña de Acceso</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input type="password" name="clave" class="form-control" placeholder="••••••••" required>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-login w-100 fw-bold mb-4">
                <i class="fas fa-sign-in-alt me-2"></i>Ingresar al Sistema
            </button>
        </form>

        <div class="text-center pt-2 border-top">
            <a href="index.php" class="text-decoration-none small btn-volver d-inline-flex align-items-center">
                <i class="fas fa-arrow-left me-2"></i> Volver a la Vista de Votación
            </a>
        </div>
    </div>
</div>

</body>
</html>
