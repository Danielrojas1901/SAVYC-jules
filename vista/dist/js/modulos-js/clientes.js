console.log('abrio js');

//BUSCAR POR CEDULA O RIF
$('#cedula_rif').blur(function (e){
    var buscar=$('#cedula_rif').val();
    $.post('index.php?pagina=clientes', {buscar}, function(response){
        if(response != ''){
            Swal.fire({
                title: 'Advertencia',
                text: 'El cliente ya se encuentra registrado',
                icon: 'warning'
            });
        }
    },'json');
});

$('#cedularif1').blur(function (e){
    var buscar=$('#cedularif1').val();
    $.post('index.php?pagina=clientes', {buscar}, function(response){
        if(response != ''){
            Swal.fire({
                title: 'Advertencia',
                text: 'El cliente ya se encuentra registrado',
                icon: 'warning'
            });
        }
    },'json');
});

//EDITAR CLIENTE
$('#editModal').on('show.bs.modal', function (event) {

    console.log('EDITAR js');
        var button=$(event.relatedTarget);
        var codigo=button.data('codigo');
        var nombre=button.data('nombre');
        var apellido=button.data('apellido');
        var cedula_rif=button.data('cedula_rif');
        var telefono=button.data('telefono');
        var email=button.data('email');
        var direccion=button.data('direccion');
        var status=button.data('status');
        var origin=button.data('cedula_rif');

        // Modal
        var modal = $(this);
        modal.find('.modal-body #codigo').val(codigo);
        modal.find('.modal-body #nombre1').val(nombre);
        modal.find('.modal-body #apellido1').val(apellido);
        modal.find('.modal-body #cedularif1').val(cedula_rif);
        modal.find('.modal-body #telefono1').val(telefono);
        modal.find('.modal-body #email1').val(email);
        modal.find('.modal-body #direccion1').val(direccion);
        modal.find('.modal-body #status').val(status);
        modal.find('.modal-body #origin').val(origin);
    });

//ELIMINAR CLIENTE
$('#eliminarcliente').on('show.bs.modal', function (event) {
    console.log('ELIMINAR js');

    var button = $(event.relatedTarget); 
    var nombre = button.data('nombre');
    var codigo = button.data('codigo');

    var modal = $(this);
    modal.find('#clienteNombre').text(nombre);
    modal.find('.modal-body #clienteCodigo').val(codigo);
});

