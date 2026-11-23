<?php
/**
 * Lab 12 - Ejercicio 3: Eliminar estudiante
 * Confirmación + DELETE con sentencia preparada.
 */
require_once '../ejercicio1/config_db.php';

$conn = conectar();
$id   = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    header('Location: index.php');
    exit;
}

// 1) Cargar el nombre del estudiante para mostrarlo en la confirmación
$stmt = $conn->prepare("SELECT nombre FROM estudiantes WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$fila = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Si el id no existe, volver a la lista
if (!$fila) {
    $conn->close();
    header('Location: index.php');
    exit;
}
$nombre_estudiante = $fila['nombre'];

// 2) Si el usuario confirmó, eliminar
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirmar'])) {
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
    <title>Lab 12 - Eliminar Estudiante</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>
    <nav>
        <a href="index.php">← Volver a la lista</a>
    </nav>

    <h1>Eliminar Estudiante</h1>

    <p>¿Estás seguro de que deseas eliminar a
       <strong><?= htmlspecialchars($nombre_estudiante) ?></strong>?
    </p>

    <form method="POST" action="eliminar.php?id=<?= $id ?>">
        <button type="submit" name="confirmar" value="1">Sí, eliminar</button>
        <a href="index.php">Cancelar</a>
    </form>
</body>
</html>