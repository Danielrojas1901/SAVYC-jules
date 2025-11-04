<?php

namespace Tests\Functional;

use PHPUnit\Framework\TestCase;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverWait;
use Facebook\WebDriver\WebDriverExpectedCondition;

/**
 * Base class for Selenium functional tests
 *
 * Provides common functionality and utilities for testing web pages
 */
abstract class SeleniumTest extends TestCase
{
        protected RemoteWebDriver $driver;
        protected string $baseUrl;
        protected int $defaultTimeout = 10;

        protected function setUp(): void
        {
                parent::setUp();

                //cargar config
                $config = include __DIR__ . "/config.php";
                $this->baseUrl = $config["base_url"];

                // instanciar webdriver
                $this->driver = $this->createWebDriver($config);

                // configurar timeout implicito
                $this->driver
                        ->manage()
                        ->timeouts()
                        ->implicitlyWait($this->defaultTimeout);
        }

        protected function tearDown(): void
        {
                if (isset($this->driver)) {
                        $this->driver->quit();
                }
                parent::tearDown();
        }

        /**
         * configuracion de webdriver
         */
        private function createWebDriver(array $config): RemoteWebDriver
        {
                $capabilities = null;

                switch (strtolower($config["browser"])) {
                        case "chrome":
                                $capabilities = DesiredCapabilities::chrome();
                                $chromeOptions = [
                                        "args" => [
                                                "--no-sandbox",
                                                "--disable-dev-shm-usage",
                                                "--disable-gpu",
                                                "--window-size=1920,1080",
                                        ],
                                ];
                                if ($config["headless"]) {
                                        $chromeOptions["args"][] = "--headless";
                                }
                                $capabilities->setCapability(
                                        "goog:chromeOptions",
                                        $chromeOptions,
                                );
                                break;

                        case "firefox":
                                $capabilities = DesiredCapabilities::firefox();
                                if ($config["headless"]) {
                                        $capabilities->setCapability(
                                                "moz:firefoxOptions",
                                                [
                                                        "args" => ["-headless"],
                                                ],
                                        );
                                }
                                break;

                        default:
                                $capabilities = DesiredCapabilities::chrome();
                }

                return RemoteWebDriver::create(
                        $config["selenium_hub_url"],
                        $capabilities,
                );
        }

        /**
         * visitar una página relativa a la URL base
         */
        protected function visitPage(string $path = ""): void
        {
                $url = rtrim($this->baseUrl, "/") . "/" . ltrim($path, "/");
                $this->driver->get($url);
        }

        /**
         * Esperar a que un elemento esté presente y retornarlo
         */
        protected function waitForElement(
                WebDriverBy $locator,
                int $timeout = null,
        ): \Facebook\WebDriver\Remote\RemoteWebElement {
                $timeout = $timeout ?? $this->defaultTimeout;
                $wait = new WebDriverWait($this->driver, $timeout);

                return $wait->until(
                        WebDriverExpectedCondition::presenceOfElementLocated(
                                $locator,
                        ),
                );
        }

        /**
         * Esperar a que un elemento esté clickeable y retornarlo
         */
        protected function waitForClickableElement(
                WebDriverBy $locator,
                int $timeout = null,
        ): \Facebook\WebDriver\Remote\RemoteWebElement {
                $timeout = $timeout ?? $this->defaultTimeout;
                $wait = new WebDriverWait($this->driver, $timeout);

                return $wait->until(
                        WebDriverExpectedCondition::elementToBeClickable(
                                $locator,
                        ),
                );
        }

        /**
         * Esperar a que un texto específico aparezca en la página
         */
        protected function waitForText(string $text, int $timeout = null): bool
        {
                $timeout = $timeout ?? $this->defaultTimeout;
                $wait = new WebDriverWait($this->driver, $timeout);

                return $wait->until(function ($driver) use ($text) {
                        $pageSource = $driver->getPageSource();
                        return stripos($pageSource, $text) !== false;
                });
        }

