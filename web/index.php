<!DOCTYPE html>
<html lang="en" >
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>HTML5电脑端微信聊天窗口界面 - 站长素材</title>
<link rel="stylesheet" href="https://unpkg.com/element-ui/lib/theme-chalk/index.css">
<link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,600" rel="stylesheet">
<link rel="stylesheet" href="css/reset.min.css">
<link rel="stylesheet" href="css/style.css">
</head>
<body>

<div id="app">
    <div class="wrapper">
        <div class="container">
            <div class="left">
                <div class="top">
                    <img src="img/thomas.jpg" alt="" style="width: 40px;height: 40px;border-radius: 50%;margin-left: 12px;"/>
                    <input type="text" placeholder="Search" />
                    <!-- <a href="javascript:;" class="search"></a> -->
                </div>
                <ul class="people">
                <!-- active -->
                    <li class="person " data-chat="person1"  v-for="(item,index) in allfriendlist" :class="item.default_check?'active':''" @click="showChatlog(index)"> 
                        <img :src="item.img" alt="" />
                        <span class="name">{{item.name}}</span>
                        <span class="time">{{ item.last_time }}</span>
                        <span class="preview">{{ item.preview}}</span>
                    </li>
                </ul>
            </div>
            <div class="right">
                <div class="top"><span>To: <span class="name">{{chatuser.name}}</span></span></div>
                <!-- active-chat -->
                <div class="chat active-chat ">
                    <div class="conversation-start">
                        <span>Today, 6:48 AM</span>
                    </div>
                    <div class="bubble" v-for="item in chatlog" :class="loginuser.id != item.send_id?'me':'you'" v-if="loginuser.id == item.send_id || chatuser.id == item.send_id">
                        {{item.content}}
                    </div>
                </div>
                <div class="write">
                    <!-- <a href="javascript:;" class="write-link attach"></a> -->
                    <input type="text" v-model="msg_content" />
                    <a href="javascript:;" class="write-link smiley"></a>
                    <a href="javascript:;" class="write-link send" @click="sendMsg"></a>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
