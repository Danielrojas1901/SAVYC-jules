
/* =======================
SCRIPT DE VENTA
============================= */
console.log('script de ventas');

var productoIndex = 1; 

// Función para agregar una nueva fila a la tabla
function crearfila(index) {
    return `
        <tr id="fila${index}">
            <td>
                <input type="text" class="form-control" id="codigoProducto${index}" name="productos[${index}][codigo]" placeholder="Código del producto" readonly required>
                <input type="hidden" class="form-control" id="tipoProducto${index}">
                <input type="hidden" class="form-control" id="costo${index}" name="productos[${index}][costo]">
            </td>
            <td>
                <div class="input-group">
                    <input type="text" class="form-control" id="nombreProducto${index}" name="productos[${index}][nombre]" placeholder="Nombre del producto" required>
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="button" onclick="mostrarProductos()">+</button>
                    </div>
                </div>
                <div id="lista-productos${index}" class="list-group" style="position: absolute; z-index: 1000;"></div>
            </td>
            <td>
                <input type="hidden" class="form-control" id="stockproducto${index}" step="0.001">
                <div class="input-group">
                    <input type="number" class="form-control" name="productos[${index}][cantidad]" id="cantidadProducto${index}" min="0" step="0.001" onchange="calcularTotal(${index})" oninput="calcularTotal(${index})" required>
                    <div class="invalid-feedback" style="display: none; position: absolute; top: 100%; margin-top: 2px; width: calc(100% - 2px); font-size: 0.875em; text-align: left;"></div>
                    <div class="input-group-append">
                        <span id="unidadm${index}" class="input-group-text" value=" "></span>
                    </div>
                </div>
            </td>
            <td>
                <div class="input-group">
                    <input type="text" class="form-control" id="precioProducto${index}" name="productos[${index}][precio]" placeholder="Precio" onchange="calcularTotal(${index})" readonly>
                    <div class="input-group-append">
                        <span class="input-group-text">Bs</span>
                    </div>
                </div>
            </td>
            <td>
                <div class="input-group">
                    <input type="text" class="form-control" id="total${index}" name="productos[${index}][total]" placeholder="Total" readonly>
                    <div class="input-group-append">
                        <span class="input-group-text">Bs</span>
                    </div>
                </div>
            </td>
            <td>
                <button type="button" class="btn-sm btn-danger" onclick="eliminarFila(${index})">&times;</button>
            </td>
        </tr>
    `;
    
}

function crearfilaVertical(index) {
    return `
        <div class="border rounded p-3 mb-3" id="fila${index}">
            <div class="form-group">
                <label>Código</label>
                <input type="text" class="form-control" id="codigoProducto${index}" name="productos[${index}][codigo]" readonly required>
                <input type="hidden" id="tipoProducto${index}">
                <input type="hidden" id="costo${index}" name="productos[${index}][costo]">
            </div>

            <div class="form-group">
                <label>Producto</label>
                <div class="input-group">
                    <input type="text" class="form-control" id="nombreProducto${index}" name="productos[${index}][nombre]" required>
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="button" onclick="mostrarProductos()">+</button>
                    </div>
                </div>
                <div id="lista-productos${index}" class="list-group" style="position: absolute; z-index: 1000;"></div>
            </div>

            <div class="form-group">
                <label>Cantidad</label>
                <div class="input-group">
                    <input type="number" class="form-control" name="productos[${index}][cantidad]" id="cantidadProducto${index}" min="0" step="0.001" onchange="calcularTotal(${index})" oninput="calcularTotal(${index})" required>
                    <div class="input-group-append">
                        <span id="unidadm${index}" class="input-group-text"></span>
                    </div>
                </div>
                <input type="hidden" id="stockproducto${index}">
            </div>

            <div class="form-group">
                <label>Precio</label>
                <div class="input-group">
                    <input type="text" class="form-control" id="precioProducto${index}" name="productos[${index}][precio]" onchange="calcularTotal(${index})" readonly>
                    <div class="input-group-append">
                        <span class="input-group-text">Bs</span>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>Total</label>
                <div class="input-group">
                    <input type="text" class="form-control" id="total${index}" name="productos[${index}][total]" readonly>
                    <div class="input-group-append">
                        <span class="input-group-text">Bs</span>
                    </div>
                </div>
            </div>

            <div class="form-group text-right">
                <button type="button" class="btn btn-sm btn-danger" onclick="eliminarFila(${index})">
                    <i class="fas fa-trash"></i> Eliminar
                </button>
            </div>
        </div>
    `;
}

