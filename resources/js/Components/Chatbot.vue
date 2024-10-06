<template>
    <div id="chatbot">
      <!-- The chat circle -->
      <div 
        v-if="!chatExpanded" 
        @click="expandChat" 
        class="chat-circle"
      >
        <!-- Icon or image for the chatbot -->
        <img src="chat-icon.png" alt="Chat" />
      </div>
  
      <!-- The expanded chat window -->
      <div 
        v-if="chatExpanded" 
        class="chat-window"
      >
        <!-- Chatbot header with close option -->
        <div class="chat-header">
          <span>Chatbot</span>
          <button @click="collapseChat">X</button>
        </div>
  
        <!-- Chatbot body for messages and buttons -->
        <div class="chat-body">
          <div class="messages">
            <div 
              v-for="message in messages" 
              :key="message.id" 
              class="message"
            >
              {{ message.text }}
            </div>
          </div>
  
          <!-- Buttons for interaction -->
          <div class="chat-buttons">
            <button 
              v-for="button in buttons" 
              :key="button.id" 
              @click="handleButtonClick(button)"
            >
              {{ button.text }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    data() {
      return {
        chatExpanded: false, // State to toggle between circle and window
        messages: [], // Chatbot messages
        buttons: [] // Buttons to interact with the chatbot
      };
    },
    methods: {
      expandChat() {
        this.chatExpanded = true;
        this.initializeChat(); // Initialize chatbot logic
      },
      collapseChat() {
        this.chatExpanded = false;
      },
      initializeChat() {
        // Fetch or set up the initial buttons and message
        this.messages.push({ id: 1, text: "Hello! How can I assist you?" });
        this.buttons = [
          { id: 1, text: "Option 1" },
          { id: 2, text: "Option 2" },
          { id: 3, text: "Option 3" }
        ];
      },
      handleButtonClick(button) {
        // Handle button click and fetch Botman responses
        this.messages.push({ id: new Date().getTime(), text: `You clicked ${button.text}` });
        // Send message to Botman to get response
        this.sendMessageToBotman(button.text);
      },
      sendMessageToBotman(text) {
        axios.post('/botman', { message: text })
          .then(response => {
            const botResponse = response.data;
            this.messages.push({ id: new Date().getTime(), text: botResponse });
          })
          .catch(error => {
            console.error("Error communicating with Botman:", error);
          });
      }
    }
  };
  </script>
  
  <style scoped>
  /* Basic styles for the chat circle and window */
  #chatbot {
    position: fixed;
    bottom: 20px;
    right: 20px;
  }
  
  .chat-circle {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background-color: #007bff;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
  }
  
  .chat-window {
    width: 300px;
    height: 400px;
    background-color: white;
    border: 1px solid #ccc;
    border-radius: 10px;
    box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
    display: flex;
    flex-direction: column;
  }
  
  .chat-header {
    background-color: #007bff;
    color: white;
    padding: 10px;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  
  .chat-body {
    flex: 1;
    padding: 10px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
  }
  
  .messages {
    flex: 1;
    overflow-y: auto;
  }
  
  .message {
    background-color: #f1f1f1;
    padding: 5px 10px;
    margin-bottom: 10px;
    border-radius: 5px;
  }
  
  .chat-buttons {
    display: flex;
    justify-content: space-around;
    margin-top: 10px;
  }
  
  button {
    padding: 10px;
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
  }
  
  button:hover {
    background-color: #0056b3;
  }
  </style>
  