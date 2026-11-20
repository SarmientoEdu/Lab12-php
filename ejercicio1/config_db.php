<?php
/**
 * Lab 12 - Configuración de conexión a la base de datos
 * Ajustado para MAMP en Mac (Puerto 8889, pass: root)
 */
define('DB_HOST', 'localhost');
define('DB_PORT', 8889);          // Puerto de MySQL en MAMP
define('DB_USER', 'root');
define('DB_PASS', 'root');        // Contraseña por defecto en MAMP
define('DB_NAME', 'lab12_db');    // Nombre de la BD según tu archivo

function conectar(): mysqli {
    // Se agrega el puerto como 5to parámetro
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
    if ($conn->connect_error) {
        die('Error de conexión: ' . $conn->connect_error);
    }
    $conn->set_charset('utf8mb4');
    return $conn;
}