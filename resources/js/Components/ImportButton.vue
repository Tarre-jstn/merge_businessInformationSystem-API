<template>
  <div>
    <button @click="uploadFile" class="group margin-10 text-white font-bold rounded-xl flex items-center flex-col p-5 transition-colors duration-300 ease-in-out">
      <div class="w-96 border border-black transition hover:scale-105 ease-in-out duration-150 bg-white dark:bg-gray-800 overflow-hidden h-full p-11 sm:rounded-lg group-hover:bg-gray-900 group-hover:dark:bg-white">
        <div class="flex items-center justify-center">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-36 w-36 text-gray-800 dark:text-white group-hover:text-white group-hover:dark:text-gray-800">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5" />
          </svg>

        </div>

        <div class="text-4xl text-gray-800 dark:text-white group-hover:text-white group-hover:dark:text-gray-800">Import Backup</div>
      </div>
    </button>
    
    <div class="ml-5 mb-6 mt-2">
        <h2 class="text-lg">Upload SQL Backup File</h2>
        <input type="file" @change="onFileChange" class="text-lg"/>
      </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      sqlFile: null
    };
  },
  methods: {
    onFileChange(event) {
      this.sqlFile = event.target.files[0];
    },
    uploadFile() {
      if (!this.sqlFile) {
        alert("Please select a file first!");
        return;
      }

      const formData = new FormData();
      formData.append('sql_file', this.sqlFile);

      axios.post('/api/import', formData, {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      })
      .then(response => {
        alert('File imported successfully!');
      })
      .catch(error => {
        console.error('Error importing file:', error);
      });
    }
  }
}
</script>