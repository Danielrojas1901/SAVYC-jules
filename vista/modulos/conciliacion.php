<?php require_once "controlador/conciliacion.php"; ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <br>
                    <h2>Conciliación bancaria</h2>
                </div>
            </div>
        </div>
    </section>

    <div style=" text-align-last: right;
    margin-right: 20px; margin-bottom: 20px">
    
      
<?php if(!empty($_SESSION["permisos"]["tesoreria"]["consultar"])): ?>
<button type="button" class="btn btn-primary ml-2" data-toggle="modal" data-target="#modalHistorialConciliaciones">
    <i class="fas fa-history mr-1"></i> Historial de conciliaciones
</button>
<?php endif; ?>
</div>

     <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        
                        <div class="card-body">
                            <!-- MOSTRAR EL REGISTRO DE UNIDADES DE MEDIDA -->
                            <div class="table-responsive">
                                <table id="unidad" class="table table-bordered table-striped datatable" style="width: 100%;">
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
                                            if ($dato['status'] == 1) {
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
                                                      <?php if(!empty($_SESSION["permisos"]["tesoreria"]["consultar"])): ?>
                                                      <button name="ajustar" class="btn btn-secondary btn-sm movimientos" title="Conciliar" onclick="verMovimientosCuenta('<?php echo $dato['cod_cuenta_bancaria']; ?>')"> <i class="fas fa-eye"></i> </button>
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


 
</div>

<!-------------------------------------------------------------->
<div class="modal fade" id="modalMovimientosCuenta" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h4 class="modal-title">Historial de Movimientos - Cuenta <span id="titulo-numero-cuenta"></span></h4>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <!-- Info Cuenta -->
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
          <div class="row mb-3 align-items-end">
                <div class="col-md-4">
                    <label>Rango de Fecha</label>
                    <div class="input-group">
                            <button type="button" class="btn btn-outline-primary" id="daterange-movimientos-btn" style="min-width: 300px;">
                              <i class="fa fa-calendar"></i> <span>Seleccionar Rango</span> <i class="fas fa-caret-down"></i>
                             </button>

                    </div>
                </div>
               
                <div class="col-md-4 d-none">
                    <input type="hidden" name="fechaInicioMov" id="fechaInicioMov" value="<?php echo date('Y-m-d') ?>">
                    <input type="hidden" name="fechaFinMov" id="fechaFinMov" value="<?php echo date('Y-m-d') ?>">
                </div>
                     <div class="col-md-4 offset-md-1">
                       <?php if(!empty($_SESSION["permisos"]["tesoreria"]["registrar"])): ?>
                       <button type="button" class="btn btn-outline-secondary w-100" id="formSubirExtractoBtn">
                                       Subir Extracto PDF
                           </button>
                       <input type="file" id="pdfFileInput" accept="application/pdf" style="display: none;" />
                       <?php endif; ?>
                      </div>
            </div>
   
                <!-- Tabla de Movimientos -->
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
                        <tbody id="tabla-movimientos-body">
                          
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

<!-- Modal para mostrar registros extraídos del PDF -->
<div class="modal fade" id="modalRegistrosPDF" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-secondary text-white">
                <h4 class="modal-title">Registros Extraídos del Extracto PDF</h4>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table id="tablaRegistrosPDF" class="table table-bordered table-hover">
                        <thead class="thead-dark">
                            <tr>
                                <th>Fecha</th>
                                <th>Referencia</th>
                                <th>Descripción</th>
                                <th>Tipo</th>
                                <th>Monto</th>
                                <th>Saldo</th>
                            </tr>
                        </thead>
                        <tbody id="tabla-registros-body">
                            <!-- Aquí se cargan dinámicamente los registros -->
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


<script >
    
//RANGO DE FECHAS
$(document).ready(function () {
    $('#daterange-movimientos-btn').daterangepicker({
        locale: {
            format: 'YYYY-MM-DD',
            applyLabel: 'Aplicar',
            cancelLabel: 'Cancelar',
            fromLabel: 'Desde',
            toLabel: 'Hasta',
            customRangeLabel: 'Rango Personalizado',
            weekLabel: 'S',
            firstDay: 1
        },
        ranges: {
            'Hoy': [moment(), moment().add(1, 'days')],
            'Ayer': [moment().subtract(1, 'days'), moment()],
            'Últimos 7 días': [moment().subtract(6, 'days'), moment().add(1, 'days')],
            'Últimos 30 días': [moment().subtract(29, 'days'), moment().add(1, 'days')],
            'Este mes': [moment().startOf('month'), moment().endOf('month')],
            'Mes pasado': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        },
        startDate: moment().subtract(6, 'days'),
        endDate: moment().add(1, 'days')
    }, function(start, end) {
        $('#daterange-movimientos-btn span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        $('#fechaInicioMov').val(start.format('YYYY-MM-DD'));
        $('#fechaFinMov').val(end.format('YYYY-MM-DD'));
    });

    // Set initial display and values
    $('#daterange-movimientos-btn span').html(moment().subtract(6, 'days').format('MMMM D, YYYY') + ' - ' + moment().add(1, 'days').format('MMMM D, YYYY'));
    $('#fechaInicioMov').val(moment().subtract(6, 'days').format('YYYY-MM-DD'));
    $('#fechaFinMov').val(moment().add(1, 'days').format('YYYY-MM-DD'));

    // Botón para restablecer
    $('#reset-movimientos-btn').on('click', function () {
        $('#fechaInicioMov').val('');
        $('#fechaFinMov').val('');
        $('#daterange-movimientos-btn span').html('Seleccionar Rango');
    });
});

document.getElementById('formSubirExtractoBtn').addEventListener('click', function() {
    document.getElementById('pdfFileInput').click();
});

document.getElementById('pdfFileInput').addEventListener('change', function () {
    const file = this.files[0];
    if (!file || file.type !== 'application/pdf') {
        alert('Por favor selecciona un archivo PDF válido.');
        return;
    }

    const formData = new FormData();
    formData.append('archivo_pdf', file);
    formData.append('bancario_pdf', "descarga");

    fetch('index.php?pagina=conciliacion', {
        method: 'POST',
        body: formData
    })
    .then(resp => resp.json())
    .then(data => {
        const tbody = document.getElementById('tabla-registros-body');
        tbody.innerHTML = '';

        if (!data.success) {
            alert(data.message || 'Error al procesar el PDF.');
            return;
        }

        data.registros.forEach(reg => {
            const row = `
                <tr>
                    <td>${reg.fecha}</td>
                    <td>${reg.referencia}</td>
                    <td>${reg.descripcion}</td>
                    <td>${reg.tipo}</td>
                    <td>${reg.monto}</td>
                    <td>${reg.saldo}</td>
                </tr>
            `;
            tbody.insertAdjacentHTML('beforeend', row);
        });

        // Mostrar el modal con los registros
        $('#modalRegistrosPDF').modal('show');
    })
    .catch(err => {
        console.error(err);
        alert('Error en la carga del archivo.');
    });
});
</script>


</script>

<script src="vista/dist/js/modulos-js/cuentabancariacopia.js"></script>
<script src="vista/dist/js/modulos-js/conciliacion.js"></script>
<!-- Moment.js -->
<script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>

<!-- Daterangepicker -->
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

