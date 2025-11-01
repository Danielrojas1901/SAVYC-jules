<div class="tab-pane fade show active" id="cuentas" role="tabpanel">
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <h1>Análisis de Cuentas</h1>
                    <p class="text-muted mb-4">Aquí puedes ver el análisis detallado de movimientos por cuenta administrativa.</p>
                </div>
            </div>
            
            <div class="row mb-4">
                <div class="col-12">
                    <div class="form-group">
                        <label for="cuenta">Cuenta Administrativa:</label>
                        <select id="cuenta" name="cuenta" class="form-control">
                            <option value="" selected disabled>Cargando cuentas...</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="row mb-4">
                <div class="col-lg-8 col-md-8 col-12">
                    <div class="form-group">
                        <label class="text-muted small mb-1">Período de Análisis:</label>
                        <div class="row">
                            <div class="col-lg-6 col-md-12">
                                <div class="d-flex align-items-center bg-light rounded p-2">
                                    <label class="mr-2 text-nowrap small text-muted mb-0 d-none d-sm-block">Desde:</label>
                                    <label class="mr-2 small text-muted mb-0 d-sm-none">Desde:</label>
                                    <div class="d-flex flex-grow-1">
                                        <select id="mes-inicio" name="mes-inicio" class="form-control form-control-sm mr-2">
                                            <!-- opciones desde js -->
                                        </select>
                                        <select id="ano-inicio" name="ano-inicio" class="form-control form-control-sm" style="min-width: 80px; max-width: 100px;">
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
                                        <select id="mes-fin" name="mes-fin" class="form-control form-control-sm mr-2">
                                            <!-- opciones desde js -->
                                        </select>
                                        <select id="ano-fin" name="ano-fin" class="form-control form-control-sm" style="min-width: 80px; max-width: 100px;">
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
                            ? 'No se puede generar el reporte de cuentas, debes registrar la informacion de la empresa'
                            : 'Exportar reporte de análisis de cuentas en PDF';
                        $btnClass = empty($_SESSION['rif']) ? 'btn-outline-secondary' : 'btn-outline-primary';
                        ?>
                        <button id="exportar-cuentas-pdf" 
                                class="btn <?php echo $btnClass; ?> w-100"
                                <?php echo $disabled; ?> 
                                title="<?php echo $title; ?>">
                            <i class="fas fa-file-pdf mr-2"></i>Exportar PDF
                        </button>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card border">
                        <div class="card-body">
                            <div id="grafico-cuentas" class="chart-container" style="position: relative; height: 50vh; min-height: 300px; width: 100%;">
                                <canvas></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> 