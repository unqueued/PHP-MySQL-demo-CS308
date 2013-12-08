<?php

require("common.inc");

session_start();

$username = $_SESSION['username'];

check_login();

$query = <<<QUERY
select sharer from friend_of where viewer = ?
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
$statement->bind_result($sharer);
$returned = array();
while($statement->fetch()) {
	$returned[] = array(
		"sharer"	=>	$sharer
	);
}

?>
<html>
	<head>
		<title>Schedule for today</title>
	</head>
	<body>
		<h3>View events of friends</h3>
		<p>Query to select events</p>
		<pre style="width: 80%; font-family:monospace; font-size: .75em; border: 1px solid red;">
<?=$query?>

		</pre>
		<p>People who are friends:</p>
		
		<p>Please select which friend's schedule to view:</p>
		<ul>
		
<?php
		foreach($returned as $friend) {
			$friend_str = $friend["sharer"];
?>
			<li><a href="view_friend_schedule.php?view_pid=<?=htmlspecialchars($friend_str)?>"><?=$friend_str?></a></li>
<?php
		}
?>
		</ul>
		
		<a href="logout.php">Logout</a>
		<a href="index.php">Main page</a>
		<p>Session length: <?=total_login_time() ?> seconds</p>
	</body>
</html>