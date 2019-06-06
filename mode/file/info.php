<?php
$response['status']="failed";
$_GET['code']=strtoupper($_GET['code']);
$sql="select fileid from file_rand where code='{$_GET['code']}'";
$mysqli->sql_query($sql);
$fileid=$mysqli->res_array()[0];
if(!$fileid)  $response['error']="[Error]:错误指定的文件不存在或者已经删除!\nInfo:\n&nbsp;Code:".$_GET['code'];
else {

    //加载oss 配置文件
    $sql = "select * from file_upload where fileid='$fileid'";
    $mysqli->sql_query($sql);
    $res = $mysqli->res_array();
    if (!$res) $response['error']="[Error]:错误指定的文件不存在或者已经删除!\Info:\n&nbsp;Code:" . $_GET['code'] . "\&nbsp;Fileid:" . $fileid;
    else {
        $response['status']="success";
        $response['data']['filename']=$res['filename'];
        $response['data']['filesize']=FilesizeChange($res['filesize']);
        $response['data']['Uptime']=$res['Uptime'];
        $response['data']['DelTime']=$res['DelTime'];
        $response['data']['DownNum']=$res['DownNum'];
        $response['data']['url']= "http://api.iw3c.top/file.php?fun=download&code=".$_GET['code'];
    }
}
echo json_encode($response);



function FilesizeChange($size)
{
    $s = $size;$dw = "";if($s >= pow(2,40)){$dw = "TB";}elseif($s >= pow(2,30)){$dw = "GB";}elseif($s >= pow(2,20)){$dw = "MB";}elseif($s >= pow(2,10)){$dw = "KB";}elseif($s >= pow(1,10)){$dw = "types";}return $s.$dw;
}

