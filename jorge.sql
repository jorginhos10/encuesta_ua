-- jorge.sql
-- Carga las 10 filas (Aspecto + Expectativa) en la pregunta tipo MATRIZ de la encuesta 6
-- (la pregunta de prueba cuyo texto es 'kkk'). Ejecutar sobre la base de datos de la aplicacion.
-- Si hay mas de una matriz en la encuesta, cambie @pregunta_id por el id exacto.

SET NAMES utf8mb4;

SET @pregunta_id := (
    SELECT p.id
    FROM preguntas p
    JOIN encabezados_encuesta e ON e.id = p.encabezado_id
    WHERE e.encuesta_id = 6
      AND p.tipo_pregunta = 'matriz'
      AND p.pregunta = 'kkk'
    ORDER BY p.id DESC
    LIMIT 1
);

-- Verificacion previa (debe devolver 1 fila):
SELECT id, pregunta, tipo_pregunta FROM preguntas WHERE id = @pregunta_id;

UPDATE preguntas
SET opciones = '{"col_n": "N°", "col_aspecto": "Aspecto", "col_necesidad": "EXPECTATIVA", "col_necesidad_sub": "Lo que usted ESPERA", "col_importancia": "¿Qué tan IMPORTANTE es para usted?", "col_satisfaccion": "¿En qué medida se CUMPLE esta expectativa?", "filas": [{"aspecto": "Calidad académica", "necesidad": "Que los programas y la Universidad obtengan y mantengan la acreditación de alta calidad."}, {"aspecto": "Recursos educativos y tecnológicos", "necesidad": "Mayor uso de las tecnologías de la información (TIC) en las clases y actividades académicas."}, {"aspecto": "Representación estudiantil", "necesidad": "Participación real de los estudiantes en las decisiones de la Universidad."}, {"aspecto": "Grupos estudiantiles", "necesidad": "Programas que apoyen los objetivos de los grupos estudiantiles y reconocimiento de sus logros ante la comunidad universitaria."}, {"aspecto": "Empleabilidad", "necesidad": "Convenios con empresas y entidades para prácticas y empleo."}, {"aspecto": "Educación inclusiva", "necesidad": "Un ambiente inclusivo que promueva la igualdad de oportunidades."}, {"aspecto": "Campus seguro", "necesidad": "Prevención de accidentes e incidentes dentro del campus."}, {"aspecto": "Permanencia y graduación", "necesidad": "Trámites de grado sencillos, con variedad de opciones de grado, y apoyo socioeconómico y psicosocial."}, {"aspecto": "Formación integral", "necesidad": "Eventos académicos (congresos, ferias, seminarios) que favorezcan su crecimiento académico."}, {"aspecto": "Rendición de cuentas", "necesidad": "Buena gestión y buen gobierno de la Universidad."}]}'
WHERE id = @pregunta_id;

-- Verificacion posterior (debe mostrar 10 filas):
SELECT id, pregunta, JSON_LENGTH(JSON_EXTRACT(opciones, '$.filas')) AS total_filas
FROM preguntas WHERE id = @pregunta_id;
