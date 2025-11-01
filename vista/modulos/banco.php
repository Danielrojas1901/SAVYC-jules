<?php

require_once "controlador/banco.php";
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-9">
                    <h1>Bancos</h1>
                    <p>En esta sección se pueden registrar las entidades bancarias.</p>
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
                        <?php if (isset($_SESSION["permisos"]["config_finanza"]["registrar"])): ?>
                            <div class="card-header">
                             
                                <button class="btn btn-primary" data-toggle="modal" data-target="#modalRegistrarbanco">Registrar Entidad Bancaria</button>
                            </div>
                        <?php endif; ?>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="tabla_bancos" class="table table-bordered table-striped table-hover datatable" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>Código</th>
                                            <th>Nombre De La Entidad Bancaria</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                       
                                        <?php
                                        foreach ($registro as $banco) {
                                        ?>
                                            <tr>
                                                <td> <?php echo $banco["cod_banco"] ?></td>
                                                <td> <?php echo $banco["nombre_banco"] ?></td>
                                               
                                                <td>
                                                    <?php if (!empty($_SESSION["permisos"]["config_finanza"]["editar"])): ?>
                                                        <button name="editar" title="Editar" class="btn btn-primary btn-sm editar" data-toggle="modal" data-target="#editModal"
                                                            data-codigo="<?php echo $banco["cod_banco"]; ?>"
                                                            data-nombre="<?php echo $banco["nombre_banco"]; ?>">
                                                            <i class="fas fa-pencil-alt"></i></button>
                                                    <?php endif; ?>
                                                    <?php if (!empty($_SESSION["permisos"]["config_finanza"]["eliminar"])): ?>
                                                        <button name="eliminar" title="Eliminar" class="btn btn-danger btn-sm eliminar" data-toggle="modal" data-target="#eliminarModal"
                                                            data-codigo="<?php echo $banco["cod_banco"]; ?>"
                                                            data-nombre="<?php echo $banco["nombre_banco"]; ?>">
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
    MODAL REGISTRAR BANCO 
================================== -->
                    <div class="modal fade" id="modalRegistrarbanco" tabindex="-1" aria-labelledby="modalRegistrarbancoLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="registrarModalLabel">Registrar Entidad Bancaria</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>

                                <div class="modal-body">
                                    <form id="formRegistrarbanco" method="post">
                                        <div class="form-group">
                                            <label for="nombre">Nombre de la Entidad Bancaria</label>
                                          
                                            <button class="btn btn-xs" data-toggle="tooltip" data-placement="top" title="Ingresa el nombre de una entidad bancaria (ej Banco de Venezuela).">
                                                <i class="fas fa-info-circle"></i>
                                            </button>
                                            <script>
                                                $(function() {
                                                    $('[data-toggle="tooltip"]').tooltip();
                                                });
                                            </script>
                                            <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Ej: Banco Mercantil" maxlength="50" required>

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
                                    window.location = 'banco';
                                }
                            });
                        </script>
                    <?php endif; ?>
                    <!-- =============================
    MODAL EDITAR BANCO
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
                                            <input type="text" class="form-control" id="codigo" name="cod_banco" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="nombre">Nombre De La Entidad Bancaria</label>
                                            <input type="text" class="form-control" id="nombre1" name="nombre" maxlength="50" required>
                                            <div class="invalid-feedback" style="display: none;"></div>
                                            <input type="hidden" class="form-control" id="origin" name="origin" readonly>
                                        </div>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                                    <button type="submit" form="editForm" class="btn btn-primary" name="actualizar">Editar</button>
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
                                    window.location = 'banco';
                                }
                            });
                        </script>
                    <?php endif; ?>

                    <!-- ====================================
    MODAL CONFIRMAR ELIMINAR BANCO
========================================= -->
                    <div class="modal fade" id="eliminarModal" tabindex="-1" aria-labelledby="eliminarModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header bg-danger">
                                    <h5 class="modal-title" id="eliminarModalLabel">Eliminar banco</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <form id="eliminarForm" method="post">
                                        <input type="hidden" id="bancoCodigo" name="cod_banco">
                                        <div class="form-group">
                                            <p>¿Estás seguro que deseas eliminar? <b><span id="bancoNombre"></span></b></p>
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
                window.location = 'banco';
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
                window.location = 'banco';
            }
        });
    </script>
<?php endif; ?>

<script src="vista/dist/js/modulos-js/banco.js"> </script>