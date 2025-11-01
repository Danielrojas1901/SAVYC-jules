<?php 
#Requerir al controlador
require_once "controlador/usuarios.php";
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Usuarios</h1>
                    <p>En esta sección se puede gestionar los usuarios del sistema</p>
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
                            <button class="btn btn-primary" data-toggle="modal" data-target="#modalregistrarusuario">Registrar usuario</button>
                            <?php endif; ?>
                        </div>
                        <div class="card-body">
                        <div class="table-responsive">
                            <table id="usuarios" class="table table-bordered table-striped table-hover datatable" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>Código</th>
                                        <th>Nombre</th>
                                        <th>User</th>
                                        <th>Rol</th>
                                        <th>Status</th>
                                        <th>Acciones</th>
                                    </tr>         
                                </thead>
                                <tbody>
                                    <?php foreach ($registro as $usuario){
                                            if($usuario["cod_usuario"]!=1):?>
                                        <tr>
                                            <td> <?php echo $usuario["cod_usuario"] ?></td>
                                            <td> <?php echo $usuario["nombre"] ?></td>
                                            <td> <?php echo $usuario["user"] ?></td>
                                            <td> <?php echo $usuario["rol"] ?></td>
                                            <td>
                                                <?php if ($usuario['status']==1):?>
                                                    <span class="badge bg-success">Activo</span>
                                                <?php else:?>
                                                    <span class="badge bg-danger">Inactivo</span>
                                                <?php endif;?>
                                            </td>
                                            <td>
                                                <?php if (!empty($_SESSION["permisos"]["seguridad"]["editar"])): ?>
                                                <button name="editar" title="Editar" class="btn btn-primary btn-sm editar" data-toggle="modal" data-target="#editModal"
                                                data-codigo="<?php echo $usuario["cod_usuario"];?>"
                                                data-nombre="<?php echo $usuario["nombre"];?>"
                                                data-user="<?php echo $usuario["user"];?>"
                                                data-cod="<?php echo $usuario["cod_tipo_usuario"];?>"
                                                data-status="<?php echo $usuario["status"];?>">
                                                <i class="fas fa-pencil-alt"></i></button>
                                                <?php endif; ?>
                                                <?php if (!empty($_SESSION["permisos"]["seguridad"]["eliminar"])): ?>
                                                <button name="eliminar" title="Eliminar" class="btn btn-danger btn-sm eliminar" data-toggle="modal" data-target="#eliminarModal"
                                                data-codigo="<?php echo $usuario["cod_usuario"]; ?>"
                                                data-nombre="<?php echo $usuario["user"]; ?>"
                                                data-status="<?php echo $usuario["status"];?>">
                                                <i class="fas fa-trash-alt"></i></button>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endif;
                                        } ?>
                                </tbody>
                            </table>
                            </div>
                        </div>
                    </div>
                </div>

<!-- =============================
    MODAL REGISTRAR USUARIO 
