<?php
require_once( FRAMEWORK_PATH.'/Config/Light_Config_Base.class.php' );

/**
 *  ini配置文件类
 *
 */
class Light_Config_INI extends Light_Config_Base {
    const INI_CONFIG_LIMIT = '.';

    protected function load(){
        $temp = parse_ini_file( $this->path, true );
        $this->parse( $temp );
    }

    private function parse( $ini )  {
        $configs=array();
        $config=array();
        foreach ( $ini as $key => $value ) {
            foreach ( $value as $k => $v ) {
                $configs = array_merge_recursive( $configs, $this->processKey( $config, $k, $v ) );
            }
            $this->config[$key] = $configs;
            $configs=array();
        }
    }

    private function processKey( $config = array(), $key, $value ) {
        if (strpos( $key, self::INI_CONFIG_LIMIT ) !== false ) {
            $pieces = explode( self::INI_CONFIG_LIMIT , $key, 2 );
            if ( strlen($pieces[0]) && strlen($pieces[1]) ) {
                $config[$temp] = $this->processKey( $config[$temp], $pieces[1], $value );
            }
        } else {
            $config[$key]=$value;
        }
        return $config;
    }
}
?>