function agregarFila() {
    let nuevaFila;

    if (window.innerWidth < 768) {
        nuevaFila = crearfilaVertical(productoIndex);
        $('#ventaProductosBodyMobile').append(nuevaFila);
    } else {
        nuevaFila = crearfila(productoIndex);
        $('#ventaProductosBody').append(nuevaFila);
    }

    productoIndex++;
}


/*function agregarFila() {
    var nuevaFila = crearfila(productoIndex);
    $('#ventaProductosBody').append(nuevaFila);
    productoIndex++; 
}*/

function inicializarFilas() {
    for (let i = 1; i <= 1; i++) {
        agregarFila();
    }
}

$(document).ready(function() {
    inicializarFilas(); 
});

function eliminarFila(index) {
    // Eliminar la fila del DOM usando el identificador de fila
    var fila = document.getElementById(`fila${index}`);
    if (fila) {
        fila.remove();
    }
    calcularTotal();
}

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

$(document).ready(function() {
    $(document).on('input', '[id^=cantidadProducto]', function() {
        var inputId = $(this).attr('id');
        var index = inputId.replace('cantidadProducto', ''); // Extrae el índice de la cantidad
        var cantidad = parseFloat($(this).val()) || 0;
        var negativo=$(this).val();
        var stock = parseFloat($('#stockproducto' + index).val()) || 0;

        if (cantidad > stock) {
            showError('#' + inputId, 'stock insuficiente');
        } else if(!/^(?!-)\d*\.?\d+$/.test(negativo) || cantidad==0){
            showError('#' + inputId, 'cantidad invalida');
        }else {
            hideError('#' + inputId);
        }
    });
    
    $('#cedula-rif').on('input', function() {
        var cedula_rif = $(this).val();
        if (cedula_rif.length > 12) {
            showError('#cedula-rif', 'debe contener maximo 12 números');
        } else if (!/^\d+$/.test(cedula_rif)) {
            showError('#cedula-rif', 'debe contener solo numeros');
        }else {
            hideError('#cedula-rif');
        }
    });
});


// Calcular el total de cada fila
function calcularTotal(index) {
    var cantidad = parseFloat($(`[name="productos[${index}][cantidad]"]`).val()) || 0;
    var precio = parseFloat($(`[name="productos[${index}][precio]"]`).val()) || 0;
    var total = cantidad * precio;
    $(`[name="productos[${index}][total]"]`).val(total.toFixed(2));
    actualizarResumen(); 
}


