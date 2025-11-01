<?php
namespace Modelo;
use Modelo\Conexion;
use Exception;
use PDO;

class DatabaseReset extends Conexion
{
        public function __construct()
        {
                global $_ENV;
                parent::__construct(
                        $_ENV["_DB_HOST_"],
                        $_ENV["_DB_NAME_"],
                        $_ENV["_DB_USER_"],
                        $_ENV["_DB_PASS_"],
                );
        }

        /**
         * Reinicia la base de datos usando el enfoque de drop tables + import completo
         * @return bool|string True si fue exitoso, mensaje de error si falló
         */
        public function resetDatabase()
        {
                try {
                        $this->conectarBD();

                        $this->logSecurityInfo();

                        $sqlFile = __DIR__ . "/../savyc_testing.sql";
                        if (!file_exists($sqlFile)) {
                                throw new Exception(
                                        "El archivo savyc_testing.sql no existe",
                                );
                        }

                        $this->conex->exec("SET FOREIGN_KEY_CHECKS = 0");
                        $this->conex->exec(
                                "SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO'",
                        );

                        $this->dropAllTables();

                        $importResult = $this->importSqlFile($sqlFile);

                        if ($importResult !== true) {
                                $tablas = $this->conex
                                        ->query("SHOW TABLES")
                                        ->fetchAll(PDO::FETCH_COLUMN);

                                if (count($tablas) <= 0) {
                                        throw new Exception(
                                                "Error crítico durante la importación: " .
                                                        $importResult,
                                        );
                                }
                        }

                        $this->conex->exec("SET FOREIGN_KEY_CHECKS = 1");

                        $this->desconectarBD();

                        return true;
                } catch (Exception $e) {
                        // Limpiar conexión en caso de error
                        if (isset($this->conex)) {
                                try {
                                        $this->conex->exec(
                                                "SET FOREIGN_KEY_CHECKS = 1",
                                        );
                                } catch (Exception $cleanupException) {
                                        error_log(
                                                "DatabaseReset: Error en cleanup: " .
                                                        $cleanupException->getMessage(),
                                        );
                                }
                                $this->desconectarBD();
                        }

                        $errorMsg =
                                "Error al reiniciar la base de datos: " .
                                $e->getMessage();
                        return $errorMsg;
                }
        }

        /**
         * Registra información crítica de seguridad sobre la operación
         */
        private function logSecurityInfo()
        {
                global $_ENV;

                $currentDb = $this->getCurrentDatabase();
                error_log("DatabaseReset: INFORMACIÓN DE SEGURIDAD");
                error_log(
                        "DatabaseReset: Entorno: " .
                                ($_ENV["ENTORNO"] ?? "NO_SET"),
                );
                error_log(
                        "DatabaseReset: Base de datos objetivo: " . $currentDb,
                );
                error_log(
                        "DatabaseReset: Usuario DB: " .
                                ($_ENV["_DB_USER_"] ?? "NO_SET"),
                );
                error_log(
                        "DatabaseReset: Host DB: " .
                                ($_ENV["_DB_HOST_"] ?? "NO_SET"),
                );
                error_log(
                        "DatabaseReset: Usuario sistema: " .
                                ($_SESSION["user"] ?? "NO_SESSION"),
                );

                if (
                        $currentDb === "seguridadv2" ||
                        strpos($currentDb, "seguridad") !== false
                ) {
                        throw new Exception(
                                "OPERACIÓN BLOQUEADA: No se puede resetear base de datos de seguridad",
                        );
                }

                if (
                        !isset($_ENV["ENTORNO"]) ||
                        $_ENV["ENTORNO"] !== "TESTING"
                ) {
                        throw new Exception(
                                "OPERACIÓN BLOQUEADA: Solo permitido en entorno TESTING",
                        );
                }
        }

        /**
         * Obtiene el nombre de la base de datos actual
         * @return string
         */
        private function getCurrentDatabase()
        {
                try {
                        $stmt = $this->conex->query("SELECT DATABASE()");
                        return $stmt->fetchColumn();
                } catch (Exception $e) {
                        error_log(
                                "DatabaseReset: Error obteniendo nombre DB: " .
                                        $e->getMessage(),
                        );
                        return "unknown";
                }
        }

