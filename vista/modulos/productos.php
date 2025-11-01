<?php 
require_once "controlador/productos.php";

if (isset($error)): ?>
    <script>
        Swal.fire({
            title: '<?= $error["title"] ?>',
            text: '<?= $error["message"] ?>',
            icon: '<?= $error["icon"] ?>'
        });
    </script>
<?php endif; ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Productos</h1>
                    <p>En esta sección se puede gestionar los productos.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <?php foreach ($datos as $v){ ?>
                <div class="col-lg-3 col-md-6 col-sm-6 col-12 mb-3">
                    <div class="small-box" style="background-color: #8770fa; color: white; height: 100px;">
                        <div class="inner d-flex flex-column justify-content-center h-100 px-3 py-2">
                            <p class="mb-1 text-truncate" style="font-size: clamp(0.85rem, 2vw, 1rem);">Valor inventario costo</p>
                            <h3 class="mb-0 text-truncate" style="font-size: clamp(1.2rem, 3vw, 1.8rem); font-weight: bold;"><?php echo number_format($v['total_costo'], 2, ',', '.')?>Bs</h3> 
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 col-12 mb-3">
                    <div class="small-box bg-primary" style="height: 100px;">
                        <div class="inner d-flex flex-column justify-content-center h-100 px-3 py-2">
                            <p class="mb-1 text-truncate" style="font-size: clamp(0.85rem, 2vw, 1rem);">Valor inventario venta</p>
                            <h3 class="mb-0 text-truncate" style="font-size: clamp(1.2rem, 3vw, 1.8rem); font-weight: bold;"><?php echo number_format($v['total_venta'], 2, ',', '.')?>Bs</h3> 
                        </div>
                    </div>
                </div>
                <?php } ?>
            </div>

                    <div class="card">
                        <div class="card-header">
                            <!-- Boton registrar producto -->
                            <?php if (!empty($_SESSION["permisos"]["producto"]["registrar"])): ?>
                            <button class="btn btn-primary" data-toggle="modal" data-target="#modalRegistrarProducto">Registrar producto</button>
                            <?php endif; ?>
                        </div>
                        <div class="card-body">
                        <div class="table-responsive">
                            <table id="productos" class="table table-bordered table-striped table-hover datatable" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>Código</th>
                                        <th>Nombre</th>
                                        <th>Marca</th>
                                        <th>Presentación</th>
                                        <th>Categoría</th>
                                        <th>Costo</th>
                                        <th>IVA</th>
                                        <th>Precio de venta</th>
                                        <th>Stock</th>
                                        <th>Detalle</th>
                                        <th>Imagen</th>
                                        <th>Acciones</th>
                                    </tr>         
                                </thead>
                                <tbody>
                                <!-- Tabla con los datos que se muestren dinamicamente -->
                                    <?php
                                    foreach ($registro as $producto){
                                        ?>
                                        <tr>
                                            <td> <?php echo $producto["cod_presentacion"] ?></td>
                                            <td> <?php echo $producto["nombre"] ?></td>
                                            <td> <?php echo $producto["marca"] ?  $producto["marca"] : 'No disponible'?></td>
                                            <td> <?php echo $producto["presentacion_concat"] ? $producto["presentacion_concat"] : 'No disponible' ?></td>
                                            <td> <?php echo $producto["cat_nombre"] ?></td>
                                            <td> <?php echo $producto["costo"] ?> Bs</td>
                                            <td> <?php if($producto["excento"] == 1){
                                                echo 'E';
                                            }  else{
                                                echo 'G';
                                            }
                                            ?>
                                            </td>
                                            <td>
                                                <?php 
                                            if($producto["excento"] == 1){
                                                $precioVenta = ($producto["porcen_venta"] / 100 + 1) * $producto["costo"];
                                                echo number_format($precioVenta, 2, '.', '')." Bs"; //2 decimales . se redondea 
                                            }  else{
                                                $costoiva = $producto["costo"] * 1.16;
                                                $precioVenta = ($producto["porcen_venta"] / 100 + 1) * $costoiva;
                                                echo number_format($precioVenta, 2, '.', '')." Bs"; //2 decimales . se redondea 
                                            }
                                            ?>
                                        </td>
                                        <td><?php echo $producto["stock_total"] ?></td>
                                        <!-- Detalle de producto -->
                                        <td class="text-center">
                                            <button class="btn btn-primary btn-sm" style="position: center;" data-toggle="modal" data-target="#detallemodal" title="Ver detalle"
                                            data-codigo="<?php echo $producto["cod_presentacion"];?>"
                                            data-presentp="<?php echo $producto["presentacion_concat"];?>"
                                            data-nombrep="<?php echo $producto["nombre"];?>">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                        
                                    </td>
                                    <!-- Botones -->
                                    <td>
                                        <?php if (!empty($producto['imagen'])): ?>
                                            <img src="<?php echo $producto['imagen']; ?>" alt="Imagen producto" style="width: 100px; height: auto;">
                                        <?php else: ?>
                                            <span>No disponible</span>
                                        <?php endif; ?>
                                    </td>
                                            <td>
                                                <?php if (!empty($_SESSION["permisos"]["producto"]["editar"])): ?>
                                                <button name="editar" title="Editar" class="btn btn-primary btn-sm editar" data-toggle="modal" data-target="#editModal"
                                                data-imagen="<?php echo $producto["imagen"];?>"
                                                data-codigo="<?php echo $producto["cod_presentacion"];?>"
                                                data-producto="<?php echo $producto["cod_producto"];?>"
                                                data-nombre="<?php echo $producto["nombre"]; ?>"
                                                data-marca="<?php echo $producto["cod_marca"]; ?>"
                                                data-unidad="<?php echo $producto['cod_unidad']; ?>"
                                                data-present="<?php echo $producto['presentacion']; ?>" 
                                                data-cantpresent="<?php echo $producto['cantidad_presentacion'];?>"
                                                data-categoria="<?php echo $producto["cat_codigo"]; ?>"
                                                data-costo="<?php echo $producto["costo"]; ?>"
                                                data-iva="<?php echo $producto["excento"]; ?>"
                                                data-porcen="<?php echo $producto["porcen_venta"];?>">
                                                <i class="fas fa-pencil-alt"></i>
                                                <?php endif; ?>
                                            </button>
                                                <?php if (!empty($_SESSION["permisos"]["producto"]["eliminar"])): ?>
                                                <button name="eliminar" title="Eliminar" class="btn btn-danger btn-sm eliminar" data-toggle="modal" data-target="#eliminarModal"
                                                data-codigo="<?php echo $producto["cod_presentacion"];?>"
                                                data-producto="<?php echo $producto["cod_producto"];?>"
                                                data-nombre="<?php echo $producto["nombre"]; ?>"
                                                >
                                                <i class="fas fa-trash-alt"></i></button>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                        </div>
                    </div>
<!-- =============================
    MODAL REGISTRAR PRODUCTO 
================================== -->
<div class="modal fade" id="modalRegistrarProducto" tabindex="-1" aria-labelledby="modalRegistrarProductoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="registrarModalLabel">Registrar producto</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formRegistrarProducto" method="post" enctype="multipart/form-data">
                    <div class="form-group row">
                        <div class="col-6">
                            <!-- Campo oculto para el código del producto -->
                            <input type="hidden" id="cod_productoR" name="cod_productoR">

                            <label for="nombre">Nombre del producto<span class="text-danger" style="font-size: 15px;"> *</span></label>
                            <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Ingresa el nombre" required>
                            <div class="invalid-feedback" style="display: none;"></div>
                            <div id="lista-productos" class="list-group" style="display: none;"></div>
                            
                        </div>
                        <div class="col-6">
                            <label for="marca">Marca</label>
                            <div class="input-group">
                                    <select class="form-control" id="marca" name="marca">
                                        <option value="" selected disabled>Seleccione una opción</option>
                                        <?php foreach($marcas as $mar): ?>
                                            <option value="<?php echo $mar['cod_marca']; ?>">
                                                <?php echo $mar['nombre']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" type="button" data-toggle="modal" data-target="#modalNuevaMarca">+</button>
                                    </div>
                                </div>
                            <div class="invalid-feedback" style="display: none;"></div>
                        </div>
                    </div>

                    <div class="form-group row">
                            <div class="col-6">
                                <label for="categoria">Categoría de producto<span class="text-danger" style="font-size: 15px;"> *</span></label>
                                <div class="input-group">
                                    <select class="form-control" id="categoria" name="categoria" required>
                                        <option value="" selected disabled>Seleccione una opción</option>
                                            <?php foreach($categoria as $cate): ?>
                                                <option value="<?php echo $cate['cod_categoria']; ?>">
                                                    <?php echo $cate['nombre']; ?>
                                                </option>
                                            <?php endforeach; ?>
                                    </select>
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" type="button" data-toggle="modal" data-target="#modalNuevaCategoria">+</button>
                                    </div>
                                </div>
                            </div>

                        <div class="col-6">
                            <label for="exento">Impuesto IVA<span class="text-danger" style="font-size: 15px;"> *</span></label>
                            <!-- TOOLTIPS-->
                            <button class="btn btn-xs" data-toggle="tooltip" data-placement="top" title="Selecciona si el producto es exento (tiene IVA) o gravable (No tiene IVA). El IVA es el 16%">
                                <i class="fas fa-info-circle"></i>
                            </button>
                            <script>
                                $(function () {
                                    $('[data-toggle="tooltip"]').tooltip();
                                });
                            </script>
                                <select class="form-control" id="iva" name="iva" required>
                                    <option value="" selected disabled>Seleccione una opción</option>
                                    <option value="1">Exento</option>
                                    <option value="2">Gravable</option>
                                </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="unidad">Unidad de medida<span class="text-danger" style="font-size: 15px;"> *</span></label>
                        <!-- TOOLTIPS-->
                        <button class="btn btn-xs" data-toggle="tooltip" data-placement="top" title="Selecciona la unidad de medida para la venta de productos, por ejemplo: Kg">
                                <i class="fas fa-info-circle"></i>
                            </button>
                            <script>
                                $(function () {
                                    $('[data-toggle="tooltip"]').tooltip();
                                });
                            </script>
                        <div class="input-group">
                            <select class="form-control" id="unidad" name="unidad" required>
                                <option value="" selected disabled>Seleccione una opción</option>
                                    <?php foreach($unidad as $u): ?>
                                        <option value="<?php echo $u['cod_unidad']; ?>">
                                            <?php echo $u['tipo_medida']; ?>
                                        </option>
                                    <?php endforeach; ?>
                            </select>
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="button" data-toggle="modal" data-target="#modalNuevaUnidad">+</button>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-6">
                            <label for="presentacion">Presentación</label>
                            <!-- TOOLTIPS-->
                            <button class="btn btn-xs" data-toggle="tooltip" data-placement="top" title="Ingresa la presentación de como viene el producto. Ej: Pieza.">
                                <i class="fas fa-info-circle"></i>
                            </button>
                            <script>
                                $(function () {
                                    $('[data-toggle="tooltip"]').tooltip();
                                });
                            </script>
                            <input type="text" class="form-control" id="presentacion" name="presentacion" placeholder="Ej: Pieza.">
                            <div class="invalid-feedback" style="display: none;"></div>
                        </div>
                        <div class="col-6">
                            <label for="cant_presentacion">Cantidad de presentación</label>
                            <!-- TOOLTIPS-->
                            <button class="btn btn-xs" data-toggle="tooltip" data-placement="top" title="Ingresa la cantidad de presentación de como viene el producto. Ej: 250gr.">
                                <i class="fas fa-info-circle"></i>
                            </button>
                            <script>
                                $(function () {
                                    $('[data-toggle="tooltip"]').tooltip();
                                });
                            </script>
                            <input type="text" class="form-control" id="cant_presentacion" name="cant_presentacion" placeholder="Ej: 1.5kg">
                            <div class="invalid-feedback" style="display: none;"></div>
                        </div>
                    </div>
                    <hr>
                    <div class="form-group row">
                        <div class="col-6">
                            <label for="costo">Costo</label>
                            <input type="number" class="form-control" step="0.01" id="costo" name="costo" placeholder="Precio de compra en Bs" >
                            <div class="invalid-feedback" style="display: none;"></div>
                        </div>
                        <div class="col-6">
                            <label for="precio">Precio</label>
                            <input type="number" class="form-control" min="0" id="precio" placeholder="Precio de venta en Bs" readonly >
                            <div class="invalid-feedback" style="display: none;"></div>
                        </div>
                    </div>
                    <div class="input-group mb-2">
                        <input type="number" class="form-control nuevoPorcentaje" min="0" step="1" placeholder="Porcentaje de ganancia" id="porcen" name="porcen">
                        <div class="invalid-feedback" style="display: none;"></div>
                        <div class="input-group-append">
                            <span class="input-group-text"><i class="fas fa-percent"></i></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="imagen">Ingrese la imagen<span class="text-danger" style="font-size: 20px;">
                                *</span>
                        </label>
                        <button class="btn btn-xs" data-toggle="tooltip" data-placement="top" title="Ingresa la imagen representativa del producto">
                            <i class="fas fa-info-circle"></i>
                        </button>
                        <script>
                            $(function() {
                                $('[data-toggle="tooltip"]').tooltip();
                            });
                        </script>
                        <input type="file" class="form-control inputImagen" name="imagen" id="imagenE">
                        <div class="invalid-feedback" style="display: none;"></div>
                    </div>
                    <!-- Alert Message -->
                    <div class="alert alert-light d-flex align-items-center" role="alert">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <span>Todos los campos marcados con (*) son obligatorios</span>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-secondary" name="deshacer" id="deshacer">Deshacer</button>
                        <button type="submit" class="btn btn-primary" name="guardar">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
        
<?php if (isset($registrarp)): ?>
    <script>
        Swal.fire({
            title: '<?php echo $registrarp["title"]; ?>',
            text: '<?php echo $registrarp["message"]; ?>',
            icon: '<?php echo $registrarp["icon"]; ?>',
            confirmButtonText: 'Ok'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location = 'productos';
            }
        });
    </script>
