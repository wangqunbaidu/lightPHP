<?php
require_once( FRAMEWORK_PATH.'/Config/Light_Config.class.php' );

/**
 *  ini配置文件类
 *
 */
class Light_Config_INI extends Light_Config {
    protected function load(){
        $this->config = parse_ini_file( $this->path, true );
    }
}
?>