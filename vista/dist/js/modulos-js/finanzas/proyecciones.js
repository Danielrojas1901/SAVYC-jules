const ProyeccionesTab = {
    tablaFuturo: null,
    tablaHistorico: null,
    grafico: null,
    tablaDetalle: null,
    graficoDetalle: null,

    initialize: function() {
        this.initializeTables();
        this.initializeEventListeners();
        this.actualizarTipoAnalisis();
        this.initializeYearSelectors();
    },

    initializeTables: function() {
        this.tablaFuturo = TableUtils.initializeTable('#tabla-proyecciones', {
            columns: [
                TableUtils.createTextColumn('producto'),
                TableUtils.createMoneyColumn('ventas_actuales'),
                TableUtils.createMoneyColumn('proyeccion_3m'),
                TableUtils.createMoneyColumn('proyeccion_6m'),
                TableUtils.createMoneyColumn('proyeccion_12m'),
                {
                    data: 'cod_producto',
                    className: 'text-center',
                    render: function(data, type) {
                        if (type === 'display') {
                            return `<button class="btn btn-sm btn-info ver-detalle-proyeccion" data-id="${data}">
                                <i class="fas fa-chart-line"></i>
                            </button>`;
                        }
                        return data;
                    }
                }
            ]
        }, [], 'tabla proyecciones');

        this.tablaHistorico = TableUtils.initializeTable('#tabla-precision', {
            columns: [
                TableUtils.createTextColumn('producto'),
                TableUtils.createPercentageColumn('precision_promedio'),
                TableUtils.createPercentageColumn('mejor_precision'),
                TableUtils.createPercentageColumn('peor_precision'),
                {
                    data: 'cod_producto',
                    className: 'text-center',
                    render: function(data, type) {
                        if (type === 'display') {
                            return `<button class="btn btn-sm btn-info ver-detalle-proyeccion" data-id="${data}">
                                <i class="fas fa-chart-line"></i>
                            </button>`;
                        }
                        return data;
                    }
                }
            ]
        }, [], 'tabla precision');

        this.tablaDetalle = TableUtils.initializeTable('#tabla-detalle-proyeccion', {
            columns: [
                TableUtils.createTextColumn('fecha'),
                TableUtils.createMoneyColumn('valor_proyectado'),
                TableUtils.createMoneyColumn('valor_real'),
                TableUtils.createPercentageColumn('precision')
            ]
        });
    },

    initializeEventListeners: function() {
        $('#tipo-analisis').on('change', () => {
            this.actualizarTipoAnalisis();
        });
        
        $('#periodo-proyeccion').on('change', () => {
            if ($('#tipo-analisis').val() === 'futuro') {
                this.initializeGrafico();
            }
        });

        $('#tabla-proyecciones, #tabla-precision').on('click', '.ver-detalle-proyeccion', (e) => {
            const codProducto = $(e.currentTarget).data('id');
            this.mostrarDetalle(codProducto);
        });

        $('#periodo-detalle-proyeccion').on('change', () => {
            const codProducto = $('#modal-detalle-proyeccion').data('cod-producto');
            if (codProducto) {
                this.actualizarDatosDetalle(codProducto);
            }
        });

        $('#mes-inicio-detalle-proyeccion, #año-inicio-detalle-proyeccion, #mes-fin-detalle-proyeccion, #año-fin-detalle-proyeccion').on('change', () => {
            if (this.validarRangoFechas()) {
                const codProducto = $('#modal-detalle-proyeccion').data('cod-producto');
                this.actualizarDatosDetalle(codProducto);
            }
        });

        $('#exportar-proyeccion-pdf').on('click', () => {
            this.exportarPDF();
        });

        // evento para exportar PDF general
        $('#exportar-proyecciones-general-pdf').on('click', () => {
            this.exportarReporteGeneral();
        });
    },

    initializeYearSelectors: function() {
        const fechaActual = new Date();
        const añoActual = fechaActual.getFullYear();
        
        const $yearSelectors = $('#año-inicio-detalle-proyeccion, #año-fin-detalle-proyeccion');
        $yearSelectors.empty();
        
        for (let año = añoActual - 1; año <= añoActual + 1; año++) {
            $yearSelectors.append(new Option(año, año));
        }
        
        $yearSelectors.val(añoActual);
        
        const mesActual = fechaActual.getMonth() + 1;
        let mesInicio = mesActual - 5;
        let añoInicio = añoActual;
        
        if (mesInicio <= 0) {
            mesInicio += 12;
            añoInicio--;
            $('#año-inicio-detalle-proyeccion').val(añoInicio);
        }
        
        $('#mes-inicio-detalle-proyeccion').val(mesInicio);
        $('#mes-fin-detalle-proyeccion').val(mesActual);
    },

    validarRangoFechas: function() {
        const mesInicio = parseInt($('#mes-inicio-detalle-proyeccion').val());
        const añoInicio = parseInt($('#año-inicio-detalle-proyeccion').val());
        const mesFin = parseInt($('#mes-fin-detalle-proyeccion').val());
        const añoFin = parseInt($('#año-fin-detalle-proyeccion').val());

        const fechaInicio = new Date(añoInicio, mesInicio - 1);
        const fechaFin = new Date(añoFin, mesFin - 1);

        if (fechaFin < fechaInicio) {
            Swal.fire({
                icon: 'error',
                title: 'Error en el rango de fechas',
                text: 'La fecha de inicio no puede ser posterior a la fecha final',
                confirmButtonText: 'Entendido'
            });
            return false;
        }
        return true;
    },

    actualizarTipoAnalisis: function() {
        const tipoAnalisis = $('#tipo-analisis').val();
        const $periodoSelect = $('#periodo-proyeccion');
        const $tablaProyecciones = $('#tabla-proyecciones').closest('.table-responsive');
        const $tablaPrecision = $('#tabla-precision').closest('.table-responsive');

        if (tipoAnalisis === 'futuro') {
            $tablaProyecciones.show();
            $tablaPrecision.hide();
            $periodoSelect.prop('disabled', false);
            $periodoSelect.html(`
                <option value="3">Próximos 3 meses</option>
                <option value="6">Próximos 6 meses</option>
                <option value="12">Próximo año</option>
            `);
            if (window.datosFinanzas?.proyecciones) {
                TableUtils.updateTable(this.tablaFuturo, window.datosFinanzas.proyecciones, 'tabla proyecciones');
            }
        } else {
            $tablaProyecciones.hide();
            $tablaPrecision.show();
            $periodoSelect.prop('disabled', true);
            $periodoSelect.html(`<option value="6">Últimos 6 meses</option>`);
            if (window.datosFinanzas?.proyecciones_historicas) {
                TableUtils.updateTable(this.tablaHistorico, window.datosFinanzas.proyecciones_historicas, 'tabla precision');
            }
        }
        this.initializeGrafico();
    },

    mostrarDetalle: function(codProducto) {
        const tipoAnalisis = $('#tipo-analisis').val();
        const modalTitle = tipoAnalisis === 'futuro' ? 'Proyecciones Futuras' : 'Precisión Histórica';
        $('#modal-detalle-proyeccion-label').text(`${modalTitle} - Detalle`);

        if ($.fn.DataTable.isDataTable('#tabla-detalle-proyeccion')) {
            $('#tabla-detalle-proyeccion').DataTable().destroy();
            $('#tabla-detalle-proyeccion').empty();
        }

        const tableHeaders = tipoAnalisis === 'futuro' ? `
            <tr>
                <th>Fecha</th>
                <th class="text-end">Valor Proyectado</th>
                <th class="text-center">Tendencia</th>
            </tr>
        ` : `
            <tr>
                <th>Fecha</th>
                <th class="text-end">Valor Proyectado</th>
                <th class="text-end">Valor Real</th>
                <th class="text-end">Precisión</th>
            </tr>
        `;
        $('#tabla-detalle-proyeccion thead').html(tableHeaders);

        $('#modal-detalle-proyeccion')
            .data('cod-producto', codProducto)
            .data('tipo-analisis', tipoAnalisis);

        if (tipoAnalisis === 'futuro') {
            $('#periodo-detalle-proyeccion')
                .prop('disabled', false)
                .html(`
                    <option value="3">Próximos 3 meses</option>
                    <option value="6" selected>Próximos 6 meses</option>
                    <option value="12">Próximo año</option>
                `);
        } else {
            $('#periodo-detalle-proyeccion')
                .prop('disabled', true)
                .html(`<option value="6">Últimos 6 meses</option>`);
        }

        $('#modal-detalle-proyeccion').modal('show');
        this.actualizarDatosDetalle(codProducto);
    },

    actualizarDatosDetalle: function(codProducto) {
        const tipoAnalisis = $('#modal-detalle-proyeccion').data('tipo-analisis');
        const periodo = parseInt($('#periodo-detalle-proyeccion').val()) || 6;

        let params = {
            accion: 'obtener_detalle_producto',
            cod_producto: codProducto,
            tipo: tipoAnalisis
        };

        if (tipoAnalisis === 'futuro') {
            params.periodo = periodo;
        } else {
            params.mes_inicio = $('#mes-inicio-detalle-proyeccion').val();
            params.año_inicio = $('#año-inicio-detalle-proyeccion').val();
            params.mes_fin = $('#mes-fin-detalle-proyeccion').val();
            params.año_fin = $('#año-fin-detalle-proyeccion').val();
        }

        $.ajax({
            url: 'index.php?pagina=finanzas',
            method: 'POST',
            data: params,
            success: (response) => {
                if (response.success) {
                    this.actualizarGraficoDetalle(response.datos, tipoAnalisis);
                    this.actualizarTablaDetalle(response.datos, tipoAnalisis);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Error al obtener el detalle de proyección'
                    });
                }
            },
            error: (xhr, status, error) => {
                console.error('Error al obtener detalle de proyección:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al obtener el detalle de proyección'
                });
            }
        });
    },

    actualizarGraficoDetalle: function(datos, tipoAnalisis) {
        if (this.graficoDetalle) {
            UtilidadesGraficos.destruirGrafico(this.graficoDetalle);
        }

        const ctx = document.getElementById('grafico-detalle-proyeccion');
        if (!ctx) return;

        if (tipoAnalisis === 'futuro') {
            console.group('Detalle Proyección - Preparación de Datos');
            console.log('Datos recibidos:', datos);
            console.log('Datos históricos:', datos.historico);
            console.log('Datos proyecciones:', datos.proyecciones);

            const allLabels = [
                ...datos.historico.map(item => item.mes),
                ...datos.proyecciones.map(item => item.mes)
            ];
            console.log('Labels combinados:', allLabels);

            const lastHistoricalValue = parseFloat(datos.historico[datos.historico.length - 1].ventas_totales);

            const historicValues = [
                ...datos.historico.map(item => parseFloat(item.ventas_totales)),
                ...Array(datos.proyecciones.length).fill(null)
            ];
            console.log('Valores históricos:', historicValues);

            const projectionValues = [
                ...Array(datos.historico.length - 1).fill(null),
                lastHistoricalValue,
                ...datos.proyecciones.map(item => parseFloat(item.valor_proyectado))
            ];
            console.log('Valores proyecciones:', projectionValues);
            console.groupEnd();

            // determinar tipo de gráfico basado en cantidad de datos
            const tipoGrafico = allLabels.length === 1 ? 'bar' : 'line';
            const esBarras = tipoGrafico === 'bar';

            // configurar datasets con colores estandarizados
            const datasets = [
                        {
                            label: 'Ventas Históricas',
                            data: historicValues,
                    borderColor: 'rgba(94, 193, 211, 1)', // teal
                    backgroundColor: esBarras ? 'rgba(94, 193, 211, 0.8)' : 'rgba(94, 193, 211, 0.2)',
                    borderWidth: esBarras ? 2 : 1,
                    ...(esBarras ? {} : {
                            tension: 0.4,
                            fill: true,
                            spanGaps: false
                    })
                        },
                        {
                            label: 'Proyecciones',
                            data: projectionValues,
                    borderColor: 'rgba(82, 113, 255, 1)', // azul
                    backgroundColor: esBarras ? 'rgba(82, 113, 255, 0.8)' : 'rgba(82, 113, 255, 0.2)',
                    borderWidth: esBarras ? 2 : 1,
                    ...(esBarras ? {} : {
                            borderDash: [5, 5],
                            tension: 0.4,
                            fill: true,
                            spanGaps: true
                    })
                        }
            ];

            this.graficoDetalle = new Chart(ctx, {
                type: tipoGrafico,
                data: {
                    labels: allLabels,
                    datasets: datasets
                },
                options: {
                    ...UtilidadesGraficos.obtenerOpciones('Proyecciones del Producto'),
                    maintainAspectRatio: false,
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Ventas (USD)'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Período'
                            },
                            ...(esBarras ? {
                                grid: { display: true, color: 'rgba(0, 0, 0, 0.1)' }
                            } : {})
                        }
                    },
                    ...(esBarras ? {
                        categoryPercentage: 0.8,
                        barPercentage: 0.9
                    } : {})
                }
            });
        } else {
            // determinar tipo de gráfico basado en cantidad de datos
            const tipoGraficoHistorico = datos.labels.length === 1 ? 'bar' : 'line';
            const esBarrasHistorico = tipoGraficoHistorico === 'bar';

            // configurar datasets con colores estandarizados
            const datasetsHistorico = [
                        {
                            label: 'Valor Real',
                            data: datos.real,
                    borderColor: 'rgba(94, 193, 211, 1)', // teal
                    backgroundColor: esBarrasHistorico ? 'rgba(94, 193, 211, 0.8)' : 'rgba(94, 193, 211, 0.2)',
                    borderWidth: esBarrasHistorico ? 2 : 1,
                    ...(esBarrasHistorico ? {} : {
                            tension: 0.4,
                            fill: true
                    })
                        },
                        {
                            label: 'Valor Proyectado',
                            data: datos.proyectado,
                    borderColor: 'rgba(82, 113, 255, 1)', // azul
                    backgroundColor: esBarrasHistorico ? 'rgba(82, 113, 255, 0.8)' : 'rgba(82, 113, 255, 0.2)',
                    borderWidth: esBarrasHistorico ? 2 : 1,
                    ...(esBarrasHistorico ? {} : {
                            borderDash: [5, 5],
                            tension: 0.4,
                            fill: true
                    })
                        }
            ];

            this.graficoDetalle = new Chart(ctx, {
                type: tipoGraficoHistorico,
                data: {
                    labels: datos.labels,
                    datasets: datasetsHistorico
                },
                options: {
                    ...UtilidadesGraficos.obtenerOpciones('Análisis Histórico de Proyecciones'),
                    maintainAspectRatio: false,
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Ventas (USD)'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Período'
                            },
                            ...(esBarrasHistorico ? {
                                grid: { display: true, color: 'rgba(0, 0, 0, 0.1)' }
                            } : {})
                        }
                    },
                    ...(esBarrasHistorico ? {
                        categoryPercentage: 0.8,
                        barPercentage: 0.9
                    } : {})
                }
            });
        }
    },

    actualizarTablaDetalle: function(datos, tipoAnalisis) {
        let columns, detalleData;
        
        console.group('Actualizando Tabla Detalle');
        console.log('Tipo de análisis:', tipoAnalisis);
        console.log('Datos recibidos:', datos);
        
        if (tipoAnalisis === 'futuro') {
            columns = [
                { ...TableUtils.createTextColumn('fecha'), title: 'Fecha' },
                { ...TableUtils.createMoneyColumn('valor_proyectado'), title: 'Valor Proyectado' },
                {
                    data: 'tendencia',
                    className: 'text-center',
                    title: 'Tendencia',
                    render: function(data) {
                        if (data === null) return '';
                        const icon = data === 'up' ? 
                            '<i class="fas fa-arrow-up text-success"></i>' : 
                            '<i class="fas fa-arrow-down text-danger"></i>';
                        return icon;
                    }
                }
            ];

            detalleData = datos.proyecciones.map((item, index) => {
                const valorProyectado = parseFloat(item.valor_proyectado);
                const prevProyectado = index > 0 ? parseFloat(datos.proyecciones[index - 1].valor_proyectado) : valorProyectado;
                
                return {
                    fecha: item.mes,
                    valor_proyectado: valorProyectado,
                    tendencia: valorProyectado >= prevProyectado ? 'up' : 'down'
                };
            });
        } else {
            columns = [
                { ...TableUtils.createTextColumn('fecha'), title: 'Fecha' },
                { ...TableUtils.createMoneyColumn('valor_proyectado'), title: 'Valor Proyectado' },
                { ...TableUtils.createMoneyColumn('valor_real'), title: 'Valor Real' },
                { ...TableUtils.createPercentageColumn('precision'), title: 'Precisión' }
            ];

            detalleData = datos.labels.map((fecha, index) => ({
                fecha: fecha,
                valor_proyectado: parseFloat(datos.proyectado[index] || 0),
                valor_real: parseFloat(datos.real[index] || 0),
                precision: parseFloat(datos.precision[index] || 0)
            }));
        }

        console.log('Datos procesados para la tabla:', detalleData);
        console.groupEnd();

        const tableElement = $('#tabla-detalle-proyeccion');
        if ($.fn.DataTable.isDataTable(tableElement)) {
            const existingTable = tableElement.DataTable();
            existingTable.clear();
            existingTable.rows.add(detalleData);
            existingTable.draw();
        } else {
            this.tablaDetalle = TableUtils.initializeTable('#tabla-detalle-proyeccion', {
                columns: columns,
                data: detalleData,
                order: [[0, 'asc']]
            });
        }
    },

    initializeGrafico: function() {
        const ctx = document.getElementById('grafico-proyecciones');
        if (!ctx || !window.datosFinanzas?.datos_grafico_proyecciones) return;
        if (this.grafico) {
            UtilidadesGraficos.destruirGrafico(this.grafico);
        }
        const tipoAnalisis = $('#tipo-analisis').val();
        if (tipoAnalisis === 'historico') {
            const historicas = window.datosFinanzas.datos_grafico_proyecciones.historicas;
            if (!historicas) return;

            this.grafico = UtilidadesGraficos.inicializarGraficoProyecciones(
                ctx,
                { labels: historicas.labels, valores: historicas.reales },
                { labels: historicas.labels, valores: historicas.valores }
            );
        } else {
            const historico = window.datosFinanzas.datos_grafico_proyecciones.historico;
            const proyecciones = window.datosFinanzas.datos_grafico_proyecciones.proyecciones;
            const periodoSeleccionado = parseInt($('#periodo-proyeccion').val()) || 6;
            const proyeccionesFiltradas = {
                labels: proyecciones.labels.slice(0, periodoSeleccionado),
                valores: proyecciones.valores.slice(0, periodoSeleccionado)
            };
            this.grafico = UtilidadesGraficos.inicializarGraficoProyecciones(
                ctx,
                historico,
                proyeccionesFiltradas
            );
        }
    },

    generarEstadisticasProyecciones: function() {
        const stats = [];
        let totalProyecciones = 0;
        let crecimientoPositivo = 0;
        let valorMaximo = 0;
        let valorMinimo = Number.MAX_VALUE;
        let sumaValores = 0;
        
        if (this.tablaDetalle && this.tablaDetalle.rows) {
            this.tablaDetalle.rows().every(function(rowIdx) {
                const row = this.data();
                totalProyecciones++;
                
                const valor = parseFloat(row.valor_proyectado) || 0;
                sumaValores += valor;
                
                if (row.tendencia === 'up') crecimientoPositivo++;
                valorMaximo = Math.max(valorMaximo, valor);
                valorMinimo = Math.min(valorMinimo, valor);
            });
        }
        
        if (totalProyecciones > 0) {
            const promedio = sumaValores / totalProyecciones;
            const porcentajeCrecimiento = ((crecimientoPositivo / totalProyecciones) * 100);
            
            stats.push({
                title: 'Total de Proyecciones',
                value: `${totalProyecciones} períodos`
            });
            
            stats.push({
                title: 'Tendencia de Crecimiento',
                value: `${porcentajeCrecimiento.toFixed(1)}% de los períodos (${crecimientoPositivo} de ${totalProyecciones})`
            });
            
            stats.push({
                title: 'Valor Promedio Proyectado',
                value: formatearMoneda(promedio)
            });
            
            stats.push({
                title: 'Valor Máximo Proyectado',
                value: formatearMoneda(valorMaximo)
            });
            
            stats.push({
                title: 'Valor Mínimo Proyectado',
                value: formatearMoneda(valorMinimo)
            });
            
            const rango = valorMaximo - valorMinimo;
            stats.push({
                title: 'Rango de Variación',
                value: formatearMoneda(rango)
            });
        }
        
        return stats;
    },

    generarEstadisticasProyeccionesHistoricas: function() {
        const stats = [];
        let totalProyecciones = 0;
        let precisionTotal = 0;
        let mejorPrecision = 0;
        let peorPrecision = 100;
        let proyeccionesAcertadas = 0; // con precisión >= 80%
        let totalValorProyectado = 0;
        let totalValorReal = 0;
        
        if (this.tablaDetalle && this.tablaDetalle.rows) {
            this.tablaDetalle.rows().every(function(rowIdx) {
                const row = this.data();
                totalProyecciones++;
                
                const precision = parseFloat(row.precision) || 0;
                const valorProyectado = parseFloat(row.valor_proyectado) || 0;
                const valorReal = parseFloat(row.valor_real) || 0;
                
                precisionTotal += precision;
                totalValorProyectado += valorProyectado;
                totalValorReal += valorReal;
                
                if (precision >= 80) proyeccionesAcertadas++;
                mejorPrecision = Math.max(mejorPrecision, precision);
                peorPrecision = Math.min(peorPrecision, precision);
            });
        }
        
        if (totalProyecciones > 0) {
            const precisionPromedio = precisionTotal / totalProyecciones;
            const porcentajeAcertadas = (proyeccionesAcertadas / totalProyecciones) * 100;
            const desviacionPromedio = totalValorReal > 0 ? 
                Math.abs(totalValorProyectado - totalValorReal) / totalValorReal * 100 : 0;
            
            stats.push({
                title: 'Total de Proyecciones Analizadas',
                value: `${totalProyecciones} períodos`
            });
            
            stats.push({
                title: 'Precisión Promedio',
                value: formatearPorcentaje(precisionPromedio)
            });
            
            stats.push({
                title: 'Mejor Precisión Alcanzada',
                value: formatearPorcentaje(mejorPrecision)
            });
            
            stats.push({
                title: 'Peor Precisión Registrada',
                value: formatearPorcentaje(peorPrecision)
            });
            
            stats.push({
                title: 'Proyecciones Acertadas (80% o más)',
                value: `${proyeccionesAcertadas} de ${totalProyecciones} proyecciones (${porcentajeAcertadas.toFixed(1)}%)`
            });
            
            stats.push({
                title: 'Total Proyectado Acumulado',
                value: formatearMoneda(totalValorProyectado)
            });
            
            stats.push({
                title: 'Total Real Acumulado',
                value: formatearMoneda(totalValorReal)
            });
            
            stats.push({
                title: 'Desviación Promedio',
                value: `${desviacionPromedio.toFixed(1)}%`
            });
        }
        
        return stats;
    },

    exportarPDF: function() {
        const codProducto = $('#modal-detalle-proyeccion').data('cod-producto');
        const tipoAnalisis = $('#modal-detalle-proyeccion').data('tipo-analisis');
        
        // obtener nombre del producto
        let nombreProducto = 'Producto';
        if (tipoAnalisis === 'futuro') {
            const productos = window.datosFinanzas?.proyecciones || [];
            const producto = productos.find(p => p.cod_producto === codProducto);
            if (producto) {
                nombreProducto = producto.producto;
            }
        } else {
            const productos = window.datosFinanzas?.proyecciones_historicas || [];
            const producto = productos.find(p => p.cod_producto === codProducto);
            if (producto) {
                nombreProducto = producto.producto;
            }
        }
        
        if (tipoAnalisis === 'futuro') {
            const periodo = $('#periodo-detalle-proyeccion option:selected').text();
            
            // obtener datos de la tabla
            const tableData = this.tablaDetalle ? this.tablaDetalle.rows().data().toArray() : [];
            
            ExportarPDFs.exportToPDF('proyecciones', {
                producto: nombreProducto,
                periodo: periodo,
                sourceCanvasId: 'grafico-detalle-proyeccion',
                tableData: tableData,
                stats: this.generarEstadisticasProyecciones()
            }).catch(error => {
                console.error('Error en exportación:', error);
            });
        } else {
            // análisis histórico - siempre son los últimos 6 meses
            const periodo = 'Últimos 6 meses';
            
            // obtener datos de la tabla
            const tableData = this.tablaDetalle ? this.tablaDetalle.rows().data().toArray() : [];
            
            ExportarPDFs.exportToPDF('proyecciones_historicas', {
                producto: nombreProducto,
                periodo: periodo,
                sourceCanvasId: 'grafico-detalle-proyeccion',
                tableData: tableData,
                stats: this.generarEstadisticasProyeccionesHistoricas()
            }).catch(error => {
                console.error('Error en exportación:', error);
            });
        }
    },

    exportarReporteGeneral: function() {
        const tipoAnalisis = $('#tipo-analisis').val();
        
        // verificar que hay gráfico disponible
        if (!this.grafico || !this.grafico.data || !this.grafico.data.labels.length) {
            Swal.fire({
                icon: 'warning',
                title: 'Sin datos',
                text: 'No hay datos disponibles en el gráfico para generar el reporte'
            });
            return;
        }

        // verificar que hay tabla correspondiente con datos
        const tablaActiva = tipoAnalisis === 'futuro' ? this.tablaFuturo : this.tablaHistorico;
        if (!tablaActiva || tablaActiva.data().count() === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Sin datos',
                text: 'No hay datos disponibles en la tabla para generar el reporte'
            });
            return;
        }

        // obtener datos de la tabla activa
        const tableData = tablaActiva.rows().data().toArray();
        
        // construir título y período según el tipo
        let titulo, periodo, tipoReporte;
        
        if (tipoAnalisis === 'futuro') {
            titulo = 'Proyecciones Futuras';
            periodo = $('#periodo-proyeccion option:selected').text();
            tipoReporte = 'proyecciones_general';
        } else {
            titulo = 'Análisis de Precisión Histórica';
            periodo = 'Últimos 6 meses';
            tipoReporte = 'proyecciones_historicas_general';
        }

        ExportarPDFs.exportToPDF(tipoReporte, {
            titulo: titulo,
            periodo: periodo,
            tipoAnalisis: tipoAnalisis,
            sourceCanvasId: 'grafico-proyecciones',
            tableData: tableData,
            stats: tipoAnalisis === 'futuro' ? 
                this.generarEstadisticasProyeccionesGeneral(tableData) :
                this.generarEstadisticasProyeccionesHistoricasGeneral(tableData)
        }).catch(error => {
            console.error('Error en exportación:', error);
        });
    },

    generarEstadisticasProyeccionesGeneral: function(tableData) {
        const stats = [];
        let totalProductos = tableData.length;
        let totalVentasActuales = 0;
        let totalProyeccion3M = 0;
        let totalProyeccion6M = 0;
        let totalProyeccion12M = 0;
        let ventasPromedio = 0;
        let productosConCrecimiento3M = 0;
        let productosConCrecimiento6M = 0;
        let productosConCrecimiento12M = 0;
        
        tableData.forEach(row => {
            const ventasActuales = parseFloat(row.ventas_actuales) || 0;
            const proy3M = parseFloat(row.proyeccion_3m) || 0;
            const proy6M = parseFloat(row.proyeccion_6m) || 0;
            const proy12M = parseFloat(row.proyeccion_12m) || 0;
            
            totalVentasActuales += ventasActuales;
            totalProyeccion3M += proy3M;
            totalProyeccion6M += proy6M;
            totalProyeccion12M += proy12M;
            
            // analizar crecimiento
            if (proy3M > ventasActuales) productosConCrecimiento3M++;
            if (proy6M > ventasActuales) productosConCrecimiento6M++;
            if (proy12M > ventasActuales) productosConCrecimiento12M++;
        });
        
        if (totalProductos > 0) {
            ventasPromedio = totalVentasActuales / totalProductos;
            const porcentajeCrecimiento3M = ((totalProyeccion3M - totalVentasActuales) / totalVentasActuales) * 100;
            const porcentajeCrecimiento6M = ((totalProyeccion6M - totalVentasActuales) / totalVentasActuales) * 100;
            const porcentajeCrecimiento12M = ((totalProyeccion12M - totalVentasActuales) / totalVentasActuales) * 100;
            
            stats.push({
                title: 'Total de Productos Proyectados',
                value: `${totalProductos} productos`
            });
            
            stats.push({
                title: 'Ventas Consolidadas Últimos 6 Meses',
                value: formatearMoneda(totalVentasActuales)
            });
            
            stats.push({
                title: 'Proyección Consolidada 3 Meses',
                value: `${formatearMoneda(totalProyeccion3M)} (${porcentajeCrecimiento3M >= 0 ? '+' : ''}${porcentajeCrecimiento3M.toFixed(1)}%)`
            });
            
            stats.push({
                title: 'Proyección Consolidada 6 Meses',
                value: `${formatearMoneda(totalProyeccion6M)} (${porcentajeCrecimiento6M >= 0 ? '+' : ''}${porcentajeCrecimiento6M.toFixed(1)}%)`
            });
            
            stats.push({
                title: 'Proyección Consolidada 12 Meses',
                value: `${formatearMoneda(totalProyeccion12M)} (${porcentajeCrecimiento12M >= 0 ? '+' : ''}${porcentajeCrecimiento12M.toFixed(1)}%)`
            });
            
            stats.push({
                title: 'Ventas Promedio por Producto',
                value: formatearMoneda(ventasPromedio)
            });
            
            stats.push({
                title: 'Productos con Crecimiento Esperado (3M)',
                value: `${productosConCrecimiento3M} de ${totalProductos} productos (${((productosConCrecimiento3M/totalProductos)*100).toFixed(1)}%)`
            });
            
            stats.push({
                title: 'Productos con Crecimiento Esperado (6M)',
                value: `${productosConCrecimiento6M} de ${totalProductos} productos (${((productosConCrecimiento6M/totalProductos)*100).toFixed(1)}%)`
            });
            
            stats.push({
                title: 'Productos con Crecimiento Esperado (12M)',
                value: `${productosConCrecimiento12M} de ${totalProductos} productos (${((productosConCrecimiento12M/totalProductos)*100).toFixed(1)}%)`
            });
        }
        
        return stats;
    },

    generarEstadisticasProyeccionesHistoricasGeneral: function(tableData) {
        const stats = [];
        let totalProductos = tableData.length;
        let precisionPromedio = 0;
        let mejorPrecisionGlobal = 0;
        let peorPrecisionGlobal = 100;
        let productosConBuenaPrecision = 0; // >= 80%
        let productosConPrecisionRegular = 0; // 60-80%
        let productosConPrecisionBaja = 0; // < 60%
        
        tableData.forEach(row => {
            const precision = parseFloat(row.precision_promedio) || 0;
            const mejorPrecision = parseFloat(row.mejor_precision) || 0;
            const peorPrecision = parseFloat(row.peor_precision) || 0;
            
            precisionPromedio += precision;
            mejorPrecisionGlobal = Math.max(mejorPrecisionGlobal, mejorPrecision);
            peorPrecisionGlobal = Math.min(peorPrecisionGlobal, peorPrecision);
            
            // clasificar por precisión
            if (precision >= 80) {
                productosConBuenaPrecision++;
            } else if (precision >= 60) {
                productosConPrecisionRegular++;
            } else {
                productosConPrecisionBaja++;
            }
        });
        
        if (totalProductos > 0) {
            precisionPromedio = precisionPromedio / totalProductos;
            
            stats.push({
                title: 'Total de Productos Analizados',
                value: `${totalProductos} productos`
            });
            
            stats.push({
                title: 'Precisión Promedio Global',
                value: formatearPorcentaje(precisionPromedio)
            });
            
            stats.push({
                title: 'Mejor Precisión Registrada',
                value: formatearPorcentaje(mejorPrecisionGlobal)
            });
            
            stats.push({
                title: 'Peor Precisión Registrada',
                value: formatearPorcentaje(peorPrecisionGlobal)
            });
            
            stats.push({
                title: 'Productos con Buena Precisión (80% o más)',
                value: `${productosConBuenaPrecision} productos (${((productosConBuenaPrecision/totalProductos)*100).toFixed(1)}%)`
            });
            
            stats.push({
                title: 'Productos con Precisión Regular (60% a 80%)',
                value: `${productosConPrecisionRegular} productos (${((productosConPrecisionRegular/totalProductos)*100).toFixed(1)}%)`
            });
            
            stats.push({
                title: 'Productos con Precisión Baja (menos de 60%)',
                value: `${productosConPrecisionBaja} productos (${((productosConPrecisionBaja/totalProductos)*100).toFixed(1)}%)`
            });
            
            // análisis de confiabilidad
            const productosConfiables = productosConBuenaPrecision;
            const porcentajeConfiabilidad = (productosConfiables / totalProductos) * 100;
            
            stats.push({
                title: 'Índice de Confiabilidad del Sistema',
                value: `${porcentajeConfiabilidad.toFixed(1)}% (${productosConfiables} de ${totalProductos} productos con precisión 80% o más)`
            });
            
            // recomendación
            let recomendacion = '';
            if (porcentajeConfiabilidad >= 80) {
                recomendacion = 'Excelente - Sistema de proyecciones muy confiable';
            } else if (porcentajeConfiabilidad >= 60) {
                recomendacion = 'Bueno - Sistema de proyecciones confiable con margen de mejora';
            } else if (porcentajeConfiabilidad >= 40) {
                recomendacion = 'Regular - Se recomienda revisar metodología de proyección';
            } else {
                recomendacion = 'Bajo - Se requiere mejora significativa en el sistema de proyecciones';
            }
            
            stats.push({
                title: 'Evaluación del Sistema',
                value: recomendacion
            });
        }
        
        return stats;
    }
}; 