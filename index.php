<?php
session_start();
define('BASE_URL', rtrim(str_replace('index.php', '', $_SERVER['SCRIPT_NAME']), '/') . '/');
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/helpers.php';
require_once __DIR__ . '/controlador/EncuestaControlador.php';
require_once __DIR__ . '/modelo/EncuestaModelo.php';
require_once __DIR__ . '/controlador/LoginControlador.php';
require_once __DIR__ . '/modelo/UsuarioModelo.php';
require_once __DIR__ . '/controlador/PreguntaControlador.php';
require_once __DIR__ . '/modelo/PreguntaModelo.php';
require_once __DIR__ . '/controlador/BancoPreguntasControlador.php';
require_once __DIR__ . '/modelo/BancoPreguntasModelo.php';
require_once __DIR__ . '/controlador/RespuestaControlador.php';
require_once __DIR__ . '/modelo/RespuestaModelo.php';

$database = new Database();
$db = $database->getConnection();

$action = isset($_GET['action']) ? $_GET['action'] : 'login';
$subaction = isset($_GET['subaction']) ? $_GET['subaction'] : '';

switch($action) {
    case 'login':
        $loginControlador = new LoginControlador();
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $loginControlador->login();
        } else {
            $loginControlador->mostrarLogin();
        }
        break;
        
    case 'logout':
        $loginControlador = new LoginControlador();
        $loginControlador->logout();
        break;
        
    case 'responder':
        $respuestaControlador = new RespuestaControlador($db);
        $encuesta_id = isset($_GET['id']) ? $_GET['id'] : 0;
        $respuestaControlador->mostrarFormulario($encuesta_id);
        break;
        
    case 'validar_acceso':
        require_once __DIR__ . '/modelo/AccesoModelo.php';
        $respuestaControlador = new RespuestaControlador($db);
        $respuestaControlador->validarAcceso();
        break;
        
    case 'guardar_respuesta':
        $respuestaControlador = new RespuestaControlador($db);
        $respuestaControlador->guardarRespuestas();
        break;
        
    case 'informe':
        if(!isset($_SESSION['user_id'])) {
            header("Location: " . BASE_URL . "login");
            exit();
        }
        $respuestaControlador = new RespuestaControlador($db);
        $encuesta_id = isset($_GET['id']) ? $_GET['id'] : 0;
        $respuestaControlador->verInforme($encuesta_id);
        break;

    case 'eliminar_participante':
        header('Content-Type: application/json');
        if(!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'No autorizado']); exit();
        }
        $respuestaControlador = new RespuestaControlador($db);
        $respuestaControlador->eliminarParticipante();
        break;

    case 'descargar_excel':
        if(!isset($_SESSION['user_id'])) {
            header("Location: " . BASE_URL . "login");
            exit();
        }
        $respuestaControlador = new RespuestaControlador($db);
        $encuesta_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $respuestaControlador->descargarExcel($encuesta_id);
        break;
        
    case 'encuestas':
        if(!isset($_SESSION['user_id'])) {
            header("Location: " . BASE_URL . "login");
            exit();
        }
        
        $encuestaControlador = new EncuestaControlador($db);
        
        if($subaction == 'ver') {
            $id = isset($_GET['id']) ? $_GET['id'] : 0;
            $preguntaControlador = new PreguntaControlador($db);
            $preguntaControlador->verEncuesta($id);
        } 
        elseif($subaction == 'crear') {
            $encuestaControlador->crear();
        } 
        elseif($subaction == 'obtener') {
            $encuestaControlador->obtener();
        } 
        elseif($subaction == 'actualizar') {
            $encuestaControlador->actualizar();
        } 
        elseif($subaction == 'eliminar') {
            $encuestaControlador->eliminar();
        }
        elseif($subaction == 'resetear') {
            header('Content-Type: application/json');
            $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
            $encuesta = $encuestaControlador->encuestaModelo->getById($id);
            if(!$encuesta || $encuesta['usuario_id'] != $_SESSION['user_id']) {
                echo json_encode(['success' => false, 'message' => 'No autorizado']);
                exit();
            }
            $stmt = $db->prepare("DELETE FROM respuestas_encuesta WHERE encuesta_id = :id");
            if($stmt->execute([':id' => $id])) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al resetear respuestas']);
            }
        }
        elseif($subaction == 'listar') {
            header('Content-Type: application/json');
            $encuestaControlador->encuestaModelo->updateEstados();
            $encuestas = $encuestaControlador->encuestaModelo->getAllByUser($_SESSION['user_id']);
            echo json_encode(['success' => true, 'data' => $encuestas]);
        } 
        else {
            $encuestaControlador->index();
        }
        break;
        
    case 'banco':
        if(!isset($_SESSION['user_id'])) {
            header("Location: " . BASE_URL . "login");
            exit();
        }
        
        $bancoControlador = new BancoPreguntasControlador($db);
        $preguntaModelo = new PreguntaModelo($db);
        
        switch($subaction) {
            case 'listar_encabezados':
                header('Content-Type: application/json');
                $encuesta_id = isset($_GET['encuesta_id']) ? $_GET['encuesta_id'] : 0;
                $encabezados = $preguntaModelo->getEncabezadosByEncuesta($encuesta_id);
                echo json_encode(['success' => true, 'data' => $encabezados]);
                break;
                
            case 'listar_preguntas':
                header('Content-Type: application/json');
                $encabezado_id = isset($_GET['encabezado_id']) ? $_GET['encabezado_id'] : 0;
                $preguntas = $preguntaModelo->getPreguntasByEncabezado($encabezado_id);
                echo json_encode(['success' => true, 'data' => $preguntas]);
                break;
                
            case 'listar_preguntas_banco':
                header('Content-Type: application/json');
                $categoria = isset($_GET['categoria']) ? $_GET['categoria'] : null;
                $preguntas = $bancoControlador->bancoModelo->getPreguntasByUser($_SESSION['user_id'], $categoria);
                echo json_encode(['success' => true, 'data' => $preguntas]);
                break;
                
            case 'listar_preguntas_condicion':
                header('Content-Type: application/json');
                $encabezado_id = isset($_GET['encabezado_id']) ? intval($_GET['encabezado_id']) : 0;
                $excluir = isset($_GET['excluir']) ? intval($_GET['excluir']) : 0;
                $query = "SELECT id, pregunta, tipo_pregunta, opciones FROM preguntas
                          WHERE encabezado_id = :encabezado_id
                          AND tipo_pregunta IN ('seleccion_unica', 'seleccion_multiple')";
                if($excluir) {
                    $query .= " AND id != :excluir";
                }
                $query .= " ORDER BY orden ASC";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':encabezado_id', $encabezado_id);
                if($excluir) {
                    $stmt->bindParam(':excluir', $excluir);
                }
                $stmt->execute();
                $preguntas = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode(['success' => true, 'data' => $preguntas]);
                break;
                
            case 'agregar_encabezado':
                header('Content-Type: application/json');
                $encuesta_id = isset($_POST['encuesta_id']) ? intval($_POST['encuesta_id']) : 0;
                $titulo = isset($_POST['titulo']) ? $_POST['titulo'] : '';
                $descripcion = isset($_POST['descripcion']) ? $_POST['descripcion'] : '';
                $stmtMax = $db->prepare("SELECT COALESCE(MAX(orden),0)+1 FROM encabezados_encuesta WHERE encuesta_id = :eid");
                $stmtMax->execute([':eid' => $encuesta_id]);
                $nextOrden = (int)$stmtMax->fetchColumn();
                if($preguntaModelo->createEncabezado($encuesta_id, $titulo, $descripcion, $nextOrden)) {
                    echo json_encode(['success' => true, 'message' => 'Sección agregada correctamente']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Error al crear la sección']);
                }
                break;
                
            case 'actualizar_encabezado':
                header('Content-Type: application/json');
                $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
                $titulo = isset($_POST['titulo']) ? trim($_POST['titulo']) : '';
                $descripcion = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : '';
                if(!$id || !$titulo) { echo json_encode(['success'=>false,'message'=>'Datos inválidos']); break; }
                if($preguntaModelo->updateEncabezado($id, $titulo, $descripcion)) {
                    echo json_encode(['success'=>true]);
                } else {
                    echo json_encode(['success'=>false,'message'=>'Error al actualizar']);
                }
                break;

            case 'eliminar_encabezado':
                header('Content-Type: application/json');
                $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
                if(!$id) { echo json_encode(['success'=>false]); break; }
                $db->prepare("DELETE FROM preguntas WHERE encabezado_id = :id")->execute([':id'=>$id]);
                if($preguntaModelo->deleteEncabezado($id)) {
                    echo json_encode(['success'=>true]);
                } else {
                    echo json_encode(['success'=>false,'message'=>'Error al eliminar']);
                }
                break;

            case 'guardar_orden_encabezados':
                header('Content-Type: application/json');
                $data = json_decode(file_get_contents('php://input'), true);
                $ordenes = $data['ordenes'] ?? [];
                $ok = true;
                foreach($ordenes as $item) {
                    $stmt = $db->prepare("UPDATE encabezados_encuesta SET orden = :orden WHERE id = :id");
                    if(!$stmt->execute([':orden'=>$item['orden'],':id'=>$item['id']])) $ok=false;
                }
                echo json_encode(['success'=>$ok]);
                break;

            case 'guardar_pregunta':
                $encabezado_id = isset($_POST['encabezado_id']) ? intval($_POST['encabezado_id']) : 0;
                $pregunta = isset($_POST['pregunta']) ? $_POST['pregunta'] : '';
                $tipo_pregunta = isset($_POST['tipo_pregunta']) ? trim($_POST['tipo_pregunta']) : 'texto_corto';
                $tiposPermitidos = ['texto_corto','texto_largo','seleccion_unica','seleccion_multiple','checkbox','tinder','satisfaccion','matriz','encabezado','texto_ordinario'];
                if(!in_array($tipo_pregunta, $tiposPermitidos)) $tipo_pregunta = 'texto_corto';
                $opciones = isset($_POST['opciones']) && $_POST['opciones'] !== 'null' ? $_POST['opciones'] : null;
                $requerido = (isset($_POST['requerido']) && $_POST['requerido'] == '1') ? 1 : 0;
                $guardar_banco = (isset($_POST['guardar_banco']) && $_POST['guardar_banco'] == '1') ? 1 : 0;
                $orden = isset($_POST['orden']) ? intval($_POST['orden']) : 0;
                $pregunta_padre_id = isset($_POST['pregunta_padre_id']) && $_POST['pregunta_padre_id'] !== '' ? intval($_POST['pregunta_padre_id']) : null;
                $condicion_operador = isset($_POST['condicion_operador']) ? $_POST['condicion_operador'] : 'igual';
                $condicion_valor = isset($_POST['condicion_valor']) ? $_POST['condicion_valor'] : null;
                
                if(empty($pregunta)) {
                    echo json_encode(['success' => false, 'message' => 'El contenido es requerido']);
                    break;
                }
                
                // Validar que si tiene pregunta_padre_id, los campos de condición estén completos
                if($pregunta_padre_id && empty($condicion_valor)) {
                    echo json_encode(['success' => false, 'message' => 'El valor de condición es requerido cuando la pregunta depende de otra']);
                    break;
                }
                
                // Obtener el último orden para esta sección
                $preguntasExistentes = $preguntaModelo->getPreguntasByEncabezado($encabezado_id);
                $orden = count($preguntasExistentes);
                
                $resultado = $preguntaModelo->createPregunta($encabezado_id, $pregunta, $tipo_pregunta, $opciones, $requerido, $guardar_banco, $orden, $pregunta_padre_id, $condicion_operador, $condicion_valor);
                
                if($resultado) {
                    if($guardar_banco == 1) {
                        $bancoControlador->bancoModelo->guardarPregunta($_SESSION['user_id'], $pregunta, $tipo_pregunta, $opciones);
                    }
                    echo json_encode(['success' => true, 'message' => 'Elemento guardado correctamente']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Error al guardar el elemento']);
                }
                break;
                
            case 'eliminar_pregunta':
                $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
                if($preguntaModelo->deletePregunta($id)) {
                    echo json_encode(['success' => true, 'message' => 'Elemento eliminado']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Error al eliminar']);
                }
                break;
                
            case 'guardar_orden_preguntas':
                $data = json_decode(file_get_contents('php://input'), true);
                $ordenes = $data['ordenes'];
                $success = true;
                
                foreach($ordenes as $item) {
                    $query = "UPDATE preguntas SET orden = :orden WHERE id = :id";
                    $stmt = $db->prepare($query);
                    $stmt->bindParam(':orden', $item['orden']);
                    $stmt->bindParam(':id', $item['id']);
                    if(!$stmt->execute()) {
                        $success = false;
                    }
                }
                
                echo json_encode(['success' => $success]);
                break;
                
            case 'obtener_pregunta':
                header('Content-Type: application/json');
                $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
                $query = "SELECT * FROM preguntas WHERE id = :id";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':id', $id);
                $stmt->execute();
                $pregunta = $stmt->fetch(PDO::FETCH_ASSOC);
                if($pregunta) {
                    echo json_encode(['success' => true, 'data' => $pregunta]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Pregunta no encontrada']);
                }
                break;
                
            case 'actualizar_pregunta':
                $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
                $pregunta = isset($_POST['pregunta']) ? $_POST['pregunta'] : '';
                $tipo_pregunta = isset($_POST['tipo_pregunta']) ? trim($_POST['tipo_pregunta']) : 'texto_corto';
                $tiposPermitidos = ['texto_corto','texto_largo','seleccion_unica','seleccion_multiple','checkbox','tinder','satisfaccion','matriz','encabezado','texto_ordinario'];
                if(!in_array($tipo_pregunta, $tiposPermitidos)) $tipo_pregunta = 'texto_corto';
                $opciones = isset($_POST['opciones']) && $_POST['opciones'] !== 'null' ? $_POST['opciones'] : null;
                $requerido = (isset($_POST['requerido']) && $_POST['requerido'] == '1') ? 1 : 0;
                $pregunta_padre_id = isset($_POST['pregunta_padre_id']) && $_POST['pregunta_padre_id'] !== '' ? intval($_POST['pregunta_padre_id']) : null;
                $condicion_operador = isset($_POST['condicion_operador']) ? $_POST['condicion_operador'] : 'igual';
                $condicion_valor = isset($_POST['condicion_valor']) ? $_POST['condicion_valor'] : null;
                
                $query = "UPDATE preguntas SET pregunta = :pregunta, tipo_pregunta = :tipo_pregunta, opciones = :opciones, 
                          requerido = :requerido, pregunta_padre_id = :pregunta_padre_id, 
                          condicion_operador = :condicion_operador, condicion_valor = :condicion_valor 
                          WHERE id = :id";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':pregunta', $pregunta);
                $stmt->bindParam(':tipo_pregunta', $tipo_pregunta);
                $stmt->bindParam(':opciones', $opciones);
                $stmt->bindParam(':requerido', $requerido);
                $stmt->bindParam(':pregunta_padre_id', $pregunta_padre_id);
                $stmt->bindParam(':condicion_operador', $condicion_operador);
                $stmt->bindParam(':condicion_valor', $condicion_valor);
                $stmt->bindParam(':id', $id);
                
                if($stmt->execute()) {
                    echo json_encode(['success' => true, 'message' => 'Pregunta actualizada correctamente']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Error al actualizar la pregunta']);
                }
                break;
                
            case 'importar':
                $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
                $preguntasBanco = $bancoControlador->bancoModelo->getPreguntasByUser($_SESSION['user_id']);
                $preguntaBanco = null;
                foreach($preguntasBanco as $p) {
                    if($p['id'] == $id) {
                        $preguntaBanco = $p;
                        break;
                    }
                }
                if($preguntaBanco) {
                    $bancoControlador->bancoModelo->incrementarUso($id);
                    echo json_encode([
                        'success' => true, 
                        'data' => [
                            'pregunta' => $preguntaBanco['pregunta'],
                            'tipo_pregunta' => $preguntaBanco['tipo_pregunta'],
                            'opciones' => $preguntaBanco['opciones']
                        ]
                    ]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Pregunta no encontrada']);
                }
                break;
                
            case 'eliminar_banco':
                $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
                if($bancoControlador->bancoModelo->deletePregunta($id)) {
                    echo json_encode(['success' => true, 'message' => 'Pregunta eliminada del banco']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Error al eliminar']);
                }
                break;
                
            default:
                $categoria = isset($_GET['categoria']) ? $_GET['categoria'] : null;
                $preguntas = $bancoControlador->bancoModelo->getPreguntasByUser($_SESSION['user_id'], $categoria);
                $categorias = $bancoControlador->bancoModelo->getCategorias($_SESSION['user_id']);
                include __DIR__ . '/vista/banco/index.php';
                break;
        }
        break;
        
    case 'imagenes':
        if(!isset($_SESSION['user_id'])){
            header("Location: " . BASE_URL . "login"); exit();
        }
        $uid = intval($_SESSION['user_id']);
        $uploadDir = __DIR__ . '/uploads/imagenes/' . $uid . '/';
        $uploadUrl = BASE_URL . 'uploads/imagenes/' . $uid . '/';
        $extPermitidas = ['jpg','jpeg','png','gif','webp','svg'];
        $maxBytes = 5 * 1024 * 1024; // 5 MB

        switch($subaction) {
            case 'subir':
                header('Content-Type: application/json');
                if(empty($_FILES['imagen'])){
                    echo json_encode(['success'=>false,'message'=>'No se recibió archivo']); exit();
                }
                $file = $_FILES['imagen'];
                if($file['error'] !== UPLOAD_ERR_OK){
                    echo json_encode(['success'=>false,'message'=>'Error en la subida']); exit();
                }
                if($file['size'] > $maxBytes){
                    echo json_encode(['success'=>false,'message'=>'El archivo supera 5 MB']); exit();
                }
                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                if(!in_array($ext, $extPermitidas)){
                    echo json_encode(['success'=>false,'message'=>'Tipo de archivo no permitido']); exit();
                }
                if(!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                $nombreBase = preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', pathinfo($file['name'], PATHINFO_FILENAME));
                $nombreBase = substr($nombreBase, 0, 60);
                $destino = $uploadDir . $nombreBase . '.' . $ext;
                $i = 1;
                while(file_exists($destino)){
                    $destino = $uploadDir . $nombreBase . '_' . $i . '.' . $ext;
                    $i++;
                }
                if(move_uploaded_file($file['tmp_name'], $destino)){
                    echo json_encode(['success'=>true,'url'=>$uploadUrl . basename($destino)]);
                } else {
                    echo json_encode(['success'=>false,'message'=>'Error al guardar el archivo']);
                }
                break;

            case 'listar':
                header('Content-Type: application/json');
                $imagenes = [];
                if(is_dir($uploadDir)){
                    foreach(scandir($uploadDir) as $archivo){
                        if($archivo === '.' || $archivo === '..') continue;
                        $ext = strtolower(pathinfo($archivo, PATHINFO_EXTENSION));
                        if(in_array($ext, $extPermitidas)){
                            $imagenes[] = [
                                'nombre'  => $archivo,
                                'archivo' => $archivo,
                                'url'     => $uploadUrl . $archivo,
                                'size'    => filesize($uploadDir . $archivo),
                                'fecha'   => filemtime($uploadDir . $archivo),
                            ];
                        }
                    }
                    usort($imagenes, fn($a,$b) => $b['fecha'] - $a['fecha']);
                }
                echo json_encode(['success'=>true,'data'=>$imagenes]);
                break;

            case 'eliminar':
                header('Content-Type: application/json');
                $archivo = basename($_POST['archivo'] ?? '');
                if(!$archivo){ echo json_encode(['success'=>false,'message'=>'Archivo no especificado']); exit(); }
                $ruta = $uploadDir . $archivo;
                $ext = strtolower(pathinfo($archivo, PATHINFO_EXTENSION));
                if(!in_array($ext, $extPermitidas) || !file_exists($ruta) || realpath($ruta) !== realpath($uploadDir) . DIRECTORY_SEPARATOR . $archivo){
                    echo json_encode(['success'=>false,'message'=>'Archivo no válido']); exit();
                }
                if(unlink($ruta)){
                    echo json_encode(['success'=>true]);
                } else {
                    echo json_encode(['success'=>false,'message'=>'No se pudo eliminar']);
                }
                break;

            default:
                include __DIR__ . '/vista/imagenes/index.php';
                break;
        }
        break;

    default:
        header("Location: " . BASE_URL . "login");
        break;
}
?>