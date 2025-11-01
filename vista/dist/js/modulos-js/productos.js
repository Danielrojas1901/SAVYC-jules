//Si ya existe un producto, tener la opcion de asignarle una presentacion
$(document).ready(function() {

    $(document).on('input', '#nombre', function() {
        var query = $(this).val(); // Valor ingresado por el usuario

        if (query.length > 2) { // Realiza la búsqueda si hay más de 2 caracteres
            $.ajax({
                url: 'index.php?pagina=productos',
                method: 'POST',
                data: { buscar: query },
                dataType: 'json',
                success: function(data) {
                    var listaProductos = $('#lista-productos'); // Elemento donde mostrar resultados
                    listaProductos.empty(); // Limpiar resultados anteriores
                    
                    //console.log("Data recibida:", data);

                    if (data.length > 0) {
                        $.each(data, function(key, producto) { //Necesario para que reconozca las variables!
                            // Crea un nuevo elemento de lista para cada producto

                            let marca = producto.marca || 'No disponible'; 
                            listaProductos.append(
                                '<a href="#" class="list-group-item list-group-item-action producto-item" style="color:#333333; font-weight:normal;"' +
                                'data-codigo="'+ producto.cod_producto +'" '+
                                'data-nombre="'+ producto.producto_nombre +'" ' +
                                'data-marca="'+ marca +'" ' +
                                'data-cod-marca="'+ producto.cod_marca +'" ' +
                                'data-categoria="'+ producto.cod_categoria +'" ' + 
                                'data-cat-nombre="'+ producto.cat_nombre +'">' + 
                                producto.producto_nombre + ' - ' + marca + ' - ' + producto.cat_nombre + '</a>'
                            );
                        });
                        listaProductos.fadeIn(); // Muestra la lista de productos
                    } else {
                        listaProductos.append('<p class="list-group-item"><b>No se encontraron productos</b></p>');
                    }
                }
            });
        } else {
            $('#lista-productos').fadeOut(); // Ocultar la lista si no hay suficientes caracteres
        }
    });

    // Cuando el usuario selecciona un producto
    $(document).on('click', '.producto-item', function(event) {
        event.preventDefault(); // Previene la acción por defecto del enlace
        var codigo = $(this).data('codigo'); 
        var nombre = $(this).data('nombre'); 
        var marca = $(this).data('marca');
        var codMarca = $(this).data('cod-marca');
        var categoriaCod = $(this).data('categoria'); 

        // Asigna los valores seleccionados a los inputs
        $('#cod_productoR').val(codigo).prop('readonly', true); 
        $('#nombre').val(nombre).prop('readonly', true); 
        $('#marca').val(codMarca).prop('readonly', true); 
        $('#categoria').val(categoriaCod).prop('readonly', true);
        
        $('#lista-productos').fadeOut();
    });

    $(document).on('blur', '#nombre', function(event) {
        $('#lista-productos').fadeOut(); 
    });

    // Botón "deshacer" para limpiar el formulario
    $('#deshacer').on('click', function() {
        // Restablece los valores y quita la propiedad readonly
        $('#cod_productoR').val('').prop('readonly', false);
        $('#nombre').val('').prop('readonly', false);
        $('#marca').val('').prop('readonly', false);
        $('#categoria').val('').prop('readonly', false);
    });

});

