<?php

/**
 * Functional Tests Configuration
 *
 * Configuration settings for Selenium WebDriver functional tests.
 * Functional tests only need web interface access - no database connection required.
 */

return [
        // ajustes de aplicacion
        "base_url" => $_ENV["APP_BASE_URL"] ?? "http://localhost/sistema-act",

        // selenium
        "selenium_hub_url" =>
                $_ENV["SELENIUM_HUB_URL"] ?? "http://localhost:4444",
        "browser" => $_ENV["BROWSER"] ?? "chrome", // chrome, firefox
        "headless" => $_ENV["HEADLESS"] ?? false, // abrir navegador en modo headless, sin interfaz

        // timeouts
        "default_timeout" => 10, // segundos
        "page_load_timeout" => 30, // segundos

        // credenciales de prueba (deben existir en el sistema) (precondicion, basicamente)
        "test_users" => [
                "admin" => [
                        "username" => "admin",
                        "password" => "admin123!",
                        "role" => "admin",
                ],
                "user" => [
                        "username" => "user",
                        "password" => "user123!",
                        "role" => "vendedor",
                ],
        ],

        // rutas disponibles
        "routes" => [
                "login" => "login",
                "inicio" => "inicio",
                "usuarios" => "usuarios",
                "productos" => "productos",
                "compras" => "compras",
                "proveedores" => "proveedores",
                "clientes" => "clientes",
                "venta" => "venta",
                "finanzas" => "finanzas",
                "backup" => "backup",
                "logout" => "cerrarsesion",
        ],

        // permitir capturas de pantalla, capturas de pantalla en caso de fallo
        "screenshots_enabled" => $_ENV["SCREENSHOTS_ENABLED"] ?? true,
        "screenshot_on_failure" => $_ENV["SCREENSHOT_ON_FAILURE"] ?? true,
];
