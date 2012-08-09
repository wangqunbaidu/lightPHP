<?php
/**
 * 前端控制器 类
 * 单例
 * 用于分发路由
 */

class Light_Controller_Front{
    //instance
    private static $instance;
    
    //系统配置
    private $config = array();
    
    private function __construct(){
        //首先加载配置
        $this->setConfig( FRAMEWORK_PATH.'/Config/config.ini' );
    }
    
    private function __clone(){}
    
    public function getInstance(){
        if ( !self::$instance ) self::$instance = new self;
        
        return self::$instance;
    }
    
    /**
     * 设置控制器路径
     *
     * @param unknown_type $path
     */
    public function setControllerDirectory( $path ){
        Light_Register::set( 'controllerDirectory', rtrim( $path, '/' ) . '/' );
    }
    
    /**
     * 设置模型路径
     *
     * @param unknown_type $path
     */
    public function setModelDirectory( $path ){
        Light_Register::set( 'modelDirectory', rtrim( $path, '/' ) . '/' );
    }
    
    /**
     * 设置视图路径
     *
     * @param unknown_type $path
     */
    public function setViewDirectory( $path ){
        Light_Register::set( 'viewDirectory', rtrim( $path, '/' ) . '/' );
    }
    
    /**
     * 设置配置文件 加载配置 并重写 之前加载的配置
     *
     * @param unknown_type $path
     */
    public function setConfig( $path ){
        $config = Light_Config_Factory::factory( $path );

        $this->config = Light_Util::arrayMerge( $this->config, $config->get() );
    }
    
    /**
     * 获取路由信息
     *
     */
    private function getRouter(){
        $uri = preg_replace( "/\?.*/", "", $_SERVER['REQUEST_URI'] );
        
        $uri = explode('/', trim( $uri, '/' ) );
        
        $routerInfo = array();
        
        //检查第一个串是否为一个分组
        if ( $this->checkIsGroup( $uri[0] ) ) {
            //如果是则 该参数做为分组
        	$routerInfo['group'] = $uri[0];
        	array_shift( $uri );
        } else {
            //否则取默认分组
            $routerInfo['group'] = $this->getDefaultGroup();
        }
        
        //设置controller 和 action
        $routerInfo['controller'] = $uri[0] ? $uri[0] . 'Controller' : 'IndexController';
        $routerInfo['action'] = $uri[1] ? $uri[1] : 'index';
        
        //设置get
        for ( $i = 2, $j = count($uri); $i < $j; $i++ ) {
            $_GET[$uri[$i]] = $uri[++$i]; 
        }
        
        
        return $routerInfo;
    }
    
    /**
     * 检查 uri的第一个串是否是一个分组
     *
     * @param unknown_type $group
     * @return unknown
     */
    private function checkIsGroup( $group = '' ){
        $grouplist = $this->config['Framework']['group'];
        
        //如果没有分组 则直接返回
        if ( !$grouplist ) return false;
        
        $grouplist = explode(',', $grouplist);
        
        return in_array( $group, $grouplist );
    }
    
    /**
     *  获取默认的分组
     *
     * @return unknown 有可能不存在分组 则返回 空
     */
    private function getDefaultGroup(){
        return $this->config['Framework']['defaultGroup'];
    }
    
    /**
     *  分发路由
     *
     */
    public function dispatch(){
        //获取路由信息
        //包括 分组 控制器 和 动作
        $router = $this->getRouter();

        //加载控制器文件 加载成功则执行 否则 执行empty操作
        $this->loadController( $router ) ? $this->exec( $router ) : $this->execEmpty( $router );
        
        //执行控制器操作
    }
    
    /**
     * 加载控制器文件
     *
     * @param unknown_type $router
     */
    private function loadController( $router ){
        $path = Light_Register::get('controllerDirectory') . ( $router['group'] ?  $router['group'] . '/' : '' ) . $router['controller'] . '.class.php';

        return @include_once( $path );
    }
    
    /**
     * 执行控制器
     *
     * @param unknown_type $router
     */
    private function exec( $router ){
        //反射该类
        $class = new ReflectionClass( $router['controller'] );

        //如果该类继承于controller类
        if ( $class->isSubclassOf( 'Light_Controller' ) ) {

            //检查它的是否拥有方法 action
        	if ( $class->hasMethod( $router['action'] ) ) {
        	    
        	    //如果有 则反射该动作
        		$method = $class->getMethod( $router['action'] );
                
        		//调用
        		$method->isAbstract() ? $method->invoke() : $method->invoke( $class->newInstance( $router['controller'], $router['action'], $router['group'] ) );
        		
        	} else {
        	    //执行empty魔术函数
        	    $method = $class->getMethod('__empty');
        	    
        	    $method->invoke( $class->newInstance( $router['controller'], $router['action'], $router['group'] ) );
        	    
        	}
        }
    }
    
    /**
     *  当找不到控制器时 执行emptycontroller
     *
     * @param unknown_type $router
     */
    private function execEmpty( $router = array() ){
        $temp = array_slice( $router, 0 );
        
        $temp['controller'] = 'EmptyController';
        
        if ( !$this->loadController( $temp ) ) {
            
        	Light_Exception::error( "无法加载控制器{$router['controller']}" );
        } else {
            //执行empty controller
            new $temp['controller']( $router['controller'], $router['action'], $router['group'] );   
        }
    }
}
?>