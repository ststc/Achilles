<!DOCTYPE html>
<html>
<head>
  <title><?=$title?></title>
  <meta charset="UTF-8">
  <script type="text/javascript">
  var Socket = new WebSocket("ws://0.0.0.0:9502");
  Socket.onopen = function (event) {
  };
  Socket.onmessage = function (event) {
    var data = event.data;
    var res  = JSON.parse(data);
    if(res.status == 0){
       alert('had push!');
    }else if(res.status == 1){
       alert('push ok!');
    }
  }
  //转跳
  function turn(){
    //设置传递的参数
    var data = {
        'event'   : 'live',
    }
    //设置请求参数
    var base = {
        'controller' : 'long',
        'action'     : 'console',
        'sent'       : '1',
        'type'       : 'group',
        'data'       :  data,
    }
    //推送信息
    var mesaage = JSON.stringify(base);
    Socket.send(mesaage);
  }
  </script>
</head>
<body style='margin:0 auto;text-align:center;'>
<div style='height:10px;'></div>
<button onclick='turn()' style="font-size:45px;height:100px;width:300px;">录播</button>
</body>
</html>