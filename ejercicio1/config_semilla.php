<?php
/**
 * Lab 12 - Configuración de semilla personal
 * Coloca aquí los últimos 3 dígitos de tu cédula.
 * Ejemplo: si tu cédula es 8-123-456, escribe 456.
 */

// TODO: Reemplaza 0 por los últimos 3 dígitos de tu cédula
define('SEMILLA', 565);

// Generador determinista de datos de prueba basado en la semilla
function generar_datos(int $semilla): array {
    $nombres = [
        'Ana Lopez', 'Carlos Medina', 'Maria Ramos', 'Jose Perez',
        'Laura Torres', 'Luis Garcia', 'Sofia Castro', 'Pedro Vargas',
        'Diana Ruiz', 'Miguel Flores'
    ];
    srand($semilla);
    $nombre     = $nombres[$semilla % count($nombres)];
    $nota       = round(50 + ($semilla % 50) + (($semilla * 7) % 100) / 100, 2);
    $nota       = min(100.00, max(0.00, $nota));
    $nueva_nota = round($nota + 10 > 100 ? $nota - 10 : $nota + 10, 2);
    return [
        'nombre'     => $nombre,
        'nota'       => $nota,
        'nueva_nota' => $nueva_nota,
    ];
}