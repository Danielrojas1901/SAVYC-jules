<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SAVYC</title>
  <link rel="icon" href="vista/dist/img/logo_generico.png">

<!-- ==================
PLUGINGS DE CSS
======================= -->
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;600&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap" rel="stylesheet">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="vista/plugins/fontawesome-free/css/all.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="vista/dist/css/adminlte.css">
  <link rel="stylesheet" href="vista/dist/css/custom.css">
  <link rel="stylesheet" href="vista/dist/css/notificaciones.css">

  <!-- Date Range Picker -->
  <link rel="stylesheet" href="vista/plugins/daterangepicker/daterangepicker.css">

  <!-- DataTables -->
  <link rel="stylesheet" href="vista/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="vista/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
  <link rel="stylesheet" href="vista/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">

<!-- =======================
    PLUGINGS DE JAVASACRIPT  
=============================-->
  <!-- jQuery -->
  <script src="vista/plugins/jquery/jquery.min.js"></script>
  <!-- Bootstrap 4 -->
  <script src="vista/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <!-- AdminLTE App -->
  <script src="vista/dist/js/adminlte.min.js"></script>

  <!-- Date Range Picker -->
  <script src="vista/plugins/moment/moment.min.js"></script>
  <script src="vista/plugins/daterangepicker/daterangepicker.js"></script>
  <!-- DataTables  & Plugins -->
<script src="vista/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="vista/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="vista/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="vista/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="vista/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="vista/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<script src="vista/plugins/jszip/jszip.min.js"></script>
<script src="vista/plugins/pdfmake/pdfmake.min.js"></script>
<script src="vista/plugins/pdfmake/vfs_fonts.js"></script>
<script src="vista/plugins/datatables-buttons/js/buttons.html5.min.js"></script>
<script src="vista/plugins/datatables-buttons/js/buttons.print.min.js"></script>
<script src="vista/plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
<!-- Custom JS -->
<script src="vista/dist/js/custom.js"></script>
<!-- SweetAlert2 -->
<script src="vista/plugins/sweetalert2/sweetalert2.all.js"></script>
<!-- Toastr -->
<link rel="stylesheet" href="vista/plugins/toastr/toastr.min.css">
<script src="vista/plugins/toastr/toastr.min.js"></script>
<!-- ChartJS -->
<script src="vista/plugins/chart.js/Chart.min.js"></script>

<!-- jsPDF y html2canvas -->
<script src="vista/plugins/jspdf/jspdf.umd.min.js"></script>
<script src="vista/plugins/html2canvas/html2canvas.min.js"></script>
<script>
  // inicializar jsPDF
  window.jsPDF = window.jspdf.jsPDF;
</script>

</head>
<!-- =======================
    BODY
