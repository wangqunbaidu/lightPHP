<?php
/**
 * mysql driver的基类
 *
 */
class Light_Db_Mysql_Base{
    protected $currentConn;
    protected $connRW;
    protected $connRO;
    
    public function __construct( $host = '', $user = '', $pass = '', $db = '' ){
        if ( $host && $user && $pass ) {
            $this->setWriteUser( $host, $user, $pass, $db );
        }
    }
    
    public function setWriteUser( $host, $user, $pass, $db = '' ){
        $this->connRW = $this->connect( $host, $user, $pass );
        $this->switchMode(1);
        $this->selectDb( $db );
    }
    
    public function setReadOnlyUser( $host, $user, $pass, $db = '' ){
        $this->connRO = $this->connect( $host, $user, $pass );
        if ( !$this->connRW ) $this->connRW = $this->connRO;
        $this->selectDb( $db );
    }
    
    
    /**
     * 切换读写模式
     * 
     *
     * @param unknown_type $mode 0表示读 1表示写
     */
    protected function switchMode( $mode = 0 ){
        $this->currentConn = $mode ? $this->connRW : $this->connRO;   
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
    private function setCharset( $charset ){
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
        	$this->switchMode();
        } else {
            $this->switchMode(1);
        }
        
        $result = mysql_query( $sql, $this->currentConn );
        
        if ( mysql_errno( $this->currentConn ) ) {
        	Light_Exception::error( mysql_error( $this->currentConn ) );
        }
        
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
    private $table;
    private $tempTable;
    private $order;
    private $where;
    private $field;
    private $group;
    private $limit;
    
    private $sql;
    private $lastSql;
    
    private $data = array();
    
    public function selectTable( $table ){
        $this->table = $table;
        
        return $this->table( $table );
    }
    
    public function table( $table ){
        $this->tempTable = $table;
        
        return $this;
    }
    
    public function data( $data = array() ){
        $this->data = $data;
        
        return $this;
    }
    
    public function getLastSql(){
        return $this->lastSql;
    }
}
?>