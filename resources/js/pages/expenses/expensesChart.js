import Chart from 'chart.js/auto';

document.addEventListener('DOMContentLoaded', () => {
    const canvas = document.getElementById('expensesChart');
    if (!canvas) return;

    const labels = JSON.parse(canvas.dataset.labels);
    const income = JSON.parse(canvas.dataset.income);
    const expense = JSON.parse(canvas.dataset.expense);

    new Chart(canvas, {
        type: 'bar',
        data: {
            labels,
            datasets: [
                {
                    label: 'Income',
                    data: income,
                    backgroundColor: 'rgba(59, 130, 246, 0.6)',
                    borderRadius: 6,
                },
                {
                    label: 'Expense',
                    data: expense,
                    backgroundColor: 'rgba(239, 68, 68, 0.6)',
                    borderRadius: 6,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                },
            },
            plugins: {
                legend: {
                    position: 'top',
                },
            },
        },
    });
});
