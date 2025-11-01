<?php
use Modelo\Productos;
use Modelo\Dcarga;
use Modelo\Descarga;
use Modelo\Categorias;

//DATATABLE CARGA
$carga = new Dcarga();
$datos = $carga->getodoo();

//DATATABLE PRODUCTOS
$obj = new Productos();
$productos = $obj->getmostrar();
$objCategoria = new Categorias();
$categoria = $objCategoria->getmostrar();

//DATATABLE DESCARGA
$objdescarga = new Descarga();
$descarga = $objdescarga->consultardescargar();

$_GET['ruta'] = 'rep-inventario';
require_once 'plantilla.php';