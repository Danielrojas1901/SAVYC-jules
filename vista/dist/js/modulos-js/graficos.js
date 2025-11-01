window.UtilidadesGraficos = {
    configuraciones: {
        comun: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                intersect: false,
                mode: 'index'
            },
            scales: {
                x: {
                    grid: {
                        display: true,
                        color: 'rgba(0, 0, 0, 0.1)'
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        display: true,
                        color: 'rgba(0, 0, 0, 0.1)'
                    }
                }
            }
        },
        graficoLinea: {
            type: 'line',
            options: {
                plugins: {
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        callbacks: {
                            label: function (contexto) {
                                let etiqueta = contexto.dataset.label || '';
                                if (etiqueta) {
                                    etiqueta += ': ';
                                }
                                if (contexto.parsed.y !== null) {
                                    etiqueta += formatearNumero(contexto.parsed.y);
                                }
                                return etiqueta;
                            }
                        }
                    }
                }
            }
        }
    },
    // colores estandarizados: azul y teal
    coloresEstandar: {
        azul: {
            border: 'rgba(82, 113, 255, 1)',
            background: 'rgba(82, 113, 255, 0.2)',
            backgroundBar: 'rgba(82, 113, 255, 0.8)'
        },
        teal: {
            border: 'rgba(94, 193, 211, 1)',
            background: 'rgba(94, 193, 211, 0.2)',
            backgroundBar: 'rgba(94, 193, 211, 0.8)'
        }
    },

    estilosDataset: {
        datosReales: {
            borderColor: 'rgba(94, 193, 211, 1)',
            backgroundColor: 'rgba(94, 193, 211, 0.2)',
            tension: 0.4,
            fill: true
        },
        datosProyectados: {
            borderColor: 'rgba(82, 113, 255, 1)',
            backgroundColor: 'rgba(82, 113, 255, 0.2)',
            borderDash: [5, 5],
            tension: 0.4,
            fill: true
        },
        presupuestoLinea: {
            borderColor: 'rgba(82, 113, 255, 1)',
            backgroundColor: 'rgba(82, 113, 255, 0.2)',
            tension: 0.4,
            fill: true
        },
        gastoLinea: {
            borderColor: 'rgba(94, 193, 211, 1)',
            backgroundColor: 'rgba(94, 193, 211, 0.2)',
            tension: 0.4,
            fill: true
        }
    },
    destruirGrafico: function (grafico) {
        if (grafico) {
            grafico.destroy();
            return null;
        }
        return null;
    },

    crearDatasetLinea: function (etiqueta, datos, esProyeccion = false) {
        return {
            label: etiqueta,
            data: datos,
            ...(esProyeccion ? this.estilosDataset.datosProyectados : this.estilosDataset.datosReales)
        };
    },

    // determinar tipo de gráfico basado en cantidad de datos
    determinarTipoGrafico: function(labels) {
        return labels && labels.length === 1 ? 'bar' : 'line';
    },

    // crear dataset adaptado al tipo de gráfico (línea o barra) con colores estandarizados
    crearDatasetAdaptivo: function(etiqueta, datos, colorKey = 'teal', tipoGrafico = 'line') {
        const esBarras = tipoGrafico === 'bar';
        const colores = this.coloresEstandar[colorKey] || this.coloresEstandar.teal;
        
        return {
            label: etiqueta,
            data: datos,
            borderColor: colores.border,
            backgroundColor: esBarras ? colores.backgroundBar : colores.background,
            borderWidth: esBarras ? 2 : 1,
            ...(esBarras ? {} : {
                tension: 0.4,
                fill: true
            })
        };
    },
    
    obtenerOpciones: function(titulo, opcionesAdicionales = {}) {
        return {
            ...this.configuraciones.comun,
            ...this.configuraciones.graficoLinea.options,
            plugins: {
                ...this.configuraciones.graficoLinea.options.plugins,
                title: {
                    display: true,
                    text: titulo
                }
            },
            ...opcionesAdicionales
        };
    },

    utilidadesModal: {
        configurarModal: function (idModal, alMostrar = null, alOcultar = null) {
            const $modal = $(idModal);
            $modal.off('shown.bs.modal hidden.bs.modal');
            if (alMostrar) {
                $modal.on('shown.bs.modal', alMostrar);
            }

            if (alOcultar) {
                $modal.on('hidden.bs.modal', alOcultar);
            }

            return $modal;
        },

        actualizarTituloModal: function (idModal, titulo) {
            $(`${idModal}-label`).text(titulo);
        },

        ajustarAltoCanvas: function (canvas) {
            if (canvas && canvas.parentElement) {
                canvas.parentElement.style.height = '400px';
            }
        }
    },

    inicializarGraficoCuentas: function (ctx, datos) {
        if (!datos || !datos.labels || datos.labels.length === 0 ||
            (datos.ingresos?.every(val => val === 0) && datos.egresos?.every(val => val === 0))) {

            if (ctx.chart) {
                ctx.chart.destroy();
            }

            ctx.width = ctx.parentElement.offsetWidth || 800;
            ctx.height = 400;
            ctx.style.width = '100%';
            ctx.style.height = '400px';

            const context = ctx.getContext('2d');
            context.clearRect(0, 0, ctx.width, ctx.height);

            context.textAlign = 'center';
            context.textBaseline = 'middle';
            context.font = '19px Arial';
            context.fillStyle = '#6c757d';
            context.fillText('Aún no existen registros para el periodo seleccionado', ctx.width / 2, ctx.height / 2);

            return null;
        }

        return new Chart(ctx, {
            type: 'line',
            ...this.configuraciones.comun,
            data: {
                labels: datos.labels,
                datasets: [
                    this.crearDatasetAdaptivo('Ingresos', datos.ingresos, 'teal', 'line'),
                    this.crearDatasetAdaptivo('Egresos', datos.egresos, 'azul', 'line')
                ]
            },
            options: this.obtenerOpciones('Análisis de Cuentas')
        });
    },

    inicializarGraficoPresupuesto: function (ctx, datos, categoria, opcionesAdicionales = {}) {
        if (!datos || !datos.labels || datos.labels.length === 0 ||
            (datos.presupuesto.every(val => val === 0) && datos.gasto_real.every(val => val === 0))) {

            if (ctx.chart) {
                ctx.chart.destroy();
            }

            ctx.width = ctx.parentElement.offsetWidth || 800;
            ctx.height = 400;
            ctx.style.width = '100%';
            ctx.style.height = '400px';

            const context = ctx.getContext('2d');
            context.clearRect(0, 0, ctx.width, ctx.height);

            context.textAlign = 'center';
            context.textBaseline = 'middle';
            context.font = '19px Arial';
            context.fillStyle = '#6c757d';
            context.fillText('Aún no existen registros para el periodo seleccionado', ctx.width / 2, ctx.height / 2);

            return null;
        }

        // determinar tipo de gráfico basado en cantidad de datos
        const tipoGrafico = this.determinarTipoGrafico(datos.labels);
        const esBarras = tipoGrafico === 'bar';

        const opciones = {
            ...this.configuraciones.comun,
            ...opcionesAdicionales,
            plugins: {
                ...this.configuraciones.comun.plugins,
                ...opcionesAdicionales.plugins,
                title: {
                    display: true,
                    text: `Presupuesto vs Gasto Real - ${categoria}`
                }
            },
            scales: {
                ...this.configuraciones.comun.scales,
                y: {
                    ...this.configuraciones.comun.scales.y,
                    beginAtZero: true
                },
                ...(esBarras ? {
                    x: {
                        ...this.configuraciones.comun.scales.x,
                        grid: { display: true, color: 'rgba(0, 0, 0, 0.1)' }
                    }
                } : {})
            },
            ...(esBarras ? {
                categoryPercentage: 0.8,
                barPercentage: 0.9
            } : {})
        };

        return new Chart(ctx, {
            type: tipoGrafico,
            data: {
                labels: datos.labels,
                datasets: [
                    this.crearDatasetAdaptivo('Presupuesto', datos.presupuesto, 'azul', tipoGrafico),
                    this.crearDatasetAdaptivo('Gasto Real', datos.gasto_real, 'teal', tipoGrafico)
                ]
            },
            options: opciones
        });
    },

    inicializarGraficoProyecciones: function (ctx, historico, proyeccion) {
        console.group('Inicializando Gráfico Proyecciones');
        console.log('Datos históricos recibidos:', historico);
                    console.log('Datos proyecciones recibidos:', proyeccion);

        const tipoAnalisis = $('#tipo-analisis').val();
        let datos;

        if (!historico || !proyeccion ||
            (!historico.labels?.length && !proyeccion.labels?.length) ||
            (historico.valores?.every(val => val === 0) && proyeccion.valores?.every(val => val === 0))) {

            if (ctx.chart) {
                ctx.chart.destroy();
            }

            ctx.width = ctx.parentElement.offsetWidth || 800;
            ctx.height = 400;
            ctx.style.width = '100%';
            ctx.style.height = '400px';

            const context = ctx.getContext('2d');
            context.clearRect(0, 0, ctx.width, ctx.height);

            context.textAlign = 'center';
            context.textBaseline = 'middle';
            context.font = '19px Arial';
            context.fillStyle = '#6c757d';
            context.fillText('Aún no existen registros para el periodo seleccionado', ctx.width / 2, ctx.height / 2);

            console.log('No hay datos para mostrar');
            console.groupEnd();
            return null;
        }

        if (tipoAnalisis === 'historico') {
            const historicas = window.datosFinanzas.datos_grafico_proyecciones.historicas;
            datos = {
                labels: historicas.labels,
                datasets: [
                    this.crearDatasetLinea(
                        'Ventas Reales',
                        historicas.reales
                    ),
                    this.crearDatasetLinea(
                        'Proyecciones Históricas',
                        historicas.valores,
                        true
                    )
                ]
            };
        } else {
            datos = {
                labels: [...historico.labels, ...proyeccion.labels],
                datasets: [
                    this.crearDatasetLinea(
                        'Ventas Pasadas',
                        [...historico.valores, ...Array(proyeccion.valores.length).fill(null)]
                    ),
                    ...(proyeccion.valores.length > 0 ? [
                        this.crearDatasetLinea(
                            'Proyecciones',
                            [
                                ...Array(historico.valores.length - 1).fill(null),
                                historico.valores[historico.valores.length - 1],
                                ...proyeccion.valores
                            ],
                            true
                        )
                    ] : [])
                ]
            };
        }

        console.log('Datos finales para el gráfico:', datos);
        console.groupEnd();

        // determinar tipo de gráfico y adaptar datasets
        const tipoGrafico = this.determinarTipoGrafico(datos.labels);
        
        // recrear datasets con estilo adaptivo y colores estandarizados
        const datasetsAdaptivos = datos.datasets.map((dataset, index) => {
            const esProyeccion = dataset.label.toLowerCase().includes('proyecc');
            const colorKey = esProyeccion ? 'azul' : 'teal';
            return this.crearDatasetAdaptivo(dataset.label, dataset.data, colorKey, tipoGrafico);
        });

        return new Chart(ctx, {
            type: tipoGrafico,
            data: {
                ...datos,
                datasets: datasetsAdaptivos
            },
            options: this.obtenerOpciones('Análisis de Ventas', {
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
                        ...(tipoGrafico === 'bar' ? {
                            grid: { display: true, color: 'rgba(0, 0, 0, 0.1)' }
                        } : {})
                    }
                },
                ...(tipoGrafico === 'bar' ? {
                    categoryPercentage: 0.8,
                    barPercentage: 0.9
                } : {})
            })
        });
    },

    inicializarGraficoModal: function (ctx, idModal, datos, producto) {
        if (!datos || !Object.values(datos).some(dataset =>
            dataset && dataset.labels && dataset.labels.length > 0 &&
            Object.values(dataset).some(arr => Array.isArray(arr) && arr.some(val => val !== 0)))) {

            if (ctx.chart) {
                ctx.chart.destroy();
            }

            ctx.width = ctx.parentElement.offsetWidth || 800;
            ctx.height = 400;
            ctx.style.width = '100%';
            ctx.style.height = '400px';

            const context = ctx.getContext('2d');
            context.clearRect(0, 0, ctx.width, ctx.height);

            context.textAlign = 'center';
            context.textBaseline = 'middle';
            context.font = '19px Arial';
            context.fillStyle = '#6c757d';
            context.fillText('Aún no existen registros para el periodo seleccionado', ctx.width / 2, ctx.height / 2);

            return null;
        }

        let datosGrafico;

        switch (idModal) {
            case 'modal-rotacion':
                datosGrafico = {
                    labels: datos.rotacion.labels,
                    datasets: [
                        this.crearDatasetLinea('Stock', datos.rotacion.stock),
                        this.crearDatasetLinea('Ventas', datos.rotacion.ventas)
                    ]
                };
                break;
            case 'modal-proyeccion':
                const tipoAnalisis = $('#ver-historico').val();
                if (tipoAnalisis === 'proyecciones') {
                    const historico = datos.proyeccion;
                    const ultimoValorHistorico = historico.ventas_reales[historico.ventas_reales.length - 1];
                    const mesesFuturos = ['Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];

                    datosGrafico = {
                        labels: [...historico.labels, ...mesesFuturos],
                        datasets: [
                            this.crearDatasetLinea(
                                'Ventas Pasadas',
                                [...historico.ventas_reales, ...Array(mesesFuturos.length).fill(null)]
                            ),
                            this.crearDatasetLinea(
                                'Proyecciones',
                                [
                                    ...Array(historico.ventas_reales.length - 1).fill(null),
                                    ultimoValorHistorico,
                                    datos.resumen.proyecciones.tresMeses,
                                    (datos.resumen.proyecciones.tresMeses + datos.resumen.proyecciones.seisMeses) / 2,
                                    datos.resumen.proyecciones.seisMeses,
                                    (datos.resumen.proyecciones.seisMeses + datos.resumen.proyecciones.docesMeses) / 2,
                                    (datos.resumen.proyecciones.seisMeses + datos.resumen.proyecciones.docesMeses) / 2,
                                    datos.resumen.proyecciones.docesMeses
                                ],
                                true
                            )
                        ]
                    };
                } else {
                    datosGrafico = {
                        labels: datos.proyeccion.labels,
                        datasets: [
                            this.crearDatasetLinea('Ventas Reales', datos.proyeccion.ventas_reales),
                            this.crearDatasetLinea('Proyectado', datos.proyeccion.proyectado, true)
                        ]
                    };
                }
                break;
            case 'modal-rentabilidad':
                datosGrafico = {
                    labels: datos.rentabilidad.labels,
                    datasets: [
                        this.crearDatasetLinea('Rentabilidad', datos.rentabilidad.rentabilidad),
                        this.crearDatasetLinea('ROI', datos.rentabilidad.roi)
                    ]
                };
                break;
        }

        return new Chart(ctx, {
            type: 'line',
            ...this.configuraciones.comun,
            data: datosGrafico,
            options: this.obtenerOpciones(
                `${idModal === 'modal-proyeccion' ?
                    ($('#ver-historico').val() === 'proyecciones' ? 'Proyección de Ventas' : 'Precisión Histórica') :
                    idModal.replace('modal-', '').charAt(0).toUpperCase() + idModal.slice(7)} - ${producto}`
            )
        });
    }
};

