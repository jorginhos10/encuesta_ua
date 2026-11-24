-- jorge_facultades.sql
-- Encuesta 6, seccion "INFORMACION GENERAL":
--  * 'Facultad'           -> pasa a SELECCION UNICA con las 10 facultades.
--  * 'Programa academico' -> pasa a SELECCION UNICA condicional: se muestra la lista de programas de la
--                            facultad elegida. La pregunta existente queda para ARQUITECTURA y se crean
--                            9 preguntas 'Programa academico' mas, una por cada otra facultad.
-- Ejecutar UNA sola vez sobre la base de datos de la aplicacion.

SET NAMES utf8mb4;

SET @fac_id  := (SELECT p.id FROM preguntas p JOIN encabezados_encuesta e ON e.id = p.encabezado_id
                 WHERE e.encuesta_id = 6 AND TRIM(p.pregunta) = 'Facultad' ORDER BY p.id LIMIT 1);
SET @prog_id := (SELECT p.id FROM preguntas p JOIN encabezados_encuesta e ON e.id = p.encabezado_id
                 WHERE e.encuesta_id = 6 AND TRIM(p.pregunta) IN ('Programa académico','Programa academico') ORDER BY p.id LIMIT 1);

-- Verificacion previa (ambos deben tener id, no NULL):
SELECT @fac_id AS facultad_id, @prog_id AS programa_id;

SET @enc_id     := (SELECT encabezado_id FROM preguntas WHERE id = @prog_id);
SET @prog_orden := (SELECT orden FROM preguntas WHERE id = @prog_id);
SET @prog_req   := (SELECT requerido FROM preguntas WHERE id = @prog_id);
SET @prog_txt   := (SELECT pregunta FROM preguntas WHERE id = @prog_id);

-- Hace espacio en el orden para las 9 preguntas nuevas
UPDATE preguntas SET orden = orden + 20
WHERE encabezado_id = @enc_id AND orden > @prog_orden AND id <> @prog_id AND @prog_id IS NOT NULL;

-- 1) Facultad -> seleccion unica
UPDATE preguntas SET tipo_pregunta = 'seleccion_unica',
  opciones = '["ARQUITECTURA", "BELLAS ARTES", "CIENCIAS BASICAS", "CIENCIAS DE LA EDUCACION", "CIENCIAS DE LA SALUD", "CIENCIAS ECONOMICAS", "CIENCIAS HUMANAS", "CIENCIAS JURIDICAS", "INGENIERIA", "QUIMICA Y FARMACIA"]'
WHERE id = @fac_id;

-- 2) Programa academico existente -> seleccion unica condicionada a ARQUITECTURA
UPDATE preguntas SET tipo_pregunta = 'seleccion_unica',
  opciones = '["ARQUITECTURA", "MAESTRIA DESARROLLO URBANO SOSTENIBLE", "MAESTRIA PATRIMONIO ARQUITECTONICO, URBANO Y PAISAJISTICO", "TECNICA PROFESIONAL EN CONSTRUCCION DE PROYECTOS ARQUITECTONICOS", "TECNICA PROFESIONAL EN DOCUMENTACION DE PROYECTOS URBANISTICOS", "TECNICA PROFESIONAL EN EXPRESION GRAFICA ARQUITECTONICA", "TECNOLOGIA EN GESTION DE LA CONSTRUCCIÓN DE PROYECTOS ARQUITECTONICOS", "TECNOLOGIA EN GESTION DE PROYECTOS URBANISTICOS", "TECNOLOGIA EN MODELADO DIGITAL ARQUITECTONICO"]',
  pregunta_padre_id = @fac_id, condicion_operador = 'igual', condicion_valor = 'ARQUITECTURA'
WHERE id = @prog_id;

-- Programas de BELLAS ARTES
INSERT INTO preguntas (encabezado_id, pregunta, tipo_pregunta, opciones, requerido, guardar_banco, orden, pregunta_padre_id, condicion_operador, condicion_valor)
SELECT @enc_id, @prog_txt, 'seleccion_unica', '["ARTE DRAMATICO", "ARTES PLASTICAS", "DANZA", "LICENCIATURA EN MUSICA", "MUSICA"]', @prog_req, 0, @prog_orden + 1, @fac_id, 'igual', 'BELLAS ARTES'
FROM DUAL WHERE @fac_id IS NOT NULL AND @prog_id IS NOT NULL;

