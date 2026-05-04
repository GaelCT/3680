<!-- all imports 
and scripts

Need to add a add event listener for enter. 
if shift and enter then not enter. 


-->

<script setup>

//both versions were being used . . .
 //import { useEventSource } from './node_modules/@vueuse/core'; //used to catch the sse stream with JSON
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { VBtn } from 'vuetify/components';
// import enter from '../enter.js';
import { useEventSource } from '@vueuse/core'; 


const router = useRouter();
const username = ref('');
const message = ref('');
const users = ref([]);
const chatHistory = ref([]);

const { status, data, error, close } = useEventSource('/api/sse-endpoint');

//const connection = new EventSource("davalos.cs3680.com/api/sse-endpoint");
onMounted(async () => {
    const token = localStorage.getItem('token')
    if (!token) {
        router.push('/')
        return
    }
    username.value = localStorage.getItem('username')

    //fetch all users
    const res = await fetch('/dro/api/getUsers.php', {
        headers: { 'Authorization': 'Bearer ' + token }
    })
    users.value = await res.json()
})

const logOut = () => {
    localStorage.removeItem('token')
    localStorage.removeItem('username')
    localStorage.removeItem('userId')
    router.push('/')
}


// Event Source
const EndpointA = new EventSource('https://davalos.cs3680.com/stream.php', {
    withCredentials: true
});

//onmessage is a event listener for messages
EndpointA.onmessage = function(event) {
    console.log('Received data:', event.data);
    const incomingMessage = JSON.parse(event.data); // data is JSON so parse it
    chatHistory.value.push(incomingMessage);
};

const userID = localStorage.getItem('userId');
const packageMessageAnduserID = () => {
    return {
        user_id: userID,
        content: message.value
    }
}
//add a function tied to a button
 //probably load the entire thing in a different file
 async function enter(){
    try{
        const response = await fetch('https://davalos.cs3680.com/dro/ProjectCMPS3680/sendMessage.php', {
        method: 'POST',
            headers: {
                'Content-Type': 'application/json'
    },
        body: JSON.stringify(packageMessageAnduserID())
    })
        if (!response.ok) {
            throw new Error('Network response was not ok'); 
        }

        const data = await response.json();
        message.value = '';
        console.log('Message sent successfully:', data);

        } catch(error) {
            console.error('Fetch error:', error);
        }
    }


</script>
<template>

<div class="fulltextcontainer">
    <div class="sidebar">
        <div class="profileLayout">
            <h3>{{  username  }}</h3>
            <button @click="logOut">Log Out</button>
        </div>

        <div class="userList">
            <h4>Users</h4>
            <div v-for="user in users" :key="user.user_id" class="userItem">
                {{  user.username  }}
            </div>
        </div>
    </div>

    <div class="chat-window">
        <div v-for="msg in chatHistory" :key="msg.message_id" class="message-bubble">
          <span class="user">User {{ msg.user_id }}:</span>
          <p class="text">{{ msg.content }}</p>
          <small class="time">{{ msg.sent_at }}</small>
        </div>
    </div>

<div class="container">
    
    <div class="textplate">
        <span class="icon">+</span>
            <textarea
                v-model="message" 
                name="mainText" 
                rows="2" 
                cols="33"
                placeholder="Text here...">
            </textarea> <!-- can the error go away-->

            <div class="search">
                <input type="text" placeholder="Search...">
                <!-- pull js search into here-->
            </div> 
            <!--reference the previous js file here-->
            <v-btn color="blue" @click="enter">
                Send
            </v-btn>
            
    </div>
    <div class="squareAI">
        <v-btn class="aiButton" color="primary" @click="search">Summarize</v-btn>
    </div>  
</div>
 
</div>

</template>


<style>
html, body {
   margin: 0;
   padding: 0;
   height: 100%;
   width: 100%;
   background-color: #1a1a1a !important;
}

.squareAI {
    display: flex;
    justify-content: center;
    color: rgb(186, 198, 20); 
    border: #c44b4b solid 4px;
    border-radius: 5px;
    cursor: pointer;
    text-align: center;
}

.aibutton {
    background-color: #c44b4b;
    color: white;
    border: cadetblue solid 1px;
    padding: 10px 20px;
    border-radius: 5px;
    cursor: pointer;
}

</style>

<style scoped>
* {
   margin: 0;
   padding: 0;
   box-sizing: border-box;
}

.fulltextcontainer {
    display: flex;
    min-height: 100vh;
    width: 100vw;
    position: fixed;
    top: 0;
    left: 0;
}

.sidebar {
    width: 250px;
    background-color: #2a2a2a;
    border-right: 1px solid #444;
    padding: 20px;
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.profileLayout h3 {
    color: white;
}

.profileLayout button {
    background-color: #444;
    color: white;
    border: none;
    padding: 6px 12px;
    border-radius: 5px;
    cursor: pointer;
}

.userList h4 {
    color: #888;
    margin-bottom: 10px;
}

.userItem {
    color: white;
    padding: 8px;
    border-radius: 5px;
    cursor: pointer;
    margin-bottom: 5px;
}

.userItem:hover {
    background-color: #444;
}

.container{
    flex: 1;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    width: 100%;
    background-color: #1a1a1a;
    position: relative;
}

.textplate {
    display: flex;
    justify-content: center;
    align-items: center;
    border: 1px solid #444; 
    padding: 8px 16px;
    width: 100%;
    border-radius: 15px;
    max-width: 600px;
    transition: border-color 0.3s;
    background-color: #2a2a2a; 
    position: absolute;
    top: 80%;
}

textarea {
    width: 100%;
    background: transparent; 
    border: none;           
    outline: none;           
    color: white;            
    font-size: 1.1rem;
    padding: 10px;
    resize: none;            
}

.icon {
    color: #888;             
    font-weight: 900;
    font-size: 1.5rem;
    margin-right: 10px;
    cursor: pointer;
}

input[type="text"] {
    width: 100%;
    padding: 10px;
    border: 1px solid #444;
    border-radius: 5px;
    background-color: #2a2a2a;
    color: white;
}

.chat-window {
    flex: 1;
    display: flex;
    flex-direction: column;
    overflow-y: auto;
    padding: 20px;
    gap: 10px;
    margin-bottom: 120px;
    background-color: #1a1a1a;
}

.message-bubble {
    background-color: #2a2a2a;
    border-radius: 10px;
    padding: 10px 14px;
    max-width: 70%;
    align-self: flex-start;
}

.message-bubble .user {
    color: #888;
    font-size: 0.75rem;
    display: block;
    margin-bottom: 4px;
}

.message-bubble .text {
    color: white;
    margin: 0;
    font-size: 0.95rem;
}

.message-bubble .time {
    color: #555;
    font-size: 0.7rem;
    display: block;
    margin-top: 4px;
}
</style>