<template>
  <div class="company-page">
    <h1>{{ companyTitle }}</h1>
    <div class="card card--details">
      <section class="company-details">
        <h2>Основные данные</h2>
        <div class="company-info">
          <!-- Блок с основными регистрационными данными -->
          <div class="company-columns">
            <div class="details-column">
              <div class="detail-row opf-row">
                <b class="detail-key">
                  ОПФ:

                  <div
                      v-if="showOpfTooltip"
                      class="custom-tooltip"
                  >{{ company.opf.fullName }}</div>
                </b>
                <span class="detail-value detail-value--opf">
    {{ company.opf.abbreviation ?? 'нет данных' }}
                                    <span
                                        v-if="company.opf.fullName"
                                        class="tooltip-icon"
                                        @mouseenter="showOpfTooltip = true"
                                        @mouseleave="showOpfTooltip = false"
                                    >?</span>
  </span>
              </div>
              <div class="detail-row">
                <b class="detail-key">ИНН:</b>
                <span class="detail-value">{{ company.inn ?? 'нет данных' }}</span>
              </div>
              <div class="detail-row">
                <b class="detail-key">ОГРН:</b>
                <span class="detail-value">{{ company.ogrn ?? 'нет данных' }}</span>
              </div>
              <div class="detail-row">
                <b class="detail-key">КПП:</b>
                <span class="detail-value">{{ company.kpp ?? 'нет данных' }}</span>
              </div>
            </div>
            <div class="details-column">
              <div class="detail-row">
                <b class="detail-key">ОКПО:</b>
                <span class="detail-value">{{ company.okpo ?? 'нет данных' }}</span>
              </div>
              <div class="detail-row">
                <b class="detail-key">ОКАТО:</b>
                <span class="detail-value">{{ company.okato ?? 'нет данных' }}</span>
              </div>
              <div class="detail-row">
                <b class="detail-key">ОКТМО:</b>
                <span class="detail-value">{{ company.oktmo ?? 'нет данных' }}</span>
              </div>
              <div class="detail-row">
                <b class="detail-key">ОКОГУ:</b>
                <span class="detail-value">{{ company.okogu ?? 'нет данных' }}</span>
              </div>
            </div>
            <div class="details-column">
              <div class="detail-row">
                <b class="detail-key">ОКФС:</b>
                <span class="detail-value">{{ company.okfs ?? 'нет данных' }}</span>
              </div>
            </div>
          </div>
          <br>
          <!-- Статус компании с акцентом и датами -->
          <div class="detail-row status-row">
            <span><b class="detail-key">Статус:</b></span>
            <span class="detail-value detail-value--status">
              <span :class="statusColorClass">{{ statusText }}</span>
              <span class="status-dates">  дата регистрации: {{ company.status?.registrationDate }}  дата последних изменений: {{ company.status?.actuallyDate }}</span>
            </span>
          </div>
          <span v-if="company.status?.comment" class="status-comment" :class="statusColorClass">
            {{ company.status.liquidationDate ?? '' }} {{ company.status?.comment }}
          </span>
          <!-- Основной вид деятельности по ОКВЭД -->
          <div class="detail-row">
            <span class="detail-value details-value--okved">{{ company.okved?.name ?? 'нет данных' }}</span>
          </div>
          <!-- Вторичные виды деятельности с возможностью раскрытия -->
          <div v-if="company.secondaryOkveds.length !== 0" class="detail-row clickable" @click="toggleOkveds">
            <b class="detail-key link">Остальные виды дея-сти по ОКВЭД:</b>
            <span class="toggle-icon">{{ showOkveds ? '▼' : '▶' }}</span>
          </div>
          <ul v-if="showOkveds" class="secondary-okveds-list">
            <li v-for="okved in company.secondaryOkveds" :key="okved.code">
              <div class="detail-row okved-item">
                <b class="detail-key">{{ okved.code }}</b>
                <span class="detail-value">{{ okved.name }}</span>
              </div>
            </li>
          </ul>


          <licenses-block v-if="company.licenses !== null" :licenses="company.licenses"></licenses-block>

          <br>
          <!-- Финансовая информация и адрес -->
          <div class="detail-row">
            <b class="detail-key">Уставной капитал:</b>
            <span class="detail-value">{{ company.capital ? formatNumber(company.capital) : 'нет данных' }}</span>
          </div>
          <div class="detail-row">
            <b class="detail-key">Юридический адрес:</b>
            <span class="detail-value">{{ company.address }}</span>
          </div>
          <br>
          <div v-if="company.taxService !== null" class="detail-row">
            <b class="detail-key">Налоговый орган:</b>
            <span class="detail-value">{{ company.taxService.name }}</span>
            <span class="authority-date">Дата постановки на учет: {{ company.taxReport.issueDate }}</span>
          </div>
          <div v-if="company.pensionFond !== null" class="detail-row">
            <b class="detail-key">Пенсионный фонд:</b>
            <div class="detail-value pension-fond-block">
              <span class="detail-value">{{ company.pensionFond.name }}</span>
              <span class="authority-date">Дата постановки на учет: {{ company.pensionFondRegistration.issueDate }}</span>
            </div>
          </div>
          <div v-if="company.socialInsuranceFond !== null" class="detail-row">
            <b class="detail-key">Фонд соц. страхования:</b>
            <div class="detail-value pension-fond-block">
              <span class="detail-value">{{ company.socialInsuranceFond.name }}</span>
              <span class="authority-date">Дата постановки на учет: {{ company.socialInsuranceFondRegistration.issueDate }}</span>
            </div>
          </div>
          <br>
          <div class="manager-card" v-if="company.managers.length > 0" v-for="manager in company.managers" :key="manager.inn">
            <div class="manager-header">
              <span class="manager-post">{{ manager?.post?.toLowerCase() }} с {{ manager.startDate }}</span>
            </div>
            <a :href="getManagerRoute(manager)" class="link manager-link">
              {{ manager.name }}
            </a>
            <span class="manager-inn">ИНН: {{ manager.inn }}</span>
          </div>
          <br>
          <template v-if="company.historyStatus === 'processing'">
            <div class="detail-row info-message">
              <b class="detail-key">История компании:</b>
              <span class="detail-value detail-value--processing">Информация о правопредшественниках и правопреемниках будет доступна в течение пары минут...</span>
            </div>
          </template>
          <template v-else>
            <b v-if="company.predecessors.length > 0" class="detail-key">Правопредшественники:</b>
            <div v-if="company.predecessors.length > 0" class="detail-row predecessors-row">
              <a v-for="predecessor in company.predecessors" :href="`/company/${predecessor.slug}`" class="link predecessor-link" :key="predecessor.slug">
                <span class="detail-value">{{ predecessor.name }} </span>
              </a>
            </div>
            <b v-if="company.successors.length > 0" class="detail-key">Правопреемники:</b>
            <div v-if="company.successors.length > 0" class="detail-row successors-row clickable">
              <a v-for="successor in company.successors" :href="`/company/${successor.slug}`" class="link successor-link" :key="successor.slug">
                <span class="detail-value">{{ successor.name }}</span>
              </a>
            </div>
          </template>
          <contacts-block :company="company"></contacts-block>

          <!-- Блок с сообщениями об отсутствии данных -->
          <div class="missing-data" v-if="missingDataMessages.length">
            <p v-for="(msg, index) in missingDataMessages" :key="index">{{ msg }}</p>
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
import FoundersBlock from '../components/FoundersBlock.vue';
import { formatNumber } from '../app';

