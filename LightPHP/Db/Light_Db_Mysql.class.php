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
        $this->currentConn = $this->connRW;
        $this->selectDb( $db );
    }
    
    public function setReadOnlyUser( $host, $user, $pass, $db = '' ){
        $this->connRO = $this->connect( $host, $user, $pass );
        $this->selectDb( $db );
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
        
        if( !@mysql_select_db( $db, $this->connRW ) ){
            Light_Exception::error( "无法连接到数据库{$db}, " . mysql_error( $this->connRW ) );
        }
        
        if ( $this->connRO && !@mysql_select_db( $db, $this->connRO) ) {
        	Light_Exception::error( "无法连接到数据库{$db}, " . mysql_error( $this->connRO ) );
        }
        
        if ( $charset ) {
            $this->setCharset( $charset );
        }
    }
    
    private function setCharset( $charset ){
        $string = "SET NAMES {$charset}";
        mysql_query( $string, $this->connRW );
        
        if ( $this->connRO ) {
        	mysql_query( $string, $this->connRO );
        }
    }
    
    /**
     * exec
     *
     * @param unknown_type $sql
     * @return unknown
     */
    public function query( $sql ){
        $type = self::getSqlType( $sql );
        
        $this->currentConn = $this->connRO && $type == 'select' ? $this->connRO : $this->connRW;
        
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
					if ( $this->affected_rows() > 0 ) {
						$result = $this->insert_id();
					} else {
						$result = false;
					}
					
					break;
					
			case 'delete' :
					if ( $this->affected_rows() > 0 ) {
						$result = true;
					} else {
						$result = false;
					}
					
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


$n = new Light_Db_Mysql_Base('localhost', 'zhanghao', '123456', 'test');

var_dump($n->query('insert into test values(null, 123)'));

class Light_Db_Mysql{
    
}
?>