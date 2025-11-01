<?php require_once 'controlador/proveedores.php'; ?>


<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Proveedores</h1>
                    <p>En esta sección se puede gestionar los proveedores.</p>
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
                            <!-- Botón para ventana modal -->
                            <?php if (!empty($_SESSION["permisos"]["proveedor"]["registrar"])): ?>
                                <button class="btn btn-primary" data-toggle="modal" data-target="#modalProv">Registrar proveedor</button>
                            <?php endif; ?>
                        </div>
                        <div class="card-body">
                            <!-- Tabla de proveedores -->
                            <div class="table-responsive">
                                <table id="proveedores" class="table table-bordered table-striped table-hover datatable" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>Código</th>
                                            <th>Rif</th>
                                            <th>Razón social</th>
                                            <th>Correo electronico</th>
                                            <th>Dirección</th>
                                            <th>Teléfonos</th>
                                            <th>Status</th>

                                            <th>Representante</th>

                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($registro as $datos) { ?>
                                                <tr>
                                                    <td><?php echo $datos["cod_prov"] ?></td>
                                                    <td><?php echo $datos["rif"] ?></td>
                                                    <td><?php echo $datos["razon_social"] ?></td>
                                                    <td><?php echo !empty($datos["email"]) ? $datos["email"] : 'No disponible'; ?></td>
                                                    <td><?php echo !empty($datos["direccion"]) ? $datos["direccion"] : 'No disponible'; ?></td>
                                                    
                                                    <!-- REGISTRO TELEFONO PROVEEDOR -->
                                                    <td>
                                                        <button name="telef" class="btn btn-outline-primary telef" title="telef" data-toggle="modal" 
                                                            <?php if (!empty($_SESSION["permisos"]["proveedor"]["registrar"])): ?>
                                                                data-target="#myModalt"
                                                            <?php endif; ?>
                                                            data-cod1="<?php echo $datos['cod_prov']; ?>"
                                                            data-rif="<?php echo $datos['rif']; ?>">
                                                            <i class="fa fa-phone"></i>
                                                            <?php echo $datos["telefonos"]; ?>
                                                        </button>
                                                    </td>
                                                    <td>
                                                        <?php if ($datos['proveedor_status'] == 1): ?>
                                                            <span class="badge bg-success">Activo</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-danger">Inactivo</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <!-- BOTON REGISTRAR REPRESENTANTE -->
                                                    <td>
                                                        <?php if (empty($datos['cod_representante'])): ?>
                                                            <?php if (!empty($_SESSION["permisos"]["proveedor"]["registrar"])): ?>
                                                                <button name="mas" class="btn btn-sm btn-primary mas" title="Registrar" data-toggle="modal" data-target="#registrarRepresentante"
                                                                    data-codigoproveedor="<?php echo $datos['cod_prov']; ?>">
                                                                    <i class="fa fa-plus"></i>
                                                                </button>
                                                            <?php endif; ?>
                                                        <?php else: ?>
                                                            <button name="mostrar" class="btn btn-outline-info btn-sm mostrar" title="consultar" data-toggle="modal" data-target="#myModalr"
                                                                data-cod="<?php echo $datos["cod_representante"]; ?>"
                                                                data-cedula="<?php echo $datos['cedula']; ?>"
                                                                data-nombre="<?php echo $datos['nombre']; ?>"
                                                                data-apellido="<?php echo $datos['apellido']; ?>"
                                                                data-teler="<?php echo $datos['rep_tel']; ?>"
                                                                data-status1="<?php echo $datos['statusr']; ?>">
                                                                <i class="fa fa-user"></i>
                                                                <?php echo $datos['nombre']; ?>
                                                            </button>
                                                        <?php endif; ?>
                                                    </td>
                                                    <!-- BOTON EDITAR PROVEEDOR -->
                                                    <td>
                                                        <?php if (!empty($_SESSION["permisos"]["proveedor"]["editar"])): ?>
                                                            <button name="editar" class="btn btn-primary btn-sm editar" data-toggle="modal" data-target="#modalProvedit" title="editar"
                                                                data-cod="<?php echo $datos["cod_prov"]; ?>"
                                                                data-rif="<?php echo $datos['rif']; ?>"
                                                                data-razon="<?php echo $datos["razon_social"]; ?>"
                                                                data-email="<?php echo $datos['email']; ?>"
                                                                data-dire="<?php echo $datos['direccion']; ?>"
                                                                data-status="<?php echo $datos['proveedor_status']; ?>">
                                                                <i class="fas fa-pencil-alt"></i>
                                                            </button>
                                                        <?php endif; ?>
                                                        <!-- BOTON ELIMINAR PROVEEDORES -->
                                                        <?php if (!empty($_SESSION["permisos"]["proveedor"]["eliminar"])): ?>
                                                            <button name="eliminar" class="btn btn-danger btn-sm eliminar" data-toggle="modal" id="eliminar" data-target="#Modale" title="eliminar"
                                                                data-codigo="<?php echo $datos["cod_prov"]; ?>"
                                                                data-rif="<?php echo $datos["rif"]; ?>"
                                                                data-nombreproveedor="<?php echo $datos["razon_social"];?>">
                                                                <i class="fas fa-trash-alt"></i>
                                                            </button>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>


<!--------------------------------------- 
    REGISTRO DEL PROVEEDOR
--------------------------------------------->
<div class="modal fade" id="modalProv" tabindex="-1" role="dialog" aria-labelledby="provModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="provModalLabel">Registrar proveedor</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formproveedores" method="post">
                    <div class="form-group row">
                        <div class="col-md-6">
                            <label for="rif">Rif<span class="text-danger" style="font-size: 15px;"> *</span></label>
                            <!-- TOOLTIPS-->
                            <button class="btn btn-xs" data-toggle="tooltip" data-placement="top" title="Ingresa el Rif del proveedor. Ej: J12345678.">
                                <i class="fas fa-info-circle"></i>
                            </button>
                            <script>
                                $(function () {
                                    $('[data-toggle="tooltip"]').tooltip();
                                });
                            </script>
                            <input type="text" class="form-control" id="rif" name="rif" placeholder="Ingrese el Rif" required>
                            <div class="invalid-feedback" style="display: none;"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="razon_social">Razon Social<span class="text-danger" style="font-size: 15px;"> *</span></label>
                            <!-- TOOLTIPS-->
                            <button class="btn btn-xs" data-toggle="tooltip" data-placement="top" title="Ingresa la Razón Social. Ej: Inversiones Savyc.">
                                <i class="fas fa-info-circle"></i>
                            </button>
                            <script>
                                $(function () {
                                    $('[data-toggle="tooltip"]').tooltip();
                                });
                            </script>
                            <input type="text" class="form-control" id="razon_social" name="razon_social" placeholder="Ingrese Razon Social" required>
                            <div class="invalid-feedback" style="display: none;"></div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-6">
                            <label for="email">Correo Electronico:</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Ingrese Correo Electronico">
                            <div class="invalid-feedback" style="display: none;"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="direccion">Direccion:</label>
                            <input type="text" class="form-control" id="direccion" name="direccion" placeholder="Ingrese Direccion">
                            <div class="invalid-feedback" style="display: none;"></div>
                        </div>
                    </div>
                    <!-- Alert Message -->
                    <div class="alert alert-light d-flex align-items-center" role="alert">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <span>Todos los campos marcados con (*) son obligatorios</span>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary" name="guardar">Guardar</button>
                    </div>
                </form>
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
        }).then((resul) => {
            if (resul.isConfirmed) {
                window.location = 'proveedores';
            }
        });
    </script>
