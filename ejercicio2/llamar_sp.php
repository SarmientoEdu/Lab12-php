<?php
require_once __DIR__ . '/../ejercicio1/config_db.php';
require_once __DIR__ . '/../ejercicio1/config_semilla.php';

$conn = conectar();
$datos = generar_datos(SEMILLA);
$nombre = $datos['nombre'];
$nota = $datos['nota'];
$minima = 60.00;
$stmt = $conn->prepare('CALL sp_insertar_estudiante(?, ?)');
$stmt->bind_param('sd', $nombre, $nota);
$stmt->execute();
$stmt->close();
while ($conn->more_results() && $conn->next_result()) {
    if ($extra = $conn->store_result()) $extra->free();
}
$stmt = $conn->prepare('CALL sp_reporte_aprobados(?)');
$stmt->bind_param('d', $minima);
$stmt->execute();
$filas = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
while ($conn->more_results() && $conn->next_result()) {
    if ($extra = $conn->store_result()) $extra->free();
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="es"><head><meta charset="UTF-8"><title>Reporte de aprobados</title>
<style>
body{font-family:Arial,sans-serif;background:#f2f5f8;color:#172d43;margin:48px}
main{background:white;border:1px solid #ced9e3;border-radius:8px;padding:32px}
.tabla-reporte{border-collapse:collapse;width:100%}.tabla-reporte th,.tabla-reporte td{border:1px solid #bccbd5;padding:10px;text-align:left}
.tabla-reporte th{background:#245e80;color:white}.fila-aprobado{background:#e2f4e8}.fila-minimo{background:#fff2c9}
</style></head><body><main><p>LABORATORIO 12 · EJERCICIO 2</p><h1>Reporte de estudiantes aprobados</h1>
<p>Alejandro Sáenz · Semilla <?= SEMILLA ?></p>
<p>Procedimiento ejecutado: sp_insertar_estudiante('<?= htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') ?>', <?= number_format($nota, 2, '.', '') ?>)</p>
<table class="tabla-reporte"><thead><tr><th>ID</th><th>Nombre</th><th>Nota</th></tr></thead><tbody>
<?php foreach ($filas as $fila): ?>
<tr class="<?= (float)$fila['nota'] >= 70 ? 'fila-aprobado' : 'fila-minimo' ?>"><td><?= (int)$fila['id'] ?></td><td><?= htmlspecialchars($fila['nombre'], ENT_QUOTES, 'UTF-8') ?></td><td><?= number_format((float)$fila['nota'], 2, '.', '') ?></td></tr>
<?php endforeach; ?></tbody></table></main></body></html>
