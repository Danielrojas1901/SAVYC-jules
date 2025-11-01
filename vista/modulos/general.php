<?php require_once 'controlador/general.php' ?>
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Configuración de la empresa</h1>
                    <p>Gestiona la información de la empresa.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                    <?php if (empty($datos)): ?>
                        <div class="card-header">
                                <!-- Botón para ventana modal -->
                                <?php if (!empty($_SESSION["permisos"]["seguridad"]["registrar"])): ?>
                                    <button class="btn btn-primary" data-toggle="modal" id="registrar" data-target="#modalregistrarempresa">Registrar Información</button>
                                <?php endif; ?>
                        </div>
                        <?php endif; ?>
                        <div class="card-body">
                        
                            <?php foreach ($datos as $dato): ?>
                                <div class="card mb-3">
                                    <div class="card-header">
                                        <h5 class="card-title"><?php echo $dato['nombre']; ?></h5>
                                    </div>
                                    <div class="card-body">
                                        <p><b>Rif: </b> <?php echo $dato['rif']; ?></p>
                                        <p><b>Razón Social: </b><?php echo $dato['nombre']; ?></p>
                                        <p><b>Dirección: </b><?php echo $dato['direccion']; ?></p>
                                        <p><b>Teléfono: </b><?php echo $dato['telefono']; ?></p>
                                        <p><b>Email: </b><?php echo $dato['email']; ?></p>
                                        <p><b>Descripción: </b><?php echo $dato['descripcion']; ?></p>
                                        <p><b>Horarios de atención: </b></p>
                                        <div class="table-responsive">
                                            <table class="table table_bordered table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Día</th>
                                                        <th>Horario</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php 
                                                        foreach($horarios as $horario){ ?>
                                                        <tr>
                                                            <td><?= $horario['dia']; ?></td>
                                                            <?php 
                                                            if ($horario["cerrado"] === 1) {
                                                                echo "<td>Cerrado</td>";
                                                            } else { 
                                                                $desde=substr($horario['desde'], 0, 5);
                                                                $hasta=substr($horario['hasta'], 0, 5);?>
                                                                <td><?= "De ".$desde." a ".$hasta; ?></td>
                                                            <?php } ?>
                                                        </tr>
                                                    <?php }
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        <p>
                                            <b>Logo: </b>
                                            <?php if (!empty($dato['logo'])): ?>
                                                <img src="<?php echo $dato['logo']; ?>" alt="Logo" style="width: 100px; height: auto;">
                                            <?php else: ?>
                                                <span>No disponible</span>
                                            <?php endif; ?>
                                        </p>
                                    <div class="card-footer">
                                        <?php if (!empty($_SESSION["permisos"]["seguridad"]["editar"])): ?>
                                            <button name="ajustar" class="btn btn-primary btn-sm editar" data-target="#editModal" data-toggle="modal"
                                                data-cod="<?= $dato['cod']; ?>"
                                                data-rif="<?php echo $dato['rif']; ?>"
                                                data-rs="<?php echo $dato['nombre']; ?>"
                                                data-direc="<?php echo $dato['direccion']; ?>"
                                                data-tlf="<?php echo $dato['telefono']; ?>"
                                                data-email="<?php echo $dato['email']; ?>"
                                                data-des="<?php echo $dato['descripcion']; ?>"
                                                data-logo="<?php echo $dato['logo']; ?>">
                                                <i class="fas fa-pencil-alt" title="Editar"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        

<!-- ===========================
    MODAL REGISTRAR INFO GENERAL 
