<?php
/**
 * Lab 12 - Ejercicio 2: Llamar procedimientos almacenados desde PHP
 * Completa las secciones marcadas con TODO.
 */
require_once '../ejercicio1/config_db.php';
require_once '../ejercicio1/config_semilla.php';

$conn = conectar();
$datos = generar_datos(SEMILLA);

$nota_minima = 60.00;

// TODO: Llamar al procedimiento sp_insertar_estudiante
// con $datos['nombre'] y $datos['nota']
$stmtInsert = $conn->prepare("CALL sp_insertar_estudiante(?, ?)");
$stmtInsert->bind_param('sd', $datos['nombre'], $datos['nota']);
$stmtInsert->execute();
$stmtInsert->close();

while ($conn->more_results() && $conn->next_result()) {
    if ($resExtra = $conn->store_result()) {
        $resExtra->free();
    }
}

$notaFormateada = number_format((float)$datos['nota'], 2, '.', '');
$mensajeConfirmacion = "Procedimiento ejecutado: sp_insertar_estudiante('{$datos['nombre']}', {$notaFormateada})";

// TODO: Llamar a sp_reporte_aprobados con $nota_minima
$stmtReporte = $conn->prepare("CALL sp_reporte_aprobados(?)");
$stmtReporte->bind_param('d', $nota_minima);
$stmtReporte->execute();
$resultado = $stmtReporte->get_result();
$estudiantes = $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
$stmtReporte->close();

while ($conn->more_results() && $conn->next_result()) {
    if ($resExtra = $conn->store_result()) {
        $resExtra->free();
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
        /* TODO: Agregar estilos para .tabla-reporte, .fila-aprobado, .fila-minimo */
        body { font-family: Arial, sans-serif; margin: 20px; }
        .tabla-reporte { border-collapse: collapse; width: 100%; }
        .tabla-reporte th, .tabla-reporte td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        .tabla-reporte th { background-color: #4a90d9; color: white; }
        /* TODO: .fila-aprobado y .fila-minimo */
        .fila-aprobado { background-color: #e8f5e9; }
        .fila-minimo { background-color: #fff8e1; }
    </style>
</head>
<body>
    <!-- TODO: Mostrar mensaje de confirmación con el formato exacto -->
    <p><?= htmlspecialchars($mensajeConfirmacion) ?></p>

    <!-- TODO: Mostrar los resultados en una tabla HTML -->
    <table class="tabla-reporte">
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Nota</th>
        </tr>
        <?php foreach ($estudiantes as $fila): ?>
            <?php $claseFila = ((float)$fila['nota'] >= 70) ? 'fila-aprobado' : 'fila-minimo'; ?>
            <tr class="<?= $claseFila ?>">
                <td><?= (int)$fila['id'] ?></td>
                <td><?= htmlspecialchars($fila['nombre']) ?></td>
                <td><?= number_format((float)$fila['nota'], 2, '.', '') ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>