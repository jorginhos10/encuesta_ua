-- jorge_facultades.sql
-- Encuesta 6, seccion "INFORMACION GENERAL" (Nivel de formacion -> Facultad -> Programa academico):
--  * 'Nivel de formacion' -> SELECCION UNICA: Pregrado / Posgrado.
--  * 'Facultad'           -> LISTA DESPLEGABLE que depende del nivel. La existente queda para Pregrado
--                            (10 facultades) y se crea otra para Posgrado (9 facultades).
--  * 'Programa academico' -> LISTA DESPLEGABLE que depende de la facultad (y por tanto del nivel): una pregunta
--                            por cada nivel+facultad (19 en total; la existente se reutiliza y se crean 18).
--                            101 programas en total. Posgrado = Especializacion, Maestria y Doctorado.
-- Ejecutar UNA sola vez sobre la base de datos de la aplicacion. Si 'Facultad' ya es lista desplegable
-- (script anterior ya ejecutado) no hace cambios; en ese caso restaure las preguntas de esa seccion y vuelva a correrlo.

SET NAMES utf8mb4;

-- Permite los tipos nuevos (lista_desplegable, matriz, ...). Es seguro repetirlo.
ALTER TABLE `preguntas` MODIFY `tipo_pregunta` VARCHAR(30) NOT NULL DEFAULT 'texto_corto';
ALTER TABLE `banco_preguntas` MODIFY `tipo_pregunta` VARCHAR(30) NOT NULL DEFAULT 'texto_corto';

SET @niv_id  := (SELECT p.id FROM preguntas p JOIN encabezados_encuesta e ON e.id = p.encabezado_id
                 WHERE e.encuesta_id = 6 AND TRIM(p.pregunta) LIKE 'Nivel de formaci%' ORDER BY p.id LIMIT 1);
SET @fac_id  := (SELECT p.id FROM preguntas p JOIN encabezados_encuesta e ON e.id = p.encabezado_id
                 WHERE e.encuesta_id = 6 AND TRIM(p.pregunta) = 'Facultad' ORDER BY p.id LIMIT 1);
SET @prog_id := (SELECT p.id FROM preguntas p JOIN encabezados_encuesta e ON e.id = p.encabezado_id
                 WHERE e.encuesta_id = 6 AND TRIM(p.pregunta) IN ('Programa académico','Programa academico') ORDER BY p.id LIMIT 1);

-- Verificacion previa (los tres deben tener id, no NULL):
SELECT @niv_id AS nivel_id, @fac_id AS facultad_id, @prog_id AS programa_id;

SET @ok := (@niv_id IS NOT NULL AND @fac_id IS NOT NULL AND @prog_id IS NOT NULL
            AND (SELECT tipo_pregunta FROM preguntas WHERE id = @fac_id) <> 'lista_desplegable');

SET @enc_id     := (SELECT encabezado_id FROM preguntas WHERE id = @fac_id);
SET @fac_orden  := (SELECT orden FROM preguntas WHERE id = @fac_id);
SET @fac_req    := (SELECT requerido FROM preguntas WHERE id = @fac_id);
SET @fac_txt    := (SELECT pregunta FROM preguntas WHERE id = @fac_id);
SET @prog_req   := (SELECT requerido FROM preguntas WHERE id = @prog_id);
SET @prog_txt   := (SELECT pregunta FROM preguntas WHERE id = @prog_id);

-- Hace espacio en el orden para las preguntas nuevas
UPDATE preguntas SET orden = orden + 100
WHERE @ok = 1 AND encabezado_id = @enc_id AND orden > @fac_orden AND id <> @prog_id;

-- 1) Nivel de formacion -> seleccion unica (Pregrado / Posgrado)
UPDATE preguntas SET tipo_pregunta = 'seleccion_unica', opciones = '["Pregrado", "Posgrado"]'
WHERE @ok = 1 AND id = @niv_id;

-- 2) Facultad existente -> lista desplegable, solo si Nivel = Pregrado
UPDATE preguntas SET tipo_pregunta = 'lista_desplegable', opciones = '["ARQUITECTURA", "BELLAS ARTES", "CIENCIAS BASICAS", "CIENCIAS DE LA EDUCACION", "CIENCIAS DE LA SALUD", "CIENCIAS ECONOMICAS", "CIENCIAS HUMANAS", "CIENCIAS JURIDICAS", "INGENIERIA", "QUIMICA Y FARMACIA"]',
  pregunta_padre_id = @niv_id, condicion_operador = 'igual', condicion_valor = 'Pregrado'
