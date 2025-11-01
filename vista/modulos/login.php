<?php 
#Requerir al controlador
require_once "controlador/login.php";

$hayinternet = true;
// verificar internet solo en entorno de testing
if($_ENV["ENTORNO"] == "TESTING"){
    $conexion = @fsockopen("www.google.com", 80, $errno, $errstr, 2);
    if ($conexion) {
        fclose($conexion);
        $hayinternet = true;
    } else {
        $hayinternet = false;
    }
}

// determinar qué captchas mostrar basado en la instalación
$mostrarCaptchaPhp = false;
$mostrarCaptchaCloudflare = false;

if (isset($_ENV["INSTALACION"])) {
    if ($_ENV["INSTALACION"] == "LOCAL") {
        $mostrarCaptchaPhp = true;
        $mostrarCaptchaCloudflare = $hayinternet; // solo si hay internet
    } elseif ($_ENV["INSTALACION"] == "SERVIDOR") {
        $mostrarCaptchaPhp = false;
        $mostrarCaptchaCloudflare = true; // siempre en servidor
    }
} else {
    // comportamiento por defecto si no está definida la variable
    $mostrarCaptchaPhp = true;
    $mostrarCaptchaCloudflare = $hayinternet;
}
?>

<!--<div id="wallpaper">-->
    <div class="login-page">
        <div class="login-box">
            <div class="card card-outline card-primary">
                <div class="card-header text-center">
                    <h1 class="h1"><b>SAVYC+</h1>
                </div>
                <div class="card-body">
                    <p class="login-box-msg">Ingresa tus datos para iniciar sesión</p>
                    <form method="post">
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" placeholder="Usuario" name="ingUsuario" required>
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-user"></span>
                                </div>
                            </div>
                        </div>
                        <!-- Campo de contraseña con el ojito y el candado -->
                        <div class="input-group mb-3">
                            <input type="password" class="form-control" id="pass" placeholder="Contraseña" name="ingPassword" required>
                            <span class="fas fa-eye icon-password" data-target="pass"></span>
                            
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-lock"></span>
                                </div>
                            </div>
                        </div>
                        
                        <?php if ($mostrarCaptchaPhp): ?>
                            <!-- CAPTCHA PHP -->
                            <div class="input-group mb-3 d-flex align-items-center"> 
                                <img src="index.php?pagina=captcha" alt="code" id="codigo">
                                <input type="text" class="form-control" id="captcha" placeholder="Ingresa el codigo" name="captchaCodigo" required>
                            </div>
                        <?php endif; ?>

                        <?php if ($mostrarCaptchaCloudflare): ?>
                            <!-- CAPTCHA Cloudflare -->
                            <?php
                            // seleccionar el site key según el tipo de instalación
                            $site_key = '';
                            if ($_ENV["INSTALACION"] == "LOCAL" && isset($_ENV["CLOUDFLARE_LOCAL_SITE"])) {
                                $site_key = $_ENV["CLOUDFLARE_LOCAL_SITE"];
                            } elseif ($_ENV["INSTALACION"] == "SERVIDOR" && isset($_ENV["CLOUDFLARE_SERVIDOR_SITE"])) {
                                $site_key = $_ENV["CLOUDFLARE_SERVIDOR_SITE"];
                            } else {
                                // key por defecto si no hay variables de entorno configuradas
                                $site_key = "0x4AAAAAABUTeiES0tXs0HGp";
                            }
                            ?>
                            <div class="cf-turnstile" data-sitekey="<?php echo htmlspecialchars($site_key); ?>"></div>
                        <?php endif; ?>
                        
                        <div class="row">
                            <div class="col-4">
                                <button type="submit" class="btn btn-primary btn-block" name="ingresar">Ingresar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php
if (isset($login)): ?>
    <script>
        Swal.fire({
            title: '<?php echo $login["title"]; ?>',
            text: '<?php echo $login["message"]; ?>',
            icon: '<?php echo $login["icon"]; ?>',
            confirmButtonText: 'Ok'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location = 'login';
            }
        });
    </script>
<?php endif; ?>

<?php if (isset($_SESSION['login'])): ?>
<script>
    Swal.fire({
        title: '<?php echo $_SESSION["login"]["title"]; ?>',
        text: '<?php echo $_SESSION["login"]["message"]; ?>',
        icon: '<?php echo $_SESSION["login"]["icon"]; ?>',
        confirmButtonText: 'Ok'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location = 'login'; 
        }
    });
</script>
<?php unset($_SESSION['login']); endif; ?>

    <?php if ($mostrarCaptchaCloudflare): ?>
        <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    <?php endif; ?>
    <script src='vista/dist/js/modulos-js/usuarios.js'></script>
