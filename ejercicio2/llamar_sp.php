<?php
/**
 * Lab 12 - Ejercicio 2: Llamar procedimientos almacenados desde PHP
 */
require_once __DIR__ . '/../ejercicio1/config_db.php';
require_once __DIR__ . '/../ejercicio1/config_semilla.php';

$conn = conectar();
$datos = generar_datos(SEMILLA);

$nota_minima = 60.00;
$mensaje = '';
$filas = [];
$error = '';

try {
    // Llamar al procedimiento sp_insertar_estudiante
    $stmtInsertar = $conn->prepare(
        "CALL sp_insertar_estudiante(?, ?)"
    );

    $stmtInsertar->bind_param(
        "sd",
        $datos['nombre'],
        $datos['nota']
    );

    $stmtInsertar->execute();
    $stmtInsertar->close();

    // Mensaje de confirmación
    $mensaje = "Procedimiento ejecutado: sp_insertar_estudiante('" .
        htmlspecialchars($datos['nombre'], ENT_QUOTES, 'UTF-8') .
        "', " . number_format((float)$datos['nota'], 2, '.', '') . ")";

    // Llamar al procedimiento sp_reporte_aprobados
    $stmtReporte = $conn->prepare(
        "CALL sp_reporte_aprobados(?)"
    );

    $stmtReporte->bind_param("d", $nota_minima);
    $stmtReporte->execute();

    $resultado = $stmtReporte->get_result();

    // Guardar los resultados para mostrarlos en la tabla
    while ($fila = $resultado->fetch_assoc()) {
        $filas[] = $fila;
    }

    $resultado->free();
    $stmtReporte->close();

} catch (Throwable $e) {
    $error = "Error al ejecutar los procedimientos: " . $e->getMessage();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Lab 12 - Ejercicio 2</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        .tabla-reporte {
            border-collapse: collapse;
            width: 100%;
        }

        .tabla-reporte th,
        .tabla-reporte td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }

        .tabla-reporte th {
            background-color: #4a90d9;
            color: white;
        }

        .fila-aprobado {
            background-color: #d4edda;
        }

        .fila-minimo {
            background-color: #fff3cd;
        }

        .mensaje {
            padding: 10px;
            margin-bottom: 15px;
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
        }

        .error {
            padding: 10px;
            margin-bottom: 15px;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
    </style>
</head>
<body>

    <h2>Lab 12 - Ejercicio 2: Procedimientos almacenados</h2>

    <?php if ($mensaje !== ''): ?>
        <p class="mensaje">
            <?= $mensaje ?>
        </p>
    <?php endif; ?>

    <?php if ($error !== ''): ?>
        <p class="error">
            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
        </p>
    <?php else: ?>

        <h3>Reporte de estudiantes aprobados</h3>
        <p>Nota mínima: <?= number_format($nota_minima, 2) ?></p>

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
                        $clase = ((float)$fila['nota'] >= 70)
                            ? 'fila-aprobado'
                            : 'fila-minimo';
                        ?>
                        <tr class="<?= $clase ?>">
                            <td><?= htmlspecialchars((string)$fila['id'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($fila['nombre'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= number_format((float)$fila['nota'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3">No hay estudiantes que cumplan con la nota mínima.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

    <?php endif; ?>

</body>
</html>

