<?php
/**
 * 控制器主类
 * 抽象, 提供所有控制器的基本方法 如调用view等
 */
abstract class Light_Controller {
    private static $CONTROLLER_DIRECTORY;
    
    private $view;
    protected $controller;
    protected $action;
    protected $group;
    
    public function __construct( $controller, $action, $group = '' ){
        $this->controller = $controller;
        $this->action = $action;
        $this->group = $group;
        
        //获取view引擎 可能是第3方的也说不定 HOHOHO...
        $this->view = Light_View::getEngine();

        if ( !$this->view->template_dir ) {
        	$this->view->template_dir = Light_View::getDirectory();
        }
        
        $this->__before();
    }
    
    public function __set( $name, $value = '' ){
        $this->view->assign( $name, $value );
    }
    
    /**
     * 同view方法 设置模板变量值
     *
     * @param unknown_type $name
     * @param unknown_type $value
     */
    protected function assign( $name, $value = '' ){
        $this->view->assign( $name, $value );
    }
    
    /**
     * 同view方法 输出模板
     *
     * @param unknown_type $tpl
     * @param unknown_type $charset
     * @param unknown_type $type
     */
    protected function display( $tpl, $charset = 'utf-8', $type = 'text/html' ){
        $this->view->display( $tpl, $charset, $type );
    }
    
    /**
     * 重定向
     *
     * @param unknown_type $url
     */
    protected function redirect( $url = '' ){
        echo "<script>location.href='$url';</script>";
    }
    
    /**
     * empty魔术函数 用于找不到action时执行
     *
     */
    public function __empty(){
        Light_Exception::error( "控制器{$this->controller}中不存在{$this->action}方法" );
    }
    
    /**
     * before魔术函数 用于执行action之前执行
     *
     */
    protected function __before(){}
    
    /**
     * after魔术函数 用于执行action之后执行
     *
     */
    protected function __after(){}
    
    public function __destruct(){
        //最后执行after魔术函数
        $this->__after();
    }
    
    public static function setDirectory( $path ){
        self::$CONTROLLER_DIRECTORY = rtrim( $path, '/' ) . '/';
    }   
    
    public static function getDirectory(){
        return self::$CONTROLLER_DIRECTORY;
    }
}
?>