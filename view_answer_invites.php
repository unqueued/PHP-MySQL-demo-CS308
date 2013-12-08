<?php

require("common.inc");

session_start();

$username = $_SESSION['username'];

#Clean this up
if(isset($_SESSION['username']) && $_SESSION['username'] != "") {
	
} else {
	header("Location: login.php");
	exit(0);
}

$get_events_query = <<<QUERY
select event.eid, event.description, event.pid as invited_by,
	event.start_time, event.duration,
	group_concat(edate ORDER BY edate DESC SEPARATOR ', ') as dates
from invited, event, eventdate
where invited.eid = event.eid and invited.pid = ? and response = '0'
and eventdate.eid = event.eid
group by event.eid
QUERY;


$d_privacy = $_SESSION['dprivacy'];

if(count($_POST) > 0) {
	foreach($_POST as $eid=>$i) {
		if($i['accepted'] == "true") {
			update_event($eid, 1, $i['visibility'], $update_event_query);
		}
		else if($i['accepted'] == "false") {
			update_event($eid, 2, $i['visibility'], $update_event_query);
		}
	}
}

$results = get_invites($get_events_query);

?>
<html>
	<head>
		<title>Pending invites</title>
	</head>
	<body>
		<h3>View / answer invites</h3>

		<p>Information query</p>
		<pre style="width: 80%; font-family:monospace; font-size: .75em; border: 1px solid red;">
<?=$get_events_query?>
		</pre>
<?php if(count($_POST) > 0): ?>
		<p>Update query:</p>
		<pre style="width: 80%; font-family:monospace; font-size: .75em; border: 1px solid red;">
<?=$update_event_query?>
		</pre>
<?php endif; ?>
		
		<form action="<?=basename(__FILE__) ?>" method="post">
			
			<table border="1">
				<tr>
					<th>EID</th>
					<th>Description</th>
					<th>Invited by</th>
					<th>Start time</th>
					<th>Dates</th>
					<th>Visibility level</th>
					<th>Accept / Decline</th>
				</tr>
<?php foreach($results as $row): ?>
				<tr>
					<td><?=htmlspecialchars($row['eid']) ?></td>
					<td><?=htmlspecialchars($row['description']) ?></td>
					<td><?=htmlspecialchars($row['invited_by']) ?></td>
					<td><?=htmlspecialchars($row['start_time']) ?> for <?=$row['duration'] ?></td>
					<td><?=htmlspecialchars($row['dates']) ?></td>
					<td><input type="text" value="<?=$d_privacy ?>" name="<?=htmlspecialchars($row['eid']) ?>[visibility]" /></td>
					<td>
						<input type="radio" name="<?=htmlspecialchars($row['eid']) ?>[accepted]" value="true" />
						<input type="radio" name="<?=htmlspecialchars($row['eid']) ?>[accepted]" value="false" />
					</td>
				</tr>
<?php endforeach; ?>
			</table>
			
			<input type="submit" value="Submit" />
			
		</form>
		
		<a href="logout.php">Logout</a>
		<a href="index.php">Main page</a>
		<p>Session length: <?=total_login_time() ?> seconds</p>
	</body>
</html>

<?php

function update_event($eid, $response, $visibility, $query) {
	global $db_username, $db_passwd, $db_host, $db_name, $username;
	
	$connection = new mysqli($db_host, $db_username, $db_passwd, $db_name);
	if($connection->connect_errno > 0){
		die('Unable to connect to database [' . $db->connect_error . ']');
	}
	$statement = $connection->prepare($query);
	$statement->bind_param('ssss', $response, $visibility, $username, $eid);
	if(!$statement) {
		die($connection->error);
	}
	$statement->execute();
	
	$connection->close();
	$statement->close();
}

function get_invites($query) {
	global $db_username, $db_passwd, $db_host, $db_name, $username;
	
	$returned = array();
	
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
	$statement->bind_result($eid, $description, $invited_by, $start_time, $duration, $dates);
	while($statement->fetch()) {
		$returned[] = array(
			"eid"			=>		$eid,
			"description"	=>		$description,
			"invited_by"	=>		$invited_by,
			"start_time"	=>		$start_time,
			"duration"		=>		$duration,
			"dates"			=>		$dates
		);
	}
	
	$statement->close();
	$connection->close();
	
	return $returned;
}

/*
function get_privacy($query) {
	global $db_username, $db_passwd, $db_host, $db_name, $username;
	
	$connection = new mysqli($db_host, $db_username, $db_passwd, $db_name);
	if($connection->connect_errno > 0){
		die('Unable to connect to database [' . $db->connect_error . ']');
	}
	$statement = $connection->prepare($query);
		if(!$statement) {
		die($connection->error);
	}
	$statement->bind_param('s', $username);
	if(!$statement) {
		die($connection->error);
	}
	$statement->execute();
	$statement->bind_result($d_privacy);
	
	$statement->fetch();
	
	$statement->close();
	$connection->close();
	
	return $d_privacy;
}
*/
?>
