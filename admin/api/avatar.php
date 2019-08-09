<?php
require_once '../../config.php';
require_once '../../functions.php';
$email = $_GET['email'];
if(empty($email)){
    exit('缺少必要的参数');
}
$sql = "SELECT avatar FROM users where email = '{$email}' limit 1;";
$row = xiu_fetch_one($sql);
echo $row['avatar'];
