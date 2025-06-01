<template>
  <div class="search-results" v-if="items && items.length">
    <div
        v-for="subject in items"
        :key="subject.id"
        class="card"
        @mouseenter="hover = subject.id"
        @mouseleave="hover = null"
        :class="[ getCardClass(subject.id), { 'search-card': isSearchPage }, { 'card-fixed-min-width': !isSearchPage }  ]"
    >
      <a :href="getSubjectRoute(subject)" class="result-subject">

        <!-- Header with title and status badge -->
        <div class="result-header">
          <b>
            <span class="details-value">
              {{ subject.entity_type === 'Company'
                ? subject.opf_abbreviation + ` \"${subject.name}\"`
                : subject.ogrnip ? 'ИП ' + subject.name : subject.name
              }}
            </span>
          </b>
          <span
              v-if="subject.state_status && subject.state_status !== 'UNDEFINED'"
              class="status-badge"
              :class="subject.state_status"
          >
            {{ defineCyrillicStatus(subject.state_status, subject.entity_type) }}
          </span>
          <!-- Дата регистрации -->
          <span
              v-if="subject.registration_date_int"
              class="registration-date"
          >
  {{
              new Date(
                  Number(String(subject.registration_date_int).slice(0, 4)),       // год
                  Number(String(subject.registration_date_int).slice(4, 6)) - 1,  // месяц (ноль-индекс)
                  Number(String(subject.registration_date_int).slice(6, 8))       // день
              ).toLocaleDateString('ru-RU', { day: '2-digit', month: '2-digit', year: 'numeric' })
            }}
</span>
        </div>
        <span class="details-value details-value--okved">{{ subject.region_name ? subject.region_name + ',' : '' }} {{ subject.okved_name ?? '' }}</span>

        <div v-if="subject.entity_type === 'Company'" class="company-details">
          <div class="company-details-key-value">
            <div class="result-info-oneline-key-value">
              <div class="result-info">
                <b>ИНН:</b>
                <span class="details-value">{{ subject.inn }}</span>
              </div>
              <div class="result-info">
                <b>ОГРН:</b>
                <span class="details-value">{{ subject.ogrn }}</span>
              </div>
              <div class="result-info">
                <b>КПП:</b>
                <span class="details-value">{{ subject.kpp }}</span>
              </div>
            </div>
          </div>
          <div class="company-details-type-icon">
          </div>
        </div>

        <div v-else class="individual-details">
          <div class="individual-details-key-value">
            <div class="result-info-oneline-key-value">
              <div class="result-info">
                <b>ИНН:</b>
                <span class="details-value">{{ subject.inn ?? '-' }}</span>
              </div>
              <div v-if="subject.ogrnip" class="result-info">
                <b>ОГРНИП:</b>
                <span class="details-value">{{ subject.ogrnip ?? '-' }}</span>
              </div>
            </div>

            <span class="details-value details-value--okved">{{ subject.okved?.name ?? '' }}</span>
          </div>
          <div class="individual-details-type-icon">
          </div>
        </div>

      </a>
    </div>

    <div v-if="!isSearchPage" class="search-button">
      <a href="/search" class="extended-search-button">
        Все результаты и фильтры →
      </a>
    </div>

    <div v-if="isSearchPage && pagination" class="pagination-container">
      <button
          class="pagination-button"
          :disabled="pagination.currentPage === 1"
          @click="changePage(pagination.currentPage - 1)"
      >
        Назад
      </button>

      <template v-for="page in paginationPages" :key="page.key">
        <span v-if="page.isEllipsis" class="pagination-ellipsis">...</span>
        <button
            v-else
            class="pagination-button"
            :class="{ active: page.number === pagination.currentPage }"
            @click="changePage(page.number)"
        >
          {{ page.number }}
        </button>
      </template>

      <button
          class="pagination-button"
          :disabled="pagination.currentPage === pagination.totalPages"
          @click="changePage(pagination.currentPage + 1)"
      >
        Вперёд
      </button>
    </div>
  </div>

  <div v-else-if="noResults" class="card">
    <div class="block-not-found">
      <img class="svg-icon-question" :src="require('../assets/svg/question.svg')" alt="question icon">
      <span class="block-not-found-text">
        По Вашему запросу ничего не найдено. <br>
        Если ищете по ФИО или названию компании, попробуйте поискать по ИНН или ОГРН.
      </span>
    </div>
  </div>

  <div v-if="serverError" class="card">
    <div class="block-error">
      <img class="svg-icon-error" :src="require('../assets/svg/error.svg')" alt="server error icon">
      <span class="block-error-text">На нашей стороне что-то пошло не так, попробуйте поиск позднее...</span>
    </div>
  </div>
