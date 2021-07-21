<?php 
require 'db.php';
require 'regionborders.php';
//require 'functions.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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
  <link rel="stylesheet" href="css/styletot.css">
</head>
<body>
<?php
// Display message about account verification link only once
if ( isset($_SESSION['message']) ){
	echo $_SESSION['message'];
              
	// Don't annoy the user with more messages upon page refresh
	unset( $_SESSION['message'] );
}
         
// Keep reminding the user this account is not active, until they activate
if ( !$active ){
	/*
	echo
	'<div class="boxed">
	Account is unverified, please confirm your email by clicking
	on the email link!
	</div>';
	*/
	die('Account is unverified, please confirm your email by clicking on the email link!');
}

// maintenance
$maintenance=0;
if($maintenance==1){
	die('Website is under maintenance please come back later');
}

// Check if user with that username already exists and create username in currency table
$result = $mysqli->query("SELECT * FROM currency WHERE usercur='$username'") or die($mysqli->error());

// We know table for inventory & currency exists if the rows returned are more than 0
if ( $result->num_rows > 0 ) {   
	//echo "goed";  
}else { // username doesn't already exist in a database, proceed...

    $sql = "INSERT INTO currency (usercur) " 
            . "VALUES ('$username')";
	$mysqli->query($sql);
	$sql2 = "INSERT INTO inventory (userinv) " 
            . "VALUES ('$username')";
	$mysqli->query($sql2);
	
	date_default_timezone_set('UTC'); //current date
	$datecur = date("Y-m-d H:i:s"); 
	echo "$datecur";
	$date = new DateTime($datecur);
	$date->add(new DateInterval('P3M')); // P1D means a period of 1 day
	
	$Datenew1 = $date->format('Y-m-d H:i:s');
	
	$sql3 = "INSERT INTO shop (username, trial) " 
            . "VALUES ('$username', '$Datenew1')";
	$mysqli->query($sql3);
	
	//update state for new users
	date_default_timezone_set('UTC'); //current date
	$datecur = date("Y-m-d H:i:s"); 
	//echo "$datecur";
	$date = new DateTime($datecur);
	$date->add(new DateInterval('PT8H')); // P1D means a period of 1 day
	
	$Datenew1 = $date->format('Y-m-d H:i:s');
	
	$sql = "UPDATE users SET state='awake', statetime='$Datenew1' WHERE username='$username'";
	mysqli_query($mysqli, $sql);
	
	//give start gold for first users
	$results_per_page2=101;
	$start_from2=0;
	$sql = "SELECT username FROM users LIMIT $start_from2, ".$results_per_page2;
	$rs_result = $mysqli->query($sql);
	$count = $rs_result->num_rows;
	if($count < 101000){
		$sql = "UPDATE currency SET gold='15' WHERE usercur='$username'";
		mysqli_query($mysqli, $sql);
	}
	
	//sent welcome message
	$subject="Welcome to Fantasy-Sim";
	$content="Hello $username, <br> welcome to Fantasy-Sim. I hope you will enjoy your stay on this website and feel free to ask any questions on the forum or you can message me. <br> Regards,";
	$sql = "INSERT INTO messages (sender, recipient, date, subject, content) " 
     . "VALUES ('admin','$username',NOW(),'$subject','$content')";
	mysqli_query($mysqli2, $sql);
}

