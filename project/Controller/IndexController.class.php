<?php
require_once( APP_PATH.'/Controller/Common/CommonController.class.php' );

class IndexController extends CommonController{
    public function index(){
        //$this->name = 'jsyczhanghao';
        //$this->assign('name', 'jsyczhanghao');
        
        require_once( APP_PATH.'/Model/ProductModel.class.php' );
        
        $product = new ProductModel('衣服', 200);
        
        $product->save();
        
        $product = new ProductModel('裤子', 200);
        
        $product->save();
        
        //$this->assign( 'product', ProductModel::getProductList() );
        $this->product = ProductModel::getProductList();
        
        $this->display('Home/Index/index.tpl');
        
        //$this->redirect('http://www.baidu.com');
        //$this->display('Home/Index/index.tpl');
    }
    
    /**
     * 找不到action的时候执行
     *
     */
    public function __empty(){
        echo $this->action;
    }
    
    public function add(){
        echo 'this is add action';
    }
}
?>