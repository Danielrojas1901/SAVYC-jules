<?php require_once 'controlador/divisa.php'; ?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-6">
        <h1>Gestión de Divisas</h1>
        <p>En esta sección se puede configurar las divisas.</p>
        </div><!-- /.col -->
    </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <?php if (!empty($_SESSION["permisos"]["config_finanza"]["registrar"])): ?>
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalregistrarDivisa">
                                Registrar Divisa
                            </button>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        
                            <table id="paymentTypesTable" class="table table-bordered table-striped datatable">
                                <thead>
                                    <tr>
                                        <th>Código</th>
                                        <th>Nombre</th>
                                        <th>Símbolo/Abreviatura</th>
                                        <th>Tasa</th>
                                        <th>Última actualización</th>
                                        <th>Status</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($consulta as $divisa) { ?>
                                    <?php if ($divisa['status'] != 2): ?>
                                    <tr>
                                        <td><?php echo $divisa['cod_divisa']?></td>
                                        <td><?php echo $divisa['nombre']?></td>
                                        <td><?php echo $divisa['abreviatura'] ?></td>
                                        <td><?php echo $divisa['tasa']."  Bs" ?></td>
                                        <td><?php echo $divisa['fecha'] ?>
                                            <?php if($divisa['cod_divisa']==1): ?>
                                            <?php else:?>
                                                <?php if (!empty($_SESSION["permisos"]["config_finanza"]["editar"])): ?>
                                                    <div class="float-right">
                                                        <button name="Actualizar" title="Actualizar" class="btn btn-primary btn-sm editar" data-toggle="modal" data-target="#actModal" 
                                                            data-codigo="<?php echo $divisa["cod_divisa"]; ?>" 
                                                            data-nombre="<?php echo $divisa["nombre"]; ?>" 
                                                            data-abreviatura="<?php echo $divisa["abreviatura"]; ?>"
                                                            data-tasa="<?php echo $divisa["tasa"]; ?>"
                                                            data-status="<?php echo $divisa["status"]; ?>" >
                                                            <i class="fa fa-history" aria-hidden="true"></i>
                                                        </button>
                                                    </div>
                                                <?php endif; ?>
                                            <?php endif;?>
                                        </td>
                                        <td>
                                            <?php if ($divisa['status']==1):?>
                                                <span class="badge bg-success">Activo</span>
                                            <?php else:?>
                                                <span class="badge bg-danger">Inactivo</span>
                                            <?php endif;?>
                                        </td>
                                        <td>
                                        <?php if($divisa['cod_divisa']==1): ?>

                                        <?php else:?>
                                            <?php if (!empty($_SESSION["permisos"]["config_finanza"]["editar"])): ?>
                                                <button name="editar" title="Editar" class="btn btn-primary btn-sm editar" data-toggle="modal" data-target="#editModal" 
                                                data-codigo="<?php echo $divisa["cod_divisa"]; ?>" 
                                                data-nombre="<?php echo $divisa["nombre"]; ?>" 
                                                data-abreviatura="<?php echo $divisa["abreviatura"]; ?>"
                                                data-tasa="<?php echo $divisa["tasa"]; ?>"
                                                data-status="<?php echo $divisa["status"]; ?>" >
                                                <i class="fas fa-pencil-alt"></i>
                                                </button>
                                            <?php endif; ?>
                                            <?php if (!empty($_SESSION["permisos"]["config_finanza"]["eliminar"])): ?>
                                                <button name="eliminar" title="Eliminar" class="btn btn-danger btn-sm eliminar" data-toggle="modal" data-target="#eliminardivisa"
                                                data-codigo="<?php echo $divisa["cod_divisa"]; ?>" 
                                                data-nombre="<?php echo $divisa["nombre"]; ?>" >
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            <?php endif;?>
                                        <?php endif;?>
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
    </div>
</section>
</div>
    
    <!-- registrar Divisa Modal -->
<div class="modal fade" id="modalregistrarDivisa">
    <div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
        <h4 class="modal-title">Registrar Divisa</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        </div>
        <form role="form" method="post">
        <div class="modal-body">
            <div class="form-group">
                <label for="nombre">Nombre de la Divisa<span class="text-danger" style="font-size: 15px;"> *</span></label>
                <input type="text" class="form-control" id="nombre" placeholder="Ej: Dólares" name="nombre" required>
                <div class="invalid-feedback" style="display: none;"></div>
            </div>
            <div class="form-group">
                <label for="simbolo">Símbolo o Abreviatura<span class="text-danger" style="font-size: 15px;"> *</span></label>
                <!-- TOOLTIPS-->
                <button class="btn btn-xs" data-toggle="tooltip" data-placement="top" title="Selecciona el simbolo abreviatura de la divisa, por ejemplo: USD ó $">
                    <i class="fas fa-info-circle"></i>
                </button>
                <script>
                    $(function () {
                        $('[data-toggle="tooltip"]').tooltip();
                    });
                </script>
                <input type="text" class="form-control" id="simbolo" name="simbolo" placeholder="Ej: USD ó $" required>
                <div class="invalid-feedback" style="display: none;"></div>
            </div>
            <div class="form-group row">
                <div class="col-6">
                    <label for="tasa">Tasa de la Divisa<span class="text-danger" style="font-size: 15px;"> *</span></label>
                    <div class="input-group">
                        <input type="number" step="0.01" class="form-control" min="0" id="tasa" name="tasa" placeholder="Tasa en Bolívares" required>
                        <div class="invalid-feedback" style="display: none; position: absolute; top: 100%; margin-top: 2px; width: calc(100% - 2px); font-size: 0.875em; text-align: left;"></div>
                        <div class="input-group-append">
                            <span class="input-group-text">Bs</span>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <label for="fecha">Fecha<span class="text-danger" style="font-size: 15px;"> *</span></label>
                    <input type="date" class="form-control" id="fecha" name="fecha" required>
                </div>
            </div>
            <br>
            <!-- Alert Message -->
            <div class="alert alert-light d-flex align-items-center" role="alert">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                <span>Todos los campos marcados con (*) son obligatorios</span>
            </div>
        </div>
        <div class="modal-footer justify-content-between">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            <button type="submit" class="btn btn-primary" name="registrar">Guardar</button>
        </div>
        </form>
    </div>
    <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
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
                window.location = 'divisa';
            }
        });
    </script>
