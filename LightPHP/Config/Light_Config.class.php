<?php
/**
 *  配置操作类
 *  支持php array和ini文件
 */
class Light_Config{
    private static $config;
    
    /**
     * 获取一个配置节点
     *
     * @param unknown_type $section
     * @param unknown_type $name
     * @return unknown
     */
    public static function get( $section = null, $name = null ){
        return self::$config->get( $section, $name );
    }   
    
    /**
     *  工厂方法 获取配置实例
     *
     * @param unknown_type $path
     * @return unknown
     */
    public static function load( $path ){
        //获取文件后缀 
        $info = new Light_File_Info( $path );
        
        $type = $info->getExtension( $path );

        switch ( $type ){
            case 'php':
                require_once( FRAMEWORK_PATH.'/Config/Light_Config_Array.class.php' );
                self::$config = new Light_Config_Array( $path );
                break;
            //some type case
            //
            default:
                require_once( FRAMEWORK_PATH.'/Config/Light_Config_INI.class.php' );
                self::$config = new Light_Config_INI( $path );
                break;
        };
        
        return self::$config;
    }
}
?>