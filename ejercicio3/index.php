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
$stmt = $conn->prepare("SELECT * FROM estudiantes ORDER BY id");
$stmt->execute();
$resultado = $stmt->get_result();
$estudiantes = $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
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
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Nota</th>
            <th>Acciones</th>
        </tr>
        <?php foreach ($estudiantes as $est): ?>
            <?php
            $notaVal = (float)$est['nota'];
            if ($notaVal >= 90) {
                $claseNota = 'nota-alta';
            } elseif ($notaVal >= 70) {
                $claseNota = 'nota-media';
            } else {
                $claseNota = 'nota-baja';
            }
            ?>
            <tr class="<?= $claseNota ?>">
                <td><?= (int)$est['id'] ?></td>
                <td><?= htmlspecialchars($est['nombre']) ?></td>
                <td><?= number_format($notaVal, 2, '.', '') ?></td>
                <td>
                    <a href="editar.php?id=<?= (int)$est['id'] ?>">Editar</a> |
                    <a href="eliminar.php?id=<?= (int)$est['id'] ?>">Eliminar</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

</body>
</html>