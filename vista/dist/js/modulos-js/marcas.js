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
        $.post('index.php?pagina=marcas', {buscar}, function(response){
            if(response != ''){
                Swal.fire({
                    icon: 'warning',
                    title: 'Advertencia',
                    text: 'La marca ya se encuentra registrada',
                    confirmButtonText: 'Aceptar'
                });
            }
        },'json');
    });

    //Validar editar
    $('#name').blur(function (e){
        var buscar=$('#name').val();
        $.post('index.php?pagina=marcas', {buscar}, function(response){
            if(response != ''){
                Swal.fire({
                    icon: 'warning',
                    title: 'Advertencia',
                    text: 'La marca ya se encuentra registrada',
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
        modal.find('.modal-body #marcacodigo').val(codigo);
        modal.find('.modal-body #statusDelete').val(status);
        modal.find('.modal-body #marcanombre').val(nombre);

        console.log(nombre,codigo,status);
    });
// LIMPIAR MODALES AL CERRAR
$('#modalRegistrarMarca').on('hidden.bs.modal', function () {
    const form = $('#formRegistrarMarca');
    form[0].reset();
    
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').css('display', 'none');
});

$('#editModal').on('hidden.bs.modal', function () {
    const form = $('#editForm');
    form[0].reset();
    
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').css('display', 'none');
});

/* Clase electiva II let prueba1 = document.getElementById('nombre').value;
console.log(prueba1);

let nombre='Manuela Mujica'; console.log(typeof nombre);

let longitud = 5; 
if (nombre.length >= longitud){
console.log('El nombre: ' +nombre+ ' tiene la longitud requerida')}
else {
    console.log('El nombre:' +nombre+ ' NO tiene la longitud requerida')
        }

function sumar(a,b){
    if(typeof a !== 'number' || typeof b !== 'number'){
        return 'Los argumentos son validos';
    }
    return a + b;
}
console.log(sumar(8,4));
console.log(sumar(2,9));
*/
