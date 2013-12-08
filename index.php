<?php

require('common.inc');

session_start();

$username = $_SESSION['username'];

//print_r($_SESSION);

check_login();

?>
<html>
	<head>
		<title></title>
	</head>
	<body>
		<h3>Welcome, <?=htmlspecialchars($_SESSION['fname']. " ($username)")?></h3>
		<h4>Select task</h4>
		<ul>
			<li><a href="schedule_for_today.php">View my schedule for today</a></li>
			<li><a href="view_my_schedule.php">View my schedule</a></li>
			<li><a href="all_events_ive_organized.php">View events I've organized</a></li>
			<li><a href="view_answer_invites.php">View/answer pending invitations</a></li>
			<li><a href="organize_event.php">Organize an event</a></li>
			<li><a href="issue_invitations.php">Issue invitations</a></li>
			<li><a href="select_friend_to_view_events.php">View friend's schedule<a/></li>
		</ul>
		<hr />
		<br />
		
		<ul>
			<li><a href="all_events_for_user.php">All events for user</a></li>
		</ul>
		
		<hr />
		<br />
		<a href="logout.php">Logout</a>
		<p>Session length: <?=total_login_time() ?> seconds</p>
	</body>
</html>
