
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

// TODO: Obtener todos los estudiantes con sentencia preparada (SELECT * FROM estudiantes)
// $stmt = $conn->prepare("SELECT * FROM estudiantes ORDER BY id");
// $stmt->execute();
// $resultado = $stmt->get_result();

$stmt = $conn->prepare("SELECT * FROM estudiantes ORDER BY id");
$stmt->execute();
$resultado = $stmt->get_result();

$estudiantes = $resultado->fetch_all(MYSQLI_ASSOC);
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

    <!-- TODO: Mostrar tabla con columnas: ID | Nombre | Nota | Acciones -->
    <!-- TODO: Clase CSS en <tr>: nota-alta (>=90), nota-media (70-89), nota-baja (<70) -->
    <!-- TODO: En Acciones: enlaces a editar.php?id=X y eliminar.php?id=X -->

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
                <tr class="<?php echo $clase; ?>">
                    <td><?php echo (int)$estudiante['id']; ?></td>
                    <td><?php echo htmlspecialchars($estudiante['nombre'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo number_format($nota, 2, '.', ''); ?></td>
                    <td>
                        <a href="editar.php?id=<?php echo (int)$estudiante['id']; ?>">Editar</a>
                        <a href="eliminar.php?id=<?php echo (int)$estudiante['id']; ?>">Eliminar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</body>
</html>
