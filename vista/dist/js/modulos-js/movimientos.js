$(document).ready(function() {
    $(document).on('click', '.btn-ver-asiento[data-toggle="modal"]', function() {
        var codOperacion = $(this).data('cod');
        var descripcion = $(this).data('des');
        var fecha=$(this).data('fecha');
        $('#descripcion').val(descripcion);
        $('#fecha').val(fecha);

        // Limpia el contenedor antes de cargar nuevos datos
        $('#asientosGeneradosContainer').html('<p>Cargando...</p>');

        // AJAX para obtener los detalles de todos los asientos relacionados
        $.ajax({
            url: 'index.php?pagina=movimientos',
            method: 'POST',
            data: { codmov: codOperacion },
            dataType: 'json',
            success: function(response) {
                if (Array.isArray(response) && response.length > 0) {
                    // Agrupar por cod_asiento
                    var asientos = {};
                    response.forEach(function(item) {
                        if (!asientos[item.cod_asiento]) {
                            asientos[item.cod_asiento] = {
                                descripcion: item.descripcion,
                                fecha: item.fecha,
                                total: item.total,
                                detalles: []
                            };
                        }
                        asientos[item.cod_asiento].detalles.push(item);
                    });
                    console.log(asientos);
                    var html = '';
                    $.each(asientos, function(cod_asiento, asiento) {
                        html += '<div class="mb-4">';
                        html += '<h5>Asiento #' + cod_asiento + ' - ' + asiento.descripcion + '  <b>Fecha:</b> ' + asiento.fecha + ' &nbsp; </h5>';
                        html += '<div class="table-responsive">';
                        html += '<table class="table-bordered table" style="width: 100%;">';
                        html += '<thead><tr><th>Código</th><th>Cuenta</th><th>Debe</th><th>Haber</th></tr></thead><tbody>';
                        asiento.detalles.forEach(function(det) {
                            html += '<tr>' +
                                '<td>' + det.codigo_cuenta + '</td>' +
                                '<td>' + det.nombre_cuenta + '</td>' +
                                '<td>' + (det.tipo === 'Debe' ? det.monto : '') + '</td>' +
                                '<td>' + (det.tipo === 'Haber' ? det.monto : '') + '</td>' +
                            '</tr>';
                        });
                        html += '<tr><td colspan="2" style="text-align:right;"><b>Total: </b></td><td><b>' + asiento.total + '</b></td><td><b>' + asiento.total + '</b></td></tr>';
                        html += '</tbody></table></div>';
                    });
                    $('#asientosGeneradosContainer').html(html);
                } else {
                    $('#asientosGeneradosContainer').html('<p>No hay detalles.</p>');
                }
            },
            error: function() {
                $('#asientosGeneradosContainer').html('<p>Error al cargar detalles.</p>');
            }
        });
    });

    $(document).on('click', '.btn-ver-asiento-manual[data-toggle="modal"]', function () {
        var codAsiento = $(this).data('cod');        // este es el cod_asiento directo
        var descripcion = $(this).data('des');
        var fecha = $(this).data('fecha');
    
        // Asigna los datos básicos al modal
        $('#descripcion2').val(descripcion);
        $('#fecha2').val(fecha);
        $('#asientosGeneradosContainer2').html('<p>Cargando...</p>');
    
        // Petición AJAX por cod_asiento
        $.ajax({
            url: 'index.php?pagina=movimientos',
            method: 'POST',
            data: { cod_asiento: codAsiento },
            dataType: 'json',
            success: function (response) {
                if (Array.isArray(response) && response.length > 0) {
                    var asiento = {
                        descripcion: response[0].descripcion,
                        fecha: response[0].fecha,
                        total: response[0].total,
                        detalles: response
                    };
    
                    var html = '';
                    html += '<div class="mb-4">';
                    html += '<h5>Asiento #' + codAsiento + ' - ' + asiento.descripcion + ' <b>Fecha:</b> ' + asiento.fecha + '</h5>';
                    html += '<div class="table-responsive">';
                    html += '<table class="table-bordered table" style="width: 100%;">';
                    html += '<thead><tr><th>Código</th><th>Cuenta</th><th>Debe</th><th>Haber</th></tr></thead><tbody>';
    
                    asiento.detalles.forEach(function (det) {
                        html += '<tr>' +
                            '<td>' + det.codigo_cuenta + '</td>' +
                            '<td>' + det.nombre_cuenta + '</td>' +
                            '<td>' + (det.tipo === 'Debe' ? det.monto : '') + '</td>' +
                            '<td>' + (det.tipo === 'Haber' ? det.monto : '') + '</td>' +
                            '</tr>';
                    });
    
                    html += '<tr><td colspan="2" style="text-align:right;"><b>Total: </b></td><td><b>' + asiento.total + '</b></td><td><b>' + asiento.total + '</b></td></tr>';
                    html += '</tbody></table></div>';
                    html += '</div>';
    
                    $('#asientosGeneradosContainer2').html(html);
                } else {
                    $('#asientosGeneradosContainer2').html('<p>No hay detalles para este asiento.</p>');
                }
            },
            error: function () {
                $('#asientosGeneradosContainer2').html('<p>Error al cargar detalles del asiento.</p>');
            }
        });
    });
    
});

