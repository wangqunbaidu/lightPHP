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
    
    public static function factory(){
        $db = Light_Config::get('db');
        
        switch ( $db['type'] ){
            default: 
                require_once( FRAMEWORK_PATH . '/Db/Light_Db_Mysql.class.php' );
                $driver = new Light_Db_Mysql( $db['host'], $db['user'], $db['pass'], $db['name'] );
                break;
        }
        
        return $driver;
    }
}
?>