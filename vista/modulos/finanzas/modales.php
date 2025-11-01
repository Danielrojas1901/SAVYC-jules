<!-- Modal para el gráfico de rotación -->
<div class="modal fade" id="modal-rotacion" tabindex="-1" aria-labelledby="modal-rotacion-label" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-rotacion-label">Detalle de Rotación</h5>
                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="card mb-4">
                    <div class="card-body">
                        <div style="height: 400px;">
                            <canvas id="grafico-rotacion"></canvas>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="tabla-detalle-rotacion" class="table table-bordered table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Período</th>
                                        <th class="text-center">Estado</th>
                                        <th class="text-end">Stock Inicial</th>
                                        <th class="text-end">Stock Final</th>
                                        <th class="text-end">Ventas</th>
                                        <th class="text-end">Días de Rotación</th>
                                        <th class="text-end">Promedio</th>
                                        <th class="text-center">Estado Rotación</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <?php 
                $disabled = empty($_SESSION['rif']) ? 'disabled' : '';
                $title = empty($_SESSION['rif'])
                    ? 'No se puede generar el reporte de rotación, debes registrar la informacion de la empresa'
                    : 'Exportar reporte detallado de rotación en PDF';
                $btnClass = empty($_SESSION['rif']) ? 'btn-secondary' : 'btn-primary';
                ?>
                <button id="exportar-rotacion-pdf" 
                        class="btn <?php echo $btnClass; ?>"
                        <?php echo $disabled; ?> 
                        title="<?php echo $title; ?>">
                    <i class="fas fa-file-pdf me-2"></i>Exportar PDF
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para proyecciones -->
<div class="modal fade" id="modal-proyeccion" tabindex="-1" aria-labelledby="modal-proyeccion-label" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-proyeccion-label">Detalle de Proyección</h5>
                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div style="height: 400px;">
                    <canvas id="grafico-proyeccion"></canvas>
                </div>
                <div id="resumen-proyeccion" class="mt-4"></div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para rentabilidad -->
<div class="modal fade" id="modal-rentabilidad" tabindex="-1" aria-labelledby="modal-rentabilidad-label" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-rentabilidad-label">Detalle de Rentabilidad</h5>
                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div style="height: 400px;">
                    <canvas id="grafico-rentabilidad"></canvas>
                </div>
                <div id="resumen-rentabilidad" class="mt-4"></div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para precisión -->
<div class="modal fade" id="modal-precision" tabindex="-1" aria-labelledby="modal-precision-label" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-precision-label">Precisión Histórica</h5>
                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div style="height: 400px;">
                    <canvas id="grafico-precision"></canvas>
                </div>
                <div id="resumen-precision" class="mt-4"></div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para registrar presupuesto -->
<div class="modal fade" id="modal-registro-presupuesto" tabindex="-1" aria-labelledby="modal-registro-presupuesto-label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-registro-presupuesto-label">Registrar Nuevo Presupuesto</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form-registro-presupuesto">
                    <div class="form-group">
                        <label for="categoria-presupuesto">Categoría de Gasto<span class="text-danger">*</span></label>
                        <select id="categoria-presupuesto" name="categoria-presupuesto" class="form-control" required>
                            <option value="" selected disabled>Seleccione una categoría</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="mes-presupuesto">Mes<span class="text-danger">*</span></label>
                        <div class="row">
                            <div class="col-8">
                                <select id="mes-presupuesto" name="mes-presupuesto" class="form-control" required>
                                    <!-- opciones desde js -->
                                </select>
                            </div>
                            <div class="col-4">
                                <select id="año-presupuesto" name="año-presupuesto" class="form-control" required>
                                    <!-- opciones desde js -->
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="monto-presupuesto">Monto Presupuestado<span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">Bs.</span>
                            <input type="number" step="0.01" min="0" id="monto-presupuesto" name="monto-presupuesto" class="form-control" placeholder="0.00" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="descripcion-presupuesto">Descripción</label>
                        <textarea id="descripcion-presupuesto" name="descripcion-presupuesto" class="form-control" rows="3" placeholder="Ingrese una descripción o notas adicionales"></textarea>
                    </div>
                    <div class="alert alert-light d-flex align-items-center mt-3" role="alert">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <span>Los campos marcados con (*) son obligatorios</span>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="submit" form="form-registro-presupuesto" class="btn btn-primary">Guardar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para detalle de presupuesto -->
