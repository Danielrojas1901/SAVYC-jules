document.addEventListener("DOMContentLoaded", function () {
    const checkbox = document.querySelector('#checkAuto');

    // Campos visibles
    const frecuencia = document.querySelector('select[name="frecuencia"]');
    const dia = document.querySelector('select[name="dia"]');
    const hora = document.querySelector('input[name="hora"]');

    // Campos ocultos
    const frecuenciaHidden = document.querySelector('input[name="frecuencia_hidden"]');
    const diaHidden = document.querySelector('input[name="dia_hidden"]');
    const horaHidden = document.querySelector('input[name="hora_hidden"]');

    // Alternar si los campos están activos o no
    function toggleCamposAuto() {
    const activo = checkbox.checked;
    frecuencia.disabled = !activo;
    dia.disabled = !activo;
    hora.disabled = !activo;
    }

    checkbox.addEventListener('change', toggleCamposAuto);
    toggleCamposAuto(); // Ejecutar al cargar

    // Al enviar el formulario, copiar valores si están deshabilitados
    document.querySelector('#formConfigBackup').addEventListener('submit', function () {
    if (!checkbox.checked) {
        frecuenciaHidden.value = frecuencia.value;
        diaHidden.value = dia.value;
        horaHidden.value = hora.value;
    }
    });


    //ELIMINAR
    $('#modalEliminar').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); 
        var nombre = button.data('nombre');
        var codigo = button.data('codigo');
        var ruta = button.data('ruta');

        var modal = $(this);
        modal.find('#nombreE').text(nombre);
        modal.find('.modal-body #codE').val(codigo);
        modal.find('.modal-body #rutaE').val(ruta);

        console.log(nombre,codigo,ruta);
    });
});