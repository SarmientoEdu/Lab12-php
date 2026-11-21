<?php
/**
 * Lab 12 - Ejercicio 2: Llamar procedimientos almacenados desde PHP
 */
require_once '../ejercicio1/config_db.php';
require_once '../ejercicio1/config_semilla.php';

$conn = conectar();
$datos = generar_datos(SEMILLA);

$nota_minima = 60.00;

// 1. Llamar sp_insertar_estudiante con sentencia preparada
$stmt = $conn->prepare("CALL sp_insertar_estudiante(?, ?)");
$stmt->bind_param('sd', $datos['nombre'], $datos['nota']);
$stmt->execute();
$stmt->close();

// 2. Mensaje de confirmación con el formato exacto
$mensaje = "Procedimiento ejecutado: sp_insertar_estudiante('"
         . $datos['nombre'] . "', " . number_format($datos['nota'], 2) . ")";

// 3. Llamar sp_reporte_aprobados con nota mínima 60.00
$stmt = $conn->prepare("CALL sp_reporte_aprobados(?)");
$stmt->bind_param('d', $nota_minima);
$stmt->execute();
$resultado = $stmt->get_result();
$filas = $resultado->fetch_all(MYSQLI_ASSOC);
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
        .fila-minimo { background-color: #fff3cd; }
    </style>
</head>
<body>
    <p><?php echo htmlspecialchars($mensaje); ?></p>

    <table class="tabla-reporte">
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Nota</th>
        </tr>
        <?php foreach ($filas as $fila): ?>
            <?php $clase = ((float)$fila['nota'] >= 70) ? 'fila-aprobado' : 'fila-minimo'; ?>
            <tr class="<?php echo $clase; ?>">
                <td><?php echo $fila['id']; ?></td>
                <td><?php echo htmlspecialchars($fila['nombre']); ?></td>
                <td><?php echo number_format((float)$fila['nota'], 2); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>