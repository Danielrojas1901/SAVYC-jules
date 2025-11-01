<?php 
#Requerir al controlador
require_once "controlador/marcas.php";
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Marcas</h1>
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
                        <?php if (!empty($_SESSION["permisos"]["config_producto"]["registrar"])): ?>
                            <!-- Botón para ventana modal -->
                            <button class="btn btn-primary" data-toggle="modal" data-target="#modalRegistrarMarca">Registrar marca</button>
                        <?php endif; ?>
                        </div>
                        <div class="card-body">
                        <div class="table-responsive">
                            <table id="marcas" class="table table-bordered table-striped table-hover datatable" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>Código</th>
                                        <th>Marca</th>
                                        <th>Status</th>
                                        <th>Acciones</th>
                                    </tr>         
                                </thead>
                                <tbody>
                                <!-- Tabla con los datos que se muestren dinamicamente -->
                                    <?php
                                    foreach ($registro as $marca){
                                        ?>
                                        <tr>
                                            <td> <?php echo $marca["cod_marca"] ?></td>
                                            <td> <?php echo $marca["nombre"] ?></td>
                                            <td>
                                                <?php if ($marca['status']==1):?>
                                                    <span class="badge bg-success">Activo</span>
                                                <?php else:?>
                                                    <span class="badge bg-danger">Inactivo</span>
                                                <?php endif;?>
                                            </td>
                                            <!-- Botones -->
                                            <td>
                                            <?php if (!empty($_SESSION["permisos"]["config_producto"]["editar"])): ?>
                                                <button name="editar" title="Editar" class="btn btn-primary btn-sm editar" data-toggle="modal" data-target="#editModal"
                                                data-codigo="<?php echo $marca["cod_marca"]; ?>"
                                                data-nombre="<?php echo $marca["nombre"]; ?>"
                                                data-status="<?php echo $marca["status"]; ?>">
                                                <i class="fas fa-pencil-alt"></i></button>
                                            <?php endif; ?>
                                            <?php if (!empty($_SESSION["permisos"]["config_producto"]["eliminar"])): ?>
                                                <button name="eliminar" title="Eliminar" class="btn btn-danger btn-sm eliminar" data-toggle="modal" data-target="#eliminarModal"
                                                data-codigo="<?php echo $marca["cod_marca"]; ?>"
                                                data-nombre="<?php echo $marca["nombre"]; ?>"
                                                data-status="<?php echo $marca["status"]; ?>">
                                                <i class="fas fa-trash-alt"></i></button>
                                            <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
<!-- =============================
    MODAL REGISTRAR MARCA
================================== -->
                    <div class="modal fade" id="modalRegistrarMarca" tabindex="-1" aria-labelledby="modalRegistrarMarcaLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="registrarModalLabel">Registrar marca</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>

                                <div class="modal-body">
                                    <form id="formRegistrarMarca" method="post">
                                        <div class="form-group">
                                            <label for="nombre">Nombre de la marca</label>
                                            <!-- TOOLTIPS-->
                                            <button class="btn btn-xs" data-toggle="tooltip" data-placement="top" title="Ingresa el nombre de una marca para productos, por ejemplo: Charcutería">
                                                    <i class="fas fa-info-circle"></i>
                                            </button>
                                            <script>
                                                $(function () {
                                                    $('[data-toggle="tooltip"]').tooltip();
                                                });
                                            </script>
                                            <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Ej: Polar" required>
                                            <div class="invalid-feedback" style="display: none;"></div>
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
                window.location = 'marcas';
            }
        });
    </script>
<?php endif; ?>
<!-- =============================
    MODAL EDITAR MARCA
================================== -->
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
                                            <label for="codigo">Nombre</label>
                                            <input type="text" class="form-control" id="name" name="nombre">
                                            <div class="invalid-feedback" style="display: none;"></div>
                                            <input type="hidden" class="form-control" id="origin" name="origin" readonly>
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
                window.location = 'marcas';
            }
        });
    </script>
<?php endif; ?>

<!-- ====================================
    MODAL CONFIRMAR ELIMINAR MARCA
========================================= -->
                    <div class="modal fade" id="eliminarModal" tabindex="-1" aria-labelledby="eliminarModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header bg-danger">
                                    <h5 class="modal-title" id="eliminarModalLabel">Eliminar marca</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <form id="eliminarForm" method="post">
                                        <div class="form-group">
                                            <p>¿Estás seguro que deseas eliminar a <b><span id="catnombre"></b></span>?</p>
                                            <input type="hidden" id="marcacodigo" name="marcacodigo">
                                            <input type="hidden" id="statusDelete" name="statusDelete">
                                            <input type="hidden" id="marcanombre" name="nombre">
                                        </div>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                                    <button type="submit" form="eliminarForm" class="btn btn-danger" id="confimDelete" name="borrar">Eliminar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>  
        </div>      
    </section>
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
                window.location = 'marcas';
            }
        });
    </script>
<?php endif; ?>
<?php
if (isset($advertencia)): ?>
    <script>
        Swal.fire({
            title: '<?php echo $advertencia["title"]; ?>',
            text: '<?php echo $advertencia["message"]; ?>',
            icon: '<?php echo $advertencia["icon"]; ?>',
            confirmButtonText: 'Ok'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location = 'marcas';
            }
        });
    </script>
<?php endif; ?>

<script src="vista/dist/js/modulos-js/marcas.js"> </script>