// Función para actualizar el resumen de venta
function actualizarResumen() { 
    var subtotal = 0;
    var exento = 0;
    var baseImponible = 0;
    var iva = 0;

    //recorrer todas las filas 
    for (var i = 1; i < productoIndex; i++) {
        var totalProducto = parseFloat($('#total' + i).val()) || 0;
        console.log('Total del producto ' + i + ':', totalProducto);
        if (isNaN(totalProducto)) {
            totalProducto = 0;
        }
        subtotal += totalProducto;
        var tipoProducto = parseFloat($('#tipoProducto' + i).val());
    if (tipoProducto === 1) {
            exento += totalProducto;
        } else if (tipoProducto === 2) {
            baseImponible += totalProducto;
        }
    }
    iva = baseImponible * 0.16;
    var totalGeneral = subtotal + iva;

    console.log('Subtotal:', subtotal);
    console.log('Exento:', exento);
    console.log('Base Imponible:', baseImponible);
    console.log('IVA:', iva);
    console.log('Total General:', totalGeneral);

    //asignacion de valores 
    $('#subtotal').text(subtotal.toFixed(2));
    $('#subtotal_i').val(subtotal.toFixed(2));
    $('#exento').text(exento.toFixed(2));
    $('#base-imponible').text(baseImponible.toFixed(2));
    $('#iva').text(iva.toFixed(2));
    $('#impuesto').val(iva.toFixed(2));
    $('#total-span').text(totalGeneral.toFixed(2));
    $('#total-general').val(totalGeneral.toFixed(2));
}


    $('#cedula-rif').on('blur keydown', function (e) {
        if (e.type === 'blur' || (e.type === 'keydown' && e.keyCode === 13)) {
            e.preventDefault(); 
            var buscar = $('#cedula-rif').val();
            $.post('index.php?pagina=clientes', { buscar }, function(response) {
                if(response !== false){
                    if (response['status'] != 0) {
                    var nombre = response['nombre'] + " " + response['apellido'];
                    var telefono = response['telefono'];
                    var apellido = response['apellido'];
                    var codigo = response['cod_cliente'];
                    var modal = $('#ventaModal');
                    modal.find('.modal-body #numero-cliente').val(telefono);
                    modal.find('.modal-body #nombre-cliente').val(nombre);
                    modal.find('.modal-body #cod_cliente').val(codigo);
                    modal.find('.modal-body [id^="nombreProducto"]').first().focus();
                    }else{
                        limpiarCamposCliente();
                        Swal.fire({
                            title: 'Error',
                            text: 'El cliente se encuentra inactivo.',
                            icon: 'error',
                            confirmButtonText: 'Ok'
                        });
                    }
                }else{
                    limpiarCamposCliente();
                    Swal.fire({
                        title: 'Error',
                        text: 'Cliente no encontrado. Por favor, registra al cliente.',
                        icon: 'error',
                        confirmButtonText: 'Ok'
                    }).then(() => {
                            $('#modalRegistrarClientes').modal('show');
                    });
                }
            }, 'json');
        }
    });

    function limpiarCamposCliente() {
        var modal = $('#ventaModal');
        modal.find('.modal-body #numero-cliente').val('');
        modal.find('.modal-body #nombre-cliente').val('');
        modal.find('.modal-body #cod_cliente').val('');
    }


    $(document).ready(function() {
    $(document).on('input', '[id^=nombreProducto]', function() {
        var inputId = $(this).attr('id');
        var index = inputId.replace('nombreProducto', '');
        var query = $(this).val();

        if (query.length > 2) {
            $.ajax({
                url: 'index.php?pagina=venta',
                method: 'POST',
                data: {buscar: query},
                dataType: 'json', 
                success: function(data) {
                    var listaProductos = $('#lista-productos' + index);
                    listaProductos.empty(); 

                    if (data.length > 0) {
                        $.each(data, function(key, producto) {
                            if(producto.total_stock>0){
                            var costo = parseFloat(producto.costo);
                            var precioVenta = costo + (costo * producto.porcen_venta / 100);
                            listaProductos.append(
                                '<a href="#" class="list-group-item list-group-item-action producto-item" ' +
                                'data-nombre="'+producto.producto_nombre+'" ' +
                                'data-tipo="'+producto.excento+'" ' +
                                'data-codigo="'+producto.cod_presentacion+'" ' +
                                'data-marca="'+producto.marca+'" ' +
                                'data-stock="'+producto.total_stock+'" ' +
                                'data-unidad="'+producto.tipo_medida+'" ' +
                                'data-costo="'+costo+'"' +
                                'data-precio="'+precioVenta+'">' +
                                producto.producto_nombre + ' - ' + (producto.marca ?? 'sin marca') + ' - ' + (producto.presentacion ? producto.presentacion + ' - ' : '') +precioVenta.toFixed(2) +'.Bs'+' - '+producto.total_stock+producto.tipo_medida+ '</a>'
                            );
                            }
                        });
                        listaProductos.fadeIn();
                    } else {
                        listaProductos.append('<p class="list-group-item">No se encontraron productos</p>');
                    }
                }
            });
        } else {
            $('#lista-productos' + index).fadeOut();
        }
    });
    // Cuando el usuario selecciona un producto
    $(document).on('click', '.producto-item', function(){
        var selectedProduct = $(this).data('nombre'); 
        var codigo = $(this).data('codigo'); 
        var precio = $(this).data('precio'); 
        var tipo = $(this).data('tipo');
        var unidad = $(this).data('unidad');
        var stock=$(this).data('stock');
        var costo=$(this).data('costo');
        var cant=1;


        var inputId = $(this).closest('.list-group').attr('id'); 
        var index = inputId.replace('lista-productos', ''); 

        $('#nombreProducto' + index).val(selectedProduct); 
        $('#codigoProducto' + index).val(codigo); 
        $('#tipoProducto' + index).val(tipo);
        $('#costo' + index).val(costo);
        $('#precioProducto' + index).val(precio.toFixed(2));
        $('#stockproducto'+index).val(stock);
        $('#unidadm'+index).text(unidad);
        $('#cantidadProducto' + index).val(cant).trigger('change');
        $(this).closest('.list-group').fadeOut(); 
    });
});