// VALIDAR ENTRADAS
$(document).ready(function() {
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
    // FIN FUNCIONES

    // Registrar
    $('#nombre').on('blur', function() {
        var nombre = $(this).val();
        if (nombre.trim() === '') {
            hideError('#nombre');
        }else if (nombre.length > 40) {
            showError('#nombre', 'El texto no debe exceder los 40 caracteres'); // Validar longitud máxima
        } else if (!/^[a-zA-ZÀ-ÿ0-9\s]+$/.test(nombre)) {
            showError('#nombre', 'Solo se permiten letras y números');
        } else {
            hideError('#nombre');
        }
    });
    $('#marca').on('blur', function() {
        var marca = $(this).val();
        if (marca.trim() === '') {
            hideError('#marca'); 
        }else if (marca.length > 40) {
            showError('#marca', 'El texto no debe exceder los 40 caracteres'); // Validar longitud máxima
        } else if (!/^[a-zA-ZÀ-ÿ0-9\-\s]+$/.test(marca)) {
            showError('#marca', 'Solo se permiten letras, números y (-)');
        } else {
            hideError('#marca');
        }
    });
    $('#presentacion').on('blur', function() {
        var presentacion = $(this).val();
        if (presentacion.trim() === '') {
            hideError('#presentacion');
        }else if (presentacionE.length > 30) {
            showError('#presentacion', 'El texto no debe exceder los 30 caracteres'); // Validar longitud máxima
        } else if (!/^[a-zA-ZÀ-ÿ\s]+$/.test(presentacion)) {
            showError('#presentacion', 'Solo se permiten letras');
        } else {
            hideError('#presentacion');
        }
    });

    $('#cant_presentacion').on('input', function() {
        var cant_presentacion = $(this).val();
        if (cant_presentacion.trim() === '') {
            hideError('#cant_presentacion');
        } else if (cant_presentacion.length > 20) {
            showError('#cant_presentacion', 'El texto no debe exceder los 20 caracteres'); // Validar longitud máxima
        } else if (!/^[a-zA-ZÀ-ÿ0-9\s.,]+$/.test(cant_presentacion)) { 
            showError('#cant_presentacion', 'Solo se permiten letras, números, punto (.) y coma (,)');
        } else {
            hideError('#cant_presentacion'); 
        }
    });

    $('#costo').on('input', function() {
        var costo = $(this).val();
        if (costo.trim() === '') {
            hideError('#costo'); 
        } else if (!/^\d+(\.\d{1,2})?$/.test(costo)) { // Permite números y un máximo de 2 decimales
            showError('#costo', 'Solo se permiten números y 2 decimales opcional.');
        } else {
            hideError('#costo'); 
        }
    });

    $('#porcen').on('input', function() {
        var porcen = $(this).val();
        if (porcen.trim() === '') {
            hideError('#porcen'); 
        } else if (!/^\d+$/.test(porcen)) { // Permite números enteros 
            showErrorP('#porcen', 'Solo se permiten números enteros.');
        } else {
            hideError('#porcen'); 
        }
    });
    //Fin validar registrar

    // Editar
    $('#name').on('blur', function() {
        var name = $(this).val();
        if (name.trim() === '') {
            hideError('#name');
        }else if (name.length > 40) {
            showError('#name', 'El texto no debe exceder los 40 caracteres'); 
        } else if (!/^[a-zA-ZÀ-ÿ0-9\s]+$/.test(name)) {
            showError('#name', 'Solo se permiten letras y números');
        } else {
            hideError('#name');
        }
    });
    $('#marcaE').on('blur', function() {
        var marcaE = $(this).val();
        if (marcaE.trim() === '') {
            hideError('#marcaE'); 
        }else if (marcaE.length > 40) {
            showError('#marcaE', 'El texto no debe exceder los 40 caracteres'); 
        } else if (!/^[a-zA-ZÀ-ÿ0-9\-\s]+$/.test(marcaE)) {
            showError('#marcaE', 'Solo se permiten letras, números y (-)');
        } else {
            hideError('#marcaE');
        }
    });
    $('#presentacionE').on('blur', function() {
        var presentacionE = $(this).val();
        if (presentacionE.trim() === '') {
            hideError('#presentacionE');
        } else if (presentacionE.length > 30) {
        showError('#presentacionE', 'El texto no debe exceder los 30 caracteres');
        } else if (!/^[a-zA-ZÀ-ÿ\s]+$/.test(presentacionE)) {
            showError('#presentacionE', 'Solo se permiten letras');
        } else {
            hideError('#presentacionE');
        }
    });
    
    $('#cant_presentacionE').on('input', function() {
        var cant_presentacionE = $(this).val();
        if (cant_presentacionE.trim() === '') {
            hideError('#cant_presentacionE');
        } else if (cant_presentacionE.length > 20) {
            showError('#cant_presentacionE', 'El texto no debe exceder los 20 caracteres'); // Validar longitud máxima
        } else if (!/^[a-zA-ZÀ-ÿ0-9\s.,]+$/.test(cant_presentacionE)) { 
            showError('#cant_presentacionE', 'Solo se permiten letras, números, punto (.) y coma (,)');
        } else {
            hideError('#cant_presentacionE'); 
        }
    });

    $('#costoE').on('input', function() {
        var costoE = $(this).val();
        if (costoE.trim() === '') {
            hideError('#costoE'); 
        } else if (!/^\d+(\.\d{1,2})?$/.test(costoE)) { // Permite números y un máximo de 2 decimales
            showError('#costoE', 'Solo se permiten números y 2 decimales opcional.');
        } else {
            hideError('#costoE'); 
        }
    });

    $('#porcenE').on('input', function() {
        var porcenE = $(this).val();
        if (porcenE.trim() === '') {
            hideError('#porcenE'); 
        } else if (!/^\d+$/.test(porcenE)) { // Permite números enteros     FUNCIONA PERO MUEVE EL ICONO DE %
            showErrorP('#porcenE', 'Solo se permiten números enteros.');
        } else {
            hideError('#porcenE'); 
        }
    });
    //Fin validar editar

});


