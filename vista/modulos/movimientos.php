<?php
require_once "controlador/movimientos.php";
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-9">
                    <h1>Gestion de asientos</h1>
                    <p>En esta sección se puede convertir los movimientos de tu empresa en asientos administrativos.</p>
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
                        <div class="card-header d-flex align-items-center">
                            <div class="mb-2">
                            <button class="btn btn-success" title="Sicronizar asientos para movimientos selecionados" onclick="mostrarPrevia()">
                                <i class="fas fa-sync"></i> Generar Asientos
                            </button>
                            <!--<button class="btn btn-primary" onclick="sincronizarTodo()">
                                <i class="fas fa-sync"></i> Sincronizar Todo
                            </button>-->
                            <button class="btn btn-primary" data-toggle="modal" data-target="#modalAsientoManual">
                                Registrar Asiento
                            </button>
                            <button class="btn btn-info text-right" data-toggle="modal" data-target="#modalAsientoApertura">
                                Registrar Asiento de Apertura
                            </button>
                            </div>
                    </div>
                    <!-- Tabla Movimientos -->
                    <div class="card">
                        <div class="card-header">
                        <h3 class="card-title">Movimientos Operativos</h3>
                        </div>
                        <div class="card-body">
                        <table id="movimientosTable" class="table table-bordered table-striped datatable table-hover" style="width: 100%;">
                            <thead>
                            <tr>
                                <th><input type="checkbox" id="selectAll"></th>
                                <th>Fecha</th>
                                <th>Tipo</th>
                                <th>Descripción</th>
                                <th>Monto</th>
                                <th>Estado</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach($movi as $mov){ 
                                if($mov['status']===1){?>
                            <tr>
                                <td>
                                    <input type="checkbox" class="mov-checkbox" 
                                        data-cod_mov="<?= $mov['cod_mov']; ?>"
                                        data-tipo_operacion="<?= $mov['tipo_operacion']; ?>"
                                        data-detalle_operacion="<?= $mov['detalle_operacion']; ?>"
                                        data-cod_operacion="<?= $mov['cod_operacion']; ?>"
                                        data-monto="<?= $mov['monto']; ?>"
                                        data-descripcion="<?= $mov['descripcion_operacion']; ?>">
                                </td>
                                <td><?= $mov['fecha']; ?> </td>
                                <td><span class="badge bg-info"><?= $mov['tipo_operacion']; ?></span></td>
                                <td><?= $mov['descripcion_operacion']; ?></td>
                                <td><?= $mov['monto']."Bs"; ?></td>
                                <td><span class="badge bg-danger">Pendiente</span></td>
                            </tr>
                            <?php } 
                                }?>
                            </tbody>
                        </table>
                        </div>
                    </div>

                    <!-- Tabla Asientos -->
                    <div class="card mt-4">
                        <div class="card-header">
                        <h3 class="card-title">Asientos Generados</h3>
                        </div>
                        <div class="card-body">
                        <table id="asientosTable" class="table table-bordered table-striped datatable1 table-hover " style="width: 100%;">
                            <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Descripción</th>
                                <th>Status</th>
                                <th>Acciones</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach($movi_a as $mov){ 
                                    //if($mov['status']===2){?>
                                <tr>
                                    <td><?= $mov['fecha']; ?></td>
                                    <td><?= $mov['descripcion_operacion']; ?></td>
                                    <?php if(!empty($mov['cod_mov'])): ?>
                                    <td><span class="badge bg-success">Asiento Automatico</span></td>
                                    <?php else: ?>
                                    <td><span class="badge bg-primary"><?= 'Asiento '.$mov['status']; ?></span></td>
                                    <?php endif; ?>
                                    <td>
                                        <?php if(!empty($mov['cod_mov'])): ?>
                                        <button class="btn btn-info btn-sm btn-ver-asiento" data-toggle="modal" data-target="#modalAsientosGenerados"
                                        data-des="<?= $mov['descripcion_operacion']; ?>" 
                                        data-cod="<?= ($mov['cod_mov']); ?>"
                                        data-fecha="<?= $mov['fecha']; ?>">
                                            <i class="fas fa-eye"></i> Ver Asientos
                                        </button>
                                        <?php else: ?>
                                        <button class="btn btn-info btn-sm btn-ver-asiento-manual" data-toggle="modal" data-target="#modalAsientosGenerados2"
                                        data-des="<?= $mov['descripcion_operacion']; ?>" 
                                        data-cod="<?= ($mov['cod_asiento']); ?>"
                                        data-fecha="<?= $mov['fecha']; ?>">
                                            <i class="fas fa-eye"></i> Ver Asientos
                                        </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php } 
                                //}?>
                            </tbody>
                        </table>
                        </div>
                    </div>
                    </div>
                </section>
                </div>

