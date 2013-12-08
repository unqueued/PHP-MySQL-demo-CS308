<?php

require("common.inc");

session_start();

$username = $_SESSION['username'];

check_login();

$query = <<<QUERY
select invited.pid, event.eid, response, visibility, start_time, duration, description, edate
from invited, event, eventdate
where invited.eid = event.eid and eventdate.eid = invited.eid and
invited.pid = ? and response = '1' and 
edate = ?
QUERY;

$connection = new mysqli($db_host, $db_username, $db_passwd, $db_name);
if($connection->connect_errno > 0){
	die('Unable to connect to database [' . $db->connect_error . ']');
}
$statement = $connection->prepare($query);
$statement->bind_param('ss', $username, date('y-m-d'));
if(!$statement) {
	die($connection->error);
}
$statement->execute();

$statement->bind_result($pid, $eid, $response, $visibility, $start_time, 
	$duration, $description, $edate);
while($statement->fetch()) {
	$returned[] = array(
		"pid"			=>	$pid,
		"eid"			=>	$eid,
		"response"		=>	$response,
		"visibility"	=>	$visibility,
		"start_time"	=>	$start_time,
		"duration"		=>	$duration,
		"description"	=>	$description,
		"edate"			=>	$edate
	);
}

$statement->close();
$connection->close();

$table = array_to_table($returned);

?>
<html>
	<head>
		<title>Schedule for today <?php echo date('y-m-d'); ?></title>
	</head>
	<body>
		<h3>Schedule for today</h3>
		<p>Query to read events for today:</p>
		<pre style="width: 80%; font-family:monospace; font-size: .75em; border: 1px solid red;">
<?=$query?>

		</pre>
		<p>Events that you have scheduled for today that you have accepted:</p>
<?php print entab($table, 2); ?>
		<a href="logout.php">Logout</a>
		<a href="index.php">Main page</a>
		<p>Session length: <?=total_login_time() ?> seconds</p>
	</body>
</html>