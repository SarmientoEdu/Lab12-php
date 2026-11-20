-- Lab 12 - Ejercicio 2: Procedimientos almacenados
USE lab12_db;

DROP PROCEDURE IF EXISTS sp_insertar_estudiante;
DROP PROCEDURE IF EXISTS sp_reporte_aprobados;

DELIMITER //

CREATE PROCEDURE sp_insertar_estudiante(
    IN p_nombre VARCHAR(100),
    IN p_nota   DECIMAL(5,2)
)
BEGIN
    INSERT INTO estudiantes (nombre, nota) VALUES (p_nombre, p_nota);
END //

CREATE PROCEDURE sp_reporte_aprobados(
    IN p_nota_minima DECIMAL(5,2)
)
BEGIN
    SELECT id, nombre, nota 
    FROM estudiantes 
    WHERE nota >= p_nota_minima 
    ORDER BY nota DESC;
END //

DELIMITER ;