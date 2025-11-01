
$('#rif-r').on('blur keydown', function (e) {
    if (e.type === 'blur' || (e.type === 'keydown' && e.keyCode === 13)) {
        e.preventDefault();
        var buscar = $('#rif-r').val();
        
        $.post('index.php?pagina=proveedores', { buscar: buscar }, function (response) {
            if (response) {
                var razon_social = response['razon_social'];
                var codigo = response['cod_prov'];
                var telefono = response['telefono'];

                var modal = $('#modalcom');
                modal.find('.modal-body #razon-social').val(razon_social);
                modal.find('.modal-body #cod_prov').val(codigo);
                if (telefono) {
                    modal.find('.modal-body #telefono').val(telefono);
                } else {
                    modal.find('.modal-body #telefono').val('');
                }
                modal.find('.modal-body [id^="nombreProducto"]').first().focus();
            } else {
                limpiarCamposProv();
                Swal.fire({
                    title: 'Error',
                    text: 'Proveedor no encontrado. Por favor, verifica el rif o la cedula.',
                    icon: 'error',
                    confirmButtonText: 'Ok'
                });
            }
        }, 'json');
    }
});

function limpiarCamposProv() {
    var modal = $('#modalcom');
    modal.find('.modal-body #razon-social').val('');
    modal.find('.modal-body #cod_prov').val('');
    modal.find('.modal-body #telefono').val('');
}


var productoIndex = 1;


function crearfila(index) {
    return `
        <tr id="fila${index}">
            <td>
                <input type="text" class="form-control" name="productos[${index}][cod_presentacion]" id="codigoProducto${index}" placeholder="Código de la presentacion" required readonly>
            </td>
            <td>
                <div class="input-group">
                    <input type="text" class="form-control" name="productos[${index}][nombre_producto]" id="nombreProducto${index}" placeholder="Nombre del producto" required>
                    
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="button" data-toggle="modal" data-target="#modalRegistrarProducto">+</button>
                    </div>
                </div>
                <div id="lista${index}" class="list-group" style="position: absolute; z-index: 1000;"></div>
            </td>
            <td>
                <div class="input-group">
                    <input type="date" id="fecha-v${index}" class="form-control" name="productos[${index}][fecha_v]" placeholder="Fecha" onchange="validarfecha(${index})" min="<?= date('Y-m-d'); ?>">
                    <div class="invalid-feedback" style="display: none; position: absolute; top: 100%; margin-top: 2px; width: calc(100% - 2px); font-size: 0.875em; text-align: left;"></div>
                </div>
            </td>
            <td>
                <input type="hidden" id="cod-dp${index}" class="form-control" name="productos[${index}][cod-dp]">
                <input type="text" id="lotes${index}" class="form-control" name="productos[${index}][lote]" placeholder="Lote">
                <div id="lista-lotes${index}" class="list-group" style="position: absolute; z-index: 1000;"></div>
            </td>
            <td>
                <div class="input-group">
                    <input type="number" class="form-control" name="productos[${index}][cantidad]" value="1" step="0.001" oninput="calcularMontos(${index})" required>
                    <div class="input-group-append">
                        <span id="unidadm${index}" class="input-group-text" value=" "></span>
                    </div>
                </div>
            </td>
            <td class="col-divisa" style="display: none;">
                <input type="number" step="0.001" class="form-control precio-divisa" placeholder="Precio en divisa" id="precio_divisa${index}" oninput="calcularMontos(${index})">
            </td>
            <td>
                <input type="number" class="form-control" step="0.001" name="productos[${index}][precio]" placeholder="Precio" oninput="calcularMontos(${index})" required>
            </td>
            <td>
                <select class="form-control" id="tipoProducto${index}" name="productos[${index}][iva]" onchange="calcularMontos(${index})" required>
                <option value="1"> E </option>
                <option value="2"> G </option>
                </select>
            </td>
            <td><input type="number" class="form-control" id="total${index}" name="productos[${index}][total]" placeholder="Total" readonly required></td>
            <td><button type="button" class="btn btn-sm btn-danger" title="eliminar fila" onclick="eliminarFila(${index})">&times;</button></td>
        </tr>
    `;
}

