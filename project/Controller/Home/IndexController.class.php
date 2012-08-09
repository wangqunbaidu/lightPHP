<?php
require_once( APP_PATH.'/Controller/Common/CommonController.class.php' );

class IndexController extends CommonController{
    public function index(){
        $this->name = 'jsyczhanghao';
        //$this->assign('name', 'jsyczhanghao');
        $this->display('Home/Index/index.tpl');
        $this->display('Home/Index/index.tpl');
    }
}
?>