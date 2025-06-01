<template>
  <div class="search-container">
    <!-- Компонент поисковой строки -->
    <SearchInput
        @update:query="updateQuery"
        @search-button-click="triggerSearch"
        @reset-filters="$emit('reset-filters')"
        @search-readiness-changed="(value) => $emit('search-readiness-changed', value)"
        :isLoading="isLoading"
        :isSearchPage="isSearchPage"
        :applied-filters-count="filtersCount"
    />
    <!-- Sort Control сразу после SearchInput -->
    <div class="row-under-search">
    <SortControl v-if="isSearchPage"
        :options="sortOptions"
        :initialField="sort.field"
        :initialDirection="sort.direction"
        :default-field="sortOptions[2]"
        @update:sort="handleSortChange"
        :default-direction="asc"
        />

      <span v-if="results.pagination.totalItems" class="details-value--res-count">найдено <span class="count-finded-subjects">{{ results.pagination.totalItems }}</span> субъектов</span>
    </div>
    <SearchResults
        :results="results"
        :noResults="noResults"
        :serverError="serverError"
        :isSearchPage="isSearchPage"
        @page-changed="handlePageChanged"
    />
  </div>
</template>

<script setup>
import { ref, watch, computed } from 'vue';
import axios from 'axios';
import SearchInput from './SearchInput.vue';
import SearchResults from './SearchResults.vue';
import SortControl from "./SortControl.vue";

const props = defineProps({
  filters: {
    type: Object,
    default: () => ({})
  },
  isSearchPage: {
    type: Boolean,
    default: false
  },
  identFromHomePage: {
    default: null
  }
});

const emit = defineEmits(['reset-filters', 'search-readiness-changed']);

// Состояния для поисковой строки и фильтров
const query = ref('');              // значение поисковой строки
const filtersQuery = ref('');       // строка, собранная из фильтров
// Режим поиска: 'query' (только поисковая строка) или 'filter' (по фильтрам + поисковая строка)
const lastSearchMode = ref('query');

// Результаты запроса и состояния загрузки
const results = ref({ items: [], pagination: {}, totalItems: 0 });
const isLoading = ref(false);
const noResults = ref(false);
const searchReadiness = ref(false);
const filtersCount = ref(0);
const serverError = ref(false);
const currentPage = ref(1);
let debounceTimeout = null;

// Обновление поискового запроса (из SearchInput)
const updateQuery = (newQuery) => {
  query.value = newQuery;
  lastSearchMode.value = 'query';
};

const sort = ref({ field: 'state_status', direction: 'asc' });
const sortOptions = [
  { value: 'capital_value', label: 'Капиталу' },
  { value: 'state_status', label: 'Статусу' },
  { value: 'registration_date_int', label: 'Дате регистрации' },
];

// Позволяет задать query извне (если потребуется)
const setQuery = (newQuery) => {
  query.value = newQuery;
};

// Функция для отправки запроса
const fetchResults = (apiUrl, page = 1, params) => {
  isLoading.value = true;
  axios.get(apiUrl, { params })
      .then(response => {
        results.value = {
          items: response.data.items,
          pagination: response.data.pagination
        };
        noResults.value = (response.data.items && response.data.items.length === 0);
      })
      .catch(error => {
        if (error.response) {
          if (error.response.status === 404) {
            noResults.value = true;
          }
          if (error.response.status >= 500 && error.response.status < 600) {
            serverError.value = true;
          }
        }
        results.value = { items: [], pagination: {} };
      })
      .finally(() => {
        isLoading.value = false;
      });
};

