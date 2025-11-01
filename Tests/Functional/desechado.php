<?php
/**
 * Perform login with given credentials
 */
protected function login(string $username, string $password): void
{
        $this->visitPage("login");

        // Fill login form
        $this->fillField(WebDriverBy::name("ingUsuario"), $username);
        $this->fillField(WebDriverBy::name("ingPassword"), $password);

        // CAPTCHA should be disabled for testing
        $captchaElements = $this->driver->findElements(
                WebDriverBy::name("captchaCodigo"),
        );
        if (count($captchaElements) > 0) {
                throw new \Exception(
                        "CAPTCHA must be disabled for functional tests - configure test environment to disable CAPTCHA.",
                );
        }

        // Submit login form
        $this->clickElement(WebDriverBy::name("ingresar"));

        // Wait for response
        sleep(3);

        // Check if login was successful
        $currentUrl = $this->driver->getCurrentURL();
        $pageSource = $this->driver->getPageSource();

        // If still on login page with login form, login likely failed
        if (
                strpos($currentUrl, "login") !== false &&
                strpos($pageSource, "ingUsuario") !== false
        ) {
                throw new \Exception(
                        "Login failed - check credentials or disable CAPTCHA",
                );
        }
}

/**
 * Wait for login success indicators (optional - for when needed)
 */
protected function waitForLoginSuccess(int $timeout = 10): void
{
        $wait = new WebDriverWait($this->driver, $timeout);

        try {
                $wait->until(function ($driver) {
                        $currentUrl = $driver->getCurrentURL();
                        $pageSource = $driver->getPageSource();

                        // Success indicator: URL changed from login page AND no login form present
                        if (
                                strpos($currentUrl, "login") ===
                                        false ||
                                strpos($pageSource, "ingUsuario") ===
                                        false
                        ) {
                                return true;
                        }

                        return false;
                });
        } catch (\Exception $e) {
                throw new \Exception(
                        "Login timeout - login may have failed or page is slow to load",
                );
        }
}

/**
 * Perform logout
 */
protected function logout(): void
{
        // Try different logout methods
        $logoutSelectors = [
                WebDriverBy::xpath(
                        '//a[contains(@href, "logout") or contains(@href, "cerrarsesion")]',
                ),
                WebDriverBy::xpath(
                        '//a[contains(text(), "Logout") or contains(text(), "Cerrar Sesión") or contains(text(), "Salir")]',
                ),
                WebDriverBy::xpath(
                        '//button[contains(text(), "Logout") or contains(text(), "Cerrar Sesión")]',
                ),
        ];

        $logoutClicked = false;
        foreach ($logoutSelectors as $selector) {
                $elements = $this->driver->findElements($selector);
                if (
                        count($elements) > 0 &&
                        $elements[0]->isDisplayed()
                ) {
                        $elements[0]->click();
                        $logoutClicked = true;
                        break;
                }
        }

        if (!$logoutClicked) {
                // Alternative: navigate directly to logout URL
                $this->visitPage("cerrarsesion");
        }
}

/**
 * Wait for logout to complete
 */
protected function waitForLogout(int $timeout = 10): void
{
        $wait = new WebDriverWait($this->driver, $timeout);

        $wait->until(function ($driver) {
                $pageSource = $driver->getPageSource();
                return strpos($pageSource, "ingUsuario") !== false ||
                        strpos($pageSource, "Ingresa tus datos") !==
                                false;
        });
}

/**
 * Check if user is currently logged in
 */
protected function isLoggedIn(): bool
{
        $currentUrl = $this->driver->getCurrentURL();
        $pageSource = $this->driver->getPageSource();

        // If on login page, definitely not logged in
        if (strpos($currentUrl, "login") !== false) {
                return false;
        }

        // If login form is present, not logged in
        if (strpos($pageSource, "ingUsuario") !== false) {
                return false;
        }

        // Look for logout indicators
        $logoutElements = $this->driver->findElements(
                WebDriverBy::xpath(
                        '//a[contains(@href, "logout") or contains(@href, "cerrarsesion")]',
                ),
        );

        return count($logoutElements) > 0;
}

/**
 * Visit a protected page (automatically handles login if needed)
 */
protected function visitProtectedPage(
        string $path,
        string $username = "admin",
        string $password = "admin123!",
): void {
        $this->visitPage($path);

        $currentUrl = $this->driver->getCurrentURL();

        // If redirected to login, authenticate and retry
        if (strpos($currentUrl, "login") !== false) {
                $this->login($username, $password);
                $this->visitPage($path);
        }
}
