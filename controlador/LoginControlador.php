<?php
class LoginControlador {
    
    public function mostrarLogin() {
        if(isset($_SESSION['user_id'])) {
            header("Location: " . BASE_URL . "encuestas");
            exit();
        }
        include __DIR__ . '/../vista/login/index.php';
    }

    public function login() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            require_once __DIR__ . '/../modelo/UsuarioModelo.php';
            
            $email = $_POST['email'];
            $password = $_POST['password'];
            
            $database = new Database();
            $db = $database->getConnection();
            $usuarioModelo = new UsuarioModelo($db);
            
            $usuario = $usuarioModelo->validarUsuario($email, $password);
            
            if($usuario) {
                $_SESSION['user_id'] = $usuario['id'];
                $_SESSION['user_name'] = $usuario['nombre'];
                $_SESSION['user_email'] = $usuario['email'];
                header("Location: " . BASE_URL . "encuestas");
                exit();
            } else {
                $_SESSION['error'] = "Email o contraseña incorrectos";
                header("Location: " . BASE_URL . "login");
                exit();
            }
        }
    }

    public function logout() {
        session_destroy();
        header("Location: " . BASE_URL . "login");
        exit();
    }
}
?>