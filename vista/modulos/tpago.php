<?php require_once 'controlador/tpago.php'; ?>

<div class="content-wrapper">
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">Gestión de Tipos de Pago</h1>
                <p>Configura los tipos de pago utilizados en la empresa.</p>
            </div>
        </div>
    </div>
</div>

        <!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <?php if (!empty($_SESSION["permisos"]["config_finanza"]["registrar"])): ?>
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#registrarTipoPagoModal">
                                Registrar Tipo de Pago
                            </button>
                            <button type="button" class="btn btn-info" data-toggle="modal" data-target="#gestionarMediosPagoModal" style="background-color: #8770fa; color: white;border-color: #8770fa;">
                                Gestionar Medios de Pago
                            </button>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                    <div class="table-responsive">
                        <table id="paymentTypesTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Codigo</th>
                                    <th>Medio de pago</th>
                                    <th>Descripcion</th>
                                    <th>Divisa</th>
                                    <th>Status</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($registro as $dato) { ?>
                                <?php if ($dato['status_tipo_pago'] != 2): ?>
                                <tr>
                                    <td><?php echo $dato['cod_tipo_pago']?></td>
                                    <td><?php echo $dato['medio_pago']?></td>
                                    <td><?php echo $dato['descripcion'];?></td>
                                    <td><?php echo $dato['nombre_divisa'];?></td>
                                    <td>
                                        <?php if ($dato['status']==1):?>
                                            <span class="badge bg-success">Activo</span>
                                        <?php else:?>
                                            <span class="badge bg-danger">Inactivo</span>
                                        <?php endif;?>
                                    </td>
                                    <td>
                                    <?php if($dato['cod_tipo_pago']!=1): ?>
                                        <?php if (!empty($_SESSION["permisos"]["config_finanza"]["editar"])): ?>
                                            <button name="editar" title="Editar" class="btn btn-primary btn-sm editar" data-toggle="modal" data-target="#editModal" 
                                            data-codigo="<?php echo $dato["cod_tipo_pago"]; ?>" 
                                            data-medio="<?php echo $dato["medio_pago"]; ?>" 
                                            data-desc="<?php echo $dato["descripcion"]; ?>"
                                            data-cod_metodo="<?php echo $dato["cod_metodo"]; ?>"
                                            data-status="<?php echo $dato["status"]; ?>" >
                                            <i class="fas fa-pencil-alt"></i>
                                            </button>
                                        <?php endif; ?>
                                        <?php if (!empty($_SESSION["permisos"]["config_finanza"]["eliminar"])): ?>
                                            <button name="eliminar" title="Eliminar" class="btn btn-danger btn-sm eliminar" data-toggle="modal" data-target="#eliminartpago"
                                            data-codigo="<?php echo $dato["cod_tipo_pago"]; ?>" 
                                            data-medio="<?php echo $dato["medio_pago"]." - ".$dato["descripcion"]; ?>" >
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endif; ?>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                    <!-- /.reponsive -->
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
        </div>
    </div><!-- /.container-fluid -->
</section>
        <!-- /.content -->
</div>
<!-- /.content-wrapper -->

    <!-- Modal para registrar tipo de pago -->
<?php if (!empty($_SESSION["permisos"]["config_finanza"]["registrar"])): ?>
<div class="modal fade" id="registrarTipoPagoModal" tabindex="-1" role="dialog" aria-labelledby="registrarTipoPagoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="registrarTipoPagoModalLabel">Registrar Tipo de Pago</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formularioregistrarTpago" method="post">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Tipo de Moneda<span class="text-danger" style="font-size: 15px;"> *</span></label>
                        <div class="btn-group btn-group-toggle w-100" data-toggle="buttons">
                            <label class="btn btn-outline-primary active">
                                <input type="radio" name="tipo_moneda" id="digital" value="2" checked> Digital
                            </label>
                            <label class="btn btn-outline-primary">
                                <input type="radio" name="tipo_moneda" id="efectivo" value="1"> Efectivo
                            </label>
                        </div>
                    </div>
                    <label for="nombre_tipo_pago">Medio de Pago<span class="text-danger" style="font-size: 15px;"> *</span></label>
                    <div class="input-group">
                        <select class="form-control" id="nombre_tipo_pago" name="cod_metodo" required>
                            <option value="" disabled selected>Seleccione un tipo de pago</option>
                            <?php 
                            foreach ($tipos_pago as $tipo): 
                                $modalidad = trim($tipo['modalidad']) ?: 'undefined';
                                echo "<!-- Debug: cod_metodo={$tipo['cod_metodo']}, medio_pago={$tipo['medio_pago']}, modalidad={$modalidad} -->\n";
                            ?>
                                <option value="<?= $tipo['cod_metodo']; ?>" 
                                        data-modalidad="<?= trim($modalidad); ?>">
                                    <?= htmlspecialchars($tipo['medio_pago']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary" type="button" data-toggle="modal" data-target="#modalRegistrarMedio">+</button>
                        </div>
                    </div>
                    
                    
                    <div class="form-group bancos-container">
                        <label for="banco">Seleccionar Banco<span class="text-danger" style="font-size: 15px;"> *</span></label>
                        <select class="form-control" id="banco" name="cod_cuenta_bancaria" required>
                            <option value="" disabled selected>Seleccione un banco</option>
                            <?php foreach ($bancos as $banco): ?>
                                <option value="<?= $banco['cod_cuenta_bancaria']; ?>"><?= $banco['nombre_banco']; ?> - <?= $banco['numero_cuenta']; ?> - <?= $banco['tipo_cuenta_nombre']; ?> - <?= $banco['divisa_nombre']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group cajas-container" style="display: none;">
                        <label for="caja">Seleccionar Caja<span class="text-danger" style="font-size: 15px;"> *</span></label>
                        <select class="form-control" id="caja" name="cod_caja" required>
                            <option value="" disabled selected>Seleccione una caja</option>
                            <?php foreach ($cajas as $caja): ?>
                                <option value="<?= $caja['cod_caja']; ?>"><?= $caja['nombre']; ?> - <?= $caja['divisa_nombre']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
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
    </div>
</div>
<?php endif; ?>
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
                window.location = 'tpago';
            }
        });
    </script>
