<?php
require_once( FRAMEWORK_PATH.'/Config/Light_Config.class.php' );

/**
 *  array配置文件类
 *
 */
class Light_Config_Array extends Light_Config {
    protected function load(){
        $this->config = require( $this->path );
    }
}
?>