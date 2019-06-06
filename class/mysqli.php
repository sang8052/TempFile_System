
<?php
/*
ClassName: conn_mysqli
Version: 1.6
Date:2019-05-13
Author:Sudem
mysqli 的简单操作类

优化了函数执行逻辑,执行更新和插入方法可以一步直接执行
新增了判断数据是否存在的方法

*/

Class class_mysqli
{
private $br;       //换行标记
private $conf;     //配置文件
private $conn;     //数据库链接句柄
private $res;      //数据库资源句柄
public $error_mes; //错误信息


public function __construct()
{
global $Mysql_Conf;
if(is_cli()) $this->br="\n";
else $this->br="<br/>";
$this->conf=$Mysql_Conf;
$conn=mysqli_connect($this->conf['host'],$this->conf['user'],$this->conf['pass'],$this->conf['data'],$this->conf['port']);
if(!$conn)
{
echo "【ERROR】Server:".$this->conf['host']."Cannot Connect!The Response Code:".mysqli_connect_errno().$this->br;
exit();
}
else
{
if($this->conf['code']!="utf8")
mysqli_query("set character_set_client=".$this->conf["code"]);
}
$this->conn=$conn;
if(is_cli()) echo "数据库[{$this->conf['host']}]连接成功!\n";
}

public function sql_query($sql,$exit=true,$show_error=true)
// 执行sql语句
// @exit 发生错误后的处理逻辑，为真时脚本将退出运行
// @show_error 是否直接显示错误消息
{


$res=mysqli_query($this->conn,$sql);
if(!$res)
{
if($exit)
{

echo"【ERROR】SomeThing Wrong in The SQL You Input.The Error Infor:".mysqli_error($this->conn).$this->br;
exit();
}
else
{
$this->error_mes="【Warning】SomeThing Wrong in The SQL You Input.The Error Infor:".mysqli_error($this->conn).$this->br;
$file=fopen("Debug/sql_error.txt","a");
fwrite($file,$sql."\n");
fclose($file);
if($show_error)
echo "【SQL】".$sql.$this->br.$this->error_mes.$this->br;

}
}
$this->res=$res;
return $res;

}

public function res_array($mode="array",$key="",$value="")
// 从资源句柄中取得数据
// @mode array 获得一行作为关联数组，或数字数组，或二者兼有。
//       assoc 获得一行作为关联数组
//       row   获得一行作为数字数组
//       all   获得全部行数据
{
if($this->res)
{
switch($mode)
{
case "array": return mysqli_fetch_array($this->res); break;
case "assoc": return mysqli_fetch_assoc($this->res); break;
case "all": return $this->fetch_all($this->res); break;
case "all_assoc":return $this->fetch_all_assoc($this->res); break;
case "row": return mysqli_fetch_row($this->res); break;
case "key_value": return $this->key_value($this->res,$key,$value);break;
default: return "【Warning】Undefined Mode Choose".$this->br;
}
}
}

private function key_value($res,$key,$value)
{
$DATA=array();
if($res)
{
while($data=mysqli_fetch_assoc($res))
{
$DATA[$data[$key]]=$data[$value];
}
}
return $DATA;

}

public function res_rows()
//统计资源句柄中返回的数据的数量
{
if($this->res)
return mysqli_num_rows($this->res);
}

public function res_fields()
//统计行中的字段的数量
{
if($this->res)
return mysqli_num_fields($this->res);
}

public function sql_insert($table,$data)
//sql 快速插入
{

$fields=$value="";
$i=count($data);$m=0;
foreach ($data as $key=>$val)
{

$m++;
$fields=$fields.$key;
if($m!=$i) $fields=$fields.",";
$value=$value."'".$val."'";
if($m!=$i) $value=$value.",";
}

$sql="insert into ".$table." (".$fields.") values (".$value.")";
return $this->sql_query($sql,false);
}

public function sql_update($table,$data,$where)
//SQL 快速更新
{
$fields=$value="";
$i=count($data);$m=0;
foreach ($data as $key=>$val)
{
$m++;
$fields=$fields.$key."='".$val."'";
if($m!=$i) $fields=$fields.",";
}

$sql="update ".$table." set ".$fields." where ".$where;
return $this->sql_query($sql);
}


private function fetch_all($res)
{
$i=1;
while($data=mysqli_fetch_array($res))
{
$DATA[$i]=$data;
$i++;
}
$DATA[0]=$i-1;
return $DATA;
}

private function fetch_all_assoc($res)
{
$i=1;
while($data=mysqli_fetch_assoc($res))
{
$DATA[$i]=$data;
$i++;
}
$DATA[0]=$i-1;
return $DATA;
}

public function data_exist($table,$where)
{
$sql="select count(* )from $table where $where ";
$this->sql_query($sql);
$data=$this->res_array()[0];
if($data!=0)
return true;
else return false;
}
}