// Основной метод запуска поиска. Собираем URLSearchParams из разных блоков.
const triggerSearch = (page = 1) => {
  noResults.value = false;
  serverError.value = false;
  currentPage.value = page;

  const params = new URLSearchParams();
  params.append('page', page);
  // Указываем, что это страница поиска (чтобы на бэке использовать фильтры)
  params.append('search-page', props.isSearchPage);

  // Если режим "query" (поисковая строка, без фильтров)
  if (lastSearchMode.value === 'query' && query.value.trim() !== '') {
    params.append('identifier', query.value.trim());
  }
  // добавляем сортировку
  params.append('sort', sort.value.field);
  params.append('dir', sort.value.direction);

  console.log(true)
  // Если режим "filter" (фильтры + поисковая строка)
  if (lastSearchMode.value === 'filter' && filtersQuery.value) {
    // Разбираем строку фильтров и добавляем каждую пару параметров.
    const parsedFilters = new URLSearchParams(filtersQuery.value);
    for (const [key, value] of parsedFilters.entries()) {
      params.append(key, value);
    }
    // Если поисковая строка не пуста, добавляем параметр identifier.
    if (query.value.trim() !== '') {
      params.append('identifier', query.value.trim());
    }
  }

  // Собираем URL для запроса
  const apiUrl = '/api/subject';
  fetchResults(apiUrl, page, params);
};

// Метод, вызываемый из родительского компонента при нажатии "Применить" в фильтрах.
// Здесь filtersQuery передается из фильтров, а поисковая строка остается в query.
const applyFiltersSearch = (filtersQs) => {
  filtersQuery.value = filtersQs;
  lastSearchMode.value = 'filter';
  triggerSearch();
};

const updateFiltersCount = (count) => {
  filtersCount.value = count;
}

// Обработчик смены сортировки
const handleSortChange = ({field, direction}) => {
  // Сохраняем новые параметры сортировки
  sort.value = {field, direction};
  // При смене сортировки сбрасываем страницу на первую
  currentPage.value = 1;
  // Если уже есть данные — сразу делаем запрос с новой сортировкой
  if (results.value.items.length > 0) {
    triggerSearch(1);
  }
  // Иначе просто сохранили sort.value,
  // запрос будет отправлен при следующем triggerSearch от SearchInput или фильтров
};

// Если мы не на странице поиска, можно использовать дебаунс для автопоиска по строке.
if (!props.isSearchPage) {
  watch([query, () => props.filters], ([newQuery]) => {
    if (debounceTimeout) clearTimeout(debounceTimeout);
    debounceTimeout = setTimeout(() => {
      noResults.value = false;
      serverError.value = false;
      if (newQuery.trim() !== '' && lastSearchMode.value === 'query') {
        triggerSearch();
      } else {
        results.value = { items: [], pagination: {} };
        isLoading.value = false;
      }
    }, 200);
  });
}

// Вычисляем количество примененных фильтров для отображения в SearchInput
const appliedFiltersCount = computed(() => {
  let count = 0;
  const f = props.filters;
  for (const key in f) {
    const val = f[key];
    if (typeof val === 'boolean' && val) count++;
    if (typeof val === 'string' && val.trim() !== '') count++;
    if (Array.isArray(val) && val.length > 0) count++;
  }
  return count;
});

const handlePageChanged = (newPage) => {
  triggerSearch(newPage);
};

// Экспонируем методы и переменные для использования из родительского компонента через ref
defineExpose({
  updateQuery,
  setQuery,
  triggerSearch,
  handlePageChanged,
  applyFiltersSearch,
  updateFiltersCount,
  query,
  results,
  noResults,
  serverError,
  isLoading,
  appliedFiltersCount,
  lastSearchMode
});
</script>

<style scoped>
.search-container {
  display: flex;
  flex-direction: column;
  margin: 20px auto;
  gap: 5px;
  padding: 0 15px;
}

.row-under-search {
  margin-bottom: 25px;
  display: flex;
  min-width: 750px;
  flex-direction: row;
  align-items: center;
  justify-content: space-between;
}

.details-value--res-count {
  color: #333333;
}

.search-controls {
  display: flex;
  align-items: center;
  gap: 20px;
}
.search-applied-filters {
  align-self: end;
}

.count-finded-subjects {
  color: #1a2e5a;
}
</style>
