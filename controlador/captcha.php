<?php

if (!extension_loaded('gd')) {
    die('extension gd no disponible.');
}

// Baraja una cadena aleatoriamente
$codigo = substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789"), 0, 5);
$ancho = 100;
$alto = 50;
$fuente = 'vista/dist/font/FiraCode-VariableFont_wght.ttf'; //slash / en lugar de \
$tfuente = 22; // Tamaño de la fuente

$_SESSION['captcha'] = $codigo; // Guardamos el código en la sesión

$imagen = imagecreate($ancho, $alto); // Creamos la imagen
$color_fondo = imagecolorallocate($imagen, 255, 255, 255); // Color de fondo blanco
$color_t = imagecolorallocate($imagen, 0, 0, 0); // Color del texto negro
imagefill($imagen, 0, 0, $color_fondo); // Rellenamos el fondo con blanco
$color_linea = imagecolorallocate($imagen, 0, 0, 128); // Color de la línea


for($i = 0; $i < 8; $i++) { // Creamos líneas aleatorias
    imageline($imagen, 0, rand(0, $alto), $ancho, rand(0, $alto), $color_linea); // Dibujamos la línea
}

for($i = 0; $i <100; $i++) { // Creamos puntos aleatorios
    imagesetpixel($imagen, rand(0, $ancho), rand(0, $alto), $color_linea); // Dibujamos el pixel
}

// Escribimos el texto en negro
imagettftext($imagen, $tfuente, -5, 10, 30, $color_t, $fuente, $codigo);

// Prevent caching
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');

// Output image
header("Content-Type: image/png");
imagepng($imagen);
imagedestroy($imagen);