<?php endif; ?>


<!--------------------------------------- 
REGISTRO DEL REPRESENTANTE DEL PROVEEDOR
--------------------------------------------->

<div class="modal fade" id="registrarRepresentante" tabindex="-1" role="dialog" aria-labelledby="registrarRepresentanteLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="registrarRepresentanteLabel">Registrar representante</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="index.php?pagina=representantes" method="post">
                    <input type="hidden" name="cod_provREPRE" id="cod_provREPRE">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="cedula">Cédula:<span class="text-danger" style="font-size: 15px;"> *</span></label>
                                        <input type="text" class="form-control" id="cedula" name="cedula" placeholder="Ingrese Cédula" required>
                                        <div class="invalid-feedback" style="display: none;"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="nombre">Nombre:<span class="text-danger" style="font-size: 15px;"> *</span></label>
                                        <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Ingrese Nombre" required>
                                        <div class="invalid-feedback" style="display: none;"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="apellido">Apellido:</label>
                                        <input type="text" class="form-control" id="apellido" name="apellido" placeholder="Ingrese Apellido" >
                                        <div class="invalid-feedback" style="display: none;"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="telefono">Teléfono:</label>
                                        <input type="text" class="form-control" id="telefono" name="telefono" placeholder="Ingrese Teléfono" >
                                        <div class="invalid-feedback" style="display: none;"></div>
                                    </div>
                                </div>
                                <!-- Alert Message -->
                                <div class="alert alert-light d-flex align-items-center" role="alert">
                                    <i class="fas fa-exclamation-triangle mr-2"></i>
                                    <span>Todos los campos marcados con (*) son obligatorios</span>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                <button type="submit" class="btn btn-primary" name="ok">Guardar</button>
                            </div>
                        </div>
                </form>
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
        }).then((resul) => {
            if (resul.isConfirmed) {
                window.location = 'proveedores';
            }
        });
    </script>