// Función para calcular el precio de venta REGISTRAR
function calcularPrecioVenta(modal) {
    var valorPorcentaje = Number(modal.find('#porcen').val());
    var costo = Number(modal.find('#costo').val());
    var iva = Number(modal.find('#iva').val());

    if (!isNaN(costo) && !isNaN(valorPorcentaje)) {
        if(iva == 2){
            var costoiva = costo * 1.16;
            console.log(costoiva);
            var precioVenta = (valorPorcentaje / 100 + 1) * costoiva;
        }else{
            var precioVenta = (valorPorcentaje / 100 + 1) * costo;
        }
        modal.find('#precio').val(precioVenta.toFixed(2)); // Mostrar en el id precio el resultado obtenido con dos decimales
    } else {
        modal.find('#precio').val('0'); // Si es NaN, el precio es 0
    }
}

// Llama a la funcion de calcular para el modal de registro
$(document).on('input', '#costo, #porcen, #iva', function() {
    calcularPrecioVenta($('#modalRegistrarProducto')); 
});

// Función para calcular el precio de venta EDITAR
function calcularPrecioVentaEditar(modal) {
    var valorPorcentaje = Number(modal.find('#porcenE').val());
    var costo = Number(modal.find('#costoE').val());
    var iva = Number(modal.find('#ivaE').val());

    if (!isNaN(costo) && !isNaN(valorPorcentaje)) {
        if(iva == 2){
            var costoiva = costo * 1.16;
            //console.log(costoiva);
            var precioVenta = (valorPorcentaje / 100 + 1) * costoiva;
        }else{
            var precioVenta = (valorPorcentaje / 100 + 1) * costo;
        }
        modal.find('#precioE').val(precioVenta.toFixed(2)); // Mostrar en el id precio el resultado obtenido con dos decimales
    } else {
        modal.find('#precioE').val('0'); // Si es NaN, el precio es 0
    }
}

// Llamar a la funcion de calcular para el modal de edicion
$(document).on('input', '#costoE, #porcenE, #ivaE', function() {
    calcularPrecioVentaEditar($('#editModal')); 
});

