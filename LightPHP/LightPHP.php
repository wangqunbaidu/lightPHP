<?php
define( FRAMEWORK_PATH, dirname(__FILE__) );

require_once( FRAMEWORK_PATH.'/Common/Common.php' );
require_once( FRAMEWORK_PATH.'/File/Light_File_Info.class.php' );
require_once( FRAMEWORK_PATH.'/Exception/Light_Exception.class.php' );
require_once( FRAMEWORK_PATH.'/Controller/Light_Controller_Front.class.php' );
require_once( FRAMEWORK_PATH.'/Register/Light_Register.class.php' );
require_once( FRAMEWORK_PATH.'/Controller/Light_Controller.class.php' );
require_once( FRAMEWORK_PATH.'/Model/Light_Model.class.php' );
require_once( FRAMEWORK_PATH.'/View/Light_View.class.php' );
require_once( FRAMEWORK_PATH.'/Config/Light_Config.class.php' );

require_once( FRAMEWORK_PATH.'/Db/Light_Db_Mysql.class.php' );
?>