/**
 * Initialize the Booking Analytics Chart
 * @param {Object} data - Contains labels (days/months) and values (counts)
 */
const initBookingChart = (data) => {
    const canvas = document.getElementById('bookingAnalytics');
    if (!canvas) return;

    const ctx = canvas.getContext('2d');

    // Create a vertical gradient for a premium "SaaS" look
    const gradient = ctx.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, '#1F7A63');      // Brand Primary
    gradient.addColorStop(1, 'rgba(31, 122, 99, 0.1)'); // Faded Base

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data.labels,
            datasets: [{
                label: 'Appointments',
                data: data.values,
                backgroundColor: gradient,
                hoverBackgroundColor: '#165b4a',
                borderRadius: 6,
                borderSkipped: false,
                barThickness: 'flex',
                maxBarThickness: 20
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false, // Essential for our .chart-container CSS
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1a202c',
                    padding: 12,
                    titleFont: { size: 14, weight: 'bold' },
                    callbacks: {
                        label: (context) => ` ${context.parsed.y} Bookings`
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        color: '#94a3b8' // Muted text
                    },
                    grid: {
                        drawBorder: false,
                        color: '#f1f5f9' // Very light grid lines
                    }
                },
                x: {
                    grid: { display: false },
                    ticks: { color: '#94a3b8' }
                }
            },
            interaction: {
                intersect: false,
                mode: 'index',
            }
        }
    });
};

export default initBookingChart;