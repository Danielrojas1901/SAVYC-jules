<?php require_once 'controlador/general.php';
require_once 'controlador/inicio.php'; ?>
<!-- Preloader-->
<div class="preloader flex-column justify-content-center align-items-center">
    <?php
    if (isset($_SESSION["logo"])): ?>
        <img src="<?php echo $_SESSION["logo"]; ?>" alt="Quesera Don Pedro" class="" height="200" width="200">
    <?php else: ?>
        <img src="vista/dist/img/logo_generico.png" alt="Quesera Don Pedro" class="" height="200" width="200">
    <?php endif; ?>
</div>

<!-- Content Wrapper -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>INICIO</h1>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-3 col-md-6 col-sm-6 col-12 mb-3">
                    <div class="small-box bg-info" style="height: 100px;">
                        <div class="inner d-flex flex-column justify-content-center h-100 px-3 py-2">
                            <p class="mb-1 text-truncate" style="font-size: clamp(0.90rem, 2vw, 1rem);">Ventas de hoy</p>
                            <h3 class="mb-0 text-truncate" style="font-size: clamp(1.4rem, 3vw, 1.8rem); font-weight: bold;"><?php $total=$t_v['total_ventas'] ?? 0.00; 
                                    echo number_format($total,2,',','.');?> Bs</h3>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 col-sm-6 col-12 mb-3">
                    <div class="small-box bg-primary" style="height: 100px;">
                        <div class="inner d-flex flex-column justify-content-center h-100 px-3 py-2">
                            <p class="mb-1 text-truncate" style="font-size: clamp(0.85rem, 2vw, 1rem);">Ventas esta semana</p>
                            <h3 class="mb-0 text-truncate" style="font-size: clamp(1.2rem, 3vw, 1.8rem); font-weight: bold;"><?php $semana=$t_s['total_semana'] ?? 0.00;
                                    echo number_format($semana,2,',','.');?> Bs</h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 col-12 mb-3">
                    <div class="small-box bg-primary" style="height: 100px;">
                        <div class="inner d-flex flex-column justify-content-center h-100 px-3 py-2">
                            <p class="mb-1 text-truncate" style="font-size: clamp(0.85rem, 2vw, 1rem);">Clientes</p>
                            <h3 class="mb-0 text-truncate" style="font-size: clamp(1.2rem, 3vw, 1.8rem); font-weight: bold;"><?php echo $clientes['total_clientes'] ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 col-12 mb-3">
                    <div class="small-box bg-primary" style="height: 100px;">
                        <div class="inner d-flex flex-column justify-content-center h-100 px-3 py-2">
                            <p class="mb-1 text-truncate" style="font-size: clamp(0.85rem, 2vw, 1rem);">Gastos</p>
                            <h3 class="mb-0 text-truncate" style="font-size: clamp(1.2rem, 3vw, 1.8rem); font-weight: bold;">
                                <?php foreach ($totalP as $gastoP) { ?>
                                    <?php if ($gastoP['total_monto'] != 0) {
                                        echo $gastoP['total_monto'] . ' Bs';
                                    } else {
                                        echo '0.00 Bs';
                                    } ?>
                                <?php } ?>
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <h3>Productos más vendidos</h3>
                            <table id="productos" class="table table-bordered table-striped table-hover datatable2" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>Código</th>
                                        <th>Nombre</th>
                                        <th>Marca</th>
                                        <th>Presentación</th>
                                        <th>Vendidos</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($bestseller as $producto): ?>
                                        <tr>
                                            <td> <?php echo $producto["cod_presentacion"] ?></td>
                                            <td> <?php echo $producto["nombre"] ?></td>
                                            <td> <?php echo $producto["nombre_marca"] ?  $producto["nombre_marca"] : 'No disponible' ?></td>
                                            <td> <?php echo $producto["presentacion_concat"] ? $producto["presentacion_concat"] : 'No disponible' ?></td>

                                            <td><?php echo $producto["vendido"] ?></td>
                                        </tr>
                                    <?php endforeach ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <h3>Alertas de stock</h3>
                            <p>Productos con stock menor a 5</p>
                            <table id="productos" class="table table-bordered table-striped table-hover datatable" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>Codigo</th>
                                        <th>Producto</th>
                                        <th>Stock</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($menorStock as $producto): ?>
                                        <tr>
                                            <td><?php echo $producto["cod_presentacion"] ?></td>
                                            <td><?php echo $producto["nombre"] . " " . $producto["marca"] . " " . $producto["presentacion_concat"] ?></td>
                                            <td><?php echo number_format($producto["stock"], 2, '.', ',') ?></td>
                                        </tr>
                                    <?php endforeach ?>
                            </table>
                        </div>
                    </div>
                </div>
            </div>


            <div class="row d-flex" id="rowGraficos">
                <!-- Gráfico de Ingresos - Egresos -->
                <div class="col-lg-6 mb-4" id="graficoIngresosCol">
                    <div class="card h-100">
                        <div class="card-header">
                            <h3>Ingresos vs Gastos</h3>
                            <p>De los últimos 7 dias</p>
                        </div>
                        <div class="card-body">
                            <canvas style="width: 100%; max-height: 300px; height: auto;" id="graficoIngresos"></canvas>
                        </div>
                    </div>
                </div>
                <!-- Gráfico de torta (falta ordenar las letras de forma responsiva. Moví el JS para el graficos.js) FALTA TRABAJAR MAS cuando es pequeñp cpm 368 y grande con 145-->
                <div class="col-lg-6 mb-4" id="graficoGastosCol">
                    <div class="card h-100">
                        <div class="card-header">
                            <h3>Gastos</h3>
                        </div>
                        <div class="card-body">
                            <canvas style="width: 100%; max-height: 300px; height: auto;" id="graficoGastos"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    $(document).ready(function() {
        $('#tablaProductos, #tablaStock').DataTable();

        // Gráfico de Ingresos vs Egresos
        $.ajax({
            url: 'index.php?pagina=inicio',
            type: 'POST',
            data: {
                accion: 'obtener_balance_semanal'
            },
            dataType: 'json',
            beforeSend: function() {
                console.log('Enviando petición de balance semanal...');
                // Destruir el gráfico existente si existe
                if (window.graficoIngresos instanceof Chart) {
                    window.graficoIngresos.destroy();
                }
            },
            success: function(response) {
                console.log('Respuesta recibida:', response);
                var datos = response.success ? response.datos : null;

                var ctxIngresos = document.getElementById('graficoIngresos');
                if (!ctxIngresos) {
                    console.error('No se encontró el elemento canvas para el gráfico');
                    return;
                }

                window.graficoIngresos = new Chart(ctxIngresos, {
                    type: 'line',
                    data: {
                        labels: datos?.datos_grafico?.labels || ['Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab', 'Dom'],
                        datasets: [{
                            label: 'Ingresos',
                            data: datos?.datos_grafico?.ingresos || [0, 0, 0, 0, 0, 0, 0],
                            borderColor: '#5271ff',
                            fill: false,
                            tension: 0.4
                        }, {
                            label: 'Gastos',
                            data: datos?.datos_grafico?.egresos || [0, 0, 0, 0, 0, 0, 0],
                            borderColor: '#ed1c2a',
                            fill: false,
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top',
                            },
                            title: {
                                display: true,
                                text: 'Ingresos vs Egresos'
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Monto'
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Día'
                                }
                            }
                        }
                    }
                });
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
                console.error('Respuesta del servidor:', xhr.responseText);

                // vacio en caso de error
                var ctxIngresos = document.getElementById('graficoIngresos');
                if (!ctxIngresos) {
                    console.error('No se encontró el elemento canvas para el gráfico');
                    return;
                }

                window.graficoIngresos = new Chart(ctxIngresos, {
                    type: 'line',
                    data: {
                        labels: ['Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab', 'Dom'],
                        datasets: [{
                            label: 'Ingresos',
                            data: [0, 0, 0, 0, 0, 0, 0],
                            borderColor: '#5271ff',
                            fill: false,
                            tension: 0.4
                        }, {
                            label: 'Gastos',
                            data: [0, 0, 0, 0, 0, 0, 0],
                            borderColor: '#ed1c2a',
                            fill: false,
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top',
                            },
                            title: {
                                display: true,
                                text: 'Ingresos vs Egresos'
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Monto'
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Día'
                                }
                            }
                        }
                    }
                });
            }
        });


    });
