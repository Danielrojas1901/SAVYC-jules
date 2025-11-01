
console.log('JS');

/* =======================================
FILTRADO POR FECHAS GASTOS
==========================================*/
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
        }else{
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

