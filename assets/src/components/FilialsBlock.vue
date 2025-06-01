<template>
  <div class="card cards-row__item">
    <h2 class="block-title">Филиалы</h2>
    <ul class="filials-list">
      <li
          v-for="filial in filials"
          :key="filial.slug"
          class="filial-item"
      >
        <a class="filial-name" :href="`/company/${filial.slug}`">
          {{ filial.name }}
        </a>
        <span class="filial-kpp"><strong>КПП:</strong> {{ filial.kpp }}</span>
      </li>
    </ul>
  </div>
</template>

<script>
export default {
  name: 'FilialsBlock',
  props: {
    filials: {
      type: Array,
      required: true
    }
  }
};
</script>

<style scoped>
.filials-list {
  list-style: none;
  padding: 0;
  margin: 0;
}

.filial-item {
  display: flex;
  flex-direction: column;
  padding: 8px 0;
  border-bottom: 1px solid #ddd;
}

.filial-item:last-child {
  border-bottom: none;
}

.filial-name {
  font-size: 16px;
  font-weight: bold;
  color: #233461;
  text-decoration: none;
}

.filial-name:hover {
  text-decoration: underline;
}

.filial-kpp {
  font-size: 14px;
  color: #616161;
}
</style>