</script>


<!-- MODAL REGISTRAR EMPRESA 1ERA VEZ-->
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
                <form id="formGeneral" action="index.php?pagina=general" method="post" enctype="multipart/form-data">
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
                        <input type="hidden" name="inicio" value="inicio">
                        <input type="email" class="form-control" name="email" id="email" maxlength="70" placeholder="Ej: savyc@gmail.com">
                        <div class="invalid-feedback" style="display: none;"></div>
                    </div>
                    <div class="form-group">
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
                                                    <?php for ($i = 7; $i <= 20; $i++): ?>
                                                        <option value="<?php echo sprintf('%02d:00', $i); ?>"><?php echo sprintf('%02d:00', $i); ?></option>
                                                        <option value="<?php echo sprintf('%02d:30', $i); ?>"><?php echo sprintf('%02d:30', $i); ?></option>
                                                    <?php endfor; ?>
                                                </select>
                                            </td>
                                            <td class="text-center">a</td>
                                            <td>
                                                <select class="form-control" name="Horario[<?php echo $dia['dia']; ?>][hasta]" id="<?php echo $dia['dia']; ?>_hasta">
                                                    <?php for ($i = 7; $i <= 22; $i++): ?>
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
                    </div>

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
<?php
if (isset($registrar)) { ?>
    <script>
        Swal.fire({
            title: '<?php echo $registrar["title"]; ?>',
            text: '<?php echo $registrar["message"]; ?>',
            icon: '<?php echo $registrar["icon"]; ?>',
            confirmButtonText: 'Ok'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location = 'inicio';
            }
        });
    </script>
