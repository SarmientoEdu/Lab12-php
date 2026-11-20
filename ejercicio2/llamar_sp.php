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
$salida_html = "";

// TODO: Llamar al procedimiento sp_insertar_estudiante con sentencia preparada
$stmt_insert = $conn->prepare("CALL sp_insertar_estudiante(?, ?)");
$stmt_insert->bind_param("sd", $datos['nombre'], $datos['nota']);
$stmt_insert->execute();
$stmt_insert->close();

// TODO: Mostrar mensaje de confirmación con el formato exacto
$salida_html .= "<p>Procedimiento ejecutado: sp_insertar_estudiante('" . htmlspecialchars($datos['nombre']) . "', " . number_format($datos['nota'], 2) . ")</p>";

// TODO: Llamar a sp_reporte_aprobados con $nota_minima (sentencia preparada)
$stmt_report = $conn->prepare("CALL sp_reporte_aprobados(?)");
$stmt_report->bind_param("d", $nota_minima);
$stmt_report->execute();
$resultado = $stmt_report->get_result();

// TODO: Mostrar los resultados en una tabla HTML
$salida_html .= "<table class='tabla-reporte'>";
$salida_html .= "<tr><th>ID</th><th>Nombre</th><th>Nota</th></tr>";

while ($fila = $resultado->fetch_assoc()) {
    // Clase "fila-aprobado" si nota >= 70, sino "fila-minimo"
    $clase_fila = ($fila['nota'] >= 70) ? "fila-aprobado" : "fila-minimo";
    
    $salida_html .= "<tr class='" . $clase_fila . "'>";
    $salida_html .= "<td>" . $fila['id'] . "</td>";
    $salida_html .= "<td>" . htmlspecialchars($fila['nombre']) . "</td>";
    $salida_html .= "<td>" . number_format($fila['nota'], 2) . "</td>";
    $salida_html .= "</tr>";
}

$salida_html .= "</table>";
$resultado->free();
$stmt_report->close();

$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Lab 12 - Ejercicio 2</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        
        /* TODO: Agregar estilos para .tabla-reporte, .fila-aprobado, .fila-minimo */
        .tabla-reporte { border-collapse: collapse; width: 100%; max-width: 600px; }
        .tabla-reporte th, .tabla-reporte td { border: 1px solid #ccc; padding: 8px; text-align: center; }
        .tabla-reporte th { background-color: #4a90d9; color: white; }
        
        .fila-aprobado { background-color: #d4edda; color: #155724; } /* Verde claro */
        .fila-minimo { background-color: #fff3cd; color: #856404; }   /* Amarillo claro */
    </style>
</head>
<body>
    <!-- El PHP de arriba genera la salida aquí -->
    <?php echo $salida_html; ?>
</body>
</html>