$(document).ready(function() {
//FUNCIONES PARA VALIDAR
function showError(selector, message) {
    $(selector).addClass('is-invalid');
    $(selector).next('.invalid-feedback').html('<i class="fas fa-exclamation-triangle"></i> ' + message.toLowerCase()).css({
        'display': 'block',
        'color': 'red',
        'background-color': 'white'
    });
}

function hideError(selector) {
    $(selector).removeClass('is-invalid');
    $(selector).next('.invalid-feedback').css('display', 'none');
}

//VALIDACIONES REGISTRO
$(document).ready(function() {
    // Validación de Cédula o Rif
    $('#cedula_rif').on('input', function() {
        var cedula_rif = $(this).val();
        if (cedula_rif.length > 12) {
            showError('#cedula_rif', 'debe contener maximo 12 números');
        } else if (!/^\d+$/.test(cedula_rif)) {
            showError('#cedula_rif', 'debe contener solo numeros');
        }else {
            hideError('#cedula_rif');
        }
    });

    // Validación de Nombre
    $('#nombre').on('input', function() {
        var nombre = $(this).val();
        if (!nombre || !/^[a-zA-Z\s]+$/.test(nombre)) {
            showError('#nombre', 'debe contener solo letras');
        } else if (nombre.length > 80) {
            showError('#nombre', 'debe contener maximo 80 letras ');
        } else {
            hideError('#nombre');
        }
    });

    // Validación de Apellido
    $('#apellido').on('input', function() {
        var apellido = $(this).val();
        if (!apellido || !/^[a-zA-Z\s]+$/.test(apellido)) {
            showError('#apellido', 'debe contener solo letras');
        } else if (apellido.length > 80) {
            showError('#apellido', 'debe contener entre maximo 80 letras ');
        } else {
            hideError('#apellido');
        }
    });

    // Validación de Teléfono
    $('#telefono').on('input', function() {
        var telefono = $(this).val();
        if (!telefono || !/^[\d\s()+-]*$/.test(telefono)) {
            showError('#telefono', 'solo números y "( ) +"');
        } else {
            hideError('#telefono');
        }
    });
  
//VALIDACIONES EDITAR
    $('#email').on('blur', function() {
        var email = $(this).val();
        if (!email || !/^[\w\.-]+@[a-zA-Z\d\.-]+\.[a-zA-Z]{2,}$/.test(email)) {
            showError('#email', 'formato de correo invalido');
        } else {
            hideError('#email');
        }
    });


    function showError(selector, message) {
        $(selector).addClass('is-invalid');
        $(selector).next('.invalid-feedback').html('<i class="fas fa-exclamation-triangle"></i> ' + message.toLowerCase()).css({
            'display': 'block',
            'color': 'red',
            'background-color': 'white'
        });
    }

    function hideError(selector) {
        $(selector).removeClass('is-invalid');
        $(selector).next('.invalid-feedback').css('display', 'none');
    }
});





//validacion de editar cliente 
$(document).ready(function() {
    // Validación de Nombre
    $('#nombre1').on('input', function() {
        var nombre = $(this).val();
        if (!nombre || !/^[a-zA-Z\s]+$/.test(nombre)) {
            showError('#nombre1', 'debe contener solo letras');
        } else if (nombre.length > 80) {
            showError('#nombre1', 'debe contener maximo 80 letras ');
        } else {
            hideError('#nombre1');
        }
        
    });
        

    // Validación de Apellido
    $('#apellido1').on('input', function() {
        var apellido = $(this).val();
        if (!apellido || !/^[a-zA-Z\s]+$/.test(apellido)) {
            showError('#apellido1', 'debe contener solo letras');
        } else if (apellido.length > 80) {
            showError('#apellido1', 'debe contener entre maximo 80 letras ');
        } else {
            hideError('#apellido1');
        }
        
    });
        

    // Validación de Cédula-Rif
    $('#cedularif1').on('input', function() {
        var cedula_rif = $(this).val();
        if (cedula_rif.length > 12) {
            showError('#cedularif1', 'debe contener maximo 12 números');
        } else if (!/^\d+$/.test(cedula_rif)) {
            showError('#cedularif1', 'debe contener solo numeros');
        }else {
            hideError('#cedularif1');
        }
        
    });

    // Validación de Teléfono
    $('#telefono1').on('input', function() {
        var telefono = $(this).val();
        if (telefono && !/^[\d\s()+-]*$/.test(telefono)) {
            showError('#telefono1', 'Solo números y caracteres especiales permitidos');
        } else {
            hideError('#telefono1');
        }
        
    });

    /*Función para mostrar el error
    function showError(selector, message) {
        $(selector).addClass('is-invalid');
        $(selector).next('.invalid-feedback').html('<i class="fas fa-exclamation-triangle"></i> ' + message.toLowerCase()).css({
            'display': 'block',
            'color': 'red',
            'background-color': 'white'
        });
    }

    // Función para ocultar el error
    function hideError(selector) {
        $(selector).removeClass('is-invalid');
        $(selector).next('.invalid-feedback').css('display', 'none');
    }*/

    // Función para habilitar o deshabilitar el botón de editar
    function toggleEditButton() {
        var isValid = true;
        $('#editForm input[required]').each(function() {
            if ($(this).hasClass('is-invalid') || !$(this).val()) {
                isValid = false;
            }
        });
        $('button[name="actualizar"]').prop('disabled', !isValid);
    }
});
});