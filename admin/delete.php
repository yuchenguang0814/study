<?php
require_once '../functions.php';
if(empty($_GET['id'])&&empty($_GET['user_id'])&&empty($_GET['post_id'])&&empty($_GET['comments_id'])){
exit('缺少必要参数');
}
if(isset($_GET['id'])){
$rows =xiu_execute("delete from categories WHERE id in(".$_GET['id'].");");
if($rows<=0){
    exit('数据删除失败');
}
header('Location:/admin/categories.php');
}
if(isset($_GET['user_id'])){
$rows =xiu_execute("delete from users WHERE id in(".$_GET['user_id'].");");
if($rows<=0){
    exit('数据删除失败');
}
header('Location:/admin/users.php');
}
if(isset($_GET['post_id'])){
$rows =xiu_execute("delete from posts WHERE id in(".$_GET['post_id'].");");
if($rows<=0){
    exit('数据删除失败');
}
header('Location:'.$_SERVER['HTTP_REFERER']);
}