<?php endif; ?>

<!-- =============================
    MODAL NUEVA CATEGORIA 
================================== -->

    <div class="modal fade" id="modalNuevaCategoria" tabindex="-1" aria-labelledby="modalNuevaCategoriaLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="registrarModalLabel">Registrar categoría</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <form id="formNuevaCategoria" action="index.php?pagina=categorias" method="post">
                        <div class="form-group">
                            <label for="nombre">Nombre de la categoría</label>
                            <input type="text" class="form-control" id="nombrec" name="nombre" placeholder="Ingresa el nombre de la categoría" required>
                            <input type="hidden" name="vista" value="categorias">
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="submit" form="formNuevaCategoria" class="btn btn-primary" name="registrarc">Guardar</button>
                </div>
            </div>
        </div>
    </div>
    <?php if (isset($registrar)): ?>
    <script>
        Swal.fire({
            title: '<?php echo $registrar["title"]; ?>',
            text: '<?php echo $registrar["message"]; ?>',
            icon: '<?php echo $registrar["icon"]; ?>',
            confirmButtonText: 'Ok'
        }).then((result) => {
            if (result.isConfirmed) {
                localStorage.setItem('categoriaModal', 'true');
                window.location='productos';
            }
    });
</script> 
<?php endif; ?>
<!-- =============================
    MODAL NUEVA MARCA
