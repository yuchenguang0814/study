<?php
require_once '../functions.php';
xiu_get_current_user();
function add_post(){
//验证表单数据
if(empty($_POST['title'])){
$GLOBALS['message'] = "标题不能为空";
return;
}
if(empty($_POST['content'])){
$GLOBALS['message'] = "请填写内容";
return;
}
if(empty($_POST['slug'])){
 $GLOBALS['message'] = "请填写别名";
return;
}
if(empty($_FILES['feature']['name'])){
    $GLOBALS['message'] = "请上传图片";
    return;
}
  $ext = pathinfo($_FILES['feature']['name'], PATHINFO_EXTENSION);
  $target .= '../static/uploads/feature/feature-' . uniqid() . '.' . $ext;
  if (!move_uploaded_file($_FILES['feature']['tmp_name'], $target)) {
    $GLOBALS['message'] = '上传头像失败';
    return;
  }

if(empty($_POST['category'])){
$GLOBALS['message'] = "请选择分类";
return;
}
if(empty($_POST['created'])){
$GLOBALS['message'] = "请选择时间";
return;
 }

 $title = $_POST['title'];
 $content = $_POST['content'];
 $slug = $_POST['slug'];
 $feature =substr($target, 2);
 $GLOBALS['touxiang']=$feature;
 $created = $_POST['created'];
 $status = $_POST['status'];
 $category_id = $_POST['category'];
 $user_id = $_SESSION['current_login_user']['id'];
xiu_execute("INSERT INTO posts VALUES (null, '{$slug}','{$title}', '{$feature}', '{$created}','{$content}',0,0,'{$status}',".$user_id.",".$category_id.");");
 }

if(empty($_GET['post_id'])){
    if($_SERVER['REQUEST_METHOD']==='POST'){
        add_post();
    }
}else{
    $postvalue = xiu_fetch_one("SELECT * FROM posts where id ='{$_GET['post_id']}'");
    if($_SERVER['REQUEST_METHOD']==='POST'){
        edit_post();
    }

}


$categories = xiu_fetch_all("SELECT * FROM categories;");
 ?>


<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Add new post &laquo; Admin</title>
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
        <h1>写文章</h1>
      </div>
      <?php if (isset($message)): ?>
      <!-- 有错误信息时展示 -->
      <div class="alert alert-danger">
        <strong>错误！</strong><?php echo $message ?>
      </div>
      <?php endif ?>
      <?php if (isset($postvalue)): ?>
        <form class="row" action="<?php echo $_SERVER['PHP_SELF'] ?>?post_id=<?php echo $postvalue['id'] ?>" method="post" enctype="multipart/form-data">
        <div class="col-md-9">
          <div class="form-group">
            <label for="title">标题</label>
            <input id="title" class="form-control input-lg" name="title" type="text" placeholder="<?php echo $postvalue['title'] ?>">
          </div>
          <div class="form-group">
            <label for="content">标题</label>
            <script type="text/plain" id="content" name="content"><?php echo $postvalue['content'] ?></script>
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label for="slug">别名</label>
            <input id="slug" class="form-control" name="slug" type="text" placeholder="<?php echo $postvalue['slug'] ?>">
            <p class="help-block">https://zce.me/post/<strong>slug</strong></p>
          </div>
          <div class="form-group">
            <label for="feature">特色图像</label>
            <!-- show when image chose -->
            <img src="<?php echo $postvalue['feature'] ?>" class="help-block thumbnail" style="display: block">
            <input id="feature" class="form-control" name="feature" type="file">
          </div>
          <div class="form-group">
            <label for="category">所属分类</label>
            <select id="category" class="form-control" name="category">
            <?php foreach ($categories as $key): ?>
            <option value="<?php echo $key['id'] ?>" <?php echo $postvalue['category_id']==$key['id']?'selected':''; ?>><?php echo $key['name'] ?></option>
            <?php endforeach ?>
            </select>
          </div>
          <div class="form-group">
            <label for="created">发布时间</label>
            <input id="created" class="form-control" name="created" type="datetime-local">
          </div>
          <div class="form-group">
            <label for="status">状态</label>
            <select id="status" class="form-control" name="status">
              <option value="drafted">草稿</option>
              <option value="published">已发布</option>
            </select>
          </div>
          <div class="form-group">
            <button class="btn btn-primary" type="submit">编辑</button>
          </div>
        </div>
      </form>
      <?php else: ?>
          <form class="row" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post" enctype="multipart/form-data">
        <div class="col-md-9">
          <div class="form-group">
            <label for="title">标题</label>
            <input id="title" class="form-control input-lg" name="title" type="text" placeholder="文章标题">
          </div>
          <div class="form-group">
            <label for="content">标题</label>
            <script type="text/plain" id="content" name="content"></script>
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label for="slug">别名</label>
            <input id="slug" class="form-control" name="slug" type="text" placeholder="slug">
            <p class="help-block">https://zce.me/post/<strong>slug</strong></p>
          </div>
          <div class="form-group">
            <label for="feature">特色图像</label>
            <!-- show when image chose -->
            <img src="<?php echo $touxiang ?>" class="help-block thumbnail" style="display: none">
            <input id="feature" class="form-control" name="feature" type="file">
          </div>
          <div class="form-group">
            <label for="category">所属分类</label>
            <select id="category" class="form-control" name="category">
            <?php foreach ($categories as $key): ?>
            <option value="<?php echo $key['id'] ?>"><?php echo $key['name'] ?></option>
            <?php endforeach ?>
            </select>
          </div>
          <div class="form-group">
            <label for="created">发布时间</label>
            <input id="created" class="form-control" name="created" type="datetime-local">
          </div>
          <div class="form-group">
            <label for="status">状态</label>
            <select id="status" class="form-control" name="status">
              <option value="drafted">草稿</option>
              <option value="published">已发布</option>
            </select>
          </div>
          <div class="form-group">
            <button class="btn btn-primary" type="submit">保存</button>
          </div>
        </div>
      </form>
      <?php endif ?>
    </div>
  </div>
  <?php include "inc/sidebar.php" ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script src="/static/assets/vendors/ueditor/ueditor.config.js"></script>
  <script src="/static/assets/vendors/ueditor/ueditor.all.js"></script>
  <script>
  UE.getEditor('content',{
    initialFrameHeight:450,
    autoHeight:false
  });
  </script>
  <script>
      var d = new Date();
var datestring  = d.getFullYear() + "-" +  ("0"+(d.getMonth()+1)).slice(-2)  + "-"  + ("0" + d.getDate()).slice(-2)
                  + "T" + ("0" + d.getHours()).slice(-2) + ":" + ("0" + d.getMinutes()).slice(-2);
$('#created').val(datestring);
  </script>
  <script>NProgress.done()</script>
</body>
</html>
