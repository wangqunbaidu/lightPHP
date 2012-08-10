<?php
/**
 * mysql driver的基类
 *
 */
class Light_Db_Mysql_Base{
    protected $currentConn;
    protected $connRW;
    protected $connRO;

    protected $_lastSql;

    public function __construct( $host = '', $user = '', $pass = '', $db = '' ){
        if ( $host && $user && $pass ) {
            $this->setWriteUser( $host, $user, $pass, $db );
        }
    }

    /**
     * 设置写帐号
     *
     * @param unknown_type $host
     * @param unknown_type $user
     * @param unknown_type $pass
     * @param unknown_type $db
     */
    public function setWriteUser( $host, $user, $pass, $db = '' ){
        $this->connRW = $this->connect( $host, $user, $pass );
        $this->switchWriteMode();
        $this->selectDb( $db );
    }

    /**
     * 设置读帐号
     *
     * @param unknown_type $host
     * @param unknown_type $user
     * @param unknown_type $pass
     * @param unknown_type $db
     */
    public function setReadOnlyUser( $host, $user, $pass, $db = '' ){
        $this->connRO = $this->connect( $host, $user, $pass );
        if ( !$this->connRW ) $this->connRW = $this->connRO;
        $this->selectDb( $db );
    }


    /**
     * 切换写模式
     */
    protected function switchWriteMode(){
        $this->currentConn = $this->connRW;
    }

    /**
     * 切换读模式
     *
     */
    protected function switchReadMode(){
        $this->currentConn = $this->connRO;
    }

    /**
     * connect db
     *
     * @param unknown_type $host
     * @param unknown_type $user
     * @param unknown_type $pass
     */
    protected function connect( $host, $user, $pass ){
        if ( !$conn = @mysql_connect( $host, $user, $pass ) ) {
            Light_Exception::error( "无法连接到{$host}, " . mysql_error() );
        }

        return $conn;
    }

    /**
     * select db
     *
     * @param unknown_type $db
     * @param unknown_type $charset
     */
    public function selectDb( $db, $charset = '' ){
        if ( !$db ) return ;

        if( $this->connRW && !@mysql_select_db( $db, $this->connRW ) ){
            Light_Exception::error( "无法连接到数据库{$db}, " . mysql_error( $this->connRW ) );
        }

        if ( $this->connRO && !@mysql_select_db( $db, $this->connRO) ) {
            Light_Exception::error( "无法连接到数据库{$db}, " . mysql_error( $this->connRO ) );
        }

        if ( $charset ) {
            $this->setCharset( $charset );
        }
    }

    /**
     * 设置charset
     *
     * @param unknown_type $charset
     */
    protected function setCharset( $charset ){
        $string = "SET NAMES {$charset}";

        if ( $this->connRW ) mysql_query( $string, $this->connRW );

        if ( $this->connRO ) mysql_query( $string, $this->connRO );
    }

    /**
     * exec
     *
     * @param unknown_type $sql
     * @return unknown
     */
    public function query( $sql ){
        $type = self::getSqlType( $sql );

        if ( $this->connRO && $type == 'select' ) {
            $this->switchReadMode();
        } else {
            $this->switchWriteMode();
        }

        $result = mysql_query( $sql, $this->currentConn );

        if ( mysql_errno( $this->currentConn ) ) {
            Light_Exception::error( "执行SQL: {$sql} 失败, " . mysql_error( $this->currentConn ) );
        }

        $this->_lastSql = $sql;

        return $this->dispatch( $result, $type );
    }

    /**
     * 返回最后一次插入的id
     *
     * @return unknown
     */
    public function insert_id(){
        return mysql_insert_id( $this->currentConn );
    }

    /**
     * 返回影响的行数
     *
     * @return unknown
     */
    public function affected_rows(){
        return mysql_affected_rows( $this->currentConn );
    }

    /**
     * deal result
     *
     * @param unknown_type $result
     * @param unknown_type $type
     * @return unknown
     */
    protected function dispatch( $result, $type ){
        switch ( $type ){
            case 'select' :
                $result = $this->fetch( $result );
                break;

            case 'insert' :
                $result = $this->affected_rows() > 0 ? $this->insert_id() : false;

                break;

            default: break;
        }

        return $result;
    }

    /**
     * return select result
     *
     * @param unknown_type $result
     * @return unknown
     */
    protected function fetch( $result ){
        $data = array();

        while ( $rows = mysql_fetch_array( $result, MYSQL_ASSOC ) ) {
            $data[] = $rows;
        }

        return $data;
    }

