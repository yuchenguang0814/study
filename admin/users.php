<?php
require_once '../functions.php';
xiu_get_current_user();
function add_user(){
  $email = $_POST['email'];
  $slug = $_POST['slug'];
  $nickname = $_POST['nickname'];
  $password = $_POST['password'];
  if (empty($email) || empty($slug)||empty($nickname)||empty($password)) {
    $GLOBALS['message'] = '请完整填写表单！';
    $GLOBALS['success'] = false;
    return;
  }
  $rows = xiu_execute("insert into users values (null,'{$slug}','{$email}','{$password}','{$nickname}','/static/uploads/avatar.jpg',null,'inactive
');");
  $GLOBALS['success'] = $rows > 0;
  $GLOBALS['message'] = $rows <= 0 ? '添加失败！' : '添加成功！';
}
function edit_user(){
  global $current_edit_user;
  $id = $current_edit_user['id'];
  $email = empty($_POST['email']) ? $current_edit_user['email'] : $_POST['email'];
  $current_edit_user['email'] = $email;
  $slug = empty($_POST['slug']) ? $current_edit_user['slug'] : $_POST['slug'];
  $current_edit_user['slug'] = $slug;
  $nickname = empty($_POST['nickname']) ? $current_edit_user['nickname'] : $_POST['nickname'];
  $current_edit_user['nickname'] = $nickname;
  $password = empty($_POST['password']) ? $current_edit_user['password'] : $_POST['password'];
  $current_edit_user['password'] = $password;

  $rows = xiu_execute("update users set slug = '{$slug}', email = '{$email}',password = '{$password}',nickname = '{$nickname}' where id = '{$id}'");
  $GLOBALS['success'] = $rows > 0;
  $GLOBALS['message'] = $rows <= 0 ? '更新失败！' : '更新成功！';
}
if(empty($_GET['user_id'])){
  if($_SERVER['REQUEST_METHOD']==='POST'){
     add_user();
  }
}else{
  $current_edit_user = xiu_fetch_one('select * from users where id = ' . $_GET['user_id']);
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      edit_user();
    }
}
$users = xiu_fetch_all("SELECT * FROM users");
 ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Users &laquo; Admin</title>
  <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/static/assets/vendors/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" href="/static/assets/vendors/nprogress/nprogress.css">
  <link rel="stylesheet" href="/static/assets/css/admin.css">
  <script src="/static/assets/vendors/nprogress/nprogress.js"></script>
</head>
<body>
  <script>NProgress.start()</script>

  <div class="main">
