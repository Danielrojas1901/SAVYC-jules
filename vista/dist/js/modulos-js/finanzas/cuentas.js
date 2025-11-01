const CuentasTab = {
    grafico: null,

    initialize: function() {
        console.group('Inicializando CuentasTab');
        
        // configurar event listeners
        this.initializeEventListeners();
        
        // cargar cuentas contables
        this.cargarCuentasContables();
        
        console.groupEnd();
    },

    cargarCuentasContables: function() {
        // verificar si ya están cargadas en datos iniciales
        if (window.datosFinanzas?.cuentas_contables && window.datosFinanzas.cuentas_contables.length > 0) {
            this.llenarSelectCuentas(window.datosFinanzas.cuentas_contables);
            return;
        }

        // si no están en datos iniciales, cargarlas via AJAX
        $.ajax({
            url: 'index.php?pagina=finanzas',
            method: 'POST',
            data: {
                accion: 'obtener_cuentas_contables'
            },
            success: (response) => {
                if (response.success) {
                    this.llenarSelectCuentas(response.cuentas);
                } else {
                    console.error('Error al cargar cuentas:', response.message);
                }
            },
            error: (xhr, status, error) => {
                console.error('Error en petición de cuentas:', error);
            }
        });
    },

    llenarSelectCuentas: function(cuentas) {
        const $cuentaSelect = $('#cuenta');
        $cuentaSelect.empty();
        $cuentaSelect.append('<option value="" disabled selected>Seleccione una cuenta</option>');
        
        cuentas.forEach(cuenta => {
            $cuentaSelect.append(new Option(cuenta.nombre_cuenta, cuenta.cod_cuenta));
        });

        // seleccionar y cargar primera cuenta por defecto
        setTimeout(() => {
            const $primeraCuenta = $cuentaSelect.find('option:not([disabled]):first');
            
            if ($primeraCuenta.length) {
                $cuentaSelect.val($primeraCuenta.val()).trigger('change');
            }
        }, 100);
    },

    initializeEventListeners: function() {
        console.log('Configurando event listeners de CuentasTab');
        
        $('#cuenta').on('change', () => {
            this.actualizarDatos();
        });

        $('#mes-inicio, #mes-fin, #ano-inicio, #ano-fin').on('change', function() {
            const seccion = this.id.split('-')[1];
            DateUtils.validatePeriod(seccion);
            CuentasTab.actualizarDatos();
        });

        // evento para exportar PDF
        $('#exportar-cuentas-pdf').on('click', () => {
            this.exportarReporte();
        });
        
        console.log('Event listeners configurados');
    },

    actualizarDatos: function() {
        const cuenta = $('#cuenta').val();
        const mesInicio = $('#mes-inicio').val();
        const anoInicio = $('#ano-inicio').val();
        const mesFin = $('#mes-fin').val();
        const anoFin = $('#ano-fin').val();

        if (!cuenta || !mesInicio || !anoInicio || !mesFin || !anoFin) return;

        console.group('Petición de datos de cuenta');
        console.log('Parámetros:', {
            cuenta,
            mesInicio,
            anoInicio,
            mesFin,
            anoFin
        });

        $.ajax({
            url: 'index.php?pagina=finanzas',
            method: 'POST',
            data: {
                accion: 'obtener_movimientos_cuenta',
                cod_cuenta: cuenta,
                mes_inicio: mesInicio,
                año_inicio: anoInicio,
                mes_fin: mesFin,
                año_fin: anoFin
            },
            success: (response) => {
                console.log('Respuesta:', response);
                if (response.success) {
                    if (!response.datos || response.datos.length === 0) {
                        console.warn('No se recibieron datos');
                    }
                    this.actualizarGrafico(response.datos);
                } else {
                    console.error('Error al obtener datos:', response.message);
                }
            },
            error: (xhr, status, error) => {
                console.error('Error en la petición:', error);
                console.error('Estado:', status);
                console.error('Respuesta:', xhr.responseText);
            },
            complete: () => {
                console.groupEnd();
            }
        });
    },

    actualizarGrafico: function(datos) {
        let canvas = document.getElementById('grafico-cuentas');
        if (!canvas) {
            console.error('No se encontró el elemento canvas');
            return;
        }

        // Asegurarnos que es un elemento canvas
        if (!(canvas instanceof HTMLCanvasElement)) {
            const contenedor = document.getElementById('grafico-cuentas');
            // Crear nuevo canvas si el contenedor existe
            if (contenedor) {
                while (contenedor.firstChild) {
                    contenedor.removeChild(contenedor.firstChild);
                }
                const nuevoCanvas = document.createElement('canvas');
                contenedor.appendChild(nuevoCanvas);
                canvas = nuevoCanvas;
            } else {
                console.error('No se encontró el contenedor del gráfico');
                return;
            }
        }
        
        if (!datos || !datos.length) {
            if (this.grafico) {
                UtilidadesGraficos.destruirGrafico(this.grafico);
                this.grafico = null;
            }
            
            canvas.width = canvas.parentElement.offsetWidth || 800;
            canvas.height = 400;
            canvas.style.width = '100%';
            canvas.style.height = '400px';
            
            const context = canvas.getContext('2d');
            context.clearRect(0, 0, canvas.width, canvas.height);
            
            context.textAlign = 'center';
            context.textBaseline = 'middle';
            context.font = '16px Arial';
            context.fillStyle = '#6c757d';
            context.fillText('No hay datos para el período seleccionado', canvas.width / 2, canvas.height / 2);
            
            return;
        }

        const labels = datos.map(d => {
            const [año, mes] = d.periodo.split('-');
            const fecha = new Date(año, mes - 1);
            return fecha.toLocaleDateString('es-ES', { month: 'short', year: 'numeric' });
        });

        // determinar tipo de gráfico basado en cantidad de datos
        const tipoGrafico = labels.length === 1 ? 'bar' : 'line';
        const esBarras = tipoGrafico === 'bar';

        // configurar datasets con colores estandarizados
        const datasets = [
                {
                    label: 'Debe',
                    data: datos.map(d => d.debe),
                borderColor: 'rgba(94, 193, 211, 1)', // teal
                backgroundColor: esBarras ? 'rgba(94, 193, 211, 0.8)' : 'rgba(94, 193, 211, 0.2)',
                borderWidth: esBarras ? 2 : 1,
                ...(esBarras ? {} : {
                    tension: 0.4,
                    fill: true
                })
                },
                {
                    label: 'Haber',
                    data: datos.map(d => d.haber),
                borderColor: 'rgba(82, 113, 255, 1)', // azul
                backgroundColor: esBarras ? 'rgba(82, 113, 255, 0.8)' : 'rgba(82, 113, 255, 0.2)',
                borderWidth: esBarras ? 2 : 1,
                ...(esBarras ? {} : {
                    tension: 0.4,
                    fill: true
                })
                }
        ];

        if (this.grafico) {
            UtilidadesGraficos.destruirGrafico(this.grafico);
        }

        this.grafico = new Chart(canvas, {
            type: tipoGrafico,
            data: {
                labels: labels,
                datasets: datasets
            },
            options: UtilidadesGraficos.obtenerOpciones('Movimientos de la Cuenta', {
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Monto (Bs.)'
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
            })
        });
    },

    exportarReporte: function() {
        const cuenta = $('#cuenta').val();
        const cuentaNombre = $('#cuenta option:selected').text();
        
        if (!cuenta) {
            Swal.fire({
                icon: 'warning',
                title: 'Atención',
                text: 'Por favor seleccione una cuenta para generar el reporte'
            });
            return;
        }

        if (!this.grafico || !this.grafico.data || !this.grafico.data.labels.length) {
            Swal.fire({
                icon: 'warning',
                title: 'Sin datos',
                text: 'No hay datos disponibles para generar el reporte'
            });
            return;
        }

        // obtener datos del gráfico actual
        const labels = this.grafico.data.labels;
        const debeData = this.grafico.data.datasets.find(d => d.label === 'Debe').data;
        const haberData = this.grafico.data.datasets.find(d => d.label === 'Haber').data;

        // preparar datos para la tabla
        const tableData = labels.map((label, index) => {
            const debe = debeData[index] || 0;
            const haber = haberData[index] || 0;
            return {
                periodo: label,
                debe: debe,
                haber: haber
            };
        });

        // obtener período
        const mesInicio = $('#mes-inicio').val();
        const anoInicio = $('#ano-inicio').val();
        const mesFin = $('#mes-fin').val();
        const anoFin = $('#ano-fin').val();
        
        const meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
                      'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
        
        const periodo = `${meses[mesInicio - 1]} ${anoInicio} - ${meses[mesFin - 1]} ${anoFin}`;

        ExportarPDFs.exportToPDF('cuentas', {
            cuentaNombre: cuentaNombre,
            periodo: periodo,
            sourceCanvasId: 'grafico-cuentas',
            tableData: tableData,
            stats: this.generarEstadisticas(tableData)
        }).catch(error => {
            console.error('Error en exportación:', error);
        });
    },

    generarEstadisticas: function(tableData) {
        const stats = [];
        
        if (!tableData || tableData.length === 0) return stats;

        // calcular totales y promedios
        let totalDebe = 0;
        let totalHaber = 0;
        let periodosActivos = 0;
        let periodosConDebe = 0;
        let periodosConHaber = 0;
        let mayorDebe = { valor: 0, periodo: '' };
        let mayorHaber = { valor: 0, periodo: '' };

        tableData.forEach(row => {
            const debe = parseFloat(row.debe) || 0;
            const haber = parseFloat(row.haber) || 0;

            totalDebe += debe;
            totalHaber += haber;

            // contar períodos con actividad (debe O haber, no ambos sumados)
            if (debe > 0 || haber > 0) periodosActivos++;
            if (debe > 0) periodosConDebe++;
            if (haber > 0) periodosConHaber++;

            // encontrar mayores valores
            if (debe > mayorDebe.valor) {
                mayorDebe = { valor: debe, periodo: row.periodo };
            }
            
            if (haber > mayorHaber.valor) {
                mayorHaber = { valor: haber, periodo: row.periodo };
            }
        });

        const totalPeriodos = tableData.length;
        const promedioDebe = totalDebe / totalPeriodos;
        const promedioHaber = totalHaber / totalPeriodos;

        // generar estadísticas
        stats.push({
            title: 'Resumen de Movimientos',
            value: `Total Debe: ${formatearMoneda(totalDebe)} | Total Haber: ${formatearMoneda(totalHaber)}`
        });

        stats.push({
            title: 'Promedios Mensuales',
            value: `Promedio Debe: ${formatearMoneda(promedioDebe)} | Promedio Haber: ${formatearMoneda(promedioHaber)}`
        });

        stats.push({
            title: 'Análisis de Actividad',
            value: `${periodosActivos} de ${totalPeriodos} períodos con movimientos (${((periodosActivos/totalPeriodos)*100).toFixed(1)}%)`
        });

        if (periodosConDebe > 0) {
            stats.push({
                title: 'Períodos con Movimientos Debe',
                value: `${periodosConDebe} períodos (${((periodosConDebe/totalPeriodos)*100).toFixed(1)}%)`
            });
        }

        if (periodosConHaber > 0) {
            stats.push({
                title: 'Períodos con Movimientos Haber',
                value: `${periodosConHaber} períodos (${((periodosConHaber/totalPeriodos)*100).toFixed(1)}%)`
            });
        }

        if (mayorDebe.valor > 0) {
            stats.push({
                title: 'Mayor Movimiento Debe',
                value: `${formatearMoneda(mayorDebe.valor)} en ${mayorDebe.periodo}`
            });
        }

        if (mayorHaber.valor > 0) {
            stats.push({
                title: 'Mayor Movimiento Haber',
                value: `${formatearMoneda(mayorHaber.valor)} en ${mayorHaber.periodo}`
            });
        }

        // análisis de distribución debe vs haber (solo si hay movimientos)
        const totalVolumen = totalDebe + totalHaber;
        if (totalVolumen > 0) {
            const porcentajeDebe = (totalDebe / totalVolumen) * 100;
            const porcentajeHaber = (totalHaber / totalVolumen) * 100;
            stats.push({
                title: 'Distribución de Volumen',
                value: `${porcentajeDebe.toFixed(1)}% Debe | ${porcentajeHaber.toFixed(1)}% Haber`
            });
        }

        // análisis de actividad más preciso
        if (mayorDebe.valor > 0 && mayorHaber.valor > 0) {
            const periodoMayorDebe = mayorDebe.periodo;
            const periodoMayorHaber = mayorHaber.periodo;
            
            if (periodoMayorDebe === periodoMayorHaber) {
                stats.push({
                    title: 'Período de Mayor Actividad',
                    value: `${periodoMayorDebe} (Mayor Debe: ${formatearMoneda(mayorDebe.valor)} | Mayor Haber: ${formatearMoneda(mayorHaber.valor)})`
                });
            } else {
                stats.push({
                    title: 'Períodos de Mayor Actividad',
                    value: `Mayor Debe en ${periodoMayorDebe} (${formatearMoneda(mayorDebe.valor)}) | Mayor Haber en ${periodoMayorHaber} (${formatearMoneda(mayorHaber.valor)})`
                });
            }
        }

        // análisis de tendencias
        if (tableData.length >= 3) {
            const ultimosTres = tableData.slice(-3);
            const tendenciaDebe = ultimosTres[2].debe - ultimosTres[0].debe;
            const tendenciaHaber = ultimosTres[2].haber - ultimosTres[0].haber;
            
            if (Math.abs(tendenciaDebe) > 0.01) {
                const direccionDebe = tendenciaDebe > 0 ? 'incremento' : 'decremento';
                stats.push({
                    title: 'Tendencia Debe (últimos 3 períodos)',
                    value: `${direccionDebe} de ${formatearMoneda(Math.abs(tendenciaDebe))}`
                });
            }
            
            if (Math.abs(tendenciaHaber) > 0.01) {
                const direccionHaber = tendenciaHaber > 0 ? 'incremento' : 'decremento';
                stats.push({
                    title: 'Tendencia Haber (últimos 3 períodos)',
                    value: `${direccionHaber} de ${formatearMoneda(Math.abs(tendenciaHaber))}`
                });
            }
        }

        return stats;
    }
}; 