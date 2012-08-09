<?php
/**
 * 该类可缓存model等全局信息
 */
class Register{
    private static $data = array();
    
    /**
     * 设置一个全局数据
     *
     * @param unknown_type $name
     * @param unknown_type $value
     */
    public static function set( $name, $value = '' ){
        self::$data[$name] = $value;
    }
    
    /**
     * 获取一个全局数据
     *
     * @param unknown_type $name
     * @return unknown
     */
    public static function get( $name ){
        return self::$data[$name];
    }
}
?>