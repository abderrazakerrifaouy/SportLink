// stores/chatStore.js
import { defineStore } from 'pinia';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
import { useUserStore } from '@/stores/userStore';


export const useChatStore = defineStore('chat', {
  state: () => ({
    echo: null,
    isConnected: false,
  }),

  actions: {
    initEcho() {
      window.Pusher = Pusher;
      Pusher.logToConsole = true;
      const token = localStorage.getItem('token');

      this.echo = new Echo({
        broadcaster: 'pusher',
        key: 'e0dd07e59dc85662c542',
        cluster: 'eu',
        forceTLS: true,
        authEndpoint: 'http://localhost:8081/api/broadcasting/auth',
        auth: {
          headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          }
        }
      });
    },

    startListening(userId) {
      if (!this.echo) {
        this.initEcho();
      }

      const userStore = useUserStore(); 

      this.echo.private(`SporeLink.${userId}`)
        .listen('.MessageSent', (data) => {
          console.log("New message received via Echo:", data);

          // تحويل البيانات لكتناسب الـ format لي عندك فـ الـ store
          const newMessage = {
            id: data.id || Date.now(),
            message: data.message,
            sender_id: data.idSender, // ردي البال هنا: idSender من Echo كيرجع sender_id فـ الـ UI
            receiver_id: data.idReceiver,
            created_at: new Date().toISOString()
          };

          // 1. كنضيفو الميساج للـ Array لي كيتعرض دابا
          userStore.messages.push(newMessage);

          // 2. كنحدثو الـ Conversations باش يبان الميساج اللخر فـ الجنب
          userStore.fetchConversations();
        });
    }
  }
});
