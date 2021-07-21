<?php 
require 'navigationbar.php';
require 'db.php';
require 'regionborders.php';
require 'functions.php';
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
    //$last_name = $_SESSION['last_name'];
    $email = $_SESSION['email'];
    $active = $_SESSION['active'];
	if(isset($_SESSION['usercharacterid'])){  $usercharacterid = $_SESSION['usercharacterid'];}else{$usercharacterid=0;}
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
<?php require 'advertisement.php'; ?>
<div class="boxedtot">
<?php
require 'ageing.php';

if (isset($_GET["page"])) { $page  = $_GET["page"]; } else { $page=1; };
$page=$mysqli->escape_string($page);
$results_per_page=5;
$start_from = ($page-1) * $results_per_page;

$result2 = $mysqli2->query("SELECT * FROM messages WHERE recipient='$username' AND `read`='0'") or die($mysqli->error());
$count = $result2->num_rows;
if($count != 0){
	echo'<div class="boxed">You have unread message(s)</div>';
}

//update lastonline
$sql = "UPDATE users SET lastonline = NOW(), inactive='0' WHERE username='$username'";
mysqli_query($mysqli, $sql);

$sql = "UPDATE characters SET lastonline = NOW() WHERE id='$usercharacterid'";
mysqli_query($mysqli, $sql);

echo nl2br ("<div class=\"h1\">Home</div>");
?> <hr /> <?php

?>
<div id = "dailytask" style = "width: 70%; font-size: 100%; text-indent: 5px;">
<?php
	$result = $mysqli->query("SELECT * FROM users WHERE username='$username'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$trained = $row['trained'];
	$worked = $row['worked'];
	$dueled = $row['dueled'];
	$spread = $row['spread'];
	
	if($trained==0 || $worked == 0 || $dueled==0 || $spread==0){
		echo "Daily tasks";
	}
	
	?>
	<div class="textbox">
		<form method="post" action="">
			<?php if($trained==0){?><button type="submit" name="dailyform" value="train" />Train</button><?php } ?>
			<?php if($worked==0){?><button type="submit" name="dailyform" value="work" />Work</button><?php } ?>
			<?php if($dueled==0){?><button type="submit" name="dailyform" value="duel" />Duel</button><?php } ?>
			<?php if($spread==0){?><button type="submit" name="dailyform" value="spread" />Spread religion</button><?php } ?>
			<?php if($sleepstate=="awake" || $sleepstate=="neither"){?><button type="submit" name="dailyform" value="sleep" />Sleep</button><?php } ?>
		</form>
	</div>
	<?php
	
	if(isset($_POST['dailyform'])){
		$type = $mysqli->escape_string($_POST['dailyform']);
		
		if($type=="train"){
			?>
			<script>
			  location.replace("trainingground.php")
			</script>
			<?php
		}elseif($type=="work"){
			?>
			<script>
			  location.replace("work.php")
			</script>
			<?php
		}elseif($type=="duel"){
			?>
			<script>
			  location.replace("account.php?user=<?php echo $username; ?>")
			</script>
			<?php
		}elseif($type=="spread"){
			?>
			<script>
			  location.replace("religion.php")
			</script>
			<?php
		}elseif($type=="sleep"){
			?>
			<script>
			  location.replace("account.php?user=<?php echo $username; ?>")
			</script>
			<?php
		}
	}
?>	
</div>
<?php

?> <div class="homebox"> <?php

?> <div class="homebox2"> <?php
$sql = "SELECT * FROM events WHERE extrainfo IS NULL OR extrainfo='$username' ORDER BY date DESC LIMIT $start_from, ".$results_per_page;
$rs_result = $mysqli2->query($sql);

?> 
<div class="scroll">
<table id="table1">
<tr>
    <th> Events</th>
</tr>
<?php 
while($row = $rs_result->fetch_assoc()) {
	$date=$row["date"];
	$content=$row["content"];
	?> 
           <tr>
           <td>
           		<?php echo "date: $date"; ?>
           		<br />
           		<?php echo "$content"; ?>
           </td>
           </tr>
	<?php 
	
}; 
?> 
</table>
</div>
<?php 

$sql = "SELECT COUNT(id) AS total FROM events WHERE extrainfo IS NULL OR extrainfo='$username'";
$result = $mysqli2->query($sql);
$row = $result->fetch_assoc();
$total_pages = ceil($row["total"] / $results_per_page); // calculate total pages with results	

for ($i=1; $i<=$total_pages; $i++) {  // print links for all pages
            echo "<a href='home.php?page=".$i."'";
            if ($i==$page)  echo " class='curPage'";
            echo ">".$i."</a> "; 
};

?> </div> <?php

?> <div class="homebox2"> <?php
$results_per_page2=10;
$start_from2=0;
$sql = "SELECT * FROM topics ORDER BY lastreply DESC LIMIT $start_from2, ".$results_per_page2;
$rs_result = $mysqli2->query($sql);

?> 
<div class="scroll">
<table id="table1">
<tr>
    <th> Topics</th>
</tr>
<?php 
while($row = $rs_result->fetch_assoc()) {
	$subject=$row["subject"];
	$category=$row["category"];
	$id=$row["id"];
	?> 
           <tr>
           <td>
           		<?php echo "<a href='forum.php?category=$category&topic=$id'>$subject </a>";?>
           </td>
           </tr>
	<?php 
	
}; 
?> 
</table>
</div>
<?php 
?> </div> <?php


?> </div> <?php

?>
</div>
</body>
<footer>
<?php require 'bottombar.php'; ?>
</footer>
</html>
