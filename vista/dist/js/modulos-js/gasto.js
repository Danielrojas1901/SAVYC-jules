$('#descripcion').blur(function (e) {
    var buscar = $('#descripcion').val();
    $.post('index.php?pagina=gasto', { buscar }, function (response) {
        if (response != '') {
            Swal.fire({
                title: 'Error',
                text: 'No se puede registrar un gasto existente.',
                icon: 'warning'
            });
        }
    }, 'json');
});

$('#nombreG').blur(function (e) {
    var buscar = $('#nombreG').val();
    $.post('index.php?pagina=gasto', { buscar }, function (response) {
        if (response != '') {
            Swal.fire({
                title: 'Error',
                text: 'No se puede registrar un gasto existente.',
                icon: 'warning'
            });
        }
    }, 'json');
});

$('#frecuencia').blur(function (e) {
    var buscarF = $('#frecuencia').val();
    $.post('index.php?pagina=categoriag', { buscarF }, function (response) {
        if (response != '') {
            Swal.fire({
                title: 'Error',
                text: 'No se puede registrar una frecuencia existente.',
                icon: 'warning'
            });
        }
    }, 'json');
});

$('#pagoFormG').on('submit', function (event) {
    event.preventDefault(); 
    var errores = 0;
    var peticiones = 0;
    var respuestas = 0;
    var form = this;
    console.log('DETENER ENVIO');

    $('#pagoFormG').find('.monto-bsG, .monto-divisaG').each(function () {
        var $input = $(this);
        var monto = parseFloat($input.val()) || 0;
        if (monto > 0) {
            var id = $input.attr('id');
            var index = id ? id.split('-').pop() : null;
            var cod_tipo_pago = $('input[name="pago[' + index + '][cod_tipo_pago]"]').val();

            peticiones++;
            $.ajax({
                type: 'POST',
                url: 'index.php?pagina=gasto',
                data: {
                    cod_tipo_pago: cod_tipo_pago
                },
                dataType: 'json',
                success: function (response) {
                    respuestas++;
                    var saldo = response.n;
              
                    if (typeof saldo === 'object' && saldo !== null && saldo.saldo !== undefined) {
                        saldo = saldo.saldo;
                    }
                    console.log('saldo recibido:', saldo);
                    if (saldo !== null && saldo < monto) {
                        errores++;
                        Swal.fire({
                            title: 'Saldo insuficiente',
                            text: 'No hay saldo suficiente para este tipo de pago.',
                            icon: 'error'
                        });
                        $input.val('');

                    } else if (respuestas === peticiones && response.n !== null) {
                        if (errores === 0) {
                            Swal.fire({
                                title: 'Saldo suficiente',
                                text: '¿Desea finalizar el pago?',
                                icon: 'success',
                                showCancelButton: true,
                                confirmButtonText: 'Sí, finalizar',
                                cancelButtonText: 'Cancelar'
                            }).then((result) => {
                                if ($('#pagoFormG').find('input[name="pagar_gasto"]').length === 0) {
                                    $('<input>').attr({
                                        type: 'hidden',
                                        name: 'pagar_gasto',
                                        value: '1'
                                    }).appendTo('#pagoFormG');
                                }
                                if (result.isConfirmed) {
                                    form.submit(); 
                                }
                            });
                        }
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Error en AJAX:', error);
                    Swal.fire({
                        title: 'Error',
                        text: 'Ocurrió un error al verificar el saldo.',
                        icon: 'error'
                    });
                }
            });
        }
    });
});

$('#nombre').blur(function (e) {
    var buscar = $('#nombre').val();
    $.post('index.php?pagina=categoriag', { buscar }, function (response) {
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

$(document).ready(function () {

    $('#formregistrarCategoria').on('submit', function (e) {
        var fecha = $('#fecha').val();
        var fechactual = new Date();
        fechactual.setHours(0, 0, 0, 0);

        var anual = fechactual.getFullYear();
        var mes = String(fechactual.getMonth() + 1).padStart(2, '0');
        var dia = String(fechactual.getDate()).padStart(2, '0');
        var fechactualformateada = `${anual}-${mes}-${dia}`;

        if (fecha < fechactualformateada) {
            e.preventDefault();
            Swal.fire({
                title: 'Advertencia',
                text: 'La fecha no puede ser una fecha pasada.',
                icon: 'warning'
            }).then(() => {
                location.reload();
            });
            return;
        }
    });
});

$('#fecha').on('blur', function () {
    var fecha = $(this).val();
    if (fecha.trim() === '') {
        showError('#fecha', 'el campo fecha no puede estar vacío');
    } else {
        hideError('#fecha');
    }
});

$('#fechavencimiento').on('blur', function () {
    var fecha = $(this).val();
    if (fecha.trim() === '') {
        showError('#fechavencimiento', 'el campo fecha del vencimiento del producto/servicio no puede estar vacío');
    } else {
        hideError('#fechavencimiento');
    }
});

$(document).ready(function () {
    $('#naturaleza').on('input', function () {
        var selectedValue = $(this).val();

        if (selectedValue === '1') {
            $('#frecuenciaContainer').show();
        } else {
            $('#frecuenciaContainer').hide();
        }
    });
});

//AJUSTE DE GASTOS 

$(document).ready(function () {
    var fechactual = new Date();
    var anual = fechactual.getFullYear();
    var mes = String(fechactual.getMonth() + 1).padStart(2, '0');
    var dia = String(fechactual.getDate()).padStart(2, '0');
    var formateada = `${dia}/${mes}/${anual}`;
    $('#fecha_del_pago').text(formateada);
    var fechaguardar = `${anual}-${mes}-${dia}`;
    $('#fecha_del_pago').val(formateada);
    $('#categoriaG').on('change', function () {
        var codCategoria = $(this).val();
        $.ajax({
            url: 'index.php?pagina=gasto',
            type: 'POST',
            data: { mostrarTporC: codCategoria },
            success: function (response) {
                try {
                    if (response.tipo_gasto) {
                        $('#Tgasto').val(response.tipo_gasto);
                        if (response.tipo_gasto === 'producto') {
                            $('#condicion').empty();
                            $('#condicion').append('<option value="">Seleccione una opción</option>');
                            $('#condicion').append('<option value="3">A crédito</option>');
                            $('#condicion').append('<option value="4">Al contado</option>');
                        } else {
                            $('#condicion').empty();
                            $('#condicion').append('<option value="" >Seleccione una opción</option>');
                            $('#condicion').append('<option value="1">Prepago</option>');
                            $('#condicion').append('<option value="2">Pospago</option>');
                        }
                    } else {
                        $('#Tgasto').val('');
                    }
                } catch (error) {
                    console.error('Error al procesar la respuesta:', error);
                }
            },
            error: function () {
                console.error('Error al obtener el tipo de gasto');
            }
        });
    });

    $('#condicion').on('change', function () {
        var condicion = $(this).find('option:selected').text().toLowerCase();

        if (condicion.includes('contado')) {
            $('#fechavencimiento').show();
            console.log(fechaguardar)
            $('#fechavencimiento label').text('Vencimiento');
            $('#fecha_inicio').val(fechaguardar);
            $('#fecha_vencimiento').val(fechaguardar).prop('readonly', true);
        } else if (condicion.includes('crédito')) {
            $('#fechavencimiento').show();
            $('#fechavencimiento label').text('Vencimiento');
            $('#fecha_inicio').val(fechaguardar);
            $('#fecha_vencimiento').prop('readonly', false).val('');
        } else if (condicion.includes('prepago')) {
            $('#fechavencimiento').show();
            $('#fechavencimiento label').text('Recarga');
            $('#fecha_inicio').val(fechaguardar);
            $('#fecha_vencimiento').prop('readonly', false).val('');
        } else if (condicion.includes('pospago')) {
            $('#fechavencimiento').show();
            $('#fechavencimiento label').text('Vencimiento');
            $('#fecha_inicio').val(fechaguardar);
            $('#fecha_vencimiento').prop('readonly', false).val('');
        } else {
            $('#fechavencimiento').hide();
            $('#fecha_vencimiento').prop('readonly', false).val('');
        }
    });
});

$(document).ready(function () {
    if (localStorage.getItem('categoriaModal') === 'true') {
        $('#modalRGasto').modal('show');
        localStorage.removeItem('categoriaModal');
    }
});

//PAGOS DE GASTOS
$('#pagoModalG').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var codigoG = button.data('cod_gasto');
    var total = button.data('totalg');
    var nombre = button.data('nombre');
    var saldog = button.data('montop');
    var pagopendiente = parseFloat(total) - parseFloat(saldog) || 0;

    var now = new Date();
    var fecha = now.getFullYear() + '-' +
        String(now.getMonth() + 1).padStart(2, '0') + '-' +
        String(now.getDate()).padStart(2, '0');

    var hora = String(now.getHours()).padStart(2, '0') + ':' +
        String(now.getMinutes()).padStart(2, '0') + ':' +
        String(now.getSeconds()).padStart(2, '0');
    var fechaHoraG = fecha + ' ' + hora;

    var modal = $(this);
    modal.find('.modal-body #cod_gasto').val(codigoG);
    modal.find('.modal-body #saldog').val(saldog);
    modal.find('.modal-body #total-pagoG').text(total + 'Bs');
    modal.find('.modal-body #total_gasto').val(total);
    modal.find('.modal-body #descripcionG').val(nombre);
    modal.find('.modal-body #fecha_pagoG').val(fechaHoraG);

    if (saldog > 0) {
        $('#campo-saldoG').show();
        $('#saldo_pendienteG').text(pagopendiente.toFixed(2) + ' Bs');
        modal.find('.modal-body #monto_pagarG').val(pagopendiente.toFixed(2));
    } else {
        $('#campo-saldoG').hide();
        $('#saldo_pendienteG').val('');
        modal.find('.modal-body #monto_pagarG').val(total);
    }

    window.vueltoRegistradoG = false;
    $('#btn-registrar-vueltoG').text('Registrar Vuelto');
    $('#div-boton-vueltoG').hide();
    $('#finalizarPagoG').prop('disabled', false);
});

function calcularTotalpagoG() {
    console.log("Calculando total de pago...");
    let totalBs = 0;

    document.querySelectorAll('.monto-bsG:not(.monto-conG)').forEach(function (input) {
        let montoBs = parseFloat(input.value) || 0;
        totalBs += montoBs; 
        console.log('Monto en Bs:', montoBs);
    });
 
    document.querySelectorAll('.monto-divisaG').forEach(function (inputDivisa) {
        let index = inputDivisa.id.split('-').pop(); 

      
        let montoDivisa = parseFloat(inputDivisa.value) || 0;

      
        let tasaConversion = parseFloat(document.getElementById('tasa-conversionG-' + index).value) || 1;

   
        let montoConvertidoBs = montoDivisa * tasaConversion;

       
        document.getElementById('monto-bsG-con-' + index).value = montoConvertidoBs.toFixed(2);

       
        totalBs += montoConvertidoBs;
    });

   
    document.getElementById('monto_pagadoG').value = totalBs.toFixed(2);

  
    let montoPagar = parseFloat(document.getElementById('monto_pagarG').value) || 0;
    let diferenciaG = montoPagar - totalBs;
    document.getElementById('diferenciaG').value = diferenciaG.toFixed(2);
    if (diferenciaG < 0) {
        vuelto = Math.abs(diferenciaG);
        document.getElementById('vueltoG').value = vuelto.toFixed(2);
        console.log("vueltoG: " + vuelto);
    }
    window.vueltoRegistradoG = false;
    if (diferenciaG < 0) {
      
        $('#div-boton-vueltoG').show();

        
        $('#btn-registrar-vueltoG').data('monto-vueltoG', Math.abs(diferenciaG));

  
        $('#finalizarPagoG').prop('disabled', true);
    } else {
       
        $('#div-boton-vueltoG').hide();
       
        $('#btn-registrar-vueltoG').removeData('monto-vueltoG');
       
        $('#finalizarPagoG').prop('disabled', false);
        
        window.vueltoRegistradoG = false;
    }

    if (window.vueltoRegistradoG && diferenciaG < 0) {
       
        Swal.fire({
            title: 'Atención',
            text: 'Has modificado el pago. Debes registrar el vuelto nuevamente.',
            icon: 'warning',
            confirmButtonText: 'Entendido'
        });
        window.vueltoRegistradoG = false;
        $('#finalizarPagoG').prop('disabled', true);
    }
}

$('#vueltoModalG').on('show.bs.modal', function (event) {
    const button = $(event.relatedTarget);
    let montoVueltoG = button.data('monto-vueltoG');

    if (montoVueltoG === undefined || montoVueltoG === null || isNaN(montoVueltoG)) {
        montoVueltoG = 0;
    }

    $('#total-vueltoG').text(montoVueltoG.toFixed(2) + 'Bs');
    $('#monto_pagarvg').val(montoVueltoG.toFixed(2));

    document.querySelectorAll('.monto-bsvG').forEach(function (input) {
        input.value = '';
    });
    document.querySelectorAll('.monto-divisavG').forEach(function (input) {
        input.value = '';
    });

    $('#registrarVueltoG').prop('disabled', true);

    calcularTotalvueltoG();
});

$(document).ready(function () {
    window.vueltoRegistradoG = false;
    $('#pagoModalG').on('hidden.bs.modal', function () {
        
        location.reload();
    });

   
    $('#registrarVueltoG').on('click', function (e) {
        e.preventDefault();

     
        let diferencia = parseFloat(document.getElementById('diferenciavG').value);

        if (Math.abs(diferencia) < 0.01) { 
          
            let vueltoData = $('#vueltoFormG').serialize();

        
            let vueltoInput = document.createElement('input');
            vueltoInput.type = 'hidden';
            vueltoInput.name = 'pagoV';
            vueltoInput.value = vueltoData;
            document.getElementById('pagoFormG').appendChild(vueltoInput);

           
            window.vueltoRegistradoG = true;

   
            $('#vueltoModalG').modal('hide');

     
            $('#finalizarPagoG').prop('disabled', false);

            
            Swal.fire({
                title: 'Vuelto registrado',
                text: 'El vuelto ha sido registrado correctamente.',
                icon: 'success',
                confirmButtonText: 'Aceptar'
            });

           
            $('#btn-registrar-vueltoG').text('Editar Vuelto');
        } else {
         
            Swal.fire({
                title: 'Error',
                text: 'El vuelto debe ser exactamente igual al monto calculado.',
                icon: 'error',
                confirmButtonText: 'Entendido'
            });
        }
    });

  
    $('#pagoFormG').on('submit', function (e) {
        let diferencia = parseFloat(document.getElementById('diferenciaG').value);

    
        if (diferenciaG < 0 && !window.vueltoRegistradoG) {
            e.preventDefault(); 

        
            Swal.fire({
                title: 'Error',
                text: 'Debes registrar el vuelto antes de finalizar el pago.',
                icon: 'error',
                confirmButtonText: 'Entendido'
            });

            return false;
        }


        return true;
    });

   
    $('.monto-bs, .monto-divisaG').on('input', function () {
        calcularTotalpagoG();
    });

});

function calcularTotalvueltoG() {
    let totalBs = 0;

    document.querySelectorAll('.monto-bsvG:not(.monto-convG)').forEach(function (input) {
        let montoBs = parseFloat(input.value) || 0;
        totalBs += montoBs;
    });

    document.querySelectorAll('.monto-divisavG').forEach(function (inputDivisa) {
        let index = inputDivisa.id.split('-').pop();
        let montoDivisa = parseFloat(inputDivisa.value) || 0;
        let tasaConversion = parseFloat(document.getElementById('tasa-conversionvG-' + index).value) || 1;
        let montoConvertidoBs = montoDivisa * tasaConversion;
        document.getElementById('monto-bs-con-vG' + index).value = parseFloat(montoConvertidoBs.toFixed(2));
        totalBs += montoConvertidoBs;

    });

    document.getElementById('vuelto_pagadoG').value = totalBs.toFixed(2);
    let montoPagar = parseFloat(document.getElementById('monto_pagarvg').value) || 0;
    let diferencia = montoPagar - totalBs;
    document.getElementById('diferenciavG').value = diferencia.toFixed(2);

    if (Math.abs(diferencia) < 0.01) {
        $('#registrarVueltoG').prop('disabled', false);
    } else {
        $('#registrarVueltoG').prop('disabled', true);
    }
}


//EDITAR
$('#modificargasto').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var codigo = button.data('cod_gasto');
    var nombre = button.data('descripcion');
    var monto = button.data('monto');
    
    var modal = $(this);
    modal.find('#cod_gastoE').val(codigo);
    modal.find('#nombreG').val(nombre);
    modal.find('#cod_gasto_oculto').val(codigo);
    modal.find('#origin').val(nombre);
    modal.find('#montoe').val(monto);
});

// MODAL ELIMINAR GASTO
$('#eliminarG').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var codigo = button.data('cod_gasto');
    var nombre = button.data('descripcion');
    var modal = $(this);
    modal.find('#cod_eliminar').val(codigo);
    modal.find('#gasto').text(nombre);
});

