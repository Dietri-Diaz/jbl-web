<?php

namespace App\Soporte;

use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Descarga de listados en CSV para abrirlos en Excel.
 * Uso: return Csv::descargar('productos.csv', ['Nombre', 'Precio'], $filas);
 */
class Csv
{
    /**
     * @param  array<int, string>  $encabezados  primera fila del archivo
     * @param  iterable<int, array>  $filas  una lista de valores por cada fila
     */
    public static function descargar(string $archivo, array $encabezados, iterable $filas): StreamedResponse
    {
        return response()->streamDownload(function () use ($encabezados, $filas) {
            $salida = fopen('php://output', 'w');

            // La marca BOM le dice a Excel que el archivo está en UTF-8 (tildes y ñ).
            fwrite($salida, "\xEF\xBB\xBF");

            // Excel en español separa las columnas con punto y coma.
            fputcsv($salida, $encabezados, ';');

            foreach ($filas as $fila) {
                fputcsv($salida, array_map([self::class, 'limpiar'], $fila), ';');
            }

            fclose($salida);
        }, $archivo, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /**
     * Un texto que empieza con = + - o @ se ejecutaría en Excel como fórmula.
     * Como hay datos que escribe el cliente (nombre, notas), se les antepone
     * un apóstrofo para que se lean como texto. Los números no se tocan.
     */
    private static function limpiar(mixed $valor): mixed
    {
        if (is_string($valor) && ! is_numeric($valor) && preg_match('/^[=+\-@]/', $valor)) {
            return "'".$valor;
        }

        return $valor;
    }
}
