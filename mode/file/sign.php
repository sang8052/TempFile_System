<?php
//前端文件直传 签名接口

function gmt_iso8601($time)
{
    $dtStr = date("c", $time);
    $mydatetime = new DateTime($dtStr);
    $expiration = $mydatetime->format(DateTime::ISO8601);
    $pos = strpos($expiration, '+');
    $expiration = substr($expiration, 0, $pos);
    return $expiration . "Z";
}




// 用户上传文件时指定的前缀
$dir = 'TmpFileUpload/';
// 生成上传的文件的随机密钥戳 64 位
$fileid=randstr(64);


$callback_param = array('callbackUrl' => $callbackUrl,
    'callbackhost'=>$host,
    'callbackBody' => 'filename=${object}&size=${size}&fileid='.$fileid."&code=".$_POST['code'],
    'callbackBodyType' => "application/x-www-form-urlencoded");
$callback_string = json_encode($callback_param);

$base64_callback_body = base64_encode($callback_string);
$now = time();
$expire = 30;  //设置该policy超时时间是10s. 即这个policy过了这个有效时间，将不能访问。
$end = $now + $expire;
$expiration = gmt_iso8601($end);


//最大文件大小.用户可以自己设置
//最大语序上传文件 15M
$condition = array(0 => 'content-length-range', 1 => 0, 2 => 15728640);
$conditions[] = $condition;

// 表示用户上传的数据，必须是以$dir开始，不然上传会失败，这一步不是必须项，只是为了安全起见，防止用户通过policy上传到别人的目录。
$start = array(0 => 'starts-with', 1 => '$key', 2 => $dir);
$conditions[] = $start;


$arr = array('expiration' => $expiration, 'conditions' => $conditions);
$policy = json_encode($arr);
$base64_policy = base64_encode($policy);
$string_to_sign = $base64_policy;
$signature = base64_encode(hash_hmac('sha1', $string_to_sign, $key, true));

//把OSS的加密密钥和文件信息 写入数据库
$insert['fileid']=$fileid;
$insert['object']=$dir.$_POST['filetype']."/".time()."/".$fileid.".".$_POST['filetype'];
$insert['filename']=$_POST['filename'];
$insert['filetype']=$_POST['filetype'];
$insert['DownNum']=$_POST['num'];
$insert['Sign']=$signature;

$mysqli->sql_insert("file_upload",$insert);

$response = array();
$response['fileid']=$fileid;
$response['OSSAccessKeyID'] = $id;
$response['host'] = $host;
$response['policy'] = $base64_policy;
$response['callback']=$base64_callback_body;
$response['Signature'] = $signature;
$response['dir'] = $dir;  // 这个参数是设置用户上传文件时指定的前缀。
$response['object']=$insert['object'];
$RES['status']="success";
$RES['data']=$response;
echo json_encode($RES);
