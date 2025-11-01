<?php
use Modelo\Proveedores;

$objProveedores = new Proveedores();


$registro = $objProveedores->getconsulta();
$_GET['ruta'] = 'rep-proveedores';
require_once 'plantilla.php';