<?php

use Modelo\DatabaseReset;
use Modelo\Bitacora;

// Verificar que solo se ejecute en entorno de testing
if (!isset($_ENV["ENTORNO"]) || $_ENV["ENTORNO"] !== "TESTING") {
        http_response_code(403);
        echo json_encode([
                "success" => false,
                "message" =>
                        "Esta operación solo está disponible en entorno de testing",
        ]);
        exit();
}

// Verificar que el usuario tenga permisos de administrador
if (
        !isset($_SESSION["permisos"]["seguridad"]["eliminar"]) ||
        empty($_SESSION["permisos"]["seguridad"]["eliminar"])
) {
        http_response_code(403);
        echo json_encode([
                "success" => false,
                "message" => "No tienes permisos para realizar esta operación",
        ]);
        exit();
}

// Verificar que la sesión esté iniciada
if (!isset($_SESSION["cod_usuario"])) {
        http_response_code(401);
        echo json_encode([
                "success" => false,
                "message" => "Sesión no iniciada",
        ]);
        exit();
}

$objDatabaseReset = new DatabaseReset();
$objbitacora = new Bitacora();

// Procesar solicitud de reset de base de datos
if (isset($_POST["reset_database"]) && $_POST["reset_database"] === "confirm") {
        // Verificar que el archivo SQL existe
        if (!$objDatabaseReset->verifySqlFile()) {
                $response = [
                        "success" => false,
                        "message" =>
                                "El archivo savyc_testing.sql no existe o no es accesible",
                        "title" => "Error",
                        "icon" => "error",
                ];
        } else {
                // Intentar reset de base de datos
                $result = $objDatabaseReset->resetDatabase();

                if ($result === true) {
                        // Registrar en bitácora
                        $objbitacora->registrarEnBitacora(
                                $_SESSION["cod_usuario"],
                                "Reset de Base de Datos",
                                "Base de datos reiniciada desde savyc_testing.sql",
                                "Database Reset",
                        );

                        $response = [
                                "success" => true,
                                "message" =>
                                        "Base de datos reiniciada exitosamente desde savyc_testing.sql",
                                "title" => "Éxito",
                                "icon" => "success",
                        ];
                } else {
                        $response = [
                                "success" => false,
                                "message" => $result, // $result contiene el mensaje de error
                                "title" => "Error",
                                "icon" => "error",
                        ];
                }
        }

        // Enviar respuesta JSON
        header("Content-Type: application/json");
        echo json_encode($response);
        exit();
}

// Si no es una solicitud POST válida, redirigir
header("Location: inicio");
exit();
