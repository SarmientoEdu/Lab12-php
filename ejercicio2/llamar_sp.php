<?php
/**
 * Lab 12 - Ejercicio 2: Llamar procedimientos almacenados desde PHP
 * Se usan sentencias preparadas también para el CALL.
 */
require_once '../ejercicio1/config_db.php';
require_once '../ejercicio1/config_semilla.php';

$conn = conectar();
$datos = generar_datos(SEMILLA);

$nota_minima = 60.00;

// 1) Llamar a sp_insertar_estudiante con los datos de la semilla
$nombre = $datos['nombre'];
$nota   = $datos['nota'];
$stmt = $conn->prepare("CALL sp_insertar_estudiante(?, ?)");
$stmt->bind_param('sd', $nombre, $nota);
$stmt->execute();
$stmt->close(); // cerrar antes de la siguiente llamada (evita "Commands out of sync")

$mensaje = "Procedimiento ejecutado: sp_insertar_estudiante('" . $nombre . "', " . number_format($nota, 2) . ")";

// 2) Llamar a sp_reporte_aprobados con la nota mínima
$stmt = $conn->prepare("CALL sp_reporte_aprobados(?)");
$stmt->bind_param('d', $nota_minima);
$stmt->execute();
$resultado = $stmt->get_result();
$filas = $resultado->fetch_all(MYSQLI_ASSOC);
$resultado->free();
$stmt->close();

// Consumir resultados extra que devuelve el CALL
while ($conn->more_results() && $conn->next_result()) {
    if ($extra = $conn->store_result()) {
        $extra->free();
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Lab 12 - Ejercicio 2</title>
    <style>
        body { font-family: system-ui, "Segoe UI", Arial, sans-serif; margin: 20px; color: #333; }
        .mensaje { background: #e8f4fd; border-left: 4px solid #4a90d9; padding: 10px 14px; margin-bottom: 16px; }
        .tabla-reporte { border-collapse: collapse; width: 100%; }
        .tabla-reporte th, .tabla-reporte td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        .tabla-reporte th { background-color: #4a90d9; color: white; }
        .fila-aprobado { background-color: #d4edda; }
        .fila-minimo   { background-color: #fff3cd; }
    </style>
</head>
<body>
    <h1>Ejercicio 2 – Procedimientos Almacenados</h1>

    <p class="mensaje"><?= htmlspecialchars($mensaje) ?></p>

    <h2>Reporte de aprobados (nota &ge; <?= number_format($nota_minima, 2) ?>)</h2>

    <table class="tabla-reporte">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Nota</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($filas as $fila): ?>
            <?php $clase = ($fila['nota'] >= 70) ? 'fila-aprobado' : 'fila-minimo'; ?>
            <tr class="<?= $clase ?>">
                <td><?= (int)$fila['id'] ?></td>
                <td><?= htmlspecialchars($fila['nombre']) ?></td>
                <td><?= number_format($fila['nota'], 2) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>