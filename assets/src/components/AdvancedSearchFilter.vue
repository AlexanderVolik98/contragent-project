<template>
  <div class="advanced-search-container">
    <div class="card card--filter">
      <h2>Фильтры</h2>
      <form @submit.prevent>
        <!-- Тип субъекта -->
        <div class="filter-field">
          <label>Тип субъекта:</label>
          <div class="subject-type-tags">
            <label
                v-for="type in availableSubjectTypes"
                :key="type.value"
                :class="['subject-type-tag', { 'tag--selected': filters.subjectType.includes(type.value) }]"
                @click="toggleSubjectType(type.value)"
            >
      <span class="checkbox-custom">
        <svg v-if="filters.subjectType.includes(type.value)" class="checkbox-check" viewBox="0 0 16 16">
          <path d="M6 10.17L3.53 7.7l-1.06 1.06L6 12.29l8-8-1.06-1.06z" />
        </svg>
      </span>
              <span class="subject-type-label">{{ type.label }}</span>
            </label>
          </div>
        </div>
        <!-- Селектор регионов с автодополнением -->
        <div class="filter-field region-filter" v-click-outside="hideDropdown">
          <label for="region">Регионы:</label>
          <div class="region-selector">
            <input
                type="text"
                id="region"
                v-model="filters.region"
                @focus="showDropdown = true"
                placeholder="Введите или выберите регион"
            />
            <div v-if="showDropdown" class="dropdown">
              <ul>
                <li
                    class="sort-item"
                    v-for="region in filteredRegions"
                    :key="region.id"
                    @click="selectRegion(region)"
                >
                  {{ region.name }}
                </li>
              </ul>
            </div>
          </div>
        </div>

        <!-- Статусы -->
        <div class="filter-field">
          <label>Статусы:</label>
          <div class="status-tags">
            <label
                v-for="status in availableStatuses"
                :key="status"
                :class="['status-tag', statusClass(status), { 'tag--selected': filters.statuses.includes(status) }]"
            >
              <input
                  type="checkbox"
                  class="status-checkbox"
                  :value="status"
                  v-model="filters.statuses"
              />
              <span class="checkbox-custom">
                <svg v-if="filters.statuses.includes(status)" class="checkbox-check" viewBox="0 0 16 16">
                  <path d="M6 10.17L3.53 7.7l-1.06 1.06L6 12.29l8-8-1.06-1.06z" />
                </svg>
              </span>
              <span class="status-label">{{ statusLabel(status) }}</span>
            </label>
          </div>
        </div>

        <!-- Уставной капитал (от и до) -->
        <div class="filter-field capital-field">
          <label>Капитал:</label>
          <div class="capital-inputs">
            <input
                type="number"
                v-model="filters.capitalMin"
                placeholder="от"
                :disabled="isCapitalDisabled"
            />
            <span class="capital-separator">-</span>
            <input
                type="number"
                v-model="filters.capitalMax"
                placeholder="до"
                :disabled="isCapitalDisabled"
            />
          </div>
        </div>

        <!-- Дата регистрации (с и по) -->
        <div class="filter-field date-field">
          <label>Дата регистрации:</label>
          <div class="date-inputs">
            <flat-pickr
                v-model="filters.registrationDateFrom"
                :config="dateConfig"
                class="flatpickr-wrapper"
                placeholder="с" />
            <span class="capital-separator">-</span>
            <flat-pickr
                v-model="filters.registrationDateTo"
                :config="dateConfig"
                class="flatpickr-wrapper"
                placeholder="по" />
          </div>
        </div>

        <!-- Кнопка "Применить" -->
        <div class="filter-field apply-field">
          <div class="button-tooltip-wrapper" @mouseenter="showTooltip = true" @mouseleave="showTooltip = false">
            <button
                type="button"
                :disabled="!searchReadiness"
                :class="{ disabled: !searchReadiness }"
                class="apply-button"
                @click="applyFilters"
            >
              Применить
            </button>
            <transition name="fade">
              <div v-if="!searchReadiness && showTooltip" class="tooltip">
                Введите поисковой запрос ⬈
              </div>
            </transition>
          </div>
        </div>

        <div class="filter-field reset-field">
          <button type="button" class="reset-button" @click="resetFilters">Сбросить фильтры</button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import FlatPickr from 'vue-flatpickr-component';
import 'flatpickr/dist/flatpickr.css';
import { Russian } from "flatpickr/dist/l10n/ru.js";

