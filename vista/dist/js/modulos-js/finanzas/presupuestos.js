const PresupuestosTab = {
    tabla: null,
    grafico: null,
    graficoDetalle: null,
    tablaDetalle: null,

    initialize: function() {
        console.group('Inicializando PresupuestosTab');
        console.log('Datos disponibles:', {
            presupuestos: window.datosFinanzas?.presupuestos?.length || 0,
            datos_presupuestos: !!window.datosFinanzas?.datos_presupuestos,
            categorias_gasto: window.datosFinanzas?.categorias_gasto?.length || 0
        });
        
        this.initializeRegistroModal();
        this.initializePeriodSelectors();
        this.initializeEventListeners();
        
        // SIEMPRE cargar datos actualizados via AJAX
        this.cargarDatosPresupuestos();
        
        console.groupEnd();
    },

    cargarDatosPresupuestos: function() {
        console.log('Cargando datos de presupuestos via AJAX...');
        
        $.ajax({
            url: 'index.php?pagina=finanzas',
            method: 'POST',
            data: {
                accion: 'obtener_presupuestos'
            },
            success: (response) => {
                if (response.success) {
                    window.datosFinanzas = window.datosFinanzas || {};
                    window.datosFinanzas.presupuestos = response.presupuestos;
                    
                    console.log('Datos de presupuestos cargados exitosamente:', response.presupuestos);
                    console.log('Cantidad de registros:', response.presupuestos?.length || 0);
                    
                    // ahora inicializar la tabla con los datos correctos
                    this.initializeTable();
                    
                    // cargar datos del gráfico por separado
                    this.actualizarDatos();
                } else {
                    console.error('Error al cargar presupuestos:', response.message);
                    this.initializeTable(); // inicializar tabla vacía
                }
            },
            error: (xhr, status, error) => {
                console.error('Error en petición de presupuestos:', error);
                this.initializeTable(); // inicializar tabla vacía
            }
        });
    },

    initializeRegistroModal: function() {
        const $select = $('#categoria-presupuesto');
        $select.empty();
        $select.append(new Option('Seleccione una categoría', '', true, true));
        
        if (window.datosFinanzas?.categorias_gasto) {
            window.datosFinanzas.categorias_gasto.forEach(categoria => {
                $select.append(new Option(categoria.nombre, categoria.cod_cat_gasto));
            });
        }

        const fechaActual = new Date();
        const siguienteMes = new Date(fechaActual.setMonth(fechaActual.getMonth() + 1));
        const añoActual = siguienteMes.getFullYear();
        const numeroMesSiguiente = siguienteMes.getMonth() + 1;

        const $yearSelect = $('#año-presupuesto');
        $yearSelect.empty();
        for (let year = añoActual - 1; year <= añoActual + 1; year++) {
            $yearSelect.append(new Option(year, year));
        }
        $yearSelect.val(añoActual);

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

        const $mesSelect = $('#mes-presupuesto');
        $mesSelect.empty();
        meses.forEach(mes => {
            $mesSelect.append(new Option(mes.name, mes.value));
        });
        $mesSelect.val(numeroMesSiguiente);
    },

    validarRangoFechas: function(esDetalle = false) {
        const prefijo = esDetalle ? '-detalle' : '-vis';
        const mesInicio = parseInt($(`#mes-inicio${prefijo}`).val());
        const añoInicio = parseInt($(`#año-inicio${prefijo}`).val());
        const mesFin = parseInt($(`#mes-fin${prefijo}`).val());
        const añoFin = parseInt($(`#año-fin${prefijo}`).val());

        const fechaInicio = new Date(añoInicio, mesInicio - 1);
        const fechaFin = new Date(añoFin, mesFin - 1);

        if (fechaFin < fechaInicio) {
            Swal.fire({
                icon: 'error',
                title: 'Error en el rango de fechas',
                text: 'La fecha de inicio no puede ser posterior a la fecha final',
                confirmButtonText: 'Entendido'
            });
            
            const fechaActual = new Date();
            const mesActual = fechaActual.getMonth() + 1;
            const añoActual = fechaActual.getFullYear();
            
            let mesInicio = mesActual - 5;
            let añoInicio = añoActual;
            if (mesInicio <= 0) {
                mesInicio += 12;
                añoInicio--;
            }
            
            $(`#mes-inicio${prefijo}`).val(mesInicio);
            $(`#año-inicio${prefijo}`).val(añoInicio);
            $(`#mes-fin${prefijo}`).val(mesActual);
            $(`#año-fin${prefijo}`).val(añoActual);
            
            return false;
        }
        return true;
    },

    initializeTable: function() {
        console.group('Initializing Presupuestos Table');
        console.log('Initial data:', window.datosFinanzas?.presupuestos);
        console.log('Data length:', window.datosFinanzas?.presupuestos?.length || 0);
        
        this.tabla = TableUtils.initializeTable('#tabla-presupuestos', {
            columns: [
                TableUtils.createTextColumn('categoria'),
                {
                    data: 'presupuesto',
                    className: 'text-end',
                    render: function(data, tipo, fila) {
                        if (tipo === 'display') {
                            if (data === null || data === undefined) {
                                return `<button class="btn btn-sm btn-outline-primary registrar-presupuesto-mes" 
                                    data-id="${fila.cod_cat_gasto}" 
                                    data-categoria="${fila.categoria}">
                                    <i class="fas fa-plus-circle"></i> Registrar Presupuesto
                                </button>`;
                            }
                            return formatearMoneda(data);
                        }
                        return data;
                    }
                },
                TableUtils.createMoneyColumn('gasto_real'),
                {
                    data: 'diferencia',
                    className: 'text-end',
                    render: function(data, tipo, fila) {
                        if (tipo === 'display') {
                            if (fila.presupuesto === null || fila.presupuesto === undefined) {
                                return '-';
                            }
                            const valor = formatearMoneda(data);
                            return `<span class="text-${data >= 0 ? 'success' : 'danger'}">${valor}</span>`;
                        }
                        return data;
                    }
                },
                {
                    data: 'porcentaje_utilizado',
                    className: 'text-end',
                    render: function(data, tipo, fila) {
                        if (tipo === 'display') {
                            if (fila.presupuesto === null || fila.presupuesto === undefined || data === null) {
                                return '-';
                            }
                            return formatearPorcentaje(data);
                        }
                        return data;
                    }
                },
                {
                    data: 'estado',
                    className: 'text-center',
                    render: function(data, tipo, fila) {
                        if (tipo === 'display') {
                            if (fila.presupuesto === null || fila.presupuesto === undefined || data === null) {
                                return '-';
                            }
                            const texto = data === 'success' ? 'Dentro del Presupuesto' : 'Excedido';
                            return `<span class="badge bg-${data}">${texto}</span>`;
                        }
                        return data;
                    }
                },
                {
                    data: null,
                    className: 'text-center',
                    render: function(data, tipo, fila) {
                        if (tipo === 'display') {
                            if (fila.presupuesto === null || fila.presupuesto === undefined) {
                                return '';
                            }
                            const fechaActual = new Date();
                            const fechaFormato = `${fechaActual.getFullYear()}-${String(fechaActual.getMonth() + 1).padStart(2, '0')}`;
                            let buttons = '<div class="btn-group">';
                            
                            // Permiso de consultar para ver detalles
                            if (window.permisos?.finanza?.consultar) {
                                buttons += `<button class="btn btn-sm btn-info ver-detalle-presupuesto" data-id="${fila.cod_cat_gasto}">
                                    <i class="fas fa-chart-line"></i>
                                </button>`;
                            }
                            
                            // Permiso de editar
                            if (window.permisos?.finanza?.editar) {
                                buttons += `<button class="btn btn-sm btn-warning editar-presupuesto" data-id="${fila.cod_cat_gasto}" data-mes="${fechaFormato}" data-monto="${fila.presupuesto}" data-notas="${fila.notas || ''}">
                                    <i class="fas fa-edit"></i>
                                </button>`;
                            }

                            buttons += '</div>';
                            return buttons;
                        }
                        return data;
                    }
                }
            ],
            data: window.datosFinanzas?.presupuestos || []
        }, [], 'tabla presupuestos');
        
        console.groupEnd();

        $('#tabla-presupuestos').on('click', '.registrar-presupuesto-mes', (e) => {
            const btn = $(e.currentTarget);
            const codCatGasto = btn.data('id');
            const categoria = btn.data('categoria');
            
            $('#categoria-presupuesto').val(codCatGasto);
            
            const fechaActual = new Date();
            const mesActual = fechaActual.getMonth() + 1;
            const añoActual = fechaActual.getFullYear();
            
            $('#mes-presupuesto').val(mesActual);
            $('#año-presupuesto').val(añoActual);
            
            $('#modal-registro-presupuesto').modal('show');
        });
    },

    initializePeriodSelectors: function() {
        const fechaActual = new Date();
        const añoActual = fechaActual.getFullYear();
        const mesActual = fechaActual.getMonth() + 1;

        const yearSelectors = ['#año-inicio-vis', '#año-fin-vis', '#año-inicio-detalle', '#año-fin-detalle'];
        yearSelectors.forEach(selector => {
            const $select = $(selector);
            $select.empty();
            for (let año = añoActual - 1; año <= añoActual + 1; año++) {
                $select.append(new Option(año, año));
            }
        });

        $('#año-inicio-vis, #año-inicio-detalle').val(añoActual);
        $('#año-fin-vis, #año-fin-detalle').val(añoActual);

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

        const selectoresMeses = ['#mes-inicio-vis', '#mes-fin-vis', '#mes-inicio-detalle', '#mes-fin-detalle'];
        selectoresMeses.forEach(selector => {
            const $select = $(selector);
            $select.empty();
            meses.forEach(mes => {
                $select.append(new Option(mes.name, mes.value));
            });
        });

        let mesInicio = mesActual - 5;
        let añoInicio = añoActual;
        if (mesInicio <= 0) {
            mesInicio += 12;
            añoInicio--;
            $('#año-inicio-vis, #año-inicio-detalle').val(añoInicio);
        }
        $('#mes-inicio-vis, #mes-inicio-detalle').val(mesInicio);
        $('#mes-fin-vis, #mes-fin-detalle').val(mesActual);

        this.actualizarDatos();
    },

    initializeEventListeners: function() {
        $('#mes-inicio-vis, #año-inicio-vis, #mes-fin-vis, #año-fin-vis').on('change', () => {
            if (this.validarRangoFechas(false)) {
                this.actualizarDatos();
            }
        });

        $('#tabla-presupuestos').on('click', '.ver-detalle-presupuesto', (e) => {
            const codCatGasto = $(e.currentTarget).data('id');
            this.mostrarDetalle(codCatGasto);
        });

        $('#tabla-presupuestos').on('click', '.editar-presupuesto', (e) => {
            const btn = $(e.currentTarget);
            const codCatGasto = btn.data('id');
            const mes = btn.data('mes');
            const monto = btn.data('monto');
            const notas = btn.data('notas');

            $('#edit-cod-cat-gasto').val(codCatGasto);
            $('#edit-mes').val(mes);
            $('#edit-monto-presupuesto').val(monto);
            $('#edit-descripcion-presupuesto').val(notas);

            $('#modal-editar-presupuesto').modal('show');
        });

        $('#tabla-presupuestos').on('click', '.eliminar-presupuesto', (e) => {
            const btn = $(e.currentTarget);
            const codCatGasto = btn.data('id');
            const mes = btn.data('mes');

            // fecha como YYYY-MM-01
            const [year, month] = mes.split('-');
            const formattedDate = `${year}-${month}-01`;

            $('#btn-confirmar-eliminar').data('cod-cat-gasto', codCatGasto);
            $('#btn-confirmar-eliminar').data('mes', formattedDate);
            $('#modal-eliminar-presupuesto').modal('show');
        });

        $('#form-editar-presupuesto').on('submit', (e) => {
            e.preventDefault();
            const self = this;
            
            const mes = $('#edit-mes').val();
            const [year, month] = mes.split('-');
            const fechaFormato = `${year}-${month}-01`;
            
            const formData = {
                cod_cat_gasto: $('#edit-cod-cat-gasto').val(),
                mes: fechaFormato,
                monto: parseFloat($('#edit-monto-presupuesto').val()),
                descripcion: $('#edit-descripcion-presupuesto').val()
            };

            console.group('Editando presupuesto');
            console.log('Datos a enviar:', formData);

            if (!formData.cod_cat_gasto || !formData.mes || !formData.monto) {
                console.error('Faltan campos requeridos:', formData);
                console.groupEnd();
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Por favor complete todos los campos requeridos'
                });
                return;
            }

            if (isNaN(formData.monto) || formData.monto <= 0) {
                console.error('Monto inválido:', formData.monto);
                console.groupEnd();
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'El monto debe ser un número positivo'
                });
                return;
            }

            $.ajax({
                url: 'index.php?pagina=finanzas',
                method: 'POST',
                data: {
                    accion: 'editar_presupuesto',
                    ...formData
                },
                success: (response) => {
                    console.log('Respuesta del servidor:', response);
                    if (response.success) {
                        console.log('Edición exitosa');
                        console.groupEnd();
                        
                        $.ajax({
                            url: 'index.php?pagina=finanzas',
                            method: 'POST',
                            data: {
                                accion: 'obtener_presupuestos'
                            },
                            success: (response) => {
                                if (response.success) {
                                    self.tabla.clear();
                                    self.tabla.rows.add(response.presupuestos);
                                    self.tabla.draw();
                                }
                                self.actualizarDatos();
                            },
                            error: (xhr, status, error) => {
                                console.error('Error al actualizar tabla:', error);
                                self.actualizarDatos();
                            }
                        });

                        Swal.fire({
                            icon: 'success',
                            title: 'Éxito',
                            text: response.message
                        });
                        $('#modal-editar-presupuesto').modal('hide');
                    } else {
                        console.error('Error en la edición:', response.message);
                        console.groupEnd();
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message
                        });
                    }
                },
                error: (xhr, status, error) => {
                    console.error('Error al editar presupuesto:', {xhr, status, error});
                    console.groupEnd();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error al comunicarse con el servidor'
                    });
                }
            });
        });

        $('#btn-confirmar-eliminar').on('click', () => {
            const self = this;
            const codCatGasto = $('#btn-confirmar-eliminar').data('cod-cat-gasto');
            const mes = $('#btn-confirmar-eliminar').data('mes');

            console.group('Eliminando presupuesto');
            console.log('Datos a enviar:', {codCatGasto, mes});

            if (!codCatGasto || !mes) {
                console.error('Datos inválidos para eliminar:', {codCatGasto, mes});
                console.groupEnd();
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Datos inválidos para eliminar el presupuesto'
                });
                return;
            }

            $.ajax({
                url: 'index.php?pagina=finanzas',
                method: 'POST',
                data: {
                    accion: 'eliminar_presupuesto',
                    cod_cat_gasto: codCatGasto,
                    mes: mes
                },
                success: (response) => {
                    console.log('Respuesta del servidor:', response);
                    if (response.success) {
                        console.log('Eliminación exitosa');
                        console.groupEnd();
                        Swal.fire({
                            icon: 'success',
                            title: 'Éxito',
                            text: response.message
                        });
                        $('#modal-eliminar-presupuesto').modal('hide');
                        self.actualizarDatos();
                    } else {
                        console.error('Error en la eliminación:', response.message);
                        console.groupEnd();
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message
                        });
                    }
                },
                error: (xhr, status, error) => {
                    console.error('Error al eliminar presupuesto:', {xhr, status, error});
                    console.groupEnd();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error al comunicarse con el servidor'
                    });
                }
            });
        });

        $('#form-registro-presupuesto').on('submit', (e) => {
            e.preventDefault();
            this.registrarPresupuesto();
        });

        $('#mes-inicio-detalle, #año-inicio-detalle, #mes-fin-detalle, #año-fin-detalle').on('change', () => {
            const codCatGasto = $('#modal-detalle-presupuesto').data('cod-cat-gasto');
            if (this.validarRangoFechas(true)) {
                this.actualizarDatosDetalle(codCatGasto);
            }
        });

        $('#categoria-presupuesto, #mes-presupuesto, #año-presupuesto').on('change', () => {
            this.validarPresupuestoExistente();
        });

        // evento para exportar reporte general
        $('#exportar-presupuestos-general-pdf').on('click', () => {
            this.exportarReporteGeneral();
        });
    },

    validarPresupuestoExistente: function() {
        const categoria = $('#categoria-presupuesto').val();
        const mes = $('#mes-presupuesto').val();
        const año = $('#año-presupuesto').val();

        if (!categoria || !mes || !año) return;

        const fecha = `${año}-${String(mes).padStart(2, '0')}-01`;

        $.ajax({
            url: 'index.php?pagina=finanzas',
            method: 'POST',
            dataType: 'json',
            data: {
                accion: 'validar_presupuesto_existente',
                cod_cat_gasto: categoria,
                mes: fecha
            },
            success: (response) => {
                if (response && response.exists) {
                    const fechaActual = new Date();
                    const unMesDespues = new Date(fechaActual.setMonth(fechaActual.getMonth() + 1));
                    const nuevoMes = unMesDespues.getMonth() + 1;
                    const nuevoAño = unMesDespues.getFullYear();

                    Swal.fire({
                        icon: 'warning',
                        title: 'Presupuesto existente',
                        text: 'Ya existe un presupuesto para esta categoría en el mes seleccionado. Seleccione otro mes o año.'
                    });

                    $('#mes-presupuesto, #año-presupuesto').off('change');
                    
                    $('#mes-presupuesto').val(nuevoMes);
                    $('#año-presupuesto').val(nuevoAño);
                    
                    setTimeout(() => {
                        $('#mes-presupuesto, #año-presupuesto').on('change', () => {
                            this.validarPresupuestoExistente();
                        });
                    }, 100);
                }
            },
            error: (xhr, status, error) => {
                console.error('Error al validar presupuesto:', error);
            }
        });
    },

    initializeGrafico: function() {
        console.group('Inicializando Gráfico Presupuestos');
        const ctx = document.getElementById('grafico-presupuestos');
        console.log('Canvas:', ctx);
        console.log('Datos disponibles:', window.datosFinanzas?.datos_presupuestos);

        if (!ctx || !window.datosFinanzas?.datos_presupuestos) {
            console.warn('No se puede inicializar el gráfico:', {
                canvasExiste: !!ctx,
                datosExisten: !!window.datosFinanzas?.datos_presupuestos
            });
            console.groupEnd();
            return;
        }

        if (this.grafico) {
            console.log('Destruyendo gráfico existente');
            UtilidadesGraficos.destruirGrafico(this.grafico);
        }

        const datos = {
            labels: window.datosFinanzas.datos_presupuestos.labels || [],
            presupuesto: window.datosFinanzas.datos_presupuestos.presupuesto || [],
            gasto_real: window.datosFinanzas.datos_presupuestos.gasto_real || []
        };

        console.log('Datos formateados para el gráfico:', datos);

        ctx.parentElement.style.height = '40vh';
        ctx.parentElement.style.minHeight = '300px';

        this.grafico = UtilidadesGraficos.inicializarGraficoPresupuesto(
            ctx,
            datos,
            'Presupuesto Global',
            {
                maintainAspectRatio: false,
                responsive: true
            }
        );
        
        console.log('Gráfico inicializado:', this.grafico);
        console.groupEnd();
    },

    actualizarGrafico: function(datos) {
        console.log('Actualizando gráfico con datos:', datos);
        
        if (this.grafico) {
            UtilidadesGraficos.destruirGrafico(this.grafico);
        }

        const ctx = document.getElementById('grafico-presupuestos');
        if (!ctx) {
            console.error('No se encontró el elemento canvas');
            return;
        }

        ctx.parentElement.style.height = '40vh';
        ctx.parentElement.style.minHeight = '300px';

        this.grafico = UtilidadesGraficos.inicializarGraficoPresupuesto(
            ctx,
            {
                labels: datos.labels,
                presupuesto: datos.presupuesto,
                gasto_real: datos.gasto_real
            },
            'Presupuesto Global',
            {
                maintainAspectRatio: false,
                responsive: true
            }
        );
    },

    actualizarDatos: function() {
        console.group('Actualizando datos presupuestos');
        const mesInicio = $('#mes-inicio-vis').val();
        const añoInicio = $('#año-inicio-vis').val();
        const mesFin = $('#mes-fin-vis').val();
        const añoFin = $('#año-fin-vis').val();
        
        console.group('Actualizando datos presupuesto global');
        console.log('Parámetros:', {
            mesInicio,
            añoInicio,
            mesFin,
            añoFin
        });

        $.ajax({
            url: 'index.php?pagina=finanzas',
            method: 'POST',
            dataType: 'json',
            data: {
                accion: 'obtener_datos_grafico_presupuestos',
                tipo: 'global',
                mes_inicio: mesInicio,
                año_inicio: añoInicio,
                mes_fin: mesFin,
                año_fin: añoFin
            },
            success: (response) => {
                console.log('Respuesta del servidor:', response);
                if (response && response.success && response.datos) {
                    if (response.datos.labels && response.datos.labels.length > 0) {
                        console.log('Datos para el gráfico:', response.datos);
                        window.datosFinanzas.datos_presupuestos = response.datos;
                        if (!this.grafico) {
                            this.initializeGrafico();
                        } else {
                            this.actualizarGrafico(response.datos);
                        }
                    } else {
                        console.warn('No hay datos para mostrar en el gráfico');
                        this.mostrarMensajeNoData();
                    }
                } else {
                    console.error('Error en la respuesta:', response);
                    this.mostrarMensajeError();
                }
            },
            error: (xhr, status, error) => {
                console.error('Error en la petición:', error);
                console.error('Estado:', status);
                console.error('Respuesta:', xhr.responseText);
                this.mostrarMensajeError();
            },
            complete: () => {
                console.groupEnd();
            }
        });
    },

    mostrarMensajeNoData: function() {
        const ctx = document.getElementById('grafico-presupuestos');
        if (!ctx) return;

        if (this.grafico) {
            UtilidadesGraficos.destruirGrafico(this.grafico);
        }

        ctx.parentElement.style.height = '40vh';
        ctx.parentElement.style.minHeight = '300px';
        ctx.width = ctx.parentElement.offsetWidth;
        ctx.height = ctx.parentElement.offsetHeight;
        
        const context = ctx.getContext('2d');
        context.clearRect(0, 0, ctx.width, ctx.height);
        
        context.textAlign = 'center';
        context.textBaseline = 'middle';
        context.font = '16px Arial';
        context.fillStyle = '#6c757d';
        context.fillText('No hay datos disponibles para el periodo seleccionado', ctx.width / 2, ctx.height / 2);
    },

    mostrarMensajeError: function() {
        const ctx = document.getElementById('grafico-presupuestos');
        if (!ctx) return;

        if (this.grafico) {
            UtilidadesGraficos.destruirGrafico(this.grafico);
        }

        ctx.parentElement.style.height = '40vh';
        ctx.parentElement.style.minHeight = '300px';
        ctx.width = ctx.parentElement.offsetWidth;
        ctx.height = ctx.parentElement.offsetHeight;
        
        const context = ctx.getContext('2d');
        context.clearRect(0, 0, ctx.width, ctx.height);
        
        context.textAlign = 'center';
        context.textBaseline = 'middle';
        context.font = '16px Arial';
        context.fillStyle = '#dc3545';
        context.fillText('Error al cargar los datos del gráfico', ctx.width / 2, ctx.height / 2);
    },

    actualizarDatosDetalle: function(codCatGasto) {
        const mesInicio = $('#mes-inicio-detalle').val();
        const añoInicio = $('#año-inicio-detalle').val();
        const mesFin = $('#mes-fin-detalle').val();
        const añoFin = $('#año-fin-detalle').val();

        $.ajax({
            url: 'index.php?pagina=finanzas',
            method: 'POST',
            dataType: 'json',
            data: {
                accion: 'obtener_datos_grafico_presupuestos',
                tipo: 'categoria',
                categoria: codCatGasto,
                mes_inicio: mesInicio,
                año_inicio: añoInicio,
                mes_fin: mesFin,
                año_fin: añoFin
            },
            success: (response) => {
                if (response && response.success && response.datos) {
                    this.actualizarGraficoDetalle(response.datos);
                    this.actualizarTablaDetalle(response.datos);
                } else {
                    console.error('Error en la respuesta:', response);
                }
            },
            error: (xhr, status, error) => {
                console.error('Error en la petición:', error);
            }
        });
    },

    actualizarTablaDetalle: function(datos) {
        try {
            console.group('Debug actualizarTablaDetalle');
            console.log('Datos recibidos:', datos);
            
            const mapMesIngles = {
                'Dec': 'Diciembre', 'Jan': 'Enero', 'Feb': 'Febrero', 'Mar': 'Marzo',
                'Apr': 'Abril', 'May': 'Mayo', 'Jun': 'Junio', 'Jul': 'Julio',
                'Aug': 'Agosto', 'Sep': 'Septiembre', 'Oct': 'Octubre', 'Nov': 'Noviembre'
            };

            const mapMes = {
                'Enero': 1, 'Febrero': 2, 'Marzo': 3, 'Abril': 4, 'Mayo': 5, 'Junio': 6,
                'Julio': 7, 'Agosto': 8, 'Septiembre': 9, 'Octubre': 10, 'Noviembre': 11, 'Diciembre': 12
            };
            
            const tableData = datos.labels.map((mes, index) => {
                console.log('Procesando mes:', mes);
                const presupuesto = datos.presupuesto[index];
                const gastoReal = datos.gasto_real[index];
                
                if ((presupuesto === 0 || presupuesto === null) && 
                    (gastoReal === 0 || gastoReal === null)) {
                    return null;
                }
                
                const [mesIngles, año] = mes.split(' ');
                console.log('Mes parseado:', { mesIngles, año });
                
                const nombreMes = mapMesIngles[mesIngles];
                if (!nombreMes) {
                    console.error('Mes en inglés no reconocido:', mesIngles);
                    return null;
                }
                
                const indiceMes = mapMes[nombreMes];
                if (!indiceMes) {
                    console.error('Nombre de mes inválido:', nombreMes);
                    console.log('Meses válidos:', Object.keys(mapMes));
                    return null;
                }
                
                const fechaOrdenar = `${año}-${String(indiceMes).padStart(2, '0')}-01`;
                const mesMostrar = `${nombreMes} ${año}`;
                
                const diferencia = presupuesto - gastoReal;
                const estado = presupuesto === 0 ? 'danger' : (diferencia >= 0 ? 'success' : 'danger');
                
                const row = {
                    fecha_orden: fechaOrdenar,
                    mes: mesMostrar,
                    presupuesto: presupuesto,
                    gasto_real: gastoReal,
                    diferencia: diferencia,
                    porcentaje: presupuesto > 0 ? (gastoReal / presupuesto * 100) : null,
                    estado: estado
                };
                console.log('Fila generada:', row);
                return row;
            }).filter(fila => fila !== null);

            console.log('Datos para la tabla:', tableData);
            console.groupEnd();

            this.tablaDetalle = TableUtils.initializeTable('#tabla-detalle-presupuesto', {
                destroy: true,
                data: tableData,
                columns: [
                    {
                        data: 'fecha_orden',
                        visible: false // columna solo para ordenar, es oculta
                    },
                    TableUtils.createTextColumn('mes'),
                    TableUtils.createMoneyColumn('presupuesto'),
                    TableUtils.createMoneyColumn('gasto_real'),
                    {
                        data: 'diferencia',
                        className: 'text-end',
                        render: function(data, tipo) {
                            if (tipo === 'display') {
                                const valor = formatearMoneda(data);
                                return `<span class="text-${data >= 0 ? 'success' : 'danger'}">${valor}</span>`;
                            }
                            return data;
                        }
                    },
                    TableUtils.createPercentageColumn('porcentaje'),
                    {
                        data: 'estado',
                        className: 'text-center',
                        render: function(data, tipo, fila) {
                            if (tipo === 'display') {
                                if (fila.presupuesto === 0) {
                                    return '-';
                                }
                                const texto = data === 'success' ? 'Dentro del Presupuesto' : 'Excedido';
                                return `<span class="badge bg-${data}">${texto}</span>`;
                            }
                            return data;
                        }
                    }
                ],
                order: [[0, 'asc']] // ordenar por la columna oculta
            }, [], 'tabla detalle presupuesto');

        } catch (error) {
            console.error('Error al actualizar tabla detalle:', error);
        }
    },

    actualizarGraficoDetalle: function(datos) {
        if (this.graficoDetalle) {
            UtilidadesGraficos.destruirGrafico(this.graficoDetalle);
        }

        const ctx = document.getElementById('grafico-detalle-presupuesto');
        if (!ctx) {
            console.error('No se encontró el elemento canvas del detalle');
            return;
        }

        ctx.parentElement.style.width = '100%';

        this.graficoDetalle = UtilidadesGraficos.inicializarGraficoPresupuesto(
            ctx,
            {
                labels: datos.labels,
                presupuesto: datos.presupuesto,
                gasto_real: datos.gasto_real
            },
            datos.categoria,
            {
                maintainAspectRatio: false,
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                }
            }
        );
    },

    mostrarDetalle: function(codCatGasto) {
        const categoria = window.datosFinanzas.categorias_gasto.find(c => c.cod_cat_gasto === codCatGasto);
        if (!categoria) {
            console.error('Categoría no encontrada:', codCatGasto);
            return;
        }

        $('#mes-inicio-detalle').val($('#mes-inicio-vis').val());
        $('#año-inicio-detalle').val($('#año-inicio-vis').val());
        $('#mes-fin-detalle').val($('#mes-fin-vis').val());
        $('#año-fin-detalle').val($('#año-fin-vis').val());

        $('#modal-detalle-presupuesto').data('cod-cat-gasto', codCatGasto);
        $('#modal-detalle-presupuesto-label').text(`Detalle de Presupuesto - ${categoria.nombre}`);
        $('#detalle-categoria-titulo').text(`Categoría: ${categoria.nombre}`);

        $('#modal-detalle-presupuesto').modal('show');

        if (this.tablaDetalle) {
            try {
                this.tablaDetalle.destroy();
            } catch (error) {
                console.warn('Error al destruir tabla:', error);
            }
            this.tablaDetalle = null;
        }

        $('#tabla-detalle-presupuesto').html(`
            <thead>
                <tr>
                    <th style="display:none;">Orden</th>
                    <th>Mes</th>
                    <th class="text-end">Presupuesto</th>
                    <th class="text-end">Gasto Real</th>
                    <th class="text-end">Diferencia</th>
                    <th class="text-end">% Utilizado</th>
                    <th class="text-center">Estado</th>
                </tr>
            </thead>
            <tbody></tbody>
        `);

        this.actualizarDatosDetalle(codCatGasto);

        // volver aainicializar el evento del boton de exportacion
        $('#exportar-pdf').off('click').on('click', () => {
            ExportarPDFs.exportToPDF('presupuesto', {
                categoria: categoria.nombre,
                mesInicio: $('#mes-inicio-detalle').val(),
                añoInicio: $('#año-inicio-detalle').val(),
                mesFin: $('#mes-fin-detalle').val(),
                añoFin: $('#año-fin-detalle').val(),
                sourceCanvasId: 'grafico-detalle-presupuesto',
                tableData: this.tablaDetalle.rows().data().toArray(),
                stats: this.generarEstadisticas()
            }).catch(error => {
                console.error('Error en exportación:', error);
            });
        });
    },

    generarEstadisticas: function() {
        const stats = [];
        let totalMeses = 0;
        let mesesDentroPresupuesto = 0;
        let mayorExceso = 0;
        let mayorAhorro = 0;
        let totalPresupuestado = 0;
        let totalGastado = 0;
        
        this.tablaDetalle.rows().every(function(rowIdx) {
            const row = this.data();
            totalMeses++;
            
            if (row.estado === 'success') mesesDentroPresupuesto++;
            mayorExceso = Math.min(mayorExceso, row.diferencia);
            mayorAhorro = Math.max(mayorAhorro, row.diferencia);
            totalPresupuestado += parseFloat(row.presupuesto) || 0;
            totalGastado += parseFloat(row.gasto_real) || 0;
        });
        
        if (totalMeses > 0) {
            stats.push({
                title: 'Cumplimiento del Presupuesto',
                value: `${((mesesDentroPresupuesto / totalMeses) * 100).toFixed(1)}% de los meses (${mesesDentroPresupuesto} de ${totalMeses})`
            });
            
            if (mayorExceso < 0) {
                stats.push({
                    title: 'Mayor Exceso',
                    value: formatearMoneda(Math.abs(mayorExceso))
                });
            }
            
            if (mayorAhorro > 0) {
                stats.push({
                    title: 'Mayor Ahorro',
                    value: formatearMoneda(mayorAhorro)
                });
            }
            
            if (totalPresupuestado > 0) {
                stats.push({
                    title: 'Total Presupuestado vs Gastado',
                    value: `${formatearMoneda(totalPresupuestado)} vs ${formatearMoneda(totalGastado)} (${((totalGastado/totalPresupuestado)*100).toFixed(1)}%)`
                });
            }
        }
        
        return stats;
    },

    registrarPresupuesto: function() {
        const mes = parseInt($('#mes-presupuesto').val());
        const año = parseInt($('#año-presupuesto').val());
        const fecha = `${año}-${String(mes).padStart(2, '0')}-01`;

        const formData = {
            cod_cat_gasto: $('#categoria-presupuesto').val(),
            mes: fecha,
            monto: parseFloat($('#monto-presupuesto').val()),
            descripcion: $('#descripcion-presupuesto').val()
        };

        if (!formData.cod_cat_gasto || !formData.mes || !formData.monto) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Por favor complete todos los campos requeridos'
            });
            return;
        }

        if (isNaN(formData.monto) || formData.monto <= 0) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'El monto debe ser un número positivo'
            });
            return;
        }

        const self = this;
        $.ajax({
            url: 'index.php?pagina=finanzas',
            method: 'POST',
            data: {
                accion: 'registrar_presupuesto',
                ...formData
            },
            success: (response) => {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Éxito',
                        text: response.message
                    });
                    $('#modal-registro-presupuesto').modal('hide');
                    
                    // reiniciar formulario
                    $('#categoria-presupuesto').val('');
                    $('#monto-presupuesto').val('');
                    $('#descripcion-presupuesto').val('');
                    
                    // inicializar mes siguiente como predeterminado
                    const fechaActual = new Date();
                    const unMesDespues = new Date(fechaActual.setMonth(fechaActual.getMonth() + 1));
                    const nuevoMes = unMesDespues.getMonth() + 1;
                    const nuevoAño = unMesDespues.getFullYear();
                    $('#mes-presupuesto').val(nuevoMes);
                    $('#año-presupuesto').val(nuevoAño);

                    // Actualizar tabla y gráfico
                    $.ajax({
                        url: 'index.php?pagina=finanzas',
                        method: 'POST',
                        data: {
                            accion: 'obtener_presupuestos'
                        },
                        success: (response) => {
                            if (response.success) {
                                console.group('Updating table data');
                                console.log('Raw data from server:', response.presupuestos);
                                
                                self.tabla.clear();
                                self.tabla.rows.add(response.presupuestos);
                                self.tabla.draw();
                                
                                console.log('Table updated');
                                console.groupEnd();
                            }
                            self.actualizarDatos();
                        },
                        error: (xhr, status, error) => {
                            console.error('Error al actualizar tabla:', error);
                            self.actualizarDatos();
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message
                    });
                }
            },
            error: (xhr, status, error) => {
                console.error('Error al registrar presupuesto:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al comunicarse con el servidor'
                });
            }
        });
    },

    exportarReporteGeneral: function() {
        // obtener datos de la tabla principal
        const tableData = this.tabla.rows().data().toArray();
        
        // obtener período seleccionado
        const mesInicio = $('#mes-inicio-vis').val();
        const añoInicio = $('#año-inicio-vis').val();
        const mesFin = $('#mes-fin-vis').val();
        const añoFin = $('#año-fin-vis').val();
        
        const meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
                      'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
        
        const periodo = `${meses[mesInicio - 1]} ${añoInicio} - ${meses[mesFin - 1]} ${añoFin}`;
        const fechaGeneracion = new Date().toLocaleDateString('es-ES', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
        
        ExportarPDFs.exportToPDF('presupuestos_general', {
            periodo: periodo,
            fechaGeneracion: fechaGeneracion,
            sourceCanvasId: 'grafico-presupuestos',
            tableData: tableData,
            stats: this.generarEstadisticasGenerales(tableData)
        }).catch(error => {
            console.error('Error en exportación:', error);
        });
    },

    generarEstadisticasGenerales: function(tableData) {
        const stats = [];
        let totalCategorias = tableData.length;
        let categoriasConPresupuesto = 0;
        let categoriasDentroPresupuesto = 0;
        let categoriasExcedidas = 0;
        let totalPresupuestado = 0;
        let totalGastado = 0;
        let mayorExceso = 0;
        let mayorAhorro = 0;
        
        tableData.forEach(row => {
            if (row.presupuesto && row.presupuesto > 0) {
                categoriasConPresupuesto++;
                totalPresupuestado += parseFloat(row.presupuesto) || 0;
                
                if (row.estado === 'success') {
                    categoriasDentroPresupuesto++;
                } else {
                    categoriasExcedidas++;
                }
                
                const diferencia = parseFloat(row.diferencia) || 0;
                mayorExceso = Math.min(mayorExceso, diferencia);
                mayorAhorro = Math.max(mayorAhorro, diferencia);
            }
            
            totalGastado += parseFloat(row.gasto_real) || 0;
        });
        
        if (totalCategorias > 0) {
            stats.push({
                title: 'Cobertura de Presupuestos',
                value: `${categoriasConPresupuesto} de ${totalCategorias} categorías tienen presupuesto asignado (${((categoriasConPresupuesto / totalCategorias) * 100).toFixed(1)}%)`
            });
            
            if (categoriasConPresupuesto > 0) {
                stats.push({
                    title: 'Cumplimiento del Presupuesto',
                    value: `${categoriasDentroPresupuesto} de ${categoriasConPresupuesto} categorías dentro del presupuesto (${((categoriasDentroPresupuesto / categoriasConPresupuesto) * 100).toFixed(1)}%)`
                });
                
                if (categoriasExcedidas > 0) {
                    stats.push({
                        title: 'Categorías Excedidas',
                        value: `${categoriasExcedidas} categorías han excedido su presupuesto`
                    });
                }
                
                if (mayorExceso < 0) {
                    stats.push({
                        title: 'Mayor Exceso Registrado',
                        value: formatearMoneda(Math.abs(mayorExceso))
                    });
                }
                
                if (mayorAhorro > 0) {
                    stats.push({
                        title: 'Mayor Ahorro Registrado',
                        value: formatearMoneda(mayorAhorro)
                    });
                }
                
                stats.push({
                    title: 'Resumen Financiero',
                    value: `Presupuestado: ${formatearMoneda(totalPresupuestado)} | Gastado: ${formatearMoneda(totalGastado)} | Diferencia: ${formatearMoneda(totalPresupuestado - totalGastado)}`
                });
                
                if (totalPresupuestado > 0) {
                    const porcentajeUtilizacion = (totalGastado / totalPresupuestado) * 100;
                    stats.push({
                        title: 'Utilización Global del Presupuesto',
                        value: `${porcentajeUtilizacion.toFixed(1)}% del presupuesto total utilizado`
                    });
                }
            }
            
            const categoriasSinPresupuesto = totalCategorias - categoriasConPresupuesto;
            if (categoriasSinPresupuesto > 0) {
                stats.push({
                    title: 'Categorías sin Presupuesto',
                    value: `${categoriasSinPresupuesto} categorías no tienen presupuesto asignado`
                });
            }
        }
        
        return stats;
    }
}; 