================================== -->
    <div class="modal fade" id="modalNuevaMarca" tabindex="-1" aria-labelledby="modalNuevaMarcaLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="registrarModalLabel">Registrar Marca</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <form id="formNuevaMarca" action="index.php?pagina=marcas" method="post">
                        <div class="form-group">
                            <label for="nombre">Nombre de la marca</label>
                            <input type="text" class="form-control" id="nombrem" name="nombre" placeholder="Ingresa el nombre de la marca" required>
                            <input type="hidden" name="vista" value="marcas">
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="submit" form="formNuevaMarca" class="btn btn-primary" name="registrarm">Guardar</button>
                </div>
            </div>
        </div>
    </div>
    <?php if (isset($registrar)): ?>
    <script>
        Swal.fire({
            title: '<?php echo $registrar["title"]; ?>',
            text: '<?php echo $registrar["message"]; ?>',
            icon: '<?php echo $registrar["icon"]; ?>',
            confirmButtonText: 'Ok'
        }).then((result) => {
            if (result.isConfirmed) {
                localStorage.setItem('marcaModal', 'true');
                window.location='productos';
            }
    });
</script> 
<?php endif; ?>
<!-- =============================
    MODAL NUEVA UNIDAD DE MEDIDA
================================== -->
        <div class="modal fade" id="modalNuevaUnidad" tabindex="-1" aria-labelledby="modalnuevaUnidadLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-primary">
                        <h5 class="modal-title" id="exampleModalLabel">Registrar Unidad de medida</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="formregistrarUnidad" action="index.php?pagina=unidad" method="post">
                            <div class="form-group">
                                <label for="tipo_medida">Tipo de medida</label>
                                <!-- TOOLTIPS-->
                                <button class="btn btn-xs" data-toggle="tooltip" data-placement="top" title="Ingresa la unidad de medida para la venta de productos, por ejemplo: Kg">
                                    <i class="fas fa-info-circle"></i>
                                </button>
                                <script>
                                    $(function () {
                                        $('[data-toggle="tooltip"]').tooltip();
                                    });
                                </script>
                                <input type="text" class="form-control" name="tipo_medida" id="tipo_medidau" placeholder="Ej: Kg" required>
                                <input type="hidden" name="vista" value="unidad">
                            </div>
                        </div>
                        <div class="modal-footer justify-content-between">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-primary" name="guardaru">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
