-- Permite el tipo de seguridad 'correos_especificos' en encuestas.
-- (La app lo hace sola al guardar; ejecutar esto solo si el usuario de MySQL no tiene permiso ALTER.)
ALTER TABLE `encuestas` MODIFY `tipo_seguridad` VARCHAR(30) NOT NULL DEFAULT 'publica';