$(document).ready(function() {
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
    $('#fecha-hora').val(fechaHora);
    $('#fecha_vencimiento').val(fecha);
    
    //$('#fecha_pago').val(fechaHora); 
});

$(function() {
    var Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000
    });

$(document).ready(function() {
    $('#ventamodal').on('submit', function(event) {
        event.preventDefault();
        var datosVenta = $(this).serialize();
        console.log('form:', datosVenta);

        $.ajax({
            type: 'POST',
            url: 'index.php?pagina=venta',
            data: datosVenta,
            dataType: 'json',
            success: function(response) {
                if (response.success===true) {
                    $('#ventaModal').modal('hide');
                    console.log('cod:', response.cod_venta);
                    console.log('total:', response.total);
                    var condicion = $('#condicion_pago').val();
                    if (condicion === "1") {
                        Toast.fire({
                            icon: 'success',
                            title: response.message
                        })
                        $('#pagoModal').modal('show');
                        $('#nro-venta').val(response.cod_venta);
                        $('#nombre_cliente').val(response.cliente); 
                        $('#fecha_venta').val(response.fecha);
                        $('#total-pago').text(response.total+ ' Bs');
                        $('#monto_pagar').val(response.total);
                    }else{
                        localStorage.setItem('ventaSuccessMessage', response.message);
                        window.location.reload();
                    }
                } else {
                    Swal.fire({
                        title: 'Error al registrar la venta',
                        text: response.message,
                        icon: 'error',
                        confirmButtonText: 'Ok'
                    });
                }
            },
            error: function(jqXHR, xhr, status, error) {
                alert('Hubo un problema al registrar la venta. Inténtalo de nuevo.');
            }
        });
    });
});

});

$(document).ready(function() {
    // Verificar si hay un mensaje de éxito almacenado
    const successMessage = localStorage.getItem('ventaSuccessMessage');
    if (successMessage) {
        // Mostrar la alerta
        Swal.fire({
            title: 'Venta Registrada',
            text: successMessage,
            icon: 'success',
            confirmButtonText: 'Ok'
        });

        // Limpiar el mensaje de localStorage
        localStorage.removeItem('ventaSuccessMessage');
    }
});