<?php if (isset($registrar)): ?>
    <script>
        Swal.fire({
            title: '<?php echo $registrar["title"]; ?>',
            text: '<?php echo $registrar["message"]; ?>',
            icon: '<?php echo $registrar["icon"]; ?>',
            confirmButtonText: 'Ok'
        }).then((result) => {
            if (result.isConfirmed) {
                localStorage.setItem('unidadModal', 'true');
                window.location='productos';
            }
    });
</script> 
<?php endif; ?> 

<!-- =============================
    MODAL EDITAR PRODUCTO 
================================== -->
                    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editModalLabel">Editar Información</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <form id="editForm" method="post" enctype="multipart/form-data">
                                    <div class="form-group">
                                            <label for="codigo">Código</label>
                                            
                                            <input type="text" class="form-control" id="cod_presentacion" name="cod_presentacion" readonly>
                                            <input type="hidden" id="cod_producto" name="cod_producto">
                                        </div>
                                    <div class="form-group row">
                                        <div class="col-6">
                                            <label for="nombre">Nombre del producto</label>
                                            <input type="text" class="form-control" id="name" name="nombre" placeholder="Ingresa el nombre" required>
                                            <div class="invalid-feedback" style="display: none;"></div>
                                        </div>
                                        <div class="col-6">
                                                <label for="marca">Marca</label>
                                                <div class="input-group">
                                                        <select class="form-control" id="marcaE" name="marca">
                                                            <option value="" selected disabled>Seleccione una opción</option>
                                                            <?php foreach($marcas as $mar): ?>
                                                                <option value="<?php echo $mar['cod_marca']; ?>">
                                                                    <?php echo $mar['nombre']; ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                        <div class="input-group-append">
                                                            <button class="btn btn-outline-secondary" type="button" data-toggle="modal" data-target="#modalNuevaMarca">+</button>
                                                        </div>
                                                    </div>
                                                <div class="invalid-feedback" style="display: none;"></div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-6">
                                            <label for="categoria">Categoría</label>
                                                <select class="form-control" id="categoriaE" name="categoria" required>
                                                        <?php foreach($categoria as $cate): ?>
                                                            <option value="<?php echo $cate['cod_categoria']; ?>">
                                                                <?php echo $cate['nombre']; ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                </select>
                                        </div>
                                        <div class="col-6">
                                            <label for="exento">¿Tiene IVA?</label>
                                                <select class="form-control" id="ivaE" name="iva" required>
                                                    <option value="" selected disabled>Seleccione una opción</option>
                                                    <option value="1">Exento</option>
                                                    <option value="2">Gravable</option>
                                                </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="unidad">Unidad de medida</label>
                                            <select class="form-control" id="unidadE" name="unidad" required>
                                                    <?php foreach($unidad as $u): ?>
                                                        <option value="<?php echo $u['cod_unidad']; ?>">
                                                            <?php echo $u['tipo_medida']; ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                            </select>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-6">
                                            <label for="presentacion">Presentación</label>
                                            <input type="text" class="form-control" id="presentacionE" name="presentacion" placeholder="Ingresa la presentación">
                                            <div class="invalid-feedback" style="display: none;"></div>
                                        </div>
                                        <div class="col-6">
                                            <label for="cant_presentacion">Cantidad de presentación</label>
                                            <input type="text" class="form-control" id="cant_presentacionE" name="cant_presentacion" placeholder="Ingresa la cantidad">
                                            <div class="invalid-feedback" style="display: none;"></div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="form-group row">
                                        <div class="col-6">
                                            <label for="costo">Costo</label>
                                            <input type="number" class="form-control" step="0.01" min="0" id="costoE" name="costo" placeholder="Precio de compra" required>
                                            <div class="invalid-feedback" style="display: none;"></div>
                                        </div>
                                        <div class="col-6">
                                            <label for="precio">Precio</label>
                                            <input type="number" class="form-control" min="0" id="precioE" placeholder="Precio de venta" readonly >
                                            <div class="invalid-feedback" style="display: none;"></div>
                                        </div>
                                    </div>
                                    <div class="input-group mb-2">
                                        <input type="number" class="form-control nuevoPorcentaje" min="0" step="0" placeholder="Porcentaje de ganancia" id="porcenE" name="porcen" required>
                                        <div class="input-group-append">
                                            <span class="input-group-text"><i class="fas fa-percent"></i></span>
                                        </div>
                                        <div class="invalid-feedback" style="display: none;"></div>
                                    </div>
                                    <div class="form-group">
                                        <div style="display: flex;justify-content:space-between;align-items:center;">
                                            <div>
                                                <label for="imagen">Ingrese la nueva imagen<span class="text-danger" style="font-size: 20px;">
                                                    *</span>
                                                </label>
                                                <button class="btn btn-xs" data-toggle="tooltip" data-placement="top" title="Ingresa la imagen representativa del producto">
                                                    <i class="fas fa-info-circle"></i>
                                                </button>
                                                <script>
                                                    $(function() {
                                                        $('[data-toggle="tooltip"]').tooltip();
                                                    });
                                                </script>
                                            </div>
                                            <div>
                                                <?php if (!empty($producto['imagen'])): ?>
                                                    <label for="">Imagen actual:</label>
                                                    <img src="<?php echo $producto['imagen']; ?>" alt="Logo"  style="width: 75px; height: auto;">
                                                <?php else: ?>
                                                    <span>No disponible</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                            <input type="file" class="form-control inputImagen" name="imagenE" id="imagenE">
                                            <input type="hidden" name="imagenActual" value="<?php echo $producto["imagen"];?>">
                                            <div class="invalid-feedback" style="display: none;"></div>
                                        </div>
                                    <div class="modal-footer justify-content-between">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                        <button type="submit" class="btn btn-primary" name="editar">Guardar</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
