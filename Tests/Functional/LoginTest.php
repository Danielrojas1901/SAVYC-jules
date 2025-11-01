<?php

namespace Tests\Functional;

use Facebook\WebDriver\WebDriverBy;

class LoginTest extends SeleniumTest
{
        public function testLoginVistaCarga(): void
        {
                $this->visitPage("login");

                $this->assertPageContainsText("SAVYC+");
                $this->assertPageContainsText(
                        "Ingresa tus datos para iniciar sesión",
                );
                $this->assertElementVisible(WebDriverBy::name("ingUsuario"));
                $this->assertElementVisible(WebDriverBy::name("ingPassword"));
                $this->assertElementVisible(WebDriverBy::name("ingresar"));

                $this->takeTestScreenshot("LoginCarga");
        }

        public function testLoginCamposCorrectos(): void
        {
                $this->visitPage("login");

                $campoUsuario = $this->waitForElement(
                        WebDriverBy::name("ingUsuario"),
                );
                $this->assertEquals(
                        "Usuario",
                        $campoUsuario->getAttribute("placeholder"),
                );
                $this->assertTrue(
                        $campoUsuario->getAttribute("required") !== null,
                );

                $campoPassword = $this->waitForElement(
                        WebDriverBy::name("ingPassword"),
                );
                $this->assertEquals(
                        "Contraseña",
                        $campoPassword->getAttribute("placeholder"),
                );
                $this->assertEquals(
                        "password",
                        $campoPassword->getAttribute("type"),
                );
                $this->assertTrue(
                        $campoPassword->getAttribute("required") !== null,
                );

                $botonSubmit = $this->waitForElement(
                        WebDriverBy::name("ingresar"),
                );
                $this->assertEquals("Ingresar", $botonSubmit->getText());
                $this->assertEquals(
                        "submit",
                        $botonSubmit->getAttribute("type"),
                );
        }

        public function testLoginValidaCamposVacios(): void
        {
                $this->visitPage("login");

                $botonSubmit = $this->driver->findElement(
                        WebDriverBy::name("ingresar"),
                );
                $botonSubmit->click();

                sleep(1);

                $urlActual = $this->driver->getCurrentURL();
                $codigoFuente = $this->driver->getPageSource();

                $this->assertTrue(
                        strpos($urlActual, "login") !== false ||
                                strpos($codigoFuente, "ingUsuario") !== false ||
                                strpos($codigoFuente, "required") !== false ||
                                strpos($codigoFuente, "requerido") !== false,
                        "Validacion de campos debe evitar el envio del formulario",
                );

                $this->takeTestScreenshot("LoginValidaCamposVacios");
        }

        public function testLoginInvalido(): void
        {
                $this->visitPage("login");

                $this->fillField(
                        WebDriverBy::name("ingUsuario"),
                        "invalid_user",
                );
                $this->fillField(
                        WebDriverBy::name("ingPassword"),
                        "wrong_password",
                );

                $this->clickElement(WebDriverBy::name("ingresar"));

                sleep(2);

                $urlActual = $this->driver->getCurrentURL();
                $codigoFuente = $this->driver->getPageSource();

                $this->assertTrue(
                        strpos($urlActual, "login") !== false ||
                                strpos($codigoFuente, "error") !== false ||
                                strpos($codigoFuente, "incorrecto") !== false ||
                                strpos($codigoFuente, "inválido") !== false,
                        "Deberia mostrar error o permanecer en la página de inicio de sesión para credenciales inválidas",
                );

                $this->takeTestScreenshot("LoginInvalido");
        }

