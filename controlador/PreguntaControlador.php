<?php
if (!class_exists('PreguntaControlador')) {
class PreguntaControlador {
    public $preguntaModelo;
    public $encuestaModelo;
    public $bancoModelo;

    public function __construct($db) {
        if (!class_exists('PreguntaModelo')) {
            require_once __DIR__ . '/../modelo/PreguntaModelo.php';
        }
        if (!class_exists('EncuestaModelo')) {
            require_once __DIR__ . '/../modelo/EncuestaModelo.php';
        }
        if (!class_exists('BancoPreguntasModelo')) {
            require_once __DIR__ . '/../modelo/BancoPreguntasModelo.php';
        }

        $this->preguntaModelo = new PreguntaModelo($db);
        $this->encuestaModelo = new EncuestaModelo($db);
        $this->bancoModelo    = new BancoPreguntasModelo($db);
    }

    /**
     * Vista principal del constructor de encuesta (subaction=ver).
     * Carga la encuesta, sus encabezados y preguntas, y muestra vista/encuestas/ver.php
     */
    public function verEncuesta($encuesta_id) {
        if (!isset($_SESSION['user_id'])) {
            header("Location: " . BASE_URL . "login");
            exit();
        }

        $encuesta_id = intval($encuesta_id);
        if (!$encuesta_id) {
            echo "<h2>Encuesta no especificada</h2>";
            echo "<a href='index.php?action=encuestas'>Volver</a>";
            exit();
        }

        $encuesta = $this->encuestaModelo->getById($encuesta_id);
        if (!$encuesta) {
            echo "<h2>Encuesta no encontrada</h2>";
            echo "<a href='index.php?action=encuestas'>Volver</a>";
            exit();
        }

        // Validar propiedad
        if ($encuesta['usuario_id'] != $_SESSION['user_id']) {
            echo "<h2>No autorizado</h2>";
            echo "<a href='index.php?action=encuestas'>Volver</a>";
            exit();
        }

        // Cargar encabezados con sus preguntas
        $encabezadosRaw = $this->preguntaModelo->getEncabezadosByEncuesta($encuesta_id);
        $encabezados = [];
        foreach ($encabezadosRaw as $enc) {
            $preguntas = $this->preguntaModelo->getPreguntasByEncabezado($enc['id']);
            $preguntasNormalizadas = [];
            foreach ($preguntas as $preg) {
                if (!isset($preg['pregunta_padre_id']))  $preg['pregunta_padre_id']  = null;
                if (!isset($preg['condicion_operador'])) $preg['condicion_operador'] = 'igual';
                if (!isset($preg['condicion_valor']))    $preg['condicion_valor']    = null;
                $preguntasNormalizadas[] = $preg;
            }
            $enc['preguntas'] = $preguntasNormalizadas;
            $encabezados[] = $enc;
        }

        // Banco de preguntas del usuario
        $bancoPreguntas = $this->bancoModelo->getPreguntasByUser($_SESSION['user_id']);
        $categorias     = $this->bancoModelo->getCategorias($_SESSION['user_id']);

        include __DIR__ . '/../vista/encuestas/ver.php';
    }
}
}
?>
