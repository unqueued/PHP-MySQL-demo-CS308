<?php

require("common.inc");

session_start();

$username = $_SESSION['username'];

check_login();

if(!isset($_GET['view_pid'])) {
	print "Error, no friend selected";
	exit(0);
}

$friend = $_GET['view_pid'];

# Can probably be made more efficient

$query = <<<QUERY
select invited.pid, invited.eid, event.start_time, event.duration, eventdate.edate, event.description,
	(level - visibility) as visible from invited, friend_of, event, eventdate
where invited.pid = friend_of.sharer and  invited.pid = ? and viewer = ? and
response = '1' and invited.eid = event.eid and eventdate.eid = event.eid
QUERY;

$connection = new mysqli($db_host, $db_username, $db_passwd, $db_name);
if($connection->connect_errno > 0){
	die('Unable to connect to database [' . $db->connect_error . ']');
}
$statement = $connection->prepare($query);
if(!$statement) {
	die($connection->error);
}
$statement->bind_param('ss', $friend, $username);
$statement->execute();
$statement->bind_result($pid, $eid, $start_time, $duration, $edate, $description, $visible);
$returned = array();
while($statement->fetch()) {
	$returned[] = array(
		"pid"			=>	$pid,
		"eid"			=>	$eid,
		"start_time"	=>	$start_time,
		"duration"		=>	$duration,
		"edate"			=>	$edate,
		"description"	=>	$description,
		"visible"		=>	$visible
	);
}

$statement->close();
$connection->close();

?>
<html>
	<head>
		<title>Schedule for today</title>
	</head>
	<body>
		<h3>View free/busy and events for selected <?=$friend ?></h3>
		<p>Query to return friend's events</p>
		<pre style="width: 80%; font-family:monospace; font-size: .75em; border: 1px solid red;">
<?=$query?>

		</pre>		
		<table border="1">
			<tr>
				<th>Date</th>
				<th>Event ID</th>
				<th>Time</th>
				<th>Duration</th>
				<th>Details</th>
			</tr>
<?php
foreach($returned as $row) {
?>
			<tr>
				<td><?=htmlspecialchars($row['edate'])?></td>
<?php if($row['visible'] >= 0) : ?>
				<td><?=htmlspecialchars($row['eid'])?></td>
<?php else : ?>
				<td style="background-color: grey">[Not visible]</td>
<?php endif; ?>
				<td><?=htmlspecialchars($row['start_time'])?></td>
				<td><?=htmlspecialchars($row['duration'])?></td>
<?php if($row['visible'] >= 0) : ?>
				<td><?=htmlspecialchars($row['description']) ?></td>
<?php else : ?>
				<td style="background-color: grey">[Not visible]</td>
<?php endif; ?>
			</tr>
<?php
}
?>
		</table>		
		<a href="logout.php">Logout</a>
		<a href="index.php">Main page</a>
		<p>Session length: <?=total_login_time() ?> seconds</p>
	</body>
</html>