<?php require_once 'controlador/carga.php' ?>
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <!-- MODULO TRANSACIONAL DE CARGA DE PRODUCTOS EN AJUSTE DE INVENTARIO  -->
                    <h1>Carga de productos</h1>
                    <p>En esta sección se puede gestionar cargas al inventario.</p>
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
                            <?php if (!empty($_SESSION["permisos"]["inventario"]["registrar"])): ?>
                            <!-- Botón para ventana modal -->
                            <button class="btn btn-primary" data-toggle="modal" data-target="#modalregistrarCarga">Registrar Carga</button>
                            <?php endif; ?>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <!-- MOSTRAR EL REGISTRO DE CARGA DE PRODUCTOS -->
                                <table id="carga" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Código</th>
                                            <th>Fecha</th>
                                            <th>Descripción</th>
                                            <th>Detalles</th>

                                            <!--<th>Cantidad total</th>-->
                                            <!-- ##### CUAL DEJO? CANTIDAD CARGADA O CANTIDAD TOTAL(STOCK) ##### -->
                                            <th>Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        foreach ($carga as $dato) {
                                        ?>
                                            <?php if ($dato['status'] != 2): ?>

                                                <td><?php echo $dato['cod_carga'] ?></td>
                                                <td><?php echo $dato['fecha'] ?></td>
                                                <td><?php echo $dato['descripcion'] ?></td>
                                                <td class="text-center">
                                                    <button class="btn btn-primary btn-sm" style="position: center;" data-toggle="modal" data-target="#detallemodal" title="Ver detalle"
                                                        data-codigo="<?php echo $dato["cod_carga"]; ?>"
                                                        data-fecha_carga="<?php echo $dato["fecha"]; ?>"
                                                        data-descrip="<?php echo $dato["descripcion"]; ?>">
                                                        <i class="fas fa-plus"></i>
                                                    </button>

                                                </td>

                                                <!--<td><?php //echo $dato['stock'] 
                                                        ?></td>-->
                                                <td>
                                                    <?php if ($dato['status'] == 1): ?>
                                                        <span class="badge bg-success">Cargado</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-danger">No disponible</span>
                                                    <?php endif; ?>
                                                </td>


                            </div>

                            </tr>
                        <?php endif; ?>
                    <?php } ?>
                    </tbody>
                    </table>
                        </div>
                    </div>
                </div>

                <!-- =======================
MODAL REGISTRAR CARGA CON EXITO
============================= -->

                <div class="modal fade" id="modalregistrarCarga" tabindex="-1" aria-labelledby="modalregistrarCargaLabel">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Registrar Carga</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>

                            <div class="modal-body">
                                <form id="formregistrarCarga" method="post">
                                    <!-- Campo oculto para enviar la fecha y hora al servidor -->
                                    <input type="hidden" id="fecha-hora" name="fecha_hora">

                                    <!-- Campo de texto para mostrar la fecha y hora al usuario -->
                                    <div class="form-group">
                                        <label for="fecha-hora-display">Fecha y Hora</label>
                                        <input type="text" class="form-control form-control-sm" id="fecha-hora-display" readonly>
                                    </div>
                                    <!--   DESCRIPCIÓN  -->
                                    <div class="form-group">
                                        <label for="descripcion">Descripción <span class="text-danger" style="font-size: 20px;"> *</span> </label>
                                        <!-- TOOLTIPS-->
                                        <button class="btn btn-xs" data-toggle="tooltip" data-placement="top" title="Ingresa el motivo por el cual existe la carga, por ejemplo: Compra sin factura">
                                            <i class="fas fa-info-circle"></i>
                                        </button>
                                        <script>
                                            $(function() {
                                                $('[data-toggle="tooltip"]').tooltip();
                                            });
                                        </script>
                                        <input type="text" class="form-control" id="descripcion" name="descripcion" maxlength="100">
                                        <div class="invalid-feedback" style="display: none;"></div>
                                    </div>

                                    <div class="table-responsive mt-4">
                                        <table id="productosCarga" class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Código</th>
                                                    <th>Producto <span class="text-danger" style="font-size: 20px;"> *</span></th>
                                                    <th>Cantidad <span class="text-danger" style="font-size: 20px;"> *</span></th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!--  filas dinámicas  -->
                                            </tbody>
                                        </table>
                                        <button type="button" class="btn btn-secondary" id="add-product">Agregar otro producto</button>
                                        <div class="alert alert-light d-flex align-items-center" role="alert">
                                            <i class="fas fa-exclamation-triangle mr-2"></i>
                                            <span>Todos los campos marcados con (*) son obligatorios</span>
                                        </div>
                                    </div>
                                    <div class="modal-footer justify-content-between">
                                        <input type="hidden" id="totalcarga" name="costo_carga">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                        <button type="submit" class="btn btn-primary" name="guardar">Guardar</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <?php
        if (isset($registrar)): ?>
            <script>
                Swal.fire({
                    title: '<?php echo $registrar["title"]; ?>',
                    text: '<?php echo $registrar["message"]; ?>',
                    icon: '<?php echo $registrar["icon"]; ?>',
                    confirmButtonText: 'Ok'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location = 'carga';
                    }
                });
            </script>
        <?php endif; ?>



<!-- =============================
    MODAL MOSTRAR DETALLES 
================================== -->
        <div class="modal fade" id="detallemodal" tabindex="-1" aria-labelledby="detalleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Información de la carga</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="codigo">Nro de Carga</label>
                                    <input type="text" class="form-control" id="codigo" name="codigo" readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="descrip">Descripción</label>
                                    <input type="text" class="form-control" id="descrip" name="descrip" readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="fecha_carga">Fecha de la carga</label>
                                    <input type="text" class="form-control" id="fecha_carga" name="fecha_carga" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="modal-body">
                            <div class="card">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table id="productos" class="table table-bordered table-striped table-hover" style="width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th>Imagen</th>
                                                    <th>Código</th>
                                                    <th>Producto</th>
                                                    <th>Fecha de vencimiento</th>
                                                    <th>Lote</th>
                                                    <th>cantidad cargada</th>

                                                </tr>
                                            </thead>
                                            <tbody id="detalleBody">
                                                <!-- Los detalles se cargarán aquí -->
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


        <!-- registrar DETALLEP -->
        <div class="modal fade" id="modalregistrardetallep">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Añadir detalles</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form role="form" method="post" id="formRegistrarDetalle">
                        <div class="modal-body">

                            <div class="form-group">
                                <label for="cod_presentacion">Código del Producto:</label>
                                <input type="text" class="form-control" name="cod_presentacion" id="cod_presentacion" required readonly>
                            </div>
                            <div class="form-group">
                                <label for="fecha_vencimiento">Fecha de vencimiento</label>
                                <input type="date" class="form-control" id="fecha_vencimiento" name="fecha_vencimiento">
                            </div>
                            <div class="form-group">
                                <label for="lote">Lote</label>
                                <input type="text" class="form-control" id="lote" name="lote" placeholder="Ej: 122-0G12" maxlength="20">
                            </div>
                        </div>
                        <div class="modal-footer justify-content-between">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-primary" name="registrarD" value="">Guardar</button>
                        </div>
                    </form>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>

    </section>
</div>

<script src="vista/dist/js/modulos-js/carga.js"></script>