<div class="modal fade" id="modal-detalle-presupuesto" tabindex="-1" aria-labelledby="modal-detalle-presupuesto-label" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-detalle-presupuesto-label">Detalle de Presupuesto</h5>
                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="form-group">
                            <label class="text-muted small mb-1">Período de Visualización:</label>
                            <div class="row">
                                <div class="col-lg-6 col-md-12">
                                    <div class="d-flex align-items-center bg-light rounded p-2">
                                        <label class="mr-2 text-nowrap small text-muted mb-0 d-none d-sm-block">Desde:</label>
                                        <label class="mr-2 small text-muted mb-0 d-sm-none">Desde:</label>
                                        <div class="d-flex flex-grow-1">
                                            <select id="mes-inicio-detalle" name="mes-inicio-detalle" class="form-control form-control-sm mr-2">
                                                <!-- opciones desde js -->
                                            </select>
                                            <select id="año-inicio-detalle" name="año-inicio-detalle" class="form-control form-control-sm" style="min-width: 80px; max-width: 100px;">
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
                                            <select id="mes-fin-detalle" name="mes-fin-detalle" class="form-control form-control-sm mr-2">
                                                <!-- opciones desde js -->
                                            </select>
                                            <select id="año-fin-detalle" name="año-fin-detalle" class="form-control form-control-sm" style="min-width: 80px; max-width: 100px;">
                                                <!-- opciones desde js -->
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border mb-4">
                    <div class="card-body">
                        <div class="chart-container" style="position: relative; height: 50vh; min-height: 300px; width: 100%;">
                            <canvas id="grafico-detalle-presupuesto"></canvas>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-title mb-0" id="detalle-categoria-titulo"></h5>
                            <div class="text-muted">
                                <small>Detalle mensual de presupuestos y gastos</small>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table id="tabla-detalle-presupuesto" class="table table-bordered table-striped table-hover" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>Mes</th>
                                        <th class="text-end">Presupuesto</th>
                                        <th class="text-end d-none d-md-table-cell">Gasto Real</th>
                                        <th class="text-end d-none d-lg-table-cell">Diferencia</th>
                                        <th class="text-end">% Utilizado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <?php 
                $disabled = empty($_SESSION['rif']) ? 'disabled' : '';
                $title = empty($_SESSION['rif'])
                    ? 'No se puede generar el reporte de presupuesto, debes registrar la informacion de la empresa'
                    : 'Exportar reporte detallado de presupuesto en PDF';
                $btnClass = empty($_SESSION['rif']) ? 'btn-secondary' : 'btn-primary';
                ?>
                <button id="exportar-pdf" 
                        class="btn <?php echo $btnClass; ?>"
                        <?php echo $disabled; ?> 
                        title="<?php echo $title; ?>">
                    <i class="fas fa-file-pdf me-2"></i>Exportar PDF
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para editar presupuesto -->
<div class="modal fade" id="modal-editar-presupuesto" tabindex="-1" aria-labelledby="modal-editar-presupuesto-label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-editar-presupuesto-label">Editar Presupuesto</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form-editar-presupuesto">
                    <input type="hidden" id="edit-cod-cat-gasto">
                    <input type="hidden" id="edit-mes">
                    
                    <div class="form-group">
                        <label for="edit-monto-presupuesto">Monto Presupuestado<span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">Bs.</span>
                            <input type="number" step="0.01" min="0" id="edit-monto-presupuesto" name="edit-monto-presupuesto" class="form-control" placeholder="0.00" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="edit-descripcion-presupuesto">Descripción</label>
                        <textarea id="edit-descripcion-presupuesto" name="edit-descripcion-presupuesto" class="form-control" rows="3" placeholder="Ingrese una descripción o notas adicionales"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="submit" form="form-editar-presupuesto" class="btn btn-primary">Guardar cambios</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para confirmar eliminación -->
<div class="modal fade" id="modal-eliminar-presupuesto" tabindex="-1" aria-labelledby="modal-eliminar-presupuesto-label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-eliminar-presupuesto-label">Confirmar Eliminación</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>¿Está seguro que desea eliminar este presupuesto?</p>
                <p>Esta acción no se puede deshacer.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="btn-confirmar-eliminar">Eliminar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para detalle de rentabilidad -->
