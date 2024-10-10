<template>
  <div id="chatbot">
    <!-- The chat circle - only display when chatbotImageUrl is loaded and chat is not expanded -->
    <div 
      v-if="chatbotImageUrl && !chatExpanded" 
      @click="expandChat" 
      class="chat-circle"
    >
      <!-- Dynamically bind the image source -->
      <img 
        :src="chatbotImageUrl" 
        alt="Chat" 
        class="chat-icon"
      />  
    </div>

    <!-- Expanded chat window -->
    <div v-if="chatExpanded" class="chat-window">
      <div class="chat-header">
        <span>Chatbot</span>
        <button @click="collapseChat">X</button>
      </div>

      <div class="chat-body">
        <div ref="messages" class="messages">
          <transition-group name="message" tag="div">
            <div 
              v-for="message in messages" 
              :key="message.id" 
              :class="['message', message.sender]"
            >
              {{ message.text }}
              <div v-if="message.buttons && message.sender === 'bot'" class="chat-buttons">
                <button 
                  v-for="button in message.buttons" 
                  :key="button.id" 
                  @click="handleButtonClick(button)"
                >
                  {{ button.text }}
                </button>
              </div>
            </div>
          </transition-group>

          <div v-if="isTyping" class="message bot typing">
            <div class="dot"></div>
            <div class="dot"></div>
            <div class="dot"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      chatExpanded: false,
      messages: [],
      isTyping: false,
      businessName: '',
      chatInitialized: false,
      chatbotImageUrl: '', // Reactive URL for the chat image
      buttonIndex: 0, // Tracks the current set of buttons
      buttonLimit: 3, // Maximum number of buttons to display at a time
    };
  },
  mounted() {
    // Preload the business data and image when the component is mounted
    this.preloadBusinessData();
  },
    methods: {
    expandChat() {
      this.chatExpanded = true;
      if (!this.chatInitialized) {
        this.initializeChat();
        this.chatInitialized = true;
      }
    },
    collapseChat() {
      this.chatExpanded = false;
    },
    async preloadBusinessData() {
      try {
        const response = await axios.get('/api/Business');
        if (response.data && Array.isArray(response.data) && response.data.length > 0) {
          const businessData = response.data[0];
          const businessImage = businessData.business_image;
          this.chatbotImageUrl = `/storage/business_logos/${businessImage}`;
        } else {
          console.error("No business data found or invalid format.");
        }
      } catch (error) {
        console.error("Error fetching business data:", error);
      }
    },
    async initializeChat() {
      if (!this.chatbotImageUrl) {
        await this.preloadBusinessData();
      }
      this.addInitialChatMessage();
    },
    addInitialChatMessage() {
      this.messages.push({
        id: new Date().getTime(),
        text: `Hi! Welcome to ${this.businessName || 'our business'}. How can we help you today?`,
        sender: 'bot',
        buttons: [
          { id: 1, text: "Business Details" },
          { id: 2, text: "Regional Delivery Times" },
          { id: 3, text: "Chatbot use cases" }
        ]
      });
    },
    showEndMessage() {
      this.messages.push({
        id: new Date().getTime(),
        text: 'Is there anything else you would like to know?',
        sender: 'bot',
        buttons: [
          { id: 1, text: "Business Details" },
          { id: 2, text: "Regional Delivery Times" },
          { id: 3, text: "Chatbot use cases" }
        ]
      });
    },

    handleButtonClick(button) {
      this.messages.push({ id: new Date().getTime(), text: `You clicked ${button.text}`, sender: 'user' });
      this.scrollToBottom();
      this.isTyping = true;
      
      setTimeout(() => {
        this.isTyping = false;
        let botResponse = '';
        
        switch (button.id) {
          case 1:
            this.messages.push({
              id: new Date().getTime(),
              text: 'Please choose:',
              sender: 'bot',
              buttons: [
                { id: 4, text: "Schedule for Today" },
                { id: 5, text: "Schedule for Tomorrow" }
              ]
            });
            break;
            
          case 2:
          this.messages.push({
              id: new Date().getTime(),
              text: 'Please Select a Region:',
              sender: 'bot',
              buttons: [
                { id: 7, text: "Region 1" },
                { id: 8, text: "Region 2" },
                { id: 9, text: "Region 3 " },
                { id: 10, text: "Region 4" },
                { id: 11, text: "Region 5" }
              ]
            });
            break;
            
          case 3:
            botResponse = 'You picked "Chatbot use cases"! Let me show you how chatbots can help.';
            break;
            
          case 4:
            // Send the response for id: 4
            this.messages.push({
              id: new Date().getTime(),
              text: 'You chose to "Schedule for Today". We will get back to you shortly.',
              sender: 'bot'
            });
            
           
            setTimeout(() => {
              this.showEndMessage(); 
              this.scrollToBottom();
            }, 1000); // Small delay for a better user experience
            
            break;
            
          case 5:
            botResponse = 'You chose to "Schedule for Tomorrow". We will confirm the availability soon.';
            break;

          case 6:
            this.messages.push({
              id: new Date().getTime(),
              text: 'Is there anything else you would like to know?:',
              sender: 'bot',
              buttons: [
                { id: 1, text: "Business Details" },
                { id: 2, text: "Regional Delivery Times" },
                { id: 3, text: "Chatbot use cases" }
              ]
            });
            break;
            
          default:
            botResponse = 'I\'m not sure what you selected!';
        }

        // If there's a response, push it to the chat
        if (botResponse.trim()) {
          this.messages.push({ id: new Date().getTime(), text: botResponse, sender: 'bot' });
        }

        this.scrollToBottom();
      }, 1000);
    },
    scrollToBottom() {
      this.$nextTick(() => {
        const messagesContainer = this.$refs.messages;
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
      });
    }
  } 
};
</script>

<style scoped>
/* Same styles as before */
#chatbot {
  position: fixed;
  bottom: 20px;
  right: 20px;
}

.chat-circle {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  overflow: hidden; /* Important to make sure the image fits within the circle */
}

.chat-icon {
  width: 100%; /* Ensure the image takes up the full width of the container */
  height: 100%; /* Ensure the image takes up the full height */
  object-fit: cover; /* Ensure the image is not distorted */
  border-radius: 50%; /* Keep the image in a circular shape */
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
  overflow: hidden;
}

.messages {
  flex: 1;
  overflow-y: auto;
  max-height: 300px;
}

.message {
  padding: 5px 10px;
  margin-bottom: 10px;
  border-radius: 5px;
}

.message.bot {
  background-color: #f1f1f1;
  text-align: left;
}

.message.user {
  background-color: #007bff;
  color: white;
  text-align: right;
}

.chat-buttons {
  display: flex;
  gap: 10px;
  margin-top: 10px;
}

.chat-buttons button {
  padding: 8px;
  background-color: transparent;
  color: #007bff;
  border: 1px solid #007bff;
  border-radius: 20px;
  cursor: pointer;
  font-size: 12px;
}

.chat-buttons button:hover {
  background-color: #007bff;
  color: white;
}

.typing {
  display: flex;
  gap: 5px;
}

.dot {
  width: 6px;
  height: 6px;
  background-color: #007bff;
  border-radius: 50%;
  animation: blink 1s infinite alternate;
}

.dot:nth-child(2) {
  animation-delay: 0.2s;
}

.dot:nth-child(3) {
  animation-delay: 0.4s;
}

@keyframes blink {
  from {
    opacity: 0.3;
  }
  to {
    opacity: 1;
  }
}
</style>
