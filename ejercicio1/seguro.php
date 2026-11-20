<?php
/**
 * Lab 12 - Ejercicio 1: Versión SEGURA con sentencias preparadas
 * Completa las secciones marcadas con TODO.
 */
require_once 'config_db.php';
require_once 'config_semilla.php';

$conn = conectar();
$datos = generar_datos(SEMILLA);
$nombre_buscar = $_GET['nombre'] ?? $datos['nombre'];

// Sentencia preparada para prevenir inyección SQL
$stmt = $conn->prepare("SELECT * FROM estudiantes WHERE nombre = ?");
$stmt->bind_param('s', $nombre_buscar);
$stmt->execute();
$resultado = $stmt->get_result();

echo "<h2>Búsqueda (SEGURA)</h2>";

if ($resultado && $resultado->num_rows > 0) {
    echo "<table border='1'><tr><th>ID</th><th>Nombre</th><th>Nota</th></tr>";
    while ($fila = $resultado->fetch_assoc()) {
        echo "<tr><td>" . htmlspecialchars($fila['id']) . "</td><td>" . htmlspecialchars($fila['nombre']) . "</td><td>" . htmlspecialchars($fila['nota']) . "</td></tr>";
    }
    echo "</table>";
} else {
    echo "<p>No se encontraron resultados.</p>";
}

$stmt->close();
$conn->close();