$(document).ready(function () {

    $('#modalAperturaCaja').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var nombre = button.data('nombre');
        var divisa = button.data('divisa');
        var codigo = button.data('cod');
        var saldo = button.data('saldo');

        var modal = $(this);
        modal.find('.modal-body #cod_caja_apertura').val(codigo);
        modal.find('.modal-body #nombrea').val(nombre);
        modal.find('.modal-body #divisaa').val(divisa);
        modal.find('.modal-body #saldoa').val(saldo);
        modal.find('.modal-body #saldoa_hidden').val(saldo);

        setFechaHoraLocal('fecha_apertura');
    });

    $('#movimientosActuales').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var nombre = button.data('nombre');
        var divisa = button.data('divisa');
        var codigo = button.data('cod');
        var codigoc = button.data('codigoc');
        var saldo = button.data('saldo');
        var fechaa = button.data('fecha');

        var modal = $(this);
        modal.find('.modal-body #codigo').val(codigo);
        modal.find('.modal-body #codigoc').val(codigoc);
        modal.find('.modal-body #fecha').val(fechaa);
        modal.find('.modal-body #nombreCajaMov').text(nombre);
        modal.find('.modal-body #divisaCajaMov').text(divisa);
        modal.find('.modal-body #saldoCajaMov').text(saldo);

        // Paso extra: consultar el cod_control de la caja abierta
        $.post('index.php?pagina=controlcaja', {
            buscar_control_abierto: 1,
            cod_caja: codigo,
            fecha: fechaa
        }, function (res) {
            console.log("🔹 Respuesta de la consulta de control abierto:", res);
            if (res.cod_control) {
                // Ahora sí pedimos los movimientos
                $.post('index.php?pagina=controlcaja', {
                    movimientosActuales: 1,
                    cod_control: codigoc,
                    fecha: fechaa,
                }, function (response) {
                    console.log("🔹 Respuesta de movimientos actuales:", response);
                    const tbody = $('#tablaMovimientosDia');
                    tbody.empty();

                    if (response.error) {
                        tbody.append(`<tr><td colspan="5">${response.error}</td></tr>`);
                        return;
                    }

                    if (response.movimientos && response.movimientos.length > 0) {
                        const modalTable = $('#movimientosActuales .datatable');
                        if ($.fn.DataTable.isDataTable(modalTable)) {
                            modalTable.DataTable().clear().destroy();
                        }
                        tbody.empty(); // Limpiar el cuerpo de la tabla antes de añadir

                        // Insertar nuevas filas
                        response.movimientos.forEach(mov => {
                            const clase = parseFloat(mov.monto) >= 0 ? 'text-success' : 'text-danger';
                            const claseTipo = mov.tipo_movimiento === 'ENTRADA' ? 'bg-success' : 'bg-danger';
                            tbody.append(`
                                <tr>
                                    <td>${mov.fecha || '---'}</td>
                                    <td>${mov.modulo || '---'}</td>
                                    <td><span class="badge ${claseTipo}">${mov.tipo_movimiento || '---'}</span></td>
                                    <td>${mov.referencia || '---'}</td>
                                    <td class="${clase}">${parseFloat(mov.monto).toFixed(2)}</td>
                                </tr>
                            `);
                        });

                        modalTable.DataTable({
                            responsive: true,
                            autoWidth: false,
                            order: [[0, 'desc']],
                            language: {
                                url: "//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json"
                            }
                        });

                    } else {
                        tbody.append('<tr><td colspan="5">No hay movimientos registrados.</td></tr>');
                    }

                }, 'json');
            } else {
                tbody.html('<tr><td colspan="5" class="text-center text-danger">Error, no es posible acceder al historial.</td></tr>');

            }
        }, 'json');
    });


    $('#modalCierreCaja').on('show.bs.modal', function (event) {
        const button = $(event.relatedTarget);
        const nombre = button.data('nombre');
        const divisa = button.data('divisa');
        const codCaja = button.data('cod');
        const codControl = button.data('codigoc');

        const modal = $(this);
        modal.find('#cod_caja_cierre').val(codCaja);
        modal.find('#cod_control_cierre').val(codControl);
        modal.find('#nombreCierre').text(nombre);
        modal.find('#divisaCierre').text(divisa);
        modal.find('#monto_contado').val('');
        modal.find('#observacion').val('');
        modal.find('#btnConfirmarCierre').prop('disabled', true);

        const resumenPagos = modal.find('#resumenPagos');
        resumenPagos.empty();

        // Obtener resumen de pagos
        $.ajax({
            url: 'index.php?pagina=controlcaja',
            type: 'POST',
            dataType: 'json',
            data: {
                resumen_pagos_caja: 1,
                //cod_caja: codCaja,
                cod_control: codControl
            },
            success: function (res) {
                //console.log("✅ Respuesta del backend:", res);
                if (res.success && res.resumen) {
                    let totalSistema = 0;
                    let html = '<ul class="list-group mb-3">';
                    res.resumen.forEach(item => {
                        const totalNeto = parseFloat(item.total_neto) || 0;
                        totalSistema += totalNeto;
                        html += `<li class="list-group-item d-flex justify-content-between">
                        <span><b>${item.tipo_pago}</b></span>
                        <span>${totalNeto.toFixed(2)}</span>
                    </li>`;
                    });
                    html += `<li class="list-group-item d-flex justify-content-between bg-light">
                            <span><b>Total</b></span>
                            <span id="totalSistemaMonto"><b>${totalSistema.toFixed(2)}</b></span>
                        </li>`;
                    html += '</ul>';
                    resumenPagos.html(html);
                    //$('#saldoSistema').text(totalSistema.toFixed(2));
                    $('#monto_contado').data('total-sistema', totalSistema);
                } else {
                    resumenPagos.html('<p class="text-danger">No tienes movimientos en este control de caja.</p>');
                    //console.error("❌ Error en los datos recibidos:", res);
                }
            },
            error: function (xhr, status, error) {
                resumenPagos.html('<p class="text-danger">Error al consultar el resumen.</p>');
                //console.error("❌ Error en la solicitud AJAX:", error);
                //console.log("📄 Respuesta completa:", xhr.responseText);
            }
        });
    });


    $('#monto_contado').on('input', function () {
        const contado = parseFloat($(this).val()) || 0;
        const totalSistema = parseFloat($('#totalSistemaMonto').text().replace(',', '.')) || 0;

        const observacion = $('#observacion');
        const btnCerrar = $('#btnConfirmarCierre');

        if (contado < 0) {
            observacion.val('');
            btnCerrar.prop('disabled', true);
            return;
        }

        //console.log("contado:", contado, "→", contado.toFixed(2));
        //console.log("totalSistema:", totalSistema, "→", totalSistema.toFixed(2));
        //console.log("comparación:", contado.toFixed(2) === totalSistema.toFixed(2));

        //console.log("Monto contado (crudo):", contado, typeof contado);
        //console.log("Total del sistema (crudo):", totalSistema, typeof totalSistema);

        const epsilon = 0.001; // Tolerancia para comparación de montos
        if (Math.abs(contado - totalSistema) < epsilon) {
            observacion.val('Cierre OK. Montos coinciden.');
            btnCerrar.prop('disabled', false);
        } else {
            observacion.val('Diferencia detectada en el cierre.');
            btnCerrar.prop('disabled', false);
        }

    });

    function setFechaHoraLocal(inputId) {
        const now = new Date();
        const fechaHoraLocal = now.toISOString().slice(0, 16);

        const offsetMs = now.getTimezoneOffset() * 60000;
        const localISOTime = new Date(now - offsetMs).toISOString().slice(0, 16);

        const input = document.getElementById(inputId);
        input.value = localISOTime;
        input.max = localISOTime;
    }


});