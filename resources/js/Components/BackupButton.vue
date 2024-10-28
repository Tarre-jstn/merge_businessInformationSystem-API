<template>
    <div>
        <br>
      <button @click="downloadBackup" class="margin-10 text-white font-bold rounded-xl flex items-center flex-col p-5" style="margin-top: 10px;">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class=" h-36 w-36">
  <path stroke-linecap="round" stroke-linejoin="round" d="M9 8.25H7.5a2.25 2.25 0 0 0-2.25 2.25v9a2.25 2.25 0 0 0 2.25 2.25h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25H15M9 12l3 3m0 0 3-3m-3 3V2.25" /></svg>
        <div class=" text-4xl">Download Backup</div></button>
        <br>
    </div>
  </template>
  
  <script>
  import axios from 'axios';
  
  export default {
    methods: {
      downloadBackup() {
        axios.get('/api/backup', { responseType: 'blob' })
          .then(response => {
            const url = window.URL.createObjectURL(new Blob([response.data]));
            const link = document.createElement('a');
            link.href = url;
            link.setAttribute('download', 'backup.sql');
            document.body.appendChild(link);
            link.click();
          })
          .catch(error => {
            console.log('Error downloading the backup:', error);
          });
      }
    }
  }
  </script>
  