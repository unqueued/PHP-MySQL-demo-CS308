<?php

require("common.inc");

session_start();

$username = $_SESSION['username'];

check_login();

if(isset($_POST['startmonth'])) {
	$query = <<<QUERY
select invited.pid, start_time, duration, description, edate
from invited, event, eventdate
where invited.eid = event.eid and eventdate.eid = invited.eid and
invited.pid = ? and response = '1' and edate >= ? and edate <= ?
QUERY;
	
	$connection = new mysqli($db_host, $db_username, $db_passwd, $db_name);
	if($connection->connect_errno > 0){
		die('Unable to connect to database [' . $db->connect_error . ']');
	}
	$statement = $connection->prepare($query);
	
	$startdate	=	"$_POST[startyear]-$_POST[startmonth]-$_POST[startday]";
	$enddate	=	"$_POST[endyear]-$_POST[endmonth]-$_POST[endday]";
	$statement->bind_param('sss', $username, $startdate, $enddate);
	if(!$statement) {
		die($connection->error);
	}
	
	$statement->execute();
	$statement->bind_result($pid, $start_time, $duration, $description, $edate);
	
	$returned = array();
	while($statement->fetch()) {
		$returned[] = array(
			"pid"			=>	$pid,
			"start_time"	=>	$start_time,
			"duration"		=>	$duration,
			"description"	=>	$description,
			"edate"			=>	$edate
		);
	}
	
	$table = array_to_table($returned);
	
	$table = entab($table, 2);

}

?>
<html>
	<head>
		<title>View my schedule</title>
	</head>
	<body>
		<h3>View my schedule</h3>
<?php if(isset($query)): ?>
		<p>Query to select rows for your schedule</p>
		<pre style="width: 80%; font-family:monospace; font-size: .75em; border: 1px solid red;">
<?=$query?>

		</pre>
<?php endif; ?>
<?php if(isset($table)): ?>
		<p>All events that you have organized between</p>
		<p><?php print "$_POST[startyear]-". "$_POST[startmonth]-". "$_POST[startday]" ?>
		and 
		<?php print "$_POST[endyear]-". "$_POST[endmonth]-". "$_POST[endday]" ?></p>

<?php print($table) ?>
<?php endif;?>
		<p>Please select date range</p>
		<form action="<?=basename(__FILE__) ?>" method="post">
		<p>From:</p>
<?php print entab(date_picker("start", $startyear = 2011, $endyear = 2014), 2); ?>
		<br />
		<p>To:</p>
<?php print entab(date_picker("end", $startyear = 2011, $endyear = 2014), 2); ?>
		<br />
		<input type="submit" value="Submit" /><br />
		</form>
		<a href="logout.php">Logout</a>
		<a href="index.php">Main page</a>
		<p>Session length: <?=total_login_time() ?> seconds</p>
	</body>
</html>
