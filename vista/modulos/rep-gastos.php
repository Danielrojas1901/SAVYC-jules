<?php
require_once 'controlador/reporteGasto.php';
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Reporte Gastos</h1>
                </div>
            </div>
        </div>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <ul class="nav nav-tabs" id="tabContent" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="gastos-tab" data-toggle="tab" href="#gastos" role="tab">Gastos generales</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="fijo-tab" data-toggle="tab" href="#fijo" role="tab">Gastos Fijos</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="variable-tab" data-toggle="tab" href="#variable" role="tab">Gastos variables</a>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content">
                                <!--===========  Gastos generales  ==============-->
                                <div class="tab-pane fade show active" id="gastos" role="tabpanel">
                                    <div class="row mb-2">
                                        <!-- Formulario de filtrado -->
                                        <form action="index.php?pagina=reportes" method="post" target="_blank">
                                            <input type="text" name="tipo" value="gastos" hidden>
                                            <?php if (!empty($_SESSION["permisos"]["reporte"])): ?>
                                                <button class="btn btn-danger ml-2" name="pdf" title="Generar PDF" id="pdfc" type="submit">Generar PDF</button>
                                                <button type="button" class="btn btn-default float-right" id="daterange-btn">
                                                    <span><i class="fa fa-calendar"></i> Rango de fecha</span>
                                                    <i class="fas fa-caret-down"></i>
                                                </button>
                                                <button type="button" class="btn btn-secondary mx-2" id="reset-btn">Restablecer Rango</button>
                                            <?php endif; ?>
                                            <input type="hidden" name="fechaInicio1" id="fechaInicio" value="<?php echo date('Y-m-d') ?>">
                                            <input type="hidden" name="fechaFin1" id="fechaFin" value="<?php echo date('Y-m-d') ?>">
                                        </form>
                                    </div>
                                    <div class="table-responsive">
                                        <table id="gastos-table" class="table table-bordered table-striped table-hover datatable" style="width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th>Descripción</th>
                                                    <th>Monto</th>
                                                    <th>Fecha Creación</th>
                                                    <th>Status</th>
                                                    <th>Condición de Pago</th>
                                                    <th>Último Pago</th>
                                                    <th>Fecha Último Pago</th>
                                                    <th>Naturaleza</th>
                                                    <th>Tipo</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                foreach ($gastos as $gasto) {
                                                ?>
                                                    <tr>
                                                        <td><?php echo $gasto['descripcion'] ?></td>
                                                        <td><?php echo number_format($gasto['monto'], 2) ?></td>
                                                        <td><?php echo $gasto['fecha_creacion'] ?></td>
                                                        <td>
                                                            <?php
                                                            switch ($gasto['status']) {
                                                                case 1:
                                                                    echo 'Activo';
                                                                    break;
                                                                case 0:
                                                                    echo 'Inactivo';
                                                                    break;
                                                                case 3:
                                                                    echo 'Pagado';
                                                                    break;
                                                                default:
                                                                    echo $gasto['status'];
                                                            }
                                                            ?>
                                                        </td>
                                                        <td><?php echo $gasto['nombre_condicion'] ?? 'N/A' ?></td>
                                                        <td><?php echo isset($gasto['monto_total']) ? number_format($gasto['monto_total'], 2) : 'N/A' ?></td>
                                                        <td><?php echo $gasto['fecha_pago'] ?? 'N/A' ?></td>
                                                        <td><?php echo $gasto['nombre_naturaleza'] ?></td>
                                                        <td><?php echo $gasto['nombre'] ?></td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <!--===========  Gastos Fijos  ==============-->
                                <div class="tab-pane fade" id="fijo" role="tabpanel">
                                    <!-- Formulario de filtrado -->
                                    <div class="row mb-2">
                                        <form action="index.php?pagina=reportes" method="post" target="_blank" class="d-inline" id="form1">
                                            <input type="hidden" name="tipo" value="fijos">

                                            <label for="fechaInicio1">Fecha inicio:</label>
                                            <input type="date" name="fechaInicio1" id="fechaInicio1" value="<?php echo date('Y-m-d') ?>" required>

                                            <label for="fechaFin1">Fecha fin:</label>
                                            <input type="date" name="fechaFin1" id="fechaFin1" value="<?php echo date('Y-m-d') ?>" required>
                                            <?php if (!empty($_SESSION["permisos"]["reporte"])): ?>
                                                <button class="btn btn-danger ml-2" name="pdf1" title="Generar PDF" id="pdf1" type="submit">PDF</button>
                                            <?php endif; ?>
                                        </form>
                                    </div>
                                    <div class="table-responsive">
                                        <table id="fijo-table" class="table table-bordered table-striped table-hover datatable" style="width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th>Descripción</th>
                                                    <th>Monto</th>
                                                    <th>Categoría</th>
                                                    <th>Status</th>
                                                    <th>Último Pago</th>
                                                    <th>Fecha Último Pago</th>
                                                    <th>Total Pagado</th>
                                                    <th>Vuelto</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                foreach ($gastosFijos as $gasto) {
                                                ?>
                                                    <tr>
                                                        <td><?php echo $gasto['descripcion'] ?></td>
                                                        <td><?php echo number_format($gasto['monto'], 2) ?></td>
                                                        <td><?php echo $gasto['categoria_nombre'] ?></td>
                                                        <td>
                                                            <?php
                                                            switch ($gasto['status']) {
                                                                case 1:
                                                                    echo 'Activo';
                                                                    break;
                                                                case 0:
                                                                    echo 'Inactivo';
                                                                    break;
                                                                case 3:
                                                                    echo 'Pagado';
                                                                    break;
                                                                default:
                                                                    echo $gasto['status'];
                                                            }
                                                            ?>
                                                        </td>
                                                        <td><?php echo number_format($gasto['monto_ultimo_pago'], 2) ?></td>
                                                        <td><?php echo $gasto['fecha'] ?? 'Sin fecha' ?></td>
                                                        <td><?php echo number_format($gasto['total_pagos_emitidos'], 2) ?></td>
                                                        <td><?php echo number_format($gasto['vuelto_total'], 2) ?></td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!--===========  GASTOS VARIABLES  ==============-->
                                <div class="tab-pane fade table-responsive" id="variable" role="tabpanel">
                                    <div class="row mb-2">
                                        <form action="index.php?pagina=reportes" method="post" target="_blank" class="d-inline" id="form1">
                                            <input type="hidden" name="tipo" value="variables">

                                            <label for="fechaInicio1">Fecha inicio:</label>
                                            <input type="date" name="fechaInicio1" id="fechaInicio1" value="<?php echo date('Y-m-d') ?>" required>

                                            <label for="fechaFin1">Fecha fin:</label>
                                            <input type="date" name="fechaFin1" id="fechaFin1" value="<?php echo date('Y-m-d') ?>" required>
                                            <?php if (!empty($_SESSION["permisos"]["reporte"])): ?>
                                                <button class="btn btn-danger ml-2" name="pdf1" title="Generar PDF" id="pdf1" type="submit">PDF</button>
                                            <?php endif; ?>
                                        </form>
                                    </div>
                                    <div class="table-responsive">
                                        <table id="variable-table" class="table table-bordered table-striped table-hover datatable" style="width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th>Descripción</th>
                                                    <th>Monto</th>
                                                    <th>Categoría</th>
                                                    <th>Status</th>
                                                    <th>Último Pago</th>
                                                    <th>Fecha Último Pago</th>
                                                    <th>Total Pagado</th>
                                                    <th>Vuelto</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($gastosVariables as $gasto) { ?>
                                                    <tr>
                                                        <td><?php echo $gasto['descripcion'] ?></td>
                                                        <td><?php echo number_format($gasto['monto'], 2) ?></td>
                                                        <td><?php echo $gasto['categoria_nombre'] ?></td>
                                                        <td>
                                                            <?php
                                                            switch ($gasto['status']) {
                                                                case 1:
                                                                    echo 'Activo';
                                                                    break;
                                                                case 0:
                                                                    echo 'Inactivo';
                                                                    break;
                                                                case 3:
                                                                    echo 'Pagado';
                                                                    break;
                                                                default:
                                                                    echo $gasto['status'];
                                                            }
                                                            ?>
                                                        </td>
                                                        <td><?php echo number_format($gasto['monto_ultimo_pago'], 2) ?></td>
                                                        <td><?php echo $gasto['fecha'] ?? 'Sin fecha' ?></td>
                                                        <td><?php echo number_format($gasto['total_pagos_emitidos'], 2) ?></td>
                                                        <td><?php echo number_format($gasto['vuelto_total'], 2) ?></td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script src="vista/dist/js/modulos-js/rep-gastos.js"></script>