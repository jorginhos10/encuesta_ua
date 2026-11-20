-- jorge.sql
-- Carga las 19 filas (Aspecto + Necesidad) en la pregunta tipo MATRIZ de la encuesta 6
-- (la pregunta de prueba cuyo texto es 'jjj'). Ejecutar sobre la base de datos de la aplicacion.
-- Si hay mas de una matriz en la encuesta, cambie @pregunta_id por el id exacto.

SET NAMES utf8mb4;

SET @pregunta_id := (
    SELECT p.id
    FROM preguntas p
    JOIN encabezados_encuesta e ON e.id = p.encabezado_id
    WHERE e.encuesta_id = 6
      AND p.tipo_pregunta = 'matriz'
      AND p.pregunta = 'jjj'
    ORDER BY p.id DESC
    LIMIT 1
);

-- Verificacion previa (debe devolver 1 fila):
SELECT id, pregunta, tipo_pregunta FROM preguntas WHERE id = @pregunta_id;

UPDATE preguntas
SET opciones = '{"col_importancia": "¿Qué tan IMPORTANTE es para usted?", "col_satisfaccion": "¿Qué tanto se CUMPLE esta necesidad?", "filas": [{"aspecto": "Calidad académica", "necesidad": "Programas académicos de calidad."}, {"aspecto": "Docentes", "necesidad": "Docentes altamente cualificados."}, {"aspecto": "Infraestructura física y tecnológica", "necesidad": "Aulas, laboratorios y espacios físicos y tecnológicos adecuados para estudiar, investigar y para el esparcimiento."}, {"aspecto": "Recursos educativos y tecnológicos", "necesidad": "Biblioteca, bases de datos, software, equipos y conectividad adecuados."}, {"aspecto": "Costos de matrícula", "necesidad": "Matrícula a un costo accesible y con facilidades de pago."}, {"aspecto": "Educación continua", "necesidad": "Cursos y diplomados (educación continua) pertinentes que fortalezcan su perfil profesional."}, {"aspecto": "Representación estudiantil", "necesidad": "Representación de los estudiantes en los Consejos y demás órganos colegiados."}, {"aspecto": "Grupos estudiantiles", "necesidad": "Facilidades para usar las instalaciones y realizar las actividades de los grupos estudiantiles."}, {"aspecto": "Empleabilidad", "necesidad": "Apoyo para conseguir prácticas y empleo (bolsa de empleo, enlace con empleadores)."}, {"aspecto": "Educación inclusiva", "necesidad": "Una política de educación inclusiva."}, {"aspecto": "Campus seguro", "necesidad": "Gestión de los riesgos e impactos ambientales del campus y atención adecuada de emergencias."}, {"aspecto": "Cumplimiento de normas", "necesidad": "Que la Universidad cumpla las leyes y reglamentos que le aplican, incluido el reglamento estudiantil."}, {"aspecto": "Investigación", "necesidad": "Actividades de investigación (semilleros, proyectos) que fortalezcan su formación profesional."}, {"aspecto": "Permanencia y graduación", "necesidad": "Acciones que le ayuden a permanecer en la Universidad y a graduarse."}, {"aspecto": "Formación integral", "necesidad": "Formación en habilidades blandas, bilingüismo, lenguaje, cultura, competencias ciudadanas y autocuidado, entre otras."}, {"aspecto": "Movilidad", "necesidad": "Oportunidades de movilidad nacional e internacional (intercambios, pasantías)."}, {"aspecto": "Rendición de cuentas", "necesidad": "Información clara, concreta y oportuna sobre la gestión de la Universidad (rendición de cuentas)."}, {"aspecto": "Calidad del agua", "necesidad": "Agua de buena calidad en las instalaciones."}, {"aspecto": "Bienestar universitario", "necesidad": "Servicios de Bienestar: salud, deportes, apoyo económico, almuerzo y transporte."}]}'
WHERE id = @pregunta_id;

-- Verificacion posterior:
SELECT id, pregunta, JSON_LENGTH(JSON_EXTRACT(opciones, '$.filas')) AS total_filas
FROM preguntas WHERE id = @pregunta_id;
