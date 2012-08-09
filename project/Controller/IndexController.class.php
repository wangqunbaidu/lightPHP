<?php
Light_Util::import('Controller.Common.CommonController');
//require_once( APP_PATH.'/Controller/Common/CommonController.class.php' );

class IndexController extends CommonController{
    public function index(){
        //$this->name = 'jsyczhanghao';
        //$this->assign('name', 'jsyczhanghao');
        
        require_once( APP_PATH.'/Model/ProductModel.class.php' );
        Light_Util::import('Model.ProductModel');
        
        $product = Light_Model::getModel('Product', '衣服', 200);
        
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