<?php endif; ?>

<!-- =======================
MODAL EDITAR TIPO DE PAGO
============================= -->

<?php if (!empty($_SESSION["permisos"]["config_finanza"]["editar"])): ?>
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Editar Estado del Tipo de Pago</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editForm" method="post">
                    <div class="form-group">
                        <label for="codigo">Código</label>
                        <input type="text" class="form-control" id="codigo" name="codigo" readonly>
                        <input type="hidden" id="cod_metodo" name="cod_metodo">
                    </div>
                    <div class="form-group">
                        <label for="tpago">Tipo de Pago</label>
                        <input type="text" class="form-control" id="tpago" name="tpago" readonly>
                        <input type="hidden" id="origin" name="origin">
                    </div>
                    <div class="form-group">
                        <label for="descripcion">Descripción</label>
                        <input type="text" class="form-control" id="descripcion" readonly>
                    </div>
                    <div class="form-group">
                        <label for="status">Estado</label>
                        <select class="form-control" id="status" name="status">
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="submit" form="editForm" class="btn btn-primary" name="editar">Guardar cambios</button>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
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
                window.location = 'tpago';
            }
        });
    </script>
<?php endif; ?>

<!-- =======================
MODAL CONFIRMAR ELIMINAR 
============================= -->
<?php if (!empty($_SESSION["permisos"]["config_finanza"]["eliminar"])): ?>
<div class="modal fade" id="eliminartpago" tabindex="-1" aria-labelledby="eliminartpagoLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title" id="eliminartpagoLabel">Confirmar Eliminación</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="elimodal" method="post"> 
                    <p>¿Está seguro que desea eliminar el tipo de pago <b><span id="tpagonombre"></span></b>?</p>
                    <input type="hidden" id="tpagoCodigo" name="tpagoCodigo"> 
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="submit" form="elimodal" class="btn btn-danger" name="borrar">Eliminar</button>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
<?php if (isset($eliminar)): ?>
    <script>
        Swal.fire({
            title: '<?php echo $eliminar["title"]; ?>',
            text: '<?php echo $eliminar["message"]; ?>',
            icon: '<?php echo $eliminar["icon"]; ?>',
            confirmButtonText: 'Ok'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location = 'tpago';
            }
        });
    </script>
