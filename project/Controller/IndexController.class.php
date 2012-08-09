<?php
import('Controller.Common.CommonController');
//require_once( APP_PATH.'/Controller/Common/CommonController.class.php' );

class IndexController extends CommonController{
    public function index(){
        //$this->name = 'jsyczhanghao';
        //$this->assign('name', 'jsyczhanghao');
        
        //require_once( APP_PATH.'/Model/ProductModel.class.php' );
        //import('Model.ProductModel');
        
        //$product = Light_Model::getModel('Product', '衣服', 200);
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
    
    public function phpmarkdown(){
        $string = "
##title
###title
[baidu.com](http://www.baidu.com])

##[title](http://www.baidu.com)
        ";
        
        require_once( APP_PATH.'/Common/lib/php-markdown/markdown.php' );
        echo "
            <style>
                h2{
                    font-size: 30px;
                    color: red;
                }
            </style>
        ";
        echo Markdown( $string );
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