-- Programas de CIENCIAS BASICAS
INSERT INTO preguntas (encabezado_id, pregunta, tipo_pregunta, opciones, requerido, guardar_banco, orden, pregunta_padre_id, condicion_operador, condicion_valor)
SELECT @enc_id, @prog_txt, 'seleccion_unica', '["BIOLOGIA", "DOCTORADO EN CIENCIAS FÍSICA", "DOCTORADO EN CIENCIAS QUIMICAS", "DOCTORADO EN MEDICINA TROPICAL", "ESPECIALIZACIÓN EN DIDACTICA DE LAS MATEMÁTICAS", "ESPECIALIZACIÓN EN ESTADÍSTICA APLICADA", "FISICA", "MAESTRIA EN BIOLOGÍA", "MAESTRIA EN CIENCIAS AMBIENTALES", "MAESTRIA EN CIENCIAS FÍSICAS", "MAESTRIA EN CIENCIAS MATEMÁTICAS", "MAESTRÍA EN CIENCIAS QUÍMICAS", "MAESTRÍA EN DIDÁCTICA DE LAS MATEMÁTICAS", "MATEMATICAS", "QUIMICA", "TECNICA PROFESIONAL EN PISCICULTURA CONTINENTAL", "TECNOLOGIA EN ACUICULTURA CONTINENTAL"]', @prog_req, 0, @prog_orden + 2, @fac_id, 'igual', 'CIENCIAS BASICAS'
FROM DUAL WHERE @fac_id IS NOT NULL AND @prog_id IS NOT NULL;

-- Programas de CIENCIAS DE LA EDUCACION
INSERT INTO preguntas (encabezado_id, pregunta, tipo_pregunta, opciones, requerido, guardar_banco, orden, pregunta_padre_id, condicion_operador, condicion_valor)
SELECT @enc_id, @prog_txt, 'seleccion_unica', '["DOCTORADO EN CIENCIAS DE LA EDUCACIÓN", "LICENCIATURA EN CIENCIAS NATURALES", "LICENCIATURA EN CIENCIAS SOCIALES", "LICENCIATURA EN EDUCACION ARTISTICA", "LICENCIATURA EN EDUCACIÓN ESPECIAL", "LICENCIATURA EN EDUCACION FISICA, RECREACION Y DEPORTES", "LICENCIATURA EN EDUCACION INFANTIL", "LICENCIATURA EN HUMANIDADES Y LENGUA CASTELLANA", "LICENCIATURA EN LENGUAS EXTRANJERAS CON ENFASIS EN INGLES Y FRANCES", "LICENCIATURA EN MATEMATICA", "MAESTRIA EN ACTIVIDAD FISICA ADAPTADA", "MAESTRIA EN EDUCACIÓN", "MAESTRIA EN EDUCACION AMBIENTAL", "MAESTRIA EN ENTRENAMIENTO DEPORTIVO", "MAESTRÍA EN NEUROPEDAGOGÍA"]', @prog_req, 0, @prog_orden + 3, @fac_id, 'igual', 'CIENCIAS DE LA EDUCACION'
FROM DUAL WHERE @fac_id IS NOT NULL AND @prog_id IS NOT NULL;

-- Programas de CIENCIAS DE LA SALUD
INSERT INTO preguntas (encabezado_id, pregunta, tipo_pregunta, opciones, requerido, guardar_banco, orden, pregunta_padre_id, condicion_operador, condicion_valor)
SELECT @enc_id, @prog_txt, 'seleccion_unica', '["ESPECIALIZACIÓN EN CONTRATACIÓN E INTERVENTORÍA DE SERVICIOS ALIMENTARIOS A COLECTIVIDADES", "MAESTRIA EN SEGURIDAD ALIMENTARIA Y NUTRICIONAL", "MEDICINA", "NUTRICION Y DIETETICA", "TECNICA PROFESIONAL EN PROCESAMIENTO Y CALIDAD NUTRICIONAL DE ALIMENTOS", "TECNOLOGIA EN SEGURIDAD ALIMENTARIA Y NUTRICIONAL"]', @prog_req, 0, @prog_orden + 4, @fac_id, 'igual', 'CIENCIAS DE LA SALUD'
FROM DUAL WHERE @fac_id IS NOT NULL AND @prog_id IS NOT NULL;

-- Programas de CIENCIAS ECONOMICAS
INSERT INTO preguntas (encabezado_id, pregunta, tipo_pregunta, opciones, requerido, guardar_banco, orden, pregunta_padre_id, condicion_operador, condicion_valor)
SELECT @enc_id, @prog_txt, 'seleccion_unica', '["ADMINISTRACION DE EMPRESAS", "ADMINISTRACIÓN DE EMPRESAS TURISTICAS", "CONTADURIA PUBLICA", "ECONOMIA", "ESPECIALIZACION EN FINANZAS TERRITORIALES", "ESPECIALIZACION EN GESTION FISCAL Y CONTABILIDAD PUBLICA", "ESPECIALIZACION EN GESTION INTEGRAL DEL TURISMO", "MAESTRIA EN ADMINISTRACION DE EMPRESAS", "MAESTRIA EN CONTABILIDAD", "MAESTRIA EN ECONOMIA", "TECNICO PROFESIONAL EN OPERACION TURISTICA", "TECNOLOGO EN GESTION TURISTICA"]', @prog_req, 0, @prog_orden + 5, @fac_id, 'igual', 'CIENCIAS ECONOMICAS'
FROM DUAL WHERE @fac_id IS NOT NULL AND @prog_id IS NOT NULL;