<?php if (isset($editar)): ?>
    <script>
        Swal.fire({
            title: '<?php echo $editar["title"]; ?>',
            text: '<?php echo $editar["message"]; ?>',
            icon: '<?php echo $editar["icon"]; ?>',
            confirmButtonText: 'Ok'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location = 'productos';
            }
        });
    </script>
<?php endif; ?>
            
<!-- =============================
    MODAL ELIMINAR PRODUCTO 
================================== -->
                <div class="modal fade" id="eliminarModal" tabindex="-1" aria-labelledby="eliminarModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header bg-danger">
                                <h5 class="modal-title" id="eliminarModalLabel">Eliminar producto</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form id="eliminarForm" method="post">
                                    <div class="form-group">
                                        <p>¿Estás seguro que deseas eliminar a <b><span id="p_nombre"></b></span>?</p>
                                        <input type="hidden" id="present_codigo" name="present_codigo">
                                        <input type="hidden" id="p_codigo" name="p_codigo">
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                                <button type="submit" form="eliminarForm" class="btn btn-danger" id="confimDelete" name="borrar">Eliminar</button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php if (isset($eliminar)): ?>
    <script>
        Swal.fire({
            title: '<?php echo $eliminar["title"]; ?>',
            text: '<?php echo $eliminar["message"]; ?>',
            icon: '<?php echo $eliminar["icon"]; ?>',
            confirmButtonText: 'Ok'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location = 'productos';
            }
        });
    </script>
