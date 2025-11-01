<div class="tab-pane fade" id="presupuestos" role="tabpanel">
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <h1>Presupuestos</h1>
                    <p class="text-muted mb-4">Gestión y seguimiento de presupuestos por categoría.</p>
                </div>
            </div>
            
            <div class="row mb-4">
                <div class="col-lg-8 col-md-8 col-12">
                    <div class="form-group">
                        <label class="text-muted small mb-1">Período de Visualización:</label>
                        <div class="row">
                            <div class="col-lg-6 col-md-12">
                                <div class="d-flex align-items-center bg-light rounded p-2">
                                    <label class="mr-2 text-nowrap small text-muted mb-0 d-none d-sm-block">Desde:</label>
                                    <label class="mr-2 small text-muted mb-0 d-sm-none">Desde:</label>
                                    <div class="d-flex flex-grow-1">
                                        <select id="mes-inicio-vis" name="mes-inicio-vis" class="form-control form-control-sm mr-2">
                                            <!-- opciones desde js -->
                                        </select>
                                        <select id="año-inicio-vis" name="año-inicio-vis" class="form-control form-control-sm" style="min-width: 80px; max-width: 100px;">
                                            <!-- opciones desde js -->
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-12">
                                <div class="d-flex align-items-center bg-light rounded p-2">
                                    <label class="mr-2 text-nowrap small text-muted mb-0 d-none d-sm-block">Hasta:</label>
                                    <label class="mr-2 small text-muted mb-0 d-sm-none">Hasta:</label>
                                    <div class="d-flex flex-grow-1">
                                        <select id="mes-fin-vis" name="mes-fin-vis" class="form-control form-control-sm mr-2">
                                            <!-- opciones desde js -->
                                        </select>
                                        <select id="año-fin-vis" name="año-fin-vis" class="form-control form-control-sm" style="min-width: 80px; max-width: 100px;">
                                            <!-- opciones desde js -->
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-4 col-12 d-flex align-items-end">
                    <?php if (!empty($_SESSION["permisos"]["finanza"]["consultar"])): ?>
                    <div class="form-group w-100">
                        <?php 
                        $disabled = empty($_SESSION['rif']) ? 'disabled' : '';
                        $title = empty($_SESSION['rif'])
                            ? 'No se puede generar el reporte de presupuestos, debes registrar la informacion de la empresa'
                            : 'Exportar reporte de presupuestos en PDF';
                        $btnClass = empty($_SESSION['rif']) ? 'btn-outline-secondary' : 'btn-outline-primary';
                        ?>
                        <button id="exportar-presupuestos-general-pdf" 
                                class="btn <?php echo $btnClass; ?> w-100"
                                <?php echo $disabled; ?> 
                                title="<?php echo $title; ?>">
                            <i class="fas fa-file-pdf mr-2"></i>Exportar PDF
                        </button>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-12">
                    <div class="text-center">
                        <h3 class="h5 fw-bold">Presupuestos y gastos totales</h3>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-12">
                    <div class="card border">
                        <div class="card-body">
                            <div style="position: relative; height: 50vh; min-height: 300px; width: 100%;">
                                <canvas id="grafico-presupuestos"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-12">
                    <h3 class="h5 fw-bold mb-0">Presupuestos para el mes actual</h3>
                </div>
                <div class="col-lg-3 col-md-6 col-12 mt-3">
                    <?php if (!empty($_SESSION["permisos"]["finanza"]["registrar"])): ?>
                    <button type="button" class="btn btn-primary w-100 h-100 d-flex align-items-center justify-content-center" data-toggle="modal" data-target="#modal-registro-presupuesto">
                        <i class="fas fa-plus-circle me-2"></i>
                        <span class="d-none d-sm-inline">Registrar Nuevo Presupuesto</span>
                        <span class="d-sm-none">Nuevo Presupuesto</span>
                    </button>
                    <?php endif; ?>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="table-responsive">
                        <table id="tabla-presupuestos" class="table table-bordered table-striped table-hover" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>Categoría</th>
                                    <th class="text-end">Presupuesto</th>
                                    <th class="text-end">Gasto Real</th>
                                    <th class="text-end">Diferencia</th>
                                    <th class="text-end">% Utilizado</th>
                                    <th class="text-center">Estado</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> 