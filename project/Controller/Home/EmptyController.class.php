<?php
class EmptyController extends Controller{
    public function __construct( $controller, $action ){
        parent::__construct( $controller, $action );
        
        $this->{$this->controller}();
    }
    
    public function photoController(){
        echo 'photo';
    }
}
?>