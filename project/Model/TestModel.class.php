<?php
class TestModel extends Light_Model{
    public function __construct(){
       //return $this->getAll();    
    }
    
    public  function getAll(){
       $id = $this->table('test')->where(array(
            'name' => array('eq', 'haha123')
       ))->delete();

       return $this->table('test')->limit(100)->findAll();
    }
}
?>