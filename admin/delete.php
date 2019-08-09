<?php
require_once '../functions.php';
;
if(empty($_GET['id'])&&empty($_GET['user_id'])&&empty($_GET['post_id'])){
exit('缺少必要参数');
}
$id=$_GET['id'];
$user_id=$_GET['user_id'];
$post_id=$_GET['post_id'];
if(isset($id)){
$rows =xiu_execute("delete from categories WHERE id in(".$id.");");
if($rows<=0){
    exit('数据删除失败');
}
header('Location:/admin/categories.php');
}
if(isset($user_id)){
$rows =xiu_execute("delete from users WHERE id in(".$user_id.");");
if($rows<=0){
    exit('数据删除失败');
}
header('Location:/admin/users.php');
}
if(isset($post_id)){
$rows =xiu_execute("delete from posts WHERE id in(".$post_id.");");
if($rows<=0){
    exit('数据删除失败');
}
header('Location:'.$_SERVER['HTTP_REFERER']);
}