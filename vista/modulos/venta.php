<?php require_once 'controlador/venta.php'; ?>
<!-- MÓDULO TRANSACCIONAL-->
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Ventas</h1>
                    <p>En esta sección se puede gestionar las ventas de productos.</p>
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
                            <?php if (!empty($_SESSION["permisos"]["venta"]["registrar"])): ?>
                                <!-- Botón para abrir el modal -->
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#ventaModal"> Registrar venta </button>
                            <?php endif; ?>
                        </div>

                        <div class="card-body">
                            <!-- Tabla de ventas -->
                            <div class="table-responsive">
                                <table id="tablaventa" class="table table-bordered table-striped datatable1" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>Nro. de Venta</th>
                                            <th>Cliente</th>
                                            <th>Fecha de emision</th>
                                            <th>Monto</th>
                                            <th>Status</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($consulta as $venta) { ?>
                                            <tr>
                                                <td><?php echo $venta['cod_venta'] ?></td>
                                                <td><?php echo $venta['nombre'] . " " . $venta['apellido'] ?></td>
                                                <td><?php echo $venta['fecha'] ?></td>
                                                <td><?php echo $venta['total'] ?></td>
                                                <td>
                                                    <?php if ($venta['status'] == 1): ?>
                                                        <span class="badge bg-secondary">Pendiente</span>
                                                        <?php if (!empty($_SESSION["permisos"]["venta"]["registrar"])): ?>
                                                            <button name="abono" title="Pagar" class="btn btn-primary btn-sm editar" data-toggle="modal" data-target="#pagoModal"
                                                                data-codventa="<?php echo $venta["cod_venta"]; ?>"
                                                                data-totalv="<?php echo $venta["total"]; ?>"
                                                                data-fecha="<?php echo $venta["fecha"]; ?>"
                                                                data-nombre="<?php echo $venta["nombre"] . " " . $venta["apellido"]; ?>">
                                                                <i class="fas fa-money-bill-wave"></i>
                                                            </button>
                                                        <?php endif; ?>
                                                    <?php elseif ($venta['status'] == 2): ?>
                                                        <span class="badge bg-warning">Pago parcial</span>
                                                        <?php if (!empty($_SESSION["permisos"]["venta"]["registrar"])): ?>
                                                            <button name="abono" title="Pagar" class="btn btn-primary btn-sm editar" data-toggle="modal" data-target="#pagoModal"
                                                                data-codventa="<?php echo $venta["cod_venta"]; ?>"
                                                                data-saldopen="<?php echo $venta["saldo_restante"]; ?>"
                                                                data-totalv="<?php echo $venta["total"]; ?>"
                                                                data-fecha="<?php echo $venta["fecha"]; ?>"
                                                                data-nombre="<?php echo $venta["nombre"] . " " . $venta["apellido"]; ?>">
                                                                <i class="fas fa-money-bill-wave"></i>
                                                            </button>
                                                        <?php endif; ?>
                                                    <?php elseif ($venta['status'] == 3): ?>
                                                        <span class="badge bg-success">Completada</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-danger">Anulada</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if ($venta['status'] != 0): 
                                                        /*if (!empty($_SESSION["permisos"]["venta"]["eliminar"])): ?>
                                                            <button name="anular" title="Anular" class="btn btn-danger btn-sm eliminar" data-toggle="modal" data-target="#anularventa"
                                                                data-codventa="<?php echo $venta["cod_venta"]; ?>"
                                                                data-status="<?php echo $venta["status"]; ?>">
                                                                <i class="fas fa-trash-alt"></i>
                                                            </button>
                                                        <?php endif; */
                                                        $disabled = !isset($_SESSION['rif']) ? 'disabled' : '';
                                                        $title = !isset($_SESSION['rif'])
                                                            ? 'No se puede generar la Nota, debes registrar la informacion de la empresa'
                                                            : 'Ver nota de entrega';?>
                                                        <button form="facturaform_<?= $venta['cod_venta']; ?>" <?= $disabled; ?> type="submit" name="imprimir" title="<?= $title; ?>" class="btn btn-primary btn-sm editar">
                                                            <i class="fas fa-file"></i>
                                                        </button>
                                                        <form id="facturaform_<?= $venta['cod_venta']; ?>" action="index.php?pagina=factura" method="post" target="_blank">
                                                            <input type="hidden" name="cod_venta" value="<?= $venta['cod_venta']; ?>">
                                                            <input type="hidden" name="total" value="<?= $venta['total']; ?>">
                                                            <input type="hidden" name="fecha" value="<?= $venta['fecha']; ?>">
                                                            <input type="hidden" name="cliente" value="<?= $venta['nombre'] . " " . $venta['apellido']; ?>">
                                                            <input type="hidden" name="cedula" value="<?= $venta['cedula_rif']; ?>">
                                                            <input type="hidden" name="direccion" value="<?= $venta['direccion']; ?>">
                                                            <input type="hidden" name="telefono" value="<?= $venta['telefono']; ?>">
                                                        </form>
                                                    <?php else: ?>
                                                        <button title="Anular" class="btn btn-danger btn-sm disabled">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                        <button title="Ver factura" class="btn btn-primary btn-sm disabled">
                                                            <i class="fas fa-file"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                </td>
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
    </section>
</div>


<!-- Modal de Venta con búsqueda interactiva -->
<div class="modal fade" id="ventaModal" tabindex="-1" aria-labelledby="ventaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ventaModalLabel">Registrar Venta</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="ventamodal" method="post">
                    <div class="container-fluid">
                        <div class="row">
                            <!-- Información del Cliente y Nro de Venta -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="cedula-rif">Cédula/RIF</label>
                                    <div class="row">
                                        <div class="col-md-10">
                                            <input type="text" class="form-control form-control-sm" id="cedula-rif" name="cedula-rif" placeholder="Cédula o RIF">
                                            <div class="invalid-feedback" style="display: none; position: absolute; top: 100%; margin-top: 2px; width: calc(100% - 2px); font-size: 0.875em; text-align: left;"></div>
                                            <input type="hidden" id="cod_cliente" name="cod_cliente" required>
                                        </div>
                                        <div class="col-md-1">
                                        <button type="button" class="btn btn-outline-secondary btn-sm" data-toggle="modal" data-target="#modalRegistrarClientes">
                                            +
                                        </button>
                                    </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="nombre-cliente">Numero de telefono</label>
                                    <input type="text" class="form-control form-control-sm" id="numero-cliente" name="numero-cliente" placeholder="telefono del cliente" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nombre-cliente">Nombre del Cliente</label>
                                    <input type="text" class="form-control form-control-sm" id="nombre-cliente" name="nombre-cliente" placeholder="Nombre del cliente" readonly>
                                </div>
                                <div class="form-row align-items-end">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <?php
                                            if (!empty($consulta)) {
                                                $ultimo = end($consulta);
                                                $nueva = $ultimo['cod_venta'] + 1;
                                            } else {
                                                $nueva = 1;
                                            }
                                            ?>
                                            <label for="numero">Nro Venta</label>
                                            <input type="text" class="form-control form-control-sm" id="nro_venta" value="<?= $nueva ?>" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="fecha-hora">Fecha y Hora</label>
                                            <input type="text" class="form-control form-control-sm" id="fecha-hora" name="fecha_hora" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="condicion_pago">Condición de Pago</label>
                                    <select class="form-control form-control-sm" id="condicion_pago" name="condicion" required onchange="mostrarFechaVencimiento()">
                                        <option value="" selected disabled>Seleccione una opción</option>
                                        <option value="1">Contado</option>
                                        <option value="2">Crédito</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group" id="div_fecha_vencimiento" style="display: block;">
                                    <label for="fecha_vencimiento">Fecha de Vencimiento</label>
                                    <input type="date" class="form-control form-control-sm" id="fecha_vencimiento" name="fecha_v">
                                </div>
                            </div>
                        </div>

                        <!-- TABLA para escritorio (oculta en móviles) -->
                        <div class="table-responsive d-none d-md-block">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Código</th>
                                        <th>Producto</th>
                                        <th>Cantidad</th>
                                        <th>Precio Unitario</th>
                                        <th>Total</th>
                                        <th> </th>
                                    </tr>
                                </thead>
                                <tbody id="ventaProductosBody">
                                    <!-- Filas en escritorio -->
                                </tbody>
                            </table>
                        </div>

                        <!-- CONTENEDOR alternativo para tarjetas en móviles -->
                        <div id="ventaProductosBodyMobile" class="d-block d-md-none"></div>

                            <!-- Botón para agregar más filas de productos -->
                            <button type="button" class="btn btn-success" onclick="agregarFila()">Agregar Fila</button>
                            <div class="card card-outline card-primary float-right" style="width: 300px;">
                                <div class="card-body">
                                    <p>Subtotal: Bs <span id="subtotal" class="text-bold">0.00</span></p>
                                    <input type="hidden" id="subtotal_i" name="subtotal">
                                    <p>Exento: Bs <span id="exento" class="text-bold">0.00</span></p>
                                    <p>Base imponible: Bs <span id="base-imponible" class="text-bold">0.00</span></p>
                                    <p>IVA (16%): Bs <span id="iva" class="text-bold">0.00</span></p>
                                    <input type="hidden" id="impuesto" name="impuesto">
                                    <p class="text-bold">TOTAL: Bs <span id="total-span" class="text-bold">0.00</span></p>
                                    <input type="hidden" id="total-general" name="total_general">
                                </div>
                            </div>
                        
                    </div>
                    <input type="hidden" name="registrarv" value="1">
            </div>
            </form>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="submit" form="ventamodal" class="btn btn-primary" id="realizarVentaBtn">Realizar Venta</button>
            </div>
        </div>
    </div>
</div>

<!-- =======================
MODAL REGISTRAR PAGO
============================= -->
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
                <form id="pagoForm" method="post">
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
                                                <input type="number" step="0.01" maxlength="12" class="form-control monto-bs" id="monto-bs-<?= $index; ?>" name="pago[<?= $index; ?>][monto]" placeholder="Ingrese monto" oninput="calcularTotalpago()">
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
                                                <input type="number" step="0.01" maxlength="12" class="form-control monto-divisa" id="monto-divisa-<?= $index; ?>" placeholder="Monto en <?= $opcion['abreviatura_divisa']; ?>" oninput="calcularTotalpago(<?= $index; ?>)">
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
                    <!-- Botón para registrar vuelto (inicialmente oculto, se mostrará con JS si la diferencia es negativa) -->
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
                <button type="submit" class="btn btn-success" form="pagoForm" id="finalizarPagoBtn" name="finalizarp">Finalizar Pago</button>
            </div>
        </div>
    </div>
</div>
<?php if (isset($registrarp)): ?>
    <script>
        Swal.fire({
            title: '<?php echo $registrarp["title"]; ?>',
            text: '<?php echo $registrarp["message"]; ?>',
            icon: '<?php echo $registrarp["icon"]; ?>',
            confirmButtonText: 'Ok'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location = 'venta';
            }
        });
    </script>
