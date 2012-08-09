<?php
/**
 * 异常类
 * 处理异常 暂无写日志功能 只是简单的输出
 *
 */
class FrameworkException extends Exception {
    public function __construct( $message = '' ){
        $content = $message ? $message : $this->getMessage();
        
        header("Content-type: text/html; charset=utf-8;");
        echo "<div style='padding: 20px; background: #ff9; margin: 10px; font-family: 微软雅黑; text-align: center;'>{$content}</div>";
        exit();
    }
    
    public static function error( $message = '' ){
        new self( $message );
    }
}
?>