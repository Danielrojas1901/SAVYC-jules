console.log('Abrio proveedores.js');


// VALIDAR RIF 
$('#rif').blur(function (e) {
    var buscar = $('#rif').val();
    $.post('index.php?pagina=proveedores', { buscar }, function (response) {
        if (response != '') {
            Swal.fire({
                title: 'Error',
                text: 'El Proveedor  ya se encuentra registrado.',
                icon: 'error'
            });
        }
    }, 'json');
});
//VALIDAR RIF
$('#rif1').blur(function (e) {
    var buscar = $('#rif1').val();
    $.post('index.php?pagina=proveedores', { buscar }, function (response) {
        if (response != '') {
            Swal.fire({
                title: 'Error',
                text: 'El Rif ya  existe.',
                icon: 'error'
            });
        }
    }, 'json');
});

//validar telefono 

$('#telefono2').blur(function (e) {
    var buscar = $('#telefono2').val();
    $.post('index.php?pagina=tproveedores', { buscar }, function (response) {
        if (response != '') {
            Swal.fire({
                title: 'Error',
                text: 'La telefono Ya Existe.',
                icon: 'error'
            });
        }
    }, 'json');
});


//VALIDAR CEDULA
$('#cedula').blur(function (e) {
    var buscar = $('#cedula').val();
    $.post('index.php?pagina=representantes', { buscar }, function (response) {
        if (response != '') {
            Swal.fire({
                title: 'Error',
                text: 'El Representante  ya se encuentra registrado.',
                icon: 'error'
            });
        }
    }, 'json');
});

//VALIDAR CEDULA

$('#cedula3').blur(function (e) {
    var buscar = $('#cedula3').val();
    $.post('index.php?pagina=representantes', { buscar }, function (response) {
        if (response != '') {
            Swal.fire({
                title: 'Error',
                text: 'La Cedula Ya Existe.',
                icon: 'error'
            });
        }
    }, 'json');
});

// EDITAR PROVEEDOR
$('#modalProvedit').on('show.bs.modal', function(event) {
    var button = $(event.relatedTarget);
    var cod = button.data('cod');
    var rif = button.data('rif');
    var razon = button.data('razon');
    var email = button.data('email');
    var dire = button.data('dire');
    var status = button.data('status');
    var telefono = button.data('telefono');
    var origin = button.data('rif');
    var cod_tlf = button.data('cod_tlf');

    // Modal
    var modal = $(this);
    modal.find('.modal-body #cod_oculto').val(cod);
    modal.find('.modal-body #cod').val(cod);
    modal.find('.modal-body #rif1').val(rif);
    modal.find('.modal-body #razon1').val(razon);
    modal.find('.modal-body #email1').val(email);
    modal.find('.modal-body #dire1').val(dire);
    modal.find('.modal-body #status').val(status);
    modal.find('.modal-body #telefono3').val(telefono);
    modal.find('.modal-body #origin').val(origin);
    modal.find('.modal-body #cod_tlf').val(cod_tlf);
});

