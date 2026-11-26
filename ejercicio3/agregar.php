<?php
require_once __DIR__ . '/../ejercicio1/config_db.php';

$error = '';
$nombre = '';
$nota = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $nota = trim($_POST['nota'] ?? '');

    if ($nombre === '') {
        $error = 'El nombre no puede estar vacio.';
    } elseif (!is_numeric($nota) || (float)$nota < 0 || (float)$nota > 100) {
        $error = 'La nota debe ser un numero entre 0 y 100.';
    }

    if ($error === '') {
        $conn = conectar();

        $stmt = $conn->prepare(
            'INSERT INTO estudiantes (nombre, nota) VALUES (?, ?)'
        );

        if ($stmt) {
            $nota_numero = (float)$nota;
            $stmt->bind_param('sd', $nombre, $nota_numero);

            if ($stmt->execute()) {
                $stmt->close();
                $conn->close();
                header('Location: index.php');
                exit;
            } else {
                $error = 'No se pudo guardar el estudiante.';
            }

            $stmt->close();
        } else {
            $error = 'No se pudo preparar la consulta.';
        }

        $conn->close();
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
        <a href="index.php">Lista de Estudiantes</a>
        <a href="agregar.php">Agregar Estudiante</a>
    </nav>

    <h1>Agregar Estudiante</h1>

    <?php if ($error !== ''): ?>
        <div class="error">
            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="agregar.php">
        <label for="nombre">Nombre:</label>
        <input
            type="text"
            id="nombre"
            name="nombre"
            value="<?= htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') ?>"
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
            value="<?= htmlspecialchars($nota, ENT_QUOTES, 'UTF-8') ?>"
            required
        >

        <button type="submit">Guardar estudiante</button>
    </form>
</body>
</html>