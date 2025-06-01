<template>
  <button class="btn">
    <slot></slot> <!-- Позволяет передавать контент внутрь компонента -->
  </button>
</template>

<script>
export default {
  name: 'BaseButton',
};
</script>

<style>
.btn {
  padding: 10px;
  background-color: blue;
  color: white;
  border: none;
  border-radius: 5px;
}
</style>