function calcularTotalpago() {

    let totalBs = 0;
    // 1. Procesar las entradas que ya están en bolívares (sin conversión)
    document.querySelectorAll('.monto-bs:not(.monto-con)').forEach(function(input) {
        let montoBs = parseFloat(input.value) || 0;
        totalBs += montoBs;  // Sumar cada monto en bolívares directo
    });
    // 2. Procesar las entradas en divisas (convertirlas a bolívares)
    document.querySelectorAll('.monto-divisa').forEach(function(inputDivisa) {
        let index = inputDivisa.id.split('-').pop();  // Obtener el índice de la fila actual

        // Obtener el monto en divisa de la fila
        let montoDivisa = parseFloat(inputDivisa.value) || 0;

        // Obtener la tasa de conversión de la misma fila
        let tasaConversion = parseFloat(document.getElementById('tasa-conversion-' + index).value) || 1;

        // Calcular el monto en bolívares
        let montoConvertidoBs = montoDivisa * tasaConversion;

        // Actualizar el campo de bolívares convertido en esa fila
        document.getElementById('monto-bs-con-' + index).value = montoConvertidoBs.toFixed(2);

        // Sumar al total de bolívares
        totalBs += montoConvertidoBs;
    });

    // 3. Mostrar el total en el campo "Monto Pagado"
    document.getElementById('monto_pagado').value = totalBs.toFixed(2);

    // 4. Calcular y mostrar la diferencia con el monto a pagar
    let montoPagar = parseFloat(document.getElementById('monto_pagar').value) || 0;
    let diferencia = montoPagar - totalBs;
    document.getElementById('diferencia').value = diferencia.toFixed(2);
    window.vueltoRegistrado = false;
    if (diferencia < 0) {
        // Si la diferencia es negativa, muestra el div que contiene el botón
        $('#div-boton-vuelto').show();

        // Opcional: Puedes pasar datos al botón para usarlos al abrir el modal de vuelto
        // Por ejemplo, el monto del vuelto (la diferencia en positivo)
        $('#btn-registrar-vuelto').data('monto-vuelto', Math.abs(diferencia));

        // Desactivar el botón de finalizar pago hasta que se registre el vuelto
        $('#finalizarPagoBtn').prop('disabled', true);
    } else {
        // Si la diferencia no es negativa, oculta el div del botón
        $('#div-boton-vuelto').hide();
        // Limpia los datos que pudieras haber adjuntado al botón
        $('#btn-registrar-vuelto').removeData('monto-vuelto');
        $('#btn-registrar-vuelto').removeData('cod-venta');
        // Activar botón de finalizar pago
        $('#finalizarPagoBtn').prop('disabled', false);
        // Resetear la variable de vuelto registrado
        window.vueltoRegistrado = false;
    }

    if (window.vueltoRegistrado && diferencia < 0) {
        // Mostrar alerta de que debe registrar el vuelto nuevamente
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

$('#vueltoModal').on('show.bs.modal', function (event) {
    const button = $(event.relatedTarget); // Botón que activó el modal (será #btn-registrar-vuelto)
    // Validar si tiene el atributo data-monto-vuelto
    const montoRaw = button.data('monto-vuelto');
    const montoVuelto = (montoRaw != null && !isNaN(montoRaw)) ? Number(montoRaw) : 0;
    // Ahora puedes usar montoVuelto y codVenta para llenar los campos
    // del modal de vuelto o hacer lo que necesites con ellos.
    console.log('Monto del vuelto:', montoVuelto);
    // Ejemplo: si tu modal de vuelto tiene un input para el monto del vuelto con id="input-monto-vuelto"
    $('#total-vuelto').text(montoVuelto.toFixed(2)+ 'Bs');
    $('#monto_pagarv').val(montoVuelto.toFixed(2));
    // Y si tiene un input oculto para el código de venta con id="input-cod-venta-vuelto"
    // $('#input-cod-venta-vuelto').val(codVenta);
    // Limpiar los campos de entrada de vuelto
    document.querySelectorAll('.monto-bsv').forEach(function(input) {
        input.value = '';
    });
    document.querySelectorAll('.monto-divisav').forEach(function(input) {
        input.value = '';
    });
    
    // Deshabilitar el botón de registrar vuelto inicialmente
    $('#registrarVueltoBtn').prop('disabled', true);
    
    // Reiniciar el cálculo
    calcularTotalvuelto();
});

$(document).ready(function() {
    window.vueltoRegistrado = false;
    $('#pagoModal').on('hidden.bs.modal', function () {
        // Recargar la página cuando el modal se cierra
        location.reload();
    });

    // Manejar el clic en el botón de registrar vuelto
    $('#registrarVueltoBtn').on('click', function(e) {
        e.preventDefault(); // Prevenir el envío del formulario
        
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
            
            // Cerrar el modal de vuelto
            $('#vueltoModal').modal('hide');
            
            // Habilitar el botón de finalizar pago
            $('#finalizarPagoBtn').prop('disabled', false);
            
            // Mostrar confirmación
            Swal.fire({
                title: 'Vuelto registrado',
                text: 'El vuelto ha sido registrado correctamente.',
                icon: 'success',
                confirmButtonText: 'Aceptar'
            });
            
            // Cambiar el texto del botón de vuelto
            $('#btn-registrar-vuelto').text('Editar Vuelto');
        } else {
            // Mostrar error
            Swal.fire({
                title: 'Error',
                text: 'El vuelto debe ser exactamente igual al monto calculado.',
                icon: 'error',
                confirmButtonText: 'Entendido'
            });
        }
    });

    // Manejar el envío del formulario de pago
    $('#pagoForm').on('submit', function(e) {
        let diferencia = parseFloat(document.getElementById('diferencia').value);
        
        // Si hay que dar vuelto pero no se ha registrado
        if (diferencia < 0 && !window.vueltoRegistrado) {
            e.preventDefault(); // Prevenir el envío del formulario
            
            // Mostrar mensaje de error
            Swal.fire({
                title: 'Error',
                text: 'Debes registrar el vuelto antes de finalizar el pago.',
                icon: 'error',
                confirmButtonText: 'Entendido'
            });
            
            return false;
        }
        
        // Continuar con el envío normal si todo está bien
        return true;
    });
    
    // Monitorear cambios en los campos de pago para detectar modificaciones después de registrar el vuelto
    $('.monto-bs, .monto-divisa').on('input', function() {
        calcularTotalpago();
    });
});

$('#pagoModal').on('show.bs.modal', function (event) {
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
        $('#campo-saldo').show();
        $('#saldo_pendiente').text(saldoPendiente + ' Bs');
        let saldoNum = parseFloat(saldoPendiente);
        modal.find('.modal-body #monto_pagar').val(saldoNum.toFixed(2));
    } else {
        $('#campo-saldo').hide();
        $('#saldo_pendiente').val('');
        modal.find('.modal-body #monto_pagar').val(total);
    }

     // Reiniciar el estado del vuelto
    window.vueltoRegistrado = false;
    $('#btn-registrar-vuelto').text('Registrar Vuelto');
    $('#div-boton-vuelto').hide();
    $('#finalizarPagoBtn').prop('disabled', false);
});

