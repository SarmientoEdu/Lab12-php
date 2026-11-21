<?php
/**
 * Lab 12 - Ejercicio 3: Listar estudiantes
 * Listado de estudiantes.
 */
require_once '../ejercicio1/config_db.php';
require_once '../ejercicio1/config_semilla.php';
require_once 'seed_data.php';

$conn = conectar();

// Sembrar datos de prueba si la tabla está vacía
$check = $conn->query("SELECT COUNT(*) as total FROM estudiantes");
$total = $check->fetch_assoc()['total'];
if ($total == 0) {
    sembrar_datos($conn);
}

$stmt = $conn->prepare("SELECT id, nombre, nota FROM estudiantes ORDER BY id");
$stmt->execute();
$filas = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Lab 12 - Gestión de Estudiantes</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>
    <nav>
        <a href="index.php">Lista de Estudiantes</a>
        <a href="agregar.php">Agregar Estudiante</a>
    </nav>

    <h1>Lista de Estudiantes</h1>

    <p>Alejandro Sáenz · Semilla <?= SEMILLA ?></p>
    <table><thead><tr><th>ID</th><th>Nombre</th><th>Nota</th><th>Acciones</th></tr></thead><tbody>
    <?php foreach ($filas as $fila): $id = (int)$fila['id']; $nota = (float)$fila['nota']; ?>
    <tr class="<?= $nota >= 90 ? 'nota-alta' : ($nota >= 70 ? 'nota-media' : 'nota-baja') ?>">
        <td><?= $id ?></td><td><?= htmlspecialchars($fila['nombre'], ENT_QUOTES, 'UTF-8') ?></td><td><?= number_format($nota, 2) ?></td>
        <td><a href="editar.php?id=<?= $id ?>">Editar</a> · <a href="eliminar.php?id=<?= $id ?>">Eliminar</a></td>
    </tr>
    <?php endforeach; ?></tbody></table>

</body>
</html>
