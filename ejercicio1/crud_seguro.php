<?php
/**
 * Lab 12 - Ejercicio 1: CRUD completo con sentencias preparadas
 * Implementa las 4 funciones marcadas con TODO.
 * Formato de salida EXACTO requerido - no cambiar los strings de echo.
 */
require_once 'config_db.php';
require_once 'config_semilla.php';

$conn = conectar();
$datos = generar_datos(SEMILLA);

// ─── FUNCIÓN 1: Insertar ────────────────────────────────────────────────────
function insertar_estudiante(mysqli $conn, string $nombre, float $nota): int {
    // TODO: Usar prepare/bind_param/execute para INSERT
    $stmt = $conn->prepare("INSERT INTO estudiantes (nombre, nota) VALUES (?, ?)");
    $stmt->bind_param("sd", $nombre, $nota);
    $stmt->execute();
    $stmt->close();
    // TODO: Retornar $conn->insert_id (el ID generado)
    return $conn->insert_id;
}

// ─── FUNCIÓN 2: Buscar por nombre ───────────────────────────────────────────
function buscar_por_nombre(mysqli $conn, string $nombre): ?array {
    // TODO: Usar prepare/bind_param/execute/get_result para SELECT
    $stmt = $conn->prepare("SELECT id, nombre, nota FROM estudiantes WHERE nombre = ?");
    $stmt->bind_param("s", $nombre);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $fila = $resultado->fetch_assoc();
    $stmt->close();
    // TODO: Retornar la fila como array asociativo, o null si no existe
    return $fila ?: null;
}

// ─── FUNCIÓN 3: Actualizar nota ─────────────────────────────────────────────
function actualizar_nota(mysqli $conn, int $id, float $nueva_nota): void {
    // TODO: Usar prepare/bind_param/execute para UPDATE
    $stmt = $conn->prepare("UPDATE estudiantes SET nota = ? WHERE id = ?");
    $stmt->bind_param("di", $nueva_nota, $id);
    $stmt->execute();
    $stmt->close();
}

// ─── FUNCIÓN 4: Eliminar estudiante ─────────────────────────────────────────
function eliminar_estudiante(mysqli $conn, int $id): void {
    // TODO: Usar prepare/bind_param/execute para DELETE
    $stmt = $conn->prepare("DELETE FROM estudiantes WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

// ─── EJECUCIÓN (no modificar el formato de salida) ──────────────────────────
$id = insertar_estudiante($conn, $datos['nombre'], $datos['nota']);
echo "[INSERT] Estudiante '{$datos['nombre']}' con nota " . number_format($datos['nota'], 2) . " insertado. ID generado: $id\n";

$fila = buscar_por_nombre($conn, $datos['nombre']);
if ($fila) {
    echo "[SELECT] Encontrado: ID={$fila['id']} | Nombre={$fila['nombre']} | Nota=" . number_format($fila['nota'], 2) . "\n";
}

actualizar_nota($conn, $id, $datos['nueva_nota']);
echo "[UPDATE] Nota de ID=$id actualizada a " . number_format($datos['nueva_nota'], 2) . "\n";

eliminar_estudiante($conn, $id);
echo "[DELETE] Estudiante ID=$id eliminado\n";

$conn->close();