<?php
use Modelo\Usuarios;
use Modelo\General;
use Modelo\Roles;
use Modelo\Bitacora;


$hayinternet = true;
$conexion = null;

// verificar internet solo en entorno de testing
if($_ENV["ENTORNO"] == "TESTING"){
    $conexion = @fsockopen("www.google.com", 80, $errno, $errstr, 2);
	if ($conexion) {
		fclose($conexion);
	} else {
		$hayinternet = false;
	}
}

// determinar captchas ausar basado en donde esta instalado el sistema
$usarCaptchaPhp = false;
$usarCaptchaCloudflare = false;

if (isset($_ENV["INSTALACION"])) {
    if ($_ENV["INSTALACION"] == "LOCAL") {
        $usarCaptchaPhp = true;
        $usarCaptchaCloudflare = $hayinternet; // solo si hay internet
    } elseif ($_ENV["INSTALACION"] == "SERVIDOR") {
        $usarCaptchaPhp = false;
        $usarCaptchaCloudflare = true; // siempre en servidor
    }
} else {
    // comportamiento por defecto si no está definida la variable
    $usarCaptchaPhp = true;
    $usarCaptchaCloudflare = $hayinternet;
}

$obj = new General();
$objuser= new Usuarios();
$objRol= new Roles();
$objbitacora = new Bitacora();

