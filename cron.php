<?php
require_once "modelo/backup.php";

$objBackup = new Backup();

// Obtener configuración actual
$config = $objBackup->getConfig();

$retencion = $config['retencion'] ?? 10;
$modo = $config['modo'] ?? 'ambos'; // 'automatico' o 'ambos'

try {
    $resultado = $objBackup->geteliminarRetencion($retencion, $modo);

    $mensaje = ($resultado == 1)
        ? "Exito [" . date("Y-m-d H:i:s") . "] Retención aplicada correctamente\n"
        : "Fallo [" . date("Y-m-d H:i:s") . "] No se eliminaron respaldos\n";

    // GUARDAR en un archivo log
    file_put_contents("log_retencion.txt", $mensaje, FILE_APPEND);
    
} catch (Exception $e) {
    $error = "Error [" . date("Y-m-d H:i:s") . "] Error: " . $e->getMessage() . "\n";
    file_put_contents("log_retencion.txt", $error, FILE_APPEND);
}