<?php endif; ?>
<!-- =============================
    MODAL DETALLE DE PRODUCTO 
================================== -->
                <div class="modal fade" id="detallemodal" tabindex="-1" aria-labelledby="detalleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                                <div class="modal-header bg-primary">
                                    <h5 class="modal-title" id="detalleModalLabel">Detalle de productos</h5>
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
                                                        <label for="nro_venta">Nombre del producto</label>
                                                        <input type="text" class="form-control" id="nombreproducto" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="nombre_cliente">Presentación</label>
                                                        <input type="text" class="form-control" id="presentproducto" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table id="productos" class="table table-bordered table-striped table-hover" style="width: 100%;">
                                                    <thead>
                                                        <tr>
                                                            <th>Código</th>
                                                            <th>Lote</th>
                                                            <th>Fecha de vencimiento</th>
                                                            <th>Stock</th>
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

<!-- ====================================
    MODAL CONFIRMAR ELIMINAR DETALLE 
========================================= 
                <div class="modal fade" id="eliminarDetalleModal" tabindex="-1" aria-labelledby="eliminarDetalleModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Confirmar Eliminación</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                            <form id="eliminarForm" method="post">
                                <div class="form-group">
                                    <p>¿Estás seguro que deseas eliminar a este detalle?</p>
                                </div>
                            </form>
                            </div>
                            
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                <button type="button" class="btn btn-danger" name="eliminardetalle" id="confirmarEliminarDetalle">Eliminar</button>
                            </div>
                        </div>
                    </div>
                </div>-->
                
            </div>
        </div>      
    </section>
</div>


<script src="vista/dist/js/modulos-js/productos.js"></script>