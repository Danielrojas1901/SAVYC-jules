

$('#numero_cuenta1').blur(function (e) {
    var buscar = $('#numero_cuenta1').val();
    $.post('index.php?pagina=cuentabancaria', { buscar }, function (response) {
        if (response != '') {
            Swal.fire({
                title: 'Error',
                text: 'El numero de cuenta ya se encuentra registrado.',
                icon: 'warning'
            });
        }
    }, 'json');
});

$('#nombre').blur(function (e) { 
    var buscar = $('#nombre').val();
    $.post('index.php?pagina=banco', { buscar }, function (response) {
        if (response != '') {
            Swal.fire({
                title: 'Error',
                text: 'No se puede registrar una categoría existente.',
                icon: 'warning'
            });
        }
    }, 'json');
});


function showError(selector, message) {
    $(selector).addClass('is-invalid');
    $(selector).siblings('.invalid-feedback').text(message).show();
}

function hideError(selector) {
    $(selector).removeClass('is-invalid');
    $(selector).siblings('.invalid-feedback').hide();
}

/*$(document).ready(function () {
    if (localStorage.getItem('bancoModal') === 'true') {
        $('#modalregistrarCuenta').modal('show');
        localStorage.removeItem('bancoModal');
    }
});*/

$('#modalRegistrarbanco').on('hidden.bs.modal', function () {
   
    $('#formRegistrarbanco')[0].reset();

   
    $('#formRegistrarbanco .is-invalid').removeClass('is-invalid');
    $('#formRegistrarbanco .invalid-feedback').hide();

});

//VALIDACIÓN
$(document).ready(function () {
 
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
    

    $('#numerocuenta').on('blur', function () {
        var numero_cuenta = $(this).val();
        if (numero_cuenta.trim() === '') {
            hideError('#numerocuenta');
        } else if (numero_cuenta.length > 20) {
            showError('#numerocuenta', 'El numero de cuenta no debe exceder los 20 caracteres'); // Validar longitud máxima
        } else if (!/^[0-9]+$/.test(numero_cuenta)) {
            showError('#numerocuenta', 'Solo se permiten numeros');
        }
    });
    $('#saldo').on('blur', function () {
        var saldo = $(this).val();
        if (saldo.trim() === '') {
            hideError('#saldo');
        } else if (saldo.length > 10) {
            showError('#saldo', 'El saldo no debe exceder los 10 caracteres');
        } else if (!/^[0-9.]+$/.test(saldo)) {
            showError('#saldo', 'Solo se permiten números');
        } else {
            hideError('#saldo');
        }
    });


});




$('#modalmodificarcuenta').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var cod = button.data('code');
    var numero = button.data('numero');
    var saldo = button.data('saldo');
    var divisa = button.data('divisa');
    var status = button.data('status');
    var banco = button.data('banco');
    var tipocuenta = button.data('tipocuenta');
    console.log('Editar cuenta bancaria:', cod, numero, saldo, divisa, status, banco, tipocuenta);

   
    $('#cod_cuenta_bancaria_oculto').val(cod);
    $('#cod_cuenta_bancaria1').val(cod);
    $('#numero_cuenta1').val(numero);
    $('#origin').val(numero);
    $('#saldo1').val(saldo);
    $('#divisa1').val(divisa);
    $('#status').val(status);
    $('#banco1').val(banco);
    $('#tipodecuenta1').val(tipocuenta);

    
});


//ELIMINAR
$('#modaleliminar').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var codigo = button.data('cod');
    var status = button.data('status');
    var numero = button.data('numero');

    var modal = $(this);
    modal.find('.modal-body #cod_eliminar').val(codigo);
    modal.find('#numero_cuentaD').text(numero);
    modal.find('.modal-body #status_e').val(codigo);


});


