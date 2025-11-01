<?php
require_once "controlador/finanzas.php";
?>
<div class="tab-pane fade" id="proyecciones" role="tabpanel">
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <h1>Proyecciones de Ventas</h1>
                    <p class="text-muted mb-4">Información sobre proyecciones de ventas.</p>
                </div>
            </div>
            
            <div class="row mb-4">
                <div class="col-lg-8 col-md-8 col-12">
                    <div class="form-group">
                        <label class="text-muted small mb-1">Configuración de Análisis:</label>
                        <div class="row">
                            <div class="col-lg-6 col-md-12">
                                <div class="d-flex align-items-center bg-light rounded p-2">
                                    <label class="mr-2 text-nowrap small text-muted mb-0 d-none d-sm-block">Tipo:</label>
                                    <label class="mr-2 small text-muted mb-0 d-sm-none">Tipo:</label>
                                    <div class="d-flex flex-grow-1">
                                        <select id="tipo-analisis" name="tipo-analisis" class="form-control form-control-sm">
                                            <option value="futuro">Proyecciones Futuras</option>
                                            <option value="historico">Precisión Histórica</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-12">
                                <div class="d-flex align-items-center bg-light rounded p-2">
                                    <label class="mr-2 text-nowrap small text-muted mb-0 d-none d-sm-block">Período:</label>
                                    <label class="mr-2 small text-muted mb-0 d-sm-none">Período:</label>
                                    <div class="d-flex flex-grow-1">
                                        <select id="periodo-proyeccion" name="periodo-proyeccion" class="form-control form-control-sm">
                                            <option value="3">Próximos 3 meses</option>
                                            <option value="6">Próximos 6 meses</option>
                                            <option value="12">Próximo año</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-4 col-12 d-flex align-items-end">
                    <div class="form-group w-100">
                        <?php 
                        $disabled = empty($_SESSION['rif']) ? 'disabled' : '';
                        $title = empty($_SESSION['rif'])
                            ? 'No se puede generar el reporte de proyecciones, debes registrar la informacion de la empresa'
                            : 'Exportar reporte de proyecciones de ventas en PDF';
                        $btnClass = empty($_SESSION['rif']) ? 'btn-outline-secondary' : 'btn-outline-primary';
                        ?>
                        <button id="exportar-proyecciones-general-pdf" 
                                class="btn <?php echo $btnClass; ?> w-100"
                                <?php echo $disabled; ?> 
                                title="<?php echo $title; ?>">
                            <i class="fas fa-file-pdf mr-2"></i>Exportar PDF
                        </button>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-12">
                    <div class="card border">
                        <div class="card-body">
                            <div style="position: relative; height: 50vh; min-height: 300px; width: 100%;">
                                <canvas id="grafico-proyecciones"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-12">
                    <div class="table-responsive">
                        <table id="tabla-proyecciones" class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Producto</th>
                                    <th class="d-none d-lg-table-cell">Ventas Últimos 6 Meses</th>
                                    <th class="text-end">Proyección 3M</th>
                                    <th class="text-end d-none d-md-table-cell">Proyección 6M</th>
                                    <th class="text-end d-none d-lg-table-cell">Proyección 12M</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="table-responsive">
                        <table id="tabla-precision" class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Producto</th>
                                    <th class="text-end">Precisión Promedio</th>
                                    <th class="text-end d-none d-md-table-cell">Mejor Precisión</th>
                                    <th class="text-end d-none d-md-table-cell">Peor Precisión</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> 