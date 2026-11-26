<?php
require_once __DIR__ . '/../ejercicio1/config_db.php';

$conn = conectar();
$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    $conn->close();
    header('Location: index.php');
    exit;
}

// Buscar el estudiante que se desea eliminar.
$stmt = $conn->prepare(
    'SELECT nombre FROM estudiantes WHERE id = ?'
);
$stmt->bind_param('i', $id);
$stmt->execute();
$resultado = $stmt->get_result();
$estudiante = $resultado->fetch_assoc();
$stmt->close();

if (!$estudiante) {
    $conn->close();
    header('Location: index.php');
    exit;
}

$nombre_estudiante = $estudiante['nombre'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirmar'])) {
    $stmt = $conn->prepare('DELETE FROM estudiantes WHERE id = ?');
    $stmt->bind_param('i', $id);

    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        header('Location: index.php');
        exit;
    }

    $stmt->close();
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
        <a href="index.php">Lista de Estudiantes</a>
        <a href="agregar.php">Agregar Estudiante</a>
    </nav>

    <h1>Eliminar Estudiante</h1>

    <p>
        ¿Estás seguro de que deseas eliminar al estudiante
        <strong><?= htmlspecialchars($nombre_estudiante, ENT_QUOTES, 'UTF-8') ?></strong>?
    </p>

    <form method="POST" action="eliminar.php?id=<?= $id ?>">
        <button type="submit" name="confirmar" value="1">
            Sí, eliminar
        </button>
        <a href="index.php">Cancelar</a>
    </form>
</body>
</html>