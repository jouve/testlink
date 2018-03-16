<?php

require_once("/var/www/html/config.inc.php");
require_once("/var/www/html/lib/functions/database.class.php");
require_once("installUtils.php");
require_once("sqlParser.class.php");
require_once("/var/www/html/lib/functions/common.php");
require_once("/var/www/html/lib/functions/object.class.php");
require_once("/var/www/html/lib/functions/metastring.class.php");
require_once("/var/www/html/third_party/dBug/dBug.php");
require_once("/var/www/html/lib/functions/logger.class.php");

$db_server = DB_HOST;
//!!$db_admin_name = $_SESSION['databaseloginname'];
//!!$db_admin_pass = $_SESSION['databaseloginpassword'];
$db_name = DB_NAME;
$db_type = DB_TYPE;
$tl_db_login = DB_USER;
$tl_db_passwd = DB_PASS;
$db_table_prefix = DB_TABLE_PREFIX;

$check = check_db_loaded_extension($db_type);
if( $check['errors'] > 0 )
{
  echo $check['msg'];
  exit(1);
}

define('NO_DSN', FALSE);

// ------------------------------------------------------------------------------------------------
// Connect to DB Server without choosing an specific database
$db = new database($db_type);
@$conn_result = $db->connect(NO_DSN, $db_server, $tl_db_login, $tl_db_passwd); 

if( $conn_result['status'] == 0 ) 
{
  echo 'Please check the database login details and try again. Database Error Message: \n' . $db->error_msg();
  exit(1);
}
else 
{
  echo "OK!\n";
}
$db->close();
$db=null;

// --------------------------------------------------------------------------------------
// Connect to the Database (if Succesful -> database exists)
$db = new database($db_type);
@$conn_result = $db->connect(NO_DSN, $db_server, $tl_db_login, $tl_db_passwd, $db_name); 

if( $conn_result['status'] == 0 ) 
{
  $db->close();
  echo "Database $db_name does not exist.";
  exit(1);
} 
else 
{
  echo "Connecting to database `" . $db_name . "`: ";
  echo "OK!\n";
}
// ------------------------------------------------------------------------------------------------

if( ! $db->db_table_exists('db_version') )
{
  echo "creating";

  $sqlParser = new SqlParser($db,$db_type,$db_table_prefix);
  $sqlParser->process("sql/{$db_type}/testlink_create_tables.sql");
  $sqlParser->process("sql/{$db_type}/testlink_create_default_data. $sqlParser-");
}
else
{
  $tables = tlObject::getDBTables();
  $dbVersionTable = $tables['db_version'];
  $sql = "SELECT * FROM {$dbVersionTable} ORDER BY upgrade_ts DESC";
  $res = $db->exec_query($sql);  
  if (!$res)
  {
    echo "Database ERROR:" . $db->error_msg();
    exit(1); 
  }
  
  $myrow = $db->fetch_array($res);
  $schema_version=trim($myrow['version']);
  
  switch ($schema_version)
  {   
    case 'DB 1.9.16':
      echo "Nothing to migrate";
      break;
    case 'DB 1.9.15':
      $a_sql_upd_dir[] = "sql/alter_tables/1.9.15/{$db_type}/DB.1.9.15/step1/db_schema_update.sql";       
      $a_sql_data_dir[] = "sql/alter_tables/1.9.15/{$db_type}/DB.1.9.15/stepZ/z_final_step.sql";
      break;
    default:
      echo "Invalid version to migrate from " . $schema_version;
      exit(1);
      break;  
  }
}

$db->close();

?>