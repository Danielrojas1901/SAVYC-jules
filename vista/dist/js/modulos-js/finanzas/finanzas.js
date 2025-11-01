$(document).ready(function() {
    console.group('Inicialización de Finanzas');
    console.debug('Iniciando módulo de finanzas');
    
    console.log('Inicializando utilidades y eventos comunes');
    DateUtils.initializeMonthSelectors();
    EventUtils.initializeTabEvents();
    EventUtils.initializeModalEvents();
    EventUtils.initializeTableEvents();
    EventUtils.initializeSelectEvents();
    
    // optimización: inicializar solo el tab activo
    console.log('Inicializando tab inicial (cuentas)');
    CuentasTab.initialize();
    
    // configurar eventos para carga lazy de otros tabs
    FinanzasLazyLoader.initializeLazyLoading();
    
    console.log('Mostrando tab inicial');
    $('#cuentas-tab').tab('show');
    
    console.groupEnd();
});

// gestión de carga lazy de datos por tab
const FinanzasLazyLoader = {
    tabsInitialized: {
        cuentas: true, // ya inicializado
        inventario: false,
        rentabilidad: false,
        presupuestos: false,
        proyecciones: false
    },

    initializeLazyLoading: function() {
        console.log('Configurando carga lazy para tabs');
        
        // eventos para cargar datos solo cuando se active cada tab
        $('#inventario-tab').on('shown.bs.tab', () => this.loadTab('inventario'));
        $('#rentabilidad-tab').on('shown.bs.tab', () => this.loadTab('rentabilidad'));
        $('#presupuestos-tab').on('shown.bs.tab', () => this.loadTab('presupuestos'));
        $('#proyecciones-tab').on('shown.bs.tab', () => this.loadTab('proyecciones'));
    },

    loadTab: function(tabName) {
        if (this.tabsInitialized[tabName]) {
            console.log(`Tab ${tabName} ya está inicializado`);
            return;
        }

        console.log(`Cargando datos para tab: ${tabName}`);
        
        switch(tabName) {
            case 'inventario':
                InventarioTab.initialize();
                break;
            case 'rentabilidad':
                RentabilidadTab.initialize();
                break;
            case 'presupuestos':
                PresupuestosTab.initialize();
                break;
            case 'proyecciones':
                this.loadProyeccionesData();
                break;
        }
        
        this.tabsInitialized[tabName] = true;
    },



    loadProyeccionesData: function() {
        $.ajax({
            url: 'index.php?pagina=finanzas',
            method: 'POST',
            data: {
                accion: 'obtener_datos_proyecciones'
            },
            success: (response) => {
                if (response.success) {
                    // asignar datos al objeto global
                    window.datosFinanzas = window.datosFinanzas || {};
                    window.datosFinanzas.proyecciones = response.proyecciones;
                    window.datosFinanzas.proyecciones_historicas = response.proyecciones_historicas;
                    window.datosFinanzas.historico = response.historico;
                    window.datosFinanzas.precision = response.precision;
                    window.datosFinanzas.datos_grafico_proyecciones = response.datos_grafico_proyecciones;
                    
                    // inicializar tab
                    ProyeccionesTab.initialize();
                } else {
                    console.error('Error al cargar datos de proyecciones:', response.message);
                }
            },
            error: (xhr, status, error) => {
                console.error('Error en petición de proyecciones:', error);
            }
        });
    }
}; 