<?php endif; ?>

<!-- =============================
    MODAL NUEVO METODO DE PAGO
================================== -->
<?php if (!empty($_SESSION["permisos"]["config_finanza"]["registrar"])): ?>
<div class="modal fade" id="modalRegistrarMedio" tabindex="-1" aria-labelledby="modalRegistrarMedioLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header ">
                <h5 class="modal-title" id="exampleModalLabel">Registrar medio de pago</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formregistrarUnidad" action="index.php?pagina=tpago" method="post">
                    <div class="form-group">
                        <label for="tipo_medida">Medio de Pago</label>
                        <button class="btn btn-xs" data-toggle="tooltip" data-placement="top" title="Ingresa el medio de pago, por ejemplo: Transferencia, Efectivo, etc.">
                            <i class="fas fa-info-circle"></i>
                        </button>
                        <input type="text" class="form-control" name="medio" id="medio" placeholder="Ej: Transferencia" required>
                    </div>
                    <div class="form-group">
                        <label>Modalidad<span class="text-danger" style="font-size: 15px;"> *</span></label>
                        <div class="btn-group btn-group-toggle w-100" data-toggle="buttons">
                            <label class="btn btn-outline-primary active">
                                <input type="radio" name="modalidad" value="digital" checked> Digital
                            </label>
                            <label class="btn btn-outline-primary">
                                <input type="radio" name="modalidad" value="efectivo"> Efectivo
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary" name="guardarm">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>
<?php if (isset($registrarm)): ?>
    <script>
        Swal.fire({
            title: '<?php echo $registrarm["title"]; ?>',
            text: '<?php echo $registrarm["message"]; ?>',
            icon: '<?php echo $registrarm["icon"]; ?>',
            confirmButtonText: 'Ok'
        }).then((result) => {
            if (result.isConfirmed) {
                localStorage.setItem('medioModal', 'true');
                window.location='tpago';
            }
    });
</script> 
<?php endif; ?> 

<script>
    // NUEVA CATEGORIA DESDE PRODUCTO
//(Validar nombre)
$('#medio').blur(function (e){
        var buscar=$('#medio').val();
        $.post('index.php?pagina=tpago', {buscar}, function(response){
            if(response != ''){
                Swal.fire({
                    icon: 'warning',
                    title: 'Advertencia',
                    text: 'La categoria ya se encuentra registrada',
                    confirmButtonText: 'Aceptar'
                });
            }
        },'json');
    });
</script>