$('#modalAsientoManual').on('show.bs.modal', function () {
    inicializarFilas('manual');
});

$('#modalAsientoApertura').on('show.bs.modal', function () {
    inicializarFilas('apertura');
});

function inicializarFilas(tipo) {
    for (let i = 1; i <= 2; i++) {
        agregarFila(tipo);
    }
}


//Asientos apertura
$(document).on('click', '#btnRegistrarApertura', function () {
    $('#modalConfirmarApertura').modal('show');
});


// Agregar fila dinámicamente con índices correctos
function agregarFila(tipo) {
    const idTabla = tipo === 'apertura' ? 'detalleAsientoApertura' : 'detalleAsientoManual';
    const tabla = document.getElementById(idTabla);
    const filaIndex = tabla.querySelectorAll('tr').length;
    const opciones= tipo === 'apertura' ? opcionesCuentasApertura : opcionesCuentasManuales;

    const fila = `
        <tr>
            <td>
                <select name="detalles[${filaIndex}][cuenta]" class="form-control cuenta" required onchange="actualizarCodigoContable(this, ${filaIndex})">
                    <option value="">-- Selecciona cuenta --</option>
                    ${opciones}
                </select>
                <input type="hidden" name="detalles[${filaIndex}][codigo_contable]" class="codigo-contable">
                <div class="invalid-feedback" style="display: none;"></div>
            </td>
            <td>
                <input type="number" name="detalles[${filaIndex}][debe]" step="0.01" class="form-control text-right debe" oninput="bloquearOpuesto(this)">
                <div class="invalid-feedback" style="display: none;"></div>
            </td>
            <td>
                <input type="number" name="detalles[${filaIndex}][haber]" step="0.01" class="form-control text-right haber" oninput="bloquearOpuesto(this)">
                <div class="invalid-feedback" style="display: none;"></div>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('tr').remove(); reindexarFilas('${tipo}')">🗑</button>
            </td>
        </tr>
    `;
    tabla.insertAdjacentHTML('beforeend', fila);
}

function actualizarCodigoContable(selectElement, index) {
    const selectedOption = selectElement.options[selectElement.selectedIndex];
    const codigo = selectedOption.getAttribute('data-codigo');
    const inputHidden = document.querySelector(`input[name="detalles[${index}][codigo_contable]"]`);
    if (inputHidden) {
        inputHidden.value = codigo || '';
    }
}

