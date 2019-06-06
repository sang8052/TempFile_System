<?php

$_POST['code']=strtoupper($_POST['code']);
$sql="select status from file_rand where code='{$_POST['code']}'";
$mysqli->sql_query($sql);
$status=$mysqli->res_array()[0];
if($status==""||$status==0)
{
    $response['status']="success";
    if($status=="")
    {
        $insert['code']=$_POST['code'];
        $insert['fileid']=$_POST['fileid'];
        $insert['status']=1;
        $mysqli->sql_insert("file_rand",$insert);
    }
    else
    {
        $update['status']=1;
        $update['fileid']=$_POST['fileid'];
        $mysqli->sql_update("file_rand",$update,"code='{$_POST['code']}'");
    }
    $update=array();
    $update['UpTime']=date("Y-m-d H:i:s");
    $update['DelTime']=date("Y-m-d H:i:s",time()+3600*72);
    $update['filesize']=$_POST['size'];
    $update['callback']=1;
    $mysqli->sql_update("file_upload",$update,"fileid='{$_POST['fileid']}'");
    $response['status']="success";
    $response['url']="http://api.iw3c.top/file.php?fun=download&code=".$_POST['code'];
    $response['DelTime']= $update['DelTime'];
}
else $response['status']="fail";
echo json_encode($response);