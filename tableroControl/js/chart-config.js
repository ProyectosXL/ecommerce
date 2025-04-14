
    // chart-config.js
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

        // Inicializar el gráfico cuando se abre el modal
        const modalNcr = document.getElementById('modalNcr');
        if (modalNcr) {
            modalNcr.addEventListener('shown.bs.modal', function () {
                const ctx = document.getElementById('ncrChart').getContext('2d');
                // Usar los datos que fueron pasados desde PHP a través de una variable global
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: chartData.labels,
                        datasets: [{
                            label: 'Cantidad de NC',
                            data: chartData.values,
                            backgroundColor: '#e91e63',
                            borderColor: '#c2185b',
                            borderWidth: 1,
                            barPercentage: 0.5
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            title: {
                                display: true,
                                text: 'Notas de Crédito realizadas en los últimos 7 días',
                                font: { size: 16 }
                            },
                            legend: {
                                position: 'top'
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { stepSize: 1 }
                            }
                        }
                    }
                });
            });
        }
    });