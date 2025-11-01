<?php 
require_once "controlador/backup.php"; 
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-9">
                    <h1>Copias de Seguridad</h1>
                    <p>En esta sección puedes generar y configurar copias de seguridad.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <?php if (!empty($_SESSION["permisos"]["seguridad"]["editar"])): ?>
                    <button type="submit" class="btn btn-success" data-toggle="modal" data-target="#modalConfigBackup">
                        Configurar respaldo
                    </button>
                    <button class="btn btn-primary" data-toggle="modal" data-target="#modalGenerarBackup">
                        Generar respaldo manual
                    </button>
                    <?php endif; ?>
                </div>

                <div class="card-body">

                    <div class="table-responsive">
                        <table id="tablaRespaldos" class="table table-bordered table-striped datatable1" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>Codigo</th>
                                    <th>Usuario</th>
                                    <th>Fecha</th>
                                    <th>Descripción</th>
                                    <th>Archivo</th>
                                    <th>Tamaño (MB)</th>
                                    <th>Tipo</th>
                                    <th>Acciones</th>
                                </tr>         
                            </thead>
                            <tbody>
                                <?php if (!empty($_SESSION["permisos"]["seguridad"]["consultar"])): ?>
                                <?php foreach ($backup as $r): ?>
                                    <?php $nombreArchivo = basename($r['ruta']); ?>
                                    <tr>
                                        <td><?= $r['cod_backup'] ?></td>
                                        <td><?= $r['nombre_usuario'] ?></td> <!-- Poner automatico si es el caso-->
                                        <td><?= $r['fecha'] ?></td>
                                        <td><?= $r['descripcion'] ?></td>
                                        <td><a href="<?= $r['ruta'] ?>" download><?= $nombreArchivo ?></a></td>
                                        <td><?= round($r['tamanio'], 2) ?></td>
                                        <td>
                                            <span class="badge <?= $r['tipo'] === 'manual' ? 'bg-primary' : 'bg-secondary' ?>">
                                                <?= ucfirst($r['tipo']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if (!empty($_SESSION["permisos"]["seguridad"]["eliminar"])): ?>
                                            <button name="eliminar" class="btn btn-danger btn-sm" title="Eliminar" data-toggle="modal" data-target="#modalEliminar"
                                            data-codigo="<?php echo $r['cod_backup']?>"
                                            data-nombre="<?php echo $r['nombre']?>"
                                            data-ruta="<?php echo $r['ruta']?>">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach ?>
                                <?php endif ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

<!-- =============================
    MODAL CONFIGURAR RESPALDO 
============================= -->
        <div class="modal fade" id="modalConfigBackup" tabindex="-1" aria-labelledby="modalConfigBackupLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <form method="POST" id="formConfigBackup">
                <div class="modal-content">
                    <div class="modal-header bg-primary">
                    <h5 class="modal-title">Configuración de respaldo</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body row">
                    <div class="form-group col-md-4">
                        <label for="frecuencia">Frecuencia</label>
                        <select name="frecuencia" class="form-control" required>
                        <option value="1" <?= $config['frecuencia'] == 'diario' ? 'selected' : '' ?>>Diario</option>
                        <option value="2" <?= $config['frecuencia'] == 'semanal' ? 'selected' : '' ?>>Semanal</option>
                        <option value="3" <?= $config['frecuencia'] == 'quincenal' ? 'selected' : '' ?>>Quincenal</option>
                        <option value="4" <?= $config['frecuencia'] == 'mensual' ? 'selected' : '' ?>>Mensual</option>
                        </select>
                        <input type="hidden" name="frecuencia_hidden" id="frecuenciaHidden">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="dia">Día</label>
                        <select name="dia" class="form-control" required>
                        <option value="1" <?= $config['dia'] == 1 ? 'selected' : '' ?>>Lunes</option>
                        <option value="2" <?= $config['dia'] == 2 ? 'selected' : '' ?>>Martes</option>
                        <option value="3" <?= $config['dia'] == 3 ? 'selected' : '' ?>>Miércoles</option>
                        <option value="4" <?= $config['dia'] == 4 ? 'selected' : '' ?>>Jueves</option>
                        <option value="5" <?= $config['dia'] == 5 ? 'selected' : '' ?>>Viernes</option>
                        <option value="6" <?= $config['dia'] == 6 ? 'selected' : '' ?>>Sábado</option>
                        <option value="7" <?= $config['dia'] == 7 ? 'selected' : '' ?>>Domingo</option>
                        </select>
                        <input type="hidden" name="dia_hidden" id="diaHidden">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="hora">Hora</label>
                        <input type="time" name="hora" class="form-control" value="<?= $config['hora'] ?? '20:00' ?>">
                        <input type="hidden" name="hora_hidden" id="horaHidden">
                    </div>

                    <div class="form-group col-md-4">
                        <label for="retencion">Retención máxima <i class="fas fa-info-circle" data-toggle="tooltip" title="Número máximo de respaldos que se conservarán."></i></label>
                        <input type="number" name="retencion" class="form-control" value="<?= $config['retencion'] ?? 10 ?>" min="5">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="modo">Aplicar retención a</label>
                        <select name="modo" class="form-control">
                        <option value="1" <?= $config['modo'] == 'ambos' ? 'selected' : '' ?>>Ambos</option>
                        <option value="2" <?= $config['modo'] == 'automatico' ? 'selected' : '' ?>>Solo automáticos</option>
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label>¿Activar respaldo automático?</label><br>
                        <input type="checkbox" name="habilitado" id="checkAuto" data-bootstrap-switch <?= $config['habilitado'] ? 'checked' : '' ?>>
                    </div>
                    </div>

                    <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" name="guardar_config" class="btn btn-primary">Guardar</button>
                    </div>
                </div>
                </form>
            </div>
        </div>

<!-- =============================
    MODAL GENERAR RESPALDO MANUAL
============================= -->
            <div class="modal fade" id="modalGenerarBackup" tabindex="-1" aria-labelledby="modalGenerarBackupLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <form method="POST">
                        <div class="modal-content">
                            <div class="modal-header bg-primary">
                                <h5 class="modal-title">Generar Respaldo Manual</h5>
                                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                            </div>

                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="nombre">Nombre del respaldo<span class="text-danger" style="font-size: 20px;">*</span></label>
                                            <button class="btn btn-xs" data-toggle="tooltip" data-placement="top" title="Ingresa el nombre del archivo, por ejemplo: Respaldo_Mayo">
                                                <i class="fas fa-info-circle"></i>
                                            </button>
                                            <script>
                                                $(function() {
                                                    $('[data-toggle="tooltip"]').tooltip();
                                                });
                                            </script>
                                    <input type="text" name="nombre_backup" id="nombre_backup" class="form-control" placeholder="Ej: respaldo_mayo" required>
                                    <small class="text-muted">El archivo se guardará con este nombre y la extensión .sql</small>
                                </div>
                                <div class="form-group">
                                    <label for="descripcion">Descripción<span class="text-danger" style="font-size: 20px;">*</span></label>
                                    <textarea name="desc_backup" id="desc_backup" class="form-control" placeholder="Ej: Respaldo para..." required></textarea>
                                </div>
                            </div>

                            <div class="modal-footer justify-content-between">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                <button type="submit" name="generar_respaldo" class="btn btn-primary">Generar ahora</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

<!-- =============================
MODAL ELIMINAR RESPALDO 
============================= -->
            <div class="modal fade" id="modalEliminar" tabindex="-1" aria-labelledby="modalEliminarLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <form method="POST" id="formEliminar">
                    <div class="modal-content">
                        <div class="modal-header bg-danger">
                        <h5 class="modal-title">Eliminar copia de seguridad</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                        </div>
                        <div class="modal-body">
                            <p>¿Estás seguro de que deseas eliminar esta copia de seguridad: <b><span id="nombreE"></span></b>?</p>
                            <small>Esta acción no se puede deshacer.</small>
                            <input type="hidden" name="codE" id="codE">
                            <input type="hidden" name="rutaE" id="rutaE">
                        </div>
                        <div class="modal-footer justify-content-between">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                <button type="submit" name="eliminarR" class="btn btn-primary">Eliminar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </section>
</div>

<?php if(isset($registrar)): ?>
    <script>
        Swal.fire({
            title: "<?= $registrar['title'] ?>",
            text: "<?= $registrar['message'] ?>",
            icon: "<?= $registrar['icon'] ?>",
            confirmButtonText: "Aceptar"
        }).then((result) => {
            if (result.isConfirmed) {
                window.location = 'backup';
            }
        });
        </script>
<?php endif; ?>
<script src="vista/dist/js/modulos-js/backup.js"></script>
