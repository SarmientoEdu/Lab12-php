<?php
require_once '../ejercicio1/config_db.php';
require_once '../ejercicio1/config_semilla.php';
require_once 'seed_data.php';
$conn = conectar();

$check = $conn->query("SELECT COUNT(*) as total FROM estudiantes");
$total = $check->fetch_assoc()['total'];
if ($total == 0) {
    sembrar_datos($conn);
}

$stmt = $conn->prepare("SELECT * FROM estudiantes ORDER BY id");
$stmt->execute();
$resultado = $stmt->get_result();
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
    <tr><th>ID</th><th>Nombre</th><th>Nota</th><th>Acciones</th></tr>
    <?php while ($fila = $resultado->fetch_assoc()):
        $nota = (float)$fila['nota'];
        $clase = $nota >= 90 ? 'nota-alta' : ($nota >= 70 ? 'nota-media' : 'nota-baja'); ?>
        <tr class="<?= $clase ?>">
            <td><?= $fila['id'] ?></td>
            <td><?= htmlspecialchars($fila['nombre']) ?></td>
            <td><?= number_format($nota, 2) ?></td>
            <td>
                <a href="editar.php?id=<?= $fila['id'] ?>">Editar</a> |
                <a href="eliminar.php?id=<?= $fila['id'] ?>">Eliminar</a>
            </td>
        </tr>
    <?php endwhile; ?>
</table>
</body>
</html>