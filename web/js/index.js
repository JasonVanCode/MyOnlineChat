// document.querySelector('.chat[data-chat=person1]').classList.add('active-chat');
// document.querySelector('.person[data-chat=person1]').classList.add('active');

var friends = {
  list: document.querySelector('ul.people'),
  all: document.querySelectorAll('.left .person'),
  name: '' },

chat = {
  container: document.querySelector('.container .right'),
  current: null,
  person: null,
  name: document.querySelector('.container .right .top .name') };


friends.all.forEach(function (f) {
  f.addEventListener('mousedown', function () {
    f.classList.contains('active') || setAciveChat(f);
  });
});

function setAciveChat(f) {
  friends.list.querySelector('.active').classList.remove('active');
  f.classList.add('active');
  chat.current = chat.container.querySelector('.active-chat');
  chat.person = f.getAttribute('data-chat');
  chat.current.classList.remove('active-chat');
  chat.container.querySelector('[data-chat="' + chat.person + '"]').classList.add('active-chat');
  friends.name = f.querySelector('.name').innerText;
  chat.name.innerHTML = friends.name;
}




// ws = new WebSocket("ws://192.168.137.34:2000");
// ws.onopen = function() {
//     console.log('连接成功');
//     ws.send('hello world');
//     console.log('给服务器发送了一个hello world');
// };
// ws.onmessage = function(e) {
//     console.log("收到服务端的消息：" + e.data);
// };

// ws.onclose = function()
// {
//     console.log('连接已经关闭。。。');    
// }