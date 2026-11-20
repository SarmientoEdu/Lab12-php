<?php
/**
 * Lab 12 - Ejercicio 2: Llamar procedimientos almacenados desde PHP
 */
require_once '../ejercicio1/config_db.php';
require_once '../ejercicio1/config_semilla.php';

$conn = conectar();
$datos = generar_datos(SEMILLA);
$nota_minima = 60.00;

// 1. Llamar sp_insertar_estudiante
$stmt = $conn->prepare("CALL sp_insertar_estudiante(?, ?)");
$stmt->bind_param("sd", $datos['nombre'], $datos['nota']);
$stmt->execute();
$stmt->close();

echo "<h2>Procedimiento ejecutado: sp_insertar_estudiante('{$datos['nombre']}', {$datos['nota']})</h2>";

// 2. Llamar sp_reporte_aprobados
$resultado = $conn->query("CALL sp_reporte_aprobados($nota_minima)");
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
.fila-aprobado { background-color: #d4edda; } /* Verde */
.fila-minimo { background-color: #fff3cd; }  /* Amarillo */
</style>
</head>
<body>

<table class="tabla-reporte">
    <tr><th>ID</th><th>Nombre</th><th>Nota</th></tr>
    <?php while ($fila = $resultado->fetch_assoc()): 
        $clase = $fila['nota'] >= 70 ? 'fila-aprobado' : 'fila-minimo'; ?>
        <tr class="<?= $clase ?>">
            <td><?= $fila['id'] ?></td>
            <td><?= $fila['nombre'] ?></td>
            <td><?= $fila['nota'] ?></td>
        </tr>
    <?php endwhile; ?>
</table>

</body>
</html>
<?php $conn->close(); ?>