    /**
     * 获取最后执行的sql
     *
     * @return unknown
     */
    public function getLastSql(){
        return $this->_lastSql;
    }

    /**
     * 获取sql的type
     *
     * @param unknown_type $sql
     * @return unknown
     */
    protected static function getSqlType( $sql ){
        preg_match( "/^\s*([a-zA-Z]+)/", $sql, $match );

        return strtolower( $match[1] );
    }
}


$n = new Light_Db_Mysql('localhost', 'root', '123456', 'zhanghao');

$n->setReadOnlyUser('localhost', 'zhanghao', '123456', 'zhanghao');

//var_dump($n->query('insert into test values(null, 123)'));
//
//var_dump($n->query('select * from test'));
//
//var_dump($n->query('update test set name=23'));
//
//var_dump($n->query('select * from test'));
$n->selectTable('test');

//$n->where(array(
//               'name' => 'jsyzhanghao',
//               'age < 11 AND age > 20',
//               'city' => array('like', '%city%'),
//               'a' => array('lt', 22)
//           ),
//      
//           'sex = "m"')->field('name')->distinct('id')->group('name')->order('id desc')->limit()->page(3)->findAll();
//$result = $n->field('id')->distinct('name')->find();
$result = $n->query("select * from test");         

var_dump($result);

class Light_Db_Mysql extends Light_Db_Mysql_Base {
    /**
     * 预设的where类型
     *
     * @var unknown_type
     */
    private static $prepareWhere = array(
        'gt' => '>',
        'lt' => '<',
        'eq' => '=',
        'neq' => '<>',
        'egt' => '>=',
        'elt' => '<=',
    );

    private $_table;
    private $_tempTable;
    private $_order;
    private $_where;
    private $_field = array();
    private $_group;
    private $_limit = 10;
    private $_page = 0;
    private $_distinct;

    private $_data = array();

    /**
     * 选择默认操作表 执行后可不用table方法
     *
     * @param unknown_type $table
     * @return unknown
     */
    public function selectTable( $table ){
        $this->_table = $table;
        
        return $this;
    }

    /**
     * 设置临时表
     *
     * @param unknown_type $table
     * @return unknown
     */
    public function table( $table ){
        $this->_tempTable = $table;

        return $this;
    }

    /**
     * 设置数据
     *
     * @param unknown_type $data
     * @return unknown
     */
    public function data( $data = array() ){
        $this->_data = $data;
        
        return $this;
    }

    /**
     * 设置limit
     *
     * @param unknown_type $limit
     * @return unknown
     */
    public function limit( $limit = 10 ){
        $this->_limit = $limit;
        
        return $this;
    }

    /**
     * 设置page
     *
     * @param unknown_type $page
     * @return unknown
     */
    public function page( $page = 1 ){
        $this->_page = (int)$page - 1;
        
        return $this;
    }

    /**
     * 设置order
     *
     * @param unknown_type $order
     * @return unknown
     */
    public function order( $order ){
        $this->_order = (array)$order;
        
        return $this;
    }

    /**
     * 设置group
     *
     * @param unknown_type $group
     * @return unknown
     */
    public function group( $group ){
        $this->_group = (array)$group;
        
        return $this;
    }

    /**
     * 设置查询的字段
     *
     * @param unknown_type $field
     * @return unknown
     */
    public function field( $field = '*' ){
        $field = (array)$field;

        $temp = array();

        foreach ( $field as $key => $value ){
            $value = self::escape( $value );
            $temp[] = is_numeric( $key ) ? $value : self::escape( $key ) . ' as ' . $value;
        }

        $this->_field = $temp;

        return $this;
    }
    
    /**
     * distinct 操作
     *
     * @param unknown_type $field
     * @return unknown
     */
    public function distinct( $field ){
        array_unshift( $this->_field, "DISTINCT " . self::escape( $field ) );
        
        return $this;
    }