$('#modificat').on('show.bs.modal', function (event) {
    console.log("Modal de EDICIÓN abierto");

    var button = $(event.relatedTarget);
    var codigo = button.data('codigo');
    var nombre = button.data('nombre');
    var status = button.data('status');

    console.log("Nombre del gasto:", nombre);
    console.log("Código del gasto:", codigo);

    var modal = $(this);
    modal.find('.modal-body #cod_cat_gasto').val(codigo);
    modal.find('.modal-body #nombreE').val(nombre);
    modal.find('.modal-body #cod_cat_gasto_oculto').val(codigo);
    modal.find('.modal-body #origin').val(nombre);
    modal.find('.modal-body #status').val(status);


});


$('#modaleliminar').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var codigo = button.data('codigo');
    var nombre = button.data('nombre');

    var modal = $(this);
    modal.find('.modal-body #cod_eliminar').val(codigo);
    modal.find('.modal-body #categoria').text(nombre);

});





//VALIDACIONES DE FORMULARIOS
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



    $('#nombre').on('blur', function () {//cateogria
        var nombre = $(this).val();
        if (nombre.trim() === '') {
            showError('#nombre', 'no puede estar vacío');
        } else if (nombre.length > 35) {
            showError('#nombre', 'El texto no debe exceder los 35 caracteres');
        } else if (!/^[\p{L}ñÑ\d\s\.,\-#áéíóúÁÉÍÓÚüÜ]+$/u.test(nombre)) {
            showError('#nombre', 'Solo se permiten letras y algunos caracteres');
        } else {
            hideError('#nombre');
        }
    });
    $('#nombreE').on('blur', function () {
        var nombreE = $(this).val();
        if (nombreE.trim() === '') {
            showError('#nombreE', 'no puede estar vacío');
        } else if (nombreE.length > 35) {
            showError('#nombreE', 'El texto no debe exceder los 35 caracteres');
        } else if (!/^[\p{L}ñÑ\d\s\.,\-#áéíóúÁÉÍÓÚüÜ]+$/u.test(nombreE)) {
            showError('#nombreE', 'Solo se permiten letras y algunos caracteres');
        } else {
            hideError('#nombreE');
        }
    });
    $('#fecha').on('blur', function () {
        var fecha = $(this).val();
        if (fecha.trim() === '') {
            showError('#fecha', 'el campo fecha no puede estar vacío');
        } else {
            hideError('#fecha');
        }
    });
    $('#tipogasto').on('blur', function () {
        var tipogasto = $(this).val();
        if (tipogasto.trim() === '') {
            showError('#tipogasto', 'el campo tipo de gasto no puede estar vacío');
        } else {
            hideError('#tipogasto');
        }
    });
    $('#naturaleza').on('blur', function () {
        var naturaleza = $(this).val();
        if (naturaleza.trim() === '') {
            showError('#naturaleza', 'el campo naturaleza no puede estar vacío');
        } else {
            hideError('#naturaleza');
        }
    });

    $('#frecuencia').on('blur', function () {
        var frecuencia = $(this).val();
        if (frecuencia.trim() === '') {
            showError('#frecuencia', 'el campo frecuencia no puede estar vacío');
        } else if (frecuencia.length > 15) {
            showError('#frecuencia', 'El texto no debe exceder los 15 caracteres');
        } else if (!/^[\p{L}ñÑ\d\s\.,\-#áéíóúÁÉÍÓÚüÜ]+$/u.test(frecuencia)) {
            showError('#frecuencia', 'Solo se permiten letras y algunos caracteres');
        } else {
            hideError('#frecuencia');
        }
    });

    $('#dias').on('blur', function () {
        var dias = $(this).val();
        if (dias.trim() === '') {
            showError('#dias', 'el campo días no puede estar vacío');
        } else if (dias.length > 11) {
            showError('#dias', 'El texto no debe exceder los 11 caracteres');
            preventDefault(e);
        } else if (!/^[0-9\.,]+$/.test(dias)) {
            showError('#días', 'solo se aceptan números');
        } else {
            hideError('#dias');
        }
    });

    //GASTOS
    $('#descripcion').on('blur', function () {
        var descripcion = $(this).val();
        if (descripcion.trim() === '') {
            showError('#descripcion', 'el campo descripción no puede estar vacío');
        } else if (descripcion.length > 45) {
            showError('#descripcion', 'El texto no debe exceder los 45 caracteres');
        } else if (!/^[\p{L}ñÑ\d\s\.,\-#áéíóúÁÉÍÓÚüÜ]+$/u.test(descripcion)) {
            showError('#descripcion', 'Solo se permiten letras y algunos símbolos');
        } else {
            hideError('#descripcion');
        }
    });

    $('#monto').on('blur', function () {
        var monto = $(this).val();
        if (monto.trim() === '') {
            showError('#monto', 'el campo monto no puede estar vacío');
        } else if (!/^[0-9\.,]+$/.test(monto)) {
            showError('#monto', 'solo se aceptan números');
        } else {
            hideError('#monto');
        }
    });

    $('#montoe').on('blur', function () {
        var monto = $(this).val();
        if (monto.trim() === '') {
            showError('#montoe', 'el campo monto no puede estar vacío');
        } else if (!/^[0-9\.,]+$/.test(monto)) {
            showError('#montoe', 'solo se aceptan números');
        } else {
            hideError('#montoe');
        }
    });

    $('#nombreG').on('blur', function () {
        var nombreG = $(this).val();
        if (nombreG.trim() === '') {
            showError('#nombreG', 'el campo descripción no puede estar vacío');
        } else if (nombreG.length > 45) {
            showError('#nombreG', 'El texto no debe exceder los 45 caracteres');
        } else if (!/^[\p{L}ñÑ\d\s\.,\-#áéíóúÁÉÍÓÚüÜ]+$/u.test(nombreG)) {
            showError('#nombreG', 'Solo se permiten letras y algunos símbolos');
        } else {
            hideError('#nombreG');
        }
    });

});

$('#modalRGasto').on('hidden.bs.modal', function () {

    var fechaPago = $('#fecha_del_pago').val();


    $('#formRegistrarGastos')[0].reset();

   
    $('#fecha_del_pago').val(fechaPago);

    
    $('#formRegistrarGastos .is-invalid').removeClass('is-invalid');
    $('#formRegistrarGastos .invalid-feedback').hide();

  
    $('#Tgasto').val('');
    $('#condicion').empty();
    $('#condicion').append('<option value="" selected disabled>Seleccione una opción</option>');
    $('#fechavencimiento').hide();
    $('#fecha_vencimiento').val('');
});

$('#deshacer').on('click', function (e) {
    e.preventDefault();
    var fechaPago = $('#fecha_del_pago').val();
    $('#formRegistrarGastos')[0].reset();
    $('#fecha_del_pago').val(fechaPago);
    $('#formRegistrarGastos .is-invalid').removeClass('is-invalid');
    $('#formRegistrarGastos .invalid-feedback').hide();
    $('#Tgasto').val('');
    $('#condicion').empty();
    $('#condicion').append('<option value="" selected disabled>Seleccione una opción</option>');
    $('#fechavencimiento').hide();
    $('#fecha_vencimiento').val('');
});

$('#modalCategoria').on('hidden.bs.modal', function () {
  
    $('#formregistrarCategoria')[0].reset();

    
    $('#formregistrarCategoria .is-invalid').removeClass('is-invalid');
    $('#formregistrarCategoria .invalid-feedback').hide();

});

$('#modalregistrarFrecuencia').on('hidden.bs.modal', function () {
 
    $('#formregistrarFrecuancia')[0].reset();

   
    $('#formregistrarFrecuancia .is-invalid').removeClass('is-invalid');
    $('#formregistrarFrecuancia .invalid-feedback').hide();

});
