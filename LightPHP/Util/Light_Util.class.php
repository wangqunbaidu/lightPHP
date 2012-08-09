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
}
?>