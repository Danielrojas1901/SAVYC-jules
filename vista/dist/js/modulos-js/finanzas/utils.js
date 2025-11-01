const graficos = {
    proyecciones: null,
    presupuestos: null
};

const tablas = {
    proyecciones: null,
    precision: null
};

function formatearMoneda(value) {
    if (value === null || value === undefined) {
        return 'Bs. 0,00';
    }

    let numericValue = typeof value === 'string' ? parseFloat(value.replace(/[^\d.,]/g, '').replace(',', '.')) : value;
    
    if (isNaN(numericValue)) {
        console.warn('Valor numérico inválido:', value);
        return 'Bs. 0,00';
    }

    return 'Bs. ' + numericValue.toLocaleString('es-VE', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
        useGrouping: true
    }).replace(/\./g, '*').replace(/,/g, '.').replace(/\*/g, ',');
}

function formatearPorcentaje(valor) {
    if (typeof valor !== 'number') {
        valor = parseFloat(valor);
    }
    
    if (isNaN(valor)) {
        return '0.00%';
    }
    
    return new Intl.NumberFormat('es-VE', {
        style: 'percent',
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(valor / 100);
}

const DATATABLE_SPANISH_CONFIG = {
    "sProcessing": "Procesando...",
    "sLengthMenu": "Mostrar _MENU_ registros",
    "sZeroRecords": "No se encontraron resultados",
    "sEmptyTable": "Ningún dato disponible en esta tabla",
    "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
    "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
    "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
    "sInfoPostFix": "",
    "sSearch": "Buscar:",
    "sUrl": "",
    "sInfoThousands": ",",
    "sLoadingRecords": "Cargando...",
    "oPaginate": {
        "sFirst": "Primero",
        "sLast": "Último",
        "sNext": "Siguiente",
        "sPrevious": "Anterior"
    },
    "oAria": {
        "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
        "sSortDescending": ": Activar para ordenar la columna de manera descendente"
    },
    "buttons": {
        "copy": "Copiar",
        "colvis": "Visibilidad"
    }
};

const configComun = {
    responsive: true,
    autoWidth: false,
    language: DATATABLE_SPANISH_CONFIG
};

function inicializarTabla(idTabla, config) {
    const $tabla = $(idTabla);
    if (!$.fn.DataTable.isDataTable($tabla)) {
        return $tabla.DataTable(config);
    }
    return $tabla.DataTable();
}

const TableUtils = {
    createMoneyColumn: function(field, className = 'text-end') {
        return {
            data: field,
            className: className,
            defaultContent: '',
            render: function(data, type) {
                if (type === 'display') {
                    return formatearMoneda(data || 0);
                }
                return data || 0;
            }
        };
    },

    createPercentageColumn: function(field, className = 'text-end') {
        return {
            data: field,
            className: className,
            defaultContent: '',
            render: function(data, type) {
                if (type === 'display') {
                    return formatearPorcentaje(data || 0);
                }
                return data || 0;
            }
        };
    },

    createTextColumn: function(field, className = '') {
        return {
            data: field,
            className: className,
            defaultContent: '',
            render: function(data, type) {
                if (type === 'display') {
                    return data || '';
                }
                return data || '';
            }
        };
    },

    createActionButtonColumn: function(field, icon, buttonClass, buttonText = '') {
        return {
            data: field,
            className: 'text-center',
            defaultContent: '',
            render: function(data, type) {
                if (type === 'display' && data) {
                    return `<button class="btn btn-sm ${buttonClass}" data-id="${data}">
                        <i class="fas ${icon}"></i> ${buttonText}
                    </button>`;
                }
                return data || '';
            }
        };
    },

    updateTable: function(table, data, tableName = 'tabla') {
        if (!table) return;
        
        try {
            console.group(`Actualización ${tableName}`);
            console.log('Actualizando con:', data);
            table.clear();
            table.rows.add(data).draw();
            console.log(`${tableName} actualizada con éxito`);
            console.groupEnd();
        } catch (error) {
            console.error(`Error al actualizar ${tableName}:`, error);
            console.groupEnd();
        }
    },

    initializeTable: function(selector, config, data, tableName = 'tabla') {
        try {
            console.group(`Inicialización ${tableName}`);
            console.log('Datos a cargar:', data);

            const table = inicializarTabla(selector, {
                ...configComun,
                data: data,
                ...config
            });

            console.log(`${tableName} inicializada correctamente`);
            console.groupEnd();
            return table;
        } catch (error) {
            console.error(`Error al inicializar ${tableName}:`, error);
            console.groupEnd();
            return null;
        }
    }
};


const DateUtils = {
    initializeMonthSelectors: function() {
        console.group('Inicializando selectores de fecha');
        
        const fecha = new Date();
        const mesActual = fecha.getMonth() + 1;
        const añoActual = fecha.getFullYear();

        console.log('Fecha actual:', { mesActual, añoActual });

        // definir meses
        const meses = [
            {value: 1, name: 'Enero'},
            {value: 2, name: 'Febrero'},
            {value: 3, name: 'Marzo'},
            {value: 4, name: 'Abril'},
            {value: 5, name: 'Mayo'},
            {value: 6, name: 'Junio'},
            {value: 7, name: 'Julio'},
            {value: 8, name: 'Agosto'},
            {value: 9, name: 'Septiembre'},
            {value: 10, name: 'Octubre'},
            {value: 11, name: 'Noviembre'},
            {value: 12, name: 'Diciembre'}
        ];

        // inicializar selectores de mes
        console.log('Inicializando selectores de mes');
        const monthSelectors = $('select[id^="mes-"]');
        console.log('Encontrados selectores de mes:', monthSelectors.length);
        monthSelectors.each(function() {
            const $select = $(this);
            const id = $select.attr('id');
            console.log('Inicializando selector de mes:', id);
            $select.empty();
            meses.forEach(mes => {
                $select.append(new Option(mes.name, mes.value));
            });
        });
        
        // inicializar selectores de año
        console.log('Inicializando selectores de año');
        const yearSelectors = $('select[id^="ano-"]');
        console.log('Encontrados selectores de año:', yearSelectors.length);
        yearSelectors.each(function() {
            const $select = $(this);
            const id = $select.attr('id');
            console.log('Inicializando selector de año:', id);
            $select.empty();
            
            const añoInicial = 2020;  // año inicial fijo
            const añoFinal = añoActual + 1;  // año actual + 1
            console.log('Rango de años:', { añoInicial, añoFinal });
            
            for (let año = añoFinal; año >= añoInicial; año--) {
                $select.append(new Option(año, año));
            }
        });
        
        // establecer valores por defecto
        let mesInicio = mesActual - 5;
        let añoInicio = añoActual;
        if (mesInicio <= 0) {
            mesInicio += 12;
            añoInicio--;
        }
        
        console.log('Estableciendo valores por defecto:', {
            mesInicio,
            añoInicio,
            mesFin: mesActual,
            añoFin: añoActual
        });
        
        $('#mes-inicio').val(mesInicio);
        $('#ano-inicio').val(añoInicio);
        $('#mes-fin').val(mesActual);
        $('#ano-fin').val(añoActual);
        
        // también inicializar otros selectores específicos si existen
        if ($('#mes-inventario').length) {
            $('#mes-inventario').val(mesActual);
            $('#ano-inventario').val(añoActual);
        }
        
        if ($('#mes-rentabilidad').length) {
            $('#mes-rentabilidad').val(mesActual);
            $('#ano-rentabilidad').val(añoActual);
        }
        
        console.groupEnd();
    },

    validatePeriod: function(seccion) {
        if (seccion === 'inicio' || seccion === 'fin') {
            const mesInicio = parseInt($('#mes-inicio').val());
            const añoInicio = parseInt($('#ano-inicio').val());
            const mesFin = parseInt($('#mes-fin').val());
            const añoFin = parseInt($('#ano-fin').val());
            
            const fechaInicio = new Date(añoInicio, mesInicio - 1);
            const fechaFin = new Date(añoFin, mesFin - 1);

            if (fechaFin < fechaInicio) {
                $('#mes-fin').val(mesInicio);
                $('#ano-fin').val(añoInicio);
            }
        }
    }
};

const EventUtils = {
    initializeTabEvents: function() {
        $('#pestañas button[data-toggle="tab"]').on('click', function (e) {
            e.preventDefault();
            $(this).tab('show');
        });

        $('#pestañas button[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            const targetTab = $(e.target).attr('data-target');
            if (targetTab === '#presupuestos' && graficos.presupuestos) {
                graficos.presupuestos.resize();
            } else if (targetTab === '#proyecciones' && graficos.proyecciones) {
                graficos.proyecciones.resize();
            }
        });
    },

    initializeModalEvents: function() {
        $('.modal').off('hidden.bs.modal');
        
        $('.modal').on('hidden.bs.modal', function() {
            const idModal = $(this).attr('id');
            if (graficos[idModal]) {
                UtilidadesGraficos.destruirGrafico(graficos[idModal]);
                graficos[idModal] = null;
            }
        });

        $('.modal canvas').each(function() {
            const contenedor = $(this).parent();
            if (contenedor) {
                contenedor.css('height', '400px');
            }
        });
    },

    initializeTableEvents: function() {
        $('#tabla-proyecciones-futuras tbody, #tabla-precision-historica tbody').on('click', 'tr', function() {
            const tabla = $(this).closest('table').attr('id');
            const data = tabla === 'tabla-proyecciones-futuras' ? 
                tablas.proyecciones.row(this).data() : 
                tablas.precision.row(this).data();
                
            if (data) {
                const tipoAnalisis = $('#ver-historico').val();
                if (tipoAnalisis === 'proyecciones') {
                    mostrarModalProyeccion(data.producto, data.cod_producto);
                } else {
                    mostrarModalPrecision(data.producto, data.cod_producto);
                }
            }
        });
    },

    initializeSelectEvents: function() {
        $('#ver-historico').on('change', function() {
            actualizarTipoAnalisis();
            actualizarTablaProyecciones();
        });

        $('#periodo-proyeccion').on('change', function() {
            const tipoAnalisis = $('#ver-historico').val();
            if (tipoAnalisis === 'proyecciones') {
                if (graficos.proyecciones) {
                    UtilidadesGraficos.destruirGrafico(graficos.proyecciones);
                }
                inicializarGraficos();
                actualizarTablaProyecciones();
            }
        });

    }
}; 