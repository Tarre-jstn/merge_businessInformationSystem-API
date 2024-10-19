<template>
          <div class=" bg-business-website-header flex items-center p-5">
            <div class="ml-[50px] w-[50px] h-[50px]">
                <img :src='businessImage' class="w-full h-full object-cover rounded-full"/>
            </div>
                <div class="ml-auto flex items-center space-x-[40px] mr-[40px]">
                    <a class="text-white text-[18px] cursor-pointer" >Home</a>
                    <a class="text-black text-[18px] cursor-pointer" :href="route('chat_with_us')">Chat with Us</a>
                    <a class="text-white text-[18px] cursor-pointer" :href="route('products_page')">Products & Services</a>
                    <a class="text-white text-[18px] cursor-pointer" :href="route('aboutUs_page')">About Us</a>
                    <p>|</p>
                    <div class="flex flex-col">
                        <a @click="logout('logout')" class=" cursor-pointer text-white text-[14px] underline">Log Out</a>
                        <a @click="account" class=" cursor-pointer text-white text-[14px] underline">Account</a>
                    </div> 
                    <div class="w-[50px] h-[50px]">
                        <img v-if="isLoading" src='/storage/user_profile/default-profile.png'/>
                        <img v-else-if="profilePicture" :src='profilePicture' alt="Logo" class="h-full object-cover rounded-full" />
                        <img v-else src='/storage/user_profile/default-profile.png'/>
                    </div>
                    
                </div>
        </div>
      <!-- Expanded chat window -->
      <div class="bg-website-main flex min-h-screen">

      <div class="chat-window w-2/3 h-full mx-auto my-8">
        <div class="chat-header">
          <span>Chatbot</span>
          <!-- <button @click="collapseChat">X</button> -->
        </div>
  
        <div class="chat-body">
          <div ref="messages" class="messages">
            <transition-group name="message" tag="div">
              <div 
                v-for="message in messages" 
                :key="message.id" 
                :class="['message', message.sender]"
              >
                <!-- Use v-html here to render HTML in the message -->
                <div v-html="message.text"></div>
  
                <div v-if="message.buttons && message.sender === 'bot'" class="chat-buttons flex justify-center">
                  <button 
                    v-for="button in message.buttons" 
                    :key="button.id" 
                    @click="handleButtonClick(button)"
                    :disabled="buttonsDisabled"
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
      <section>
        <div class="bg-website-main flex flex-col min-h-screen" style="min-height: calc(70vh);">

            <!-- Footnote -->
            <div class="w-full">
                <hr class="border-white mx-auto w-11/12">
            </div>
        <div class="flex flex-row justify-between ml-8 mr-8 w-full">
        <!-- FootNote -->
        <div class="mr-auto mt-[100px] ml-8 flex flex-col h-1/2 w-1/2 max-w-md">
            <div class="max-w-[50px]">
                <img :src='businessImage' class="w-full h-full object-cover rounded-full"/>
            </div>

            <div class="mt-5">
                <p class=" text-xl text-white">{{website_footNote }}</p>
                <div class="mt-[20px]">
                <a :href="formatUrl(businessFacebook)" class="w-[30px] h-[40px] bg-white rounded-xl mr-[5px] p-2 cursor-pointer"><i class="fa-brands fa-facebook-f"></i></a>
                <a :href="formatUrl(businessX)" class="w-[30px] h-[40px] bg-white rounded-xl mr-[5px] p-2 cursor-pointer"><i class="fa fa-twitter"></i></a>
                <a :href="formatUrl(businessInstagram)" class="w-[30px] h-[40px] bg-white rounded-xl mr-[5px] p-2 cursor-pointer"><i class="fa-brands fa-instagram"></i></a>
                <a :href="formatUrl(businessTiktok)" class="w-[30px] h-[40px] bg-white rounded-xl mr-[5px] p-2 cursor-pointer"><i class="fa-brands fa-tiktok"></i></a>
                </div>
            </div>

        </div>

        <!-- Contact Us -->
        <div class="mt-[50px] ml-auto flex flex-grow-0 w-1/2 max-w-md w-1/2 max-w-md">
            <div class="mt-2 flex flex-col ">
                <p class="text-white font-bold text-[50px]">Contact Us</p>
                <p class="text-white mt-[10px]">Email: {{ businessEmail }} </p>
                <p class="text-white">Contact No.: {{ businessPhoneNumber }} </p>
                <p class="text-white">Address: {{ businessAddress }} </p>
                <p class="text-white">{{ businessProvince }}, 
                    {{ businessCity }}, {{ businessBarangay }}  </p>
            </div>
        </div>
        </div>

        <div class="mt-[60px] w-full">
            <hr class="border-white mx-auto w-11/12">
        </div>

        <div class="ml-[60px] mr-auto w-full">
            <p class="text-[17px] text-white mt-2"><i class="fa fa-copyright"></i> {{ businessName }} All rights reserved</p>
        </div>
        </div>

