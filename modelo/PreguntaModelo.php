<?php
if (!class_exists('PreguntaModelo')) {

    class PreguntaModelo {
        private $conn;

        public function __construct($db) {
            $this->conn = $db;
        }

        public function getEncabezadosByEncuesta($encuesta_id) {
            $query = "SELECT * FROM encabezados_encuesta WHERE encuesta_id = :encuesta_id ORDER BY orden ASC";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':encuesta_id', $encuesta_id);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function createEncabezado($encuesta_id, $titulo, $descripcion, $orden) {
            $query = "INSERT INTO encabezados_encuesta (encuesta_id, titulo, descripcion, orden) 
                      VALUES (:encuesta_id, :titulo, :descripcion, :orden)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':encuesta_id', $encuesta_id);
            $stmt->bindParam(':titulo', $titulo);
            $stmt->bindParam(':descripcion', $descripcion);
            $stmt->bindParam(':orden', $orden);
            return $stmt->execute();
        }

        public function updateEncabezado($id, $titulo, $descripcion) {
            $query = "UPDATE encabezados_encuesta SET titulo = :titulo, descripcion = :descripcion WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':titulo', $titulo);
            $stmt->bindParam(':descripcion', $descripcion);
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        }

        public function deleteEncabezado($id) {
            $query = "DELETE FROM encabezados_encuesta WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        }

        public function getPreguntasByEncabezado($encabezado_id, $incluir_condicionales = true) {
            $query = "SELECT * FROM preguntas WHERE encabezado_id = :encabezado_id ORDER BY orden ASC";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':encabezado_id', $encabezado_id);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function createPregunta($encabezado_id, $pregunta, $tipo_pregunta, $opciones, $requerido, $guardar_banco, $orden, $pregunta_padre_id = null, $condicion_operador = null, $condicion_valor = null) {
            try {
                $query = "INSERT INTO preguntas 
                          (encabezado_id, pregunta, tipo_pregunta, opciones, requerido, guardar_banco, orden, pregunta_padre_id, condicion_operador, condicion_valor) 
                          VALUES (:encabezado_id, :pregunta, :tipo_pregunta, :opciones, :requerido, :guardar_banco, :orden, :pregunta_padre_id, :condicion_operador, :condicion_valor)";
                
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':encabezado_id', $encabezado_id);
                $stmt->bindParam(':pregunta', $pregunta);
                $stmt->bindParam(':tipo_pregunta', $tipo_pregunta);
                $stmt->bindParam(':opciones', $opciones);
                $stmt->bindParam(':requerido', $requerido);
                $stmt->bindParam(':guardar_banco', $guardar_banco);
                $stmt->bindParam(':orden', $orden);
                $stmt->bindParam(':pregunta_padre_id', $pregunta_padre_id);
                $stmt->bindParam(':condicion_operador', $condicion_operador);
                $stmt->bindParam(':condicion_valor', $condicion_valor);
                
                return $stmt->execute();
            } catch(PDOException $e) {
                error_log("Error al crear pregunta: " . $e->getMessage());
                return false;
            }
        }

        public function updatePregunta($id, $pregunta, $tipo_pregunta, $opciones, $requerido, $pregunta_padre_id = null, $condicion_operador = null, $condicion_valor = null) {
            try {
                $query = "UPDATE preguntas 
                          SET pregunta = :pregunta, tipo_pregunta = :tipo_pregunta, opciones = :opciones, 
                              requerido = :requerido, pregunta_padre_id = :pregunta_padre_id, 
                              condicion_operador = :condicion_operador, condicion_valor = :condicion_valor 
                          WHERE id = :id";
                
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':id', $id);
                $stmt->bindParam(':pregunta', $pregunta);
                $stmt->bindParam(':tipo_pregunta', $tipo_pregunta);
                $stmt->bindParam(':opciones', $opciones);
                $stmt->bindParam(':requerido', $requerido);
                $stmt->bindParam(':pregunta_padre_id', $pregunta_padre_id);
                $stmt->bindParam(':condicion_operador', $condicion_operador);
                $stmt->bindParam(':condicion_valor', $condicion_valor);
                
                return $stmt->execute();
            } catch(PDOException $e) {
                error_log("Error al actualizar pregunta: " . $e->getMessage());
                return false;
            }
        }

        public function deletePregunta($id) {
            $query = "DELETE FROM preguntas WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        }

        public function getPreguntaById($id) {
            $query = "SELECT * FROM preguntas WHERE id = :id LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        public function getAllPreguntasByEncuesta($encuesta_id) {
            $query = "SELECT p.* FROM preguntas p 
                      JOIN encabezados_encuesta e ON p.encabezado_id = e.id 
                      WHERE e.encuesta_id = :encuesta_id 
                      ORDER BY e.orden ASC, p.orden ASC";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':encuesta_id', $encuesta_id);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        
        public function getPreguntasDisponiblesParaCondicion($encabezado_id, $pregunta_actual_id = null) {
            $query = "SELECT * FROM preguntas WHERE encabezado_id = :encabezado_id AND tipo_pregunta IN ('seleccion_unica', 'seleccion_multiple')";
            if($pregunta_actual_id) {
                $query .= " AND id != :pregunta_actual_id";
            }
            $query .= " ORDER BY orden ASC";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':encabezado_id', $encabezado_id);
            if($pregunta_actual_id) {
                $stmt->bindParam(':pregunta_actual_id', $pregunta_actual_id);
            }
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }
}
?>