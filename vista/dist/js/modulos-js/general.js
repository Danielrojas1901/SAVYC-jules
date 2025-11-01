console.log('Abrio general.js');


/*$('#registrar').click(function (e) {
    var buscar = 'true';
    $.post('index.php?pagina=general', { buscar }, function (response) {
        if (response.total > 0) {
            alert('Los datos de la empresa ya estan registrados');
        }
    }, 'json');
});*/

$(document).ready(function () {
    //Editar modal
    $('#editModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var rif = button.data('rif');
        var cod = button.data('cod');
        var rs = button.data('rs');
        var direc = button.data('direc');
        var tlf = button.data('tlf');
        var email = button.data('email')
        var des = button.data('des');


        // Modal
        var modal = $(this);
        modal.find('.modal-body #rifE').val(rif);
        modal.find('.modal-body #rsE').val(rs);
        modal.find('.modal-body #direcE').val(direc);
        modal.find('.modal-body #tlfE').val(tlf);
        modal.find('.modal-body #emailE').val(email);
        modal.find('.modal-body #desE').val(des);
        modal.find('.modal-body #cod').val(cod);

    });
});

//VALIDACIONES
$(document).ready(function () {

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

//Registrar
    $('#rif').on('blur', function () {
        var rif = $(this).val();
        if (rif.trim() === '') {
            hideError('#rif');
        } else if(rif.length > 15){
            showError('#rif','El texto no debe exceder los 15 caracteres');
        } else if (!/^[a-zA-ZÀ-ÿ0-9\-_\/.]+$/.test(rif)) {
            showError('#rif', 'solo letras, números y caracteres "-" "_" "." "/" permitidos');
        } else {
            hideError('#rif');
        }
    });

    $('#nombre').on('blur', function () {
        var nombre = $(this).val();
        if (nombre.trim() === '') {
            hideError('#nombre');
        } else if(nombre.length > 50){
            showError('#nombre','El texto no debe exceder los 50 caracteres');
        } else if (!/^[a-zA-ZÀ-ÿ0-9\-_\/. ]+$/.test(nombre)) {
            showError('#nombre', 'solo letras, números y caracteres "-" "_" "." "/" permitidos');
        } else {
            hideError('#nombre');
        }
    });

    $('#direccion').on('blur', function () {
        var direccion = $(this).val();
        if (direccion.trim() === '') {
            hideError('#direccion');
        } else if(direccion.length > 100){
            showError('#direccion','El texto no debe exceder los 100 caracteres');
        } else if (!/^[a-zA-ZÀ-ÿ0-9\-_\/. ]+$/.test(direccion)) {
            showError('#direccion', 'solo letras, números y caracteres "-" "_" "." "/" permitidos');
        } else {
            hideError('#direccion');
        }
    });

    $('#telefono').on('blur', function () {
        var telefono = $(this).val();
        if (telefono.trim() === '') {
            hideError('#telefono');
        } else if(telefono.length > 15){
            showError('#telefono','El texto no debe exceder los 15 caracteres');
        } else if (!/^[0-9\-\/]+$/.test(telefono)) {
            showError('#telefono', 'solo números y caracteres "-" "/" permitidos');
        } else {
            hideError('#telefono');
        }
    });

    $('#email').on('blur', function () {
        var email = $(this).val();
        if (email.trim() === '') {
            hideError('#email');
        } else if(email.length > 70){
            showError('#email','El texto no debe exceder los 70 caracteres');
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) { //No funciona 13/11
            showError('#email', 'por favor ingresa un correo electrónico válido');
        } else {
            hideError('#email');
        }
    });

    $('#descripcion').on('blur', function () {
        var descripcion = $(this).val();
        if (descripcion.trim() === '') {
            hideError('#descripcion');
        } else if(descripcion.length > 100){
            showError('#descripcion','El texto no debe exceder los 100 caracteres');
        } else if (!/^[a-zA-ZÀ-ÿ\-_\/. ]+$/.test(descripcion)) {
            showError('#descripcion', 'solo letras y caracteres "-" "_" "." "/" permitidos');
        } else {
            hideError('#descripcion');
        }
    });

//Editar

    $('#rifE').on('blur', function () {
        var rifE = $(this).val();
        if (rifE.trim() === '') {
            hideError('#rifE');
        } else if(rifE.length > 15){
            showError('#rifE','El texto no debe exceder los 15 caracteres');
        } else if (!/^[a-zA-ZÀ-ÿ0-9\-_\/.]+$/.test(rifE)) {
            showError('#rifE', 'solo letras, números y caracteres "-" "_" "." "/" permitidos');
        } else {
            hideError('#rifE');
        }

    });

    $('#rsE').on('blur', function () {
        var rsE = $(this).val();
        if (rsE.trim() === '') {
            hideError('#rsE');
        } else if(rsE.length > 50){
            showError('#rsE','El texto no debe exceder los 50 caracteres');
        } else if (!/^[a-zA-ZÀ-ÿ0-9\-_\/. ]+$/.test(rsE)) {
            showError('#rsE', 'solo letras, números y caracteres "-" "_" "." "/" permitidos');
        } else {
            hideError('#rsE');
        }

    });
    $('#direcE').on('blur', function () {
        var direcE = $(this).val();
        if (direcE.trim() === '') {
            showError('#direcE', 'el campo direccion no puede estar vacío');
        } else if(direcE.length > 100){
            showError('#direcE','El texto no debe exceder los 100 caracteres');
        } else if (!/^[a-zA-ZÀ-ÿ0-9\-_\/. ]+$/.test(direcE)) {
            showError('#direcE', 'solo letras, números y caracteres "-" "_" "." "/" permitidos');
        } else {
            hideError('#direcE');
        }

    });
    $('#tlfE').on('blur', function () {
        var tlfE = $(this).val();
        if (tlfE.trim() === '') {
            showError('#tlfE', 'el campo telefono no puede estar vacío');
        } else if(telefono.length > 15){
            showError('#tlfE','El texto no debe exceder los 15 caracteres');
        } else if (!/^[0-9\-\/]+$/.test(tlfE)) {
            showError('#tlfE', 'solo números y caracteres "-" "/" permitidos');
        } else {
            hideError('#tlfE');
        }

    });
    $('#emailE').on('blur', function () {
        var emailE = $(this).val();
        if (emailE.trim() === '') {
            hideError('#emailE');
        } else if(emailE.length > 70){
            showError('#emailE','El texto no debe exceder los 70 caracteres');
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailE)) { //No funciona 13/11
            showError('#emailE', 'por favor ingresa un correo electrónico válido');
        } else {
            hideError('#emailE');
        }
    });

    $('#desE').on('blur', function () {
        var desE = $(this).val();
        if (desE.trim() === '') {
            hideError('#desE');
        } else if(desE.length > 100){
            showError('#desE','El texto no debe exceder los 100 caracteres');
        } else if (!/^[a-zA-ZÀ-ÿ\-_\/. ]+$/.test(desE)) {
            showError('#desE', 'solo letras y caracteres "-" "_" "." "/" permitidos');
        } else {
            hideError('#desE');
        }
    });

});

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

$('#formGeneral').on('submit', async function(e) {
    e.preventDefault();
    
    const imagenInput = document.querySelector('input[name="logo"]');
    
    if (imagenInput && imagenInput.files.length > 0) {
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
    

    $(this).attr('enctype', 'multipart/form-data');

    const guardarInput = document.createElement('input');
    guardarInput.type = 'hidden';
    guardarInput.name = 'guardar';
    guardarInput.value = '1';
    this.appendChild(guardarInput);
    
    this.submit();
});


$('#Generaleditar').on('submit', async function(e) {
    e.preventDefault();
    
    const imagenInput = document.querySelector('input[name="logo1"]');
    
    if (imagenInput && imagenInput.files.length > 0) {
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
    
    $(this).attr('enctype', 'multipart/form-data');
    
    const editarInput = document.createElement('input');
    editarInput.type = 'hidden';
    editarInput.name = 'editar';
    editarInput.value = '1';
    this.appendChild(editarInput);
    
    this.submit();
});