<!-- Modal para gestionar medios de pago -->
<div class="modal fade" id="gestionarMediosPagoModal" tabindex="-1" role="dialog" aria-labelledby="gestionarMediosPagoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="gestionarMediosPagoModalLabel">Gestionar Medios de Pago</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?php if (!empty($_SESSION["permisos"]["config_finanza"]["registrar"])): ?>
                    <button type="button" class="btn btn-primary mb-3" data-toggle="modal" data-target="#modalRegistrarMedio">
                        <i class="fas fa-plus"></i> Nuevo Medio de Pago
                    </button>
                <?php endif; ?>
                
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Medio de Pago</th>
                                <th>Modalidad</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($medios_pago as $tipo): ?>
                                <tr>
                                    <td><?= $tipo['cod_metodo']; ?></td>
                                    <td><?= htmlspecialchars($tipo['medio_pago']); ?></td>
                                    <td>
                                        <span class="badge <?= $tipo['modalidad'] === 'digital' ? 'badge-info' : 'badge-success'; ?>">
                                            <?= ucfirst($tipo['modalidad']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($tipo['status'] == 1): ?>
                                            <span class="badge badge-success">Activo</span>
                                        <?php else: ?>
                                            <span class="badge badge-danger">Inactivo</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($_SESSION["permisos"]["config_finanza"]["editar"]) && $tipo['cod_metodo'] != 1): ?>
                                            <button type="button" class="btn btn-primary btn-sm editar-medio" 
                                                    data-cod-metodo="<?= $tipo['cod_metodo']; ?>"
                                                    data-medio-pago="<?= htmlspecialchars($tipo['medio_pago']); ?>"
                                                    data-modalidad="<?= $tipo['modalidad']; ?>"
                                                    data-status="<?= $tipo['status']; ?>">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        <?php endif; ?>
                                        <?php if (!empty($_SESSION["permisos"]["config_finanza"]["eliminar"]) && $tipo['cod_metodo'] != 1): ?>
                                            <button type="button" class="btn btn-danger btn-sm eliminar-medio"
                                                    data-cod-metodo="<?= $tipo['cod_metodo']; ?>"
                                                    data-medio-pago="<?= htmlspecialchars($tipo['medio_pago']); ?>">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para editar medio de pago -->
<div class="modal fade" id="editarMedioModal" tabindex="-1" role="dialog" aria-labelledby="editarMedioModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editarMedioModalLabel">Editar Medio de Pago</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formEditarMedio" method="post">
                <div class="modal-body">
                    <input type="hidden" name="cod_metodo" id="editar_cod_metodo">
                    <div class="form-group">
                        <label for="editar_medio_pago">Medio de Pago</label>
                        <input type="text" class="form-control" name="medio" id="editar_medio_pago" required>
                    </div>
                    <div class="form-group">
                        <label>Modalidad</label>
                        <div class="btn-group btn-group-toggle w-100" data-toggle="buttons">
                            <label class="btn btn-outline-primary">
                                <input type="radio" name="modalidad" value="digital"> Digital
                            </label>
                            <label class="btn btn-outline-primary">
                                <input type="radio" name="modalidad" value="efectivo"> Efectivo
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="editar_status">Estado</label>
                        <select class="form-control" name="status" id="editar_status">
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" name="editarm">Guardar cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para confirmar eliminación de medio de pago -->
<div class="modal fade" id="eliminarMedioModal" tabindex="-1" role="dialog" aria-labelledby="eliminarMedioModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title" id="eliminarMedioModalLabel">Confirmar Eliminación</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formEliminarMedio" method="post">
                <div class="modal-body">
                    <input type="hidden" name="cod_metodo" id="eliminar_cod_metodo">
                    <p>¿Está seguro que desea eliminar el medio de pago <b id="eliminar_medio_nombre"></b>?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger" name="borrarm">Eliminar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .modal-backdrop + .modal-backdrop {
        z-index: 1060;
    }
    #modalRegistrarMedio {
        z-index: 1065;
    }
    #gestionarMediosPagoModal {
        z-index: 1050;
    }
</style>

<?php if (isset($editarm)): ?>
    <script>
        Swal.fire({
            title: '<?php echo $editarm["title"]; ?>',
            text: '<?php echo $editarm["message"]; ?>',
            icon: '<?php echo $editarm["icon"]; ?>',
            confirmButtonText: 'Ok'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location = 'tpago';
            }
        });
    </script>
<?php endif; ?>

<?php if (isset($borrarm)): ?>
    <script>
        Swal.fire({
            title: '<?php echo $borrarm["title"]; ?>',
            text: '<?php echo $borrarm["message"]; ?>',
            icon: '<?php echo $borrarm["icon"]; ?>',
            confirmButtonText: 'Ok'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location = 'tpago';
            }
        });
    </script>
<?php endif; ?>

<script src="vista/dist/js/modulos-js/tpago.js"></script>