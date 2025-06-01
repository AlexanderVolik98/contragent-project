<template>
  <div :class="['card cards-row__item', loading ? 'card--loading' : '']">
    <h2 class="block-title">Основатели</h2>

    <div v-if="loading" class="info-message">
      Данные по основателям загружаются. Пожалуйста, подождите пару минут.
    </div>

    <div v-else class="chart-flex-container">
      <div class="chart-container">
        <canvas ref="foundersChart"></canvas>
      </div>
      <ul class="custom-legend">
        <li
            v-for="(founder, index) in founders"
            :key="founder.slug"
            @click="handleLegendClick(founder)"
            @mouseover="hoveredIndex = index"
            @mouseleave="hoveredIndex = null"
            :class="{ hovered: hoveredIndex === index }"
        >
          <span
              class="legend-dot"
              :style="{ backgroundColor: colors[index] }"
          ></span>
          {{ founder.name }} ({{ founder.share }}%)
        </li>
      </ul>
    </div>
  </div>
</template>

<script>
import {
  Chart,
  DoughnutController,
  PieController,
  ArcElement,
  Tooltip,
  Legend,
} from 'chart.js';

export default {
  name: 'FoundersBlock',
  props: {
    founders: {
      type: Array,
      required: true,
    },
    loading: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      chart: null,
      colors: [],
      hoveredIndex: null,
    };
  },
  methods: {
    createChart() {
      const ctx = this.$refs.foundersChart.getContext('2d');
      const dataValues = this.founders.map(f => f.share);
      const labels = this.founders.map(f => `${f.name} (${f.share}%)`);
      this.colors = this.generateColors(dataValues.length);

      const self = this;

      if (!this.chart) {
        this.chart = new Chart(ctx, {
          type: 'pie',
          data: {
            labels,
            datasets: [{
              data: dataValues,
              backgroundColor: this.colors,
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
              legend: { display: false },
              tooltip: {
                callbacks: {
                  label: context => context.raw + '%',
                },
                displayColors: false,
              },
            },
            onHover(event, elements) {
              if (elements.length > 0) {
                const index = elements[0].index;
                event.native.target.style.cursor = 'pointer';
                self.hoveredIndex = index;

                self.chart.setActiveElements([{ datasetIndex: 0, index }]);
                self.chart.tooltip.setActiveElements([{ datasetIndex: 0, index }], {
                  x: event.native.offsetX,
                  y: event.native.offsetY,
                });
                if (self.chart.chartArea) {
                  self.chart.draw();
                }
              } else {
                event.native.target.style.cursor = 'default';
                self.hoveredIndex = null;
                self.chart.setActiveElements([]);
                self.chart.tooltip.setActiveElements([], { x: 0, y: 0 });
                if (self.chart.chartArea) {
                  self.chart.draw();
                }
              }
            },
            onClick(event, elements) {
              if (elements.length > 0) {
                const index = elements[0].index;
                const founder = self.founders[index];
                if (founder.slug) {
                  if (founder.type === 'company') {
                    window.open(`/company/${founder.slug}`, '_blank');
                  } else if (founder.type === 'person') {
                    window.open(`/individual/${founder.slug}`, '_blank');
                  }
                }
              }
            }
          }
        });
      } else {
        this.chart.data.labels = labels;
        this.chart.data.datasets[0].data = dataValues;
        this.chart.data.datasets[0].backgroundColor = this.colors;
        this.chart.update();
      }
    },
    generateColors(count) {
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
    },
    handleLegendClick(founder) {
      if (founder.slug) {
        if (founder.type === 'company') {
          window.open(`/company/${founder.slug}`, '_blank');
        } else if (founder.type === 'person') {
          window.open(`/individual/${founder.slug}`, '_blank');
        }
      }
    },
  },
  mounted() {
    Chart.register(DoughnutController, ArcElement, Tooltip, Legend, PieController);
    if (!this.loading) this.createChart();
  },
  watch: {
    founders: {
      handler() {
        if (!this.loading) this.createChart();
      },
      deep: true,
    },
    loading(val) {
      if (!val) {
        this.$nextTick(() => this.createChart());
      }
    }
  },
};
</script>

<style scoped>
.cards-row__item {
  padding: 20px;
  background: #fff;
  border-radius: 8px;
  flex: 0 0 50%;
  min-width: 300px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.chart-flex-container {
  display: flex;
  gap: 24px;
  justify-content: center;
  align-items: center;
  flex-wrap: wrap;
  flex-direction: column;
}

.chart-container {
  width: 250px;
  height: 250px;
  position: relative;
  flex-shrink: 0;
}

canvas {
  display: block;
  width: 100% !important;
  height: 100% !important;
}

.custom-legend {
  list-style: none;
  max-width: 700px;
  margin: 0;
  padding: 0;
}

.custom-legend li {
  display: flex;
  align-items: center;
  margin-bottom: 8px;
  font-size: 14px;
  transition: color 0.3s ease;
  color: inherit;
}

.custom-legend li:hover {
  cursor: pointer;
  color: rgba(0, 123, 255, 0.8);
}

.legend-dot {
  display: inline-block;
  width: 12px;
  height: 12px;
  border-radius: 50%;
  margin-right: 8px;
  flex-shrink: 0;
  transition: transform 0.3s ease;
}

.card--loading {
  max-height: 150px;
  overflow: hidden;
}

.custom-legend li:hover .legend-dot,
.custom-legend li.hovered .legend-dot {
  transform: scale(1.2);
}

.custom-legend li.hovered {
  color: rgba(0, 123, 255, 0.8);
}
</style>
