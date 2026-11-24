<?php
class BancoPreguntasModelo {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    /** tipo_pregunta era ENUM; se amplía a VARCHAR(30) para admitir tipos nuevos (matriz, lista_desplegable...). */
    private static function asegurarColumnaTipo($conn, $tabla) {
        static $hecho = [];
        if (isset($hecho[$tabla])) return;
        $hecho[$tabla] = true;
        try {
            $col = $conn->query("SHOW COLUMNS FROM `$tabla` LIKE 'tipo_pregunta'")->fetch(PDO::FETCH_ASSOC);
            if ($col && stripos($col['Type'], 'enum') === 0) {
                $conn->exec("ALTER TABLE `$tabla` MODIFY `tipo_pregunta` VARCHAR(30) NOT NULL DEFAULT 'texto_corto'");
            }
        } catch (PDOException $e) {
            error_log("No se pudo ampliar tipo_pregunta: " . $e->getMessage());
        }
    }

    public function guardarPregunta($usuario_id, $pregunta, $tipo_pregunta, $opciones, $categoria = null) {
        self::asegurarColumnaTipo($this->conn, 'banco_preguntas');
        $query = "INSERT INTO banco_preguntas 
                  (usuario_id, pregunta, tipo_pregunta, opciones, categoría) 
                  VALUES (:usuario_id, :pregunta, :tipo_pregunta, :opciones, :categoria)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':usuario_id', $usuario_id);
        $stmt->bindParam(':pregunta', $pregunta);
        $stmt->bindParam(':tipo_pregunta', $tipo_pregunta);
        $stmt->bindParam(':opciones', $opciones);
        $stmt->bindParam(':categoria', $categoria);
        return $stmt->execute();
    }

    public function getPreguntasByUser($usuario_id, $categoria = null) {
        $query = "SELECT * FROM banco_preguntas WHERE usuario_id = :usuario_id";
        if($categoria) {
            $query .= " AND categoría = :categoria";
        }
        $query .= " ORDER BY veces_usado DESC, created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':usuario_id', $usuario_id);
        if($categoria) {
            $stmt->bindParam(':categoria', $categoria);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCategorias($usuario_id) {
        $query = "SELECT DISTINCT categoría FROM banco_preguntas WHERE usuario_id = :usuario_id AND categoría IS NOT NULL AND categoría != ''";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':usuario_id', $usuario_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function incrementarUso($id) {
        $query = "UPDATE banco_preguntas SET veces_usado = veces_usado + 1 WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function deletePregunta($id) {
        $query = "DELETE FROM banco_preguntas WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
?>