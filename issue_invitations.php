<?php

require("common.inc");

session_start();

$username = $_SESSION['username'];

check_login();

$organized_events_query = <<<QUERY
select eid, description, start_time, duration
from event
where pid = ?
QUERY;

$connection = new mysqli($db_host, $db_username, $db_passwd, $db_name);
if($connection->connect_errno > 0){
	die('Unable to connect to database [' . $db->connect_error . ']');
}
$statement = $connection->prepare($organized_events_query);
if(!$statement) {
	die($connection->error);
}
$statement->bind_param('s', $username);
$statement->execute();
$statement->bind_result($eid, $description, $start_time, $duration);

$returned = array();
while($statement->fetch()) {
	$returned[] = array(
		"eid"			=>	$eid,
		"description"			=>	$description,
		"start_time"		=>	$start_time,
		"duration"	=>	$duration
	);
}

$organized_events = $returned;

$all_users_query = <<<QUERY
select pid from person
QUERY;

$connection = new mysqli($db_host, $db_username, $db_passwd, $db_name);
if($connection->connect_errno > 0){
	die('Unable to connect to database [' . $db->connect_error . ']');
}
$statement = $connection->prepare($all_users_query);
if(!$statement) {
	die($connection->error);
}
$statement->execute();
$statement->bind_result($pid);

$returned = array();
while($statement->fetch()) {
	$returned[] = array(
		"pid"			=>	$pid,
	);
}

$all_users = $returned;

// Extract values from POST
$selected_eid = $_POST['eid'];
$invited_users = array();
foreach(preg_grep("/^pid_/", array_keys($_POST)) as $i) {
	$invited_users[] = str_replace("pid_", "", $i);
}

// Invite any selected users
if(isset($selected_eid)) {
	if(count($invited_users) < 1) {
	} else {
		verify_organizer($selected_eid, $username);
		invite_users($selected_eid, $invited_users, $invitation_query);
	}
}

?>
<html>
	<head>
		<title>Issue invitations for events</title>
	</head>
	<body>
		<h3>invite users to events you have organized</h3>
		<p>Query to fetch all events you have organized</p>
		<pre style="width: 80%; font-family:monospace; font-size: .75em; border: 1px solid red;">
<?=$organized_events_query?>
		</pre>
		<p>Query to fetch all users</p>
		<pre style="width: 80%; font-family:monospace; font-size: .75em; border: 1px solid red;">
<?=$all_users_query?>
		</pre>
<?php if(isset($selected_eid)): ?>
		<p>Query to insert event inviting users</p>
		<pre style="width: 80%; font-family:monospace; font-size: .75em; border: 1px solid red;">
<?=$invitation_query?>
		</pre>
<?php endif; ?>
		<form action="<?=basename(__FILE__) ?>" method="post">
		
			<table border="1">
				<tr>
					<th>Select</th>
					<th>Event ID</th>
					<th>Description</th>
					<th>Start time</th>
					<th>Duration</th>
				</tr>
<?php foreach($organized_events as $row):?>
				<tr>
					<td><input type="radio" name="eid" value="<?=$row[eid] ?>" /></td>
					<td><?=htmlspecialchars($row['eid']) ?></td>
					<td><?=htmlspecialchars($row['description']) ?></td>
					<td><?=htmlspecialchars($row['start_time']) ?></td>
					<td><?=htmlspecialchars($row['duration']) ?></td>
				</tr>
<?php endforeach?>
			</table>
			
			<table border="1">
				<tr>
					<th>Select</th>
					<th>User ID</th>
				</tr>
<?php foreach($all_users as $row):?>
				<tr>
					<td><input type="checkbox" name="pid_<?=$row[pid] ?>" value="true" /></td>
					<td><?=htmlspecialchars($row['pid']) ?></td>
				</tr>
<?php endforeach?>
			</table>
			
			<input type="submit" value="Submit" />
			
		</form>



		<a href="logout.php">Logout</a>
		<a href="index.php">Main page</a>
		<p>Session length: <?=total_login_time() ?> seconds</p>
	</body>
</html>