<div class="modal fade" id="modal-detalle-rentabilidad" tabindex="-1" aria-labelledby="modal-detalle-rentabilidad-label" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-detalle-rentabilidad-label">Detalle de Rentabilidad</h5>
                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="form-group">
                            <label class="text-muted small mb-1">Período de Visualización:</label>
                            <div class="row">
                                <div class="col-lg-6 col-md-12">
                                    <div class="d-flex align-items-center bg-light rounded p-2">
                                        <label class="mr-2 text-nowrap small text-muted mb-0 d-none d-sm-block">Desde:</label>
                                        <label class="mr-2 small text-muted mb-0 d-sm-none">Desde:</label>
                                        <div class="d-flex flex-grow-1">
                                            <select id="mes-inicio-detalle-rentabilidad" name="mes-inicio-detalle-rentabilidad" class="form-control form-control-sm mr-2">
                                                <option value="1">Enero</option>
                                                <option value="2">Febrero</option>
                                                <option value="3">Marzo</option>
                                                <option value="4">Abril</option>
                                                <option value="5">Mayo</option>
                                                <option value="6">Junio</option>
                                                <option value="7">Julio</option>
                                                <option value="8">Agosto</option>
                                                <option value="9">Septiembre</option>
                                                <option value="10">Octubre</option>
                                                <option value="11">Noviembre</option>
                                                <option value="12">Diciembre</option>
                                            </select>
                                            <select id="año-inicio-detalle-rentabilidad" name="año-inicio-detalle-rentabilidad" class="form-control form-control-sm" style="min-width: 80px; max-width: 100px;">
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
                                            <select id="mes-fin-detalle-rentabilidad" name="mes-fin-detalle-rentabilidad" class="form-control form-control-sm mr-2">
                                                <option value="1">Enero</option>
                                                <option value="2">Febrero</option>
                                                <option value="3">Marzo</option>
                                                <option value="4">Abril</option>
                                                <option value="5">Mayo</option>
                                                <option value="6">Junio</option>
                                                <option value="7">Julio</option>
                                                <option value="8">Agosto</option>
                                                <option value="9">Septiembre</option>
                                                <option value="10">Octubre</option>
                                                <option value="11">Noviembre</option>
                                                <option value="12">Diciembre</option>
                                            </select>
                                            <select id="año-fin-detalle-rentabilidad" name="año-fin-detalle-rentabilidad" class="form-control form-control-sm" style="min-width: 80px; max-width: 100px;">
                                                <!-- opciones desde js -->
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border mb-4">
                    <div class="card-body">
                        <div class="chart-container" style="position: relative; height: 50vh; min-height: 300px; width: 100%;">
                            <canvas id="grafico-detalle-rentabilidad"></canvas>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-title mb-0" id="detalle-rentabilidad-titulo"></h5>
                            <div class="text-muted">
                                <small>Detalle mensual de ventas y costos</small>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table id="tabla-detalle-rentabilidad" class="table table-bordered table-striped table-hover" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th class="text-end">Ventas Totales</th>
                                        <th class="text-end d-none d-md-table-cell">Costo de Ventas</th>
                                        <th class="text-end d-none d-lg-table-cell">Margen Bruto</th>
                                        <th class="text-end">Rentabilidad</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <?php 
                $disabled = empty($_SESSION['rif']) ? 'disabled' : '';
                $title = empty($_SESSION['rif'])
                    ? 'No se puede generar el reporte de rentabilidad, debes registrar la informacion de la empresa'
                    : 'Exportar reporte detallado de rentabilidad en PDF';
                $btnClass = empty($_SESSION['rif']) ? 'btn-secondary' : 'btn-primary';
                ?>
                <button id="exportar-rentabilidad-pdf" 
                        class="btn <?php echo $btnClass; ?>"
                        <?php echo $disabled; ?> 
                        title="<?php echo $title; ?>">
                    <i class="fas fa-file-pdf me-2"></i>Exportar PDF
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para detalle de proyección -->
<div class="modal fade" id="modal-detalle-proyeccion" tabindex="-1" aria-labelledby="modal-detalle-proyeccion-label" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-detalle-proyeccion-label">Detalle de Proyección</h5>
                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-4">
                    <div class="col-lg-6 col-md-6 col-12">
                        <div class="form-group">
                            <label for="periodo-detalle-proyeccion">Período de Visualización:</label>
                            <select id="periodo-detalle-proyeccion" name="periodo-detalle-proyeccion" class="form-control">
                                <option value="3">Próximos 3 meses</option>
                                <option value="6" selected>Próximos 6 meses</option>
                                <option value="12">Próximo año</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="card border mb-4">
                    <div class="card-body">
                        <div style="position: relative; height: 50vh; min-height: 300px; width: 100%;">
                            <canvas id="grafico-detalle-proyeccion"></canvas>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-title mb-0" id="detalle-proyeccion-titulo"></h5>
                            <div class="text-muted">
                                <small>Detalle mensual de proyecciones y valores reales</small>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table id="tabla-detalle-proyeccion" class="table table-bordered table-striped table-hover" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th class="text-end">Valor Proyectado</th>
                                        <th class="text-end d-none d-md-table-cell">Valor Real</th>
                                        <th class="text-end">Precisión</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <?php 
                $disabled = empty($_SESSION['rif']) ? 'disabled' : '';
                $title = empty($_SESSION['rif'])
                    ? 'No se puede generar el reporte de proyección, debes registrar la informacion de la empresa'
                    : 'Exportar reporte detallado de proyección en PDF';
                $btnClass = empty($_SESSION['rif']) ? 'btn-secondary' : 'btn-primary';
                ?>
                <button id="exportar-proyeccion-pdf" 
                        class="btn <?php echo $btnClass; ?>"
                        <?php echo $disabled; ?> 
                        title="<?php echo $title; ?>">
                    <i class="fas fa-file-pdf me-2"></i>Exportar PDF
                </button>
            </div>
        </div>
    </div>
</div>

<!-- agregar libreria de jsPDF -->
<script src="vista/plugins/jspdf/jspdf.umd.min.js"></script>
<script src="vista/plugins/html2canvas/html2canvas.min.js"></script>