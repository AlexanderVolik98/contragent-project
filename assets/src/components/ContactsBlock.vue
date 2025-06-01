<template>
    <div class="contacts-grid">
      <div class="contact-card" v-if="company?.phones?.length">
        <img class="contact-icon" :src="require('../assets/svg/phone.svg')" alt="телефон">
        <div class="contact-info">
          <span class="contact-title">Телефоны Компании</span>
          <div class="contact-details">
            <a
                v-for="phone in company.phones"
                :key="phone.value"
                :href="`tel:${phone.value}`"
                class="contact-link"
            >
              {{ phone.value }}
            </a>
          </div>
        </div>
      </div>
      <div class="contact-card" v-else>
        <span class="no-data">Нет данных по телефонам</span>
      </div>

      <div class="contact-card" v-if="company?.emails?.length">
        <img class="contact-icon" :src="require('../assets/svg/email.svg')" alt="почта">
        <div class="contact-info">
          <span class="contact-title">Email Компании</span>
          <div class="contact-details">
            <a
                v-for="email in company.emails"
                :key="email.value"
                :href="`mailto:${email.value}`"
                class="contact-link"
            >
              {{ email.value.toLowerCase() }}
            </a>
          </div>
        </div>
      </div>
      <div class="contact-card" v-else>
        <span class="no-data">Нет данных по email</span>
      </div>
    </div>
</template>

<script>
export default {
  name: 'ContactsBlock',
  props: {
    company: {
      type: Object,
      required: true
    }
  }
};
</script>

<style scoped>
.contacts-block {
  max-width: 400px;
}

.contacts-grid {
  display: flex;
  flex-direction: row;
  gap: 10px;
}

.contact-card {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 10px;
  border: 1px solid #ddd;
  border-radius: 8px;
  background: #f9f9f9;
  transition: 0.3s ease;
}

.contact-card:hover {
  background: #f1f1f1;
}

.contact-icon {
  width: 24px;
  height: 24px;
}

.contact-info {
  display: flex;
  flex-direction: column;
}

.contact-title {
  font-weight: bold;
  color: #233461;
  margin-bottom: 3px;
}

.contact-details {
  font-size: 14px;
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.contact-link {
  color: #007bff;
  text-decoration: none;
}

.contact-link:hover {
  text-decoration: underline;
}

.no-data {
  color: #616161;
}
</style>
