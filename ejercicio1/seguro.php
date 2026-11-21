<?php
require_once __DIR__ . '/config_db.php';
require_once __DIR__ . '/config_semilla.php';
$datos = generar_datos(SEMILLA);
$nombre_buscar = $_GET['nombre'] ?? $datos['nombre'];
if (!is_string($nombre_buscar)) {
    http_response_code(400);
    exit('El nombre debe ser texto.');
}
$conn = conectar();
$stmt = $conn->prepare('SELECT id, nombre, nota FROM estudiantes WHERE nombre = ? ORDER BY id');
$stmt->bind_param('s', $nombre_buscar);
$stmt->execute();
$filas = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="es"><head><meta charset="UTF-8"><title>Búsqueda segura</title></head>
<body><h2>Búsqueda (SEGURA)</h2>
<p>El nombre se envía como un dato separado de la consulta.</p>
<?php if ($filas): ?>
<table border="1"><tr><th>ID</th><th>Nombre</th><th>Nota</th></tr>
<?php foreach ($filas as $fila): ?>
<tr><td><?= (int)$fila['id'] ?></td><td><?= htmlspecialchars($fila['nombre'], ENT_QUOTES, 'UTF-8') ?></td><td><?= number_format((float)$fila['nota'], 2) ?></td></tr>
<?php endforeach; ?></table>
<?php else: ?><p>No se encontraron resultados.</p><?php endif; ?>
</body></html>