    /**
     * 设置查询条件
     *
     * @param unknown_type 参数为多个时 为or操作 每一个参数可为数组或者字符串
     * where(
     *      array(
     *          'name' => 'jsyzhanghao',
     *          'age < 11 AND age > 20',
     *          'city' => array('like', '%city%'),
     *      ),
     * 
     *      'sex = "m"'
     * )
     * 
     * ( name = 'jsyczhangha' AND age < 11 AND age > 20 AND city like '%city%' ) OR ( sex = 'm' )
     * @return unknown
     */
    public function where(){
        $args = func_get_args();
        
        $Temp = array();
        
        foreach ( $args as $condition ) {
            $condition = (array)$condition;

            $temp = array();
        
            foreach ( $condition as $key => $item ) {

                if ( is_numeric( $key ) ) {
                    $temp[] = $item;
                    continue;
                }

                $key = self::escape( $key );

                if ( is_array( $item ) ) {
                    
                    $item = $key . ' '
                             . 
                             ( 
                                self::$prepareWhere[$item[0]] 
                                ? 
                                self::$prepareWhere[$item[0]]
                                : 
                                $item[0] 
                              ) 
                              . ' ' .
                              self::addQuote( $item[1] );
                              
                } else if( is_string( $item ) ) {
                    $item = $key . '=' . self::addQuote( $item );
                }

                $temp[] = $item;
            }    
            
            $temp = implode( ' AND ', $temp );
            
            $Temp[] = count( $args ) > 1 ? ' ( ' . $temp . ' ) ' : $temp;
        }
        
        $this->_where = $Temp;
        return $this;
    }
    
    public function query( $sql ){
        $this->cleanAllSqlArguments();
        return parent::query( $sql );
    }
    
    /**
     * 查询 返回第一条
     *
     * @param unknown_type $condition
     * @return unknown
     */
    public function find( $condition = null){
        $result = $this->limit( 1 )->findAll( $condition );
        
        return $result ? $result[0] : null;
    }
    
    /**
     * 查询 返回所有记录 根据limit
     *
     * @param unknown_type $condition
     */
    public function findAll( $condition = null ){
        if( $condition ) $this->where( $condition );
        
        $args = $this->getAllSqlArguments();
        
        $sql = "SELECT {$args['field']} FROM {$args['table']} {$args['where']} {$args['group']} {$args['order']} {$args['limit']}";
        
        return $this->query( $sql );
    }
    
    /**
     * 插入数据
     *
     * @param unknown_type $data 可为一维也可为多维数组
     */
    public function insert( $data = null ){
        if ( $data ) $this->data( $data );
    }
    
    /**
     * 更新数据
     *
     * @param unknown_type $data 可为一维数组
     */
    public function update( $data = null ){
        if ( $data ) $this->data( $data );
    }
    
    /**
     * 删除
     *
     * @param unknown_type $condition
     */
    public function delete( $condition = null ){
        if( $condition ) $this->where( $condition );
    }
    
    /**
     * 返回某一个字段的count
     *
     * @param unknown_type $field
     * @return unknown
     */
    public function count( $field = '*' ){
        $this->_field[] = "COUNT({$field}) as c";
        
        return $this->findAll();
    }
    
    /**
     * 获取所有sql语句所需的参数 包括where, group, field, order, limit 主要用于select, update-where, delete
     *
     * @return unknown
     */
    private function getAllSqlArguments(){
        $temp = array( 'table' => $this->_tempTable ? self::escape( $this->_tempTable ) : self::escape( $this->_table ) );
        
        if ( $this->_where ) $temp['where'] = "WHERE " . implode( ' OR ', $this->_where );
        if ( $this->_field ) $temp['field'] = implode( ', ', $this->_field );
        if ( $this->_group ) $temp['group'] = "GROUP BY " . self::escape( implode( ', ', $this->_group ) );
        if ( $this->_limit ) {
            if ( is_numeric( $this->_limit ) ) {
                $limit = $this->_page > 0 ? $this->_page * $this->_limit . ", " . $this->_limit : $this->_limit;
            }
            else $limit = self::escape( $this->_limit );
            $temp['limit'] = "LIMIT {$limit}";
        }
        if ( $this->_order ) $temp['order'] = "ORDER BY " . self::escape( implode( ', ', $this->_order ) );
        
        return $temp;
    }
    
    private function cleanAllSqlArguments(){
        $this->_tempTable = '';
        $this->_order = null;
        $this->_where = null;
        $this->_field = array();
        $this->_group = null;
        $this->_limit = 10;
        $this->_page = 0;
        $this->_distinct = null;

        $this->_data = array();
    }

    /**
     * 过滤 防sql注入
     *
     * @param unknown_type $string
     * @return unknown
     */
    private static function escape( $string ){
        return mysql_real_escape_string( $string );
    }
    
    /**
     * 添加双引号
     *
     * @param unknown_type $string
     * @return unknown
     */
    private static function addQuote( $string = '' ){
        return  '"' . self::escape( $string ) . '"';
    }
}
?>