function crearfilaVertical(index) {
    return `
    <div class="border rounded p-3 mb-3" id="fila${index}">
        <div class="form-group">
            <label>Código de presentación</label>
            <input type="text" class="form-control" name="productos[${index}][cod_presentacion]" id="codigoProducto${index}" readonly required>
        </div>

        <div class="form-group">
            <label>Producto</label>
            <div class="input-group">
                <input type="text" class="form-control" name="productos[${index}][nombre_producto]" id="nombreProducto${index}" required>
                <div class="input-group-append">
                    <button class="btn btn-outline-secondary" type="button" data-toggle="modal" data-target="#modalRegistrarProducto">+</button>
                </div>
            </div>
            <div id="lista${index}" class="list-group" style="position: absolute; z-index: 1000;"></div>
        </div>

        <div class="form-group">
            <label>Fecha de vencimiento</label>
            <input type="date" class="form-control" id="fecha-v${index}" name="productos[${index}][fecha_v]" onchange="validarfecha(${index})" min="<?= date('Y-m-d'); ?>">
        </div>

        <div class="form-group">
            <label>Lote</label>
            <input type="hidden" id="cod-dp${index}" name="productos[${index}][cod-dp]">
            <input type="text" class="form-control" id="lotes${index}" name="productos[${index}][lote]" placeholder="Lote">
            <div id="lista-lotes${index}" class="list-group" style="position: absolute; z-index: 1000;"></div>
        </div>

        <div class="form-group">
            <label>Cantidad</label>
            <div class="input-group">
                <input type="number" class="form-control" name="productos[${index}][cantidad]" value="1" step="0.001" oninput="calcularMontos(${index})" required>
                <div class="input-group-append">
                    <span id="unidadm${index}" class="input-group-text"></span>
                </div>
            </div>
        </div>

        <div class="form-group col-divisa" style="display: none;">
            <label>Precio en divisa</label>
            <input type="number" step="0.001" class="form-control precio-divisa" id="precio_divisa${index}" oninput="calcularMontos(${index})">
        </div>

        <div class="form-group">
            <label>Precio (Bs)</label>
            <input type="number" class="form-control" step="0.001" name="productos[${index}][precio]" oninput="calcularMontos(${index})" required>
        </div>

        <div class="form-group">
            <label>IVA</label>
            <select class="form-control" id="tipoProducto${index}" name="productos[${index}][iva]" onchange="calcularMontos(${index})" required>
                <option value="1">E</option>
                <option value="2">G</option>
            </select>
        </div>

        <div class="form-group">
            <label>Total</label>
            <input type="number" class="form-control" id="total${index}" name="productos[${index}][total]" readonly required>
        </div>

        <div class="form-group text-right">
            <button type="button" class="btn btn-sm btn-danger" title="Eliminar fila" onclick="eliminarFila(${index})">
                <i class="fas fa-trash"></i> Eliminar
            </button>
        </div>
    </div>
    `;
}

// Función para agregar una nueva fila en compras (responsive)
function agregarFila() {
    const abreviatura = $('#selectDivisa').find('option:selected').data('abreviatura');
    const tasa = $('#selectDivisa').find('option:selected').data('tasa');
    const cod = $('#selectDivisa').find('option:selected').data('cod');

    let nuevaFila;

    if (window.innerWidth < 768) {
        nuevaFila = crearfilaVertical(productoIndex);
        $('#ProductosBodyMobile').append(nuevaFila);
    } else {
        nuevaFila = crearfila(productoIndex);
        $('#ProductosBody').append(nuevaFila);
    }

    // Mostrar u ocultar campos de divisa según la moneda seleccionada
    if (cod != 1) {
        $('#fila' + productoIndex + ' .col-divisa').show();
        $('#fila' + productoIndex + ' .precio-divisa')
            .show()
            .attr('data-tasa', tasa);
    } else {
        $('#fila' + productoIndex + ' .col-divisa').hide();
        $('#fila' + productoIndex + ' .precio-divisa').hide();
    }

    productoIndex++;
}


