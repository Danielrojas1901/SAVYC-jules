$(document).ready(function () {

    function setFechas(start, end) {
        $('.daterange-btn span').html(start.format('D MMM YYYY') + ' - ' + end.format('D MMM YYYY'));
        $('.fecha-inicio').val(start.format('YYYY-MM-DD'));
        $('.fecha-fin').val(end.format('YYYY-MM-DD'));
    }

    $('.daterange-btn').daterangepicker({
        locale: {
            format: 'YYYY-MM-DD',
            applyLabel: 'Aplicar',
            cancelLabel: 'Cancelar',
            fromLabel: 'Desde',
            toLabel: 'Hasta',
            customRangeLabel: 'Rango Personalizado',
            firstDay: 1
        },
        ranges: {
            'Hoy': [moment(), moment()],
            'Últimos 7 días': [moment().subtract(6, 'days'), moment()],
            'Últimos 30 días': [moment().subtract(29, 'days'), moment()],
            'Este mes': [moment().startOf('month'), moment().endOf('month')],
            'Mes pasado': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        },
        startDate: moment().subtract(6, 'days'),
        endDate: moment()
    }, setFechas);

    setFechas(moment().subtract(6, 'days'), moment());

    $('form').on('submit', function (e) {
        const fechaInicio = $(this).find('.fecha-inicio').val();
        const fechaFin = $(this).find('.fecha-fin').val();
        const inicio = new Date(fechaInicio);
        const fin = new Date(fechaFin);

        if (inicio > fin) {
            e.preventDefault();
            Swal.fire('Error', 'La fecha de inicio no puede ser posterior a la fecha de fin.', 'warning');
        }
    });

    $('.reset-btn').on('click', function () {
        $('.fecha-inicio, .fecha-fin').val('');
        $('.daterange-btn span').html('Rango de fecha');
    });
});