<?php endif; ?>

<!-- =======================
MODAL REGISTRAR VUELTO 
============================= -->
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
                                                <input type="number" step="0.01" maxlength="12" class="form-control monto-bsv" id="monto-bsv-<?= $index; ?>" name="vuelto[<?= $index; ?>][monto]" placeholder="Ingrese monto" oninput="calcularTotalvuelto()">
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
                                                <input type="number" step="0.01" maxlength="12" class="form-control monto-divisav" id="monto-divisav-<?= $index; ?>" placeholder="Monto en <?= $opcion['abreviatura_divisa']; ?>" oninput="calcularTotalvuelto(<?= $index; ?>)">
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
                                                <input type="number" step="0.01" maxlength="12" class="form-control monto-bsv monto-con-v" id="monto-bs-con-v<?= $index; ?>" name="vuelto[<?= $index; ?>][monto]" placeholder="Monto en Bs" oninput="calcularTotalvuelto(<?= $index; ?>)" readonly>
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



<!-- =======================
MODAL REGISTRAR CLIENTES 
============================= -->


<div class="modal fade" id="modalRegistrarClientes" tabindex="-1" aria-labelledby="modalRegistrarClientesLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="clientesModalLabel">Registrar cliente</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form id="formRegistrarClientes" action="index.php?pagina=clientes" method="post">
                    <div class="form-group">
                        <label for="cedula_rif">Cédula o Rif:</label>
                        <input type="text" class="form-control" name="cedula_rif" id="cedula_rif" placeholder="Ingrese la cédula" required>
                        <input type="hidden" name="vista" value="venta">

                        <label for="nombre">Nombre:</label>
                        <input type="text" class="form-control" name="nombre" placeholder="Ingrese el nombre" required>

                        <label for="apellido">Apellido:</label>
                        <input type="text" class="form-control" name="apellido" placeholder="Ingrese el apellido" required>

                        <label for="telefono">Teléfono:</label>
                        <input type="tel" class="form-control" name="telefono" placeholder="Ingrese el teléfono">

                        <label for="email">Email:</label>
                        <input type="email" class="form-control" name="email" placeholder="Ingrese el correo electrónico">

                        <label for="direccion">Direccion:</label>
                        <textarea class="form-control" name="direccion" placeholder="Ingrese la dirección de vivienda"></textarea>
                    </div>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="submit" class="btn btn-primary" name="guardar">Guardar</button>
            </div>
            </form>
        </div>
    </div>
