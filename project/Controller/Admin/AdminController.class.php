<?php
class AdminController extends Controller{
    public function index(){
        echo 123;
    }
    
    public function s(){
        $this->index();
        var_dump($_GET);
    }
}
?>