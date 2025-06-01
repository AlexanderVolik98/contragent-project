<template>
  <div class="card cards-row__item">
    <h2 class="block-title">Финансы</h2>
    <table class="finances-table">
      <thead>
<!--      <span style="font-size: 12px">Рассчитываются по <br>«Отчету о финансовых результатах»<br> (форма по ОКУД 0710002)</span>-->
      <tr>
        <th>Показатель</th>
        <th>Значение</th>
      </tr>
      </thead>
      <tbody>
      <tr>
        <td>Год</td>
        <td>{{ finances.year }}</td>
      </tr>
      <tr>
        <td>Доход</td>
        <td>{{ finances.income ? formatNumber(finances.income) : 'нет данных' }}</td>
      </tr>
      <tr>
        <td>Расход</td>
        <td>{{ finances.expense ? formatNumber(finances.expense) : 'нет данных' }}</td>
      </tr>
      <tr>
        <td>Выручка</td>
        <td>{{ finances.revenue ? formatNumber(finances.revenue) : 'нет данных'}}</td>
      </tr>
      <tr>
        <td>Долги</td>
        <td>{{ finances.debt ? formatNumber(finances.debt) : 'нет данных' }}</td>
      </tr>
      <tr>
        <td>Налоговая система</td>
        <td>{{ finances.taxSystem ?? 'нет данных' }}</td>
      </tr>
      <tr>
        <td>Штрафы</td>
        <td>{{ finances.penalty ? formatNumber(finances.penalty) : 'нет данных' }}</td>
      </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
import {formatNumber} from "../app";

export default {
  name: 'FinancesBlock',
  methods: {formatNumber},
  props: {
    finances: {
      type: Object,
      required: true
    }
  }
};
</script>

<style scoped>
.finances-table {
  width: 100%;
  border-collapse: collapse;
}

.finances-table th,
.finances-table td {
  border: 1px solid #ddd;
  padding: 8px;
}

.finances-table th {
  background-color: #f4f4f4;
  text-align: left;
}
</style>
