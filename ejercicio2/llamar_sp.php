<?php
/**
 * Lab 12 - Ejercicio 2: Llamar procedimientos almacenados desde PHP
 */
require_once '../ejercicio1/config_db.php';
require_once '../ejercicio1/config_semilla.php';

$conn = conectar();
$datos = generar_datos(SEMILLA);

$nota_minima = 60.00;

// 1) Insertar con el procedimiento almacenado.
// Uso sentencia preparada (?) en vez de concatenar: aunque el valor venga de
// la semilla, la regla del lab es no concatenar variables en el SQL.
$nombre = $datos['nombre'];
$nota   = $datos['nota'];

$stmt = $conn->prepare("CALL sp_insertar_estudiante(?, ?)");
$stmt->bind_param("sd", $nombre, $nota);
$stmt->execute();
$stmt->close();

$mensaje = "Procedimiento ejecutado: sp_insertar_estudiante('$nombre', " . number_format($nota, 2) . ")";

// 2) Reporte de aprobados
$stmt = $conn->prepare("CALL sp_reporte_aprobados(?)");
$stmt->bind_param("d", $nota_minima);
$stmt->execute();
$filas = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Lab 12 - Ejercicio 2</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .tabla-reporte { border-collapse: collapse; width: 100%; }
        .tabla-reporte th, .tabla-reporte td { border: 1px solid #ccc; padding: 8px; }
        .tabla-reporte th { background-color: #4a90d9; color: white; }
        .fila-aprobado { background-color: #d4edda; }
        .fila-minimo   { background-color: #fff3cd; }
    </style>
</head>
<body>
    <p><?= htmlspecialchars($mensaje) ?></p>

    <table class="tabla-reporte">
        <tr><th>ID</th><th>Nombre</th><th>Nota</th></tr>
        <?php foreach ($filas as $f): ?>
        <tr class="<?= $f['nota'] >= 70 ? 'fila-aprobado' : 'fila-minimo' ?>">
            <td><?= $f['id'] ?></td>
            <td><?= htmlspecialchars($f['nombre']) ?></td>
            <td><?= number_format($f['nota'], 2) ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>