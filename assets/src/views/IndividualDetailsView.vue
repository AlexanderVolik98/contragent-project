<template>
  <div class="individual-page">
    <h1>{{ individualTitle }}</h1>
    <div class="card card--details">
      <section class="individual-details">
        <h2>Основные данные</h2>
        <div class="individual-info">
          <!-- Блок с основными регистрационными данными -->
          <div class="individual-columns">
            <div class="details-column">
              <div class="detail-row">
                <b class="detail-key">ИНН:</b>
                <span class="detail-value"  :class="{'detail-value--empty': !individual.inn}">{{ individual.inn ?? 'нет данных' }}</span>
              </div>
              <div class="detail-row">
                <b class="detail-key">ОГРНИП:</b>
                <span class="detail-value" :class="{'detail-value--empty': !individual.ogrnip}">{{ individual.ogrnip ?? 'нет данных' }}</span>
              </div>
            </div>
            <div class="details-column">
              <div class="detail-row">
                <b class="detail-key">ОКПО:</b>
                <span class="detail-value" :class="{'detail-value--empty': !individual.okpo}">{{ individual.okpo ?? 'нет данных' }}</span>
              </div>
              <div class="detail-row">
                <b class="detail-key">ОКАТО:</b>
                <span class="detail-value" :class="{'detail-value--empty': !individual.okato}">{{ individual.okato ?? 'нет данных' }}</span>
              </div>
              <div class="detail-row">
                <b class="detail-key">ОКТМО:</b>
                <span class="detail-value" :class="{'detail-value--empty': !individual.oktmo}">{{ individual.oktmo ?? 'нет данных' }}</span>
              </div>
              <div class="detail-row">
                <b class="detail-key">ОКОГУ:</b>
                <span class="detail-value" :class="{'detail-value--empty': !individual.okogu}">{{ individual.okogu ?? 'нет данных' }}</span>
              </div>
            </div>
            <div class="details-column">
              <div class="detail-row">
                <b class="detail-key">ОКФС:</b>
                <span class="detail-value" :class="{'detail-value--empty': !individual.okfs}">{{ individual.okfs ?? 'нет данных' }}</span>
              </div>
            </div>
          </div>
          <!-- Статус компании с акцентом и датами -->
          <div class="detail-row status-row" v-if="individual.status">
            <span><b class="detail-key">Статус:</b></span>
            <span class="detail-value">
              <span :class="statusColorClass">{{ statusText }}</span>
              <span class="status-dates">| дата регистрации: {{ individual.status?.registrationDate }} | дата последних изменений: {{ individual.status?.actuallyDate }}</span>
            </span>
          </div>
          <span v-if="individual.status?.comment" class="status-comment" :class="statusColorClass">
            {{ individual.status.liquidationDate ?? '' }} {{ individual.status.comment }}
          </span>
          <!-- Основной вид деятельности по ОКВЭД -->

          <div class="detail-row" v-if="individual.okved">
            <span class="detail-value">{{ individual.okved?.name ?? 'нет данных' }}</span>
          </div>
          <!-- Вторичные виды деятельности с возможностью раскрытия -->
          <div v-if="individual.secondaryOkveds.length !== 0" class="detail-row clickable" @click="toggleOkveds">
            <b class="detail-key link">Остальные виды дея-сти по ОКВЭД:</b>
            <span class="toggle-icon">{{ showOkveds ? '▼' : '▶' }}</span>
          </div>
          <ul v-if="showOkveds" class="secondary-okveds-list">
            <li v-for="okved in individual.secondaryOkveds" :key="okved.code">
              <div class="detail-row okved-item">
                <b class="detail-key">{{ okved.code }}</b>
                <span class="detail-value">{{ okved.name }}</span>
              </div>
            </li>
          </ul>
          <div v-if="individual.address" class="detail-row">
            <b class="detail-key">Город:</b>
            <span class="detail-value">{{ individual.address }}</span>
          </div>
          <div class="detail-row managers-title"><b class="detail-key">Руководитель:</b></div>
          <div class="manager-wrapper" v-if="individual.managedCompanies.length > 0">
            <div class="manager-card manager-item" v-for="managedCompany in individual.managedCompanies" :key="managedCompany.inn">
              <div class="manager-header">
                <span class="manager-post">{{ managedCompany.post.toLowerCase() }} с {{
                    managedCompany.startDate
                  }}</span>
              </div>
              <a :href="getCompanyRoute(managedCompany)" class="link manager-link">
                {{ managedCompany.name }}
              </a>
              <span class="manager-inn">ИНН: {{ managedCompany.inn }}</span>
            </div>
          </div>
          <div v-if="individual.taxService !== null" class="detail-row">
            <b class="detail-key">Налоговый орган:</b>
            <span class="detail-value">{{ individual.taxService.name }}</span>
            <span class="authority-date">Дата постановки на учет: {{ individual.taxReport.issueDate }}</span>
          </div>
          <div v-if="individual.pensionFond !== null" class="detail-row">
            <b class="detail-key">Пенсионный фонд:</b>
            <div class="detail-value pension-fond-block">
              <span class="detail-value">{{ individual.pensionFond.name }}</span>
              <span class="authority-date">Дата постановки на учет: {{ individual.pensionFondRegistration.issueDate }}</span>
            </div>
          </div>
          <div v-if="individual.socialInsuranceFond !== null" class="detail-row">
            <b class="detail-key">Фонд соц. страхования:</b>
            <div class="detail-value pension-fond-block">
              <span class="detail-value">{{ individual.socialInsuranceFond.name }}</span>
              <span class="authority-date">Дата постановки на учет: {{ individual.socialInsuranceFondRegistration.issueDate }}</span>
            </div>
          </div>
        </div>
      </section>
    </div>
    <!-- Динамический блок с дополнительными секциями (контакты, филиалы, финансы, лицензии) -->
    <div class="cards-row">
      <component
          v-for="block in sortedBlocks"
          :is="block.component"
          :key="block.key"
          v-bind="block.props"
      />
    </div>
  </div>
