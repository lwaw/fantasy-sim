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
$show=$mysqli->escape_string($show);
if (isset($_GET["sellcur"])) { $sellcur  = $_GET["sellcur"]; } else { $sellcur=NULL; };
$sellcur=$mysqli->escape_string($sellcur);
if (isset($_GET["forcur"])) { $forcur  = $_GET["forcur"]; } else { $forcur=NULL; };
$forcur=$mysqli->escape_string($forcur);

echo nl2br ("<div class=\"h1\">Currency market</div>");
?> <hr /> <?php

?>
<form method="post" action="">      
    <button type="submit" name="sellform" />Sell items</button>
    <button type="submit" name="showform" />Show your offers</button>
    <button type="submit" name="buyform" />Buy items</button>
</form>

	<?php
	if(isset($_POST['sellform'])){
	//put offer on currency market
	?>
	<form method="post" action="">
		<?php
		$result = mysqli_query($mysqli,"SELECT currency FROM countryinfo");
		$columnValues = Array();
		while ( $row = mysqli_fetch_assoc($result) ) {
			$columnValues[] = $row['currency'];
		}
		$columnValues[]='gold';
		//print_r($columnValues);
		
		?>
		<label for="sellcur">Currency to sell:</label>
	   	<select required name="sellcur" id="sellcur" type="text">
			<?php        
	    	// Iterating through the product array
	    	foreach($columnValues as $item){
	    		?>
	    		<option value="<?php echo strtolower($item); ?>"><?php echo $item; ?></option>
	    		<?php
	 		}
	    	?>
	    </select>
	    
	    <label for="sellamount">Amount of currency to sell:</label>
	    <input type="number" size="5" required autocomplete="off" id="sellamount" name="sellamount" min="0.01" step="0.01" /> 
			
		<label for="forcur">Currency to buy:</label>
	   	<select required name="forcur" id="forcur" type="text">
	   		
		    <?php     
	        // Iterating through the product array
	        foreach($columnValues as $item){
	        	?>
	        	<option value="<?php echo strtolower($item); ?>"><?php echo $item; ?></option>
	        	<?php
	 		}
	        ?>
	   </select>
			
		<label for="exchangerate">Exchange price for 1 sold currency:</label>
	    <input type="number" size="5" required autocomplete="off" id="exchangerate" name="exchangerate" min="0.01" step="0.01" />
			
		<button type="submit" name="putoffer" /><?php echo "Put exchange offer on market"; ?></button>
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

if(isset($_POST['putoffer'])){
	$sellcur = $mysqli->escape_string($_POST['sellcur']);
	$sellamount = $mysqli->escape_string($_POST['sellamount']);
	$sellamount = (double) $sellamount;
	$forcur = $mysqli->escape_string($_POST['forcur']);
	$exchangerate = $mysqli->escape_string($_POST['exchangerate']);
	$exchangerate = (double) $exchangerate;
	if($sellamount <= 0 || $exchangerate <= 0){
		$sellamount == 1;
		$exchangerate == 1;
	}
	
	$result2 = $mysqli->query("SELECT id FROM countryinfo WHERE currency='$sellcur'") or die($mysqli->error());
	$count = $result2->num_rows;
	$result2 = $mysqli->query("SELECT id FROM countryinfo WHERE currency='$forcur'") or die($mysqli->error());
	$count2 = $result2->num_rows;
	
	if($sellcur=="gold"){
		$count = 1;
	}elseif($forcur=="gold"){
		$count2 = 2;
	}
	
	if($count != 0 && $count2 != 0){
		if($sellcur !== $forcur){
			//get money of user info
			$result = $mysqli->query("SELECT * FROM currency WHERE usercur='$username'") or die($mysqli->error());
			$row = mysqli_fetch_array($result);
			$money=$row[$sellcur];
			$money=$money-$sellamount;
			
			if($money>=0){
				$sql = "INSERT INTO currencymarket (offerid, sellcur, sellamount, forcur, foramount) " 
	            . "VALUES ('$username','$sellcur','$sellamount', '$forcur', '$exchangerate')";
				mysqli_query($mysqli, $sql);
				
				$sql = "UPDATE currency SET $sellcur='$money' WHERE usercur='$username'";
				mysqli_query($mysqli, $sql);
				echo "Put offer on market!";
				echo'<div class="boxed">Offer is succesfully put on the market!</div>';
			}
		}else{
			echo'<div class="boxed">You are trying to sell and buy the same currency!</div>';
		}
	}
}

//remove own offer

if(isset($_POST['showform']) || $show == 1){
	$sql = "SELECT id, sellcur, sellamount, foramount, forcur FROM currencymarket WHERE offerid='$username' ORDER BY sellcur ASC LIMIT $start_from, ".$results_per_page;
	$rs_result = $mysqli->query($sql);	
	
	?> 
	<div class="scroll">
	<table id="table1">
		<tr>
	    <th> Currency for sale by you</th>
	    <th> Amount for sale</th>
	    <th> Exchange rate</th>
	    <th> Currency to buy by you</th>
	    <th> Remove from market</th>
	</tr>
	<?php
	while($row = $rs_result->fetch_assoc()) {
		?> 
       <tr>
           <td><?php echo $row["sellcur"]; ?></td>
           <td><?php echo $row["sellamount"]; ?></td>
           <td><?php echo $row["foramount"]; ?></td>
           <td><?php echo $row["forcur"]; ?></td>
           <td>
				<form method="post" action="">
					<input type="hidden" name="id" value="<?php echo $row["id"]; ?>" />
					<input type="hidden" name="sellamount" value="<?php echo $row["sellamount"]; ?>" />
					<input type="hidden" name="sellcur" value="<?php echo $row["sellcur"]; ?>" />
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
	$sql = "SELECT COUNT(id) AS total FROM currencymarket WHERE offerid='$username'";
	$result = $mysqli->query($sql);
	$row = $result->fetch_assoc();
	$total_pages = ceil($row["total"] / $results_per_page); // calculate total pages with results
	
	for ($i=1; $i<=$total_pages; $i++) {  // print links for all pages
		echo "<a href='currencymarket.php?show=1&page=".$i."'";
	    if ($i==$page)  echo " class='curPage'";
	    echo ">".$i."</a> "; 
	};
}

if(isset($_POST['remove'])){
	$id = $mysqli->escape_string($_POST['id']);
	$id = (int) $id;
	
	$result = $mysqli->query("SELECT * FROM currencymarket WHERE id='$id' AND offerid='$username'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$sellamount=$row['sellamount'];
	$sellcur=$row['sellcur'];
	
	$result = $mysqli->query("SELECT $sellcur FROM currency WHERE usercur='$username'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$money = $row[$sellcur];
	$money=$money+$sellamount;
	//echo "$money";
	$sql = "UPDATE currency SET $sellcur ='$money' WHERE usercur='$username'";
	mysqli_query($mysqli, $sql);
	
	$sql = "DELETE FROM currencymarket WHERE id='$id'";
	mysqli_query($mysqli, $sql); 
	
	echo'<div class="boxed">Done!</div>';
	
	?>
	<script>
	    if ( window.history.replaceState ) {
	        window.history.replaceState( null, null, window.location.href );
	    }
	</script>
	<?php
}

?> <div class="textbox"> <?php
//search exchange offers
if(isset($_POST['buyform'])){
	?>
	<form method="post" action="">
		<?php
		$result = mysqli_query($mysqli,"SELECT currency FROM countryinfo");
		$columnValues = Array();
		while ( $row = mysqli_fetch_assoc($result) ) {
			$columnValues[] = $row['currency'];
		}
		$columnValues[]='gold';
		//print_r($columnValues);
		
		?>
		<label for="sellcur">Currency to buy:</label>
	   	<select required name="sellcur" id="sellcur" type="text">
			<?php        
	    	// Iterating through the product array
	    	foreach($columnValues as $item){
	    		?>
	    		<option value="<?php echo strtolower($item); ?>"><?php echo $item; ?></option>
	    		<?php
	 		}
	    	?>
	    </select>
	    
		<label for="forcur">Currency to sell:</label>
	   	<select required name="forcur" id="forcur" type="text">
	   		
		    <?php     
	        // Iterating through the product array
	        foreach($columnValues as $item){
	        	?>
	        	<option value="<?php echo strtolower($item); ?>"><?php echo $item; ?></option>
	        	<?php
	 		}
	        ?>
		</select>	
		<button type="submit" name="searchoffer" /><?php echo "Search offers"; ?></button>
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
	if($sellcur != NULL && $forcur != NULL){
		
	}else{
		$sellcur = $mysqli->escape_string($_POST['sellcur']);
		$forcur = $mysqli->escape_string($_POST['forcur']);
	}
		
	$sql = "SELECT * FROM currencymarket WHERE sellcur='$sellcur' AND forcur='$forcur' ORDER BY foramount ASC LIMIT $start_from, ".$results_per_page;
	$rs_result = $mysqli->query($sql);	
	//print_r($set);
	
	?> 
	<div class="scroll">
	<table id="table1">
		<tr>
		    <th> Seller</th>
		    <th> Currency for sale by seller</th>
		    <th> Amount of currency for sale</th>
		    <th> Exchange rate</th>
		    <th> Currency to buy by seller</th>
		    <th> Buy</th>
		</tr>
		<?php
		while($row = $rs_result->fetch_assoc()) {
			?> 
	       <tr>
	           <td><?php echo $row["offerid"]; ?></td>
	           <td><?php echo $row["sellcur"]; ?></td>
	           <td><?php echo $row["sellamount"]; ?></td>
	           <td><?php echo $row["foramount"]; ?></td>
	           <td><?php echo $row["forcur"]; ?></td>
	           <td>
					<form method="post" action="">
						<input type="hidden" name="id" value="<?php echo $row["id"]; ?>" />
						<input type="hidden" name="offerid" value="<?php echo $row["offerid"]; ?>" />
						<input type="hidden" name="sellamount" value="<?php echo $row["sellamount"]; ?>" />
						<input type="hidden" name="foramount" value="<?php echo $row["foramount"]; ?>" />
						<input type="hidden" name="sellcur" value="<?php echo $row["sellcur"]; ?>" />
						<input type="hidden" name="forcur" value="<?php echo $row["forcur"]; ?>" />
						<input type="number" size="25" required autocomplete="off" id="nobuyitems" name="nobuyitems" min="0.01" step="0.01" />
						<button type="submit" name="buy" value="<?php echo "$foramount[$key]"; ?>" /><?php echo "Buy"; ?></button>
					</form>
	           </td>
	       </tr>
			<?php		
		}; 
		?>
	</table>
	</div>
	<?php
		
	$sql = "SELECT COUNT(id) AS total FROM currencymarket WHERE sellcur='$forcur' AND forcur='$sellcur'";
	$result = $mysqli->query($sql);
	$row = $result->fetch_assoc();
	$total_pages = ceil($row["total"] / $results_per_page); // calculate total pages with results
	
	for ($i=1; $i<=$total_pages; $i++) {  // print links for all pages
		echo "<a href='currencymarket.php?show=2&sellcur=$sellcur&forcur=$forcur&page=".$i."'";
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

if(isset($_POST['buy'])){

	$id = $mysqli->escape_string($_POST['id']);
	$id = (int) $id;
	$nobuyitems = $mysqli->escape_string($_POST['nobuyitems']);
	$nobuyitems = (double) $nobuyitems;
	if($nobuyitems <= 0){
		$nobuyitems = 1;
	}
	
	$result = $mysqli->query("SELECT * FROM currencymarket WHERE id='$id'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$sellcur=$row['sellcur'];
	$forcur=$row['forcur'];
	$offerid=$row['offerid'];
	$sellamount=$row['sellamount'];
	$foramount=$row['foramount'];
	
	$result = $mysqli->query("SELECT * FROM currency WHERE usercur='$username'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	
	//money sold
	$money=$row[$forcur];
	$money=$money-$nobuyitems*$foramount;
	
	//money bought
	$money2=$row[$sellcur];
	$money2=$money2+$nobuyitems;
	//echo "$forcur";
	//echo "$money2";
			
	//update sellamount
	$sellamount=$sellamount-$nobuyitems;
	
	if($money>=0){
		if($sellamount>=0){				
			//owner of offer money
			$result = $mysqli->query("SELECT * FROM currency WHERE usercur='$offerid'") or die($mysqli->error());
			$row = mysqli_fetch_array($result);
			$moneyowner2=$row[$forcur];

			
			$sql = "UPDATE currency SET $sellcur ='$money2', $forcur='$money' WHERE usercur='$username'";
			mysqli_query($mysqli, $sql);
			
			$result = $mysqli->query("SELECT * FROM currency WHERE usercur='$offerid'") or die($mysqli->error());
			$row = mysqli_fetch_array($result);
			$moneyowner=$row[$forcur];
			$moneyowner=$moneyowner+$nobuyitems*$foramount;

			//update money, moneyowner with forcur the same and updated sellcur
			$sql = "UPDATE currency SET $forcur ='$moneyowner' WHERE usercur='$offerid'";
			mysqli_query($mysqli, $sql);
			
			echo'<div class="boxed">Currency transfer succesfull!</div>';
			
			
			//check if offer is sold out
			if($sellamount>0){
				$sql = "UPDATE currencymarket SET sellamount ='$sellamount' WHERE id='$id'";
				mysqli_query($mysqli, $sql);
			}elseif($sellamount==0){
				$sql = "DELETE FROM currencymarket WHERE id='$id'";
				mysqli_query($mysqli, $sql);
			}
		}else{
			echo'<div class="boxed">There is not enough currency for sale!</div>';
		}
	}else{
		echo'<div class="boxed">You don\'t have enough money!</div>';
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
</html>
