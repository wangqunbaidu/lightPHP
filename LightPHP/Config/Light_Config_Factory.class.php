<?php
/**
 *  配置操作工厂类
 *  只支持ini目前
 */
class Light_Config_Factory{    
    /**
     *  工厂方法 获取配置实例
     *
     * @param unknown_type $path
     * @return unknown
     */
    public static function factory( $path ){
        //获取文件后缀 
        $info = new Light_File_Info( $path );
        
        $type = $info->getExtension( $path );

        switch ( $type ){
            //some type case
            //
            default:
                require_once( FRAMEWORK_PATH.'/Config/Light_Config_INI.class.php' );
                $config = new Light_Config_INI( $path );
                break;
        };

        return $config;
    }
}
?>