<?php endif; ?>


<!-------------------------------------------
MOSTRAR MODAL DEL  REPRESENTANTE DEL PROVEEDOR
--------------------------------------------->
<div class="modal fade" id="myModalr" tabindex="-1" role="dialog" aria-labelledby="pprovModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pprovModalLabel">Datos Del representante</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="cod_prov" id="cod_oculto" readonly>
                    <form action="index.php?pagina=representantes" method="post">
                        <div class="form-group">
                            <label for="cod">Código</label>
                            <input type="text" class="form-control" name="cod_prov" id="cod" readonly>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="cedula">Cédula:</label>
                                    <input type="text" class="form-control" id="cedula" name="cedula" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nombre">Nombre:</label>
                                    <input type="text" class="form-control" id="nombre" name="nombre" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="apellido">Apellido:</label>
                                    <input type="text" class="form-control" id="apellido" name="apellido" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="telefono">Teléfono:</label>
                                    <input type="text" class="form-control" id="rep_tel" name="telefono" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <?php if (!empty($_SESSION["permisos"]["proveedor"]["editar"])): ?>
                                <button type="button" name="modificar" class="btn btn-primary btn-sm editar" data-toggle="modal" data-target="#modalProveditr" title="editar"
                                    data-codigo="<?php echo $datos['cod_representante']; ?>"
                                    data-cedula="<?php echo $datos['cedula']; ?>"
                                    data-nombre="<?php echo $datos['nombre']; ?>"
                                    data-apellido="<?php echo $datos['apellido']; ?>"
                                    data-teler="<?php echo $datos['rep_tel']; ?>"
                                    data-status1="<?php echo $datos['statusr']; ?>">
                                    <i class="fas fa-pencil-alt"></i>
                                </button>
                            <?php endif; ?>
                            <?php if (!empty($_SESSION["permisos"]["proveedor"]["eliminar"])): ?>
                                <button type="button" name="eliminar" class="btn btn-danger btn-sm eliminar" data-toggle="modal" data-target="#Modalel" title="eliminar"
                                    data-codigo="<?php echo $datos['cod_representante']; ?>"
                                    data-nombre="<?php echo $datos['nombre'] . ' ' . $datos['apellido']; ?>">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            <?php endif; ?>
                        </div>
                    </form>
            </div>
        </div>
    </div>
</div>


<!--------------------------------------- 
EDITAR REPRESENTANTE DEL PROVEEDOR
--------------------------------------------->
<div class="modal fade" id="modalProveditr" tabindex="-1" role="dialog" aria-labelledby="provModaledit" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="provModaleditr">Editar representante</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="index.php?pagina=representantes" id="formrepresentantesedit" method="post">
                    <input type="hidden" name="cod_representante" id="cod_oculto">
                    <div class="form-group">
                        <label for="cod">Código</label>
                        <input type="text" class="form-control" name="codigo" id="codigo" readonly>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="cedula">Cédula:</label>
                                <input type="text" class="form-control" id="cedula3" name="cedula" required>
                                <input type="hidden" class="form-control" id="origin" name="origin">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nombre">Nombre:</label>
                                <input type="text" class="form-control" id="nombre3" name="nombre" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="apellido">Apellido:</label>
                                <input type="text" class="form-control" id="apellido3" name="apellido">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="telefono">Teléfono:</label>
                                <input type="text" class="form-control" id="reptel" name="reptel">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="status">Estatus</label>
                                <select name="status" id="status1" class="form-control">
                                    <option value="1">Activo</option>
                                    <option value="0">Inactivo</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary" id="editButton" disabled name="editarr">Editar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php if (isset($editar)): ?>
    <script>
        Swal.fire({
            title: '<?php echo $editar["title"]; ?>',
            text: '<?php echo $editar["message"]; ?>',
            icon: '<?php echo $editar["icon"]; ?>',
            confirmButtonText: 'Ok'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location = 'proveedores';
            }
        });
    </script>
<?php endif; ?>