//REPRESENTANTE REGISTRAR
$(document).ready(function () {
    $('#registrarRepresentante').on('show.bs.modal', function(event){

        var button = $(event.relatedTarget);
        var cod_provREPRE = button.data('codigoproveedor'); 

        // Modal
        var modal = $(this);
        modal.find('.modal-body #cod_provREPRE').val(cod_provREPRE); 

        console.log(cod_provREPRE);
    });


    $('#myModalr').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        var cod = button.data('cod');
        var nombre = button.data('nombre');
        var cedula = button.data('cedula');
        var apellido = button.data('apellido');
        var telefono = button.data('teler');
        var status1 = button.data('status1');

        // Modal
        var modal = $(this);
        modal.find('.modal-body #cod').val(cod);
        modal.find('.modal-body #cedula').val(cedula);
        modal.find('.modal-body #nombre').val(nombre);
        modal.find('.modal-body #apellido').val(apellido);
        modal.find('.modal-body #rep_tel').val(telefono);
        modal.find('.modal-body #statusr').val(status1);

        modal.find('button[name="eliminar"]').attr({
            'data-codigo': cod,
            'data-nombre': nombre + ' ' + apellido
        });
    });

    $('#Modalel').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        var cod = button.data('codigo');
        var nombre = button.data('nombre');
        
        var modal = $(this);
        modal.find('#repreNombre').text(nombre);
        modal.find('#reprCodigo').val(cod);
    });

    $('#formrepresentreselim').on('submit', function(e) {
        if (!$('#reprCodigo').val()) {
            e.preventDefault();
            console.error('Error: No se encontró el código del representante');
            Swal.fire({
                title: 'Error',
                text: 'No se pudo encontrar el código del representante',
                icon: 'error'
            });
            return false;
        }
    });

    $('#modalProveditr').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        var cod = button.data('codigo');
        var cedula = button.data('cedula');
        var nombre = button.data('nombre');
        var apellido = button.data('apellido');
        var telefono = button.data('teler');
        var status1 = button.data('status1');
        
        var modal = $(this);
        modal.find('.modal-body #codigo').val(cod);
        modal.find('.modal-body #cod_oculto').val(cod);
        modal.find('.modal-body #cedula3').val(cedula);
        modal.find('.modal-body #nombre3').val(nombre);
        modal.find('.modal-body #apellido3').val(apellido);
        modal.find('.modal-body #reptel').val(telefono);
        modal.find('.modal-body #status1').val(status1);
        modal.find('.modal-body #origin').val(cedula);

        $('#editButton').prop('disabled', false);
    });
});



// REGISTRAR TELEFONO A PROVEEDOR
$('#myModalt').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget); // Botón que abrió el modal
    var cod1 = button.data('cod1'); // Extraer datos de los atributos data-*
    var rif = button.data('rif');

    var modal = $(this);
    modal.find('.modal-body #cod1').val(cod1); // Asignar valores a los campos del modal
    modal.find('.modal-body #rif').val(rif);
});




//ELIMINAR PROVEEDOR
$('#Modale').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var codigo = button.data('codigo');
    var np = button.data('nombreproveedor');

    var modal = $(this);
    modal.find('.modal-body #provCodigo').val(codigo);
    modal.find('.modal-body #nombreprove').text(np);
});



