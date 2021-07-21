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
$foreignaffairs=$row['foreignaffairs'];
//$gold=$row['gold'];
//$vat=$row['vat'];
//$worktax=$row['worktax'];
$nodecisions=$row['nodecisions'];
$moneycreation=$row['moneycreation'];
$hospital=$row['hospital'];

$maxnodecisions=15;
$maxmoneycreation=1000;
$napcost=5;
$warcost=0;

if($foreignaffairs==$username){
	echo nl2br ("<div class=\"h1\">Minister of foreign affairs of $nationality</div>");
	?> <hr /> <?php
	
	?> <div class="textbox"> <?php
	echo nl2br("The max number of decisions to make is $maxnodecisions per term. $nodecisions already taken this term. \n");
	echo nl2br("The max number of money to change is $maxmoneycreation per term. $moneycreation already used. \n");
	echo nl2br("The amount of hospitals the country has is: $hospital")
	?> </div> <?php
	
?> <div class="textbox"> <?php
//show wars
echo nl2br(" \n");
?>
<form method="post" action="">
   	<button type="submit" name="showwars" /><?php echo "Show wars"; ?></button>
</form>   		
<?php

if(isset($_POST['showwars'])){
	$result = $mysqli->query("SELECT * FROM diplomacy WHERE type='war' AND ( country1='$nationality' OR country2='$nationality' ) ") or die($mysqli->error());
	for ($set=array(); $row=$result->fetch_assoc(); $set[]=$row);
	//print_r($set);
		
	echo nl2br(" \n");
		
	//create forms for every item
	foreach ($set as $key => $value) {
		?> <div class="listbox"> <?php
		$country1[$key] = $value['country1'];
		$country2[$key] = $value['country2'];
		$id[$key]=$value['id'];
		$peace[$key]=$value['peace'];
		$attackcountry1[$key]=$value['attackcountry1'];
		$attackcountry2[$key]=$value['attackcountry2'];
			
		echo "War between $country1[$key] and $country2[$key]";
		
		//peace 1 by country1 and peace 2 by country 2; Dus als peace==1 dan country 2 accepteren
		if($peace[$key]==0){
			?>
			<form onsubmit="return confirm('Are you sure?');" method="post" action="">
				<input type="hidden" name="id" value="<?php echo "$id[$key]"; ?>" />
				<input type="hidden" name="country1" value="<?php echo "$country1[$key]"; ?>" />
				<input type="hidden" name="country2" value="<?php echo "$country2[$key]"; ?>" />
				<button type="submit" name="peaceoffer" value="<?php echo "1"; ?>" /><?php echo "Offer peace"; ?></button>
			</form>
			<?php
		}
		
		?>		
		<form onsubmit="return confirm('Are you sure?');" method="post" action="">
			<input type="hidden" name="id" value="<?php echo "$id[$key]"; ?>" />
			<input type="hidden" name="country1" value="<?php echo "$country1[$key]"; ?>" />
			<input type="hidden" name="country2" value="<?php echo "$country2[$key]"; ?>" />
			<label for="bioweapon">Costs are 100 gold:</label>
			<button type="submit" name="bioweapon" value="<?php echo "1"; ?>" /><?php echo "Use biological weapons"; ?></button>
		</form>

		<form onsubmit="return confirm('Are you sure?');" method="post" action="">
			<input type="hidden" name="id" value="<?php echo "$id[$key]"; ?>" />
			<input type="hidden" name="country1" value="<?php echo "$country1[$key]"; ?>" />
			<input type="hidden" name="country2" value="<?php echo "$country2[$key]"; ?>" />
			<label for="hospitaluse">Use hospital gives 500 energy:</label>
			<button type="submit" name="hospitaluse" value="<?php echo "1"; ?>" /><?php echo "Use hospital"; ?></button>
		</form>
				
		<form method="post" action="">
			<input type="hidden" name="id" value="<?php echo "$id[$key]"; ?>" />
			<input type="hidden" name="country1" value="<?php echo "$country1[$key]"; ?>" />
			<input type="hidden" name="country2" value="<?php echo "$country2[$key]"; ?>" />
			<label for="paygold:">Total amount to pay:</label>
			<input type="number" size="25" required autocomplete="off" step="0.01" id="paygold" name='paygold' min="0.01" />
			<label for="priceperdamage:">Price per 100 damage:</label>
			<input type="number" size="25" required autocomplete="off" step="0.01" id="priceperdamage" name='priceperdamage' min="0.01" />
			<label for="paydamage">Pay amount per 100 damage:</label>
			<button type="submit" name="paydamage" value="<?php echo "1"; ?>" /><?php echo "Submit"; ?></button>
		</form>
		<?php

			if($country1[$key]==$nationality && ($attackcountry1[$key]=='NULL' || $attackcountry1[$key]==NULL)){
				//check neighbooring regions
				?>				
				<form method="post" action="">
					<input type="hidden" name="id" value="<?php echo "$id[$key]"; ?>" />
					<input type="hidden" name="country1" value="<?php echo "$country1[$key]"; ?>" />
					<input type="hidden" name="country2" value="<?php echo "$country2[$key]"; ?>" />

					<?php //................................attack region
			
				$result2 = $mysqli->query("SELECT * FROM region WHERE curowner='$nationality' ") or die($mysqli->error());
				for ($set2=array(); $row2=$result2->fetch_assoc(); $set2[]=$row2);
				
				$result3 = $mysqli->query("SELECT * FROM region WHERE curowner='$country2[$key]' ") or die($mysqli->error());
				for ($set3=array(); $row3=$result3->fetch_assoc(); $set3[]=$row3);
				
				echo nl2br(" \n");
				$attack=array();
		
				//foreach own region
				foreach ($set2 as $key2 => $value2) {
					$name2[$key2] = $value2['name'];
					$curowner2[$key2] = $value2['curowner'];
					//echo "$name2[$key2]";
					
					//foreach enemy region
					foreach ($set3 as $key3 => $value3) {
						$name3[$key3] = $value3['name'];
						$curowner3[$key3] = $value3['curowner'];
						//echo "$name3[$key3]";
						
						//foreach array in border array
						foreach ($borders as $key4 => $value4) {
							$name4[$key4] = $value4['name'];
							$border1[$key4]=$value4['border1'];
							$border2[$key4]=$value4['border2'];
							$border3[$key4]=$value4['border3'];
							$border4[$key4]=$value4['border4'];
							$border5[$key4]=$value4['border5'];
							
							//check if region is not already attacked
							$searchname = $mysqli->escape_string($name4[$key4]);
							$result4 = $mysqli->query("SELECT id FROM diplomacy WHERE attackcountry1='$searchname'") or die($mysqli->error());
							$count = $result4->num_rows;
							$result5 = $mysqli->query("SELECT id FROM diplomacy WHERE attackcountry2='$searchname'") or die($mysqli->error());
							$count2 = $result5->num_rows;
							
							
							//if region==enemy region && (own region==border1 || border2 || border3)
							if($count==0 && $count2==0 && $name4[$key4]==$name3[$key3] && ($name2[$key2]==$border1[$key4] || $name2[$key2]==$border2[$key4] || $name2[$key2]==$border3[$key4] || $name2[$key2]==$border4[$key4] || $name2[$key2]==$border5[$key4])){
								//echo "$name4[$key4]";
								array_push($attack,$name4[$key4]);
								


							}
						}
						
					}
					
					
				}
				?>
				<select required name="regionselect" id="regionselect" type="text">
					<?php
					// Iterating through the product array
        			foreach($attack as $item){
        				?>
        				<option value="<?php echo strtolower($item); ?>"><?php echo $item; ?></option>
        				<?php
        			}
				?> </select>
				<button type="submit" name="attackregion" value="<?php echo "1"; ?>" /><?php echo "Attack region"; ?></button>
				<?php
				
			?>
			</form>
			<?php
			
			}elseif($country2[$key]==$nationality && ($attackcountry2[$key]=='NULL' || $attackcountry2[$key]==NULL)){
				//check neighbooring regions
				
				?>				
				<form method="post" action="">
					<input type="hidden" name="id" value="<?php echo "$id[$key]"; ?>" />
					<input type="hidden" name="country1" value="<?php echo "$country1[$key]"; ?>" />
					<input type="hidden" name="country2" value="<?php echo "$country2[$key]"; ?>" />

					<?php //................................attack region
			
				$result2 = $mysqli->query("SELECT * FROM region WHERE curowner='$nationality' ") or die($mysqli->error());
				for ($set2=array(); $row2=$result2->fetch_assoc(); $set2[]=$row2);
				
				$result3 = $mysqli->query("SELECT * FROM region WHERE curowner='$country1[$key]' ") or die($mysqli->error());
				for ($set3=array(); $row3=$result3->fetch_assoc(); $set3[]=$row3);
				
				echo nl2br(" \n");
				$attack=array();
		
				//create forms for every item
				foreach ($set2 as $key2 => $value2) {
					$name2[$key2] = $value2['name'];
					$curowner2[$key2] = $value2['curowner'];
					//echo "$name2[$key2]";
					
					foreach ($set3 as $key3 => $value3) {
						$name3[$key3] = $value3['name'];
						$curowner3[$key3] = $value3['curowner'];
						//echo "$name3[$key3]";
						
						foreach ($borders as $key4 => $value4) {
							$name4[$key4] = $value4['name'];
							$border1[$key4]=$value4['border1'];
							$border2[$key4]=$value4['border2'];
							$border3[$key4]=$value4['border3'];
							$border4[$key4]=$value4['border4'];
							$border5[$key4]=$value4['border5'];
							
							//check if region is not already attacked
							$searchname = $mysqli->escape_string($name4[$key4]);
							$result4 = $mysqli->query("SELECT id FROM diplomacy WHERE attackcountry1='$searchname' OR attackcountry2='$name4[$key4]'") or die($mysqli->error());
							$count = $result4->num_rows;
							$result5 = $mysqli->query("SELECT id FROM diplomacy WHERE attackcountry2='$searchname'") or die($mysqli->error());
							$count2 = $result5->num_rows;
							
							if($count==0 && $count2 ==0 && $name4[$key4]==$name3[$key3] && ($name2[$key2]==$border1[$key4] || $name2[$key2]==$border2[$key4] || $name2[$key2]==$border3[$key4] || $name2[$key2]==$border4[$key4] || $name2[$key2]==$border5[$key4])){
								//echo "$name4[$key4]";
								array_push($attack,$name4[$key4]);
								


							}
						}
						
					}
					
					
				}
				?>
				<select required name="regionselect" id="regionselect" type="text">
					<?php
					// Iterating through the product array
        			foreach($attack as $item){
        				?>
        				<option value="<?php echo strtolower($item); ?>"><?php echo $item; ?></option>
        				<?php
        			}
				?> </select>
				<button type="submit" name="attackregion" value="<?php echo "1"; ?>" /><?php echo "Attack region"; ?></button>
				<?php
				
			?>
			</form>
			<?php
			
			}
			

		echo nl2br(" \n");
		?> </div> <?php
	}
}
//update attack
if(isset($_POST['attackregion'])){
	$id = $mysqli->escape_string($_POST['id']);
	$id = (int) $id;
	$regionselect = $mysqli->escape_string($_POST['regionselect']);
	
	$result2 = $mysqli->query("SELECT id FROM diplomacy WHERE type='war' AND (country1='$nationality' OR country2='$nationality')") or die($mysqli->error());
	$count = $result2->num_rows;
	if($count != 0){
		$result2 = $mysqli->query("SELECT id FROM region WHERE name='$regionselect'") or die($mysqli->error());
		$count = $result2->num_rows;
		if($count != 0){
			$result = $mysqli->query("SELECT * FROM diplomacy WHERE id='$id'") or die($mysqli->error());
			$row = mysqli_fetch_array($result);
			$country1=$row['country1'];
			$country2=$row['country2'];
			
			date_default_timezone_set('UTC');
			$date = date("Y\-m\-d H:i:s");
			
			if($country1=$nationality){
				$sql = "UPDATE diplomacy SET attackcountry1 ='$regionselect', attackcountry1start=NOW(), country11damage='0', country12damage='0' WHERE id='$id'";
				mysqli_query($mysqli, $sql);
				
				$content= "$nationality launched an attack on $regionselect";
				$sql = "INSERT INTO events (date, content) " 
			     . "VALUES (NOW(),'$content')";
				mysqli_query($mysqli2, $sql);
			}else{
				$sql = "UPDATE diplomacy SET attackcountry2 ='$regionselect', attackcountry2start=NOW(), country21damage='0', country22damage='0' WHERE id='$id'";
				mysqli_query($mysqli, $sql);
				
				$content= "$nationality launched an attack on $regionselect";
				$sql = "INSERT INTO events (date, content) " 
			     . "VALUES (NOW(),'$content')";
				mysqli_query($mysqli2, $sql);
			}
		}
	}
}

if(isset($_POST['peaceoffer'])){
	$id = $mysqli->escape_string($_POST['id']);
	$id = (int) $id;
	
	$result2 = $mysqli->query("SELECT id FROM diplomacy WHERE type='war' AND (country1='$nationality' OR country2='$nationality')") or die($mysqli->error());
	$count = $result2->num_rows;
	if($count != 0){
		$result = $mysqli->query("SELECT * FROM diplomacy WHERE id='$id'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$country1=$row['country1'];
		$country2=$row['country2'];
			
		if($country1==$nationality){
			$extratext=$country2;
		}else{
			$extratext=$country1;
		}
		
		$sql = "UPDATE diplomacy SET peace ='1' WHERE id='$id'";
		mysqli_query($mysqli, $sql);
		
		$sql = "INSERT INTO congress (type, country, start, extraint, extratext) " 
		. "VALUES ('peaceoffer','$nationality',NOW(), '$id', '$extratext')";
		mysqli_query($mysqli, $sql);
		
		echo "Done!";
	}
}

if(isset($_POST['bioweapon'])){
	$id = $mysqli->escape_string($_POST['id']);
	$id = (int) $id;
	
	$result2 = $mysqli->query("SELECT id FROM diplomacy WHERE type='war' AND (country1='$nationality' OR country2='$nationality')") or die($mysqli->error());
	$count = $result2->num_rows;
	if($count != 0){
		$result = $mysqli->query("SELECT * FROM diplomacy WHERE id='$id'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$country1=$row['country1'];
		$country2=$row['country2'];
		
		if($country1 == $nationality){
			?>
			<br><br>
			<form method="post" action=""> 
				<label for="regionselect">Select region:</label>
				<?php
				$result = mysqli_query($mysqli,"SELECT name FROM region WHERE curowner='$country2'");
				$columnValues = Array();
				
				while ( $row = mysqli_fetch_assoc($result) ) {
				  $columnValues[] = $row['name'];
				}
				// Sort the data with wek ascending order, add $mar as the last parameter, to sort by the common key
				asort($columnValues);
				?>
				<select required name="bioweaponregion" type="text">
					<?php	        
					// Iterating through the product array
					foreach($columnValues as $item){
						?>
					 	<option value="<?php echo strtolower($item); ?>"><?php echo $item; ?></option>
					    <?php
				    }
				    ?>
				</select> 
				<button type="submit" name="regionselect" />Attack region</button>
			</form>
			<?php
		}else{
			?>
			<br><br>
			<form method="post" action=""> 
				<label for="regionselect">Select region:</label>
				<?php
				$result = mysqli_query($mysqli,"SELECT name FROM region WHERE curowner='$country1'");
				$columnValues = Array();
				
				while ( $row = mysqli_fetch_assoc($result) ) {
				  $columnValues[] = $row['name'];
				}
				// Sort the data with wek ascending order, add $mar as the last parameter, to sort by the common key
				asort($columnValues);
				?>
				<select required name="bioweaponregion" type="text">
					<?php	        
					// Iterating through the product array
					foreach($columnValues as $item){
						?>
					 	<option value="<?php echo strtolower($item); ?>"><?php echo $item; ?></option>
					    <?php
				    }
				    ?>
				</select> 
				<button type="submit" name="regionselect" />Attack region</button>
			</form>
			<?php
		}
	}
}

if(isset($_POST['regionselect'])){
	$bioweaponregion = $mysqli->escape_string($_POST['bioweaponregion']);
	
	$result = $mysqli->query("SELECT curowner FROM region WHERE name='$bioweaponregion'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$curowner=$row['curowner'];
	
	$result2 = $mysqli->query("SELECT id FROM diplomacy WHERE type='war' AND ((country1='$nationality' AND country2='$curowner') OR (country1='$curowner' AND country2='$nationality'))") or die($mysqli->error());
	$count = $result2->num_rows;
	if($count != 0){
		$result = $mysqli->query("SELECT gold, nodecisions FROM countryinfo WHERE country='$nationality'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$gold = $row['gold'];
		$nodecisions=$row['nodecisions'];
		
		$nodecisions=$nodecisions+1;
		if($nodecisions<=$maxnodecisions){
			$sql = "UPDATE countryinfo SET nodecisions='$nodecisions' WHERE country='$nationality'";
			mysqli_query($mysqli, $sql);
			
			$sql = "INSERT INTO congress (type, country, start, extratext) " 
			. "VALUES ('bioweapon','$nationality',NOW(), '$bioweaponregion')";
	 		mysqli_query($mysqli, $sql);
			
			echo "Done!";
		}
	}
}

if(isset($_POST['hospitaluse'])){
	$id = $mysqli->escape_string($_POST['id']);
	$id = (int) $id;
	
	$result2 = $mysqli->query("SELECT id FROM diplomacy WHERE type='war' AND (country1='$nationality' OR country2='$nationality')") or die($mysqli->error());
	$count = $result2->num_rows;
	if($count!=0){
		$result = $mysqli->query("SELECT * FROM diplomacy WHERE id='$id'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$country1=$row['country1'];
		$country2=$row['country2'];
	
		$result = $mysqli->query("SELECT hospital FROM countryinfo WHERE country='$nationality'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$hospital = $row['hospital'];
		
		if($hospital != 0){
			if($nationality == $country1){
				$result = $mysqli->query("SELECT hospital1 FROM diplomacy WHERE id='$id'") or die($mysqli->error());
				$row = mysqli_fetch_array($result);
				$hospitalenergy = $row['hospital1'];
				
				$hospitalenergy=$hospitalenergy+500;
				$hospital=$hospital-1;
				
				$sql = "UPDATE countryinfo SET hospital='$hospital' WHERE country='$nationality'";
				mysqli_query($mysqli, $sql);
				
				$sql = "UPDATE diplomacy SET hospital1='$hospitalenergy' WHERE id='$id'";
				mysqli_query($mysqli, $sql);
				
				echo "Done!";
			}elseif($nationality == $country2){
				$result = $mysqli->query("SELECT hospita12 FROM diplomacy WHERE id='$id'") or die($mysqli->error());
				$row = mysqli_fetch_array($result);
				$hospitalenergy = $row['hospital2'];
				
				$hospitalenergy=$hospitalenergy+500;
				$hospital=$hospital-1;
				
				$sql = "UPDATE countryinfo SET hospital='$hospital' WHERE country='$nationality'";
				mysqli_query($mysqli, $sql);
				
				$sql = "UPDATE diplomacy SET hospital2='$hospitalenergy' WHERE id='$id'";
				mysqli_query($mysqli, $sql);
				
				echo "Done!";
			}
		}else{
			echo "Country doesn't have enough hospitals in inventory!";
		}
	}
}

if(isset($_POST['paydamage'])){
	$id = $mysqli->escape_string($_POST['id']);
	$id = (int) $id;
	$paygold = $mysqli->escape_string($_POST['paygold']);
	$paygold = (double) $paygold;
	if($paygold <= 0){
		$paygold = 1;
	}
	$priceperdamage = $mysqli->escape_string($_POST['priceperdamage']);
	$priceperdamage = (double) $priceperdamage;
	if($priceperdamage <= 0){
		$priceperdamage = 1;
	}
	
	$result2 = $mysqli->query("SELECT id FROM diplomacy WHERE type='war' AND (country1='$nationality' OR country2='$nationality')") or die($mysqli->error());
	$count = $result2->num_rows;
	if($count != 0){
		$result = $mysqli->query("SELECT * FROM diplomacy WHERE id='$id'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$country1=$row['country1'];
		$country2=$row['country2'];
		
		$result = $mysqli->query("SELECT gold FROM countryinfo WHERE country='$nationality'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$gold = $row['gold'];
		
		$result = $mysqli->query("SELECT paygold1, paygold2 FROM diplomacy WHERE id='$id'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$paygold1 = $row['paygold1'];
		$paygold2 = $row['paygold2'];
		
		$gold=$gold-$paygold;
		if($gold>=0){
			if($country1==$nationality){
				$paygold=$paygold+$paygold1;
				$sql = "UPDATE diplomacy SET paygold1='$paygold', goldperdamage1='$priceperdamage' WHERE id='$id'";
				mysqli_query($mysqli, $sql);
				
				$sql = "UPDATE countryinfo SET gold='$gold' WHERE country='$nationality'";
				mysqli_query($mysqli, $sql);
			}else{
				$paygold=$paygold+$paygold2;
				$sql = "UPDATE diplomacy SET paygold2='$paygold', goldperdamage2='$priceperdamage' WHERE id='$id'";
				mysqli_query($mysqli, $sql);
				
				$sql = "UPDATE countryinfo SET gold='$gold' WHERE country='$nationality'";
				mysqli_query($mysqli, $sql);
			}
		}else{
			echo "Country doesn't have enough gold!";
		}
	}
}

?> </div> <?php

}else{
	echo "You are not minister of foreign affairs!";

}

?>
</div>
</body>
<footer>
<?php require 'bottombar.php'; ?>
</footer>
</html>
