<?php require_once 'controlador/caja.php' ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-9">
                    <h1>Cajas</h1>
                    <p>En esta sección se gestionan las cajas de efectivo utilizadas para registrar y controlar los movimientos de dinero en la empresa. </p>
                </div>
            </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <?php if (!empty($_SESSION["permisos"]["config_finanza"]["registrar"])): ?>
                                <button class="btn btn-primary" data-toggle="modal" data-target="#modalregistrarCaja">Registrar Caja</button>
                            <?php endif; ?>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive ">
                                <table id="caja" class="table table-bordered table-striped datatable text-center align-middle" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>Código</th>
                                            <th>Nombre</th>
                                            <th>Saldo</th>
                                            <th>Divisa</th>
                                            <th>Historial</th>
                                            <th>Status</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($datos as $dato) { ?>
                                            <tr>
                                                <td><?php echo $dato['cod_caja'] ?></td>
                                                <td><?php echo $dato['nombre'] ?></td>
                                                <td><?php echo $dato['saldo'] ?></td>
                                                <td><?php echo $dato['divisa'] ?></td>
                                                <td>
                                                    <button class="btn btn-secondary btn-sm ver-historial" title="Ver historial" data-toggle="modal" data-target="#modalHistorialCaja"
                                                        data-cod="<?php echo $dato['cod_caja']; ?>"
                                                        data-nombre="<?php echo $dato['nombre']; ?>"
                                                        data-divisa="<?php echo $dato['divisa']; ?>">
                                                        <i class="fas fa-calendar-alt"></i>
                                                    </button>
                                                </td>
                                                <td>
                                                    <?php if ($dato['caja_status'] == 1): ?>
                                                        <span class="badge bg-success">Activo</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-danger">Inactivo</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if (!empty($_SESSION["permisos"]["config_finanza"]["editar"])): ?>
                                                        <button name="ajustar" class="btn btn-primary btn-sm editar" title="Editar" data-toggle="modal" data-target="#modalmodificarcaja"
                                                            data-cod="<?php echo $dato['cod_caja']; ?>"
                                                            data-nombre="<?php echo $dato['nombre']; ?>"
                                                            data-saldo="<?php echo $dato['saldo']; ?>"
                                                            data-divisa="<?php echo $dato['cod_divisa']; ?>"
                                                            data-status="<?php echo $dato['caja_status']; ?>">
                                                            <i class="fas fa-pencil-alt"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                    <?php if (!empty($_SESSION["permisos"]["config_finanza"]["eliminar"])): ?>
                                                        <button name="confirmar" class="btn btn-danger btn-sm eliminar" title="Eliminar" data-toggle="modal" id="modificar" data-target="#modaleliminar"
                                                            data-cod="<?php echo $dato['cod_caja']; ?>"
                                                            data-nombre="<?php echo $dato['nombre']; ?>">
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
                    <!-- =======================
                        MODAL REGISTRAR CAJA
                    ============================= -->
                    <div class="modal fade" id="modalregistrarCaja" tabindex="-1" aria-labelledby="modalregistrarCajaLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Registrar Caja</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <form id="formregistrarCaja" method="post">
                                        <div class="form-group">
                                            <label for="nombre">Nombre de la Caja<span class="text-danger" style="font-size: 20px;">*</span></label>
                                            <button class="btn btn-xs" data-toggle="tooltip" data-placement="top" title="Ingresa el nombre de la caja, por ejemplo: Caja Chica">
                                                <i class="fas fa-info-circle"></i>
                                            </button>
                                            <script>
                                                $(function() {
                                                    $('[data-toggle="tooltip"]').tooltip();
                                                });
                                            </script>
                                            <input type="text" class="form-control" name="nombre" placeholder="Ej: Caja principal" id="nombre" maxlength="20">
                                            <div class="invalid-feedback" style="display: none;"></div>
                                        </div>
                                        <div class="form-group">
                                            <label for="saldo">Saldo en caja</label>
                                            <button class="btn btn-xs" data-toggle="tooltip" data-placement="top" title="Ingresa el saldo inicial de la caja, por ejemplo: 1000">
                                                <i class="fas fa-info-circle"></i>
                                            </button>
                                            <script>
                                                $(function() {
                                                    $('[data-toggle="tooltip"]').tooltip();
                                                });
                                            </script>
                                            <input type="number" step="0.01" class="form-control" name="saldo" placeholder="Ej: 560" id="saldo" maxlength="20" value="0" required>
                                            <div class="invalid-feedback" style="display: none;"></div>
                                        </div>
                                        <div class="form-group">
                                            <label for="divisa">Divisa<span class="text-danger" style="font-size: 20px;">*</span></label>
                                            <select class="form-control" name="divisa" id="divisa" required>
                                                <?php foreach ($divisas as $div): ?>
                                                    <option value="<?php echo $div['cod_divisa']; ?>"><?php echo $div['nombre']; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            <div class="invalid-feedback" style="display: none;"></div>
                                        </div>
                                        <div class="alert alert-light d-flex align-items-center" role="alert">
                                            <i class="fas fa-exclamation-triangle mr-2"></i>
                                            <span>Todos los campos marcados con (*) son obligatorios</span>
                                        </div>
                                        <small class="form-text text-muted">
                                            Recuerda: Para usar esta caja debes crear un tipo de pago asociado desde el módulo de <a href="index.php?pagina=tpago">Tipos de Pago</a>.
                                        </small>
                                </div>
                                <div class="modal-footer justify-content-between">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                    <button type="submit" class="btn btn-primary" name="guardar">Guardar</button>
                                </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php if (isset($registrar)): ?>
                        <script>
                            <?php
                            $options = [
                                "title" => $registrar["title"],
                                "text" => $registrar["message"],
                                "icon" => $registrar["icon"],
                                "confirmButtonText" => 'Ok'
                            ];

                            if ($registrar["icon"] === 'success') {
                                $options["showDenyButton"] = true;
                                $options["denyButtonText"] = 'Ir a Tipo de Pago';
                                $options["customClass"] = [
                                    "denyButton" => 'btn btn-primary'
                                ];
                            }

                            echo "Swal.fire(" . json_encode($options) . ").then((result) => {
                                if (result.isConfirmed) {
                                    window.location = 'caja';
                                } else if (result.isDenied) {
                                    window.location = 'tpago';
                                }
                            });";
                            ?>
                        </script>
                    <?php endif; ?>

                    <!-- =====================
                        MODAL EDITAR
                    =======================-->
                    <div class="modal fade" id="modalmodificarcaja">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Editar Caja</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>

                                <form role="form" method="post" id="form-editar-caja">
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label for="cod_caja">Código</label>
                                            <input type="text" class="form-control" name="cod_caja" id="cod_caja" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="nombre1">Nombre de la Caja</label>
                                            <input type="text" class="form-control" name="nombre1" id="nombre1" required>
                                            <div class="invalid-feedback" style="display: none;"></div>
                                        </div>
                                        <div class="form-group">
                                            <label for="saldo1">Saldo en caja</label>
                                            <input type="number" step="0.01" class="form-control" name="saldo1" id="saldo1" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="divisa1">Divisa</label>
                                            <select class="form-control" name="divisa1" id="divisa1" disabled>
                                                <?php foreach ($divisas as $div): ?>
                                                    <option value="<?php echo $div['cod_divisa']; ?>" selected>
                                                        <?php echo htmlspecialchars($div['nombre']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="status">Status</label>
                                            <select class="form-control" name="status" id="status" required>
                                                <option value="1">Activo</option>
                                                <option value="0">Inactivo</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer justify-content-between">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                        <button type="submit" class="btn btn-primary" name="editar">Guardar cambios</button>
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
                                    window.location = 'caja';
                                }
                            });
                        </script>
                    <?php endif; ?>
                    <!-- =====================
                    MODAL HISTORIAL DE CAJA
                =======================-->
                    <div class="modal fade" id="modalHistorialCaja" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-xl modal-dialog-scrollable">
                            <div class="modal-content">
                                <div class="modal-header bg-primary text-white">
                                    <h5 class="modal-title">Historial de Movimientos de Caja</h5>
                                    <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                                </div>
                                <div class="modal-body">
                                    <div class="card">
                                        <div class="card-header">
                                            <div class="form-row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="nombrecaja">Caja:</label>
                                                        <input type="text" class="form-control" id="nombrecaja" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="monedacaja">Moneda</label>
                                                        <input type="text" class="form-control" id="monedacaja" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <input type="hidden" name="cod_caja" id="cod_caja_historial">
                                            <!-- Tabla de Historial -->
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-hover text-center align-middle" id="tablaHistorialCaja">
                                                    <thead class="thead-light">
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Fecha</th>
                                                            <th>Hora Apertura</th>
                                                            <th>Hora Cierre</th>
                                                            <th>Saldo Inicial</th>
                                                            <th>Saldo Final</th>
                                                            <th>Acciones</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <!-- Aquí se llenan dinámicamente las filas por JS -->
                                                        <tr>
                                                            <td colspan="7" class="text-center">Cargando historial...</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Confirmar Eliminar Modal -->
                    <div class="modal fade" id="modaleliminar">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header bg-danger">
                                    <h5 class="modal-title">Confirmar Eliminar</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <form method="post">
                                        <p>¿Estás seguro de eliminar la caja: <b><span id=nombreD></span>?</p></b>
                                        <input type="hidden" name="eliminar" id="cod_eliminar">
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                    <button type="submit" class="btn btn-danger">Eliminar</button>
                                </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="modal fade" id="modalMovimientosHistorial" tabindex="-1" role="dialog">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header bg-primary">
                                    <h5 class="modal-title">Movimientos del Historial de Caja</h5>
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                </div>
                                <div class="modal-body">
                                    <div class="card">
                                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                            <div class="justify-content-start">
                                                <b>Nombre:</b> <span id="nombreCaja"></span><br>
                                                <b>Divisa:</b> <span id="divisaCaja"></span><br>
                                                <b>Responsable del cierre y conteo:</b> <span id="cod_usuario"></span>
                                            </div>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-hover text-center align-middle" id="tablaHistorialMovimientos" style="width: 100%;">
                                                <thead>
                                                    <tr>
                                                        <th>Fecha y Hora</th>
                                                        <th>Origen</th>
                                                        <th>Movimiento</th>
                                                        <th>Referencia</th>
                                                        <th>Monto</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="bodyHistorialMovimientos">

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
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
                window.location = 'caja';
            }
        });
    </script>
<?php endif; ?>

<?php
    $disabled = !isset($_SESSION['rif']) ? 'disabled' : '';
    $title = !isset($_SESSION['rif']) 
        ? 'No se puede generar el reporte, debes registrar la informacion de la empresa' 
        : 'Exportar PDF';
?>
<script>
    const Disabled = <?= json_encode($disabled) ?>;
    const Title = <?= json_encode($title) ?>;
</script>

<script src="vista/dist/js/modulos-js/caja.js"></script>