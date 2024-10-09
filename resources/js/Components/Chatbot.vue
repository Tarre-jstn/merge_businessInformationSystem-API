  <template>
    <div id="chatbot">
      <!-- The chat circle -->
      <div 
        v-if="!chatExpanded" 
        @click="expandChat" 
        class="chat-circle"
      >
        <img src="chat-icon.png" alt="Chat" />
      </div>

      <!-- The expanded chat window -->
      <div v-if="chatExpanded" class="chat-window">
        <!-- Chatbot header with close option -->
        <div class="chat-header">
          <span>Chatbot</span>
          <button @click="collapseChat">X</button>
        </div>

        <!-- Chatbot body for messages and buttons -->
        <div class="chat-body">
          <div ref="messages" class="messages">
            <!-- Animate each message -->
            <transition-group name="message" tag="div">
              <div 
                v-for="message in messages" 
                :key="message.id" 
                :class="['message', message.sender]"
              >
                {{ message.text }}
                <!-- Check if the message is from the bot and if buttons should appear -->
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

            <!-- Typing indicator when bot is thinking -->
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
      isTyping: false, // For typing animation
      businessName: '', // To store the business name
      chatInitialized: false, // Flag to check if chat has been initialized
    };
  },
  methods: {
    expandChat() {
      this.chatExpanded = true;
      
      // Only initialize chat if it's the first time
      if (!this.chatInitialized) {
        this.initializeChat();
        this.chatInitialized = true; // Set the flag to true after initialization
      }
    },
    collapseChat() {
      this.chatExpanded = false;
    },
    async initializeChat() {
      try {
        const response = await axios.get('/api/Business');
        if (response.data && Array.isArray(response.data) && response.data.length > 0) {
          const businessData = response.data[0];  
          const businessName = businessData.business_Name;
          const welcomeMessage = `Hi! Welcome to ${businessName}. How can we help you today?`;
          
          this.messages.push({
            id: 1,
            text: welcomeMessage,
            sender: 'bot',
            buttons: [
              { id: 1, text: "Book a Demo" },
              { id: 2, text: "Know about me" },
              { id: 3, text: "Chatbot use cases" }
            ]
          });
        } else {
          console.error("No business data found or invalid format.");
          this.messages.push({
            id: 1,
            text: "Hi! Welcome to our business. How can I help you today?",
            sender: 'bot',
            buttons: [
              { id: 1, text: "Book a Demo" },
              { id: 2, text: "Know about me" },
              { id: 3, text: "Chatbot use cases" }
            ]
          });
        }
      } catch (error) {
        console.error("Error fetching business data:", error);
        this.messages.push({
          id: 1,
          text: "Hi! Welcome to our business. How can I help you today?",
          sender: 'bot',
          buttons: [
            { id: 1, text: "Book a Demo" },
            { id: 2, text: "Know about me" },
            { id: 3, text: "Chatbot use cases" }
          ]
        });
      }
    },
    handleButtonClick(button) {
      // Add user message
      this.messages.push({ id: new Date().getTime(), text: `You clicked ${button.text}`, sender: 'user' });
      this.scrollToBottom();

      // Simulate typing animation
      this.isTyping = true;

      // Simulate bot response after a delay based on the button clicked
      setTimeout(() => {
        this.isTyping = false; // Stop typing animation

        let botResponse = '';
        switch (button.id) {
          case 1:
            botResponse = 'You chose "Book a Demo"! This option helps you with scheduling a demo.';
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
            botResponse = 'You selected "Know about me"! Here’s some information about BotPenguin.';
            break;
          case 3:
            botResponse = 'You picked "Chatbot use cases"! Let me show you how chatbots can help.';
            break;
          case 4:
            botResponse = 'You chose to "Schedule for Today". We will get back to you shortly.';
            break;
          case 5:
            botResponse = 'You chose to "Schedule for Tomorrow". We will confirm the availability soon.';
            break;
          default:
            botResponse = 'I\'m not sure what you selected!';
        }

        this.messages.push({ id: new Date().getTime(), text: botResponse, sender: 'bot' });
        this.scrollToBottom();
      }, 1000); // 1 second delay for bot response
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
    overflow: hidden; /* Prevent messages from overflowing */
  }

  .messages {
    flex: 1;
    overflow-y: auto; /* Allows scrolling */
    max-height: 300px; /* Ensures messages container stays within limits */
  }

  .message {
    padding: 5px 10px;
    margin-bottom: 10px;
    border-radius: 5px;
    transition: transform 0.3s, opacity 0.3s; /* Smooth transition */
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

  .message-enter-active,
  .message-leave-active {
    transition: all 0.3s ease;
  }

  .message-enter {
    opacity: 0;
    transform: translateY(20px);
  }

  .message-leave-to {
    opacity: 0;
    transform: translateY(-20px);
  }

  .chat-buttons {
    display: flex;
    justify-content: flex-start;
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
