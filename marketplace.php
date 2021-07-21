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

if (isset($_GET["show"])) { $show  = $_GET["show"]; } else { $show=0; };
if (isset($_GET["searchtype"])) { $searchtype  = $_GET["searchtype"]; } else { $searchtype=NULL; };
$searchtype=$mysqli->escape_string($searchtype);
$show=$mysqli->escape_string($show);

//get location info
$result = $mysqli->query("SELECT * FROM users WHERE username='$username'") or die($mysqli->error());
$row = mysqli_fetch_array($result);
$location = $row['location'];

//select currency country info
$result = $mysqli->query("SELECT * FROM countryinfo WHERE country='$location'") or die($mysqli->error());
$row = mysqli_fetch_array($result);
$currency=$row['currency'];

//get user money info
$result = $mysqli->query("SELECT * FROM currency WHERE usercur='$username'") or die($mysqli->error());
$row = mysqli_fetch_array($result);
$money=$row[$currency];

echo nl2br ("<div class=\"h1\">Marketplace of $location</div>");
?> <hr /> <?php

?>
 <form method="post" action="">      
    <button type="submit" name="sellform" />Sell items</button>
    <button type="submit" name="showform" />Show your offers</button>
    <button type="submit" name="buyform" />Buy items</button>
 </form>
<?php

//put offer on market
if(isset($_POST['sellform'])){
	?>
	<form method="post" action="">
		<select name="type" type="text">
			<option value="rawfood">food raw</option>
	 		<option value="rawweapon">weapon raw</option>
	 		<option value="rawhouse">rawhouse</option>
	 		<option value="paper">paper</option>
	 		<option value="rawhospital">rawhospital</option>
	  		<option value="weaponq1">weapon q1</option>
	  		<option value="weaponq2">weapon q2</option>
	  		<option value="weaponq3">weapon q3</option>
	  		<option value="weaponq4">weapon q4</option>
	  		<option value="weaponq5">weapon q5</option>
	  		<option value="foodq1">food q1</option>
	  		<option value="foodq2">food q2</option>
	  		<option value="foodq3">food q3</option>
	  		<option value="foodq4">food q4</option>
	  		<option value="foodq5">food q5</option>
	  		<option value="house">house</option>
	  		<option value="book">books</option>
	  		<option value="hospital">hospital</option>
	   	</select>
	   	<label for="nosellitems">Number of items:</label>
	   	<input type="number" size="25" required autocomplete="off" id="nosellitems" name='nosellitems' min="1" />
		<label for="priceitems:">Price per item:</label>
		<input type="number" size="25" required autocomplete="off" step="0.01" id="priceitem" name='sellprice' min="0.01" />
		<button type="submit" name="sellitems" />Sell items</button>
	</form>
	<?php
	
	?>
	<script>
	    if ( window.history.replaceState ) {
	        window.history.replaceState( null, null, window.location.href );
	    }
	</script>
	<?php
}