// Reindexar nombres después de eliminar fila
function reindexarFilas(tipo) {
    const idTabla = tipo === 'apertura' ? 'detalleAsientoApertura' : 'detalleAsientoManual';
    const filas = document.querySelectorAll(`#${idTabla} tr`);

    filas.forEach((fila, i) => {
        fila.querySelector('select.cuenta').name = `detalles[${i}][cuenta]`;
        fila.querySelector('input.debe').name   = `detalles[${i}][debe]`;
        fila.querySelector('input.haber').name  = `detalles[${i}][haber]`;
        fila.querySelector('input.codigo-contable').name = `detalles[${i}][codigo_contable]`; // 🔹 Esta es la línea nueva
    });
}



function bloquearOpuesto(input) {
    const fila = input.closest('tr');
    const debe = fila.querySelector('input.debe');
    const haber = fila.querySelector('input.haber');

    if (input === debe) {
        haber.readOnly = !!debe.value;
        if (debe.value) haber.value = '';
    }

    if (input === haber) {
        debe.readOnly = !!haber.value;
        if (haber.value) debe.value = '';
    }
}

$(document).ready(function () {
    function showError($el, message) {
        $el.addClass('is-invalid');
        $el.closest('td').find('.invalid-feedback').html('<i class="fas fa-exclamation-triangle"></i> ' + message.toUpperCase()).css({
            'display': 'block',
            'color': 'red',
        });
    }

    function hideError($el) {
        $el.removeClass('is-invalid');
        $el.closest('td').find('.invalid-feedback').hide();
    }

    function validarAsiento(tbodyId, botonId, mensajeId) {
        const filas = $('#' + tbodyId + ' tr');
        let errores = 0;
        let totalDebe = 0;
        let totalHaber = 0;

        if (filas.length < 2) {
            $('#' + mensajeId).text("Debe registrar al menos 2 cuentas contables").show();
            errores++;
        } else {
            $('#' + mensajeId).hide().text('');
        }

        filas.each(function () {
            const fila = $(this);
            const cuentaEl = fila.find('select.cuenta');
            const debeEl = fila.find('input.debe');
            const haberEl = fila.find('input.haber');

            const cuenta = cuentaEl.val();
            const debe = parseFloat(debeEl.val()) || 0;
            const haber = parseFloat(haberEl.val()) || 0;

            if (!cuenta) {
                showError(cuentaEl, 'Debe seleccionar una cuenta');
                errores++;
            } else {
                hideError(cuentaEl);
            }

            if (debe <= 0 && haber <= 0) {
                showError(debeEl, 'Debe llenar uno de los campos');
                showError(haberEl, 'Debe llenar uno de los campos');
                errores++;
            } else {
                hideError(debeEl);
                hideError(haberEl);
            }

            totalDebe += debe;
            totalHaber += haber;
        });

        if (totalDebe.toFixed(2) !== totalHaber.toFixed(2)) {
            $('#' + mensajeId).text("El asiento no está cuadrado. Debe no es igual al Haber").show();
            errores++;
        } else if (filas.length >= 2) {
            $('#' + mensajeId).hide().text('');
        }

        $('#' + botonId).prop('disabled', errores > 0);

        return errores === 0;
    }

    // Listeners para ambos modales
    $('#detalleAsientoApertura, #detalleAsientoManual').on('input change', 'select, input', function () {
        const tbodyId = $(this).closest('tbody').attr('id');
        const config = {
            'detalleAsientoApertura': ['btnRegistrarApertura', 'mensajeValidacionApertura'],
            'detalleAsientoManual': ['btnRegistrarManual', 'mensajeValidacionManual']
        };

        if (config[tbodyId]) {
            validarAsiento(tbodyId, config[tbodyId][0], config[tbodyId][1]);
        }
    });

    // Funciones para mostrar modal solo si es válido
    window.confirmarApertura = function () {
        if (validarAsiento('detalleAsientoApertura', 'btnRegistrarApertura', 'mensajeValidacionApertura')) {
            $('#modalConfirmarApertura').modal('show');
        }
    };
});
