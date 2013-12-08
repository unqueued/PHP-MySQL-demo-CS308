<?php

require("common.inc");

session_start();

$username = $_SESSION['username'];

check_login();

$insert_query = <<<QUERY
INSERT INTO `event` (
	eid,
	start_time,
	duration,
	description,
	pid
)
VALUES (
	NULL,
	?,
	?,
	?,
	?
)
QUERY;

// Server side session variables are considered safe
$accept_invitation_query = <<<QUERY
INSERT INTO invited (pid, eid, response, visibility)
VALUES (
	?,
	?,
	1,
	$_SESSION[dprivacy]
);
QUERY;


$eventdate_query = <<<QUERY
INSERT INTO eventdate (eid, edate) 
VALUES (?, ?);
QUERY;

if(isset($_POST['description'])) {
	$eid = insert_event(
		$_POST['description'],
		$_POST['eventday'],
		$_POST['eventmonth'],
		$_POST['eventyear'],
		$_POST['starting_hour'],
		$_POST['duration'],
		$_POST['repeat'],
		$insert_query,
		$eventdate_query);
		
		invite_user($eid, $username, $accept_invitation_query);

}

?>
<html>
	<head>
		<title>Organize an event</title>
	</head>
	<body>
		<h3>Organize an event</h3>
<?php if(isset($_POST['description'])) : ?>
		<p>Insert event entry</p>
		<pre style="width: 80%; font-family:monospace; font-size: .75em; border: 1px solid red;">
<?=$insert_query ?>
		</pre>
		<p>Insert eventdate entry</p>
		<pre style="width: 80%; font-family:monospace; font-size: .75em; border: 1px solid red;">
<?=$eventdate_query?>
		</pre>
		<p>Update invitation for user to accept</p>
		<pre style="width: 80%; font-family:monospace; font-size: .75em; border: 1px solid red;">
<?=$accept_invitation_query?>
		</pre>
<?php endif; ?>
		<form action="<?=basename(__FILE__) ?>" method="post">
			
			<label for="description" style="display:block">Description</label>
			<input type="text" name="description" />
			<br />
			<label for="eventmonth" style="display:block">Start date</label>
<?php 
print entab(date_picker("event", $startyear = 2011, $endyear = 2014), 3);
?>
			<br />
			<label for="duration" style="display:block">Number of hours (duration)</label>
			<select name="duration">
<?php for($i = 0; $i < 24; $i++): ?>
				<option value='<?=$i?>'><?=$i?></option>
<?php endfor?>
			</select>
			<label for="starting_hour" style="display:block">Starting hour</label>
			<select name="starting_hour">
<?php for($i = 0; $i < 24; $i++): ?>
				<option value='<?=$i?>'><?=$i?></option>
<?php endfor?>
			</select>
			<label for="repeat" style="display:block">Number of days to repeat</label>
			<input type="text" value = "0" name="repeat">
			<br />
			
			<input type="submit" value="Submit" />
		</form>
		<a href="logout.php">Logout</a>
		<a href="index.php">Main page</a>
		<p>Session length: <?=total_login_time() ?> seconds</p>
	</body>
</html>

<?php
function insert_event($description, $day, $month, $year, $start_time, $duration, $repeat, $query, $eventdate_query) {
global $db_username, $db_passwd, $db_host, $db_name, $username;
	
	// Make sure our values are in the right form
	$duration = sprintf("%02s:00:00", $duration);
	$start_time = sprintf("%02s:00:00", $start_time);
	$repeat = intval($repeat);

	
	// Using prepared syntax
	$connection = new mysqli($db_host, $db_username, $db_passwd, $db_name);
	if($connection->connect_errno > 0){
		die('Unable to connect to database [' . $db->connect_error . ']');
	}
	
	$statement = $connection->prepare($query);
	$statement->bind_param('ssss', $start_time, $duration, $description, $username);
	if(!$statement) {
		die($connection->error);
	}
	
	$statement->execute();
	
	$inserted_id = $connection->insert_id;
	
	$inserted_id = $connection->insert_id;
	if($inserted_id == 0 or $inserted_id == FALSE) {
		die($connection);
	}
	
	$statement->free_result();
	
	// Now we insert into eventdate
	
	$query = $eventdate_query;

	for($i = 0; $i < $repeat + 1; $i++) {
		$date_formatted = "$year-$month-". ($day + $i);
		
		$statement = $connection->prepare($query);
		$statement->bind_param("ss", $inserted_id, $date_formatted);
		$statement->execute();
	}
	$statement->close();
	$connection->close();
	
	return $inserted_id;
}

function invite_user($eid, $pid, $query) {
	global $db_username, $db_passwd, $db_host, $db_name, $username;
	
	//TODO
	// Check to make sure that user matches.
	// Right now, you don't
	
	// Using prepared syntax
	$connection = new mysqli($db_host, $db_username, $db_passwd, $db_name);
	if($connection->connect_errno > 0){
		die('Unable to connect to database [' . $db->connect_error . ']');
	}
	
//	print "pid: $pid\n";
//	exit(0);
	
	// Now, insert event
	$statement = $connection->prepare($query);
	
	//$pid = "";
	$statement->bind_param('ss', $pid, $eid);
	if(!$statement) {
		die($connection->error);
	}
	$statement->execute();
	
	$statement->close();
	$connection->close();
}

?>