<?php require_once 'controlador/compras.php'; ?>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Compras</h1>
                    <p>En esta sección se puede gestionar las compras de productos.</p>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <!-- Formulario y tabla de compras -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <?php if (!empty($_SESSION["permisos"]["compra"]["registrar"])): ?>
                                <!-- Botones  de registar en línea -->
                                <button name="reg" class="btn btn-primary mr-2" data-toggle="modal" data-target="#modalcom">
                                    Registrar compra
                                </button>
                            <?php endif; ?>
                        </div>
                        <br>
                        <div class="card-body">
                            <!-- Tabla de compra-->
                            <div class="table-responsive">
                                <table id="compras" class="table table-bordered table-striped table-hover datatable1" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>Código</th>
                                            <th>Fecha</th>
                                            <th>Proveedor</th>
                                            <th>Sub total</th>
                                            <th>Total</th>
                                            <th>Status</th>
                                            <th>Detalles</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- registro de puro de compra-->
                                        <?php foreach ($compra as $compras) {
                                        ?>
                                            <?php if ($compras['status'] != 0): ?>
                                                <tr>
                                                    <td><?php echo $compras["cod_compra"] ?></td>
                                                    <td><?php echo $compras["fecha"] ?></td>
                                                    <td><?php echo $compras['razon_social'] ?></td>
                                                    <td><?php echo $compras['subtotal'] ?></td>
                                                    <td><?php echo $compras["total"] ?></td>
                                                    <td>
                                                        <?php if ($compras['status'] == 1): ?>
                                                            <span class="badge bg-secondary">Pendiente</span>
                                                            <?php if (!empty($_SESSION["permisos"]["compra"]["registrar"])): ?>
                                                                <button name="abono" title="Pagar" class="btn btn-primary btn-sm editar" data-toggle="modal" data-target="#pagoModalc"
                                                                    data-cod_compra="<?php echo $compras["cod_compra"]; ?>"
                                                                    data-total="<?php echo $compras["total"];  ?>"
                                                                    data-nombre="<?php echo $compras["razon_social"]; ?>">
                                                                    <i class="fas fa-money-bill-wave"></i>
                                                                </button>
                                                            <?php endif; ?>
                                                        <?php elseif ($compras['status'] == 2): ?>
                                                            <span class="badge bg-warning">Pago parcial</span>
                                                            <?php if (!empty($_SESSION["permisos"]["compra"]["registrar"])): ?>
                                                                <button name="partes" title="Pagar" class="btn btn-primary btn-sm editar" data-toggle="modal" data-target="#pagoModalc"
                                                                    data-cod_compra="<?php echo $compras["cod_compra"]; ?>"
                                                                    data-total="<?php echo $compras["total"];  ?>"
                                                                    data-montop="<?php echo $compras["total_pagos_emitidos"];  ?>"
                                                                    data-nombre="<?php echo $compras["razon_social"]; ?>">
                                                                    <i class="fas fa-money-bill-wave"></i>
                                                                </button>
                                                            <?php endif; ?>
                                                        <?php elseif ($compras['status'] == 3): ?>
                                                            <span class="badge bg-success">Completada</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-danger">Anulada</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-primary btn-sm" style="position: center;" data-toggle="modal" data-target="#detallemodal" title="Ver detalle"
                                                            data-codigo="<?= $compras["cod_compra"]; ?>"
                                                            data-nombre="<?= $compras['razon_social'] ?>"
                                                            data-fecha="<?= $compras["fecha"] ?>"
                                                            data-total="<?= $compras["total"] ?>">
                                                            <i class="fas fa-plus"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php endif; ?>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </section><!-- /.content -->
</div><!-- /.content-wrapper -->



<!-- Registrar modal  compra -->
<div class="modal fade" id="modalcom" tabindex="-1" role="dialog" aria-labelledby="compraModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="max-width: 96%;">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title" id="compraModalLabel">Registrar compra</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formcompra" method="post">
                    <div class="container-fluid">
                        <!-- Información del Proveedor -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="cedula_rif">RIF</label>
                                    <div class="row">
                                        <div class="col-md-11">
                                            <input type="text" class="form-control form-control-sm" id="rif-r" name="rif" placeholder="RIF" required>
                                            <div class="invalid-feedback" style="display: none; position: absolute; top: 100%; margin-top: 2px; width: calc(100% - 2px); font-size: 0.875em; text-align: left;"></div>
                                            <input type="hidden" id="cod_prov" name="cod_prov" required>
                                        </div>
                                        <!--<div class="col-md-1">
                                            <button type="button" class="btn btn-outline-secondary btn-sm" data-toggle="modal" data-target="#modalProv">+</button>
                                        </div>-->
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="telefono">Número de teléfono</label>
                                    <input type="text" class="form-control form-control-sm" id="telefono" name="telefono" placeholder="Número del proveedor" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="razon-social">Razón social</label>
                                    <input type="text" class="form-control form-control-sm" id="razon-social" name="razon_social" placeholder="Razón social" readonly>
                                </div>
                                <div class="form-row align-items-end">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="fecha-hora">Fecha y Hora</label>
                                            <input type="text" class="form-control form-control-sm" id="fecha-hora" name="fecha" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="selectDivisa">Selecciona la divisa:</label>
                                            <select id="selectDivisa" class="form-control form-control-sm">
                                                <?php foreach ($d as $divisa) { ?>
                                                    <option data-cod="<?= $divisa['cod_divisa'] ?>"
                                                        data-tasa="<?= $divisa['tasa'] ?>"
                                                        data-abreviatura="<?= $divisa['abreviatura'] ?>" <?= $divisa['cod_divisa'] == 1 ? 'selected' : '' ?>>
                                                        <?= $divisa['nombre'] . ' - ' . $divisa['abreviatura'] ?></option>
                                                <?php } ?>
                                            </select>
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
                                <div class="form-group" id="div_fecha_vencimiento" style="display: none;">
                                    <label for="fecha_vencimiento">Fecha de Vencimiento</label>
                                    <input type="date" class="form-control form-control-sm" id="fecha_vencimiento" name="fecha_v">
                                </div>
                            </div>
                        </div>


                        <!-- Tabla para escritorio -->
                        <div class="table-responsive d-none d-md-block">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Código</th>
                                        <th>Producto</th>
                                        <th>Fecha de vencimiento</th>
                                        <th>Lote</th>
                                        <th>Cantidad</th>
                                        <th class="col-divisa" style="display: none;">Precio (<span id="labelDivisa"></span>)</th>
                                        <th>Precio (Bs)</th>
                                        <th style="width: 7%;">IVA</th>
                                        <th>Total</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody id="ProductosBody"></tbody>
                            </table>
                        </div>

                        <!-- Contenedor alternativo para móviles -->
                        <div id="ProductosBodyMobile" class="d-block d-md-none"></div>

                        <!-- Botón para agregar fila -->
                        <button type="button" class="btn btn-success" onclick="agregarFila()">Agregar Fila</button>

                            <div class="card card-outline card-primary float-right" style="width: 300px;">
                                <div class="card-body">
                                    <p>Subtotal: Bs <span id="subtotal" class="text-bold">0.00</span></p>
                                    <p>Exento: Bs <span id="exento" class="text-bold">0.00</span></p>
                                    <p>Base imponible: Bs <span id="base-imponible" class="text-bold">0.00</span></p>
                                    <p>IVA (16%): Bs <span id="iva" class="text-bold">0.00</span></p>
                                    <p class="text-bold">TOTAL: Bs <span id="total-span" class="text-bold">0.00</span></p>
                                    <input type="hidden" id="total-general" name="total_general">
                                    <input type="hidden" id="subt" name="subtotal">
                                    <input type="hidden" id="impuesto_total" name="impuesto_total">

                                </div>
                            </div>
                        
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="submit" class="btn btn-primary" form="formcompra" name="registrar">Guardar</button>
            </div>
        </div>
    </div>
</div>
<?php
if (isset($registrar)): ?>
    <script>
        Swal.fire({
            title: '<?php echo $registrar["title"]; ?>',
            icon: '<?php echo $registrar["icon"]; ?>',
            confirmButtonText: 'Ok'
        }).then((resul) => {
            if (resul.isConfirmed) {
                window.location = 'compras';
            }
        });
    </script>
<?php endif; ?>
<!-- registrar compra-->

<div class="modal fade" id="detallemodal" tabindex="-1" aria-labelledby="detalleLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title" id="detalleLabel">Informacion de la compra</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="nro_venta">Nro de compra</label>
                            <input type="text" class="form-control" id="nro-compra" name="nro_venta" readonly>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="nombre_cliente">Razon social</label>
                            <input type="text" class="form-control" id="r_social" name="nombre_cliente" readonly>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="fecha_venta">Fecha de la compra</label>
                            <input type="text" class="form-control" id="fecha_compra" name="fecha_venta" readonly>
                        </div>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Producto</th>
                                    <th>Fecha de vencimiento</th>
                                    <th>Lote</th>
                                    <th>Cantidad</th>
                                    <th>Precio unitario (Bs)</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody id="detalleBodyc">
                                <!-- Aquí se agregarán dinámicamente las filas de productos -->
                            </tbody>
                        </table>
                    </div>
                    <div class="card card-outline card-primary float-right" style="width: 300px;">
                        <div class="card-body">
                            <p class="text-bold">TOTAL: Bs <span id="total_compra" class="text-bold">0.00</span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- =============================
    MODAL REGISTRAR PRODUCTO 
================================== -->
<div class="modal fade" id="modalRegistrarProducto" tabindex="-1" aria-labelledby="modalRegistrarProductoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="registrarModalLabel">Registrar producto</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formRegistrarProducto" action="index.php?pagina=productos" method="post">
                    <div class="form-group row">
                        <div class="col-6">
                            <!-- Campo oculto para el código del producto -->
                            <input type="hidden" id="cod_productoR" name="cod_productoR">
                            <input type="hidden" name="compra" value="compras">

                            <label for="nombre">Nombre del producto<span class="text-danger" style="font-size: 15px;"> *</span></label>
                            <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Ingresa el nombre" required>
                            <div class="invalid-feedback" style="display: none;"></div>
                            <div id="lista-productos" class="list-group" style="display: none;"></div>

                        </div>
                        <div class="col-6">
                            <label for="marca">Marca</label>
                            <input type="text" class="form-control" id="marca" name="marca" placeholder="Ingresa la marca">
                            <div class="invalid-feedback" style="display: none;"></div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-6">
                            <label for="categoria">Categoría de producto<span class="text-danger" style="font-size: 15px;"> *</span></label>
                            <div class="input-group">
                                <select class="form-control" id="categoria" name="categoria" required>
                                    <option value="" selected disabled>Seleccione una opción</option>
                                    <?php foreach ($categoria as $cate): ?>
                                        <option value="<?php echo $cate['cod_categoria']; ?>">
                                            <?php echo $cate['nombre']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button" data-toggle="modal" data-target="#modalNuevaCategoria">+</button>
                                </div>
                            </div>
                        </div>

                        <div class="col-6">
                            <label for="exento">Impuesto IVA<span class="text-danger" style="font-size: 15px;"> *</span></label>
                            <!-- TOOLTIPS-->
                            <button class="btn btn-xs" data-toggle="tooltip" data-placement="top" title="Selecciona si el producto es exento (tiene IVA) o gravable (No tiene IVA). El IVA es el 16%">
                                <i class="fas fa-info-circle"></i>
                            </button>
                            <script>
                                $(function() {
                                    $('[data-toggle="tooltip"]').tooltip();
                                });
                            </script>
                            <select class="form-control" id="iva" name="iva" required>
                                <option value="" selected disabled>Seleccione una opción</option>
                                <option value="1">Exento</option>
                                <option value="2">Gravable</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="unidad">Unidad de medida<span class="text-danger" style="font-size: 15px;"> *</span></label>
                        <!-- TOOLTIPS-->
                        <button class="btn btn-xs" data-toggle="tooltip" data-placement="top" title="Selecciona la unidad de medida para la venta de productos, por ejemplo: Kg">
                            <i class="fas fa-info-circle"></i>
                        </button>
                        <script>
                            $(function() {
                                $('[data-toggle="tooltip"]').tooltip();
                            });
                        </script>
                        <div class="input-group">
                            <select class="form-control" id="unidad" name="unidad" required>
                                <option value="" selected disabled>Seleccione una opción</option>
                                <?php foreach ($unidad as $u): ?>
                                    <option value="<?php echo $u['cod_unidad']; ?>">
                                        <?php echo $u['tipo_medida']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="button" data-toggle="modal" data-target="#modalNuevaUnidad">+</button>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-6">
                            <label for="presentacion">Presentación</label>
                            <button class="btn btn-xs" data-toggle="tooltip" data-placement="top" title="Ingresa la presentación de como viene el producto. Ej: Pieza.">
                                <i class="fas fa-info-circle"></i>
                            </button>
                            <script>
                                $(function() {
                                    $('[data-toggle="tooltip"]').tooltip();
                                });
                            </script>
                            <input type="text" class="form-control" id="presentacion" name="presentacion" placeholder="Ej: Pieza.">
                            <div class="invalid-feedback" style="display: none;"></div>
                        </div>
                        <div class="col-6">
                            <label for="cant_presentacion">Cantidad de presentación</label>
                            <button class="btn btn-xs" data-toggle="tooltip" data-placement="top" title="Ingresa la cantidad de presentación de como viene el producto. Ej: 250gr.">
                                <i class="fas fa-info-circle"></i>
                            </button>
                            <script>
                                $(function() {
                                    $('[data-toggle="tooltip"]').tooltip();
                                });
                            </script>
                            <input type="text" class="form-control" id="cant_presentacion" name="cant_presentacion" placeholder="Ej: 1.5kg">
                            <div class="invalid-feedback" style="display: none;"></div>
                        </div>
                    </div>
                    <hr>
                    <div class="form-group row">
                        <div class="col-6">
                            <label for="costo">Costo</label>
                            <input type="number" class="form-control" step="0.01" min="0" id="costo" name="costo" placeholder="Precio de compra en Bs">
                            <div class="invalid-feedback" style="display: none;"></div>
                        </div>
                        <div class="col-6">
                            <label for="precio">Precio</label>
                            <input type="number" class="form-control" min="0" id="precio" placeholder="Precio de venta en Bs" readonly>
                            <div class="invalid-feedback" style="display: none;"></div>
                        </div>
                    </div>
                    <div class="input-group mb-2">
                        <input type="number" class="form-control nuevoPorcentaje" min="0" step="1" placeholder="Porcentaje de ganancia" id="porcen" name="porcen">
                        <div class="invalid-feedback" style="display: none;"></div>
                        <div class="input-group-append">
                            <span class="input-group-text"><i class="fas fa-percent"></i></span>
                        </div>
                    </div>
                    <div class="alert alert-light d-flex align-items-center" role="alert">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <span>Todos los campos marcados con (*) son obligatorios</span>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-secondary" name="deshacer" id="deshacer">Deshacer</button>
                        <button type="submit" class="btn btn-primary" name="guardar">Guardar</button>
                    </div>
                </form>
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
                window.location = 'compras';
            }
        });
    </script>