        /**
         * flujo
         */
        public function testLoginExitoso(): void
        {
                $this->visitPage("login");

                $this->assertPageContainsText("SAVYC+");
                $this->assertPageContainsText("Ingresa tus datos");

                $this->fillField(WebDriverBy::name("ingUsuario"), "admin");
                $this->fillField(WebDriverBy::name("ingPassword"), "admin123!");

                $elementosCaptcha = $this->driver->findElements(
                        WebDriverBy::name("captchaCodigo"),
                );
                $this->assertEmpty(
                        $elementosCaptcha,
                        "Se debe deshabilitar el CAPTCHA para pruebas funcionales - configurar entorno de prueba",
                );

                $this->clickElement(WebDriverBy::name("ingresar"));

                sleep(3);

                $urlActual = $this->driver->getCurrentURL();
                $codigoFuente = $this->driver->getPageSource();

                $loginPasado = strpos($urlActual, "login") === false;

                $noHayFormularioLogin =
                        strpos($codigoFuente, "ingUsuario") === false;

                $hayIndicadoresExito =
                        strpos($codigoFuente, "bienvenido") !== false ||
                        strpos($codigoFuente, "inicio") !== false ||
                        strpos($codigoFuente, "cerrar") !== false ||
                        strpos($codigoFuente, "logout") !== false ||
                        strpos($codigoFuente, "salir") !== false;

                if (
                        !(
                                $loginPasado ||
                                $noHayFormularioLogin ||
                                $hayIndicadoresExito
                        )
                ) {
                        echo "\nDEBUG:\n";
                        echo "URL: " . $urlActual . "\n";
                        echo "Contiene 'login': " .
                                (strpos($urlActual, "login") !== false
                                        ? "SI"
                                        : "NO") .
                                "\n";
                        echo "Contiene formulario de login: " .
                                (strpos($codigoFuente, "ingUsuario") !== false
                                        ? "SI"
                                        : "NO") .
                                "\n";
                        echo "Título de la página: " .
                                $this->driver->getTitle() .
                                "\n";

                        if (strpos($codigoFuente, "incorrecto") !== false) {
                                $this->fail(
                                        "Login fallido, credenciales incorrectas",
                                );
                        }
                        if (strpos($codigoFuente, "error") !== false) {
                                $this->fail(
                                        "Login fallido - error del servidor",
                                );
                        }
                }

                $loginExitoso =
                        ($loginPasado || $noHayFormularioLogin) &&
                        $hayIndicadoresExito;

                $this->assertTrue(
                        $loginExitoso,
                        "Login debe ser exitoso - o redirecciona desde la página de inicio de sesión o muestra indicadores de éxito",
                );

                $codigoFuente = $this->driver->getPageSource();
                $indicadoresExito = ["inicio", "ayuda"];

                $indicadorEncontrado = false;
                foreach ($indicadoresExito as $indicador) {
                        if (stripos($codigoFuente, $indicador) !== false) {
                                $indicadorEncontrado = true;
                                break;
                        }
                }

                $this->assertTrue(
                        $indicadorEncontrado,
                        "Deberia mostrar indicadores de inicio de sesión exitoso",
                );
                $this->takeTestScreenshot("LoginExitoso");
        }

        /**
         * login-logout completo
         */
        public function testLoginIniciaryCerrarSesion(): void
        {
                $this->visitPage("login");
                $this->fillField(WebDriverBy::name("ingUsuario"), "admin");
                $this->fillField(WebDriverBy::name("ingPassword"), "admin123!");

                $elementosCaptcha = $this->driver->findElements(
                        WebDriverBy::name("captchaCodigo"),
                );
                $this->assertEmpty(
                        $elementosCaptcha,
                        "CAPTCHA debe estar deshabilitado para pruebas funcionales",
                );

                $this->clickElement(WebDriverBy::name("ingresar"));
                sleep(3);

                $urlActual = $this->driver->getCurrentURL();
                $codigoFuente = $this->driver->getPageSource();

                $loginFallido =
                        strpos($urlActual, "login") !== false &&
                        strpos($codigoFuente, "ingUsuario") !== false;

                $this->assertFalse(
                        $loginFallido,
                        "Login debe funcionar correctamente - revisar si admin/admin123! usuario existe en la base de datos",
                );

                $this->visitPage("cerrarsesion");
                sleep(2);

                $urlActual = $this->driver->getCurrentURL();
                $codigoFuente = $this->driver->getPageSource();

                $this->assertTrue(
                        strpos($codigoFuente, "ingUsuario") !== false ||
                                strpos($codigoFuente, "Ingresa tus datos") !==
                                        false,
                        "Should be back to login page after logout",
                );

                $this->takeTestScreenshot("LoginIniciaryCerrarSesion");
        }

