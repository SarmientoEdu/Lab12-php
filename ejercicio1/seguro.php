<?php
/**
 * Lab 12 - Ejercicio 1: Versión SEGURA con sentencias preparadas
 */
require_once 'config_db.php';
require_once 'config_semilla.php';

$conn = conectar();
$datos = generar_datos(SEMILLA);
$nombre_buscar = $_GET['nombre'] ?? $datos['nombre'];

// SENTENCIA PREPARADA (reemplazo de la consulta concatenada)
$stmt = $conn->prepare("SELECT * FROM estudiantes WHERE nombre = ?");
$stmt->bind_param("s", $nombre_buscar);
$stmt->execute();
$resultado = $stmt->get_result();

echo "<h2>Búsqueda (SEGURA)</h2>";

// Mostrar resultados con fetch_assoc()
while ($fila = $resultado->fetch_assoc()) {
    echo "ID: {$fila['id']} | Nombre: {$fila['nombre']} | Nota: " . number_format($fila['nota'], 2, '.', '') . "<br>";
}

$conn->close();
?>