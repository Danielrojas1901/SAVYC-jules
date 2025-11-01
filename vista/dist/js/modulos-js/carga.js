//MUESTRA LOS DETALLES DE UNA CARGA
$(document).ready(function () {

    // Evento al abrir el modal
    $('#detallemodal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // Botón que abrió el modal
        var cod = button.data('codigo');
        var fechaC = button.data('fecha_carga');
        var descrip = button.data('descrip');

        var modal = $(this);
        modal.find('.modal-body #codigo').val(cod);
        modal.find('.modal-body #descrip').val(descrip);
        modal.find('.modal-body #fecha_carga').val(fechaC);
        //console.log(cod);

        // Limpiar la tabla de detalles antes de cargar nuevos datos
        $('#detalleBody').empty();

        // Hacer una llamada AJAX para obtener los detalles del producto
        $.ajax({
            url: 'index.php?pagina=carga',
            method: 'POST',
            data: {
                detalle: cod
            },
            dataType: 'json',
            success: function (data) {
                //console.log(data);
                // Verificar si hay datos en la respuesta
                if (data.length === 0) {
                    // Si no hay detalles mostrar un mensaje 
                    $('#detalleBody').append(
                        '<tr>' +
                        '<td colspan="6" class="text-center">No hay detalles disponibles para esta carga</td>' +
                        '</tr>'
                    );
                } else {
                    // Recorrer los datos devueltos y llenar la tabla
                    $.each(data, function (index, detalle) {
                        //console.log(detalle);

                        var statusText = detalle.status == '1' //Si el status es 1 mostrar activo sino inactivo
                            ?
                            '<span class="badge badge-success">Activo</span>' :
                            '<span class="badge badge-danger">Inactivo</span>' //NECESITO MEJORAR LA LOGIA Y EL FILTRADO DEL CONSULTAR
                        var fecha = detalle.fecha_vencimiento || " ";
                        var lote = detalle.lote || "No disponible";
                        $('#detalleBody').append(
                            '<tr>' +
                                '<td>' + 
                                    '<img src='+detalle.imagen+' alt="Imagen producto" style="width: 100px; height: auto;">' + 
                                '</td>' +
                                '<td>' + detalle.cod_det_carga + '</td>' +
                                '<td>' + detalle.nombre + (detalle.presentacion ? ' x ' + detalle.presentacion : '') + ' - ' + (detalle.marca ?? 'sin marca') +  '</td>' +
                                '<td>' + fecha + '</td>' +
                                '<td>' + lote + '</td>' +
                                '<td>' + detalle.cantidad + '</td>' +
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
});


$(document).ready(function () {

    var productoIndex = 0; // Contador para las filas de productos

    // Función para crear una nueva fila
    function crearFila(index) {
        return `
        <tr id="fila${index}">
        <td>
            <input type="text" class="form-control" id="codigoProducto${index}" name="productos[${index}][codigo1]" placeholder="Código del producto" readonly>
            <input type="hidden" id="costo${index}">
        </td>
        <td>
            <div class="input-group">
                <input type="text" class="form-control" id="nombreProducto${index}" name="productos[${index}][nombre]" placeholder="Nombre del producto" maxlength="30">
            </div>
            <div id="lista${index}" class="list-group" style="position: absolute; z-index: 1000;"></div>
        </td>
            <td>
                <input type="number" class="form-control" name="productos[${index}][cantidad]" id="cantidad${index}" required min="0" step="0.001" placeholder="Cantidad">
            </td>
            <td>
                <button type="button" class="btn btn-danger btn-sm" onclick="eliminarFila(${index})">&times;</button>
            </td>
        </tr>
    `;
    }

    // Evento al abrir el modal de registrar carga
    $('#modalregistrarCarga').on('show.bs.modal', function (event) {
        // Limpiar la tabla de productos
        $('#productosCarga tbody').empty();  //FUNCIONA
        // Reiniciar el índice de productos
        productoIndex = 1; // Reiniciar si quieres empezar desde 1 cada vez que abres el modal

        // Agregar una fila vacía al abrir el modal
        var nuevaFila = crearFila(productoIndex);
        $('#productosCarga tbody').append(nuevaFila);
    });

    // Función para agregar una nueva fila a la tabla
    $('#add-product').on('click', function () {
        productoIndex++; // Incrementar el índice antes de crear la nueva fila

        var nuevaFila = crearFila(productoIndex);
        $('#productosCarga tbody').append(nuevaFila);
    });



    // Función para eliminar una fila
    window.eliminarFila = function (index) {
        $('#fila' + index).remove();
    };

    // Manejo del envío del formulario para registrar la carga
    $('#formregistrarCarga').on('submit', function (event) {

        var productosTabla = [];
        $('#productosCarga tbody tr').each(function () {
            var codigo = $(this).find('input[name^="productos["][name$="[codigo]"]').val(); // Obtener el código
            //var nombre = $(this).find('input[name^="productos["][name$="[nombre]"]').val(); // Nombre
            var cantidad = $(this).find('input[name^="productos["][name$="[cantidad]"]').val(); // Obtener la cantidad
            if (codigo && cantidad) {
                productosTabla.push({
                    codigo: codigo,
                    //nombre: nombre,
                    cantidad: cantidad
                });
            }
        });
        var detallesVerificados = 0;
        var totalProductos = productosTabla.length;
        $.each(productosTabla, function (index, producto) {
            $.ajax({
                type: 'POST',
                url: 'index.php?pagina=carga',
                data: {
                    verificarDetalle: true,
                    id: producto.codigo // Usar el código del producto para verificar
                },
                dataType: 'json',
                success: function (response) {
                    if (response.status === 'success') {
                        detallesVerificados++;
                        console.log('no');
                    } else {
                        Swal.fire({
                            title: 'Producto Sin información',
                            text: 'El producto con código "' + producto.codigo + '" no tiene información registrada.',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Registrar Detalle',
                            cancelButtonText: 'Cancelar'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $('#cod_presentacion').val(producto.codigo); // Llenar el campo del modal
                                $('#modalregistrardetallep').modal('show'); // Mostrar el modal
                            }
                        });
                    }

                    // Si se han verificado todos los productos
                    if (detallesVerificados === totalProductos) {
                        console.log("Pasa por la funcion de registrar");
                        //return true // Enviar el formulario si todos los detalles están verificados
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Error en AJAX:', error);
                    Swal.fire({
                        title: 'Error',
                        text: 'Ocurrió un error al verificar los detalles del producto.',
                        icon: 'error'
                    });
                    event.preventDefault();
                    return;
                }
            });
        });


    });

    // Manejo del envío del formulario para registrar detalle de producto
    $('#formRegistrarDetalle').on('submit', function (event) {
        event.preventDefault(); // Prevenir el envío del formulario por defecto
        enviarFormularioDetalle(); // Llamar a la función para registrar el detalle
    });

    function enviarFormularioDetalle() {
        var formData = new FormData($('#formRegistrarDetalle')[0]); // Crear un objeto FormData

        // Agregar un campo adicional
        formData.append('registrarD', '1'); // Usar append para agregar parámetros

        $.ajax({
            type: 'POST',
            url: 'index.php?pagina=carga',
            data: formData,
            processData: false, // Importante: No procesar los datos
            contentType: false, // Importante: No establecer el tipo de contenido
            dataType: 'json',
            success: function (response) {
                if (response.status === 'success') {
                    Swal.fire({
                        title: response.data.title,
                        text: response.data.message,
                        icon: response.data.icon
                    }).then(() => {
                        $('#modalregistrardetallep').modal('hide'); // Cerrar el modal
                        $('#formRegistrarDetalle')[0].reset(); // Reiniciar el formulario
                    });
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: response.message,
                        icon: 'error'
                    });
                }
            },
            error: function (xhr, status, error) {
                console.error('Error en AJAX:', error);
                console.log('Respuesta del servidor:', xhr.responseText); // Muestra la respuesta del servidor
                Swal.fire({
                    title: 'Error',
                    text: 'Ocurrió un error al registrar más detalles.',
                    icon: 'error'
                });
            }
        });
    }

    // Evento 'input' en los campos de productos dinámicos
    $(document).on('input', '[id^=nombreProducto]', function () {
        var inputId = $(this).attr('id');
        var index = inputId.replace('nombreProducto', ''); // Obtener el índice del campo
        var query = $(this).val(); // Valor ingresado por el usuario

        if (query.length > 2) {
            $.ajax({
                url: 'index.php?pagina=carga',
                method: 'POST',
                data: {
                    buscar: query
                },
                dataType: 'json',
                success: function (data) {
                    var listaProductos = $('#lista' + index);
                    listaProductos.empty(); // Limpiar resultados anteriores

                    if (data.length > 0) {
                        // Recorrer los productos recibidos y mostrar nombre + código + precio concatenados Solo tome la logica y su funcion de venta para mostrar productos pero al quitar el costo no me muestra los productos
                        $.each(data, function (key, producto) {
                            
                            listaProductos.append(
                                '<a href="#" class="list-group-item list-group-item-action producto-item" style="color:#333333; font-weight:normal;"' +
                                'data-nombre="' + producto.producto_nombre + '" ' +
                                'data-tipo="' + producto.excento + '" ' +
                                'data-codigo="' + producto.cod_presentacion + '" ' +
                                'data-costo="' + producto.costo + '" ' +
                                'data-marca="' + producto.marca +'">' +
                                producto.producto_nombre + ' - ' + (producto.marca ?? 'sin marca') + (producto.presentacion ? ' - ' + producto.presentacion : '') + '</a>'
                            );
                        });
                        listaProductos.fadeIn();
                    } else {
                        listaProductos.append('<p class="list-group-item">No se encontraron productos</p>');
                    }
                }
            });
        } else {
            $('#lista' + index).fadeOut(); // Ocultar la lista si no hay suficientes caracteres
        }
    });

    // Cuando el usuario selecciona un producto
    $(document).on('click', '.producto-item', function () {
        var selectedProduct = $(this).data('nombre');
        var codigo = $(this).data('codigo');
        var tipo = $(this).data('tipo');
        var cant = 1;

        var inputId = $(this).closest('.list-group').attr('id');
        var index = inputId.replace('lista', ''); // Extrae el índice del campo
        $('#' + inputId).val(selectedProduct);

        $('#nombreProducto' + index).val(selectedProduct);  
        $('#codigoProducto' + index).val(codigo);
        $('#tipoProducto' + index).val(tipo);
        $('#cantidadProducto' + index).val(cant).trigger('change');
        $(this).closest('.list-group').fadeOut();
    });
});

$(document).ready(function () {

    $(document).on('blur', '.producto-item', function (e) {
        var codigo = $(this).data('codigo');
        if (codigo) { // Verifica que el código no esté vacío
            $.post('index.php?pagina=carga', {
                id: codigo,
                verificarDetalle: true // Asegúrate de enviar este parámetro
            }, function (response) {
                if (response.status === 'error') {
                    Swal.fire({
                        title: 'Error',
                        text: 'No tiene detalle.',
                        icon: 'warning'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $('#cod_presentacion').val(codigo);
                            $('#modalregistrardetallep').modal('show'); // Mostrar el modal
                        }
                    });
                }
            }, 'json');
        }
    });

    //Mostrar detalle
    $('#myModalr').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var nombre = button.data('nombre');
        var cantidad = button.data('cantidad');


        // Modal
        var modal = $(this);
        modal.find('.modal-body #cod').val(nombre);
        modal.find('.modal-body #nombre').val(cantidad);


    });
    // Validaciones JS
    // Validación por cada campo cuando se pierde el foco
    $('#fecha').on('blur', function () {
        var fecha = $(this).val();
        if (fecha.trim() === '') {
            showError('#fecha', 'el campo fecha no puede estar vacío');
        } else {
            hideError('#fecha');
        }
    });

    $('#cantidad').on('blur', function () {
        var cantidad = $(this).val();
        if (cantidad.trim() === '') {
            showError('#cantidad', 'el campo cantidad no puede estar vacío');
        } else if (!/^[0-9\.,]+$/.test(cantidad)) { // Cambié 'Z0-9' a '0-9'
            showError('#cantidad', 'solo se aceptan números');
        } else {
            hideError('#cantidad');
        }
    });

    $('#descripcion').on('blur', function () {
        var descripcion = $(this).val();
        if (descripcion.trim() === '') {
            showError('#descripcion', 'el campo descripcion no puede estar vacío');
        } else if (!/^[a-zA-Z0-9\s\.,]+$/.test(descripcion)) {
            showError('#descripcion', 'solo letras, números y signos');
        } else {
            hideError('#descripcion');
        }
    });

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
});

$(document).ready(function () {
    // Evento al abrir el modal de registrar carga
    $('#modalregistrarCarga').on('show.bs.modal', function (event) {

        var now = new Date();
        var fecha = now.getFullYear() + '-' +
            String(now.getMonth() + 1).padStart(2, '0') + '-' +
            String(now.getDate()).padStart(2, '0');
        // Formatea la hora en el formato HH:MM:SS
        var hora = String(now.getHours()).padStart(2, '0') + ':' +
            String(now.getMinutes()).padStart(2, '0') + ':' +
            String(now.getSeconds()).padStart(2, '0');

        var fechaHora = fecha + ' ' + hora;
        $('#fecha-hora').val(fechaHora); // Guardar en el campo oculto
        $('#fecha-hora-display').val(now.toLocaleString()); // Mostrar en un campo de texto
    });
});

// ...existing code...

// Función para recalcular el total de la carga
function recalcularTotalCarga() {
    console.log('Recalculando total de carga');
    var total = 0;
    $('#productosCarga tbody tr').each(function () {
        var index = $(this).attr('id').replace('fila', '');
        var cantidad = parseFloat($('#cantidad' + index).val()) || 0;
        var costo = parseFloat($('#costo' + index).val()) || 0;
        total += cantidad * costo;
        console.log(total);
    });
    $('#totalcarga').val(total.toFixed(2));
}

// Cuando cambia la cantidad de cualquier producto
$(document).on('input', '[id^=cantidad]', function () {
    recalcularTotalCarga();
    console.log('Cantidad cambiada');
});

// Cuando seleccionas un producto y se asigna el costo
$(document).on('click', '.producto-item', function () {
    var inputId = $(this).closest('.list-group').attr('id');
    var index = inputId.replace('lista', '');
    var costo = $(this).data('costo');
    $('#costo' + index).val(costo);

    // Recalcula el total de la carga
    recalcularTotalCarga();
});

// Cuando eliminas una fila, también recalcula el total
window.eliminarFila = function (index) {
    $('#fila' + index).remove();
    recalcularTotalCarga();
};

// ...existing code...