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

// Obtener todos los estudiantes con sentencia preparada
$stmt = $conn->prepare("SELECT id, nombre, nota FROM estudiantes ORDER BY id");
$stmt->execute();
$resultado = $stmt->get_result();

$estudiantes = [];
if ($resultado) {
    while ($fila = $resultado->fetch_assoc()) {
        $estudiantes[] = $fila;
    }
    $resultado->free();
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
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Nota</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($estudiantes) > 0): ?>
                <?php foreach ($estudiantes as $est): ?>
                    <?php
                        $nota = (float)$est['nota'];
                        if ($nota >= 90) {
                            $clase_nota = 'nota-alta';
                        } elseif ($nota >= 70) {
                            $clase_nota = 'nota-media';
                        } else {
                            $clase_nota = 'nota-baja';
                        }
                    ?>
                    <tr class="<?php echo $clase_nota; ?>">
                        <td><?php echo htmlspecialchars($est['id']); ?></td>
                        <td><?php echo htmlspecialchars($est['nombre']); ?></td>
                        <td><?php echo number_format($nota, 2, '.', ''); ?></td>
                        <td>
                            <a href="editar.php?id=<?php echo urlencode($est['id']); ?>">Editar</a> |
                            <a href="eliminar.php?id=<?php echo urlencode($est['id']); ?>">Eliminar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4">No hay estudiantes registrados.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

</body>
</html>