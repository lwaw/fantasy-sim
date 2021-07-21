<?php 
require 'navigationbar.php';
require 'db.php';
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
  <link rel="stylesheet" href="css/styletot.css">
   <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
   <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
   <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
   <link rel="manifest" href="/site.webmanifest">
   <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#add8e6">
   <meta name="msapplication-TileColor" content="#add8e6">
   <meta name="theme-color" content="#ffffff">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src='https://www.google.com/recaptcha/api.js'></script>
  

</head>

<body>
<div class="boxedtot">
<?php
require 'ageing.php';
//echo $username;
?> <div class="textbox"> <?php
?> <div class="h1"> Trainings compound </div> <?php
?> <hr /> <?php

//get strength info
$result = $mysqli->query("SELECT * FROM users WHERE username='$username'") or die($mysqli->error());
$row = mysqli_fetch_array($result);
$strength = $row['strength'];
echo nl2br ("<div class=\"t1\">Here you can train daily to gain strength. Training will gain you 5 strength each time and will consume 10 energy. For every 60 gain in strength ou will be awarded 5 gold. Your current strength is: $strength</div>");
//echo $row['strength'];

?>
<form method="post" action="">
	<button type="submit" name="train" />Train</button>
	<!-- <div class="g-recaptcha" data-sitekey="6LetFYEUAAAAAE8OKM03o0WWhbqghy2y7UmPc3s8"></div> -->
</form>
<?php
if(isset($_POST['train'])){
    /*	
    $userIP = $_SERVER["REMOTE_ADDR"];
    $recaptchaResponse = $_POST['g-recaptcha-response'];
	echo "$recaptchaResponse";
    $secretKey = $g_secretKey;
    $request = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$secretKey}&response={$recaptchaResponse}&remoteip={$userIP}");

    if(!strstr($request, "true")){
        echo "Failed Verification";
    }
    else{*/
		$result = $mysqli->query("SELECT * FROM users WHERE username='$username'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$strength = $row['strength'];
	
		//get trained info
		$result = $mysqli->query("SELECT * FROM users WHERE username='$username'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$trained = $row['trained'];
	
		//get trainbonus info
		$result = $mysqli->query("SELECT * FROM users WHERE username='$username'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$trainbonus = $row['trainbonus'];
	
		//get energy info
		$result = $mysqli->query("SELECT * FROM users WHERE username='$username'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$energy = $row['energy'];
	
		//get gold info
		$result = $mysqli->query("SELECT * FROM currency WHERE usercur='$username'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$gold = $row['gold']; 
		//echo "gelukt";
		
		if($trained==0){
				 
			//set new strength
			//$energy = $energy-10;
			if($sleepstate=="asleep" || $sleepstate=="neither"){
				//echo'<div class="boxed">You don\'t have enough energy!</div>';
				echo'<div class="boxed">You need to be awake to perform this action!</div>';
			}else{
				//$sql = "UPDATE users SET energy='$energy' WHERE username='$username'";
				//mysqli_query($mysqli, $sql);
							
				//set new strength
				$strength = $strength+5;
				$strength = $mysqli->escape_string($strength);
				$sql = "UPDATE users SET strength='$strength' WHERE username='$username'";
				mysqli_query($mysqli, $sql);
				echo'<div class="boxed">You gained 5 strength!</div>';
					 
				//set trained to 1
				$sql = "UPDATE users SET trained='1' WHERE username='$username'";
				mysqli_query($mysqli, $sql);
				//echo "$strength";
							 
				//set new trainbonus
				$trainbonus = $trainbonus+5;
				$sql = "UPDATE users SET trainbonus='$trainbonus' WHERE username='$username'";
				mysqli_query($mysqli, $sql);
							
				//set bonus gold
				if($trainbonus>=60){
					$gold=$gold+5;
					$sql = "UPDATE currency SET gold='$gold' WHERE usercur='$username'";
					mysqli_query($mysqli, $sql);
					
					$content= "You have gained 60 strength and have been awarded 5 gold";
					$sql = "INSERT INTO events (date, content, extrainfo) " 
				     . "VALUES (NOW(),'$content','$username')";
					mysqli_query($mysqli2, $sql);
								
					//set new trainbonus
					$trainbonus = 0;
					$sql = "UPDATE users SET trainbonus='$trainbonus' WHERE username='$username'";
					mysqli_query($mysqli, $sql);
								
					echo'<div class="boxed">You earned 5 bonus gold!</div>';
				}else{
								
				}
			}
			?>
			<script>
			    if ( window.history.replaceState ) {
			        window.history.replaceState( null, null, window.location.href );
			    }
			</script>
			<?php
				 
		}else{
			echo'<div class="boxed">You have already trained today!</div>';
		}
	//}
			 
			 
}else{
	//echo "niet gelukt";  
}

?> </div> <?php
?>
</div>
</body>
<footer>
<?php require 'bottombar.php'; ?>
</footer>
</html>
