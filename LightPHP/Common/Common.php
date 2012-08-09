<?php
spl_autoload_register( 'Light_Autoload_Model', false );

function Light_Autoload_Model( $modelname ){
    import("Model.{$modelname}");
}

/**
     * 导入某个文件
     * 如导入controller中的类或者model
     * import('Controller.IndexController') import('Controller.Home.IndexController');
     *
     * @param unknown_type $path
     * @return unknown
     */
function import( $path ){
    $path = explode( '.' , $path );

    $prefix = array_shift( $path );

    switch ( $prefix ){
        case 'Controller':
            $prefix = Light_Controller::getDirectory(); break;

        case 'Model':
            $prefix = Light_Model::getDirectory(); break;

        default: '';
    }

    $path = $prefix . implode( '/', $path ) . '.class.php';

    if ( !$result = @include_once( $path ) ) {
        Light_Exception::error("文件 {$path} 木有找到！ ");
    }

    return $result;
}

/**
     * 处理php函数无法合并多维关联数组的情况
     * 函数处理和js中的深度克隆一样
     *
     * @return unknown
     */
function merge(){
    $temp = array();

    $args = func_get_args();

    foreach ( $args as $key => $value ) {
        $value = (array)$value;

        foreach ( $value as $k => $v ) {
            if( isset($v) ){
                $temp[$k] = is_array( $v ) ? merge( $temp[$k], $v ) : $v;
            }
        }
    }

    return $temp;
}
?>