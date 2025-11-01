const RentabilidadTab = {
    tabla: null,
    graficoDetalle: null,
    tablaDetalle: null,

    initialize: function() {
        this.initializeTable();
        this.initializePeriodSelectors();
        this.initializeEventListeners();
        this.actualizarDatos();
    },

    initializeTable: function() {
        console.group('Initializing Rentabilidad Table');
        
        this.tabla = TableUtils.initializeTable('#tabla-rentabilidad', {
            columns: [
                TableUtils.createTextColumn('producto'),
                TableUtils.createMoneyColumn('ventas_totales', 'text-end'),
                TableUtils.createMoneyColumn('costo_ventas', 'text-end'),
                TableUtils.createMoneyColumn('margen_bruto', 'text-end'),
                TableUtils.createPercentageColumn('rentabilidad', 'text-end'),
                {
                    data: 'cod_producto',
                    className: 'text-center',
                    render: function(data, type) {
                        if (type === 'display') {
                            if (window.permisos?.finanza?.consultar) {
                                return `<button class="btn btn-sm btn-info ver-detalle-rentabilidad" data-id="${data}">
                                    <i class="fas fa-chart-line"></i>
                                </button>`;
                            }
                            return '';
                        }
                        return data;
                    }
                }
            ]
        });

        console.groupEnd();
    },

    actualizarDatos: function() {
        const mesInicio = $('#mes-inicio-rentabilidad').val();
        const añoInicio = $('#año-inicio-rentabilidad').val();
        const mesFin = $('#mes-fin-rentabilidad').val();
        const añoFin = $('#año-fin-rentabilidad').val();

        $.ajax({
            url: 'index.php?pagina=finanzas',
            method: 'POST',
            data: {
                accion: 'obtener_rentabilidad',
                mes_inicio: mesInicio,
                año_inicio: añoInicio,
                mes_fin: mesFin,
                año_fin: añoFin
            },
            success: (response) => {
                if (response.success) {
                    this.tabla.clear();
                    this.tabla.rows.add(response.rentabilidad);
                    this.tabla.draw();

                    $('#rentabilidad-promedio').text(formatearPorcentaje(response.metricas.rentabilidad_promedio));
                    $('#margen-bruto-total').text(formatearMoneda(response.metricas.margen_bruto_total));
                }
            },
            error: (xhr, status, error) => {
                console.error('Error al obtener datos de rentabilidad:', error);
            }
        });
    },

    validarRangoFechas: function() {
        const mesInicio = parseInt($('#mes-inicio-rentabilidad').val());
        const añoInicio = parseInt($('#año-inicio-rentabilidad').val());
        const mesFin = parseInt($('#mes-fin-rentabilidad').val());
        const añoFin = parseInt($('#año-fin-rentabilidad').val());

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

    initializePeriodSelectors: function() {
        const fechaActual = new Date();
        const añoActual = fechaActual.getFullYear();
        
        const $yearSelectors = $('#año-inicio-rentabilidad, #año-fin-rentabilidad, #año-inicio-detalle-rentabilidad, #año-fin-detalle-rentabilidad');
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
            $('#año-inicio-rentabilidad').val(añoInicio);
        }
        
        $('#mes-inicio-rentabilidad').val(mesInicio);
        $('#mes-fin-rentabilidad').val(mesActual);
    },

    initializeEventListeners: function() {
        $('#mes-inicio-rentabilidad, #año-inicio-rentabilidad, #mes-fin-rentabilidad, #año-fin-rentabilidad').on('change', () => {
            if (this.validarRangoFechas()) {
                this.actualizarDatos();
            }
        });

        $('#tabla-rentabilidad').on('click', '.ver-detalle-rentabilidad', (e) => {
            const codProducto = $(e.currentTarget).data('id');
            this.mostrarDetalle(codProducto);
        });

        $('#mes-inicio-detalle-rentabilidad, #año-inicio-detalle-rentabilidad, #mes-fin-detalle-rentabilidad, #año-fin-detalle-rentabilidad').on('change', () => {
            if (this.validarRangoFechasDetalle()) {
                const codProducto = $('#modal-detalle-rentabilidad').data('cod-producto');
                this.actualizarDatosDetalle(codProducto);
            }
        });

        $('#exportar-rentabilidad-pdf').on('click', () => {
            this.exportarPDF();
        });

        // evento para exportar PDF general
        $('#exportar-rentabilidad-general-pdf').on('click', () => {
            this.exportarReporteGeneral();
        });
    },

    mostrarDetalle: function(codProducto) {
        const mesInicio = $('#mes-inicio-rentabilidad').val();
        const añoInicio = $('#año-inicio-rentabilidad').val();
        const mesFin = $('#mes-fin-rentabilidad').val();
        const añoFin = $('#año-fin-rentabilidad').val();

        const fechaActual = new Date();
        const añoActual = fechaActual.getFullYear();
        const $yearDetailSelectors = $('#año-inicio-detalle-rentabilidad, #año-fin-detalle-rentabilidad');
        $yearDetailSelectors.empty();
        
        for (let año = añoActual - 1; año <= añoActual + 1; año++) {
            $yearDetailSelectors.append(new Option(año, año));
        }

        $('#mes-inicio-detalle-rentabilidad').val(mesInicio);
        $('#año-inicio-detalle-rentabilidad').val(añoInicio);
        $('#mes-fin-detalle-rentabilidad').val(mesFin);
        $('#año-fin-detalle-rentabilidad').val(añoFin);

        $('#modal-detalle-rentabilidad').data('cod-producto', codProducto);

        $('#modal-detalle-rentabilidad').modal('show');

        this.actualizarDatosDetalle(codProducto);
    },

    validarRangoFechasDetalle: function() {
        const mesInicio = parseInt($('#mes-inicio-detalle-rentabilidad').val());
        const añoInicio = parseInt($('#año-inicio-detalle-rentabilidad').val());
        const mesFin = parseInt($('#mes-fin-detalle-rentabilidad').val());
        const añoFin = parseInt($('#año-fin-detalle-rentabilidad').val());

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

    actualizarDatosDetalle: function(codProducto) {
        const mesInicio = $('#mes-inicio-detalle-rentabilidad').val();
        const añoInicio = $('#año-inicio-detalle-rentabilidad').val();
        const mesFin = $('#mes-fin-detalle-rentabilidad').val();
        const añoFin = $('#año-fin-detalle-rentabilidad').val();

        console.log('Actualizando detalle con:', {
            codProducto,
            mesInicio,
            añoInicio,
            mesFin,
            añoFin
        });

        $.ajax({
            url: 'index.php?pagina=finanzas',
            method: 'POST',
            data: {
                accion: 'obtener_detalle_rentabilidad',
                cod_producto: codProducto,
                mes_inicio: mesInicio,
                año_inicio: añoInicio,
                mes_fin: mesFin,
                año_fin: añoFin
            },
            success: (response) => {
                if (response.success) {
                    console.log('Datos recibidos:', response.datos);
                    $('#modal-detalle-rentabilidad-label').text(`Detalle de Rentabilidad - ${response.datos.producto}`);
                    $('#detalle-rentabilidad-titulo').text(`Producto: ${response.datos.producto}`);
                    
                    this.actualizarGraficoDetalle(response.datos);
                    this.actualizarTablaDetalle(response.datos.detalle);
                } else {
                    console.error('Error en la respuesta:', response);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error al obtener el detalle de rentabilidad'
                    });
                }
            },
            error: (xhr, status, error) => {
                console.error('Error al obtener detalle de rentabilidad:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al obtener el detalle de rentabilidad'
                });
            }
        });
    },

    actualizarGraficoDetalle: function(datos) {
        if (this.graficoDetalle) {
            UtilidadesGraficos.destruirGrafico(this.graficoDetalle);
        }

        const ctx = document.getElementById('grafico-detalle-rentabilidad');
        if (!ctx) return;

        // determinar tipo de gráfico basado en cantidad de datos
        const tipoGrafico = datos.labels.length === 1 ? 'bar' : 'line';
        const esBarras = tipoGrafico === 'bar';

        // configurar datasets con colores estandarizados
        const datasets = [
                    {
                        label: 'Ventas Totales',
                        data: datos.ventas_totales,
                borderColor: 'rgba(94, 193, 211, 1)', // teal
                backgroundColor: esBarras ? 'rgba(94, 193, 211, 0.8)' : 'rgba(94, 193, 211, 0.2)',
                borderWidth: esBarras ? 2 : 1,
                ...(esBarras ? {} : {
                        tension: 0.4,
                        fill: true
                })
                    },
                    {
                        label: 'Costo de Ventas',
                        data: datos.costo_ventas,
                borderColor: 'rgba(82, 113, 255, 1)', // azul
                backgroundColor: esBarras ? 'rgba(82, 113, 255, 0.8)' : 'rgba(82, 113, 255, 0.2)',
                borderWidth: esBarras ? 2 : 1,
                ...(esBarras ? {} : {
                        tension: 0.4,
                        fill: true
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
                        text: `Evolución de Ventas y Costos - ${datos.producto}`
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
    },

    actualizarTablaDetalle: function(datos) {
        if (this.tablaDetalle) {
            this.tablaDetalle.destroy();
        }

        this.tablaDetalle = TableUtils.initializeTable('#tabla-detalle-rentabilidad', {
            data: datos,
            columns: [
                TableUtils.createTextColumn('fecha'),
                TableUtils.createMoneyColumn('ventas_totales'),
                TableUtils.createMoneyColumn('costo_ventas'),
                TableUtils.createMoneyColumn('margen_bruto'),
                TableUtils.createPercentageColumn('rentabilidad')
            ]
        });
    },

    generarEstadisticasRentabilidad: function() {
        const stats = [];
        let totalVentas = 0;
        let totalCostos = 0;
        let totalMargenBruto = 0;
        let mejorRentabilidad = 0;
        let peorRentabilidad = 100;
        let mesesPositivos = 0;
        let totalMeses = 0;
        
        if (this.tablaDetalle && this.tablaDetalle.rows) {
            this.tablaDetalle.rows().every(function(rowIdx) {
                const row = this.data();
                totalMeses++;
                
                const ventas = parseFloat(row.ventas_totales) || 0;
                const costos = parseFloat(row.costo_ventas) || 0;
                const margen = parseFloat(row.margen_bruto) || 0;
                const rentabilidad = parseFloat(row.rentabilidad) || 0;
                
                totalVentas += ventas;
                totalCostos += costos;
                totalMargenBruto += margen;
                
                if (rentabilidad > 0) mesesPositivos++;
                mejorRentabilidad = Math.max(mejorRentabilidad, rentabilidad);
                peorRentabilidad = Math.min(peorRentabilidad, rentabilidad);
            });
        }
        
        if (totalMeses > 0) {
            const rentabilidadPromedio = totalVentas > 0 ? ((totalMargenBruto / totalVentas) * 100) : 0;
            const porcentajeMesesPositivos = (mesesPositivos / totalMeses) * 100;
            
            stats.push({
                title: 'Total de Meses Analizados',
                value: `${totalMeses} períodos`
            });
            
            stats.push({
                title: 'Ventas Totales Acumuladas',
                value: formatearMoneda(totalVentas)
            });
            
            stats.push({
                title: 'Costos Totales Acumulados',
                value: formatearMoneda(totalCostos)
            });
            
            stats.push({
                title: 'Margen Bruto Total',
                value: formatearMoneda(totalMargenBruto)
            });
            
            stats.push({
                title: 'Rentabilidad Promedio',
                value: formatearPorcentaje(rentabilidadPromedio)
            });
            
            stats.push({
                title: 'Mejor Rentabilidad',
                value: formatearPorcentaje(mejorRentabilidad)
            });
            
            stats.push({
                title: 'Peor Rentabilidad',
                value: formatearPorcentaje(peorRentabilidad)
            });
            
            stats.push({
                title: 'Meses con Rentabilidad Positiva',
                value: `${porcentajeMesesPositivos.toFixed(1)}% (${mesesPositivos} de ${totalMeses})`
            });
        }
        
        return stats;
    },

    exportarPDF: function() {
        const codProducto = $('#modal-detalle-rentabilidad').data('cod-producto');
        
        if (!codProducto) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se pudo identificar el producto seleccionado'
            });
            return;
        }
        
        // obtener nombre del producto del título del modal
        const tituloModal = $('#modal-detalle-rentabilidad-label').text();
        const nombreProducto = tituloModal.replace('Detalle de Rentabilidad - ', '') || 'Producto';
        
        // construir período
        const mesInicio = $('#mes-inicio-detalle-rentabilidad option:selected').text();
        const añoInicio = $('#año-inicio-detalle-rentabilidad').val();
        const mesFin = $('#mes-fin-detalle-rentabilidad option:selected').text();
        const añoFin = $('#año-fin-detalle-rentabilidad').val();
        const periodo = `${mesInicio} ${añoInicio} - ${mesFin} ${añoFin}`;
        
        // obtener datos de la tabla
        const tableData = this.tablaDetalle ? this.tablaDetalle.rows().data().toArray() : [];
        
        ExportarPDFs.exportToPDF('rentabilidad', {
            producto: nombreProducto,
            periodo: periodo,
            sourceCanvasId: 'grafico-detalle-rentabilidad',
            tableData: tableData,
            stats: this.generarEstadisticasRentabilidad()
        }).catch(error => {
            console.error('Error en exportación:', error);
        });
    },

    exportarReporteGeneral: function() {
        // verificar si hay datos en la tabla
        if (!this.tabla || this.tabla.data().count() === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Sin datos',
                text: 'No hay datos de rentabilidad disponibles para generar el reporte'
            });
            return;
        }

        // obtener datos de la tabla
        const tableData = this.tabla.rows().data().toArray();
        
        // construir período
        const mesInicio = $('#mes-inicio-rentabilidad option:selected').text();
        const añoInicio = $('#año-inicio-rentabilidad').val();
        const mesFin = $('#mes-fin-rentabilidad option:selected').text();
        const añoFin = $('#año-fin-rentabilidad').val();
        const periodo = `${mesInicio} ${añoInicio} - ${mesFin} ${añoFin}`;

        // obtener métricas del DOM
        const rentabilidadPromedio = $('#rentabilidad-promedio').text();
        const margenBrutoTotal = $('#margen-bruto-total').text();

        ExportarPDFs.exportToPDF('rentabilidad_general', {
            periodo: periodo,
            tableData: tableData,
            metricas: {
                rentabilidad_promedio: rentabilidadPromedio,
                margen_bruto_total: margenBrutoTotal
            },
            stats: this.generarEstadisticasRentabilidadGeneral(tableData)
        }).catch(error => {
            console.error('Error en exportación:', error);
        });
    },

    generarEstadisticasRentabilidadGeneral: function(tableData) {
        const stats = [];
        let totalProductos = tableData.length;
        let totalVentas = 0;
        let totalCostos = 0;
        let totalMargenBruto = 0;
        let productosRentables = 0;
        let mejorRentabilidad = 0;
        let peorRentabilidad = 100;
        let productoMejorRentabilidad = '';
        let productoPeorRentabilidad = '';
        let ventasPromedio = 0;
        
        tableData.forEach(row => {
            const ventas = parseFloat(row.ventas_totales) || 0;
            const costos = parseFloat(row.costo_ventas) || 0;
            const margen = parseFloat(row.margen_bruto) || 0;
            const rentabilidad = parseFloat(row.rentabilidad) || 0;
            
            totalVentas += ventas;
            totalCostos += costos;
            totalMargenBruto += margen;
            
            if (rentabilidad > 0) {
                productosRentables++;
            }
            
            // mejor y peor rentabilidad
            if (rentabilidad > mejorRentabilidad) {
                mejorRentabilidad = rentabilidad;
                productoMejorRentabilidad = row.producto;
            }
            if (rentabilidad < peorRentabilidad) {
                peorRentabilidad = rentabilidad;
                productoPeorRentabilidad = row.producto;
            }
        });
        
        if (totalProductos > 0) {
            ventasPromedio = totalVentas / totalProductos;
            const rentabilidadGlobal = totalVentas > 0 ? ((totalMargenBruto / totalVentas) * 100) : 0;
            const porcentajeProductosRentables = (productosRentables / totalProductos) * 100;
            
            stats.push({
                title: 'Total de Productos Analizados',
                value: `${totalProductos} productos`
            });
            
            stats.push({
                title: 'Ventas Totales Consolidadas',
                value: formatearMoneda(totalVentas)
            });
            
            stats.push({
                title: 'Costos Totales Consolidados',
                value: formatearMoneda(totalCostos)
            });
            
            stats.push({
                title: 'Margen Bruto Total Consolidado',
                value: formatearMoneda(totalMargenBruto)
            });
            
            stats.push({
                title: 'Rentabilidad Global Calculada',
                value: formatearPorcentaje(rentabilidadGlobal)
            });
            
            stats.push({
                title: 'Productos con Rentabilidad Positiva',
                value: `${productosRentables} productos (${porcentajeProductosRentables.toFixed(1)}%)`
            });
            
            stats.push({
                title: 'Ventas Promedio por Producto',
                value: formatearMoneda(ventasPromedio)
            });
            
            if (productoMejorRentabilidad) {
                stats.push({
                    title: 'Producto Más Rentable',
                    value: `${productoMejorRentabilidad} (${formatearPorcentaje(mejorRentabilidad)})`
                });
            }
            
            if (productoPeorRentabilidad && peorRentabilidad < 100) {
                stats.push({
                    title: 'Producto Menos Rentable',
                    value: `${productoPeorRentabilidad} (${formatearPorcentaje(peorRentabilidad)})`
                });
            }
            
            // análisis de distribución de rentabilidad
            let rangosRentabilidad = {
                excelente: 0,  // > 30%
                buena: 0,      // 15-30%
                regular: 0,    // 5-15%
                baja: 0,       // 0-5%
                negativa: 0    // < 0%
            };
            
            tableData.forEach(row => {
                const rentabilidad = parseFloat(row.rentabilidad) || 0;
                if (rentabilidad > 30) rangosRentabilidad.excelente++;
                else if (rentabilidad > 15) rangosRentabilidad.buena++;
                else if (rentabilidad > 5) rangosRentabilidad.regular++;
                else if (rentabilidad > 0) rangosRentabilidad.baja++;
                else rangosRentabilidad.negativa++;
            });
            
            stats.push({
                title: 'Distribución por Nivel de Rentabilidad',
                value: `Excelente (>30%): ${rangosRentabilidad.excelente} | Buena (15-30%): ${rangosRentabilidad.buena} | Regular (5-15%): ${rangosRentabilidad.regular} | Baja (0-5%): ${rangosRentabilidad.baja} | Negativa (<0%): ${rangosRentabilidad.negativa}`
            });
        }
        
        return stats;
    }
};
