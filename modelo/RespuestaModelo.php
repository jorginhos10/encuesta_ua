<?php
// Verificar si la clase ya existe antes de declararla
if (!class_exists('RespuestaModelo')) {

    class RespuestaModelo {
        private $conn;

        public function __construct($db) {
            $this->conn = $db;
        }

        public function guardarRespuesta($encuesta_id, $pregunta_id, $respuesta, $usuario_respondio = null, $ip_address = null) {
            try {
                $query = "INSERT INTO respuestas_encuesta (encuesta_id, pregunta_id, respuesta, usuario_respondio, ip_address)
                          VALUES (:encuesta_id, :pregunta_id, :respuesta, :usuario_respondio, :ip_address)";

                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':encuesta_id', $encuesta_id);
                $stmt->bindParam(':pregunta_id', $pregunta_id);
                $stmt->bindParam(':respuesta', $respuesta);
                $stmt->bindParam(':usuario_respondio', $usuario_respondio);
                $stmt->bindParam(':ip_address', $ip_address);

                return $stmt->execute();
            } catch(PDOException $e) {
                error_log("Error al guardar respuesta: " . $e->getMessage());
                return false;
            }
        }

        public function getRespuestasByEncuesta($encuesta_id) {
            $query = "SELECT r.*, p.pregunta, p.tipo_pregunta, p.opciones
                      FROM respuestas_encuesta r
                      JOIN preguntas p ON r.pregunta_id = p.id
                      WHERE r.encuesta_id = :encuesta_id
                      ORDER BY r.created_at DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':encuesta_id', $encuesta_id);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function getEstadisticasByPregunta($encuesta_id, $pregunta_id) {
            $key = $this->sesionKey();
            $query = "SELECT respuesta, COUNT(DISTINCT $key) as total
                      FROM respuestas_encuesta
                      WHERE encuesta_id = :encuesta_id AND pregunta_id = :pregunta_id
                      GROUP BY respuesta
                      ORDER BY total DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':encuesta_id', $encuesta_id);
            $stmt->bindParam(':pregunta_id', $pregunta_id);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        // Clave única de envío: misma IP + mismo usuario + mismo segundo = una sola encuesta enviada
        private function sesionKey() {
            return "CONCAT(ip_address,'||',COALESCE(usuario_respondio,''),'||',DATE_FORMAT(created_at,'%Y-%m-%d %H:%i:%s'))";
        }

        public function getTotalRespuestas($encuesta_id) {
            $query = "SELECT COUNT(DISTINCT " . $this->sesionKey() . ") as total
                      FROM respuestas_encuesta
                      WHERE encuesta_id = :encuesta_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':encuesta_id', $encuesta_id);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ? (int)$result['total'] : 0;
        }

        public function getRespuestasIndividuales($encuesta_id) {
            $key = $this->sesionKey();
            $query = "SELECT ip_address, usuario_respondio, pregunta_id, respuesta, created_at,
                             $key AS sesion_key
                      FROM respuestas_encuesta
                      WHERE encuesta_id = :encuesta_id
                      ORDER BY created_at ASC, pregunta_id ASC";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':encuesta_id', $encuesta_id);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function getRespuestasRawByPregunta($encuesta_id, $pregunta_id) {
            $key = $this->sesionKey();
            $query = "SELECT r.respuesta
                      FROM respuestas_encuesta r
                      INNER JOIN (
                          SELECT MIN(id) AS min_id
                          FROM respuestas_encuesta
                          WHERE encuesta_id = :encuesta_id AND pregunta_id = :pregunta_id
                          GROUP BY $key
                      ) d ON r.id = d.min_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':encuesta_id', $encuesta_id);
            $stmt->bindParam(':pregunta_id', $pregunta_id);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        }

        public function getRespuestasPorFecha($encuesta_id) {
            $key = $this->sesionKey();
            $query = "SELECT DATE(created_at) as fecha, COUNT(DISTINCT $key) as total
                      FROM respuestas_encuesta
                      WHERE encuesta_id = :encuesta_id
                      GROUP BY DATE(created_at)
                      ORDER BY fecha ASC";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':encuesta_id', $encuesta_id);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function eliminarDuplicados($encuesta_id) {
            $query = "DELETE r FROM respuestas_encuesta r
                      INNER JOIN respuestas_encuesta r2
                        ON  r.encuesta_id  = r2.encuesta_id
                        AND r.pregunta_id  = r2.pregunta_id
                        AND r.ip_address   = r2.ip_address
                        AND COALESCE(r.usuario_respondio,'') = COALESCE(r2.usuario_respondio,'')
                        AND DATE_FORMAT(r.created_at,'%Y-%m-%d %H:%i:%s')
                          = DATE_FORMAT(r2.created_at,'%Y-%m-%d %H:%i:%s')
                        AND r.id > r2.id
                      WHERE r.encuesta_id = :encuesta_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':encuesta_id', $encuesta_id);
            return $stmt->execute();
        }

        public function getDuplicados($encuesta_id) {
            $key = "CONCAT(r.ip_address,'||',COALESCE(r.usuario_respondio,''),'||',DATE_FORMAT(r.created_at,'%Y-%m-%d %H:%i:%s'))";
            $query = "SELECT
                          $key AS sesion_key,
                          r.pregunta_id,
                          p.pregunta AS pregunta_texto,
                          COUNT(*) AS total,
                          GROUP_CONCAT(r.respuesta ORDER BY r.id SEPARATOR ' ||| ') AS respuestas,
                          MIN(r.usuario_respondio) AS usuario,
                          MIN(r.ip_address) AS ip,
                          MIN(DATE_FORMAT(r.created_at,'%Y-%m-%d %H:%i:%s')) AS fecha
                      FROM respuestas_encuesta r
                      LEFT JOIN preguntas p ON p.id = r.pregunta_id
                      WHERE r.encuesta_id = :encuesta_id
                      GROUP BY sesion_key, r.pregunta_id, p.pregunta
                      HAVING COUNT(*) > 1
                      ORDER BY sesion_key, r.pregunta_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':encuesta_id', $encuesta_id);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function eliminarPorParticipante($encuesta_id, $ip, $usuario, $dt) {
            $query = "DELETE FROM respuestas_encuesta
                      WHERE encuesta_id = :eid
                        AND ip_address = :ip
                        AND COALESCE(usuario_respondio,'') = :usuario
                        AND DATE_FORMAT(created_at,'%Y-%m-%d %H:%i:%s') = :dt";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':eid', $encuesta_id);
            $stmt->bindParam(':ip', $ip);
            $stmt->bindParam(':usuario', $usuario);
            $stmt->bindParam(':dt', $dt);
            return $stmt->execute();
        }
    }
}
?>
