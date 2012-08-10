<?php
/**
 * view data 存储类
 *
 */
class Light_View_Data{
    protected $data = array();
    
    /**
     * 设置值
     *
     * @param unknown_type $name
     * @param unknown_type $value
     */
    public function assign( $name, $value = '' ){
        if ( is_array( $name ) ) {
        	$this->data = array_merge( $this->data, $name );
        } else {
            $this->data[$name] = $value;
        }
    }
    
    /**
     * 清除所有的赋值
     *
     */
    public function clear_all_assign(){
        $this->data = array();
    }
    
    /**
     * 清除某一个赋值
     *
     * @param unknown_type $name 为空时 清除所有值
     */
    public function clear_assign( $name = '' ){
        if ( $name ) {
        	unset( $this->data[$name] );
        } else {
            $this->clear_all_assign();
        }
    }
}
?>