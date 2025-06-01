<template>
    <!-- Заголовок-спойлер -->
    <div class="detail-row clickable" @click="toggleLicenses">
      <b class="detail-key link">Лицензии:</b>
      <span class="toggle-icon">{{ showLicenses ? '▼' : '▶' }}</span>
    </div>
    <!-- Содержимое лицензий, показываемое при раскрытии -->
    <div v-if="showLicenses" class="licenses-info">
      <div
          v-for="(license, index) in licenses"
          :key="index"
          class="license-card"
          :class="{ 'single-license': licenses.length === 1 }"
      >
        <div class="license-header">
          <span class="license-number">{{ license.number }}</span>
          <span v-if="license.series" class="license-series"> ({{ license.series }})</span>
        </div>
        <div class="license-details">
          <div class="license-row">
            <span class="license-label">Дата выдачи:</span>
            <span class="license-value">{{ license.issueDate }}</span>
          </div>
          <div class="license-row">
            <span class="license-label">Выдана:</span>
            <span class="license-value">{{ license.issueAuthority }}</span>
          </div>
          <div class="license-row" v-if="license.validFrom">
            <span class="license-label">Действует с:</span>
            <span class="license-value">{{ license.validFrom }}</span>
          </div>
          <div class="license-row" v-if="license.validTo">
            <span class="license-label">Действует до:</span>
            <span class="license-value">{{ license.validTo }}</span>
          </div>
          <div class="license-row" v-else>
            <span class="license-label">Действует до:</span>
            <span class="license-value">Бессрочно</span>
          </div>
          <div class="license-row" v-if="license.suspendDate">
            <span class="license-label">Приостановлена:</span>
            <span class="license-value">{{ license.suspendDate }}</span>
          </div>
          <div class="license-row" v-if="license.suspendAuthority">
            <span class="license-label">Приостановлена (орган):</span>
            <span class="license-value">{{ license.suspendAuthority }}</span>
          </div>
          <div class="license-row" v-if="license.activities && license.activities.length">
            <span class="license-label">Деятельность:</span>
            <span class="license-value">
              <template v-for="(activity, idx) in license.activities" :key="idx">
                <span v-if="activity.split(' ').length <= 20">
                  {{ activity }}
                </span>
                <details v-else>
                  <summary class="link">Подробнее</summary>
                  <span>{{ activity }}</span>
                </details>
                <span v-if="idx < license.activities.length - 1">, </span>
              </template>
            </span>
          </div>
          <div class="license-row" v-if="license.addresses">
            <span class="license-label">Адреса:</span>
            <span class="license-value">{{ license.addresses }}</span>
          </div>
        </div>
      </div>
    </div>
</template>

<script>
export default {
  name: "LicensesBlock",
  props: {
    licenses: {
      type: Array,
      required: true,
    },
  },
  data() {
    return {
      showLicenses: false,
    };
  },
  methods: {
    toggleLicenses() {
      this.showLicenses = !this.showLicenses;
    },
  },
};
</script>

<style scoped>

</style>
