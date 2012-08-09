<?php
/**
 *  配置操作工厂类
 *  只支持ini目前
 */
class ConfigFactory{    
    /**
     *  工厂方法 获取配置实例
     *
     * @param unknown_type $path
     * @return unknown
     */
    public static function factory( $path ){
        //获取文件后缀 
        $info = new FileInfo( $path );
        
        $type = $info->getExtension( $path );

        switch ( $type ){
            //some type case
            //
            default:
                require_once( FRAMEWORK_PATH.'/Config/INIConfig.class.php' );
                $config = new INIConfig( $path );
                break;
        };

        return $config;
    }
}
?>