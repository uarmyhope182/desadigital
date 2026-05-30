document.addEventListener('DOMContentLoaded', () => {
    const canvas = document.getElementById('dashboardChart');
    if (!canvas) return;

    const labels = canvas.dataset.chartLabels ? canvas.dataset.chartLabels.split(',') : [];
    const values = canvas.dataset.chartValues ? canvas.dataset.chartValues.split(',').map(value => Number(value)) : [];

    new Chart(canvas, {
        type: 'line',
        data: {
            labels,
            datasets: [{
                label: 'Permohonan',
                data: values,
                borderColor: '#2563EB',
                backgroundColor: 'rgba(37, 99, 235, 0.16)',
                fill: true,
                tension: 0.35,
                pointRadius: 4,
                pointBackgroundColor: '#2563EB',
                borderWidth: 3,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false,
                },
                tooltip: {
                    backgroundColor: '#111827',
                    titleColor: '#ffffff',
                    bodyColor: '#e5e7eb',
                },
            },
            scales: {
                x: {
                    grid: {
                        display: false,
                    },
                    ticks: {
                        color: '#475569',
                    },
                },
                y: {
                    grid: {
                        color: 'rgba(148, 163, 184, 0.18)',
                    },
                    ticks: {
                        color: '#475569',
                        precision: 0,
                    },
                },
            },
        },
    });
});