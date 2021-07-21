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
require 'ageing.php';

if (isset($_GET["page"])) { $page  = $_GET["page"]; } else { $page=1; };
$page=$mysqli->escape_string($page);
$results_per_page=20;
$start_from = ($page-1) * $results_per_page;

?> <div class="h1"> Job market </div> <?php
?> <hr /> <?php

//get location & work info
$result = $mysqli->query("SELECT * FROM users WHERE username='$username'") or die($mysqli->error());
$row = mysqli_fetch_array($result);
$location = $row['location'];
$workid=$row['workid'];

?> <div class="textbox"> <?php
//calculate multidimensional array results
$sql = "SELECT * FROM companies WHERE countryco='$location' AND joboffer != 'NULL' ORDER BY joboffer DESC LIMIT $start_from, ".$results_per_page;
$rs_result = $mysqli->query($sql);	

?>
<div class="scroll"> 
<table id="table1">
	<tr>
    <th> Salary</th>
    <th> Owner</th>
    <th> Type</th>
    <th> Name</th>
    <th> Region</th>
    <th> Take job</th>
</tr>
<?php
while($row = $rs_result->fetch_assoc()) {
	?> 
           <tr>
	           <td><?php echo $row["joboffer"]; ?></td>
	           <td><?php echo $row["owner"]; ?></td>
	           <td><?php echo $row["type"]; ?></td>
	           <td><?php echo $row["companyname"]; ?></td>
	           <td><?php echo $row["region"]; ?></td>
	           <td>
					<form method="post" action="">
						<input type="hidden" name="id" value="<?php echo $row["id"]; ?>" />
						<input type="hidden" name="joboffer" value="<?php echo $row["joboffer"]; ?>" />
						<button type="submit" name="takejob" />Take job</button>
					</form>
	           </td>
           </tr>
	<?php		
}; 
?>
</table>
</div>
<?php
$sql = "SELECT COUNT(id) AS total FROM companies WHERE countryco='$location'";
$result = $mysqli->query($sql);
$row = $result->fetch_assoc();
$total_pages = ceil($row["total"] / $results_per_page); // calculate total pages with results

for ($i=1; $i<=$total_pages; $i++) {  // print links for all pages
	echo "<a href='jobmarket.php?page=".$i."'";
    if ($i==$page)  echo " class='curPage'";
    echo ">".$i."</a> "; 
};

//setjob to user
if(isset($_POST['takejob'])){
	$compid = $mysqli->escape_string($_POST['id']);
	
	$result = $mysqli->query("SELECT * FROM users WHERE username='$username'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$workid=$row['workid'];
	$location=$row['location'];
	
	$result = $mysqli->query("SELECT * FROM companies WHERE id='$compid'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$compid=$row['id'];
	$countryco=$row['countryco'];
	$salary=$row['joboffer'];
	
	if($workid>0){
		echo'<div class="boxed">You already have a job!</div>';
	}else{
	
		//echo "$compid";
		//echo "$salary";
		if($countryco==$location){
			date_default_timezone_set('UTC');
			$date = date("Y\-m\-d H:i:s");
			
			$sql = "UPDATE users SET workid='$compid', salary='$salary', workstart=NOW() WHERE username='$username'";
			mysqli_query($mysqli, $sql);
			
			//update company database
			$result = $mysqli->query("SELECT * FROM companies WHERE id='$compid'") or die($mysqli->error());
			$row = mysqli_fetch_array($result);
			$position1=$row['position1'];
			$position2=$row['position2'];
			$position3=$row['position3'];
			$position4=$row['position4'];
			$position5=$row['position5'];
			
			if($position1=='free'){
				$sql = "UPDATE companies SET position1='$username', joboffer=null WHERE id='$compid'";
				mysqli_query($mysqli, $sql);
			}else{
				if($position2=='free'){
					$sql = "UPDATE companies SET position2='$username', joboffer=null WHERE id='$compid'";
					mysqli_query($mysqli, $sql);
				}else{
					if($position3=='free'){
						$sql = "UPDATE companies SET position3='$username', joboffer=null WHERE id='$compid'";
						mysqli_query($mysqli, $sql);
					}else{
						if($position4=='free'){
							$sql = "UPDATE companies SET position4='$username', joboffer=null WHERE id='$compid'";
							mysqli_query($mysqli, $sql);
						}else{
							if($position5=='free'){
								$sql = "UPDATE companies SET position5='$username', joboffer=null WHERE id='$compid'";
								mysqli_query($mysqli, $sql);
							}
						}
					}
				}
			}
		}
	}
	?>
	<script>
	    if ( window.history.replaceState ) {
	        window.history.replaceState( null, null, window.location.href );
	    }
	</script>
	<?php
}
?> </div> <?php
?>
</div>
</body>
<footer>
<?php require 'bottombar.php'; ?>
</footer>
</html>
