<?php
if (!class_exists('EncuestaModelo')) {
class EncuestaModelo {
    private $conn;
    private $table_name = "encuestas";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAllByUser($usuario_id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE usuario_id = :usuario_id ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':usuario_id', $usuario_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($nombre, $fecha_inicio, $fecha_cierre, $usuario_id, $tipo_seguridad = 'publica', $password = null) {
        // Si es password, hashearla
        $hashedPassword = null;
        if ($tipo_seguridad === 'password' && !empty($password)) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        }

        $query = "INSERT INTO " . $this->table_name . " 
                  (nombre, fecha_inicio, fecha_cierre, usuario_id, tipo_seguridad, password) 
                  VALUES (:nombre, :fecha_inicio, :fecha_cierre, :usuario_id, :tipo_seguridad, :password)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':fecha_inicio', $fecha_inicio);
        $stmt->bindParam(':fecha_cierre', $fecha_cierre);
        $stmt->bindParam(':usuario_id', $usuario_id);
        $stmt->bindParam(':tipo_seguridad', $tipo_seguridad);
        $stmt->bindParam(':password', $hashedPassword);
        
        return $stmt->execute() ? (int)$this->conn->lastInsertId() : false;
    }

    public function update($id, $nombre, $fecha_inicio, $fecha_cierre, $tipo_seguridad = 'publica', $password = null, $cambiarPassword = false, $banner_url = '', $banner_modo = 'enunciado', $background_url = '', $opacidad_formulario = 100, $opacidad_banner = 100, $opacidad_sidebar = 100) {
        $camposComunes = "nombre = :nombre, fecha_inicio = :fecha_inicio, fecha_cierre = :fecha_cierre,
                          tipo_seguridad = :tipo_seguridad,
                          banner_url = :banner_url, banner_modo = :banner_modo,
                          background_url = :background_url,
                          opacidad_formulario = :opacidad_formulario,
                          opacidad_banner = :opacidad_banner,
                          opacidad_sidebar = :opacidad_sidebar";

        if ($cambiarPassword) {
            $hashedPassword = null;
            if ($tipo_seguridad === 'password' && !empty($password)) {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            }
            $query = "UPDATE " . $this->table_name . " SET $camposComunes, password = :password WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':password', $hashedPassword);
        } else {
            if ($tipo_seguridad !== 'password') {
                $query = "UPDATE " . $this->table_name . " SET $camposComunes, password = NULL WHERE id = :id";
            } else {
                $query = "UPDATE " . $this->table_name . " SET $camposComunes WHERE id = :id";
            }
            $stmt = $this->conn->prepare($query);
        }

        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':fecha_inicio', $fecha_inicio);
        $stmt->bindParam(':fecha_cierre', $fecha_cierre);
        $stmt->bindParam(':tipo_seguridad', $tipo_seguridad);
        $stmt->bindParam(':banner_url', $banner_url);
        $stmt->bindParam(':banner_modo', $banner_modo);
        $stmt->bindParam(':background_url', $background_url);
        $stmt->bindParam(':opacidad_formulario', $opacidad_formulario, \PDO::PARAM_INT);
        $stmt->bindParam(':opacidad_banner',     $opacidad_banner,     \PDO::PARAM_INT);
        $stmt->bindParam(':opacidad_sidebar',    $opacidad_sidebar,    \PDO::PARAM_INT);
        $stmt->bindParam(':id', $id);

        return $stmt->execute();
    }

    // ── Correos específicos (lista de correos autorizados por encuesta) ──
    private function asegurarTablaCorreos() {
        $this->conn->exec("CREATE TABLE IF NOT EXISTS `encuesta_correos` (
            `id`          INT          NOT NULL AUTO_INCREMENT,
            `encuesta_id` INT          NOT NULL,
            `email`       VARCHAR(150) NOT NULL,
            `created_at`  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `uq_encuesta_email` (`encuesta_id`, `email`),
            CONSTRAINT `fk_encuesta_correos` FOREIGN KEY (`encuesta_id`) REFERENCES `encuestas` (`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    }

    /** Convierte un texto (uno por línea, o separados por coma/;/espacio) en [correos_validos, invalidos]. */
    public static function parsearCorreos($texto) {
        $validos = []; $invalidos = [];
        foreach (preg_split('/[\s,;]+/', (string)$texto, -1, PREG_SPLIT_NO_EMPTY) as $c) {
            $c = strtolower(trim($c));
            if (filter_var($c, FILTER_VALIDATE_EMAIL) && strlen($c) <= 150) $validos[$c] = true;
            else $invalidos[] = $c;
        }
        return [array_keys($validos), $invalidos];
    }

    public function getCorreos($encuesta_id) {
        $this->asegurarTablaCorreos();
        $stmt = $this->conn->prepare("SELECT email FROM encuesta_correos WHERE encuesta_id = :id ORDER BY email");
        $stmt->execute([':id' => $encuesta_id]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function setCorreos($encuesta_id, $correos) {
        $this->asegurarTablaCorreos();
        $this->conn->beginTransaction();
        try {
            $this->conn->prepare("DELETE FROM encuesta_correos WHERE encuesta_id = :id")->execute([':id' => $encuesta_id]);
            $ins = $this->conn->prepare("INSERT INTO encuesta_correos (encuesta_id, email) VALUES (:id, :email)");
            foreach ($correos as $c) $ins->execute([':id' => $encuesta_id, ':email' => $c]);
            $this->conn->commit();
            return true;
        } catch (PDOException $e) {
            $this->conn->rollBack();
            error_log("Error al guardar correos: " . $e->getMessage());
            return false;
        }
    }

    public function correoPermitido($encuesta_id, $email) {
        $this->asegurarTablaCorreos();
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM encuesta_correos WHERE encuesta_id = :id AND email = :email");
        $stmt->execute([':id' => $encuesta_id, ':email' => strtolower(trim($email))]);
        return (int)$stmt->fetchColumn() > 0;
    }

    public function verificarPassword($id, $password) {
        $query = "SELECT password FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row || empty($row['password'])) return false;
        return password_verify($password, $row['password']);
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function updateEstados() {
        $now = date('Y-m-d H:i:s');
        
        $query = "UPDATE " . $this->table_name . " 
                  SET estado = 'activa' 
                  WHERE fecha_inicio <= :now AND fecha_cierre > :now AND estado != 'activa'";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':now', $now);
        $stmt->execute();
        
        $query = "UPDATE " . $this->table_name . " 
                  SET estado = 'cerrada' 
                  WHERE fecha_cierre <= :now AND estado != 'cerrada'";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':now', $now);
        return $stmt->execute();
    }
}
}
?>