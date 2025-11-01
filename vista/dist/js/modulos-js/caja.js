$(document).ready(function () {

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


    $('#nombre').on('blur', function () {
        var tipo_medida = $(this).val();
        if (tipo_medida.trim() === '') {
            hideError('#nombre');
        } else if (tipo_medida.length > 20) {
            showError('#nombre', 'El texto no debe exceder los 20 caracteres');
        } else if (!/^[a-zA-ZÀ-ÿ\s]+$/.test(tipo_medida)) {
            showError('#nombre', 'Solo se permiten letras');
        } else {
            hideError('#nombre');
        }
    });

    $('#nombre1').on('blur', function () {
        var nombre1 = $(this).val();
        if (nombre1.trim() === '') {
            hideError('#nombre1');
        } else if (nombre1.length > 20) {
            showError('#nombre1', 'El texto no debe exceder los 20 caracteres');
        } else if (!/^[a-zA-ZÀ-ÿ\s]+$/.test(nombre1)) {
            showError('#nombre1', 'Solo se permiten letras');
        } else {
            hideError('#nombre1');
        }
    });

    $('#saldo').on('input', function () {
        this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');
        if (this.value.trim() === '' || isNaN(this.value)) {
            showError('#saldo', 'Ingrese un valor numérico válido');
        } else {
            hideError('#saldo');
        }
    });

    $('#saldo1').on('input', function () {
        this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');
        if (this.value.trim() === '' || isNaN(this.value)) {
            showError('#saldo1', 'Ingrese un valor numérico válido');
        } else {
            hideError('#saldo1');
        }
    });

    // Validar si la caja ya existe (registrar)
    $('#nombre').blur(function (e) {
        var buscar = $('#nombre').val();
        if (buscar.trim() !== '') {
            $.post('index.php?pagina=caja', { buscar }, function (response) {
                if (response != '') {
                    showError('#nombre', 'La caja ya está registrada');
                    Swal.fire({
                        title: 'Error',
                        text: 'La caja ya se encuentra registrada.',
                        icon: 'warning'
                    });
                }
            }, 'json');
        }
    });


    //EDITAR
    $('#modalmodificarcaja').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);

        var cod = button.data('cod');
        var nombre = button.data('nombre');
        var saldo = button.data('saldo');
        var divisa = button.data('divisa');
        var status = button.data('status');

        console.log('🟢 DATOS RECIBIDOS DEL BOTÓN:');
        console.log('COD:', cod);
        console.log('NOMBRE:', nombre);
        console.log('SALDO:', saldo);
        console.log('DIVISA:', divisa);
        console.log('STATUS:', status);

        var modal = $(this);

        // Asignar los valores a los campos del formulario dentro del modal
        modal.find('#cod_caja').val(cod);
        modal.find('#nombre1').val(nombre);
        modal.find('#saldo1').val(saldo);
        modal.find('#divisa1').val(divisa);
        modal.find('#status').val(status);
        console.log('✅ CAMPOS DEL MODAL ASIGNADOS');
        console.log('cod_caja:', modal.find('#cod_caja').val());
        console.log('nombre1:', modal.find('#nombre1').val());
        console.log('saldo1:', modal.find('#saldo1').val());
        console.log('divisa1:', modal.find('#divisa1').val());
        console.log('status:', modal.find('#status').val());
    });


    // Validar formulario de edición antes de enviar
    $('#form-editar-caja').submit(function (e) {
        var isValid = true;
        // Validar nombre
        if ($('#nombre1').val().trim() === '') {
            showError('#nombre1', 'El nombre es requerido');
            isValid = false;
        }
        // Validar saldo
        if ($('#saldo1').val().trim() === '' || isNaN($('#saldo1').val())) {
            showError('#saldo1', 'Ingrese un saldo válido');
            isValid = false;
        }
        if (!isValid) {
            e.preventDefault();
            return false;
        }
        return true;
    });

    //ELIMINAR
    $('#modaleliminar').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var codigo = button.data('cod');
        var status = button.data('status');
        var nombre = button.data('nombre');

        var modal = $(this);
        modal.find('.modal-body #cod_eliminar').val(codigo);
        modal.find('#nombreD').text(nombre);
        modal.find('.modal-body #status_e').val(codigo);
    });


    ////////////////////////////HISTORIAL///////////////////////////////////
    $(document).on('click', '.ver-historial', function () {
        let cod_caja = $(this).data('cod');
        let nombre = $(this).data('nombre');
        let moneda = $(this).data('divisa');

        $('#cod_caja_historial').val(cod_caja);
        $('#nombrecaja').val(nombre);
        $('#monedacaja').val(moneda);
        $('#tablaHistorialCaja tbody').html('<tr><td colspan="7" class="text-center">Cargando historial...</td></tr>');

        $('#modalHistorialCaja').modal('show');
        console.log('Cargando historial para Caja:', cod_caja, 'Nombre:', nombre, 'Moneda:', moneda);
        $.ajax({
            url: 'index.php?pagina=caja',
            method: 'POST',
            data: { cod_caja_historial: cod_caja },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    let historial = response.data;
                    let html = '';
                    console.log('Historial de Caja:', historial);
                    if (historial.length > 0) {
                        historial.forEach((item, index) => {
                            html += `
                                <tr>
                                    <td>${index + 1}</td>
                                    <td>${item.fecha}</td>
                                    <td>${item.hora_apertura}</td>
                                    <td>${item.hora_cierre ?? '-'}</td>
                                    <td>${item.saldo_inicial} Bs</td>
                                    <td>${item.saldo_final ?? '-'} Bs</td>
                                    <td>
                                        <button class="btn btn-info btn-sm ver-movimientos-historial" title="Ver Movimientos" data-target="#modalMovimientosHistorial" data-toggle="modal"
                                                data-id="${item.cod_control}"
                                                data-apertura="${item.fecha_apertura}"
                                                data-cierre="${item.fecha_cierre}"
                                                data-caja="${item.cod_caja}"
                                                data-nombre="${item.nombre_caja}"
                                                data-divisa="${item.nombre_divisa}"
                                                data-usuario="${item.username}">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button type="button" class="btn btn-danger btn-sm btn-exportar-pdf" title="${Title}" ${Disabled}
                                        data-id="${item.cod_control}"
                                        data-caja="${item.cod_caja}"
                                        data-apertura="${item.fecha_apertura}"
                                                data-cierre="${item.fecha_cierre}">
                                            <i class="fas fa-file-pdf"></i>
                                        </button>
                                    </td>
                                </tr>`;
                        });
                    } else {
                        html = `<tr><td colspan="7" class="text-center">No hay historial disponible.</td></tr>`;
                    }

                    $('#tablaHistorialCaja tbody').html(html);
                } else {
                    $('#tablaHistorialCaja tbody').html(`<tr><td colspan="7" class="text-center text-danger">${response.message}</td></tr>`);
                    console.error('Error al cargar historial:', response.message);
                }
            },
            error: function () {
                $('#tablaHistorialCaja tbody').html(`<tr><td colspan="7" class="text-center text-danger">Error al cargar historial.</td></tr>`);
            }
        });
    });

    //MODAL MOVIMIENTOS HISTORIAL
    $(document).on('click', '.ver-movimientos-historial', function () {
        const cod_caja = $(this).data('caja');
        const cod_control = $(this).data('id');
        const fecha_apertura = $(this).data('apertura');
        const fecha_cierre = $(this).data('cierre');

        const nombre = $(this).data('nombre');
        const divisa = $(this).data('divisa');
        const usuario = $(this).data('usuario');

        // Asignar a los spans del modal
        $('#nombreCaja').text(nombre || '---');
        $('#divisaCaja').text(divisa || '---');
        $('#cod_usuario').text(usuario || '---');

        console.log('Cargando movimientos para Caja:', cod_caja, 'Control:', cod_control, 'Apertura:', fecha_apertura, 'Cierre:', fecha_cierre);

        const tbody = $('#bodyHistorialMovimientos');
        tbody.html('<tr><td colspan="5" class="text-center">Cargando movimientos...</td></tr>');

        $.ajax({
            url: 'index.php?pagina=caja',
            type: 'POST',
            data: {
                movimientosHistorial: true,
                cod_control: cod_control,
                fecha: fecha_apertura,
                fechac: fecha_cierre
            },
            dataType: 'json',
            success: function (response) {
                tbody.empty();

                console.log('Movimientos Historial:', response);
                if (response.movimientos && response.movimientos.length > 0) {
                    response.movimientos.forEach(mov => {
                        const clase = parseFloat(mov.monto) >= 0 ? 'text-success' : 'text-danger';
                        const claseTipo = mov.tipo_movimiento === 'ENTRADA' ? 'bg-success' : 'bg-danger';

                        tbody.append(`
                        <tr>
                            <td>${mov.fecha || '---'}</td>
                            <td>${mov.modulo || '---'}</td>
                            <td><span class="badge ${claseTipo}">${mov.tipo_movimiento}</span></td>
                            <td>${mov.referencia || '---'}</td>
                            <td class="${clase}">${parseFloat(mov.monto).toFixed(2)}</td>
                        </tr>
                    `);
                    });
                } else {
                    tbody.append('<tr><td colspan="5" class="text-center">No hay movimientos.</td></tr>');
                }
            },
            error: function (xhr, status, error) {
                console.error("❌ Error AJAX", { xhr, status, error });
                tbody.html('<tr><td colspan="5" class="text-center text-danger">⚠ Error al consultar. Revisa la consola.</td></tr>');
            }
        });
    });

    $(document).on('click', '.btn-exportar-pdf', function () {
        const cod_control = $(this).data('id');
        const cod_caja = $(this).data('caja');
        const fechaa = $(this).data('apertura');
        const fechac = $(this).data('cierre');

        const form = $('<form>', {
            method: 'POST',
            action: 'index.php?pagina=reportes',
            target: '_blank'
        });

        form.append($('<input>', {
            type: 'hidden',
            name: 'tipo',
            value: 'caja'
        }));

        form.append($('<input>', {
            type: 'hidden',
            name: 'cod_control',
            value: cod_control
        }));

        form.append($('<input>', {
            type: 'hidden',
            name: 'cod_caja',
            value: cod_caja
        }));

        form.append($('<input>', {
            type: 'hidden',
            name: 'fechaa',
            value: fechaa
        }));

        form.append($('<input>', {
            type: 'hidden',
            name: 'fechac',
            value: fechac
        }));

        $('body').append(form);
        form.submit();
        form.remove();
    });

    // VACIAR MODAL DE REGISTRAR CAJA
    function resetRegistrarCajaModal() {
        const modal = $('#modalregistrarCaja');
        modal.find('input[type="text"], input[type="number"], textarea, select').val('');
        modal.find('.is-invalid').removeClass('is-invalid');
        modal.find('.invalid-feedback').hide();
        $('#divisa').prop('selectedIndex', 0);
    }

    $('#modalregistrarCaja').on('hidden.bs.modal', resetRegistrarCajaModal);




});