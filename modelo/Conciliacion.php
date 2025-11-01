<?php
namespace Modelo;
use Exception;
use Smalot\PdfParser\Parser;


class Conciliacion
{
    public function leerPDF($rutaPdf)
    {
        if (!file_exists($rutaPdf)) {
            throw new Exception("El archivo PDF no existe.");
        }

        try {
            $parser = new Parser();
            $pdf = $parser->parseFile($rutaPdf);
            $text = $pdf->getText();
            
            error_log("=== contenido del PDF ===");
            error_log($text);
            error_log("=== fin contenido===");

            $lineas = array_values(array_filter(array_map('trim', explode("\n", $text)))); // quitar vacías
            $registros = [];
            $i = 0;
            
            // Saltar encabezados si existen
            while ($i < count($lineas) && !preg_match('/^\d{2}-\d{2}-\d{4}/', $lineas[$i])) {
                $i++;
            }

            while ($i < count($lineas)) {
                // Si encontramos una línea que comienza con fecha
                if (preg_match('/^(\d{2}-\d{2}-\d{4}\s*-\s*\d{2}:\d{2})(\d+)$/', $lineas[$i], $matches)) {
                    $fecha = $matches[1];
                    $referencia = $matches[2];
                    $descripcion = [];
                    $i++;

                    // Recolectar líneas de descripción hasta encontrar DEBITO o CREDITO
                    while ($i < count($lineas) && !preg_match('/^(DEBITO|CREDITO)\t/', $lineas[$i])) {
                        $descripcion[] = $lineas[$i];
                        $i++;
                    }

                    // Procesar la línea de débito/crédito
                    if ($i < count($lineas) && preg_match('/^(DEBITO|CREDITO)\t(-?\d+,\d{2})\t(\d+,\d{2})$/', $lineas[$i], $montoMatch)) {
                        $registros[] = [
                            'fecha' => trim($fecha),
                            'referencia' => $referencia,
                            'descripcion' => implode(' ', $descripcion),
                            'tipo' => $montoMatch[1],
                            'monto' => $montoMatch[2],
                            'saldo' => $montoMatch[3]
                        ];
                    }
                    $i++;
                    continue;
                }
                $i++;
            }

            error_log("=== registros encontrados ===");
            error_log(print_r($registros, true));
            error_log("=== fin registros encontrados ===");

            if (empty($registros)) {
                throw new Exception("No se encontraron registros válidos en el PDF.");
            }

            return $registros;
        } catch (Exception $e) {
            error_log("Error en leerPDF: " . $e->getMessage());
            throw $e;
        }
    }
}