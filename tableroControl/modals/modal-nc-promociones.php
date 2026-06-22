
            <!-- Modal para el detalle de NC Promociones -->
            <div class="modal fade" id="modalNcPromocionesDetalle" tabindex="-1" data-modal-loader="ncPromociones">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header d-flex justify-content-between align-items-center">
                            <h5 class="modal-title">
                                <i class="fas fa-tags"></i> Detalle de NC Pendientes por Promociones
                            </h5>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-outline-secondary" id="btnVerHistorialNcPromo">
                                    <i class="fas fa-clock-rotate-left me-2"></i>Historial
                                </button>
                                <button type="button" class="btn btn-success" onclick="exportToExcelNcPromociones()">
                                    <i class="fas fa-file-excel me-2"></i>Exportar
                                </button>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                        </div>
                        <div class="modal-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width:40px"><input type="checkbox" id="ncPromoSelectAll" title="Seleccionar todas"></th>
                                            <th>Fecha</th>
                                            <th>Cód. Promoción</th>
                                            <th>Descripción</th>
                                            <th>% Reintegro</th>
                                            <th>Cód. Artículo</th>
                                            <th class="text-end">Importe NC</th>
                                        </tr>
                                    </thead>
                                    <tbody class="modal-lazy-tbody">
                                        <tr><td colspan="7" class="text-center py-3"><div class="spinner-border spinner-border-sm text-primary" role="status"><span class="visually-hidden">Cargando...</span></div></td></tr>
                                    </tbody>
                                </table>
                            </div>

                            <div id="ncPromoRegistroPanel" class="mt-3 p-3 border rounded bg-light d-none">
                                <div class="d-flex align-items-center gap-3 flex-wrap">
                                    <span class="text-muted"><strong><span id="ncPromoSelCount">0</span></strong> fila(s) seleccionada(s)</span>
                                    <span class="fw-semibold">Total: $<span id="ncPromoTotal">0</span></span>
                                    <input type="text" id="ncPromoNumNc" class="form-control form-control-sm"
                                           style="max-width:200px" placeholder="Nro. NC *" maxlength="20" required>
                                    <button type="button" class="btn btn-primary btn-sm" id="btnRegistrarNcPromo">
                                        <i class="fas fa-check me-1"></i>Registrar procesadas
                                    </button>
                                    <div id="ncPromoMsg" class="ms-2"></div>
                                </div>
                            </div>

                            <div class="modal-lazy-extra"></div>
                        </div>
                    </div>
                </div>
            </div>

            <script>
            (function () {
                function updateNcPromoPanel() {
                    const checked = document.querySelectorAll('#modalNcPromocionesDetalle .nc-promo-check:checked');
                    const n = checked.length;
                    let total = 0;
                    checked.forEach(cb => total += parseFloat(cb.dataset.nc) || 0);
                    document.getElementById('ncPromoSelCount').textContent = n;
                    document.getElementById('ncPromoTotal').textContent = total.toLocaleString('es-AR', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
                    const panel = document.getElementById('ncPromoRegistroPanel');
                    if (n > 0) panel.classList.remove('d-none');
                    else panel.classList.add('d-none');
                }

                document.addEventListener('change', function (e) {
                    if (e.target.id === 'ncPromoSelectAll') {
                        document.querySelectorAll('#modalNcPromocionesDetalle .nc-promo-check')
                            .forEach(cb => cb.checked = e.target.checked);
                        updateNcPromoPanel();
                    } else if (e.target.classList.contains('nc-promo-check')) {
                        updateNcPromoPanel();
                    }
                });

                function resetNcPromoPanel() {
                    document.getElementById('ncPromoSelectAll').checked = false;
                    const inp = document.getElementById('ncPromoNumNc');
                    inp.value = '';
                    inp.classList.remove('is-invalid');
                    document.getElementById('ncPromoSelCount').textContent = '0';
                    document.getElementById('ncPromoTotal').textContent = '0';
                    document.getElementById('ncPromoMsg').innerHTML = '';
                    document.getElementById('ncPromoRegistroPanel').classList.add('d-none');
                }

                function toastNcPromo(mensaje) {
                    const alertDiv = document.createElement('div');
                    alertDiv.className = 'alert alert-success alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3';
                    alertDiv.style.zIndex = '20000';
                    alertDiv.innerHTML =
                        '<i class="fas fa-check-circle me-2"></i>' + mensaje +
                        '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
                    document.body.appendChild(alertDiv);
                    setTimeout(() => alertDiv.remove(), 3000);
                }

                // Resetear estado al abrir el modal
                document.getElementById('modalNcPromocionesDetalle').addEventListener('show.bs.modal', resetNcPromoPanel);

                // Navegación entre modal principal e historial (oculta uno y muestra el otro tras la transición)
                function switchModal(fromEl, toEl) {
                    fromEl.addEventListener('hidden.bs.modal', function handler() {
                        fromEl.removeEventListener('hidden.bs.modal', handler);
                        bootstrap.Modal.getOrCreateInstance(toEl).show();
                    });
                    bootstrap.Modal.getOrCreateInstance(fromEl).hide();
                }

                // Delegación: los botones viven en modales incluidos en distinto orden,
                // se resuelven al hacer click cuando ya existen en el DOM.
                document.addEventListener('click', function (e) {
                    if (e.target.closest('#btnVerHistorialNcPromo')) {
                        switchModal(
                            document.getElementById('modalNcPromocionesDetalle'),
                            document.getElementById('modalHistorialNcPromo')
                        );
                    } else if (e.target.closest('#btnVolverNcPromo')) {
                        switchModal(
                            document.getElementById('modalHistorialNcPromo'),
                            document.getElementById('modalNcPromocionesDetalle')
                        );
                    }
                });

                document.getElementById('btnRegistrarNcPromo').addEventListener('click', function () {
                    const registros = [];
                    document.querySelectorAll('#modalNcPromocionesDetalle .nc-promo-check:checked').forEach(function (cb) {
                        registros.push({
                            fecha:     cb.dataset.fecha,
                            cod_promo: cb.dataset.promo,
                            cod_articu: cb.dataset.articu,
                            nc:        cb.dataset.nc
                        });
                    });

                    if (registros.length === 0) return;

                    const numNcInput = document.getElementById('ncPromoNumNc');
                    const numNc = numNcInput.value.trim();
                    const btn = this;
                    const msg = document.getElementById('ncPromoMsg');

                    if (!numNc) {
                        numNcInput.classList.add('is-invalid');
                        msg.innerHTML = '<span class="text-danger"><i class="fas fa-exclamation-circle me-1"></i>Ingrese el número de NC</span>';
                        numNcInput.focus();
                        return;
                    }
                    numNcInput.classList.remove('is-invalid');

                    btn.disabled = true;
                    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Guardando...';
                    msg.innerHTML = '';

                    fetch('includes/action-nc-promociones.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: 'registros=' + encodeURIComponent(JSON.stringify(registros)) + '&num_nc=' + encodeURIComponent(numNc)
                    })
                    .then(r => r.json())
                    .then(function (res) {
                        btn.disabled = false;
                        btn.innerHTML = '<i class="fas fa-check me-1"></i>Registrar procesadas';
                        if (res.success) {
                            const modalEl = document.getElementById('modalNcPromocionesDetalle');
                            resetNcPromoPanel();
                            refreshModalDetail(modalEl);
                            actualizarDocumentacion();
                            // Invalidar caché del historial si ya fue abierto
                            const histEl = document.getElementById('modalHistorialNcPromo');
                            if (histEl && histEl.classList.contains('show')) {
                                refreshModalDetail(histEl);
                            }
                            toastNcPromo('NC registradas correctamente');
                        } else {
                            msg.innerHTML = '<span class="text-danger"><i class="fas fa-exclamation-circle me-1"></i>' + (res.message || 'Error al guardar') + '</span>';
                        }
                    })
                    .catch(function () {
                        btn.disabled = false;
                        btn.innerHTML = '<i class="fas fa-check me-1"></i>Registrar procesadas';
                        msg.innerHTML = '<span class="text-danger"><i class="fas fa-exclamation-circle me-1"></i>Error de conexión</span>';
                    });
                });
            })();
            </script>
