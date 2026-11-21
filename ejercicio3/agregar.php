
<?php
/**
 * Lab 12 - Ejercicio 3: Agregar estudiante
 * Implementa las secciones marcadas con TODO.
 */
require_once '../ejercicio1/config_db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $nota   = $_POST['nota'] ?? '';

    // TODO: Validar que $nombre no esté vacío y que $nota esté entre 0 y 100
    // Si hay error, guardar mensaje en $error
    if ($nombre === '') {
        $error = 'El nombre no puede estar vacío.';
    } elseif (!is_numeric($nota) || $nota < 0 || $nota > 100) {
        $error = 'La nota debe estar entre 0 y 100.';
    }

    if (empty($error)) {
        $conn = conectar();
        // TODO: Insertar con sentencia preparada (prepare/bind_param/execute)
        $stmt = $conn->prepare(
            "INSERT INTO estudiantes (nombre, nota) VALUES (?, ?)"
        );
        $nota = (float)$nota;
        $stmt->bind_param("sd", $nombre, $nota);
        $stmt->execute();
        $stmt->close();

        // TODO: Redirigir a index.php con header('Location: index.php'); exit;
        $conn->close();
        header('Location: index.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Lab 12 - Agregar Estudiante</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>
    <nav>
        <a href="index.php">← Volver a la lista</a>
    </nav>

    <h1>Agregar Estudiante</h1>

    <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="agregar.php">
        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="nombre"
               value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>" required>

        <label for="nota">Nota (0–100):</label>
        <input type="number" id="nota" name="nota" min="0" max="100" step="0.01"
               value="<?= htmlspecialchars($_POST['nota'] ?? '') ?>" required>

        <button type="submit">Agregar</button>
    </form>
</body>
</html>
