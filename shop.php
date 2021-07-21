<?php 
require 'navigationbar.php';
require 'db.php';
require 'regionborders.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/* Displays user information and some useful messages */
//session_start();

// Check if user is logged in using the session variable
if ( $_SESSION['logged_in'] != 1 ) {
  $_SESSION['message'] = "You must log in before viewing your profile page!";
  header("location: error.php");    
}
else {
    // Makes it easier to read
    $username = $_SESSION['username'];
    $last_name = $_SESSION['last_name'];
    $email = $_SESSION['email'];
    $active = $_SESSION['active'];
}
?>

<!DOCTYPE html>

<html>
	
<head>
  <title>Fantasy-Sim</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/styletot.css">
   <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
   <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
   <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
   <link rel="manifest" href="/site.webmanifest">
   <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#add8e6">
   <meta name="msapplication-TileColor" content="#add8e6">
   <meta name="theme-color" content="#ffffff">
  

  
</head>

<body>

<div class="boxedtot">
<?php

echo nl2br ("<div class=\"h1\">Shop</div>");
?> <hr /> <?php

$result = $mysqli->query("SELECT * FROM shop WHERE username='$username'") or die($mysqli->error());
$row = mysqli_fetch_array($result);
$lid=$row['id'];
$trial=$row['trial'];
$game=$row['game'];

date_default_timezone_set('UTC'); //current date
$datecur = date("Y-m-d H:i:s"); 

if($datecur < $trial AND $game == 0){
	//echo nl2br("<div class=\"t1\">Your trial is valid until $trial</div>");
}elseif($game == 1){
	echo nl2br("<div class=\"t1\">Thanks for buying the game</div>");
}elseif($datecur > $trial AND $game == 0){
	//echo nl2br("<div class=\"t1\">Your trial has ended, please buy the game</div>");
}

if($game == 0){
	echo nl2br("<div class=\"t1\">Content will arrive soon</div>");
}

?>
</div>
</body>
<footer>
<?php require 'bottombar.php'; ?>
</footer>
</html>