export default {
  name: 'AdvancedSearchFilter',
  components: { FlatPickr },
  props: {
    regions: { type: Array, required: true },
    searchReadiness: { type: Boolean, required: true },
  },
  data() {
    return {
      showTooltip: false,
      filters: {
        region: '',
        subjectType: ['Company', 'Individual'],
        statuses: ['ACTIVE', 'LIQUIDATED', 'LIQUIDATING', 'REORGANIZING', 'BANKRUPT'],
        capitalMin: '',
        capitalMax: '',
        registrationDateFrom: '',
        registrationDateTo: '',
        manager: '',
      },
      showDropdown: false,
      dateConfig: {
        dateFormat: "d.m.Y",
        altInput: false,
        locale: Russian,
        altFormat: "d/m/Y",
        allowInput: true,
      },
    };
  },
  directives: {
    clickOutside: {
      mounted(el, binding) {
        el.clickOutsideEvent = event => { if (!el.contains(event.target)) binding.value(event); };
        document.addEventListener('click', el.clickOutsideEvent);
      },
      unmounted(el) { document.removeEventListener('click', el.clickOutsideEvent); }
    }
  },
  computed: {
    filteredRegions() {
      if (!this.filters.region) return this.regions;
      const search = this.filters.region.toLowerCase();
      return this.regions.filter(r => r.name.toLowerCase().includes(search));
    },
    availableStatuses() {
      return ['ACTIVE', 'LIQUIDATING', 'LIQUIDATED', 'REORGANIZING', 'BANKRUPT'];
    },
    availableSubjectTypes() {
      return [
        { label: 'Компании', value: 'Company' },
        { label: 'ИП/Руководители', value: 'Individual' },
      ];
    },
    isCapitalDisabled() {
      return this.filters.subjectType.length === 1 && this.filters.subjectType[0] === 'Individual';
    },
    appliedFiltersCount() {
      let count = 0;
      if (this.filters.region.trim()) count++;
      if (this.filters.subjectType.length !== this.availableSubjectTypes.length) count++;
      if (this.filters.statuses.length !== this.availableStatuses.length) count++;
      if (this.filters.capitalMin || this.filters.capitalMax) count++;
      if (this.filters.registrationDateFrom || this.filters.registrationDateTo) count++;
      return count;
    }
  },
  methods: {
    selectRegion(region) {
      this.filters.region = region.name;
      this.showDropdown = false;
      this.$emit('region-selected', region);
    },
    hideDropdown() {
      this.showDropdown = false;
    },
    toggleSubjectType(type) {
      const i = this.filters.subjectType.indexOf(type);
      if (i !== -1) {
        if (this.filters.subjectType.length > 1) this.filters.subjectType.splice(i, 1);
      } else {
        this.filters.subjectType.push(type);
      }
    },
    statusLabel(s) {
      const m = {
        ACTIVE: 'Активные',
        LIQUIDATED: 'Ликвидированные',
        LIQUIDATING: 'Ликвидируются',
        REORGANIZING: 'Реорганизация',
        BANKRUPT: 'Банкроты'
      };
      return m[s];
    },
    statusClass(s) {
      return {
        ACTIVE: 'green-status',
        BANKRUPT: 'grey-status',
        LIQUIDATED: 'red-status',
        LIQUIDATING: 'yellow-status',
        REORGANIZING: 'blue-status',
      }[s];
    },
    applyFilters() {
      this.$emit('apply-filters');
    },
    resetFilters() {
      this.filters = {
        region: '',
        subjectType: ['Company', 'Individual'],
        statuses: ['ACTIVE', 'LIQUIDATED', 'LIQUIDATING', 'BANKRUPT', 'REORGANIZING'],
        capitalMin: '',
        capitalMax: '',
        registrationDateFrom: '',
        registrationDateTo: '',
        manager: '',
      };
      this.showDropdown = false;
      this.showTooltip = false;
      this.$emit('update-filters', { ...this.filters });
    }
  },
  watch: {
    filters: {
      handler(newF) {
        this.$emit('update-filters', { ...newF });
        this.$emit('filter-count', this.appliedFiltersCount);
      },
      deep: true
    }
  },
  mounted() {
    this.$emit('update-filters', { ...this.filters });
  }
};
</script>

