<?php require_once 'controlador/categoriag.php' ?>
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Categoría de Gastos</h1>
                    <p>Organiza tus gastos en categorías y controla sus frecuencias para tener una visión clara de tus finanzas.</p>
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
                            <?php if (!empty($_SESSION["permisos"]["config_finanza"]["registrar"])): ?>
                                <button class="btn btn-primary ml-2" data-toggle="modal" data-target="#modalCategoria">
                                    Registrar categoría
                                </button>

                                <button class="btn btn-primary ml-2" data-toggle="modal" data-target="#modalregistrarFrecuencia">
                                    Registrar frecuencia
                                </button>
                            <?php endif; ?>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <h5>Frecuencia de Pago</h5>
                                <table id="tabla_frecuencias" class="table table-bordered table-striped text-center align-middle datatable" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>Código</th>
                                            <th>nombre</th>
                                            <th>Días</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($frecuencia)): ?>
                                            <tr>
                                                <td colspan="8" class="text-center">No hay frecuencias de pago</td>
                                            </tr>
                                        <?php else: ?>

                                            <?php
                                            foreach ($frecuencia as $f) {
                                            ?>
                                                <td><?php echo $f['cod_frecuencia']
                                                    ?></td>
                                                <td><?php echo $f['nombre']
                                                    ?></td>
                                                <td><?php echo $f['dias']
                                                    ?></td>
                                                </tr>
                                            <?php }
                                            ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <h5>Categoría de Gastos</h5>
                                <table id="tabla_categorias_gastos" class="table table-bordered table-striped text-center align-middle datatable" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>Código</th>
                                            <th>Fecha</th>
                                            <th>Nombre</th>
                                            <th>Frecuancia de gastos</th>
                                            <th>Tipo de gasto</th>
                                            <th>Naturaleza</th>
                                            <th>Status</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($categorias)): ?>
                                            <tr>
                                                <td colspan="8" class="text-center">No hay categorías de gastos</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($categorias as $c) { ?>
                                                <?php 
                                                ?>
                                                <tr>
                                                    <td><?php echo $c['cod_cat_gasto']
                                                        ?></td>
                                                    <td><?php echo $c['fecha']
                                                        ?></td>
                                                    <td><?php echo $c['categoria']
                                                        ?></td>
                                                    <td><?php echo $c['nombre'];
                                                        ?></td>
                                                    <td><?php echo $c['nombret']
                                                        ?>
                                                    </td>
                                                    <td><?php echo $c['nombre_naturaleza']
                                                        ?>
                                                    <td>
                                                        <?php if ($c['status_cat_gasto'] == 1): ?>
                                                            <span class="badge bg-success">Activo</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-danger">Inactivo</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php if (!empty($_SESSION["permisos"]["config_finanza"]["editar"])): ?>
                                                            <button name="editar" title="Editar" class="btn btn-primary btn-sm editar" data-toggle="modal" data-target="#modificat"

                                                                data-nombre="<?php echo $c["categoria"]; ?>"
                                                                data-codigo="<?php echo $c["cod_cat_gasto"]; ?>"
                                                                data-status="<?php echo $c["status_cat_gasto"]; ?>">
                                                                <i class="fas fa-pencil-alt"></i>
                                                            </button>
                                                        <?php endif; ?>
                                                        <?php if (!empty($_SESSION["permisos"]["config_finanza"]["eliminar"])): ?>
                                                            <button name="eliminar" title="Eliminar" class="btn btn-danger btn-sm eliminar" data-toggle="modal" data-target="#modaleliminar"
                                                                data-codigo="<?php echo $c["cod_cat_gasto"]; ?>"
                                                                data-nombre="<?php echo $c["categoria"]; ?>"
                                                                data-status="<?php echo $c["status_cat_gasto"]; ?>">
                                                                <i class="fas fa-trash-alt"></i>
                                                            </button>
                                                        <?php endif; ?>


                                                    </td>
                                                </tr>
                                                <?php 
                                                ?>
                                            <?php } ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- =============================
            MODAL REGISTRAR CATEGORIA PARA GASTOS 
        ================================== -->
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
                                    window.location = 'categoriag';
                                }
                            });
                        </script>
                    <?php endif; ?>


                </div>
            </div>
        </div>
    </section>
