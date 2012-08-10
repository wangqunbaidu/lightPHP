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

    public function setWriteUser( $host, $user, $pass, $db = '' ){
        $this->connRW = $this->connect( $host, $user, $pass );
        $this->switchWriteMode();
        $this->selectDb( $db );
    }

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
    protected function switchReadModel(){
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
            $this->switchReadModel();
        } else {
            $this->switchWriteMode();
        }

        $result = mysql_query( $sql, $this->currentConn );

        if ( mysql_errno( $this->currentConn ) ) {
            Light_Exception::error( mysql_error( $this->currentConn ) );
        }

        $this->_lastSql = $sql;

        return $this->dispatch( $result, $type );
    }

    public function insert_id(){
        return mysql_insert_id( $this->currentConn );
    }

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

    public function getLastSql(){
        return $this->_lastSql();
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


$n = new Light_Db_Mysql_Base('localhost', 'root', '123456', 'zhanghao');

$n->setReadOnlyUser('localhost', 'zhanghao', '123456', 'zhanghao');

var_dump($n->query('insert into test values(null, 123)'));

var_dump($n->query('select * from test'));

var_dump($n->query('update test set name=23'));

var_dump($n->query('select * from test'));



class Light_Db_Mysql extends Light_Db_Mysql_Base {
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
    private $_field = '*';
    private $_group;
    private $_limit = 10;
    private $_page = 0;
    private $_limitLength = self::LIMIT_LENGTH ;

    private $_sql;

    private $_data = array();

    public function selectTable( $table ){
        $this->_table = $table;

        return $this->table( $table );
    }

    public function table( $table ){
        $this->_tempTable = $table;

        return $this;
    }

    public function data( $data = array() ){
        $this->_data = $data;

        return $this;
    }

    public function limit( $limit = 10 ){
        $this->_limit = (int)$limit;
        return $this;
    }

    public function page( $page = 1 ){
        $this->_page = (int)$page - 1;

        return $this;
    }

    public function order( $order ){
        $order = (array)$order;
        
        $this->_order = implode( ', ', $order );
        
        return $this;
    }

    public function field( $field = '*' ){
        $field = (array)$field;

        $temp = array();

        foreach ( $field as $key => $value ){
            $value = self::eacape( $value );
            $temp[] = is_numeric( $key ) ? $value : self::eacape( $key ) . ' as ' . $value;
        }

        $this->_field = implode( ', ', $temp );

        return $this;
    }

    public function where( $condition ){
        if ( is_array( $condition ) ) {
            $temp = array();

            foreach ( $condition as $key => $item ) {
                $key = self::eacape( $key );

                if ( is_array( $item ) ) {
                    $item = $key . ( self::$prepareWhere[$item[0]] ? self::$prepareWhere[$item[0]] : $item[0] ) . self::eacape( $item[1] );
                } else if( is_string( $item ) ) {
                    $item = $key . '=' . self::eacape( $item );
                }

                $temp[] = $item;
            }

            $this->_where = implode( ' AND ', $temp );
        } else {
            $this->_where = $condition;
        }

        return $this;
    }

    private static function eacape( $string ){
        return mysql_real_escape_string( $string );
    }
}
?>