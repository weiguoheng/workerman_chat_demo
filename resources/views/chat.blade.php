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
    }
</style>
<body onload="connect()">
<div class="container">
    <div class="row chat-box">
        <div>PHP聊天室</div>
        <div>
            <div class="thumbnail">
                <div class="chat-box chat-history" id="dialog">
                    <ul style="width:auto; min-width:100px;" class="fr" id="chat_ul">
                    </ul>
                </div>
            </div>
            <form onsubmit="onSubmit(); return false;">
                <textarea class="chat-box" style="height: 100px;" id="textarea"></textarea>
                <div class="say-btn chat-box">
                    <input type="submit" class="btn fr" value="发送" />
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
        };
        ws.onmessage = function(e) {
            console.log("收到服务端的消息：" + e.data);
        };
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
