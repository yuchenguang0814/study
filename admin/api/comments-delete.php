<?php
require_once '../../functions.php';
if(empty($_GET['comments_id'])){
    exit('缺少必要参数');
}
$comments_id =$_GET['comments_id'];
$rows =xiu_execute("delete from comments WHERE id in(".$_GET['comments_id'].");");
if($rows<=0){
    exit('数据删除失败');
}
header('Content-Type: application/json');
echo json_encode($rows>0);