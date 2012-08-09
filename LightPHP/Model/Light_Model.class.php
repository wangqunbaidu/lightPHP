<?php
/**
 * Model 类
 *
 */
class Light_Model{
    private static $MODEL_DIRECTORY;
    
    public function __construct( $modelname = '' ){
        
    }
    
    public static function setDirectory( $path ){
        self::$MODEL_DIRECTORY = rtrim( $path, '/' ) . '/';
    }   
    
    public static function getDirectory(){
        return self::$MODEL_DIRECTORY;
    }
}
?>