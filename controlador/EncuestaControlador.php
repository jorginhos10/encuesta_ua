<?php
if (!class_exists('EncuestaControlador')) {
class EncuestaControlador {
    public $encuestaModelo;

    public function __construct($db) {
        $this->encuestaModelo = new EncuestaModelo($db);
    }

    public function index() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: " . BASE_URL . "login");
            exit();
        }
        $this->encuestaModelo->updateEstados();
        $encuestas = $this->encuestaModelo->getAllByUser($_SESSION['user_id']);
        include __DIR__ . '/../vista/encuestas/index.php';
    }

    public function crear() {
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'No autorizado']);
            exit();
        }

        $nombre         = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
        $fecha_inicio   = isset($_POST['fecha_inicio']) ? $_POST['fecha_inicio'] : '';
        $fecha_cierre   = isset($_POST['fecha_cierre']) ? $_POST['fecha_cierre'] : '';
        $tipo_seguridad = isset($_POST['tipo_seguridad']) ? $_POST['tipo_seguridad'] : 'publica';
        $password       = isset($_POST['password']) ? $_POST['password'] : null;
        $usuario_id     = $_SESSION['user_id'];

        // Validar tipo_seguridad
        $tiposValidos = ['publica', 'password', 'gmail_cualquiera', 'gmail_uniatlantico', 'correos_especificos'];
        if (!in_array($tipo_seguridad, $tiposValidos)) {
            $tipo_seguridad = 'publica';
        }

        if (empty($nombre) || empty($fecha_inicio) || empty($fecha_cierre)) {
            echo json_encode(['success' => false, 'message' => 'Todos los campos son requeridos']);
            exit();
        }

        // Si es tipo password, la password es obligatoria
        if ($tipo_seguridad === 'password' && empty($password)) {
            echo json_encode(['success' => false, 'message' => 'Debes establecer una contraseña para encuestas privadas']);
            exit();
        }

        if (strtotime($fecha_inicio) >= strtotime($fecha_cierre)) {
            echo json_encode(['success' => false, 'message' => 'La fecha de inicio debe ser menor a la fecha de cierre']);
            exit();
        }

        $correos = [];
        if ($tipo_seguridad === 'correos_especificos') {
            list($correos, $invalidos) = EncuestaModelo::parsearCorreos($_POST['correos'] ?? '');
            if (!empty($invalidos)) {
                echo json_encode(['success' => false, 'message' => 'Correos no válidos: ' . implode(', ', array_slice($invalidos, 0, 5))]);
                exit();
            }
            if (empty($correos)) {
                echo json_encode(['success' => false, 'message' => 'Agrega al menos un correo autorizado']);
                exit();
            }
        }

        $nuevoId = $this->encuestaModelo->create($nombre, $fecha_inicio, $fecha_cierre, $usuario_id, $tipo_seguridad, $password);
        if ($nuevoId) {
            if ($tipo_seguridad === 'correos_especificos') $this->encuestaModelo->setCorreos($nuevoId, $correos);
            echo json_encode(['success' => true, 'message' => 'Encuesta creada exitosamente']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al crear la encuesta']);
        }
    }

    public function obtener() {
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'No autorizado']);
            exit();
        }

        $id = isset($_GET['id']) ? $_GET['id'] : 0;
        $encuesta = $this->encuestaModelo->getById($id);

        if ($encuesta && $encuesta['usuario_id'] == $_SESSION['user_id']) {
            if (($encuesta['tipo_seguridad'] ?? '') === 'correos_especificos') {
                $encuesta['correos'] = implode("\n", $this->encuestaModelo->getCorreos($encuesta['id']));
            }
            echo json_encode(['success' => true, 'data' => $encuesta]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Encuesta no encontrada']);
        }
    }

    public function actualizar() {
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'No autorizado']);
            exit();
        }

        $id              = isset($_POST['id']) ? $_POST['id'] : 0;
        $nombre          = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
        $fecha_inicio    = isset($_POST['fecha_inicio']) ? $_POST['fecha_inicio'] : '';
        $fecha_cierre    = isset($_POST['fecha_cierre']) ? $_POST['fecha_cierre'] : '';
        $tipo_seguridad  = isset($_POST['tipo_seguridad']) ? $_POST['tipo_seguridad'] : 'publica';
        $password        = isset($_POST['password']) ? $_POST['password'] : null;
        $cambiarPassword = isset($_POST['cambiar_password']) && $_POST['cambiar_password'] == '1';
        $banner_url      = isset($_POST['banner_url']) ? trim($_POST['banner_url']) : '';
        $modosBannerValidos = ['enunciado', 'ambos', 'banner'];
        $banner_modo     = isset($_POST['banner_modo']) && in_array($_POST['banner_modo'], $modosBannerValidos)
                           ? $_POST['banner_modo'] : 'enunciado';
        $background_url      = isset($_POST['background_url']) ? trim($_POST['background_url']) : '';
        $opacidad_formulario = max(10, min(100, (int)($_POST['opacidad_formulario'] ?? 100)));
        $opacidad_banner     = max(10, min(100, (int)($_POST['opacidad_banner']     ?? 100)));
        $opacidad_sidebar    = max(10, min(100, (int)($_POST['opacidad_sidebar']    ?? 100)));

        $tiposValidos = ['publica', 'password', 'gmail_cualquiera', 'gmail_uniatlantico', 'correos_especificos'];
        if (!in_array($tipo_seguridad, $tiposValidos)) {
            $tipo_seguridad = 'publica';
        }

        $encuesta = $this->encuestaModelo->getById($id);
        if (!$encuesta || $encuesta['usuario_id'] != $_SESSION['user_id']) {
            echo json_encode(['success' => false, 'message' => 'No autorizado']);
            exit();
        }

        if (strtotime($fecha_inicio) >= strtotime($fecha_cierre)) {
            echo json_encode(['success' => false, 'message' => 'La fecha de inicio debe ser menor a la fecha de cierre']);
            exit();
        }

        $correos = null;
        if ($tipo_seguridad === 'correos_especificos') {
            list($correos, $invalidos) = EncuestaModelo::parsearCorreos($_POST['correos'] ?? '');
            if (!empty($invalidos)) {
                echo json_encode(['success' => false, 'message' => 'Correos no válidos: ' . implode(', ', array_slice($invalidos, 0, 5))]);
                exit();
            }
            if (empty($correos)) {
                echo json_encode(['success' => false, 'message' => 'Agrega al menos un correo autorizado']);
                exit();
            }
        }

        // Si pidieron cambiar password pero no la dieron y el tipo sigue siendo password
        if ($tipo_seguridad === 'password' && $cambiarPassword && empty($password)) {
            echo json_encode(['success' => false, 'message' => 'Debes establecer una contraseña']);
            exit();
        }

        // Si el tipo es password pero antes no era y ahora no envió password
        if ($tipo_seguridad === 'password' && $encuesta['tipo_seguridad'] !== 'password' && empty($password)) {
            echo json_encode(['success' => false, 'message' => 'Debes establecer una contraseña para activar el modo privado']);
            exit();
        }

        // Si el tipo es password pero ANTES también era password y no quieren cambiarla, no se manda
        if ($tipo_seguridad === 'password' && $encuesta['tipo_seguridad'] === 'password' && !$cambiarPassword) {
            $cambiarPassword = false; // mantener la actual
        } else if ($tipo_seguridad === 'password' && !empty($password)) {
            $cambiarPassword = true;
        }

        if ($this->encuestaModelo->update($id, $nombre, $fecha_inicio, $fecha_cierre, $tipo_seguridad, $password, $cambiarPassword, $banner_url, $banner_modo, $background_url, $opacidad_formulario, $opacidad_banner, $opacidad_sidebar)) {
            if ($correos !== null) $this->encuestaModelo->setCorreos($id, $correos);
            echo json_encode(['success' => true, 'message' => 'Encuesta actualizada exitosamente']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al actualizar la encuesta']);
        }
    }

    public function eliminar() {
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'No autorizado']);
            exit();
        }

        $id = isset($_POST['id']) ? $_POST['id'] : 0;

        $encuesta = $this->encuestaModelo->getById($id);
        if (!$encuesta || $encuesta['usuario_id'] != $_SESSION['user_id']) {
            echo json_encode(['success' => false, 'message' => 'No autorizado']);
            exit();
        }

        if ($this->encuestaModelo->delete($id)) {
            echo json_encode(['success' => true, 'message' => 'Encuesta eliminada exitosamente']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al eliminar la encuesta']);
        }
    }
}
}
?>
