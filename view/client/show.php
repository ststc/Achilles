<!DOCTYPE html>
<html>
<head>
  <title><?=$title?></title>
  <meta charset="UTF-8">
  <script type="text/javascript">
  var Socket = new WebSocket("ws://0.0.0.0:9502");
  Socket.onopen = function (event) {
    //设置传递的参数
    var data = {
        'event'   : 'live',
    }
    //设置请求参数
    var base = {
        'controller' : 'long',
        'action'     : 'show',
        'sent'       : '0',
        'type'       : 'group',
        'data'       :  data,
    }
    var mesaage = JSON.stringify(base);
    Socket.send(mesaage); 
  };
  Socket.onmessage = function (event) {
    var data = event.data;
    var res  = JSON.parse(data);
    if(res.code == 200){
       alert('next step');
    }else {
       alert(res.desc);
    }
  }
  </script>
</head>
<body style='margin:0 auto;text-align:center;'>
<div style='height:10px;'></div>
<video controls src='http://cdn.midoci.com/ZC/liveshow/show.mp4'></video>
</body>
</html>