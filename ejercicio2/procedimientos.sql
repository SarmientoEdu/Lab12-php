
-- Lab 12 - Ejercicio 2: Procedimientos almacenados
-- Ejecutar en phpMyAdmin DESPUÉS de haber creado la tabla (setup.sql del ej1)

USE lab12_db;

-- Eliminar procedimientos si existen (para poder re-ejecutar el script)
DROP PROCEDURE IF EXISTS sp_insertar_estudiante;
DROP PROCEDURE IF EXISTS sp_reporte_aprobados;

DELIMITER //

-- Procedimiento 1: Insertar un estudiante
CREATE PROCEDURE sp_insertar_estudiante(
    IN p_nombre     VARCHAR(100),
    IN p_nota       DECIMAL(5,2)
)
BEGIN
    -- TODO: Escribir el INSERT en la tabla estudiantes usando p_nombre y p_nota
    -- Pista: INSERT INTO estudiantes (nombre, nota) VALUES (p_nombre, p_nota);
    INSERT INTO estudiantes (nombre, nota) VALUES (p_nombre, p_nota);
END //

-- Procedimiento 2: Reporte de estudiantes aprobados
CREATE PROCEDURE sp_reporte_aprobados(
    IN p_nota_minima DECIMAL(5,2)
)
BEGIN
    -- TODO: Escribir el SELECT que retorne id, nombre, nota
    -- WHERE nota >= p_nota_minima ORDER BY nota DESC
    SELECT id, nombre, nota
    FROM estudiantes
    WHERE nota >= p_nota_minima
    ORDER BY nota DESC;
END //

DELIMITER ;
