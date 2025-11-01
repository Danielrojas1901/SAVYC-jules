<?php require_once 'controlador/cuentabancaria.php' ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-9">
                    <h1>Cuenta Bancaria</h1>
                    <p>En esta sección se puede gestionar las cuentas bancarias.</p>
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
                            
                            <?php if (!empty($_SESSION["permisos"]["tesoreria"]["registrar"])): ?>
                                <button class="btn btn-primary" data-toggle="modal" data-target="#modalregistrarCuenta">Registrar Cuenta Bancaria</button>
                            <?php endif; ?>
                        </div>
                        <div class="card-body">
                        
                            <div class="table-responsive">
                                <table id="tabla_cuentas_bancarias" class="table table-bordered table-striped datatable" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>Código</th>
                                            <th>Banco</th>
                                            <th>Tipo de cuenta</th>
                                            <th>Numero de cuenta</th>
                                            <th>Saldo</th>
                                            <th>Divisa</th>
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
                                                    <td><?php echo $dato['cod_cuenta_bancaria'] ?></td>
                                                    <td><?php echo $dato['nombre_banco'] ?></td>
                                                    <td><?php echo $dato['tipo_cuenta'] ?></td>
                                                    <td><?php echo $dato['numero_cuenta'] ?></td>
                                                    <td><?php echo $dato['saldo'] ?></td>
                                                    <td><?php echo $dato['divisa'] ?></td>
                                                    <td>
                                                        <?php if ($dato['status'] == 1): ?>
                                                            <span class="badge bg-success">Activo</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-danger">Inactivo</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <button name="ajustar" class="btn btn-secondary btn-sm movimientos" title="Ver Movimientos" data-toggle="modal" data-target="#detallemodal"
                                                            data-cod="<?php echo $dato['cod_cuenta_bancaria']; ?>"
                                                            data-numero="<?php echo $dato['numero_cuenta']; ?>"
                                                            data-saldo="<?php echo $dato['saldo']; ?>"
                                                            data-divisa="<?php echo $dato['cod_divisa']; ?>"
                                                            data-status="<?php echo $dato['status']; ?>"
                                                            data-banco="<?php echo $dato['cod_banco']; ?>"
                                                            data-tipocuenta="<?php echo $dato['cod_tipo_cuenta']; ?>">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                        <?php if (!empty($_SESSION["permisos"]["tesoreria"]["editar"])): ?>
                                                            <button class="btn btn-primary btn-sm editar" data-toggle="modal" data-target="#modalmodificarcuenta"
                                                                data-codE="<?php echo $dato['cod_cuenta_bancaria']; ?>"
                                                                data-numero="<?php echo $dato['numero_cuenta']; ?>"
                                                                data-saldo="<?php echo $dato['saldo']; ?>"
                                                                data-divisa="<?php echo $dato['cod_divisa']; ?>"
                                                                data-status="<?php echo $dato['status']; ?>"
                                                                data-banco="<?php echo $dato['cod_banco']; ?>"
                                                                data-tipocuenta="<?php echo $dato['cod_tipo_cuenta']; ?>">
                                                                <i class="fas fa-pencil-alt"></i>
                                                            </button>
                                                        <?php endif; ?>
                                                        <?php if (!empty($_SESSION["permisos"]["tesoreria"]["eliminar"])): ?>
                                                            <button name="confirmar" class="btn btn-danger btn-sm eliminar" title="Eliminar" data-toggle="modal" id="modificar" data-target="#modaleliminar"
                                                                data-cod="<?php echo $dato['cod_cuenta_bancaria']; ?>"
                                                                data-numero="<?php echo $dato['numero_cuenta']; ?>">
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
MODAL REGISTRAR CUENTA BANCARIA
============================= -->

                    <div class="modal fade" id="modalregistrarCuenta" tabindex="-1" aria-labelledby="modalregistrarCuentaLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header" style="background:rgb(35, 102, 245) ;color: #ffffff; ">
                                    <h5 class="modal-title" id="exampleModalLabel">Registrar Cuenta Bancaria</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>

                                <div class="modal-body">
                                    <form id="formregistrarCuenta" method="post">
                                     
                                        <div class="form-group row">
                                            <label for="banco">Banco</label>
                                            <button class="btn btn-xs" data-toggle="tooltip" data-placement="top" title="Seleccione un banco para la cuenta">
                                                <i class="fas fa-info-circle"></i>
                                            </button>
                                            <script>
                                                $(function() {
                                                    $('[data-toggle="tooltip"]').tooltip();
                                                });
                                            </script>
                                            <div class="input-group">
                                                <select class="form-control" name="banco" id="banco" required>
                                                    <option value="">Seleccione un banco</option>
                                                    <?php foreach ($banco as $ban): ?>
                                                        <option value="<?php echo $ban['cod_banco']; ?>"><?php echo $ban['nombre_banco']; ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <div class="invalid-feedback" style="display: none;"></div>
                                                <div class="input-group-append">
                                                    <?php if (!empty($_SESSION["permisos"]["config_finanza"]["registrar"])): ?>
                                                        <button class="btn btn-outline-secondary" type="button" data-toggle="modal" data-target="#modalRegistrarbanco">+</button>
                                                    <?php endif; ?>
                                                </div>
                                                <script>
                                                    $(function() {
                                                        $('[data-toggle="tooltip"]').tooltip();
                                                    });
                                                </script>
                                            </div>

                                        </div>

                                        <div class="form-group">
                                            <label for="tipo_cuenta">Tipo de cuenta</label>
                                            <select class="form-control" name="tipo_cuenta" id="tipo_cuenta" required>
                                                <option value="">Seleccione un tipo de cuenta</option>
                                                <?php foreach ($tipo as $tip): ?>
                                                    <option value="<?php echo $tip['cod_tipo_cuenta']; ?>"><?php echo $tip['nombre']; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                                <script>
                                                    $(function() {
                                                        $('[data-toggle="tooltip"]').tooltip();
                                                    });
                                                </script>
                                            </select>
                                            <div class="invalid-feedback" style="display: none;"></div>
                                        </div>

                                        <div class="form-group">
                                            <label for="numerocuenta">Numero de cuenta</label>
                                            
                                            <button class="btn btn-xs" data-toggle="tooltip" data-placement="top" title="Ingrese el número de cuenta">
                                                <i class="fas fa-info-circle"></i>
                                            </button>
                                            <script>
                                                $(function() {
                                                    $('[data-toggle="tooltip"]').tooltip();
                                                });
                                            </script>
                                            <input type="text" class="form-control" name="numerocuenta" placeholder="Ingrese el numero de cuenta" id="numerocuenta" maxlength="20">
                                            <div class="invalid-feedback" style="display: none;"></div>
                                        </div>
                                        <div class="form-group">
                                            <label for="divisa">Divisa</label>
                                            <select class="form-control" name="divisa" id="divisa" required>
                                                <?php foreach ($divisas as $div): ?>
                                                    <?php  if ($div['cod_divisa'] == 1): 
                                                    ?>
                                                    <option value=""></option>
                                                    <option value="<?php echo $div['cod_divisa']; ?>"><?php echo $div['nombre']; ?></option>
                                                    <?php endif; 
                                                    ?>
                                                <?php endforeach; ?>
                                            </select>
                                            <script>
                                                $(function() {
                                                    $('[data-toggle="tooltip"]').tooltip();
                                                });
                                            </script>
                                            <div class="invalid-feedback" style="display: none;"></div>
                                        </div>
                                        <div class="form-group">
                                            <label for="saldo">Saldo Inicial en cuenta</label>
                                           
                                            <button class="btn btn-xs" data-toggle="tooltip" data-placement="top" title="Ingrese el saldo inicial en cuenta">
                                                <i class="fas fa-info-circle"></i>
                                            </button>
                                            <script>
                                                $(function() {
                                                    $('[data-toggle="tooltip"]').tooltip();
                                                });
                                            </script>
                                            <input type="text" class="form-control" name="saldo" placeholder="Ingrese el saldo inicial en cuenta" id="saldo" maxlength="10">
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
                                    window.location = 'cuentabancaria';
                                }
                            });
                        </script>
                    <?php endif; ?>

                    <!-- MODAL EDITAR -->
                    <div class="modal fade" id="modalmodificarcuenta">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header" style="background:rgb(27, 77, 242); color: #ffffff;">
                                    <h4 class="modal-title">Editar Cuenta Bancaria</h4>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <form role="form" method="post" id="form-editar-cuenta">

                                   

                                    <div class="modal-body">
                                        <input type="hidden" name="cod_cuenta_bancaria" id="cod_cuenta_bancaria_oculto" value="">
                                        <div class="form-group">
                                            <label for="cod_unidad">Código</label>
                                            <input type="text" class="form-control" name="cod_cuenta_bancaria1" id="cod_cuenta_bancaria1" value="" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="numero_cuenta">Número de cuenta</label>
                                            <input type="text" class="form-control" name="numero_cuenta1" id="numero_cuenta1" value="" maxlength="20">
                                            <div class="invalid-feedback" style="display: none;"></div>
                                            <input type="hidden" id="origin" class="form-control" name="origin" maxlength="10">
                                        </div>
                                        <div class="form-group">
                                            <label for="saldo">Saldo</label>
                                            <input type="text" class="form-control" name="saldo1" id="saldo1" value="" readonly>

                                        </div>
                                        <div class="form-group">
                                            <label for="banco">Banco</label>
                                            <select class="form-control" name="banco1" id="banco1" required>
                                                <?php foreach ($banco as $ban): ?>
                                                    <option value="<?php echo $ban['cod_banco']; ?>"><?php echo $ban['nombre_banco']; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            <div class="invalid-feedback" style="display: none;"></div>
                                        </div>
                                        <div class="form-group">
                                            <label for="tipo">Tipo de cuenta</label>
                                            <select class="form-control" name="tipodecuenta1" id="tipodecuenta1" required>
                                                <?php foreach ($tipo as $tip): ?>
                                                    <option value="<?php echo $tip['cod_tipo_cuenta']; ?>"
                                                        <?php echo ($tip['cod_tipo_cuenta'] == $dato['cod_tipo_cuenta']); ?>>
                                                        <?php echo $tip['nombre'];  ?>
                                                    </option>
                                                <?php endforeach; ?>

                                            </select>
                                            <div class="invalid-feedback" style="display: none;"></div>
                                        </div>
                                        <div class="form-group">
                                            <label for="divisa">Divisa</label>
                                            <select class="form-control" name="divisa1" id="divisa1" required>
                                                <?php foreach ($divisas as $div): ?>
                                                    <option value="<?php echo $div['cod_divisa']; ?>"><?php echo $div['nombre']; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            <div class="invalid-feedback" style="display: none;"></div>
                                        </div>
                                        <div class="form-group">
                                            <label for="status">Estatus</label>
                                            <select name="status" id="status">
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
                                    window.location = 'cuentabancaria';
                                }
                            });
                        </script>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- MODAL MOVIMIENTOS-->
