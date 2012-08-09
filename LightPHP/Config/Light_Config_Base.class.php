<?php
/**
 * config基类 为各中配置文件格式提供基本方法
 * config为系统提供, 只读
 *
 */
abstract class Light_Config_Base{
    //path
    protected $path;
    
    //已取出的config 应为一个数组
    protected $config = array();
    
    public function __construct( $path = '' ){
        $this->setConfig( $path );
    }
    
    /**
     * 设置config的路径 设置完自动加载
     *
     * @param unknown_type $path
     */
    public function setConfig( $path = '' ){
        $this->path = $path;
        $this->load();
    }
    
    /**
     * 子类实现load方法 处理加载配置的逻辑
     *
     */
    abstract protected function load();
    
    /**
     * 通用获取配置的节点信息
     *
     * @param unknown_type $section
     * @param unknown_type $name
     * @return unknown
     */
    public function get( $section = null, $name = null ){
        if( !$section ) return $this->config;
        
        $section = $this->config[$section];
        
        return $name ? $section[$name] : $section;
    }
}
?>