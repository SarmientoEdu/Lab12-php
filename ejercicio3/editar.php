<?php
require_once __DIR__ . '/../ejercicio1/config_db.php';

$conn = conectar();
$id = (int)($_GET['id'] ?? 0);
$error = '';

if ($id <= 0) {
    $conn->close();
    header('Location: index.php');
    exit;
}

// Buscar estudiante mediante sentencia preparada.
$stmt = $conn->prepare(
    'SELECT id, nombre, nota FROM estudiantes WHERE id = ?'
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $nota = trim($_POST['nota'] ?? '');

    if ($nombre === '') {
        $error = 'El nombre no puede estar vacio.';
    } elseif (!is_numeric($nota) || (float)$nota < 0 || (float)$nota > 100) {
        $error = 'La nota debe ser un numero entre 0 y 100.';
    }

    if ($error === '') {
        $nota_numero = (float)$nota;

        $stmt = $conn->prepare(
            'UPDATE estudiantes SET nombre = ?, nota = ? WHERE id = ?'
        );
        $stmt->bind_param('sdi', $nombre, $nota_numero, $id);

        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
            header('Location: index.php');
            exit;
        } else {
            $error = 'No se pudo actualizar el estudiante.';
        }

        $stmt->close();
    }

    // Mantener los valores introducidos si hay un error.
    $estudiante['nombre'] = $nombre;
    $estudiante['nota'] = $nota;
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
        <a href="index.php">Lista de Estudiantes</a>
        <a href="agregar.php">Agregar Estudiante</a>
    </nav>

    <h1>Editar Estudiante</h1>

    <?php if ($error !== ''): ?>
        <div class="error">
            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="editar.php?id=<?= (int)$estudiante['id'] ?>">
        <label for="nombre">Nombre:</label>
        <input
            type="text"
            id="nombre"
            name="nombre"
            value="<?= htmlspecialchars($estudiante['nombre'], ENT_QUOTES, 'UTF-8') ?>"
            required
        >

        <label for="nota">Nota (0 a 100):</label>
        <input
            type="number"
            id="nota"
            name="nota"
            min="0"
            max="100"
            step="0.01"
            value="<?= htmlspecialchars((string)$estudiante['nota'], ENT_QUOTES, 'UTF-8') ?>"
            required
        >

        <button type="submit">Guardar cambios</button>
    </form>
</body>
</html>