        /**
         * Llenar un campo de formulario con texto
         */
        protected function fillField(WebDriverBy $locator, string $text): void
        {
                $element = $this->waitForElement($locator);
                $element->clear();
                $element->sendKeys($text);
        }

        /**
         * click en un elemento
         */
        protected function clickElement(WebDriverBy $locator): void
        {
                $element = $this->waitForClickableElement($locator);
                $element->click();
        }

        /**
         * tomar una captura de pantalla
         */
        protected function takeScreenshot(string $filename): void
        {
                $baseScreenshotDir = dirname(__DIR__, 2) . "/screenshots";

                $pathInfo = pathinfo($filename);
                $directory = $pathInfo["dirname"];
                $baseFilename = $pathInfo["filename"];
                $extension = $pathInfo["extension"] ?? "png";

                if ($directory && $directory !== ".") {
                        $fullDir = $baseScreenshotDir . "/" . $directory;
                } else {
                        $fullDir = $baseScreenshotDir;
                }

                if (!is_dir($fullDir)) {
                        mkdir($fullDir, 0755, true);
                }

                $timestamp = date("m.d.Y_His");

                // Build final filepath
                $finalFilename =
                        $baseFilename . "_" . $timestamp . "." . $extension;
                $filepath = $fullDir . "/" . $finalFilename;

                $this->driver->takeScreenshot($filepath);
        }

        /**
         * tomar una captura de pantalla con organización automática de carpetas por clase de prueba y método
         */
        protected function takeTestScreenshot(string $description = ""): void
        {
                // Get test class name
                $reflection = new \ReflectionClass($this);
                $testClass = $reflection->getShortName();

                // Get method name from backtrace
                $backtrace = debug_backtrace();
                $testMethod = $backtrace[1]["function"] ?? "Unknown";

                // Remove "test" prefix from method name for cleaner folder structure
                $cleanMethodName = preg_replace("/^test/", "", $testMethod);

                // Build path: Tests/ClassName/MethodName/Description
                $path = "Tests/" . $testClass . "/" . $cleanMethodName;
                if (!empty($description)) {
                        $path .= "/" . $description;
                }

                $this->takeScreenshot($path);
        }

        /**
         * asercion para verificar si la URL contiene una ruta específica
         */
        protected function assertUrlContains(string $expectedPath): void
        {
                $currentUrl = $this->driver->getCurrentURL();
                $this->assertStringContainsString($expectedPath, $currentUrl);
        }

        /**
         * asercion para verificar si un elemento está visible
         */
        protected function assertElementVisible(WebDriverBy $locator): void
        {
                $element = $this->driver->findElement($locator);
                $this->assertTrue(
                        $element->isDisplayed(),
                        "Element should be visible",
                );
        }

        /**
         * asercion para verificar si una página contiene texto específico
         */
        protected function assertPageContainsText(string $text): void
        {
                $pageSource = $this->driver->getPageSource();
                $this->assertStringContainsString($text, $pageSource);
        }

        /**
     * Espera hasta que no existan elementos que coincidan con $by o estén ocultos.
     * Devuelve true si desaparecen dentro del timeout, false en caso contrario.
     *
     * @param \Facebook\WebDriver\WebDriverBy $by
     * @param int $timeout segundos
     * @return bool
     */
    protected function waitForElementToDisappear(\Facebook\WebDriver\WebDriverBy $by, int $timeout = 5): bool
    {
        $end = time() + max(1, $timeout);
        while (time() <= $end) {
            try {
                $elements = $this->driver->findElements($by);
                if (count($elements) === 0) {
                    return true;
                }
                $allHidden = true;
                foreach ($elements as $el) {
                    try {
                        if ($el->isDisplayed()) {
                            $allHidden = false;
                            break;
                        }
                    } catch (\Throwable $_) {
                        // si el elemento desapareció entre la búsqueda y el isDisplayed, ignorar
                        continue;
                    }
                }
                if ($allHidden) {
                    return true;
                }
            } catch (\Throwable $_) {
                // si hay un error al buscar, asumir que desapareció
                return true;
            }
            usleep(200000); // 200ms
        }
        return false;
    }
}
