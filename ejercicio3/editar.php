<?php
/**
 * Lab 12 - Ejercicio 3: Editar estudiante
 * Edición de estudiantes.
 */
require_once '../ejercicio1/config_db.php';

$conn = conectar();
$id   = (int)(filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: 0);
$error = '';

if ($id <= 0) {
    header('Location: index.php');
    exit;
}

$stmt = $conn->prepare('SELECT id, nombre, nota FROM estudiantes WHERE id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$estudiante = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$estudiante) { $conn->close(); header('Location: index.php'); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim((string)(filter_input(INPUT_POST, 'nombre') ?? ''));
    $nota = (string)(filter_input(INPUT_POST, 'nota') ?? '');

    if ($nombre === '' || strlen($nombre) > 100 || !is_numeric($nota) || (float)$nota < 0 || (float)$nota > 100) {
        $error = 'Escribe un nombre válido y una nota entre 0 y 100.';
    }

    if (empty($error)) {
        $valor_nota = (float)$nota;
        $stmt = $conn->prepare('UPDATE estudiantes SET nombre = ?, nota = ? WHERE id = ?');
        $stmt->bind_param('sdi', $nombre, $valor_nota, $id);
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
    <title>Lab 12 - Editar Estudiante</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>
    <nav>
        <a href="index.php">← Volver a la lista</a>
    </nav>

    <h1>Editar Estudiante</h1>

    <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="editar.php?id=<?= $id ?>">
        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="nombre"
               value="<?= htmlspecialchars($nombre ?? $estudiante['nombre'], ENT_QUOTES, 'UTF-8') ?>" required>

        <label for="nota">Nota (0–100):</label>
        <input type="number" id="nota" name="nota" min="0" max="100" step="0.01"
               value="<?= htmlspecialchars($nota ?? $estudiante['nota'], ENT_QUOTES, 'UTF-8') ?>" required>

        <button type="submit">Guardar Cambios</button>
    </form>
</body>
</html>
