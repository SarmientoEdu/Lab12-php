
<?php
/**
 * Lab 12 - Ejercicio 3: Editar estudiante
 * Implementa las secciones marcadas con TODO.
 */
require_once '../ejercicio1/config_db.php';

$conn = conectar();
$id   = (int)($_GET['id'] ?? 0);
$error = '';

if ($id <= 0) {
    header('Location: index.php');
    exit;
}

// TODO: Cargar datos del estudiante con sentencia preparada usando $id
// $stmt = $conn->prepare("SELECT * FROM estudiantes WHERE id = ?");
// $stmt->bind_param('i', $id);
// $stmt->execute();
// $estudiante = $stmt->get_result()->fetch_assoc();
// if (!$estudiante) { header('Location: index.php'); exit; }
$stmt = $conn->prepare("SELECT * FROM estudiantes WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$estudiante = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$estudiante) {
    $conn->close();
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $nota   = $_POST['nota'] ?? '';

    // TODO: Validar campos (igual que agregar.php)
    if ($nombre === '') {
        $error = 'El nombre no puede estar vacío.';
    } elseif (!is_numeric($nota) || $nota < 0 || $nota > 100) {
        $error = 'La nota debe estar entre 0 y 100.';
    }

    if (empty($error)) {
        // TODO: UPDATE con sentencia preparada
        $stmt = $conn->prepare(
            "UPDATE estudiantes SET nombre = ?, nota = ? WHERE id = ?"
        );

        $nota = (float)$nota;
        $stmt->bind_param("sdi", $nombre, $nota, $id);
        $stmt->execute();
        $stmt->close();

        // TODO: Redirigir a index.php
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
               value="<?= htmlspecialchars($_POST['nombre'] ?? $estudiante['nombre']) ?>" required>

        <label for="nota">Nota (0–100):</label>
        <input type="number" id="nota" name="nota" min="0" max="100" step="0.01"
               value="<?= htmlspecialchars($_POST['nota'] ?? $estudiante['nota']) ?>" required>

        <button type="submit">Guardar Cambios</button>
    </form>
</body>
</html>