<!--------------------------------------- 
ELIMINAR  REPRESENTANTE DEL PROVEEDOR acomodar 
--------------------------------------------->
<div class="modal fade" id="Modalel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title" id="exampleModalLabel">Eliminar</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="index.php?pagina=representantes" id="formrepresentreselim" method="post">
                    <p>¿Desea eliminar al representante <b><span id="repreNombre"></span>?</b></p>
                    <input type="hidden" id="reprCodigo" name="reprCodigo">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="submit" name="eliminar_representante" id="confirmDeletettt" class="btn btn-danger">Eliminar</button>
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
        }).then((resul) => {
            if (resul.isConfirmed) {
                window.location = 'proveedores';
            }
        });
    </script>
<?php endif; ?>

<!--------------------------------------- 
    REGISTRO DEL TELEFONO PROVEEDOR
--------------------------------------------->
<div class="modal fade" id="myModalt" tabindex="-1" role="dialog" aria-labelledby="pprovModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pprovModalLabel">Registrar teléfono</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formTelefono" action="index.php?pagina=tproveedores" method="post">
                    <div class="form-group">
                        <label for="cod1">Código</label>
                        <input type="text" class="form-control" name="cod_prov" id="cod1" readonly>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="rif">RIF:</label>
                                <input type="text" class="form-control" id="rif" name="rif" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="telefono2">Teléfono:</label>
                                <input type="text" class="form-control" id="telefono2" name="telefono" placeholder="Ingrese Teléfono" required>
                                <div class="invalid-feedback" style="color: red; "></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary" name="okk">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php if (isset($registrar)): ?>
    <script>
        Swal.fire({
            title: '<?php echo $registrar["title"]; ?>',
            text: '<?php echo $registrar["message"]; ?>',
            icon: '<?php echo $registrar["icon"]; ?>',
            confirmButtonText: 'Ok'
        }).then((resul) => {
            if (resul.isConfirmed) {
                window.location = 'proveedores';
            }
        });
    </script>
<?php endif; ?>


<!--------------------------------------- 
    EDITAR PROVEEDOR
--------------------------------------------->

<div class="modal fade" id="modalProvedit" tabindex="-1" role="dialog" aria-labelledby="provModaledit" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="provModaledit">Editar proveedor</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formproveedoresedit" method="post">
                    <input type="hidden" name="cod_prov" id="cod_oculto" value="<?php echo $datos['cod_prov']; ?>" readonly>
                        <div class="form-group">
                            <label for="cod">Código</label>
                            <input type="text" class="form-control" id="cod" readonly>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="rif">RIF</label>
                                    <input type="text" class="form-control" id="rif1" name="rif1" required>
                                    <input type="hidden" class="form-control" id="origin" name="origin" value="<?php echo $datos['rif']; ?>">
                                    <div class="invalid-feedback" style="display: none;"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="razon">Razón Social</label>
                                    <input type="text" class="form-control" id="razon1" name="razon1" required>
                                    <div class="invalid-feedback" style="display: none;"></div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Correo Electrónico:</label>
                                    <input type="email" class="form-control" id="email1" name="email1">
                                    <div class="invalid-feedback" style="display: none;"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="dire">Dirección:</label>
                                    <input type="text" class="form-control" id="dire1" name="dire1">
                                    <div class="invalid-feedback" style="display: none;"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <select name="status" id="status" class="form-control">
                                        <option value="1">Activo</option>
                                        <option value="0">Inactivo</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-primary" id="editButtonn" disabled name="editar">Editar</button>
                        </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php if (isset($editar)): ?>
    <script>
        Swal.fire({
            title: '<?php echo $editar["title"]; ?>',
            text: '<?php echo $editar["message"]; ?>',
            icon: '<?php echo $editar["icon"]; ?>',
            confirmButtonText: 'Ok'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location = 'proveedores';
            }
        });
    </script>
<?php endif; ?>

<!--------------------------------------- 
ELIMINAR PROVEEDOR  
--------------------------------------------->
<div class="modal fade" id="Modale" tabindex="-1" role="dialog" aria-labelledby="exampaleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title" id="exampaleModalLabel">Confirmar Eliminar </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formproveedoreselim" method="post">
                    <p>¿Desea eliminar al proveedor <b><span id="nombreprove"></span>?</b></p>
                    <input type="hidden" id="provCodigo" name="provCodigo">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
                <button type="submit" class="btn btn-danger" id="confirmDelete" name="eliminar">Eliminar</button>
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
        }).then((resul) => {
            if (resul.isConfirmed) {
                window.location = 'proveedores';
            }
        });
    </script>
<?php endif; ?>

<script src="vista/dist/js/modulos-js/proveedores.js"> </script>
