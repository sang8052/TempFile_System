<?php
/*
Version:1.0
Data:2019-04-15
Author:Sudem

判断当前PHP脚本是否在cli 模式下运行
*/
function is_cli()
{
     return preg_match("/cli/i", php_sapi_name()) ? true : false;
}