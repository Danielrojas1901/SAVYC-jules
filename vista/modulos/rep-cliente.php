<?php
require_once 'controlador/reportec.php';
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Reporte De Clientes </h1>
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
                                <input type="hidden" name="tipo" value="clientes">
                                <?php if(!empty($_SESSION["permisos"]["reporte"])): ?>
                                    <button class="btn btn-danger" name="pdf" alt="Generar pdf" id="pdf">Generar PDF</button>
                                <?php endif; ?>
                            </form>
                        </div>
                        <div class="card-body">
                            <!-- Tabla de clientes -->
                            <div class="table-responsive">
                                <table id="cliente" class="table table-bordered table-striped table-hover datatable" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>Codigo</th>
                                            <th>Nombre</th>
                                            <th>Apellido</th>
                                            <th>Cédula-Rif</th>
                                            <th>Teléfono</th>
                                            <th>Email</th>
                                            <th>Dirección</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($registro as $datos) { ?>
                                            <?php if ($datos['status'] != 2): ?>
                                                <tr>
                                                    <td> <?php echo $datos["cod_cliente"] ?></td>
                                                    <td> <?php echo $datos["nombre"] ?></td>
                                                    <td> <?php echo $datos["apellido"] ?></td>
                                                    <td> <?php echo $datos["cedula_rif"] ?></td>
                                                    <td> <?php echo $datos["telefono"] ? $datos["telefono"] : 'No disponible' ?></td>
                                                    <td> <?php echo $datos["email"] ? $datos["email"] : 'No disponible' ?></td>
                                                    <td> <?php echo $datos["direccion"] ?  $datos["direccion"] : 'No disponible' ?></td>
                                                </tr>
                                            <?php endif; ?>
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