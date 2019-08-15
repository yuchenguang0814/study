<?php
require_once '../functions.php';
xiu_get_current_user();
function convert_date($created){
  return date('Y年m月d日 <b\r> H:i:s',strtotime($created));
}
function convert_status($status){
  $result =array(
    'published' => '已发布',
    'drafted' => '草稿',
    'trashed' => '回收站'
  );
  return isset($result[$status])?$result[$status]:'未知';
}
$categories = xiu_fetch_all("SELECT * from categories");
$where = '1 = 1';
$search = '';
if(isset($_GET['category'])&& $_GET['category']!=='all'){
$where .= ' and posts.category_id ='.$_GET['category'];
$search .= '&category=' . $_GET['category'];
}
if(isset($_GET['status'])&& $_GET['status']!=='all'){
$where .= " and posts.status = '{$_GET['status']}'";
$search .= '&status=' . $_GET['status'];

}

//渲染页面的数据获取
$page = empty($_GET['page'])?1:(int)$_GET['page'];
$size = 20;
$offset = ($page-1)*$size;
var_dump($page < 1);
if($page < 1){
  header('Location: /admin/posts.php?page=1'.$search);
}
$posts = xiu_fetch_all("SELECT
  posts.id,
  posts.title,
  posts.created,
  posts.status,
  users.nickname as user_name,
  categories.name as category_name
  From posts
  INNER JOIN users ON posts.user_id = users.id
  INNER JOIN categories ON posts.category_id = categories.id
  where {$where}
  order by posts.created desc
  limit {$offset},{$size};
  ;");
//分页功能
$total_count = (int)xiu_fetch_one("SELECT count(1) as num From posts
  INNER JOIN users ON posts.user_id = users.id
  INNER JOIN categories ON posts.category_id = categories.id where {$where};")['num'];
$total_page = (int)ceil($total_count/$size);
if($page > $total_page){
  header("Location: /admin/posts.php?page={$total_page}".$search);
}
$visiables =5;
$begin = $page - ($visiables-1)/2;
$end = $begin + ($visiables-1);
$begin = $begin<1?1:$begin;
$end = $begin + ($visiables-1);
$end = $end>$total_page?$total_page:$end;
$begin = $end - $visiables+1;
$begin = $begin < 1 ? 1 : $begin;
  ?>
  <!DOCTYPE html>
  <html lang="zh-CN">
  <head>
    <meta charset="utf-8">
    <title>Posts &laquo; Admin</title>
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
          <h1>所有文章</h1>
          <a href="post-add.php" class="btn btn-primary btn-xs">写文章</a>
        </div>
        <!-- 有错误信息时展示 -->
      <!-- <div class="alert alert-danger">
        <strong>错误！</strong>发生XXX错误
      </div> -->
      <div class="page-action">
        <!-- show when multiple checked -->
        <a class="btn btn-danger btn-sm" href="javascript:;" style="display: none">批量删除</a>
        <form class="form-inline" action="<?php echo $_SERVER['PHP_SELF']?>">
          <select name="category" class="form-control input-sm">
            <option value="all">所有分类</option>
            <?php foreach ($categories as $key): ?>
            <option value="<?php echo $key['id'] ?>" <?php echo isset($_GET['category'])&&$_GET['category']==$key['id']?'selected':'' ?>><?php echo $key['name'] ?></option>
            <?php endforeach ?>
          </select>
          <select name="status" class="form-control input-sm">
            <option value="all" >所有状态</option>
            <option value="drafted" <?php echo isset($_GET['status'])&&$_GET['status']=="drafted"?'selected':'' ?>>草稿</option>
            <option value="published" <?php echo isset($_GET['status'])&&$_GET['status']=="published"?'selected':'' ?>>已发布</option>
            <option value="trashed" <?php echo isset($_GET['status'])&&$_GET['status']=="trashed"?'selected':'' ?>>回收站</option>
          </select>
          <button class="btn btn-default btn-sm">筛选</button>
        </form>
        <ul class="pagination pagination-sm pull-right">
          <li><a href="?page=<?php echo $page==1?'1'.$search:($page-1).$search ?>">上一页</a></li>
          <?php for($i=$begin;$i<=$end;$i++) :?>
          <li class="<?php echo $i==$page?'active':'' ?>"><a href="?page=<?php echo $i .$search?>"><?php echo $i ?></a></li>
          <?php endfor ?>
          <li><a href="?page=<?php echo $page==$end?$end.$search:($page+1).$search ?>">下一页</a></li>
        </ul>
      </div>
      <table class="table table-striped table-bordered table-hover">
        <thead>
          <tr>
            <th class="text-center" width="40"><input type="checkbox"></th>
            <th>标题</th>
            <th>作者</th>
            <th>分类</th>
            <th class="text-center">发表时间</th>
            <th class="text-center">状态</th>
            <th class="text-center" width="100">操作</th>
          </tr>
        </thead>
        <tbody>

          <?php foreach ($posts as $key): ?>
            <tr>
              <td class="text-center"><input type="checkbox"></td>
              <td><?php echo $key['title']; ?></td>
              <td><?php echo $key['user_name']; ?></td>
              <td><?php echo $key['category_name']; ?></td>
              <td class="text-center">
                <?php echo  convert_date($key['created']); ?></td>
                <td class="text-center"><?php echo convert_status($key['status']); ?></td>
                <td class="text-center">
                  <a href="/admin/post-add.php?post_id=<?php echo $key['id'] ?>" class="btn btn-default btn-xs">编辑</a>
                  <a href="/admin/delete.php?post_id=<?php echo $key['id'] ?>" class="btn btn-danger btn-xs">删除</a>
                </td>
              </tr>
            <?php endforeach ?>
          </tbody>
        </table>
      </div>
    </div>
    <?php include "inc/sidebar.php" ?>
    <script src="/static/assets/vendors/jquery/jquery.js"></script>
    <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
    <script>NProgress.done()</script>
  </body>
  </html>