//editar representante
$(document).ready(function() {
    // Función para validar Cédula representante
    function validateCedula() {
        var cedula = $('#cedula3').val();
        if (!cedula || !/^[\d\s()+-]*$/.test(cedula) || cedula.length < 5 || cedula.length > 8) {
            showError('#cedula3', 'Debe contener máximo 8 caracteres, solo números y signos permitidos');
            return false;
        } else {
            hideError('#cedula3');
            return true;
        }
    }

    // Función para validar Nombre
    function validateNombre() {
        var nombre = $('#nombre3').val();
        if (!nombre || !/^[a-zA-Z\s]+$/.test(nombre) || nombre.length < 4 || nombre.length > 20) {
            showError('#nombre3', 'Debe contener máximo 20 letras y no estar vacío');
            return false;
        } else {
            hideError('#nombre3');
            return true;
        }
    }

    // Función para validar Apellido (opcional)
    function validateApellido() {
        var apellido = $('#apellido3').val();
        if (apellido && (!/^[a-zA-Z\s]+$/.test(apellido) || apellido.length < 4 || apellido.length > 20)) {
            showError('#apellido3', 'Debe contener máximo 20 letras');
            return false;
        } else {
            hideError('#apellido3');
            return true;
        }
    }

    // Función para validar Teléfono (opcional)
    function validateTelefono() {
        var telefono = $('#reptel').val();
        if (telefono && (!/^[\d\s()+-]*$/.test(telefono) || telefono.length < 6 || telefono.length > 12)) {
            showError('#reptel', 'Debe contener máximo 12 caracteres, solo números y signos permitidos');
            return false;
        } else {
            hideError('#reptel');
            return true;
        }
    }

    function showError(selector, message) {
        $(selector).addClass('is-invalid');
        $(selector).next('.invalid-feedback').html('<i class="fas fa-exclamation-triangle"></i> ' + message.toLowerCase()).css({
            'display': 'block',
            'color': 'red',
            'background-color': 'white'
        });
    }

    function hideError(selector) {
        $(selector).removeClass('is-invalid');
        $(selector).next('.invalid-feedback').css('display', 'none');
    }

    // Función para habilitar o deshabilitar el botón de editar
    function toggleEditButton() {
        var isCedulaValid = validateCedula();
        var isNombreValid = validateNombre();
        var isStatusValid = $('#status1').val() !== '';

        // Habilitar el botón solo si todas las validaciones son verdaderas
        $('#editButton').prop('disabled', !(isCedulaValid && isNombreValid && isStatusValid));
    }

    // Asignar eventos de blur para validar al salir del campo
    $('#cedula3').on('blur', toggleEditButton);
    $('#nombre3').on('blur', toggleEditButton);
    $('#apellido3').on('blur', toggleEditButton);
    $('#reptel').on('blur', toggleEditButton);
    $('#status1').on('change', toggleEditButton); // Validar cuando se cambie el estado
});
    //validar tu  refistro de proveedores 
  

        $('#rif').on('blur', function() {
            var rif = $(this).val();
            if (rif.trim() === '') {
                showError('#rif', 'El campo RIF no puede estar vacío');
            } else if (!/^[a-zA-Z0-9\s\-\.\/]{4,12}$/.test(rif)) {
                showError('#rif', 'El RIF debe tener  maximo y 12 caracteres, incluyendo letras, números .');
            } else {
                hideError('#rif');
            }
        });

        $('#razon_social').on('blur', function() {
            var razon_social = $(this).val();
            if (razon_social.trim() === '') {
                showError('#razon_social', 'El campo razón social no puede estar vacío');
            } else if (!/^[a-zA-Z0-9\s\-\.\/]{6,30}$/.test(razon_social)) {
                showError('#razon_social', 'La razón social debe tener maximo y 30 caracteres, incluyendo letras.');
            } else {
                hideError('#razon_social');
            }
        });

        $('#direccion').on('blur', function() {
            var direccion = $(this).val();
            if (direccion.length < 6 || direccion.length > 100) {
                showError('#direccion', 'La dirección debe tener maximo y 100 caracteres.');
            } else {
                hideError('#direccion');
            }
        });

        $('#email').on('blur', function() {
            var email = $(this).val();
            if (!/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/.test(email) || email.length < 10 || email.length > 40) {
                showError('#email', 'El email debe ser válido y tener maximo 40 caracteres.');
            } else {
                hideError('#email');
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
  


    // refistro de reprsentante  -->

    $(document).ready(function() {
        // Validación de Cédula
        $('#cedula').on('blur', function() {
            var cedula = $(this).val();
            // Permite de 5 a 12 caracteres (números y caracteres especiales permitidos)
            if (!/^[\d\s().\-\/]{7,8}$/.test(cedula)) {
                showError('#cedula', 'La cédula debe tener solo números maximo 8 caracteres');
            } else {
                hideError('#cedula');
            }
        });

        // Validación de Nombre
        $('#nombre').on('blur', function() {
            var nombre = $(this).val();
            // Longitud mínima de 4 y máxima de 20 (solo letras y espacios)
            if (!/^[a-zA-Z\s]{4,20}$/.test(nombre)) {
                showError('#nombre', 'El nombre debe tener solo letras maximo 20 caracteres');
            } else {
                hideError('#nombre');
            }
        });

        // Validación de Apellido
        $('#apellido').on('blur', function() {
            var apellido = $(this).val();
            // Longitud mínima de 4 y máxima de 20 (solo letras y espacios)
            if (!/^[a-zA-Z\s]{4,20}$/.test(apellido)) {
                showError('#apellido', 'El apellido debe tener solo letras maximo 20 caracteres');
            } else {
                hideError('#apellido');
            }
        });

        // Validación de Teléfono
        $('#telefono').on('change', function() {
            var telefono = $(this).val();
            // Permite entre 6 y 12 caracteres (números y caracteres especiales permitidos)
            if (!/^[\d\s().\-\/]{6,12}$/.test(telefono)) {
                showError('#telefono', 'El teléfono debe tener solo números maximo 12 caracteres');
            } else {
                hideError('#telefono');
            }
        });

        // Función para mostrar el error
        function showError(selector, message) {
            $(selector).addClass('is-invalid');
            $(selector).next('.invalid-feedback').html('<i class="fas fa-exclamation-triangle"></i> ' + message.toLowerCase()).css({
                'display': 'block',
                'color': 'red',
                'background-color': 'white'
            });
        }

        // Función para ocultar el error
        function hideError(selector) {
            $(selector).removeClass('is-invalid');
            $(selector).next('.invalid-feedback').css('display', 'none');
        }
    });

    // validar  refistro de reprsentante  -->


   // validar  telefono de proveedor   -->
    $(document).ready(function() {
    $('#telefono2').on('blur', function() {
        var telefono = $('#telefono2').val().trim();
        // Limpiar el mensaje de error
        $('.invalid-feedback').text('').hide();
        $('#telefono2').removeClass('is-invalid');

        // Validar que el teléfono no esté vacío
        if (telefono === '') {
            $('#telefono2').addClass('is-invalid');
            $('.invalid-feedback').text('El teléfono es requerido').show();
        } else if (!/^[\d\s().\-\/+]*$/.test(telefono)) {
            $('#telefono2').addClass('is-invalid');
            $('.invalid-feedback').text('El teléfono solo puede contener números, ').show();
        } else if (telefono.length < 10 || telefono.length > 12) {
            $('#telefono2').addClass('is-invalid');
            $('.invalid-feedback').text('El teléfono debe tener maximo y 12 caracteres.').show();
        } else {
            $('#telefono2').removeClass('is-invalid');
            $('.invalid-feedback').hide();
        }
    });
});

    //validar  telefono de proveedor   -->

//validar  editar proveedor   -->
$(document).ready(function() {
    // Función para validar los campos
    function validarField(campo, regex, mensaje, minLength, maxLength, allowEmpty = false) {
        var valor = campo.val().trim();
        if (allowEmpty && valor === '') {
            hideError(campo); // Si el campo es opcional y está vacío, no muestra error
        } else if (valor === '') {
            showError(campo, 'Este campo no puede estar vacío');
        } else if (valor.length < minLength || valor.length > maxLength) {
            showError(campo, mensaje);
        } else if (!regex.test(valor)) {
            showError(campo, mensaje);
        } else {
            hideError(campo);
        }
        toggleEditButton();
    }

    // Función para mostrar el error
    function showError(selector, mensaje) {
        selector.addClass('is-invalid');
        selector.next('.invalid-feedback').html('<i class="fas fa-exclamation-triangle"></i> ' + mensaje).css({
            'display': 'block',
            'color': 'red',
            'background-color': 'transparent'
        });
    }

    function hideError(selector) {
        selector.removeClass('is-invalid');
        selector.next('.invalid-feedback').css('display', 'none');
    }

    // Función para habilitar o deshabilitar el botón de editar
    function toggleEditButton() {
        var isRIFValid = $('#rif1').val().length > 0 && $('#rif1').val().length <= 15;
        var isRazonValid = $('#razon1').val().length > 0 && $('#razon1').val().length <= 30;
        var isEmailValid = $('#email1').val() === '' || /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/.test($('#email1').val());
        var isStatusValid = $('#status').val() !== '';

        // Habilitar el botón solo si todas las validaciones son verdaderas
        if (isRIFValid && isRazonValid && isEmailValid && isStatusValid) {
            $('#editButtonn').prop('disabled', false);
        } else {
            $('#editButtonn').prop('disabled', true);
        }
    }

    $('#rif1').on('blur', function() {
        validarField($(this), /^[a-zA-Z0-9]+$/, 'El RIF debe contener entre 1 y 15 caracteres (letras y números)', 1, 15);
    });

    $('#razon1').on('blur', function() {
        validarField($(this), /^[a-zA-Z0-9\s\.,\-#]+$/, 'La razón social debe contener entre 2 y 50 caracteres (letras, números y signos permitidos)', 2, 50);
    });

    $('#email1').on('blur', function() {
        validarField($(this), /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/, 'El campo email debe ser un email válido', 1, 100, true);
    });

    $('#dire1').on('blur', function() {
        validarField($(this), /^[a-zA-Z0-9\s\.,]*$/, 'La dirección solo debe contener letras, números y signos permitidos', 0, 100, true);
    });

    $('#status').on('change', function() {
        toggleEditButton();
    });
});