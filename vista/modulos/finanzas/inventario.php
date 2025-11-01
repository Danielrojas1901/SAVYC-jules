<div class="tab-pane fade" id="inventario" role="tabpanel">
    <div class="card">
        <div class="card-body">
            <h1>Rotación de Inventario</h1>
            <p class="text-muted">Análisis de rotación y estado del inventario. Los indicadores muestran el estado actual y tendencias para la toma de decisiones.</p>
            
            

            <!-- Leyenda de indicadores -->
            
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card border">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <h6 class="card-title text-dark mb-3">
                                        <i class="fas fa-info-circle text-primary me-2"></i>
                                        Leyenda de Indicadores
                                    </h6>
                                </div>
                            </div>
                            
                            <!-- Estado del Stock -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6 class="text-dark mb-3">
                                        <i class="fas fa-boxes text-secondary me-2"></i>
                                        Estado del Stock
                                    </h6>
                                    <div class="row g-2">
                                        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-6">
                                            <div class="d-flex align-items-center p-2 border rounded bg-light" 
                                                 data-bs-toggle="tooltip" 
                                                 data-bs-placement="top" 
                                                 title="Producto sin unidades disponibles en inventario">
                                                <span class="badge bg-danger me-2">Sin Stock</span>
                                                <small class="text-muted d-none d-md-block">Sin unidades</small>
                                            </div>
                                        </div>
                                        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-6">
                                            <div class="d-flex align-items-center p-2 border rounded bg-light" 
                                                 data-bs-toggle="tooltip" 
                                                 data-bs-placement="top" 
                                                 title="Stock por debajo del nivel mínimo recomendado">
                                                <span class="badge bg-warning text-dark me-2">Crítico</span>
                                                <small class="text-muted d-none d-md-block">Nivel bajo</small>
                                            </div>
                                        </div>
                                        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-6">
                                            <div class="d-flex align-items-center p-2 border rounded bg-light" 
                                                 data-bs-toggle="tooltip" 
                                                 data-bs-placement="top" 
                                                 title="Stock cercano al nivel mínimo">
                                                <span class="badge bg-info text-white me-2">Bajo</span>
                                                <small class="text-muted d-none d-md-block">Atención</small>
                                            </div>
                                        </div>
                                        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-6">
                                            <div class="d-flex align-items-center p-2 border rounded bg-light" 
                                                 data-bs-toggle="tooltip" 
                                                 data-bs-placement="top" 
                                                 title="Stock por encima del nivel máximo recomendado">
                                                <span class="badge bg-warning text-dark me-2">Exceso</span>
                                                <small class="text-muted d-none d-md-block">Sobre stock</small>
                                            </div>
                                        </div>
                                        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-6">
                                            <div class="d-flex align-items-center p-2 border rounded bg-light" 
                                                 data-bs-toggle="tooltip" 
                                                 data-bs-placement="top" 
                                                 title="Stock dentro de los niveles óptimos">
                                                <span class="badge bg-success me-2">Normal</span>
                                                <small class="text-muted d-none d-md-block">Óptimo</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tendencia de Rotación -->
                            <div class="row mb-3">
                                <div class="col-12">
                                    <h6 class="text-dark mb-3">
                                        <i class="fas fa-chart-line text-secondary me-2"></i>
                                        Tendencia de Rotación
                                    </h6>
                                    <div class="row g-2">
                                        <div class="col-lg-3 col-md-4 col-sm-6 col-6">
                                            <div class="d-flex align-items-center p-2 border rounded bg-light" 
                                                 data-bs-toggle="tooltip" 
                                                 data-bs-placement="top" 
                                                 title="Los días de rotación están disminuyendo, indicando mejor eficiencia">
                                                <i class="fas fa-arrow-down text-success me-2"></i>
                                                <span class="text-dark fw-medium">Mejorando</span>
                                                <small class="text-muted ms-auto d-none d-lg-block">↓ Días</small>
                                            </div>
                                        </div>
                                        <div class="col-lg-3 col-md-4 col-sm-6 col-6">
                                            <div class="d-flex align-items-center p-2 border rounded bg-light" 
                                                 data-bs-toggle="tooltip" 
                                                 data-bs-placement="top" 
                                                 title="Los días de rotación están aumentando, indicando menor eficiencia">
                                                <i class="fas fa-arrow-up text-danger me-2"></i>
                                                <span class="text-dark fw-medium">Empeorando</span>
                                                <small class="text-muted ms-auto d-none d-lg-block">↑ Días</small>
                                            </div>
                                        </div>
                                        <div class="col-lg-3 col-md-4 col-sm-6 col-6">
                                            <div class="d-flex align-items-center p-2 border rounded bg-light" 
                                                 data-bs-toggle="tooltip" 
                                                 data-bs-placement="top" 
                                                 title="Los días de rotación se mantienen estables">
                                                <i class="fas fa-equals text-muted me-2"></i>
                                                <span class="text-dark fw-medium">Estable</span>
                                                <small class="text-muted ms-auto d-none d-lg-block">= Días</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Estado de Rotación -->
                            <div class="row">
                                <div class="col-12">
                                    <h6 class="text-dark mb-3">
                                        <i class="fas fa-tachometer-alt text-secondary me-2"></i>
                                        Estado de Rotación
                                    </h6>
                                    <div class="row g-2">
                                        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-6">
                                            <div class="d-flex align-items-center p-2 border rounded bg-light" 
                                                 data-bs-toggle="tooltip" 
                                                 data-bs-placement="top" 
                                                 title="Rotación muy lenta, producto se vende muy despacio">
                                                <span class="badge bg-danger me-2">Alto</span>
                                                <small class="text-muted d-none d-md-block">Lenta</small>
                                            </div>
                                        </div>
                                        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-6">
                                            <div class="d-flex align-items-center p-2 border rounded bg-light" 
                                                 data-bs-toggle="tooltip" 
                                                 data-bs-placement="top" 
                                                 title="Rotación muy rápida, producto se vende muy rápido">
                                                <span class="badge bg-success me-2">Bajo</span>
                                                <small class="text-muted d-none d-md-block">Rápida</small>
                                            </div>
                                        </div>
                                        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-6">
                                            <div class="d-flex align-items-center p-2 border rounded bg-light" 
                                                 data-bs-toggle="tooltip" 
                                                 data-bs-placement="top" 
                                                 title="Rotación dentro de parámetros normales">
                                                <span class="badge bg-info me-2">Normal</span>
                                                <small class="text-muted d-none d-md-block">Óptima</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Información adicional -->
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="alert alert-info border-0 bg-light">
                                        <div class="d-flex">
                                            <i class="fas fa-lightbulb text-info me-2 mt-1"></i>
                                            <div>
                                                <small class="text-muted">
                                                    <strong>Nota:</strong> Los días de rotación indican cuánto tiempo permanece un producto en inventario antes de venderse. 
                                                    Un valor más bajo significa mejor rotación. Los indicadores se basan en el promedio histórico del producto.
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-lg-8 col-md-7 col-12">
                    <div class="form-group">
                        <label class="text-muted small mb-1">Período:</label>
                        <div class="d-flex align-items-center bg-light rounded p-2">
                            <div class="d-flex">
                                <select id="mes-inventario" name="mes-inventario" class="form-control form-control-sm mr-2">
                                    <!-- opciones desde js -->
                                </select>
                                <select id="año-inventario" name="año-inventario" class="form-control form-control-sm" style="width: 100px;">
                                    <!-- opciones desde js -->
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-5 col-12 d-flex align-items-end">
                    <div class="form-group w-100">
                        <?php 
                        $disabled = empty($_SESSION['rif']) ? 'disabled' : '';
                        $title = empty($_SESSION['rif'])
                            ? 'No se puede generar el reporte de rotación, debes registrar la informacion de la empresa'
                            : 'Exportar reporte de rotación de inventario en PDF';
                        $btnClass = empty($_SESSION['rif']) ? 'btn-outline-secondary' : 'btn-outline-primary';
                        ?>
                        <button id="exportar-rotacion-general-pdf" 
                                class="btn <?php echo $btnClass; ?> w-100"
                                <?php echo $disabled; ?> 
                                title="<?php echo $title; ?>">
                            <i class="fas fa-file-pdf mr-2"></i>
                            Exportar Reporte PDF
                        </button>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table id="tabla-rotacion" class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Producto</th>
                            <th class="text-center">Estado</th>
                            <th class="text-end">Stock Inicial</th>
                            <th class="text-end">Stock Final</th>
                            <th class="text-end">Ventas</th>
                            <th class="text-end">Días de Rotación</th>
                            <th class="text-end">Promedio</th>
                            <th class="text-center">Estado Rotación</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div> 