================================= -->

                <div class="modal fade" id="modalregistrarempresa" tabindex="-1" aria-labelledby="modalregistrarempresaLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Registrar informacion</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>

                            <div class="modal-body">
                                <form id="formGeneral" method="post" enctype="multipart/form-data">
                                    <!--   RIF DE LA empresa     -->
                                    <div class="form-group">
                                        <label for="rif">Rif de la empresa <span class="text-danger" style="font-size: 20px;"> *</span> </label>
                                        <button class="btn btn-xs" data-toggle="tooltip" data-placement="top" title="Ingresa el rif de la empresa, por ejemplo: J-010523">
                                            <i class="fas fa-info-circle"></i>
                                        </button>
                                        <script>
                                            $(function() {
                                                $('[data-toggle="tooltip"]').tooltip();
                                            });
                                        </script>
                                        <input type="text" class="form-control" id="rif" name="rif" maxlength="15" placeholder="Ej: J123456789">
                                        <div class="invalid-feedback" style="display: none;"></div>
                                    </div>
                                    <!--   NOMBRE DE LA empresa     -->
                                    <div class="form-group">
                                        <label for="nombre">Nombre <span class="text-danger" style="font-size: 20px;"> *</span> </label>
                                        <button class="btn btn-xs" data-toggle="tooltip" data-placement="top" title="Ingresa el nombre o razón social de la empresa, por ejemplo: Lacteos los Andes">
                                            <i class="fas fa-info-circle"></i>
                                        </button>
                                        <script>
                                            $(function() {
                                                $('[data-toggle="tooltip"]').tooltip();
                                            });
                                        </script>
                                        <input type="text" class="form-control" name="nombre" id="nombre" maxlength="50" placeholder="Ej: Inversiones SAVYC">
                                        <div class="invalid-feedback" style="display: none;"></div>
                                    </div>
                                    <!-- DIRECCION-->
                                    <div class="form-group row">
                                        <div class="col-6">
                                            <label for="direccion">Dirección <span class="text-danger" style="font-size: 20px;"> *</span></label>
                                            <button class="btn btn-xs" data-toggle="tooltip" data-placement="top" title="Ingresa la dirección de la empresa, por ejemplo: Avenida Los Horcones">
                                                <i class="fas fa-info-circle"></i>
                                            </button>
                                            <script>
                                                $(function() {
                                                    $('[data-toggle="tooltip"]').tooltip();
                                                });
                                            </script>
                                            <input type="text" class="form-control" name="direccion" id="direccion" maxlength="100" placeholder="Ej: Av. ejemplo con calle 1">
                                            <div class="invalid-feedback" style="display: none;"></div>
                                        </div>
                                    <!--   TELEFONO     -->
                                        <div class="col-6">
                                            <label for="telefono">Teléfono<span class="text-danger" style="font-size: 20px;"> *</span></label>
                                            <button class="btn btn-xs" data-toggle="tooltip" data-placement="top" title="Ingresa el telefono de la empresa, por ejemplo: 0424-555-21-23">
                                                <i class="fas fa-info-circle"></i>
                                            </button>
                                            <script>
                                                $(function() {
                                                    $('[data-toggle="tooltip"]').tooltip();
                                                });
                                            </script>
                                            <input type="tel" class="form-control" name="telefono" id="telefono" maxlength="12" placeholder="Ej: 0412-1234567">
                                            <div class="invalid-feedback" style="display: none;"></div>
                                        </div>
                                    </div>
                                    <!--   EMAIL     -->
                                    <div class="form-group ">
                                            <label for="email">Correo:</label>
                                            <button class="btn btn-xs" data-toggle="tooltip" data-placement="top" title="Ingresa el correo de la empresa, por ejemplo: savyc@gmail.com">
                                                <i class="fas fa-info-circle"></i>
                                            </button>
                                            <script>
                                                $(function() {
                                                    $('[data-toggle="tooltip"]').tooltip();
                                                });
                                            </script>
                                            <input type="email" class="form-control" name="email" id="email" maxlength="70" placeholder="Ej: savyc@gmail.com">
                                            <div class="invalid-feedback" style="display: none;"></div>
                                    </div>
                                    <div class="form-group">
                                            <!--   DESCRIPCION    -->
                                            <label for="descripcion">Descripción</label>
                                            <button class="btn btn-xs" data-toggle="tooltip" data-placement="top" title="Ingresa una descripción breve de la empresa, por ejemplo: Comercio para la venta de alimentos">
                                                <i class="fas fa-info-circle"></i>
                                            </button>
                                            <script>
                                                $(function() {
                                                    $('[data-toggle="tooltip"]').tooltip();
                                                });
                                            </script>
                                            <textarea class="form-control" name="descripcion" id="descripcion" maxlength="100" placeholder="Ej: Comercio para la venta de alimentos"></textarea>
                                            <div class="invalid-feedback" style="display: none;"></div>
                                    </div>
                                    <!-- HORARIOS -->
                                    <div class="form-group">
                                        <label>Horarios de trabajo</label>
                                        <button class="btn btn-xs" data-toggle="tooltip" data-placement="top" title="Configura los horarios de atención de la empresa para cada día de la semana">
                                            <i class="fas fa-info-circle"></i>
                                        </button>
                                        <script>
                                            $(function() {
                                                $('[data-toggle="tooltip"]').tooltip();
                                            });
                                        </script>
                                        
                                        <div class="table-responsive">
                                        <table class="table table-bordered table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Día</th>
                                                    <th>Desde</th>
                                                    <th></th>
                                                    <th>Hasta</th>
                                                    <th>Cerrado</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($horarios as $dia): ?>
                                                    <tr>
                                                        <td><?= $dia['dia']; ?></td>
                                                        <td>
                                                            <input type="hidden" name="Horario[<?= $dia['dia']; ?>][cod]" value="<?= $dia['cod_dia']; ?>">
                                                            <select class="form-control" name="Horario[<?php echo $dia['dia']; ?>][desde]" id="<?php echo $dia['dia']; ?>_desde">
                                                                <?php for ($i = 0; $i <= 23; $i++): ?>
                                                                    <option value="<?php echo sprintf('%02d:00', $i); ?>"><?php echo sprintf('%02d:00', $i); ?></option>
                                                                    <option value="<?php echo sprintf('%02d:30', $i); ?>"><?php echo sprintf('%02d:30', $i); ?></option>
                                                                <?php endfor; ?>
                                                            </select>
                                                        </td>
                                                        <td class="text-center">a</td>
                                                        <td>
                                                            <select class="form-control" name="Horario[<?php echo $dia['dia']; ?>][hasta]" id="<?php echo $dia['dia']; ?>_hasta">
                                                                <?php for ($i = 0; $i <= 23; $i++): ?>
                                                                    <option value="<?php echo sprintf('%02d:00', $i); ?>"><?php echo sprintf('%02d:00', $i); ?></option>
                                                                    <option value="<?php echo sprintf('%02d:30', $i); ?>"><?php echo sprintf('%02d:30', $i); ?></option>
                                                                <?php endfor; ?>
                                                            </select>
                                                        </td>
                                                        <td class="text-center">
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" class="custom-control-input" id="<?php echo $dia['dia']; ?>_cerrado" name="Horario[<?php echo $dia['dia']; ?>][cerrado]" value="1">
                                                                <label class="custom-control-label" for="<?php echo $dia['dia']; ?>_cerrado"></label>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <!--   LOGO    -->
                                    <div class="form-group">
                                        <label for="logo">Ingrese el logo<span class="text-danger" style="font-size: 20px;"> *</span></label>
                                        <button class="btn btn-xs" data-toggle="tooltip" data-placement="top" title="Ingresa un logo representativo de la empresa">
                                            <i class="fas fa-info-circle"></i>
                                        </button>
                                        <script>
                                            $(function() {
                                                $('[data-toggle="tooltip"]').tooltip();
                                            });
                                        </script>
                                        <input type="file" class="form-control" name="logo" id="logo">
                                        <div class="invalid-feedback" style="display: none;"></div>
                                    </div>
                                    <!-- Alert Message -->
                                    <div class="alert alert-light d-flex align-items-center" role="alert">
                                            <i class="fas fa-exclamation-triangle mr-2"></i>
                                            <span>Todos los campos marcados con (*) son obligatorios</span>
                                    </div>
                            </div>
                            <div class="modal-footer justify-content-between">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                <button type="submit" class="btn btn-primary" name="guardar">Guardar</button>
                            </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

                <?php
                if (isset($registrar)): ?>
                    <script>
                        Swal.fire({
                            title: '<?php echo $registrar["title"]; ?>',
                            text: '<?php echo $registrar["message"]; ?>',
                            icon: '<?php echo $registrar["icon"]; ?>',
                            confirmButtonText: 'Ok'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location = 'general';
                            }
                        });
                    </script>
                <?php endif; ?>
