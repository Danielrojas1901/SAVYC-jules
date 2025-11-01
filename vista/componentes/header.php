<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars" title="Menú"></i></a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="inicio" class="nav-link">Inicio</a>
        </li>
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown2" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Ayuda
            </a>
            <div class="dropdown-menu" aria-labelledby="navbarDropdown2">
                <a class="dropdown-item" href="vista/dist/manual/manual.pdf" target="_blank">Manual de usuario</a>
            </div>
        </li>

        <?php if (
                isset($_ENV["ENTORNO"]) &&
                $_ENV["ENTORNO"] == "TESTING" &&
                !empty($_SESSION["permisos"]["seguridad"]["eliminar"])
        ): ?>
        <li class="nav-item">
            <button class="btn btn-danger btn-sm ml-2" id="resetDatabaseBtn" title="Reiniciar Base de Datos (Solo Testing)">
                <i class="fas fa-database"></i> Reset DB
            </button>
        </li>
        <?php endif; ?>
    </ul>

    <ul class="navbar-nav ml-auto">
        <div class="user d-flex align-items-center position-relative" style="margin-left: 5px;">
            <div class="notification position-relative mr-3">
                <i class="fas fa-bell fa-lg text-dark" id="bell-icon" style="cursor: pointer;"></i>
            <span id="notification-count" class="badge badge-pill badge-danger position-absolute" style="top: -5px; right: 5px; font-size: 0.5rem; display: none;">0</span>

            <div class="notification-dropdown overflow-auto" style="max-height: 400px;"></div>

            </div>
        </div>


        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#" title="usuario">
                <i class="fa fa-user" title="Usuario"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <span class="dropdown-header"> <?php echo $_SESSION[
                        "nombre"
                ]; ?></span>
                <div class="dropdown-divider"></div>
                <button class="dropdown-item" data-toggle="modal" data-target="#modalPerfil">
                    <i class="fas fa-user mr-2"></i>
                    Ver perfil
                </button>
                <div class="dropdown-divider"></div>
                <a href="cerrarsesion" class="dropdown-item">
                    <i class="fas fa-sign-out-alt mr-2"></i> Cerrar sesión
                </a>
            </div>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-widget="fullscreen" href="#" role="button" title="Pantalla completa">
                <i class="fas fa-expand-arrows-alt"></i>
            </a>
        </li>
    </ul>
</nav>

<div class="modal fade" id="modalPerfil" tabindex="-1" role="dialog" aria-labelledby="modalPerfilLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel">Perfil de usuario</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="col-md-12">
                    <p><b>Nombre: </b><?php echo $_SESSION["nombre"]; ?></p>
                    <p><b>Usuario: </b> <?php echo $_SESSION["user"]; ?> </p>
                    <p><b>Rol: </b> <?php echo $_SESSION["rol"]; ?></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para confirmar reset de base de datos -->
<div class="modal fade" id="modalResetDatabase" tabindex="-1" role="dialog" aria-labelledby="modalResetDatabaseLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="modalResetDatabaseLabel">
                    <i class="fas fa-exclamation-triangle"></i> Confirmar Reset de Base de Datos
                </h5>
                <button class="close text-white" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>¡ATENCIÓN!</strong> Esta acción es irreversible.
                </div>
                <p>Esta operación:</p>
                <ul>
                    <li>Eliminará TODOS los datos actuales de la base de datos</li>
                    <li>Restaurará la base de datos desde <code>savyc_testing.sql</code></li>
                    <li>Solo está disponible en entorno de TESTING</li>
                </ul>
                <p><strong>¿Estás seguro de que deseas continuar?</strong></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="confirmResetDatabase">
                    <i class="fas fa-database"></i> Sí, Reiniciar Base de Datos
                </button>
            </div>
        </div>
    </div>
</div>

<script src="vista/dist/js/modulos-js/notificaciones.js"></script>

<script>
$(document).ready(function() {
    // Mostrar modal de confirmación al hacer clic en el botón de reset
    $('#resetDatabaseBtn').click(function() {
        $('#modalResetDatabase').modal('show');
    });

    // Confirmar reset de base de datos
    $('#confirmResetDatabase').click(function() {
        const btn = $(this);
        const originalText = btn.html();

        // Deshabilitar botón y mostrar loading
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Procesando...');

        $.ajax({
            url: 'index.php?pagina=database_reset',
            type: 'POST',
            data: {
                reset_database: 'confirm'
            },
            dataType: 'json',
            success: function(response) {
                $('#modalResetDatabase').modal('hide');

                if (response.success) {
                    Swal.fire({
                        title: response.title || 'Éxito',
                        text: response.message,
                        icon: response.icon || 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        // Recargar la página después de un reset exitoso
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        title: response.title || 'Error',
                        text: response.message,
                        icon: response.icon || 'error',
                        confirmButtonText: 'OK'
                    });
                }
            },
            error: function(xhr, status, error) {
                $('#modalResetDatabase').modal('hide');
                Swal.fire({
                    title: 'Error',
                    text: 'Error de conexión: ' + error,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            },
            complete: function() {
                // Rehabilitar botón
                btn.prop('disabled', false).html(originalText);
            }
        });
    });
});
</script>
