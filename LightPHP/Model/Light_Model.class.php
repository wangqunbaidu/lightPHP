<?php
/**
 * Model 类
 *
 */
class Light_Model{
    private static $MODEL_DIRECTORY;

    private static $db;
    
    private $table;
    
    private $tempTable;

    public function __construct( $modelname = '' ){
        if( $modelname ) $this->selectTable( $modelname );
    }
    
    public function selectTable( $table ){
        $this->table = $table;
        
        return $this;
    }
    
    public function table( $table ){
        $this->tempTable = $table;
        
        return $this;
    }
    
    /**
     * 设置数据
     *
     * @param unknown_type $data
     * @return unknown
     */
    public function data( $data = array() ){
        self::getDb()->data( $data );
        
        return $this;
    }

    /**
     * 设置limit
     *
     * @param unknown_type $limit
     * @return unknown
     */
    public function limit( $limit = 10 ){
        self::getDb()->limit( $limit );
        
        return $this;
    }

    /**
     * 设置page
     *
     * @param unknown_type $page
     * @return unknown
     */
    public function page( $page = 1 ){
        self::getDb()->page( $page );
        
        return $this;
    }

    /**
     * 设置order
     *
     * @param unknown_type $order
     * @return unknown
     */
    public function order( $order ){
        self::getDb()->order( $order );
        
        return $this;
    }

    /**
     * 设置group
     *
     * @param unknown_type $group
     * @return unknown
     */
    public function group( $group ){
        self::getDb()->group( $group );
        
        return $this;
    }
    
        /**
     * 设置查询的字段
     *
     * @param unknown_type $field
     * @return unknown
     */
    public function field( $field = '*' ){
        self::getDb()->field( $field );

        return $this;
    }
    
    /**
     * distinct 操作
     *
     * @param unknown_type $field
     * @return unknown
     */
    public function distinct( $field ){
        self::getDb()->distinct( $field );
        
        return $this;
    }

    /**
     * 设置查询条件
     * @return unknown
     */
    public function where(){
        call_user_method_array( 'where', self::getDb(), func_get_args() );
        
        return $this;
    }
    
    /**
     *
     * @param unknown_type $sql
     * @return unknown
     */
    public function query( $sql ){        
        self::getDb()->query( $sql );
        
        return $this;
    }
    
    /**
     * 查询 返回第一条
     *
     * @param unknown_type $condition
     * @return unknown
     */
    public function find( $condition = null){
        $this->beforeExec();
        return self::getDb()->find( $condition );
    }
    
    /**
     * 查询 返回所有记录 根据limit
     *
     * @param unknown_type $condition
     */
    public function findAll( $condition = null ){
        $this->beforeExec();
        return self::getDb()->findAll( $condition );
    }
    
    /**
     * 插入数据
     *
     * @param unknown_type $data 目前仅支持一维数组
     */
    public function insert( $data = null ){
        $this->beforeExec();
        return self::getDb()->insert( $data );
    }
    
    /**
     * 更新数据
     *
     * @param unknown_type $data 可为一维数组
     */
    public function update( $data = null ){
        $this->beforeExec();
        return self::getDb()->update( $data );
    }
    
    /**
     * 删除
     *
     * @param unknown_type $condition
     */
    public function delete( $condition = null ){
        $this->beforeExec();
        return self::getDb()->delete( $condition );
    }
    
    /**
     * 返回某一个字段的count
     *
     * @param unknown_type $field
     * @return unknown
     */
    public function count( $field = '*' ){
        $this->beforeExec();
        return self::getDb()->count( $field );
    }
    
    /**
     * 获取最后执行的sql
     *
     * @return unknown
     */
    public function getLastSql(){
        return self::getDb()->getLastSql();
    }

    /**
     * 返回最后一次插入的id
     *
     * @return unknown
     */
    public function insert_id(){
        return self::getDb()->insert_id();
    }

    /**
     * 返回影响的行数
     *
     * @return unknown
     */
    public function affected_rows(){
        return self::getDb()->affected_rows();
    }
    
    /**
     * 执行sql之前执行
     *
     */
    private function beforeExec(){
        $table = $this->tempTable ? $this->tempTable : $this->table;
        self::getDb()->table( $table );
        
        $this->tempTable = null;
    }

    public static function setDirectory( $path ){
        self::$MODEL_DIRECTORY = rtrim( $path, '/' ) . '/';
    }

    public static function getDirectory(){
        return self::$MODEL_DIRECTORY;
    }

    private static function getDb(){
        if ( !self::$db ) {
            $config = Light_Config::get('db');
            
            switch ( $config['type'] ){
                default:
                    require_once( FRAMEWORK_PATH . '/Db/Light_Db_Mysql.class.php' );
                    self::$db = new Light_Db_Mysql( $config['host'], $config['user'], $config['pass'], $config['name'] );
                    break;
            }
            
            if( $config['read'] ) {
                self::$db->setReadConnect( 
                    $config['read']['host'], 
                    $config['read']['user'], 
                    $config['read']['pass'], 
                    $config['read']['name']
                );
            }
        }
        
        return self::$db;
    }
}
?>