</template>

<script>
import ContactsBlock from '../components/ContactsBlock.vue';
import FilialsBlock from '../components/FilialsBlock.vue';
import FinancesBlock from '../components/FinancesBlock.vue';
import LicensesBlock from '../components/LicensesBlock.vue';
import { formatNumber } from '../app';

export default {
  name: 'IndividualDetails',
  props: {
    individual: {
      type: Object,
      required: true
    }
  },
  data() {
    return {
      showOkveds: false
    };
  },
  computed: {
    individualTitle() {
      return `${this.individual.surname} ${this.individual.name} ${this.individual.patronymic}`;
    },
    statusText() {
      const statusMap = {
        LIQUIDATED: 'Ликвидирован',
        ACTIVE: 'Действующий',
        SUSPENDED: 'Приостановлен',
        INACTIVE: 'Неактивен',
        REORGANIZING: 'Реорганизация',
        UNDEFINED: 'Неизвестен'
      };
      return statusMap[this.individual.status?.name] || this.individual.status?.name;
    },
    statusColorClass() {
      const colorMap = {
        LIQUIDATED: 'red-status',
        ACTIVE: 'green-status',
        SUSPENDED: 'orange-status',
        REORGANIZING: 'orange-status',
        INACTIVE: 'grey-status',
        UNDEFINED: 'purple-status'
      };
      return colorMap[this.individual?.status?.name] || '';
    },
    // Формируем динамический массив блоков с учетом приоритета
    sortedBlocks() {
      const blocks = [];
      // Блок с контактами (приоритет 1)
      if (
          (this.individual.phones && this.individual.phones.length) ||
          (this.individual.emails && this.individual.emails.length)
      ) {
        blocks.push({
          key: 'contacts',
          component: ContactsBlock,
          priority: 1,
          props: { individual: this.individual }
        });
      }
      // Блок филиалов (приоритет 2)
      if (this.individual.filials && this.individual.filials.length > 0) {
        blocks.push({
          key: 'filials',
          component: FilialsBlock,
          priority: 2,
          props: { filials: this.individual.filials }
        });
      }
      // Блок финансов (приоритет 3)
      if (this.individual.finances) {
        blocks.push({
          key: 'finances',
          component: FinancesBlock,
          priority: 3,
          props: { finances: this.individual.finances }
        });
      }
      // Блок лицензий (приоритет 10)
      if (this.individual.licenses && this.individual.licenses.length > 0) {
        blocks.push({
          key: 'licenses',
          component: LicensesBlock,
          priority: 10,
          props: { licenses: this.individual.licenses }
        });
      }
      return blocks.sort((a, b) => a.priority - b.priority);
    }
  },
  methods: {
    formatNumber,
    toggleOkveds() {
      this.showOkveds = !this.showOkveds;
    },
    getManagerRoute(manager) {
      return manager.type === 'individual'
          ? `/individual/${manager.slug}`
          : `/individual/${manager.slug}`;
    },
    getCompanyRoute(managedCompany) {
      return '/company/' + managedCompany.slug
    }
  }
};
</script>

