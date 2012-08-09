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
        	throw new Light_Exception("文件 {$path} 木有找到！ ");
        }
        
        return $result;
}
?>