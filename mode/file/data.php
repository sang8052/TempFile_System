<?php
if($_GET['token']=="H8EloTODrlaOxnrqBMnoQ29PIWFKQjEx")
{
    if($_GET['action']=="up")
    {
        $sql="select code,Id from file_rand";
        $res=$mysqli->sql_query($sql);
        $codes=$mysqli->res_array("all");
        for($i=1;$i<=$codes[0];$i++)
        {
            $update['code']=strtoupper($codes[$i]['code']);
            $mysqli->sql_update("file_rand",$update,"Id='{$codes[$i]['Id']}'");
        }
    }
    else
    {
        // 删除 回调出错的数据
        //$sql="delete * from   file_upload where callback!='1'";
        //$mysqli->sql_query($sql);
        // 删除 超过最大保存时间 的数据
        $sql="select DelTime,Id from file_upload where callback='1'";
        $res=$mysqli->sql_query($sql);
        $files=$mysqli->res_array("all");
        for($i=1;$i<$files[0];$i++)
        {
            if(strtotime($files[$i]['DelTime'])<=time())
            {
                $sql="delete from file_upload where Id='{$files[$i]['Id']}'";
                $res=$mysqli->sql_query($sql);
                $sql="update file_rand set status=0 where fileid='{$files[$i]['fileid']}'";
                $res=$mysqli->sql_query($sql);
            }
        }
    }
    $response['status']="success";
}
else
{
    $response['status']="failed";
    $response['msg']="Auth Token Error!";
}
echo json_encode($response);