<!-- =======================
MODAL EDITAR INFO GENERAL 
============================= -->
                <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Editar información</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form id="Generaleditar" method="post" enctype="multipart/form-data">
                                    <input type="hidden" name="rif">
                                    <!-- RIF DE LA empresa -->
                                    <div class="form-group">
                                        <label for="rif">Rif de la empresa</label>
                                        <input type="text" class="form-control" name="rif" id="rifE" required>
                                        <input type="hidden" id="cod" name="cod">
                                        <div class="invalid-feedback" style="display: none;"></div>
                                    </div>
                                    <!-- NOMBRE DE LA empresa -->
                                    <div class="form-group">
                                        <label for="nombre">Nombre</label>
                                        <input type="text" class="form-control" name="nombre" id="rsE" required>
                                        <div class="invalid-feedback" style="display: none;"></div>
                                    </div>
                                    <!-- DIRECCION -->
                                    <div class="form-group row">
                                        <div class="col-6">
                                            <label for="direccion">Dirección</label>
                                            <input type="text" class="form-control" name="direccion" id="direcE" required>
                                            <div class="invalid-feedback" style="display: none;"></div>
                                        </div>
                                    <!-- TELEFONO -->
                                        <div class="col-6">
                                            <label for="telefono">Teléfono</label>
                                            <input type="tel" class="form-control" name="telefono" id="tlfE">
                                            <div class="invalid-feedback" style="display: none;"></div>
                                        </div>
                                    </div>
                                    <!-- EMAIL -->
                                    <div class="form-group">
                                        <label for="email">Email</label>
                                        <input type="email" class="form-control" name="email" id="emailE">
                                        <div class="invalid-feedback" style="display: none;"></div>
                                    </div>
                                    <!-- DESCRIPCION -->
                                    <div class="form-group">
                                        <label for="descripcion">Descripción</label>
                                        <input type="text" class="form-control" name="descripcion" id="desE">
                                        <div class="invalid-feedback" style="display: none;"></div>
                                    </div>
                                    <!-- HORARIOS -->
                                    <div class="form-group">
                                        <label>Horarios de trabajo</label>
                                        <button class="btn btn-xs" data-toggle="tooltip" data-placement="top" title="Configura los horarios de atención de la empresa para cada día de la semana">
                                            <i class="fas fa-info-circle"></i>
                                        </button>
                                        <script>
                                            $(function() {
                                                $('[data-toggle="tooltip"]').tooltip();
                                            });
                                        </script>
                                        
                                        <div class="table-responsive">
                                        <table class="table table-bordered table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Día</th>
                                                    <th>Desde</th>
                                                    <th></th>
                                                    <th>Hasta</th>
                                                    <th>Cerrado</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($horarios as $dia): 
                                                    $desde = date('H:i', strtotime($dia['desde']));
                                                    $hasta = date('H:i', strtotime($dia['hasta']));
                                                ?>
                                                    <tr>
                                                        <td><?= $dia['dia']; ?></td>
                                                        <td>
                                                            <input type="hidden" name="Horario[<?= $dia['dia']; ?>][cod]" value="<?= $dia['cod_dia']; ?>">
                                                            <select class="form-control" name="Horario[<?php echo $dia['dia']; ?>][desde]" id="<?php echo $dia['dia']; ?>_desde">
                                                                <?php for ($i = 0; $i <= 23; $i++): 
                                                                    $hora1 = sprintf('%02d:00', $i);
                                                                    $hora2 = sprintf('%02d:30', $i);
                                                                ?>
                                                                    <option value="<?= $hora1 ?>" <?= ($desde == $hora1) ? 'selected' : '' ?>><?= $hora1 ?></option>
                                                                    <option value="<?= $hora2 ?>" <?= ($desde == $hora2) ? 'selected' : '' ?>><?= $hora2 ?></option>
                                                                <?php endfor; ?>
                                                            </select>
                                                        </td>
                                                        <td class="text-center">a</td>
                                                        <td>
                                                            <select class="form-control" name="Horario[<?php echo $dia['dia']; ?>][hasta]" id="<?php echo $dia['dia']; ?>_hasta">
                                                                <?php for ($i = 0; $i <= 23; $i++): 
                                                                    $hora1 = sprintf('%02d:00', $i);
                                                                    $hora2 = sprintf('%02d:30', $i);
                                                                ?>
                                                                    <option value="<?= $hora1 ?>" <?= ($hasta == $hora1) ? 'selected' : '' ?>><?= $hora1 ?></option>
                                                                    <option value="<?= $hora2 ?>" <?= ($hasta == $hora2) ? 'selected' : '' ?>><?= $hora2 ?></option>
                                                                <?php endfor; ?>
                                                            </select>
                                                        </td>
                                                        <td class="text-center">
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" class="custom-control-input" id="edit_<?php echo $dia['dia']; ?>_cerrado" name="Horario[<?php echo $dia['dia']; ?>][cerrado]" value="1" <?= ($dia['cerrado'] == 1) ? 'checked' : '' ?>>
                                                                <label class="custom-control-label" for="edit_<?php echo $dia['dia']; ?>_cerrado"></label>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <!-- LOGO -->
                                    <div class="form-group">
                                        <label for="logo">Ingrese el logo</label>
                                        <input type="file" class="form-control" name="logo1" id="logoE">
                                        <div class="invalid-feedback" style="display: none;"></div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                        <button type="submit" class="btn btn-primary" name="editar">Guardar cambios</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
                if (isset($editar)): ?>
                    <script>
                        Swal.fire({
                            title: '<?php echo $editar["title"]; ?>',
                            text: '<?php echo $editar["message"]; ?>',
                            icon: '<?php echo $editar["icon"]; ?>',
                            confirmButtonText: 'Ok'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location = 'general';
                            }
                        });
                    </script>
                <?php endif; ?>

                <?php if (isset($r)): ?>
                    <script>
                        Swal.fire({
                            title: '<?php echo $r["title"]; ?>',
                            text: '<?php echo $r["message"]; ?>',
                            icon: '<?php echo $r["icon"]; ?>',
                            confirmButtonText: 'Ok'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location = 'general';
                                exit;
                            }
                        });
                    </script>
                <?php endif; ?>
                
                <?php if (isset($e)): ?>
                    <script>
                        Swal.fire({
                            title: '<?php echo $e["title"]; ?>',
                            text: '<?php echo $e["message"]; ?>',
                            icon: '<?php echo $e["icon"]; ?>',
                            confirmButtonText: 'Ok'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location = 'general';
                            }
                        });
                    </script>
                <?php endif; ?>
    </section>
</div>

<script src="vista/dist/js/modulos-js/general.js"></script>