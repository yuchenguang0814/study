<?php
require_once '../../functions.php';
$size =30;
$page =empty($_GET["page"])?1:intval($_GET["page"]);
$offset = ($page-1)*$size;
$sql=sprintf("SELECT
    comments.*,
    posts.title as post_title
    FROM comments
    inner join posts on comments.post_id = posts.id
    order by comments.created desc
    limit %d,%d;",$offset,$size);
$comments = xiu_fetch_all($sql);
$toatl_comment =xiu_fetch_one("SELECT count(1) as count From comments
    inner join posts on comments.post_id = posts.id;")['count'];
$total_pages = ceil($toatl_comment/$size);
$json = json_encode(array(
'total_pages'=> $total_pages,
'comments' => $comments
));
header('Content-Type: application/json');
echo $json;