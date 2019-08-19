<?php
$avatar = $_FILES['avatar'];
if(empty($avatar)){
    exit('必须上传文件');
}
if($avatar['error']!==UPLOAD_ERR_OK){
     exit('上传文件失败');
}
$ext = pathinfo($avatar['name'],PATHINFO_EXTENSION);
$target = '../../static/uploads/feature/img-'.uniqid().'.'.$ext;

if(!move_uploaded_file($avatar['tmp_name'], $target)){
     exit('上传文件失败');
}
echo substr($target,5);

//返回
