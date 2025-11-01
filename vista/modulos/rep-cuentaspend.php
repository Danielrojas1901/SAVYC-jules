<?php
require_once 'controlador/rep-cuentaspend.php';
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Reporte de Cuentas Pendientes</h1>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <ul class="nav nav-tabs" id="tabContent" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="pagar-tab" data-toggle="tab" href="#pagar" role="tab">Cuentas por Pagar</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="cobrar-tab" data-toggle="tab" href="#cobrar" role="tab">Cuentas por Cobrar</a>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content">
                                <!-- =========== CUENTAS POR PAGAR ============== -->
                                <div class="tab-pane fade show active" id="pagar" role="tabpanel">
                                    <div class="row mb-2">
                                        <form action="index.php?pagina=reportes" method="post" target="_blank" id="form-pagar" class="form-fechas">
                                            <input type="hidden" name="tipo" value="pagar">
                                            <input type="hidden" name="fechaInicio1" class="fecha-inicio">
                                            <input type="hidden" name="fechaFin1" class="fecha-fin">
                                            <?php if (!empty($_SESSION["permisos"]["reporte"])): ?>
                                                <button type="button" class="btn btn-default float-right daterange-btn">
                                                    <span><i class="fa fa-calendar"></i> Rango de fecha</span>
                                                    <i class="fas fa-caret-down"></i>
                                                </button>
                                                <button type="button" class="btn btn-secondary mx-2 reset-btn">Restablecer Rango</button>
                                                <button class="btn btn-danger mx-2" name="pdf" title="Generar PDF" type="submit">Generar PDF</button>
                                            <?php endif; ?>
                                        </form>
                                    </div>
                                    <div class="ml-auto d-flex">
                                        <!-- Select de gasto o compra 
                                        <form action="index.php?pagina=reportes" method="post" target="_blank" class="d-inline">
                                            <input type="hidden" name="tipo" value="tipopagar">
                                            <div class="form-group d-flex align-items-center">
                                                <select class="form-control mr-2" name="tipopagar" required>
                                                    <option value="" selected disabled>Seleccione una categoría</option>
                                                    <?php //foreach($tipo as $t): 
                                                    ?>
                                                        <option value="<?php //echo $t['cod_categoria']; 
                                                                        ?>">
                                                            <?php //echo $t['nombre']; 
                                                            ?>
                                                        </option>
                                                    <?php //endforeach; 
                                                    ?>
                                                </select>
                                                <button class="btn btn-primary" name="categoria" title="Filtrar por categoría" id="categoria" type="submit">Filtrar</button>
                                            </div>
                                        </form>-->
                                    </div>
                                    <div class="table-responsive">
                                        <table id="cuentas-pagar" class="table table-bordered table-striped table-hover datatable" style="width: 100%;">
                                            <thead class="thead-primary">
                                                <tr>
                                                    <th>#</th>
                                                    <th>Asunto/Proveedor</th>
                                                    <th>Fecha de Creacion</th>
                                                    <th>Fecha de Vencimiento</th>
                                                    <th>Importe Total</th>
                                                    <th>Monto Pagado</th>
                                                    <th>Saldo pendiente</th>
                                                    <th>Status</th>

                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $contador = 1;
                                                foreach ($cuentasPagar as $cuenta):

                                                ?>
                                                    <tr>
                                                        <td><?php echo $contador++; ?></td>
                                                        <td><?php echo htmlspecialchars($cuenta['asunto']); ?></td>
                                                        <td><?php echo date('d/m/Y', strtotime($cuenta['fecha'])); ?></td>
                                                        <td><?php echo isset($cuenta['fecha_vencimiento']) ? date('d/m/Y', strtotime($cuenta['fecha_vencimiento'])) : "No disponible"; ?></td>
                                                        <td class="text-right"><?php echo number_format($cuenta['monto_total'], 2, ',', '.'); ?></td>
                                                        <td class="text-right"><?php echo number_format($cuenta['monto_pagado'], 2, ',', '.'); ?></td>
                                                        <td class="text-right font-weight-bold"><?php echo number_format($cuenta['monto_pendiente'], 2, ',', '.'); ?></td>
                                                        <td><span class="badge bg-<?php echo ($cuenta['status']  == 'Vencido') ? 'danger' : (($cuenta['status']  == 'Pago parcial') ? 'warning' : (($cuenta['status'] == 'Pendiente') ? 'secondary' : 'primary')); ?>"><?php echo $cuenta['status']; ?></span></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                            <tfoot>
                                                <tr class="bg-light">
                                                    <td colspan="4" class="text-right font-weight-bold">TOTALES:</td>
                                                    <td class="text-right font-weight-bold"><?php
                                                                                            $totalPagar = array_sum(array_column($cuentasPagar, 'monto_total'));
                                                                                            echo number_format($totalPagar, 2, ',', '.');
                                                                                            ?></td>
                                                    <td class="text-right font-weight-bold"><?php
                                                                                            $totalPagado = array_sum(array_column($cuentasPagar, 'monto_pagado'));
                                                                                            echo number_format($totalPagado, 2, ',', '.');
                                                                                            ?></td>
                                                    <td class="text-right font-weight-bold"><?php
                                                                                            $totalSaldo = array_sum(array_column($cuentasPagar, 'monto_pendiente'));
                                                                                            echo number_format($totalSaldo, 2, ',', '.');
                                                                                            ?></td>
                                                    <td></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>

                                <!-- =========== CUENTAS POR COBRAR ============== -->
                                <div class="tab-pane fade show" id="cobrar" role="tabpanel">
                                    <div class="row mb-2">
                                        <form action="index.php?pagina=reportes" method="post" target="_blank" id="form-cobrar" class="form-fechas">
                                            <input type="hidden" name="tipo" value="cobrar">
                                            <input type="hidden" name="fechaInicio1" class="fecha-inicio">
                                            <input type="hidden" name="fechaFin1" class="fecha-fin">
                                            <?php if (!empty($_SESSION["permisos"]["reporte"])): ?>
                                                <button type="button" class="btn btn-default float-right daterange-btn">
                                                    <span><i class="fa fa-calendar"></i> Rango de fecha</span>
                                                    <i class="fas fa-caret-down"></i>
                                                </button>
                                                <button type="button" class="btn btn-secondary mx-2 reset-btn">Restablecer Rango</button>
                                                <button class="btn btn-danger mx-2" name="pdf" title="Generar PDF" type="submit">Generar PDF</button>
                                            <?php endif; ?>
                                        </form>
                                    </div>

                                    <div class="table-responsive">
                                        <table id="cuentas-cobrar" class="table table-bordered table-striped table-hover datatable" style="width: 100%;">
                                            <thead class="thead-primary">
                                                <tr>
                                                    <th>#</th>
                                                    <th>Cliente</th>
                                                    <th>Fecha de Venta</th>
                                                    <th>Fecha de Vencimiento</th>
                                                    <th>Importe Total</th>
                                                    <th>Monto Cobrado</th>
                                                    <th>Saldo pendiente</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $contador = 1;
                                                foreach ($cuentasCobrar as $cuenta):
                                                ?>
                                                    <tr>
                                                        <td><?php echo $contador++; ?></td>
                                                        <td><?php echo htmlspecialchars($cuenta['nombre']); ?></td>
                                                        <td><?php echo date('d/m/Y', strtotime($cuenta['fecha'])); ?></td>
                                                        <td><?php echo (isset($cuenta['fecha_vencimiento']) && $cuenta['fecha_vencimiento'] != '0000-00-00') ? date('d/m/Y', strtotime($cuenta['fecha_vencimiento'])) : "No disponible"; ?> </td>
                                                        <td class="text-right"><?php echo number_format($cuenta['total'], 2, ',', '.'); ?></td>
                                                        <td class="text-right"><?php echo number_format($cuenta['monto_pagado'], 2, ',', '.'); ?></td>
                                                        <td class="text-right font-weight-bold"><?php echo number_format($cuenta['saldo_pendiente'], 2, ',', '.'); ?></td>
                                                        <td>
                                                            <span class="badge bg-<?php echo ($cuenta['estado']  == 'Vencido') ? 'danger' : (($cuenta['estado']  == 'Pago parcial') ? 'warning' : (($cuenta['estado'] == 'Pendiente') ? 'secondary' : 'primary')); ?>">
                                                                <?php echo $cuenta['estado']; ?>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                            <tfoot>
                                                <tr class="bg-light">
                                                    <td colspan="4" class="text-right font-weight-bold">TOTALES:</td>
                                                    <td class="text-right font-weight-bold"><?php
                                                                                            $totalCobrar = array_sum(array_column($cuentasCobrar, 'total'));
                                                                                            echo number_format($totalCobrar, 2, ',', '.');
                                                                                            ?></td>
                                                    <td class="text-right font-weight-bold"><?php
                                                                                            $totalCobrado = array_sum(array_column($cuentasCobrar, 'monto_pagado'));
                                                                                            echo number_format($totalCobrado, 2, ',', '.');
                                                                                            ?></td>
                                                    <td class="text-right font-weight-bold"><?php
                                                                                            $totalSaldoCobrar = array_sum(array_column($cuentasCobrar, 'saldo_pendiente'));
                                                                                            echo number_format($totalSaldoCobrar, 2, ',', '.');
                                                                                            ?></td>
                                                    <td></td>
                                                </tr>
                                            </tfoot>
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

<script src="vista\dist\js\modulos-js\rep-cuentaspend.js"></script>