$('#abonoModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var codigo = button.data('codventa');
    var codp=button.data('codpago');
    var total = button.data('totalv');
    var monto=button.data('montop');
    var fecha = button.data('fecha');
    var nombre = button.data('nombre');
    var mpagar=total-monto;
    console.log(button.data('codventa'));
    // Modal
    var modal = $(this);
    modal.find('.modal-body #nro-venta1').val(codigo);
    modal.find('.modal-body #monto_pagar1').val(mpagar.toFixed(2));
    modal.find('.modal-body #total-venta1').text(total+ 'Bs');
    modal.find('.modal-body #t-parcial').val(mpagar);
    modal.find('.modal-body #total-pago1').text(mpagar.toFixed(2)+ 'Bs');
    modal.find('.modal-body #fecha_venta1').val(fecha);
    modal.find('.modal-body #nombre_cliente1').val(nombre);
    modal.find('.modal-body #codigop').val(codp);

});

function calcularTotalpago1() {
    let totalBs = 0;

    // 1. Procesar las entradas que ya están en bolívares (sin conversión)
    document.querySelectorAll('.monto-bs1:not(.monto-con1)').forEach(function(input) {
        let montoBs = parseFloat(input.value) || 0;
        totalBs += montoBs;  // Sumar cada monto en bolívares directo
    });

    // 2. Procesar las entradas en divisas (convertirlas a bolívares)
    document.querySelectorAll('.monto-divisa1').forEach(function(inputDivisa) {
        let index = inputDivisa.id.split('-').pop();  // Obtener el índice de la fila actual

        // Obtener el monto en divisa de la fila
        let montoDivisa = parseFloat(inputDivisa.value) || 0;

        // Obtener la tasa de conversión de la misma fila
        let tasaConversion = parseFloat(document.getElementById('tasa-conversion1-' + index).value) || 1;

        // Calcular el monto en bolívares
        let montoConvertidoBs = montoDivisa * tasaConversion;

        // Actualizar el campo de bolívares convertido en esa fila
        document.getElementById('monto-bs-con-1' + index).value = parseFloat(montoConvertidoBs.toFixed(2));

        // Sumar al total de bolívares
        totalBs += montoConvertidoBs;
    });

    // 3. Mostrar el total en el campo "Monto Pagado"
    document.getElementById('monto_pagado1').value = totalBs.toFixed(2);

    // 4. Calcular y mostrar la diferencia con el monto a pagar
    let montoPagar = parseFloat(document.getElementById('monto_pagar1').value) || 0;
    let diferencia = montoPagar - totalBs;
    document.getElementById('diferencia1').value = diferencia.toFixed(2);
}


$('#anularventa').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var codigo = button.data('codventa');
    var status=button.data('status');
    // Modal
    var modal = $(this);
    modal.find('.modal-body #cventa').val(codigo);
    modal.find('.modal-body #statusv').val(status);
    modal.find('.modal-body #codv').text(codigo);
});

function calcularTotalvuelto() {
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
        console.log(inputDivisa.value);
        // Obtener la tasa de conversión de la misma fila
        let tasaConversion = parseFloat(document.getElementById('tasa-conversionv-' + index).value) || 1;
        // Calcular el monto en bolívares
        let montoConvertidoBs = montoDivisa * tasaConversion;
        console.log(montoDivisa);
        console.log(tasaConversion);
        console.log(montoConvertidoBs);
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

$('#ventaModal').on('show.bs.modal', function() { console.log('Abriendo ventaModal'); });
$('#pagoModal').on('show.bs.modal', function() { console.log('Abriendo pagoModal'); });
$('#vueltoModal').on('show.bs.modal', function() { console.log('Abriendo vueltoModal'); });
$('#modalRegistrarClientes').on('show.bs.modal', function() { console.log('Abriendo modalRegistrarClientes'); });