<!-- 引入组件库 -->
<script src="https://cdn.bootcdn.net/ajax/libs/element-ui/2.15.0/index.min.js"></script>
<!-- <script  src="js/index.js"></script> -->
<script type="text/javascript">//you 0 me 1
    var app = new Vue({
        el: '#app',
        data: {
            message: 'Hello Vue!',
            allfriendlist:[],
            userlist:[
                {id:'C001',name:'A',img:'img/thomas.jpg',last_time:'2:09 PM','preview':'I was wondering...','default_check':true,chatlog:[
                    {'content':' Hello,','send_id':'C001','time':''},
                    {'content':"it's me.",'send_id':'C001','time':''},
                    {'content':"I was wondering...",'send_id':'C001','time':''},
                    {'content':" Hello, can you hear me?",'send_id':'C002','time':''},
                    {'content':" I'm in California dreaming",'send_id':'C002','time':''},
                    {'content':"When we were younger and free...",'send_id':'C002','time':''},
                    {'content':"I've forgotten how it felt before",'send_id':'C002','time':''}
                ]},
                {id:'C002',name:'B',img:'img/dog.png',last_time:'1:44 PM','preview':'Ive forgotten....','default_check':false,chatlog:[
                    {'content':' Hello,','send_id':'C001','time':''},
                    {'content':"it's me.",'send_id':'C001','time':''},
                    {'content':"I was wondering...",'send_id':'C001','time':''},
                    {'content':" Hello, can you hear me?",'send_id':'C002','time':''},
                    {'content':" I'm in California dreaming",'send_id':'C002','time':''},
                    {'content':"When we were younger and free...",'send_id':'C002','time':''},
                    {'content':"I've forgotten how it felt before",'send_id':'C002','time':''}
                ]},
                {id:'C003',name:'C',img:'img/louis-ck.jpeg',last_time:'2:09 PM','preview':'But we’re probably gonna need a new carpet.','default_check':false,chatlog:[
                    {'content':"Hey human!",'to_me':0,'time':''},
                    {'content':"Umm... Someone took a shit in the hallway.",'to_me':0,'time':''},
                    {'content':" ... what.",'to_me':1,'time':''},
                    {'content':" Are you serious?",'to_me':1,'time':''},
                    {'content':"I mean...",'to_me':0,'time':''},
                    {'content':"It’s not that bad...",'to_me':0,'time':''},
                    {'content':"But we’re probably gonna need a new carpet.",'to_me':0,'time':''},
                ]},
            ],
            chatlog:[],
            loginuser:{id:null,name:null},
            chatuser:{id:null,name:null},
            ws_client:null,
            msg_content:''
        },
        mounted:function(){
            if(this.ws_client){
                this.ws_client.close();
            }
            this.login();
        },
        methods:{
            showChatlog(index){
                let self = this;
                self.allfriendlist.map((val,key)=>{
                    if(key == index){
                        val.default_check = true;
                        self.chatlog = val.chatlog;
                        self.chatuser = {id:val.id,name:val.name};
                    }else{
                        val.default_check = false;
                    }
                });
            },
            sendMsg(){
                if(!this.msg_content){
                    this.$message({
                            type: 'error',
                            message: '请填写要发送的内容！'
                        });
                        return;
                }
                let data = JSON.stringify({my_id:this.loginuser.id,chatuser_id:this.chatuser.id,content:this.msg_content});
                this.sendmessage(data);
                this.chatlog.push( {'content':this.msg_content,'send_id':this.loginuser.id,'time':''});
                this.msg_content = '';
            },
            login(){
                this.$prompt('请输入账号', '提示', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                }).then(({ value }) => {
                    let errmsg = '';
                    if(!value){
                        errmsg = '请输入账号！';
                    }
                    let data = this.checkuser(value)
                    if(!data){
                        errmsg = '该用户不存在！';
                    }
                    if(errmsg){
                        this.$message({
                            type: 'error',
                            message: errmsg
                        });
                        return;
                    }
                 
                    try {
                        this.ws_client = new WebSocket("ws://192.168.137.34:2000");
                        console.log(this.ws_client);
                    } catch (e) {
                        this.$message({
                            type: 'error',
                            message: e.message
                        });
                    }
                    let self = this;
                    self.ws_client.onopen = function(){
                        self.sendmessage(data);
                    }
                    this.onmessage();
                    this.onclose();
                });
            },
            onmessage(){
                let self = this;
                self.ws_client.onmessage = function(evt){
                    var received_msg = evt.data;
                    if(!received_msg){
                        return;
                    }
                    received_msg = JSON.parse(received_msg);
                    if(received_msg.issuccess == 0){
                        self.$message({
                            type: 'error',
                            message: received_msg.content
                        });
                    }else{
                        self.chatlog.push( {'content':received_msg.content,'send_id':received_msg.send_id,'time':''});
                    }
                }
            },
            onclose()
            {
                this.ws_client.onclose  = function(){
                    alert('连接已经关闭。。。');
                }
            },
            sendmessage(data)
            {
                this.ws_client.send(data);
            },
            checkuser(name){
                let data = null;
                for(let item of this.userlist){
                    if(name == item.name){
                        this.loginuser = {id:item.id,name:item.name};
                        data = {userid:item.id};
                    }else{
                        this.allfriendlist.push(item);
                    }
                }
                if(this.allfriendlist.length > 0){
                    this.showChatlog(0);
                }
                
              return data?JSON.stringify(data):'';
            }
        }
})

</script>
<!-- <script  src="js/index.js"></script> -->
<div style="text-align:center;margin:1px 0; font:normal 14px/24px 'MicroSoft YaHei';">
<p>适用浏览器：360、FireFox、Chrome、Opera、傲游、搜狗、世界之窗. 不支持Safari、IE8及以下浏览器。</p>
<p>来源：<a href="http://sc.chinaz.com/" target="_blank">站长素材</a></p>
</div>
</body>
</html>