<?php
/**
 * 工具类
 *
 */
class Light_Util{
    //import path的分割符
    const IMPORT_PATH_LIMIT = '.';
    
    /**
     * 处理php函数无法合并多维关联数组的情况
     * 函数处理和js中的深度克隆一样
     *
     * @return unknown
     */
    public static function arrayMerge(){
        $temp = array();
        
        $args = func_get_args();
        
        foreach ( $args as $key => $value ) {
            $value = (array)$value;
            
            foreach ( $value as $k => $v ) {
                if( isset($v) ){
                    $temp[$k] = is_array( $v ) ? self::arrayMerge( $temp[$k], $v ) : $v;
                }
            }
        }
        
        return $temp;
    }
    
    /**
     * 导入某个文件
     * 如导入controller中的类或者model
     * import('Controller.IndexController') import('Controller.Home.IndexController');
     *
     * @param unknown_type $path
     * @return unknown
     */
    public static function import( $path ){
        $path = explode( self::IMPORT_PATH_LIMIT , $path );
        
        $prefix = array_shift( $path );
        
        switch ( $prefix ){
            case 'Controller':
                   $prefix = Light_Register::get('controllerDirectory'); break;
            
            case 'Model':
                   $prefix = Light_Register::get('modelDirectory'); break;
                   
            default: '';
        }
        
        $path = $prefix . implode( '/', $path ) . '.class.php';
        
        if ( !$result = @include_once( $path ) ) {
        	throw new Light_Exception("文件 {$path} 木有找到！ ");
        }
        
        return $result;
    }
}
?>