WHERE @ok = 1 AND id = @fac_id;

-- 3) Facultad para Posgrado (nueva)
INSERT INTO preguntas (encabezado_id, pregunta, tipo_pregunta, opciones, requerido, guardar_banco, orden, pregunta_padre_id, condicion_operador, condicion_valor)
SELECT @enc_id, @fac_txt, 'lista_desplegable', '["ARQUITECTURA", "CIENCIAS BASICAS", "CIENCIAS DE LA EDUCACION", "CIENCIAS DE LA SALUD", "CIENCIAS ECONOMICAS", "CIENCIAS HUMANAS", "CIENCIAS JURIDICAS", "INGENIERIA", "QUIMICA Y FARMACIA"]', @fac_req, 0, @fac_orden + 1, @niv_id, 'igual', 'Posgrado'
FROM DUAL WHERE @ok = 1;
SET @fac_pos_id := IF(@ok = 1, LAST_INSERT_ID(), NULL);

-- 4) Programas de Pregrado / ARQUITECTURA  (reutiliza la pregunta 'Programa academico' existente)
UPDATE preguntas SET tipo_pregunta = 'lista_desplegable', opciones = '["ARQUITECTURA", "TECNICA PROFESIONAL EN CONSTRUCCION DE PROYECTOS ARQUITECTONICOS", "TECNICA PROFESIONAL EN DOCUMENTACION DE PROYECTOS URBANISTICOS", "TECNICA PROFESIONAL EN EXPRESION GRAFICA ARQUITECTONICA", "TECNOLOGIA EN GESTION DE LA CONSTRUCCIÓN DE PROYECTOS ARQUITECTONICOS", "TECNOLOGIA EN GESTION DE PROYECTOS URBANISTICOS", "TECNOLOGIA EN MODELADO DIGITAL ARQUITECTONICO"]', orden = @fac_orden + 2,
  pregunta_padre_id = @fac_id, condicion_operador = 'igual', condicion_valor = 'ARQUITECTURA'
WHERE @ok = 1 AND id = @prog_id;

-- Programas de Pregrado / BELLAS ARTES
INSERT INTO preguntas (encabezado_id, pregunta, tipo_pregunta, opciones, requerido, guardar_banco, orden, pregunta_padre_id, condicion_operador, condicion_valor)
SELECT @enc_id, @prog_txt, 'lista_desplegable', '["ARTE DRAMATICO", "ARTES PLASTICAS", "DANZA", "LICENCIATURA EN MUSICA", "MUSICA"]', @prog_req, 0, @fac_orden + 3, @fac_id, 'igual', 'BELLAS ARTES'
FROM DUAL WHERE @ok = 1;

-- Programas de Pregrado / CIENCIAS BASICAS
INSERT INTO preguntas (encabezado_id, pregunta, tipo_pregunta, opciones, requerido, guardar_banco, orden, pregunta_padre_id, condicion_operador, condicion_valor)
SELECT @enc_id, @prog_txt, 'lista_desplegable', '["BIOLOGIA", "FISICA", "MATEMATICAS", "QUIMICA", "TECNICA PROFESIONAL EN PISCICULTURA CONTINENTAL", "TECNOLOGIA EN ACUICULTURA CONTINENTAL"]', @prog_req, 0, @fac_orden + 4, @fac_id, 'igual', 'CIENCIAS BASICAS'
FROM DUAL WHERE @ok = 1;

-- Programas de Pregrado / CIENCIAS DE LA EDUCACION
INSERT INTO preguntas (encabezado_id, pregunta, tipo_pregunta, opciones, requerido, guardar_banco, orden, pregunta_padre_id, condicion_operador, condicion_valor)
SELECT @enc_id, @prog_txt, 'lista_desplegable', '["LICENCIATURA EN CIENCIAS NATURALES", "LICENCIATURA EN CIENCIAS SOCIALES", "LICENCIATURA EN EDUCACION ARTISTICA", "LICENCIATURA EN EDUCACIÓN ESPECIAL", "LICENCIATURA EN EDUCACION FISICA, RECREACION Y DEPORTES", "LICENCIATURA EN EDUCACION INFANTIL", "LICENCIATURA EN HUMANIDADES Y LENGUA CASTELLANA", "LICENCIATURA EN LENGUAS EXTRANJERAS CON ENFASIS EN INGLES Y FRANCES", "LICENCIATURA EN MATEMATICA"]', @prog_req, 0, @fac_orden + 5, @fac_id, 'igual', 'CIENCIAS DE LA EDUCACION'
FROM DUAL WHERE @ok = 1;