<!-------------------------------------------------------------->
<div class="modal fade" id="detallemodal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h4 class="modal-title">Historial de Movimientos - Cuenta <span id="titulo-numero-cuenta"></span></h4>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body">
             
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label>Código</label>
                        <input type="text" class="form-control" id="mov-codigo" readonly>
                    </div>
                    <div class="col-md-4">
                        <label>Divisa</label>
                        <input type="text" class="form-control" id="mov-divisa" readonly>
                    </div>
                    <div class="col-md-4">
                        <label>Saldo</label>
                        <input type="text" class="form-control" id="mov-saldo" readonly>
                    </div>
                </div>

               
                <div class="table-responsive">
                    <table id="tablaMovimientos" class="table table-bordered table-striped">
                        <thead class="thead-light">
                            <tr>
                                <th>Fecha</th>
                                <th>Tipo</th>
                                <th>Motivo</th>
                                <th>Monto</th>

                            </tr>
                        </thead>
                        <tbody id="detalleBody">

                        </tbody>
                    </table>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
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
                    <p>¿Estás seguro de eliminar la cuenta bancaria: <b><span id=numero_cuentaD></span>?</p></b>
                    <input type="hidden" name="eliminar" id="cod_eliminar">
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
                window.location = 'cuentabancaria';
            }
        });
    </script>
<?php endif; ?>

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
                <button type="submit" class="btn btn-primary" name="guardarB">Guardar</button>
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
                localStorage.setItem('bancoModal', 'true');
                window.location = 'cuentabancaria';
            }
        });
    </script>
<?php endif; ?>


<script src="vista/dist/js/modulos-js/cuentabancariacopia.js"></script>

</section>
</div>