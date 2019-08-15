<?php
require_once '../functions.php';
xiu_get_current_user();
 ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Comments &laquo; Admin</title>
  <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/static/assets/vendors/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" href="/static/assets/vendors/nprogress/nprogress.css">
  <link rel="stylesheet" href="/static/assets/css/admin.css">
  <script src="/static/assets/vendors/nprogress/nprogress.js"></script>
  <style type="text/css">
#loading{
    position: fixed;
    z-index: 999;
    bottom: 0;
    top: 0;
    right: 0;
    left: 0;
    display: flex;
    background-color: rgba(0,0,0,.7);
    align-items: center;
    justify-content: center;
}
.lds-roller {
  display: inline-block;
  position: relative;
  width: 64px;
  height: 64px;
}
.lds-roller div {
  animation: lds-roller 1.2s cubic-bezier(0.5, 0, 0.5, 1) infinite;
  transform-origin: 32px 32px;
}
.lds-roller div:after {
  content: " ";
  display: block;
  position: absolute;
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background: #fff;
  margin: -3px 0 0 -3px;
}
.lds-roller div:nth-child(1) {
  animation-delay: -0.036s;
}
.lds-roller div:nth-child(1):after {
  top: 50px;
  left: 50px;
}
.lds-roller div:nth-child(2) {
  animation-delay: -0.072s;
}
.lds-roller div:nth-child(2):after {
  top: 54px;
  left: 45px;
}
.lds-roller div:nth-child(3) {
  animation-delay: -0.108s;
}
.lds-roller div:nth-child(3):after {
  top: 57px;
  left: 39px;
}
.lds-roller div:nth-child(4) {
  animation-delay: -0.144s;
}
.lds-roller div:nth-child(4):after {
  top: 58px;
  left: 32px;
}
.lds-roller div:nth-child(5) {
  animation-delay: -0.18s;
}
.lds-roller div:nth-child(5):after {
  top: 57px;
  left: 25px;
}
.lds-roller div:nth-child(6) {
  animation-delay: -0.216s;
}
.lds-roller div:nth-child(6):after {
  top: 54px;
  left: 19px;
}
.lds-roller div:nth-child(7) {
  animation-delay: -0.252s;
}
.lds-roller div:nth-child(7):after {
  top: 50px;
  left: 14px;
}
.lds-roller div:nth-child(8) {
  animation-delay: -0.288s;
}
.lds-roller div:nth-child(8):after {
  top: 45px;
  left: 10px;
}
@keyframes lds-roller {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
  }
}

  </style>
</head>
<body>
  <script>NProgress.start()</script>
  <div id="loading">
    <div class="lds-roller"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
  </div>
  <div class="main">
<?php include "inc/navbar.php" ?>
    <div class="container-fluid">
      <div class="page-title">
        <h1>所有评论</h1>
      </div>
      <!-- 有错误信息时展示 -->
      <!-- <div class="alert alert-danger">
        <strong>错误！</strong>发生XXX错误
      </div> -->
      <div class="page-action">
        <!-- show when multiple checked -->
        <div class="btn-batch" style="display: none">
          <button class="btn btn-info btn-sm">批量批准</button>
          <button class="btn btn-warning btn-sm">批量拒绝</button>
          <button class="btn btn-danger btn-sm">批量删除</button>
        </div>
        <ul class="pagination pagination-sm pull-right">
        </ul>
      </div>
      <table class="table table-striped table-bordered table-hover">
        <thead>
          <tr>
            <th class="text-center" width="40"><input type="checkbox"></th>
            <th width="50">作者</th>
            <th>评论</th>
            <th width="150" style="text-align: center;">评论在</th>
            <th width="100">提交于</th>
            <th width="80">状态</th>
            <th class="text-center" width="100">操作</th>
          </tr>
        </thead>
        <tbody>
        </tbody>
      </table>
    </div>
  </div>
<?php include "inc/sidebar.php" ?>
  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script src="/static/assets/vendors/jsrender/jsrender.js"></script>
  <script src="/static/assets/vendors/twbs-pagination/jquery.twbsPagination.js"></script>
  <script id="comment_tmpl" type="text/x-jsrender">
    {{for comments}}
    <tr {{if status =="held"}} class="warning"{{else status =="rejected"}} class="danger"{{else}} class="info"{{/if}} data-id="{{:id}}">
            <td class="text-center"><input type="checkbox"></td>
            <td>{{:author}}</td>
            <td>{{:content}}</td>
            <td style="text-align: center;"><<{{:post_title}}>></td>
            <td>{{:created}}</td>
            <td>{{if status =="held"}}待审 {{else status =="rejected"}}拒绝{{else}} 批准{{/if}}</td>
            <td class="text-center">
             {{if status =="held"}}<a href="post-add.php" class="btn btn-info btn-xs">批准</a>{{/if}}
              <a href="javascript:;" id="btn-delete" class="btn btn-danger btn-xs">删除</a>
            </td>
          </tr>
    {{/for}}
  </script>
  <script>
  $(document)
  .ajaxStart(function (){
    NProgress.start();
    $('#loading').fadeIn();
  })
  .ajaxStop(function (){
    NProgress.done();
      $('#loading').fadeOut();
  });


  var current_Page=1;
  function loadPageData(page){
    $('tbody').fadeOut();
    $.getJSON("/admin/api/comments.php",{page:page},function(res){
      if(page>res.total_pages){
        loadPageData(res.total_pages);
        return false;
      }
      $('.pagination').twbsPagination('destroy');
      $('.pagination').twbsPagination({
      first:'首页',
      last:'尾页',
      prev:'上一页',
      next:'下一页',
      startPage:page,
      totalPages:res.total_pages,
      visiablePages:5,
      initiateStartPageClick:false,
      onPageClick:function(e,page){
        loadPageData(page);
      }
    });
      var html = $('#comment_tmpl').render({comments: res.comments});
      $('tbody').html(html).fadeIn();
      current_Page = page;
    });
  }
loadPageData(current_Page);
  $('tbody').on('click','#btn-delete',function(){
    var comments_id =$(this).parent().parent().data('id');
    $.getJSON('/admin/api/comments-delete.php',{comments_id:comments_id},function(res){
      console.log(res);
      if(!res) return;
       loadPageData(current_Page);
    });
  });

  </script>
  <script>NProgress.done()</script>
</body>
</html>