<?php } ?>
<?php if (empty($_SESSION["rif"]) && $_SESSION["cod_usuario"] != 1): ?>
    <script>
        //console.log(jsonen_encode($_SESSION["rif"]));
        console.log("pasa la primera condicion");
        $(document).ready(function() {
            if (!localStorage.getItem("modalMostrado")) {
                $('#modalregistrarempresa').modal('show');
                localStorage.setItem("modalMostrado", "true");
            }
        });
    </script>
<?php endif; ?>

<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Actualizar Tasas</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editForm" method="post">
                    <?php foreach ($consulta as $index => $divisa):
                        if ($divisa['cod_divisa'] != 1): ?>
                            <div class="form-group">
                                <input type="hidden" class="form-control" id="codigo" name="tasa[<?= $index ?>][cod_divisa]" value="<?= $divisa['cod_divisa']; ?>">
                            </div>
                            <div class="form-group">
                                <label for="nombre">Divisa</label>
                                <input type="text" class="form-control" value="<?= $divisa['nombre'] . ' - ' . $divisa['abreviatura']; ?>" readonly>
                            </div>
                            <div class="form-group row justify-content-center">
                                <div class="col-md-7">
                                    <label for="tasa">Tasa de la Divisa</label>
                                    <div class="input-group">
                                        <input type="number" id="tasaactual" step="0.01" class="form-control" value="<?= $divisa['tasa']; ?>" name="tasa[<?= $index ?>][tasa]" required>
                                        <div class="input-group-append">
                                            <span class="input-group-text">Bs</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <label for="fecha">Fecha</label>
                                    <input type="date" class="form-control" id="fecha" name="tasa[<?= $index ?>][fecha]" value="<?= $divisa['fecha']; ?>" required>
                                </div>
                            </div>
                            <hr>
                    <?php endif;
                    endforeach; ?>
                    <input type="hidden" name="inicio">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="submit" form="editForm" class="btn btn-primary" name="r_tasa">Guardar cambios</button>
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
                window.location = 'inicio';
            }
        });
    </script>
<?php endif; ?>

<!--<div class="modal fade" id="loadingModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center">
            <div class="modal-body">
                <h5>Cargando tasas...</h5>
                <div class="spinner-border text-primary mt-3" role="status">
                    <span class="sr-only">Cargando...</span>
                </div>
            </div>
        </div>
    </div>
</div>-->

<?php
/* $ultimo=end($consulta);
    $ultimo=end($consulta);
    if($ultimo['cod_divisa']!=1):
        if($ultimo['fecha'] != date('Y-m-d') && $_SESSION["cod_usuario"] != 1): 
?>
    <script>
    $(document).ready(function() {  
        var sen = "dolar";
        var tasaorig = $("#tasaactual").val();

        //Mostrar modal de carga antes de la solicitud
        $("#loadingModal").modal("show");

        $.post('index.php?pagina=divisa', { sen: sen }, function(response) {
            console.log("Respuesta del servidor:", response);
            let tasa = parseFloat(response.replace(',', '.'));

            if (response !== "error") {
                $('#tasaactual').val(tasa.toFixed(2));
                var now = new Date();
                var fecha = now.getFullYear() + '-' +
                    String(now.getMonth() + 1).padStart(2, '0') + '-' +
                    String(now.getDate()).padStart(2, '0');
                $('#fecha').val(fecha);
            } else {
                console.log("Error en la respuesta.");
                $('#tasaactual').val(tasaorig);
            }

            // Ocultar modal de carga y mostrar modal de edición
            $("#loadingModal").modal("hide");
            $('#editModal').modal('show');
            
        }).fail(function() {
            console.log("Error en la solicitud AJAX.");
            $("#loadingModal").modal("hide");
        });
    });
    </script>


<?php endif; 
    endif;*/ ?>

<script>
    const permisos = <?php echo json_encode($_SESSION["permisos"]); ?>;
    const horario = <?= json_encode($_SESSION['horario']); ?>;
    console.log(horario);
</script>

<script src="vista/dist/js/modulos-js/general.js"></script>
<script src="vista/dist/js/modulos-js/graficos.js"></script>