export default {
  name: 'CompanyDetails',
  components: { LicensesBlock, ContactsBlock },
  props: {
    company: {
      type: Object,
      required: true
    }
  },
  data() {
    return {
      showOkveds: false,
      showOpfTooltip: false,
    };
  },
  computed: {
    companyTitle() {
      return `${this.company.opf.abbreviation} "${this.company.name}"`;
    },
    statusText() {
      const statusMap = {
        LIQUIDATED: 'Ликвидировано',
        ACTIVE: 'Активно',
        SUSPENDED: 'Приостановлено',
        INACTIVE: 'Неактивно',
        REORGANIZING: 'Реорганизация',
        UNDEFINED: 'Неизвестно'
      };
      return statusMap[this.company?.status?.name] || this.company?.status?.name;
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
      return colorMap[this.company?.status?.name] || '';
    },
    // Вычисляем недостающие данные для соответствующих блоков:
    missingDataMessages() {
      const msgs = [];
      if (!this.company.finances) {
        msgs.push("Данные по финансам отсутствуют");
      }
      if (!this.company.filials || this.company.filials.length === 0) {
        msgs.push("Данные по филиалам отсутствуют");
      }
      if (!this.company.foundersProcessed || this.company.founders === null) {
        msgs.push("Данные по основателям отсутствуют");
      }
      if (!this.company.licenses || this.company.licenses.length === 0) {
        msgs.push("Данные по лицензиям отсутствуют");
      }
      // Если нужно, можно добавить и другие проверки (например, для контактов)
      return msgs;
    },
    // Формируем динамический массив блоков с учетом приоритета
    sortedBlocks() {
      const blocks = [];
      // Блок филиалов (приоритет 2)
      if (this.company.filials && this.company.filials.length > 0) {
        blocks.push({
          key: 'filials',
          component: FilialsBlock,
          priority: 2,
          props: { filials: this.company.filials }
        });
      }
      // Блок финансов (приоритет 3)
      if (this.company.finances) {
        blocks.push({
          key: 'finances',
          component: FinancesBlock,
          priority: 3,
          props: { finances: this.company.finances }
        });
      }
      // Блок основателей (приоритет 4)
      if (this.company.foundersStatus === 'processing') {
        blocks.push({
          key: 'founders',
          component: FoundersBlock,
          priority: 1,
          props: { loading: true } // или просто status: 'processing'
        });
      } else if (true === this.company.foundersProcessed && this.company.founders !== null) {
        blocks.push({
          key: 'founders',
          component: FoundersBlock,
          priority: 1,
          props: { founders: this.company.founders }
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
      return manager.type === 'company'
          ? `/company/${manager.slug}`
          : `/individual/${manager.slug}`;
    }
  }
};
</script>

<style scoped>
.company-page {
  display: flex;
  flex-direction: column;
  gap: 20px;
  max-width: 1200px;
  margin: 0 auto;
  padding: 20px;
}
.missing-data {
  font-style: italic;
  color: #888;
  font-size: 14px;
  margin-top: 20px;
  text-align: center;
}
.card--details {
  /* Дополнительные стили для карточки с деталями можно добавить здесь */
}

.company-details {
  display: flex;
  flex-direction: column;
  gap: 30px;
  margin: 10px;
}

.company-info {
  display: flex;
  flex-direction: column;
  gap: 5px;
}

.company-columns {
  display: flex;
  gap: 40px;
  align-items: flex-start;
  justify-content: center;
  margin: 0 50px;
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

.detail-value--status {
  gap: 10px;
  align-items: center;
}

.status-row {
  background-color: #f0f8ff;
  padding: 10px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  gap: 10px;
}

.detail-value--processing {
  font-style: italic;
  color: #888;
  font-size: 14px;
  margin-top: 20px;
  text-align: center;
}

.status-dates {
  font-size: 12px;
  color: #505050;
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

/* Универсальный стиль для ссылок */
.link {
  color: #007bff;
  text-decoration: none;
  cursor: pointer;
}

.link:hover {
  text-decoration: underline;
}

.predecessors-row {
  display: flex;
  flex-wrap: wrap;
  flex-direction: row;
  gap: 15px;
}

.detail-value--opf {
  align-items: center;
}

.successors-row {
  display: flex;
  flex-direction: row;
  gap: 15px;
}
</style>
