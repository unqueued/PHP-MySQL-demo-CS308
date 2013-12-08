<?php

require('common.inc');

session_start();

$message = "";

# Message explaining why user must login (if any reason)
if($_SESSION['logout_message']) {
	$message = $_SESSION['logout_message'];
	session_destroy();
	session_start();
}

if(isset($_POST['username']) && isset($_POST['passwd'])) {
	
	$submitted_username = $_POST['username'];
	$submitted_passwd = $_POST['passwd'];
	
	$connection = new mysqli("localhost", "root", "passme123", "cs308project_part2");
	
	if($connection->connect_errno > 0){
		die('Unable to connect to database [' . $db->connect_error . ']');
	}
	
	$query = <<<QUERY
SELECT fname, lname FROM person WHERE pid = ? AND passwd = MD5(?)
QUERY;
	
	$statement = $connection->prepare($query);
	
	$statement->bind_param('ss', $submitted_username, $submitted_passwd);
	
	if(!$statement) {
		die("Failed to create object $statement\n");
	}
	
	$statement->execute();
	$statement->bind_result($fname, $lname);
	
	$statement->fetch();
	
	// Check to see if a row was returned
	if(isset($fname) && isset($lname)) {
		//echo "Welcome $fname $lname\n";
		$_SESSION['fname'] = $fname;
		$_SESSION['lname'] = $lname;
		$_SESSION['username'] = $submitted_username;
		$username = $submitted_username;
		$_SESSION['login_time'] = time();
		$_SESSION['last_activity'] = $_SESSION['login_time'];
		$_SESSION['dprivacy'] = get_privacy();
		header("Location: index.php");
	} else {
		$message = "Invalid username or password";
	}
	
	$statement->free_result();
}

?>
<html>
	<head>
		<title><?=$message ?></title>
	</head>
	<body>
		<p><?=$message ?></p>
		<form name="login" action="" method="post">
			<input type="text" name="username" />
			<br />
			<input type="password" name="passwd" />
			<br />
			<input type="submit" value="Submit" />
		</form>
	</body>
</html>

<?php

function get_privacy() {
	global $db_username, $db_passwd, $db_host, $db_name, $username;
	
$query = <<<QUERY
	select d_privacy from person where pid = ?
QUERY;
	
	//print "db_username: $db_username";
	
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
?>