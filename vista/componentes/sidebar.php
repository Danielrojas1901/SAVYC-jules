<!-- MAIN SIDEBAR CONTAINER-->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Logo -->
    <a href="inicio" class="brand-link">
        <?php
        if (isset($_SESSION["logo"])): ?>
            <img src="<?php echo $_SESSION["logo"]; ?>" alt="Quesera Don Pedro" class="brand-image img-circle elevation-3" style="opacity: .8">
        <?php else: ?>
            <img src="vista/dist/img/logo_generico.png" alt="Quesera Don Pedro" class="brand-image img-circle elevation-3" style="opacity: .8">
        <?php endif; ?>
        <span class="brand-text font-weight-bold">SAVYC</span>
    </a>

    <!--=====================
    MENU / sidebar
=====================-->
    <?php //if (isset($_SESSION["rif"]) || $_SESSION["cod_usuario"] == 1): ?>

        <div class="sidebar">
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column nav-child-indent text-sm" data-widget="treeview" role="menu" data-accordion="false">
                    <?php if (!empty($_SESSION["permisos"]["producto"]) || !empty($_SESSION["permisos"]["inventario"])): ?>
                        <li class="nav-header">INVENTARIO</li>
                    <?php endif; ?>
                    <?php if (!empty($_SESSION["permisos"]["producto"])): ?>
                        <li class="nav-item">
                            <a href="productos" class="nav-link bitacora-link" data-modulo="Productos">
                                <i class="nav-icon fa fa-shopping-bag"></i>
                                <p>
                                    Productos
                                </p>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if (!empty($_SESSION["permisos"]["inventario"])): ?>
                        <li class="nav-item">
                            <a href="#" class="nav-link bitacora-link" data-modulo="Ajuste de Inventario">
                                <i class="fas fa-dolly-flatbed nav-icon"></i>
                                <p>
                                    Ajustes<i class="right fas fa-angle-left nav-icon"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="carga" class="nav-link bitacora-link" data-modulo="Carga de productos">
                                        <i class="fas fa-sort-amount-up-alt nav-icon"></i>
                                        <p>Carga de productos</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="descarga" class="nav-link bitacora-link" data-modulo="Descarga de productos">
                                        <i class="fas fa-sort-amount-down-alt nav-icon"></i>
                                        <p>Descarga de productos</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    <?php endif; ?>
                    </li>

                    <?php if (!empty($_SESSION["permisos"]["compra"]) || !empty($_SESSION["permisos"]["venta"])): ?>
                        <li class="nav-header">COMERCIO</li>
                    <?php endif; ?>
                    <?php if (!empty($_SESSION["permisos"]["compra"])): ?>
                        <li class="nav-item">
                            <a href="compras" class="nav-link bitacora-link" data-modulo="Compras">
                                <i class="nav-icon fa fa-shopping-cart"></i>
                                <p>
                                    Compras
                                </p>
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if (!empty($_SESSION["permisos"]["venta"])): ?>
                        <li class="nav-item">
                            <a href="venta" class="nav-link bitacora-link" data-modulo="Ventas">
                                <i class="nav-icon fa fa-file"></i>
                                <p>
                                    Ventas
                                </p>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if (!empty($_SESSION["permisos"]["cliente"]) || !empty($_SESSION["permisos"]["proveedor"])): ?>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="fas fa-user-circle nav-icon"></i>
                                <p>
                                    Terceros<i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <?php if (!empty($_SESSION["permisos"]["cliente"])): ?>
                                    <li class="nav-item">

                                        <a href="clientes" class="nav-link bitacora-link" data-modulo="Clientes">
                                            <i class="nav-icon fa fa-users"></i>
                                            <p>
                                                Clientes
                                            </p>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                <?php if (!empty($_SESSION["permisos"]["proveedor"])): ?>
                                    <li class="nav-item">
                                        <a href="proveedores" class="nav-link  bitacora-link" data-modulo="Proveedores">
                                            <i class="nav-icon far fa fa-truck"></i>
                                            <p>
                                                Proveedores
                                            </p>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </li>
                    <?php endif; ?>
                    <?php if (!empty($_SESSION["permisos"]["contabilidad"]) || !empty($_SESSION["permisos"]["finanza"])): ?>
                        <li class="nav-header">GESTIÓN FINANCIERA</li>
                    <?php endif; ?>
                    <?php if (!empty($_SESSION["permisos"]["contabilidad"])): ?>
                        <li class="nav-item">
                            <a href="#" class="nav-link bitacora-link" data-modulo="Contabilidad">
                                <i class="fas fa-coins nav-icon"></i>
                                <p>
                                    Administración<i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="catalogocuentas" class="nav-link bitacora-link" data-modulo="Catálogo de cuentas">
                                        <i class="fas fa-wallet nav-icon"></i>
                                        <p>Catálogo de cuentas</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="movimientos" class="nav-link bitacora-link" data-modulo="Gestionar asientos">
                                        <i class="fas fa-cogs nav-icon"></i>
                                        <p>Gestionar asientos</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="rep-contabilidad" class="nav-link bitacora-link" data-modulo="Reportes contables">
                                        <i class="fas fa-chart-line nav-icon"></i>
                                        <p>Reportes administrativos</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    <?php endif; ?>
                    <?php if (!empty($_SESSION["permisos"]["finanza"])): ?>
                        <li class="nav-item">
                            <a href="finanzas" class="nav-link bitacora-link" data-modulo="Finanzas">
                                <i class="fas fa-chart-bar nav-icon"></i>
                                <p>
                                    Finanzas
                                </p>
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if (!empty($_SESSION["permisos"]["tesoreria"]) || !empty($_SESSION["permisos"]["gasto"]) || !empty($_SESSION["permisos"]["cuentas_pendiente"])): ?>
                        <li class="nav-header">TESORERÍA</li>
                    <?php endif; ?>
                    <?php if (!empty($_SESSION["permisos"]["tesoreria"])): ?>
                        <li class="nav-item">
                            <a href="controlcaja" class="nav-link bitacora-link" data-modulo="Caja">
                                <i class="fas fa-cash-register nav-icon"></i>
                                <p>
                                    Apertura y Cierre de Caja
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="cuentabancaria" class="nav-link">
                                <i class="fas fa-credit-card nav-icon"></i>
                                <p>
                                    Cuentas bancarias
                                </p>
                            </a>
                        </li>
                        <!--<ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="cuentabancaria" class="nav-link bitacora-link" data-modulo="Cuenta Bancaria">
                                    <i class="fas fa-money-check nav-icon"></i>
                                    <p>
                                        Cuentas
                                    </p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="conciliacion" class="nav-link bitacora-link" data-modulo="Conciliación bancaria">
                                    <i class="fas fa-check-circle nav-icon"></i>
                                    <p>
                                        Conciliación bancaria
                                    </p>
                                </a>
                            </li>
                        </ul>-->
                        
                    <?php endif; ?>
                    <?php if (!empty($_SESSION["permisos"]["gasto"])): ?>
                        <li class="nav-item">
                            <a href="gasto" class="nav-link bitacora-link" data-modulo="Gastos">
                                <i class="fas fa-file-invoice-dollar nav-icon"></i>
                                <p>
                                    Gastos
                                </p>
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if (!empty($_SESSION["permisos"]["cuentas_pendiente"])): ?>
                        <li class="nav-item">
                            <a href="cuentaspend" class="nav-link bitacora-link" data-modulo="Cuentas pendientes">
                                <i class="fas fa-wallet nav-icon"></i>
                                <p>
                                    Cuentas pendientes</i>
                                </p>
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if (!empty($_SESSION["permisos"]["reporte"])): ?>
                        <li class="nav-header">INFORMES</li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="fas fa-chart-line nav-icon"></i>
                                <p>
                                    Reportes<i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="rep-proveedores" class="nav-link bitacora-link" data-modulo="Reporte De proveedores">
                                        <i class="fas fa-store nav-icon"></i>
                                        <p>
                                            De proveedores
                                        </p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="rep-inventario" class="nav-link   bitacora-link" data-modulo="Reporte De Inventario">
                                        <i class="fas fa-pallet nav-icon"></i>
                                        <p>
                                            De inventario
                                        </p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="rep-venta" class="nav-link   bitacora-link" data-modulo="Reporte De Inventario">
                                        <i class="fas fa-file-invoice nav-icon"></i>
                                        <p>
                                            De Ventas
                                        </p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="rep-cliente" class="nav-link bitacora-link" data-modulo="Reporte De Clientes">
                                        <i class="fas fa-users nav-icon"></i>
                                        <p>
                                            De clientes
                                        </p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="rep-compra" class="nav-link bitacora-link" data-modulo="Reporte De compras">
                                        <i class="fas fa-shopping-bag nav-icon"></i>
                                        <p>
                                            De compras
                                        </p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="rep-cuentaspend" class="nav-link bitacora-link" data-modulo="Reporte De Cuentas Pendientes">
                                        <i class="fas fa-comment-dollar nav-icon"></i>
                                        <p>
                                            De Cuentas Pendientes
                                        </p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="rep-gastos" class="nav-link bitacora-link" data-modulo="Reporte De Gastos">
                                        <i class="fas fa-file-invoice-dollar nav-icon"></i>
                                        <p>
                                            De gastos
                                        </p>
                                    </a>
                                </li>

                            </ul>
                        </li>
                    <?php endif; ?>
                    <?php if (!empty($_SESSION["permisos"]["seguridad"]) || !empty($_SESSION["permisos"]["config_producto"]) || !empty($_SESSION["permisos"]["config_finanza"])): ?>
                        <li class="nav-header">AJUSTES</li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="fas fa-cog  nav-icon"></i>
                                <p>
                                    Configuración<i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <?php if (!empty($_SESSION["permisos"]["seguridad"])): ?>
                                    <li class="nav-header">GENERAL</li>
                                    <li class="nav-item">
                                        <a href="general" class="nav-link bitacora-link" data-modulo="Ajuste Empresa">
                                            <i class="fas fa-cogs nav-icon"></i>
                                            <p>
                                                Empresa
                                            </p>
                                        </a>
                                    </li>
                                    <li class="nav-header">SEGURIDAD Y ACCESOS</li>
                                    <li class="nav-item">
                                        <a href="usuarios" class="nav-link bitacora-link" data-modulo="Usuarios">
                                            <i class="nav-icon fas fa-users-cog"></i>
                                            <p>
                                                Usuarios
                                            </p>
                                        </a>
                                    </li>
                                    <li class="nav-item">

                                        <a href="roles" class="nav-link">
                                            <i class="nav-icon fas fa-user-tag"></i>
                                            <p>
                                                Roles y Permisos
                                            </p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="bitacora" class="nav-link">
                                            <i class="nav-icon fas fa-user-shield"></i>

                                            <p>
                                                Bitacora
                                            </p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="backup" class="nav-link">
                                            <i class="nav-icon fas fa-shield-alt"></i>
                                            <p>
                                                Copias de Seguridad
                                            </p>
                                        </a>
                                    </li>
                                <?php endif; ?>

                                <?php if (!empty($_SESSION["permisos"]["config_producto"])): ?>
                                    <li class="nav-header">PRODUCTOS</li>
                                    <li class="nav-item">
                                        <a href="unidad" class="nav-link bitacora-link" data-modulo="Unidades de medida">
                                            <i class="fas fa-balance-scale nav-icon"></i>
                                            <p>
                                                Unidades de medida
                                            </p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="categorias" class="nav-link bitacora-link" data-modulo="Categorías">
                                            <i class="nav-icon fa fa-table"></i>
                                            <p>
                                                Categorías
                                            </p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="marcas" class="nav-link bitacora-link" data-modulo="Marcas">
                                            <i class="fas fa-tags nav-icon"></i>
                                            <p>
                                                Marcas
                                            </p>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                <?php if (!empty($_SESSION["permisos"]["config_finanza"])): ?>
                                    <li class="nav-header">FINANZAS</li>
                                    <li class="nav-item">
                                        <a href="divisa" class="nav-link bitacora-link" data-modulo="Divisas">
                                            <i class="fas fa-dollar-sign nav-icon"></i>
                                            <p>
                                                Divisas
                                            </p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="tpago" class="nav-link bitacora-link" data-modulo="Tipos de pago">
                                            <i class="fas fa-money-bill nav-icon"></i>
                                            <p>
                                                Tipos de pago
                                            </p>
                                        </a>
                                    </li>

                                    <li class="nav-item">
                                        <a href="banco" class="nav-link bitacora-link" data-modulo="Banco">
                                            <i class="fas fa-university nav-icon"></i>
                                            <p>
                                                Bancos
                                            </p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="caja" class="nav-link bitacora-link" data-modulo="Caja">
                                            <i class="fas fa-cash-register nav-icon"></i>
                                            <p>
                                                Cajas
                                            </p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="categoriag" class="nav-link bitacora-link" data-modulo="Gastos">
                                            <i class="fas fa-file-invoice-dollar nav-icon"></i>
                                            <p>
                                                Categoría de Gastos
                                            </p>
                                        </a>
                                    </li>
                                    <br>
                                    <br>
                                <?php endif; ?>
                            </ul>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>


    <?php /*else: ?>

        <div class="sidebar">
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                    <li class="nav-header">INVENTARIO</li>
                    <?php if ($_SESSION["producto"] == 1): ?>
                        <li class="nav-item">
                            <a href="productos" class="nav-link bitacora-link" data-modulo="Productos">
                                <i class="nav-icon fa fa-shopping-bag"></i>
                                <p>
                                    Productos
                                </p>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if ($_SESSION["inventario"] == 1): ?>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="fas fa-dolly-flatbed nav-icon"></i>
                                <p>
                                    Ajustes<i class="right fas fa-angle-left nav-icon"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="carga" class="nav-link bitacora-link" data-modulo="Carga de productos">
                                        <i class="fas fa-sort-amount-up-alt nav-icon"></i>
                                        <p>Carga de productos</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="descarga" class="nav-link bitacora-link" data-modulo="Descarga de productos">
                                        <i class="fas fa-sort-amount-down-alt nav-icon"></i>
                                        <p>Descarga de productos</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    <?php endif; ?>
                    </li>


                    <li class="nav-header">COMERCIO</li>
                    <?php if ($_SESSION["compra"] == 1): ?>
                        <li class="nav-item">
                            <a href="compras" class="nav-link bitacora-link" data-modulo="Compras">
                                <i class="nav-icon fa fa-shopping-cart"></i>
                                <p>
                                    Compras
                                </p>
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if ($_SESSION["venta"] == 1): ?>
                        <li class="nav-item">
                            <a href="venta" class="nav-link bitacora-link" data-modulo="Ventas">
                                <i class="nav-icon fa fa-file"></i>
                                <p>
                                    Ventas
                                </p>
                            </a>
                        </li>
                    <?php endif; ?>

                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="fas fa-user-circle nav-icon"></i>
                            <p>
                                Terceros<i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <?php if ($_SESSION["cliente"] == 1): ?>
                                <li class="nav-item">

                                    <a href="clientes" class="nav-link bitacora-link" data-modulo="Clientes">
                                        <i class="nav-icon fa fa-users"></i>
                                        <p>
                                            Clientes
                                        </p>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if ($_SESSION["proveedor"] == 1): ?>
                                <li class="nav-item">
                                    <a href="proveedores" class="nav-link  bitacora-link" data-modulo="Proveedores">
                                        <i class="nav-icon far fa fa-truck"></i>
                                        <p>
                                            Proveedores
                                        </p>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </li>
                    <li class="nav-header">ADMINISTRACIÓN</li>
                    <li class="nav-item">
                        <a href="#" class="nav-link bitacora-link" data-modulo="Contabilidad">
                            <i class="fas fa-coins nav-icon"></i>
                            <p>
                                Contabilidad<i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="catalogocuentas" class="nav-link bitacora-link" data-modulo="Catálogo de cuentas">
                                    <i class="fas fa-wallet nav-icon"></i>
                                    <p>Catálogo de cuentas</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="movimientos" class="nav-link bitacora-link" data-modulo="Gestionar asientos">
                                    <i class="fas fa-cogs nav-icon"></i>
                                    <p>Gestionar asientos</p>
                                </a>
                            </li>
                            <li class="nav-item">

                                <a href="descarga" class="nav-link bitacora-link" data-modulo="Reporte contables">

                                    <i class="fas fa-chart-line nav-icon"></i>
                                    <p>Reportes contables</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a href="finanzas" class="nav-link bitacora-link" data-modulo="Finanzas">
                            <i class="fas fa-chart-bar nav-icon"></i>
                            <p>
                                Finanzas
                            </p>
                        </a>
                    </li>
                    <li class="nav-header">TESORERÍA</li>
                    <li class="nav-item">

                        <a href="caja" class="nav-link bitacora-link" data-modulo="Caja">
                            <i class="fas fa-cash-register nav-icon"></i>

                            <p>
                                Caja
                            </p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="cuentabancaria" class="nav-link bitacora-link" data-modulo="Cuenta Bancaria">
                            <i class="fas fa-credit-card nav-icon"></i>
                            <p>
                                Cuenta Bancaria
                            </p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="conciliacion" class="nav-link bitacora-link" data-modulo="Conciliación bancaria">
                            <i class="fas fa-check-circle nav-icon"></i>

                            <p>
                                Banco y Caja<i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">

                                <a href="tipocuenta" class="nav-link bitacora-link" data-modulo="Tipo de cuenta">
                                    <i class="fas fa-university nav-icon"></i>

                                    <p>
                                        Conciliación bancaria
                                    </p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <?php if (!empty($_SESSION["permisos"]["gasto"])): ?>
                        <li class="nav-item">
                            <a href="gasto" class="nav-link bitacora-link" data-modulo="Gastos">
                                <i class="fas fa-file-invoice-dollar nav-icon"></i>
                                <p>
                                    Gastos
                                </p>
                            </a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a href="cuentaspend" class="nav-link bitacora-link" data-modulo="Cuentas pendientes">
                            <i class="fas fa-wallet nav-icon"></i>
                            <p>
                                Cuentas pendientes</i>
                            </p>
                        </a>
                    </li>

                    <li class="nav-header">INFORMES</li>
                    <?php if(!empty($_SESSION["permisos"]["reporte"])): ?>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="fas fa-chart-line nav-icon"></i>
                                <p>
                                    Reportes<i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="rep-proveedores" class="nav-link bitacora-link" data-modulo="Reporte De proveedores">
                                        <i class="fas fa-store nav-icon"></i>
                                        <p>
                                            De proveedores
                                        </p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="rep-inventario" class="nav-link bitacora-link" data-modulo="Reporte De inventario">
                                        <i class="fas fa-pallet nav-icon"></i>
                                        <p>
                                            De inventario
                                        </p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="rep-venta" class="nav-link bitacora-link" data-modulo="Reporte De ventas">
                                        <i class="fas fa-file-invoice nav-icon"></i>
                                        <p>
                                            De clientes
                                        </p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="rep-compra" class="nav-link bitacora-link" data-modulo="Reporte De compras">
                                        <i class="fas fa-shopping-bag nav-icon"></i>
                                        <p>
                                            De compras
                                        </p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    <?php endif; ?>

                    <li class="nav-header">AJUSTES</li>
                    <?php if ($_SESSION["configuracion"] == 1): ?>

                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="fas fa-cog  nav-icon"></i>
                                <p>
                                    Configuración<i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="general" class="nav-link bitacora-link" data-modulo="Ajuste general">
                                        <i class="fas fa-cogs nav-icon"></i>
                                        <p>
                                            Ajuste general
                                        </p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="divisa" class="nav-link bitacora-link" data-modulo="Divisas">
                                        <i class="fas fa-dollar-sign nav-icon"></i>
                                        <p>
                                            Divisas
                                        </p>
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a href="tpago" class="nav-link bitacora-link" data-modulo="Tipos de pago">
                                        <i class="fas fa-money-bill nav-icon"></i>
                                        <p>
                                            Tipos de pago
                                        </p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="banco" class="nav-link bitacora-link" data-modulo="Banco">
                                        <i class="fas fa-landmark nav-icon"></i>

                                        <p>
                                            Bancos
                                        </p>
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a href="tipocuenta" class="nav-link bitacora-link" data-modulo="Tipo de cuenta">
                                        <i class="fas fa-university nav-icon"></i>
                                        <p>
                                            Tipo de cuenta
                                        </p>
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a href="unidad" class="nav-link bitacora-link" data-modulo="Unidades de medida">
                                        <i class="fas fa-balance-scale nav-icon"></i>
                                        <p>
                                            Unidades de medida
                                        </p>
                                    </a>
                                </li>

                                <ul class="nav nav-treeview">
                                    <li class="nav-header">GENERAL</li>
                                    <li class="nav-item">
                                        <a href="general" class="nav-link bitacora-link" data-modulo="Ajuste general">
                                            <i class="fas fa-cogs nav-icon"></i>
                                            <p>
                                                Empresa
                                            </p>
                                        </a>
                                    </li>

                                    <li class="nav-header">SEGURIDAD Y ACCESOS</li>
                                    <?php if ($_SESSION["usuario"] == 1): ?>
                                        <li class="nav-item">
                                            <a href="usuarios" class="nav-link bitacora-link" data-modulo="Usuarios">
                                                <i class="nav-icon fas fa-users-cog"></i>
                                                <p>
                                                    Usuarios
                                                </p>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                    <li class="nav-item">
                                        <a href="roles" class="nav-link bitacora-link" data-modulo="Ajuste de roles">
                                            <i class="nav-icon fas fa-user-tag"></i>
                                            <p>
                                                Roles y Permisos
                                            </p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="bitacora" class="nav-link">
                                            <i class="nav-icon fas fa-user-shield"></i>
                                            <p>
                                                Bitacora
                                            </p>
                                        </a>
                                    </li>

                                    <li class="nav-header">PRODUCTOS</li>
                                    <li class="nav-item">
                                        <a href="unidad" class="nav-link bitacora-link" data-modulo="Unidades de medida">
                                            <i class="fas fa-balance-scale nav-icon"></i>
                                            <p>
                                                Unidades de medida
                                            </p>
                                        </a>
                                    </li>
                                    <?php if ($_SESSION["categoria"] == 1): ?>
                                        <li class="nav-item">
                                            <a href="categorias" class="nav-link bitacora-link" data-modulo="Categorias">
                                                <i class="nav-icon fa fa-table"></i>
                                                <p>
                                                    Categorías
                                                </p>
                                            </a>
                                        </li>
                                    <?php endif; ?>

                                    <li class="nav-item">
                                        <a href="marcas" class="nav-link bitacora-link" data-modulo="Marcas">
                                            <i class="fas fa-tags nav-icon"></i>
                                            <p>
                                                Marcas
                                            </p>
                                        </a>
                                    </li>
                                    <li class="nav-header">FINANZAS</li>
                                    <li class="nav-item">
                                        <a href="divisa" class="nav-link ">
                                            <i class="fas fa-dollar-sign nav-icon"></i>
                                            <p>
                                                Divisas
                                            </p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="tpago" class="nav-link ">
                                            <i class="fas fa-money-bill nav-icon"></i>
                                            <p>
                                                Tipos de pago
                                            </p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="categoriag" class="nav-link">
                                            <i class="fas fa-file-invoice-dollar nav-icon"></i>
                                            <p>
                                                Categoría de Gastos
                                            </p>
                                        </a>
                                    </li>

                                </ul>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    <?php endif; */?>


</aside>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        document.querySelectorAll(".bitacora-link").forEach(link => {
            link.addEventListener("click", (e) => {
                const modulo = link.getAttribute("data-modulo");

                // Enviar la solicitud AJAX sin interrumpir la navegación
                navigator.sendBeacon("index.php?pagina=bitacora", new URLSearchParams({
                    modulo: modulo
                }));
            });
        });
    });
</script>