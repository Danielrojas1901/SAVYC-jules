<?php 
require_once "controlador/descarga.php";
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Descarga de productos</h1>
                    <p>En esta sección se puede gestionar descargas al inventario.</p>
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
                        <div class="card-header">
                            <?php if (!empty($_SESSION["permisos"]["inventario"]["registrar"])): ?>
                            <!-- Botón para ventana modal -->
                            <button class="btn btn-primary" data-toggle="modal" data-target="#modalRegistrarDescarga">Registrar descarga</button>
                            <?php endif; ?>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="descarga" class="table table-bordered table-striped table-hover datatable" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>Código</th>
                                            <th>Fecha</th>
                                            <th>Descripción</th> 
                                            <th>Status</th>
                                            <th>Detalles</th>
                                        </tr>         
                                    </thead>
                                    <tbody>
                                    <!-- Tabla con los datos que se muestren dinamicamente -->
                                        <?php
                                        foreach ($descarga as $d){
                                            ?>
                                            <tr>
                                                <td> <?php echo $d["cod_descarga"] ?></td>
                                                <td> <?php echo $d["fecha"] ?></td>
                                                <td> <?php echo $d["descripcion"] ?></td>
                                                <td>
                                                    <?php if ($d['status'] == 1):?>
                                                        <span class="badge bg-success">Activo</span>
                                                    <?php else:?>
                                                        <span class="badge bg-danger">Inactivo</span>
                                                    <?php endif;?>
                                                </td>
                                                <td>
                                                    <button class='btn btn-primary btn-sm' id='detalles' style="position: center;" data-toggle="modal" data-target="#detallemodal" title="Ver detalle"
                                                    data-codigo="<?php echo $d["cod_descarga"];?>"
                                                    data-fechad="<?php echo $d["fecha"];?>"
                                                    data-descripd="<?php echo $d["descripcion"];?>">
                                                    <i class="fas fa-plus"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
<!-- =============================
    MODAL REGISTRAR DESCARGA 
================================== -->
                <div class="modal fade" id="modalRegistrarDescarga" tabindex="-1" aria-labelledby="modalRegistrarDescargaLabel" aria-hidden="true">
                        <div class="modal-dialog modal-xl">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="registrarModalLabel">Registrar descarga</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <form id="formRegistrarDescarga" method="post">
                                        <div class="form-group">
                                            <label for="fecha-hora">Fecha y Hora</label>
                                            <input type="text" class="form-control form-control-sm" id="fecha-hora" name="fecha" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="descripcion">Descripcion<span class="text-danger" style="font-size: 15px;"> *</span></label>
                                            <textarea class="form-control" id="descripcion" name="descripcion" placeholder="Ej: Ajuste de stock." required></textarea>
                                            <div class="invalid-feedback" style="display: none;"></div>
                                        </div>
                                        <!--SELECCIONAR PRODUCTOS (+ agregar productos)-->
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Código</th>
                                                        <th>Producto<span class="text-danger" style="font-size: 15px;"> *</span></th>
                                                        <th>Presentación</th>
                                                        <th>Lote</th>
                                                        <th>Stock disponible</th>
                                                        <th>Cantidad<span class="text-danger" style="font-size: 15px;"> *</span></th>
                                                    </tr>
                                                </thead>
                                                <tbody id="detdescargabody">
                                                <!--Se generan dinamicamente las filas de productos-->
                                                </tbody>
                                            </table>
                                            <!--Agregar otro producto-->
                                            <button type="button" class="btn btn-success" onclick="agregarFila()">Agregar otro producto</button>
                                            </div>
                                        <br>
                                        <!-- Alert Message -->
                                        <div class="alert alert-light d-flex align-items-center" role="alert">
                                            <i class="fas fa-exclamation-triangle mr-2"></i>
                                            <span>Todos los campos marcados con (*) son obligatorios</span>
                                        </div>
                                        
                                </div>
                                <div class="modal-footer justify-content-between">
                                    <input type="hidden" id="totaldescarga" min="0" step="0.001" name="costo_descarga" >
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                    <button type="submit" class="btn btn-primary" name="guardar">Guardar</button>
                                </div>
                                </form>
                            </div>
                        </div>
                    </div>
<!-- =============================
    MODAL DETALLE DESCARGA 
================================== -->
                <div class="modal fade" id="detallemodal" tabindex="-1" aria-labelledby="detalleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="detalleModalLabel">Detalle de descarga</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="card">
                                    <div class="card-header">
                                            <div class="form-row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="fecha">Fecha</label>
                                                        <input type="text" class="form-control" id="fecha" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="descripcion">Descripcion</label>
                                                        <input type="text" class="form-control" id="descripdescarga" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <h2><span id='descripciondetdescarga'></span></h2>
                                            <div class="table-responsive">
                                                <table id="productos" class="table table-bordered table-striped table-hover" style="width: 100%;">
                                                    <thead>
                                                        <tr>
                                                            <th>Imagen</th>
                                                            <th>Código</th>
                                                            <th>Producto</th>
                                                            <th>Presentacion</th>
                                                            <th>Lote</th>
                                                            <th>Cantidad descargada</th>
                                                        </tr>         
                                                    </thead>
                                                    <tbody id="detalleBody">
                                                        <!-- Los detalles se cargarán aquí -->
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
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
                window.location = 'descarga';
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
                window.location = 'descarga';
                exit;
            }
        });
    </script>
<?php endif; ?>
            </div>
        </div>
    </section>
</div>

<script src='vista/dist/js/modulos-js/descarga.js'></script>



