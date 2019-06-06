<?php
if($_POST['code']==""||!isset($_POST['code']))
{
    // 随机抽取一个新的字符串作为文件标识
    $sql="SELECT * FROM `file_rand` WHERE  status=0 and Id >= (SELECT floor( RAND() * ((SELECT MAX(Id) FROM `file_rand`)-(SELECT MIN(Id) FROM `file_rand`)) + (SELECT MIN(Id) FROM `file_rand`)))   ORDER BY Id LIMIT 1";
    $mysqli->sql_query($sql);
    $res=$mysqli->res_array();
    $response['status']="free";
    $response['code']=strtoupper($res['code']);
}
else
{
    $_POST['code']=strtoupper($_POST['code']);
    if($mysqli->data_exsit("api_iw3c","code={$_POST['code']} and status=0"))
        $response['status']="used";
    else   $response['status']="free";
    $response['code']=$_POST['code'];
}
echo json_encode($response);


