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

//get location info
$result = $mysqli->query("SELECT location, location2 FROM users WHERE username='$username'") or die($mysqli->error());
$row = mysqli_fetch_array($result);
$location = $row['location'];
$location2 = $mysqli->escape_string($row['location2']);

//select currency country info
$result = $mysqli->query("SELECT * FROM countryinfo WHERE country='$location'") or die($mysqli->error());
$row = mysqli_fetch_array($result);
$currency=$row['currency'];

//get user money info
$result = $mysqli->query("SELECT * FROM currency WHERE usercur='$username'") or die($mysqli->error());
$row = mysqli_fetch_array($result);
$money=$row[$currency];

echo nl2br ("<div class=\"h1\">Marketplace of $location2</div>");
?> <hr /> <?php

//show offers
$type = "tavern";
$location2=$mysqli->escape_string($location2);

$sql = "SELECT * FROM companies WHERE type='$type' AND region='$location2' AND rooms != '0' AND price != '0' ORDER BY price ASC LIMIT $start_from, ".$results_per_page;
$rs_result = $mysqli->query($sql);
//print_r($set);

?> 
<div class="scroll">
<table id="table1">
	<tr>
    <th> Amount</th>
    <th> Price</th>
    <th> Owner</th>
    <th> Rent</th>
</tr>
<?php
while($row = $rs_result->fetch_assoc()) {
	?> 
   <tr>
       <td><?php echo $row["rooms"]; ?></td>
       <td><?php echo $row["price"]; ?></td>
       <td><?php echo $row["owner"]; ?></td>
       <td>
			<form method="post" action="">
				<input type="hidden" name="id" value="<?php echo $row["id"]; ?>" />
				<input type="hidden" name="amount" value="<?php echo $row["amount"]; ?>" />
				<input type="hidden" name="type" value="<?php echo $row["type"]; ?>" />
				<input type="hidden" name="region" value="<?php echo $row["region"]; ?>" />
				<input type="hidden" name="price" value="<?php echo $row["price"]; ?>" />
				<input type="hidden" name="owner" value="<?php echo $row["owner"]; ?>" />
				<button type="submit" name="buy" />Rent</button>
			</form>
       </td>
   </tr>
	<?php		
}; 
?>
</table>
</div>
<?php

$sql = "SELECT COUNT(id) AS total FROM companies WHERE type='$type' AND region='$location2'";
$result = $mysqli->query($sql);
$row = $result->fetch_assoc();
$total_pages = ceil($row["total"] / $results_per_page); // calculate total pages with results

for ($i=1; $i<=$total_pages; $i++) {  // print links for all pages
	echo "<a href='tavern.php?page=".$i."'";
    if ($i==$page)  echo " class='curPage'";
    echo ">".$i."</a> "; 
};


//buy items
if(isset($_POST['buy'])){
	$id = $mysqli->escape_string($_POST['id']);
	$id = (int) $id;
	
	$result = $mysqli->query("SELECT * FROM companies WHERE id='$id' AND type='tavern'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$amount=$row['rooms'];
	$type=$row['type'];
	$owner=$row['owner'];
	$region=$row['region'];
	$region = $mysqli->escape_string($region);
	$price=$row['price'];
	
	$nobuyitems = 1;
	
	$result = $mysqli->query("SELECT * FROM users WHERE username='$username'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$location2 = $mysqli->escape_string($row['location2']);
	
	//select today tax from region
	$result = $mysqli->query("SELECT taxtoday FROM region WHERE name='$location2'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$taxtoday = $row['taxtoday'];
	
	$result = $mysqli->query("SELECT * FROM countryinfo WHERE country='$location'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$cur = $row['currency'];
	$vat = $row['vat'];
	$countrymoney = $row['money'];
	
	$result = $mysqli->query("SELECT * FROM currency WHERE usercur='$username'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$money = $row[$cur];
	
	if($region==$location2){
		$price=$price*$nobuyitems;
		$moneyafter=$money-$price;
		
		if($moneyafter>=0){
			$itemafter=$amount-$nobuyitems;
			if($itemafter>=0){
				//update buyer and country
				$result = $mysqli->query("SELECT tavern FROM users WHERE username='$username'") or die($mysqli->error());
				$row = mysqli_fetch_array($result);
				$tavernuse = $row['tavern'];
				
				if($tavernuse==0){								
					$tax=$price*($vat/100);
					$countrymoney=$countrymoney+$tax;
					
					$taxtoday=$taxtoday+$tax;
					
					$sql = "UPDATE countryinfo SET money ='$countrymoney' WHERE country='$location'";
					mysqli_query($mysqli, $sql);
					
					$sql = "UPDATE region SET taxtoday ='$taxtoday' WHERE name='$location2'";
					mysqli_query($mysqli, $sql);
					
					$sql = "UPDATE currency SET $cur ='$moneyafter'WHERE usercur='$username'";
					mysqli_query($mysqli, $sql);
					
					$sql = "UPDATE inventory SET $type ='$itemafter'WHERE userinv='$username'";
					mysqli_query($mysqli, $sql);
					
					$sql = "UPDATE users SET tavern ='1'WHERE username='$username'";
					mysqli_query($mysqli, $sql);
					
					//update owner
					$result = $mysqli->query("SELECT * FROM currency WHERE usercur='$owner'") or die($mysqli->error());
					$row = mysqli_fetch_array($result);
					$moneyowner = $row[$cur];
					
					$moneyowner=$moneyowner+$price-$tax;
					
					$sql = "UPDATE currency SET $cur ='$moneyowner' WHERE usercur='$owner'";
					mysqli_query($mysqli, $sql);
					
					$sql = "UPDATE companies SET rooms ='$itemafter' WHERE id='$id'";
					mysqli_query($mysqli, $sql);
					
					echo'<div class="boxed">You slept in a tavern!</div>';
				}else{
					echo nl2br ("<div class=\"boxed\">You already used a tavern or house today!</div>");
				}
			}else{
				echo nl2br ("<div class=\"boxed\">Not enough $type for sale!</div>");
			}
		}else{
			echo nl2br ("<div class=\"boxed\">You don't have enough $cur</div>");
		}
	}else{
		echo nl2br ("<div class=\"boxed\">You are in the wrong country!</div>");
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
