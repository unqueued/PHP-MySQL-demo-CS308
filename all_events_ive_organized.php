<?php

require("common.inc");

session_start();

$username = $_SESSION['username'];

check_login();

$query = <<<QUERY
select event.eid as Event_ID, count(*) as attending
from event, invited
where event.eid = invited.eid and event.pid = ? and
invited.response = '1' group by event.eid
QUERY;

$connection = new mysqli($db_host, $db_username, $db_passwd, $db_name);
if($connection->connect_errno > 0){
	die('Unable to connect to database [' . $db->connect_error . ']');
}
$statement = $connection->prepare($query);
$statement->bind_param('s', $username);
if(!$statement) {
	die($connection->error);
}
$statement->execute();
$statement->bind_result($eid, $num_attending);
$returned = array();
while($statement->fetch()) {
	$returned[] = array(
		"eid"			=>	$eid,
		"num_attending"	=>	$num_attending
	);
}

$connection->close();
$statement->close();

?>
<html>
	<head>
		<title>All events that you have organized<</title>
	</head>
	<body>
		<h3>All events that you have organized</h3>
		<p>Query to select all events you've organized</p>
		<pre style="width: 80%; font-family:monospace; font-size: .75em; border: 1px solid red;">
<?=$query?>

		</pre>
		<table border="1">
			<tr>
				<td>Event ID</td>
				<td>Number attending</td>
			</tr>
<?php foreach($returned as $i): ?>
			<tr>
				<td><?=htmlspecialchars($i['eid'])?></td>
				<td><?=htmlspecialchars($i['num_attending'])?></td>
			</tr>
<?php endforeach; ?>
		</table>
		<a href="logout.php">Logout</a>
		<a href="index.php">Main page</a>
		<p>Session length: <?=total_login_time() ?> seconds</p>
	</body>
</html>
