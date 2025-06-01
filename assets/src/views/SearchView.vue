<template>
  <h1>Расширенный поиск</h1>
  <div style="display: flex; flex-direction: row; margin-right: 300px">
    <!-- Фильтры поиска, которые сразу отправляют данные в родителя -->
    <advanced-search-filter
        ref="filter"
        @update-filters="handleFilters"
        @apply-filters="handleApplyFilters"
        @filter-count="handleCountFilters"
        :search-readiness="searchReadiness"
        :regions="regions">
    </advanced-search-filter>
    <!-- Компонент поиска, которому передаются фильтры и флаг, что это страница поиска -->
    <Search ref="search"
            :filters="filters"
            :isSearchPage="true"
            :identFromHomePage="identFromHomePage"
            @reset-filters="resetFiltersFromSearchInput"
            @search-readiness-changed="handleSearchReadinessChanged" />
  </div>
</template>

<script>
import Search from "./../components/Search.vue";
import AdvancedSearchFilter from "./../components/AdvancedSearchFilter.vue";

export default {
  name: "ExtendedSearch",
  props: {
    regions: {
      type: Array,
      required: true
    },
    identFromHomePage: {
      required: false
    }
  },
  components: {
    Search,
    AdvancedSearchFilter,
  },
  data() {
    return {
      filters: {}, // объект выбранных фильтров, получаемый из AdvancedSearchFilter
      searchReadiness: false
    };
  },
  methods: {
    handleCountFilters(count) {
      this.$refs.search.updateFiltersCount(count);
    },
    handleSearchReadinessChanged(value) {
      this.searchReadiness = value;
    },
    handleFilters(newFilters) {
      this.filters = newFilters;
      // Здесь можно обновлять объект фильтров, если нужно
    },
    handleApplyFilters() {
      const queryString = this.buildQueryFromFilters(this.filters);
      this.$refs.search.applyFiltersSearch(queryString);
    },
    buildQueryFromFilters(filters) {
      const params = new URLSearchParams();
      if (filters.region) {
        params.append('region', filters.region);
      }
      if (filters.subjectType && filters.subjectType.length > 0) {
        params.set('subjectType', filters.subjectType.join(','));
      }
      if (filters.statuses && filters.statuses.length > 0) {
        params.set('status', filters.statuses.join(','));
      }
      if (filters.capitalMin) {
        params.append('capitalMin', filters.capitalMin);
      }
      if (filters.capitalMax) {
        params.append('capitalMax', filters.capitalMax);
      }
      if (filters.registrationDateFrom) {
        params.append('dateFrom', filters.registrationDateFrom);
      }
      if (filters.registrationDateTo) {
        params.append('dateTo', filters.registrationDateTo);
      }
      if (filters.manager) {
        params.append('manager', filters.manager);
      }
      return params.toString();
    }
  }
};
</script>
