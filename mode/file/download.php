<?php
$sql="select fileid from file_rand where code='{$_GET['code']}'";
$mysqli->sql_query($sql);
$fileid=$mysqli->res_array()[0];
if(!$fileid)  echo "[Error]:错误指定的文件不存在或者已经删除!<br/>Info:<br/>&nbsp;Code:".$_GET['code'];
else {

    //加载oss 配置文件
    $sql = "select object,DownNum from file_upload where fileid='$fileid'";
    $mysqli->sql_query($sql);
    $res = $mysqli->res_array();
    if (!$res) echo "[Error]:错误指定的文件不存在或者已经删除!<br/>Info:<br/>&nbsp;Code:" . $_GET['code'] . "<br/>&nbsp;Fileid:" . $fileid;
    else {
        if ($res['DownNum'] == 1) // 执行删除文件操作
        {
            $sql = "update file_rand set status='0' where code='{$_GET['code']}'";
            $mysqli->sql_query($sql);
            $sql = "delete from file_upload  where fileid='$fileid'";
            $mysqli->sql_query($sql);
        } else {
            $update['DownNum'] = $res['DownNum'] - 1;
            $mysqli->sql_update("file_upload", $update, "fileid='$fileid'");

        }
        $domain = "https://iw3c-nuaa.oss-cn-shanghai.aliyuncs.com/";
        $expire = time() + 15;
        $StringToSign = "GET\n\n\n" . $expire . "\n/" . $bucket . "/" . $res['object'];
        $Sign = base64_encode(hash_hmac("sha1", $StringToSign, $accessKeySecret, true));
        $url = $domain . urlencode($res['object']) . "?OSSAccessKeyId=" . $accessKeyId . "&Expires=" . $expire . "&Signature=" . urlencode($Sign);
        header("location:" . $url);
    }
}