<?php endif; ?>

<!-- =======================
MODAL EDITAR DIVISA
============================= -->

<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Editar Información</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editForm" method="post">
                    <div class="form-group">
                        <label for="codigo">Código</label>
                        <input type="text" class="form-control" id="codigo" name="codigo" readonly>
                    </div>
                    <div class="form-group">
                        <label for="nombre">Nombre de la Divisa</label>
                        <input type="text" class="form-control" id="nombre1" name="nombre" required>
                        <div class="invalid-feedback" style="display: none;"></div>
                        <input type="hidden" id="origin" name="origin"> 
                        
                    </div>
                    <div class="form-group">
                        <label for="abreviatura">Símbolo o Abreviatura</label>
                        <input type="text" class="form-control" id="abreviatura" name="simbolo" required>
                        <div class="invalid-feedback" style="display: none;"></div>
                    </div>
                    
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select class="form-control" id="status" name="status">
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="submit" form="editForm" class="btn btn-primary" name="actualizar">Guardar cambios</button>
            </div>
        </div>
    </div>
</div>
<?php 
if (isset($editar)): ?>
    <script>
        Swal.fire({
            title: '<?php echo $editar["title"]; ?>',
            text: '<?php echo $editar["message"]; ?>',
            icon: '<?php echo $editar["icon"]; ?>',
            confirmButtonText: 'Ok'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location = 'divisa';
            }
        });
    </script>
<?php endif; ?>

<!-- =======================
MODAL CONFIRMAR ELIMINAR 
============================= -->

<div class="modal fade" id="eliminardivisa" tabindex="-1" aria-labelledby="eliminardivisaLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title" id="eliminardivisaLabel">Confirmar Eliminación</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
            <form id="elimodal" method="post"> 
                <p>¿Está seguro que desea eliminar la divisa: <b><span id="divisaNombre"></b></span>?</p>
                <input type="hidden" id="divisaCodigo" name="divisaCodigo"> 
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            <button type="submit" form="elimodal" class="btn btn-danger" id="confirmDelete" name="borrar">Eliminar</button>
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
                window.location = 'divisa';
            }
        });
    </script>
<?php endif; ?>

<!-- =======================
MODAL ACTUALIZAR TASA DE CAMBIO
============================= -->
<div class="modal fade" id="actModal" tabindex="-1" aria-labelledby="actModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="actModalLabel">Tasas de cambio</h5>
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
                                        <label for="nro_venta">Divisa</label>
                                        <input type="text" class="form-control" id="nombre2" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="nombre_cliente">Abreviatura</label>
                                        <input type="text" class="form-control" id="abreviatura2" readonly>
                                    </div>
                                </div>
                            </div>
                            <ul class="nav nav-tabs" id="tabContent" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="producto-tab" data-toggle="tab" href="#producto" role="tab">actualizacion de tasa</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="carga-tab" data-toggle="tab" href="#carga" role="tab">Historial/variacion</a>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content">
                                <!-- ACTUALIZACION DE TASA  -->
                                <div class="tab-pane fade show active" id="producto" role="tabpanel">
                                    <form id="actForm" method="post">
                                        <div class="form-group row justify-content-center">
                                            <div class="col-md-7">
                                                <label for="tasa2">Tasa de la Divisa</label>
                                                <div class="input-group">
                                                    <input type="number" id="tasa2" step="0.01" min="0.01" class="form-control" name="tasa[tasa]" required>
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">Bs</span>
                                                    </div>
                                                    <div class="invalid-feedback"></div>
                                                        <div class="col-md-5" id="div-boton-actualizar-tasa" style="display: none;">
                                                            <button type="button" class="btn btn-primary btn-block" id="btn-actualizar-tasa">
                                                                Actualizar tasa
                                                            </button>
                                                        </div>
                                                </div>
                                            </div>
                                            <div class="col-md-5">
                                                <label for="fecha2">Fecha</label>
                                                <input type="date" class="form-control" id="fecha2" name="tasa[fecha]" required>
                                                <input type="hidden" id="codigo2" name="tasa[cod_divisa]">
                                                <div class="invalid-feedback"></div>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <button type="submit" name="r_tasa" class="btn btn-primary">Guardar cambios</button>
                                        </div>
                                    </form>
                                </div>
                                <!-- HISTORIAL/VARIACION -->
                                <div class="tab-pane fade" id="carga" role="tabpanel">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped datatable" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th data-orderable="true" data-order="desc">Fecha</th>
                                                    <th>Tasa</th>
                                                </tr>
                                            </thead>
                                            <tbody>
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
</div>

<div class="modal fade" id="loadingModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center">
            <div class="modal-body">
                <h5>Cargando tasas...</h5>
                <div class="spinner-border text-primary mt-3" role="status">
                    <span class="sr-only">Cargando...</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script> 
//ACTUALIZAR
</script>

<script>
    var historialDivisas = <?php echo json_encode($historial); ?>;
</script>

<script src='vista/dist/js/modulos-js/divisa.js'></script>