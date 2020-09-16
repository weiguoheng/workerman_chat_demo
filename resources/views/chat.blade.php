<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>PHP聊天室</title>
</head>
<link href="/resources/css/chat.css" rel="stylesheet">
<body onload="connect()">
<div class="container">
    <div class="row chat-box">
        <div>PHP聊天室</div>
        <div class="chat-box-all">
            <div class="friends-list-box">
                <ul id="friends_list">
                </ul>
            </div>
            <div class="thumbnail chat-box chat-history">
                <div class="chat-ul" id="dialog">
                    <ul style="width:1px; min-width:100px;" class="fr chat-ul" id="chat_ul">
                    </ul>
                </div>
            </div>
            <form onsubmit="onSubmit(); return false;">
                <textarea class="chat-box" style="height: 100px;" id="textarea"></textarea>
                <div class="say-btn chat-box">
                    <input id="submitBtn" type="submit" class="btn fr" value="发送" />
                </div>
            </form>
        </div>

    </div>
    <div class="row chat-box">
        <div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var name,ws,client_list={};
    var ws = new WebSocket("ws://127.0.0.1:2000");
    function connect() {
        ws = new WebSocket("ws://127.0.0.1:2000");
        ws.onopen = function() {
            console.log("连接成功");
            ws.send('tom');
            console.log("给服务端发送一个字符串：tom");
            onopen()
        };
        ws.onmessage = onmessage;
        ws.onmessage = function(e) {
            console.log("收到服务端的消息：" + e.data);
        };
    }

    // 连接建立时发送登录信息
    function onopen()
    {
        if(!name)
        {
            show_prompt();
        }
        let userName = name.replace(/"/g, '\\"')
        // 登录
        var login_data = '{"type":"login","client_name":"'+userName+'","room_id":"<?php echo isset($_GET['room_id']) ? $_GET['room_id'] : 1?>"}';
        console.log("websocket握手成功，发送登录数据:"+login_data);
        // friends_list
        var ul = document.getElementById("friends_list");
        //添加 li
        var li = document.createElement("li");
        var text = document.createTextNode(userName);
        li.appendChild(text);
        li.className += 'friends-list';
        ul.appendChild(li);
        ws.send(login_data);
    }
    // 回车提交表单
    document.onkeydown = function(e) {
        var textContent = document.getElementById("textarea");
            if (!e)
                e = window.event;//火狐中是 window.event
            if ((e.keyCode || e.which) == 13) {
                if((textContent.value).replace(/\s+/g, "") != '') {
                    document.getElementById("submitBtn").click(); //loginButtonId为button登录按钮的ID
                } else {
                    alert('请输入内容');
                }
        }
    }
    function onSubmit(){
        ws = new WebSocket("ws://127.0.0.1:2000");
        var input = document.getElementById("textarea");
        ws.onopen = function() {
            console.log("连接成功");
            ws.send(input.value);
            console.log("给服务端发送一个字符串："+input.value);
        };
        ws.onmessage = function(e) {
            var ul = document.getElementById("chat_ul");
            var textContent = document.getElementById("textarea");
            //添加 li
            var li = document.createElement("li");
            var text = document.createTextNode(e.data);
            li.appendChild(text);
            li.className += 'chat-li';
            ul.appendChild(li);
            console.log("收到服务端的消息：" + e.data);
            // 清空输入框内容
            textContent.value = '';
        };
    }
    // 输入姓名
    function show_prompt(){
        name = prompt('输入你的名字：', '');
        if(!name || name=='null'){
            name = '游客';
        }
    }

    // 服务端发来消息时
    function onmessage(e)
    {
        console.log('ddddddddddd');
        var data = JSON.parse(e.data);
        console.log(data);
        switch(data['type']){
            // 服务端ping客户端
            case 'ping':
                ws.send('{"type":"pong"}');
                break;
            // 登录 更新用户列表
            case 'login':
                //{"type":"login","client_id":xxx,"client_name":"xxx","client_list":"[...]","time":"xxx"}
                say(data['client_id'], data['client_name'],  data['client_name']+' 加入了聊天室', data['time']);
                if(data['client_list'])
                {
                    client_list = data['client_list'];
                }
                else
                {
                    client_list[data['client_id']] = data['client_name'];
                }
                flush_client_list();
                console.log(data['client_name']+"登录成功");
                break;
            // 发言
            case 'say':
                //{"type":"say","from_client_id":xxx,"to_client_id":"all/client_id","content":"xxx","time":"xxx"}
                say(data['from_client_id'], data['from_client_name'], data['content'], data['time']);
                break;
            // 用户退出 更新用户列表
            case 'logout':
                //{"type":"logout","client_id":xxx,"time":"xxx"}
                say(data['from_client_id'], data['from_client_name'], data['from_client_name']+' 退出了', data['time']);
                delete client_list[data['from_client_id']];
                flush_client_list();
        }
    }
    function flush_client_list(){
        var userlist_window = $("#userlist");
        var client_list_slelect = $("#client_list");
        userlist_window.empty();
        client_list_slelect.empty();
        userlist_window.append('<h4>在线用户</h4><ul>');
        client_list_slelect.append('<option value="all" id="cli_all">所有人</option>');
        for(var p in client_list){
            userlist_window.append('<li id="'+p+'">'+client_list[p]+'</li>');
            client_list_slelect.append('<option value="'+p+'">'+client_list[p]+'</option>');
        }
        $("#client_list").val(select_client_id);
        userlist_window.append('</ul>');
    }
</script>
</body>
</html>
