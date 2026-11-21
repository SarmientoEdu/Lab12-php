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
$stmt = $conn->prepare("SELECT id, nombre, nota FROM estudiantes ORDER BY id");
$stmt->execute();
$estudiantes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
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

       <p><a href="agregar.php">+ Agregar estudiante</a></p>

    <table>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Nota</th>
            <th>Acciones</th>
        </tr>
        <?php foreach ($estudiantes as $e): ?>
            <?php
            $nota = (float)$e['nota'];
            if ($nota >= 90) {
                $clase = 'nota-alta';
            } elseif ($nota >= 70) {
                $clase = 'nota-media';
            } else {
                $clase = 'nota-baja';
            }
            ?>
            <tr class="<?php echo $clase; ?>">
                <td><?php echo $e['id']; ?></td>
                <td><?php echo htmlspecialchars($e['nombre']); ?></td>
                <td><?php echo number_format($nota, 2); ?></td>
                <td>
                    <a href="editar.php?id=<?php echo $e['id']; ?>">Editar</a>
                    <a href="eliminar.php?id=<?php echo $e['id']; ?>">Eliminar</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>