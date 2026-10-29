<?php
if (!class_exists('RespuestaControlador')) {
class RespuestaControlador {
    private $respuestaModelo;
    private $encuestaModelo;
    private $preguntaModelo;
    private $accesoModelo;

    public function __construct($db) {
        if (!class_exists('RespuestaModelo')) {
            require_once __DIR__ . '/../modelo/RespuestaModelo.php';
        }
        if (!class_exists('EncuestaModelo')) {
            require_once __DIR__ . '/../modelo/EncuestaModelo.php';
        }
        if (!class_exists('PreguntaModelo')) {
            require_once __DIR__ . '/../modelo/PreguntaModelo.php';
        }
        if (!class_exists('AccesoModelo')) {
            require_once __DIR__ . '/../modelo/AccesoModelo.php';
        }

        $this->respuestaModelo = new RespuestaModelo($db);
        $this->encuestaModelo  = new EncuestaModelo($db);
        $this->preguntaModelo  = new PreguntaModelo($db);
        $this->accesoModelo    = new AccesoModelo($db);
    }

    public function mostrarFormulario($encuesta_id) {
        $encuesta = $this->encuestaModelo->getById($encuesta_id);
        if (!$encuesta) {
            echo "<h2>Encuesta no encontrada</h2>";
            echo "<a href='index.php'>Volver al inicio</a>";
            exit();
        }

        if ($encuesta['estado'] != 'activa') {
            echo "<h2>Esta encuesta no está disponible</h2>";
            echo "<p>La encuesta se encuentra en estado: " . $encuesta['estado'] . "</p>";
            echo "<p>Fecha de inicio: " . $encuesta['fecha_inicio'] . "</p>";
            echo "<p>Fecha de cierre: " . $encuesta['fecha_cierre'] . "</p>";
            echo "<a href='index.php'>Volver al inicio</a>";
            exit();
        }

        // ── VERIFICAR ACCESO SEGÚN TIPO DE SEGURIDAD ──
        $tipoSeg = $encuesta['tipo_seguridad'] ?? 'publica';
        if ($tipoSeg !== 'publica') {
            // ¿Ya está validado para esta encuesta en la sesión?
            $sessKey = 'acceso_encuesta_' . $encuesta_id;
            if (!isset($_SESSION[$sessKey]) || $_SESSION[$sessKey] !== true) {
                // Mostrar pantalla de acceso
                include __DIR__ . '/../vista/encuestas/acceso.php';
                exit();
            }
        }

        // ── CARGAR PREGUNTAS (sin foreach por referencia para evitar bug) ──
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

        $submit_token = bin2hex(random_bytes(16));
        $_SESSION['submit_token_' . $encuesta_id] = $submit_token;

        include __DIR__ . '/../vista/encuestas/responder.php';
    }

    /**
     * Valida el acceso (password o gmail) y guarda en sesión.
     * Devuelve JSON.
     */
    public function validarAcceso() {
        header('Content-Type: application/json');

        $encuesta_id = isset($_POST['encuesta_id']) ? intval($_POST['encuesta_id']) : 0;
        if (!$encuesta_id) {
            echo json_encode(['success' => false, 'message' => 'Encuesta no especificada']);
            exit();
        }

        $encuesta = $this->encuestaModelo->getById($encuesta_id);
        if (!$encuesta) {
            echo json_encode(['success' => false, 'message' => 'Encuesta no encontrada']);
            exit();
        }

        if ($encuesta['estado'] !== 'activa') {
            echo json_encode(['success' => false, 'message' => 'Esta encuesta no está activa']);
            exit();
        }

        $tipoSeg = $encuesta['tipo_seguridad'] ?? 'publica';
        $ip = $_SERVER['REMOTE_ADDR'] ?? null;

        if ($tipoSeg === 'publica') {
            // No requiere validación
            $_SESSION['acceso_encuesta_' . $encuesta_id] = true;
            echo json_encode(['success' => true]);
            exit();
        }

        if ($tipoSeg === 'password') {
            $password = isset($_POST['password']) ? $_POST['password'] : '';
            if (empty($password)) {
                echo json_encode(['success' => false, 'message' => 'Debes ingresar la contraseña']);
                exit();
            }
            if (!$this->encuestaModelo->verificarPassword($encuesta_id, $password)) {
                echo json_encode(['success' => false, 'message' => 'Contraseña incorrecta']);
                exit();
            }
            // OK
            $_SESSION['acceso_encuesta_' . $encuesta_id] = true;
            $this->accesoModelo->registrar($encuesta_id, 'password', null, null, $ip);
            echo json_encode(['success' => true]);
            exit();
        }

        if ($tipoSeg === 'gmail_cualquiera' || $tipoSeg === 'gmail_uniatlantico') {
            $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
            $email  = isset($_POST['email']) ? trim(strtolower($_POST['email'])) : '';

            if (empty($nombre) || strlen($nombre) < 2) {
                echo json_encode(['success' => false, 'message' => 'Debes ingresar tu nombre completo']);
                exit();
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                echo json_encode(['success' => false, 'message' => 'El correo no es válido']);
                exit();
            }

            // Validar dominio
            if ($tipoSeg === 'gmail_uniatlantico') {
                if (!preg_match('/@mail\.uniatlantico\.edu\.co$/i', $email)) {
                    echo json_encode(['success' => false, 'message' => 'Debes usar un correo @mail.uniatlantico.edu.co']);
                    exit();
                }
            }

            // ¿Ya respondió?
            if ($this->accesoModelo->yaRespondio($encuesta_id, $email)) {
                echo json_encode(['success' => false, 'message' => 'Este correo ya respondió esta encuesta']);
                exit();
            }

            // OK
            $_SESSION['acceso_encuesta_' . $encuesta_id] = true;
            $_SESSION['acceso_nombre_' . $encuesta_id]   = $nombre;
            $_SESSION['acceso_email_' . $encuesta_id]    = $email;
            $this->accesoModelo->registrar($encuesta_id, $tipoSeg, $nombre, $email, $ip);
            echo json_encode(['success' => true]);
            exit();
        }

        echo json_encode(['success' => false, 'message' => 'Tipo de seguridad no reconocido']);
        exit();
    }

    public function guardarRespuestas() {
        $encuesta_id = isset($_POST['encuesta_id']) ? intval($_POST['encuesta_id']) : 0;
        $ip_address  = $_SERVER['REMOTE_ADDR'];

        // Verificar token anti-doble-envío
        $tokenKey  = 'submit_token_' . $encuesta_id;
        $tokenPost = $_POST['submit_token'] ?? '';
        if (empty($tokenPost) || !isset($_SESSION[$tokenKey]) || $tokenPost !== $_SESSION[$tokenKey]) {
            echo "<!DOCTYPE html><html><head><title>Envío duplicado</title></head><body style='font-family:sans-serif;display:flex;align-items:center;justify-content:center;height:100vh;margin:0;background:#f2f2f7;'>";
            echo "<div style='text-align:center;background:white;padding:40px;border-radius:16px;box-shadow:0 4px 20px rgba(0,0,0,.1);max-width:420px;'>";
            echo "<p style='font-size:40px;margin-bottom:12px;'>⚠️</p>";
            echo "<h2 style='margin-bottom:10px;color:#1c1c1e;'>Envío ya procesado</h2>";
            echo "<p style='color:#636366;margin-bottom:20px;'>Tu respuesta ya fue guardada anteriormente. No es posible enviarla de nuevo.</p>";
            echo "<a href='index.php' style='background:#007aff;color:white;padding:12px 28px;border-radius:10px;text-decoration:none;font-weight:600;'>Volver al inicio</a>";
            echo "</div></body></html>";
            exit();
        }
        unset($_SESSION[$tokenKey]);

        if ($encuesta_id == 0) {
            echo "<h2>Error: No se especificó la encuesta</h2>";
            echo "<a href='index.php'>Volver al inicio</a>";
            exit();
        }

        // Verificar que la encuesta sigue activa (seguridad)
        $encuesta = $this->encuestaModelo->getById($encuesta_id);
        if (!$encuesta || $encuesta['estado'] != 'activa') {
            echo "<h2>Esta encuesta ya no está activa</h2>";
            echo "<a href='index.php'>Volver al inicio</a>";
            exit();
        }

        // ── VERIFICAR ACCESO SI LA ENCUESTA TIENE SEGURIDAD ──
        $tipoSeg = $encuesta['tipo_seguridad'] ?? 'publica';
        if ($tipoSeg !== 'publica') {
            $sessKey = 'acceso_encuesta_' . $encuesta_id;
            if (!isset($_SESSION[$sessKey]) || $_SESSION[$sessKey] !== true) {
                echo "<h2>Acceso no autorizado</h2>";
                echo "<p>Debes validar el acceso antes de responder.</p>";
                echo "<a href='index.php?action=responder&id={$encuesta_id}'>Validar acceso</a>";
                exit();
            }
        }

        // Determinar el "usuario_respondio" según tipo
        $usuarioRespondio = 'Anónimo';
        if (isset($_SESSION['acceso_nombre_' . $encuesta_id]) && isset($_SESSION['acceso_email_' . $encuesta_id])) {
            $usuarioRespondio = $_SESSION['acceso_nombre_' . $encuesta_id] . ' <' . $_SESSION['acceso_email_' . $encuesta_id] . '>';
        }

        $respuestas_guardadas = 0;
        $errores = 0;

        foreach ($_POST as $key => $value) {
            if (strpos($key, 'pregunta_') === 0) {
                $pregunta_id = intval(str_replace('pregunta_', '', $key));

                // Obtener el tipo de pregunta para manejar Tinder correctamente
                $pregunta = $this->preguntaModelo->getPreguntaById($pregunta_id);
                if (!$pregunta) continue;

                // Si es tipo Tinder, el valor ya es un string JSON (no procesar como array)
                if ($pregunta['tipo_pregunta'] === 'tinder') {
                    $respuesta = trim($value);
                    if (empty($respuesta)) continue;

                    // Deduplicar por título antes de guardar
                    $tinderArr = json_decode($respuesta, true);
                    if (is_array($tinderArr)) {
                        $seen = []; $clean = [];
                        foreach ($tinderArr as $card) {
                            $k = mb_strtolower(trim($card['titulo'] ?? ''), 'UTF-8');
                            if ($k === '' || isset($seen[$k])) continue;
                            $seen[$k] = true; $clean[] = $card;
                        }
                        $respuesta = json_encode($clean, JSON_UNESCAPED_UNICODE);
                    }

                    if ($this->respuestaModelo->guardarRespuesta($encuesta_id, $pregunta_id, $respuesta, $usuarioRespondio, $ip_address)) {
                        $respuestas_guardadas++;
                    } else {
                        $errores++;
                    }
                    continue;
                }

                // Matriz (Importancia / Satisfacción por aspecto): guardar como JSON
                if ($pregunta['tipo_pregunta'] === 'matriz') {
                    if (!is_array($value)) continue;
                    $cfg = json_decode($pregunta['opciones'] ?? '{}', true);
                    $filasDef = is_array($cfg['filas'] ?? null) ? array_values($cfg['filas']) : [];
                    $items = [];
                    foreach ($filasDef as $i => $f) {
                        $imp = isset($value[$i]['imp']) ? (int)$value[$i]['imp'] : 0;
                        $sat = isset($value[$i]['sat']) ? (int)$value[$i]['sat'] : 0;
                        if ($imp < 1 || $imp > 5 || $sat < 1 || $sat > 5) continue;
                        $items[] = ['fila' => $i, 'aspecto' => $f['aspecto'] ?? '', 'importancia' => $imp, 'satisfaccion' => $sat];
                    }
                    if (empty($items)) continue;
                    $respuesta = json_encode($items, JSON_UNESCAPED_UNICODE);
                    if ($this->respuestaModelo->guardarRespuesta($encuesta_id, $pregunta_id, $respuesta, $usuarioRespondio, $ip_address)) {
                        $respuestas_guardadas++;
                    } else {
                        $errores++;
                    }
                    continue;
                }

                // Para los demás tipos, procesar normalmente
                if (is_array($value)) {
                    $value = array_filter($value, function($v) { return !empty(trim($v)); });
                    if (empty($value)) continue;
                    $respuesta = implode(', ', $value);
                } else {
                    $respuesta = trim($value);
                    if (empty($respuesta)) continue;
                }

                // Evaluar condición si la pregunta es condicional
                if ($pregunta['pregunta_padre_id'] && $pregunta['pregunta_padre_id'] > 0) {
                    $valorPadre = '';
                    if (isset($_POST['pregunta_' . $pregunta['pregunta_padre_id']])) {
                        $valorPadreRaw = $_POST['pregunta_' . $pregunta['pregunta_padre_id']];
                        $valorPadre = is_array($valorPadreRaw)
                            ? implode(', ', $valorPadreRaw)
                            : trim($valorPadreRaw);
                    }

                    $mostrar  = false;
                    $operador = $pregunta['condicion_operador'];
                    $valorCond = $pregunta['condicion_valor'];

                    switch ($operador) {
                        case 'igual':
                            $mostrar = $valorPadre == $valorCond;
                            break;
                        case 'diferente':
                            $mostrar = $valorPadre != $valorCond && !empty($valorPadre);
                            break;
                        case 'contiene':
                            $vals = array_filter(array_map('trim', explode(',', $valorCond)), 'strlen');
                            $mostrar = false;
                            foreach ($vals as $v) {
                                if (stripos($valorPadre, $v) !== false) { $mostrar = true; break; }
                            }
                            break;
                        case 'no_contiene':
                            $vals = array_filter(array_map('trim', explode(',', $valorCond)), 'strlen');
                            $mostrar = !empty($valorPadre);
                            foreach ($vals as $v) {
                                if (stripos($valorPadre, $v) !== false) { $mostrar = false; break; }
                            }
                            break;
                        default:
                            $mostrar = $valorPadre == $valorCond;
                    }

                    if (!$mostrar) continue;
                }

                if ($this->respuestaModelo->guardarRespuesta($encuesta_id, $pregunta_id, $respuesta, $usuarioRespondio, $ip_address)) {
                    $respuestas_guardadas++;
                } else {
                    $errores++;
                }
            }
        }

        // ── LIMPIAR ACCESO DE LA SESIÓN para que tenga que re-validar si vuelve ──
        unset($_SESSION['acceso_encuesta_' . $encuesta_id]);
        unset($_SESSION['acceso_nombre_' . $encuesta_id]);
        unset($_SESSION['acceso_email_' . $encuesta_id]);

        echo "<!DOCTYPE html>";
        echo "<html><head><title>Respuesta Guardada</title>";
        echo "<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css'>";
        echo "<style>";
        echo "body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }";
        echo ".container { text-align: center; background: white; padding: 40px; border-radius: 10px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); max-width: 500px; }";
        echo "h1 { color: #28a745; margin-bottom: 20px; }";
        echo "p { color: #333; margin-bottom: 20px; line-height: 1.6; }";
        echo ".btn { background: #667eea; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block; margin-top: 10px; }";
        echo ".btn:hover { background: #5a67d8; }";
        echo ".stats { background: #f8f9fa; padding: 15px; border-radius: 8px; margin: 20px 0; }";
        echo "</style></head><body>";
        echo "<div class='container'>";
        echo "<h1><i class='fas fa-check-circle'></i> ¡Respuestas guardadas!</h1>";
        echo "<p>Gracias por participar en esta encuesta.</p>";
        echo "<div class='stats'>";
        echo "<p><i class='fas fa-save'></i> Se guardaron <strong>" . $respuestas_guardadas . "</strong> respuestas</p>";
        if ($errores > 0) {
            echo "<p><i class='fas fa-exclamation-triangle'></i> Hubo <strong>" . $errores . "</strong> errores</p>";
        }
        echo "</div>";
        echo "<a href='index.php' class='btn'><i class='fas fa-home'></i> Volver al inicio</a>";
        echo "</div></body></html>";
    }

    // Promedios y distribución (1-5) de Importancia y Satisfacción por fila de una pregunta matriz
    private function estadisticasMatriz($encuesta_id, $preg) {
        $cfg = json_decode($preg['opciones'] ?? '{}', true);
        $filasDef = is_array($cfg['filas'] ?? null) ? array_values($cfg['filas']) : [];
        $filas = [];
        foreach ($filasDef as $i => $f) {
            $filas[$i] = [
                'aspecto'   => $f['aspecto'] ?? '',
                'necesidad' => $f['necesidad'] ?? '',
                'dist_imp'  => [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0],
                'dist_sat'  => [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0],
                'n' => 0, 'prom_imp' => 0, 'prom_sat' => 0,
            ];
        }
        $participantes = 0;
        foreach ($this->respuestaModelo->getRespuestasRawByPregunta($encuesta_id, $preg['id']) as $raw) {
            $items = json_decode($raw, true);
            if (!is_array($items)) continue;
            $participantes++;
            foreach ($items as $it) {
                $i = $it['fila'] ?? null;
                if (!isset($filas[$i])) continue;
                $imp = (int)($it['importancia'] ?? 0);
                $sat = (int)($it['satisfaccion'] ?? 0);
                if ($imp < 1 || $imp > 5 || $sat < 1 || $sat > 5) continue;
                $filas[$i]['dist_imp'][$imp]++;
                $filas[$i]['dist_sat'][$sat]++;
                $filas[$i]['n']++;
            }
        }
        foreach ($filas as &$fila) {
            if ($fila['n'] > 0) {
                $si = 0; $ss = 0;
                foreach ($fila['dist_imp'] as $v => $c) $si += $v * $c;
                foreach ($fila['dist_sat'] as $v => $c) $ss += $v * $c;
                $fila['prom_imp'] = round($si / $fila['n'], 2);
                $fila['prom_sat'] = round($ss / $fila['n'], 2);
            }
        }
        unset($fila);
        return [
            'pregunta' => $preg['pregunta'],
            'tipo'     => 'matriz',
            'datos'    => array_values($filas),
            'total'    => $participantes,
        ];
    }

    public function eliminarParticipante() {
        header('Content-Type: application/json');
        $encuesta_id = isset($_POST['encuesta_id']) ? intval($_POST['encuesta_id']) : 0;
        $ip          = isset($_POST['ip'])       ? $_POST['ip']       : '';
        $usuario     = isset($_POST['usuario'])  ? $_POST['usuario']  : '';
        $dt          = isset($_POST['dt'])       ? $_POST['dt']       : '';
        if (!$encuesta_id || empty($dt)) {
            echo json_encode(['success' => false, 'message' => 'Datos inválidos']);
            exit();
        }
        $encuesta = $this->encuestaModelo->getById($encuesta_id);
        if (!$encuesta || $encuesta['usuario_id'] != $_SESSION['user_id']) {
            echo json_encode(['success' => false, 'message' => 'No autorizado']);
            exit();
        }
        if ($this->respuestaModelo->eliminarPorParticipante($encuesta_id, $ip, $usuario, $dt)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al eliminar']);
        }
        exit();
    }

    public function descargarExcel($encuesta_id) {
        $encuesta = $this->encuestaModelo->getById($encuesta_id);
        if (!$encuesta || $encuesta['usuario_id'] != $_SESSION['user_id']) {
            echo "No autorizado"; exit();
        }
        $this->respuestaModelo->eliminarDuplicados($encuesta_id);

        $totalRespuestas = $this->respuestaModelo->getTotalRespuestas($encuesta_id);
        $encabezadosRaw  = $this->preguntaModelo->getEncabezadosByEncuesta($encuesta_id);
        $encabezados = [];
        foreach ($encabezadosRaw as $enc) {
            $enc['preguntas'] = $this->preguntaModelo->getPreguntasByEncabezado($enc['id']);
            $encabezados[] = $enc;
        }

        $estadisticas = [];
        foreach ($encabezados as $enc) {
            foreach ($enc['preguntas'] as $preg) {
                if ($preg['tipo_pregunta'] === 'tinder') {
                    $rawResp = $this->respuestaModelo->getRespuestasRawByPregunta($encuesta_id, $preg['id']);
                    $cardCounts = [];
                    foreach ($rawResp as $raw) {
                        $parsed = json_decode($raw, true);
                        if (!is_array($parsed)) continue;
                        foreach ($parsed as $item) {
                            if (!isset($item['titulo'], $item['decision'])) continue;
                            $tRaw = trim($item['titulo']);
                            $tKey = mb_strtolower(iconv('UTF-8','ASCII//TRANSLIT//IGNORE',$tRaw),'UTF-8');
                            if (!isset($cardCounts[$tKey])) $cardCounts[$tKey] = ['titulo' => $tRaw, 'yes' => 0, 'no' => 0];
                            if ($item['decision'] === 'yes') $cardCounts[$tKey]['yes']++;
                            else $cardCounts[$tKey]['no']++;
                        }
                    }
                    $estadisticas[$preg['id']] = ['tipo' => 'tinder', 'datos' => array_values($cardCounts)];
                } elseif ($preg['tipo_pregunta'] === 'matriz') {
                    $estadisticas[$preg['id']] = $this->estadisticasMatriz($encuesta_id, $preg);
                } elseif ($preg['tipo_pregunta'] === 'satisfaccion') {
                    $rawData = $this->respuestaModelo->getEstadisticasByPregunta($encuesta_id, $preg['id']);
                    $counts = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
                    foreach ($rawData as $d) {
                        $v = (int)$d['respuesta'];
                        if ($v >= 1 && $v <= 5) $counts[$v] = (int)$d['total'];
                    }
                    $totalSat = array_sum($counts);
                    $suma = 0;
                    foreach ($counts as $v => $c) $suma += $v * $c;
                    $promedio = $totalSat > 0 ? round($suma / $totalSat, 2) : 0;
                    $datosSat = [];
                    for ($v = 1; $v <= 5; $v++) $datosSat[] = ['valor' => $v, 'total' => $counts[$v]];
                    $estadisticas[$preg['id']] = [
                        'tipo'     => 'satisfaccion',
                        'datos'    => $datosSat,
                        'promedio' => $promedio,
                        'total'    => $totalSat
                    ];
                } else {
                    $estadisticas[$preg['id']] = [
                        'tipo'  => $preg['tipo_pregunta'],
                        'datos' => $this->respuestaModelo->getEstadisticasByPregunta($encuesta_id, $preg['id'])
                    ];
                }
            }
        }

        $nombreArchivo = preg_replace('/[^a-zA-Z0-9_-]/', '_', $encuesta['nombre']);
        header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
        header('Content-Disposition: attachment; filename="informe_' . $nombreArchivo . '.xls"');
        header('Cache-Control: max-age=0');
        echo "\xEF\xBB\xBF"; // UTF-8 BOM
        include __DIR__ . '/../vista/encuestas/informe_excel.php';
        exit();
    }

    public function verInforme($encuesta_id) {
        if (!isset($_SESSION['user_id'])) {
            header("Location: " . BASE_URL . "login");
            exit();
        }

        $encuesta = $this->encuestaModelo->getById($encuesta_id);
        if (!$encuesta || $encuesta['usuario_id'] != $_SESSION['user_id']) {
            echo "<h2>No autorizado</h2>";
            echo "<a href='index.php'>Volver</a>";
            exit();
        }

        $respuestasPorFecha = $this->respuestaModelo->getRespuestasPorFecha($encuesta_id);
        $encabezadosRaw     = $this->preguntaModelo->getEncabezadosByEncuesta($encuesta_id);

        // ── FIX: NO usar foreach con referencias (causaba duplicaciones) ──
        $encabezados = [];
        foreach ($encabezadosRaw as $enc) {
            $enc['preguntas'] = $this->preguntaModelo->getPreguntasByEncabezado($enc['id']);
            $encabezados[] = $enc;
        }

        $estadisticas = [];
        foreach ($encabezados as $enc) {
            foreach ($enc['preguntas'] as $preg) {
                if ($preg['tipo_pregunta'] === 'tinder') {
                    $rawResp = $this->respuestaModelo->getRespuestasRawByPregunta($encuesta_id, $preg['id']);
                    $cardCounts = [];
                    foreach ($rawResp as $raw) {
                        $parsed = json_decode($raw, true);
                        if (!is_array($parsed)) continue;
                        foreach ($parsed as $item) {
                            if (!isset($item['titulo'], $item['decision'])) continue;
                            $tRaw = trim($item['titulo']);
                            $tKey = mb_strtolower(iconv('UTF-8','ASCII//TRANSLIT//IGNORE',$tRaw),'UTF-8');
                            if (!isset($cardCounts[$tKey])) $cardCounts[$tKey] = ['titulo' => $tRaw, 'yes' => 0, 'no' => 0];
                            if ($item['decision'] === 'yes') $cardCounts[$tKey]['yes']++;
                            else $cardCounts[$tKey]['no']++;
                        }
                    }
                    $estadisticas[$preg['id']] = [
                        'pregunta' => $preg['pregunta'],
                        'tipo'     => 'tinder',
                        'datos'    => array_values($cardCounts)
                    ];
                } elseif ($preg['tipo_pregunta'] === 'matriz') {
                    $estadisticas[$preg['id']] = $this->estadisticasMatriz($encuesta_id, $preg);
                } elseif ($preg['tipo_pregunta'] === 'satisfaccion') {
                    $rawData = $this->respuestaModelo->getEstadisticasByPregunta($encuesta_id, $preg['id']);
                    $counts = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
                    foreach ($rawData as $d) {
                        $v = (int)$d['respuesta'];
                        if ($v >= 1 && $v <= 5) $counts[$v] = (int)$d['total'];
                    }
                    $totalSat = array_sum($counts);
                    $suma = 0;
                    foreach ($counts as $v => $c) $suma += $v * $c;
                    $promedio = $totalSat > 0 ? round($suma / $totalSat, 2) : 0;
                    $datosSat = [];
                    for ($v = 1; $v <= 5; $v++) $datosSat[] = ['valor' => $v, 'total' => $counts[$v]];
                    $estadisticas[$preg['id']] = [
                        'pregunta' => $preg['pregunta'],
                        'tipo'     => 'satisfaccion',
                        'datos'    => $datosSat,
                        'promedio' => $promedio,
                        'total'    => $totalSat
                    ];
                } else {
                    $datos = $this->respuestaModelo->getEstadisticasByPregunta($encuesta_id, $preg['id']);
                    // Rellenar opciones con 0 votos que no recibieron respuestas
                    if (in_array($preg['tipo_pregunta'], ['seleccion_unica', 'seleccion_multiple']) && !empty($preg['opciones'])) {
                        $opcionesDef = json_decode($preg['opciones'], true);
                        if (is_array($opcionesDef)) {
                            $counts = [];
                            foreach ($datos as $d) { $counts[$d['respuesta']] = (int)$d['total']; }
                            $datos = [];
                            foreach ($opcionesDef as $op) {
                                $datos[] = ['respuesta' => $op, 'total' => $counts[$op] ?? 0];
                            }
                            usort($datos, function($a, $b) { return $b['total'] - $a['total']; });
                        }
                    }
                    $estadisticas[$preg['id']] = [
                        'pregunta' => $preg['pregunta'],
                        'tipo'     => $preg['tipo_pregunta'],
                        'datos'    => $datos
                    ];
                }
            }
        }

        // Respuestas individuales por participante
        $rawIndividuales = $this->respuestaModelo->getRespuestasIndividuales($encuesta_id);
        $participantes = [];
        foreach ($rawIndividuales as $row) {
            $key = $row['sesion_key'];
            if (!isset($participantes[$key])) {
                $participantes[$key] = [
                    'nombre'      => ($row['usuario_respondio'] && $row['usuario_respondio'] !== 'Anónimo')
                                     ? $row['usuario_respondio'] : null,
                    'ip'          => $row['ip_address'],
                    'fecha'       => date('d/m/Y H:i', strtotime($row['created_at'])),
                    'del_ip'      => $row['ip_address'],
                    'del_usuario' => $row['usuario_respondio'] ?? '',
                    'del_dt'      => date('Y-m-d H:i:s', strtotime($row['created_at'])),
                    'resps'       => []
                ];
            }
            if (!isset($participantes[$key]['resps'][$row['pregunta_id']])) {
                $participantes[$key]['resps'][$row['pregunta_id']] = $row['respuesta'];
            }
        }
        $respuestasIndividuales = array_values($participantes);
        $totalRespuestas = count($respuestasIndividuales);

        // Lista plana de preguntas respondibles para la vista individual
        $todasPreguntas = [];
        foreach ($encabezados as $enc) {
            foreach ($enc['preguntas'] as $preg) {
                if (!in_array($preg['tipo_pregunta'], ['encabezado', 'texto_ordinario'])) {
                    $todasPreguntas[] = $preg;
                }
            }
        }

        $this->respuestaModelo->eliminarDuplicados($encuesta_id);
        $duplicados = $this->respuestaModelo->getDuplicados($encuesta_id);

        include __DIR__ . '/../vista/encuestas/informe.php';
    }
}
}
?>