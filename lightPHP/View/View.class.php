<?php
require_once( FRAMEWORK_PATH.'/View/ViewData.class.php' );

/**
 *  模板主类
 *  用于输出模板
 *  该模板引擎暂不支持 后缀扩展 后缀需自己加上
 *  也不支持自动识别当前控制器和action功能
 */
class View extends ViewData{
    //模板引擎
    //用于第3方引擎
    private static $engine;
    
    public  $template_dir = '';
    
    /**
     * 返回结果 不输出
     *
     * @param unknown_type $tpl
     * @return unknown
     */
    public function fetch( $tpl ){
        //实例文件
        $file = new FileInfo( $this->template_dir . ltrim( $tpl ) );
        
        //获取内容
        $content = $file->getFileContent();
        
        ob_start();

        //设置data 将data做为数组 在下面内容中引用
        extract( $this->data );
        
        //执行
        eval( "?>" .$content );
        
        //获取数据 清空buffer
        return ob_get_clean();
    }
    
    public function display( $tpl , $charset = 'utf-8', $type = 'text/html' ){
        header( "Content-type: {$type}; charset: {$charset};" );
        
        //直接echo
        echo $this->fetch( $tpl );
    }
    
    /**
     * 设置第3方模板引擎
     *
     * @param unknown_type $engine
     */
    public static function setEngine( $engine ){
        self::$engine = $engine;
    }
    
    /**
     * 获取模板引擎
     *
     * @return unknown
     */
    public static function getEngine(){
        return self::$engine ? self::$engine : new self();
    }
}
?>