        /**
         * Elimina todas las tablas de la base de datos
         */
        private function dropAllTables()
        {
                error_log(
                        "DatabaseReset: Iniciando eliminación de todas las tablas",
                );

                $tablas = $this->conex
                        ->query("SHOW TABLES")
                        ->fetchAll(PDO::FETCH_COLUMN);

                error_log(
                        "DatabaseReset: Se encontraron " .
                                count($tablas) .
                                " tablas para eliminar",
                );

                if (empty($tablas)) {
                        error_log(
                                "DatabaseReset: No se encontraron tablas para eliminar",
                        );
                        return;
                }

                $vistas = $this->conex
                        ->query(
                                "
                        SELECT table_name
                        FROM information_schema.views
                        WHERE table_schema = DATABASE()
                ",
                        )
                        ->fetchAll(PDO::FETCH_COLUMN);

                foreach ($vistas as $vista) {
                        try {
                                $this->conex->exec(
                                        "DROP VIEW IF EXISTS `$vista`",
                                );
                                error_log(
                                        "DatabaseReset: Vista eliminada: $vista",
                                );
                        } catch (Exception $e) {
                                error_log(
                                        "DatabaseReset: Error eliminando vista $vista: " .
                                                $e->getMessage(),
                                );
                        }
                }

                foreach ($tablas as $tabla) {
                        try {
                                $this->conex->exec(
                                        "DROP TABLE IF EXISTS `$tabla`",
                                );
                                error_log(
                                        "DatabaseReset: Tabla eliminada: $tabla",
                                );
                        } catch (Exception $e) {
                                error_log(
                                        "DatabaseReset: Error eliminando tabla $tabla: " .
                                                $e->getMessage(),
                                );
                                try {
                                        $this->conex->exec(
                                                "DROP TABLE IF EXISTS `$tabla` CASCADE",
                                        );
                                } catch (Exception $e2) {
                                        error_log(
                                                "DatabaseReset: Error eliminando tabla $tabla con CASCADE: " .
                                                        $e2->getMessage(),
                                        );
                                }
                        }
                }

                error_log("DatabaseReset: Eliminación de tablas completada");
        }

        /**
         * Importa el archivo SQL completo usando MySQL command line
         * @param string $sqlFile
         * @return bool Success status
         */
        private function importSqlFile($sqlFile)
        {
                global $_ENV;

                $mysqlResult = $this->importWithMysqlCommand($sqlFile);
                if ($mysqlResult === true) {
                        return true;
                }

                return $this->importWithPDO($sqlFile);
        }

        /**
         * Importa usando mysql command line
         * @param string $sqlFile
         * @return bool|string Success (true) o error message (string)
         */
        private function importWithMysqlCommand($sqlFile)
        {
                global $_ENV;

                $host = $_ENV["_DB_HOST_"];
                $database = $_ENV["_DB_NAME_"];
                $username = $_ENV["_DB_USER_"];
                $password = $_ENV["_DB_PASS_"];

                $sqlFile = escapeshellarg($sqlFile);
                $database = escapeshellarg($database);
                $username = escapeshellarg($username);

                $mysqlPath = $this->findMysqlPath();

                $mysqlCmd = $mysqlPath ? "\"$mysqlPath\"" : "mysql";

                if (!empty($password)) {
                        $command =
                                "$mysqlCmd -h$host -u$username -p" .
                                escapeshellarg($password) .
                                " $database < $sqlFile 2>&1";
                } else {
                        $command = "$mysqlCmd -h$host -u$username $database < $sqlFile 2>&1";
                }

                $output = [];
                $returnCode = 0;

                exec($command, $output, $returnCode);

                if ($returnCode === 0) {
                        return true;
                } else {
                        $errorOutput = implode("\n", $output);

                        // Verificar si MySQL no está disponible
                        if (
                                stripos(
                                        $errorOutput,
                                        "not recognized as an internal or external command",
                                ) !== false ||
                                stripos($errorOutput, "command not found") !==
                                        false
                        ) {
                                return "MySQL command not available";
                        }

                        if (
                                $returnCode <= 2 &&
                                stripos($errorOutput, "warning") !== false
                        ) {
                                return true;
                        }

                        return "MySQL command error (código $returnCode): " .
                                $errorOutput;
                }
        }