-- Programas de CIENCIAS HUMANAS
INSERT INTO preguntas (encabezado_id, pregunta, tipo_pregunta, opciones, requerido, guardar_banco, orden, pregunta_padre_id, condicion_operador, condicion_valor)
SELECT @enc_id, @prog_txt, 'seleccion_unica', '["FILOSOFIA", "HISTORIA", "INTERPRETACION DE LENGUA DE SEÑAS COLOMBIANA - ESPAÑOL", "MAESTRÍA EN ESTUDIOS DE GÉNERO Y VIOLENCIA INTRAFAMILIAR", "MAESTRIA EN FILOSOFIA", "MAESTRIA EN GESTION LOGISTICA", "MAESTRÍA EN HISTORIA", "MAESTRÍA EN LINGÜISTICA", "MAESTRIA EN LITERATURA HISPANOAMERICANA Y DEL CARIBE", "SOCIOLOGIA"]', @prog_req, 0, @prog_orden + 6, @fac_id, 'igual', 'CIENCIAS HUMANAS'
FROM DUAL WHERE @fac_id IS NOT NULL AND @prog_id IS NOT NULL;

-- Programas de CIENCIAS JURIDICAS
INSERT INTO preguntas (encabezado_id, pregunta, tipo_pregunta, opciones, requerido, guardar_banco, orden, pregunta_padre_id, condicion_operador, condicion_valor)
SELECT @enc_id, @prog_txt, 'seleccion_unica', '["CIENCIA POLITICA", "DERECHO", "ESPECIALIZACION EN DERECHO ADMINISTRATIVO Y DE LA FUNCION PUBLICA", "ESPECIALIZACIÓN EN DERECHO LABORAL Y SEGURIDAD SOCIAL", "ESPECIALIZACION EN DERECHO PROCESAL, PROBATORIO Y TECNOLOGIAS DIGITALES", "ESPECIALIZACION EN TEORIA DEL DELITO Y CASACION PENAL", "MAESTRIA EN ESTUDIOS E INVESTIGACIONES POLITICAS Y JURIDICAS", "MAESTRIA EN INVESTIGACIONES JURIDICAS Y SOCIOJURIDICAS", "MAESTRIA EN PROTECCION SOCIAL"]', @prog_req, 0, @prog_orden + 7, @fac_id, 'igual', 'CIENCIAS JURIDICAS'
FROM DUAL WHERE @fac_id IS NOT NULL AND @prog_id IS NOT NULL;

-- Programas de INGENIERIA
INSERT INTO preguntas (encabezado_id, pregunta, tipo_pregunta, opciones, requerido, guardar_banco, orden, pregunta_padre_id, condicion_operador, condicion_valor)
SELECT @enc_id, @prog_txt, 'seleccion_unica', '["DOCTORADO EN INGENIERIA", "ESPECIALIZACION EN GESTION DE LA CALIDAD", "ESPECIALIZACION EN GESTION EFICIENTE DE ENERGIA", "ESPECIALIZACION EN LOGISTICA INTEGRAL", "INGENIERIA AGROINDUSTRIAL", "INGENIERIA DE SOFTWARE", "INGENIERIA EN AGRONEGOCIOS", "INGENIERIA INDUSTRIAL", "INGENIERIA MECANICA", "INGENIERIA QUIMICA", "MAESTRÍA EN GESTIÓN DE LA CALIDAD", "MAESTRÍA EN GESTIÓN ENERGÉTICA", "MAESTRIA EN INGENIERIA QUIMICA", "TECNICA PROFESIONAL EN APROVECHAMIENTO DE RESIDUOS AGROPECUARIOS", "TECNOLOGÍA DE TRANSFORMACIÓN DE PRODUCTOS AGROPECUARIOS"]', @prog_req, 0, @prog_orden + 8, @fac_id, 'igual', 'INGENIERIA'
FROM DUAL WHERE @fac_id IS NOT NULL AND @prog_id IS NOT NULL;

-- Programas de QUIMICA Y FARMACIA
INSERT INTO preguntas (encabezado_id, pregunta, tipo_pregunta, opciones, requerido, guardar_banco, orden, pregunta_padre_id, condicion_operador, condicion_valor)
SELECT @enc_id, @prog_txt, 'seleccion_unica', '["ESPECIALIZACIÓN EN PLANTAS MEDICINALES", "FARMACIA", "MAESTRÍA EN GESTIÓN FARMACÉUTICA"]', @prog_req, 0, @prog_orden + 9, @fac_id, 'igual', 'QUIMICA Y FARMACIA'
FROM DUAL WHERE @fac_id IS NOT NULL AND @prog_id IS NOT NULL;

-- Verificacion posterior
SELECT id, pregunta, tipo_pregunta, orden, condicion_valor, JSON_LENGTH(opciones) AS opciones
FROM preguntas WHERE id = @fac_id OR pregunta_padre_id = @fac_id ORDER BY orden;
