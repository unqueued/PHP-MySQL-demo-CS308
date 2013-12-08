<?php

require("common.inc");

session_start();

$username = $_SESSION['username'];

check_login();

$query = <<<QUERY
select * from invited where pid = ? and response = 1
QUERY;

$connection = new mysqli($db_host, $db_username, $db_passwd, $db_name);
if($connection->connect_errno > 0){
	die('Unable to connect to database [' . $db->connect_error . ']');
}
$statement = $connection->prepare($query);
if(!$statement) {
	die($connection->error);
}
$statement->bind_param('s', $username);
$statement->execute();
$statement->bind_result($pid, $eid, $response, $visibility);
$returned = array();
while($statement->fetch()) {
	$returned[] = array(
		"pid"			=>	$pid,
		"eid"			=>	$eid,
		"response"		=>	$response,
		"visibility"	=>	$visibility
	);
}

$statement->close();
$connection->close();

$table = array_to_table($returned);

?>
<html>
	<head>
		<title>Schedule for today</title>
	</head>
	<body>
		<h3>Events that you have scheduled for today that you have accepted:</h3>
		<p>Query to select events</p>
		<pre style="width: 80%; font-family:monospace; font-size: .75em; border: 1px solid red;">
<?=$query?>

		</pre>
<?=entab($table, 3)?>
		<a href="logout.php">Logout</a>
		<a href="index.php">Main page</a>
		<p>Session length: <?=total_login_time() ?> seconds</p>
	</body>
</html>
