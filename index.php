<?php
session_start();

require_once './vendor/autoload.php';
require_once './config/config.php';

$pagina = 'plantilla';

if(!empty($_GET['pagina'])){
    $pagina = $_GET['pagina'];
}

if (is_file('controlador/'.$pagina.'.php')){ 
require_once 'controlador/'.$pagina.'.php';
} 