$('#detallemodal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var cod = button.data('cod');
    var numero = button.data('numero');
    var saldo = button.data('saldo');
    var divisa = button.data('divisa');
    var status = button.data('status');
    var banco = button.data('banco');
    var tipocuenta = button.data('tipocuenta');
    if (divisa == 1) {
        mostrar = 'Bolivares';
    }

    var modal = $(this);
    modal.find('.modal-body #mov-codigo').val(cod);
    modal.find('.modal-body #mov-divisa').val(mostrar);
    modal.find('.modal-body #mov-saldo').val(saldo);
   

   
    $('#detalleBody').empty();

    
    $.ajax({
        url: 'index.php?pagina=cuentabancaria',
        method: 'POST',
        data: {
            detalle: cod
        },
        dataType: 'json',
        success: function (data) {
      
            if (data.length === 0) {
               
                $('#detalleBody').append(
                    '<tr>' +
                    '<td colspan="6" class="text-center">No hay movimientos en esta cuenta</td>' +
                    '</tr>'
                );
            } else {
                
                $.each(data, function (index, detalle) {
                    console.log(detalle);
                    const monto = parseFloat(detalle.monto);
                    const tipoClase = monto >= 0 ? 'text-success' : 'text-danger';
                    const tipoTexto = monto >= 0 ? 'ENTRADA' : 'SALIDA';
                   
                    const fecha = new Date(detalle.fecha_movimiento);
                    const dia = String(fecha.getDate()).padStart(2, '0');
                    const mes = String(fecha.getMonth() + 1).padStart(2, '0');
                    const año = fecha.getFullYear();
                    let horas = fecha.getHours();
                    const minutos = String(fecha.getMinutes()).padStart(2, '0');
                    const segundos = String(fecha.getSeconds()).padStart(2, '0');
                    const ampm = horas >= 12 ? 'PM' : 'AM';
                    horas = horas % 12;
                    horas = horas ? horas : 12;
                    var usar =  `${dia}-${mes}-${año} ${horas}:${minutos}:${segundos} ${ampm}`;
                    console.log(usar);
                    $('#detalleBody').append(
                        '<tr>' +
                        '<td>' + usar + '</td>' +
                        '<td>' + tipoTexto + '</td>' +
                        '<td>' + (detalle.modulo || " ") + '</td>' +
                        '<td class="' + tipoClase + '">' + monto.toFixed(2) + '</td>' +
                        '</tr>'
                    );

                });
            }

        },
        error: function (xhr, status, error) {
            console.error('Error al cargar los detalles:', error);
        }
    });


});


function exportarMovimientosExcel($modal) {
    const numeroCuenta = $modal.find('#mov-numero-cuenta').text();
    const fechaInicio = $modal.find('#fecha-inicio').val();
    const fechaFin = $modal.find('#fecha-fin').val();

    const wb = XLSX.utils.book_new();
    const wsData = [
        ['Movimientos de Cuenta Bancaria'],
        ['Número de Cuenta:', numeroCuenta],
        ['Fecha Inicio:', fechaInicio],
        ['Fecha Fin:', fechaFin],
        [],
        ['Fecha', 'Módulo', 'Tipo', 'Referencia', 'Monto']
    ];

    
    $modal.find('#tbodyMovimientos tr').each(function () {
        const row = [
            $(this).find('td:eq(0)').text(),
            $(this).find('td:eq(1)').text(),
            $(this).find('td:eq(2)').text(),
            $(this).find('td:eq(3)').text(),
            $(this).find('td:eq(4)').text()
        ];
        wsData.push(row);
    });

    const ws = XLSX.utils.aoa_to_sheet(wsData);
    XLSX.utils.book_append_sheet(wb, ws, "Movimientos");


    XLSX.writeFile(wb, `Movimientos_Cuenta_${numeroCuenta}_${fechaInicio}_a_${fechaFin}.xlsx`);
}

function mostrarError(mensaje) {
    Swal.fire({
        title: 'Error',
        text: mensaje,
        icon: 'error',
        confirmButtonText: 'OK'
    });
}

function formatearFecha(fechaISO) {
    const fecha = new Date(fechaISO);
    const dia = String(fecha.getDate()).padStart(2, '0');
    const mes = String(fecha.getMonth() + 1).padStart(2, '0'); 
    const año = fecha.getFullYear();

    return `${dia}-${mes}-${año}`;
}

$('#modalregistrarCuenta').on('hidden.bs.modal', function () {
    
    $('#formregistrarCuenta')[0].reset();

 
    $('#formregistrarCuenta .is-invalid').removeClass('is-invalid');
    $('#formregistrarCuenta .invalid-feedback').hide();

});