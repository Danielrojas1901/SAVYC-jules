<?php
require_once 'controlador/venta.php';
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Reportes de Ventas</h1>
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
                            <ul class="nav nav-tabs" id="tabContent" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="producto-tab" data-toggle="tab" href="#ventaf" role="tab">Por fecha</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="carga-tab" data-toggle="tab" href="#cliente" role="tab">Por cliente</a>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="ventaf" role="tabpanel">
                                    <div class="row mb-2">
                                        <?php 
                                            $disabled = !isset($_SESSION['rif']) ? 'disabled' : '';
                                            $title = !isset($_SESSION['rif'])
                                                ? 'No se puede generar el reporte, debes registrar la informacion de la empresa'
                                                : NULL ;
                                            if (!empty($_SESSION["permisos"]["reporte"])):
                                        ?>
                                            <form action="index.php?pagina=reportes" method="post" target="_blank">
                                                <input type="hidden" name="tipo" value="venta">
                                                <button class="btn btn-danger mx-2" name="pdf" <?= $disabled; ?> title="<?= ($title??'Generar PDF'); ?>" id="pdfc" type="submit">Generar PDF</button>
                                                <button type="button" class="btn btn-default float-right" id="daterange-btn">
                                                    <span><i class="fa fa-calendar"></i> Rango de fecha</span>
                                                    <i class="fas fa-caret-down"></i>
                                                </button>
                                                <button type="button" class="btn btn-secondary mx-2" id="reset-btn">Restablecer Rango</button>
                                                <input type="hidden" name="fechaInicio1" id="fechaInicio" value="<?php echo date('Y-m-d') ?>">
                                                <input type="hidden" name="fechaFin1" id="fechaFin" value="<?php echo date('Y-m-d') ?>">
                                            </form>
                                            <!-- botón "Generar Excel" -->
                                            <form action="index.php?pagina=reportes" method="post" target="_blank" class="d-inline ml-auto">
                                                <input type="hidden" name="tipo" value="ventaxlsx">
                                                <input type="hidden" name="fechaInicio1" id="fechaInicio1" value="<?php echo date('Y-m-d') ?>">
                                                <input type="hidden" name="fechaFin1" id="fechaFin1" value="<?php echo date('Y-m-d') ?>">
                                                <button class="btn btn-success" name="excel" <?= $disabled; ?> title="<?= ($title??'Generar Excel'); ?>" id="excel">Generar Excel</button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                    <table id="venta-table" class="table table-bordered table-striped table-hover datatable1" style="width: 100%;">
                                        <thead>
                                            <tr>
                                                <th>Nro. de Venta</th>
                                                <th>Clientes</th>
                                                <th>Fecha de emisión</th>
                                                <th>Monto</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($consulta as $venta) { ?>
                                                <tr>
                                                    <td><?php echo $venta['cod_venta'] ?></td>
                                                    <td><?php echo $venta['nombre'] . " " . $venta['apellido'] ?></td>
                                                    <td><?php echo $venta['fecha'] ?></td>
                                                    <td><?php echo $venta['total'] ?></td>
                                                    <td>
                                                        <?php if ($venta['status'] == 1): ?>
                                                            <span class="badge bg-secondary">Pendiente</span>
                                                        <?php elseif ($venta['status'] == 2): ?>
                                                            <span class="badge bg-warning">Pago parcial</span>
                                                        <?php elseif ($venta['status'] == 3): ?>
                                                            <span class="badge bg-success">Completada</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-danger">Anulada</span>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="tab-pane fade" id="cliente" role="tabpanel">
                                    <!-- Formulario de filtrado -->
                                    <div class="row mb-2">
                                        <form action="index.php?pagina=reportes" method="post" target="_blank" class="d-inline" id="form1">
                                            <input type="hidden" name="tipo" value="vclientes">
                                            <?php if(!empty($_SESSION["permisos"]["reporte"])): ?>
                                            <button class="btn btn-danger ml-2" name="pdf" <?= $disabled; ?> title="<?= ($title??'Generar PDF'); ?>" id="pdf1" type="submit">Generar PDF</button>
                                            <?php endif; ?>
                                        </form>
                                    </div>
                                    <table id="carga" class="table table-bordered table-striped table-hover datatable" style="width: 100%;">
                                        <thead>
                                            <tr>
                                                <th>Nombre</th>
                                                <th>Cédula/Rif</th>
                                                <th>Teléfono</th>
                                                <th>Cantidad de ventas</th>
                                                <th>Monto total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($datos as $dato) { ?>
                                                <tr>
                                                    <td><?= $dato['nombre'] . " " . $dato['apellido'] ?></td>
                                                    <td><?= $dato['cedula_rif'] ?></td>
                                                    <td><?= $dato['telefono'] ?></td>
                                                    <td><?= $dato['cantidad_ventas'] ?></td>
                                                    <td><?= $dato['monto_total'] ?></td>
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
        </div>
    </section>
</div>
<script>
    $(document).ready(function() {
        $('#daterange-btn').daterangepicker({
            locale: {
                format: 'YYYY-MM-DD',
                applyLabel: 'Aplicar',
                cancelLabel: 'Cancelar',
                fromLabel: 'Desde',
                toLabel: 'Hasta',
                customRangeLabel: 'Rango Personalizado', // Cambia el texto aquí
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
            $('#daterange-btn span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
            // Guardar fechas en campos ocultos
            $('#fechaInicio').val(start.format('YYYY-MM-DD'));
            $('#fechaFin').val(end.format('YYYY-MM-DD'));
            $('#fechaInicio1').val(start.format('YYYY-MM-DD'));
            $('#fechaFin1').val(end.format('YYYY-MM-DD'));
        });

        $('#daterange-btn span').html(moment().subtract(6, 'days').format('MMMM D, YYYY') + ' - ' + moment().add(1, 'days').format('MMMM D, YYYY'));
        $('#fechaInicio').val(moment().subtract(6, 'days').format('YYYY-MM-DD'));
        $('#fechaFin').val(moment().add(1, 'days').format('YYYY-MM-DD'));
        $('#fechaInicio1').val(moment().subtract(6, 'days').format('YYYY-MM-DD'));
        $('#fechaFin1').val(moment().add(1, 'days').format('YYYY-MM-DD'));

        $('#form').on('submit', function(e) {
            const fechaInicio = $('#fechaInicio').val();
            const fechaFin = $('#fechaFin').val();

            const inicio = new Date(fechaInicio);
            const fin = new Date(fechaFin);
            // Validar que la fecha de inicio no sea posterior a la fecha de fin
            if (inicio > fin) {
                Swal.fire({
                    title: 'Error',
                    text: 'La fecha de inicio no puede ser posterior a la fecha de fin.',
                    icon: 'warning'
                });
                e.preventDefault(); // Evitar que el formulario se envíe
                return;
            } else {
                Swal.fire({
                    title: 'Exito',
                    text: 'Reporte Generado.',
                    icon: 'success'
                });
            }
        });
        // Restablecer el rango de fechas al hacer clic en el botón
        $('#reset-btn').on('click', function() {
            $('#fechaInicio').val('');
            $('#fechaFin').val('');
            $('#fechaInicio1').val('');
            $('#fechaFin1').val('');
            $('#daterange-btn span').html('Rango de fecha'); // Cambia el texto del botón
        });
    });
</script>