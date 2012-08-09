<?php
require_once( FRAMEWORK_PATH.'/Config/Light_Config_Base.class.php' );

/**
 *  array配置文件类
 *
 */
class Light_Config_Array extends Light_Config_Base {
    protected function load(){
        $this->config = require( $this->path );
    }
}
?>