<!-- Modal para mostrar asientos Automaticos generados -->
<div class="modal fade" id="modalAsientosGenerados" tabindex="-1" aria-labelledby="modalAsientosGeneradosLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalAsientosGeneradosLabel">Asientos Administrativos Generados</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nombre_cliente">Descripcion: </label>
                            <input type="text" class="form-control" id="descripcion" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="fecha_venta">Fecha de Operacion</label>
                            <input type="text" class="form-control" id="fecha" readonly>
                        </div>
                    </div>
                </div>
                <div class="modal-body">
                    <div id="asientosGeneradosContainer">
                        <!-- Aquí se generarán las tablas dinámicamente -->
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para mostrar asientos registrados manualmente -->
<div class="modal fade" id="modalAsientosGenerados2" tabindex="-1" aria-labelledby="modalAsientosGenerados2Label" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalAsientosGenerados2Label">Asientos Administrativos Generados</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nombre_cliente">Descripcion: </label>
                            <input type="text" class="form-control" id="descripcion2" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="fecha_venta">Fecha de Operacion</label>
                            <input type="text" class="form-control" id="fecha2" readonly>
                        </div>
                    </div>
                </div>
                <div class="modal-body">
                    <div id="asientosGeneradosContainer2">
                        <!-- Aquí se generarán las tablas dinámicamente -->
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Asiento Manual -->
<div class="modal fade" id="modalAsientoManual" tabindex="-1" role="dialog" aria-labelledby="tituloAsientoManual" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <form id="formAsientoManual" method="POST">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="tituloAsientoManual">Registrar Asiento Manual</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="status" value="manual" id="tipoAsiento">
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="fechaAsiento">Fecha:</label>
                        <input type="date" class="form-control" name="fecha" id="fechaAsiento1" required>
                    </div>
                    <div class="form-group col-md-8">
                        <label for="descripcionAsiento">Descripción:</label>
                        <textarea class="form-control" name="descripcion" id="descripcionAsiento1" rows="2" required></textarea>
                    </div>
                </div>
                <div class="table-responsive">
                <table class="table table-bordered table-striped" id="tablaAsientoManual1">
                    <thead class="thead-light">
                    <tr>
                        <th>Cuenta Contable</th>
                        <th class="text-center">Debe</th>
                        <th class="text-center">Haber</th>
                        <th class="text-center">Acción</th>
                    </tr>
                    </thead>
                    <tbody id="detalleAsientoManual">
                    <!-- Filas dinámicas aquí -->
                    </tbody>
                </table>
                <button type="button" class="btn btn-sm btn-secondary" onclick="agregarFila('manual')">+ Agregar Cuenta</button>
                </div>
            </div>
            <small id="mensajeValidacionApertura" class="text-danger font-italic" style="display:none;"></small>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="submit" name="confirmarManual" id="btnRegistrarManual" class="btn btn-primary">Registrar Asiento</button>
            </div>
            </form>
        </div>
    </div>
</div>



