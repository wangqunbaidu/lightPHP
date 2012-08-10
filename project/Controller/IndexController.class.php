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

    public function modelTest(){
        $model = new Light_Model('test');

        //$n = new Light_Db_Mysql('localhost', 'root', '', 'zhanghao');

        //$n->setReadOnlyUser('localhost', 'zhanghao', '', 'zhanghao');

        //var_dump($n->query('insert into test values(null, 123)'));
        //
        //var_dump($n->query('select * from test'));
        //
        //var_dump($n->query('update test set name=23'));
        //
        //var_dump($n->query('select * from test'));
        //$n->selectTable('test');

        $model->where(array(
                       'name' => 'jsyzhanghao',
                       'age < 11 AND age > 20',
                       'city' => array('like', '%"city%'),
                       'a' => array('lt', 22)
                   ),
        
                   'sex = "m"')->field('name')->distinct('id')->group('name')->order('id desc')->limit()->page(3)->findAll();
        
                   
        
        //echo $n->getLastSql();
        //$result = $n->field('id')->distinct('name')->find();
        //$result = $n->delete();
        //$result = $model->findAll();
        //$n->insert(array('name' => '123'));
        //var_dump($result);
        
        $this->assign('data', $result);
        
        $this->display('Home/Index/index.tpl');
        //var_dump($result);
    }
    
    public function test(){
        import('Model.TestModel');
        
        $model = new TestModel();
        
        var_dump($model->getAll());
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