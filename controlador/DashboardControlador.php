<?php
class DashboardControlador {
    
    public function mostrarDashboard() {
        // Verificar si el usuario está logueado
        if(!isset($_SESSION['user_id'])) {
            header("Location: " . BASE_URL . "login");
            exit();
        }
        
        // Obtener datos del usuario
        $database = new Database();
        $db = $database->getConnection();
        $usuarioModelo = new UsuarioModelo($db);
        $usuario = $usuarioModelo->obtenerUsuario($_SESSION['user_id']);
        
        include 'vista/dashboard.php';
    }
}
?>