<?php
include_once "conf.php";
include_once "class/mysqli.php";
include_once "function/is_cli.php";
include_once "function/getip.php";
include_once "function/randstr.php";
$mysqli=new class_mysqli();
$ip=getip(); 
if(!isset($_GET['fun']))
       include_once "mode/file/index.html";
else
{
  if($_GET['fun']=="callback")        include_once "mode/file/callback.php";     //  OSS 回调接口
  else if($_GET['fun']=="upload")     include_once "mode/file/upload.php";       //  测试POST上传接口 无实际价值
  else if($_GET['fun']=="code")       include_once "mode/file/code.php";         //  文件代码查询接口
  else if($_GET['fun']=="down")       include_once "mode/file/down.html";        //  文件下载界面
  else if($_GET['fun']=="download")   include_once "mode/file/download.php";     //  文件下载接口
  else if($_GET['fun']=="sign")       include_once "mode/file/sign.php";         //  文件上传签名接口
  else if($_GET['fun']=="info")       include_once "mode/file/info.php";         //  文件数据接口
  else if($_GET['fun']=="data")       include_once "mode/file/data.php";         //  数据清理接口 需要传递加密效验参数
}