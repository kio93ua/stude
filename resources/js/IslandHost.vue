<!-- resources/js/IslandHost.vue -->
<template>
  <!-- Рендеримо реальний компонент як дитину, без scoped-стилів -->
  <component :is="is" v-bind="props" />
</template>

<script setup>
defineProps({
  is: { type: [Object, Function], required: true },
  props: { type: Object, default: () => ({}) },
})
</script>
<!-- ЖОДНИХ <style> тут не потрібно -->
