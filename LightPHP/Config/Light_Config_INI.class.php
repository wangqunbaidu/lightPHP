<?php
require_once( FRAMEWORK_PATH.'/Config/Light_Config_Base.class.php' );

/**
 *  ini配置文件类
 *
 */
class Light_Config_INI extends Light_Config_Base {
    protected function load(){
        $this->config = parse_ini_file( $this->path, true );
    }
    
    private function deal(){
        
    }
}
?>