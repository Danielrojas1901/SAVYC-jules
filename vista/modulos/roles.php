<?php require_once 'controlador/roles.php'; ?>
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Roles y Permisos</h1>
                    <p>Configura los roles y permisos de usuario.</p>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <?php if (!empty($_SESSION["permisos"]["seguridad"]["registrar"])): ?>
                            <button class="btn btn-primary" data-toggle="modal" data-target="#modalregistrarroles">Registrar Rol</button>
                            <?php endif; ?>
                        </div>
                        <div class="card-body">
                            
                                <table id="categorias" class="table table-bordered table-striped datatable" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>Código</th>
                                            <th>Rol</th>
                                            <th>Status</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php

                                        foreach ($registro as $dato) {
                                        ?>
                                            <?php if ($dato['status'] != 2): ?>
                                                <tr>
                                                    <td><?php echo $dato['cod_tipo_usuario'] ?></td>
                                                    <td><?php echo $dato['rol'] ?></td>
                                                    <td>
                                                        <?php if ($dato['status'] == 1): ?>
                                                            <span class="badge bg-success">Activo</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-danger">Inactivo</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php if($dato['cod_tipo_usuario']!=1): ?>
                                                            <?php if (!empty($_SESSION["permisos"]["seguridad"]["editar"])): ?>
                                                            <button name="ajustar" class="btn btn-primary btn-sm editar" title="Editar" data-toggle="modal" data-target="#modalmodificarol"
                                                                data-cod="<?php echo $dato['cod_tipo_usuario']; ?>"
                                                                data-rol="<?php echo $dato['rol']; ?>"
                                                                data-status="<?php echo $dato['status']; ?>">
                                                                <i class="fas fa-pencil-alt"></i>
                                                            </button>
                                                            <?php endif; ?>
                                                            <?php if (!empty($_SESSION["permisos"]["seguridad"]["eliminar"])): ?>
                                                            <button name="confirmar" class="btn btn-danger btn-sm eliminar" title="Eliminar" data-toggle="modal" id="modificar" data-target="#modaleliminar" 
                                                            data-cod="<?php echo $dato['cod_tipo_usuario']; ?>"
                                                            data-roleliminar="<?php echo $dato['rol']; ?>"
                                                            ><i class="fas fa-trash-alt"></i></button>
                                                            <?php endif; ?>
                                                        <?php endif;?>
                                                    </td>
                                                </tr>
                                            <?php endif; ?>
                                        <?php } ?>

                                    </tbody>
                                </table>
                            
                        </div>
                    </div>

<!-- =======================
MODAL REGISTRAR ROLES
============================= -->

<div class="modal fade" id="modalregistrarroles" tabindex="-1" aria-labelledby="modalregistrarrolLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Registrar Rol</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form id="formAgregarRol" method="post">
                    <!--   NOMBRE DEL ROL     -->
                    <div class="form-group">
                        <label for="rol">Rol<span class="text-danger" style="font-size: 15px;"> *</span></label>
                        <input type="text" class="form-control" id="rol1" name="rol" placeholder="Ej: Vendedor." maxlength="30">
                    </div>
                    
                    <div class="form-group">
                        <label>Permisos por Módulo:</label>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="bg-primary text-white">
                                    <tr>
                                        <th style="width: 40%;">Módulo</th>
                                        <th style="width: 60%;">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($modulos as $modulo): ?>
                                    <tr>
                                        <td>
                                            <div class="form-check">
                                                <input type="checkbox" name="modulo[]" value="<?php echo $modulo['cod_modulo']; ?>" 
                                                    id="modulo<?php echo $modulo['cod_modulo']; ?>" 
                                                    class="form-check-input check-modulo">
                                                <label class="form-check-label" for="modulo<?php echo $modulo['cod_modulo']; ?>">
                                                    <?php 
                                                    $nombresMostrados = [
                                                        'cuentas_pendiente' => 'Cuentas Pendientes',
                                                        'config_producto' => 'Configuración de Producto',
                                                        'config_finanza' => 'Configuración de Finanza'
                                                    ];
                                                    
                                                    echo isset($nombresMostrados[$modulo['nombre']]) ? $nombresMostrados[$modulo['nombre']] : $modulo['nombre']; 
                                                    ?>
                                                </label>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="permisos-container" id="permisos-modulo-<?php echo $modulo['cod_modulo']; ?>" style="display: none;">
                                                <?php foreach ($permisos as $permiso): ?>
                                                <div class="form-check form-check-inline">
                                                    <input type="checkbox" 
                                                        name="permisos[<?php echo $modulo['cod_modulo']; ?>][]" 
                                                        value="<?php echo $permiso['cod_crud']; ?>" 
                                                        id="permiso<?php echo $modulo['cod_modulo']; ?>-<?php echo $permiso['cod_crud']; ?>" 
                                                        class="form-check-input check-permiso">
                                                    <label class="form-check-label" for="permiso<?php echo $modulo['cod_modulo']; ?>-<?php echo $permiso['cod_crud']; ?>">
                                                        <?php echo $permiso['nombre']; ?>
                                                    </label>
                                                </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary" name="guardar">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>