-- Programas de Pregrado / CIENCIAS DE LA SALUD
INSERT INTO preguntas (encabezado_id, pregunta, tipo_pregunta, opciones, requerido, guardar_banco, orden, pregunta_padre_id, condicion_operador, condicion_valor)
SELECT @enc_id, @prog_txt, 'lista_desplegable', '["MEDICINA", "NUTRICION Y DIETETICA", "TECNICA PROFESIONAL EN PROCESAMIENTO Y CALIDAD NUTRICIONAL DE ALIMENTOS", "TECNOLOGIA EN SEGURIDAD ALIMENTARIA Y NUTRICIONAL"]', @prog_req, 0, @fac_orden + 6, @fac_id, 'igual', 'CIENCIAS DE LA SALUD'
FROM DUAL WHERE @ok = 1;

-- Programas de Pregrado / CIENCIAS ECONOMICAS
INSERT INTO preguntas (encabezado_id, pregunta, tipo_pregunta, opciones, requerido, guardar_banco, orden, pregunta_padre_id, condicion_operador, condicion_valor)
SELECT @enc_id, @prog_txt, 'lista_desplegable', '["ADMINISTRACION DE EMPRESAS", "ADMINISTRACIÓN DE EMPRESAS TURISTICAS", "CONTADURIA PUBLICA", "ECONOMIA", "TECNICO PROFESIONAL EN OPERACION TURISTICA", "TECNOLOGO EN GESTION TURISTICA"]', @prog_req, 0, @fac_orden + 7, @fac_id, 'igual', 'CIENCIAS ECONOMICAS'
FROM DUAL WHERE @ok = 1;

-- Programas de Pregrado / CIENCIAS HUMANAS
INSERT INTO preguntas (encabezado_id, pregunta, tipo_pregunta, opciones, requerido, guardar_banco, orden, pregunta_padre_id, condicion_operador, condicion_valor)
SELECT @enc_id, @prog_txt, 'lista_desplegable', '["FILOSOFIA", "HISTORIA", "INTERPRETACION DE LENGUA DE SEÑAS COLOMBIANA - ESPAÑOL", "SOCIOLOGIA"]', @prog_req, 0, @fac_orden + 8, @fac_id, 'igual', 'CIENCIAS HUMANAS'
FROM DUAL WHERE @ok = 1;

-- Programas de Pregrado / CIENCIAS JURIDICAS
INSERT INTO preguntas (encabezado_id, pregunta, tipo_pregunta, opciones, requerido, guardar_banco, orden, pregunta_padre_id, condicion_operador, condicion_valor)
SELECT @enc_id, @prog_txt, 'lista_desplegable', '["CIENCIA POLITICA", "DERECHO"]', @prog_req, 0, @fac_orden + 9, @fac_id, 'igual', 'CIENCIAS JURIDICAS'
FROM DUAL WHERE @ok = 1;

-- Programas de Pregrado / INGENIERIA
INSERT INTO preguntas (encabezado_id, pregunta, tipo_pregunta, opciones, requerido, guardar_banco, orden, pregunta_padre_id, condicion_operador, condicion_valor)
SELECT @enc_id, @prog_txt, 'lista_desplegable', '["INGENIERIA AGROINDUSTRIAL", "INGENIERIA DE SOFTWARE", "INGENIERIA EN AGRONEGOCIOS", "INGENIERIA INDUSTRIAL", "INGENIERIA MECANICA", "INGENIERIA QUIMICA", "TECNICA PROFESIONAL EN APROVECHAMIENTO DE RESIDUOS AGROPECUARIOS", "TECNOLOGÍA DE TRANSFORMACIÓN DE PRODUCTOS AGROPECUARIOS"]', @prog_req, 0, @fac_orden + 10, @fac_id, 'igual', 'INGENIERIA'
FROM DUAL WHERE @ok = 1;

-- Programas de Pregrado / QUIMICA Y FARMACIA
INSERT INTO preguntas (encabezado_id, pregunta, tipo_pregunta, opciones, requerido, guardar_banco, orden, pregunta_padre_id, condicion_operador, condicion_valor)
SELECT @enc_id, @prog_txt, 'lista_desplegable', '["FARMACIA"]', @prog_req, 0, @fac_orden + 11, @fac_id, 'igual', 'QUIMICA Y FARMACIA'
FROM DUAL WHERE @ok = 1;

