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
        
        return $stmt->execute();
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