=============================-->
<body class="hold-transition sidebar-mini layout-fixed">
<!-- Site wrapper -->
  <?php 
  if(isset($_SESSION["iniciarsesion"]) && $_SESSION["iniciarsesion"] == 'ok'){
  ?>
  <div class="wrapper">
  <?php
  include "componentes/header.php";
  include "componentes/sidebar.php";

  if(isset ($_GET["ruta"])){
    if($_GET["ruta"] == "inicio" || 
    $_GET["ruta"] == "marcas" && (!isset($_SESSION["permisos"]["config_productos"])) ||
    $_GET["ruta"] == "categorias" && (!isset($_SESSION["permisos"]["config_productos"])) ||
    $_GET["ruta"] == "carga" && (!empty($_SESSION["permisos"]["inventario"])) ||
    $_GET["ruta"] == "descarga" && (!empty($_SESSION["permisos"]["inventario"])) ||
    $_GET["ruta"] == "rep-inventario" && (!empty($_SESSION["permisos"]["reporte"])) ||
    $_GET["ruta"] == "rep-proveedores" && (!empty($_SESSION["permisos"]["reporte"])) ||
    $_GET["ruta"] == "rep-cuentaspend" && (!empty($_SESSION["permisos"]["reporte"])) ||
    $_GET["ruta"] == "rep-cliente" && (!empty($_SESSION["permisos"]["reporte"])) ||
    $_GET["ruta"] == "rep-gastos" && (!empty($_SESSION["permisos"]["reporte"])) ||
    $_GET["ruta"] == "rep-venta" && (!empty($_SESSION["permisos"]["reporte"])) ||
    $_GET["ruta"] == "rep-compra" && (!empty($_SESSION["permisos"]["reporte"])) ||
    $_GET["ruta"] == "productos" && (!empty($_SESSION["permisos"]["producto"])) ||
    $_GET["ruta"] == "usuarios" && (!empty($_SESSION["permisos"]["seguridad"])) ||
    $_GET["ruta"] == "backup" && (!empty($_SESSION["permisos"]["seguridad"])) ||
    $_GET["ruta"] == "compras" && (!empty($_SESSION["permisos"]["compra"])) ||
    $_GET["ruta"] == "banco" && (!empty($_SESSION["permisos"]["config_finanza"])) ||
    $_GET["ruta"] == "tpago" && (!empty($_SESSION["permisos"]["config_finanza"])) || 
    $_GET["ruta"] == "divisa" && (!empty($_SESSION["permisos"]["config_finanza"])) || 
    $_GET["ruta"] == "proveedores" && (!empty($_SESSION["permisos"]["proveedor"])) || 
    $_GET["ruta"] == "unidad" && (!isset($_SESSION["permisos"]["config_productos"])) ||
    $_GET["ruta"] == "general" && (!empty($_SESSION["permisos"]["seguridad"])) ||
    $_GET["ruta"] == "clientes" && (!empty($_SESSION["permisos"]["cliente"])) ||
    $_GET["ruta"] == "roles" && (!empty($_SESSION["permisos"]["seguridad"])) ||
    $_GET["ruta"] == "bitacora" && (!empty($_SESSION["permisos"]["seguridad"])) ||
    $_GET["ruta"] == "finanzas" && (!empty($_SESSION["permisos"]["finanza"])) ||
    $_GET["ruta"] == "categoriag" && (!empty($_SESSION["permisos"]["config_finanza"])) ||
    $_GET["ruta"] == "caja" && (!empty($_SESSION["permisos"]["config_finanza"])) ||
    $_GET["ruta"] == "cuentabancaria" && (!empty($_SESSION["permisos"]["tesoreria"])) ||
    $_GET["ruta"] == "conciliacion" && (!empty($_SESSION["permisos"]["tesoreria"])) ||
    $_GET["ruta"] == "controlcaja" && (!empty($_SESSION["permisos"]["tesoreria"])) ||
    $_GET["ruta"] == "venta" && (!empty($_SESSION["permisos"]["venta"])) ||
    $_GET["ruta"] == "catalogocuentas" && (!empty($_SESSION["permisos"]["contabilidad"])) ||
    $_GET["ruta"] == "rep-contabilidad" && (!empty($_SESSION["permisos"]["contabilidad"])) ||
    $_GET["ruta"] == "gasto" && (!empty($_SESSION["permisos"]["gasto"])) ||
    $_GET["ruta"] == "cuentaspend" && (!empty($_SESSION["permisos"]["cuentas_pendiente"]))||
    $_GET["ruta"] == "movimientos" && (!empty($_SESSION["permisos"]["contabilidad"])) ||
    $_GET["ruta"] == "cerrarsesion"){
      include "modulos/". $_GET["ruta"] . ".php";
    } else {
      include "modulos/inicio.php";
    }

  }else{
      include "modulos/inicio.php";
  } 
  ?>
  <aside class="control-sidebar control-sidebar-dark">
      <!-- Control sidebar content goes here -->
  </aside>
  </div>
  <?php
  //include "modulos/footer.php";
  ?>
</div>
<?php
}else{
  include "modulos/login.php";
}
?>

</body>
</html>