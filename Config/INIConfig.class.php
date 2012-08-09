<?php
require_once( FRAMEWORK_PATH.'/Config/Config.class.php' );

/**
 *  ini配置文件类
 *
 */
class INIConfig extends Config {
    protected function load(){
        $this->config = parse_ini_file( $this->path, true );
    }
}
?>