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
  <title>Country president</title>
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
$countrypresident=$row['countrypresident'];
$finance=$row['finance'];
$foreignaffairs=$row['foreignaffairs'];
$immigration=$row['immigration'];
//$gold=$row['gold'];
//$vat=$row['vat'];
//$worktax=$row['worktax'];
$nodecisions=$row['nodecisions'];
$moneycreation=$row['moneycreation'];

$maxnodecisions=15;
$maxmoneycreation=1000;
$napcost=50;
$warcost=70;

$xx=2;

if($countrypresident==$username){
	?> <div class="textbox"> <?php
	echo nl2br ("<div class=\"h1\">Country president of $nationality</div>");
	?> <hr /> <?php
	
	echo nl2br("If you are the monarch of more than one kingdom you need to change your nationality to visit the pages of your other holdings. this can be done in the account page under the nationality column. \n");
	
	echo nl2br("The max number of decisions to make is $maxnodecisions per term. $nodecisions already taken this term. \n");
	echo nl2br("The max number of money to change is $maxmoneycreation per term. $moneycreation already used. \n");
	?> </div> <?php
	
	?> <div class="textbox"> <?php	
	
	//buttons for pages
	?>
	<div class="textbox">
		<form method="post" action="">
			<button type="submit" name="financeminform" />Appoint a minister of finance</button>
			<button type="submit" name="foreignminform" />Appoint a minister of foreign affairs</button>
			<button type="submit" name="immigrationminform" />Appoint a minister of immigration</button>
			<button type="submit" name="governtypeform" />Change the government type</button>
			<button type="submit" name="nationalreligionform" />Change the national religion</button>
			<button type="submit" name="diplomacyform" />Diplomacy</button>
			<button type="submit" name="hospitalform" />Use hospital in region</button>
		</form>
	</div>
	<?php
	?> <hr /> <?php
	
	//change minister of finance
	if(isset($_POST['financeminform'])){
		?>
		<form method="post" action="">
			<label for="newminister">Enter name of minister of finance:</label>
			<input type="text" pattern="[a-zA-Z0-9-]+" size="25" required autocomplete="off" id="newminister" name="newminister"/>
			<button type="submit" name="changeministerfinance"  /><?php echo "Set minister"; ?></button>
		</form>
		<?php
	}
	if(isset($_POST['changeministerfinance'])){
		$newminister = $mysqli->escape_string($_POST['newminister']);
		
		$result = $mysqli->query("SELECT nationality, username FROM users WHERE username='$newminister'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$nationalitynewmin=$row['nationality'];
		$checkusername=$row['username'];
		
		$result = $mysqli->query("SELECT * FROM countryinfo WHERE country='$nationality'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$nodecisions=$row['nodecisions'];
		$nodecisions=$nodecisions+1;

		$result = $mysqli->query("SELECT * FROM congress WHERE (type='financemin' OR type='foreignmin' OR type='immigrationmin') AND extratext='$newminister'") or die($mysqli->error());
		
		if($result->num_rows == 0 ){
			if($nodecisions<=$maxnodecisions){		
				if($nationalitynewmin == $nationality && $checkusername==$newminister){
					if($xx==2){
						$sql = "UPDATE countryinfo SET nodecisions='$nodecisions' WHERE country='$nationality'";
						mysqli_query($mysqli, $sql);
						
						$sql = "INSERT INTO congress (type, country, start, extratext) " 
						. "VALUES ('financemin','$nationality',NOW(),'$newminister')";
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
						echo "This user is already a minister!";
					}
				}else{
					echo "This user has a different nationality!";
				}
			}else{
				echo "Not enough decissions left!";
			}
		}else{
			echo "A vote is already in progress about this user!";
		}
	}
	
	echo nl2br(" \n");
	
	//change minister of foreign affairs
	if(isset($_POST['foreignminform'])){
		?>
		<form method="post" action="">
			<label for="newminister">Enter name of minister of foreign affairs:</label>
			<input type="text" pattern="[a-zA-Z0-9-]+" size="25" required autocomplete="off" id="newminister" name="newminister"/>
			<button type="submit" name="changeministerforeign"  /><?php echo "Set minister"; ?></button>
		</form>
		<?php
	}
	
	if(isset($_POST['changeministerforeign'])){
		$newminister = $mysqli->escape_string($_POST['newminister']);
		
		$result = $mysqli->query("SELECT nationality, username FROM users WHERE username='$newminister'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$nationalitynewmin=$row['nationality'];
		$checkusername=$row['username'];
		
		$result = $mysqli->query("SELECT * FROM countryinfo WHERE country='$nationality'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$nodecisions=$row['nodecisions'];
		$nodecisions=$nodecisions+1;
		
		$result = $mysqli->query("SELECT * FROM congress WHERE (type='financemin' OR type='foreignmin' OR type='immigrationmin') AND extratext='$newminister'") or die($mysqli->error());
		
		if($result->num_rows == 0 ){
			if($nodecisions<=$maxnodecisions){		
				if($nationalitynewmin == $nationality && $checkusername==$newminister){
					if($xx==2){
						$sql = "UPDATE countryinfo SET nodecisions='$nodecisions' WHERE country='$nationality'";
						mysqli_query($mysqli, $sql);
						
						$sql = "INSERT INTO congress (type, country, start, extratext) " 
						. "VALUES ('foreignmin','$nationality',NOW(),'$newminister')";
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
						echo "This user is already a minister!";
					}
				}else{
					echo "This user has a different nationality!";
				}
			}else{
				echo "Not enough decissions left!";
			}
		}else{
			echo "A vote is already in progress about this user!";
		}
	}
		echo nl2br(" \n");
	
	//change minister of immigration
	if(isset($_POST['immigrationminform'])){
		?>
		<form method="post" action="">
			<label for="newminister">Enter name of minister of immigration:</label>
			<input type="text" pattern="[a-zA-Z0-9-]+" size="25" required autocomplete="off" id="newminister" name="newminister"/>
			<button type="submit" name="changeministerimmigration"  /><?php echo "Set minister"; ?></button>
		</form>
		<?php
	}
	if(isset($_POST['changeministerimmigration'])){
		$newminister = $mysqli->escape_string($_POST['newminister']);
		
		$result = $mysqli->query("SELECT nationality, username FROM users WHERE username='$newminister'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$nationalitynewmin=$row['nationality'];
		$checkusername=$row['username'];
		
		$result = $mysqli->query("SELECT * FROM countryinfo WHERE country='$nationality'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$nodecisions=$row['nodecisions'];
		$nodecisions=$nodecisions+1;
		
		$result = $mysqli->query("SELECT * FROM congress WHERE (type='financemin' OR type='foreignmin' OR type='immigrationmin') AND extratext='$newminister'") or die($mysqli->error());
		
		if($result->num_rows == 0 ){
			if($nodecisions<=$maxnodecisions){		
				if($nationalitynewmin == $nationality && $checkusername==$newminister){
					if($xx==2){
						$sql = "UPDATE countryinfo SET nodecisions='$nodecisions' WHERE country='$nationality'";
						mysqli_query($mysqli, $sql);
						
						$sql = "INSERT INTO congress (type, country, start, extratext) " 
						. "VALUES ('immigrationmin','$nationality',NOW(),'$newminister')";
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
						echo "This user is already a minister!";
					}
				}else{
					echo "This user has a different nationality!";
				}
			}else{
				echo "Not enough decissions left!";
			}
		}else{
			echo "A vote is already in progress about this user!";
		}
	}
	?> </div> <?php
	
	?> <div class="textbox"> <?php
	//change government type
	if(isset($_POST['governtypeform'])){
		?>
		<form method="post" action="">
			<label for="type">Change government type:</label>
	   		<select required name="type" id="type" type="text">
				<option value="democracy">Elective monarchy</option>
				<option value="kingdom">Absolute monarchy</option>
	   		</select>
	   		<button type="submit" name="change" /><?php echo "Submit"; ?></button>
		</form>   		
		<?php
	}
	
	if(isset($_POST['change'])){
		$result = $mysqli->query("SELECT * FROM countryinfo WHERE country='$nationality'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$countrypresident=$row['countrypresident'];
		$nodecisions=$row['nodecisions'];
		$changedgov=$row['changedgov'];
		
		$type = $mysqli->escape_string($_POST['type']);
		
		$nodecisions=$nodecisions+1;
		
		if($nodecisions<=$maxnodecisions){
			if($changedgov==0){
				if($type=="democracy"){ //change democracy				
					$sql = "INSERT INTO congress (type, country, start, extratext) " 
					. "VALUES ('government','$nationality',NOW(),'$type')";
			 		mysqli_query($mysqli, $sql);
				}else{ //change kingdom					
					$sql = "INSERT INTO congress (type, country, start, extratext) " 
					. "VALUES ('government','$nationality',NOW(),'$type')";
			 		mysqli_query($mysqli, $sql);
				}
			}else{
				echo "You already changed government type this month!";
			}
		}else{
			echo "You already made too many decisions!";
		}
	}
	?> </div> <?php

?> <div class="textbox"> <?php
//change religion
if(isset($_POST['nationalreligionform'])){
	$result = $mysqli->query("SELECT changedrel FROM countryinfo WHERE country='$nationality'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$changedrel=$row['changedrel'];
	if($changedrel==0){
		echo nl2br ("<div class=\"t1\"> Change the national religion. This will give bonus strength during battles when the attacked regions biggest religion matches the national religion.</div>");
		?>
		<form method="post" action="">
			<?php
			$result = mysqli_query($mysqli,"SELECT name FROM religion");
			$columnValues = Array();
			while ( $row = mysqli_fetch_assoc($result) ) {
		  		$columnValues[] = $row['name'];
			}
			// Sort the data with wek ascending order, add $mar as the last parameter, to sort by the common key
			asort($columnValues);
			?>
		   	<select required name="religion" id="religion" type="text">
		    	<?php
		        	
		      	// Iterating through the product array
		      	?>
		        <option value="NULL"><?php echo "None"; ?></option>
		        <?php
		        foreach($columnValues as $item){
		        	?>
		        	<option value="<?php echo strtolower($item); ?>"><?php echo $item; ?></option>
		        	<?php
		        }
		        ?>
		   	</select>
		   	<button type="submit" name="setreligion" /><?php echo "Submit"; ?></button>
		</form>   		
		<?php
	}
}

if(isset($_POST['setreligion'])){
	$religion = $mysqli->escape_string($_POST['religion']);
	
	$result2 = $mysqli->query("SELECT id FROM religion WHERE name='$religion'") or die($mysqli->error());
	$count = $result2->num_rows;
	
	if($count != 0){
		$result = $mysqli->query("SELECT * FROM countryinfo WHERE country='$nationality'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$nodecisions=$row['nodecisions'];
		$nodecisions=$nodecisions+1;
		
		if($nodecisions<=$maxnodecisions){
			$sql = "UPDATE countryinfo SET changedrel='1', nodecisions='$nodecisions' WHERE country='$nationality'";
			mysqli_query($mysqli, $sql);
			
			$sql = "INSERT INTO congress (type, country, start, extratext) " 
			. "VALUES ('statereligion','$nationality',NOW(),'$religion')";
	 		mysqli_query($mysqli, $sql);
			
			echo "Done!";
		}else{
			echo "You already made too many decisions!";
		}
	}
}

?> </div> <?php
		
?> <div class="textbox"> <?php
//diplomacy
if(isset($_POST['diplomacyform'])){
	echo nl2br(" \n");
	?>
	<form method="post" action="">
		<label for="diplomacy">Diplomacy:</label>
	   	<select required name="diplomacy" id="diplomacy" type="text">
			<option value="war">Declare war</option>
			<option value="nap">Offer non agression pact</option>
	   	</select>
	   	<button type="submit" name="dodiplomacy" /><?php echo "Submit"; ?></button>
	</form>   		
	<?php
}

if(isset($_POST['dodiplomacy'])){
	$diplomacy = $mysqli->escape_string($_POST['diplomacy']);
	if($diplomacy=='war'){
			echo'<div class="t1">It costs 50 gold to start a war against another country which will be subtracted from the national treassury once the congress approves the issue.</div>';
			?>
			<form method="post" action="">
			<label for="diplomacy">Select country:</label>
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
   			<input type="hidden" name="diplomacy" value="<?php echo "$diplomacy"; ?>" />
   			<button type="submit" name="countrywar" /><?php echo "Submit"; ?></button>
			</form>   		
			<?php
		
	//nap
	}else{
			echo'<div class="t1">It costs 50 gold to set up a NAP with another country which will be subtracted from the national treassury once the congress of both countries approve the issue.</div>';
			?>
			<form method="post" action="">
			<label for="diplomacy">Select country:</label>
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
   			<input type="hidden" name="diplomacy" value="<?php echo "$diplomacy"; ?>" />
   			<button type="submit" name="countryint" /><?php echo "Submit"; ?></button>
			</form>   		
			<?php

	}
}
	
//set up nap offer
if(isset($_POST['countryint'])){
	$country2 = $mysqli->escape_string($_POST['country2']);
	$diplomacy = $mysqli->escape_string($_POST['diplomacy']);
	
	$result = $mysqli->query("SELECT * FROM countryinfo WHERE country='$nationality'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$nodecisions=$row['nodecisions'];
	$nodecisions=$nodecisions+1;
	
	$result2 = $mysqli->query("SELECT id FROM countryinfo WHERE country='$country2'") or die($mysqli->error());
	$count = $result2->num_rows;
	if($count != 0){
		$result2 = $mysqli->query("SELECT * FROM diplomacy WHERE (country1='$nationality' AND country2='$country2') OR (country1='$country2' AND country2='$nationality')") or die($mysqli->error());
		$count2 = $result2->num_rows;
		
		if($count2 == 0){
			if($nodecisions<=$maxnodecisions){
				$sql = "UPDATE countryinfo SET nodecisions='$nodecisions' WHERE country='$nationality'";
				mysqli_query($mysqli, $sql);
				
				$sql = "INSERT INTO congress (type, country, start, extratext, extraint) " 
				. "VALUES ('napoffer','$nationality',NOW(),'$country2', '0')";
		 		mysqli_query($mysqli, $sql);
				
				echo "Done!";
				
				?>
				<script>
				    if ( window.history.replaceState ) {
				        window.history.replaceState( null, null, window.location.href );
				    }
				</script>
				<?php
			}else{
				echo "The country doesn't have enough currency or decisions!";
			}
		}else{
			echo "A nap is already inplace between these countries!";
		}
	}

}

//set up war offer
if(isset($_POST['countrywar'])){
	// check for nap	
	$country2 = $mysqli->escape_string($_POST['country2']);
	
	$result2 = $mysqli->query("SELECT id FROM countryinfo WHERE country='$country2'") or die($mysqli->error());
	$count = $result2->num_rows;
	
	if($count != 0){
		$result = $mysqli->query("SELECT statereligion FROM countryinfo WHERE country='$nationality'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$statereligion=$row['statereligion'];
		
		$result = $mysqli->query("SELECT crusade FROM religion WHERE name='$statereligion'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$crusade=$row['crusade'];
		
		$result = $mysqli->query("SELECT * FROM diplomacy WHERE type='nap' AND (( country1='$nationality' AND country2='$country2') OR (country1='$country2' AND country2='$nationality')) AND acceptnap='1' ") or die($mysqli->error());
		for ($set=array(); $row=$result->fetch_assoc(); $set[]=$row);
	    for($i = 0, $c = count($set); $i < $c; $i++) var_dump($set[$i]);
		
		$result2 = $mysqli->query("SELECT id FROM diplomacy WHERE type='war' AND ((country1='$nationality' AND country2='$country2') OR (country2='$nationality' AND country1='$country2'))") or die($mysqli->error());
		$count = $result2->num_rows;
		
		if($nationality != $country2){
			if($i=='0' or $crusade==$country2){
				if($count == 0){
					$result = $mysqli->query("SELECT * FROM countryinfo WHERE country='$nationality'") or die($mysqli->error());
					$row = mysqli_fetch_array($result);
					$nodecisions=$row['nodecisions'];
					$nodecisions=$nodecisions+1;
					if($nodecisions<=$maxnodecisions){
						$country2 = $mysqli->escape_string($_POST['country2']);
						$diplomacy = $mysqli->escape_string($_POST['diplomacy']);
						date_default_timezone_set('UTC');
						$date=date("d");
						
						$sql = "UPDATE countryinfo SET nodecisions='$nodecisions' WHERE country='$nationality'";
						mysqli_query($mysqli, $sql);
						
							$sql = "INSERT INTO congress (type, country, start, extratext) " 
						. "VALUES ('waroffer','$nationality',NOW(),'$country2')";
				 		mysqli_query($mysqli, $sql);
						
						echo "Done!";
					}else{
						echo "The country doesn't have enough decisions!";
					}
				}else{
					echo "$nationality already is at war with $country2 ";
				}
			}else{
				echo nl2br(" \n");
				echo "You have an nap with that country!";
			}
		}else{
			echo "You can't attack your own country!";
		}
	}
}
?> </div> <?php

/*
?> <div class="textbox"> <?php
//accept nap offers
echo nl2br(" \n");
?>
<form method="post" action="">
   	<button type="submit" name="shownap" /><?php echo "Show nap offers"; ?></button>
</form>   		
<?php

if(isset($_POST['shownap'])){
	$result = $mysqli->query("SELECT * FROM diplomacy WHERE type='nap' AND country2='$nationality' AND acceptnap='0' ") or die($mysqli->error());
	for ($set=array(); $row=$result->fetch_assoc(); $set[]=$row);
	//print_r($set);
		
	echo nl2br(" \n");
		
	//create forms for every item
	foreach ($set as $key => $value) {
		$country1[$key] = $value['country1'];
		$id[$key]=$value['id'];
			
		echo "Nap offer from $country1[$key]";
		?>
		<form method="post" action="">
			<input type="hidden" name="id" value="<?php echo "$id[$key]"; ?>" />
			<input type="hidden" name="country2" value="<?php echo "$country1[$key]"; ?>" />

			<button type="submit" name="accept" value="<?php echo "1"; ?>" /><?php echo "accept"; ?></button>
			<button type="submit" name="accept" value="<?php echo "0"; ?>" /><?php echo "don't accept"; ?></button>
		</form>
		<?php
		echo nl2br(" \n");
	}
}
//accept nap offer 2
if(isset($_POST['accept'])){
	$id = $mysqli->escape_string($_POST['id']);
	$country2 = $mysqli->escape_string($_POST['country2']);
	$accept = $mysqli->escape_string($_POST['accept']);
	
	if($accept==1){
	$result = $mysqli->query("SELECT * FROM countryinfo WHERE country='$nationality'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$money=$row['money'];
		$nodecisions=$row['nodecisions'];
		$money=$money-$napcost;
		$nodecisions=$nodecisions+1;
		if($money>=0 && $nodecisions<=$maxnodecisions){
			$sql = "UPDATE countryinfo SET money ='$money', nodecisions='$nodecisions' WHERE country='$nationality'";
			mysqli_query($mysqli, $sql);

			date_default_timezone_set('UTC'); //current date
			$datecur = date("Y-m-d H:i:s"); 
			echo "test";
	
			$sql = "UPDATE diplomacy SET acceptnap ='1',date=NOW() WHERE id='$id'";
			mysqli_query($mysqli, $sql);
			
			$content= "$nationality signed a nap with $country2";
			$sql = "INSERT INTO events (date, content) " 
		     . "VALUES (NOW(),'$content')";
			mysqli_query($mysqli2, $sql);
		}else{
			echo "The country doesn't have enough currency or decisions!";
		}
	}else{
		$sql = "DELETE FROM diplomacy WHERE id='$id'";
		mysqli_query($mysqli, $sql);
	}
}
?> </div> <?php
*/

?> <div class="textbox"> <?php
//use hospital
if(isset($_POST['hospitalform'])){
	?>
	<br><br>
	<form method="post" action=""> 
		<?php
		$result = mysqli_query($mysqli,"SELECT name FROM region WHERE curowner='$nationality'");
		$columnValues = Array();
		
		while ( $row = mysqli_fetch_assoc($result) ) {
		  $columnValues[] = $row['name'];
		}
		// Sort the data with wek ascending order, add $mar as the last parameter, to sort by the common key
		asort($columnValues);
		?>
		<select required name="hospitalregion" type="text">
			<?php	        
			// Iterating through the product array
			foreach($columnValues as $item){
				?>
			 	<option value="<?php echo strtolower($item); ?>"><?php echo $item; ?></option>
			    <?php
		    }
		    ?>
		</select> 
		<button type="submit" name="usehospital" />Use hospital</button>
	</form>
	<?php
}

if(isset($_POST['usehospital'])){
	$hospitalregion = $mysqli->escape_string($_POST['hospitalregion']);

	$result2 = $mysqli->query("SELECT id FROM region WHERE name='$hospitalregion' AND curowner='$nationality'") or die($mysqli->error());
	$count = $result2->num_rows;
	
	if($count != 0){
		$result = $mysqli->query("SELECT hospital FROM countryinfo WHERE country='$nationality'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$hospital = $row['hospital'];
		
		$hospital=$hospital-1;
		if($hospital>=0){
			$result = $mysqli->query("SELECT epidemic FROM region WHERE name='$hospitalregion'") or die($mysqli->error());
			$row = mysqli_fetch_array($result);
			$epidemic = $row['epidemic'];
			
			if($epidemic==1){
				$cure = rand(1, 10);
				if($cure==1){
					$sql = "UPDATE region SET epidemic='0' WHERE name='$hospitalregion'";
					mysqli_query($mysqli, $sql);
					
					$sql = "UPDATE countryinfo SET hospital='$hospital' WHERE country='$nationality'";
					mysqli_query($mysqli, $sql);
					
					echo'<div class="boxed">Done!</div>';
				}else{
					echo'<div class="boxed">The used hospital didn\'t cure the epidemic!</div>';
					
					$sql = "UPDATE countryinfo SET hospital='$hospital' WHERE country='$nationality'";
					mysqli_query($mysqli, $sql);
				}
			}else{
				echo'<div class="boxed">Region doesn\'t suffer from an epidemic!</div>';
			}
		}else{
			echo'<div class="boxed">Country doesn\'t have enough hospitals!</div>';
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
}else{
	echo "You are not country president!";

}

?>
</div>
</body>
<footer>
<?php require 'bottombar.php'; ?>
</footer>
</html>
