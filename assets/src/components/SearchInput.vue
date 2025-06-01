<template>
  <div class="search-input-container">
    <div class="search-input-wrapper">
      <div class="search-main-column">
        <div class="input-with-button">
          <div class="input-field">
            <input
                type="text"
                v-model="searchTerm"
                @input="onInput"
                placeholder="Введите ОГРН, ИНН, название, ФИО"
                @focus="onFocus"
                @blur="onBlur"
                @keyup.enter="onEnter"
            :class="{'input-focused': isFocused}"
            />
            <div v-if="isLoading" class="loading-spinner">
              <img class="loading-spinner-gif" src="../assets/gif/rolling.gif" alt="Loading..." />
            </div>
          </div>

          <div class="search-button-wrapper">
            <button
                class="search-button"
                @click="onSearchClick"
                :disabled="isSearchPage ? !isValidSearch : false"
                :class="{ 'disabled-button': isSearchPage && !isValidSearch }"
            >
              {{ isSearchPage ? 'Поиск' : 'Расширенный поиск' }}
            </button>
          </div>
        </div>

        <div class="search-hint-wrapper">
          <transition name="fade">
            <div v-if="!isValidSearch && searchTerm" class="search-hint">
              Для поиска введите 3 и более символов
            </div>
          </transition>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';

const searchTerm = ref('');
const isFocused = ref(false);

const emit = defineEmits(['update:query', 'search-button-click', 'reset-filters', 'search-readiness-changed']);
const props = defineProps({
  isLoading: Boolean,
  isSearchPage: {
    type: Boolean,
    default: false
  },
  appliedFiltersCount: {
    type: Number,
    default: 0,
  }
});

const isValidSearch = computed(() => searchTerm.value.trim().length >= 3);

const onSearchClick = () => {
  emit('update:query', searchTerm.value);

  if (props.isSearchPage === true) {
    if (!isValidSearch.value) return;
    emit('search-button-click');
    emit('reset-filters');
  } else {
    window.location.href = "/search?ident=" + searchTerm.value;
  }
};

watch(searchTerm, (newVal) => {
  const trimmed = newVal.trim();
  if (trimmed.length > 2 || trimmed.length === 0) {
    emit('update:query', newVal);
  }
  emit('search-readiness-changed', trimmed.length >= 3);
});

const onInput = () => {};
const onFocus = () => { isFocused.value = true; };
const onBlur = () => { isFocused.value = false; };

// Новый метод, который будет вызван при нажатии Enter
const onEnter = () => {
  onSearchClick(); // Вызываем клик на кнопку поиска
};
</script>



<style scoped>
.search-input-container {
  align-self: center;
  min-height: 73px;
  display: inline-flex;
}

.search-input-wrapper {
  display: inline-flex;
  justify-content: center;
  align-items: center;
  gap: 15px;
}

.input-field {
  display: flex;
  justify-content: center;
  align-items: center;
  position: relative; /* Чтобы разместить гифку внутри */
}

.input-field input {
  position: relative;
  min-height: 50px;
  min-width: 600px;
  flex: 1 1 0;
  padding-left: 40px; /* Добавляем отступ слева для иконки */
  background: white url('../assets/svg/loupe.svg') no-repeat 10px center; /* Фоновое изображение */
  background-size: 20px 20px; /* Размер иконки */
  box-shadow: 1px 1px 1px 1px rgba(10, 9, 11, 0.12);
  border-radius: 13px;
  border: 1px solid rgba(10, 9, 11, 0.12);
  outline: none;
  font-size: 20px;
  font-family: Inter, sans-serif;
  font-weight: 400;
  color: #757575;
  word-wrap: break-word;
  transition: border 0.3s ease; /* Плавный переход для изменения границы */
}

.search-button:hover {
  background: #1a2e5a;
}

.tooltip {
  position: absolute;
  top: -30px;
  right: 0;
  background: #444;
  color: white;
  padding: 6px 10px;
  border-radius: 6px;
  font-size: 13px;
  white-space: nowrap;
  z-index: 10;
}

.extended-search-button {
  padding: 10px 15px;
  background: #233461;
  color: white;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-size: 16px;
  text-decoration: none; /* Убираем подчеркивание */
}

.search-main-column {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
}

.input-with-button {
  display: flex;
  gap: 15px;
  align-items: center;
}

.search-hint-wrapper {
  min-height: 20px; /* зарезервированное пространство */
}

.search-hint {
  font-size: 14px;
  color: #d9534f;
  margin-left: 5px;
}

/* Плавное появление */
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.2s ease;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}

.disabled-button {
  opacity: 0.6;
  cursor: not-allowed;
}

.extended-search-button:hover {
  background: #1a2e5a;
}

.search-button-wrapper {
  position: relative;
  display: flex;
  align-items: center;
}

.search-button {
  height: 50px;
  padding: 0 20px;
  background: #233461;
  color: white;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-size: 16px;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background 0.2s ease;
}

.search-button:hover:not(:disabled) {
  background: #1a2e5a;
}

.disabled-button {
  opacity: 0.6;
  cursor: not-allowed;
}

.loading-spinner {
  position: absolute;
  right: 12px; /* Располагаем гифку справа */
  display: flex;
  align-items: center;
}
.loading-spinner-gif {
  width: 30px;
  height: 30px
}
</style>
