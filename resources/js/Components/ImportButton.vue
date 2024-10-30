<template>
  <div>
    <button @click="chooseFile" class="group margin-10 text-white font-bold rounded-xl flex items-center flex-col p-5 transition-colors duration-300 ease-in-out">
      <div class="w-96 border border-black transition hover:scale-105 ease-in-out duration-150 bg-white dark:bg-gray-800 overflow-hidden h-full p-11 sm:rounded-lg group-hover:bg-gray-900 group-hover:dark:bg-white">
        <div class="flex items-center justify-center">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-36 w-36 text-gray-800 dark:text-white group-hover:text-white group-hover:dark:text-gray-800">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5" />
          </svg>
        </div>
        <div class="text-4xl text-gray-800 dark:text-white group-hover:text-white group-hover:dark:text-gray-800">Choose File</div>
      </div>
    </button>
    
    <div class="w-full flex-col items-center justify-center mb-6">
      <h2 class="flex items-center justify-center text-lg">Upload SQL Backup File</h2>
      <div v-if="fileName" class="flex items-center justify-center text-lg text-gray-600">
        Selected file: {{ fileName }}
      </div>
      <div class="flex items-center justify-center">
        <button @click="uploadFile" class="text-lg bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
          Import Backup
        </button>
      </div>

    </div>
    <input type="file" ref="fileInput" @change="onFileChange" style="display: none;" />
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      sqlFile: null,
      fileName: ''
    };
  },
  methods: {
    chooseFile() {
      this.$refs.fileInput.click();
    },
    onFileChange(event) {
      this.sqlFile = event.target.files[0];
      this.fileName = this.sqlFile ? this.sqlFile.name : '';
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