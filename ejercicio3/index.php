<?php
/**
 * Lab 12 - Ejercicio 3: Listar estudiantes
 * Implementa las secciones marcadas con TODO.
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

$stmt = $conn->prepare("SELECT * FROM estudiantes ORDER BY id");
$stmt->execute();
$resultado = $stmt->get_result();

// Guardar las filas en un array para usarlas en el HTML
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
    <title>Lab 12 - Gestión de Estudiantes</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>
    <nav>
        <a href="index.php">Lista de Estudiantes</a>
        <a href="agregar.php">Agregar Estudiante</a>
    </nav>

    <h1>Lista de Estudiantes</h1>

    <table>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Nota</th>
            <th>Acciones</th>
        </tr>
        <?php foreach ($estudiantes as $e): ?>
            <?php
                if ($e['nota'] >= 90) {
                    $clase = 'nota-alta';
                } elseif ($e['nota'] >= 70) {
                    $clase = 'nota-media';
                } else {
                    $clase = 'nota-baja';
                }
            ?>
            <tr class="<?= $clase ?>">
                <td><?= $e['id'] ?></td>
                <td><?= htmlspecialchars($e['nombre']) ?></td>
                <td><?= number_format($e['nota'], 2) ?></td>
                <td>
                    <a href="editar.php?id=<?= $e['id'] ?>">Editar</a>
                    <a href="eliminar.php?id=<?= $e['id'] ?>">Eliminar</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

</body>
</html>