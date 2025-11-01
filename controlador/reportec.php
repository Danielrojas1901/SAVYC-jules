<?php
use Modelo\Clientes;

$obj = new Clientes();
$registro = $obj->consultar();

$_GET['ruta'] = 'rep-cliente';
require_once 'plantilla.php';