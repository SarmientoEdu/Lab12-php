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

// Consulta segura mediante sentencia preparada
$stmt = $conn->prepare("SELECT * FROM estudiantes WHERE nombre = ?");
$stmt->bind_param('s', $nombre_buscar);
$stmt->execute();
$resultado = $stmt->get_result();

echo "<h2>Búsqueda (SEGURA)</h2>";

// Mostrar resultados
if ($resultado->num_rows > 0) {
    while ($fila = $resultado->fetch_assoc()) {
        echo "ID: " . htmlspecialchars($fila['id']) . " | ";
        echo "Nombre: " . htmlspecialchars($fila['nombre']) . " | ";
        echo "Nota: " . htmlspecialchars($fila['nota']) . "<br>";
    }
} else {
    echo "No se encontraron estudiantes.";
}

$stmt->close();
$conn->close();
?>