        /**
         * Importa usando PDO como fallback
         * @param string $sqlFile
         * @return bool|string Success (true) or error message (string)
         */
        private function importWithPDO($sqlFile)
        {
                $sqlContent = file_get_contents($sqlFile);
                if ($sqlContent === false) {
                        throw new Exception("No se pudo leer el archivo SQL");
                }

                error_log(
                        "DatabaseReset: Procesando archivo SQL de " .
                                strlen($sqlContent) .
                                " caracteres con método PDO",
                );

                // Para archivos grandes, usar multi_query si está disponible
                if (strlen($sqlContent) > 1000000) {
                        return $this->importLargeSqlFile($sqlContent);
                }

                // Limpiar contenido
                $sqlContent = $this->cleanSqlContent($sqlContent);

                // Dividir en statements usando un método más robusto
                $statements = $this->splitSqlStatements($sqlContent);

                // Filtrar statements vacíos y problemáticos
                $statements = array_filter($statements, function ($stmt) {
                        $stmt = trim($stmt);
                        return !empty($stmt) &&
                                !preg_match(
                                        "/^(DELIMITER|--|\#|\/\*)/i",
                                        $stmt,
                                );
                });

                error_log(
                        "DatabaseReset: Se procesarán " .
                                count($statements) .
                                " statements válidos",
                );

                $executedCount = 0;
                $errorCount = 0;
                $criticalErrors = 0;

                // No usar transacciones para DDL statements
                $transactionStarted = false;

                try {
                        foreach ($statements as $i => $statement) {
                                $statement = trim($statement);
                                if (empty($statement)) {
                                        continue;
                                }

                                try {
                                        $this->conex->exec($statement);
                                        $executedCount++;

                                        if ($executedCount % 25 === 0) {
                                                error_log(
                                                        "DatabaseReset: Ejecutados $executedCount statements...",
                                                );
                                        }
                                } catch (Exception $e) {
                                        $errorCount++;
                                        $errorMsg = $e->getMessage();

                                        // Clasificar el tipo de error más específicamente
                                        $isWarning = $this->isNonCriticalError(
                                                $errorMsg,
                                        );

                                        if (!$isWarning) {
                                                $criticalErrors++;
                                        }

                                        $logLevel = $isWarning
                                                ? "WARNING"
                                                : "ERROR";

                                        error_log(
                                                "DatabaseReset: $logLevel en statement #$i: " .
                                                        $errorMsg .
                                                        " | Statement: " .
                                                        substr(
                                                                $statement,
                                                                0,
                                                                150,
                                                        ),
                                        );

                                        // Solo fallar si hay demasiados errores críticos
                                        if ($criticalErrors > 5) {
                                                $this->conex->rollBack();
                                                return "Demasiados errores críticos ($criticalErrors) durante importación PDO: último error " .
                                                        $errorMsg;
                                        }
                                }
                        }

                        // Solo hacer commit si hay transacción activa
                        if (
                                $transactionStarted &&
                                $this->conex->inTransaction()
                        ) {
                                $this->conex->commit();
                        }

                        error_log(
                                "DatabaseReset: Importación PDO completada. Ejecutados: $executedCount, Errores totales: $errorCount, Críticos: $criticalErrors",
                        );

                        if ($criticalErrors > 3) {
                                return "Importación PDO completada con $criticalErrors errores críticos";
                        } elseif ($errorCount > 0) {
                                return "Importación PDO completada con $errorCount advertencias (no críticas)";
                        } else {
                                return true;
                        }
                } catch (Exception $e) {
                        // Solo hacer rollback si hay transacción activa
                        if (
                                $transactionStarted &&
                                $this->conex->inTransaction()
                        ) {
                                $this->conex->rollBack();
                        }
                        return "Error fatal durante importación PDO: " .
                                $e->getMessage();
                }
        }

        /**
         * Limpia el contenido SQL removiendo comentarios y líneas problemáticas
         * @param string $sql
         * @return string
         */
        private function cleanSqlContent($sql)
        {
                // Remover comentarios de línea
                $sql = preg_replace('/^--.*$/m', "", $sql);
                $sql = preg_replace('/^#.*$/m', "", $sql);

                // Remover comentarios multilínea pero preservar MySQL directives
                $sql = preg_replace("/\/\*(?!\!\d+).*?\*\//s", "", $sql);

                // Remover líneas vacías múltiples
                $sql = preg_replace('/\n\s*\n/', "\n", $sql);

                return $sql;
        }