<!-- Modal: Asiento de Apertura -->
<div class="modal fade" id="modalAsientoApertura" tabindex="-1" role="dialog" aria-labelledby="tituloAsientoApertura" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        <form id="formAsientoApertura" method="POST">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="tituloAsientoApertura">Registrar Asiento de Apertura</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="status" value="apertura">
                <div class="alert alert-light">
                    <b>Modo Apertura:</b> Este asiento define los saldos iniciales del sistema. Solo se registra una vez.
                </div>
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="fechaApertura">Fecha:</label>
                        <input type="date" class="form-control" name="fecha" id="fechaApertura" required>
                    </div>
                    <div class="invalid-feedback" style="display: none;"></div>
                    <div class="form-group col-md-8">
                        <label for="descripcionApertura">Descripción:</label>
                        <input type="text" class="form-control" name="descripcion" id="descripcionApertura" value="Asiento de apertura" readonly>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="thead-light">
                            <tr>
                            <th>Cuenta Contable</th>
                            <th class="text-center">Debe</th>
                            <th class="text-center">Haber</th>
                            <th class="text-center">Acción</th>
                            </tr>
                        </thead>
                        <tbody id="detalleAsientoApertura">
                            <!-- Filas dinámicas aquí -->
                        </tbody>
                    </table>
                    <button type="button" class="btn btn-sm btn-secondary" onclick="agregarFila('apertura')">+ Agregar Cuenta</button>
                </div>
                <small id="mensajeValidacionApertura" class="text-danger font-italic" style="display:none;"></small>
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" name='btnRegistrarApertura' id="btnRegistrarApertura">Registrar Apertura</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de Confirmación para Asiento de Apertura -->
<div class="modal fade" id="modalConfirmarApertura" tabindex="-1" role="dialog" aria-labelledby="tituloConfirmacion" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content border-danger">
            <div class="modal-header bg-danger text-white">
            <h5 class="modal-title" id="tituloConfirmacion">Confirmar Asiento de Apertura</h5>
            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                <span aria-hidden="true">&times;</span>
            </button>
            </div>
            <div class="modal-body">
            <p><b>Advertencia:</b> Este asiento de apertura se puede registrar <u>una sola vez</u>. Una vez guardado, no podrá eliminarse ni editarse.</p>
            <p>¿Estás seguro de que deseas continuar?</p>
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            <button type="submit" form='formAsientoApertura' class="btn btn-danger" name="confirmarApertura">Sí, Registrar Asiento</button>
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
        }).then((result) => {
            if (result.isConfirmed) {
                window.location='movimientos';
            }
    });
</script> 
<?php endif; ?>

<script>
    const opcionesCuentasApertura = `<?php foreach ($cuentas_apertura as $cuenta): ?>
        <option value="<?= $cuenta['cod_cuenta'] ?>" data-codigo="<?= $cuenta['codigo_contable'] ?>">
            <?= $cuenta['codigo_contable'] ?> - <?= $cuenta['nombre_cuenta'] ?>
        </option>
    <?php endforeach; ?>`;
    const opcionesCuentasManuales = `<?php foreach ($cuentas_manual as $cuentam): ?>
        <option value="<?= $cuentam['cod_cuenta'] ?>" data-codigo="<?= $cuentam['codigo_contable'] ?>">
            <?= $cuentam['codigo_contable'] ?> - <?= $cuentam['nombre_cuenta'] ?>
        </option>
    <?php endforeach; ?>`;
</script>

<script>
// Seleccionar/Deseleccionar todos
$('#selectAll').on('change', function() {
    $('.mov-checkbox').prop('checked', this.checked);
});

// Sincronizar seleccionados
function mostrarPrevia() {
    var seleccionados = [];
    $('.mov-checkbox:checked').each(function() {
        seleccionados.push({
            cod_mov: $(this).data('cod_mov'),
            tipo_operacion: $(this).data('tipo_operacion'),
            detalle_operacion: $(this).data('detalle_operacion'),
            cod_operacion: $(this).data('cod_operacion'),
            monto: $(this).data('monto'),
            descripcion: $(this).data('descripcion')
        });
    });

    if (seleccionados.length === 0) {
        Swal.fire({
                icon: 'warning',
                title: 'Advertencia',
                text: 'Selecciona al menos un movimiento.',
                confirmButtonText: 'Aceptar'
            });
        return;
    }

    // Enviar por AJAX (ajusta la URL y el método según tu backend)
    $.ajax({
        url: 'index.php?pagina=movimientos&accion=sincronizar',
        method: 'POST',
        data: { movimientos: seleccionados },
        dataType: 'json',
        success: function(response) {
            console.log(response);
        if (response === true) {
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: 'Los movimientos se han contabilizado exitosamente.',
                confirmButtonText: 'Aceptar'
            }).then(() => {
                location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: response, // muestra el mensaje de error enviado
                confirmButtonText: 'Aceptar'
            });
        }
    },
    error: function($e) {
        Swal.fire({
            icon: 'error',
            title: 'Error del servidor',
            text: 'No se pudo establecer conexión.',
            confirmButtonText: 'Aceptar'
        });
    }
    });
}
</script>
<script src='vista/dist/js/modulos-js/movimientos.js'></script>
