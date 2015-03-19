<?php
	//Include database connection details
	require_once('system-db.php');
	
	start_db();
	
	$customerid = $_POST['customerid'];
	
	createComboOptions("id", "name", "{$_SESSION['DB_PREFIX']}customerbranch", "WHERE customerid = $customerid", true);
?>