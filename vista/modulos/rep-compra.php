<?php require_once 'controlador/compras.php'; ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Reportes de Compra</h1>
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
                            <div class="row mb-2">
                                <form action="index.php?pagina=reportes" method="post" target="_blank">
                                    <input type="hidden" name="tipo" value="compra">
                                    <?php if (!empty($_SESSION["permisos"]["reporte"])): ?>
                                        <button class="btn btn-danger mx-2" name="pdf" title="Generar PDF" id="pdfc" type="submit">Generar PDF</button>
                                        <button type="button" class="btn btn-default float-right" id="daterange-btn">
                                            <span><i class="fa fa-calendar"></i> Rango de fecha</span>
                                            <i class="fas fa-caret-down"></i>
                                        </button>
                                        <button type="button" class="btn btn-secondary mx-2" id="reset-btn">Restablecer Rango</button>
                                        <input type="hidden" name="fechaInicio" id="fechaInicio" value="<?php echo date('Y-m-d') ?>">
                                        <input type="hidden" name="fechaFin" id="fechaFin" value="<?php echo date('Y-m-d') ?>">


                                    <?php endif ?>
                                </form>
                            </div>
                        </div>
                        <div class="card-body">
                            <table id="compra-table" class="table table-bordered table-striped table-hover datatable1" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>Nro. de Compra</th>
                                        <th>Proveedor</th>
                                        <th>Fecha de recepcion</th>
                                        <th>total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($compra as $compra) { ?>
                                        <tr>
                                            <td><?= $compra['cod_compra'] ?></td>
                                            <td><?= $compra['razon_social'] ?></td>
                                            <td><?= $compra['fecha'] ?></td>
                                            <td><?= $compra['total'] ?></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
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
                'Últimos 7 días': [moment().subtract(7, 'days'), moment().add(1, 'days')],
                'Últimos 30 días': [moment().subtract(29, 'days'), moment().add(1, 'days')],
                'Este mes': [moment().startOf('month'), moment().endOf('month')],
                'Mes pasado': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            },
            startDate: moment().subtract(7, 'days'),
            endDate: moment().add(1, 'days')
        }, function(start, end) {
            $('#daterange-btn span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
            // Guardar fechas en campos ocultos
            $('#fechaInicio').val(start.format('YYYY-MM-DD'));
            $('#fechaFin').val(end.format('YYYY-MM-DD'));
            $('#fechaInicio1').val(start.format('YYYY-MM-DD'));
            $('#fechaFin1').val(end.format('YYYY-MM-DD'));
        });

        $('#daterange-btn span').html(moment().subtract(7, 'days').format('MMMM D, YYYY') + ' - ' + moment().add(1, 'days').format('MMMM D, YYYY'));
        $('#fechaInicio').val(moment().subtract(7, 'days').format('YYYY-MM-DD'));
        $('#fechaFin').val(moment().add(1, 'days').format('YYYY-MM-DD'));
        $('#fechaInicio1').val(moment().subtract(7, 'days').format('YYYY-MM-DD'));
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