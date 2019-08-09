<?php
require_once 'config.php';
session_start();
function xiu_get_current_user(){
    if(empty($_SESSION['current_login_user'])){
        header('Location: /admin/login.php');
        exit();
    }
    return $_SESSION['current_login_user'];
}
function xiu_get_connect(){
    $connect = mysqli_connect(XIU_DB_HOST,XIU_DB_UESR,XIU_DB_PASS,XIU_DB_NAME);
    if(empty($connect)){
        exit('数据库连接失败');
    }
    return $connect;
}
function xiu_get_query($connect,$sql){
    mysqli_query($connect,"SET NAMES utf8;");
    $query = mysqli_query($connect,$sql);
    if(empty($query)){
        return fasle;
    }
    return $query;
}
function xiu_fetch_all($sql){
    $connect = xiu_get_connect();
    $query = xiu_get_query($connect,$sql);
    while($row = mysqli_fetch_assoc($query)){
        $result [] = $row;
    }
    mysqli_free_result($query);
    mysqli_close($connect);
    return $result;
}
function xiu_fetch_one($sql){
    $res = xiu_fetch_all($sql);
    return isset($res[0])?$res[0]:null;
}

function xiu_execute($sql){
    $connect = xiu_get_connect();
    $query = xiu_get_query($connect,$sql);
    $affected_rows = mysqli_affected_rows($connect);
    mysqli_close($connect);
    return $affected_rows;
}



