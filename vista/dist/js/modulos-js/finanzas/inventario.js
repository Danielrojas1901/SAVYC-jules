const InventarioTab = {
    tabla: null,
    graficoDetalle: null,
    tablaDetalle: null,

    initialize: function() {
        this.initializeTable();
        this.initializeMonthSelector();
        this.initializeEventListeners();
        // actualizarDatos se llamará después de que los selectores estén inicializados
    },

    initializeTable: function() {
        console.group('Initializing Inventario Table');
        
        this.tabla = TableUtils.initializeTable('#tabla-rotacion', {
            columns: [
                TableUtils.createTextColumn('producto'),
                {
                    data: 'estado_stock',
                    className: 'text-center',
                    render: function(data, type, row) {
                        if (type === 'display') {
                            const badges = {
                                'sin_stock': '<span class="badge bg-danger">Sin Stock</span>',
                                'critico': '<span class="badge bg-warning text-dark">Crítico</span>',
                                'bajo': '<span class="badge bg-info">Bajo</span>',
                                'exceso': '<span class="badge bg-warning text-dark">Exceso</span>',
                                'normal': '<span class="badge bg-success">Normal</span>'
                            };
                            return badges[data] || data;
                        }
                        return data;
                    }
                },
                TableUtils.createTextColumn('stock_inicial', 'text-end'),
                TableUtils.createTextColumn('stock_final', 'text-end'),
                TableUtils.createTextColumn('ventas_cantidad', 'text-end'),
                {
                    data: 'dias_rotacion',
                    className: 'text-end',
                    render: function(data, type, row) {
                        if (type === 'display') {
                            const icon = row.tendencia_rotacion === 'mejorando' 
                                ? '<i class="fas fa-arrow-down text-success"></i>' 
                                : row.tendencia_rotacion === 'empeorando'
                                ? '<i class="fas fa-arrow-up text-danger"></i>'
                                : '<i class="fas fa-equals text-muted"></i>';
                            return `${data} días ${icon}`;
                        }
                        return data;
                    }
                },
                {
                    data: 'promedio_dias_rotacion',
                    className: 'text-end',
                    title: 'Promedio',
                    render: function(data, type) {
                        if (type === 'display') {
                            return `${data} días`;
                        }
                        return data;
                    }
                },
                {
                    data: 'estado_rotacion',
                    className: 'text-center',
                    render: function(data, type) {
                        if (type === 'display') {
                            const badges = {
                                'alto': '<span class="badge bg-danger">Alto</span>',
                                'bajo': '<span class="badge bg-success">Bajo</span>',
                                'normal': '<span class="badge bg-info">Normal</span>'
                            };
                            return badges[data] || data;
                        }
                        return data;
                    }
                },
                {
                    data: 'cod_presentacion',
                    className: 'text-center',
                    render: function(data, type) {
                        if (type === 'display') {
                            if (window.permisos?.finanza?.consultar) {
                                return `<button class="btn btn-sm btn-info ver-detalle-rotacion" data-id="${data}">
                                    <i class="fas fa-chart-line"></i>
                                </button>`;
                            }
                            return '';
                        }
                        return data;
                    }
                }
            ],
            order: [[1, 'asc'], [5, 'desc']]
        });

        this.tablaDetalle = TableUtils.initializeTable('#tabla-detalle-rotacion', {
            columns: [
                TableUtils.createTextColumn('mes'),
                {
                    data: 'estado_stock',
                    className: 'text-center',
                    render: function(data, type) {
                        if (type === 'display') {
                            const badges = {
                                'sin_stock': '<span class="badge bg-danger">Sin Stock</span>',
                                'critico': '<span class="badge bg-warning text-dark">Crítico</span>',
                                'bajo': '<span class="badge bg-info">Bajo</span>',
                                'exceso': '<span class="badge bg-warning text-dark">Exceso</span>',
                                'normal': '<span class="badge bg-success">Normal</span>'
                            };
                            return badges[data] || data;
                        }
                        return data;
                    }
                },
                TableUtils.createTextColumn('stock_inicial', 'text-end'),
                TableUtils.createTextColumn('stock_final', 'text-end'),
                TableUtils.createTextColumn('ventas_cantidad', 'text-end'),
                {
                    data: 'dias_rotacion',
                    className: 'text-end',
                    render: function(data, type, row) {
                        if (type === 'display') {
                            const icon = row.tendencia_rotacion === 'mejorando' 
                                ? '<i class="fas fa-arrow-down text-success"></i>' 
                                : row.tendencia_rotacion === 'empeorando'
                                ? '<i class="fas fa-arrow-up text-danger"></i>'
                                : '<i class="fas fa-equals text-muted"></i>';
                            return `${data} días ${icon}`;
                        }
                        return data;
                    }
                },
                {
                    data: 'promedio_dias_rotacion',
                    className: 'text-end',
                    title: 'Promedio',
                    render: function(data, type) {
                        if (type === 'display') {
                            return `${data} días`;
                        }
                        return data;
                    }
                },
                {
                    data: 'estado_rotacion',
                    className: 'text-center',
                    render: function(data, type) {
                        if (type === 'display') {
                            const badges = {
                                'alto': '<span class="badge bg-danger">Alto</span>',
                                'bajo': '<span class="badge bg-success">Bajo</span>',
                                'normal': '<span class="badge bg-info">Normal</span>'
                            };
                            return badges[data] || data;
                        }
                        return data;
                    }
                }
            ],
            order: [[0, 'desc']]
        });

        console.groupEnd();
    },

    actualizarDatos: function() {
        const mes = $('#mes-inventario').val();
        const año = $('#año-inventario').val();

        // Si no hay mes o año seleccionado, no hacemos nada
        if (!mes || !año) return;

        $.ajax({
            url: 'index.php?pagina=finanzas',
            method: 'POST',
            data: {
                accion: 'obtener_stock_mensual',
                mes: mes,
                año: año
            },
            success: (response) => {
                if (response.success) {
                    if (response.stock && response.stock.length > 0) {
                        $('#tabla-rotacion').show();
                        TableUtils.updateTable(this.tabla, response.stock, 'tabla rotacion');
                    } else {
                        $('#tabla-rotacion').hide();
                        // Solo mostramos la alerta si tenemos mes y año seleccionados
                        if (mes && año) {
                            Swal.fire({
                                icon: 'info',
                                title: 'Sin datos',
                                text: 'No hay datos de rotación disponibles para el período seleccionado'
                            });
                        }
                    }
                }
            },
            error: (xhr, status, error) => {
                console.error('Error al obtener datos de inventario:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al obtener datos de inventario'
                });
            }
        });
    },

    initializeMonthSelector: function() {
        $.ajax({
            url: 'index.php?pagina=finanzas',
            method: 'POST',
            data: {
                accion: 'obtener_periodos_stock'
            },
            success: (response) => {
                if (response.success && response.periodos) {
                    const periodos = response.periodos;
                    const fechaActual = new Date();
                    const mesActual = fechaActual.getMonth() + 1;
                    const añoActual = fechaActual.getFullYear();
                    
                    const años = [...new Set(periodos.map(p => p.año))];
                    
                    $('#año-inventario').empty();
                    años.forEach(año => {
                        $('#año-inventario').append(new Option(año, año));
                    });
                    
                    const actualizarMeses = (añoSeleccionado) => {
                        const mesesDisponibles = periodos.filter(p => p.año == añoSeleccionado);
                        $('#mes-inventario').empty();
                        
                        mesesDisponibles.forEach(periodo => {
                            const nombreMes = new Date(2000, periodo.mes - 1, 1)
                                .toLocaleString('es-ES', { month: 'long' });
                            const nombreMesCapitalizado = nombreMes.charAt(0).toUpperCase() + nombreMes.slice(1);
                            $('#mes-inventario').append(new Option(nombreMesCapitalizado, periodo.mes));
                        });
                    };
                    
                    $('#año-inventario').on('change', function() {
                        actualizarMeses($(this).val());
                        InventarioTab.actualizarDatos();
                    });
                    
                    // Intentar seleccionar el año y mes actual, o el más reciente disponible
                    if (años.length > 0) {
                        const añoMasReciente = años.includes(añoActual) ? añoActual : Math.max(...años);
                        $('#año-inventario').val(añoMasReciente);
                        actualizarMeses(añoMasReciente);
                        
                        // Después de actualizar los meses, intentar seleccionar el mes actual
                        const mesesDisponibles = periodos.filter(p => p.año == añoMasReciente);
                        if (mesesDisponibles.length > 0) {
                            const mesSeleccionado = mesesDisponibles.find(p => p.mes == mesActual) ? 
                                mesActual : 
                                Math.max(...mesesDisponibles.map(p => p.mes));
                            $('#mes-inventario').val(mesSeleccionado);
                        }
                    }
                    
                    this.actualizarDatos();
                } else {
                    console.error('No se pudieron obtener los periodos disponibles');
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'No se pudieron obtener los periodos disponibles'
                    });
                }
            },
            error: (xhr, status, error) => {
                console.error('Error al obtener periodos:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al obtener los periodos disponibles'
                });
            }
        });
    },

    initializeEventListeners: function() {
        $('#mes-inventario, #año-inventario').on('change', () => {
            this.actualizarDatos();
        });

        $('#tabla-rotacion').on('click', '.ver-detalle-rotacion', (e) => {
            const codPresentacion = $(e.currentTarget).data('id');
            this.mostrarDetalle(codPresentacion);
        });

        // inicializar tooltips para la leyenda de indicadores
        this.initializeTooltips();

        // evento para exportar PDF general
        $('#exportar-rotacion-general-pdf').on('click', () => {
            this.exportarReporteGeneral();
        });
    },

    initializeTooltips: function() {
        // inicializar tooltips de bootstrap
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    },

    mostrarDetalle: function(codPresentacion) {
        $.ajax({
            url: 'index.php?pagina=finanzas',
            method: 'POST',
            data: {
                accion: 'obtener_detalle_stock',
                cod_presentacion: codPresentacion
            },
            success: (response) => {
                if (response.success && response.datos) {
                    const datos = response.datos;
                    $('#modal-rotacion-label').text(`Detalle de Rotación - ${datos.producto}`);
                    this.actualizarGraficoDetalle(datos);
                    
                    const detalleData = datos.labels.map((mes, index) => ({
                        mes: mes,
                        estado_stock: datos.estado_stock[index],
                        stock_inicial: datos.stock_inicial[index],
                        stock_final: datos.stock_final[index],
                        ventas_cantidad: datos.ventas[index],
                        dias_rotacion: datos.dias_rotacion[index],
                        promedio_dias_rotacion: datos.promedio_dias_rotacion[index],
                        estado_rotacion: datos.estado_rotacion[index],
                        tendencia_rotacion: datos.tendencia_rotacion[index]
                    }));
                    
                    if ($.fn.DataTable.isDataTable('#tabla-detalle-rotacion')) {
                        $('#tabla-detalle-rotacion').DataTable().clear().rows.add(detalleData).draw();
                    }
                    
                    // configurar botón de exportar PDF
                    $('#exportar-rotacion-pdf').off('click').on('click', () => {
                        ExportarPDFs.exportToPDF('rotacion', {
                            producto: datos.producto,
                            periodo: 'Análisis Histórico',
                            sourceCanvasId: 'grafico-rotacion',
                            tableData: detalleData,
                            stats: this.generarEstadisticasRotacion(detalleData)
                        }).catch(error => {
                            console.error('Error en exportación:', error);
                        });
                    });
                    
                    $('#modal-rotacion').modal('show');
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Error al obtener el detalle de rotación'
                    });
                }
            },
            error: (xhr, status, error) => {
                console.error('Error al obtener detalle de rotación:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al obtener el detalle de rotación'
                });
            }
        });
    },

    actualizarGraficoDetalle: function(datos) {
        if (this.graficoDetalle) {
            UtilidadesGraficos.destruirGrafico(this.graficoDetalle);
        }

        const ctx = document.getElementById('grafico-rotacion');
        if (!ctx) return;

        const detalleData = datos.labels.map((mes, index) => ({
            mes: mes,
            estado_stock: datos.estado_stock[index],
            stock_inicial: datos.stock_inicial[index],
            stock_final: datos.stock_final[index],
            ventas_cantidad: datos.ventas[index],
            dias_rotacion: datos.dias_rotacion[index],
            promedio_dias_rotacion: datos.promedio_dias_rotacion[index],
            estado_rotacion: datos.estado_rotacion[index],
            tendencia_rotacion: datos.tendencia_rotacion[index]
        }));
        
        if ($.fn.DataTable.isDataTable('#tabla-detalle-rotacion')) {
            $('#tabla-detalle-rotacion').DataTable().clear().rows.add(detalleData).draw();
        }

        // determinar tipo de gráfico basado en cantidad de datos
        const tipoGrafico = datos.labels.length === 1 ? 'bar' : 'line';
        const esBarras = tipoGrafico === 'bar';

        // configurar datasets según el tipo de gráfico
        const datasets = [
            {
                label: 'Stock Inicial',
                data: datos.stock_inicial,
                borderColor: 'rgba(82, 113, 255, 1)',
                backgroundColor: 'rgba(82, 113, 255, 0.8)',
                borderWidth: esBarras ? 2 : 1,
                ...(esBarras ? {} : {
                    tension: 0.4,
                    fill: true,
                    backgroundColor: 'rgba(82, 113, 255, 0.2)'
                })
            },
            {
                label: 'Stock Final',
                data: datos.stock_final,
                borderColor: 'rgba(94, 193, 211, 1)',
                backgroundColor: 'rgba(94, 193, 211, 0.8)',
                borderWidth: esBarras ? 2 : 1,
                ...(esBarras ? {} : {
                    tension: 0.4,
                    fill: true,
                    backgroundColor: 'rgba(94, 193, 211, 0.2)'
                })
            }
        ];

        this.graficoDetalle = new Chart(ctx, {
            type: tipoGrafico,
            data: {
                labels: datos.labels,
                datasets: datasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: `Evolución de Stock - ${datos.producto}`
                    },
                    tooltip: {
                        mode: esBarras ? 'nearest' : 'index',
                        intersect: esBarras
                    },
                    legend: {
                        display: true,
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Cantidad'
                        },
                        grid: {
                            display: true,
                            color: 'rgba(0, 0, 0, 0.1)'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Período'
                        },
                        grid: {
                            display: esBarras,
                            color: 'rgba(0, 0, 0, 0.1)'
                        }
                    }
                },
                ...(esBarras ? {
                    // configuraciones específicas para gráfico de barras
                    categoryPercentage: 0.8,
                    barPercentage: 0.9
                } : {})
            }
        });
    },

    generarEstadisticasRotacion: function(detalleData) {
        const stats = [];
        let totalMeses = detalleData.length;
        let mesesOptimos = 0;
        let mesesProblematicos = 0;
        let rotacionPromedio = 0;
        let tendenciaMejorando = 0;
        let stockMinimo = Infinity;
        let stockMaximo = 0;
        let ventasTotal = 0;
        
        detalleData.forEach(row => {
            // contar estados de rotación
            if (row.estado_rotacion === 'normal') mesesOptimos++;
            if (row.estado_rotacion === 'alto') mesesProblematicos++;
            
            // tendencia
            if (row.tendencia_rotacion === 'mejorando') tendenciaMejorando++;
            
            // promedios y totales
            rotacionPromedio += parseInt(row.dias_rotacion) || 0;
            stockMinimo = Math.min(stockMinimo, parseInt(row.stock_final) || 0);
            stockMaximo = Math.max(stockMaximo, parseInt(row.stock_final) || 0);
            ventasTotal += parseInt(row.ventas_cantidad) || 0;
        });
        
        if (totalMeses > 0) {
            rotacionPromedio = (rotacionPromedio / totalMeses).toFixed(1);
            
            stats.push({
                title: 'Meses con Rotación Óptima',
                value: `${mesesOptimos} de ${totalMeses} meses (${((mesesOptimos / totalMeses) * 100).toFixed(1)}%)`
            });
            
            if (mesesProblematicos > 0) {
                stats.push({
                    title: 'Meses con Rotación Lenta',
                    value: `${mesesProblematicos} de ${totalMeses} meses (${((mesesProblematicos / totalMeses) * 100).toFixed(1)}%)`
                });
            }
            
            stats.push({
                title: 'Rotación Promedio General',
                value: `${rotacionPromedio} días`
            });
            
            if (tendenciaMejorando > 0) {
                stats.push({
                    title: 'Tendencia de Mejora',
                    value: `${tendenciaMejorando} de ${totalMeses} meses mostraron mejoría (${((tendenciaMejorando / totalMeses) * 100).toFixed(1)}%)`
                });
            }
            
            stats.push({
                title: 'Rango de Stock Final',
                value: `Mínimo: ${stockMinimo} | Máximo: ${stockMaximo} unidades`
            });
            
            stats.push({
                title: 'Total de Ventas Históricas',
                value: `${ventasTotal} unidades`
            });
        }
        
        return stats;
    },

    exportarReporteGeneral: function() {
        const mes = $('#mes-inventario').val();
        const año = $('#año-inventario').val();
        
        if (!mes || !año) {
            Swal.fire({
                icon: 'warning',
                title: 'Atención',
                text: 'Por favor seleccione un período para generar el reporte'
            });
            return;
        }

        // verificar si hay datos en la tabla
        if (!this.tabla || this.tabla.data().count() === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Sin datos',
                text: 'No hay datos de rotación disponibles para el período seleccionado'
            });
            return;
        }

        // obtener datos de la tabla
        const tableData = this.tabla.rows().data().toArray();
        
        // construir período
        const nombreMes = new Date(2000, mes - 1, 1).toLocaleString('es-ES', { month: 'long' });
        const nombreMesCapitalizado = nombreMes.charAt(0).toUpperCase() + nombreMes.slice(1);
        const periodo = `${nombreMesCapitalizado} ${año}`;

        ExportarPDFs.exportToPDF('rotacion_general', {
            periodo: periodo,
            mes: mes,
            año: año,
            tableData: tableData,
            stats: this.generarEstadisticasRotacionGeneral(tableData)
        }).catch(error => {
            console.error('Error en exportación:', error);
        });
    },

    generarEstadisticasRotacionGeneral: function(tableData) {
        const stats = [];
        let totalProductos = tableData.length;
        let productosOptimos = 0;
        let productosProblematicos = 0;
        let totalVentas = 0;
        let stockTotal = 0;
        let rotacionPromedio = 0;
        let mejorRotacion = Infinity;
        let peorRotacion = 0;
        let productoMejorRotacion = '';
        let productoPeorRotacion = '';
        
        // contadores por estado de stock
        let estadosStock = {
            sin_stock: 0,
            critico: 0,
            bajo: 0,
            exceso: 0,
            normal: 0
        };
        
        // contadores por estado de rotación
        let estadosRotacion = {
            alto: 0,    // rotación lenta
            bajo: 0,    // rotación rápida  
            normal: 0   // rotación normal
        };
        
        tableData.forEach(row => {
            // contar estados de stock
            if (estadosStock.hasOwnProperty(row.estado_stock)) {
                estadosStock[row.estado_stock]++;
            }
            
            // contar estados de rotación
            if (estadosRotacion.hasOwnProperty(row.estado_rotacion)) {
                estadosRotacion[row.estado_rotacion]++;
            }
            
            // analizar rotación
            if (row.estado_rotacion === 'normal' || row.estado_rotacion === 'bajo') {
                productosOptimos++;
            }
            if (row.estado_rotacion === 'alto') {
                productosProblematicos++;
            }
            
            // totales
            totalVentas += parseInt(row.ventas_cantidad) || 0;
            stockTotal += parseInt(row.stock_final) || 0;
            
            const diasRotacion = parseInt(row.dias_rotacion) || 0;
            rotacionPromedio += diasRotacion;
            
            // mejor y peor rotación (menor días = mejor rotación)
            if (diasRotacion > 0 && diasRotacion < mejorRotacion) {
                mejorRotacion = diasRotacion;
                productoMejorRotacion = row.producto;
            }
            if (diasRotacion > peorRotacion) {
                peorRotacion = diasRotacion;
                productoPeorRotacion = row.producto;
            }
        });
        
        if (totalProductos > 0) {
            rotacionPromedio = (rotacionPromedio / totalProductos).toFixed(1);
            
            stats.push({
                title: 'Total de Productos Analizados',
                value: `${totalProductos} productos`
            });
            
            stats.push({
                title: 'Productos con Rotación Óptima',
                value: `${productosOptimos} productos (${((productosOptimos / totalProductos) * 100).toFixed(1)}%)`
            });
            
            if (productosProblematicos > 0) {
                stats.push({
                    title: 'Productos con Rotación Lenta',
                    value: `${productosProblematicos} productos (${((productosProblematicos / totalProductos) * 100).toFixed(1)}%)`
                });
            }
            
            stats.push({
                title: 'Rotación Promedio General',
                value: `${rotacionPromedio} días`
            });
            
            stats.push({
                title: 'Total de Ventas del Período',
                value: `${totalVentas} unidades`
            });
            
            stats.push({
                title: 'Stock Total Final',
                value: `${stockTotal} unidades`
            });
            
            if (productoMejorRotacion) {
                stats.push({
                    title: 'Producto con Mejor Rotación',
                    value: `${productoMejorRotacion} (${mejorRotacion} días)`
                });
            }
            
            if (productoPeorRotacion) {
                stats.push({
                    title: 'Producto con Peor Rotación',
                    value: `${productoPeorRotacion} (${peorRotacion} días)`
                });
            }
            
            // estadísticas por estado de stock
            stats.push({
                title: 'Distribución por Estado de Stock',
                value: `Normal: ${estadosStock.normal} | Crítico: ${estadosStock.critico} | Bajo: ${estadosStock.bajo} | Exceso: ${estadosStock.exceso} | Sin Stock: ${estadosStock.sin_stock}`
            });
            
            // estadísticas por estado de rotación
            stats.push({
                title: 'Distribución por Velocidad de Rotación',
                value: `Rápida: ${estadosRotacion.bajo} | Normal: ${estadosRotacion.normal} | Lenta: ${estadosRotacion.alto}`
            });
        }
        
        return stats;
    }
};
