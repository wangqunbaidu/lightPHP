<?php
require_once('../FIS/FISFramework.php');
require_once('Common/lib/smarty-3.1.5/Smarty.class.php');
date_default_timezone_set('Asia/Shanghai');

$smarty = new Smarty();

FISView::setViewEngine($smarty);

FISView::setViewRoot(dirname(__FILE__).'/View');

$model = new FISModel();

$model->name = 'jfsdf';

$menu = new FISView('menu', $model, 'menu');


$layout = new FISView('layout');

$layout->append( $menu );

FISView::display();
?>