$(document).ready(function() {
    console.log('js loaded');
    
    function filtrarTiposPago(modalidad) {
        console.log('Filtering for modalidad:', modalidad);
        $('#nombre_tipo_pago option').each(function() {
            var optionModalidad = $(this).data('modalidad');
            console.log('Option:', $(this).text(), 'Modalidad:', optionModalidad);
            
            if (!$(this).val() || optionModalidad === modalidad) {
                $(this).show().prop('disabled', false);
                console.log('Showing option:', $(this).text());
            } else {
                $(this).hide().prop('disabled', true);
                console.log('Hiding option:', $(this).text());
            }
        });
        
        $('#nombre_tipo_pago').val('');
    }
    
    // cambiar entre bancos y cajas según el tipo de moneda
    $('input[name="tipo_moneda"]').on('change', function() {
        var tipoMoneda = $(this).val();
        var modalidad = tipoMoneda == 2 ? 'digital' : 'efectivo';
        

        filtrarTiposPago(modalidad);
        
        if (tipoMoneda == 2) {
            $('.bancos-container').show();
            $('.cajas-container').hide();
            $('#banco').prop('required', true);
            $('#caja').prop('required', false);
        } else if (tipoMoneda == 1) {
            $('.bancos-container').hide();
            $('.cajas-container').show();
            $('#banco').prop('required', false);
            $('#caja').prop('required', true);
        }
    });
    
    // Inicializar tooltips
    $('[data-toggle="tooltip"]').tooltip();

    /* Verificar si el tipo de pago ya está registrado
    $('#nombre_tipo_pago').change(function (e){
        var buscar = $('#nombre_tipo_pago option:selected').text();
        $.post('index.php?pagina=tpago', {buscar}, function(response){
            if(response != ''){
                Swal.fire({
                    title: 'Advertencia',
                    text: 'El tipo de pago ya se encuentra registrado',
                    icon: 'warning'
                });
            }
        },'json');
    });*/

    // editar modal usando eventos de Bootstrap
    $(document).on('click', '.editar', function(e) {
        console.log('Edit button clicked');
        var button = $(this);
        console.log('Button data attributes:', {
            codigo: button.data('codigo'),
            medio: button.data('medio'),
            desc: button.data('desc'),
            cod_metodo: button.data('cod_metodo'),
            status: button.data('status')
        });
    });

    $('#editModal').on('show.bs.modal', function (event) {
        console.log('Edit modal opening');
        var button = $(event.relatedTarget);
        console.log('Button:', button);
        
        var codigo = button.data('codigo');
        var tpago = button.data('medio');
        var status = button.data('status');
        var desc = button.data('desc');
        var cod_metodo = button.data('cod_metodo');
        
        console.log('Data from button:', {
            codigo: codigo,
            tpago: tpago,
            status: status,
            desc: desc,
            cod_metodo: cod_metodo
        });
        
        var modal = $(this);
        modal.find('#codigo').val(codigo);
        modal.find('#tpago').val(tpago);
        modal.find('#status').val(status);
        modal.find('#origin').val(tpago);
        modal.find('#descripcion').val(desc);
        modal.find('#cod_metodo').val(cod_metodo);
        
        console.log('Modal fields after population:', {
            codigo: modal.find('#codigo').val(),
            tpago: modal.find('#tpago').val(),
            status: modal.find('#status').val(),
            origin: modal.find('#origin').val(),
            descripcion: modal.find('#descripcion').val(),
            cod_metodo: modal.find('#cod_metodo').val()
        });
    });

    // eliminar modal usando eventos de Bootstrap
    $('#eliminartpago').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var medio = button.data('medio');
        var codigo = button.data('codigo');
        
        var modal = $(this);
        modal.find('#tpagonombre').text(medio);
        modal.find('#tpagoCodigo').val(codigo);
    });

    function showError(selector, message) {
        $(selector).addClass('is-invalid');
        $(selector).next('.invalid-feedback').html('<i class="fas fa-exclamation-triangle"></i> ' + message.toUpperCase()).css({
            'display': 'block',
            'color': 'red',
        });
    }

    function hideError(selector) {
        $(selector).removeClass('is-invalid');
        $(selector).next('.invalid-feedback').css('display', 'none');
    }

    $('.bancos-container').show();
    $('.cajas-container').hide();
    
    console.log('Triggering initial filter');
    $('input[name="tipo_moneda"]:checked').trigger('change');

    $('#modalRegistrarMedio').on('show.bs.modal', function() {
        $('#gestionarMediosPagoModal').css('opacity', 0);
    });

    $('#modalRegistrarMedio').on('hidden.bs.modal', function() {
        $('#gestionarMediosPagoModal').css('opacity', 1);
    });
    
    $('.editar-medio').on('click', function() {
        var codMetodo = $(this).data('cod-metodo');
        var medioPago = $(this).data('medio-pago');
        var modalidad = $(this).data('modalidad');
        var status = $(this).data('status');
        
        $('#editar_cod_metodo').val(codMetodo);
        $('#editar_medio_pago').val(medioPago);
        $('#editar_status').val(status);
        
        $('input[name="modalidad"][value="' + modalidad + '"]')
            .prop('checked', true)
            .parent()
            .addClass('active')
            .siblings()
            .removeClass('active');
        
        $('#editarMedioModal').modal('show');
        $('#gestionarMediosPagoModal').modal('hide');
    });
    
    $('.eliminar-medio').on('click', function() {
        var codMetodo = $(this).data('cod-metodo');
        var medioPago = $(this).data('medio-pago');
        
        $('#eliminar_cod_metodo').val(codMetodo);
        $('#eliminar_medio_nombre').text(medioPago);
        
        $('#eliminarMedioModal').modal('show');
        $('#gestionarMediosPagoModal').modal('hide');
    });
    
    $('#editarMedioModal, #eliminarMedioModal').on('hidden.bs.modal', function() {
        $('#gestionarMediosPagoModal').modal('show');
    });
});