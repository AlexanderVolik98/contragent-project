<template>
  <div class="sort-control">
    <label class="sort-label">Сортировать по:</label>
    <select class="sort-item" v-model="field" @change="onFieldChange">
      <option class="sort-item"  v-for="opt in options" :key="opt.value" :value="opt.value">
        {{ opt.label }}
      </option>
    </select>
    <button
        :class="['arrow-button', { 'arrow-active': active, 'arrow-inactive': !active } ]"
        @click="onArrowClick"
        :aria-label="`Сортировать по ${currentLabel} ${direction === 'asc' ? 'по возрастанию' : 'по убыванию'}`"
    >
      {{ direction === 'asc' ? '↑' : '↓' }}
    </button>
    <!-- кнопка сброса сортировки -->
    <button
        class="reset-button"
        v-if="active || field !== defaultField"
        @click="onReset"
        aria-label="Сбросить сортировку"
    >
      ⨉
    </button>
  </div>
</template>

<script>
export default {
  name: 'SortControl',
  props: {
    options: { type: Array, required: true },
    initialField: { type: String, default: null },
    initialDirection: { type: String, default: 'desc' },
    defaultField: { type: String, required: true },
    defaultDirection: { type: String, required: true }
  },
  data() {
    return {
      field: this.initialField || this.defaultField,
      direction: this.initialDirection,
      active: false
    };
  },
  mounted() {
    // При монтировании компонента устанавливаем дефолтные значения
    if (!this.initialField) {
      this.field = 'state_status'; // Устанавливаем дефолтное значение для поля
    }
    if (!this.initialDirection) {
      this.direction = 'asc'; // Устанавливаем дефолтное значение для направления
    }
  },
  computed: {
    currentLabel() {
      const opt = this.options.find(o => o.value === this.field);
      return opt ? opt.label : this.field;
    }
  },
  methods: {
    onFieldChange() {
      this.active = false;
      this.direction = this.defaultDirection;
    },
    onArrowClick() {
      this.direction = this.direction === 'asc' ? 'desc' : 'asc';
      this.active = true;
      this.$emit('update:sort', { field: this.field, direction: this.direction });
    },
    onReset() {
      this.field = 'state_status';
      this.direction = 'asc';
      this.active = false;
      this.$emit('update:sort', { field: this.defaultField, direction: this.defaultDirection });
    }
  }
};
</script>

<style scoped>
.sort-control {
  display: flex;
  align-items: center;
  gap: 8px;
}

.sort-control select {
  padding: 8px;
  font-size: 14px;
  border: 1px solid #ddd;
  border-radius: 4px;
  outline: none;
  transition: border 0.3s ease;
  appearance: none; /* Скрывает стрелку в большинстве браузеров */
  -webkit-appearance: none; /* Для Safari */
  -moz-appearance: none; /* Для Firefox */
  background: white; /* Обязательно задаём фон */
  background-image: none; /* Убираем встроенные стрелки */
}

.arrow-button {
  padding: 4px 8px;
  cursor: pointer;
  border: none;
  border-radius: 4px;
  font-size: 14px;
  transition: background-color 0.2s;
}

.sort-control select {
  padding: 8px;
  font-size: 14px;
  border: 1px solid #ddd;
  border-radius: 4px;
  outline: none;
  transition: border 0.3s ease;
}

.sort-control select:focus {
  border: 1px solid #007bff;
}
.sort-label { color: #505050; }
.sort-item { color: #353535; }
.arrow-inactive { background-color: #eee; color: #666; }
.arrow-active   { background-color: #007bff; color: #fff; }
.reset-button {
  padding: 2px 6px;
  background: transparent;
  border: none;
  color: #ff6457; /* Красный цвет для крестика */
  cursor: pointer;
  font-size: 14px;
}
.reset-button:hover {
  color: #d32f2f; /* Более темный красный при наведении */
}
</style>