if (isset($_POST["ingresar"])) {

    // validar captcha cloudflare si está habilitado
    if ($usarCaptchaCloudflare && isset($_POST['cf-turnstile-response'])) {
        $token = $_POST['cf-turnstile-response'];
        
        // seleccionar el token secreto segun donde esta instalado el sistema
        $secret_key = '';
        if ($_ENV["INSTALACION"] == "LOCAL" && isset($_ENV["CLOUDFLARE_LOCAL"])) {
            $secret_key = $_ENV["CLOUDFLARE_LOCAL"];
        } elseif ($_ENV["INSTALACION"] == "SERVIDOR" && isset($_ENV["CLOUDFLARE_SERVIDOR"])) {
            $secret_key = $_ENV["CLOUDFLARE_SERVIDOR"];
        }

        $ip = $_SERVER['REMOTE_ADDR'];

        $data = [
            'secret' => $secret_key,
            'response' => $token,
            'remoteip' => $ip
        ];

        // Inicializar cURL
        $ch = curl_init();

        // Configurar opciones de cURL
        curl_setopt($ch, CURLOPT_URL, 'https://challenges.cloudflare.com/turnstile/v0/siteverify');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Obtener la respuesta en variable, no en pantalla
        curl_setopt($ch, CURLOPT_TIMEOUT, 5); // Opcional: evitar que quede colgado si hay problemas

        // Ejecutar la solicitud
        $response = curl_exec($ch);

        // Verificar errores de cURL
        if (curl_errno($ch)) {
            curl_close($ch);
            $_SESSION['login'] = [
                "title" => "Error",
                "message" => "Error al verificar el CAPTCHA de Cloudflare.",
                "icon" => "error"
            ];
            header('Location: login');
            exit;
        }

        // Cerrar cURL
        curl_close($ch);

        // Decodificar respuesta JSON
        $result_json = json_decode($response, true);

        // Validar si el CAPTCHA fue exitoso
        if (!$result_json['success']) {
            $_SESSION['captcha'] = '';
            $_SESSION['login'] = [
                "title" => "Error",
                "message" => "Código CLOUDFLARE incorrecto.",
                "icon" => "error"
            ];
            header('Location: login');
            exit;
        }
    } elseif ($usarCaptchaCloudflare) {
        // si cloudflare está habilitado pero no se envió el token
        $_SESSION['login'] = [
            "title" => "Error",
            "message" => "Debe completar la verificación de seguridad.",
            "icon" => "error"
        ];
        header('Location: login');
        exit;
    }

    // validar captcha php si está habilitado
    if ($usarCaptchaPhp && isset($_POST['captchaCodigo'])) {
        $captchaCodigo = $_POST['captchaCodigo'];

        if ($captchaCodigo != $_SESSION['captcha']) {
            $_SESSION['captcha'] = ''; // limpiar código
            $_SESSION['login'] = [
                "title" => "Error",
                "message" => "Usuario o contraseña incorrecta.",
                "icon" => "error"
            ];
            header('Location: login');
            exit;
        }
    } elseif ($usarCaptchaPhp) {
        // si captcha php está habilitado pero no se envió el código
        $_SESSION['login'] = [
            "title" => "Error",
            "message" => "Debe ingresar el código de verificación.",
            "icon" => "error"
        ];
        header('Location: login');
        exit;
    }

    // validar el usuario y la contraseña
    $errores=[];

    try{
        $_POST['ingUsuario'] = strtolower($_POST['ingUsuario']);
        $objuser->setDatos($_POST);
        $objuser->check();

    } catch(Exception $e){

        $errores[] = $e->getMessage();
    }

    if(!empty($errores)){
        $login = [
            "title" => "Error",
            "message" => implode(" ", $errores),
            "icon" => "error"
        ];
        header('Location: login');
        exit;

    } else {

        // si no hay errores, procedemos a validar el usuario y la contraseña
        $respuesta = $objuser->mostrar($_POST['ingUsuario']);

        if (!empty($respuesta) && isset($respuesta["user"]) && $respuesta["status"] == 1) {

            if ($respuesta["user"] == $_POST["ingUsuario"] && password_verify($_POST["ingPassword"], $respuesta["password"])) {

                $_SESSION["iniciarsesion"] = "ok";
                $_SESSION["user"] = $respuesta["user"];
                $_SESSION["nombre"] = $respuesta["nombre"];
                $_SESSION["cod_usuario"]=$respuesta["cod_usuario"];
                $rol=$objRol->consultarLogin($respuesta["cod_tipo_usuario"]);
                $_SESSION["rol"] = $rol["rol"];

                $logo = $obj->mostrar();
                if(!empty($logo)){
                    $_SESSION["logo"] = $logo[0]["logo"];
                    $_SESSION["n_empresa"] = $logo[0]["nombre"];
                    $_SESSION["rif"] = $logo[0]["rif"];
                    $_SESSION["telefono"] = $logo[0]["telefono"];
                    $_SESSION["email"] = $logo[0]["email"];
                    $_SESSION["direccion"] = $logo[0]["direccion"];
                    $horario=$obj->horarios();
                    $_SESSION["horario"] = [];
                    if (!empty($horario)) {
                        foreach ($horario as $h) { 
                            $dia = $h["dia"];
                            $_SESSION["horario"][$dia] = [
                                "desde" => $h["desde"],
                                "hasta" => $h["hasta"],
                                "cerrado" => $h["cerrado"]
                            ];
                        }
                    }
                /*para recorrer
                foreach($_SESSION["horario"] as $dia => $horario) {
                    echo "Día: $dia, Desde: {$horario['desde']}, Hasta: {$horario['hasta']}, Cerrado: {$horario['cerrado']}<br>";
                */
                }
                $_SESSION["permisos"] = [];
                
                $accesos = $objuser->accesos($respuesta["cod_usuario"]);

                foreach ($accesos as $permisos) {
                    $modulo = $permisos["modulos"];
                    $accion = $permisos["accion"];
                
                    if (!isset($_SESSION["permisos"][$modulo])) {
                        $_SESSION["permisos"][$modulo] = [];
                    }
                
                    // Marcamos la acción permitida con 1
                    $_SESSION["permisos"][$modulo][$accion] = 1;
                    } 

                    echo '<script>
                    window.location="inicio";
                    </script>';
                    $objbitacora->registrarEnBitacora($_SESSION['cod_usuario'], 'Acceso al sistema', $_POST["ingUsuario"], 'Inicio');

                } else {
                    $login = [
                        "title" => "Error",
                        "message" => "Usuario o contraseña incorrecta.",
                        "icon" => "error"
                    ];
                }
        } else {
            $login = [
                "title" => "Error",
                "message" => "Error de acceso",
                "icon" => "error"
            ];
        }
        
    }
}