if(isset($_POST['sellitems'])){
	$selltype = $mysqli->escape_string($_POST['type']);
	$nosellitems = $mysqli->escape_string($_POST['nosellitems']);
	$nosellitems = (int) $nosellitems;
	$itemprice = $mysqli->escape_string($_POST['sellprice']);
	$itemprice = (double) $itemprice;
	$itemprice = round($itemprice, 2);
	if($nosellitems <= 0 || $itemprice <= 0){
		$nosellitems == 1;
		$itemprice == 1;
	}
		
	$result = $mysqli->query("SELECT $selltype FROM inventory WHERE userinv='$username'") or die($mysqli->error());
	$row = mysqli_fetch_assoc($result);
	$noitems=$row[$selltype];
	echo "$noitems";
	
	$result = $mysqli->query("SELECT nationality, location FROM users WHERE username='$username'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$nationality=$row['nationality'];
	$location=$row['location'];
	
	$result = $mysqli->query("SELECT id FROM diplomacy WHERE country1='$location' AND country2='$nationality' AND type='boycot'") or die($mysqli->error());
	if($result->num_rows > 0){
		echo'<div class="boxed">The country where you are trying to sell has a boycot against your country!</div>';
	}else{
		//number of items after sell >=0
		$noitemsafter=$noitems-$nosellitems;
		
		if($noitemsafter>=0){
			$sql = "UPDATE inventory SET $selltype='$noitemsafter' WHERE userinv='$username'";
			mysqli_query($mysqli, $sql);
			
			$sql = "INSERT INTO marketplace (owner, country, amount, price, type) " 
	            . "VALUES ('$username','$location','$nosellitems', '$itemprice', '$selltype')";
			mysqli_query($mysqli, $sql);
			
			echo nl2br ("<div class=\"boxed\">Done!</div>");
		}else{
			echo nl2br ("<div class=\"boxed\">You don't have enough $selltype</div>");
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

//remove own offer
if(isset($_POST['showform']) || $show == 1){
	$sql = "SELECT id, country, amount, price, type FROM marketplace WHERE owner='$username' ORDER BY type ASC LIMIT $start_from, ".$results_per_page;
	$rs_result = $mysqli->query($sql);	
	
	?> 
	<div class="scroll">
	<table id="table1">
		<tr>
	    <th> Type</th>
	    <th> Amount</th>
	    <th> Price</th>
	    <th> Market</th>
	    <th> Remove from market</th>
	</tr>
	<?php
	while($row = $rs_result->fetch_assoc()) {
		?> 
       <tr>
           <td><?php echo $row["type"]; ?></td>
           <td><?php echo $row["amount"]; ?></td>
           <td><?php echo $row["price"]; ?></td>
           <td><?php echo $row["country"]; ?></td>
           <td>
				<form method="post" action="">
					<input type="hidden" name="id" value="<?php echo $row["id"]; ?>" />
					<input type="hidden" name="amount" value="<?php echo $row["amount"]; ?>" />
					<input type="hidden" name="type" value="<?php echo $row["type"]; ?>" />
					<button type="submit" name="remove" />Remove offer</button>
				</form>
           </td>
       </tr>
		<?php		
	}; 
	?>
	</table>
	</div>
	<?php
	$sql = "SELECT COUNT(id) AS total FROM marketplace WHERE owner='$username'";
	$result = $mysqli->query($sql);
	$row = $result->fetch_assoc();
	$total_pages = ceil($row["total"] / $results_per_page); // calculate total pages with results
	
	for ($i=1; $i<=$total_pages; $i++) {  // print links for all pages
		echo "<a href='marketplace.php?show=1&page=".$i."'";
	    if ($i==$page)  echo " class='curPage'";
	    echo ">".$i."</a> "; 
	};
	?>
	<script>
	    if ( window.history.replaceState ) {
	        window.history.replaceState( null, null, window.location.href );
	    }
	</script>
	<?php
}

if(isset($_POST['remove'])){
	$id = $mysqli->escape_string($_POST['id']);
	$id = (int) $id;
	
	$result = $mysqli->query("SELECT * FROM marketplace WHERE id='$id' AND owner='$username'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$amount=$row['amount'];
	$type=$row['type'];
	echo "$type";

	$result = $mysqli->query("SELECT $type FROM inventory WHERE userinv='$username'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$number = $row[$type];
	$number=$number+$amount;
	echo "$number";
	$sql = "UPDATE inventory SET $type ='$number' WHERE userinv='$username'";
	mysqli_query($mysqli, $sql);
	
	$sql = "DELETE FROM marketplace WHERE id='$id'";
	mysqli_query($mysqli, $sql); 
	
	?>
	<script>
	    if ( window.history.replaceState ) {
	        window.history.replaceState( null, null, window.location.href );
	    }
	</script>
	<?php
}

//search offers
if(isset($_POST['buyform'])){
	?>
	<form method="post" action="">
		<select name="type" type="text">
			<option value="rawfood">food raw</option>
	 		<option value="rawweapon">weapon raw</option>
	 		<option value="rawhouse">house raw</option>
	 		<option value="paper">paper</option>
	 		<option value="rawhospital">rawhospital</option>
	  		<option value="weaponq1">weapon q1</option>
	  		<option value="weaponq2">weapon q2</option>
	  		<option value="weaponq3">weapon q3</option>
	  		<option value="weaponq4">weapon q4</option>
	  		<option value="weaponq5">weapon q5</option>
	  		<option value="foodq1">food q1</option>
	  		<option value="foodq2">food q2</option>
	  		<option value="foodq3">food q3</option>
	  		<option value="foodq4">food q4</option>
	  		<option value="foodq5">food q5</option>
	  		<option value="house">house</option>
	  		<option value="book">books</option>
	  		<option value="hospital">hospital</option>
	   	</select>
	   	<button type="submit" name="searchoffer" />Search</button>
	</form>
	<?php
	?>
	<script>
	    if ( window.history.replaceState ) {
	        window.history.replaceState( null, null, window.location.href );
	    }
	</script>
	<?php
}

if(isset($_POST['searchoffer']) || $show == 2){
	if($searchtype != NULL){
		$type = $searchtype;
	}else{
		$type = $mysqli->escape_string($_POST['type']);
	}
		
	$sql = "SELECT id, country, amount, price, type FROM marketplace WHERE type='$type' AND country='$location' ORDER BY price ASC LIMIT $start_from, ".$results_per_page;
	$rs_result = $mysqli->query($sql);	
	//print_r($set);
	
	?> 
	<div class="scroll">
	<table id="table1">
		<tr>
	    <th> Type</th>
	    <th> Amount</th>
	    <th> Price</th>
	    <th> Market</th>
	    <th> Amount to buy</th>
	</tr>
	<?php
	while($row = $rs_result->fetch_assoc()) {
		?> 
       <tr>
           <td><?php echo $row["type"]; ?></td>
           <td><?php echo $row["amount"]; ?></td>
           <td><?php echo $row["price"]; ?></td>
           <td><?php echo $row["country"]; ?></td>
           <td>
				<form method="post" action="">
					<input type="hidden" name="id" value="<?php echo $row["id"]; ?>" />
					<input type="hidden" name="amount" value="<?php echo $row["amount"]; ?>" />
					<input type="hidden" name="type" value="<?php echo $row["type"]; ?>" />
					<input type="hidden" name="country" value="<?php echo $row["country"]; ?>" />
					<input type="hidden" name="price" value="<?php echo $row["price"]; ?>" />
					<input type="hidden" name="owner" value="<?php echo $row["owner"]; ?>" />
					<input type="number" size="25" required autocomplete="off" id="nobuyitems" name="nobuyitems" max="5000" min="1" step="1" />
					<button type="submit" name="buy" />Buy</button>
				</form>
           </td>
       </tr>
		<?php		
	}; 
	?>
	</table>
	</div>
	<?php
	$sql = "SELECT COUNT(id) AS total FROM marketplace WHERE type='$type' AND country='$location'";
	$result = $mysqli->query($sql);
	$row = $result->fetch_assoc();
	$total_pages = ceil($row["total"] / $results_per_page); // calculate total pages with results
	
	for ($i=1; $i<=$total_pages; $i++) {  // print links for all pages
		echo "<a href='marketplace.php?show=2&searchtype=$type&page=".$i."'";
	    if ($i==$page)  echo " class='curPage'";
	    echo ">".$i."</a> "; 
	};
	?>
	<script>
	    if ( window.history.replaceState ) {
	        window.history.replaceState( null, null, window.location.href );
	    }
	</script>
	<?php		
}

//buy items
if(isset($_POST['buy'])){
	$id = $mysqli->escape_string($_POST['id']);
	$id = (int) $id;	
	$nobuyitems = $mysqli->escape_string($_POST['nobuyitems']);
	$nobuyitems = (int) $nobuyitems;
	if($nobuyitems <= 0){
		$nobuyitems == 1;
	}
		
	$result = $mysqli->query("SELECT * FROM users WHERE username='$username'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$location = $row['location'];
	$location2 = $row['location2'];
	$location2 = $mysqli->escape_string($row['location2']);
	
	$result = $mysqli->query("SELECT * FROM marketplace WHERE id='$id' AND country='$location'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$amount=$row['amount'];
	$type=$row['type'];
	$owner =$row['owner'];
	$country=$row['country'];
	$price=$row['price'];
	
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
	
	if($location==$country){
		$price=$price*$nobuyitems;
		$moneyafter=$money-$price;
		
		if($moneyafter>=0){
			$itemafter=$amount-$nobuyitems;
			if($itemafter>=0){
				//update buyer and country
				$result = $mysqli->query("SELECT * FROM inventory WHERE userinv='$username'") or die($mysqli->error());
				$row = mysqli_fetch_array($result);
				$inventory = $row[$type];
				
				
				$inventory=$inventory+$nobuyitems;
							
				$tax=$price*($vat/100);
				$countrymoney=$countrymoney+$tax;
				
				$taxtoday=$taxtoday+$tax;
				
				
				$sql = "UPDATE countryinfo SET money ='$countrymoney' WHERE country='$location'";
				mysqli_query($mysqli, $sql);
				
				$sql = "UPDATE region SET taxtoday ='$taxtoday' WHERE name='$location2'";
				mysqli_query($mysqli, $sql);
				
				$sql = "UPDATE currency SET $cur ='$moneyafter'WHERE usercur='$username'";
				mysqli_query($mysqli, $sql);
				
				$sql = "UPDATE inventory SET $type ='$inventory'WHERE userinv='$username'";
				mysqli_query($mysqli, $sql);
				
				//update owner
				$result = $mysqli->query("SELECT * FROM currency WHERE usercur='$owner'") or die($mysqli->error());
				$row = mysqli_fetch_array($result);
				$moneyowner = $row[$cur];
				
				$moneyowner=$moneyowner+$price-$tax;
				
				$sql = "UPDATE currency SET $cur ='$moneyowner' WHERE usercur='$owner'";
				mysqli_query($mysqli, $sql);
				
				echo "Added $nobuyitems $type to your inventory!";
				
				if($itemafter>0){
					$sql = "UPDATE marketplace SET amount ='$itemafter' WHERE id='$id'";
					mysqli_query($mysqli, $sql);
				}else{
					$sql = "DELETE FROM marketplace WHERE id='$id'";
					mysqli_query($mysqli, $sql);
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
?> 
</div>
</body>
<footer>
<?php require 'bottombar.php'; ?>
</footer>
</html>