</div>

<?php if (isset($registrar)): ?>
    <script>
        Swal.fire({
            title: '<?php echo $registrar["title"]; ?>',
            text: '<?php echo $registrar["message"]; ?>',
            icon: '<?php echo $registrar["icon"]; ?>',
            confirmButtonText: 'Ok'
        }).then((result) => {
            if (result.isConfirmed) {
                localStorage.setItem('abrirModal', 'true');
                window.location = 'venta';
            }
        });
    </script>
<?php endif; ?>
<script>
    $('#cedula_rif').blur(function(e) {
        var buscar = $('#cedula_rif').val();
        $.post('index.php?pagina=clientes', {
            buscar
        }, function(response) {
            if (response != '') {
                Swal.fire({
                    title: 'Esta cedula ya se encuentra registrada',
                    icon: 'warning',
                    confirmButtonText: 'Ok'
                });
            }
        }, 'json');
    });
    $(document).ready(function() {
        // Verifica si el valor 'abrirModal' está en localStorage
        if (localStorage.getItem('abrirModal') === 'true') {
            $('#ventaModal').modal('show');
            localStorage.removeItem('abrirModal');
        }
    });
</script>
<!-- =======================
FIN REGISTRAR CLIENTES
============================= -->

<!-- =======================
MODAL CONFIRMAR ELIMINAR 
============================= -->
<div class="modal fade" id="anularventa" tabindex="-1" aria-labelledby="anularventaLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title" id="anularventaLabel">Confirmar Anulación</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="anumodal" method="post">
                    <p>¿Está seguro que desea anular la venta nro: <span id="codv"></span>?</p>
                    <input type="hidden" id="cventa" name="cventa">
                    <input type="hidden" id="statusv" name="statusv">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="submit" form="anumodal" class="btn btn-danger" id="confirmDelete" name="anular">Anular</button>
            </div>
        </div>
    </div>
</div>
<?php if (isset($anular)): ?>
    <script>
        Swal.fire({
            title: '<?php echo $anular["title"]; ?>',
            text: '<?php echo $anular["message"]; ?>',
            icon: '<?php echo $anular["icon"]; ?>',
            confirmButtonText: 'Ok'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location = 'venta';
            }
        });
    </script>
<?php endif; ?>

<script>
    function mostrarFechaVencimiento() {
        var condicionPago = document.getElementById("condicion_pago").value;
        var divFechaVencimiento = document.getElementById("div_fecha_vencimiento");

        if (condicionPago === "2") {
            divFechaVencimiento.style.display = "block";
            document.getElementById("fecha_vencimiento").required = true;
        } else {
            //divFechaVencimiento.style.display = "none";
            //document.getElementById("fecha_vencimiento").required = false;
            //document.getElementById("fecha_vencimiento").value = "";
        }
    }
</script>

<script src="vista/dist/js/modulos-js/ventas.js"></script>
<!--<script src="vista/dist/js/modulos-js/clientes.js"></script>-->