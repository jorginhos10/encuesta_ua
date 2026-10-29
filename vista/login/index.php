<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <base href="<?php echo BASE_URL; ?>">
    <title>Acceder · Encuestas</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=DM+Serif+Display&display=swap" rel="stylesheet">
    <style>
        :root {
            --accent: #007aff;
            --accent-light: rgba(0,122,255,0.1);
            --red: #ff3b30;
            --text: #1c1c1e;
            --text2: #636366;
            --text3: #aeaeb2;
            --border: rgba(0,0,0,0.1);
            --border-strong: rgba(0,0,0,0.14);
            --surface: #ffffff;
        }
        * { margin:0; padding:0; box-sizing:border-box; -webkit-font-smoothing:antialiased; }

        body {
            font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f2f2f7;
            position: relative;
            overflow: hidden;
        }

        /* Fondo decorativo */
        .bg-orb {
            position: fixed;
            border-radius: 50%;
            filter: blur(80px);
            pointer-events: none;
        }
        .bg-orb-1 {
            width: 500px; height: 500px;
            background: radial-gradient(circle, rgba(0,122,255,0.12) 0%, transparent 70%);
            top: -150px; right: -100px;
        }
        .bg-orb-2 {
            width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(88,86,214,0.1) 0%, transparent 70%);
            bottom: -100px; left: -80px;
        }

        .login-wrap {
            width: 100%;
            max-width: 400px;
            padding: 20px;
            position: relative;
            z-index: 1;
            animation: fadeUp 0.5s cubic-bezier(0.34,1.2,0.64,1);
        }
        @keyframes fadeUp { from { opacity:0; transform:translateY(24px); } to { opacity:1; transform:none; } }

        /* Logo / Brand */
        .brand {
            text-align: center;
            margin-bottom: 32px;
        }
        .brand-icon {
            width: 64px; height: 64px;
            background: linear-gradient(145deg, #007aff, #5856d6);
            border-radius: 18px;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 26px; color: white;
            box-shadow: 0 8px 24px rgba(0,122,255,0.3);
            margin-bottom: 14px;
        }
        .brand-name {
            font-family: 'DM Serif Display', serif;
            font-size: 26px; color: var(--text);
            letter-spacing: -0.5px; line-height: 1;
        }
        .brand-sub {
            font-size: 14px; color: var(--text2);
            margin-top: 5px;
        }

        /* Card */
        .card {
            background: var(--surface);
            border-radius: 22px;
            padding: 32px 28px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.04), 0 8px 32px rgba(0,0,0,0.08);
            border: 1px solid var(--border);
        }

        .card-title {
            font-size: 20px; font-weight: 600;
            color: var(--text); letter-spacing: -0.3px;
            margin-bottom: 6px;
        }
        .card-sub {
            font-size: 13px; color: var(--text2);
            margin-bottom: 24px;
        }

        /* Error */
        .error-box {
            background: rgba(255,59,48,0.08);
            border: 1px solid rgba(255,59,48,0.2);
            border-radius: 12px;
            padding: 12px 14px;
            display: flex; align-items: center; gap: 9px;
            font-size: 13px; color: var(--red);
            margin-bottom: 20px;
        }
        .error-box i { font-size: 15px; flex-shrink: 0; }

        /* Form */
        .form-group { margin-bottom: 16px; }
        .form-label {
            display: block; font-size: 12px; font-weight: 600;
            color: var(--text2); letter-spacing: 0.2px;
            margin-bottom: 7px; text-transform: uppercase;
        }
        .form-input {
            width: 100%; padding: 13px 14px;
            border: 1.5px solid var(--border-strong);
            border-radius: 12px; font-size: 15px;
            font-family: inherit; color: var(--text);
            background: #fafafa;
            outline: none;
            transition: border-color 0.15s, box-shadow 0.15s, background 0.15s;
        }
        .form-input:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 4px var(--accent-light);
            background: white;
        }
        .form-input::placeholder { color: var(--text3); }

        .btn-submit {
            width: 100%; padding: 14px;
            background: linear-gradient(135deg, #007aff, #5856d6);
            color: white; border: none;
            border-radius: 14px; font-family: inherit;
            font-size: 15px; font-weight: 600;
            cursor: pointer; margin-top: 8px;
            box-shadow: 0 4px 16px rgba(0,122,255,0.3);
            transition: transform 0.15s, box-shadow 0.15s, opacity 0.15s;
            letter-spacing: -0.1px;
        }
        .btn-submit:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 24px rgba(0,122,255,0.35);
        }
        .btn-submit:active { transform: translateY(0); opacity: 0.9; }

        .hint {
            text-align: center; margin-top: 20px;
            font-size: 12px; color: var(--text3);
            background: #f7f7f7; border-radius: 10px;
            padding: 10px 14px;
        }
        .hint code {
            font-family: 'SF Mono', monospace;
            background: rgba(0,0,0,0.06);
            padding: 2px 6px; border-radius: 5px;
            font-size: 11px; color: var(--text2);
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="bg-orb bg-orb-1"></div>
    <div class="bg-orb bg-orb-2"></div>

    <div class="login-wrap">
        <?php include __DIR__ . '/../componentes/header.php'; ?>

        <div class="card">
            <div class="card-title">Bienvenido</div>
            <div class="card-sub">Ingresa tus credenciales para continuar</div>

            <?php if(isset($_SESSION['error'])): ?>
            <div class="error-box">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
            <?php endif; ?>

            <form method="POST" action="index.php?action=login">
                <div class="form-group">
                    <label class="form-label">Correo electrónico</label>
                    <input class="form-input" type="email" name="email" required
                           placeholder="tu@correo.com" autocomplete="email">
                </div>
                <div class="form-group">
                    <label class="form-label">Contraseña</label>
                    <input class="form-input" type="password" name="password" required
                           placeholder="••••••••" autocomplete="current-password">
                </div>
                <button type="submit" class="btn-submit">
                    <i class="fas fa-arrow-right" style="margin-right:7px;"></i>Ingresar
                </button>
            </form>

        </div>
    </div>
</body>
</html>
