const ExportarPDFs = {
    // objetos de configuracion para diferentes tipos de reportes
    reportConfig: {
        presupuesto: {
            templateId: 'reporte-presupuesto',
            filename: (data) => `Presupuesto_${data.categoria}_${data.mesInicio}_${data.añoInicio}-${data.mesFin}_${data.añoFin}.pdf`,
            prepare: function(data) {
                ReportePDFUtils.generatePresupuestoReport(data);
            }
        },
        
        proyecciones: {
            templateId: 'reporte-proyecciones',
            filename: (data) => `Proyecciones_${data.producto}_${data.periodo}.pdf`,
            prepare: function(data) {
                ReportePDFUtils.generateProyeccionesReport(data);
            }
        },
        
        proyecciones_historicas: {
            templateId: 'reporte-proyecciones-historicas',
            filename: (data) => `Proyecciones_Historicas_${data.producto}_${data.periodo}.pdf`,
            prepare: function(data) {
                ReportePDFUtils.generateProyeccionesHistoricasReport(data);
            }
        },
        
        rentabilidad: {
            templateId: 'reporte-rentabilidad',
            filename: (data) => `Rentabilidad_${data.producto}_${data.periodo}.pdf`,
            prepare: function(data) {
                ReportePDFUtils.generateRentabilidadReport(data);
            }
        },
        
        rotacion: {
            templateId: 'reporte-rotacion',
            filename: (data) => `Rotacion_${data.producto}_${data.periodo}.pdf`,
            prepare: function(data) {
                ReportePDFUtils.generateRotacionReport(data);
            }
        },
        
        presupuestos_general: {
            templateId: 'reporte-presupuestos-general',
            filename: (data) => `Presupuestos_General_${data.periodo}.pdf`,
            prepare: function(data) {
                ReportePDFUtils.generatePresupuestosGeneralReport(data);
            }
        },
        
        rotacion_general: {
            templateId: 'reporte-rotacion-general',
            filename: (data) => `Rotacion_General_${data.periodo}.pdf`,
            prepare: function(data) {
                ReportePDFUtils.generateRotacionGeneralReport(data);
            }
        },
        
        rentabilidad_general: {
            templateId: 'reporte-rentabilidad-general',
            filename: (data) => `Rentabilidad_General_${data.periodo}.pdf`,
            prepare: function(data) {
                ReportePDFUtils.generateRentabilidadGeneralReport(data);
            }
        },
        
        proyecciones_general: {
            templateId: 'reporte-proyecciones-general',
            filename: (data) => `Proyecciones_General_${data.titulo}_${data.periodo}.pdf`,
            prepare: function(data) {
                ReportePDFUtils.generateProyeccionesGeneralReport(data);
            }
        },
        
        proyecciones_historicas_general: {
            templateId: 'reporte-proyecciones-historicas-general',
            filename: (data) => `Proyecciones_Historicas_General_${data.periodo}.pdf`,
            prepare: function(data) {
                ReportePDFUtils.generateProyeccionesHistoricasGeneralReport(data);
            }
        },
        
        cuentas: {
            templateId: 'reporte-cuentas',
            filename: (data) => `Analisis_Cuentas_${data.cuentaNombre}_${data.periodo}.pdf`,
            prepare: function(data) {
                ReportePDFUtils.generateCuentasReport(data);
            }
        }
        // agregar mas tipos de reportes aqui segun sea necesario
    },

    exportToPDF: async function(type, data) {
        console.group('Exportando PDF:', type);
        const config = this.reportConfig[type];
        
        if (!config) {
            throw new Error(`Tipo de reporte no soportado: ${type}`);
        }

        try {
            // usar el nuevo sistema unificado
            config.prepare(data);

        } catch (error) {
            console.error('Error al exportar PDF:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Hubo un error al generar el PDF. Por favor intente nuevamente.'
            });
            throw error;
        } finally {
            console.groupEnd();
        }
    }
}; 