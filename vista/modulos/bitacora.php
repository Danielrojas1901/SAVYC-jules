<?php require_once 'controlador/bitacora.php'; ?>
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Bitacora</h1>
                    <p>En esta sección se puede ver la actividad de los usuarios en el sistema</p>
                </div>
            </div>
        </div>

    </section>
    
    <div class=""   style=" text-align-last: right; margin-right: 20px ">
        <?php if(!empty($_SESSION["permisos"]["seguridad"]['eliminar']) && $_SESSION['cod_usuario']===1): ?>
            <button type="button" class="btn btn-danger mb-4" data-toggle="modal" data-target="#modalEliminarBitacora">
                Eliminar registros de bitácora
            </button>
        <?php endif; ?>
    </div>

<!-- Modal -->
<div class="modal fade" id="modalEliminarBitacora" tabindex="-1" role="dialog" aria-labelledby="modalEliminarBitacoraLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form action="" method="POST">
      <div class="modal-content">
        <div class="modal-header bg-danger text-white">
          <h5 class="modal-title" id="modalEliminarBitacoraLabel">Eliminar Registros de Bitácora</h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label for="fecha_desde">Desde:</label>
                <input type="date" name="fecha_desde" id="fecha_desde" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="fecha_hasta">Hasta:</label>
                <input type="date" name="fecha_hasta" id="fecha_hasta" class="form-control" required>
            </div>
            <p class="text-danger"><small>Esta acción no se puede deshacer.</small></p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-danger" name="eliminar_por_fecha">Eliminar</button>
        </div>
      </div>
    </form>
  </div>
</div>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="carga" class="table table-bordered table-striped datatable" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>Usuario</th>
                                            <th>Acción</th>
                                            <th>Modulo</th>
                                            <th>Descripción</th>
                                            <th>Fecha</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($bitacora as $dato): ?>
                                                <tr>
                                                    <td><?php echo $dato['nombre']; ?></td>
                                                    <td><?php echo $dato['accion']; ?></td>
                                                    <td><?php echo $dato['modulo']; ?></td>
                                                    <td><?php echo $dato['detalles']; ?></td>
                                                    <td><?php echo date("d-m-Y H:i:s", strtotime($dato['fecha'])); ?></td>
                                                </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<?php if (isset($resultado)): ?>
    <script>
        Swal.fire({
            title: '<?php echo $resultado["title"]; ?>',
            text: '<?php echo $resultado["message"]; ?>',
            icon: '<?php echo $resultado["icon"]; ?>',
            confirmButtonText: 'Ok'
        });
    </script>
<?php endif; ?>