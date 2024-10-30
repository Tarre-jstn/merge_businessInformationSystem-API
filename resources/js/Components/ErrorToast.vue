<template>
  <div v-if="visible" class="toast-container error">
    {{ message }}
    <button class="close-btn" @click="$emit('close')">×</button>
  </div>
</template>

<script setup>
import { defineProps, defineEmits } from 'vue';

const props = defineProps({
  visible: Boolean,
  message: String,
});
const emit = defineEmits(['close']);
</script>

<style scoped>
.toast-container {
  position: fixed;
  top: 10%;
  left: 50%;
  transform: translate(-50%, -50%); /* Centers horizontally and vertically */
  max-width: 500px;
  width: fit-content;
  color: white;
  padding: 10px 20px;
  border-radius: 5px;
  font-size: 16px;
  box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
  z-index: 1000;
  opacity: 0;
  animation: fadeInOut 3s ease forwards;
}

.toast-container.error {
  background-color: red;
}

.close-btn {
  background: none;
  border: none;
  color: white;
  font-size: 16px;
  cursor: pointer;
}

@keyframes fadeInOut {
  0% {
    opacity: 0;
    transform: translate(-50%, -30px); /* Adjust for initial entrance */
  }
  10%, 90% {
    opacity: 1;
    transform: translate(-50%, 0);
  }
  100% {
    opacity: 0;
    transform: translate(-50%, -20px); /* Adjust for exit */
  }
}

</style>
