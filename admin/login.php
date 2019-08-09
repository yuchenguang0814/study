<?php
require_once '../functions.php';
function login(){
  $email = $_POST['email'];
  $password = $_POST['password'];
  //验证登录信息
  if(empty($email)){
    $GLOBALS['messeage'] = "邮箱不为空";
    return;
  }
  if(empty($password)){
    $GLOBALS['messeage'] = "密码不为空";
    return;
  }
  $sql = "SELECT * FROM users where email = '{$email}' limit 1;";
  $users = xiu_fetch_one($sql);
  if(empty($users)){
    $GLOBALS['messeage'] = "用户名和密码不匹配";
    return;
  }
  if($password!==$users['password']){
    $GLOBALS['messeage'] = "用户名和密码不匹配";
    return;
  }
  //响应
  $_SESSION['current_login_user']=$users;
  header('Location: /admin/');
}
if($_SERVER['REQUEST_METHOD']==='POST'){
  login();
}
if($_SERVER['REQUEST_METHOD']==='GET' && isset($_GET['action']) && $_GET['action']==='logout'){
  unset($_SESSION['current_login_user']);
}
 ?>
}
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Sign in &laquo; Admin</title>
  <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" type="text/css" href="/static/assets/vendors/animate/animate.css">
  <link rel="stylesheet" href="/static/assets/css/admin.css">
</head>
<body>
  <div class="login">
    <form class="login-wrap <?php echo isset($messeage)?'shake animated':'';?>" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" autocomplete="off" novalidate>
      <img class="avatar" src="/static/assets/img/default.png">
      <!-- 有错误信息时展示 -->
      <?php if (isset($messeage)): ?>
      <div class="alert alert-danger">
        <strong>错误！</strong> <?php echo $messeage ?>
      </div>
      <?php endif ?>
      <div class="form-group">
        <label for="email" class="sr-only">邮箱</label>
        <input id="email" name="email" type="email" class="form-control" placeholder="邮箱" autofocus value="<?php echo isset($_POST['email'])?$_POST['email']:'' ?>">
      </div>
      <div class="form-group">
        <label for="password" class="sr-only">密码</label>
        <input id="password" name="password" type="password" class="form-control" placeholder="密码">
      </div>
      <button class="btn btn-primary btn-block" href="index.php">登 录</button>
    </form>
  </div>
  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script>
  $(function($){
    $('#email').on('blur',function(){
      var value = $(this).val()
      var emailFormat = /^[a-zA-Z0-9]+@[a-zA-Z0-9]+\.[a-zA-Z0-9]+$/
      if(!value || !emailFormat.test(value)) return
      $.get('/admin/api/avatar.php',{email:value},function(res){
        if (!res) return
            $('.avatar').fadeOut(function () {
            $(this).on('load', function () {
            $(this).fadeIn()
            }).attr('src', res)
          })
       })
    })
  })
  </script>
</body>
</html>
