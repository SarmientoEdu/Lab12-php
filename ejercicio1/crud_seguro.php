<?php
require_once __DIR__ . '/config_db.php';
require_once __DIR__ . '/config_semilla.php';

function insertar_estudiante(mysqli $conn, string $nombre, float $nota): int {
    $stmt = $conn->prepare('INSERT INTO estudiantes (nombre, nota) VALUES (?, ?)');
    $stmt->bind_param('sd', $nombre, $nota);
    $stmt->execute();
    $id = $conn->insert_id;
    $stmt->close();
    return $id;
}

function buscar_por_nombre(mysqli $conn, string $nombre): ?array {
    // Si el nombre se repite, la demostración recupera el registro más reciente.
    $stmt = $conn->prepare('SELECT id, nombre, nota FROM estudiantes WHERE nombre = ? ORDER BY id DESC LIMIT 1');
    $stmt->bind_param('s', $nombre);
    $stmt->execute();
    $fila = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $fila;
}

function actualizar_nota(mysqli $conn, int $id, float $nueva_nota): void {
    $stmt = $conn->prepare('UPDATE estudiantes SET nota = ? WHERE id = ?');
    $stmt->bind_param('di', $nueva_nota, $id);
    $stmt->execute();
    $stmt->close();
}

function eliminar_estudiante(mysqli $conn, int $id): void {
    $stmt = $conn->prepare('DELETE FROM estudiantes WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();
}

// Incluir las funciones desde otra página no ejecuta la demostración.
if (realpath($_SERVER['SCRIPT_FILENAME'] ?? '') === __FILE__) {
    $conn = conectar();
    $datos = generar_datos(SEMILLA);
    $mensajes = [];
    $conn->begin_transaction();
    try {
        $id = insertar_estudiante($conn, $datos['nombre'], $datos['nota']);
        $mensajes[] = sprintf("[INSERT] Estudiante '%s' con nota %.2f insertado. ID generado: %d", $datos['nombre'], $datos['nota'], $id);
        $fila = buscar_por_nombre($conn, $datos['nombre']);
        if ($fila) {
            $mensajes[] = sprintf('[SELECT] Encontrado: ID=%d | Nombre=%s | Nota=%.2f', $fila['id'], $fila['nombre'], $fila['nota']);
        }
        actualizar_nota($conn, $id, $datos['nueva_nota']);
        $mensajes[] = sprintf('[UPDATE] Nota de ID=%d actualizada a %.2f', $id, $datos['nueva_nota']);
        eliminar_estudiante($conn, $id);
        $mensajes[] = sprintf('[DELETE] Estudiante ID=%d eliminado', $id);
        $conn->commit();
    } catch (Throwable $error) {
        $conn->rollback();
        throw $error;
    } finally {
        $conn->close();
    }
    $salida = implode("\n", $mensajes) . "\n";
    if (PHP_SAPI === 'cli') {
        echo $salida;
    } else {
        header('Content-Type: text/html; charset=UTF-8');
        ?>
<!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"><title>Lab 12 · CRUD seguro</title>
<style>body{font-family:Arial,sans-serif;background:#f2f5f8;color:#172d43;margin:48px}main{background:white;border:1px solid #ced9e3;border-radius:8px;padding:32px}h1{font-size:30px}pre{white-space:pre-wrap;line-height:2;font-size:16px;background:#edf5f2;padding:24px;border-left:5px solid #27795e}.datos{color:#415971}</style></head>
<body><main><p>LABORATORIO 12 · EJERCICIO 1</p><h1>CRUD con sentencias preparadas</h1>
<p class="datos">Alejandro Sáenz · Semilla <?= SEMILLA ?></p>
<pre><?= htmlspecialchars($salida, ENT_QUOTES, 'UTF-8') ?></pre>
<p>INSERT → SELECT → UPDATE → DELETE</p></main></body></html>
        <?php
    }
}