</template>

<script>
export default {
  name: "SearchResults",
  props: {
    results: { type: [Array, Object], required: true },
    noResults: Boolean,
    serverError: Boolean,
    isSearchPage: { type: Boolean, default: false }
  },
  data() {
    return { hover: null };
  },
  computed: {
    items() {
      return Array.isArray(this.results.items) ? this.results.items : [];
    },
    pagination() {
      return this.isSearchPage ? this.results.pagination : null;
    },
    paginationPages() {
      if (!this.pagination) return [];
      const { currentPage, totalPages } = this.pagination;
      const pages = [];
      let key = 1;
      if (totalPages <= 7) {
        for (let i = 1; i <= totalPages; i++) pages.push({ number: i, isEllipsis: false, key: key++ });
      } else {
        pages.push({ number: 1, isEllipsis: false, key: key++ });
        if (currentPage > 4) pages.push({ isEllipsis: true, key: key++ });
        const start = Math.max(2, currentPage - 1);
        const end = Math.min(totalPages - 1, currentPage + 1);
        for (let i = start; i <= end; i++) pages.push({ number: i, isEllipsis: false, key: key++ });
        if (currentPage < totalPages - 3) pages.push({ isEllipsis: true, key: key++ });
        pages.push({ number: totalPages, isEllipsis: false, key: key++ });
      }
      return pages;
    }
  },
  methods: {
    getSubjectRoute(subject) {
      if (subject.entity_type) {
      return subject.entity_type == 'Company'
          ? `/company/${subject.slug}`
          : `/individual/${subject.slug}`;
      } else {
        return subject.type === 'company'
            ? `/company/${subject.slug}`
            : `/individual/${subject.slug}`;
      }
    },
    defineCyrillicStatus(status, type) {
      const map = {
        Individual: {
          LIQUIDATED: 'Ликвидировано', LIQUIDATING: 'Ликвидируется', ACTIVE: 'Активно', REORGANIZING: 'Реорганизуется', BANKRUPT: 'Банкрот'
        },
        Company: {
          LIQUIDATED: 'Ликвидировано', LIQUIDATING: 'Ликвидируется', ACTIVE: 'Активно', REORGANIZING: 'Реорганизуется', BANKRUPT: 'Банкрот'
        }
      };
      return map[type][status] || status;
    },
    getCardClass(id) { return this.hover === id ? 'hover-card' : ''; },
    changePage(newPage) {
      if (newPage === this.pagination.currentPage) return;
      this.$emit('page-changed', newPage);
    }
  }
};
</script>

<style scoped>
.search-results { display: flex; flex-direction: column; gap: 15px; margin-bottom: 70px; }
.result-subject { text-decoration: none; color: inherit; display: flex; flex-direction: column; gap: 5px; }
.result-header { display: flex; align-items: center; gap: 8px; margin-bottom: 4px; }
.status-badge { padding: 2px 6px; border-radius: 12px; font-size: 12px; font-weight: 500; }
.status-badge.ACTIVE     { background: #e6ffed; color: #007a33; }
.status-badge.LIQUIDATED { background: #ffe6e6; color: #a30000; }
.status-badge.REORGANIZING { background: #65acff; color: #fff; }
.status-badge.SUSPENDED  { background: #fff8e6; color: #a36b00; }
.status-badge.BANKRUPT   { background: #f2f2f2; color: #666; }
.registration-date {
  margin-left: auto;
  font-size: 12px;
  color: #888; /* сероватый цвет */
  white-space: nowrap;
}
.individual-details, .company-details { display: flex; flex-direction: row; gap: 20px; }
.company-details-key-value, .individual-details-key-value { display: flex; flex-direction: column; flex-grow: 1; gap: 1px; }
.result-info-oneline-key-value { display: flex; flex-direction: row; gap: 20px; }
.search-card { max-width: 730px; min-width: 730px; }
.hover-card { border: 1px solid #007bff !important; }
.pagination-button { padding: 8px 16px; border: 1px solid #ccc; background-color: #f9f9f9; font-size: 14px; border-radius: 4px; cursor: pointer; transition: background-color 0.2s, border-color 0.2s; }
.pagination-container { display: flex; flex-direction: row; gap: 10px; }
.pagination-button:hover:not(:disabled) { background-color: #e6e6e6; border-color: #bbb; }
.pagination-button.active { background-color: #007bff; border-color: #007bff; color: #fff; font-weight: bold; }
.pagination-button:disabled { opacity: 0.5; cursor: not-allowed; }
.pagination-ellipsis { padding: 8px 16px; font-size: 14px; }
</style>