<?php endif; ?>

<!-- =======================
MODAL REGISTRAR PAGO
============================= -->
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
                        <form id="pagoFormc" method="post">
                        <div class="form-row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="nro_compra">Nro de Compra</label>
                                    <input type="text" class="form-control" id="nro-compra" name="cod_compra" readonly>
                                    <input type="hidden" name="tipo_pago" value="compra" >
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
                            <h4>Total de Compra:   <span id="total-pagoc" class="font-weight-bold" style="font-size: 3rem;">0.00</span></h4>
                            <input type="hidden" id="total_compra" name="montototal">
                        </div>
                        <div class="text-center my-3" id="campo-saldoc" style="display:none;">
                            <h4>Saldo pendiente:   <span id="saldo_pendientec" class="font-weight-bold" style="font-size: 3rem;">0.00</span></h4>
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
                                    if($opcion['status']==1): ?>
                                <?php if ($opcion['cod_divisa']==1): ?>
                                    <!-- Si es bolívares (sin conversión de divisas) -->
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <input type="text" class="form-control" value="<?= $opcion['medio_pago'].' - '.$opcion['descripcion'] ?>" readonly>
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
                                            <input type="text" class="form-control" value="<?= $opcion['medio_pago'].' - '.$opcion['descripcion']; ?>" readonly>
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
                                    endif;?>
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
                                        <input type="number" step="0.001" class="form-control" id="monto_pagarc"  name="monto_pagar" readonly>
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
                        <!-- Botón para registrar vuelto (inicialmente oculto, se mostrará con JS si la diferencia es negativa) -->
                        <div class="form-row justify-content-end" id="div-boton-vueltoc" style="display: none;">
                            <div class="col-md-4">
                              
                                <input type="hidden" step="0.001" name="vuelto" id="vueltoC" value="">

                                <button type="button" class="btn btn-info btn-block" data-toggle="modal" data-target="#vueltoModalc"  id="btn-registrar-vueltoc">
                                    Registrar Vuelto
                                </button>
                            </div>
                        </div>

                        <input type="hidden" name="pagoV" id="vuelto_data" value="">

                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success" form="pagoFormc" id="finalizarPagoBtnc" name="pagar_compra">Finalizar Pago</button>
                    </div>
                </div>
            </div>
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
                window.location = 'compras';
            }
        });
    </script>