</div>



<!-- =============================
                    MODAL REGISTRAR FRECUENCIA DE PAGOS PARA GASTOS 
                ================================== -->
<div class="modal fade" id="modalregistrarFrecuencia" tabindex="-1" aria-labelledby="modalregistrarFrecuenciaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Registrar frecuancia de pagos de gastos</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form id="formregistrarFrecuancia" method="post">
                  
                    <div class="form-group">
                        <label for="frecuencia">Frecuencia de pagos</label>
                        
                        <button class="btn btn-xs" data-toggle="tooltip" data-placement="top" title="Ingrese el plazo del pago de gastos, por ejemplo: Quincenal">
                            <i class="fas fa-info-circle"></i>
                        </button>
                        <script>
                            $(function() {
                                $('[data-toggle="tooltip"]').tooltip();
                            });
                        </script>
                        <input type="text" class="form-control" name="frecuencia" placeholder="Ej: Mensual." id="frecuencia" maxlength="15">
                        <div class="invalid-feedback" style="display: none;"></div>
                    </div>
                    
                    <div class="form-group">
                        <label for="dias">Días de plazo</label>
                       
                        <button class="btn btn-xs" data-toggle="tooltip" data-placement="top" title="Se muestran el plazo de días para el pago del gasto, por ejemplo: 15 días.">
                            <i class="fas fa-info-circle"></i>
                        </button>
                        <script>
                            $(function() {
                                $('[data-toggle="tooltip"]').tooltip();
                            });
                        </script>
                        <input type="number" class="form-control" name="dias" placeholder="Ej: 15." id="dias" maxlength="3">
                        <div class="invalid-feedback" style="display: none;"></div>
                    </div>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="submit" class="btn btn-dark" name="guardar_frecuencia" onclick="return validacion();">Guardar</button>
            </div>
            </form>
        </div>
    </div>
</div>
<?php
if (isset($guardarF)): ?>
    <script>
        Swal.fire({
            title: '<?php echo $guardarF["title"]; ?>',
            text: '<?php echo $guardarF["message"]; ?>',
            icon: '<?php echo $guardarF["icon"]; ?>',
            confirmButtonText: 'Ok'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location = 'categoriag';
            }
        });
    </script>
<?php endif; ?>

<div class="modal fade" id="modificat">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Editar Categoría de Gasto</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form role="form" method="post" id="form-editar-gasto">
                <div class="modal-body">
                    <input type="hidden" name="cod_cat_gasto" id="cod_cat_gasto">
                    <div class="form-group">
                        <label for="cod_cat_gasto">Código</label>
                        <input type="text" class="form-control" name="cod_cat_gasto_oculto" id="cod_cat_gasto_oculto" readonly>
                    </div>
                    <div class="form-group">
                        <label for="nombre">Categoría</label>
                        <input type="text" class="form-control" name="nombre" id="nombreE">
                        <div class="invalid-feedback" style="display: none;"></div>
                        <input type="hidden" id="origin" class="form-control" name="origin" maxlength="10">
                    </div>
                    <div class="form-group">
                        <label for="status">Estatus</label>
                        <select name="status_cat_gasto" id="status">
                            <option value="1">Activo</option>
                            <option value="3">Inactivo</option>
                        </select>
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
if (isset($editar)): ?>
    <script>
        Swal.fire({
            title: '<?php echo $editar["title"]; ?>',
            text: '<?php echo $editar["message"]; ?>',
            icon: '<?php echo $editar["icon"]; ?>',
            confirmButtonText: 'Ok'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location = 'categoriag';
            }
        });
    </script>
<?php endif; ?>

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
                    <p>¿Estás seguro de eliminar la categoría: <b><span id=categoria></span>?</p></b>
                    <input type="hidden" name="cod_cat_gasto" id="cod_eliminar">
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
                window.location = 'categoriag';
            }
        });
    </script>
<?php endif; ?>


<script src="vista/dist/js/modulos-js/gasto.js"></script>

</section>
</div>