<template>
  <div class="subject-details" v-if="subject">
    <h1>{{ subject.name }}</h1>
    <div><strong>ИНН:</strong> {{ subject.inn }}</div>
    <div><strong>Город:</strong> {{ subject.address }}</div>
    <div><strong>Статус:</strong> {{ subject.status }}</div>
    <div><strong>Оквэд:</strong> {{ subject.okved.name }}</div>
    <div v-if="subject.type === 'company'">
      <h2>Компания</h2>
      <div>Тип компании: {{ subject.companyType }}</div>
    </div>
    <div v-if="subject.type === 'individual'">
      <h2>Индивидуальный предприниматель</h2>
      <div>Дата регистрации: {{ subject.registrationDate }}</div>
    </div>
    <!-- Можно добавить другие данные о субъекте -->
  </div>
</template>

<script>
export default {
  name: 'Subject',
  data() {
    return {
      subject: null
    };
  },
  created() {
    this.fetchSubjectDetails();
  },
  methods: {
    // Метод для загрузки данных о субъекте
    async fetchSubjectDetails() {
      const id = this.$route.params.id; // Получаем ID из параметра URL
      const response = await fetch(`/api/subject/${id}`); // Запрос к API
      this.subject = await response.json();
    }
  }
}
</script>

<style scoped>
.subject-details {
  padding: 20px;
}
h1 {
  font-size: 2em;
  margin-bottom: 10px;
}
</style>