/*Función para agregar una nueva fila
function agregarFila() {
    var abreviatura = $('#selectDivisa').find('option:selected').data('abreviatura');
    var tasa = $('#selectDivisa').find('option:selected').data('tasa');
    var cod = $('#selectDivisa').find('option:selected').data('cod');
    var nuevaFila = crearfila(productoIndex);

    $('#ProductosBody').append(nuevaFila);
    if (cod != 1) {
        $('#fila' + productoIndex + ' .col-divisa').show();
        $('#fila' + productoIndex + ' .precio-divisa').show().attr('data-tasa', tasa);
    } else {
        $('#fila' + productoIndex + ' .col-divisa').hide();
        $('#fila' + productoIndex + ' .precio-divisa').hide();
    }
    productoIndex++;
}*/


function inicializarFilas() {
    agregarFila(); 
}

$(document).ready(function () {
    inicializarFilas();
});

function eliminarFila(index) {
   
    var fila = document.getElementById(`fila${index}`);
    if (fila) {
        fila.remove();
    }
    calcularMontos();
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

$(document).ready(function () {
    
    $('#selectDivisa').on('change', function () {
        var selectedOption = $(this).find('option:selected');
        var tasa = parseFloat(selectedOption.data('tasa'));
        var abreviatura = selectedOption.data('abreviatura');
        var cod = selectedOption.data('cod') || 1;
        console.log(cod);

        if (cod != 1) {
           
            $('.col-divisa').show();
            $('#labelDivisa').text(abreviatura);
            $('#ProductosBody .precio-divisa').show().attr('data-tasa', tasa);
        } else {
           
            $('.col-divisa').hide();
            $('#ProductosBody .precio-divisa').hide();
        }
    });

    $(document).on('input', '[id^=precio_divisa]', function () {
        var inputId = $(this).attr('id');
        var index = inputId.replace('precio_divisa', '');  
        var tasa = parseFloat($(this).attr('data-tasa')); 
        var precioDivisa = parseFloat($(this).val()) || 0; 
        var precioBs = (precioDivisa * tasa).toFixed(2);  

       
        $(`[name="productos[${index}][precio]"]`).val(precioBs);

       
        calcularMontos(index);
    });

    $('#rif-r').on('input', function () {
        var cedula_rif = $(this).val();
        if (cedula_rif.length > 1) {
            if (cedula_rif.length > 12) {
                showError('#rif-r', 'debe contener maximo 12 números');
            } else if (!/^[VJEvje]\d+$/.test(cedula_rif)) {
                showError('#rif-r', 'debe comenzar con "J", "V" , "E" Y luego numeros');
            } else {
                hideError('#rif-r');
            }
        }
    });
});

function validarfecha(index) {
    var fecha = new Date($(`[name="productos[${index}][fecha_v]"]`).val());
    var actual = new Date();
    actual.setHours(0, 0, 0, 0);
    if (fecha <= actual) {
        showError(`#fecha-v${index}`, 'La fecha debe ser futura');
    } else {
        hideError(`#fecha-v${index}`);
    }

}


function calcularMontos(index) {
    var cantidad = parseFloat($(`[name="productos[${index}][cantidad]"]`).val()) || 0;
    var precio = parseFloat($(`[name="productos[${index}][precio]"]`).val()) || 0;
    var total = cantidad * precio;
    $(`[name="productos[${index}][total]"]`).val(total.toFixed(2));
    actualizarResumen();
}

function actualizarResumen() {
    var subtotal = 0;
    var exento = 0;
    var baseImponible = 0;
    var iva = 0;

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


    $('#subtotal').text(subtotal.toFixed(2));
    $('#exento').text(exento.toFixed(2));
    $('#iva').text(iva.toFixed(2));
    $('#total-span').text(totalGeneral.toFixed(2));
    $('#total-general').val(totalGeneral.toFixed(2));
    $('#base-imponible').text(baseImponible.toFixed(2));
    $('#subt').val(subtotal.toFixed(2));
    $('#impuesto_total').val(iva.toFixed(2));

 
}

$(document).ready(function () {

    $('#ProductosBody').on('input', 'input[name^="productos"][name$="[total]"]', function () {
        actualizarResumen();
    });
});

$(document).on('input', 'input[name^="productos"][name$="[cantidad]"], input[name^="productos"][name$="[precio]"]', function () {
    
    var name = $(this).attr('name');
    var index = name.match(/\[([0-9]+)\]/)[1]; 
    calcularMontos(index);
});


$(document).ready(function () {
   
    $(document).on('input', '[id^=nombreProducto]', function () {
        var inputId = $(this).attr('id');
        var index = inputId.replace('nombreProducto', ''); 
        var query = $(this).val(); 
        console.log(query);

        if (query.length > 2) {
            $.ajax({
                url: 'index.php?pagina=compras',
                method: 'POST',
                data: { buscar: query },
                dataType: 'json',
                success: function (data) {
                    console.log("Enviando búsqueda con valor:", query);
                    console.log(data);
                    var listaProductos = $('#lista' + index);
                    listaProductos.empty(); 

                    if (data.length > 0) {
                        console.log("entra en el condicional");
                        
                        $.each(data, function (key, producto) {
                            console.log(producto);
                            console.log(listaProductos);
                            listaProductos.append(
                                '<a href="#" class="list-group-item list-group-item-action producto-item" ' +
                                'data-nombre="' + producto.producto_nombre + '" ' +
                                'data-tipo="' + producto.excento + '" ' +
                                'data-codigo="' + producto.cod_presentacion + '" ' +
                                'data-unidad="' + producto.tipo_medida + '" ' +
                                'data-marca="' + producto.marca + '">' +
                                producto.producto_nombre + ' - ' + (producto.marca ?? 'sin marca') + (producto.presentacion ? ' - ' + producto.presentacion : '') + ' </a>'
                            );
                        });
                        listaProductos.fadeIn();
                    } else {
                        listaProductos.fadeOut();
                    }
                }
            });
        } else {
            $('#lista' + index).fadeOut();
        }
    });


    $(document).on('click', '.producto-item', function () {
        var selectedProduct = $(this).data('nombre');
        var codigo = $(this).data('codigo');
        var tipo = $(this).data('tipo');
        var unidad = $(this).data('unidad');
        var cant = 1;

       
        var inputId = $(this).closest('.list-group').attr('id'); 
        var index = inputId.replace('lista', ''); 

       
        $('#nombreProducto' + index).val(selectedProduct);
        $('#codigoProducto' + index).val(codigo);
        $('#tipoProducto' + index).val(tipo);
        $('#unidadm' + index).text(unidad);
        $(this).closest('.list-group').fadeOut();
    });
});


$(document).ready(function () {
    
    $(document).on('input', '[id^=lotes]', function () {
        var inputId = $(this).attr('id');
        var index = inputId.replace('lotes', ''); 
        var query = $(this).val(); 
        var cod_detalle = $('#codigoProducto' + index).val();
        $('#cod-dp' + index).val('');
        console.log(query);
        console.log(cod_detalle);
        if (query.length > 2) {
            console.log("es mayor de 2");
            $.ajax({
                url: 'index.php?pagina=compras',
                method: 'POST',
                data: { b_lotes: query, cod: cod_detalle },
                dataType: 'json',
                success: function (data) {
                    console.log("Enviando búsqueda con valor:", query); 
                    console.log(data);
                    var listaProductos = $('#lista-lotes' + index);
                    listaProductos.empty(); 

                    if (data.length > 0) {
                        console.log("entra en el condicional");
                 
                        $.each(data, function (key, producto) {
                            console.log(producto);
                            console.log(listaProductos);
                            listaProductos.append(
                                '<a href="#" class="list-group-item list-group-item-action producto-item" ' +
                                'data-nombre="' + producto.lote + '" ' +
                                'data-fecha="' + producto.fecha_vencimiento + '" ' +
                                'data-codigo="' + producto.cod_detallep + '">' +
                                producto.lote + ' </a>'
                            );
                        });
                        listaProductos.fadeIn();
                    } else {
                        listaProductos.fadeOut();
                    }
                }
            });
        } else {
            $('#lista-lotes' + index).fadeOut(); 
        }
   
        $(document).on('click', '.producto-item', function () {
            var selectedProduct = $(this).data('nombre');
            var codigo = $(this).data('codigo');
            var fecha = $(this).data('fecha');


            var inputId = $(this).closest('.list-group').prev('input').attr('id');
            var index = inputId.replace('lotes', ''); 
            $('#' + inputId).val(selectedProduct);

            $('#cod-dp' + index).val(codigo);
            $('#fecha-v' + index).val(fecha);
            $(this).closest('.list-group').fadeOut();
        });
    });
});

$(document).ready(function () {
 
    var now = new Date();
    var fecha = now.getFullYear() + '-' +
        String(now.getMonth() + 1).padStart(2, '0') + '-' +
        String(now.getDate()).padStart(2, '0');

    $('#fecha-hora').val(fecha);
});

//Modal detalle
$(document).ready(function () {

    $('#detallemodal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); 
        var codigo = button.data('codigo'); 
        var fecha = button.data('fecha');
        var nombre = button.data('nombre');
        var total = button.data('total');

        var modal = $(this);
        modal.find('.modal-body #nro-compra').val(codigo);
        modal.find('.modal-body #r_social').val(nombre);
        modal.find('.modal-body #fecha_compra').val(fecha);
        modal.find('.modal-body #total_compra').text(total);

        $('#detalleBodyc').empty();

       
        $.ajax({
            url: 'index.php?pagina=compras',
            method: 'POST',
            data: { detallep: codigo },
            dataType: 'json',

            success: function (data) {
                console.log(data);
                $('#detalleBodyc').empty();
               
                if (Array.isArray(data) && data.length > 0) {
                    $.each(data, function (index, detalle) {

                        $('#detalleBodyc').append(
                            '<tr>' +
                            '<td>' + detalle.cod_detallep + '</td>' +
                            '<td>' + detalle.nproducto + (detalle.presentacion ? detalle.presentacion + ' - ':'') + (detalle.marca ? ' - '+detalle.marca :'sin marca') + '</td>' +
                            '<td>' + detalle.fecha_vencimiento + '</td>' +
                            '<td>' + detalle.lote + '</td>' +
                            '<td class="stock">' + detalle.cantidad + '</td>' +
                            '<td>' + detalle.monto + '</td>' +
                            '<td>' + (detalle.monto * detalle.cantidad).toFixed(2) + '</td>' +
                            '</tr>'
                        );
                    });

                } else {
                    $('#detalleBodyc').append(
                        '<tr>' +
                        '<td colspan="7" class="text-center">No hay detalles disponibles para este producto</td>' +
                        '</tr>'
                    );

                }
            },
            error: function (xhr, status, error) {
                console.error('Error al cargar los detalles:', error);
            }
        });
    });
});