================================== -->
<div class="modal fade" id="modalregistrarusuario" tabindex="-1" aria-labelledby="modalregistrarusuariolabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="registrarModalLabel">Registrar usuario</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formregistrarusuario" method="post">
                    <div class="form-group">
                        <label for="nombre">Nombre<span class="text-danger" style="font-size: 15px;"> *</span></label>
                        <!-- TOOLTIPS-->
                        <button class="btn btn-xs" data-toggle="tooltip" data-placement="top" title="Ingresa el nombre de quién usará el usuario. Ej: Daniel Rojas.">
                            <i class="fas fa-info-circle"></i>
                        </button>
                        <script>
                            $(function () {
                                $('[data-toggle="tooltip"]').tooltip();
                            });
                        </script>
                        <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Ingresa el nombre" required>
                        <div class="invalid-feedback" style="display: none;"></div>
                    </div>
                    <div class="form-group">
                        <label for="user">Usuario<span class="text-danger" style="font-size: 15px;"> *</span></label>
                        <input type="text" class="form-control" id="user" name="user" placeholder="Ingresa el nombre de usuario"  required>
                        <div class="invalid-feedback" style="display: none;"></div>
                    </div>
                    <div class="form-group">
                        <label for="pass">Contraseña<span class="text-danger" style="font-size: 15px;"> *</span></label>
                            <div class="input-group ">
                                <input type="password" class="form-control" id="passU" placeholder="Contraseña" name="pass" required>
                                <div class="invalid-feedback" style="display: none; margin: botton 4px;"></div>
                                <span class="fas fa-eye icon-password" data-target="passU"></span>
                            </div>
                    </div>
                    <div class="form-group">
                        <label for="rol">Rol<span class="text-danger" style="font-size: 15px;"> *</span></label>
                        <select class="form-control" id="rol" name="rol" required>
                        <option value="" selected disabled>Seleccione un rol</option>
                            <?php foreach($roles as $role): ?>
                                <option value="<?php echo $role['cod_tipo_usuario']; ?>">
                                    <?php echo $role['rol']; ?>
                                </option>
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
                window.location = 'usuarios';
            }
        });
    </script>  
<?php endif; ?>
<!-- =============================
    MODAL EDITAR USUARIO 
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
                                    <input type="text" class="form-control" id="nombreE" name="nombre">
                                    <div class="invalid-feedback" style="display: none;"></div>
                                </div>
                                <div class="form-group">
                                    <label for="codigo">User</label>
                                    <input type="text" class="form-control" id="userE" name="user">
                                    <div class="invalid-feedback" style="display: none;"></div>
                                    <input type="hidden" class="form-control" id="origin" name="origin" > <!--Lo pasamos oculto-->
                                    
                                </div>
                                <label for="roles">Rol</label>
                                        <select class="form-control" id="rolesE" name="roles" required>
                                            <?php foreach($roles as $role): ?>
                                                <option value="<?php echo $role['cod_tipo_usuario']; ?>">
                                                    <?php echo $role['rol']; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <select class="form-control" id="statusE" name="status">
                                        <option value="1">Activo</option>
                                        <option value="0">Inactivo</option>
                                    </select>
                                </div>
                            <!-- Botón "Cambiar contraseña" -->
                            <div class="form-group">
                                <button type="button" name="changePasswordBtn" class="btn btn-dark" id="changePasswordBtn">Cambiar contraseña</button>
                            </div>
                            <!-- Input de nueva contraseña (oculto por defecto)-->

                            <div class="form-group" id="passwordField" style="display: none;">
                                <label for="password">Nueva Contraseña</label>
                                    <div class="input-group ">
                                    <input type="password" class="form-control" id="passE" name="pass" placeholder="Ingrese la nueva contraseña">
                                        <div class="invalid-feedback" style="display: none;"></div>
                                        <span class="fas fa-eye icon-password" data-target="passE"></span>
                                    </div>
                            </div>
                        </div>
                        </form>
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
                window.location = 'usuarios';
            }
        });
    </script>
<?php endif; ?>
<!-- ====================================
    MODAL CONFIRMAR ELIMINAR USUARIO 
========================================= -->
            <div class="modal fade" id="eliminarModal" tabindex="-1" aria-labelledby="eliminarModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-danger">
                            <h5 class="modal-title" id="eliminarModalLabel">Eliminar Usuario</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="eliminarForm" method="post">
                                <div class="form-group">
                                    <p>¿Estás seguro que deseas eliminar al usuario: <b><span id="username"></b></span>?</p>
                                    <input type="hidden" id="usercode" name="usercode">
                                    <input type="hidden" id="statusDelete" name="statusDelete">
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
                window.location = 'usuarios';
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
                window.location = 'usuarios';
            }
        });
    </script>
<?php endif; ?>

<script src="vista/dist/js/modulos-js/usuarios.js"></script>