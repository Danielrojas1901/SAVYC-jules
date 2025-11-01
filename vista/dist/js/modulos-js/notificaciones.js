function cargarNotificacionesGlobal() {
    $.ajax({
        url: 'index.php?pagina=notificaciones',
        method: 'POST',
        data: { accion: 'todas_las_alertas', dias_alerta: 3, dias_alerta_productos: 20},
        dataType: 'json',
        success: function (data) {
            console.log(data);
            const dropdown = $('.notification-dropdown');
            dropdown.empty();

            if (!data || data.length === 0) {
                dropdown.append('<div class="notification-item text-muted">No hay alertas activas.</div>');
                actualizarContadorNotificaciones(0);
                return;
            }

            let contador = 0;

            data.forEach(function (item) {
                let html = '';
                

                if (item.tipo_alerta === 'venta' || item.tipo_alerta === 'gasto' || item.tipo_alerta === 'compra') {
                    let diasTexto = '';
                    if (item.estado === 'vencida') {
                        diasTexto = `<span class="text-danger fw-bold">¡Vencido hace ${Math.abs(item.dias_restantes)} días!</span>`;
                    } else if (parseInt(item.dias_restantes) === 0) {
                        diasTexto = `<span class="text-warning fw-bold">¡Hoy!</span>`;
                    } else {
                        diasTexto = `<span class="text-success">en ${item.dias_restantes} días</span>`;
                    }

                    const url = item.tipo === 'cobrar'
                        ? 'index.php?pagina=venta'
                        : item.tipo === 'compra'
                            ? 'index.php?pagina=compras'
                            : 'index.php?pagina=gasto';

                    html = `
                        <div class="notification-item">
                            <div class="notification-title">${item.descripcion} ${diasTexto}</div>
                            <div class="notification-meta">Fecha límite: ${item.fecha_vencimiento}</div>
                            <a href="${url}" class="notification-link">Ver detalle</a>
                        </div>
                    `;
                    
                } else if (item.tipo_alerta === 'producto') {
                    let diasTexto = '';
                    let iconoTexto = '';
                    if (item.estado === 'vencida') {
                        diasTexto = `<span class="text-danger fw-bold">¡Vencido hace ${Math.abs(item.dias_restantes)} días!</span>`;
                        iconoTexto = '<i class="fas fa-exclamation-triangle text-danger"></i> ';
                    } else if (parseInt(item.dias_restantes) === 0) {
                        diasTexto = `<span class="text-warning fw-bold">¡Vence hoy!</span>`;
                        iconoTexto = '<i class="fas fa-clock text-warning"></i> ';
                    } else {
                        diasTexto = `<span class="text-warning">vence en ${item.dias_restantes} días</span>`;
                        iconoTexto = '<i class="fas fa-calendar-alt text-warning"></i> ';
                    }

                    let stockTexto = item.stock ? ` (Stock: ${item.stock})` : '';

                    html = `
                        <div class="notification-item">
                            <div class="notification-title">${iconoTexto}${item.descripcion} ${diasTexto}</div>
                            <div class="notification-meta">Fecha vencimiento: ${item.fecha_vencimiento}${stockTexto}</div>
                            <a href="index.php?pagina=productos" class="notification-link">Ver inventario</a>
                        </div>
                    `;

                } else if (item.tipo_alerta === 'caja') {
                    if (item.tipo === 'apertura') {
                        html = `
                            <div class="notification-item">
                                <div class="notification-title text-danger fw-bold">Caja sin apertura: ${item.nombre_caja}</div>
                                <div class="notification-meta">Horario: ${item.desde} - ${item.hasta}</div>
                                <div class="notification-desc">Debes aperturar esta caja.</div>
                                <a href="index.php?pagina=caja" class="notification-link">Ver cajas</a>
                            </div>
                        `;
                    } else if (item.tipo === 'cierre') {
                        html = `
                            <div class="notification-item">
                                <div class="notification-title text-danger fw-bold">Caja abierta fuera de horario: ${item.nombre_caja}</div>
                                <div class="notification-meta">Cierre esperado: ${item.hasta}</div>
                                <div class="notification-desc">Debes cerrar esta caja.</div>
                                <a href="index.php?pagina=caja" class="notification-link">Ver cajas</a>
                            </div>
                        `;
                    }
                }

                if (html !== '') {
                    dropdown.append(html);
                    contador++;
                }
            });

            // Botón fijo al final
            dropdown.append(`
                <div class="notification-item text-center">
                    <a href="#" class="notification-link">Ver más notificaciones</a>
                </div>
            `);

            actualizarContadorNotificaciones(contador);
        },
        error: function () {
            $('.notification-dropdown').html('<div class="notification-item text-center text-danger">Error al cargar notificaciones.</div>');
            actualizarContadorNotificaciones(0);
        }
    });
}

function actualizarContadorNotificaciones(total) {
    const contador = document.getElementById('notification-count');
    if (!contador) return;

    if (total > 0) {
        contador.textContent = total;
        contador.style.display = 'inline-block';
    } else {
        contador.style.display = 'none';
    }
}

$(document).ready(function () {
    cargarNotificacionesGlobal();
    setInterval(cargarNotificacionesGlobal, 60000); // Recarga cada minuto

    // Mostrar/ocultar el dropdown al hacer clic en la campana
    const bellIcon = document.getElementById("bell-icon");
    const dropdown = document.querySelector(".notification-dropdown");

    if (bellIcon && dropdown) {
        bellIcon.addEventListener("click", function (e) {
            e.stopPropagation(); // Para evitar que se cierre instantáneamente
            dropdown.classList.toggle("show");
        });

        document.addEventListener("click", function () {
            dropdown.classList.remove("show");
        });

        dropdown.addEventListener("click", function (e) {
            e.stopPropagation(); // Para que no se cierre al hacer clic dentro
        });
    }
});
