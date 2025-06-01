<template>
  <footer class="footer">
    <div class="footer-content">
      <slot></slot> <!-- Место под разделы футера -->
    </div>
    <p class="copyright">&copy; 2025 Все права защищены</p>
  </footer>
</template>

<script setup>
</script>

<style scoped>
.footer {
  height: 100px;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  background-color: #233461; /* Оставляем темный фон */
  color: white;
  box-shadow: 0 -4px 15px rgba(0, 0, 0, 0.1); /* Легкая тень */
  padding: 20px;
  border-radius: 10px; /* Скругленные углы */
  transition: box-shadow 0.3s ease-in-out; /* Плавное изменение */
}

.footer:hover {
  box-shadow: 0 -4px 20px rgba(0, 153, 255, 0.5); /* Синие свечение при наведении */
}

.footer-content {
  display: flex;
  gap: 20px;
  justify-content: center;
}

.copyright {
  margin-top: 10px;
  font-size: 14px;
}
</style>