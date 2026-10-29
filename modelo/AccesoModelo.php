<?php
if (!class_exists('AccesoModelo')) {

    class AccesoModelo {
        private $conn;
        private $table_name = "respuestas_acceso";

        public function __construct($db) {
            $this->conn = $db;
        }

        /**
         * Registra un acceso a una encuesta.
         * @return int|false ID del registro o false si falló
         */
        public function registrar($encuesta_id, $tipo_acceso, $nombre = null, $email = null, $ip = null) {
            try {
                $query = "INSERT INTO " . $this->table_name . " 
                          (encuesta_id, nombre, email, ip_address, tipo_acceso) 
                          VALUES (:encuesta_id, :nombre, :email, :ip, :tipo_acceso)";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':encuesta_id', $encuesta_id);
                $stmt->bindParam(':nombre', $nombre);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':ip', $ip);
                $stmt->bindParam(':tipo_acceso', $tipo_acceso);
                if ($stmt->execute()) {
                    return $this->conn->lastInsertId();
                }
                return false;
            } catch (PDOException $e) {
                error_log("Error al registrar acceso: " . $e->getMessage());
                return false;
            }
        }

        /**
         * Verifica si un email ya respondió esta encuesta
         */
        public function yaRespondio($encuesta_id, $email) {
            $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " 
                      WHERE encuesta_id = :encuesta_id AND email = :email";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':encuesta_id', $encuesta_id);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row && $row['total'] > 0;
        }

        /**
         * Listar accesos de una encuesta (para reportes)
         */
        public function getAccesosByEncuesta($encuesta_id) {
            $query = "SELECT * FROM " . $this->table_name . " 
                      WHERE encuesta_id = :encuesta_id 
                      ORDER BY created_at DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':encuesta_id', $encuesta_id);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        /**
         * Total de accesos únicos por email/IP
         */
        public function getTotalAccesos($encuesta_id) {
            $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " 
                      WHERE encuesta_id = :encuesta_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':encuesta_id', $encuesta_id);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ? (int)$row['total'] : 0;
        }
    }
}
?>
