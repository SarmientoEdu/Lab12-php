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
$stmt = $conn->prepare("SELECT id, nombre, nota FROM estudiantes ORDER BY id");
$stmt->execute();
$resultado = $stmt->get_result();
$estudiantes = $resultado->fetch_all(MYSQLI_ASSOC);
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
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lab 12 - Gestión de Estudiantes</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>
    <nav>
        <a href="index.php">Lista de Estudiantes</a>
        <a href="agregar.php">Agregar Estudiante</a>
    </nav>

    <h1>Lista de Estudiantes</h1>

    <p><a class="boton" href="agregar.php">+ Agregar estudiante</a></p>

    <?php if (count($estudiantes) === 0): ?>
        <p>No hay estudiantes registrados.</p>
    <?php else: ?>
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
        <?php foreach ($estudiantes as $e): ?>
            <tr class="<?= clase_nota((float)$e['nota']) ?>">
                <td><?= (int)$e['id'] ?></td>
                <td><?= htmlspecialchars($e['nombre']) ?></td>
                <td><?= number_format($e['nota'], 2) ?></td>
                <td class="acciones">
                    <a href="editar.php?id=<?= (int)$e['id'] ?>">Editar</a>
                    <a class="peligro" href="eliminar.php?id=<?= (int)$e['id'] ?>">Eliminar</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</body>
</html>