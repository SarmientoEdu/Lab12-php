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
$stmt = $conn->prepare("SELECT id, nombre, nota FROM estudiantes ORDER BY id");
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
     <table class="tabla-estudiantes">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Nota</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($estudiantes as $est): ?>
                <?php
                $nota = (float) $est['nota'];
                if ($nota >= 90) {
                    $clase_nota = 'nota-alta';
                } elseif ($nota >= 70) {
                    $clase_nota = 'nota-media';
                } else {
                    $clase_nota = 'nota-baja';
                }
                $id = (int) $est['id'];
                ?>
                <tr class="<?php echo $clase_nota; ?>">
                    <td><?php echo $id; ?></td>
                    <td><?php echo htmlspecialchars($est['nombre']); ?></td>
                    <td><?php echo number_format($nota, 2); ?></td>
                    <td>
                        <a href="editar.php?id=<?php echo $id; ?>">Editar</a> |
                        <a href="eliminar.php?id=<?php echo $id; ?>">Eliminar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>