// NUEVA CATEGORIA DESDE PRODUCTO
//(Validar nombre)
    $('#nombrec').blur(function (e){
        var buscar=$('#nombrec').val();
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

$(document).ready(function() {
    // Verifica si el valor 'categoriaModal' está en localStorage
    if (localStorage.getItem('categoriaModal') === 'true') {
        $('#modalRegistrarProducto').modal('show');
        localStorage.removeItem('categoriaModal');
    }
});

// NUEVA MARCA DESDE PRODUCTO
//(Validar nombre)
$('#nombrem').blur(function (e){
    var buscar=$('#nombrem').val();
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

$(document).ready(function() {
// Verifica si el valor 'categoriaModal' está en localStorage
if (localStorage.getItem('marcaModal') === 'true') {
    $('#modalRegistrarProducto').modal('show');
    localStorage.removeItem('marcaModal');
}
});

//NUEVA UNIDAD DESDE PRODUCTO
//(Validar nombre)
$('#tipo_medidau').blur(function (e){
    var buscar=$('#tipo_medidau').val();
    $.post('index.php?pagina=unidad', {buscar}, function(response){
        if(response != ''){
            Swal.fire({
                icon: 'warning',
                title: 'Advertencia',
                text: 'La unidad de medida ya se encuentra registrada',
                confirmButtonText: 'Aceptar'
            });
        }
    },'json');
});

$(document).ready(function() {
    // Verifica si el valor 'unidadModal' está en localStorage
    if (localStorage.getItem('unidadModal') === 'true') {
        $('#modalRegistrarProducto').modal('show');
        localStorage.removeItem('unidadModal');
    }
});

//Modal detalle
$(document).ready(function() {
    // Evento al abrir el modal
    $('#detallemodal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget); // Botón que abrió el modal
        var codigoProducto = button.data('codigo'); // Extraer el cod_presentacion
        var np = button.data('nombrep');
        var pp = button.data('presentp');

        var modal = $(this);
        modal.find('.modal-body #nombreproducto').val(np);
        modal.find('.modal-body #presentproducto').val(pp);
        
        console.log(codigoProducto);
        // Limpiar la tabla de detalles antes de cargar nuevos datos
        $('#detalleBody').empty();

        // Hacer una llamada AJAX para obtener los detalles del producto
        $.ajax({
            url: 'index.php?pagina=productos',
            method: 'POST',
            data: { detallep: codigoProducto },
            dataType: 'json',
            success: function(data) {
                
                // Verificar si hay datos en la respuesta
                console.log(data);
                if (data.length === 0) {
                    // Si no hay detalles mostrar un mensaje 
                    $('#detalleBody').append(
                        '<tr>' +
                            '<td colspan="5" class="text-center">No hay detalles disponibles para este producto</td>' +
                        '</tr>'
                    );
                } else {
                // Recorrer los datos devueltos y llenar la tabla
                $.each(data, function(index, detalle) {
                    let lote = detalle.lote || 'No disponible';
                    let fechaVencimiento = detalle.fecha_vencimiento; //No esta funcionando  || 'No disponible'

                        $('#detalleBody').append(
                            '<tr>' +
                                '<td>' + detalle.cod_detallep + '</td>' +
                                '<td>' + lote + '</td>' +
                                '<td>' + fechaVencimiento + '</td>' +
                                '<td>' + detalle.stock + '</td>' +
                            '</tr>'
                        );
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar los detalles:', error);
            }
        });
    });
});

/*Botón de eliminar detalle
$(document).on('click', '.eliminarDetalle', function() {
    var codigoDetalle = $(this).data('codigo');
    var statusDetalle = $(this).closest('tr').find('.status').data('status');  
    var stockDetalle = $(this).closest('tr').find('.stock').text();

    // Convertir stock a número para comparación
    stockDetalle = parseInt(stockDetalle, 10);

    // Validar status y stock
    if (statusDetalle == 1 || stockDetalle > 0) { //2 es inactivo y 1 es activo en status
        Swal.fire({
            icon: 'warning',
            title: 'Error al eliminar',
            text: 'Solo se puede eliminar si tiene status inactivo y stock 0',
            confirmButtonText: 'Aceptar'
        });
        return;
    }else {
        // Si cumple las condiciones, abrir el modal de confirmación
        $('#eliminarDetalleModal').modal('show');
            $('#confirmarEliminarDetalle').off('click').on('click', function() {

                // Realizar la solicitud AJAX para eliminar
                $.ajax({
                    url: 'index.php?pagina=productos', 
                    method: 'POST',
                    data: { codigo: codigoDetalle },
                    success: function(response) {
                        $('#eliminarDetalleModal').modal('hide');
                        if (response.status === 'success') {
                            $('#eliminarDetalleModal').modal('hide');
                            
                            Swal.fire({
                                icon: 'success',
                                title: 'Eliminación exitosa',
                                text: response.message,
                                confirmButtonText: 'Aceptar'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    location.reload(); 
                                }
                            });
                        } else {
                            $('#eliminarDetalleModal').modal('hide');
                            
                            Swal.fire({
                                icon: 'error',
                                title: 'Error al eliminar',
                                text: response.message,
                                confirmButtonText: 'Aceptar'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error al eliminar el detalle:', error);
                        // Error en caso de fallo en AJAX
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Ocurrió un error al intentar eliminar el detalle.',
                            confirmButtonText: 'Aceptar'
                        });
                    }
                });
            });
        }
    });
*/
//Editar modal
$('#editModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    
    var imagenSrc = button.closest('tr').find('img').attr('src') || 'vista/dist/img/productos/default.png';
    

    $(this).find('.modal-body img').attr('src', imagenSrc);
    $(this).find('input[name="imagenActual"]').val(imagenSrc);

    var codigo = button.data('codigo');
    var nombre = button.data('nombre');
    var marca = button.data('marca');
    var unidad = button.data('unidad');
    var present = button.data('present');
    var cantpresent = button.data('cantpresent');
    var categoria = button.data('categoria');
    var costo = button.data('costo');
    var iva = button.data('iva');
    var porcen = button.data('porcen');


    console.log(imagenSrc, marca)

    // Modal
    var modal = $(this); 
    modal.find('.modal-body #cod_presentacion').val(codigo);
    modal.find('.modal-body #name').val(nombre);
    modal.find('.modal-body #marcaE').val(marca);
    modal.find('.modal-body #unidadE').val(unidad);
    modal.find('.modal-body #presentacionE').val(present);
    modal.find('.modal-body #cant_presentacionE').val(cantpresent);
    modal.find('.modal-body #categoriaE').val(categoria);
    modal.find('.modal-body #costoE').val(costo);
    modal.find('.modal-body #ivaE').val(iva);
    modal.find('.modal-body #porcenE').val(porcen);

    modal.find('.modal-body #cod_producto').val(button.data('producto'));

    calcularPrecioVentaEditar(modal);
});

$(document).on('change', '#editModal input[type="file"]', function(e) {
    var input = e.target;
    var modal = $(this).closest('.modal');
    
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        
        reader.onload = function(e) {
            modal.find('.modal-body img').attr('src', e.target.result);
        }
        
        reader.readAsDataURL(input.files[0]);
    }
});

//Eliminar
$('#eliminarModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget); 
    var codigo = button.data('codigo');
    var nombre = button.data('nombre');        

    var modal = $(this);
    modal.find('#p_nombre').text(nombre);
    modal.find('.modal-body #present_codigo').val(codigo);
    modal.find('.modal-body #p_codigo').val(button.data('producto'));

    console.log('Código presentacion:', codigo);
    console.log('Codigo producto:', button.data('producto'));
});

// Función para redimensionar imágenes
function redimensionarImagen(archivo, anchoMaximo, altoMaximo) {
    return new Promise((resolve) => {
        const reader = new FileReader();
        reader.readAsDataURL(archivo);
        reader.onload = function(event) {
            const img = new Image();
            img.src = event.target.result;
            img.onload = function() {
                const canvas = document.createElement('canvas');
                let width = img.width;
                let height = img.height;

                if (width > height) {
                    if (width > anchoMaximo) {
                        height = Math.round((height * anchoMaximo) / width);
                        width = anchoMaximo;
                    }
                } else {
                    if (height > altoMaximo) {
                        width = Math.round((width * altoMaximo) / height);
                        height = altoMaximo;
                    }
                }

                canvas.width = width;
                canvas.height = height;
                const ctx = canvas.getContext('2d');
                ctx.drawImage(img, 0, 0, width, height);

                canvas.toBlob((blob) => {
                    const archivoRedimensionado = new File([blob], archivo.name, {
                        type: archivo.type,
                        lastModified: Date.now()
                    });
                    
                    resolve(archivoRedimensionado);
                }, archivo.type);
            };
        };
    });
}


$('#formRegistrarProducto').on('submit', async function(e) {
    e.preventDefault();
    const imagenInput = document.querySelector('input[name="imagen"]');
    
    if (imagenInput.files.length > 0) {
        const archivo = imagenInput.files[0];
        try {
            const archivoRedimensionado = await redimensionarImagen(archivo, 1000, 1000);
            
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(archivoRedimensionado);
            imagenInput.files = dataTransfer.files;
            
        } catch (error) {
            console.error('Error al redimensionar la imagen:', error);
        }
    }
    
    const guardarInput = document.createElement('input');
    guardarInput.type = 'hidden';
    guardarInput.name = 'guardar';
    guardarInput.value = '1';
    this.appendChild(guardarInput);
    
    this.submit();
});

$('#editForm').on('submit', async function(e) {
    e.preventDefault();
    const imagenInput = document.querySelector('input[name="imagenE"]');
    
    if (imagenInput.files.length > 0) {
        const archivo = imagenInput.files[0];
        try {
            const archivoRedimensionado = await redimensionarImagen(archivo, 1000, 1000);
            
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(archivoRedimensionado);
            imagenInput.files = dataTransfer.files;
            
        } catch (error) {
            console.error('Error al redimensionar la imagen:', error);
        }
    }
    
    const editarInput = document.createElement('input');
    editarInput.type = 'hidden';
    editarInput.name = 'editar';
    editarInput.value = '1';
    this.appendChild(editarInput);
    
    this.submit();
});

$('#modalRegistrarProducto').on('hidden.bs.modal', function () {
    const form = $('#formRegistrarProducto');
    form[0].reset();
    
    $('#cod_productoR').val('').prop('readonly', false);
    $('#nombre').val('').prop('readonly', false);
    $('#marca').val('').prop('readonly', false);
    $('#categoria').val('').prop('readonly', false);
    
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').css('display', 'none');
    
    $('#lista-productos').empty().hide();
    
    const imagenInput = form.find('input[type="file"]');
    if (imagenInput.length) {
        imagenInput.val('');
    }
    
    $('#precio').val('');
});

$('#editModal').on('hidden.bs.modal', function () {
    const form = $('#editForm');
    form[0].reset();
    
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').css('display', 'none');
    
    const imagenInput = form.find('input[type="file"]');
    if (imagenInput.length) {
        imagenInput.val('');
    }

    $('#precioE').val('');
    
    form.find('img').attr('src', '');
});

