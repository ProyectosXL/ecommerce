<!-- Modal NC Pendientes -->
<div class="modal fade modal-nc-pendientes" id="modalNcPendientes" tabindex="-1" role="dialog" aria-labelledby="modalNcPendientesLabel" aria-hidden="true" data-backdrop="true" data-keyboard="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalNcPendientesLabel">
                    <i class="fas fa-exclamation-triangle icon-warning"></i>
                    Notas de Crédito Pendientes
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="contenidoNcPendientes">
                    <!-- El contenido se cargará dinámicamente -->
                </div>
            </div>
            <div class="modal-footer">
                <div class="resumen-footer">
                    <div class="resumen-item">
                        <span class="resumen-label">Total Pendientes</span>
                        <span class="resumen-value" id="totalNcPendientes">0</span>
                    </div>
                    <div class="resumen-item">
                        <span class="resumen-label">Atrasados (+5 días)</span>
                        <span class="resumen-value atrasados" id="totalNcAtrasados">0</span>
                    </div>
                    <button type="button" class="btn btn-cerrar" data-dismiss="modal">
                        <i class="fas fa-check-circle"></i> Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function calcularDiasAtraso(fechaPendiente) {
    const hoy = new Date();
    const fecha = new Date(fechaPendiente);
    const diferencia = Math.floor((hoy - fecha) / (1000 * 60 * 60 * 24));
    return diferencia;
}

function formatearImporte(importe) {
    return '$ ' + parseFloat(importe).toLocaleString('es-AR', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

function cargarNcPendientes(datos) {
    const contenedor = document.getElementById('contenidoNcPendientes');
    
    if (!datos || datos.length === 0) {
        contenedor.innerHTML = `
            <div class="sin-datos">
                <i class="fas fa-check-circle"></i>
                <h4>¡Todo al día!</h4>
                <p>No hay notas de crédito pendientes</p>
            </div>
        `;
        document.getElementById('totalNcPendientes').textContent = '0';
        document.getElementById('totalNcAtrasados').textContent = '0';
        return;
    }
    
    let totalAtrasados = 0;
    let html = `
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Promoción</th>
                        <th>Artículo</th>
                        <th class="text-right">Importe</th>
                        <th class="text-center">Días</th>
                    </tr>
                </thead>
                <tbody>
    `;
    
    datos.forEach(function(item) {
        const diasAtraso = calcularDiasAtraso(item.fecha);
        const esAtrasado = diasAtraso > 5;
        const claseAtrasado = esAtrasado ? 'atrasado' : '';
        
        if (esAtrasado) {
            totalAtrasados++;
        }
        
        html += `
            <tr class="${claseAtrasado}">
                <td>
                    ${item.fecha}
                    ${esAtrasado ? '<span class="badge-atrasado">Atrasado</span>' : ''}
                </td>
                <td>${item.promocion}</td>
                <td><strong>${item.cod_articulo}</strong></td>
                <td class="text-right importe-cell">${formatearImporte(item.importe)}</td>
                <td class="text-center">
                    <span style="font-weight: ${esAtrasado ? '700' : '500'}; color: ${esAtrasado ? '#d63031' : '#6c757d'}">
                        ${diasAtraso} ${diasAtraso === 1 ? 'día' : 'días'}
                    </span>
                </td>
            </tr>
        `;
    });
    
    html += `
                </tbody>
            </table>
        </div>
    `;
    
    contenedor.innerHTML = html;
    document.getElementById('totalNcPendientes').textContent = datos.length;
    document.getElementById('totalNcAtrasados').textContent = totalAtrasados;
}
</script>
