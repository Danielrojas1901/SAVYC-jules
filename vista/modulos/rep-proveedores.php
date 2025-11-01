<?php
require_once 'controlador/reportep.php';
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Reporte De Proveedores</h1>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <form action="index.php?pagina=reportes" method="post" target="_blank">
                                <input type="hidden" name="tipo" value="proveedores">
                                <?php if (!empty($_SESSION["permisos"]["reporte"])):
                                    $disabled = !isset($_SESSION['rif']) ? 'disabled' : '';
                                    $title = !isset($_SESSION['rif'])
                                        ? 'No se puede generar el reporte, debes registrar la informacion de la empresa'
                                        : 'Exportar PDF';?>
                                    <button class="btn btn-danger" title="<?= $title ?>" <?= $disabled ?> name="pdf" alt="Generar pdf" id="pdf">Generar PDF</button>
                                <?php endif; ?>
                            </form>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="proveedores" class="table table-bordered table-striped table-hover datatable" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>Código</th>
                                            <th>Rif</th>
                                            <th>Razon social</th>
                                            <th>Correo electronico</th>
                                            <th>Dirección</th>
                                            <th>Telefonos</th>
                                            <th>Status</th>
                                            <th>Representante</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($registro as $datos) { ?>
                                            <tr>
                                                <td><?php echo $datos["cod_prov"] ?></td>
                                                <td><?php echo $datos["rif"] ?></td>
                                                <td><?php echo $datos["razon_social"] ?></td>
                                                <td><?php echo !empty($datos["email"]) ? $datos["email"] : 'No disponible'; ?></td>
                                                <td><?php echo !empty($datos["direccion"]) ? $datos["direccion"] : 'No disponible'; ?></td>
                                                <td><?php echo !empty($datos["telefonos"]) ? $datos["telefonos"] : 'No disponible'; ?></td>

                                                <td>
                                                    <?php if ($datos['proveedor_status'] == 1): ?>
                                                        <span class="badge bg-success">Activo</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-danger">Inactivo</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo !empty($datos["nombre"]) ? $datos["nombre"] : 'No disponible'; ?></td>
                                            </tr>
                                        <?php } ?>
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








</div>