<?php
class BancoPreguntasControlador {
    public $bancoModelo;
    
    public function __construct($db) {
        $this->bancoModelo = new BancoPreguntasModelo($db);
    }
    
    public function index() {
        if(!isset($_SESSION['user_id'])) {
            header("Location: " . BASE_URL . "login");
            exit();
        }
        
        $categoria = isset($_GET['categoria']) ? $_GET['categoria'] : null;
        $preguntas = $this->bancoModelo->getPreguntasByUser($_SESSION['user_id'], $categoria);
        $categorias = $this->bancoModelo->getCategorias($_SESSION['user_id']);
        
        include __DIR__ . '/../vista/banco/index.php';
    }
}
?>