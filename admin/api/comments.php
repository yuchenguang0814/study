<?php
require_once '../../functions.php';
$comments = xiu_fetch_all("SELECT * FROM comments");
echo json_encode($comments);