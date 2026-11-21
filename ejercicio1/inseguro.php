<?php
/**
 * Lab 12 - Ejercicio 1: Versión INSEGURA (demostración)
 * Este archivo muestra cómo NO se debe hacer una consulta SQL.
 * NO usar este patrón en producción.
 */
require_once __DIR__ . '/config_db.php';
require_once __DIR__ . '/config_semilla.php';

$conn = conectar();
$datos = generar_datos(SEMILLA);

// Recibe el nombre por GET (vulnerable a SQLi)
$nombre_buscar = $_GET['nombre'] ?? $datos['nombre'];

// INSEGURO: vulnerable a SQLi - el valor se concatena directamente
$sql = "SELECT * FROM estudiantes WHERE nombre = '$nombre_buscar'"; // INSEGURO: vulnerable a SQLi
$resultado = $conn->query($sql);

echo "<h2>Búsqueda (INSEGURA)</h2>";
echo "<p>Consulta ejecutada: <code>" . htmlspecialchars($sql) . "</code></p>";

if ($resultado && $resultado->num_rows > 0) {
    echo "<table border='1'><tr><th>ID</th><th>Nombre</th><th>Nota</th></tr>";
    while ($fila = $resultado->fetch_assoc()) {
        echo '<tr><td>' . (int)$fila['id'] . '</td><td>' . htmlspecialchars($fila['nombre'], ENT_QUOTES, 'UTF-8') . '</td><td>' . number_format((float)$fila['nota'], 2) . '</td></tr>';
    }
    echo "</table>";
} else {
    echo "<p>No se encontraron resultados.</p>";
}

$conn->close();
