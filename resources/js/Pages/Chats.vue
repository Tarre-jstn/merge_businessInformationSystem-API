<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import BackupButton from '@/Components/BackupButton.vue';
import ImportButton from '@/Components/ImportButton.vue';
export default {
  name: 'App',
  components: {AuthenticatedLayout, BackupButton, ImportButton}
}
</script>

<template>
  <Head title="Home" />
  <AuthenticatedLayout>
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900 dark:text-gray-100">Welcome to the Backup Page!</div>
        </div>
      </div>
    </div>
    <div class="flex flex-col items-center">
      <div class="flex flex-row">
        <div class=" m-8">
          <BackupButton />
        </div>
        <svg width="100" height="600" xmlns="http://www.w3.org/2000/svg"> 
            <line x1="50" y1="20" x2="50" y2="380" stroke="black" stroke-width="2" /> 
        </svg>
        <div class=" m-8">
          <ImportButton />
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
