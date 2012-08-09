<?php
abstract class CommonController extends Light_Controller{
    public function __before(){
        $this->display('Common/header.tpl');
    }
    
    public function __after(){
        $this->display('Common/footer.tpl');
    }
}
?>