        /**
         * Divide el SQL en statements de manera más robusta
         * @param string $sql
         * @return array
         */
        private function splitSqlStatements($sql)
        {
                $statements = [];
                $currentStatement = "";
                $inDelimiterBlock = false;
                $currentDelimiter = ";";

                $lines = explode("\n", $sql);

                foreach ($lines as $line) {
                        $line = trim($line);

                        if (empty($line)) {
                                continue;
                        }

                        // Detectar cambios de DELIMITER
                        if (
                                preg_match(
                                        '/^DELIMITER\s+(.+)$/i',
                                        $line,
                                        $matches,
                                )
                        ) {
                                // Guardar statement actual si existe
                                if (!empty(trim($currentStatement))) {
                                        $statements[] = trim($currentStatement);
                                        $currentStatement = "";
                                }
                                $currentDelimiter = trim($matches[1]);
                                $inDelimiterBlock = $currentDelimiter !== ";";
                                continue;
                        }

                        // Agregar línea al statement actual
                        $currentStatement .= $line . "\n";

                        // Verificar si el statement termina
                        if (
                                substr(
                                        rtrim($line),
                                        -strlen($currentDelimiter),
                                ) === $currentDelimiter
                        ) {
                                // Remover delimiter del final
                                $currentStatement = substr(
                                        trim($currentStatement),
                                        0,
                                        -strlen($currentDelimiter),
                                );

                                if (!empty(trim($currentStatement))) {
                                        $statements[] = trim($currentStatement);
                                }

                                $currentStatement = "";
                        }
                }

                // Agregar statement final si existe
                if (!empty(trim($currentStatement))) {
                        $statements[] = trim($currentStatement);
                }

                return array_filter($statements, function ($stmt) {
                        return !empty(trim($stmt));
                });
        }

        /**
         * Importa archivos SQL grandes usando un método más eficiente
         * @param string $sqlContent
         * @return bool|string
         */
        private function importLargeSqlFile($sqlContent)
        {
                // Dividir en chunks más pequeños
                $chunks = str_split($sqlContent, 100000); // 100KB chunks
                $totalChunks = count($chunks);

                for ($i = 0; $i < $totalChunks; $i++) {
                        $chunk = $chunks[$i];

                        // Encontrar el último punto y coma completo en el chunk
                        if ($i < $totalChunks - 1) {
                                $lastSemicolon = strrpos($chunk, ";");
                                if ($lastSemicolon !== false) {
                                        $chunks[$i] = substr(
                                                $chunk,
                                                0,
                                                $lastSemicolon + 1,
                                        );
                                        $chunks[$i + 1] =
                                                substr(
                                                        $chunk,
                                                        $lastSemicolon + 1,
                                                ) . $chunks[$i + 1];
                                }
                        }

                        $result = $this->importWithPDO($chunks[$i]);
                        if ($result !== true && !is_string($result)) {
                                return "Error en chunk " .
                                        ($i + 1) .
                                        ": " .
                                        $result;
                        }
                }

                return true;
        }

        /**
         * Determina si un error es no crítico
         * @param string $errorMsg
         * @return bool
         */
        private function isNonCriticalError($errorMsg)
        {
                $nonCriticalPatterns = [
                        "already exists",
                        "duplicate entry",
                        "duplicate key",
                        "table.*already exists",
                        "procedure.*already exists",
                        "trigger.*already exists",
                        "view.*already exists",
                        "event.*already exists",
                        "function.*already exists",
                        "index.*already exists",
                        "constraint.*already exists",
                        "warning",
                        "note:",
                ];

                foreach ($nonCriticalPatterns as $pattern) {
                        if (preg_match("/$pattern/i", $errorMsg)) {
                                return true;
                        }
                }

                return false;
        }

        /**
         * Encuentra la ruta de MySQL en sistemas XAMPP Windows
         * @return string|null
         */
        private function findMysqlPath()
        {
                // Rutas comunes de MySQL en XAMPP Windows
                $possiblePaths = [
                        'C:\xampp\mysql\bin\mysql.exe',
                        'C:\XAMPP\mysql\bin\mysql.exe',
                        'D:\xampp\mysql\bin\mysql.exe',
                        'D:\XAMPP\mysql\bin\mysql.exe',
                        // Detectar automáticamente basado en la ruta actual
                        dirname(dirname(__DIR__)) . "\mysql\bin\mysql.exe",
                ];

                foreach ($possiblePaths as $path) {
                        if (file_exists($path)) {
                                return $path;
                        }
                }

                // Intentar detectar desde variables de entorno
                $pathEnv = getenv("PATH");
                if ($pathEnv) {
                        $paths = explode(";", $pathEnv);
                        foreach ($paths as $path) {
                                $mysqlExe =
                                        rtrim($path, "\\/") .
                                        DIRECTORY_SEPARATOR .
                                        "mysql.exe";
                                if (file_exists($mysqlExe)) {
                                        error_log(
                                                "DatabaseReset: MySQL encontrado en PATH: $mysqlExe",
                                        );
                                        return $mysqlExe;
                                }
                        }
                }

                return null;
        }

        /**
         * Verifica si el archivo savyc_testing.sql existe y es legible
         * @return bool
         */
        public function verifySqlFile()
        {
                $sqlFile = __DIR__ . "/../savyc_testing.sql";
                return file_exists($sqlFile) && is_readable($sqlFile);
        }
}
