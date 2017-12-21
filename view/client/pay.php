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
  //支付函数
  function paid(){
    //设置传递的参数
    var data = {
        'event'   : 'pay',
        'param'   : '123456789',
    }
    //设置请求参数
    var base = {
        'controller' : 'long',
        'action'     : 'paid',
        'sent'       : '1',
        'type'       : 'one',
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
<button onclick='paid()' style="font-size:45px;height:100px;width:300px;">支付</button>
</body>
</html>