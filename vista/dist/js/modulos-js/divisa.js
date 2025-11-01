console.log('abrio js');

//VALIDACIONES REGISTRAR
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


$('#nombre').on('blur', function() {
    var nombre = $(this).val();
    if (nombre.trim() === '') {
        hideError('#nombre');
    }else if (nombre.length > 50) {
        showError('#nombre', 'El texto no debe exceder los 50 caracteres'); // Validar longitud máxima
    } else if (!/^[a-zA-ZÀ-ÿ\s]+$/.test(nombre)) {
        showError('#nombre', 'Solo se permiten letras');
    } else {
        hideError('#nombre');
    }
});

$('#simbolo').on('blur', function() {
    var simbolo = $(this).val();
    if (simbolo.trim() === '') {
        hideError('#simbolo'); 
    }else if (simbolo.length > 5) {
        showError('#simbolo', 'El texto no debe exceder los 5 caracteres'); // Validar longitud máxima
    } else if (!/^[a-zA-ZÀ-ÿ\s\$\€]+$/.test(simbolo)) {
        showError('#simbolo', 'Solo se permiten letras, números, espacios y $, y €');
    } else {
        hideError('#simbolo');
    }
});

$('#tasa').on('input', function() {
    var tasa = $(this).val();
    if (tasa.trim() === '') {
        hideError('#tasa'); 
    } else if (!/^\d+(\.\d{1,2})?$/.test(tasa)) { // Permite números y un máximo de 2 decimales
        showError('#tasa', 'Solo se permiten números y 2 decimales opcional.');
    } else {
        hideError('#tasa'); 
    }
});

//VALIDACIONES EDITAR

$('#nombre1').on('blur', function() {
    var nombre1 = $(this).val();
    if (nombre1.trim() === '') {
        hideError('#nombre1');
    }else if (nombre1.length > 50) {
        showError('#nombre1', 'El texto no debe exceder los 50 caracteres'); // Validar longitud máxima
    } else if (!/^[a-zA-ZÀ-ÿ\s]+$/.test(nombre1)) {
        showError('#nombre1', 'Solo se permiten letras');
    } else {
        hideError('#nombre1');
    }
});


$('#abreviatura').on('blur', function() {
    var simbolo = $(this).val();
    if (simbolo.trim() === '') {
        hideError('#abreviatura'); 
    } else if (simbolo.length > 5) {
        showError('#abreviatura', 'El texto no debe exceder los 5 caracteres');
    } else if (!/^[a-zA-Z0-9\s\.\,\-\#]+$/.test(simbolo)) {
        showError('#abreviatura', 'Solo se permiten letras, números y signos (.,-#)');
    } else {
        hideError('#abreviatura');
    }
});


$('#tasa1').on('input', function() {
    var tasa1 = $(this).val();
    if (tasa1.trim() === '') {
        hideError('#tasa1'); 
    } else if (!/^\d+(\.\d{1,2})?$/.test(tasa1)) { // Permite números y un máximo de 2 decimales
        showError('#tasa1', 'Solo se permiten números y 2 decimales opcional.');
    } else {
        hideError('#tasa1'); 
    }
});

//VALIDAR NOMBRE REGISTRO
$('#nombre').blur(function (e){
    var buscar=$('#nombre').val();
    $.post('index.php?pagina=divisa', {buscar}, function(response){
    if(response != ''){
        Swal.fire({
            icon: 'warning',
            title: 'Advertencia',
            text: 'La divida ya se encuentra registrada',
            confirmButtonText: 'Aceptar'
        });
    }
    },'json');
});
// VALIDAR NOMBRE EDITAR
$('#nombre1').blur(function (e){
    var buscar=$('#nombre1').val();
    $.post('index.php?pagina=divisa', {buscar}, function(response){
    if(response != ''){
        Swal.fire({
            icon: 'warning',
            title: 'Advertencia',
            text: 'La divisa ya se encuentra registrada',
            confirmButtonText: 'Aceptar'
        });
    }
    },'json');
});

//EDITAR
$('#actModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var codigo = button.data('codigo');
    var nombre = button.data('nombre');
    var abreviatura = button.data('abreviatura');
    var tasa = button.data('tasa');

    // campos modal
    var modal = $(this);
    modal.find('.modal-body #codigo2').val(codigo);
    modal.find('.modal-body #nombre2').val(nombre);
    modal.find('.modal-body #abreviatura2').val(abreviatura);
    modal.find('.modal-body #tasa2').val(tasa);

    $('#div-boton-actualizar-tasa').hide();
    if(codigo===2){
        $('#div-boton-actualizar-tasa').show();
    }
    
    var hoy = new Date();
    var dd = String(hoy.getDate()).padStart(2, '0');
    var mm = String(hoy.getMonth() + 1).padStart(2, '0');
    var yyyy = hoy.getFullYear();
    hoy = yyyy + '-' + mm + '-' + dd;
    modal.find('.modal-body #fecha2').val(hoy);

    // actualizar datatabla historial
    var historialFiltrado = historialDivisas.filter(function(item) {
        return item.cod_divisa == codigo;
    });

    var tabla = $(this).find('#carga table').DataTable();
    tabla.clear();
    
    historialFiltrado.forEach(function(item) {
        tabla.row.add([
            item.fecha,
            item.tasa
        ]);
    });
    
    tabla.order([0, 'desc']).draw();
});

