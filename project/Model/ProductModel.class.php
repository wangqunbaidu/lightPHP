<?php
/**
 * 一个简单的model例子 暂无model驱动扩展
 * 商品类
 *
 */
class ProductModel{
    //存在商品实例
    private static $data = array();
    
    //商品的id
    private $id;
    //商品的名字
    public $name;
    //价格
    public $price;
    
    public function __construct( $name, $price = 200 ){
        $this->name = $name;
        $this->price = $price;
        
        $this->id = count(self::$data);
    }
    
    //保存
    public function save(){
        self::$data[] = $this;
    }
    
    //获取所有商品
    public static function getProductList(){
        return self::$data;
    }
}
?>