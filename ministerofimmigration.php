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
require 'ageing.php';

$result = $mysqli->query("SELECT * FROM users WHERE username='$username'") or die($mysqli->error());
$row = mysqli_fetch_array($result);
$nationality=$row['nationality'];

$result = $mysqli->query("SELECT * FROM countryinfo WHERE country='$nationality'") or die($mysqli->error());
$row = mysqli_fetch_array($result);
$immigration=$row['immigration'];
//$gold=$row['gold'];
//$vat=$row['vat'];
//$worktax=$row['worktax'];
$nodecisions=$row['nodecisions'];
$moneycreation=$row['moneycreation'];

$maxnodecisions=15;
$maxmoneycreation=1000;
$napcost=5;
$warcost=0;

if($immigration==$username){
	?> <div class="textbox"> <?php
	echo nl2br("The max number of decisions to make is $maxnodecisions per term. $nodecisions already taken this term. \n");
	echo nl2br("The max number of money to change is $maxmoneycreation per term. $moneycreation already used. \n");
	?> </div> <?php
	
?> <div class="textbox"> <?php
//change immigration tax
?>
<form method="post" action="">
	<label for="changetax">New immigration tax:</label>
	<input type="number" size="25" required autocomplete="off" id="changetax" name="changetax" min="1" max="99" step="0.01" />
	<button type="submit" name="tax"  /><?php echo "Change immigration tax"; ?></button>
</form>
<?php
if(isset($_POST['tax'])){
	$result = $mysqli->query("SELECT * FROM countryinfo WHERE country='$nationality'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$nodecisions=$row['nodecisions'];
	$changetax = $mysqli->escape_string($_POST['changetax']);
	$changetax = (int) $changetax;
	if($changetax > 100){
		$changetax = 100;
	}elseif($changetax < 0){
		$changetax = 0;
	}
			
	if($nodecisions<$maxnodecisions){
		$nodecisions=$nodecisions+1;
		
		$sql = "UPDATE countryinfo SET nodecisions ='$nodecisions' WHERE country='$nationality'";
		mysqli_query($mysqli, $sql);
		
		$sql = "INSERT INTO congress (type, country, start, extraint) " 
		. "VALUES ('immigrationtax','$nationality',NOW(),'$changetax')";
 		mysqli_query($mysqli, $sql);
		echo'<div class="boxed">Done!</div>';
		
		?>
		<script>
		    if ( window.history.replaceState ) {
		        window.history.replaceState( null, null, window.location.href );
		    }
		</script>
		<?php
	}else{
		echo'<div class="boxed">Country doesn\'t have enough decisions left!</div>';
		
		?>
		<script>
		    if ( window.history.replaceState ) {
		        window.history.replaceState( null, null, window.location.href );
		    }
		</script>
		<?php
	}
}
?> </div> <?php

?> <div class="textbox"> <?php
//aanmeldingen bekijken
$result = $mysqli->query("SELECT * FROM immigration WHERE tocountry='$nationality'") or die($mysqli->error());
for ($set=array(); $row=$result->fetch_assoc(); $set[]=$row);
//create forms for every item
foreach ($set as $key => $value){
	?> <div class="listbox"> <?php
	$id[$key] = $value['id'];
	$immigrant[$key] = $value['immigrant'];
	$message[$key] = $value['message'];
	
	echo "Immigrant: $immigrant[$key]";
	echo nl2br(" \n");
	echo "Message: $message[$key]";
	
	?>
	<form method="post" action="">
		<input type="hidden" name="id" value="<?php echo "$id[$key]"; ?>" />
		<input type="hidden" name="immigrant" value="<?php echo "$immigrant[$key]"; ?>" />
		<button type="submit" name="accept" /><?php echo "Accept"; ?></button>
   		<button type="submit" name="reject" /><?php echo "Reject"; ?></button>
	</form>   		
	<?php		
	?> </div> <?php
}

if(isset($_POST['accept'])){
	$id = $mysqli->escape_string($_POST['id']);
	$id = (int) $id;
	
	$result = $mysqli->query("SELECT * FROM immigration WHERE id='$id' AND tocountry='$nationality'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$immigrant=$row['immigrant'];
	
	$result = $mysqli->query("SELECT immigrationtax FROM countryinfo WHERE country='$nationality'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$immigrationtax=$row['immigrationtax'];
	
	$result = $mysqli->query("SELECT gold FROM currency WHERE usercur='$immigrant'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$gold=$row['gold'];
	$gold=$gold-$immigrationtax;
	
	if($gold>=0){
		$result = $mysqli->query("SELECT gold FROM countryinfo WHERE country='$nationality'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$countrygold=$row['gold'];
		
		$countrygold=$countrygold+$immigrationtax;
		
		$sql = "UPDATE countryinfo SET gold='$gold' WHERE country='$nationality'";
		mysqli_query($mysqli, $sql);
		
		$sql = "UPDATE currency SET gold='$gold' WHERE usercur='$immigrant'";
		mysqli_query($mysqli, $sql);
		
		$sql = "UPDATE users SET nationality='$nationality' WHERE username='$immigrant'";
		mysqli_query($mysqli, $sql);
		
		$sql = "DELETE FROM immigration WHERE id='$id'";
		mysqli_query($mysqli, $sql); 
	}else{
		echo "User doesn't have enough gold!";
	}
}
if(isset($_POST['reject'])){
	$id = $mysqli->escape_string($_POST['id']);
	$immigrant = $mysqli->escape_string($_POST['immigrant']);
	
	$sql = "DELETE FROM immigration WHERE id='$id'";
	mysqli_query($mysqli, $sql); 
}

?> </div> <?php

}else{
	echo "You are not minister of immigration!";
}

?>
</div>
</body>
<footer>
<?php require 'bottombar.php'; ?>
</footer>
</html>