<style scoped>
.individual-page {
  display: flex;
  flex-direction: column;
  gap: 20px;
  max-width: 1200px;
  margin: 0 auto;
  padding: 20px;
}

.individual-details {
  display: flex;
  flex-direction: column;
  gap: 30px;
  margin: 10px;
}

.individual-info {
  display: flex;
  flex-direction: column;
  gap: 5px;
}

.individual-columns {
  display: flex;
  gap: 40px;
  align-items: flex-start;
  justify-content: center;
  margin: 0 50px;
}

.detail-value--empty {
  font-style: italic;
  color: #bbb;
}

.details-column {
  display: flex;
  flex-direction: column;
  gap: 5px;
}

.detail-row {
  display: flex;
  flex-direction: row;
  gap: 3px;
  align-items: baseline;
}

.detail-row__filial {
  font-size: 14px;
}

.filial-add-value {
  color: #616161;
}

.detail-row b {
  min-width: 50px;
  text-align: left;
}

.detail-value {
  font-size: 14px;
  display: flex;
  flex-direction: row;
  gap: 5px;
}

.detail-key {
  color: #233461;
}

.detail-value--highlight {
  font-weight: bold;
}

.detail-value--small {
  font-size: 12px;
}

.status-comment {
  font-size: 13px;
  margin-left: 10px;
}

.status-row {
  background-color: #f0f8ff;
  padding: 10px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  gap: 10px;
}

.status-dates {
  font-size: 12px;
  color: #505050;
}

.red-status {
  color: red;
}

.green-status {
  color: green;
}

.orange-status {
  color: orange;
}

.grey-status {
  color: grey;
}

.purple-status {
  color: purple;
}

.managers-title {
  margin-top: 20px;
  font-size: 16px;
  color: #233461;
}

.manager-card {
  background: #f9f9f9;
  padding: 10px;
  display: flex;
  flex-wrap: wrap;
  max-width: 300px;
  flex-direction: column;
  border-radius: 8px;
  border: 1px solid #ddd;
}

.manager-header {
  font-size: 14px;
  color: #333;
  margin-bottom: 5px;
}

.manager-wrapper {
  display: flex;
  flex-wrap: wrap; /* Элементы будут переноситься на следующую строку */
  gap: 16px; /* Отступы между элементами */
}

.manager-item {
  flex: 0 0 calc(50% - 8px); /* Каждый элемент будет занимать 50% ширины с учетом отступов */
  box-sizing: border-box;
}

.manager-link {
  font-size: 16px;
  font-weight: bold;
  color: #007bff;
  text-decoration: none;
}

.manager-link:hover {
  text-decoration: underline;
}

.manager-inn {
  font-size: 12px;
  color: grey;
}

.cards-row {
  display: flex;
  flex-wrap: wrap;
  gap: 20px;
  justify-content: space-between; /* Равномерное распределение по ширине */
}

/* Новый блок стилей для Пенсионного фонда */
.pension-fond-block {
  display: flex;
  flex-direction: column;
}

.pension-fond-name {
  font-size: 14px;
  color: #333;
}

.authority-date {
  font-size: 12px;
  color: #616161;
}

.link:hover {
  text-decoration: underline;
}

.predecessors-row {
  display: flex;
  flex-direction: row;
  gap: 15px;
}

.successors-row {
  display: flex;
  flex-direction: row;
  gap: 15px;
}
</style>
