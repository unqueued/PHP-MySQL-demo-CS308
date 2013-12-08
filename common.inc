<?php

$db_host = "localhost";
$db_username = "root";
$db_passwd = "passme123";
$db_name = "cs308project_part3";

# number of seconds of inactivity before terminating a session
$inactivity_timeout = 600;

$invitation_query = <<<QUERY
INSERT INTO invited (pid, eid, response, visibility)
VALUES (
	?,
	?,
	0,
	(select d_privacy from person where pid = ?)
);
QUERY;

$verify_organizer_query = <<<QUERY
select pid from event where eid = ?
QUERY;

$update_event_query = <<<QUERY
UPDATE invited
SET response = ?, visibility = ?
WHERE invited.pid = ? AND invited.eid = ?;
QUERY;

# This function inserts a tab into every row of a blob of text
function entab($input, $i) {	
	$output = "";
	
	$input = explode("\n", $input);
	
	foreach($input as $line) {
		$output .= str_repeat("\t", $i) . $line . "\n";
	}
	
	return $output;
}

function logout($message) {
	if(isset($message)) {
		//print "Logout message: $message\n";
	}
	//print "Logging out";
	$_SESSION['logout_message'] = $message;
	$_SESSION['username'] = "";
	header("Location: login.php");
	exit(0);
}

// Checks to make sure that this is a valid session
function check_login() {
	global $inactivity_timeout;
	
	if(seconds_since_activity() > $inactivity_timeout) {
		logout("Logged out due to inactivty");
	}
	
	if(!(isset($_SESSION['username']) && $_SESSION['username'] != "")) {
		logout("");
		exit(0);
	}
	return;
}

// Returns the total number of seconds since function was called
function seconds_since_activity() {
	//print time() - $_SESSION['last_activity'];
	
	// Check time since last activity
	$total_time = time() - $_SESSION['last_activity'];
	// Update total logged intime
	$_SESSION['last_activity'] = time();
	
	//print "total time: $total_time\n";
	
	return $total_time;
}

function total_login_time() {
	return time() - $_SESSION['login_time'];
}

function array_to_table($results) {

	$return = "";
	
	$return .= "<table border=\"1\">\n";
	
	if(count($results > 0)) {	
		$return .= "\t<tr>\n";
		# Print the table header
		foreach(array_keys($results[0]) as $key) {
			$return .= "\t\t<th>". htmlspecialchars($key). "</th>\n";
		}
		$return .= "\t</tr>\n";
		
		# and now, each row
		foreach($results as $row) {
			# and each element
			$return .= "\t<tr>\n";
			foreach($row as $i) {
				$return .= "\t\t<td>". htmlspecialchars($i). "</td>\n";
			}
			$return .= "\t</tr>\n";
		}

	}
	$return .= "</table>\n";
	
	return $return;
}

function date_picker($name, $startyear=NULL, $endyear=NULL)
{
    if($startyear==NULL) $startyear = date("Y")-100;
    if($endyear==NULL) $endyear=date("Y")+50; 

    $months=array('','January','February','March','April','May',
    'June','July','August', 'September','October','November','December');

    // Month dropdown
    $html="<select name=\"".$name."month\">\n";
    
    for($i=1;$i<=12;$i++)
    {
    	$month_number = sprintf("%02s", $i);
       $html.="\t<option value='$month_number'>$month_number ($months[$i])</option>\n";
    }
    $html.="</select> \n";
   
    // Day dropdown
    $html.="<select name=\"".$name."day\">\n";
    for($i=1;$i<=31;$i++)
    {
    	$day_number = sprintf("%02s", $i);
       $html.="\t<option $selected value='$day_number'>$day_number</option>\n";
    }
    $html.="</select> \n";

    // Year dropdown
    $html.="<select name=\"".$name."year\">\n";

    for($i=$startyear;$i<=$endyear;$i++)
    {      
      $html.="\t<option value='$i'>$i</option>\n";
    }
    $html.="</select> \n";

    return $html;
}

function verify_organizer($eid, $organizer) {
	global $verify_organizer_query, $db_username, $db_passwd, $db_host, $db_name, $username;
	// Using prepared syntax
	$connection = new mysqli($db_host, $db_username, $db_passwd, $db_name);
	if($connection->connect_errno > 0){
		die('Unable to connect to database [' . $db->connect_error . ']');
	}
	
	// Now, insert event
	$statement = $connection->prepare($verify_organizer_query);
	if(!$statement) {
		die($connection->error);
	}
	$statement->bind_param('s', $eid);
	$statement->execute();
	
	$statement->bind_result($pid);
	$statement->fetch();
	if($pid != $organizer) {
		logout("Intrusion attempt detected");
	}
	//print_r($statement);
	//print_r($connection);
//	print_r($pid);
	
	return false;
}

function invite_users($eid, $pids, $query) {
	global $db_username, $db_passwd, $db_host, $db_name, $username;
	
	// Using prepared syntax
	$connection = new mysqli($db_host, $db_username, $db_passwd, $db_name);
	if($connection->connect_errno > 0){
		die('Unable to connect to database [' . $db->connect_error . ']');
	}
	
	// Now, insert event
	$statement = $connection->prepare($query);
	
	$pid = "";
	$statement->bind_param('sis', $pid, $eid, $pid);
	if(!$statement) {
		die($connection->error);
	}
	foreach($pids as $pid) {
		$statement->execute();
	}
	
	$statement->close();
	$connection->close();
}

?>