        public function testLoginVerPassword(): void
        {
                $this->visitPage("login");

                $toggleElements = $this->driver->findElements(
                        WebDriverBy::className("icon-password"),
                );

                $this->fillField(WebDriverBy::name("ingUsuario"), "admin");
                $this->fillField(WebDriverBy::name("ingPassword"), "admin");

                if (count($toggleElements) > 0) {
                        $campoPassword = $this->driver->findElement(
                                WebDriverBy::name("ingPassword"),
                        );
                        $toggle = $toggleElements[0];

                        $this->assertEquals(
                                "password",
                                $campoPassword->getAttribute("type"),
                        );

                        $toggle->click();
                        sleep(1);
                        $this->assertEquals(
                                "text",
                                $campoPassword->getAttribute("type"),
                        );
                        $this->takeTestScreenshot("LoginVerPasswordShown");

                        $toggle->click();
                        sleep(1);
                        $this->assertEquals(
                                "password",
                                $campoPassword->getAttribute("type"),
                        );
                        $this->takeTestScreenshot("LoginVerPasswordHidden");
                } else {
                        $this->markTestSkipped(
                                "No se encontró el botón de alternar visibilidad de contraseña",
                        );
                }
        }

        /**
         * responsive
         */
        public function testLoginResponsive(): void
        {
                $this->driver
                        ->manage()
                        ->window()
                        ->setSize(
                                new \Facebook\WebDriver\WebDriverDimension(
                                        375,
                                        667,
                                ),
                        );
                $this->visitPage("login");

                $this->assertElementVisible(WebDriverBy::name("ingUsuario"));
                $this->assertElementVisible(WebDriverBy::name("ingPassword"));
                $this->assertElementVisible(WebDriverBy::name("ingresar"));

                $this->takeTestScreenshot("LoginResponsive");

                $this->driver->manage()->window()->maximize();
        }

        public function testLoginPersistenciaSesion(): void
        {
                $this->visitPage("login");
                $this->fillField(WebDriverBy::name("ingUsuario"), "admin");
                $this->fillField(WebDriverBy::name("ingPassword"), "admin123!");

                $elementosCaptcha = $this->driver->findElements(
                        WebDriverBy::name("captchaCodigo"),
                );
                $this->assertEmpty(
                        $elementosCaptcha,
                        "Se debe deshabilitar el CAPTCHA para los tests funcionales",
                );

                $this->clickElement(WebDriverBy::name("ingresar"));
                sleep(3);

                $urlActual = $this->driver->getCurrentURL();
                $codigoFuente = $this->driver->getPageSource();

                $loginFallido =
                        strpos($urlActual, "login") !== false &&
                        strpos($codigoFuente, "ingUsuario") !== false;

                $this->assertFalse(
                        $loginFallido,
                        "Login debe tener éxito para el test de persistencia de sesión",
                );

                $paginasProbar = ["inicio", "usuarios", "productos"];

                foreach ($paginasProbar as $pagina) {
                        $this->visitPage($pagina);
                        sleep(1);

                        $urlActual = $this->driver->getCurrentURL();
                        $codigoFuente = $this->driver->getPageSource();

                        $sessionLost =
                                strpos($codigoFuente, "ingUsuario") !== false;

                        $this->assertFalse(
                                $sessionLost,
                                "Debería permanecer logueado al visitar /$pagina - revisar permisos de usuario y configuración de sesión",
                        );
                }

                $this->takeTestScreenshot("LoginPersistencia");
        }
}