-- Programas de Posgrado / ARQUITECTURA
INSERT INTO preguntas (encabezado_id, pregunta, tipo_pregunta, opciones, requerido, guardar_banco, orden, pregunta_padre_id, condicion_operador, condicion_valor)
SELECT @enc_id, @prog_txt, 'lista_desplegable', '["MAESTRIA DESARROLLO URBANO SOSTENIBLE", "MAESTRIA PATRIMONIO ARQUITECTONICO, URBANO Y PAISAJISTICO"]', @prog_req, 0, @fac_orden + 12, @fac_pos_id, 'igual', 'ARQUITECTURA'
FROM DUAL WHERE @ok = 1;

-- Programas de Posgrado / CIENCIAS BASICAS
INSERT INTO preguntas (encabezado_id, pregunta, tipo_pregunta, opciones, requerido, guardar_banco, orden, pregunta_padre_id, condicion_operador, condicion_valor)
SELECT @enc_id, @prog_txt, 'lista_desplegable', '["DOCTORADO EN CIENCIAS FÍSICA", "DOCTORADO EN CIENCIAS QUIMICAS", "DOCTORADO EN MEDICINA TROPICAL", "ESPECIALIZACIÓN EN DIDACTICA DE LAS MATEMÁTICAS", "ESPECIALIZACIÓN EN ESTADÍSTICA APLICADA", "MAESTRIA EN BIOLOGÍA", "MAESTRIA EN CIENCIAS AMBIENTALES", "MAESTRIA EN CIENCIAS FÍSICAS", "MAESTRIA EN CIENCIAS MATEMÁTICAS", "MAESTRÍA EN CIENCIAS QUÍMICAS", "MAESTRÍA EN DIDÁCTICA DE LAS MATEMÁTICAS"]', @prog_req, 0, @fac_orden + 13, @fac_pos_id, 'igual', 'CIENCIAS BASICAS'
FROM DUAL WHERE @ok = 1;

-- Programas de Posgrado / CIENCIAS DE LA EDUCACION
INSERT INTO preguntas (encabezado_id, pregunta, tipo_pregunta, opciones, requerido, guardar_banco, orden, pregunta_padre_id, condicion_operador, condicion_valor)
SELECT @enc_id, @prog_txt, 'lista_desplegable', '["DOCTORADO EN CIENCIAS DE LA EDUCACIÓN", "MAESTRIA EN ACTIVIDAD FISICA ADAPTADA", "MAESTRIA EN EDUCACIÓN", "MAESTRIA EN EDUCACION AMBIENTAL", "MAESTRIA EN ENTRENAMIENTO DEPORTIVO", "MAESTRÍA EN NEUROPEDAGOGÍA"]', @prog_req, 0, @fac_orden + 14, @fac_pos_id, 'igual', 'CIENCIAS DE LA EDUCACION'
FROM DUAL WHERE @ok = 1;

-- Programas de Posgrado / CIENCIAS DE LA SALUD
INSERT INTO preguntas (encabezado_id, pregunta, tipo_pregunta, opciones, requerido, guardar_banco, orden, pregunta_padre_id, condicion_operador, condicion_valor)
SELECT @enc_id, @prog_txt, 'lista_desplegable', '["ESPECIALIZACIÓN EN CONTRATACIÓN E INTERVENTORÍA DE SERVICIOS ALIMENTARIOS A COLECTIVIDADES", "MAESTRIA EN SEGURIDAD ALIMENTARIA Y NUTRICIONAL"]', @prog_req, 0, @fac_orden + 15, @fac_pos_id, 'igual', 'CIENCIAS DE LA SALUD'
FROM DUAL WHERE @ok = 1;

-- Programas de Posgrado / CIENCIAS ECONOMICAS
INSERT INTO preguntas (encabezado_id, pregunta, tipo_pregunta, opciones, requerido, guardar_banco, orden, pregunta_padre_id, condicion_operador, condicion_valor)
SELECT @enc_id, @prog_txt, 'lista_desplegable', '["ESPECIALIZACION EN FINANZAS TERRITORIALES", "ESPECIALIZACION EN GESTION FISCAL Y CONTABILIDAD PUBLICA", "ESPECIALIZACION EN GESTION INTEGRAL DEL TURISMO", "MAESTRIA EN ADMINISTRACION DE EMPRESAS", "MAESTRIA EN CONTABILIDAD", "MAESTRIA EN ECONOMIA"]', @prog_req, 0, @fac_orden + 16, @fac_pos_id, 'igual', 'CIENCIAS ECONOMICAS'
FROM DUAL WHERE @ok = 1;

