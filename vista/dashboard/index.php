<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistema MVC</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
        }
        .navbar {
            background: #333;
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .navbar h1 {
            font-size: 20px;
        }
        .user-info {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .logout-btn {
            background: #dc3545;
            color: white;
            padding: 8px 15px;
            text-decoration: none;
            border-radius: 5px;
        }
        .logout-btn:hover {
            background: #c82333;
        }
        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 20px;
        }
        .dashboard-card {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .welcome-message {
            font-size: 24px;
            margin-bottom: 20px;
            color: #333;
        }
        .user-details {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin-top: 20px;
        }
        .user-details p {
            margin: 10px 0;
            font-size: 16px;
        }
        .user-details strong {
            color: #667eea;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>Sistema MVC - Dashboard</h1>
        <div class="user-info">
            <span>Bienvenido, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
            <a href="index.php?action=logout" class="logout-btn">Cerrar Sesión</a>
        </div>
    </div>
    
    <div class="container">
        <div class="dashboard-card">
            <div class="welcome-message">
                ¡Bienvenido al Dashboard!
            </div>
            <p>Has iniciado sesión correctamente en el sistema MVC.</p>
            
            <div class="user-details">
                <h3>Información del Usuario:</h3>
                <p><strong>ID:</strong> <?php echo htmlspecialchars($_SESSION['user_id']); ?></p>
                <p><strong>Nombre:</strong> <?php echo htmlspecialchars($_SESSION['user_name']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['user_email']); ?></p>
                <?php if(isset($usuario)): ?>
                <p><strong>Última conexión:</strong> <?php echo date('d/m/Y H:i:s'); ?></p>
                <?php endif; ?>
            </div>
            
            <div style="margin-top: 30px;">
                <h3>Opciones disponibles:</h3>
                <ul style="margin-left: 20px; margin-top: 10px;">
                    <li>Gestión de usuarios</li>
                    <li>Reportes</li>
                    <li>Configuración</li>
                    <li>Perfil de usuario</li>
                </ul>
            </div>
        </div>
    </div>
</body>
</html>