// Validate rate update form
$('#actForm').on('submit', function(e) {
    var isValid = true;
    var tasa = $('#tasa2').val();
    var fecha = $('#fecha2').val();
    
    // Validate tasa
    if (!tasa || tasa <= 0) {
        showError('#tasa2', 'La tasa debe ser mayor que 0');
        isValid = false;
    } else {
        hideError('#tasa2');
    }
    
    // Validate fecha
    if (!fecha) {
        showError('#fecha2', 'La fecha es requerida');
        isValid = false;
    } else {
        hideError('#fecha2');
    }
    
    if (!isValid) {
        e.preventDefault();
    }
});


$(document).ready(function() {
    $('#btn-actualizar-tasa').on('click', function() {
        //e.preventDefault();
        var sen = "dolar";
        var tasaorig = $("#tasa2").val();

        // Mostrar modal de carga
        $("#loadingModal").modal("show");

        $.post('index.php?pagina=divisa', { sen: sen }, function(response) {
            console.log("Respuesta del servidor:", response);
            let tasa = parseFloat(response.replace(',', '.'));

            if (response !== "error") {
                $('#tasa2').val(tasa.toFixed(2));
            } else {
                console.log("Error en la respuesta.");
                $('#tasaactual').val(tasaorig);
            }
            console.log(tasa);
            // Ocultar modal de carga
            $("#loadingModal").modal("hide");

        }).fail(function() {
            console.log("Error en la solicitud AJAX.");
            $("#loadingModal").modal("hide");
        });
    });
});








$('#editModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var codigo = button.data('codigo');
    var nombre = button.data('nombre');
    var origi = button.data('nombre');
    var abreviatura = button.data('abreviatura');
    var tasa = button.data('tasa');
    var status = button.data('status');

    // Modal
    var modal = $(this);
    modal.find('.modal-body #codigo').val(codigo);
    modal.find('.modal-body #nombre1').val(nombre);
    modal.find('.modal-body #abreviatura').val(abreviatura);
    modal.find('.modal-body #tasa1').val(tasa);
    modal.find('.modal-body #status').val(status);
    modal.find('.modal-body #origin').val(origi);

});

//ELIMINAR
$('#eliminardivisa').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget); 
    var nombre = button.data('nombre');
    var codigo = button.data('codigo');

    var modal = $(this);
    modal.find('#divisaNombre').text(nombre);
    modal.find('.modal-body #divisaCodigo').val(codigo);
});




