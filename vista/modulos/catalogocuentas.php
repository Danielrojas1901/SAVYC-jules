<?php 
require_once "controlador/catalogocuentas.php";
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Catálogo de cuentas</h1>
                    <p>En esta sección se pueden gestionar las cuentas administrativas.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <?php if (!empty($_SESSION["permisos"]["contabilidad"]["registrar"])): ?>
                            <button class="btn btn-primary" data-toggle="modal" data-target="#modalRegistrarCuenta">
                                Registrar Cuenta
                            </button>
                            <?php endif; ?>
                            <!-- <div class="d-flex">
                                Filtro por Tipo (FALTA FOREACH) 
                                <select id="filtroTipo" class="form-control mr-2">
                                    <option value="">Filtrar por Tipo</option>
                                    
                                </select>
                                Filtro por Clase (Se llena dinámicamente) 
                                <select id="filtroClase" class="form-control">
                                    <option value="">Filtrar por Clase</option>
                                </select>
                            </div>-->
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="tabla_cuentas" class="table table-bordered table-striped datatable" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>Código</th>
                                            <th>Nombre</th>
                                            <th>Naturaleza</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        foreach ($registro as $item) {
                                        ?>
                                        <tr>
                                            <td><?php echo $item['codigo_contable']; ?></td>
                                            <td><?php echo $item['nombre_cuenta']; ?></td>
                                            <td><?php echo $item['naturaleza']; ?></td>
                                            <td>
                                            <?php if ($_SESSION['cod_usuario'] == 1 || $item['status'] == 2) {?>
                                                <?php if (!empty($_SESSION["permisos"]["contabilidad"]["editar"])): ?>
                                                    <button title="Editar cuenta administrativa" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#editarModal"
                                                        data-codigocontable="<?php echo $item['codigo_contable']?>"
                                                        data-nombre="<?php echo $item['nombre_cuenta'];?>"
                                                        data-naturaleza="<?php echo $item['naturaleza'];?>"
                                                        data-saldo="<?php echo $item['saldo'];?>"
                                                        data-nivel="<?php echo $item['nivel'];?>"
                                                        data-codigo="<?php echo $item['cod_cuenta'];?>"
                                                        data-status="<?php echo $item['status'];?>"
                                                        data-cuentapadre="<?php echo $item['cuenta_padreid'];?>">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                <?php endif; ?>
                                                <?php if (!empty($_SESSION["permisos"]["contabilidad"]["eliminar"])): ?>
                                                <button title="Eliminar cuenta administrativa" class="btn btn-danger btn-sm" data-target="#eliminarModal" data-toggle="modal"
                                                data-nombre="<?php echo $item['nombre_cuenta']; ?>"
                                                data-codigo="<?php echo $item['cod_cuenta']; ?>"
                                                data-status="<?php echo $item['status']; ?>">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                                <?php endif; ?>
                                            <?php } else {?>
                                                <button title="No tienes permiso para editar una cuenta predefinida" class="btn btn-sm btn-primary disabled">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button title="No tienes permiso para eliminar una cuenta predefinida" class="btn btn-danger btn-sm disabled">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            <?php } ?>   
                                            </td>
                                        </tr>
                                        <?php
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Modal Registro de Cuenta Contable -->
            <div class="modal fade" id="modalRegistrarCuenta" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                    <form id="formCuentaContable" method="post">
                        <div class="modal-header">
                        <h5 class="modal-title">Registrar Cuenta Administrativa</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body">
                        <div class="form-group">
                            <div>
                            <select id="nivel" name="nivel" class="form-control" required>
                                <option value="">Seleccione nivel</option>
                                <option value="1">Nivel 1</option>
                                <option value="2">Nivel 2</option>
                                <option value="3">Nivel 3</option>
                                <option value="4">Nivel 4</option>
                                <option value="5">Nivel 5</option>
                            </select>
                            <div class="invalid-feedback" style="display: none;"></div>
                            </div>
                        </div>
                        <!-- Seleccionar cuenta padre (solo si es hija) -->
                        <div class="form-group" id="grupoCuentaPadre" style="display: none;">
                            <label>Cuenta Padre</label>
                            <div>
                                <select id="listaPadres" name="cuentaPadre" class="form-control">
                                </select>
                                <div class="invalid-feedback" style="display: none;"></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Nombre de la Cuenta<span class="text-danger" style="font-size: 15px;"> *</span></label>
                            <button class="btn btn-xs" data-toggle="tooltip" data-placement="top" title="Ingresa el nombre de una cuenta administrativa">
                                <i class="fas fa-info-circle"></i>
                            </button>
                            <script>
                                $(function () {
                                    $('[data-toggle="tooltip"]').tooltip();
                                });
                            </script>
                            <input type="text" class="form-control" id="nombreCuenta" name="nombreCuenta" placeholder="Ej: Caja principal" required>
                            <div class="invalid-feedback" style="display: none;"></div>
                        </div>
                        <!-- Código contable generado automáticamente -->
                        <div class="form-group">
                            <label>Código</label>
                            <input type="text" class="form-control" id="codigoContable" name="codigoContable" readonly required>
                        </div>
                        <!-- Naturaleza: visible solo si es cuenta padre -->
                            <div class="form-group" id="grupoNaturaleza">
                                <label>Naturaleza</label>
                                <div>
                                    <select class="form-control" id="naturaleza" name="naturaleza" required>
                                        <option value="">Seleccione</option>
                                        <option value="deudora">Deudora</option>
                                        <option value="acreedora">Acreedora</option>
                                    </select>
                                    <div class="invalid-feedback" style="display: none;"></div>
                                    <input type="hidden" id="naturalezaHidden" name="naturalezaHidden">
                                </div>
                            </div>
                            <div class="alert alert-light d-flex align-items-center" role="alert">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                <span>Todos los campos marcados con (*) son obligatorios</span>
                            </div>
                        </div>
                        <div class="modal-footer">
                        <button type="submit" name="guardar" class="btn btn-primary">Registrar</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        </div>
                    </form>
                    </div>
                </div>
            </div>

            <!-- Modal Editar Cuenta Contable -->
            <div class="modal fade" id="editarModal" tabindex="-1" aria-labelledby="editarModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-primary">
                            <h5 class="modal-title" id="editarModalLabel">Editar Cuenta Administrativa</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form method="post" id="editarForm">
                                <div class="form-group">
                                    <div>
                                        <select id="nivele" name="nivel" class="form-control">
                                            <option value="">Seleccione nivel</option>
                                            <option value="1">Nivel 1</option>
                                            <option value="2">Nivel 2</option>
                                            <option value="3">Nivel 3</option>
                                            <option value="4">Nivel 4</option>
                                            <option value="5">Nivel 5</option>
                                        </select>
                                        <div class="invalid-feedback" style="display: none;"></div>
                                    </div>
                                </div>
                                <div class="invalid-feedback" style="display: none;"></div>
                                <div class="form-group" id="grupoCuentaPadre" style="display: none;">
                                    <label>Cuenta Padre</label>
                                    <div>
                                        <select id="listaPadrese" class="form-control">
                                        </select>
                                        <!--<input type="hidden" id="cuentaPadre" name="cuentaPadre">-->
                                        <div class="invalid-feedback" style="display: none;"></div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Nombre de la Cuenta<span class="text-danger" style="font-size: 15px;"> *</span></label>
                                    <button class="btn btn-xs" data-toggle="tooltip" data-placement="top" title="Para editar, ingresa el nombre de una nueva cuenta administrativa">
                                        <i class="fas fa-info-circle"></i>
                                    </button>
                                    <script>
                                        $(function () {
                                            $('[data-toggle="tooltip"]').tooltip();
                                        });
                                    </script>
                                    <input type="text" class="form-control" id="nombreCuentae" name="nombreCuenta" required>
                                    <div class="invalid-feedback" style="display: none;"></div>
                                </div>
                                
                                <div class="form-group">
                                    <label>Código</label>
                                    <input type="text" class="form-control" id="codigoContablee" name="codigoContable" readonly required>
                                </div>
                                <div class="form-group">
                                    <label for="saldo">Saldo</label>
                                    <input type="number" step="0.01" min="0" class="form-control" id="saldoe" name="saldo" readonly>
                                </div>
                                <div class="form-group" id="grupoNaturaleza">
                                    <label>Naturaleza</label>
                                    <div>
                                        <select class="form-control" id="naturalezae" name="naturaleza" required>
                                            <option value="">Seleccione</option>
                                            <option value="deudora">Deudora</option>
                                            <option value="acreedora">Acreedora</option>
                                        </select>
                                        <div class="invalid-feedback" style="display: none;"></div>
                                        <input type="hidden" name="naturalezah" id="naturalezah">
                                    </div>
                                </div>
                                <input type="hidden" id="codigocuenta" name="codigocuenta">
                                <input type="hidden" id="statuse" name="statuse">
                            </form>
                            <div class="alert alert-light d-flex align-items-center" role="alert">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                <span>Todos los campos marcados con (*) son obligatorios</span>
                            </div>
                        </div>
                        <div class="modal-footer" >
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                            <button type="submit" form="editarForm" name="editar" class="btn btn-primary">Guardar</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Eliminar Cuenta Contable -->
            <div class="modal fade" id="eliminarModal" tabindex="-1" aria-labelledby="eliminarModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-danger">
                            <h5 class="modal-title" id="eliminarModalLabel">Eliminar Cuenta Administrativa</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form method="post" id="eliminarForm">
                                <div class="form-group">
                                    <p>¿Estás seguro que deseas eliminar a la cuenta: <b><span id="nombrecuenta"></b></span>?</p>
                                    <small>Esta accion no se puede deshacer</small>
                                    <input type="hidden" id="codigocuenta" name="codigocuenta">
                                    <input type="hidden" id="statusDelete" name="statusDelete">
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                            <button type="submit" form="eliminarForm" class="btn btn-danger" name="borrar">Eliminar</button>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </section> 
</div>

<?php if (isset($respuesta)): ?>
    <script>
        Swal.fire({
            title: '<?php echo $respuesta["title"]; ?>',
            text: '<?php echo $respuesta["message"]; ?>',
            icon: '<?php echo $respuesta["icon"]; ?>',
            confirmButtonText: 'Ok'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location = 'catalogocuentas';
            }
        });
    </script>
<?php endif; ?>
<script src="vista/dist/js/modulos-js/catalogocuentas.js"></script>
