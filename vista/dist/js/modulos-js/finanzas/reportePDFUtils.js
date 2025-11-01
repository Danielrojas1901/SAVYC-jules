const ReportePDFUtils = {
    config: {
        pageMargin: 20,
        lineHeight: 8,
        titleFontSize: 16,
        subtitleFontSize: 12,
        textFontSize: 10,
        colors: {
            primary: '#5271ff',
            text: '#333333',
            lightGray: '#f8f9fa'
        }
    },

    createPDF: function() {
        return new jsPDF({
            orientation: 'portrait',
            unit: 'mm',
            format: 'a4'
        });
    },

    // método unificado para finalizar y abrir PDF
    finalizePDF: function(pdf, filename = 'reporte.pdf') {
        const pdfBlob = pdf.output('blob');
        const pdfUrl = URL.createObjectURL(pdfBlob);
        window.open(pdfUrl, '_blank');
        
        // limpiar URL después de un tiempo para evitar memory leaks
        setTimeout(() => URL.revokeObjectURL(pdfUrl), 1000);
    },

    // mestructura basica
    createBasicReport: function(data, config) {
        const pdf = this.createPDF();
        
        let yPosition = this.addCompanyHeader(pdf);
        yPosition = this.addReportTitle(pdf, config.title, yPosition);
        yPosition = this.addSubtitle(pdf, `Período: ${data.periodo}`, yPosition);
        
        if (data.metricas && config.metricsFormatter) {
            yPosition = this.addSubtitle(pdf, config.metricsFormatter(data.metricas), yPosition);
        }
        
        if (data.tableData && config.tableColumns) {
            this.addTable(pdf, data.tableData, config.tableColumns, yPosition, config.tableTitle);
        }
        
        if (data.stats) {
            this.addStatsPage(pdf, data.stats, config.statsTitle || 'Estadísticas y Análisis');
        }
        
        this.finalizePDF(pdf, config.filename || 'reporte.pdf');
    },

    createChartReport: function(data, config) {
        const pdf = this.createPDF();
        
        let yPosition = this.addCompanyHeader(pdf);
        yPosition = this.addReportTitle(pdf, config.title, yPosition);
        
        if (config.entityLabel && config.entityKey && data[config.entityKey]) {
            yPosition = this.addSubtitle(pdf, `${config.entityLabel}: ${data[config.entityKey]}`, yPosition);
        }
        
        yPosition = this.addSubtitle(pdf, `Período: ${data.periodo}`, yPosition);
        
        // añadir gráfico
        yPosition = this.addChart(pdf, data.sourceCanvasId, yPosition);
        
        // añadir tabla
        if (data.tableData && config.tableColumns) {
            this.addTable(pdf, data.tableData, config.tableColumns, yPosition, config.tableTitle);
        }
        
        // página de estadísticas
        if (data.stats) {
            this.addStatsPage(pdf, data.stats, config.statsTitle || 'Estadísticas y Análisis');
        }
        
        this.finalizePDF(pdf, config.filename || 'reporte.pdf');
    },

    addCompanyHeader: function(pdf) {
        const empresa = window.datosFinanzas?.empresa;
        if (!empresa) return this.config.pageMargin;

        const margin = this.config.pageMargin;
        let yPosition = margin;

        pdf.setFontSize(14);
        pdf.setFont('helvetica', 'bold');
        pdf.setTextColor(220, 50, 47);
        pdf.text(empresa.nombre || 'Empresa', margin, yPosition);

        yPosition += 6;
        pdf.setFontSize(10);
        pdf.setFont('helvetica', 'normal');
        pdf.setTextColor(this.config.colors.text);

        const infoEmpresa = [
            `RIF: ${empresa.rif || 'N/A'}`,
            `Dirección: ${empresa.direccion || 'N/A'}`,
            `Teléfono: ${empresa.telefono || 'N/A'}`,
            `Email: ${empresa.email || 'N/A'}`
        ];

        infoEmpresa.forEach(info => {
            pdf.text(info, margin, yPosition);
            yPosition += 4;
        });

        // línea separadora
        yPosition += 5;
        pdf.setDrawColor(0, 0, 0);
        pdf.line(margin, yPosition, 190, yPosition);
        
        return yPosition + 10;
    },

    addReportTitle: function(pdf, title, yPosition) {
        pdf.setFontSize(this.config.titleFontSize);
        pdf.setFont('helvetica', 'bold');
        pdf.setTextColor(this.config.colors.text);
        
        const pageWidth = pdf.internal.pageSize.getWidth();
        const textWidth = pdf.getStringUnitWidth(title) * this.config.titleFontSize / pdf.internal.scaleFactor;
        const textX = (pageWidth - textWidth) / 2;
        
        pdf.text(title, textX, yPosition);
        return yPosition + 15;
    },

    addSubtitle: function(pdf, subtitle, yPosition) {
        pdf.setFontSize(this.config.subtitleFontSize);
        pdf.setFont('helvetica', 'normal');
        pdf.setTextColor(this.config.colors.text);
        
        const pageWidth = pdf.internal.pageSize.getWidth();
        const textWidth = pdf.getStringUnitWidth(subtitle) * this.config.subtitleFontSize / pdf.internal.scaleFactor;
        const textX = (pageWidth - textWidth) / 2;
        
        pdf.text(subtitle, textX, yPosition);
        return yPosition + 10;
    },

    addChart: function(pdf, canvasId, yPosition) {
        let canvas = document.getElementById(canvasId);
        if (!canvas) {
            console.warn('No se encontró elemento con ID:', canvasId);
            return yPosition;
        }

        // si el elemento no es un canvas, buscar el canvas hijo
        if (!(canvas instanceof HTMLCanvasElement)) {
            const canvasChild = canvas.querySelector('canvas');
            if (canvasChild) {
                canvas = canvasChild;
            } else {
                console.error('No se encontró elemento canvas en:', canvasId);
                return yPosition;
            }
        }

        try {
            // dimensiones fijas para PDF (independientes de la pantalla)
            const PDF_CHART_WIDTH = 150; // mm
            const PDF_CHART_HEIGHT = 100; // mm
            
            // dimensiones máximas disponibles en el PDF
            const pageWidth = pdf.internal.pageSize.getWidth();
            const pageHeight = pdf.internal.pageSize.getHeight();
            const maxWidth = pageWidth - (this.config.pageMargin * 2);
            const maxHeight = pageHeight - yPosition - this.config.pageMargin;
            
            // ajustar si las dimensiones fijas no caben
            let finalWidth = Math.min(PDF_CHART_WIDTH, maxWidth);
            let finalHeight = Math.min(PDF_CHART_HEIGHT, maxHeight);
            
            // mantener proporción 3:2 si se ajusta
            const targetAspectRatio = PDF_CHART_WIDTH / PDF_CHART_HEIGHT;
            if (finalWidth / finalHeight > targetAspectRatio) {
                finalWidth = finalHeight * targetAspectRatio;
            } else {
                finalHeight = finalWidth / targetAspectRatio;
            }
            
            // si aún no cabe, usar nueva página
            if (finalHeight > maxHeight || yPosition + finalHeight > pageHeight - this.config.pageMargin) {
                pdf.addPage();
                yPosition = this.addCompanyHeader(pdf);
                // usar dimensiones completas en la nueva página
                finalWidth = Math.min(PDF_CHART_WIDTH, maxWidth);
                finalHeight = Math.min(PDF_CHART_HEIGHT, pageHeight - yPosition - this.config.pageMargin);
                
                // mantener proporción
                if (finalWidth / finalHeight > targetAspectRatio) {
                    finalWidth = finalHeight * targetAspectRatio;
                } else {
                    finalHeight = finalWidth / targetAspectRatio;
                }
            }
            
            // centrar horizontalmente
            const imgX = (pageWidth - finalWidth) / 2;
            
            // crear gráfico estandarizado para PDF
            const standardCanvas = this.createStandardizedChart(canvas);
            const imgData = standardCanvas.toDataURL('image/png', 1.0);
            
            pdf.addImage(imgData, 'PNG', imgX, yPosition, finalWidth, finalHeight);
            return yPosition + finalHeight + 15;
        } catch (error) {
            console.error('Error al procesar canvas:', error);
            return yPosition;
        }
    },

    // crear gráfico estandarizado para PDF con dimensiones fijas
    createStandardizedChart: function(sourceCanvas) {
        // dimensiones fijas para PDF (independientes del dispositivo)
        const STANDARD_WIDTH = 1200;
        const STANDARD_HEIGHT = 800;
        
        console.log('Intentando crear chart estandarizado para PDF...');
        
        // método 1: buscar en Chart.instances (Chart.js v3+)
        let chartInstance = null;
        if (window.Chart && window.Chart.instances) {
            for (let id in window.Chart.instances) {
                if (window.Chart.instances[id].canvas === sourceCanvas) {
                    chartInstance = window.Chart.instances[id];
                    console.log('Chart encontrado en Chart.instances');
                    break;
                }
            }
        }
        
        // método 2: buscar en el canvas directamente
        if (!chartInstance && sourceCanvas.chart) {
            chartInstance = sourceCanvas.chart;
            console.log('Chart encontrado en canvas.chart');
        }
        
        // método 3: buscar por dataset del canvas
        if (!chartInstance && window.Chart) {
            const charts = window.Chart.instances || {};
            Object.values(charts).forEach(chart => {
                if (chart.canvas === sourceCanvas) {
                    chartInstance = chart;
                    console.log('Chart encontrado por búsqueda manual');
                }
            });
        }
        
        if (!chartInstance) {
            console.warn('No se encontró instancia de Chart.js, usando fallback de copia directa');
            return this.createStandardizedCanvas(sourceCanvas, STANDARD_WIDTH, STANDARD_HEIGHT);
        }
        
        try {
            // crear nuevo canvas con dimensiones estándar
            const standardCanvas = document.createElement('canvas');
            standardCanvas.width = STANDARD_WIDTH;
            standardCanvas.height = STANDARD_HEIGHT;
            
            // obtener datos y configuración del gráfico original
            const originalData = chartInstance.data;
            const originalOptions = chartInstance.options;
            const chartType = chartInstance.config.type;
            
            console.log('Creando configuración para PDF...', {
                type: chartType,
                datasetCount: originalData.datasets.length,
                labelCount: originalData.labels.length
            });
            
            // crear configuración simplificada para PDF
            const pdfConfig = {
                type: chartType,
                data: {
                    labels: [...originalData.labels],
                    datasets: originalData.datasets.map(dataset => ({
                        ...dataset,
                        // asegurar que las propiedades se copien correctamente
                        data: [...dataset.data]
                    }))
                },
                options: {
                    responsive: false,
                    maintainAspectRatio: false,
                    animation: false,
                    devicePixelRatio: 1,
                    font: {
                        size: 14 // tamaño base por defecto
                    },
                    plugins: {
                        title: originalOptions.plugins?.title ? {
                            display: originalOptions.plugins.title.display,
                            text: originalOptions.plugins.title.text,
                            font: { 
                                size: 22,
                                weight: 'bold'
                            }
                        } : undefined,
                        legend: originalOptions.plugins?.legend ? {
                            display: originalOptions.plugins.legend.display !== false,
                            position: originalOptions.plugins.legend.position || 'top',
                            labels: { 
                                font: { 
                                    size: 17,
                                    weight: 'normal'
                                },
                                usePointStyle: false,
                                padding: 15
                            }
                        } : {
                            display: true,
                            position: 'top',
                            labels: { 
                                font: { 
                                    size: 17,
                                    weight: 'normal'
                                },
                                usePointStyle: false,
                                padding: 15
                            }
                        }
                    },
                    scales: this.createPDFScales(originalOptions.scales)
                }
            };
            
            // crear nuevo gráfico para PDF
            console.log('Creando nuevo chart para PDF...');
            const pdfChart = new window.Chart(standardCanvas, pdfConfig);
            
            // forzar renderizado inmediato
            pdfChart.update('none');
            pdfChart.render();
            
            console.log('Chart PDF creado exitosamente');
            
            // programar limpieza del gráfico temporal
            setTimeout(() => {
                if (pdfChart) {
                    pdfChart.destroy();
                }
            }, 500);
            
            return standardCanvas;
            
        } catch (error) {
            console.error('Error creando chart para PDF:', error);
            console.log('Usando fallback de copia directa...');
            return this.createStandardizedCanvas(sourceCanvas, STANDARD_WIDTH, STANDARD_HEIGHT);
        }
    },

    // crear escalas optimizadas para PDF
    createPDFScales: function(originalScales) {
        if (!originalScales) {
            // crear escalas por defecto si no existen
            return {
                x: {
                    display: true,
                    title: {
                        display: false
                    },
                    ticks: {
                        font: { 
                            size: 14,
                            weight: 'normal'
                        },
                        maxRotation: 0,
                        minRotation: 0
                    },
                    grid: {
                        display: true,
                        color: 'rgba(0, 0, 0, 0.1)'
                    }
                },
                y: {
                    display: true,
                    beginAtZero: true,
                    title: {
                        display: false
                    },
                    ticks: {
                        font: { 
                            size: 14,
                            weight: 'normal'
                        }
                    },
                    grid: {
                        display: true,
                        color: 'rgba(0, 0, 0, 0.1)'
                    }
                }
            };
        }
        
        const pdfScales = {};
        Object.keys(originalScales).forEach(scaleKey => {
            const scale = originalScales[scaleKey];
            pdfScales[scaleKey] = {
                display: scale.display !== false,
                beginAtZero: scale.beginAtZero !== false,
                title: scale.title ? {
                    display: scale.title.display !== false,
                    text: scale.title.text,
                    font: { 
                        size: 17,
                        weight: 'bold'
                    },
                    padding: {
                        top: 10,
                        bottom: 10
                    }
                } : {
                    display: false
                },
                ticks: {
                    font: { 
                        size: 14,
                        weight: 'normal'
                    },
                    maxRotation: scale.ticks?.maxRotation || 0,
                    minRotation: scale.ticks?.minRotation || 0,
                    padding: 5
                },
                grid: scale.grid ? {
                    display: scale.grid.display !== false,
                    color: scale.grid.color || 'rgba(0, 0, 0, 0.1)',
                    lineWidth: 1
                } : {
                    display: true,
                    color: 'rgba(0, 0, 0, 0.1)',
                    lineWidth: 1
                }
            };
        });
        
        return pdfScales;
    },

    // fallback: crear canvas estandarizado por copia directa
    createStandardizedCanvas: function(sourceCanvas, targetWidth, targetHeight) {
        const standardCanvas = document.createElement('canvas');
        const ctx = standardCanvas.getContext('2d');
        
        // establecer dimensiones estándar
        standardCanvas.width = targetWidth;
        standardCanvas.height = targetHeight;
        
        // configurar contexto para renderizado de alta calidad
        ctx.imageSmoothingEnabled = true;
        ctx.imageSmoothingQuality = 'high';
        
        // fondo blanco para evitar transparencias
        ctx.fillStyle = '#ffffff';
        ctx.fillRect(0, 0, targetWidth, targetHeight);
        
        // dibujar el canvas original escalado al tamaño estándar
        ctx.drawImage(sourceCanvas, 0, 0, targetWidth, targetHeight);
        
        return standardCanvas;
    },

    // crear tabla con configuración unificada
    addTable: function(pdf, data, columns, yPosition, title) {
        const headerHeight = 70;
        const self = this;
        
        // obtener el número de página actual antes de crear la tabla
        const startingPageNumber = pdf.internal.getCurrentPageInfo().pageNumber;
        
        // si estamos en una página que ya tiene contenido (como después del grafico), 
        // verificar si necesitamos nueva página o ajustar posición
        if (yPosition > 200) {
            pdf.addPage();
            yPosition = this.addCompanyHeader(pdf);
        } else if (startingPageNumber > 1) {
            // si estamos en página > 1 y hay poco espacio, mejor usar nueva página
            if (yPosition > 150) {
                pdf.addPage();
                yPosition = this.addCompanyHeader(pdf);
            }
        }

        if (title) {
            pdf.setFontSize(14);
            pdf.setFont('helvetica', 'bold');
            pdf.text(title, this.config.pageMargin, yPosition);
            yPosition += 10;
        }

        const tableConfig = {
            head: [columns.map(col => col.title)],
            body: data.map(row => columns.map(col => this.formatCellValue(row[col.dataKey], col.type))),
            startY: yPosition,
            margin: { 
                left: this.config.pageMargin, 
                right: this.config.pageMargin,
                top: headerHeight,
                bottom: this.config.pageMargin 
            },
            pageBreak: 'auto',
            showHead: 'everyPage',
            styles: {
                fontSize: 9,
                cellPadding: 3
            },
            headStyles: {
                fillColor: [82, 113, 255],
                textColor: 255,
                fontSize: 10,
                fontStyle: 'bold'
            },
            alternateRowStyles: {
                fillColor: [248, 249, 250]
            },
            columnStyles: this.getColumnStyles(columns),
            didDrawPage: function(data) {
                // añadir header de empresa solo en páginas adicionales de la tabla (no en la primera página de la tabla)
                if (data.pageNumber > 1) {
                    console.log(`Adding header to table page ${data.pageNumber}`);
                    
                    // no necesitamos cambiar páginas, autoTable ya está trabajando en la página correcta
                    // simplemente añadir el header en la posición actual
                    self.addCompanyHeader(pdf);
                }
            }
        };

        pdf.autoTable(tableConfig);
        return pdf.lastAutoTable.finalY + 10;
    },

    formatCellValue: function(value, type) {
        switch (type) {
            case 'money':
                return formatearMoneda(value);
            case 'percentage':
                return formatearPorcentaje(value);
            case 'number':
                return typeof value === 'number' ? value.toLocaleString() : value;
            default:
                return value || '';
        }
    },

    getColumnStyles: function(columns) {
        const styles = {};
        columns.forEach((col, index) => {
            if (col.type === 'money' || col.type === 'percentage' || col.type === 'number') {
                styles[index] = { halign: 'right' };
            } else {
                styles[index] = { halign: 'left' };
            }
        });
        return styles;
    },

    addStatsPage: function(pdf, stats, title) {
        pdf.addPage();
        let yPosition = this.addCompanyHeader(pdf);
        yPosition = this.addReportTitle(pdf, title, yPosition);
        
        const { pageHeight } = { pageHeight: 297 }; // A4 height
        
        pdf.setFontSize(this.config.textFontSize);
        
        stats.forEach(stat => {
            if (yPosition > pageHeight - 50) {
                pdf.addPage();
                yPosition = this.addCompanyHeader(pdf);
                yPosition += 10;
            }
            
            pdf.setTextColor(33, 37, 41); // color texto principal
            pdf.setFont('helvetica', 'bold');
            pdf.text(stat.title, this.config.pageMargin, yPosition);
            yPosition += 6;
            
            pdf.setTextColor(108, 117, 125); // color secundario
            pdf.setFont('helvetica', 'normal');
            pdf.text(stat.value, this.config.pageMargin + 5, yPosition);
            yPosition += 12;
        });
    },

    reportConfigs: {
        presupuesto: {
            title: 'Análisis de Presupuesto vs Gastos',
            tableColumns: [
                { title: 'Período', dataKey: 'mes', type: 'text' },
            { title: 'Presupuesto', dataKey: 'presupuesto', type: 'money' },
            { title: 'Gasto Real', dataKey: 'gasto_real', type: 'money' },
            { title: 'Diferencia', dataKey: 'diferencia', type: 'money' },
                { title: 'Porcentaje', dataKey: 'porcentaje', type: 'percentage' },
                { title: 'Estado', dataKey: 'estado_texto', type: 'text' }
            ],
            tableTitle: 'Detalle de Presupuesto vs Gastos por Período',
            entityLabel: 'Categoría',
            entityKey: 'categoria'
        },
        
        rotacion: {
            title: 'Análisis de Rotación de Inventario',
            tableColumns: [
                { title: 'Período', dataKey: 'mes', type: 'text' },
                { title: 'Estado Stock', dataKey: 'estado_stock_texto', type: 'text' },
                { title: 'Stock Inicial', dataKey: 'stock_inicial', type: 'number' },
                { title: 'Stock Final', dataKey: 'stock_final', type: 'number' },
                { title: 'Ventas', dataKey: 'ventas_cantidad', type: 'number' },
                { title: 'Días Rotación', dataKey: 'dias_rotacion', type: 'number' },
                { title: 'Promedio', dataKey: 'promedio_dias_rotacion', type: 'number' },
                { title: 'Estado Rotación', dataKey: 'estado_rotacion_texto', type: 'text' }
            ],
            tableTitle: 'Detalle de Rotación por Período',
            entityLabel: 'Producto',
            entityKey: 'producto'
        },
        
        rentabilidad: {
            title: 'Análisis de Rentabilidad',
            tableColumns: [
                { title: 'Período', dataKey: 'fecha', type: 'text' },
            { title: 'Ventas Totales', dataKey: 'ventas_totales', type: 'money' },
                { title: 'Costo Ventas', dataKey: 'costo_ventas', type: 'money' },
            { title: 'Margen Bruto', dataKey: 'margen_bruto', type: 'money' },
            { title: 'Rentabilidad', dataKey: 'rentabilidad', type: 'percentage' }
            ],
            tableTitle: 'Detalle de Rentabilidad por Período',
            entityLabel: 'Producto',
            entityKey: 'producto'
        },
        
        proyecciones: {
            title: 'Análisis de Proyecciones de Ventas',
            tableColumns: [
                { title: 'Período', dataKey: 'fecha', type: 'text' },
                { title: 'Valor Proyectado', dataKey: 'valor_proyectado', type: 'money' },
                { title: 'Tendencia', dataKey: 'tendencia_texto', type: 'text' }
            ],
            tableTitle: 'Detalle de Proyecciones por Período',
            entityLabel: 'Producto',
            entityKey: 'producto'
        },
        
        proyecciones_historicas: {
            title: 'Análisis de Precisión Histórica de Proyecciones',
            tableColumns: [
                { title: 'Período', dataKey: 'fecha', type: 'text' },
            { title: 'Valor Proyectado', dataKey: 'valor_proyectado', type: 'money' },
            { title: 'Valor Real', dataKey: 'valor_real', type: 'money' },
            { title: 'Precisión', dataKey: 'precision', type: 'percentage' }
            ],
            tableTitle: 'Detalle de Precisión Histórica por Período',
            entityLabel: 'Producto',
            entityKey: 'producto'
        },
        
        cuentas: {
            title: 'Análisis de Movimientos de Cuenta',
            tableColumns: [
                { title: 'Período', dataKey: 'periodo', type: 'text' },
                { title: 'Debe', dataKey: 'debe', type: 'money' },
                { title: 'Haber', dataKey: 'haber', type: 'money' }
            ],
            tableTitle: 'Detalle de Movimientos por Período',
            entityLabel: 'Cuenta',
            entityKey: 'cuentaNombre'
        }
    },

    generatePresupuestoReport: function(data) {
        const config = {
            ...this.reportConfigs.presupuesto,
            filename: `Presupuesto_${data.categoria}_${data.periodo}.pdf`
        };
        this.createChartReport(data, config);
    },

    generateRotacionReport: function(data) {
        const config = {
            ...this.reportConfigs.rotacion,
            filename: `Rotacion_${data.producto}_${data.periodo}.pdf`
        };
        
        data.tableData = data.tableData.map(row => ({
            ...row,
            estado_stock_texto: this.formatBadgeText(row.estado_stock, {
                        'sin_stock': 'Sin Stock',
                        'critico': 'Crítico', 
                        'bajo': 'Bajo',
                        'exceso': 'Exceso',
                        'normal': 'Normal'
            }),
            estado_rotacion_texto: this.formatBadgeText(row.estado_rotacion, {
                        'alto': 'Alto (Lenta)',
                        'bajo': 'Bajo (Rápida)',
                        'normal': 'Normal'
            })
        }));
        
        this.createChartReport(data, config);
    },

    generateRentabilidadReport: function(data) {
        const config = {
            ...this.reportConfigs.rentabilidad,
            filename: `Rentabilidad_${data.producto}_${data.periodo}.pdf`
        };
        this.createChartReport(data, config);
    },

    generateProyeccionesReport: function(data) {
        const config = {
            ...this.reportConfigs.proyecciones,
            filename: `Proyecciones_${data.producto}_${data.periodo}.pdf`
        };
        
        data.tableData = data.tableData.map(row => ({
            ...row,
            tendencia_texto: row.tendencia === 'up' ? '↑ Crecimiento' : '↓ Decrecimiento'
        }));
        
        this.createChartReport(data, config);
    },

    generateProyeccionesHistoricasReport: function(data) {
        const config = {
            ...this.reportConfigs.proyecciones_historicas,
            filename: `Proyecciones_Historicas_${data.producto}_${data.periodo}.pdf`
        };
        this.createChartReport(data, config);
    },

    generateCuentasReport: function(data) {
        const config = {
            ...this.reportConfigs.cuentas,
            filename: `Analisis_Cuentas_${data.cuentaNombre}_${data.periodo}.pdf`
        };
        this.createChartReport(data, config);
    },

    generatePresupuestosGeneralReport: function(data) {
        const config = {
            title: 'Análisis General de Presupuestos',
            tableColumns: [
                { title: 'Categoría', dataKey: 'categoria', type: 'text' },
                { title: 'Presupuesto', dataKey: 'presupuesto_display', type: 'text' },
                { title: 'Gasto Real', dataKey: 'gasto_real', type: 'money' },
                { title: 'Diferencia', dataKey: 'diferencia_display', type: 'text' },
                { title: 'Porcentaje', dataKey: 'porcentaje_display', type: 'text' },
                { title: 'Estado', dataKey: 'estado_display', type: 'text' }
            ],
            tableTitle: 'Análisis de Presupuestos por Categoría',
            filename: `Presupuestos_General_${data.periodo}.pdf`,
            entityLabel: 'Período',
            entityKey: 'periodo'
        };
        
        data.tableData = data.tableData.map(row => ({
            ...row,
            presupuesto_display: !row.presupuesto || row.presupuesto === 0 ? 
                'Sin Presupuesto' : formatearMoneda(row.presupuesto),
            diferencia_display: !row.presupuesto || row.presupuesto === 0 ? 
                '-' : formatearMoneda(row.diferencia),
            porcentaje_display: !row.presupuesto || row.presupuesto === 0 ? 
                '-' : formatearPorcentaje(row.porcentaje_utilizado),
            estado_display: !row.presupuesto || row.presupuesto === 0 ? 
                '-' : (row.estado === 'success' ? 'Dentro del Presupuesto' : 'Excedido')
        }));
        
        // usar createChartReport para incluir el gráfico
        this.createChartReport(data, config);
    },

    generateRotacionGeneralReport: function(data) {
        const config = {
            title: 'Análisis General de Rotación',
            tableColumns: [
                { title: 'Producto', dataKey: 'producto', type: 'text' },
                { title: 'Estado Stock', dataKey: 'estado_stock_texto', type: 'text' },
                { title: 'Stock Inicial', dataKey: 'stock_inicial', type: 'number' },
                { title: 'Stock Final', dataKey: 'stock_final', type: 'number' },
                { title: 'Ventas', dataKey: 'ventas_cantidad', type: 'number' },
                { title: 'Días Rotación', dataKey: 'dias_rotacion', type: 'number' },
                { title: 'Promedio', dataKey: 'promedio_dias_rotacion', type: 'number' },
                { title: 'Estado Rotación', dataKey: 'estado_rotacion_texto', type: 'text' }
            ],
            tableTitle: 'Análisis de Rotación por Producto',
            filename: `Rotacion_General_${data.periodo}.pdf`
        };
        
        data.tableData = data.tableData.map(row => ({
            ...row,
            estado_stock_texto: this.formatBadgeText(row.estado_stock, {
                'sin_stock': 'Sin Stock',
                'critico': 'Crítico',
                'bajo': 'Bajo',
                'exceso': 'Exceso',
                'normal': 'Normal'
            }),
            estado_rotacion_texto: this.formatBadgeText(row.estado_rotacion, {
                'alto': 'Alto (Lenta)',
                'bajo': 'Bajo (Rápida)',
                'normal': 'Normal'
            })
        }));
        
        this.createBasicReport(data, config);
    },

    generateRentabilidadGeneralReport: function(data) {
        const config = {
            title: 'Análisis General de Rentabilidad',
            tableColumns: [
                { title: 'Producto', dataKey: 'producto', type: 'text' },
                { title: 'Ventas Totales', dataKey: 'ventas_totales', type: 'money' },
                { title: 'Costo de Ventas', dataKey: 'costo_ventas', type: 'money' },
                { title: 'Margen Bruto', dataKey: 'margen_bruto', type: 'money' },
                { title: 'Rentabilidad', dataKey: 'rentabilidad', type: 'percentage' }
            ],
            tableTitle: 'Análisis de Rentabilidad por Producto',
            metricsFormatter: (metricas) => `Rentabilidad Promedio: ${metricas.rentabilidad_promedio} | Margen Bruto Total: ${metricas.margen_bruto_total}`,
            filename: `Rentabilidad_General_${data.periodo}.pdf`
        };
        
        this.createBasicReport(data, config);
    },

    generateProyeccionesGeneralReport: function(data) {
        const config = {
            title: data.titulo || 'Análisis General de Proyecciones',
            tableColumns: data.tipoAnalisis === 'futuro' ? [
                { title: 'Producto', dataKey: 'producto', type: 'text' },
                { title: 'Ventas Últimos 6 Meses', dataKey: 'ventas_actuales', type: 'money' },
                { title: 'Proyección 3M', dataKey: 'proyeccion_3m', type: 'money' },
                { title: 'Proyección 6M', dataKey: 'proyeccion_6m', type: 'money' },
                { title: 'Proyección 12M', dataKey: 'proyeccion_12m', type: 'money' }
            ] : [
                { title: 'Producto', dataKey: 'producto', type: 'text' },
                { title: 'Precisión Promedio', dataKey: 'precision_promedio', type: 'percentage' },
                { title: 'Mejor Precisión', dataKey: 'mejor_precision', type: 'percentage' },
                { title: 'Peor Precisión', dataKey: 'peor_precision', type: 'percentage' }
            ],
            tableTitle: data.tipoAnalisis === 'futuro' ? 
                'Proyecciones de Ventas por Producto' : 
                'Análisis de Precisión por Producto',
            filename: data.tipoAnalisis === 'futuro' ? 
                `Proyecciones_General_${data.periodo}.pdf` : 
                `Proyecciones_Historicas_General_${data.periodo}.pdf`
        };
        
        this.createChartReport(data, config);
    },

    generateProyeccionesHistoricasGeneralReport: function(data) {
        data.tipoAnalisis = 'historico';
        this.generateProyeccionesGeneralReport(data);
    },

    formatBadgeText: function(value, mapping) {
        return mapping[value] || value;
    }
}; 