<?php
/**
 * Lab 12 - Ejercicio 3: Eliminar estudiante
 */
require_once '../ejercicio1/config_db.php';

$conn = conectar();
$id   = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    header('Location: index.php');
    exit;
}

// Cargar nombre del estudiante con sentencia preparada
$stmt = $conn->prepare("SELECT nombre FROM estudiantes WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$fila = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$fila) {
    $conn->close();
    header('Location: index.php');
    exit;
}
$nombre_estudiante = $fila['nombre'];

$entrada = $_POST;
$es_post = $_SERVER['REQUEST_METHOD'] === 'POST';

if ($es_post && isset($entrada['confirmar'])) {
    // DELETE con sentencia preparada
    $stmt = $conn->prepare("DELETE FROM estudiantes WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();
    $conn->close();
    header('Location: index.php');
    exit;
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lab 12 - Eliminar Estudiante</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>
    <nav>
        <a href="index.php">← Volver a la lista</a>
    </nav>

    <h1>Eliminar Estudiante</h1>

    <div class="confirmacion">
        <p>¿Estás seguro de que deseas eliminar a
           <strong><?= htmlspecialchars($nombre_estudiante) ?></strong>?
        </p>

        <form method="POST" action="eliminar.php?id=<?= $id ?>">
            <button type="submit" name="confirmar" value="1" class="btn-peligro">Sí, eliminar</button>
            <a class="cancelar" href="index.php">Cancelar</a>
        </form>
    </div>
</body>
</html>