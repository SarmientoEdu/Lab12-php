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

// TODO: Reemplaza la consulta concatenada por una sentencia preparada
$stmt = $conn->prepare("SELECT * FROM estudiantes WHERE nombre = ?");
$stmt->bind_param("s", $nombre_buscar);
$stmt->execute();
$resultado = $stmt->get_result();

echo "<h2>Búsqueda (SEGURA)</h2>";

// TODO: Muestra los resultados usando fetch_assoc() sobre $resultado
if ($resultado && $resultado->num_rows > 0) {
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>Nombre</th><th>Nota</th></tr>";

    while ($fila = $resultado->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . (int)$fila['id'] . "</td>";
        echo "<td>" . htmlspecialchars($fila['nombre'], ENT_QUOTES, 'UTF-8') . "</td>";
        echo "<td>" . htmlspecialchars((string)$fila['nota'], ENT_QUOTES, 'UTF-8') . "</td>";
        echo "</tr>";
    }

    echo "</table>";
} else {
    echo "<p>No se encontraron resultados.</p>";
}

$stmt->close();
$conn->close();