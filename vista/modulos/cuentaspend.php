<?php
require_once "controlador/cuentaspend.php";
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col">
                    <h1>Cuentas pendientes</h1>
                    <p>Consulta la informacion de las cuentas por pagar (provenientes de las compras y gastos) y cuentas por cobrar (provenientes de las ventas) de tu empresa.</p>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-6 col-12">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3 id="totalPagos">
                                <?php
                                $monto = $totalpagar[0]['total_pagar'] ?? 0;
                                echo number_format((float)$monto, 2, ',', '.') . ' Bs';
                                ?>
                            </h3>
                            <p>Cuentas por Pagar</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-12">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3 id="totalCobros">
                                <?php
                                $monto = $totalcobrar[0]['total_cobrar'] ?? 0;
                                echo number_format((float)$monto, 2, ',', '.') . ' Bs';
                                ?>
                            </h3>

                            <p>Cuentas por Cobrar</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-hand-holding-usd"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <ul class="nav nav-tabs" id="tabCuentas">
                        <li class="nav-item"><a class="nav-link active" href="#pagos" data-toggle="tab">Cuentas por Pagar</a></li>
                        <li class="nav-item"><a class="nav-link" href="#cobros" data-toggle="tab">Cuentas por Cobrar</a></li>
                    </ul>
                </div>
                <div class="card-body tab-content">
                    <!-- CUENTAS POR PAGAR -->
                    <div class="tab-pane fade show active" id="pagos">
                        <div class="table-responsive">
                            <table id="tablaPagos" class="table table-striped datatable" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>Asunto/Proveedor</th>
                                        <th>Origen</th>
                                        <th>Importe Total</th>
                                        <th>Monto Pagado</th>
                                        <th>Saldo pendiente</th>
                                        <th>Fecha de Vencimiento</th>
                                        <th>Días Restantes</th>
                                        <th>Status</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pagar as $p) { ?>
                                        <tr>
                                            <td><?php echo ($p['asunto']); ?></td>
                                            <td><span class="badge bg-<?php echo (str_starts_with($p['tipo'], 'Gasto') ? 'primary' : 'info'); ?>">
                                            <?php echo $p['tipo'] ?></span></td>
                                            <td><?php echo number_format($p['monto_total'], 2, ',', '.'); ?> Bs</td>
                                            <td><?php echo number_format($p['monto_pagado'], 2, ',', '.'); ?> Bs</td>
                                            <td><?php echo number_format($p['monto_pendiente'], 2, ',', '.'); ?> Bs</td>

                                            <td><?php echo ($p['fecha_vencimiento'] == NULL ? 'No disponible' : $p['fecha_vencimiento']); ?> </td>
                                            <td><span class="badge bg-<?php echo ($p['dias_restantes'] < 3) ? 'danger' : 'success'; ?>"><?php echo $p['dias_restantes']; ?> dias</span></td>
                                            <td><span class="badge bg-<?php echo ($p['status']  == 'Vencido') ? 'danger' : (($p['status']  == 'Pago parcial') ? 'warning' : (($p['status'] == 'Pendiente') ? 'secondary' : 'primary')); ?>"><?php echo $p['status']; ?></span></td>
                                            <td>
                                                <?php if (!empty($_SESSION["permisos"]["cuentas_pendiente"]["registrar"])): ?>
                                                    <?php if (isset($p['tipo']) && str_starts_with($p['tipo'], 'Gasto')) { ?>
                                                        <button class="btn btn-primary" data-toggle="modal" data-target="#pagoModalG"
                                                            data-cod_gasto="<?php echo $p["cod_transaccion"]; ?>"
                                                            data-totalg="<?php echo $p["monto_total"];  ?>"
                                                            data-nombre="<?php echo $p["asunto"]; ?>"
                                                            data-montop="<?php echo ($p["monto_pagado"]); ?>">
                                                            <i class="fas fa-money-bill-wave" title="Registrar pago"></i>
                                                        </button>
                                                    <?php } else { ?>
                                                        <button class="btn btn-primary" data-toggle="modal" data-target="#pagoModalc"
                                                            data-cod_compra="<?php echo $p["cod_transaccion"]; ?>"
                                                            data-fecha="<?php echo $p["fecha"]; ?>"
                                                            data-total="<?php echo $p["monto_total"]; ?>"
                                                            data-montop="<?php echo $p["monto_pagado"]; ?>"
                                                            data-montopagado="<?php echo $p["monto_pagado"]; ?>"
                                                            data-nombre="<?php echo $p["asunto"]; ?>">
                                                            <i class="fas fa-money-bill-wave" title="Registrar pago"></i>
                                                        </button>
                                                    <?php } ?>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- PAGO EMITIDO COMPRA-->
                    <div class="modal fade" id="pagoModalc" tabindex="-1" aria-labelledby="pagoLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header bg-success">
                                    <h5 class="modal-title" id="pagoLabel">Registrar Pago</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <form id="pagoFormc" method="post" action="index.php?pagina=compras">
                                        <div class="form-row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="nro_compra">Nro de Compra</label>
                                                    <input type="text" class="form-control" id="nro-compra" name="cod_compra" readonly>
                                                    <input type="hidden" name="tipo_pago" value="compra">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="nombre_cliente">Razon Social</label>
                                                    <input type="text" class="form-control" id="r_social" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="fecha_venta">Fecha de Pago</label>
                                                    <input type="text" class="form-control" id="fecha_pagoc" name="fecha" readonly>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-center my-3">
                                            <h4>Total de Compra: <span id="total-pagoc" class="font-weight-bold" style="font-size: 3rem;">0.00</span></h4>
                                            <input type="hidden" id="total_compra" name="montototal">
                                        </div>
                                        <div class="text-center my-3" id="campo-saldoc" style="display:none;">
                                            <h4>Saldo pendiente: <span id="saldo_pendientec" class="font-weight-bold" style="font-size: 3rem;">0.00</span></h4>
                                        </div>
                                        <div class="form-row">
                                            <div class="col-md-8">
                                                <h4>Tipos de Pago</h4>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <h4>Monto</h4>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <?php foreach ($formaspago as $index => $opcion):
                                                if ($opcion['status'] == 1): ?>
                                                    <?php if ($opcion['cod_divisa'] == 1): ?>
                                                        <div class="col-md-8">
                                                            <div class="form-group">
                                                                <input type="text" class="form-control" value="<?= $opcion['medio_pago'] . ' - ' . $opcion['descripcion'] ?>" readonly>
                                                                <input type="hidden" name="pago[<?= $index; ?>][cod_tipo_pago]" value="<?= $opcion['cod_tipo_pago']; ?>">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <div class="input-group">
                                                                    <div class="input-group-append">
                                                                        <span class="input-group-text">Bs</span>
                                                                    </div>
                                                                    <input type="number" step="0.01" maxlength="12" class="form-control monto-bsc" id="monto-bsc-<?= $index; ?>" name="pago[<?= $index; ?>][monto]" placeholder="Ingrese monto" oninput="calcularTotalpagoc()">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php else: ?>
                                                        <!-- Si es otra divisa (con conversión) -->
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <input type="text" class="form-control" value="<?= $opcion['medio_pago'] . ' - ' . $opcion['descripcion']; ?>" readonly>
                                                                <input type="hidden" name="pago[<?= $index; ?>][cod_tipo_pago]" value="<?= $opcion['cod_tipo_pago']; ?>">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <div class="input-group">
                                                                    <div class="input-group-append">
                                                                        <span class="input-group-text"><?= $opcion['abreviatura_divisa']; ?></span>
                                                                    </div>
                                                                    <input type="number" step="0.01" maxlength="12" class="form-control monto-divisac" id="monto-divisac-<?= $index; ?>" placeholder="Monto en <?= $opcion['abreviatura_divisa']; ?>" oninput="calcularTotalpagoc(<?= $index; ?>)">
                                                                    <input type="hidden" class="form-control tasa-conversionc" id="tasa-conversionc-<?= $index; ?>" value="<?= $opcion['ultima_tasa']; ?>">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <div class="input-group">
                                                                    <div class="input-group-append">
                                                                        <span class="input-group-text">Bs</span>
                                                                    </div>
                                                                    <input type="number" step="0.01" maxlength="12" class="form-control monto-bsc monto-conc" id="monto-bsc-con-<?= $index; ?>" name="pago[<?= $index; ?>][monto]" placeholder="Monto en Bs" readonly>
                                                                </div>
                                                            </div>
                                                        </div>
                                                <?php endif;
                                                endif; ?>
                                            <?php endforeach; ?>
                                        </div>
                                        <div class="form-row justify-content-end">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="">Monto a pagar</label>
                                                    <div class="input-group">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">Bs</span>
                                                        </div>
                                                        <input type="number" step="0.001" class="form-control" id="monto_pagarc" name="monto_pagar" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="">Monto pagado</label>
                                                    <div class="input-group">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">Bs</span>
                                                        </div>
                                                        <input type="number" step="0.001" class="form-control" id="monto_pagadoc" name="montopagado" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-row justify-content-end">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="">Diferencia</label>
                                                    <div class="input-group">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">Bs</span>
                                                        </div>
                                                        <input type="number" step="0.001" class="form-control" id="diferenciac" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Botón para registrar vuelto-->
                                        <div class="form-row justify-content-end" id="div-boton-vueltoc" style="display: none;">
                                            <div class="col-md-4">
                                                <input type="hidden" step="0.001" name="vuelto" id="vueltoC" value="">
                                                <button type="button" class="btn btn-info btn-block" data-toggle="modal" data-target="#vueltoModalc" id="btn-registrar-vueltoc">
                                                    Registrar Vuelto
                                                </button>
                                            </div>
                                        </div>
                                        <input type="hidden" name="pagoV" id="vuelto_data" value="">
                                    </form>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                        <button type="submit" class="btn btn-success" form="pagoFormc" id="finalizarPagoBtnc" name="pagocompracuenta">Finalizar Pago</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- MODAL VUELTO COMPRA -->
                    <div class="modal fade" id="vueltoModalc" tabindex="-1" aria-labelledby="vueltoLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header bg-warning">
                                    <h5 class="modal-title" id="vueltoLabel">Registrar Vuelto</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <form id="vueltoFormc" method="post">
                                        <div class="text-center my-3">
                                            <h4>Vuelto: <span id="total-vueltoc" class="font-weight-bold" style="font-size: 3rem;">0.00</span></h4>
                                            <input type="hidden" id="vuelto_calculado" name="vuelto_calculado">
                                        </div>
                                        <div class="form-row">
                                            <?php foreach ($formaspago as $index => $opcion):
                                                if ($opcion['status'] == 1): ?>
                                                    <?php if ($opcion['cod_divisa'] == 1): ?>
                                                        <!-- Si es bolívares (sin conversión de divisas) -->
                                                        <div class="col-md-8">
                                                            <div class="form-group">
                                                                <input type="text" class="form-control" value="<?= $opcion['medio_pago'] . ' - ' . $opcion['descripcion'] ?>" readonly>
                                                                <input type="hidden" name="vuelto[<?= $index; ?>][cod_tipo_pago]" value="<?= $opcion['cod_tipo_pago']; ?>">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <div class="input-group">
                                                                    <div class="input-group-append">
                                                                        <span class="input-group-text">Bs</span>
                                                                    </div>
                                                                    <input type="number" step="0.01" maxlength="12" class="form-control monto-bsvc" id="monto-bsvc-<?= $index; ?>" name="vuelto[<?= $index; ?>][monto]" placeholder="Ingrese monto" oninput="calcularTotalvueltoc()">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php else: ?>
                                                        <!-- Si es otra divisa (con conversión) -->
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <input type="text" class="form-control" value="<?= $opcion['medio_pago'] . ' - ' . $opcion['descripcion']; ?>" readonly>
                                                                <input type="hidden" name="vuelto[<?= $index; ?>][cod_tipo_pago]" value="<?= $opcion['cod_tipo_pago']; ?>">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <div class="input-group">
                                                                    <div class="input-group-append">
                                                                        <span class="input-group-text"><?= $opcion['abreviatura_divisa']; ?></span>
                                                                    </div>
                                                                    <input type="number" step="0.01" maxlength="12" class="form-control monto-divisavc" id="monto-divisavc-<?= $index; ?>" placeholder="Monto en <?= $opcion['abreviatura_divisa']; ?>" oninput="calcularTotalvueltoc(<?= $index; ?>)">
                                                                    <input type="hidden" class="form-control tasa-conversionvc" id="tasa-conversionvc-<?= $index; ?>" value="<?= $opcion['ultima_tasa']; ?>">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <div class="input-group">
                                                                    <div class="input-group-append">
                                                                        <span class="input-group-text">Bs</span>
                                                                    </div>
                                                                    <input type="number" step="0.01" maxlength="12" class="form-control monto-bsvc monto-con-v" id="monto-bs-con-v<?= $index; ?>" name="vuelto[<?= $index; ?>][monto]" placeholder="Monto en Bs" oninput="calcularTotalvueltoc(<?= $index; ?>)" readonly>
                                                                </div>
                                                            </div>
                                                        </div>
                                                <?php endif;
                                                endif; ?>
                                            <?php endforeach; ?>
                                    </form>
                                    <div class="form-row justify-content-end">
                                        <div class="form-group">
                                            <label for="">vuelto emitido</label>
                                            <div class="input-group">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">Bs</span>
                                                </div>
                                                <input type="number" step="0.001" class="form-control" id="vuelto_pagadoc" name="vuelto_pagado" readonly>
                                                <input type="hidden" step="0.001" class="form-control" id="monto_pagarvc" name="monto_pagarv" readonly>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="">Diferencia</label>
                                            <div class="input-group">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">Bs</span>
                                                </div>
                                                <input type="number" step="0.001" class="form-control" id="diferenciavc" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                                    <button type="submit" class="btn btn-success" form="vueltoFormc" id="registrarVueltoBtnc" name="registrarvuelto">Registrar Vuelto</button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- CUENTAS POR COBRAR -->
                <div class="tab-pane fade" id="cobros">
                    <div class="table-responsive">
                        <table id="tablaCobros" class="table table-striped datatable" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>Cliente</th>
                                    <th>Total Ventas</th>
                                    <th>Importe Total</th>
                                    <th>Total Pagado</th>
                                    <th>Total Pendiente</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($cobrar as $pendiente) { ?>
                                    <tr>
                                        <td><?php echo $pendiente['cliente']; ?></td>
                                        <td><?php echo $pendiente['total_ventas']; ?></td>
                                        <td><?php echo $pendiente['total']; ?></td>
                                        <td><?php echo $pendiente['total_cobrado']; ?></td>
                                        <td><?php echo $pendiente['total_pendiente']; ?></td>
                                        <td>
                                            <button title="Ver detalles" class="btn btn-primary" data-toggle="modal" data-target="#detallemodal"
                                                data-cliente="<?php echo $pendiente["cod_cliente"]; ?>"
                                                data-nombre="<?php echo $pendiente['cliente']; ?>"
                                                data-cedula="<?php echo $pendiente['cedula_rif']; ?>">
                                                <i class="fas fa-plus"></i></button>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- =============================
                    MODAL REGISTRAR PAGOS 
                ================================== -->
                <div class="modal fade" id="pagoModalG" tabindex="-1" aria-labelledby="pagoLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header bg-success">
                                <h5 class="modal-title" id="pagoLabel">Registrar Pago</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form id="pagoFormG" method="post" action="index.php?pagina=gasto">
                                    <div class="form-row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="cod_gasto">N° de gasto</label>
                                                <input type="text" class="form-control" id="cod_gasto" name="cod_gasto" readonly>
                                                <input type="hidden" name="tipo_pago" value="gasto">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="descripcion">Descripción</label>
                                                <input type="text" class="form-control" id="descripcionG" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="fecha">Fecha de Pago</label>
                                                <input type="text" class="form-control" id="fecha_pagoG" name="fecha" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-center my-3">
                                        <h4>Total del gasto: <span id="total-pagoG" class="font-weight-bold" style="font-size: 3rem;">0.00</span></h4>
                                        <input type="hidden" id="total_gasto" name="montototal">
                                    </div>
                                    <div class="text-center my-3" id="campo-saldoG" style="display:none;">
                                        <h4>Monto pendiente: <span id="saldo_pendienteG" class="font-weight-bold" style="font-size: 3rem;">0.00</span></h4>
                                    </div>
                                    <div class="form-row">
                                        <div class="col-md-8">
                                            <h4>Tipos de Pago</h4>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <h4>Monto</h4>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <?php foreach ($formaspago as $index => $opcion):
                                            if ($opcion['status'] == 1): ?>
                                                <?php if ($opcion['cod_divisa'] == 1): ?>
                                                    <!-- Si es bolívares (sin conversión de divisas) -->
                                                    <div class="col-md-8">
                                                        <div class="form-group">
                                                            <input type="text" class="form-control" value="<?= $opcion['medio_pago'] . ' - ' . $opcion['descripcion'] ?>" readonly>
                                                            <input type="hidden" name="pago[<?= $index; ?>][cod_tipo_pago]" value="<?= $opcion['cod_tipo_pago']; ?>">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <div class="input-group">
                                                                <div class="input-group-append">
                                                                    <span class="input-group-text">Bs</span>
                                                                </div>
                                                                <input type="number" step="0.01" maxlength="12" class="form-control monto-bsG" id="monto-bsG-<?= $index; ?>" name="pago[<?= $index; ?>][monto]" placeholder="Ingrese monto" oninput="calcularTotalpagoG()">
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php else: ?>
                                                    <!-- Si es otra divisa (con conversión) -->
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <input type="text" class="form-control" value="<?= $opcion['medio_pago'] . ' - ' . $opcion['descripcion']; ?>" readonly>
                                                            <input type="hidden" name="pago[<?= $index; ?>][cod_tipo_pago]" value="<?= $opcion['cod_tipo_pago']; ?>">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <div class="input-group">
                                                                <div class="input-group-append">
                                                                    <span class="input-group-text"><?= $opcion['abreviatura_divisa']; ?></span>
                                                                </div>
                                                                <input type="number" step="0.01" maxlength="12" class="form-control monto-divisaG" id="monto-divisaG-<?= $index; ?>" placeholder="Monto en <?= $opcion['abreviatura_divisa']; ?>" oninput="calcularTotalpagoG(<?= $index; ?>)">
                                                                <input type="hidden" class="form-control tasa-conversionG" id="tasa-conversionG-<?= $index; ?>" value="<?= $opcion['ultima_tasa']; ?>">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <div class="input-group">
                                                                <div class="input-group-append">
                                                                    <span class="input-group-text">Bs</span>
                                                                </div>
                                                                <input type="number" step="0.01" maxlength="12" class="form-control monto-bsG monto-conG" id="monto-bsG-con-<?= $index; ?>" name="pago[<?= $index; ?>][monto]" placeholder="Monto en Bs" readonly>
                                                            </div>
                                                        </div>
                                                    </div>
                                            <?php endif;
                                            endif; ?>
                                        <?php endforeach; ?>
                                    </div>
                                    <div class="form-row justify-content-end">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="">Monto a pagar</label>
                                                <div class="input-group">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">Bs</span>
                                                    </div>
                                                    <input type="number" step="0.001" class="form-control" id="monto_pagarG" name="monto_pagar" readonly>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="">Monto pagado</label>
                                                <div class="input-group">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">Bs</span>
                                                    </div>
                                                    <input type="number" step="0.001" class="form-control" id="monto_pagadoG" name="montopagado" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-row justify-content-end">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="">Diferencia</label>
                                                <div class="input-group">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">Bs</span>
                                                    </div>
                                                    <input type="number" step="0.001" class="form-control" id="diferenciaG" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-row justify-content-end" id="div-boton-vueltoG" style="display: none;">
                                        <div class="col-md-4">
                                            <input type="hidden" step="0.001" name="vuelto" id="vueltoG" value="">
                                            <button type="button" class="btn btn-info btn-block" data-toggle="modal" data-target="#vueltoModalG" id="btn-registrar-vueltoG" title="Registrar vuelto a recibir">
                                                Registrar Vuelto
                                            </button>
                                        </div>
                                    </div>
                                    <input type="hidden" name="pagoV" id="pagoV" value="">
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-success" form="pagoFormG" id="finalizarPagoG" name="pagogastocuenta">Finalizar Pago</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="vueltoModalG" tabindex="-1" aria-labelledby="vueltoGLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header bg-warning">
                                <h5 class="modal-title" id="vueltoGLabel">Registrar Vuelto</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form id="vueltoFormG" method="post">
                                    <div class="text-center my-3">
                                        <h4>Vuelto: <span id="total-vueltoG" class="font-weight-bold" style="font-size: 3rem;">0.00</span></h4>
                                    </div>
                                    <div class="form-row">
                                        <?php foreach ($formaspago as $index => $opcion):
                                            if ($opcion['status'] == 1): ?>
                                                <?php if ($opcion['cod_divisa'] == 1): ?>
                                                    <!-- Si es bolívares (sin conversión de divisas) -->
                                                    <div class="col-md-8">
                                                        <div class="form-group">
                                                            <input type="text" class="form-control" value="<?= $opcion['medio_pago'] . ' - ' . $opcion['descripcion'] ?>" readonly>
                                                            <input type="hidden" name="vuelto[<?= $index; ?>][cod_tipo_pago]" value="<?= $opcion['cod_tipo_pago']; ?>">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <div class="input-group">
                                                                <div class="input-group-append">
                                                                    <span class="input-group-text">Bs</span>
                                                                </div>
                                                                <input type="number" step="0.01" maxlength="12" class="form-control monto-bsvG" id="monto-bsvG-<?= $index; ?>" name="vuelto[<?= $index; ?>][monto]" placeholder="Ingrese monto" oninput="calcularTotalvueltoG()">
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php else: ?>
                                                    <!-- Si es otra divisa (con conversión) -->
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <input type="text" class="form-control" value="<?= $opcion['medio_pago'] . ' - ' . $opcion['descripcion']; ?>" readonly>
                                                            <input type="hidden" name="vuelto[<?= $index; ?>][cod_tipo_pago]" value="<?= $opcion['cod_tipo_pago']; ?>">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <div class="input-group">
                                                                <div class="input-group-append">
                                                                    <span class="input-group-text"><?= $opcion['abreviatura_divisa']; ?></span>
                                                                </div>
                                                                <input type="number" step="0.01" maxlength="12" class="form-control monto-divisavG" id="monto-divisavG-<?= $index; ?>" placeholder="Monto en <?= $opcion['abreviatura_divisa']; ?>" oninput="calcularTotalvueltoG(<?= $index; ?>)">
                                                                <input type="hidden" class="form-control tasa-conversionvG" id="tasa-conversionvG-<?= $index; ?>" value="<?= $opcion['ultima_tasa']; ?>">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <div class="input-group">
                                                                <div class="input-group-append">
                                                                    <span class="input-group-text">Bs</span>
                                                                </div>
                                                                <input type="number" step="0.01" maxlength="12" class="form-control monto-bsvG monto-con-vG" id="monto-bs-con-vG<?= $index; ?>" name="vuelto[<?= $index; ?>][monto]" placeholder="Monto en Bs" oninput="calcularTotalvueltoG(<?= $index; ?>)" readonly>
                                                            </div>
                                                        </div>
                                                    </div>
                                            <?php endif;
                                            endif; ?>
                                        <?php endforeach; ?>
                                </form>
                                <div class="form-row justify-content-end">
                                    <div class="form-group">
                                        <label for="">vuelto emitido</label>
                                        <div class="input-group">
                                            <div class="input-group-append">
                                                <span class="input-group-text">Bs</span>
                                            </div>
                                            <input type="number" step="0.001" class="form-control" id="vuelto_pagadoG" name="vuelto_pagado" readonly>
                                            <input type="hidden" step="0.001" class="form-control" id="monto_pagarvg" name="montopagadoV" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="">Diferencia</label>
                                        <div class="input-group">
                                            <div class="input-group-append">
                                                <span class="input-group-text">Bs</span>
                                            </div>
                                            <input type="number" step="0.001" class="form-control" id="diferenciavG" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                                <button type="submit" class="btn btn-success" form="vueltoFormG" id="registrarVueltoG" name="registrarvuelto">Registrar Vuelto</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>



            <!-- MODAL DETALLES CUENTAS POR COBRAR-->
            <div class="modal fade" id="detallemodal" tabindex="-1" aria-labelledby="detalleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="detalleModalLabel">Cuentas por Cobrar</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="card">
                                <div class="card-header">
                                    <div class="form-row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="nombre">Cliente:</label>
                                                <input type="text" class="form-control" id="nombreC" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="cedula">Cedula o Rif</label>
                                                <input type="text" class="form-control" id="cedulaC" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table id="tablaDetalles" class="table table-bordered table-striped table-hover" style="width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th>Codigo</th>
                                                    <th>Fecha</th>
                                                    <th>Importe Total</th>
                                                    <th>Monto Pagado</th>
                                                    <th>Saldo Pendiente</th>
                                                    <th>Fecha de vencimiento</th>
                                                    <th>Días Restantes</th>
                                                    <th>Status</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody id="detalleBody">

                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- MODAL PAGO RECIBIDO (VENTA)-->
            <div class="modal fade" id="pagoModal" tabindex="-1" aria-labelledby="pagoLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-success">
                            <h5 class="modal-title" id="pagoLabel">Registrar Pago</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="pagoForm" method="post" action="index.php?pagina=venta">
                                <div class="form-row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="nro_venta">Nro de Venta</label>
                                            <input type="text" class="form-control" id="nro-venta" name="nro_venta" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="nombre_cliente">Nombre del Cliente</label>
                                            <input type="text" class="form-control" id="nombre_cliente" name="nombre_cliente" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="fecha_venta">Fecha de Pago</label>
                                            <input type="text" class="form-control" id="fecha_pago" name="fecha_pago" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-center my-3">
                                    <h4>Total de venta: <span id="total-pago" class="font-weight-bold" style="font-size: 3rem;">0.00</span></h4>
                                </div>
                                <div class="text-center my-3" id="campo-saldo" style="display:none;">
                                    <h4>Saldo pendiente: <span id="saldo_pendiente" class="font-weight-bold" style="font-size: 3rem;">0.00</span></h4>
                                </div>
                                <div class="form-row">
                                    <div class="col-md-8">
                                        <h4>Tipos de Pago</h4>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <h4>Monto</h4>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <?php foreach ($opciones as $index => $opcion):
                                        if ($opcion['status'] == 1): ?>
                                            <?php if ($opcion['cod_divisa'] == 1): ?>
                                                <div class="col-md-8">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control" value="<?= $opcion['medio_pago'] . ' - ' . $opcion['descripcion'] ?>" readonly>
                                                        <input type="hidden" name="pago[<?= $index; ?>][cod_tipo_pago]" value="<?= $opcion['cod_tipo_pago']; ?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <div class="input-group">
                                                            <div class="input-group-append">
                                                                <span class="input-group-text">Bs</span>
                                                            </div>
                                                            <input type="number" step="0.01" maxlength="12" class="form-control monto-bs" id="monto-bs-<?= $index; ?>" name="pago[<?= $index; ?>][monto]" placeholder="Ingrese monto" oninput="calcularTotalpagoV()">
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php else: ?>
                                                <!-- Si es otra divisa (con conversión) -->
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control" value="<?= $opcion['medio_pago'] . ' - ' . $opcion['descripcion']; ?>" readonly>
                                                        <input type="hidden" name="pago[<?= $index; ?>][cod_tipo_pago]" value="<?= $opcion['cod_tipo_pago']; ?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <div class="input-group">
                                                            <div class="input-group-append">
                                                                <span class="input-group-text"><?= $opcion['abreviatura_divisa']; ?></span>
                                                            </div>
                                                            <input type="number" step="0.01" maxlength="12" class="form-control monto-divisa" id="monto-divisa-<?= $index; ?>" placeholder="Monto en <?= $opcion['abreviatura_divisa']; ?>" oninput="calcularTotalpagoV(<?= $index; ?>)">
                                                            <input type="hidden" class="form-control tasa-conversion" id="tasa-conversion-<?= $index; ?>" value="<?= $opcion['ultima_tasa']; ?>">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <div class="input-group">
                                                            <div class="input-group-append">
                                                                <span class="input-group-text">Bs</span>
                                                            </div>
                                                            <input type="number" step="0.01" maxlength="12" class="form-control monto-bs monto-con" id="monto-bs-con-<?= $index; ?>" name="pago[<?= $index; ?>][monto]" placeholder="Monto en Bs" readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                        <?php endif;
                                        endif; ?>
                                    <?php endforeach; ?>
                                </div>
                                <div class="form-row justify-content-end">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="">Monto a pagar</label>
                                            <div class="input-group">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">Bs</span>
                                                </div>
                                                <input type="number" step="0.001" class="form-control" id="monto_pagar" name="monto_pagar" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="">Monto pagado</label>
                                            <div class="input-group">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">Bs</span>
                                                </div>
                                                <input type="number" step="0.001" class="form-control" id="monto_pagado" name="monto_pagado" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-row justify-content-end">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="">Diferencia</label>
                                            <div class="input-group">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">Bs</span>
                                                </div>
                                                <input type="number" step="0.001" class="form-control" id="diferencia" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-row justify-content-end" id="div-boton-vuelto" style="display: none;">
                                    <div class="col-md-4">
                                        <button type="button" class="btn btn-info btn-block" data-toggle="modal" data-target="#vueltoModal" id="btn-registrar-vuelto">
                                            Registrar Vuelto
                                        </button>
                                    </div>
                                </div>
                                <input type="hidden" name="vuelto_data" id="vuelto_data" value="">
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-success" form="pagoForm" id="finalizarPagoBtn" name="finalizarpcuentas">Finalizar Pago</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- MODAL VUELTO VENTA-->
            <div class="modal fade" id="vueltoModal" tabindex="-1" aria-labelledby="vueltoLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-warning">
                            <h5 class="modal-title" id="vueltoLabel">Registrar Vuelto</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="vueltoForm" method="post">
                                <div class="text-center my-3">
                                    <h4>Vuelto: <span id="total-vuelto" class="font-weight-bold" style="font-size: 3rem;">0.00</span></h4>
                                    <input type="hidden" id="vuelto_calculado" name="vuelto_calculado">
                                </div>
                                <div class="form-row">
                                    <?php foreach ($opciones as $index => $opcion):
                                        if ($opcion['status'] == 1): ?>
                                            <?php if ($opcion['cod_divisa'] == 1): ?>
                                                <div class="col-md-8">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control" value="<?= $opcion['medio_pago'] . ' - ' . $opcion['descripcion'] ?>" readonly>
                                                        <input type="hidden" name="vuelto[<?= $index; ?>][cod_tipo_pago]" value="<?= $opcion['cod_tipo_pago']; ?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <div class="input-group">
                                                            <div class="input-group-append">
                                                                <span class="input-group-text">Bs</span>
                                                            </div>
                                                            <input type="number" step="0.01" maxlength="12" class="form-control monto-bsv" id="monto-bsv-<?= $index; ?>" name="vuelto[<?= $index; ?>][monto]" placeholder="Ingrese monto" oninput="calcularTotalvueltoV()">
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php else: ?>
                                                <!-- Si es otra divisa (con conversión) -->
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control" value="<?= $opcion['medio_pago'] . ' - ' . $opcion['descripcion']; ?>" readonly>
                                                        <input type="hidden" name="vuelto[<?= $index; ?>][cod_tipo_pago]" value="<?= $opcion['cod_tipo_pago']; ?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <div class="input-group">
                                                            <div class="input-group-append">
                                                                <span class="input-group-text"><?= $opcion['abreviatura_divisa']; ?></span>
                                                            </div>
                                                            <input type="number" step="0.01" maxlength="12" class="form-control monto-divisav" id="monto-divisav-<?= $index; ?>" placeholder="Monto en <?= $opcion['abreviatura_divisa']; ?>" oninput="calcularTotalvueltoV(<?= $index; ?>)">
                                                            <input type="hidden" class="form-control tasa-conversionv" id="tasa-conversionv-<?= $index; ?>" value="<?= $opcion['ultima_tasa']; ?>">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <div class="input-group">
                                                            <div class="input-group-append">
                                                                <span class="input-group-text">Bs</span>
                                                            </div>
                                                            <input type="number" step="0.01" maxlength="12" class="form-control monto-bsv monto-con-v" id="monto-bs-con-v<?= $index; ?>" name="vuelto[<?= $index; ?>][monto]" placeholder="Monto en Bs" oninput="calcularTotalvueltoV(<?= $index; ?>)" readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                        <?php endif;
                                        endif; ?>
                                    <?php endforeach; ?>
                            </form>
                            <div class="form-row justify-content-end">
                                <div class="form-group">
                                    <label for="">vuelto emitido</label>
                                    <div class="input-group">
                                        <div class="input-group-append">
                                            <span class="input-group-text">Bs</span>
                                        </div>
                                        <input type="number" step="0.001" class="form-control" id="vuelto_pagado" name="vuelto_pagado" readonly>
                                        <input type="hidden" step="0.001" class="form-control" id="monto_pagarv" name="monto_pagarv" readonly>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="">Diferencia</label>
                                    <div class="input-group">
                                        <div class="input-group-append">
                                            <span class="input-group-text">Bs</span>
                                        </div>
                                        <input type="number" step="0.001" class="form-control" id="diferenciav" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-success" form="vueltoForm" id="registrarVueltoBtn" name="registrarvuelto">Registrar Vuelto</button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
</div>
</section>
</div>

<?php if (isset($registrarPC)): ?>
    <script>
        Swal.fire({
            title: '<?php echo $registrarPC["title"]; ?>',
            text: '<?php echo $registrarPC["message"]; ?>',
            icon: '<?php echo $registrarPC["icon"]; ?>',
            confirmButtonText: 'Ok'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location = 'cuentaspend';
            }
        });
    </script>
<?php endif; ?>
<?php if (isset($registrarp)): ?>
    <script>
        Swal.fire({
            title: '<?php echo $registrarp["title"]; ?>',
            text: '<?php echo $registrarp["message"]; ?>',
            icon: '<?php echo $registrarp["icon"]; ?>',
            confirmButtonText: 'Ok'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location = 'cuentaspend';
            }
        });
    </script>
<?php endif; ?>
<?php
if (isset($registrarPG)): ?>
    <script>
        Swal.fire({
            title: '<?php echo $registrarPG["title"]; ?>',
            text: '<?php echo $registrarPG["message"]; ?>',
            icon: '<?php echo $registrarPG["icon"]; ?>',
            confirmButtonText: 'Ok'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location = 'cuentaspend';
            }
        });
    </script>
<?php endif; ?>

<?php
    $disabled = !isset($_SESSION['rif']) ? 'disabled' : '';
    $title = !isset($_SESSION['rif']) 
        ? 'No se puede generar el reporte, debes registrar la informacion de la empresa' 
        : 'Exportar detalle PDF';
?>
<script>
    const Disabled = <?= json_encode($disabled) ?>;
    const Title = <?= json_encode($title) ?>;
    const puedeAgregarPago = <?php echo !empty($_SESSION["permisos"]["cuentas_pendiente"]["registrar"]) ? 'true' : 'false'; ?>;
</script>
<script src="vista/dist/js/modulos-js/cuentaspend.js"></script>