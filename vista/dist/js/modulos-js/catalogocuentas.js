$(document).ready(function () {
    // Validar campos
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

    // Registrar
    $('#nombreCuenta').on('blur', function () {
        const nombre = $(this).val().trim();

        if (nombre === '') {
            showError('#nombreCuenta', 'El campo no puede estar vacío.');
        } else if (nombre.length < 2) {
            showError('#nombreCuenta', 'Debe tener al menos 2 caracteres.');
        } else if (nombre.length > 50) {
            showError('#nombreCuenta', 'El texto no debe exceder los 50 caracteres.');
        } else if (!/^[\p{L}\d\s]+$/u.test(nombre)) {
            showError('#nombreCuenta', 'Solo se permiten letras, números y espacios.');
        } else {
            hideError('#nombreCuenta');
        }
    });


    $('#nivel').on('blur change', function () {
        const valor = $(this).val();

        if (valor === '') {
            showError('#nivel', 'Debe seleccionar un nivel.');
        } else {
            hideError('#nivel');
        }
    });

    $('#listaPadres').on('blur change', function () {
        if ($('#grupoCuentaPadre').is(':visible')) {
            const valor = $(this).val();

            if (valor === '') {
                showError('#listaPadres', 'Debe seleccionar una cuenta padre.');
            } else {
                hideError('#listaPadres');
            }
        }
    });

    $('#naturaleza').on('blur change', function () {
        if ($('#naturaleza').is(':visible')) {
            const valor = $(this).val();

            if (valor === '') {
                showError('#naturaleza', 'Debe seleccionar una naturaleza.');
            } else {
                hideError('#naturaleza');
            }
        }
    });



    // Editar
    $('#nombreCuentae').on('blur', function () {
        const nombre = $(this).val().trim();

        if (nombre === '') {
            showError('#nombreCuentae', 'El campo no puede estar vacío.');
        } else if (nombre.length < 2) {
            showError('#nombreCuentae', 'Debe tener al menos 2 caracteres.');
        } else if (nombre.length > 50) {
            showError('#nombreCuentae', 'El texto no debe exceder los 50 caracteres.');
        } else if (!/^[\p{L}\d\s]+$/u.test(nombre)) {
            showError('#nombreCuentae', 'Solo se permiten letras, números y espacios.');
        } else {
            hideError('#nombreCuentae');
        }
    });

    $('#naturalezae').on('blur change', function () {
        if ($('#naturalezae').is(':visible')) {
            const valor = $(this).val();

            if (valor === '') {
                showError('#naturalezae', 'Debe seleccionar una naturaleza.');
            } else {
                hideError('#naturalezae');
            }
        }
    });

    //Validar registrar
    $('#nombreCuenta').blur(function (e){
        var buscar=$('#nombreCuenta').val();
        $.post('index.php?pagina=catalogocuentas', {buscar}, function(response){
            if(response != ''){
                Swal.fire({
                    icon: 'warning',
                    title: 'Advertencia',
                    text: 'La cuenta ya se encuentra registrada',
                    confirmButtonText: 'Aceptar'
                });
            }
        },'json');
    });

    //Validar editar
    $('#nombreCuentae').blur(function (e){
        var buscar=$('#nombreCuentae').val();
        $.post('index.php?pagina=catalogocuentas', {buscar}, function(response){
            if(response != ''){
                Swal.fire({
                    icon: 'warning',
                    title: 'Advertencia',
                    text: 'La cuenta ya se encuentra registrada',
                    confirmButtonText: 'Aceptar'
                });
            }
        },'json');
    });




    //Dinamico
    $('#nivel').on('change', function () {
        var nivel = parseInt($(this).val());
        // Reiniciar campos visuales
        $('#codigoContable').val('');
        $('#grupoNaturaleza').hide();
        $('#grupoCuentaPadre').hide();
        $('#listaPadres').empty();
        $('#naturaleza').val('').prop('disabled', true);

        if (nivel === 1) {
            // ES CUENTA PADRE / GENERAR NUEVO CODIGO
            $('#grupoNaturaleza').show();
            $('#naturaleza').prop('disabled', false);

            const codPadre = $('#listaPadres').val();
            const nivel = $('#nivel').val();

            $.ajax({
                url: 'index.php?pagina=catalogocuentas',
                method: 'POST',
                data: {
                    generarRaiz: true,
                    cod_padre: codPadre,
                    nivel: nivel
                },
                dataType: 'json',
                success: function (data) {
                    $('#codigoContable').val(data);
                },
                error: function () {
                    $('#codigoContable').val('Error al generar código raíz');
                }
            });

        } else if (nivel > 1) {
            $('#grupoCuentaPadre').show();
            $('#grupoNaturaleza').show();

            //TRAIGO LAS CUENTAS PADRES POR NIVEL
            $.ajax({
                url: 'index.php?pagina=catalogocuentas',
                method: 'POST',
                data: { padre: nivel },
                dataType: 'json',
                success: function (data) {
                    const listaPadres = $('#listaPadres');
                    listaPadres.empty();
                    listaPadres.append('<option value="">Seleccione una cuenta padre</option>');

                    if (data.length > 0) {
                        $.each(data, function (index, cuenta) {
                            listaPadres.append(
                                '<option value="' + cuenta.cod_cuenta + '" data-naturaleza="' + cuenta.naturaleza + '">' +
                                cuenta.codigo_contable + ' - ' + cuenta.nombre_cuenta +
                                '</option>'
                            );
                        });

                    } else {
                        listaPadres.append('<option value="">No hay cuentas de nivel ' + (nivel - 1) + '</option>');
                    }
                },
                error: function () {
                    $('#listaPadres').html('<option value="">Error al cargar cuentas</option>');
                }
            });
        } else {
            $('#grupoCuentaPadre').hide();
            $('#listaPadres').empty();
        }
    });

    $('#listaPadres').on('change', function () {
        const selectedOption = $(this).find('option:selected');
        const naturaleza = selectedOption.data('naturaleza');

        if (naturaleza) {
            $('#naturalezaHidden').val(naturaleza); // valor para enviar al backend
            $('#naturaleza').val(naturaleza);       // valor visible
        } else {
            $('#naturalezaHidden').val('');
            $('#naturaleza').val('');
        }
        generarCodigoHija();
    });

    // GENERAR CODIGO CONTABLE HIJA
    function generarCodigoHija() {
        const codPadre = $('#listaPadres').val();
        const nivel = $('#nivel').val();

        if (codPadre && nivel > 1) {
            $.ajax({
                url: 'index.php?pagina=catalogocuentas',
                method: 'POST',
                data: {
                    cod_padre: codPadre,
                    nivel: nivel,
                    codigohija: true
                },
                success: function (codigo) {
                    $('#codigoContable').val(codigo);
                    $('#naturaleza').prop('readonly', true);
                },
                error: function () {
                    $('#codigoContable').val('Error generando código');
                }
            });
        }
    }

    //Eliminar cuenta
    $('#eliminarModal').on('show.bs.modal', function (event) {

        var button = $(event.relatedTarget);
        var nombre = button.data('nombre');
        var codigo = button.data('codigo');
        var status = button.data('status');

        var modal = $(this);
        modal.find('#nombrecuenta').text(nombre);
        modal.find('.modal-body #codigocuenta').val(codigo);
        modal.find('.modal-body #statusDelete').val(status);

    });

    //Editar cuenta
    $('#editarModal').on('show.bs.modal', function (event) {

        var button = $(event.relatedTarget);
        var nombre = button.data('nombre');
        var codigocontable = button.data('codigocontable');
        var nivel = button.data('nivel');
        var naturaleza = button.data('naturaleza');
        var saldo = button.data('saldo');
        var codpadre = button.data('cuentapadre');
        var codigo = button.data('codigo');
        var status = button.data('status');

        var modal = $(this);
        modal.find('#nombreCuentae').val(nombre);
        modal.find('.modal-body #codigoContablee').val(codigocontable);
        modal.find('.modal-body #nivele').val(nivel);
        modal.find('.modal-body #naturalezae').val(naturaleza);
        modal.find('.modal-body #naturalezah').val(naturaleza);
        modal.find('.modal-body #listaPadrese').val(codpadre);
        modal.find('.modal-body #saldoe').val(saldo);
        modal.find('.modal-body #codigocuenta').val(codigo);
        modal.find('.modal-body #statuse').val(status);
        modal.find('.modal-body #cuentaPadre').val(codpadre);

        // Evaluar si es cuenta padre o hija
        if (nivel == 1) {
            $('#naturalezae').prop('disabled', false);  // habilitado para cuentas nivel 1
        } else {
            $('#naturalezae').prop('disabled', true);   // deshabilitado para hijas
        }

        $('#nivele').prop('disabled', true); // deshabilitado para editar el nivel
    });

    // VACIAR MODAL DE REGISTRAR CUENTA
    function resetRegistrarModal() {
        const modal = $('#modalRegistrarCuenta');
        modal.find('input, textarea, select').val('');
        modal.find('.is-invalid').removeClass('is-invalid');
        modal.find('.invalid-feedback').hide();
        $('#grupoNaturaleza').hide();
        $('#grupoCuentaPadre').hide();
        $('#listaPadres').empty();
        $('#codigoContable').val('');
        $('#naturaleza').val('').prop('disabled', true);
        $('#naturalezaHidden').val('');
    }

    $('#modalRegistrarCuenta').on('hidden.bs.modal', resetRegistrarModal);

});


