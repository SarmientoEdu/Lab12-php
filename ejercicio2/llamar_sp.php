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
$nombre_insertar = $datos['nombre'];
$nota_insertar = (float)$datos['nota'];
$nota_formateada = number_format($nota_insertar, 2, '.', '');

// 1. Llamar al procedimiento sp_insertar_estudiante con sentencia preparada
// Se utiliza sentencia preparada para mantener consistencia de seguridad y evitar cualquier riesgo de inyección SQL.
$stmt_insert = $conn->prepare("CALL sp_insertar_estudiante(?, ?)");
$stmt_insert->bind_param('sd', $nombre_insertar, $nota_insertar);
$stmt_insert->execute();
$stmt_insert->close();

// Limpiar posibles conjuntos de resultados pendientes antes del siguiente CALL
while ($conn->more_results() && $conn->next_result()) {
    if ($res = $conn->store_result()) {
        $res->free();
    }
}

// 2. Llamar a sp_reporte_aprobados con $nota_minima
$stmt_reporte = $conn->prepare("CALL sp_reporte_aprobados(?)");
$stmt_reporte->bind_param('d', $nota_minima);
$stmt_reporte->execute();
$resultado = $stmt_reporte->get_result();

$filas = [];
if ($resultado) {
    while ($fila = $resultado->fetch_assoc()) {
        $filas[] = $fila;
    }
    $resultado->free();
}
$stmt_reporte->close();

while ($conn->more_results() && $conn->next_result()) {
    if ($res = $conn->store_result()) {
        $res->free();
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
        body { font-family: Arial, sans-serif; margin: 20px; }
        .tabla-reporte { border-collapse: collapse; width: 100%; margin-top: 15px; }
        .tabla-reporte th, .tabla-reporte td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        .tabla-reporte th { background-color: #4a90d9; color: white; }
        .fila-aprobado { background-color: #d4edda; color: #155724; }
        .fila-minimo { background-color: #fff3cd; color: #856404; }
    </style>
</head>
<body>
    <p>Procedimiento ejecutado: sp_insertar_estudiante('<?php echo htmlspecialchars($nombre_insertar); ?>', <?php echo $nota_formateada; ?>)</p>

    <table class="tabla-reporte">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Nota</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($filas) > 0): ?>
                <?php foreach ($filas as $fila): ?>
                    <?php
                        $nota_actual = (float)$fila['nota'];
                        $clase_fila = ($nota_actual >= 70) ? 'fila-aprobado' : 'fila-minimo';
                    ?>
                    <tr class="<?php echo $clase_fila; ?>">
                        <td><?php echo htmlspecialchars($fila['id']); ?></td>
                        <td><?php echo htmlspecialchars($fila['nombre']); ?></td>
                        <td><?php echo number_format($nota_actual, 2, '.', ''); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3">No hay estudiantes con nota mayor o igual a <?php echo number_format($nota_minima, 2, '.', ''); ?>.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>