//getborn
$result = $mysqli->query("SELECT * FROM characters WHERE id='$usercharacterid' AND alive='1'") or die($mysqli->error());
if ( $result->num_rows > 0 ) { //already born	
	$row = mysqli_fetch_array($result);
	$usercharacterid = $row['id'];   

	//create family
	$result = $mysqli->query("SELECT * FROM characters WHERE user='$username' AND alive='1' AND (familyid IS NULL OR familyid = 'NULL')") or die($mysqli->error());
	if ( $result->num_rows > 0 ) {
		$url = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
		if (strpos($url,'home') !== false) {
		    //echo 'op home pagina';
		} else {
			?>
			<script>
			    window.location = 'home.php';
			</script>
			<?php
		}   
		
		?>
		<form method="post" action="">
			<input type="text" required placeholder="Enter name here" maxlength="20" pattern="[a-zA-Z0-9-]+" size="25" required autocomplete="off" id="familyname" name="familyname"/>
			<button type="submit" name="createdynasty" /><?php echo "Create dynasty"; ?></button>
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
	
	if(isset($_POST['createdynasty'])){
		$dynastyname = $mysqli->escape_string($_POST['familyname']);
		
		if(ctype_alnum($dynastyname) AND strlen($dynastyname) <= 20 AND strlen($dynastyname) >= 1){
			$result4 = $mysqli->query("SELECT id FROM characters WHERE user='$username' AND alive='1' AND familyid IS NULL") or die($mysqli->error());
			$row4 = mysqli_fetch_array($result4);
			$characterid = $row4['id'];
			
			$sql = "INSERT INTO family (name, heritagelaw, dynast) " 
			. "VALUES ('$dynastyname','1','$characterid')";
	 		mysqli_query($mysqli, $sql);
			
			$lastid = $mysqli->insert_id;
			
			$sql = "UPDATE characters SET familyid='$lastid' WHERE id='$characterid'";
			mysqli_query($mysqli, $sql);
			
			$content= "You created a new dynasty with the name $dynastyname";
			$sql = "INSERT INTO events (date, content, extrainfo) " 
		     . "VALUES (NOW(),'$content','$username')";
			mysqli_query($mysqli2, $sql);
			
			?>
			<script>
			    window.location = 'home.php';
			</script>
			<?php
			
		}
	}
	
	//create character name
	$result = $mysqli->query("SELECT * FROM characters WHERE user='$username' AND alive='1' AND age >= '6' AND name IS NULL") or die($mysqli->error());
	$result2 = $mysqli->query("SELECT * FROM characters WHERE (mother='$usercharacterid' OR father='$usercharacterid') AND alive='1' AND name IS NULL LIMIT 1") or die($mysqli->error());
	if ( $result->num_rows > 0 ) {//name zelf   
		?>
		<form method="post" action="">
			<input type="hidden" name="type" value="1" />
			<input type="text" required placeholder="Enter name here" maxlength="20" pattern="[a-zA-Z0-9-]+" size="25" required autocomplete="off" id="charactername" name="charactername"/>
			<button type="submit" name="createname" /><?php echo "Name yourself"; ?></button>
		</form>
		<?php
		?>
		<script>
		    if ( window.history.replaceState ) {
		        window.history.replaceState( null, null, window.location.href );
		    }
		</script>
		<?php
	}elseif($result2->num_rows > 0){//name child
		$row2 = mysqli_fetch_array($result2);
		$childid = $row2['id']; 
		$childfm = $row2['sex']; 
		if($childfm=="male"){
			$message="Name your son";
		}elseif($childfm=="female"){
			$message="Name your daughter";
		}
		?>
		<form method="post" action="">
			<input type="hidden" name="type" value="2" />
			<input type="text" required placeholder="Enter name here" maxlength="20" pattern="[a-zA-Z0-9-]+" size="25" required autocomplete="off" id="charactername" name="charactername"/>
			<button type="submit" name="createname" /><?php echo "$message"; ?></button>
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
	
	if(isset($_POST['createname'])){
		$charactername = $mysqli->escape_string($_POST['charactername']);
		$type = $mysqli->escape_string($_POST['type']);
		
		if(ctype_alnum($charactername) AND strlen($charactername) <= 20 AND strlen($charactername) >= 1){
			if($type==1){
				$result4 = $mysqli->query("SELECT * FROM characters WHERE user='$username' AND alive='1' AND age >= '6' AND name IS NULL") or die($mysqli->error());
				$content= "You named yourself $charactername";
			}elseif($type==2){
				$result4 = $mysqli->query("SELECT * FROM characters WHERE (mother='$usercharacterid' OR father='$usercharacterid') AND alive='1' AND name IS NULL LIMIT 1") or die($mysqli->error());				
				$content= "You named your newborn child $charactername";
			}
			$row4 = mysqli_fetch_array($result4);
			$characterid = $row4['id'];
			
			$sql = "UPDATE characters SET name='$charactername' WHERE id='$characterid'";
			mysqli_query($mysqli, $sql);
			
			$sql = "INSERT INTO events (date, content, extrainfo) " 
		     . "VALUES (NOW(),'$content','$username')";
			mysqli_query($mysqli2, $sql);
			
			?>
			<script>
			    window.location = 'home.php';
			</script>
			<?php
			
		}
	}
}else {
	$url = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
	if (strpos($url,'home') !== false) {
	    //echo 'op home pagina';
		?>
		<form method="post" action="">
		    <select required name="race" type="text">  
		  	  	  <option value="" disabled selected hidden>Choose A Race</option> 
		  	  	  <option value="random">random</option>  
				  <option value="dwarf">dwarf</option>
				  <option value="elf">elf</option>
				  <option value="kinnaera">kinnaera</option>
				  <option value="human">human</option>
				  <option value="orc">orc</option>
		    </select>
			<button type="submit" name="getborn" />Get born</button>
		</form>
		<?php
		
		//get born as one of children
		$result4 = $mysqli->query("SELECT * FROM characters WHERE user='$username' AND alive='0' ORDER BY lastonline DESC LIMIT 1") or die($mysqli->error());
		$countdead = $result4->num_rows;
		$row4 = mysqli_fetch_array($result4);
		
		//echo "$countdead";
		if($countdead != 0){
			$deadcharacterid = $row4['id'];
			$result3 = "SELECT * FROM characters WHERE (mother='$deadcharacterid' OR father='$deadcharacterid') AND alive='1' AND NOW() > DATE_ADD(lastonline, INTERVAL 7 DAY)";
			$rs_result = $mysqli->query($result3);
			$countchildren = $rs_result->num_rows;
			
			$result = $mysqli->query("SELECT * FROM characters WHERE (mother='$deadcharacterid' OR father='$deadcharacterid') AND alive='1' AND NOW() > DATE_ADD(lastonline, INTERVAL 7 DAY)") or die($mysqli->error());
			$columnValues = Array();
			
			if($countchildren != 0){
				?>
				<form method="post" action="">
				    <select required name="candidate" type="text">
				    <option value="" disabled selected hidden>Which character do you want to be?</option> 
				    <?php       
				    // Iterating through the product array
					while ( $row = mysqli_fetch_assoc($result) ) {
						$candidateid = $row['familyid'];
						$result2 = $mysqli->query("SELECT * FROM family WHERE id='$candidatefamily'") or die($mysqli->error());
						$row2 = mysqli_fetch_array($result2);
						$candidatefamilyname = $row2['name'];
					    ?>
					    <option value="<?php echo strtolower($row['id']); ?>"><?php echo $row['name']; ?></option>
					    <?php
					}
				    ?>
				    </select> 
				    <button type="submit" name="getbornchild" />Becom this character</button>
				</form>
				<?php
			}
		}
	} else {
		?>
		<script>
		    window.location = 'home.php';
		</script>
		<?php
	}
}

if(isset($_POST['getbornchild'])){//get born as existing child of dead user
	$candidateid = $mysqli->escape_string($_POST['candidate']);
	
	$sql = "UPDATE characters SET user='$username', type='user' WHERE id='$candidateid'";
	mysqli_query($mysqli, $sql);
	
	$_SESSION['usercharacterid'] = $candidateid;
	
	//select religion and update religion and location
	$result4 = $mysqli->query("SELECT * FROM characters WHERE id='$candidateid'") or die($mysqli->error());
	$row4 = mysqli_fetch_array($result4);
	$liegeid = $row4['liege'];
	$characterbirthplace = $row4['birthplace'];
	$characterlocation = $row4['location'];
	$characterlocation2 = $row4['location2'];
	$characternationality = $row4['nationality'];
	$characterbirthplace=$mysqli->escape_string($characterbirthplace);
	
	$result4 = $mysqli->query("SELECT * FROM region WHERE name='$characterbirthplace'") or die($mysqli->error());
	$row4 = mysqli_fetch_array($result4);
	$currowner = $row4['currowner'];
	$location = $motherbirthplace;
	$location2 = $currowner;
	
	$sql = "UPDATE users SET location='$characterlocation', location2='$characterlocation2', nationality='$characternationality' WHERE username='$username'";
	mysqli_query($mysqli, $sql);
	
	if($liegeid != $candidateid){
		$result4 = $mysqli->query("SELECT user FROM characters WHERE id='$liegeid'") or die($mysqli->error());
		$row4 = mysqli_fetch_array($result4);
		$liegeuser = $row4['user'];
		
		$result4 = $mysqli->query("SELECT userreligion FROM users WHERE username='$liegeuser'") or die($mysqli->error());
		$row4 = mysqli_fetch_array($result4);
		$liegereligion = $row4['userreligion'];
		
		if($liegereligion != "NULL" AND $liegereligion != NULL){
			$sql = "UPDATE users SET userreligion='$liegereligion' WHERE username='$username'";
			mysqli_query($mysqli, $sql);
		}
	}
		
	?>
	<script>
	    window.location = 'home.php';
	</script>
	<?php
}

if(isset($_POST['getborn'])){
	$race = $mysqli->escape_string($_POST['race']);
	
	$racearray=array("dwarf","elf","kinnaera","human","orc","random");
	foreach ($racearray as $key) {
		if($race == $key){
			if($race == "random"){
				$rnumber=rand(0, 5);
				$race=$racearray[$rnumber];
			}else{
				$race=$race;
			}
			
			//$result2 = $mysqli->query("SELECT * FROM characters WHERE race='$race' AND married='1' AND alive='1'") or die($mysqli->error());
			$result2 = "SELECT * FROM characters WHERE race='$race' AND married != '0' AND alive='1' AND fertile='1'";
			$rs_result = $mysqli->query($result2);
			$count = $rs_result->num_rows;
			
			$i=0;//check if married is also fertile
			$fertilenumbers=array();
			while($row = $rs_result->fetch_assoc()) {
				$married=$row["married"];
				
				$result5 = "SELECT * FROM characters WHERE id='$married' AND fertile='1'";
				$rs_result5 = $mysqli->query($result5);
				$count5 = $rs_result5->num_rows;
				
				if($count5 != 0){
					$i=$i+1;
				}
			}
			
			$result2 = "SELECT * FROM characters WHERE race='$race' AND married != '0' AND alive='1' AND fertile='1'";
			$rs_result = $mysqli->query($result2);
			$count = $rs_result->num_rows;
			
			$rnumber=rand(0, 1);
			if($rnumber==0){
				$nsex="male";
			}else{
				$nsex="female";
			}
			
			//set house to 1
			$result4 = $mysqli->query("SELECT house FROM inventory WHERE userinv='$username'") or die($mysqli->error());
			$row4 = mysqli_fetch_array($result4);
			$userhouses = $row4['house'];
			
			$userhouses = $userhouses + 1;
			$sql = "UPDATE inventory SET house='$userhouses' WHERE userinv='$username'";
			mysqli_query($mysqli, $sql);
			
			//kans van 10 procent om nieuwe familie te starten
			$rnumber2=rand(1, 10);
			
			if($i == 0 OR $rnumber2==1){//nieuwe familie als geen fertile married or kans van 1 op 10	
				$sql = "INSERT INTO characters (alive, age, type, sex, race, user, fertile) " 
				. "VALUES ('1','20','user','$nsex','$race','$username','1')";
		 		mysqli_query($mysqli, $sql);
				
				$lastid = $mysqli->insert_id;
				$_SESSION['usercharacterid'] = $lastid;
				
				//add traits to child
        addtraitstochild($lastid, 0);
		 		
				//$result3 = $mysqli->query("SELECT * FROM titles WHERE holderid IS NULL") or die($mysqli->error());
				$result3 = "SELECT * FROM titles WHERE holderid = '0'";//check for empty titles
				$rs_result = $mysqli->query($result3);
				$count = $rs_result->num_rows;
		 		
				$result4 = $mysqli->query("SELECT id FROM characters WHERE user='$username' AND alive='1' AND age='20'") or die($mysqli->error());
				$row4 = mysqli_fetch_array($result4);
				$characterid = $row4['id'];
				
				//select empty title for new family and set liege
				if($count != 0){
					$rnumber=rand(1, $count);
					$i=1;
					
					while($row = $rs_result->fetch_assoc()) {
						$titleid=$row["id"];
						$holdingtype=$row["holdingtype"];
						$holdingid=$row["holdingid"];
						if($i==$rnumber){
							if($holdingtype == "kingdom"){
								$result4 = $mysqli->query("SELECT * FROM countryinfo WHERE id='$holdingid'") or die($mysqli->error());
								$row4 = mysqli_fetch_array($result4);
								$government = $row4['government'];
								$countryname = $row4['country'];
								
								$result4 = $mysqli->query("SELECT name FROM region WHERE curowner='$countryname' LIMIT 1") or die($mysqli->error());
								$row4 = mysqli_fetch_array($result4);
								$regionname = $row4['name'];
								$regionname=$mysqli->escape_string($regionname);
								
								$sql = "UPDATE titles SET holderid='$characterid' WHERE id='$titleid'";
								mysqli_query($mysqli, $sql);
								
								$sql = "UPDATE countryinfo SET countrypresident='$username', characterowner='$lastid' WHERE id='$holdingid'";
								mysqli_query($mysqli, $sql);
								
								$sql = "UPDATE characters SET liege='$characterid', maintitle='$titleid' WHERE id='$lastid'";
								mysqli_query($mysqli, $sql);
								
								$sql = "UPDATE users SET location='$countryname', location2='$regionname', nationality='$countryname' WHERE username='$username'";
								mysqli_query($mysqli, $sql);
								
								$sql = "UPDATE characters SET location2='$regionname', location='$countryname', nationality='$countryname' WHERE id='$lastid'";
								mysqli_query($mysqli, $sql);
								
								$sql = "UPDATE characters SET birthplace='$regionname' WHERE id='$lastid'";
								mysqli_query($mysqli, $sql);
								
								$content= "You were born as the current $nsex holder of the kingdom $countryname";
								$sql = "INSERT INTO events (date, content, extrainfo) " 
							     . "VALUES (NOW(),'$content','$username')";
								mysqli_query($mysqli2, $sql);
								
								?>
								<script>
								    window.location = 'home.php';
								</script>
								<?php
							
							}elseif($holdingtype == "duchy"){
								$sql = "UPDATE titles SET holderid='$characterid' WHERE id='$titleid'";
								mysqli_query($mysqli, $sql);
								
								$sql = "UPDATE region SET characterowner='$characterid' WHERE id='$holdingid'";
								mysqli_query($mysqli, $sql);
								
								$result4 = $mysqli->query("SELECT * FROM region WHERE id='$holdingid'") or die($mysqli->error());
								$row4 = mysqli_fetch_array($result4);
								$curowner = $row4['curowner'];
								$regionname = $row4['name'];
								$regionname=$mysqli->escape_string($regionname);
								
								
								$result4 = $mysqli->query("SELECT * FROM countryinfo WHERE country='$curowner'") or die($mysqli->error());
								$row4 = mysqli_fetch_array($result4);
								$countryid = $row4['id'];
								
								$result4 = $mysqli->query("SELECT * FROM titles WHERE holdingid='$countryid'") or die($mysqli->error());
								$row4 = mysqli_fetch_array($result4);
								$holder = $row4['holderid'];
								
								if($holder != 0){//als er een king is dan word dat de liege anders eigenliege
									$result4 = $mysqli->query("SELECT * FROM characters WHERE id='$holder'") or die($mysqli->error());
									$row4 = mysqli_fetch_array($result4);
									$liegename = $row4['name'];
									
									$sql = "UPDATE characters SET liege='$holder', maintitle='$titleid' WHERE id='$lastid'";
									mysqli_query($mysqli, $sql);
									
									//select religion of liege
									$result4 = $mysqli->query("SELECT user FROM characters WHERE id='$holder'") or die($mysqli->error());
									$row4 = mysqli_fetch_array($result4);
									$liegeuser = $row4['user'];
									
									$result4 = $mysqli->query("SELECT userreligion FROM users WHERE username='$liegeuser'") or die($mysqli->error());
									$row4 = mysqli_fetch_array($result4);
									$liegereligion = $row4['userreligion'];
									
									if($liegereligion != "NULL" AND $liegereligion != NULL){
										$sql = "UPDATE users SET userreligion='$liegereligion' WHERE username='$username'";
										mysqli_query($mysqli, $sql);
									}
								}else{
									$sql = "UPDATE characters SET liege='$lastid', maintitle='$titleid' WHERE id='$lastid'";
									mysqli_query($mysqli, $sql);
								}
								
								$sql = "UPDATE users SET location='$curowner', location2='$regionname', nationality='$curowner' WHERE username='$username'";
								mysqli_query($mysqli, $sql);
								
								$sql = "UPDATE characters SET location2='$regionname', location='$curowner', nationality='$curowner' WHERE id='$lastid'";
								mysqli_query($mysqli, $sql);
								
								$sql = "UPDATE characters SET birthplace='$regionname' WHERE id='$lastid'";
								mysqli_query($mysqli, $sql);
								
								$content= "You were born as the current $nsex holder of the duchy $regionname. This duchy is part of the kingdom of $curowner which is currently owned by $liegename";
								$sql = "INSERT INTO events (date, content, extrainfo) " 
							     . "VALUES (NOW(),'$content','$username')";
								mysqli_query($mysqli2, $sql);
								
								?>
								<script>
								    window.location = 'home.php';
								</script>
								<?php
							}
						}
						$i=$i+1;
					}
				}else{//als peasant geboren worden
					$result3 = "SELECT * FROM titles WHERE holderid != '0' AND holdingtype='duchy'";
					$rs_result = $mysqli->query($result3);
					$count = $rs_result->num_rows;
					
					$rnumber=rand(1, $count);
					$i=1;
					
					while($row = $rs_result->fetch_assoc()) {
						if($i == $rnumber){
							$holderid=$row["holderid"];
							$holdingid=$row["holdingid"];
							
							$result4 = $mysqli->query("SELECT * FROM characters WHERE id='$holderid'") or die($mysqli->error());
							$row4 = mysqli_fetch_array($result4);
							$liegename = $row4['name'];
							
							$result4 = $mysqli->query("SELECT * FROM region WHERE id='$holdingid'") or die($mysqli->error());
							$row4 = mysqli_fetch_array($result4);
							$curowner = $row4['curowner'];
							$regionname = $row4['name'];
							$regionname=$mysqli->escape_string($regionname);
							
							//select religion of liege
							$result4 = $mysqli->query("SELECT user FROM characters WHERE id='$holderid'") or die($mysqli->error());
							$row4 = mysqli_fetch_array($result4);
							$liegeuser = $row4['user'];
							
							$result4 = $mysqli->query("SELECT userreligion FROM users WHERE username='$liegeuser'") or die($mysqli->error());
							$row4 = mysqli_fetch_array($result4);
							$liegereligion = $row4['userreligion'];
							
							if($liegereligion != "NULL" AND $liegereligion != NULL){
								$sql = "UPDATE users SET userreligion='$liegereligion' WHERE username='$username'";
								mysqli_query($mysqli, $sql);
							}
							
							$sql = "UPDATE users SET location='$curowner', location2='$regionname', nationality='$curowner' WHERE username='$username'";
							mysqli_query($mysqli, $sql);
							
							$sql = "UPDATE characters SET location2='$regionname', location='$curowner', nationality='$curowner' WHERE id='$lastid'";
							mysqli_query($mysqli, $sql);
							
							$sql = "UPDATE characters SET birthplace='$regionname', liege = '$holderid' WHERE id='$lastid'";
							mysqli_query($mysqli, $sql);
							
							$content= "You were born as a $nsex peasant in the duchy of $regionname which is currently owned by $liegename. As peasant you do not own any holdings from birth. ";
							$sql = "INSERT INTO events (date, content, extrainfo) " 
						     . "VALUES (NOW(),'$content','$username')";
							mysqli_query($mysqli2, $sql);
						
							?>
							<script>
							    window.location = 'home.php';
							</script>
							<?php
						}
						$i = $i+1;
					}
				}
			}else{//als kind geboren worden
				//also update update.php
				$i=1;
				$fertilenumbers=array();
				while($row = $rs_result->fetch_assoc()) {
						$characterid=$row["id"];
						$sex=$row["sex"];
						$familyid=$row["familyid"];
						$matrilineal=$row["matrilineal"];
						$married=$row["married"];
						$race=$row["race"];
						$familyid = $row['familyid'];
						
						//check if married is fertile
						$result3 = $mysqli->query("SELECT * FROM characters WHERE id='$married' AND fertile='1'") or die($mysqli->error());
						$row3 = mysqli_fetch_array($result3);
						$marriedid = $row3['id'];
						$marriedsex = $row3['sex'];
						$marriedfamily = $row3['familyid'];
						$marriedfertile = $row3['fertile'];
						
						if($marriedfertile==1){
							array_push($fertilenumbers,$i);//geeft positie[] kan dus 0 zijn
						}
						$i=$i+1;
					}
					
				$winner=array_rand($fertilenumbers,1);

				$i=0;
				$result2 = "SELECT * FROM characters WHERE race='$race' AND married != '0' AND alive='1' AND fertile='1'";
				$rs_result = $mysqli->query($result2);
				$count = $rs_result->num_rows;
				$break=0;
				while($row = $rs_result->fetch_assoc() AND $break==0) {
					$characterid=$row["id"];
					$sex=$row["sex"];
					$familyid=$row["familyid"];
					$matrilineal=$row["matrilineal"];
					$married=$row["married"];
					$race=$row["race"];
					$familyid = $row['familyid'];
					
					if($i==$winner){
						$break=1;
						$result3 = $mysqli->query("SELECT * FROM characters WHERE id='$married' AND fertile='1'") or die($mysqli->error());
						$row3 = mysqli_fetch_array($result3);
						$marriedid = $row3['id'];
						$marriedsex = $row3['sex'];
						$marriedfamily = $row3['familyid'];
						$marriedfertile = $row3['fertile'];
						
						if($matrilineal==0){
							if($sex=="male"){
								$liege=$characterid;
								$father=$characterid;
								$mother=$marriedid;
								$familyid=$familyid;
							}else{
								$liege=$marriedid;
								$father=$marriedid;
								$mother=$characterid;
								$familyid=$marriedfamily;
							}
						}else{
							if($sex=="male"){
								$liege=$marriedid;
								$father=$characterid;
								$mother=$marriedid;
								$familyid=$marriedfamily;
							}else{
								$liege=$characterid;
								$father=$marriedid;
								$mother=$characterid;
								$familyid=$familyid;
							}
						}
						
						$sql = "INSERT INTO characters (alive, type, sex, race, user,mother,father,liege,familyid) " 
						. "VALUES ('1','user','$nsex','$race','$username','$mother','$father','$liege','$familyid')";
				 		mysqli_query($mysqli, $sql);
						$lastid = $mysqli->insert_id;
						$_SESSION['usercharacterid'] = $lastid;
						
						//add traits to child
						addtraitstochild($lastid, 1);
						
						//add traits from parents
						$result3 = "SELECT * FROM traitscharacters WHERE characterid = '$father' OR characterid = '$mother'";
						$rs_result2 = $mysqli->query($result3);
						$count2 = $rs_result2->num_rows;//aantal titles
						if($count2 != 0){
							while($row2 = $rs_result2->fetch_assoc()) {//ga door traits heen
								$charactertraitid=$row2["traitid"];
							
								$result4 = $mysqli->query("SELECT * FROM traits WHERE id='$charactertraitid'") or die($mysqli->error());
								$row4 = mysqli_fetch_array($result4);
								$traitid=$row4["id"];
								$traitbirthchance=$row4["birthchance"];
								$traitinheritable=$row4["inheritable"];
								
								//check for double traits
								$result5 = "SELECT * FROM traitscharacters WHERE characterid = '$lastid' AND traitid = '$traitid'";
								$rs_result5 = $mysqli->query($result5);
								$count5 = $rs_result5->num_rows;//aantal titles
								
								if($traitinheritable == 1 AND $count5 == 0){
									$randnumber = rand(0, 3);
									if($randnumber == 1){
										$sql = "INSERT INTO traitscharacters (characterid, traitid, date) " 
										. "VALUES ('$lastid','$traitid',NOW())";
								 		mysqli_query($mysqli, $sql);
									}
								}
							}
						}
						
						//update nationality and location and religion
						$result4 = $mysqli->query("SELECT user FROM characters WHERE id='$mother'") or die($mysqli->error());
						$row4 = mysqli_fetch_array($result4);
						$motheruser = $row4['user'];
						$mothertype = $row4['type'];
						$motherbirthplace = $row4['birthplace'];
						$motherbirthplace=$mysqli->escape_string($regionname);
						
						if($mothertype=="user"){
							$result4 = $mysqli->query("SELECT location, location2, nationality FROM users WHERE id='$motheruser'") or die($mysqli->error());
							$row4 = mysqli_fetch_array($result4);
							$location = $row4['location'];
							$location2 = $row4['location2'];
							$location2=$mysqli->escape_string($location2);
							
							$sql = "UPDATE characters SET birthplace='$location2' WHERE id='$lastid'";
							mysqli_query($mysqli, $sql);
						}else{
							$result4 = $mysqli->query("SELECT * FROM region WHERE name='$motherbirthplace'") or die($mysqli->error());
							$row4 = mysqli_fetch_array($result4);
							$currowner = $row4['currowner'];
							$location = $motherbirthplace;
							$location2 = $currowner;
							$location2=$mysqli->escape_string($location2);
							
							$sql = "UPDATE characters SET birthplace='$motherbirthplace' WHERE id='$lastid'";
							mysqli_query($mysqli, $sql);
						}
						
						//select religion of liege
						$result4 = $mysqli->query("SELECT user FROM characters WHERE id='$liege'") or die($mysqli->error());
						$row4 = mysqli_fetch_array($result4);
						$liegeuser = $row4['user'];
						$liegetype = $row4['type'];
						
						if($liegetype=="user"){
							$result4 = $mysqli->query("SELECT * FROM users WHERE id='$liegeuser'") or die($mysqli->error());
							$row4 = mysqli_fetch_array($result4);
							$nationality = $row4['nationality'];
						}else{
							$result4 = $mysqli->query("SELECT * FROM region WHERE name='$motherbirthplace'") or die($mysqli->error());
							$row4 = mysqli_fetch_array($result4);
							$currowner = $row4['currowner'];
							$nationality = $currowner;
						}
						
						$result4 = $mysqli->query("SELECT userreligion FROM users WHERE username='$liegeuser'") or die($mysqli->error());
						$row4 = mysqli_fetch_array($result4);
						$liegereligion = $row4['userreligion'];
						
						if($liegereligion != "NULL" AND $liegereligion != NULL){
							$sql = "UPDATE users SET userreligion='$liegereligion' WHERE username='$username'";
							mysqli_query($mysqli, $sql);
						}
						
						$sql = "UPDATE users SET location='$location', location2='$location2', nationality='$nationality' WHERE username='$username'";
						mysqli_query($mysqli, $sql);
						
						$sql = "UPDATE characters SET location2='$location2', location='$location', nationality='$nationality' WHERE id='$lastid'";
						mysqli_query($mysqli, $sql);
						
						$result4 = $mysqli->query("SELECT * FROM characters WHERE id='$mother'") or die($mysqli->error());
						$row4 = mysqli_fetch_array($result4);
						$mothername = $row4['name'];
						$motherfamily = $row4['familyid'];
						$motheruser = $row4['user'];
						
						$result4 = $mysqli->query("SELECT * FROM characters WHERE id='$father'") or die($mysqli->error());
						$row4 = mysqli_fetch_array($result4);
						$fathername = $row4['name'];
						$fatherfamily = $row4['familyid'];
						$fatheruser = $row4['user'];
						
						//update fertility mother
						$sql = "UPDATE characters SET fertile='0' WHERE id='$mother'";
						mysqli_query($mysqli, $sql);
						
						//add trait to mother
						$result4 = $mysqli->query("SELECT * FROM traits WHERE name='baby'") or die($mysqli->error());
						$row4 = mysqli_fetch_array($result4);
						$traitid = $row4['id'];
						
						$sql = "INSERT INTO traitscharacters (characterid, traitid, date) " 
						. "VALUES ('$mother','$traitid',NOW())";
				 		mysqli_query($mysqli, $sql);
						
						//update heir & dynast
						$result4 = $mysqli->query("SELECT * FROM family WHERE id='$familyid'") or die($mysqli->error());
						$row4 = mysqli_fetch_array($result4);
						$familyname = $row4['name'];
						$heritagelaw=$row4['heritagelaw'];
						$familyheir=$row4['heir'];
						$familydynast=$row4['dynast'];
						
						if($familyheir == NULL OR $familyheir == 0){
							if($heritagelaw==1){
								if($familydynast==$father OR $familydynast==$mother){
									$sql = "UPDATE family SET heir='$lastid' WHERE id='$familyid'";
									mysqli_query($mysqli, $sql);
								}
							}
						}
						
						$result4 = $mysqli->query("SELECT * FROM family WHERE id='$motherfamily'") or die($mysqli->error());
						$row4 = mysqli_fetch_array($result4);
						$motherfamilyname = $row4['name'];
						
						$result4 = $mysqli->query("SELECT * FROM family WHERE id='$fatherfamily'") or die($mysqli->error());
						$row4 = mysqli_fetch_array($result4);
						$fatherfamilyname = $row4['name'];
						
						$content= "You were born as a $nsex in the family of $familyname as a child of $mothername $motherfamily and $fathername $fatherfamily in the duchy $location2 in the kingdom $location";
						$sql = "INSERT INTO events (date, content, extrainfo) " 
					     . "VALUES (NOW(),'$content','$username')";
						mysqli_query($mysqli2, $sql);
						
						$content= "You gave birth to a $nsex child";
						$sql = "INSERT INTO events (date, content, extrainfo) " 
					     . "VALUES (NOW(),'$content','$motheruser')";
						mysqli_query($mysqli2, $sql);
						
						$content= "Your spouse gave birth to a $nsex child";
						$sql = "INSERT INTO events (date, content, extrainfo) " 
					     . "VALUES (NOW(),'$content','$fatheruser')";
						mysqli_query($mysqli2, $sql);
						
						?>
						<script>
						    window.location = 'home.php';
						</script>
						<?php
					}
					$i=$i+1;
				}
			}
		}
	}
}



/*
//check if user has bought game
$result = $mysqli->query("SELECT * FROM shop WHERE username='$username'") or die($mysqli->error());
$row = mysqli_fetch_array($result);
$lid=$row['id'];
$trial=$row['trial'];
$game=$row['game'];

date_default_timezone_set('UTC'); //current date
$datecur = date("Y-m-d H:i:s"); 

if($game == 0 AND $datecur > $trial){
	echo "<script>window.location.replace = 'shop.php'</script>";
}
*/

//check if user is banned
$result2 = $mysqli->query("SELECT * FROM ban WHERE user='$username' AND game='1'") or die($mysqli->error());
$count = $result2->num_rows;

if ( $count != 0 ) {
	$result = $mysqli->query("SELECT * FROM ban WHERE user='$username'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$reason = $row['reason'];
	$date = $row['date'];

 	$_SESSION['message'] = "You have been banned from the game until $date for the following reason: $reason";
	header("location: error.php");    
}



//sleep and awake
$result = $mysqli->query("SELECT state, sleephours, statetime FROM users WHERE username='$username'") or die($mysqli->error());
$row = mysqli_fetch_array($result);
$state = $row['state'];
$sleepstate=$state;
$sleephours = $row['sleephours'];
$statetime = $row['statetime'];

date_default_timezone_set('UTC'); //current date
$datecur = date("Y-m-d H:i:s"); 
$date = new DateTime($datecur);

if($state=="asleep"){
	if($datecur>$statetime){ //wakker worden
		if($sleephours==1){
			$date->add(new DateInterval('PT2H')); // P1D means a period of 1 day
		}elseif($sleephours==2){
			$date->add(new DateInterval('PT4H')); // P1D means a period of 1 day
		}elseif($sleephours==3){
			$date->add(new DateInterval('PT6H')); // P1D means a period of 1 day
		}elseif($sleephours==4){
			$date->add(new DateInterval('PT8H')); // P1D means a period of 1 day
		}elseif($sleephours==5){
			$date->add(new DateInterval('PT10H')); // P1D means a period of 1 day
		}elseif($sleephours==6){
			$date->add(new DateInterval('PT12H')); // P1D means a period of 1 day
		}elseif($sleephours==7){
			$date->add(new DateInterval('PT14H')); // P1D means a period of 1 day
		}elseif($sleephours==8){
			$date->add(new DateInterval('PT16H')); // P1D means a period of 1 day
		}
		$Datenew1 = $date->format('Y-m-d H:i:s');
		
		$sql = "UPDATE users SET state='awake', statetime='$Datenew1', sleephours='0' WHERE username='$username'";
		mysqli_query($mysqli, $sql);
		$sleepstate="awake";
	}
}elseif($state=="awake"){
	if($datecur>$statetime){ //state in neither veranderen
		$sql = "UPDATE users SET state='neither' WHERE username='$username'";
		mysqli_query($mysqli, $sql);
		$sleepstate="neither";
	}
}

//update tavern
$result = $mysqli->query("SELECT housepos, tavern, tavernup, location2, energy, userreligion FROM users WHERE username='$username'") or die($mysqli->error());
$row = mysqli_fetch_array($result);
$tavernup = $row['tavernup'];
$housepos = $row['housepos'];
$housepos=$mysqli->escape_string($housepos);
$tavern = $row['tavern'];
$location2 = $row['location2'];
$location2=$mysqli->escape_string($location2);
$energy = $row['energy'];
$userreligion = $row['userreligion'];

if($tavernup==0){
	if($location2==$housepos){
		$sql = "UPDATE users SET tavernup='1' WHERE username='$username'";
		mysqli_query($mysqli, $sql);
	}else{
		if($tavern==1){
			$sql = "UPDATE users SET tavernup='1', tavern='0' WHERE username='$username'";
			mysqli_query($mysqli, $sql);
		}/*else{
			$energy=$energy-50;
			
			if($energy>=0){
				$sql = "UPDATE users SET tavernup='1', energy='$energy' WHERE username='$username'";
				mysqli_query($mysqli, $sql);
			}else{
				//die
				$sql = "UPDATE users SET inactive='0', age='0', ageup='1', strength='0', dominance='0', energy='100', lastonline=NOW(), housebuilt='0', tavernup='1' WHERE username='$username'";
				mysqli_query($mysqli, $sql);
				
				$sql = "UPDATE relics SET owner='NULL', location='NULL' WHERE owner='$username'";
				mysqli_query($mysqli, $sql);
				
				$result = $mysqli->query("SELECT * FROM religion WHERE name='$userreligion'") or die($mysqli->error());
				$row = mysqli_fetch_array($result);
				$leader=$row['leader'];
				
				if($leader==$username){
					$sql = "UPDATE religion SET leader ='NULL', changedtax='0', crusadeup='0', crusade='NULL' WHERE name='$userreligion'";
					mysqli_query($mysqli, $sql);
				}
				
				echo'<div class="boxed">You died overnight due to a lack of energy!</div>';
				echo nl2br("\n");

				$content= "You died overnight due to a lack of energy!";
				$sql = "INSERT INTO events (date, content, extrainfo) " 
			     . "VALUES (NOW(),'$content','$username')";
				mysqli_query($mysqli2, $sql);
			}
		}*/
	}
}




?>
</body>
</html>
