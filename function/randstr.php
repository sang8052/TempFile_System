<?php
/*
Version:1.0
Data:2019-04-15
Author:Sudem

生成随机字符串


@$length (可选) 随机字符的长度，默认为32
*/
function randstr($length=32)
{
	 $str = null;
     $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
     $max = strlen($strPol)-1;
     
      for($i=0;$i<$length;$i++)
      {
        $str.=$strPol[rand(0,$max)];
      }
      return $str;
}
?>