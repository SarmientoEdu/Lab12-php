<?php
require_once __DIR__ . '/../ejercicio1/config_db.php';
require_once __DIR__ . '/../ejercicio1/config_semilla.php';
require_once __DIR__ . '/seed_data.php';

$conn = conectar();

// Sembrar datos de prueba si la tabla esta vacia.
$check = $conn->query("SELECT COUNT(*) AS total FROM estudiantes");
$total = (int)$check->fetch_assoc()['total'];

if ($total === 0) {
    sembrar_datos($conn);
}

// Obtener todos los estudiantes con sentencia preparada.
$stmt = $conn->prepare(
    'SELECT id, nombre, nota FROM estudiantes ORDER BY id'
);
$stmt->execute();
$resultado = $stmt->get_result();

$estudiantes = [];

while ($fila = $resultado->fetch_assoc()) {
    $estudiantes[] = $fila;
}

$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Lab 12 - Gestion de Estudiantes</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>
    <nav>
        <a href="index.php">Lista de Estudiantes</a>
        <a href="agregar.php">Agregar Estudiante</a>
    </nav>

    <h1>Lista de Estudiantes</h1>

    <?php if (count($estudiantes) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Nota</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($estudiantes as $estudiante): ?>
                    <?php
                    $nota = (float)$estudiante['nota'];

                    if ($nota >= 90) {
                        $clase = 'nota-alta';
                    } elseif ($nota >= 70) {
                        $clase = 'nota-media';
                    } else {
                        $clase = 'nota-baja';
                    }
                    ?>
                    <tr class="<?= $clase ?>">
                        <td><?= (int)$estudiante['id'] ?></td>
                        <td><?= htmlspecialchars($estudiante['nombre'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars((string)$estudiante['nota'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td>
                            <a href="editar.php?id=<?= (int)$estudiante['id'] ?>">Editar</a>
                            |
                            <a href="eliminar.php?id=<?= (int)$estudiante['id'] ?>">Eliminar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No hay estudiantes registrados.</p>
    <?php endif; ?>
</body>
</html>