-- Programas de Posgrado / CIENCIAS HUMANAS
INSERT INTO preguntas (encabezado_id, pregunta, tipo_pregunta, opciones, requerido, guardar_banco, orden, pregunta_padre_id, condicion_operador, condicion_valor)
SELECT @enc_id, @prog_txt, 'lista_desplegable', '["MAESTRÍA EN ESTUDIOS DE GÉNERO Y VIOLENCIA INTRAFAMILIAR", "MAESTRIA EN FILOSOFIA", "MAESTRIA EN GESTION LOGISTICA", "MAESTRÍA EN HISTORIA", "MAESTRÍA EN LINGÜISTICA", "MAESTRIA EN LITERATURA HISPANOAMERICANA Y DEL CARIBE"]', @prog_req, 0, @fac_orden + 17, @fac_pos_id, 'igual', 'CIENCIAS HUMANAS'
FROM DUAL WHERE @ok = 1;

-- Programas de Posgrado / CIENCIAS JURIDICAS
INSERT INTO preguntas (encabezado_id, pregunta, tipo_pregunta, opciones, requerido, guardar_banco, orden, pregunta_padre_id, condicion_operador, condicion_valor)
SELECT @enc_id, @prog_txt, 'lista_desplegable', '["ESPECIALIZACION EN DERECHO ADMINISTRATIVO Y DE LA FUNCION PUBLICA", "ESPECIALIZACIÓN EN DERECHO LABORAL Y SEGURIDAD SOCIAL", "ESPECIALIZACION EN DERECHO PROCESAL, PROBATORIO Y TECNOLOGIAS DIGITALES", "ESPECIALIZACION EN TEORIA DEL DELITO Y CASACION PENAL", "MAESTRIA EN ESTUDIOS E INVESTIGACIONES POLITICAS Y JURIDICAS", "MAESTRIA EN INVESTIGACIONES JURIDICAS Y SOCIOJURIDICAS", "MAESTRIA EN PROTECCION SOCIAL"]', @prog_req, 0, @fac_orden + 18, @fac_pos_id, 'igual', 'CIENCIAS JURIDICAS'
FROM DUAL WHERE @ok = 1;

-- Programas de Posgrado / INGENIERIA
INSERT INTO preguntas (encabezado_id, pregunta, tipo_pregunta, opciones, requerido, guardar_banco, orden, pregunta_padre_id, condicion_operador, condicion_valor)
SELECT @enc_id, @prog_txt, 'lista_desplegable', '["DOCTORADO EN INGENIERIA", "ESPECIALIZACION EN GESTION DE LA CALIDAD", "ESPECIALIZACION EN GESTION EFICIENTE DE ENERGIA", "ESPECIALIZACION EN LOGISTICA INTEGRAL", "MAESTRÍA EN GESTIÓN DE LA CALIDAD", "MAESTRÍA EN GESTIÓN ENERGÉTICA", "MAESTRIA EN INGENIERIA QUIMICA"]', @prog_req, 0, @fac_orden + 19, @fac_pos_id, 'igual', 'INGENIERIA'
FROM DUAL WHERE @ok = 1;

-- Programas de Posgrado / QUIMICA Y FARMACIA
INSERT INTO preguntas (encabezado_id, pregunta, tipo_pregunta, opciones, requerido, guardar_banco, orden, pregunta_padre_id, condicion_operador, condicion_valor)
SELECT @enc_id, @prog_txt, 'lista_desplegable', '["ESPECIALIZACIÓN EN PLANTAS MEDICINALES", "MAESTRÍA EN GESTIÓN FARMACÉUTICA"]', @prog_req, 0, @fac_orden + 20, @fac_pos_id, 'igual', 'QUIMICA Y FARMACIA'
FROM DUAL WHERE @ok = 1;

-- Verificacion posterior
SELECT p.id, LEFT(p.pregunta, 22) AS pregunta, p.tipo_pregunta, p.orden, p.pregunta_padre_id AS padre,
       p.condicion_valor, JSON_LENGTH(p.opciones) AS opciones
FROM preguntas p
WHERE p.encabezado_id = @enc_id AND (p.id IN (@niv_id, @fac_id) OR p.pregunta_padre_id IS NOT NULL)
ORDER BY p.orden, p.id;
