<?php require_once 'controlador/clientes.php'; ?>

<div class="content-wrapper">
    <section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Clientes</h1>
                <p>En esta sección se puede gestionar los clientes.</p>
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
                <?php if (!empty($_SESSION["permisos"]["cliente"]["registrar"])): ?>
                <!-- Botón para ventana modal -->
                    <button class="btn btn-primary" data-toggle="modal" data-target="#modalRegistrarClientes">Registrar cliente</button>
                <?php endif; ?>
            </div>
            <div class="card-body">

            <!-- TABLA -->
            <div class="table-responsive">
            <table id="clientes" class="table table-bordered table-striped table-hover datatable" style="width: 100%;">
                <thead>
                        <tr>
                            <th>Codigo</th>
                            <th>Nombre</th>
                            <th>Apellido</th> 
                            <th>Cédula-Rif</th>
                            <th>Teléfono</th>
                            <th>Email</th>
                            <th>Dirección</th>
                            <th>Status</th>
                            <th>Acciones</th>
                        </tr>
                </thead>
                <tbody>
                    <?php foreach ($registro as $datos){ ?>
                    <?php if ($datos['status'] != 2): ?>
                        <tr>
                            <td> <?php echo $datos["cod_cliente"] ?></td>
                            <td> <?php echo $datos["nombre"] ?></td>
                            <td> <?php echo $datos["apellido"] ?></td>
                            <td> <?php echo $datos["cedula_rif"] ?></td>
                            <td> <?php echo $datos["telefono"] ? $datos["telefono"] : 'No disponible'?></td>
                            <td> <?php echo $datos["email"] ? $datos["email"] : 'No disponible' ?></td>
                            <td> <?php echo $datos["direccion"] ? $datos["direccion"] : 'No disponible' ?></td>
                            <td>
                                <?php if ($datos['status']==1):?>
                                <span class="badge bg-success">Activo</span>
                                <?php else:?>
                                <span class="badge bg-danger">Inactivo</span>
                                <?php endif;?>
                            </td>
                            <td>
                            <?php if (!empty($_SESSION["permisos"]["cliente"]["editar"])): ?>
                            <button name="editar" title="Editar" class="btn btn-primary btn-sm editar" data-toggle="modal" data-target="#editModal" 
                            data-cedula_rif="<?php echo $datos["cedula_rif"]; ?>" 
                            data-codigo="<?php echo $datos["cod_cliente"]; ?>" 
                            data-nombre="<?php echo $datos["nombre"]; ?>" 
                            data-apellido="<?php echo $datos["apellido"]; ?>" 
                            data-telefono="<?php echo $datos["telefono"];  ?>" 
                            data-email="<?php echo $datos["email"]; ?>"
                            data-direccion="<?php echo $datos["direccion"]; ?>" 
                            data-status="<?php echo $datos["status"]; ?>">
                            <i class="fas fa-pencil-alt"></i>
                            </button>
                            <?php endif; ?>
                            <?php if (!empty($_SESSION["permisos"]["cliente"]["eliminar"])): ?>
                            <button name="eliminar" title="Eliminar" class="btn btn-danger btn-sm eliminar" data-toggle="modal" data-target="#eliminarcliente"
                                data-codigo="<?php echo $datos["cod_cliente"]; ?>" 
                                data-nombre="<?php echo $datos["nombre"]; ?>">
                                <i class="fas fa-trash-alt" ></i>
                            </button>
                            <?php endif; ?>
                            </td>
                        <?php endif; ?>
                        <?php } ?>
                </tbody>
            </table>
            </div>
            <!-- /TABLA -->
        </div>
    </div>

<!-- =======================
MODAL REGISTRAR CLIENTES 
============================= -->
    <div class="modal fade" id="modalRegistrarClientes" tabindex="-1" aria-labelledby="modalRegistrarClientesLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="clientesModalLabel">Registrar cliente</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formRegistrarClientes" method="post">
                        <div class="form-group">
                            <label for="cedula_rif">Cédula o Rif:<span class="text-danger" style="font-size: 15px;"> *</span></label>
                            <input type="text" class="form-control" name="cedula_rif" id="cedula_rif" placeholder="Ej: 12345678" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        
                        <div class="form-group row">
                            <div class="col-6">
                                <label for="nombre">Nombre:<span class="text-danger" style="font-size: 15px;"> *</span></label>
                                <input type="text" class="form-control" name="nombre" id="nombre" placeholder="Ingrese el nombre" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-6">
                                <label for="apellido">Apellido:<span class="text-danger" style="font-size: 15px;"> *</span></label>
                                <input type="text" class="form-control" name="apellido" id="apellido" placeholder="Ingrese el apellido" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="telefono">Teléfono:</label>
                            <input type="tel" class="form-control" name="telefono" id="telefono" placeholder="Ingrese el teléfono">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="form-group">
                            <label for="email">Email:</label>
                            <input type="email" class="form-control" name="email" placeholder="Ingrese el correo electrónico">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="form-group">
                            <label for="direccion">Dirección:</label>
                            <textarea class="form-control" name="direccion" placeholder="Ingrese la dirección de vivienda"></textarea>
                            <div class="invalid-feedback"></div>
                        </div>
                    </form>
                    <!-- Alert Message -->
                    <div class="alert alert-light d-flex align-items-center" role="alert">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <span>Todos los campos marcados con (*) son obligatorios</span>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary" name="guardar" form="formRegistrarClientes">Guardar</button>
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
                window.location = 'clientes';
            }
        });
    </script>
<?php endif; ?>

<!-- =======================
MODAL EDITAR CLIENTES 
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
                    <div class="form-group row">
                        <div class="col-6">
                            <label for="nombre">Nombre</label>
                            <input type="text" class="form-control" id="nombre1" name="nombre" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-6">
                            <label for="apellido">Apellido</label>
                            <input type="text" class="form-control" id="apellido1" name="apellido" required>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="cedula_rif">Cédula-Rif</label>
                        <input type="text" class="form-control" id="cedularif1" name="cedula" required>
                        <div class="invalid-feedback"></div>
                        <input type="hidden" class="form-control" id="origin" name="origin">
                    </div>
                    <div class="form-group">
                        <label for="telefono">Teléfono</label>
                        <input type="text" class="form-control" id="telefono1" name="telefono">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email1" name="email">
                    </div>
                    <div class="form-group">
                        <label for="direccion">Dirección</label>
                        <textarea class="form-control" id="direccion1" name="direccion" maxlength="100"></textarea>
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
                <button type="submit" class="btn btn-primary" name="actualizar" form="editForm">Guardar cambios</button>
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
                window.location = 'clientes';
            }
        });
    </script>
<?php endif; ?>

<!-- =======================
MODAL CONFIRMAR ELIMINAR 
============================= -->

    <div class="modal fade" id="eliminarcliente" tabindex="-1" aria-labelledby="eliminarclienteLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h5 class="modal-title" id="eliminarclienteLabel">Confirmar Eliminación</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                <form id="elimodal" method="post"> 
                    <p>¿Está seguro que desea eliminar a <span id="clienteNombre"></span>?</p>
                    <input type="hidden" id="clienteCodigo" name="clienteCodigo"> 
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
            window.location = 'clientes';
        }
    });
</script>
<?php endif; ?>
<script src="vista/dist/js/modulos-js/clientes.js"> </script>
</section>
</div>

