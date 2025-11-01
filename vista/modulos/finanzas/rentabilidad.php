<div class="tab-pane fade" id="rentabilidad" role="tabpanel">
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <h1>Análisis de Rentabilidad</h1>
                    <p class="text-muted mb-4">Información sobre la rentabilidad de productos e inventario.</p>
                </div>
            </div>
            
            <div class="row mb-4">
                <div class="col-lg-8 col-md-7 col-12">
                    <div class="form-group">
                        <label class="text-muted small mb-1">Período de Visualización:</label>
                        <div class="row">
                            <div class="col-lg-6 col-md-12">
                                <div class="d-flex align-items-center bg-light rounded p-2">
                                    <label class="mr-2 text-nowrap small text-muted mb-0 d-none d-sm-block">Desde:</label>
                                    <label class="mr-2 small text-muted mb-0 d-sm-none">Desde:</label>
                                    <div class="d-flex flex-grow-1">
                                        <select id="mes-inicio-rentabilidad" name="mes-inicio-rentabilidad" class="form-control form-control-sm mr-2">
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
                                        <select id="año-inicio-rentabilidad" name="año-inicio-rentabilidad" class="form-control form-control-sm" style="min-width: 80px; max-width: 100px;">
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
                                        <select id="mes-fin-rentabilidad" name="mes-fin-rentabilidad" class="form-control form-control-sm mr-2">
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
                                        <select id="año-fin-rentabilidad" name="año-fin-rentabilidad" class="form-control form-control-sm" style="min-width: 80px; max-width: 100px;">
                                            <!-- opciones desde js -->
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-5 col-12 d-flex align-items-end">
                    <div class="form-group w-100">
                        <?php 
                        $disabled = empty($_SESSION['rif']) ? 'disabled' : '';
                        $title = empty($_SESSION['rif'])
                            ? 'No se puede generar el reporte de rentabilidad, debes registrar la informacion de la empresa'
                            : 'Exportar reporte de análisis de rentabilidad en PDF';
                        $btnClass = empty($_SESSION['rif']) ? 'btn-outline-secondary' : 'btn-outline-primary';
                        ?>
                        <button id="exportar-rentabilidad-general-pdf" 
                                class="btn <?php echo $btnClass; ?> w-100"
                                <?php echo $disabled; ?> 
                                title="<?php echo $title; ?>">
                            <i class="fas fa-file-pdf mr-2"></i>
                            Exportar Reporte PDF
                        </button>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-12">
                    <div class="table-responsive">
                        <table id="tabla-rentabilidad" class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Producto</th>
                                    <th class="text-end">Ventas Totales</th>
                                    <th class="text-end d-none d-md-table-cell">Costo de Ventas</th>
                                    <th class="text-end d-none d-lg-table-cell">Margen Bruto</th>
                                    <th class="text-end">Rentabilidad</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-6 col-md-6 col-12 mb-3">
                    <div class="card bg-light border">
                        <div class="card-body text-center">
                            <h5 class="card-title">Rentabilidad Promedio</h5>
                            <p class="card-text h3 text-primary" id="rentabilidad-promedio">0%</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 col-12 mb-3">
                    <div class="card bg-light border">
                        <div class="card-body text-center">
                            <h5 class="card-title">Margen Bruto Total</h5>
                            <p class="card-text h3 text-success" id="margen-bruto-total">Bs. 0,00</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> 