<?php include "inc/navbar.php" ?>
    <div class="container-fluid">
      <div class="page-title">
        <h1>用户</h1>
      </div>
      <!-- 有错误信息时展示 -->
      <?php if (isset($message)): ?>
      <?php if ($success): ?>
          <div class="alert alert-success">
            <strong>成功！</strong> <?php echo $message; ?>
          </div>
        <?php else: ?>
          <div class="alert alert-danger">
            <strong>错误！</strong> <?php echo $message; ?>
          </div>
        <?php endif ?>
      <?php endif ?>
      <div class="row">
        <div class="col-md-4">
        <?php if (isset($current_edit_user)): ?>
          <form action="<?php echo $_SERVER['PHP_SELF'] ?>?user_id=<?php echo $current_edit_user['id'] ?>" method="post">
            <h2>编辑 <?php echo $current_edit_user['nickname']; ?></h2>
            <div class="form-group">
              <label for="email">邮箱</label>
              <input id="email" class="form-control" name="email" type="email" placeholder="邮箱" value="<?php echo $current_edit_user['email']; ?>">
            </div>
            <div class="form-group">
              <label for="slug">别名</label>
              <input id="slug" class="form-control" name="slug" type="text" placeholder="slug" value="<?php echo $current_edit_user['slug']; ?>">
              <p class="help-block">https://zce.me/author/<strong>slug</strong></p>
            </div>
            <div class="form-group">
              <label for="nickname">昵称</label>
              <input id="nickname" class="form-control" name="nickname" type="text" placeholder="昵称" value="<?php echo $current_edit_user['nickname']; ?>">
            </div>
            <div class="form-group">
              <label for="password">密码</label>
              <input id="password" class="form-control" name="password" type="text" placeholder="密码" value="<?php echo $current_edit_user['password']; ?>">
            </div>
            <div class="form-group">
              <button class="btn btn-primary" type="submit">保存</button>
            </div>
          </form>
        <?php else: ?>
          <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
            <h2>添加新用户</h2>
            <div class="form-group">
              <label for="email">邮箱</label>
              <input id="email" class="form-control" name="email" type="email" placeholder="邮箱">
            </div>
            <div class="form-group">
              <label for="slug">别名</label>
              <input id="slug" class="form-control" name="slug" type="text" placeholder="slug">
              <p class="help-block">https://zce.me/author/<strong>slug</strong></p>
            </div>
            <div class="form-group">
              <label for="nickname">昵称</label>
              <input id="nickname" class="form-control" name="nickname" type="text" placeholder="昵称">
            </div>
            <div class="form-group">
              <label for="password">密码</label>
              <input id="password" class="form-control" name="password" type="text" placeholder="密码">
            </div>
            <div class="form-group">
              <button class="btn btn-primary" type="submit">添加</button>
            </div>
          </form>
        <?php endif ?>
        </div>
        <div class="col-md-8">
          <div class="page-action">
            <!-- show when multiple checked -->
            <a id="btn-delect" class="btn btn-danger btn-sm" href="/admin/delete.php;" style="display: none">批量删除</a>
          </div>
          <table class="table table-striped table-bordered table-hover">
            <thead>
               <tr>
                <th class="text-center" width="40"><input type="checkbox"></th>
                <th class="text-center" width="80">头像</th>
                <th>邮箱</th>
                <th>别名</th>
                <th>昵称</th>
                <th>状态</th>
                <th class="text-center" width="100">操作</th>
              </tr>
            </thead>
            <tbody>
            <?php if (isset($users)): ?>
              <?php foreach ($users as $key): ?>
              <tr>
                <td class="text-center"><input type="checkbox" data-id="<?php echo $key['id']; ?>"></td>
                <td class="text-center"><img class="avatar" src="<?php echo $key['avatar']; ?>"></td>
                <td><?php echo $key['email']; ?></td>
                <td><?php echo $key['slug']; ?></td>
                <td><?php echo $key['nickname']; ?></td>
                <td><?php echo $key['status']=='activated'?'激活':'未激活' ?></td>
                <td class="text-center">
                  <a href="/admin/users.php?user_id=<?php echo $key['id'] ?>" class="btn btn-default btn-xs">编辑</a>
                  <a href="/admin/delete.php?user_id=<?php echo $key['id'] ?>" class="btn btn-danger btn-xs">删除</a>
                </td>
              </tr>
              <?php endforeach ?>
            <?php endif ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  <?php include "inc/sidebar.php" ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
    <script>
    var $tbodycheckbox = $('tbody input');
    var $theadcheckbox = $('thead input');
    var $btnDelete = $('#btn-delect');
    var allCheckeds = [];
    $theadcheckbox.on('change', function () {
        var checked = $(this).prop('checked');
        $tbodycheckbox.prop('checked', checked).trigger('change');
      })

    $tbodycheckbox.on('change',function(){
      var id =$(this).data('id');
      if($(this).prop('checked')){
        allCheckeds.includes(id) || allCheckeds.push(id)
      }else{
        allCheckeds.splice(allCheckeds.indexOf(id),1);
      }
      if(allCheckeds.length==$tbodycheckbox.length){
        $theadcheckbox.prop('checked', true);
      }else{
        $theadcheckbox.prop('checked', false);
      }
      allCheckeds.length?$btnDelete.fadeIn():$btnDelete.fadeOut();
      $btnDelete.prop('search', '?user_id='+allCheckeds);
    });
  </script>
  <script>NProgress.done()</script>
</body>
</html>
