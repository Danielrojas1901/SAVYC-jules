//Validar entradas
$(document).ready(function(){
    //Funciones
    function showError(selector,message){
        $(selector).addClass('is-invalid');
        $(selector).next('.invalid-feedback').html('<i class="fas fa-exclamation-triangle"></i> ' + message.toUpperCase()).css({
            'display':'block',
            'color':'red',
        });
    }
    function hideError(selector){
        $(selector).removeClass('is-invalid');
        $(selector).next('.invalid-feedback').css('display','none');
    }

    // Registrar
    $('#nombre').on('blur', function() {
        var nombre = $(this).val();
        if (nombre.trim() === '') {
            hideError('#nombre');
        } else if (nombre.length > 30) {
            showError('#nombre', 'El texto no debe exceder los 30 caracteres'); 
        } else if (!/^[a-zA-ZÀ-ÿ\s]+$/.test(nombre)) {
            showError('#nombre', 'Solo letras');
        } else {
            hideError('#nombre');
        }
    });

    // Editar
    $('#name').on('blur', function() {
        var name = $(this).val();
        if (name.trim() === '') {
            hideError('#name'); 
        } else if (name.length > 30) {
            showError('#name', 'El texto no debe exceder los 30 caracteres');
        } else if (!/^[a-zA-ZÀ-ÿ\s]+$/.test(name)) {
            showError('#name', 'Solo letras');
        } else {
            hideError('#name');
        }
    });
});

    //Validar registrar
    $('#nombre').blur(function (e){
        var buscar=$('#nombre').val();
        $.post('index.php?pagina=categorias', {buscar}, function(response){
            if(response != ''){
                Swal.fire({
                    icon: 'warning',
                    title: 'Advertencia',
                    text: 'La categoria ya se encuentra registrada',
                    confirmButtonText: 'Aceptar'
                });
            }
        },'json');
    });

    //Validar editar
    $('#name').blur(function (e){
        var buscar=$('#name').val();
        $.post('index.php?pagina=categorias', {buscar}, function(response){
            if(response != ''){
                Swal.fire({
                    icon: 'warning',
                    title: 'Advertencia',
                    text: 'La categoria ya se encuentra registrada',
                    confirmButtonText: 'Aceptar'
                });
            }
        },'json');
    });

    //EDITAR
    $('#editModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var codigo = button.data('codigo');
            var nombre = button.data('nombre');
            var status = button.data('status');
            var origin = button.data('nombre');

            // Modal
            var modal = $(this); 
            modal.find('.modal-body #codigo').val(codigo);
            modal.find('.modal-body #name').val(nombre);
            modal.find('.modal-body #status').val(status);
            modal.find('.modal-body #origin').val(origin);
        });

    //ELIMINAR
    $('#eliminarModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); 
        var nombre = button.data('nombre');
        var codigo = button.data('codigo');
        var status = button.data('status');

        var modal = $(this);
        modal.find('#catnombre').text(nombre);
        modal.find('.modal-body #catcodigo').val(codigo);
        modal.find('.modal-body #statusDelete').val(status);

        console.log(nombre,codigo,status);
    });