//validación de pagos
$('#pagoFormc').on('submit', function (event) {
    event.preventDefault(); 
    var errores = 0;
    var peticiones = 0;
    var respuestas = 0;
    var form = this;
    console.log('DETENER ENVIO');

    $('#pagoFormc').find('.monto-bsc, .monto-divisac').each(function () {
        var $input = $(this);
        var monto = parseFloat($input.val()) || 0;
        if (monto > 0) {
            var id = $input.attr('id');
            var index = id ? id.split('-').pop() : null;
            var cod_tipo_pago = $('input[name="pago[' + index + '][cod_tipo_pago]"]').val();

            peticiones++;
            $.ajax({
                type: 'POST',
                url: 'index.php?pagina=compras',
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
                                if ($('#pagoFormc').find('input[name="pagar_compra"]').length === 0) {
                                    $('<input>').attr({
                                        type: 'hidden',
                                        name: 'pagar_compra',
                                        value: '1'
                                    }).appendTo('#pagoFormc');
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
                        text: 'Ocurrió un error al verificar los detalles del producto.',
                        icon: 'error'
                    });
                }
            });
        }
    });
});


function calcularTotalpagoc() {
    console.log("Calculando total de pago...");
    let totalBs = 0;
 
    document.querySelectorAll('.monto-bsc:not(.monto-conc)').forEach(function (input) {
        let montoBs = parseFloat(input.value) || 0;
        totalBs += montoBs;  
        console.log('Monto en Bs:', montoBs);
    });
   
    document.querySelectorAll('.monto-divisac').forEach(function (inputDivisa) {
        let index = inputDivisa.id.split('-').pop();  


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
 
    console.log('Monto del vuelto:', montoVuelto);
    
    $('#total-vueltoc').text(montoVuelto.toFixed(2) + 'Bs');
    $('#monto_pagarvc').val(montoVuelto.toFixed(2));
  
    document.querySelectorAll('.monto-bsvc').forEach(function (input) {
        input.value = '';
    });
    document.querySelectorAll('.monto-divisavc').forEach(function (input) {
        input.value = '';
    });

  
    $('#registrarVueltoBtnc').prop('disabled', true);


    calcularTotalvueltoc();
});

$(document).ready(function () {
    window.vueltoRegistrado = false;
    $('#pagoModalc').on('hidden.bs.modal', function () {
        
        location.reload();
    });

    
    $('#registrarVueltoBtnc').on('click', function (e) {
        e.preventDefault(); 

      
        let diferencia = parseFloat(document.getElementById('diferenciavc').value);

        if (Math.abs(diferencia) < 0.01) {
          
            let vueltoData = $('#vueltoFormc').serialize();

        
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

    $('#pagoFormc').on('submit', function (e) {
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

    
    $('.monto-bs, .monto-divisac').on('input', function () {
        calcularTotalpagoc();
    });
});

$('#pagoModalc').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var codigo = button.data('cod_compra');
    var total = button.data('total');
    var nombre = button.data('nombre');
    var saldoP = button.data('montop');
    var saldoPendiente = parseFloat(total) - parseFloat(saldoP) || 0;

    console.log("saldo pendiente: " + saldoPendiente);
   
    var now = new Date();
    var fecha = now.getFullYear() + '-' +
        String(now.getMonth() + 1).padStart(2, '0') + '-' +
        String(now.getDate()).padStart(2, '0');


    var hora = String(now.getHours()).padStart(2, '0') + ':' +
        String(now.getMinutes()).padStart(2, '0') + ':' +
        String(now.getSeconds()).padStart(2, '0');
    var fechaHora = fecha + ' ' + hora;

    var modal = $(this);
    modal.find('.modal-body #nro-compra').val(codigo);
    modal.find('.modal-body #total-pagoc').text(total + 'Bs');
    modal.find('.modal-body #total_compra').val(total);
    modal.find('.modal-body #r_social').val(nombre);
    modal.find('.modal-body #fecha_pagoc').val(fechaHora);


    if (saldoP > 0) {
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


    document.querySelectorAll('.monto-bsvc:not(.monto-convc)').forEach(function (input) {
        let montoBs = parseFloat(input.value) || 0;
        totalBs += montoBs; 
    });

 
    document.querySelectorAll('.monto-divisavc').forEach(function (inputDivisa) {
        let index = inputDivisa.id.split('-').pop();  
        
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





function mostrarFechaVencimiento() {
    var condicionPago = document.getElementById("condicion_pago").value;
    var divFechaVencimiento = document.getElementById("div_fecha_vencimiento");

    if (condicionPago === "2") {
        divFechaVencimiento.style.display = "block";
        document.getElementById("fecha_vencimiento").required = true;
    } else {
        divFechaVencimiento.style.display = "none";
        document.getElementById("fecha_vencimiento").required = false;
        document.getElementById("fecha_vencimiento").value = "";
    }
}