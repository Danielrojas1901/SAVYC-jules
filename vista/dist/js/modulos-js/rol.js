//VALIDAR ENTRADAS
$(document).ready(function() {
    // FUNCIONES
    function showError(selector, message) {
        $(selector).addClass('is-invalid');
        $(selector).next('.invalid-feedback').html('<i class="fas fa-exclamation-triangle"></i> ' + message).css({
            'display': 'block',
            'color': 'red',
            'background-color': 'white'
        });
    }
    
    function hideError(selector) {
        $(selector).removeClass('is-invalid');
        $(selector).next('.invalid-feedback').css('display', 'none');
    }
    //FIN FUNCIONES


    $('#rol1').on('input', function() {
        var rol1 = $(this).val();
        if (rol1.trim() === '') {
            hideError('#rol1');
        } else if(rol1.length > 50) {
            showError('#rol1', 'El texto no debe exceder los 50 caracteres');
        } else if (!/^[a-zA-ZÀ-ÿ\s]+$/.test(rol1)) {
            showError('#rol1', 'Solo se permiten letras');
        } else {
            hideError('#rol1');
        }
    });

    $('#rol').on('input', function() {
        var rol = $(this).val();
        if (rol.trim() === '') {
            hideError('#rol');
        } else if(rol.length > 50) {
            showError('#rol', 'El texto no debe exceder los 50 caracteres');
        } else if (!/^[a-zA-ZÀ-ÿ\s]+$/.test(rol)) {
            showError('#rol', 'Solo se permiten letras');
        } else {
            hideError('#rol');
        }
    });
});

//Validar rol en registrar
$('#rol1').blur(function (e) {
    var buscar = $('#rol1').val();
    $.post('index.php?pagina=roles', { buscar }, function (response) {
        if (response != '') {
            Swal.fire({
                title: 'Error',
                text: 'Este rol ya se encuentra registrado.',
                icon: 'warning'
            });
        }
    }, 'json');
});

//Validar rol en editar
$('#rol').blur(function (e) {
    var buscar = $('#rol').val();
    $.post('index.php?pagina=roles', { buscar }, function (response) {
        if (response != '') {
            Swal.fire({
                title: 'Error',
                text: 'Este rol ya se encuentra registrado.',
                icon: 'warning'
            });
        }
    }, 'json');
});

//VALIDACIÓN
function validacion() {

    const valor = document.getElementById("rol1").value;
    if (valor == null || valor.length === 0 || /^\s+$/.test(valor)) {
        Swal.fire({
            title: 'Error',
            text: 'El campo Rol es obligatorio y no puede estar vacío.',
            icon: 'warning'
        });
        return false; // Evita el envío del formulario
    }

    /* Verificar si el valor contiene solo caracteres especiales
    if (/^[^a-zA-Z\s]+$/.test(valor)) {
        Swal.fire({
            title: 'Error',
            text: 'El campo Rol no puede contener solo caracteres especiales.',
            icon: 'warning'
        });
        return false; // Evita el envío del formulario
    }*/

    //VALIDAR CHECKBOX
    const checkboxes = document.querySelectorAll('input[name="permisos[]"]');
    const isChecked = Array.from(checkboxes).some(checkbox => checkbox.checked);

    if (!isChecked) {
        Swal.fire({
            title: 'Advertencia',
            text: 'Debe seleccionar al menos un permiso.',
            icon: 'warning'
        });
        return false; // Evita el envío del formulario
    }

    return true; // Permite el envío del formulario

};


// EDITAR
$('#modalmodificarol').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var cod = button.data('cod');
    var rol = button.data('rol');
    var status = button.data('status');

    var modal = $(this);
    // asignar los valores al formulario del modal
    modal.find('#cod').val(cod);
    modal.find('#cod_oculto').val(cod);
    modal.find('#rol').val(rol);
    modal.find('#status').val(status);
    modal.find('#rol_origin').val(rol);
});


//ELIMINAR
$('#modaleliminar').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var cod = button.data('cod');
    var rol = button.data('roleliminar');

    var modal = $(this);
    modal.find('#cod_eliminar').val(cod); 
    modal.find('#tipoderol').text(rol); 
});

// LIMPIAR MODALES AL CERRAR
$('#modalregistrarroles').on('hidden.bs.modal', function () {
    const form = $('#formAgregarRol');
    form[0].reset();
    
    // limpiar validaciones
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').css('display', 'none');
    
    // ocultar todos los contenedores de permisos
    $('.permisos-container').slideUp();
    
    // desmarcar todos los checkboxes
    $('.check-modulo, .check-permiso').prop('checked', false).prop('disabled', false);
    
    // remover campos hidden de permisos
    $('[id^="hidden-permiso-"]').remove();
});

$('#modalmodificarol').on('hidden.bs.modal', function () {
    const form = $('#form-editar-rol');
    form[0].reset();
    
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').css('display', 'none');
});