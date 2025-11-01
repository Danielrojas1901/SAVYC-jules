//Cuentas por cobrar x Cliente
$(document).ready(function() {

window.vueltoRegistrado = false;

    $('#detallemodal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        var codCliente = button.data('cliente');
        var nombre = button.data('nombre');
        var cedula = button.data('cedula');

        var modal = $(this);
        modal.find('.modal-body #nombreC').val(nombre);
        modal.find('.modal-body #cedulaC').val(cedula);

        console.log(nombre, cedula);
        $('#detalleBody').empty();

        $.ajax({
            url: 'index.php?pagina=cuentaspend',
            method: 'POST',
            data: { detallecuenta: codCliente },
            dataType: 'json',
            success: function(data) {
                if (data.length === 0) {
                    $('#detalleBody').append('<tr><td colspan="9" class="text-center">No hay ventas pendientes.</td></tr>');
                } else {
                    $.each(data, function(index, c) {
                        let fechaVencimiento = c.fecha_vencimiento || 'No disponible';

                        let botones = '<div class="btn-group">';

                        if (puedeAgregarPago) {
                            botones += '<button title="Agregar pago" class="btn btn-primary" data-target="#pagoModal" data-toggle="modal"' +
                                ' data-codventa="' + c.cod_venta + '"' +
                                ' data-totalv="' + c.total + '"' +
                                ' data-fecha="' + c.fecha + '"' +
                                ' data-saldopen="' + c.saldo_pendiente + '"' +
                                ' data-nombre="' + c.nombre + ' ' + c.apellido + '">' +
                                '<i class="fas fa-money-bill-wave"></i>' +
                                '</button>';
                        }

                        botones += '<button title="'+Title+'" '+Disabled+' class="btn btn-primary btn-factura"' +
                            ' data-codventa="' + c.cod_venta + '"' +
                            ' data-total="' + c.total + '"' +
                            ' data-fecha="' + c.fecha + '"' +
                            ' data-cliente="' + c.nombre + ' ' + c.apellido + '"' +
                            ' data-cedula="' + c.cedula_rif + '"' +
                            ' data-direccion="' + c.direccion + '"' +
                            ' data-telefono="' + c.telefono + '">' +
                            '<i class="fas fa-file-invoice"></i>' +
                            '</button>' +
                            '</div>';

                        $('#detalleBody').append(
                            '<tr>' +
                                '<td>' + c.cod_venta + '</td>' +
                                '<td>' + c.fecha + '</td>' +
                                '<td>' + c.total + '</td>' +
                                '<td>' + c.monto_pagado + '</td>' +
                                '<td>' + c.saldo_pendiente + '</td>' +
                                '<td>' + fechaVencimiento + '</td>' +
                                '<td><span class="badge bg-' + (c.dias_restantes < 3 ? 'danger' : 'success') + '">' + c.dias_restantes + ' días</span></td>' +
                                '<td><span class="badge bg-' + 
                                    ((c.estado === 'Vencido') ? 'danger' : 
                                    (c.estado === 'Pago parcial') ? 'warning' : 
                                    (c.estado === 'Pendiente') ? 'secondary' : 'primary') + '">' + c.estado + '</span></td>' +
                                '<td>' + botones + '</td>' +
                            '</tr>'
                        );
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar detalles:', error);
            }
        });
    });
});
// Abrir reporte PDF
$(document).on('click', '.btn-factura', function () {
    const datosFactura = {
        cod_venta: $(this).data('codventa'),
        total: $(this).data('total'),
        fecha: $(this).data('fecha'),
        cliente: $(this).data('cliente'),
        cedula: $(this).data('cedula'),
        direccion: $(this).data('direccion'),
        telefono: $(this).data('telefono')
    };

    // Crear el formulario
    const form = $('<form>', {
        action: 'index.php?pagina=factura',
        method: 'POST',
        target: '_blank'
    });

    // Agregar campos ocultos al formulario
    $.each(datosFactura, function (key, value) {
        form.append($('<input>', {
            type: 'hidden',
            name: key,
            value: value
        }));
    });

    // Agregar, enviar y eliminar el formulario
    $('body').append(form);
    form.submit();
    form.remove();
});

// PAGO RECIBIDO / CUENTAS POR COBRAR
// ========================================================================
$('#pagoModal').on('show.bs.modal', function (event) {
    var modal = $(this);
    modal.find('.monto-bs').val('');
    modal.find('.monto-divisa').val('');
    modal.find('.monto-bs-con').val('');
    modal.find('#monto_pagado').val('0.00');
    modal.find('#diferencia').val('0.00');
    modal.find('#vuelto').val('0.00');

    var button = $(event.relatedTarget);
    var codigo = button.data('codventa');
    var total = button.data('totalv');
    var fecha = button.data('fecha');
    var nombre = button.data('nombre');
    var saldoPendiente = button.data('saldopen');

    // Obtener la fecha y hora actual
    var now = new Date();
    var fecha = now.getFullYear() + '-' +
            String(now.getMonth() + 1).padStart(2, '0') + '-' +
            String(now.getDate()).padStart(2, '0');

    // Formatea la hora en el formato HH:MM:SS
    var hora=String(now.getHours()).padStart(2, '0') + ':' +
        String(now.getMinutes()).padStart(2, '0') + ':' +
        String(now.getSeconds()).padStart(2, '0');
    var fechaHora = fecha + ' ' + hora;

    // Modal
    var modal = $(this);
    modal.find('.modal-body #nro-venta').val(codigo);
    modal.find('.modal-body #total-pago').text(total+ 'Bs');
    modal.find('.modal-body #fecha_venta').val(fecha);
    modal.find('.modal-body #nombre_cliente').val(nombre);
    modal.find('.modal-body #fecha_pago').val(fechaHora);

    // Mostrar campo de saldo pendiente si aplica
    if (saldoPendiente !== undefined) {
        
    if (!isNaN(saldoPendiente) && saldoPendiente > 0) {
        $('#campo-saldo').show();
        $('#saldo_pendiente').text(saldoPendiente + ' Bs');
        let saldoNum = parseFloat(saldoPendiente);
        modal.find('.modal-body #monto_pagar').val(saldoNum.toFixed(2));
    } else {
        $('#campo-saldo').hide();
        $('#saldo_pendiente').val('');
        modal.find('.modal-body #monto_pagar').val(total);
    }
}
    //Reiniciar el estado del vuelto
    window.vueltoRegistrado = false;
    $('#btn-registrar-vuelto').text('Registrar Vuelto');
    $('#div-boton-vuelto').hide();
    $('#finalizarPagoBtn').prop('disabled', false);
});

// VUELTO DE PAGO RECIBIDO
$('#vueltoModal').on('show.bs.modal', function (event) {
    const button = $(event.relatedTarget); // Botón que activó el modal 
    // Obtén los datos que pasaste desde la función calcularTotalpago()
    const montoVuelto = button.data('monto-vuelto');
    // Ahora puedes usar montoVuelto y codVenta para llenar los campos
    $('#total-vuelto').text(montoVuelto.toFixed(2)+ 'Bs');
    $('#monto_pagarv').val(montoVuelto.toFixed(2));
    // Limpiar los campos de entrada de vuelto
    document.querySelectorAll('.monto-bsv').forEach(function(input) {
        input.value = '';
    });
    document.querySelectorAll('.monto-divisav').forEach(function(input) {
        console.log('paso por aqui');
        input.value = '';
    });
    // Deshabilitar el botón de registrar vuelto inicialmente
    $('#registrarVueltoBtn').prop('disabled', true);
    // Reiniciar el cálculo
    calcularTotalvueltoV();
});

$('#registrarVueltoBtn').on('click', function(e) {
    e.preventDefault(); 
    // Verificar que el vuelto sea exacto
    let diferencia = parseFloat(document.getElementById('diferenciav').value);
    if (Math.abs(diferencia) < 0.01) { // Considerar una pequeña tolerancia para errores de redondeo
        // Obtener los datos del formulario de vuelto
        let vueltoData = $('#vueltoForm').serialize();
        // Guardar los datos de vuelto en un campo oculto en el formulario principal
        let vueltoInput = document.createElement('input');
        vueltoInput.type = 'hidden';
        vueltoInput.name = 'vuelto_data';
        vueltoInput.value = vueltoData;
        document.getElementById('pagoForm').appendChild(vueltoInput);
        
        // Marcar que el vuelto ha sido registrado
        window.vueltoRegistrado = true;
        
        $('#vueltoModal').modal('hide');
        $('#finalizarPagoBtn').prop('disabled', false);
        Swal.fire({
            title: 'Vuelto registrado',
            text: 'El vuelto ha sido registrado correctamente.',
            icon: 'success',
            confirmButtonText: 'Aceptar'
        });
        $('#btn-registrar-vuelto').text('Editar Vuelto');
    } else {
        Swal.fire({
            title: 'Error',
            text: 'El vuelto debe ser exactamente igual al monto calculado.',
            icon: 'error',
            confirmButtonText: 'Ok'
        });
    }
});

//Ventas
function calcularTotalpagoV() {
    let totalBs = 0;
    document.querySelectorAll('.monto-bs:not(.monto-con)').forEach(function(input) {
        let montoBs = parseFloat(input.value) || 0;
        totalBs += montoBs; 
    });
    document.querySelectorAll('.monto-divisa').forEach(function(inputDivisa) {
        let index = inputDivisa.id.split('-').pop();  

        // Obtener el monto en divisa de la fila
        let montoDivisa = parseFloat(inputDivisa.value) || 0;
        // Obtener la tasa de conversión de la misma fila
        let tasaConversion = parseFloat(document.getElementById('tasa-conversion-' + index).value) || 1;
        // Calcular el monto en bolívares
        let montoConvertidoBs = montoDivisa * tasaConversion;
        document.getElementById('monto-bs-con-' + index).value = montoConvertidoBs.toFixed(2);
        totalBs += montoConvertidoBs;
    });

    document.getElementById('monto_pagado').value = totalBs.toFixed(2);
    let montoPagar = parseFloat(document.getElementById('monto_pagar').value) || 0;
    let diferencia = montoPagar - totalBs;
    document.getElementById('diferencia').value = diferencia.toFixed(2);

    window.vueltoRegistrado = false;

    if (diferencia < 0) {
        // Si la diferencia es negativa, muestra el div que contiene el botón
        $('#div-boton-vuelto').show();
        $('#btn-registrar-vuelto').data('monto-vuelto', Math.abs(diferencia));
        $('#finalizarPagoBtn').prop('disabled', true);
    } else {
        $('#div-boton-vuelto').hide();
        $('#btn-registrar-vuelto').removeData('monto-vuelto');
        $('#btn-registrar-vuelto').removeData('cod-venta');
        $('#finalizarPagoBtn').prop('disabled', false);
        window.vueltoRegistrado = false;
    }

    if (window.vueltoRegistrado && diferencia < 0) {
        Swal.fire({
            title: 'Atención',
            text: 'Has modificado el pago. Debes registrar el vuelto nuevamente.',
            icon: 'warning',
            confirmButtonText: 'Entendido'
        });
        window.vueltoRegistrado = false;
        $('#finalizarPagoBtn').prop('disabled', true);
    }
}

function calcularTotalvueltoV() {
    let totalBs = 0;
    // 1. Procesar las entradas que ya están en bolívares (sin conversión)
    document.querySelectorAll('.monto-bsv:not(.monto-conv)').forEach(function(input) {
        let montoBs = parseFloat(input.value) || 0;
        totalBs += montoBs;  // Sumar cada monto en bolívares directo
    });
    // 2. Procesar las entradas en divisas (convertirlas a bolívares)
    document.querySelectorAll('.monto-divisav').forEach(function(inputDivisa) {
        let index = inputDivisa.id.split('-').pop();  // Obtener el índice de la fila actual
        // Obtener el monto en divisa de la fila
        let montoDivisa = parseFloat(inputDivisa.value) || 0;
        // Obtener la tasa de conversión de la misma fila
        let tasaConversion = parseFloat(document.getElementById('tasa-conversionv-' + index).value) || 1;
        // Calcular el monto en bolívares
        let montoConvertidoBs = montoDivisa * tasaConversion;
        // Actualizar el campo de bolívares convertido en esa fila
        document.getElementById('monto-bs-con-v' + index).value = parseFloat(montoConvertidoBs.toFixed(2));
        // Sumar al total de bolívares
        totalBs += montoConvertidoBs;
    });
     //3. Mostrar el total en el campo "Monto Pagado"
    document.getElementById('vuelto_pagado').value = totalBs.toFixed(2);
    // 4. Calcular y mostrar la diferencia con el monto a pagar
    let montoPagar = parseFloat(document.getElementById('monto_pagarv').value) || 0;
    let diferencia = montoPagar - totalBs;
    document.getElementById('diferenciav').value = diferencia.toFixed(2);
    if (Math.abs(diferencia) < 0.01) { // Considerar una pequeña tolerancia para errores de redondeo
        $('#registrarVueltoBtn').prop('disabled', false);
    } else {
        $('#registrarVueltoBtn').prop('disabled', true);
    }
}

//PAGO EMITIDO - COMPRA
function calcularTotalpagoc() {
    let totalBs = 0;
    // 1. Procesar las entradas que ya están en bolívares (sin conversión)
    document.querySelectorAll('.monto-bsc:not(.monto-conc)').forEach(function(input) {
        let montoBs = parseFloat(input.value) || 0;
        totalBs += montoBs;  // Sumar cada monto en bolívares directo
        console.log('Monto en Bs:', montoBs);
    });
    // 2. Procesar las entradas en divisas (convertirlas a bolívares)
    document.querySelectorAll('.monto-divisac').forEach(function(inputDivisa) {
        let index = inputDivisa.id.split('-').pop();  // Obtener el índice de la fila actual
        let montoDivisa = parseFloat(inputDivisa.value) || 0;
        let tasaConversion = parseFloat(document.getElementById('tasa-conversionc-' + index).value) || 1;
        let montoConvertidoBs = montoDivisa * tasaConversion;
        document.getElementById('monto-bsc-con-' + index).value = montoConvertidoBs.toFixed(2);
        totalBs += montoConvertidoBs;
    });

    document.getElementById('monto_pagadoc').value = totalBs.toFixed(2);
    let montoPagar = parseFloat(document.getElementById('monto_pagarc').value) || 0;
    let diferencia = montoPagar - totalBs;
    document.getElementById('diferenciac').value = diferencia.toFixed(2);

    if (diferencia < 0) {
        vuelto = Math.abs(diferencia);
        document.getElementById('vueltoC').value = vuelto.toFixed(2);
        console.log("vueltoC: " + vuelto);
    }

    window.vueltoRegistrado = false;
    if (diferencia < 0) {
        $('#div-boton-vueltoc').show();
        $('#btn-registrar-vueltoc').data('monto-vueltoc', Math.abs(diferencia));
        $('#finalizarPagoBtnc').prop('disabled', true);
    } else {
        $('#div-boton-vueltoc').hide();
        $('#btn-registrar-vueltoc').removeData('monto-vueltoc');
        $('#btn-registrar-vueltoc').removeData('cod-venta');
        $('#finalizarPagoBtnc').prop('disabled', false);
        window.vueltoRegistrado = false;
    }

    if (window.vueltoRegistrado && diferencia < 0) {
        Swal.fire({
            title: 'Atención',
            text: 'Has modificado el pago. Debes registrar el vuelto nuevamente.',
            icon: 'warning',
            confirmButtonText: 'Entendido'
        });
        window.vueltoRegistrado = false;
        $('#finalizarPagoBtnc').prop('disabled', true);
    }
}

$('#vueltoModalc').on('show.bs.modal', function (event) {
    const button = $(event.relatedTarget); 
    const montoVuelto = button.data('monto-vueltoc');
    if (typeof montoVuelto !== 'undefined') {
    $('#total-vueltoc').text(montoVuelto.toFixed(2) + ' Bs');
    $('#monto_pagarvc').val(montoVuelto.toFixed(2));
} else {
    $('#total-vueltoc').text('0.00 Bs');
    $('#monto_pagarvc').val('0.00');
}
    document.querySelectorAll('.monto-bsvc').forEach(function(input) {
        input.value = '';
    });
    document.querySelectorAll('.monto-divisavc').forEach(function(input) {
        input.value = '';
    });
    
    $('#registrarVueltoBtnc').prop('disabled', true);
    
    calcularTotalvueltoc();
});

$(document).ready(function() {
    window.vueltoRegistrado = false;

    $('#registrarVueltoBtnc').on('click', function(e) {
        e.preventDefault(); 
        // Verificar que el vuelto sea exacto
        let diferencia = parseFloat(document.getElementById('diferenciavc').value);
        
        if (Math.abs(diferencia) < 0.01) { // Considerar una pequeña tolerancia para errores de redondeo
            let vueltoData = $('#vueltoFormc').serialize();
            // Guardar los datos de vuelto en un campo oculto en el formulario principal
            let vueltoInput = document.createElement('input');
            vueltoInput.type = 'hidden';
            vueltoInput.name = 'pagoV';
            vueltoInput.value = vueltoData;
            document.getElementById('pagoFormc').appendChild(vueltoInput);
            
            window.vueltoRegistrado = true;
            
            $('#vueltoModalc').modal('hide');
            $('#finalizarPagoBtnc').prop('disabled', false);
            Swal.fire({
                title: 'Vuelto registrado',
                text: 'El vuelto ha sido registrado correctamente.',
                icon: 'success',
                confirmButtonText: 'Aceptar'
            });
            $('#btn-registrar-vueltoc').text('Editar Vuelto');
        } else {
            Swal.fire({
                title: 'Error',
                text: 'El vuelto debe ser exactamente igual al monto calculado.',
                icon: 'error',
                confirmButtonText: 'Entendido'
            });
        }
    });

    $('#pagoFormc').on('submit', function(e) {
        let diferencia = parseFloat(document.getElementById('diferenciac').value);
        
        if (diferencia < 0 && !window.vueltoRegistrado) {
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
    $('.monto-bs, .monto-divisac').on('input', function() {
        calcularTotalpagoc();
    });
});

$('#pagoModalc').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var codigo = button.data('cod_compra');
    var total = button.data('total');
    var nombre = button.data('nombre');
    var saldoP = button.data('montop');
    var saldoPendiente=parseFloat(total)-parseFloat(saldoP)||0;
    
    var now = new Date();
    var fecha = now.getFullYear() + '-' +
            String(now.getMonth() + 1).padStart(2, '0') + '-' +
            String(now.getDate()).padStart(2, '0');

    var hora=String(now.getHours()).padStart(2, '0') + ':' +
        String(now.getMinutes()).padStart(2, '0') + ':' +
        String(now.getSeconds()).padStart(2, '0');
    var fechaHora = fecha + ' ' + hora;
    
    var modal = $(this);
    modal.find('.modal-body #nro-compra').val(codigo);
    modal.find('.modal-body #total-pagoc').text(total+ 'Bs');
    modal.find('.modal-body #total_compra').val(total);
    modal.find('.modal-body #r_social').val(nombre);
    modal.find('.modal-body #fecha_pagoc').val(fechaHora);

    if (saldoP>0) {
        $('#campo-saldoc').show();
        $('#saldo_pendientec').text(saldoPendiente.toFixed(2) + ' Bs');
        modal.find('.modal-body #monto_pagarc').val(saldoPendiente.toFixed(2));
    } else {
        $('#campo-saldoc').hide();
        $('#saldo_pendientec').val('');
        modal.find('.modal-body #monto_pagarc').val(total);
    }

    window.vueltoRegistrado = false;
    $('#btn-registrar-vueltoc').text('Registrar Vuelto');
    $('#div-boton-vueltoc').hide();
    $('#finalizarPagoBtnc').prop('disabled', false);
});


function calcularTotalvueltoc() {
    let totalBs = 0;

    document.querySelectorAll('.monto-bsvc:not(.monto-convc)').forEach(function(input) {
        let montoBs = parseFloat(input.value) || 0;
        totalBs += montoBs; 
    });

    document.querySelectorAll('.monto-divisavc').forEach(function(inputDivisa) {
        let index = inputDivisa.id.split('-').pop();  // Obtener el índice de la fila actual
        let montoDivisa = parseFloat(inputDivisa.value) || 0;
        let tasaConversion = parseFloat(document.getElementById('tasa-conversionvc-' + index).value) || 1;
        let montoConvertidoBs = montoDivisa * tasaConversion;
        document.getElementById('monto-bs-con-v' + index).value = parseFloat(montoConvertidoBs.toFixed(2));
        totalBs += montoConvertidoBs;
    });

    document.getElementById('vuelto_pagadoc').value = totalBs.toFixed(2);
    let montoPagar = parseFloat(document.getElementById('monto_pagarvc').value) || 0;
    let diferencia = montoPagar - totalBs;
    document.getElementById('diferenciavc').value = diferencia.toFixed(2);

    if (Math.abs(diferencia) < 0.01) { 
        $('#registrarVueltoBtnc').prop('disabled', false);
    } else {
        $('#registrarVueltoBtnc').prop('disabled', true);
    }
}

//PAGOS DE GASTOS
$('#pagoModalG').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var codigoG = button.data('cod_gasto');
    var total = button.data('totalg');
    var nombre = button.data('nombre');
    var saldog = button.data('montop');
    var pagopendiente = parseFloat(total) - parseFloat(saldog) || 0;

    console.log('codigoG ',codigoG,'-total ',total,'-nombre ',nombre,'-saldog ',saldog)
    console.log("pago pendiente: " + pagopendiente);
    // Obtener la fecha y hora actual
    var now = new Date();
    var fecha = now.getFullYear() + '-' +
        String(now.getMonth() + 1).padStart(2, '0') + '-' +
        String(now.getDate()).padStart(2, '0');

    // Formatea la hora en el formato HH:MM:SS
    var hora = String(now.getHours()).padStart(2, '0') + ':' +
        String(now.getMinutes()).padStart(2, '0') + ':' +
        String(now.getSeconds()).padStart(2, '0');
    var fechaHoraG = fecha + ' ' + hora;
    // Modal
    var modal = $(this);
    modal.find('.modal-body #cod_gasto').val(codigoG);
    modal.find('.modal-body #campo-saldog').val(saldog);
    modal.find('.modal-body #total-pagoG').text(total + 'Bs');
    modal.find('.modal-body #total_gasto').val(total);
    modal.find('.modal-body #descripcionG').val(nombre);
    modal.find('.modal-body #fecha_pagoG').val(fechaHoraG);

    // Mostrar campo de saldo pendiente si aplica
    if (saldog > 0) {
        $('#campo-saldoG').show();
        $('#saldo_pendienteG').text(pagopendiente.toFixed(2) + ' Bs');
        modal.find('.modal-body #monto_pagarG').val(pagopendiente.toFixed(2));
    } else {
        $('#campo-saldoG').hide();
        $('#saldo_pendienteG').val('');
        modal.find('.modal-body #monto_pagarG').val(total);
    }
    // Reiniciar el estado del vuelto
    window.vueltoRegistradoG = false;
    $('#btn-registrar-vueltoG').text('Registrar Vuelto');
    $('#div-boton-vueltoG').hide();
    $('#finalizarPagoG').prop('disabled', false);
});

function calcularTotalpagoG() {
    let totalBs = 0;
    // 1. Procesar las entradas que ya están en bolívares (sin conversión)
    document.querySelectorAll('.monto-bsG:not(.monto-conG)').forEach(function (input) {
        let montoBs = parseFloat(input.value) || 0;
        totalBs += montoBs;  // Sumar cada monto en bolívares directo
    });
    // 2. Procesar las entradas en divisas (convertirlas a bolívares)
    document.querySelectorAll('.monto-divisaG').forEach(function (inputDivisa) {
        let index = inputDivisa.id.split('-').pop(); 
        let montoDivisa = parseFloat(inputDivisa.value) || 0;
        // Obtener la tasa de conversión de la misma fila
        let tasaConversion = parseFloat(document.getElementById('tasa-conversionG-' + index).value) || 1;
        let montoConvertidoBs = montoDivisa * tasaConversion;
        document.getElementById('monto-bsG-con-' + index).value = montoConvertidoBs.toFixed(2);
        totalBs += montoConvertidoBs;
    });
    // 3. Mostrar el total en el campo "Monto Pagado"
    document.getElementById('monto_pagadoG').value = totalBs.toFixed(2);
    // 4. Calcular y mostrar la diferencia con el monto a pagar
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

    // Manejar el envío del formulario de pago
    $('#pagoFormG').on('submit', function (e) {
        let diferencia = parseFloat(document.getElementById('diferenciaG').value);
        if (diferencia < 0 && !window.vueltoRegistradoG) {
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

    // 1. Procesar las entradas que ya están en bolívares (sin conversión)
    document.querySelectorAll('.monto-bsvG:not(.monto-convG)').forEach(function (input) {
        let montoBs = parseFloat(input.value) || 0;
        totalBs += montoBs; 
    });

    // 2. Procesar las entradas en divisas (convertirlas a bolívares)
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

    if (Math.abs(diferencia) < 0.01) { // Considerar una pequeña tolerancia para errores de redondeo
        $('#registrarVueltoG').prop('disabled', false);
    } else {
        $('#registrarVueltoG').prop('disabled', true);
    }
}


