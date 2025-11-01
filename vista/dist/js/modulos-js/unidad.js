//Validar registrar
$('#tipo_medida1').blur(function (e) {
    var buscar = $('#tipo_medida1').val();
    $.post('index.php?pagina=unidad', { buscar }, function (response) {
        if (response != '') {
            Swal.fire({
                title: 'Error',
                text: 'La unidad de medida ya se encuentra registrada.',
                icon: 'warning'
            });
        }
    }, 'json');
});


//VALIDACIÓN
$(document).ready(function () {
    // FUNCIONES
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
    // FIN FUNCIONES


    $('#tipo_medida1').on('blur', function() {
        var tipo_medida1 = $(this).val();
        if(tipo_medida1.trim() === ''){
            hideError('#tipo_medida1');
        }else if (tipo_medida1.length > 10) {
            showError('#tipo_medida1', 'El texto no debe exceder los 10 caracteres'); // Validar longitud máxima
        } else if (!/^[a-zA-ZÀ-ÿ\s]+$/.test(tipo_medida1)) {
            showError('#tipo_medida1', 'Solo se permiten letras');
        } else {
            hideError('#tipo_medida1');
        }
    });

    $('#tipo_medida').on('blur', function() {
        var tipo_medida = $(this).val();
        if(tipo_medida.trim() === ''){
            hideError('#tipo_medida');
        }else if (tipo_medida.length > 10) {
            showError('#tipo_medida', 'El texto no debe exceder los 10 caracteres'); // Validar longitud máxima
        } else if (!/^[a-zA-ZÀ-ÿ\s]+$/.test(tipo_medida)) {
            showError('#tipo_medida', 'Solo se permiten letras');
        } else {
            hideError('#tipo_medida');
        }
    });
});

//EDITAR
$('#modalmodificarunidad').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var cod = button.data('cod');
    var tipo = button.data('tipo');
    var status = button.data('status');

    var modal = $(this);
    // asignar los valores al formulario del modal
    modal.find('#cod_unidad').val(cod);
    modal.find('#cod_unidad_oculto').val(cod);
    modal.find('#tipo_medida').val(tipo);
    modal.find('#status').val(status);
    modal.find('#origin').val(tipo);   
});
//ELIMINAR
$('#modaleliminar').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget); 
    var codigo = button.data('cod');
    var status = button.data('status');
    var tipomedida = button.data('tipo');

    var modal = $(this);
    modal.find('.modal-body #cod_eliminar').val(codigo);
    modal.find('#tipomedidaD').text(tipomedida);
    modal.find('.modal-body #status_e').val(codigo);

    console.log(tipomedida,codigo);
});

// LIMPIAR MODALES AL CERRAR
$('#modalregistrarUnidad').on('hidden.bs.modal', function () {
    const form = $('#formregistrarUnidad');
    form[0].reset();
    
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').css('display', 'none');
});

$('#modalmodificarunidad').on('hidden.bs.modal', function () {
    const form = $('#form-editar-unidad');
    form[0].reset();
    
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').css('display', 'none');
});




