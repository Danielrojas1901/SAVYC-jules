console.log('abrio js bancos');

$(document).ready(function () {

    // =============================
    // VALIDACIONES DE FORMULARIOS
    // =============================

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

    // Validación de nombre de banco en el registro
    $('#nombre').on('input', function () {
        const nombre = $(this).val();
        if (!/^[a-zA-Z\s]+$/.test(nombre) || nombre.length > 50) {
            showError('#nombre', 'Debe contener solo letras y máximo 50 caracteres');
        } else {
            hideError('#nombre');
        }
    });

    // Validación de nombre de banco en la edición
    $('#nombre1').on('input', function () {
        const nombre = $(this).val();
        if (!/^[a-zA-Z\s]+$/.test(nombre) || nombre.length > 50) {
            showError('#nombre1', 'Debe contener solo letras y máximo 50 caracteres');
        } else {
            hideError('#nombre1');
        }
    });

    // =============================
    // EDITAR BANCO
    // =============================
    $('#editModal').on('show.bs.modal', function (event) {
        console.log('EDITAR banco');

        const button = $(event.relatedTarget);
        const codigo = button.data('codigo');
        const nombre = button.data('nombre');

        const modal = $(this);
        modal.find('.modal-body #nombre1').val(nombre);
        modal.find('.modal-body #origin').val(nombre);
        modal.find('.modal-body #codigo').val(codigo); 
    });

    // =============================
    // ELIMINAR BANCO
    // =============================
    $('#eliminarModal').on('show.bs.modal', function (event) {
        console.log('ELIMINAR banco');

        const button = $(event.relatedTarget);
        const codigo = button.data('codigo');
        const nombre = button.data('nombre');

        const modal = $(this);
        modal.find('#bancoNombre').text(nombre);
        modal.find('.modal-body #bancoCodigo').val(codigo);
    });

    // =============================
    // VERIFICAR SI BANCO YA EXISTE
    // =============================


    $('#nombre').blur(function () {
        var nombre = $(this).val().trim();

        if (nombre.length > 0) {
            $.post('index.php?pagina=banco', { validar_banco: nombre }, function (response) {
                if (response === 'true') {
                    Swal.fire({
                        title: 'Advertencia',
                        text: 'El banco ya se encuentra registrado',
                        icon: 'warning'
                    });
                    $('#nombre').addClass('is-invalid');
                } else {
                    $('#nombre').removeClass('is-invalid');
                }
            });
        }
    });
});

$('#modalRegistrarbanco').on('hidden.bs.modal', function () {
    
    $('#formRegistrarbanco')[0].reset();

  
    $('#formRegistrarbanco .is-invalid').removeClass('is-invalid');
    $('#formRegistrarbanco .invalid-feedback').hide();

});