//var ctxGastos = document.getElementById('graficoGastos').getContext('2d');

// Obtener datos para el gráfico de gastos
$(document).ready(function () {
    $.ajax({
        type: 'POST',
        url: 'index.php?pagina=inicio',
        data: { obtenerDatosGrafico: true },
        dataType: 'json',
        success: function (datosGrafico) {
            // Verifica si hay datos
            const hayGastos = datosGrafico && datosGrafico.labels && datosGrafico.labels.length > 0;
            const divGastos = document.getElementById('graficoGastosCol');
            const divIngresos = document.getElementById('graficoIngresosCol');

            if (!hayGastos) {
                // Oculta gastos
                if (divGastos) divGastos.setAttribute('hidden', 'hidden');
                // Haz que ingresos ocupe todo el ancho
                if (divIngresos) divIngresos.classList.remove('col-lg-6');
                if (divIngresos) divIngresos.classList.add('col-lg-12');
            } else {
                // Muestra gastos
                if (divGastos) divGastos.removeAttribute('hidden');
                // Restaura el ancho original de ingresos
                if (divIngresos) divIngresos.classList.remove('col-lg-12');
                if (divIngresos) divIngresos.classList.add('col-lg-6');
            }
            var ctxGastos = document.getElementById('graficoGastos').getContext('2d');
            // Procesar los datos para los últimos 3 y 6 meses
            const etiquetas = datosGrafico.labels;
            const dataGastosFijosUltimos3Meses = [];
            const dataGastosVariablesUltimos3Meses = [];
            const dataGastosFijosUltimos6Meses = [];
            const dataGastosVariablesUltimos6Meses = [];
            // Llenar los datos para los últimos 3 meses
            for (const naturaleza in datosGrafico.ultimos3meses) {
                for (const descripcion in datosGrafico.ultimos3meses[naturaleza]) {
                    const montos = datosGrafico.ultimos3meses[naturaleza][descripcion];
                    const total = Object.values(montos).reduce((a, b) => a + b, 0); // Sumar montos
                    if (naturaleza === 'fijo') {
                        dataGastosFijosUltimos3Meses.push(total);
                    } else if (naturaleza === 'variable') {
                        dataGastosVariablesUltimos3Meses.push(total);
                    }
                }
            }
            // Llenar los datos para los últimos 6 meses
            for (const naturaleza in datosGrafico.ultimos6meses) {
                for (const descripcion in datosGrafico.ultimos6meses[naturaleza]) {
                    const montos = datosGrafico.ultimos6meses[naturaleza][descripcion];
                    const total = Object.values(montos).reduce((a, b) => a + b, 0); // Sumar montos
                    if (naturaleza === 'fijo') {
                        dataGastosFijosUltimos6Meses.push(total);
                    } else if (naturaleza === 'variable') {
                        dataGastosVariablesUltimos6Meses.push(total);
                    }
                }
            }
            // Crear el gráfico
            new Chart(ctxGastos, {
                type: 'bar',
                data: {
                    labels: etiquetas,
                    datasets: [
                        {
                            label: 'Gastos Fijos - Últimos 3 meses',
                            data: dataGastosFijosUltimos3Meses,
                            backgroundColor: '#5271FF',
                            borderRadius: 8
                        },
                        {
                            label: 'Gastos Variables - Últimos 3 meses',
                            data: dataGastosVariablesUltimos3Meses,
                            backgroundColor: '#ed1c2a',
                            borderRadius: 8
                        },
                        {
                            label: 'Gastos Fijos - Últimos 6 meses',
                            data: dataGastosFijosUltimos6Meses,
                            backgroundColor: '#8770FA',
                            borderRadius: 8
                        },
                        {
                            label: 'Gastos Variables - Últimos 6 meses',
                            data: dataGastosVariablesUltimos6Meses,
                            backgroundColor: '#36A2EB',
                            borderRadius: 8
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                color: '#D1D1D1',
                                font: {
                                    size: function (context) {
                                        const width = context.chart.width;
                                        if (width > 600) return 17;
                                        if (width > 400) return 14;
                                        return 12;
                                    }
                                }
                            }
                        },
                        x: {
                            ticks: {
                                color: '#D1D1D1',
                                font: {
                                    size: function (context) {
                                        const width = context.chart.width;
                                        if (width > 600) return 17;
                                        if (width > 400) return 14;
                                        return 12;
                                    }
                                }
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                color: '#D1D1D1',
                                font: {
                                    size: function (context) {
                                        const width = context.chart.width;
                                        if (width > 600) return 17;
                                        if (width > 400) return 14;
                                        return 12;
                                    }
                                }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function (tooltipItem) {
                                    return `${tooltipItem.dataset.label}: $${tooltipItem.raw}`;
                                }
                            }
                        }
                    }
                }
            });
        },
        error: function (xhr, status, error) {
            console.log("Error al obtener los datos del gráfico:", error);
        }
    });
});