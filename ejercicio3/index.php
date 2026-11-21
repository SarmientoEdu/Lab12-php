<?php
/**
 * Lab 12 - Ejercicio 3: Listar estudiantes
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

// Obtener todos los estudiantes con sentencia preparada
$stmt = $conn->prepare("SELECT * FROM estudiantes ORDER BY id");
$stmt->execute();
$estudiantes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$conn->close();

// Devuelve la clase CSS según la nota
function clase_nota(float $nota): string {
    if ($nota >= 90) return 'nota-alta';
    if ($nota >= 70) return 'nota-media';
    return 'nota-baja';
}
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

    <a class="btn" href="agregar.php">Agregar estudiante</a>

    <table>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Nota</th>
            <th>Acciones</th>
        </tr>
        <?php foreach ($estudiantes as $e): ?>
        <tr class="<?= clase_nota((float)$e['nota']) ?>">
            <td><?= $e['id'] ?></td>
            <td><?= htmlspecialchars($e['nombre']) ?></td>
            <td><?= number_format($e['nota'], 2) ?></td>
            <td>
                <a href="editar.php?id=<?= $e['id'] ?>">Editar</a> |
                <a href="eliminar.php?id=<?= $e['id'] ?>">Eliminar</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>