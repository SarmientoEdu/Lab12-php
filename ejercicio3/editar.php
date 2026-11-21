<?php
/**
 * Lab 12 - Ejercicio 3: Editar estudiante
 */
require_once '../ejercicio1/config_db.php';

$conn  = conectar();
$id    = (int)($_GET['id'] ?? 0);
$error = '';

if ($id <= 0) {
    header('Location: index.php');
    exit;
}

// Cargar datos del estudiante con sentencia preparada
$stmt = $conn->prepare("SELECT id, nombre, nota FROM estudiantes WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$estudiante = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$estudiante) {
    $conn->close();
    header('Location: index.php');
    exit;
}

$nombre = $estudiante['nombre'];
$nota   = $estudiante['nota'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $entrada = $_POST;
    $nombre  = trim($entrada['nombre'] ?? '');
    $nota    = $entrada['nota'] ?? '';

    if ($nombre === '') {
        $error = 'El nombre es obligatorio.';
    } elseif (!is_numeric($nota) || $nota < 0 || $nota > 100) {
        $error = 'La nota debe ser un número entre 0 y 100.';
    }

    if (empty($error)) {
        $nota_num = (float)$nota;
        $stmt = $conn->prepare("UPDATE estudiantes SET nombre = ?, nota = ? WHERE id = ?");
        $stmt->bind_param('sdi', $nombre, $nota_num, $id);
        $stmt->execute();
        $stmt->close();
        $conn->close();
        header('Location: index.php');
        exit;
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lab 12 - Editar Estudiante</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>
    <nav>
        <a href="index.php">← Volver a la lista</a>
    </nav>

    <h1>Editar Estudiante #<?= $id ?></h1>

    <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="editar.php?id=<?= $id ?>">
        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="nombre" maxlength="100"
               value="<?= htmlspecialchars($nombre) ?>" required>

        <label for="nota">Nota (0–100):</label>
        <input type="number" id="nota" name="nota" min="0" max="100" step="0.01"
               value="<?= htmlspecialchars((string)$nota) ?>" required>

        <button type="submit">Guardar Cambios</button>
    </form>
</body>
</html>