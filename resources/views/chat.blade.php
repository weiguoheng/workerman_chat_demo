<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>PHP聊天室</title>
</head>
<style>
    .center-block{
        margin:0 auto;
        width:250px;
        height:50px;
    }
    .chat-box{
        margin: 0 auto;
        width: 400px;
    }
    .chat-history{
        height: 200px;
        width: 400px;
        padding: 2px;
        border: 1px solid #8c8c8c;
        margin: 5px 0;
    }
    .fr{
        float: right;
    }
    .chat-li{
        list-style: none;
        padding: 10px 15px;
        background: #2BD54D;
        margin: 5px 15px;
        border-radius: 5px;
        float: right;
    }
    .chat-ul{
        width: 300px;
        float: right;
    }
</style>
<body onload="connect()">
<div class="container">
    <div class="row chat-box">
        <div>PHP聊天室</div>
        <div>
            <div class="thumbnail chat-box chat-history">
                <div class="chat-ul" id="dialog">
                    <ul style="width:auto; min-width:100px;" class="fr chat-ul" id="chat_ul">
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
        <div class="col-md-3 column">
            <div class="thumbnail">
                <div class="caption" id="userlist"></div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var ws = new WebSocket("ws://127.0.0.1:2000");
    function connect() {
        ws = new WebSocket("ws://127.0.0.1:2000");
        ws.onopen = function() {
            console.log("连接成功");
            ws.send('tom');
            console.log("给服务端发送一个字符串：tom");
            onopen()
        };
        ws.onmessage = function(e) {
            console.log("收到服务端的消息：" + e.data);
        };
    }

    // 连接建立时发送登录信息
    function onopen()
    {
        if(!name)
        {
            // show_prompt();
        }
        // 登录
        var login_data = '{"type":"login","client_name":"'+name.replace(/"/g, '\\"')+'","room_id":"<?php echo isset($_GET['room_id']) ? $_GET['room_id'] : 1?>"}';
        console.log("websocket握手成功，发送登录数据:"+login_data);
        ws.send(login_data);
    }
    // 回车提交表单
    document.onkeydown = function(e) {
        if (!e)
            e = window.event;//火狐中是 window.event
        if ((e.keyCode || e.which) == 13) {
            document.getElementById("submitBtn").click(); //loginButtonId为button登录按钮的ID
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
</script>
</body>
</html>