<?php endif; ?>

<!-- =======================
MODAL REGISTRAR VUELTO 
============================= -->

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
                <!--<div class="form-row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="total_pagado">Total Pagado</label>
                            <div class="input-group">
                                <div class="input-group-append">
                                    <span class="input-group-text">Bs</span>
                                </div>
                                <input type="number" step="0.001" class="form-control" id="total_pagado" name="total_pagado" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="monto_compra">Monto de la venta</label>
                            <div class="input-group">
                                <div class="input-group-append">
                                    <span class="input-group-text">Bs</span>
                                </div>
                                <input type="number" step="0.001" class="form-control" id="monto_compra" name="monto_compra" readonly>
                            </div>
                        </div>
                    </div>

                </div>-->
                <div class="text-center my-3">
                    <h4>Vuelto:   <span id="total-vueltoc" class="font-weight-bold" style="font-size: 3rem;">0.00</span></h4>
                    <input type="hidden" id="vuelto_calculado" name="vuelto_calculado">
                </div>
                <div class="form-row">
                    <?php foreach ($formaspago as $index => $opcion):
                            if($opcion['status']==1):?>
                        <?php if ($opcion['cod_divisa']==1): ?>
                            <!-- Si es bolívares (sin conversión de divisas) -->
                            <div class="col-md-8">
                                <div class="form-group">
                                    <input type="text" class="form-control" value="<?= $opcion['medio_pago'].' - '.$opcion['descripcion'] ?>" readonly>
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
                                    <input type="text" class="form-control" value="<?= $opcion['medio_pago'].' - '.$opcion['descripcion']; ?>" readonly>
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
                            endif;?>
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



<?php if (isset($registrarPC)): ?>
    <script>
        Swal.fire({
            title: '<?php echo $registrarPC["title"]; ?>',
            text: '<?php echo $registrarPC["message"]; ?>',
            icon: '<?php echo $registrarPC["icon"]; ?>',
            confirmButtonText: 'Ok'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location = 'compras';
            }
        });
    </script>
<?php endif; ?>


<script src="vista/dist/js/modulos-js/compras.js"></script>
<script src="vista/dist/js/modulos-js/productos.js"></script>