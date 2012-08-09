<?php
//测试版本
define( 'APP_PATH', dirname(__FILE__) );

//可不设置 系统框架回自动设置
define( 'FRAMEWORK_PATH', APP_PATH.'/../lightPHP' );

//加载框架
require_once( FRAMEWORK_PATH.'/Framework.php' );

//框架获取前端控制器实例
$framework = ControllerFront::getInstance();

//设置控制器路径
$framework->setControllerDirectory( APP_PATH.'/Controller' );

//设置view路径 如使用第3方模板引擎 则直接在第3方模板引擎上设置即可
$framework->setViewDirectory( APP_PATH.'/View' );

/*
//设置第3方模板引擎 smarty
require_once( APP_PATH.'/Common/lib/smarty-3.1.5/Smarty.class.php' );

$smarty = new Smarty();

$smarty->template_dir = APP_PATH.'/View';

View::setEngine($smarty);
*/

//设置配置路径 如没配置 则会自动加载系统配置 配置文件类型暂时只能为ini 可扩展php-array类型 xml等
$framework->setConfig( APP_PATH.'/Common/config/config.ini' );

//分发路由
$framework->dispatch();
?>