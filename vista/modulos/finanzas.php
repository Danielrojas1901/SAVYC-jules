<?php 
require_once "controlador/finanzas.php";
?>
<!-- datos del controlador al js -->
<script>
window.datosFinanzas = <?php echo json_encode($datos_iniciales ?? [], JSON_NUMERIC_CHECK); ?>;
window.permisos = <?php echo json_encode($_SESSION["permisos"] ?? [], JSON_NUMERIC_CHECK); ?>;
</script>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Finanzas</h1>
                    <p>En esta sección se pueden consultar las finanzas de la empresa.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs" id="pestañas" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="cuentas-tab" data-toggle="tab" data-target="#cuentas" type="button" role="tab">Análisis de Cuentas</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="inventario-tab" data-toggle="tab" data-target="#inventario" type="button" role="tab">Rotación de Inventario</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="rentabilidad-tab" data-toggle="tab" data-target="#rentabilidad" type="button" role="tab">Análisis de Rentabilidad</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="presupuestos-tab" data-toggle="tab" data-target="#presupuestos" type="button" role="tab">Presupuestos</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="proyecciones-tab" data-toggle="tab" data-target="#proyecciones" type="button" role="tab">Proyecciones</button>
                </li>
            </ul>
                </div>
                <div class="card-body">
                <div class="tab-content" id="contenido-pestañas">
                        <?php
                        include "vista/modulos/finanzas/cuentas.php";
                        include "vista/modulos/finanzas/inventario.php";
                        include "vista/modulos/finanzas/rentabilidad.php";
                        include "vista/modulos/finanzas/presupuestos.php";
                        include "vista/modulos/finanzas/proyecciones.php";
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Modales -->
<?php include "vista/modulos/finanzas/modales.php"; ?>

<!-- jsPDF AutoTable plugin -->
<script src="vista/plugins/jspdf/jspdf.plugin.autotable.js"></script>

<!-- scripts parafinanzas -->
<script src="vista/dist/js/modulos-js/graficos.js"></script>
<script src="vista/dist/js/modulos-js/finanzas/utils.js"></script>

<script src="vista/dist/js/modulos-js/finanzas/reportePDFUtils.js"></script>
<script src="vista/dist/js/modulos-js/finanzas/cuentas.js"></script>
<script src="vista/dist/js/modulos-js/finanzas/presupuestos.js"></script>
<script src="vista/dist/js/modulos-js/finanzas/proyecciones.js"></script>
<script src="vista/dist/js/modulos-js/finanzas/rentabilidad.js"></script>
<script src="vista/dist/js/modulos-js/finanzas/inventario.js"></script>
<script src="vista/dist/js/modulos-js/finanzas/exportarPDFs.js"></script>
<script src="vista/dist/js/modulos-js/finanzas/finanzas.js"></script>