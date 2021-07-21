<?php 
require 'db.php';
require 'regionborders.php';
require_once 'purifier/library/HTMLPurifier.auto.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if user is logged in using the session variable
if ( $_SESSION['logged_in'] != 1 ) {
  $_SESSION['message'] = "You must log in before viewing your profile page!";
  header("location: error.php");    
}
else {
    // Makes it easier to read
    $username = $_SESSION['username'];
    //$last_name = $_SESSION['last_name'];
    $email = $_SESSION['email'];
    $active = $_SESSION['active'];
}

//html purifier setup
$config = HTMLPurifier_Config::createDefault();
$purifier = new HTMLPurifier($config);
$config->set('HTML.SafeIframe', true); //iframes
$config->set('URI.SafeIframeRegexp', '%^(https?:)?//(www\.youtube(?:-nocookie)?\.com/embed/|player\.vimeo\.com/video/)%');
?>

<!DOCTYPE html>

<html>
	
<head>
  <title>Country president</title>
  <link rel="stylesheet" href="css/styletot.css">
</head>
<body>
<?php
// Display message about account verification link only once
if ( isset($_SESSION['message']) ){
	echo $_SESSION['message'];
              
	// Don't annoy the user with more messages upon page refresh
	unset( $_SESSION['message'] );
}
         
$result = mysqli_query($mysqli,"SELECT * FROM users WHERE username='$username'");
$row=mysqli_fetch_array($result);
$nationality=$row["nationality"];
$moderator=$row["moderator"];

$result2 = $mysqli->query("SELECT adtext FROM politicalparty WHERE country='$nationality' AND ad='1'") or die($mysqli->error());
$count = $result2->num_rows;
if($count != 0){
	$result = mysqli_query($mysqli,"SELECT adtext FROM politicalparty WHERE country='$nationality' AND ad='1'");
	
	for ($set=array(); $row=$result->fetch_assoc(); $set[]=$row);
	//print_r($set);
	$r=array_rand($set,1);
	//print_r($set[$r]);
	$win=$set[$r];
	
	foreach($win as $item){
		$winner=$item;	
	}
	$winner2=$mysqli->escape_string($winner);
	$result = $mysqli->query("SELECT * FROM politicalparty WHERE adtext='$winner2'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$id = $row['id'];
	$polname = $row['name'];
	
	$winner = $purifier->purify($winner);
	
	?> <div class="adbox"> <?php
		if($moderator==1 || $moderator==2 || $moderator==5){
			echo "id: $id";
		}
		?> <br /> <?php
		echo "This post is sponsored by: $polname";
		?> <br /> <?php
		echo "$winner";
	?> </div> <?php
}

?>
</body>
</html>
