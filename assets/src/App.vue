<template>
  <main>
    <div class="page-container">
      <div class="content" v-if="pageName === 'CompanyDetailsView'">
        <component :is="currentPage" :company="company"/>
      </div>
      <div class="content" v-else-if="pageName === 'IndividualDetailsView'">
        <component :is="currentPage" :individual="individual"/>
      </div>
      <div class="content" v-else-if="pageName === 'SearchView'">
        <component :is="SearchView" :regions="regions" :identFromHomePage="identFromHomePage"/>
      </div>
      <div v-else class="content">
        <component :is="currentPage"/>
      </div>
    </div>
  </main>
</template>

<script>
import { defineAsyncComponent } from 'vue';
import Header from './components/Header.vue';
import Footer from './components/Footer.vue';
import SearchView from "./views/SearchView.vue";

export default {
  computed: {
    SearchView() {
      return SearchView
    }
  },
  components: {
    Header,
    Footer,
  },
  data() {
    return {
      currentPage: null,
      pageName: null,
      company: null,
      regions: null,
      individual: null,
      identFromHomePage: null,
    };
  },
  mounted() {
    // Читаем значение из атрибута data-page
    const pageName = document.getElementById('app').getAttribute('data-page') || 'HomeView';

    this.pageName = pageName

    if (pageName === 'CompanyDetailsView') {
      const companyData = document.getElementById('app').getAttribute('data-company');
      this.loadCompany(companyData);
    }

    if (pageName === 'SearchView') {
      const regionsData = document.getElementById('app').getAttribute('data-regions');
      const identFromHomePage = document.getElementById('app').getAttribute('data-ident-home-page');
      this.loadRegions(regionsData)
      this.loadIdentHomePage(identFromHomePage)
    }

    if (pageName === 'IndividualDetailsView') {
      const individualData = document.getElementById('app').getAttribute('data-individual');
      this.loadIndividual(individualData);
    }

    this.loadPageComponent(pageName);
  },
  methods: {
    loadPageComponent(pageName) {
      // Динамическая загрузка компонента, основываясь на значении data-page
      this.currentPage = defineAsyncComponent(() => import(`./views/${pageName}.vue`));
    },
    loadCompany(companyData) {
      try {
        this.company = JSON.parse(companyData);
      } catch (error) {
        console.error('Ошибка разбора JSON:', error);
        this.company = null;
      }
    },
    loadRegions(regionsData) {
      this.regions = JSON.parse(regionsData);
    },
    loadIdentHomePage(ident) {
      this.identFromHomePage = ident;
    },
    loadIndividual(individualData) {
      try {
        this.individual = JSON.parse(individualData);
      } catch (error) {
        console.error('Ошибка разбора JSON:', error);
        this.individual = null;
      }
    }
  }
};
</script>

<style>

.page-container {
  display: flex;
  flex-direction: column;
}

.content {
  display: flex;
  flex-direction: column;
  flex-grow: 1;
  margin: 20px 0 80px 0;
}

main {
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
}
</style>
