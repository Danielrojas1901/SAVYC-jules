<?php require_once 'controlador/unidad.php' ?>
<div class="content-wrapper">
    <section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-9">
                <h1>Unidades de medida</h1>
                <p>En esta sección se puede gestionar las unidades de medida para los productos.</p>
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
                            <button class="btn btn-primary" data-toggle="modal" data-target="#modalregistrarUnidad">Registrar Unidad de medida</button>
                            <?php endif; ?>
                        </div>
                        <div class="card-body">
                            <!-- MOSTRAR EL REGISTRO DE UNIDADES DE MEDIDA -->
                            <div class="table-responsive">
                                <table id="unidad" class="table table-bordered table-striped datatable" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>Código</th>
                                            <th>Tipo de medida</th>
                                            <th>Status</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        foreach ($datos as $dato) {
                                            if ($dato['status'] != 2) {
                                        ?>
                                                <tr>
                                                    <td><?php echo $dato['cod_unidad'] ?></td>
                                                    <td><?php echo $dato['tipo_medida'] ?></td>
                                                    <td>
                                                        <?php if ($dato['status'] == 1): ?>
                                                            <span class="badge bg-success">Activo</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-danger">Inactivo</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php if (!empty($_SESSION["permisos"]["config_producto"]["editar"])): ?>
                                                        <button name="ajustar" class="btn btn-primary btn-sm editar" title="Editar" data-toggle="modal" data-target="#modalmodificarunidad"
                                                            data-cod="<?php echo $dato['cod_unidad']; ?>"
                                                            data-tipo="<?php echo $dato['tipo_medida']; ?>"
                                                            data-status="<?php echo $dato['status']; ?>">
                                                            <i class="fas fa-pencil-alt"></i>
                                                        </button>
                                                        <?php endif; ?>
                                                        <?php if (!empty($_SESSION["permisos"]["config_producto"]["eliminar"])): ?>
                                                        <button name="confirmar" class="btn btn-danger btn-sm eliminar" title="Eliminar" data-toggle="modal" id="modificar" data-target="#modaleliminar"
                                                        data-cod="<?php echo $dato['cod_unidad']; ?>"
                                                        data-tipo="<?php echo $dato['tipo_medida']; ?>">
                                                        <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                        <?php
                                            }
                                        } ?>
                                    </tbody>
                            </div>
                            </table>
                        </div>
                    </div>

<!-- =======================
MODAL REGISTRAR Unidades de medida 
============================= -->

                    <div class="modal fade" id="modalregistrarUnidad" tabindex="-1" aria-labelledby="modalregistrarUnidadLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header bg-primary">
                                    <h5 class="modal-title" id="exampleModalLabel">Registrar Unidad de medida</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>

                                <div class="modal-body">
                                    <form id="formregistrarUnidad" method="post">
                                        <!--   TIPO DE MEDIDA      -->
                                        <div class="form-group">
                                            <label for="tipo_medida">Tipo de medida</label>
                                            <!-- TOOLTIPS-->
                                            <button class="btn btn-xs" data-toggle="tooltip" data-placement="top" title="Ingresa la unidad de medida para la venta de productos, por ejemplo: Kg">
                                                <i class="fas fa-info-circle"></i>
                                            </button>
                                            <script>
                                                $(function () {
                                                    $('[data-toggle="tooltip"]').tooltip();
                                                });
                                            </script>
                                            <input type="text" class="form-control" name="tipo_medida" placeholder="Ej: Kg." id="tipo_medida1" maxlength="10">
                                            <div class="invalid-feedback" style="display: none;"></div>
                                        </div>
                                </div>
                                <div class="modal-footer justify-content-between">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                    <button type="submit" class="btn btn-primary" name="guardar" onclick="return validacion();">Guardar</button>
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
                                    window.location = 'unidad';
                                }
                            });
                        </script>
                    <?php endif; ?>

                    <!-- MODAL EDITAR -->
                    <div class="modal fade" id="modalmodificarunidad">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header bg-primary">
                                    <h4 class="modal-title">Editar Unidad</h4>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <form role="form" method="post" id="form-editar-unidad">

                                    <!--   CODIGO DE LA UNIDAD    -->

                                    <div class="modal-body">
                                        <input type="hidden" name="cod_unidad" id="cod_unidad_oculto">
                                        <div class="form-group">
                                            <label for="cod_unidad">Código</label>
                                            <input type="text" class="form-control" name="cod_unidad" id="cod_unidad" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="tipo_medida">Tipo de medida</label>
                                            <input type="text" class="form-control" name="tipo_medida" id="tipo_medida" maxlength="10">
                                            <div class="invalid-feedback" style="display: none;"></div>
                                            <input type="hidden" id="origin" class="form-control" name="origin" maxlength="10">
                                        </div>
                                        <div class="form-group">
                                            <label for="status">Estatus</label>
                                            <select name="status" id="status" class="form-control">
                                                <option value="1">Activo</option>
                                                <option value="0">Inactivo</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer justify-content-between">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                        <button type="submit" class="btn btn-primary" name="editar">Editar</button>
                                    </div>
                                </form>
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
                                    window.location = 'unidad';
                                }
                            });
                        </script>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Confirmar Eliminar Modal -->
<div class="modal fade" id="modaleliminar">
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
                    <p>¿Estás seguro de eliminar la unidad medida: <b><span id=tipomedidaD></span>?</p></b>
                    <input type="hidden" name="eliminar" id="cod_eliminar" >
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger">Eliminar</button>
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
                window.location = 'unidad';
            }
        });
    </script>
<?php endif; ?>


<script src="vista/dist/js/modulos-js/unidad.js"></script>

</section>
</div>