<?php
//Define Database Credentials
define('DBHOST', getenv('DB_HOST') ?: 'db'); // Fallback to 'localhost' if not set
define('DBUSER', getenv('DB_USER') ?: 'darts_user'); // Fallback to default user if not set
define('DBPASS', getenv('DB_PASS') ?: 'darts_password'); // Fallback to default password if not set
define('DBNAME', getenv('DB_NAME') ?: 'darts_live'); // Fallback to default database if not set

try {
	//create PDO connection
	$db = new PDO("mysql:host=".DBHOST.";charset=utf8mb4;dbname=".DBNAME, DBUSER, DBPASS);
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch(PDOException $e) {
	//show error
    echo $e->getMessage();
    exit;
}
?>