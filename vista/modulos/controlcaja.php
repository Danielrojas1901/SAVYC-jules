<?php require_once 'controlador/controlcaja.php' ?>
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-9">
                    <h1>Apertura y Cierre de Caja</h1>
                    <p>Administra aquí las aperturas, cierres y el historial de actividad de cada caja.</p>
                </div>
            </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5>Cajas activas</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive ">
                                <table id="caja" class="table table-bordered table-striped datatable text-center align-middle" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>Código</th>
                                            <th>Nombre</th>
                                            <th>Divisa</th>
                                            <th>Control de Caja</th>
                                            <th>Movimientos</th>
                                            <th>Status</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($listado as $dato) { ?>
                                            <tr>
                                                <td><?php echo $dato['cod_caja'] ?></td>
                                                <td><?php echo $dato['nombre'] ?></td>
                                                <td><?php echo $dato['divisa'] ?></td>
                                                <td>
                                                    <?php if ($dato['status_control'] == 1): ?>
                                                        <!-- BOTÓN DE CIERRE -->
                                                        <button class="btn btn-danger btn-sm btn-cierre" title="Cerrar caja"
                                                            data-toggle="modal" data-target="#modalCierreCaja"
                                                            data-cod="<?php echo $dato['cod_caja'] ?>"
                                                            data-codigoc="<?php echo $dato['cod_control'] ?>"
                                                            data-nombre="<?php echo $dato['nombre'] ?>"
                                                            data-divisa="<?php echo $dato['divisa'] ?>"
                                                            data-saldo="<?php echo $dato['saldo'] ?>">
                                                            Cerrar caja
                                                        </button>
                                                    <?php else: ?>
                                                        <!-- BOTÓN DE APERTURA -->
                                                        <?php
                                                        $tieneTipoPago = $dato['tiene_tipo_pago'];
                                                        $disabled = ($tieneTipoPago == 0) ? 'disabled' : '';
                                                        $title = ($tieneTipoPago == 0)
                                                            ? 'No puedes abrir esta caja porque no tiene tipos de pago configurados'
                                                            : 'Apertura de caja';
                                                        ?>
                                                        <button name="abrir_caja"
                                                            <?php echo $disabled; ?>
                                                            class="btn btn-success btn-sm movimientos"
                                                            title="<?php echo $title; ?>"
                                                            data-toggle="modal" data-target="#modalAperturaCaja"
                                                            data-nombre="<?php echo $dato['nombre'] ?>"
                                                            data-divisa="<?php echo $dato['divisa'] ?>"
                                                            data-saldo="<?php echo $dato['saldo'] ?>"
                                                            data-cod="<?php echo $dato['cod_caja'] ?>">
                                                            Abrir caja
                                                        </button>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <button class="btn btn-primary btn-sm ver-mov" title="Ver movimientos"
                                                        data-toggle="modal" data-target="#movimientosActuales"
                                                        data-nombre="<?php echo $dato['nombre'] ?>"
                                                        data-divisa="<?php echo $dato['divisa'] ?>"
                                                        data-saldo="<?php echo $dato['saldo'] ?>"
                                                        data-cod="<?php echo $dato['cod_caja'] ?>"
                                                        data-fecha="<?php echo $dato['fecha_apertura'] ?>"
                                                        data-codigoc="<?php echo $dato['cod_control'] ?>"
                                                        <?php echo ($dato['status_control'] != 1) ? 'disabled' : '';
                                                        ?>>
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                </td>
                                                <td>
                                                    <?= ($dato['status_control'] == 1) ?  '<span class="badge bg-success">Abierto</span>' : '<span class="badge bg-danger">Cerrado</span>' ?>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                            </div>
                            </table>
                        </div>
                    </div>

                    <!-- MODAL APERTURA DE CAJA -->
                    <div class="modal fade" id="modalAperturaCaja" tabindex="-1" role="dialog" aria-labelledby="tituloModalApertura" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <form id="form-apertura-caja" method="post">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="tituloModalApertura">Apertura de Caja</h5>
                                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="hidden" id="cod_caja_apertura" name="cod_caja">
                                        <div class="row">
                                            <div class="form-group col-md-4">
                                                <label>Nombre de Caja</label>
                                                <input type="text" id="nombrea" class="form-control" readonly>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label>Divisa</label>
                                                <input type="text" id="divisaa" class="form-control" readonly>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label>Saldo actual</label>
                                                <input type="text" id="saldoa" class="form-control" readonly>
                                                <input type="hidden" id="saldoa_hidden" name="saldoa">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label>Fecha y Hora de Apertura</label>
                                            <input type="datetime-local" name="fecha_apertura" id="fecha_apertura" class="form-control" required readonly>
                                            <div class="invalid-feedback">Ingrese la fecha y hora.</div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                        <button type="submit" name="abrir_caja" class="btn btn-success">Abrir Caja</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- MODAL MOVIMIENTOS ACTUALES -->
                    <div class="modal fade" id="movimientosActuales" tabindex="-1">
                        <div class="modal-dialog modal-xl">
                            <div class="modal-content">
                                <div class="modal-header bg-primary text-white">
                                    <h5 class="modal-title">
                                        Movimientos de caja<span id="fechaMovimiento"></span>
                                    </h5>
                                    <button type="button" class="close text-white" data-dismiss="modal">
                                        <span>&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="card">
                                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                            <div>
                                                <b>Nombre:</b> <span id="nombreCajaMov"></span><br>
                                                <b>Divisa:</b> <span id="divisaCajaMov"></span>
                                                <input type="hidden" id="codigo">
                                                <input type="hidden" id="codigoc">
                                                <input type="hidden" id="fechaa">

                                            </div>
                                            <div class="text-right">
                                                <b>Saldo actual:</b>
                                                <b><span id="saldoCajaMov" style="font-size:200%;"></span></b>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-striped datatable text-center align-middle" style="width: 100%;">
                                                    <thead>
                                                        <tr>
                                                            <th>Fecha y Hora</th>
                                                            <th>Origen</th>
                                                            <th>Movimiento</th>
                                                            <th>Referencia</th>
                                                            <th>Monto</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="tablaMovimientosDia">
                                                        <!-- Contenido generado dinámicamente por JavaScript -->
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- MODAL CIERRE DE CAJA -->
                    <div class="modal fade" id="modalCierreCaja" tabindex="-1" role="dialog" aria-labelledby="tituloModalCierre" aria-hidden="true">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <form id="form-cierre-caja" method="post">
                                    <div class="modal-header bg-danger text-white">
                                        <h5 class="modal-title" id="tituloModalCierre">Cierre de Caja</h5>
                                        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                                    </div>
                                    <div class="modal-body">

                                        <input type="hidden" id="cod_caja_cierre" name="cod_caja">
                                        <input type="hidden" id="cod_control_cierre" name="cod_control">

                                        <div class="row mb-2">
                                            <div class="col-md-6"><b>Nombre:</b> <span id="nombreCierre"></span></div>
                                            <div class="col-md-6"><b>Divisa:</b> <span id="divisaCierre"></span></div>
                                            <!--<div class="col-md-4"><b>Saldo sistema:</b> <span id="saldoSistema" class="text-primary"></span></div>-->
                                        </div>
                                        <div id="resumenPagos">
                                            <!-- Aquí se cargará el resumen por tipo de pago -->
                                        </div>

                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <label>Monto contado físicamente</label>
                                                <input type="number" step="0.01" class="form-control" min="0" id="monto_contado" name="monto_contado" required>

                                            </div>
                                            <div class="form-group col-md-6">
                                                <label>Observación</label>
                                                <input type="text" class="form-control" name="observacion" id="observacion">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                        <button type="submit" class="btn btn-danger" id="btnConfirmarCierre" name="cerrar_caja" disabled>Cerrar Caja</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <?php if (isset($respuesta)): ?>
                        <script>
                            Swal.fire({
                                title: '<?php echo $respuesta["title"]; ?>',
                                text: '<?php echo $respuesta["message"]; ?>',
                                icon: '<?php echo $respuesta["icon"]; ?>',
                                confirmButtonText: 'Ok'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location = 'controlcaja';
                                }
                            });
                        </script>
                    <?php endif; ?>
                </div>
            </div>
    </section>
</div>


<script src="vista/dist/js/modulos-js/controlcaja.js"></script>