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
$showmarketprep=$_GET["market"];
$showmarket=$mysqli->escape_string($showmarketprep);

$boycotcost=50;

$result = $mysqli->query("SELECT * FROM users WHERE username='$username'") or die($mysqli->error());
$row = mysqli_fetch_array($result);
$nationality=$row['nationality'];

$result = $mysqli->query("SELECT * FROM countryinfo WHERE country='$nationality'") or die($mysqli->error());
$row = mysqli_fetch_array($result);
$finance=$row['finance'];
//$gold=$row['gold'];
//$vat=$row['vat'];
//$worktax=$row['worktax'];
$nodecisions=$row['nodecisions'];
$moneycreation=$row['moneycreation'];

$maxnodecisions=15;
$maxmoneycreation=1000;
$napcost=5;
$warcost=0;

if($finance==$username){
	echo nl2br ("<div class=\"h1\">Minister of finance of $nationality</div>");
	?> <hr /> <?php
	
	echo nl2br("The max number of decisions to make is $maxnodecisions per term. $nodecisions already taken this term. \n");
	echo nl2br("The max number of money to change is $maxmoneycreation per term. $moneycreation already used. \n");
	
	//buttons for pages
	?>
	<div class="textbox">
		<form method="post" action="">
			<button type="submit" name="vatform" />Change vat</button>
			<button type="submit" name="worktaxform" />Change worktax</button>
			<button type="submit" name="changecurrencyform" />Create currency</button>
			<button type="submit" name="buycurrencyform" />Buy currency</button>
			<button type="submit" name="buyhospitalform" />Buy hospital</button>
			<button type="submit" name="boycotform" />Ennact boycot</button>
			<button type="submit" name="showboycotsform" />Show boycots</button>
			<button type="submit" name="treasuryform" />Show treasury</button>
			<button type="submit" name="transferform" />Transfer gold to other country</button>
		</form>
	</div>
	<?php
	?> <hr /> <?php
	
	if($showmarket=="false"){	
		?> <div class="textbox"> <?php	
		//change vat
		if(isset($_POST['vatform'])){
			?>
			<form method="post" action="">
				<label for="changevat">New vat:</label>
				<input type="number" size="25" required autocomplete="off" id="changevat" name="changevat" min="1" max="99" step="1" />
				<button type="submit" name="vat"  /><?php echo "Change vat"; ?></button>
			</form>
			<?php
		}
		if(isset($_POST['vat'])){
			$result = $mysqli->query("SELECT * FROM countryinfo WHERE country='$nationality'") or die($mysqli->error());
			$row = mysqli_fetch_array($result);
			$finance=$row['finance'];
			$nodecisions=$row['nodecisions'];
		
			$changevat = $mysqli->escape_string($_POST['changevat']);
			$changevat = (int) $changevat;
			if($changevat > 100){
				$changevat = 100;
			}elseif($changevat < 0){
				$changevat = 0;
			}
			
			if($nodecisions<$maxnodecisions){
				$nodecisions=$nodecisions+1;
			
				$sql = "UPDATE countryinfo SET nodecisions ='$nodecisions' WHERE country='$nationality'";
				mysqli_query($mysqli, $sql);
				
				$sql = "INSERT INTO congress (type, country, start, extraint) " 
				. "VALUES ('vat','$nationality',NOW(),'$changevat')";
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
		}
		
		echo nl2br(" \n");
		//change worktax
		if(isset($_POST['worktaxform'])){
			?>
			<form method="post" action="">
				<label for="changeworktax">New worktax:</label>
				<input type="number" size="25" required autocomplete="off" id="changeworktax" name="changeworktax" min="1" max="99" step="1" />
				<button type="submit" name="worktax"  /><?php echo "Change worktax"; ?></button>
			</form>
			<?php
		}
		if(isset($_POST['worktax'])){
			$result = $mysqli->query("SELECT * FROM countryinfo WHERE country='$nationality'") or die($mysqli->error());
			$row = mysqli_fetch_array($result);
			$finance=$row['finance'];
			$nodecisions=$row['nodecisions'];
		
			$changeworktax = $mysqli->escape_string($_POST['changeworktax']);
			$changeworktax = (int) $changeworktax;
			if($changeworktax > 100){
				$changeworktax = 100;
			}elseif($changeworktax < 0){
				$changeworktax = 0;
			}
			if($nodecisions<$maxnodecisions){
				$nodecisions=$nodecisions+1;
				
				$sql = "INSERT INTO congress (type, country, start, extraint) " 
				. "VALUES ('worktax','$nationality',NOW(),'$changeworktax')";
		 		mysqli_query($mysqli, $sql);
				
				$sql = "UPDATE countryinfo SET nodecisions ='$nodecisions' WHERE country='$nationality'";
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
		}
		?> </div> <?php
	
		?> <div class="textbox"> <?php
		//create or delete currency
		if(isset($_POST['changecurrencyform'])){
			?>
			<form method="post" action="">
				 <select name="option" type="text">
					<option value="create">create currency</option>
		 			<option value="delete">delete currency</option>
		   		</select>
				<label for="currency">change in currency:</label>
				<input type="number" size="25" required autocomplete="off" id="currency" name="currency" min="1" max="1000" step="1" />
				<button type="submit" name="inflation"  /><?php echo "Change currency"; ?></button>
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
		if(isset($_POST['inflation'])){
			$result = $mysqli->query("SELECT * FROM countryinfo WHERE country='$nationality'") or die($mysqli->error());
			$row = mysqli_fetch_array($result);
			$finance=$row['finance'];
			$nodecisions=$row['nodecisions'];
			$money=$row['money'];
			$moneycreation=$row['moneycreation'];
			
			$option = $mysqli->escape_string($_POST['option']);
			$currency = $mysqli->escape_string($_POST['currency']);
			$currency = (int) $currency;
			if($currency < 0){
				$currency = 0;
			}
			
			$moneycreation=$moneycreation+$currency;
			if($moneycreation<=$maxmoneycreation){
				if($nodecisions<$maxnodecisions){
					$sql = "INSERT INTO congress (type, country, start, extraint, extratext) " 
					. "VALUES ('inflation','$nationality',NOW(),'$currency', '$option')";
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
					echo'<div class="boxed">Not enough decisions!</div>';
				}
			}else{
				echo'<div class="boxed">Limit of currency creation has been reached!</div>';
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
	
	?> <div class="textbox"> <?php
	//currency market
	echo nl2br(" \n");
	if(isset($_POST['buycurrencyform'])){
		?>
		<form method="post" action="">
		<?php
			$result = $mysqli->query("SELECT * FROM countryinfo WHERE country='$nationality'") or die($mysqli->error());
			$row = mysqli_fetch_array($result);
			$currency=$row['currency'];
			?>
			<label for="forcur">Currency to buy:</label>
		   	<select required name="forcur" id="forcur" type="text">
				<option value="<?php echo "$currency"; ?>"><?php echo "$currency"; ?></option>
				<option value="gold">gold</option>
			<label for="forcur">Currency to buy:</label>
		   	<select required name="forcur" id="forcur" type="text">
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
	
	if(isset($_POST['searchoffer'])){
		$result = $mysqli->query("SELECT * FROM countryinfo WHERE country='$nationality'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$currency=$row['currency'];
		
		$forcur = $mysqli->escape_string($_POST['forcur']);
		if($forcur == 'gold'){
			$sellcur=$currency;
			//echo "$sellcur";
		}elseif($forcur == $currency){
			$sellcur='gold';
			//echo "$sellcur";
		}
		
		$result = $mysqli->query("SELECT id, offerid, sellamount, foramount FROM currencymarket WHERE sellcur='$forcur' AND forcur='$sellcur' ") or die($mysqli->error());
		$count = $result->num_rows;
		if($count!=0){
			for ($set=array(); $row=$result->fetch_assoc(); $set[]=$row);
			//print_r($set);
			
				
			//sort data in descending salary/joboffer
			foreach ($set as $key => $row)
			{
		    	$exchangerate[$key]  = $row['sellamount'];
			}    
			// Sort the data with wek ascending order, add $mar as the last parameter, to sort by the common key
			array_multisort($exchangerate, SORT_ASC, $set);
			
			//print_r($set);
			echo nl2br(" \n");
				
			//create forms for every item
			foreach ($set as $key => $value) {
				$id[$key] = $value['id'];
				$offerid[$key] = $value['offerid'];
				$sellamount[$key] = $value['sellamount'];
				$foramount[$key] = $value['foramount'];
					
				echo "Number of $forcur for sale: $sellamount[$key] | Exchange rate, cost $forcur per $sellcur: $foramount[$key] | Seller: $offerid[$key]";
				?>
				<form method="post" action="">
					<input type="hidden" name="id" value="<?php echo "$id[$key]"; ?>" />
					<input type="hidden" name="offerid" value="<?php echo "$offerid[$key]"; ?>" />
					<input type="hidden" name="sellamount" value="<?php echo "$sellamount[$key]"; ?>" />
					<input type="hidden" name="foramount" value="<?php echo "$foramount[$key]"; ?>" />
					<input type="hidden" name="sellcur" value="<?php echo "$sellcur"; ?>" />
					<input type="hidden" name="forcur" value="<?php echo "$forcur"; ?>" />
		
					<label for="nobuyitems">Number of currency to buy:</label>
					<input type="number" size="25" required autocomplete="off" id="nobuyitems" name="nobuyitems" min="0.01" step="0.01" />
					<button type="submit" name="buy" value="<?php echo "$foramount[$key]"; ?>" /><?php echo "Buy"; ?></button>
				</form>
				<?php
				echo nl2br(" \n");
			}
		}else{
			echo'<div class="boxed">There are no offers on the market!</div>';
		}
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
			
			//echo "$foramount";
			
			$result = $mysqli->query("SELECT * FROM countryinfo WHERE country='$nationality'") or die($mysqli->error());
			$row = mysqli_fetch_array($result);
			//money sold
			$sellcur2=$sellcur;
			if($sellcur=='gold'){
				$sellcur='gold';
			}else{
				$sellcur='money';
			}
			
			$money=$row[$sellcur];
			$money=$money+$nobuyitems;
			//forcur=gold en sellcur=nec
			//echo "$sellcur";
			//echo "$money";
			
			if($forcur=='gold'){
				$forcur='gold';
			}else{
				$forcur='money';
			}
			
			$moneycreation=$row['moneycreation'];
			$moneycreation=$moneycreation+$nobuyitems;
			
			$sellamount=$sellamount-$nobuyitems;
			
			//money bought
			$money2=$row[$forcur];		
			//religeous tax
			if($forcur=='gold'){
				$result = $mysqli->query("SELECT statereligion FROM countryinfo WHERE country='$nationality'") or die($mysqli->error());
				$row = mysqli_fetch_array($result);
				$statereligion=$row['statereligion'];	
				
				if($statereligion != 'NULL'){
					$result = $mysqli->query("SELECT religiontax, gold FROM religion WHERE name='$statereligion'") or die($mysqli->error());
					$row = mysqli_fetch_array($result);
					$reltax=$row['religiontax'];
					$relgold=$row['gold'];	
					
					$reltax=$reltax/100;
					$totaltax=$nobuyitems*$reltax;
					$nobuyitems=$nobuyitems-$totaltax;
					
					$relgold=$relgold+$totaltax;
					
					$sql = "UPDATE religion SET gold ='$relgold' WHERE name='$statereligion'";
					mysqli_query($mysqli, $sql);
				}		
			}
			$money2=$money2-$nobuyitems*$foramount;
					
			//update sellamount
			if($moneycreation<=$maxmoneycreation){
				if($money>=0){
					if($sellamount>=0){				
						//owner of offer money
						$result = $mysqli->query("SELECT * FROM currency WHERE usercur='$offerid'") or die($mysqli->error());
						$row = mysqli_fetch_array($result);
						$moneyowner2=$row[$forcur];
	
					
						$sql = "UPDATE countryinfo SET $sellcur ='$money', $forcur='$money2', moneycreation='$moneycreation' WHERE country='$nationality'";
						mysqli_query($mysqli, $sql);
					
						$result = $mysqli->query("SELECT * FROM currency WHERE usercur='$offerid'") or die($mysqli->error());
						$row = mysqli_fetch_array($result);
						$moneyowner=$row[$sellcur2];
						$moneyowner=$moneyowner+$nobuyitems*$foramount;
	
						//update money, moneyowner with forcur the same and updated sellcur
						$sql = "UPDATE currency SET $sellcur ='$moneyowner', $forcur='$moneyowner2' WHERE usercur='$offerid'";
						mysqli_query($mysqli, $sql);
					
						echo "Currency transfer succesfull!";
					
						//check if offer is sold out
						if($sellamount>0){
							$sql = "UPDATE currencymarket SET sellamount ='$sellamount' WHERE id='$id'";
							mysqli_query($mysqli, $sql);
						}elseif($sellamount==0){
							$sql = "DELETE FROM currencymarket WHERE id='$id'";
							mysqli_query($mysqli, $sql);
						}
					}else{
						echo "There is not enough currency for sale!";
					}
				}else{
					echo "You don't have enough money!";
				}
				}else{
					echo "You already transferred the max amount of currency!";
				}
		}
		?> </div> <?php
		
		//buy hospital
		?> <div class="textbox"> <?php
		if(isset($_POST['buyhospitalform'])){
			?>
			<form method="post" action="">
			   	<button type="submit" name="buyhospital" /><?php echo "Buy hospitals"; ?></button>
			</form>   		
			<?php
		}
					
		if(isset($_POST['buyhospital'])){
			?>
			<script>
				var val = "true"
			    window.location = 'ministeroffinance.php?market='+val;
			</script>
			<?php
		}
			
		?> </div> <?php
		
		?> <div class="textbox"> <?php
		//boycot
		if(isset($_POST['boycotform'])){
			?>
			<form method="post" action="">
				<label for="diplomacy">Select country to boycot:</label>
				<?php
				$result = mysqli_query($mysqli,"SELECT country FROM countryinfo WHERE country != '$nationality'");
				$columnValues = Array();
				while ( $row = mysqli_fetch_assoc($result) ) {
				$columnValues[] = $row['country'];
				}
				// Sort the data with wek ascending order, add $mar as the last parameter, to sort by the common key
				asort($columnValues);
				?>
				<select required name="country2" id="country2" type="text">
					<?php
				
					// Iterating through the product array
					foreach($columnValues as $item){
						?>
						<option value="<?php echo strtolower($item); ?>"><?php echo $item; ?></option>
						<?php
					}
					?>
				</select>
				<button type="submit" name="boycot" /><?php echo "Submit"; ?></button>
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
		if(isset($_POST['boycot'])){
			$country2 = $mysqli->escape_string($_POST['country2']);
			
			$result2 = $mysqli->query("SELECT id FROM countryinfo WHERE country='$country2'") or die($mysqli->error());
			$count = $result2->num_rows;
			if($count != 0){
				$result = $mysqli->query("SELECT country2 FROM diplomacy WHERE country1='$nationality' AND country2='$country2' AND type='boycot'") or die($mysqli->error());
				if($result->num_rows > 0){
					echo "You already have a boycot against this country!";
				}else{
					$result = $mysqli->query("SELECT * FROM countryinfo WHERE country='$nationality'") or die($mysqli->error());
					$row = mysqli_fetch_array($result);
					$nodecisions=$row['nodecisions'];
					$nodecisions=$nodecisions+1;
					if($nodecisions<=$maxnodecisions){
						$sql = "INSERT INTO congress (type, country, start, extratext) " 
						. "VALUES ('boycot','$nationality',NOW(), '$country2')";
				 		mysqli_query($mysqli, $sql);
						
						echo'<div class="boxed">Done!</div>';
						
						?>
						<script>
						    if ( window.history.replaceState ) {
						        window.history.replaceState( null, null, window.location.href );
						    }
						</script>
						<?php
						
						$sql = "UPDATE countryinfo SET nodecisions='$nodecisions' WHERE country='$nationality'";
						mysqli_query($mysqli, $sql);
						$country2 = $mysqli->escape_string($_POST['country2']);
					}else{
						echo "The country doesn't have enough currency or decisions!";
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
		
		//show boycots
		echo nl2br(" \n");
		if(isset($_POST['showboycotsform'])){
			?>
			<form method="post" action="">
			   	<button type="submit" name="showboycot" /><?php echo "Show boycots"; ?></button>
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
		
		if(isset($_POST['showboycot'])){
			$result = $mysqli->query("SELECT * FROM diplomacy WHERE type='boycot' AND country1='$nationality'") or die($mysqli->error());
			for ($set=array(); $row=$result->fetch_assoc(); $set[]=$row);
			//print_r($set);
				
			echo nl2br(" \n");
				
			//create forms for every item
			foreach ($set as $key => $value) {
				$country2[$key] = $value['country2'];
				$id[$key]=$value['id'];
					
				echo "boycot against $country2[$key]";
				?>
				<form method="post" action="">
					<input type="hidden" name="country2" value="<?php echo "$country2[$key]"; ?>" />
					<input type="hidden" name="id" value="<?php echo "$id[$key]"; ?>" />
					<button type="submit" name="stop" value="<?php echo "1"; ?>" /><?php echo "End boycot"; ?></button>
				</form>
				<?php
				echo nl2br(" \n");
			}
			?>
			<script>
			    if ( window.history.replaceState ) {
			        window.history.replaceState( null, null, window.location.href );
			    }
			</script>
			<?php
		}
	
		if(isset($_POST['stop'])){
			$id = $mysqli->escape_string($_POST['id']);
			$id = (int)$id;
			$country2 = $mysqli->escape_string($_POST['country2']);
			
			$result2 = $mysqli->query("SELECT id FROM diplomacy WHERE type='boycot' AND country1='$nationality' AND country2='$country2'") or die($mysqli->error());
			$count = $result2->num_rows;
			if($count != 0){
				$result = $mysqli->query("SELECT * FROM countryinfo WHERE country='$nationality'") or die($mysqli->error());
				$row = mysqli_fetch_array($result);
				$nodecisions=$row['nodecisions'];
				$nodecisions=$nodecisions+1;
				
				if($nodecisions<=$maxnodecisions){
					$sql = "INSERT INTO congress (type, country, start, extraint, extratext) " 
					. "VALUES ('stopboycot','$nationality',NOW(), '$id', '$country2')";
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
			}
		}
		
		if(isset($_POST['treasuryform'])){
			$result = $mysqli->query("SELECT * FROM countryinfo WHERE country='$nationality'") or die($mysqli->error());
			$row = mysqli_fetch_array($result);
			$treasurymoney=$row['treasurymoney'];
			$treasurygold=$row['treasurygold'];
			
			echo nl2br("Gold in treasury: $treasurygold \n");
			echo nl2br("Money in treasury: $treasurymoney \n");
			
			?>
			<form method="post" action="">
				 <select name="type" type="text">
					<option value="gold">Gold</option>
		 			<option value="currency">Currency</option>
		   		</select>
				<input type="number" size="25" required autocomplete="off" id="amount" placeholder="Amount" name="amount" min="1" step="0.01" />
				<button type="submit" name="addtreasury"  /><?php echo "Add to treasury"; ?></button>
				<button type="submit" name="retracttreasury"  /><?php echo "Retract from treasury"; ?></button>
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
		
		if(isset($_POST['addtreasury'])){
			$type = $mysqli->escape_string($_POST['type']);
			$amount = $mysqli->escape_string($_POST['amount']);
			$amount = (double) $amount;
			if($amount <= 0){
				$amount=1;
			}
			if($type=="gold" || $type=="currency"){
				$result = $mysqli->query("SELECT * FROM countryinfo WHERE country='$nationality'") or die($mysqli->error());
				$row = mysqli_fetch_array($result);
				$gold=$row['gold'];
				$money=$row['money'];
				$treasurymoney=$row['treasurymoney'];
				$treasurygold=$row['treasurygold'];
				$nodecisions=$row['nodecisions'];
				
				$nodecisions=$nodecisions+1;
				if($nodecisions<=$maxnodecisions){
					$positive=0;
					if($type=="gold"){
						$gold=$gold-$amount;
						if($gold>=0){
							$positive=1;
						}
					}elseif($type=="currency"){
						$money=$money-$amount;
						if($money>=0){
							$positive=1;
						}
					}
					
					if($positive==1){
						$sql = "INSERT INTO congress (type, country, start, extraint, extratext) " 
						. "VALUES ('addtreasury','$nationality',NOW(),'$amount', '$type')";
				 		mysqli_query($mysqli, $sql);
						
						$sql = "UPDATE countryinfo SET nodecisions ='$nodecisions' WHERE country='$nationality'";
						mysqli_query($mysqli, $sql);
				 		
						echo "Done!";
					}else{
						echo "Not enough money/gold!";
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

		if(isset($_POST['retracttreasury'])){
			$type = $mysqli->escape_string($_POST['type']);
			$amount = $mysqli->escape_string($_POST['amount']);
			$amount = (double) $amount;
			if($amount <= 0){
				$amount=1;
			}
			if($type=="gold" || $type=="currency"){
				$result = $mysqli->query("SELECT * FROM countryinfo WHERE country='$nationality'") or die($mysqli->error());
				$row = mysqli_fetch_array($result);
				$gold=$row['gold'];
				$money=$row['money'];
				$treasurymoney=$row['treasurymoney'];
				$treasurygold=$row['treasurygold'];
				$nodecisions=$row['nodecisions'];
				
				$nodecisions=$nodecisions+1;
				if($nodecisions<=$maxnodecisions){
					$positive=0;
					if($type=="gold"){
						$treasurygold=$treasurygold-$amount;
						if($treasurygold>=0){
							$positive=1;
						}
					}elseif($type=="currency"){
						$treasurymoney=$treasurymoney-$amount;
						if($treasurymoney>=0){
							$positive=1;
						}
					}
					
					if($positive==1){
						$sql = "INSERT INTO congress (type, country, start, extraint, extratext) " 
						. "VALUES ('retracttreasury','$nationality',NOW(),'$amount', '$type')";
				 		mysqli_query($mysqli, $sql);
						
						$sql = "UPDATE countryinfo SET nodecisions ='$nodecisions' WHERE country='$nationality'";
						mysqli_query($mysqli, $sql);
				 		
						echo "Done!";
					}else{
						echo "Not enough money/gold!";
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

		//transfer money to other country
		if(isset($_POST['transferform'])){
			?>
			<form method="post" action=""> 
				<label for="countryselect">Select country:</label>
				<?php
				$result = mysqli_query($mysqli,"SELECT country FROM countryinfo WHERE country != '$nationality'");
				$columnValues = Array();
				
				while ( $row = mysqli_fetch_assoc($result) ) {
				  $columnValues[] = $row['country'];
				}
				// Sort the data with wek ascending order, add $mar as the last parameter, to sort by the common key
				asort($columnValues);
				?>
				<select required name="countryselect" type="text">
					<?php	        
					// Iterating through the product array
					foreach($columnValues as $item){
						?>
					 	<option value="<?php echo strtolower($item); ?>"><?php echo $item; ?></option>
					    <?php
				    }
				    ?>
				</select> 
				<input type="number" size="25" required autocomplete="off" id="nogold" name="nogold" min="1" step="0.01" />
				<button type="submit" name="transfergold" />Transfer gold</button>
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

		if(isset($_POST['transfergold'])){
			$country = $mysqli->escape_string($_POST['countryselect']);
			$nogold = $mysqli->escape_string($_POST['nogold']);
			$nogold = (double) $nogold;
			if($nogold <= 0){
				$nogold = 1;
			}
			
			$result2 = $mysqli->query("SELECT id FROM countryinfo WHERE country='$country'") or die($mysqli->error());
			$count = $result2->num_rows;
			if($count != 0){
				$result = $mysqli->query("SELECT nodecisions FROM countryinfo WHERE country='$nationality'") or die($mysqli->error());
				$row = mysqli_fetch_array($result);
				$nodecisions=$row['nodecisions'];
				
				$nodecisions=$nodecisions+1;
				
				if($nodecisions<=$maxnodecisions){
					$sql = "UPDATE countryinfo SET nodecisions='$nodecisions' WHERE country='$nationality'";
					mysqli_query($mysqli, $sql);
					
					$sql = "INSERT INTO congress (type, country, start, extraint, extratext) " 
					. "VALUES ('transfergold','$nationality',NOW(), '$nogold', '$country')";
			 		mysqli_query($mysqli, $sql);
					
					echo "Done!";
					
					?>
					<script>
					    if ( window.history.replaceState ) {
					        window.history.replaceState( null, null, window.location.href );
					    }
					</script>
					<?php
				}
			}
		}
		?> </div> <?php

	}elseif($showmarket=="true"){
		$result = $mysqli->query("SELECT id, country, amount, price, owner FROM marketplace WHERE type='hospital' AND country='$nationality'") or die($mysqli->error());
			$count = $result->num_rows;
			if($count!=0){
				for ($set=array(); $row=$result->fetch_assoc(); $set[]=$row);
				//print_r($set);
					
				//sort data in descending salary/joboffer
				foreach ($set as $key => $row){
			   		$pricesort[$key]  = $row['price'];
				}
				// Sort the data with wek ascending order, add $mar as the last parameter, to sort by the common key
				array_multisort($pricesort, SORT_ASC, $set);
	
				//print_r($set);
				echo nl2br(" \n");
				
				foreach ($set as $key => $row){
					?> <div class="listbox"> <?php
					$id[$key]  = $row['id'];
				    $country[$key]  = $row['country'];
					$amount[$key]  = $row['amount'];
					$price[$key]  = $row['price'];
					$owner[$key]  = $row['owner'];
					$type="hospital";
					
					echo "Number of hospitals for sale: $amount[$key] | Price: $price[$key] | Owner: $owner[$key]";
					
					?>
					<form method="post" action="">
						<input type="hidden" name="id" value="<?php echo "$id[$key]"; ?>" />
						<input type="hidden" name="amount" value="<?php echo "$amount[$key]"; ?>" />
						<input type="hidden" name="type" value="<?php echo "$type[$key]"; ?>" />
						<input type="hidden" name="country" value="<?php echo "$country[$key]"; ?>" />
						<input type="hidden" name="price" value="<?php echo "$price[$key]"; ?>" />
						<input type="hidden" name="owner" value="<?php echo "$owner[$key]"; ?>" />
						<input type="hidden" name="type" value="<?php echo "$type"; ?>" />
						<label for="nobuyitems">Number to buy:</label>
						<input type="number" size="25" required autocomplete="off" id="nobuyitems" name="nobuyitems" min="0.01" step="0.01" />
						<button type="submit" name="buyhospital" />Buy</button>
					</form>
					<?php		
					echo nl2br(" \n");
					?> </div> <?php
				}
			}else{
				echo'<div class="boxed">There are no offers on the market!</div>';
			}
			?>
			<script>
			    if ( window.history.replaceState ) {
			        window.history.replaceState( null, null, window.location.href );
			    }
			</script>
			<?php
			
			if(isset($_POST['buyhospital'])){
				$id = $mysqli->escape_string($_POST['id']);
				$nobuyitems = $mysqli->escape_string($_POST['nobuyitems']);
				$nobuyitems = (int) $nobuyitems;
				if($nobuyitems <=0){
					$nobuyitems = 1;
				}
				
				$country = $nationality;
				
				$result = $mysqli->query("SELECT * FROM marketplace WHERE id='$id' AND country='$country' AND type='hospital'") or die($mysqli->error());
				$row = mysqli_fetch_array($result);
				$type = $row['type'];
				$owner = $row['owner'];
				$price = $row['price'];
				$amount = $row['amount'];
				
				$result = $mysqli->query("SELECT * FROM countryinfo WHERE country='$nationality'") or die($mysqli->error());
				$row = mysqli_fetch_array($result);
				$cur = $row['currency'];
				$vat = $row['vat'];
				$countrymoney = $row['money'];
				$hospital = $row['hospital'];
								
				$price=$price*$nobuyitems;
				$moneyafter=$countrymoney-$price;
					
				if($moneyafter>=0){
					$itemafter=$amount-$nobuyitems;
					if($itemafter>=0){
						//update buyer and country
						$hospital=$hospital+$nobuyitems;
										
						$tax=$price*($vat/100);
						$moneyafter=$moneyafter+$tax;
							
						$sql = "UPDATE countryinfo SET money ='$moneyafter', hospital ='$hospital' WHERE country='$location'";
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
						echo "Not enough $type for sale!";
					}
				}else{
					echo "You don't have enough money";
				}
				?>
				<script>
				    if ( window.history.replaceState ) {
				        window.history.replaceState( null, null, window.location.href );
				    }
				</script>
				<?php
			}
	}		
}else{
	echo "You are not minister of finance!";

}

?>
</div>
</body>
<footer>
<?php require 'bottombar.php'; ?>
</footer>
</html>
