<template>
   <div class="row">

       <div class="col-8">
           <div class="card card-default">
               <div class="card-header">Messages</div>
               <div class="card-body p-0" id="message-box" style="height:300px; overflow-y:scroll">
                   <ul class="list-unstyled" >
                       <li class="p-2" v-for="(message, index) in messages" :key="index" >
                           <strong>{{ message.user.name }}</strong>
                           <div v-if="message.message">{{ message.message }}</div>
                           <!-- <div v-if="message.file">
                                <a
                                href="#"
                                @click.prevent="downloadWithAxios('/storage/app/'+message.file)"
                                > {{ message.file }}</a>
                            </div> -->
                            <div v-if="message.file">
                                <a
                                href="#"
                                @click.prevent="downloadWithAxios"
                                > {{ message.file }}</a>
                            </div>
                       </li>
                   </ul>
               </div>
           </div>
           <br>
           <div class="example-btn">
                    <file-upload
                        class="btn btn-primary"
                        post-action="/messages"
                        :size="1024 * 1024 * 10"
                        v-model="files"
                        ref="upload"
                        @input-file="$refs.upload.active = true"
                        :headers="{'X-CSRF-TOKEN': token}"
                        >
                        <i class="fa fa-plus"></i>
                        Select files
                    </file-upload>
                </div>
               <input
                    @keydown="sendTypingEvent"
                    @keyup.enter="sendMessage"
                    v-model="newMessage"
                    type="text"
                    name="message"
                    placeholder="Enter your message..."
                    class="form-control">
            <span class="text-muted" v-if="activeUser" >{{ activeUser.name }} is typing...</span>
            <div id="app">
                <button @click="downloadWithAxios">Download file with Axios</button>
            </div>
       </div>


        <div class="col-4">
            <div class="card card-default">
                <div class="card-header">Active Users</div>
                <div class="card-body">
                    <ul>
                        <li class="py-2" v-for="(user, index) in users" :key="index">
                            {{ user.name }}
                        </li>
                    </ul>
                </div>
            </div>
        </div>

   </div>
</template>

<script>
    export default {

        props:['user'],

        data() {
            return {
                messages: [],
                newMessage: '',
                users:[],
                activeUser: false,
                typingTimer: false,
                files:[],
                token:document.head.querySelector('meta[name="csrf-token"]').content,
            }
        },

        mounted () {
            this.scrollThis(1000);
        },

        created() {
            this.fetchMessages();

            Echo.join('chat')
                .here(user => {
                    this.users = user;
                })
                .joining(user => {
                    this.users.push(user);
                })
                .leaving(user => {
                    this.users = this.users.filter(u => u.id != user.id);
                })
                .listen('MessageSent',(event) => {
                    this.messages.push(event.message);
                })
                .listenForWhisper('typing', user => {
                    this.activeUser = user;

                    if(this.typingTimer){
                        clearTimeout(this.typingTimer)
                    }

                    this.typingTimer = setTimeout(() => {
                        this.activeUser = false;
                    }, 1500)
                })

        },

        methods: {
            fetchMessages() {
                axios.get('messages').then(response => {
                    this.messages = response.data;
                })
            },

            sendMessage() {

                this.messages.push({
                    user: this.user,
                    message: this.newMessage
                });

                this.scrollThis(500);

                axios.post('messages', {message: this.newMessage});

                this.newMessage = '';
            },

            sendTypingEvent() {
                Echo.join('chat')
                    .whisper('typing', this.user);
            },

            scrollThis (time) {
                setTimeout(function(){
                    var container = document.getElementById("message-box");
                    // console.log(container.scrollHeight);
                    container.scrollTop = container.scrollHeight;
                },time)
            },
            forceFileDownload(response){
                const url = window.URL.createObjectURL(new Blob([response.data]))
                const link = document.createElement('a')
                link.href = url
                link.setAttribute('download', 'file.png') //or any other extension
                document.body.appendChild(link)
                link.click()
            },
            downloadWithAxios(urlini){
                axios({
                    method: 'get',
                    url: 'https://png.pngtree.com/element_our/sm/20180327/sm_5aba147bcacf2.png',
                    responseType: 'arraybuffer',
                    // headers: {'Access-Control-Allow-Origin': this.token},
                })
                .then(response => {
                    // console.log(response);
                    this.forceFileDownload(response)
                })
                .catch(() => console.log('error occured'))
            },
        }
    }
</script>
