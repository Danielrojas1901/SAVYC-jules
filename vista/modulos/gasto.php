<?php
require_once "controlador/gasto.php";
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Gastos Fijos y Variables</h1>
                    <p>Lleva el control de los gastos de tu empresa.</p>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            
            <div class="card">
                <div class="card-header">
                    <?php if (!empty($_SESSION["permisos"]["gasto"]["registrar"])): ?>
                        <button class="btn btn-primary" data-toggle="modal" data-target="#modalRGasto">
                            Registrar gasto
                        </button>
                    <?php endif; ?>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-md-6 mb-3">
                    <div class="small-box" style="background-color: #8770fa; color: white;">
                        <div class="inner">
                            <h3 id="total-gastos">
                                <?php foreach ($totalG as $tg) { ?>
                                    <?php if ($tg['total_monto'] != 0) {
                                        echo $tg['total_monto'] . ' Bs';
                                    } else {
                                        echo '0.00 Bs';
                                    } ?>
                                <?php } ?>
                            </h3>
                            <p>Monto de Gastos Totales</p>
                        </div>
                    </div>
                    <div class="small-box bg-primary">
                        <div class="inner">
                            <h3 id="gastos-fijos">
                                <?php foreach ($totalF as $tf) {
                                    if ($tf['total_monto'] != 0) {
                                        echo $tf['total_monto'] . ' Bs';
                                    } else {
                                        echo '0.00 Bs';
                                    }
                                } ?>
                            </h3>
                            <p>Monto de Gastos Fijos</p>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 mb-3">
                    <div class="small-box bg-primary">
                        <div class="inner">
                            <h3 id="gastos-variables">
                                <?php foreach ($totalV as $tv) {
                                    if ($tv['total_monto'] != 0) {
                                        echo $tv['total_monto'] . ' Bs';
                                    } else {
                                        echo '0.00 Bs';
                                    }
                                } ?>
                            </h3>
                            <p>Monto de Gastos Variables</p>
                        </div>
                    </div>
                    <div class="small-box" style="background-color: #8770fa; color: white;">
                        <div class="inner">
                            <h3 id="total-gastos">
                                <?php foreach ($totalP as $tgp) {
                                    if ($tgp['total_monto'] != 0) {
                                        echo $tgp['total_monto'] . ' Bs';
                                    } else {
                                        echo '0.00 Bs';
                                    }
                                } ?>
                            </h3>
                            <p>Monto de Gastos Pagados</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main content -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h2 class="card-title"><b> Gastos Fijos </b></h2>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="tabla_gastos_fijos" class="table table-bordered table-striped text-center align-middle datatable1" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>Código</th>
                                            <th>Descripción</th>
                                            <th>Monto total</th>
                                            <th>Último pago</th>
                                            <th>Fecha de creación</th>
                                            <th>Fecha de vencimiento</th>
                                            <th>Status</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($gastosF)): ?>
                                            <tr>
                                                <td colspan="8" class="text-center">No hay gastos fijos</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php
                                            foreach ($gastosF as $F) {
                                                $hoy = date('Y-m-d');
                                                $claseColor = '';
                                                if ($F['fecha_vencimiento'] != null && !empty($F['dias'])) {
                                                    $fecha_base = $F['fecha_vencimiento'];
                                                    $proximoPago = $fecha_base;
                                                    while ($proximoPago < $hoy) {
                                                        $proximoPago = date('Y-m-d', strtotime($proximoPago . " +{$F['dias']} days"));
                                                    }

                                                    if ($F['status'] != 3) {
                                                        $dias_restantes = (strtotime($proximoPago) - strtotime($hoy)) / 86400;
                                                        if ($proximoPago == $hoy) {
                                                            $claseColor = 'table-warning';
                                                        } elseif ($proximoPago < $hoy) {
                                                            $claseColor = 'table-danger';
                                                        } elseif ($dias_restantes <= 3) {
                                                            $claseColor = 'table-success';
                                                        }
                                                    }
                                                } else {
                                                    $proximoPago = $F['fecha_vencimiento'];
                                                }

                                                if ($F['status'] == 3 || $F['status'] == 0) {
                                                    $proximoPago = 'Sin días restantes';
                                                    $claseColor = '';
                                                }


                                            ?>
                                                <tr class="<?php echo $claseColor ?>">
                                                    <td><?php echo $F['cod_gasto']
                                                        ?></td>
                                                    <td><?php echo $F['descripcion']
                                                        ?></td>
                                                    <td><?php echo $F['monto']
                                                        ?></td>
                                                    <td><?php echo $F['fecha']
                                                        ?></td>
                                                    <td><?php echo $F['fecha_creacion']
                                                        ?></td>
                                                    <td><?php echo $F['fecha_vencimiento'];
                                                        ?></td>
                                                    <td>
                                                        <?php if ($F['status'] == 1): ?>
                                                            <span class="badge bg-secondary">Pendiente</span>
                                                            <?php if (!empty($_SESSION["permisos"]["gasto"]["registrar"])): ?>
                                                                <button name="abono" title="Pagar" class="btn btn-primary btn-sm pagar" data-toggle="modal" data-target="#pagoModalG"
                                                                    data-cod_gasto="<?php echo $F["cod_gasto"]; ?>"
                                                                    data-montop="<?php echo ($F["total_pagos_emitidos"]); ?>"
                                                                    data-totalg="<?php echo $F["monto"]; ?>"
                                                                    data-nombre="<?php echo $F["descripcion"]; ?>">
                                                                    <i class="fas fa-money-bill-wave"></i>
                                                                </button>
                                                            <?php endif; ?>
                                                        <?php elseif ($F['status'] == 2): ?>
                                                            <span class="badge bg-warning">Pago parcial</span>
                                                            <?php if (!empty($_SESSION["permisos"]["gasto"]["registrar"])): ?>
                                                                <button name="partes" title="Pagar" class="btn btn-primary btn-sm pagar" data-toggle="modal" data-target="#pagoModalG"
                                                                    data-cod_gasto="<?php echo $F["cod_gasto"]; ?>"
                                                                    data-montop="<?php echo ($F["total_pagos_emitidos"]); ?>"
                                                                    data-totalg="<?php echo $F["monto"]; ?>"
                                                                    data-nombre="<?php echo $F["descripcion"]; ?>">
                                                                    <i class="fas fa-money-bill-wave"></i>
                                                                </button>
                                                            <?php endif; ?>
                                                        <?php elseif ($F['status'] == 3): ?>
                                                            <span class="badge bg-success">Completada</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-danger">Anulada</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php if ($F['status'] == 1): ?>
                                                            <?php if (!empty($_SESSION["permisos"]["gasto"]["editar"])): ?>
                                                                <button name="ajustar" class="btn btn-warning btn-sm editar-gasto" title="Editar" data-toggle="modal" data-target="#modificargasto"
                                                                    data-cod_gasto="<?php echo $F["cod_gasto"]; ?>"
                                                                    data-monto ="<?php echo $F["monto"]; ?>"
                                                                    data-descripcion="<?php echo $F["descripcion"]; ?>">
                                                                    <i class="fas fa-pencil-alt"></i>
                                                                </button>
                                                            <?php endif; ?>
                                                            <?php if (!empty($_SESSION["permisos"]["gasto"]["eliminar"])): ?>

                                                                <button name="confirmar" class="btn btn-danger btn-sm eliminar" title="Eliminar" data-toggle="modal" data-target="#eliminarG"
                                                                    data-cod_gasto="<?php echo $F["cod_gasto"]; ?>"
                                                                    data-descripcion="<?php echo $F['descripcion']; ?>">
                                                                    <i class="fas fa-trash-alt"></i></button>
                                                            <?php endif; ?>

                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php }
                                            ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h2 class="card-title"><b> Gastos Variables </b></h2>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="tabla_gastos_variables" class="table table-bordered table-striped text-center align-middle datatable1" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>Código</th>
                                            <th>Descripción</th>
                                            <th>Monto</th>
                                            <th>Último pago</th>
                                            <th>Fecha de creación</th>
                                            <th>Fecha de vencimiento</th>
                                            <th>Status</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($gastosV)): ?>
                                            <tr>
                                                <td colspan="8" class="text-center">No hay gastos variables</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php
                                            foreach ($gastosV as $v) {
                                                $hoy = date('Y-m-d');
                                                $proximoPago = null;;
                                                $claseColor = '';
                                                if (!empty($v['fecha_vencimiento'])) {
                                                    $proximoPago = $v['fecha_vencimiento'];
                                                    if ($v['status'] != 3) {
                                                        if ($proximoPago == $hoy) {
                                                            $claseColor = 'table-warning';
                                                        } elseif ($proximoPago < $hoy) {
                                                            $claseColor = 'table-danger';
                                                        } elseif ($proximoPago <= 3) {
                                                            $claseColor = 'table-success';
                                                        }
                                                    }
                                                } else {
                                                    $proximoPago = 'Sin días restantes';
                                                }


                                                if ($v['status'] == 3 || $v['status'] == 0) {
                                                    $proximoPago = 'Sín dias restantes';
                                                    $claseColor = '';
                                                }
                                            ?>
                                                <tr class="<?php echo $claseColor ?>">
                                                    <td><?php echo $v['cod_gasto']
                                                        ?></td>
                                                    <td><?php echo $v['descripcion']
                                                        ?></td>
                                                    <td><?php echo $v['monto']
                                                        ?></td>
                                                    <td><?php echo $v['fecha_ultimo_pago']
                                                        ?></td>
                                                    <td><?php echo $v['fecha_creacion']
                                                        ?></td>
                                                    <td><?php echo $v['fecha_vencimiento'];
                                                        ?></td>
                                                    <td>
                                                        <?php if ($v['status'] == 1): ?>
                                                            <span class="badge bg-secondary">Pendiente</span>
                                                            <?php if (!empty($_SESSION["permisos"]["gasto"]["registrar"])): ?>
                                                                <button name="abono" title="Pagar" class="btn btn-primary btn-sm pagar" data-toggle="modal" data-target="#pagoModalG"
                                                                    data-cod_gasto="<?php echo $v["cod_gasto"]; ?>"
                                                                    data-montop="<?php echo ($v["total_pagos_emitidos"]); ?>"
                                                                    data-totalg="<?php echo $v["monto"]; ?>"
                                                                    data-nombre="<?php echo $v["descripcion"]; ?>">
                                                                    <i class="fas fa-money-bill-wave"></i>
                                                                </button>
                                                            <?php endif; ?>
                                                        <?php elseif ($v['status'] == 2): ?>
                                                            <span class="badge bg-warning">Pago parcial</span>
                                                            <?php if (!empty($_SESSION["permisos"]["gasto"]["registrar"])): ?>
                                                                <button name="partes" title="Pagar" class="btn btn-primary btn-sm pagar" data-toggle="modal" data-target="#pagoModalG"
                                                                    data-cod_gasto="<?php echo $v["cod_gasto"]; ?>"
                                                                    data-montop="<?php echo ($v["total_pagos_emitidos"]); ?>"
                                                                    data-totalg="<?php echo $v["monto"]; ?>"
                                                                    data-nombre="<?php echo $v["descripcion"]; ?>">
                                                                    <i class="fas fa-money-bill-wave"></i>
                                                                </button>
                                                            <?php endif; ?>
                                                        <?php elseif ($v['status'] == 3): ?>
                                                            <span class="badge bg-success">Completada</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-danger">Anulada</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php if ($v['status'] != 0 && $v['status'] == 1): ?>
                                                            <?php if (!empty($_SESSION["permisos"]["gasto"]["editar"])): ?>
                                                                <button name="ajustar" class="btn btn-warning btn-sm editar-gasto" title="Editar" data-toggle="modal" data-target="#modificargasto"
                                                                    data-cod_gasto="<?php echo $v["cod_gasto"]; ?>"
                                                                    data-monto ="<?php echo $v["monto"]; ?>"
                                                                    data-descripcion="<?php echo $v["descripcion"]; ?>">
                                                                    <i class="fas fa-pencil-alt"></i>
                                                                </button>
                                                            <?php endif; ?>
                                                            <?php if ($v['status'] == 1) { ?>
                                                                <?php if (!empty($_SESSION["permisos"]["gasto"]["eliminar"])): ?>
                                                                    <button name="confirmar" class="btn btn-danger btn-sm eliminar" title="Eliminar" data-toggle="modal" data-target="#eliminarG"
                                                                        data-cod_gasto="<?php echo $v["cod_gasto"]; ?>" data-cod_gasto="<?php echo $v["cod_gasto"]; ?>"
                                                                        data-descripcion="<?php echo $v['descripcion']; ?>">
                                                                        <i class="fas fa-trash-alt"></i></button>
                                                                <?php endif; ?>
                                                            <?php } ?>
                                                        <?php endif; ?>

                                                    </td>
                                                </tr>


                                            <?php }
                                            ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- =============================
                    MODAL REGISTRAR GASTOS 
                ================================== -->
                <div class="modal fade" id="modalRGasto" tabindex="-1" aria-labelledby="modalRegistrarGastoLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="registrarModalLabel">Registrar Gasto</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form id="formRegistrarGastos" method="post">
                                    <div class="form-row">
                                        <div class="form-group col-12 col-md-4">
                                            <label for="descripcion">Descripción</label>
                                            <button class="btn btn-xs" data-toggle="tooltip" data-placement="top" title="Descripción del gasto a registrar, por ejemplo: Compra de papel.">
                                                <i class="fas fa-info-circle"></i>
                                            </button>
                                            <input type="text" class="form-control" id="descripcion" name="descripcion" placeholder="Ingrese una descripción del gasto" maxlength="45" required>
                                            <div class="invalid-feedback" style="display: none;"></div>
                                        </div>
                                        <div class="form-group col-12 col-md-4">
                                            <input type="hidden" class="form-control" id="fecha_inicio" name="fecha">
                                            <label for="fecha_del_pago">Fecha de creación</label>
                                            <input type="text" class="form-control" id="fecha_del_pago" readonly>
                                        </div>
                                        <div class="form-group col-12 col-md-4" id="fechavencimiento" style="display: none;">
                                            <label for="fecha_vencimiento">Fecha de vencimiento</label>
                                            <button class="btn btn-xs" data-toggle="tooltip" data-placement="top" title="Ingrese una fecha de vencimiento, por ejemplo: 2026-01-01, si así lo desea.">
                                                <i class="fas fa-info-circle"></i>
                                            </button>
                                            <input type="date" name="fecha_vencimiento" id="fecha_vencimiento" class="form-control">
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-12 col-md-6">
                                            <label for="categoriaG">Categoría del gasto<span class="text-danger" style="font-size: 15px;"> *</span></label>
                                            <button class="btn btn-xs" data-toggle="tooltip" data-placement="top" title="Selecciona la categoría del gasto, por ejemplo: Suministros para papelería.">
                                                <i class="fas fa-info-circle"></i>
                                            </button>
                                            <div class="input-group">
                                                <select class="form-control" id="categoriaG" name="cod_cat_gasto" required>
                                                    <option value="" selected disabled>Seleccione una opción</option>
                                                    <?php foreach ($categorias as $c): ?>
                                                        <?php if ($c['status_cat_gasto'] == 1): ?>
                                                            <option value="<?php echo $c['cod_cat_gasto']; ?>">
                                                                <?php echo $c['categoria']; ?>
                                                            </option>
                                                        <?php endif; ?>
                                                    <?php endforeach; ?>
                                                </select>
                                                <div class="input-group-append">
                                                    <?php if (!empty($_SESSION["permisos"]["config_finanza"]["registrar"])): ?>
                                                        <button class="btn btn-outline-secondary" type="button" data-toggle="modal" data-target="#modalCategoria">+</button>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group col-12 col-md-6">
                                            <label for="Tgasto">Tipo de Gasto</label>
                                            <input type="text" class="form-control" id="Tgasto" placeholder="Tipo de gasto" readonly>
                                            <div class="invalid-feedback" style="display: none;"></div>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-12 col-md-6">
                                            <label for="monto">Monto<span class="text-danger" style="font-size: 15px;"> *</span></label>
                                            <button class="btn btn-xs" data-toggle="tooltip" data-placement="top" title="Ingrese el monto del gasto a pagar en Bs, por ejemplo: 450.">
                                                <i class="fas fa-info-circle"></i>
                                            </button>
                                            <input type="number" class="form-control" step="0.01" min="0" id="monto" name="monto" placeholder="Monto del gasto en Bs">
                                            <div class="invalid-feedback" style="display: none;"></div>
                                        </div>
                                        <div class="form-group col-12 col-md-6">
                                            <label for="condicion">Condición del gasto<span class="text-danger" style="font-size: 15px;"> *</span></label>
                                            <button class="btn btn-xs" data-toggle="tooltip" data-placement="top" title="Seleccione la opción del pago, por ejemplo: Al contado.">
                                                <i class="fas fa-info-circle"></i>
                                            </button>
                                            <div class="input-group">
                                                <select class="form-control" id="condicion" name="cod_condicion" required>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="alert alert-light d-flex align-items-center" role="alert">
                                        <i class="fas fa-exclamation-triangle mr-2"></i>
                                        <span>Todos los campos marcados con (*) son obligatorios</span>
                                    </div>
                                    <div class="modal-footer flex-column flex-md-row">
                                        <button type="button" class="btn btn-default mb-2 mb-md-0" data-dismiss="modal">Cerrar</button>
                                        <button type="submit" class="btn btn-secondary mb-2 mb-md-0" name="deshacer" id="deshacer">Deshacer</button>
                                        <button type="submit" class="btn btn-primary" name="guardarG">Guardar</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
                if (isset($guardarG)): ?>
                    <script>
                        Swal.fire({
                            title: '<?php echo $guardarG["title"]; ?>',
                            text: '<?php echo $guardarG["message"]; ?>',
                            icon: '<?php echo $guardarG["icon"]; ?>',
                            confirmButtonText: 'Ok'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location = 'gasto';
                            }
                        });
                    </script>
                <?php endif; ?>
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
                                <form id="pagoFormG" method="post">
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
                                <button type="submit" class="btn btn-success" form="pagoFormG" id="finalizarPagoG" name="pagar_gasto">Finalizar Pago</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- =======================
                    MODAL REGISTRAR VUELTO
                ============================= -->

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
                            window.location = 'gasto';
                        }
                    });
                </script>
            <?php endif; ?>

            <!-- MODAL EDITAR  GASTOS -->

            <div class="modal fade" id="modificargasto">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Editar Gasto</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form role="form" method="post" id="form-editar-gasto">
                            <div class="modal-body">
                                <input type="hidden" name="cod_gasto" id="cod_gasto_oculto">
                                <div class="form-group">
                                    <label for="cod_gastoE">Código</label>
                                    <input type="text" class="form-control" name="cod_gastoE" id="cod_gastoE" readonly>
                                </div>
                                <div class="form-group">
                                    <label for="descripcion">Gasto</label>
                                    <input type="text" class="form-control" name="descripcion" id="nombreG">
                                    <div class="invalid-feedback" style="display: none;"></div>
                                    <input type="hidden" id="origin" class="form-control" name="origin" maxlength="10">
                                </div>
                                <div class="form-group">
                                    <label for="monto">Monto</label>
                                    <input type="number" name="monto" id="montoe" class="form-control">
                                    <div class="invalid-feedback" style="display: none;"></div>
                                </div>
                            </div>
                            <div class="modal-footer justify-content-between">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                <button type="submit" class="btn btn-primary" name="editarG">Editar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php
            if (isset($editarG)): ?>
                <script>
                    Swal.fire({
                        title: '<?php echo $editarG["title"]; ?>',
                        text: '<?php echo $editarG["message"]; ?>',
                        icon: '<?php echo $editarG["icon"]; ?>',
                        confirmButtonText: 'Ok'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location = 'gasto';
                        }
                    });
                </script>
            <?php endif; ?>

            <!-- ELIMINAR GASTO -->
            <div class="modal fade" id="eliminarG">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-danger">
                            <h4 class="modal-title">Confirmar Eliminar</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form method="post">
                                <p>¿Estás seguro de eliminar el gasto: <b><span id=gasto></span>?</p></b>
                                <input type="hidden" name="cod_gasto" id="cod_eliminar">
                                <div class="modal-footer justify-content-between">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                                    <button type="submit" name="eliminarG" class="btn btn-danger">Eliminar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <?php if (isset($eliminar)): ?>
                <script>
                    Swal.fire({
                        title: '<?php echo $eliminar["title"]; ?>',
                        text: '<?php echo $eliminar["message"]; ?>',
                        icon: '<?php echo $eliminar["icon"]; ?>',
                        confirmButtonText: 'Ok'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location = 'gasto';
                        }
                    });
                </script>
            <?php endif; ?>

            <div class="modal fade" id="modalCategoria" tabindex="-1" aria-labelledby="modalregistrarCategoriaLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Registrar categoría de gastos</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <div class="modal-body">
                            <form id="formregistrarCategoria" method="post">
                                <div class="form-group">
                                    <label for="fecha">Fecha</label>

                                    <button class="btn btn-xs" data-toggle="tooltip" data-placement="top" title="seleccione la fecha en la que iniciara el conteo según la frecuencia seleccionada, por ejemplo: 05-05-2025.">
                                        <i class="fas fa-info-circle"></i>
                                    </button>
                                    <script>
                                        $(function() {
                                            $('[data-toggle="tooltip"]').tooltip();
                                        });
                                    </script>
                                    <input type="date" class="form-control form-control-sm" name="fecha" id="fecha">
                                    <div class="invalid-feedback" style="display: none;"></div>
                                </div>
                                <div class="form-group">
                                    <label for="nombre">Nombre de la categoría</label>
                                    <button class="btn btn-xs" data-toggle="tooltip" data-placement="top" title="Ingrese el nombre de la categoría, por ejemplo: Maquinaria.">
                                        <i class="fas fa-info-circle"></i>
                                    </button>
                                    <script>
                                        $(function() {
                                            $('[data-toggle="tooltip"]').tooltip();
                                        });
                                    </script>
                                    <input type="text" class="form-control" name="nombre" placeholder="Ej: Maquinaria." id="nombre" maxlength="15">
                                    <div class="invalid-feedback" style="display: none;"></div>
                                </div>
                                <div class="form-group">
                                    <label for="tipogasto">Tipo de gasto</label>
                                    <button class="btn btn-xs" data-toggle="tooltip" data-placement="top" title="Seleccione el tipo de gasto, por ejemplo: producto.">
                                        <i class="fas fa-info-circle"></i>
                                    </button>
                                    <script>
                                        $(function() {
                                            $('[data-toggle="tooltip"]').tooltip();
                                        });
                                    </script>
                                    <select name="tipogasto" id="tipogasto" class="form-control">
                                        <option value=""></option>
                                        <?php foreach ($tipo as $t): ?>
                                            <option value="<?php echo $t['cod_tipo_gasto']; ?>">
                                                <?php echo $t['nombre']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="invalid-feedback" style="display: none;"></div>
                                </div>
                                <div class="form-group">
                                    <label for="naturaleza">Naturaleza del gasto</label>
                                    <button class="btn btn-xs" data-toggle="tooltip" data-placement="top" title="Seleccione la naturaleza del gasto, por ejemplo: Gastos fijos.">
                                        <i class="fas fa-info-circle"></i>
                                    </button>
                                    <select name="naturaleza" id="naturaleza" class="form-control">
                                        <option value=""></option>
                                        <?php foreach ($naturaleza as $n): ?>
                                            <option value="<?php echo $n['cod_naturaleza']; ?>">
                                                <?php echo $n['nombre_naturaleza']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="invalid-feedback" style="display: none;"></div>
                                </div>
                                <div class="form-group" id="frecuenciaContainer" style="display: none;">
                                    <label for="frecuenciaC">Frecuencia de pago</label>
                                    <button class="btn btn-xs" data-toggle="tooltip" data-placement="top" title="Ingrese la frecuencia de pago, por ejemplo: Mensual.">
                                        <i class="fas fa-info-circle"></i>
                                    </button>
                                    <select name="frecuenciaC" id="frecuenciaC" class="form-control">
                                        <option value=""></option>
                                        <?php foreach ($frecuencia as $f): ?>
                                            <option value="<?php echo $f['cod_frecuencia']; ?>" data-nombre="<?php echo $f['nombre']; ?>">
                                                <?php echo $f['nombre']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="invalid-feedback" style="display: none;"></div>
                                </div>
                        </div>
                        <div class="modal-footer justify-content-between">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-dark" name="guardarC">Guardar</button>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php
            if (isset($guardarC)): ?>
                <script>
                    Swal.fire({
                        title: '<?php echo $guardarC["title"]; ?>',
                        text: '<?php echo $guardarC["message"]; ?>',
                        icon: '<?php echo $guardarC["icon"]; ?>',
                        confirmButtonText: 'Ok'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            localStorage.setItem('modalRGasto', 'true');
                            window.location = 'gasto';
                        }
                    });
                </script>
            <?php endif; ?>

        </div>
</div>
</section>
</div>
<script src="vista/dist/js/modulos-js/gasto.js"></script>