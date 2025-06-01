import { createApp } from 'vue';
import App from './App.vue';
import HomeView from './views/HomeView.vue';
import Search from './components/Search.vue';
import CompanyDetailsView from './views/CompanyDetailsView.vue';
import IndividualDetailsView from './views/IndividualDetailsView.vue';
import { Chart, ArcElement, CategoryScale, Tooltip, Legend, PieController } from 'chart.js'; // Импортируем необходимые элементы
import '../styles/app.css';

Chart.register(ArcElement, CategoryScale, Tooltip, Legend, PieController);
// Инициализация Vue приложения
const app = createApp(App);

if (document.getElementById('app')) {
    app.component('home-view', HomeView);
    app.component('search', Search);
    app.component('company-details-view', CompanyDetailsView);
    app.component('individual-details-view', IndividualDetailsView);

    app.mount('#app');
}

export function formatNumber(value) {
    const numStr = value.toString();
    return numStr.replace(/\B(?=(\d{3})+(?!\d))/g, ' ') + ' ₽';
}

export function defineColorStatus(status) {
    switch (status) {
        case 'LIQUIDATED':
            return 'red-status';
        case 'ACTIVE':
            return 'green-status';
        case 'SUSPENDED':
            return 'yellow-status';
        case 'INACTIVE':
            return 'grey-status';
        case 'UNDEFINED':
            return 'purple-status';
        default:
            return '';
    }
}

export function generateColors(count) {
    const baseColors = [
        '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40',
        '#8B0000', '#00CED1', '#FFD700', '#ADFF2F', '#FF4500', '#DA70D6',
        '#7FFFD4', '#DC143C', '#00BFFF', '#FF69B4', '#B0C4DE', '#98FB98',
        '#FF1493', '#20B2AA', '#9370DB', '#3CB371', '#FFA07A', '#40E0D0',
        '#FF6347', '#BA55D3', '#00FA9A', '#1E90FF', '#D2691E', '#FFB6C1',
    ];
    const result = [];
    for (let i = 0; i < count; i++) {
        result.push(baseColors[i % baseColors.length]);
    }
    return result;
}

document.addEventListener('DOMContentLoaded', function () {


    const toggleLicenses = document.getElementById('toggle-licenses');
    const licensesBlock = document.getElementById('licenses-block');
    const icon = document.getElementById('licenses-icon');

    if (toggleLicenses && licensesBlock && icon) {
        toggleLicenses.addEventListener('click', () => {
            const isHidden = licensesBlock.hasAttribute('hidden');
            if (isHidden) {
                licensesBlock.removeAttribute('hidden');
                icon.textContent = '▼';
            } else {
                licensesBlock.setAttribute('hidden', '');
                icon.textContent = '▶';
            }
        });
    }

    const toggleOkvedBtn = document.getElementById('toggle-okveds');
    const okvedList = document.getElementById('secondary-okveds');
    const toggleIcon = toggleOkvedBtn ? toggleOkvedBtn.querySelector('.toggle-icon') : null;

    if (toggleOkvedBtn && okvedList) {
        toggleOkvedBtn.addEventListener('click', function () {
            const isHidden = okvedList.hasAttribute('hidden');
            if (isHidden) {
                okvedList.removeAttribute('hidden');
                if (toggleIcon) toggleIcon.textContent = '▼';
            } else {
                okvedList.setAttribute('hidden', '');
                if (toggleIcon) toggleIcon.textContent = '▶';
            }
        });
    }

    // График основателей
    const chartElement = document.getElementById('foundersChart');
    if (chartElement) {
        let founders = JSON.parse(chartElement.dataset.founders);
        const colorsCount = parseInt(chartElement.dataset.colorsCount, 10);
        const colors = generateColors(colorsCount);

        founders = founders.map(str => JSON.parse(str));

        // Запись массива цветов в data-colors (в виде JSON)
        chartElement.dataset.colors = JSON.stringify(colors);

        // Обновляем цвета легенды
        const legendItems = document.querySelectorAll('.legend-item');
        legendItems.forEach((item, index) => {
            const color = colors[index];
            item.querySelector('.legend-dot').style.backgroundColor = color;
        });

        const ctx = chartElement.getContext('2d');

        const dataValues = founders.map(f => f.share);
        const labels = founders.map(f => `${f.name} (${f.share}%)`);

        const chart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    data: dataValues,
                    backgroundColor: colors
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: (context) => `${context.raw}%`
                        },
                        displayColors: false
                    }
                },
                onHover: function(event, elements) {
                    document.querySelectorAll('.legend-item').forEach(item => item.classList.remove('hovered'));

                    if (elements.length > 0) {
                        const index = elements[0].index;
                        document.querySelectorAll('.legend-item')[index].classList.add('hovered');
                        event.native.target.style.cursor = 'pointer';
                    } else {
                        event.native.target.style.cursor = 'default';
                    }
                },
                onClick: function(event, elements) {
                    if (elements.length > 0) {
                        const index = elements[0].index;
                        const founder = founders[index];
                        if (founder.slug) {
                            const url = founder.type === 'company' ? `/company/${founder.slug}` : `/individual/${founder.slug}`;
                            window.open(url, '_blank');
                        }
                    }
                }
            }
        });

        document.querySelectorAll('.tooltip-icon').forEach(icon => {
            const tooltip = icon.closest('.opf-row').querySelector('.custom-tooltip');

            if (tooltip) {
                icon.addEventListener('mouseenter', () => {
                    tooltip.style.display = 'block';
                });

                icon.addEventListener('mouseleave', () => {
                    tooltip.style.display = 'none';
                });

                // Чтобы тултип не исчезал, когда наводишь на него самого
                tooltip.addEventListener('mouseenter', () => {
                    tooltip.style.display = 'block';
                });

                tooltip.addEventListener('mouseleave', () => {
                    tooltip.style.display = 'none';
                });
            }
        });


        legendItems.forEach((item, index) => {
            item.addEventListener('click', function () {
                const founder = founders[index];
                if (founder.slug) {
                    const url = founder.type === 'company' ? `/company/${founder.slug}` : `/individual/${founder.slug}`;
                    window.open(url, '_blank');
                }
            });

            item.addEventListener('mouseover', function () {
                item.classList.add('hovered');
                item.style.cursor = 'pointer';
            });

            item.addEventListener('mouseleave', function () {
                item.classList.remove('hovered');
                item.style.cursor = 'default';
            });
        });
    }
});