</section>
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
                window.location = 'roles';
            }
        });
    </script>
<?php endif; ?>

<!-- MODAL EDITAR -->
<div class="modal fade" id="modalmodificarol">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Editar Rol</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" id="form-editar-rol">
                <div class="modal-body">
                    <input type="hidden" name="cod_tipo_usuario" id="cod_oculto">
                    <div class="form-group">
                        <label for="cod">Código</label>
                        <input type="text" class="form-control" name="cod_tipo_usuario" id="cod" readonly>
                    </div>
                    <div class="form-group">
                        <label for="rol">Rol</label>
                        <input type="text" class="form-control" name="rol" id="rol">
                        <div class="invalid-feedback" style="display: none;"></div>
                        <input type="hidden" id="rol_origin" name="origin">

                    </div>
                    <div class="form-group">
                        <label for="status">Status</label>
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
</div>
</div>
</div>
</section>
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
                window.location = 'roles';
            }
        });
    </script>
<?php endif; ?>

<!--    MODAL DE ADVERTENCIA    -->
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
                <p>¿Estás seguro de eliminar el rol: <b><span id="tipoderol"></span></b>?</p>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <form method="post">

                    <input type="hidden" name="eliminar" id="cod_eliminar" value="">
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </form>

            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
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
                window.location = 'roles';
            }
        });
    </script>
<?php endif; ?>

<script>
$(document).ready(function() {
    // Mostrar/ocultar permisos cuando se selecciona un módulo
    $('.check-modulo').change(function() {
        var moduloId = $(this).val();
        var permisosContainer = $('#permisos-modulo-' + moduloId);
        
        if($(this).is(':checked')) {
            permisosContainer.slideDown();
            
            // NUEVO CÓDIGO: Seleccionar automáticamente el permiso de consulta (ID 2)
            var checkboxConsulta = $('#permiso' + moduloId + '-2');
            checkboxConsulta.prop('checked', true);
            checkboxConsulta.prop('disabled', true); // No se puede deseleccionar
            
            // Añadir un campo hidden para asegurar que el valor se envíe aunque esté disabled
            if ($('#hidden-permiso-' + moduloId + '-2').length === 0) {
                $('<input>').attr({
                    type: 'hidden',
                    id: 'hidden-permiso-' + moduloId + '-2',
                    name: 'permisos[' + moduloId + '][]',
                    value: '2' // El valor del permiso de consulta
                }).appendTo(permisosContainer);
            }
        } else {
            permisosContainer.slideUp();
            // Desmarcar todos los permisos de este módulo
            permisosContainer.find('.check-permiso').prop('checked', false).prop('disabled', false);
            
            // Eliminar el campo hidden si existe
            $('#hidden-permiso-' + moduloId + '-2').remove();
        }
        
    });
    
    // Capturar el envío del formulario para mostrar lo que se enviará
    $('#formAgregarRol').submit(function(e) {
        // No detener el envío pero mostrar lo que se enviará
        console.log('Datos del formulario que se enviarán:');
        
        // Crear un objeto FormData para ver todos los datos
        var formData = new FormData(this);
        for (var pair of formData.entries()) {
            console.log(pair[0] + ': ' + pair[1]);
        }
        
        // Opcionalmente, puedes agregar aquí un return false; para evitar el envío real
        // y solo ver lo que se enviaría en la consola.
        // return false;
    });
    
    // NUEVO CÓDIGO: Evitar que se deseleccione el permiso de consulta si el módulo está marcado
    $(document).on('click', '.check-permiso', function(e) {
        var permisoParts = $(this).attr('id').split('-');
        var moduloId = permisoParts[0].replace('permiso', '');
        var permisoId = permisoParts[1];
        
        // Si es el permiso de consulta (ID 2) y el módulo está marcado, evitar desmarcar
        if (permisoId == '2' && $('#modulo' + moduloId).is(':checked')) {
            e.preventDefault();
            return false;
        }
    });
});
</script>


<script src="vista/dist/js/modulos-js/rol.js"></script>