</section>
  </template>
  
  <script>
  import axios from 'axios';
  import {Inertia} from '@inertiajs/inertia';
  
  export default {
    data() {
      return {
        messages: [],
        isTyping: false,
        businessName: '',
        chatInitialized: false,
        chatbotImageUrl: '',
        buttonsDisabled: false, // New property to disable buttons
        isLoading: true,
        website_footNote: ''
        };
    },
    mounted() {
      // Preload the business data and image when the component is mounted
      this.preloadBusinessData();
      this.initializeChat();
      this.getWebsiteInfo();
    },
      methods: {
        logout(button){
            Inertia.post(route('logout'), {button});
         },
         account(){
        Inertia.visit(route('account_settings'));
        },
        formatUrl (url) {
            // Check if the URL starts with http:// or https://
            if (!/^https?:\/\//i.test(url)) {
                // Prepend https:// if it doesn't
                return `https://${url}`;
            }
            return url;
        },
        async  getWebsiteInfo(){
        try{
            const response = await axios.get('/showUser');
            if (response.data) {
                this.profilePicture = response.data.profile_img 
            ? `/storage/user_profile/${response.data.profile_img}` 
            : '/storage/user_profile/default-profile.png';
                this.isLoading=false;
            }
            this.profilePicture = response.data.profile_img 
            ? `/storage/user_profile/${response.data.profile_img}` 
            : '/storage/user_profile/default-profile.png';

            const getBusinessInfo = await axios.get('/api/business_info', {
                params: {user_id: 1}
            });
            
            const getWebsiteInfo1 = await axios.get('/api/website', {
                params: {business_id: 1}
            });
            console.log('Business data: ',getBusinessInfo.data);
            
            console.log('Website data: ',getWebsiteInfo1.data);
            
            this.website_footNote = getWebsiteInfo1.data.website_footNote;

                
        
        }catch(error){
            console.error('There was an error fetching the data:', error);
        }
},
      async preloadBusinessData() {
        try {
          const response = await axios.get('/api/Business');
          
          // Check if the response contains any data
          if (response.data) {
            // Safely check for the business data structure
            const businessData = response.data[0] || {}; // Fallback to an empty object if no data
            const businessImage = businessData.business_image || ''; // Check if image exists
  
            // Set business details with default values if fields are missing
            this.businessImage = `/storage/business_logos/${businessData.business_image}`;
            this.businessName = businessData.business_Name || 'Unknown Business';
            this.businessEmail = businessData.business_Email || 'Unknown Email';
            this.businessProvince = businessData.business_Province || 'Unknown Province';
            this.businessCity = businessData.business_City || 'Unknown City';
            this.businessBarangay = businessData.business_Barangay || 'Unknown Barangay';
            this.businessAddress = businessData.business_Address || 'Unknown Address';
            this.businessPhoneNumber = businessData.business_Contact_Number || 'Unknown Phone Number';
            this.businessTelephoneNumber = businessData.business_Telephone_Number || 'Unknown Phone Number';
            this.businessFacebook = businessData.business_Facebook || 'Unknown Facebook';
            this.businessX = businessData.business_X || 'Unknown X';
            this.businessInstagram = businessData.business_Instagram || 'Unknown Instagram ';
            this.businessTiktok = businessData.business_Tiktok || 'Unknown Tiktok  ';
  
  
            // Set the chatbot image URL, or use a default image
            this.chatbotImageUrl = businessImage 
              ? `/storage/business_logos/${businessImage}` 
              : '/default_image_path.png';
          } else {
            console.error("No business data found.");
          }
  
          // Fetch chatbot response data
          const chatbotResponse = await axios.get('/api/chatbot-response'); // Example endpoint
          if (chatbotResponse.data && chatbotResponse.data.length > 0) {
            const firstResponse = chatbotResponse.data[0]; // Get the first chatbot response
  
            this.workingHours = firstResponse?.chabot_BWHours || 'Unavailable';
            this.productDescription = firstResponse?.chabot_BPDescription || 'description available';
            this.lazada = firstResponse?.chabot_Lazada || 'Lazada Link available';
            this.shopee = firstResponse?.chabot_Shopee || 'Shopee Link available';
            this.region1 = firstResponse.chabot_Region1 || 'Delivery time for Region 1 unavailable';
            this.region2 = firstResponse.chabot_Region2 || 'Delivery time for Region 2 unavailable';
            this.region3 = firstResponse.chabot_Region3 || 'Delivery time for Region 3 unavailable';
            this.region4a = firstResponse.chabot_Region4A || 'Delivery time for Region 4A unavailable';
            this.region4b = firstResponse.chabot_Region4B || 'Delivery time for Region 4B unavailable';
            this.region5 = firstResponse.chabot_Region5 || 'Delivery time for Region 5 unavailable';
            this.regionNCR = firstResponse.chabot_NCR || 'Delivery time for NCR unavailable';
            this.regionCAR = firstResponse.chabot_CAR || 'Delivery time for CAR unavailable';
  
  
            
          } else {
            console.error("No chatbot response data found.");
          }
  
        } catch (error) {
          console.error("Error fetching business or chatbot response data:", error);
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
          text: `Hi! Welcome to ${this.businessName}. How can we help you today?`,
          sender: 'bot',
          buttons: [
            { id: 1, text: "Business Details" },
            { id: 2, text: "Regional Delivery Times" },
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
          ]
        });
      },
  
      handleButtonClick(button) {
        if (this.buttonsDisabled) return; // Prevent multiple clicks while disabled
        
        this.buttonsDisabled = true; // Disable buttons when one is clicked
  
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
                text: `What would you like to know? `,
                sender: 'bot',
                buttons: [
                  { id: 3, text: "Location" },
                  { id: 4, text: "Contact" },
                  { id: 5, text: "Working Hours"},
                  { id: 6, text: "Product Description"},
                ],
                buttonContainerClass: 'chat-buttons-container',
              });
              break;
              
            case 2:
            this.messages.push({
                id: new Date().getTime(),
                text: 'Please Select a Region:',
                sender: 'bot',
                buttons: [
                  { id: 10, text: "Region 1" },
                  { id: 11, text: "Region 2" },
                  { id: 12, text: "Region 3 " },
                  { id: 13, text: "Region 4A" },
                  { id: 14, text: "Region 4B" },
                  { id: 15, text: "Region 5" },
                  { id: 16, text: "Region CAR" },
                  { id: 17, text: "Region NCR" }
                ]
              });
  
            
              break;
              
            case 3:
            this.messages.push({
                id: new Date().getTime(),
                text: `We are located at ${this.businessProvince}, ${this.businessCity},  ${this.businessBarangay},  ${this.businessAddress}  `,
                sender: 'bot',
              });
              setTimeout(() => {
                this.showEndMessage(); 
                this.scrollToBottom();
              }, 1000);
              
              break;
              
            case 4:
            this.messages.push({
                id: new Date().getTime(),
                text: 'Select a Contact Option',
                sender: 'bot',
                buttons: [
                  { id: 7, text: "Contact Number" },
                  { id: 8, text: "Social Media" },
                  { id: 9, text: "E-Commerce" },
                ]
              });
  
            
              break;
  
            case 5:
            this.messages.push({
                id: new Date().getTime(),
                text: `We are available from  ${this.workingHours}  `,
                sender: 'bot',
              });
              setTimeout(() => {
                this.showEndMessage(); 
                this.scrollToBottom();
              }, 1000);
              
              break;
            
            case 6:
            this.messages.push({
                id: new Date().getTime(),
                text: `We sell ${this.productDescription}  `,
                sender: 'bot',
              });
              setTimeout(() => {
                this.showEndMessage(); 
                this.scrollToBottom();
              }, 1000);
              
              break;
            
            
            case 7:
            this.messages.push({
                id: new Date().getTime(),
                text:this.formatMessage( `You can contact us through the following Numbers: 
                      \n Phone number: ${this.businessPhoneNumber} 
                      \n Telephone number: ${this.businessTelephoneNumber} `),
                sender: 'bot',
              });
              setTimeout(() => {
                this.showEndMessage(); 
                this.scrollToBottom();
              }, 1000);
              
              break;
  
            case 8:
            this.messages.push({
                id: new Date().getTime(),
                text:this.formatMessage( `You can contact us through the following Social Media Sites: 
                      \n Facebook: ${this.businessFacebook} 
                      \n X: ${this.businessX} 
                      \n Instagram: ${this.businessInstagram} 
                      \n Tiktok: ${this.businessTiktok} `),
                sender: 'bot',
              });
              setTimeout(() => {
                this.showEndMessage(); 
                this.scrollToBottom();
              }, 1000);
              
              break;
  
            case 9:
            this.messages.push({
                id: new Date().getTime(),
                text:this.formatMessage( `You can contact us through the following E-Commerce Sites: 
                      \n Lazada: ${this.lazada} 
                      \n Shopee: ${this.shopee} `),
                sender: 'bot',
              });
              setTimeout(() => {
                this.showEndMessage(); 
                this.scrollToBottom();
              }, 1000);
              
              break;
            
            case 10:
            this.messages.push({
                id: new Date().getTime(),
                text: `Delivery time for Region 1: ${this.region1}  `,
                sender: 'bot',
              });
              setTimeout(() => {
                this.showEndMessage(); 
                this.scrollToBottom();
              }, 1000);
              
              break;
              
            case 11:
            this.messages.push({
                id: new Date().getTime(),
                text: `Delivery time for Region 2: ${this.region2}  `,
                sender: 'bot',
              });
              setTimeout(() => {
                this.showEndMessage(); 
                this.scrollToBottom();
              }, 1000);
              
              break;
            
            case 12:
            this.messages.push({
                id: new Date().getTime(),
                text: `Delivery time for Region 3: ${this.region3}  `,
                sender: 'bot',
              });
              setTimeout(() => {
                this.showEndMessage(); 
                this.scrollToBottom();
              }, 1000);
              
              break;
            
            case 13:
            this.messages.push({
                id: new Date().getTime(),
                text: `Delivery time for Region 4A: ${this.region4a}  `,
                sender: 'bot',
              });
              setTimeout(() => {
                this.showEndMessage(); 
                this.scrollToBottom();
              }, 1000);
              
              break;
            
            case 14:
            this.messages.push({
                id: new Date().getTime(),
                text: `Delivery time for Region 4B: ${this.region4b}  `,
                sender: 'bot',
              });
              setTimeout(() => {
                this.showEndMessage(); 
                this.scrollToBottom();
              }, 1000);
              
              break;
  
            case 15:
            this.messages.push({
                id: new Date().getTime(),
                text: `Delivery time for Region 5: ${this.region5}  `,
                sender: 'bot',
              });
              setTimeout(() => {
                this.showEndMessage(); 
                this.scrollToBottom();
              }, 1000);
              
              break;
  
            case 16:
            this.messages.push({
                id: new Date().getTime(),
                text: `Delivery time for NCR Region: ${this.regionNCR}  `,
                sender: 'bot',
              });
              setTimeout(() => {
                this.showEndMessage(); 
                this.scrollToBottom();
              }, 1000);
              
              break;
  
            case 17:
            this.messages.push({
                id: new Date().getTime(),
                text: `Delivery time for CAR Region: ${this.regionCAR}  `,
                sender: 'bot',
              });
              setTimeout(() => {
                this.showEndMessage(); 
                this.scrollToBottom();
              }, 1000);
              
              break;
  
            default:
              botResponse = 'I\'m not sure what you selected!';
          }
  
          if (botResponse.trim()) {
            this.messages.push({ id: new Date().getTime(), text: botResponse, sender: 'bot' });
          }
  
          this.scrollToBottom();
          this.buttonsDisabled = false;
        }, 1000);
      },
      scrollToBottom() {
        this.$nextTick(() => {
          const messagesContainer = this.$refs.messages;
          messagesContainer.scrollTop = messagesContainer.scrollHeight;
        });
      },
      formatMessage(messageText) {
      return messageText.replace(/\n/g, '<br>'); 
    }
  
    } 
  };
  </script>
  
  <style scoped>
  
  .chat-circle {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    overflow: hidden; 
  }
  
  .chat-icon {
    width: 100%; 
    height: 100%; 
    object-fit: cover; 
    border-radius: 50%; 
  }
  
  .chat-window {
    background-color: white;
    border: 1px solid #ccc;
    border-radius: 10px;
    box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
    display: flex;
    flex-direction: column;
    overflow: hidden; 
  }
  
  .chat-header {
    background-color: #007bff;
    color: white;
    padding: 5px 10px; 
    display: flex;
    justify-content: space-between;
    align-items: center;
    height: 40px; 
    border-top-left-radius: 10px;
    border-top-right-radius: 10px;
  }
  
  
  .chat-body {
    flex: 1;
    padding: 10px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    overflow: hidden;
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
  .chat-buttons-container {
    display: flex;
    flex-wrap: wrap;
    gap: 10px; 
    margin-top: 10px;
    justify-content: center;
  }
  
  .chat-button {
    padding: 10px 20px; 
    background-color: transparent;
    color: #007bff;
    border: 1px solid #007bff;
    border-radius: 20px;
    cursor: pointer;
    font-size: 14px;
    min-width: 80px; 
    flex: 1 1 auto; 
    text-align: center;
    box-sizing: border-box;
  }
  
  .chat-button:hover {
    background-color: #007bff;
    color: white;
  }
  
  .chat-buttons button {
    
    padding: 10px 20px; 
    background-color: transparent;
    color: #007bff;
    border: 1px solid #007bff;
    border-radius: 20px;
    cursor: pointer;
    font-size: 14px;
    min-width: 100px; 
    margin: 5px; 
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
  .fa.fa-twitter{
	font-family:sans-serif;
}
.fa.fa-twitter::before{
	content:"𝕏";
	font-size:1.2em;
}
  </style>
  