<style scoped>
.advanced-search-container { display: flex; gap: 20px; max-height: 620px; padding: 20px; }
.card--filter { padding: 20px; border: 1px solid #ddd; border-radius: 4px; max-width: 300px; background: #fff; }
.filter-field { margin-bottom: 15px; display: flex; flex-direction: column; }
.filter-field label { font-size: 14px; margin-bottom: 5px; color: #233461; }
.filter-field input[type="text"], .filter-field input[type="number"], .filter-field select {
  padding: 8px; font-size: 14px; border: 1px solid #ddd; border-radius: 4px; outline: none; transition: border 0.3s;
}
.filter-field input:focus, .filter-field select:focus { border: 1px solid #007bff; }
.region-selector { position: relative; }
.dropdown { position: absolute; z-index: 1000; top: 100%; left: 0; right: 0; background: #fff; border: 1px solid #ddd; max-height: 200px; overflow-y: auto; }
.dropdown li { padding: 8px 12px; cursor: pointer; }
.dropdown li:hover { background: #f0f0f0; }
.tag-container { display: flex; flex-wrap: wrap; gap: 10px; }
.tag { padding: 8px 12px; font-size: 14px; border: 1px solid #ccc; border-radius: 4px; cursor: pointer; background: #f5f5f5; color: #233461; transition: background 0.3s, color 0.3s, border-color 0.3s; }
.tag--selected { background: #007bff; border-color: #007bff; color: #fff; }

/* Status tags */
.status-tags { display: flex; flex-wrap: wrap; gap: 8px; }
.status-tag { display: inline-flex; align-items: center; border: 1px solid #ddd; border-radius: 999px; padding: 4px 8px; cursor: pointer; transition: background-color 0.3s, border-color 0.3s; }
.status-checkbox { display: none; }
.checkbox-custom { width: 18px; height: 18px; background: #fff; border: 1px solid #ccc; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-right: 6px; }
.checkbox-check { width: 14px; height: 14px; fill: #233461; }
.green-status { background: #e8f5e9; border-color: #e0e0e0; }
.green-status.tag--selected { background: #28a745; border-color: #28a745; color: #fff }
.red-status { background: #f8d7da; border-color: #e0e0e0; }
.red-status.tag--selected { background: #e14857; border-color: #e14857; color: #fff }
.yellow-status { background: #fff9c4; border-color: #e0e0e0; }
.yellow-status.tag--selected { background: #ffc107; border-color: #ffc107; color: #000000 }
.blue-status { background: #cce5ff; border-color: #e0e0e0; }
.blue-status.tag--selected { background: #2d8cfd; border-color: #2d8cfd; color: #fff }
.grey-status { background: #f1f1f1; border-color: #e0e0e0; }
.grey-status.tag--selected { background: #6c757d; border-color: #6c757d; color: #fff }

.capital-field label { margin-bottom: 5px; }
.capital-inputs { display: flex; align-items: center; gap: 5px; }
.capital-inputs input[type="number"] { width: 80px; text-align: center; }
.capital-separator { font-size: 14px; color: #233461; }

.date-field label { margin-bottom: 5px; }
.date-inputs { display: flex; align-items: center; gap: 6px; }
::v-deep(.flatpickr-input) { width: 100px; min-width: 90px; max-width: 100px; padding: 6px 8px; font-size: 14px; box-sizing: border-box; }

.apply-field { margin-top: 20px; display: flex; justify-content: center; }
.apply-button { padding: 10px 20px; font-size: 14px; background: #007bff; border: none; border-radius: 4px; color: #fff; cursor: pointer; transition: background 0.3s; }
.apply-button:hover { background: #0056b3; }

.reset-field { margin-top: 10px; display: flex; justify-content: center; }
.reset-button { padding: 10px 20px; font-size: 14px; background: #f8d7da; border: none; border-radius: 4px; color: #721c24; cursor: pointer; transition: background 0.3s; }
.reset-button:hover { background: #f5c6cb; color: #491217; }

.disabled { background: #ccc; cursor: not-allowed; color: #666; pointer-events: none; }

/* Subject Type tags */
.subject-type-tags {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
}

.subject-type-tag {
  display: inline-flex;
  align-items: center;
  border: 1px solid #ddd;
  border-radius: 999px;
  padding: 4px 10px;
  cursor: pointer;
  transition: background-color 0.3s, border-color 0.3s;
  background: #f5f5f5;
}

.subject-type-tag.tag--selected {
  background: #007bff;
  border-color: #007bff;
  color: #fff;
}

.subject-type-label {
  font-size: 14px;
  color: #233461;
}

.subject-type-tag.tag--selected .subject-type-label {
  color: #fff;
}

.subject-type-tag .checkbox-custom {
  width: 18px;
  height: 18px;
  background: #fff;
  border: 1px solid #ccc;
  border-radius: 50%;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  margin-right: 6px;
}

.subject-type-tag .checkbox-check {
  width: 14px;
  height: 14px;
  fill: #233461;
}
.apply-button{width:100%;}
.button-tooltip-wrapper{
  position:relative;
  display:flex;
  width:100%;
  justify-content:center;
}

.flatpickr-wrapper {
  text-align: center;
}
.tooltip{position:absolute;top:-34px;left:50%;transform:translateX(-50%);
  background:#333;color:#fff;padding:6px 10px;border-radius:4px;
  font-size:12px;white-space:nowrap;pointer-events:none;}
.fade-enter-active,.fade-leave-active{transition:opacity .2s ease}
.fade-enter-from,.fade-leave-to{opacity:0}
</style>
