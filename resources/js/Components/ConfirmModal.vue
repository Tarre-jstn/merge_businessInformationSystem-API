<template>
    <div v-if="isOpen" class="fixed inset-0 flex items-center justify-center z-50" style="background-color: rgba(0, 0, 0, 0.5);">
      <div class="text-black bg-white p-5 rounded-lg shadow-lg max-w-sm w-full">
        <h2 class="text-xl font-bold mb-4">Confirm Deletion</h2>
        <p class="mb-4">Are you sure you want to delete this invoice?</p>
        <div class="flex justify-center space-x-2">
          <button @click="$emit('cancel')" class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">
            No
          </button>
          <button @click="$emit('confirm')" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">
            Yes
          </button>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  defineProps({
    isOpen: Boolean
  });
  
  defineEmits(['confirm', 'cancel']);
  </script>