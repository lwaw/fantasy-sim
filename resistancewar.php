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
	$usercharacterid = $_SESSION['usercharacterid'];
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
//echo $username;
?> <div class="textbox"> <?php
$relbonus=0.05;
$price=50;
$wrongholdingpunishment=0.2;

$result = $mysqli->query("SELECT location, location2 FROM users WHERE username='$username'") or die($mysqli->error());
$row = mysqli_fetch_array($result);
$location=$row['location'];
$location2=$row['location2'];
$location2=$mysqli->escape_string($location2);

//start resistance war
$result = $mysqli->query("SELECT * FROM region WHERE curowner='$location' AND originalowner != '$location' AND name='$location2'") or die($mysqli->error());
//$row = mysqli_fetch_array($result);
for ($set=array(); $row=$result->fetch_assoc(); $set[]=$row);
//print_r($set);

//create forms for every item
foreach ($set as $key => $value) {
	//check if no war is busy
	$name[$key] = $value['name'];
	$result = $mysqli->query("SELECT id FROM diplomacy WHERE attackcountry1='$name[$key]' OR attackcountry2='$name[$key]'") or die($mysqli->error());
	if($result->num_rows > 0){
		//echo "ER is al een war";
	}else{
		/*
		?> <div class="listbox"> <?php
		$name[$key] = $value['name'];
		$originalowner[$key] = $value['originalowner'];
		$curowner[$key] = $value['curowner'];
		echo nl2br("Region: $name[$key] | current owner: $curowner[$key] | original owner: $originalowner[$key] \n");
		echo "Costs of starting a resistance are: $price";
		?>
		<form method="post" action="">
			<input type="hidden" name="name" value="<?php echo "$name[$key]"; ?>" />
			<input type="hidden" name="originalowner" value="<?php echo "$originalowner[$key]"; ?>" />
			<input type="hidden" name="curowner" value="<?php echo "$curowner[$key]"; ?>" />
		   	<button type="submit" name="startresistance" /><?php echo "Start resistance"; ?></button>
		</form>   		
		<?php
		?> </div> <?php
		 */
	}
}

if(isset($_POST['startresistance'])){
	$originalowner = $mysqli->escape_string($_POST['originalowner']);
	$name = $mysqli->escape_string($_POST['name']);
	$curowner = $mysqli->escape_string($_POST['curowner']);
	
	$result = $mysqli->query("SELECT id FROM diplomacy WHERE attackcountry1='$name' OR attackcountry2='$name'") or die($mysqli->error());
	if($result->num_rows > 0){
		echo "There are no resistance wars to start at the moment!";
	}else{
		$result = $mysqli->query("SELECT gold FROM currency WHERE usercur='$username'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$gold=$row['gold'];
		
		$gold=$gold-$price;
		
		if($gold>=0){
			$sql = "UPDATE currency SET gold ='$gold' WHERE useercur='$username'";
			mysqli_query($mysqli, $sql);
			
			date_default_timezone_set('UTC'); //current date
			$datecur = date("Y-m-d H:i:s"); 
			
			$sql = "INSERT INTO diplomacy (type, country1, country2, attackcountry1, attackcountry1start) " 
				. "VALUES ('resistance','$originalowner', '$curowner', '$name', NOW())";
			mysqli_query($mysqli, $sql);
			
			echo "Done!";
		}
	}
}
?> </div> <?php

?> <div class="textbox"> <?php

$result = $mysqli->query("SELECT * FROM diplomacy WHERE (type='resistance' OR type='revolution') AND ( attackcountry1='$location2' OR attackcountry2='$location2' ) ") or die($mysqli->error());
for ($set=array(); $row=$result->fetch_assoc(); $set[]=$row);
//print_r($set);

//create forms for every item
foreach ($set as $key => $value) {
	?> <div class="textbox"> <?php
	$country1[$key] = $value['country1'];
	$country2[$key] = $value['country2'];
	$id[$key]=$value['id'];
	$attackcountry1[$key]=$value['attackcountry1'];
	$attackcountry1[$key]=$mysqli->escape_string($attackcountry1[$key]);
	$attackcountry1start[$key]=$value['attackcountry1start'];
	$country11damage[$key]=$value['country11damage'];
	$country12damage[$key]=$value['country12damage'];
	$type[$key]=$value['type'];
	$acceptnap[$key]=$value['acceptnap'];//1=duchy; 2=kingdom
	
	//check dates
	$date1=$attackcountry1start[$key]; //date voor country1
	//echo date('Y-m-d H:i:s', strtotime($date1. ' + 1 days'));
	$date = new DateTime($date1);
	$date->add(new DateInterval('P3D')); // P1D means a period of 1 day
	$Datenew1 = $date->format('Y-m-d H:i:s');
	
	date_default_timezone_set('UTC'); //current date
	$datecur = date("Y-m-d H:i:s"); 
	
	$result = $mysqli->query("SELECT * FROM characters WHERE id='$country1[$key]'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$attackercharactername=$row['name'];
	$attackercharacterfamilyid=$row['familyid'];
	$attackercharacteruser=$row['user'];
	$attackercharacterid=$row['id'];
	
	$result = $mysqli->query("SELECT * FROM family WHERE id='$attackercharacterfamilyid'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$attackercharacterfamilyname=$row['name'];
	
	$result = $mysqli->query("SELECT * FROM region WHERE name='$attackcountry1[$key]'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$regionid=$row['id'];
	$regioncurowner=$row['curowner'];
	
	$result = $mysqli->query("SELECT * FROM countryinfo WHERE country='$regioncurowner'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$regioncurownerid=$row['id'];
	
	$result = $mysqli->query("SELECT * FROM characters WHERE id='$country2[$key]'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$holdercharactername=$row['name'];
	$holdercharacterfamilyid=$row['familyid'];
	$holdercharacteruser=$row['user'];
	$holdercharacterid=$row['id'];
	
	$result = $mysqli->query("SELECT * FROM family WHERE id='$holdercharacterfamilyid'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$holdercharacterfamilyname=$row['name'];
	
	if($datecur>$Datenew1){
		if($type[$key]=="resistance"){
			$attackcountry1[$key]=$mysqli->escape_string($attackcountry1[$key]);
			
			$result = $mysqli->query("SELECT biggestrel FROM region WHERE name='$attackcountry1[$key]'") or die($mysqli->error());
			$row = mysqli_fetch_array($result);
			$biggestrel=$row['biggestrel'];
			
			/*
			$result = $mysqli->query("SELECT statereligion FROM countryinfo WHERE country='$country1[$key]'") or die($mysqli->error());
			$row = mysqli_fetch_array($result);
			$statereligion1=$row['statereligion'];
			*/
			
			$result = $mysqli->query("SELECT * FROM users WHERE username='$attackercharacteruser'") or die($mysqli->error());
			$row = mysqli_fetch_array($result);
			$attackcharacterreligion=$row['userreligion'];
			
			$result = $mysqli->query("SELECT * FROM users WHERE username='$holdercharacteruser'") or die($mysqli->error());
			$row = mysqli_fetch_array($result);
			$holdercharacterreligion=$row['userreligion'];
			$holdercharacternationality=$row['nationality'];
			
			$result = $mysqli->query("SELECT * FROM titles WHERE holderid='$holdercharacterid'") or die($mysqli->error());
			$row = mysqli_fetch_array($result);
			//$holdercharacterreligion=$row['userreligion'];
			//$holdercharacternationality=$row['nationality'];
						
			if($attackcharacterreligion==$biggestrel){
				$country11damage[$key]=$country11damage[$key]+$relbonus*$country11damage[$key];
			}
			if($attackcharacterreligion==$biggestrel){
				$country12damage[$key]=$country12damage[$key]+$relbonus*$country12damage[$key];
			}
			
			//check for different holdingtype
			$result = $mysqli->query("SELECT COUNT(DISTINCT holdingtype) AS number FROM titles WHERE holderid = '$attackercharacterid'") or die($mysqli->error());
			$row = mysqli_fetch_array($result);
			$countdistincttitlesattacker=$row['number'];
			
			$result = $mysqli->query("SELECT COUNT(DISTINCT holdingtype) AS number FROM titles WHERE holderid = '$holdercharacterid'") or die($mysqli->error());
			$row = mysqli_fetch_array($result);
			$countdistincttitlesholder=$row['number'];
			
			if($countdistincttitlesattacker > 1){
				$country11damage[$key]=$country11damage[$key]-$wrongholdingpunishment*$country11damage[$key];
			}
			if($countdistincttitlesholder > 1){
				$country12damage[$key]=$country12damage[$key]-$wrongholdingpunishment*$country12damage[$key];
			}
			
			if($country11damage[$key]>$country12damage[$key]){ //country 1 gewonnen
				if($acceptnap == 1){//duchy
					$sql = "UPDATE region SET characterowner='$country1[$key]', curowner='$holdercharacternationality' WHERE name='$attackcountry1[$key]'";
					mysqli_query($mysqli, $sql);
					
					$sql = "UPDATE titles SET holderid='$country1[$key]' WHERE holdingid='$regionid' AND type='duchy'";
					mysqli_query($mysqli, $sql);
					
					$sql = "UPDATE users SET location='$holdercharacternationality' WHERE location2='$attackcountry1[$key]'"; //update country of companies
					mysqli_query($mysqli, $sql);
					
					$sql = "UPDATE characters SET location='$holdercharacternationality' WHERE location2='$attackcountry1[$key]'"; //update country of companies
					mysqli_query($mysqli, $sql);
					
					$sql = "UPDATE companies SET countryco='$holdercharacternationality' WHERE region='$attackcountry1[$key]'"; //update country of companies
					mysqli_query($mysqli, $sql);
					
					$content= "<a href='account.php?user=$attackercharacteruser&charid=$country1[$key]'>$attackercharactername $attackercharacterfamilyname won the resistance in $attackcountry1[$key]</a>";
					$sql = "INSERT INTO events (date, content) " 
				     . "VALUES (NOW(),'$content')";
					mysqli_query($mysqli2, $sql);
				}elseif($acceptnap == 2){//kingdom				
					$sql = "UPDATE titles SET holderid='$country1[$key]' WHERE holdingid='$regioncurownerid' AND type='kingdom'";
					mysqli_query($mysqli, $sql);
					
					$sql = "UPDATE countryinfo SET countrypresident='$attackercharacteruser', characterowner='$country1[$key]' WHERE holdingid='$regioncurownerid'";
					mysqli_query($mysqli, $sql);
					
					$content= "<a href='account.php?user=$attackercharacteruser&charid=$country1[$key]'>$attackercharactername $attackercharacterfamilyname won the resistance in $attackcountry1[$key] and gained the title kingdom of $regioncurowner</a>";
					$sql = "INSERT INTO events (date, content) " 
				     . "VALUES (NOW(),'$content')";
					mysqli_query($mysqli2, $sql);
				}
			}
			$sql = "DELETE FROM diplomacy WHERE id='$id[$key]'";
			mysqli_query($mysqli, $sql);
		}elseif($type[$key]=="revolution"){
			if($country12damage[$key]>$country11damage[$key]){ //country 2=leader of revolution
				$sql = "UPDATE countryinfo SET countrypresident='$country2[$key]' WHERE country='$country1[$key]'";
				mysqli_query($mysqli, $sql);
				
				$content= "$country2[$key] has won the revolution against $country1[$key]";
				$sql = "INSERT INTO events (date, content) " 
			     . "VALUES (NOW(),'$content')";
				mysqli_query($mysqli2, $sql);
			}
			$sql = "DELETE FROM diplomacy WHERE id='$id[$key]'";
			mysqli_query($mysqli, $sql);
		}
	}
	
	//doe dingen in country1 aanvallen in nederland verdedigen belgie
	if($attackcountry1[$key] != 'NULL' AND $attackcountry1[$key] != NULL){
		echo nl2br ("<div class=\"h1\">Resistance war between $attackercharactername $attackercharacterfamilyname and $holdercharactername $holdercharacterfamilyname</div>");
		
		//echo "Attack on $attackcountry1[$key] | <a href='account.php?user=$attackercharacteruser&charid=$attackercharacterid'>$attackercharactername $attackercharacterfamilyname</a> total damage: $country11damage[$key] | <a href='account.php?user=$holdercharacteruser&charid=$holdercharacterid'>$holdercharactername $holdercharacterfamilyname</a> total damage: $country12damage[$key]";
		?>
		<table id="table1">
		    <tr>
		    	<th>
		    	<div id="block_container">
					<?php 
					echo "Attack on $attackcountry1[$key]";
					?>
				</div>
				</th>
		    </tr>
		</table>
		<table id="table1">	
			<tr>
				<td><?php echo "<a href='account.php?user=$attackercharacteruser&charid=$attackercharacterid'>$attackercharactername $attackercharacterfamilyname</a> total damage: $country11damage[$key]"; ?></td>
				<td><?php echo "<a href='account.php?user=$holdercharacteruser&charid=$holdercharacterid'>$holdercharactername $holdercharacterfamilyname</a> total damage: $country12damage[$key]"; ?></td>
			</tr>
		</table>
		<?php
		
		?>
			
		<p id="demo"></p>

		<script>
		// Set the date we're counting down to 2018-04-12 15:37:25
		var countDownDate = new Date("<?php echo $Datenew1 ?>").getTime();
			
		// Update the count down every 1 second
		var x = setInterval(function() {
			
		    // Get todays date and time
		    var milisecondssince = new Date().getTime(); // get miliseconds since 1970
		    var time = new Date(); //set new time with timezone
		    
		    //var now2 = new Date();
		    var n = time.getTimezoneOffset(); //gives minute offset time zone
		    var milisecond = n * 60000; //minute to milisecond
		    var now = milisecondssince + milisecond; //calculate new miliseconds
		    
		    // Find the distance between now an the count down date
		    var distance = countDownDate - now;
			    
		    // Time calculations for days, hours, minutes and seconds
		    var days = Math.floor(distance / (1000 * 60 * 60 * 24));
		    var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
		    var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
		    var seconds = Math.floor((distance % (1000 * 60)) / 1000);
				    
		    // Output the result in an element with id="demo"
		    document.getElementById("demo").innerHTML = "Time until end of battle: " + days + "d " + hours + "h "
		    + minutes + "m " + seconds + "s ";
				    
		    // If the count down is over, write some text 
		    if (distance < 0) {
		        clearInterval(x);
		        document.getElementById("demo").innerHTML = "EXPIRED";
		        window.location.reload(true);
		    }
	}, 1000);
	</script>
			
	<canvas id="chart"></canvas>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.3.0/Chart.min.js"></script>
	
	<script>
		var ctx = document.getElementById('chart');
		
		var myChart = new Chart(ctx, {
		  type: 'horizontalBar',
		  data: {
		    labels: ['Damage'],
		    datasets: [
		      {
		        label: '<?php echo "$attackercharactername $attackercharacterfamilyname"; ?>',
		        data: [<?php echo $country11damage[$key] ?>],
		        backgroundColor: '#FF0000',
		      },
		      {
		        label: '<?php echo "$holdercharactername $holdercharacterfamilyname"; ?>',
		        data: [<?php echo $country12damage[$key] ?>],
		        backgroundColor: '#0000FF',
		      }
		    ]
		  },
		  options: {
		    scales: {
		      xAxes: [{ stacked: true }],
		      yAxes: [{ stacked: true }]
		    }
		  }
		});
	</script>
						
	<form method="post" action="">
		<input type="hidden" name="id" value="<?php echo "$id[$key]"; ?>" />
		<input type="hidden" name="attackcountry1" value="<?php echo "$attackcountry1[$key]"; ?>" />
		<input type="hidden" name="attackcountry2" value="<?php echo "$attackcountry2[$key]"; ?>" />
		<input type="hidden" name="attackcountry1start" value="<?php echo "$attackcountry1start[$key]"; ?>" />
		<input type="hidden" name="country11damage" value="<?php echo "$attackcountry11damage[$key]"; ?>" />
		<input type="hidden" name="country12damage" value="<?php echo "$attackcountry12damage[$key]"; ?>" />
		<select name="type" type="text">
			<option value="no weapon">no weapon</option>
  			<option value="weapon q1">weapon q1</option>
  			<option value="weapon q2">weapon q2</option>
  			<option value="weapon q3">weapon q3</option>
  			<option value="weapon q4">weapon q4</option>
  			<option value="weapon q5">weapon q5</option>
   		</select>
		<button type="submit" name="attack1" value="<?php echo "1"; ?>" /><?php echo "Fight for $attackercharactername $attackercharacterfamilyname";//attack ?></button>
		<button type="submit" name="defend1" value="<?php echo "1"; ?>" /><?php echo "Fight for $holdercharactername $holdercharacterfamilyname";//defend ?></button>
	</form>
	<?php
	}
	echo nl2br(" \n");
	?> </div> <?php	
}

?> </div> <?php

if(isset($_POST['attack1'])){
	$id = $mysqli->escape_string($_POST['id']);
	$id = (int) $id;
	$type = $mysqli->escape_string($_POST['type']);
	
	$result = $mysqli->query("SELECT * FROM users WHERE username='$username'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$strength=$row['strength'];
	$energy=$row['energy'];
	$alltimedamage=$row['totaldamage'];
	$militaryunit=$row['militaryunit'];
	$militaryunitrank=$row['militaryunitrank'];
	$location2=$row['location2'];
	$location2 = $mysqli->escape_string($location2);
		
	$result = $mysqli->query("SELECT * FROM diplomacy WHERE id='$id' AND (attackcountry1='$location2' OR attackcountry2='$location2')") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$attackcountry1=$row['attackcountry1'];
	$attackcountry2=$row['attackcountry2'];
	$country11damage=$row['country11damage'];
	$hospital=$row['hospital1'];
	
	if($militaryunit != 0){
		$result = $mysqli->query("SELECT camp FROM militaryunit WHERE id='$militaryunit'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$camp=$row['camp'];
		$camp = $mysqli->escape_string($camp);
		
		if($camp == $location2){
			$militaryunitrankbonus=$militaryunitrank*5+5;
		}else{
			$militaryunitrankbonus=$militaryunitrank*5;
		}
	}else{
		$militaryunitrankbonus=$militaryunitrank*5;
	}
	
	//check strength traits
	$result3 = "SELECT * FROM traitscharacters WHERE characterid='$usercharacterid'";
	$rs_result2 = $mysqli->query($result3);
	$count2 = $rs_result2->num_rows;//aantal titles
	if($count2 != 0){
		$strengthamount = 0;
		while($row2 = $rs_result2->fetch_assoc()) {//ga door traits heen
			$traitid=$row2["traitid"];
			
			$result4 = $mysqli->query("SELECT * FROM traits WHERE id='$traitid' AND type = 'strength'") or die($mysqli->error());
			$row4 = mysqli_fetch_array($result4);
			$traitamount = $row4['amount'];
			
			$strengthamount = $strengthamount + $traitamount;
		}
		$strength = round($strength + (($strength / 100) * $strengthamount));
		if($strength < 0){
			$strength = 0;
		}
	}

	if($type=='no weapon'){
		$energy=$energy-10;
		if($energy>=0){
			$country11damage=$country11damage+$strength+$militaryunitrankbonus;	
			$alltimedamage=$alltimedamage+$strength+$militaryunitrankbonus;
			
			$sql = "UPDATE diplomacy SET country11damage='$country11damage' WHERE id='$id'";
			mysqli_query($mysqli, $sql);
			
			if($hospital >= 10){
				$hospital=$hospital-10;
				$sql = "UPDATE diplomacy SET hospital1='$hospital' WHERE id='$id'";
				mysqli_query($mysqli, $sql);
				
				$sql = "UPDATE users SET totaldamage='$alltimedamage' WHERE username='$username'";
				mysqli_query($mysqli, $sql);	
			}else{
				$sql = "UPDATE users SET energy='$energy', totaldamage='$alltimedamage' WHERE username='$username'";
				mysqli_query($mysqli, $sql);
			}
			
			$result = $mysqli->query("SELECT * FROM diplomacy WHERE id='$id' ") or die($mysqli->error());
			$row = mysqli_fetch_array($result);
			$paygold1=$row['paygold1'];
			$goldperdamage1=$row['goldperdamage1'];
				
			$result = $mysqli->query("SELECT * FROM users WHERE username='$username'") or die($mysqli->error());
			$row = mysqli_fetch_array($result);
			$militaryunit=$row['militaryunit'];
				
			if($militaryunit > 0){
				$result = $mysqli->query("SELECT * FROM militaryunit WHERE id='$militaryunit'") or die($mysqli->error());
				$row = mysqli_fetch_array($result);
				$unitgold=$row['gold'];
				$percentowner=$row['percentowner'];
				$percentunit=$row['percentunit'];
				$percentuser=$row['percentuser'];
				$owner=$row['owner'];
				
				$result = $mysqli->query("SELECT gold FROM currency WHERE usercur='$owner'") or die($mysqli->error());
				$row = mysqli_fetch_array($result);
				$ownergold=$row['gold'];
			}else{
				$paygold1=0;
			}
			
			$result = $mysqli->query("SELECT gold FROM currency WHERE usercur='$username'") or die($mysqli->error());
			$row = mysqli_fetch_array($result);
			$usergold=$row['gold'];
										
			if($paygold1 > 0){
				$prize=($goldperdamage1*$strength)/100;
				$percentowner=$prize*($percentowner/100);
				$percentunit=$prize*($percentunit/100);
				$percentuser=$prize*($percentuser/100);
						
				$paygold1=$paygold1-$prize;
				$sql = "UPDATE diplomacy SET paygold1='$paygold1' WHERE id='$id'";
				mysqli_query($mysqli, $sql);
				
				$usergold=$usergold+$percentuser;
				$sql = "UPDATE currency SET gold='$usergold' WHERE usercur='$username'";
				mysqli_query($mysqli, $sql);
					
				$usergold=$usergold+$percentuser;
				$sql = "UPDATE currency SET gold='$usergold' WHERE usercur='$username'";
				mysqli_query($mysqli, $sql);
						
				$ownergold=$ownergold+$percentowner;
				$sql = "UPDATE currency SET gold='$ownergold' WHERE usercur='$owner'";
				mysqli_query($mysqli, $sql);
									
				$unitgold=$unitgold+$percentunit;
				$sql = "UPDATE militaryunit SET gold='$unitgold' WHERE id='$militaryunit'";
				mysqli_query($mysqli, $sql);		
			}
			
			//chance to get wounded
			$randnumber = rand(0, 100);
			if($randnumber == 1){
				$randnumber2 = rand(0, 100);
				
				if($randnumber2 <= 5){
					$result4 = $mysqli->query("SELECT * FROM traits WHERE name='incapable'") or die($mysqli->error());
					$row4 = mysqli_fetch_array($result4);
					$traitid = $row4['id'];
					
					$result3 = "SELECT * FROM traitscharacters WHERE characterid='$usercharacterid' AND traitid = '$traitid'";
					$rs_result2 = $mysqli->query($result3);
					$count2 = $rs_result2->num_rows;//aantal titles
					
					if($count2 != 0){
						$sql = "INSERT INTO traitscharacters (characterid, traitid, date) " 
						. "VALUES ('$usercharacterid','$traitid',NOW())";
				 		mysqli_query($mysqli, $sql);
			 		}
				}
				
				$result3 = "SELECT * FROM traitscharacters WHERE characterid='$usercharacterid'";
				$rs_result2 = $mysqli->query($result3);
				$count2 = $rs_result2->num_rows;//aantal titles
				$woundedcount = 0;
				while($row2 = $rs_result2->fetch_assoc()) {//ga door traits heen
					$traitid=$row2["traitid"];
					
					$result4 = $mysqli->query("SELECT * FROM traits WHERE id='$traitid'") or die($mysqli->error());
					$row4 = mysqli_fetch_array($result4);
					$traitname = $row4['name'];
					
					if($traitname == "wounded"){
						$woundedcount = $woundedcount + 1;
					}
				}
				
				if($woundedcount == 0){
					$result4 = $mysqli->query("SELECT * FROM traits WHERE name='wounded'") or die($mysqli->error());
					$row4 = mysqli_fetch_array($result4);
					$traitid = $row4['id'];
					
					$sql = "INSERT INTO traitscharacters (characterid, traitid, date) " 
					. "VALUES ('$usercharacterid','$traitid',NOW())";
			 		mysqli_query($mysqli, $sql);
					
					$content= "You got wounded while fighting";
					$sql = "INSERT INTO events (date, content, extrainfo) " 
				     . "VALUES (NOW(),'$content','$username')";
					mysqli_query($mysqli2, $sql);
				}
			}
			//header("Refresh:0");
		}else{
			echo "You don't have enough energy!";
		}
	}elseif($type=='weapon q1'){
		$result = $mysqli->query("SELECT * FROM inventory WHERE userinv='$username'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$weapon=$row['weaponq1'];
		
		$weapon=$weapon-1;
		$energy=$energy-10;
		
		if($energy>=0 AND $weapon>=0){
			$country11damage=$country11damage+$strength+50+$militaryunitrankbonus;
			$alltimedamage=$alltimedamage+$strength+50+$militaryunitrankbonus;				
			$sql = "UPDATE diplomacy SET country11damage='$country11damage' WHERE id='$id'";
			mysqli_query($mysqli, $sql);
			
			if($hospital >= 10){
				$hospital=$hospital-10;
				$sql = "UPDATE diplomacy SET hospital1='$hospital' WHERE id='$id'";
				mysqli_query($mysqli, $sql);
				
				$sql = "UPDATE users SET totaldamage='$alltimedamage' WHERE username='$username'";
				mysqli_query($mysqli, $sql);	
			}else{
				$sql = "UPDATE users SET energy='$energy', totaldamage='$alltimedamage' WHERE username='$username'";
				mysqli_query($mysqli, $sql);
			}
			
			$sql = "UPDATE inventory SET weaponq1='$weapon' WHERE userinv='$username'";
			mysqli_query($mysqli, $sql);
			
			$result = $mysqli->query("SELECT * FROM diplomacy WHERE id='$id' ") or die($mysqli->error());
			$row = mysqli_fetch_array($result);
			$paygold1=$row['paygold1'];
			$goldperdamage1=$row['goldperdamage1'];
				
			$result = $mysqli->query("SELECT * FROM users WHERE username='$username'") or die($mysqli->error());
			$row = mysqli_fetch_array($result);
			$militaryunit=$row['militaryunit'];
				
			if($militaryunit > 0){
				$result = $mysqli->query("SELECT * FROM militaryunit WHERE id='$militaryunit'") or die($mysqli->error());
				$row = mysqli_fetch_array($result);
				$unitgold=$row['gold'];
				$percentowner=$row['percentowner'];
				$percentunit=$row['percentunit'];
				$percentuser=$row['percentuser'];
				$owner=$row['owner'];
				
				$result = $mysqli->query("SELECT gold FROM currency WHERE usercur='$owner'") or die($mysqli->error());
				$row = mysqli_fetch_array($result);
				$ownergold=$row['gold'];
			}else{
				$paygold1=0;
			}
			
			$result = $mysqli->query("SELECT gold FROM currency WHERE usercur='$username'") or die($mysqli->error());
			$row = mysqli_fetch_array($result);
			$usergold=$row['gold'];
										
			if($paygold1 > 0){
				$prize=($goldperdamage1*($strength+50))/100;
				$percentowner=$prize*($percentowner/100);
				$percentunit=$prize*($percentunit/100);
				$percentuser=$prize*($percentuser/100);
						
				$paygold1=$paygold1-$prize;
				$sql = "UPDATE diplomacy SET paygold1='$paygold1' WHERE id='$id'";
				mysqli_query($mysqli, $sql);
				
				$usergold=$usergold+$percentuser;
				$sql = "UPDATE currency SET gold='$usergold' WHERE usercur='$username'";
				mysqli_query($mysqli, $sql);
					
				$usergold=$usergold+$percentuser;
				$sql = "UPDATE currency SET gold='$usergold' WHERE usercur='$username'";
				mysqli_query($mysqli, $sql);
						
				$ownergold=$ownergold+$percentowner;
				$sql = "UPDATE currency SET gold='$ownergold' WHERE usercur='$owner'";
				mysqli_query($mysqli, $sql);
									
				$unitgold=$unitgold+$percentunit;
				$sql = "UPDATE militaryunit SET gold='$unitgold' WHERE id='$militaryunit'";
				mysqli_query($mysqli, $sql);		
			}
			
			//chance to get wounded
			$randnumber = rand(0, 100);
			if($randnumber == 1){
				$randnumber2 = rand(0, 100);
				
				if($randnumber2 <= 5){
					$result4 = $mysqli->query("SELECT * FROM traits WHERE name='incapable'") or die($mysqli->error());
					$row4 = mysqli_fetch_array($result4);
					$traitid = $row4['id'];
					
					$result3 = "SELECT * FROM traitscharacters WHERE characterid='$usercharacterid' AND traitid = '$traitid'";
					$rs_result2 = $mysqli->query($result3);
					$count2 = $rs_result2->num_rows;//aantal titles
					
					if($count2 != 0){
						$sql = "INSERT INTO traitscharacters (characterid, traitid, date) " 
						. "VALUES ('$usercharacterid','$traitid',NOW())";
				 		mysqli_query($mysqli, $sql);
			 		}
				}
				
				$result3 = "SELECT * FROM traitscharacters WHERE characterid='$usercharacterid'";
				$rs_result2 = $mysqli->query($result3);
				$count2 = $rs_result2->num_rows;//aantal titles
				$woundedcount = 0;
				while($row2 = $rs_result2->fetch_assoc()) {//ga door traits heen
					$traitid=$row2["traitid"];
					
					$result4 = $mysqli->query("SELECT * FROM traits WHERE id='$traitid'") or die($mysqli->error());
					$row4 = mysqli_fetch_array($result4);
					$traitname = $row4['name'];
					
					if($traitname == "wounded"){
						$woundedcount = $woundedcount + 1;
					}
				}
				
				if($woundedcount == 0){
					$result4 = $mysqli->query("SELECT * FROM traits WHERE name='wounded'") or die($mysqli->error());
					$row4 = mysqli_fetch_array($result4);
					$traitid = $row4['id'];
					
					$sql = "INSERT INTO traitscharacters (characterid, traitid, date) " 
					. "VALUES ('$usercharacterid','$traitid',NOW())";
			 		mysqli_query($mysqli, $sql);
					
					$content= "You got wounded while fighting";
					$sql = "INSERT INTO events (date, content, extrainfo) " 
				     . "VALUES (NOW(),'$content','$username')";
					mysqli_query($mysqli2, $sql);
				}
			}
			//header("Refresh:0");
		}else{
			echo "You don't have enough weapons or energy!";
		}	
	}elseif($type=='weapon q2'){
		$result = $mysqli->query("SELECT * FROM inventory WHERE userinv='$username'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$weapon=$row['weaponq2'];
		
		$weapon=$weapon-1;
		$energy=$energy-10;
		
		if($energy>=0 AND $weapon>=0){
			$country11damage=$country11damage+$strength+100+$militaryunitrankbonus;
			$alltimedamage=$alltimedamage+$strength+100+$militaryunitrankbonus;				
			$sql = "UPDATE diplomacy SET country11damage='$country11damage' WHERE id='$id'";
			mysqli_query($mysqli, $sql);
			
			if($hospital >= 10){
				$hospital=$hospital-10;
				$sql = "UPDATE diplomacy SET hospital1='$hospital' WHERE id='$id'";
				mysqli_query($mysqli, $sql);
				
				$sql = "UPDATE users SET totaldamage='$alltimedamage' WHERE username='$username'";
				mysqli_query($mysqli, $sql);	
			}else{
				$sql = "UPDATE users SET energy='$energy', totaldamage='$alltimedamage' WHERE username='$username'";
				mysqli_query($mysqli, $sql);
			}
			
			$sql = "UPDATE inventory SET weaponq2='$weapon' WHERE userinv='$username'";
			mysqli_query($mysqli, $sql);
			
			$result = $mysqli->query("SELECT * FROM diplomacy WHERE id='$id' ") or die($mysqli->error());
			$row = mysqli_fetch_array($result);
			$paygold1=$row['paygold1'];
			$goldperdamage1=$row['goldperdamage1'];
				
			$result = $mysqli->query("SELECT * FROM users WHERE username='$username'") or die($mysqli->error());
			$row = mysqli_fetch_array($result);
			$militaryunit=$row['militaryunit'];
				
			if($militaryunit > 0){
				$result = $mysqli->query("SELECT * FROM militaryunit WHERE id='$militaryunit'") or die($mysqli->error());
				$row = mysqli_fetch_array($result);
				$unitgold=$row['gold'];
				$percentowner=$row['percentowner'];
				$percentunit=$row['percentunit'];
				$percentuser=$row['percentuser'];
				$owner=$row['owner'];
				
				$result = $mysqli->query("SELECT gold FROM currency WHERE usercur='$owner'") or die($mysqli->error());
				$row = mysqli_fetch_array($result);
				$ownergold=$row['gold'];
			}else{
				$paygold1=0;
			}
			
			$result = $mysqli->query("SELECT gold FROM currency WHERE usercur='$username'") or die($mysqli->error());
			$row = mysqli_fetch_array($result);
			$usergold=$row['gold'];
										
			if($paygold1 > 0){
				$prize=($goldperdamage1*($strength+100))/100;
				$percentowner=$prize*($percentowner/100);
				$percentunit=$prize*($percentunit/100);
				$percentuser=$prize*($percentuser/100);
						
				$paygold1=$paygold1-$prize;
				$sql = "UPDATE diplomacy SET paygold1='$paygold1' WHERE id='$id'";
				mysqli_query($mysqli, $sql);
				
				$usergold=$usergold+$percentuser;
				$sql = "UPDATE currency SET gold='$usergold' WHERE usercur='$username'";
				mysqli_query($mysqli, $sql);
					
				$usergold=$usergold+$percentuser;
				$sql = "UPDATE currency SET gold='$usergold' WHERE usercur='$username'";
				mysqli_query($mysqli, $sql);
						
				$ownergold=$ownergold+$percentowner;
				$sql = "UPDATE currency SET gold='$ownergold' WHERE usercur='$owner'";
				mysqli_query($mysqli, $sql);
									
				$unitgold=$unitgold+$percentunit;
				$sql = "UPDATE militaryunit SET gold='$unitgold' WHERE id='$militaryunit'";
				mysqli_query($mysqli, $sql);		
			}
			
			//chance to get wounded
			$randnumber = rand(0, 100);
			if($randnumber == 1){
				$randnumber2 = rand(0, 100);
				
				if($randnumber2 <= 5){
					$result4 = $mysqli->query("SELECT * FROM traits WHERE name='incapable'") or die($mysqli->error());
					$row4 = mysqli_fetch_array($result4);
					$traitid = $row4['id'];
					
					$result3 = "SELECT * FROM traitscharacters WHERE characterid='$usercharacterid' AND traitid = '$traitid'";
					$rs_result2 = $mysqli->query($result3);
					$count2 = $rs_result2->num_rows;//aantal titles
					
					if($count2 != 0){
						$sql = "INSERT INTO traitscharacters (characterid, traitid, date) " 
						. "VALUES ('$usercharacterid','$traitid',NOW())";
				 		mysqli_query($mysqli, $sql);
			 		}
				}
				
				$result3 = "SELECT * FROM traitscharacters WHERE characterid='$usercharacterid'";
				$rs_result2 = $mysqli->query($result3);
				$count2 = $rs_result2->num_rows;//aantal titles
				$woundedcount = 0;
				while($row2 = $rs_result2->fetch_assoc()) {//ga door traits heen
					$traitid=$row2["traitid"];
					
					$result4 = $mysqli->query("SELECT * FROM traits WHERE id='$traitid'") or die($mysqli->error());
					$row4 = mysqli_fetch_array($result4);
					$traitname = $row4['name'];
					
					if($traitname == "wounded"){
						$woundedcount = $woundedcount + 1;
					}
				}
				
				if($woundedcount == 0){
					$result4 = $mysqli->query("SELECT * FROM traits WHERE name='wounded'") or die($mysqli->error());
					$row4 = mysqli_fetch_array($result4);
					$traitid = $row4['id'];
					
					$sql = "INSERT INTO traitscharacters (characterid, traitid, date) " 
					. "VALUES ('$usercharacterid','$traitid',NOW())";
			 		mysqli_query($mysqli, $sql);
					
					$content= "You got wounded while fighting";
					$sql = "INSERT INTO events (date, content, extrainfo) " 
				     . "VALUES (NOW(),'$content','$username')";
					mysqli_query($mysqli2, $sql);
				}
			}
			//header("Refresh:0");
		}else{
			echo "You don't have enough weapons or energy!";
		}	
	}elseif($type=='weapon q3'){
		$result = $mysqli->query("SELECT * FROM inventory WHERE userinv='$username'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$weapon=$row['weaponq3'];
		
		$weapon=$weapon-1;
		$energy=$energy-10;
		
		if($energy>=0 AND $weapon>=0){
			$country11damage=$country11damage+$strength+150+$militaryunitrankbonus;
			$alltimedamage=$alltimedamage+$strength+150+$militaryunitrankbonus;				
			$sql = "UPDATE diplomacy SET country11damage='$country11damage' WHERE id='$id'";
			mysqli_query($mysqli, $sql);
			
			if($hospital >= 10){
				$hospital=$hospital-10;
				$sql = "UPDATE diplomacy SET hospital1='$hospital' WHERE id='$id'";
				mysqli_query($mysqli, $sql);
				
				$sql = "UPDATE users SET totaldamage='$alltimedamage' WHERE username='$username'";
				mysqli_query($mysqli, $sql);	
			}else{
				$sql = "UPDATE users SET energy='$energy', totaldamage='$alltimedamage' WHERE username='$username'";
				mysqli_query($mysqli, $sql);
			}
			
			$sql = "UPDATE inventory SET weaponq3='$weapon' WHERE userinv='$username'";
			mysqli_query($mysqli, $sql);
			
			$result = $mysqli->query("SELECT * FROM diplomacy WHERE id='$id' ") or die($mysqli->error());
			$row = mysqli_fetch_array($result);
			$paygold1=$row['paygold1'];
			$goldperdamage1=$row['goldperdamage1'];
				
			$result = $mysqli->query("SELECT * FROM users WHERE username='$username'") or die($mysqli->error());
			$row = mysqli_fetch_array($result);
			$militaryunit=$row['militaryunit'];
				
			if($militaryunit > 0){
				$result = $mysqli->query("SELECT * FROM militaryunit WHERE id='$militaryunit'") or die($mysqli->error());
				$row = mysqli_fetch_array($result);
				$unitgold=$row['gold'];
				$percentowner=$row['percentowner'];
				$percentunit=$row['percentunit'];
				$percentuser=$row['percentuser'];
				$owner=$row['owner'];
				
				$result = $mysqli->query("SELECT gold FROM currency WHERE usercur='$owner'") or die($mysqli->error());
				$row = mysqli_fetch_array($result);
				$ownergold=$row['gold'];
			}else{
				$paygold1=0;
			}
			
			$result = $mysqli->query("SELECT gold FROM currency WHERE usercur='$username'") or die($mysqli->error());
			$row = mysqli_fetch_array($result);
			$usergold=$row['gold'];
										
			if($paygold1 > 0){
				$prize=($goldperdamage1*($strength+150))/100;
				$percentowner=$prize*($percentowner/100);
				$percentunit=$prize*($percentunit/100);
				$percentuser=$prize*($percentuser/100);
						
				$paygold1=$paygold1-$prize;
				$sql = "UPDATE diplomacy SET paygold1='$paygold1' WHERE id='$id'";
				mysqli_query($mysqli, $sql);
				
				$usergold=$usergold+$percentuser;
				$sql = "UPDATE currency SET gold='$usergold' WHERE usercur='$username'";
				mysqli_query($mysqli, $sql);
					
				$usergold=$usergold+$percentuser;
				$sql = "UPDATE currency SET gold='$usergold' WHERE usercur='$username'";
				mysqli_query($mysqli, $sql);
						
				$ownergold=$ownergold+$percentowner;
				$sql = "UPDATE currency SET gold='$ownergold' WHERE usercur='$owner'";
				mysqli_query($mysqli, $sql);
									
				$unitgold=$unitgold+$percentunit;
				$sql = "UPDATE militaryunit SET gold='$unitgold' WHERE id='$militaryunit'";
				mysqli_query($mysqli, $sql);		
			}
			
			//chance to get wounded
			$randnumber = rand(0, 100);
			if($randnumber == 1){
				$randnumber2 = rand(0, 100);
				
				if($randnumber2 <= 5){
					$result4 = $mysqli->query("SELECT * FROM traits WHERE name='incapable'") or die($mysqli->error());
					$row4 = mysqli_fetch_array($result4);
					$traitid = $row4['id'];
					
					$result3 = "SELECT * FROM traitscharacters WHERE characterid='$usercharacterid' AND traitid = '$traitid'";
					$rs_result2 = $mysqli->query($result3);
					$count2 = $rs_result2->num_rows;//aantal titles
					
					if($count2 != 0){
						$sql = "INSERT INTO traitscharacters (characterid, traitid, date) " 
						. "VALUES ('$usercharacterid','$traitid',NOW())";
				 		mysqli_query($mysqli, $sql);
			 		}
				}
				
				$result3 = "SELECT * FROM traitscharacters WHERE characterid='$usercharacterid'";
				$rs_result2 = $mysqli->query($result3);
				$count2 = $rs_result2->num_rows;//aantal titles
				$woundedcount = 0;
				while($row2 = $rs_result2->fetch_assoc()) {//ga door traits heen
					$traitid=$row2["traitid"];
					
					$result4 = $mysqli->query("SELECT * FROM traits WHERE id='$traitid'") or die($mysqli->error());
					$row4 = mysqli_fetch_array($result4);
					$traitname = $row4['name'];
					
					if($traitname == "wounded"){
						$woundedcount = $woundedcount + 1;
					}
				}
				
				if($woundedcount == 0){
					$result4 = $mysqli->query("SELECT * FROM traits WHERE name='wounded'") or die($mysqli->error());
					$row4 = mysqli_fetch_array($result4);
					$traitid = $row4['id'];
					
					$sql = "INSERT INTO traitscharacters (characterid, traitid, date) " 
					. "VALUES ('$usercharacterid','$traitid',NOW())";
			 		mysqli_query($mysqli, $sql);
					
					$content= "You got wounded while fighting";
					$sql = "INSERT INTO events (date, content, extrainfo) " 
				     . "VALUES (NOW(),'$content','$username')";
					mysqli_query($mysqli2, $sql);
				}
			}
			//header("Refresh:0");
		}else{
			echo "You don't have enough weapons or energy!";
		}	
	}elseif($type=='weapon q4'){
		$result = $mysqli->query("SELECT * FROM inventory WHERE userinv='$username'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$weapon=$row['weaponq4'];
		
		$weapon=$weapon-1;
		$energy=$energy-10;
		
		if($energy>=0 AND $weapon>=0){
			$country11damage=$country11damage+$strength+200+$militaryunitrankbonus;
			$alltimedamage=$alltimedamage+$strength+200+$militaryunitrankbonus;		
			$sql = "UPDATE diplomacy SET country11damage='$country11damage' WHERE id='$id'";
			mysqli_query($mysqli, $sql);
			
			if($hospital >= 10){
				$hospital=$hospital-10;
				$sql = "UPDATE diplomacy SET hospital1='$hospital' WHERE id='$id'";
				mysqli_query($mysqli, $sql);
				
				$sql = "UPDATE users SET totaldamage='$alltimedamage' WHERE username='$username'";
				mysqli_query($mysqli, $sql);	
			}else{
				$sql = "UPDATE users SET energy='$energy', totaldamage='$alltimedamage' WHERE username='$username'";
				mysqli_query($mysqli, $sql);
			}
			
			$sql = "UPDATE inventory SET weaponq4='$weapon' WHERE userinv='$username'";
			mysqli_query($mysqli, $sql);
			
			$result = $mysqli->query("SELECT * FROM diplomacy WHERE id='$id' ") or die($mysqli->error());
			$row = mysqli_fetch_array($result);
			$paygold1=$row['paygold1'];
			$goldperdamage1=$row['goldperdamage1'];
				
			$result = $mysqli->query("SELECT * FROM users WHERE username='$username'") or die($mysqli->error());
			$row = mysqli_fetch_array($result);
			$militaryunit=$row['militaryunit'];
				
			if($militaryunit > 0){
				$result = $mysqli->query("SELECT * FROM militaryunit WHERE id='$militaryunit'") or die($mysqli->error());
				$row = mysqli_fetch_array($result);
				$unitgold=$row['gold'];
				$percentowner=$row['percentowner'];
				$percentunit=$row['percentunit'];
				$percentuser=$row['percentuser'];
				$owner=$row['owner'];
				
				$result = $mysqli->query("SELECT gold FROM currency WHERE usercur='$owner'") or die($mysqli->error());
				$row = mysqli_fetch_array($result);
				$ownergold=$row['gold'];
			}else{
				$paygold1=0;
			}
			
			$result = $mysqli->query("SELECT gold FROM currency WHERE usercur='$username'") or die($mysqli->error());
			$row = mysqli_fetch_array($result);
			$usergold=$row['gold'];
										
			if($paygold1 > 0){
				$prize=($goldperdamage1*($strength+200))/100;
				$percentowner=$prize*($percentowner/100);
				$percentunit=$prize*($percentunit/100);
				$percentuser=$prize*($percentuser/100);
						
				$paygold1=$paygold1-$prize;
				$sql = "UPDATE diplomacy SET paygold1='$paygold1' WHERE id='$id'";
				mysqli_query($mysqli, $sql);
				
				$usergold=$usergold+$percentuser;
				$sql = "UPDATE currency SET gold='$usergold' WHERE usercur='$username'";
				mysqli_query($mysqli, $sql);
					
				$usergold=$usergold+$percentuser;
				$sql = "UPDATE currency SET gold='$usergold' WHERE usercur='$username'";
				mysqli_query($mysqli, $sql);
						
				$ownergold=$ownergold+$percentowner;
				$sql = "UPDATE currency SET gold='$ownergold' WHERE usercur='$owner'";
				mysqli_query($mysqli, $sql);
									
				$unitgold=$unitgold+$percentunit;
				$sql = "UPDATE militaryunit SET gold='$unitgold' WHERE id='$militaryunit'";
				mysqli_query($mysqli, $sql);		
			}
			
			//chance to get wounded
			$randnumber = rand(0, 100);
			if($randnumber == 1){
				$randnumber2 = rand(0, 100);
				
				if($randnumber2 <= 5){
					$result4 = $mysqli->query("SELECT * FROM traits WHERE name='incapable'") or die($mysqli->error());
					$row4 = mysqli_fetch_array($result4);
					$traitid = $row4['id'];
					
					$result3 = "SELECT * FROM traitscharacters WHERE characterid='$usercharacterid' AND traitid = '$traitid'";
					$rs_result2 = $mysqli->query($result3);
					$count2 = $rs_result2->num_rows;//aantal titles
					
					if($count2 != 0){
						$sql = "INSERT INTO traitscharacters (characterid, traitid, date) " 
						. "VALUES ('$usercharacterid','$traitid',NOW())";
				 		mysqli_query($mysqli, $sql);
			 		}
				}
				
				$result3 = "SELECT * FROM traitscharacters WHERE characterid='$usercharacterid'";
				$rs_result2 = $mysqli->query($result3);
				$count2 = $rs_result2->num_rows;//aantal titles
				$woundedcount = 0;
				while($row2 = $rs_result2->fetch_assoc()) {//ga door traits heen
					$traitid=$row2["traitid"];
					
					$result4 = $mysqli->query("SELECT * FROM traits WHERE id='$traitid'") or die($mysqli->error());
					$row4 = mysqli_fetch_array($result4);
					$traitname = $row4['name'];
					
					if($traitname == "wounded"){
						$woundedcount = $woundedcount + 1;
					}
				}
				
				if($woundedcount == 0){
					$result4 = $mysqli->query("SELECT * FROM traits WHERE name='wounded'") or die($mysqli->error());
					$row4 = mysqli_fetch_array($result4);
					$traitid = $row4['id'];
					
					$sql = "INSERT INTO traitscharacters (characterid, traitid, date) " 
					. "VALUES ('$usercharacterid','$traitid',NOW())";
			 		mysqli_query($mysqli, $sql);
					
					$content= "You got wounded while fighting";
					$sql = "INSERT INTO events (date, content, extrainfo) " 
				     . "VALUES (NOW(),'$content','$username')";
					mysqli_query($mysqli2, $sql);
				}
			}
			//header("Refresh:0");
		}else{
			echo "You don't have enough weapons or energy!";
		}	
	}elseif($type=='weapon q5'){
		$result = $mysqli->query("SELECT * FROM inventory WHERE userinv='$username'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$weapon=$row['weaponq5'];
		
		$weapon=$weapon-1;
		$energy=$energy-10;
		
		if($energy>=0 AND $weapon>=0){
			$country11damage=$country11damage+$strength+250+$militaryunitrankbonus;
			$alltimedamage=$alltimedamage+$strength+250+$militaryunitrankbonus;		
			$sql = "UPDATE diplomacy SET country11damage='$country11damage' WHERE id='$id'";
			mysqli_query($mysqli, $sql);
			
			if($hospital >= 10){
				$hospital=$hospital-10;
				$sql = "UPDATE diplomacy SET hospital1='$hospital' WHERE id='$id'";
				mysqli_query($mysqli, $sql);
				
				$sql = "UPDATE users SET totaldamage='$alltimedamage' WHERE username='$username'";
				mysqli_query($mysqli, $sql);	
			}else{
				$sql = "UPDATE users SET energy='$energy', totaldamage='$alltimedamage' WHERE username='$username'";
				mysqli_query($mysqli, $sql);
			}
			
			$sql = "UPDATE inventory SET weaponq5='$weapon' WHERE userinv='$username'";
			mysqli_query($mysqli, $sql);
			
			$result = $mysqli->query("SELECT * FROM diplomacy WHERE id='$id' ") or die($mysqli->error());
			$row = mysqli_fetch_array($result);
			$paygold1=$row['paygold1'];
			$goldperdamage1=$row['goldperdamage1'];
				
			$result = $mysqli->query("SELECT * FROM users WHERE username='$username'") or die($mysqli->error());
			$row = mysqli_fetch_array($result);
			$militaryunit=$row['militaryunit'];
				
			if($militaryunit > 0){
				$result = $mysqli->query("SELECT * FROM militaryunit WHERE id='$militaryunit'") or die($mysqli->error());
				$row = mysqli_fetch_array($result);
				$unitgold=$row['gold'];
				$percentowner=$row['percentowner'];
				$percentunit=$row['percentunit'];
				$percentuser=$row['percentuser'];
				$owner=$row['owner'];
				
				$result = $mysqli->query("SELECT gold FROM currency WHERE usercur='$owner'") or die($mysqli->error());
				$row = mysqli_fetch_array($result);
				$ownergold=$row['gold'];
			}else{
				$paygold1=0;
			}
			
			$result = $mysqli->query("SELECT gold FROM currency WHERE usercur='$username'") or die($mysqli->error());
			$row = mysqli_fetch_array($result);
			$usergold=$row['gold'];
										
			if($paygold1 > 0){
				$prize=($goldperdamage1*($strength+250))/100;
				$percentowner=$prize*($percentowner/100);
				$percentunit=$prize*($percentunit/100);
				$percentuser=$prize*($percentuser/100);
						
				$paygold1=$paygold1-$prize;
				$sql = "UPDATE diplomacy SET paygold1='$paygold1' WHERE id='$id'";
				mysqli_query($mysqli, $sql);
				
				$usergold=$usergold+$percentuser;
				$sql = "UPDATE currency SET gold='$usergold' WHERE usercur='$username'";
				mysqli_query($mysqli, $sql);
					
				$usergold=$usergold+$percentuser;
				$sql = "UPDATE currency SET gold='$usergold' WHERE usercur='$username'";
				mysqli_query($mysqli, $sql);
						
				$ownergold=$ownergold+$percentowner;
				$sql = "UPDATE currency SET gold='$ownergold' WHERE usercur='$owner'";
				mysqli_query($mysqli, $sql);
									
				$unitgold=$unitgold+$percentunit;
				$sql = "UPDATE militaryunit SET gold='$unitgold' WHERE id='$militaryunit'";
				mysqli_query($mysqli, $sql);		
			}
			
			//chance to get wounded
			$randnumber = rand(0, 100);
			if($randnumber == 1){
				$randnumber2 = rand(0, 100);
				
				if($randnumber2 <= 5){
					$result4 = $mysqli->query("SELECT * FROM traits WHERE name='incapable'") or die($mysqli->error());
					$row4 = mysqli_fetch_array($result4);
					$traitid = $row4['id'];
					
					$result3 = "SELECT * FROM traitscharacters WHERE characterid='$usercharacterid' AND traitid = '$traitid'";
					$rs_result2 = $mysqli->query($result3);
					$count2 = $rs_result2->num_rows;//aantal titles
					
					if($count2 != 0){
						$sql = "INSERT INTO traitscharacters (characterid, traitid, date) " 
						. "VALUES ('$usercharacterid','$traitid',NOW())";
				 		mysqli_query($mysqli, $sql);
			 		}
				}
				
				$result3 = "SELECT * FROM traitscharacters WHERE characterid='$usercharacterid'";
				$rs_result2 = $mysqli->query($result3);
				$count2 = $rs_result2->num_rows;//aantal titles
				$woundedcount = 0;
				while($row2 = $rs_result2->fetch_assoc()) {//ga door traits heen
					$traitid=$row2["traitid"];
					
					$result4 = $mysqli->query("SELECT * FROM traits WHERE id='$traitid'") or die($mysqli->error());
					$row4 = mysqli_fetch_array($result4);
					$traitname = $row4['name'];
					
					if($traitname == "wounded"){
						$woundedcount = $woundedcount + 1;
					}
				}
				
				if($woundedcount == 0){
					$result4 = $mysqli->query("SELECT * FROM traits WHERE name='wounded'") or die($mysqli->error());
					$row4 = mysqli_fetch_array($result4);
					$traitid = $row4['id'];
					
					$sql = "INSERT INTO traitscharacters (characterid, traitid, date) " 
					. "VALUES ('$usercharacterid','$traitid',NOW())";
			 		mysqli_query($mysqli, $sql);
					
					$content= "You got wounded while fighting";
					$sql = "INSERT INTO events (date, content, extrainfo) " 
				     . "VALUES (NOW(),'$content','$username')";
					mysqli_query($mysqli2, $sql);
				}
			}
			//header("Refresh:0");
		}else{
			echo "You don't have enough weapons or energy!";
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

if(isset($_POST['defend1'])){
	$id = $mysqli->escape_string($_POST['id']);
	$id = (int) $id;
	$type = $mysqli->escape_string($_POST['type']);
	
	$result = $mysqli->query("SELECT * FROM users WHERE username='$username'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$strength=$row['strength'];
	$energy=$row['energy'];
	$alltimedamage=$row['totaldamage'];
	$militaryunit=$row['militaryunit'];
	$militaryunitrank=$row['militaryunitrank'];
	$location2=$row['location2'];
	$location2 = $mysqli->escape_string($location2);
		
	$result = $mysqli->query("SELECT * FROM diplomacy WHERE id='$id' AND (attackcountry1='$location2' OR attackcountry2='$location2')") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$attackcountry1=$row['attackcountry1'];
	$attackcountry2=$row['attackcountry2'];
	$country12damage=$row['country12damage'];
	$hospital=$row['hospital2'];
	
	if($militaryunit != 0){
		$result = $mysqli->query("SELECT camp FROM militaryunit WHERE id='$militaryunit'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$camp=$row['camp'];
		$camp = $mysqli->escape_string($camp);
		
		if($camp == $location2){
			$militaryunitrankbonus=$militaryunitrank*5+5;
		}else{
			$militaryunitrankbonus=$militaryunitrank*5;
		}
	}else{
		$militaryunitrankbonus=$militaryunitrank*5;
	}
	
	//check strength traits
	$result3 = "SELECT * FROM traitscharacters WHERE characterid='$usercharacterid'";
	$rs_result2 = $mysqli->query($result3);
	$count2 = $rs_result2->num_rows;//aantal titles
	if($count2 != 0){
		$strengthamount = 0;
		while($row2 = $rs_result2->fetch_assoc()) {//ga door traits heen
			$traitid=$row2["traitid"];
			
			$result4 = $mysqli->query("SELECT * FROM traits WHERE id='$traitid' AND type = 'strength'") or die($mysqli->error());
			$row4 = mysqli_fetch_array($result4);
			$traitamount = $row4['amount'];
			
			$strengthamount = $strengthamount + $traitamount;
		}
		$strength = round($strength + (($strength / 100) * $strengthamount));
		if($strength < 0){
			$strength = 0;
		}
	}

	if($type=='no weapon'){
		$energy=$energy-10;
		if($energy>=0){
			$country12damage=$country12damage+$strength+$militaryunitrankbonus;
			$alltimedamage=$alltimedamage+$strength+$militaryunitrankbonus;				
			$sql = "UPDATE diplomacy SET country12damage='$country12damage' WHERE id='$id'";
			mysqli_query($mysqli, $sql);
			
			if($hospital >= 10){
				$hospital=$hospital-10;
				$sql = "UPDATE diplomacy SET hospital2='$hospital' WHERE id='$id'";
				mysqli_query($mysqli, $sql);
				
				$sql = "UPDATE users SET totaldamage='$alltimedamage' WHERE username='$username'";
				mysqli_query($mysqli, $sql);	
			}else{
				$sql = "UPDATE users SET energy='$energy', totaldamage='$alltimedamage' WHERE username='$username'";
				mysqli_query($mysqli, $sql);
			}
			
			$result = $mysqli->query("SELECT * FROM diplomacy WHERE id='$id' ") or die($mysqli->error());
			$row = mysqli_fetch_array($result);
			$paygold2=$row['paygold2'];
			$goldperdamage2=$row['goldperdamage2'];
				
			$result = $mysqli->query("SELECT * FROM users WHERE username='$username'") or die($mysqli->error());
			$row = mysqli_fetch_array($result);
			$militaryunit=$row['militaryunit'];
				
			if($militaryunit > 0){
				$result = $mysqli->query("SELECT * FROM militaryunit WHERE id='$militaryunit'") or die($mysqli->error());
				$row = mysqli_fetch_array($result);
				$unitgold=$row['gold'];
				$percentowner=$row['percentowner'];
				$percentunit=$row['percentunit'];
				$percentuser=$row['percentuser'];
				$owner=$row['owner'];
				
				$result = $mysqli->query("SELECT gold FROM currency WHERE usercur='$owner'") or die($mysqli->error());
				$row = mysqli_fetch_array($result);
				$ownergold=$row['gold'];
			}else{
				$paygold2=0;
			}
			
			$result = $mysqli->query("SELECT gold FROM currency WHERE usercur='$username'") or die($mysqli->error());
			$row = mysqli_fetch_array($result);
			$usergold=$row['gold'];
										
			if($paygold2 > 0){
				$prize=($goldperdamage2*$strength)/100;
				$percentowner=$prize*($percentowner/100);
				$percentunit=$prize*($percentunit/100);
				$percentuser=$prize*($percentuser/100);
						
				$paygold2=$paygold2-$prize;
				$sql = "UPDATE diplomacy SET paygold2='$paygold2' WHERE id='$id'";
				mysqli_query($mysqli, $sql);
				
				$usergold=$usergold+$percentuser;
				$sql = "UPDATE currency SET gold='$usergold' WHERE usercur='$username'";
				mysqli_query($mysqli, $sql);
					
				$usergold=$usergold+$percentuser;
				$sql = "UPDATE currency SET gold='$usergold' WHERE usercur='$username'";
				mysqli_query($mysqli, $sql);
						
				$ownergold=$ownergold+$percentowner;
				$sql = "UPDATE currency SET gold='$ownergold' WHERE usercur='$owner'";
				mysqli_query($mysqli, $sql);
									
				$unitgold=$unitgold+$percentunit;
				$sql = "UPDATE militaryunit SET gold='$unitgold' WHERE id='$militaryunit'";
				mysqli_query($mysqli, $sql);		
			}
			
			//chance to get wounded
			$randnumber = rand(0, 100);
			if($randnumber == 1){
				$randnumber2 = rand(0, 100);
				
				if($randnumber2 <= 5){
					$result4 = $mysqli->query("SELECT * FROM traits WHERE name='incapable'") or die($mysqli->error());
					$row4 = mysqli_fetch_array($result4);
					$traitid = $row4['id'];
					
					$result3 = "SELECT * FROM traitscharacters WHERE characterid='$usercharacterid' AND traitid = '$traitid'";
					$rs_result2 = $mysqli->query($result3);
					$count2 = $rs_result2->num_rows;//aantal titles
					
					if($count2 != 0){
						$sql = "INSERT INTO traitscharacters (characterid, traitid, date) " 
						. "VALUES ('$usercharacterid','$traitid',NOW())";
				 		mysqli_query($mysqli, $sql);
			 		}
				}
				
				$result3 = "SELECT * FROM traitscharacters WHERE characterid='$usercharacterid'";
				$rs_result2 = $mysqli->query($result3);
				$count2 = $rs_result2->num_rows;//aantal titles
				$woundedcount = 0;
				while($row2 = $rs_result2->fetch_assoc()) {//ga door traits heen
					$traitid=$row2["traitid"];
					
					$result4 = $mysqli->query("SELECT * FROM traits WHERE id='$traitid'") or die($mysqli->error());
					$row4 = mysqli_fetch_array($result4);
					$traitname = $row4['name'];
					
					if($traitname == "wounded"){
						$woundedcount = $woundedcount + 1;
					}
				}
				
				if($woundedcount == 0){
					$result4 = $mysqli->query("SELECT * FROM traits WHERE name='wounded'") or die($mysqli->error());
					$row4 = mysqli_fetch_array($result4);
					$traitid = $row4['id'];
					
					$sql = "INSERT INTO traitscharacters (characterid, traitid, date) " 
					. "VALUES ('$usercharacterid','$traitid',NOW())";
			 		mysqli_query($mysqli, $sql);
					
					$content= "You got wounded while fighting";
					$sql = "INSERT INTO events (date, content, extrainfo) " 
				     . "VALUES (NOW(),'$content','$username')";
					mysqli_query($mysqli2, $sql);
				}
			}
			//header("Refresh:0");
		}
	}elseif($type=='weapon q1'){
		$result = $mysqli->query("SELECT * FROM inventory WHERE userinv='$username'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$weapon=$row['weaponq1'];
		
		$weapon=$weapon-1;
		$energy=$energy-10;
		
		if($energy>=0 AND $weapon>=0){
			$country12damage=$country12damage+$strength+50+$militaryunitrankbonus;
			$alltimedamage=$alltimedamage+$strength+50+$militaryunitrankbonus;			
			$sql = "UPDATE diplomacy SET country12damage='$country12damage' WHERE id='$id'";
			mysqli_query($mysqli, $sql);
			
			if($hospital >= 10){
				$hospital=$hospital-10;
				$sql = "UPDATE diplomacy SET hospital2='$hospital' WHERE id='$id'";
				mysqli_query($mysqli, $sql);
				
				$sql = "UPDATE users SET totaldamage='$alltimedamage' WHERE username='$username'";
				mysqli_query($mysqli, $sql);	
			}else{
				$sql = "UPDATE users SET energy='$energy', totaldamage='$alltimedamage' WHERE username='$username'";
				mysqli_query($mysqli, $sql);
			}
			
			$sql = "UPDATE inventory SET weaponq1='$weapon' WHERE userinv='$username'";
			mysqli_query($mysqli, $sql);
			
			$result = $mysqli->query("SELECT * FROM diplomacy WHERE id='$id' ") or die($mysqli->error());
			$row = mysqli_fetch_array($result);
			$paygold2=$row['paygold2'];
			$goldperdamage2=$row['goldperdamage2'];
				
			$result = $mysqli->query("SELECT * FROM users WHERE username='$username'") or die($mysqli->error());
			$row = mysqli_fetch_array($result);
			$militaryunit=$row['militaryunit'];
				
			if($militaryunit > 0){
				$result = $mysqli->query("SELECT * FROM militaryunit WHERE id='$militaryunit'") or die($mysqli->error());
				$row = mysqli_fetch_array($result);
				$unitgold=$row['gold'];
				$percentowner=$row['percentowner'];
				$percentunit=$row['percentunit'];
				$percentuser=$row['percentuser'];
				$owner=$row['owner'];
				
				$result = $mysqli->query("SELECT gold FROM currency WHERE usercur='$owner'") or die($mysqli->error());
				$row = mysqli_fetch_array($result);
				$ownergold=$row['gold'];
			}else{
				$paygold2=0;
			}
			
			$result = $mysqli->query("SELECT gold FROM currency WHERE usercur='$username'") or die($mysqli->error());
			$row = mysqli_fetch_array($result);
			$usergold=$row['gold'];
										
			if($paygold2 > 0){
				$prize=($goldperdamage2*($strength+50))/100;
				$percentowner=$prize*($percentowner/100);
				$percentunit=$prize*($percentunit/100);
				$percentuser=$prize*($percentuser/100);
						
				$paygold2=$paygold2-$prize;
				$sql = "UPDATE diplomacy SET paygold2='$paygold2' WHERE id='$id'";
				mysqli_query($mysqli, $sql);
				
				$usergold=$usergold+$percentuser;
				$sql = "UPDATE currency SET gold='$usergold' WHERE usercur='$username'";
				mysqli_query($mysqli, $sql);
					
				$usergold=$usergold+$percentuser;
				$sql = "UPDATE currency SET gold='$usergold' WHERE usercur='$username'";
				mysqli_query($mysqli, $sql);
						
				$ownergold=$ownergold+$percentowner;
				$sql = "UPDATE currency SET gold='$ownergold' WHERE usercur='$owner'";
				mysqli_query($mysqli, $sql);
									
				$unitgold=$unitgold+$percentunit;
				$sql = "UPDATE militaryunit SET gold='$unitgold' WHERE id='$militaryunit'";
				mysqli_query($mysqli, $sql);		
			}
			
			//chance to get wounded
			$randnumber = rand(0, 100);
			if($randnumber == 1){
				$randnumber2 = rand(0, 100);
				
				if($randnumber2 <= 5){
					$result4 = $mysqli->query("SELECT * FROM traits WHERE name='incapable'") or die($mysqli->error());
					$row4 = mysqli_fetch_array($result4);
					$traitid = $row4['id'];
					
					$result3 = "SELECT * FROM traitscharacters WHERE characterid='$usercharacterid' AND traitid = '$traitid'";
					$rs_result2 = $mysqli->query($result3);
					$count2 = $rs_result2->num_rows;//aantal titles
					
					if($count2 != 0){
						$sql = "INSERT INTO traitscharacters (characterid, traitid, date) " 
						. "VALUES ('$usercharacterid','$traitid',NOW())";
				 		mysqli_query($mysqli, $sql);
			 		}
				}
				
				$result3 = "SELECT * FROM traitscharacters WHERE characterid='$usercharacterid'";
				$rs_result2 = $mysqli->query($result3);
				$count2 = $rs_result2->num_rows;//aantal titles
				$woundedcount = 0;
				while($row2 = $rs_result2->fetch_assoc()) {//ga door traits heen
					$traitid=$row2["traitid"];
					
					$result4 = $mysqli->query("SELECT * FROM traits WHERE id='$traitid'") or die($mysqli->error());
					$row4 = mysqli_fetch_array($result4);
					$traitname = $row4['name'];
					
					if($traitname == "wounded"){
						$woundedcount = $woundedcount + 1;
					}
				}
				
				if($woundedcount == 0){
					$result4 = $mysqli->query("SELECT * FROM traits WHERE name='wounded'") or die($mysqli->error());
					$row4 = mysqli_fetch_array($result4);
					$traitid = $row4['id'];
					
					$sql = "INSERT INTO traitscharacters (characterid, traitid, date) " 
					. "VALUES ('$usercharacterid','$traitid',NOW())";
			 		mysqli_query($mysqli, $sql);
					
					$content= "You got wounded while fighting";
					$sql = "INSERT INTO events (date, content, extrainfo) " 
				     . "VALUES (NOW(),'$content','$username')";
					mysqli_query($mysqli2, $sql);
				}
			}
			//header("Refresh:0");
		}else{
			echo "You don't have enough weapons or energy!";
		}	
	}elseif($type=='weapon q2'){
		$result = $mysqli->query("SELECT * FROM inventory WHERE userinv='$username'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$weapon=$row['weaponq2'];
		
		$weapon=$weapon-1;
		$energy=$energy-10;
		
		if($energy>=0 AND $weapon>=0){
			$country12damage=$country12damage+$strength+100+$militaryunitrankbonus;
			$alltimedamage=$alltimedamage+$strength+100+$militaryunitrankbonus;		
			$sql = "UPDATE diplomacy SET country12damage='$country12damage' WHERE id='$id'";
			mysqli_query($mysqli, $sql);
			
			if($hospital >= 10){
				$hospital=$hospital-10;
				$sql = "UPDATE diplomacy SET hospital2='$hospital' WHERE id='$id'";
				mysqli_query($mysqli, $sql);
				
				$sql = "UPDATE users SET totaldamage='$alltimedamage' WHERE username='$username'";
				mysqli_query($mysqli, $sql);	
			}else{
				$sql = "UPDATE users SET energy='$energy', totaldamage='$alltimedamage' WHERE username='$username'";
				mysqli_query($mysqli, $sql);
			}
			
			$sql = "UPDATE inventory SET weaponq2='$weapon' WHERE userinv='$username'";
			mysqli_query($mysqli, $sql);
			
			$result = $mysqli->query("SELECT * FROM diplomacy WHERE id='$id' ") or die($mysqli->error());
			$row = mysqli_fetch_array($result);
			$paygold2=$row['paygold2'];
			$goldperdamage2=$row['goldperdamage2'];
				
			$result = $mysqli->query("SELECT * FROM users WHERE username='$username'") or die($mysqli->error());
			$row = mysqli_fetch_array($result);
			$militaryunit=$row['militaryunit'];
				
			if($militaryunit > 0){
				$result = $mysqli->query("SELECT * FROM militaryunit WHERE id='$militaryunit'") or die($mysqli->error());
				$row = mysqli_fetch_array($result);
				$unitgold=$row['gold'];
				$percentowner=$row['percentowner'];
				$percentunit=$row['percentunit'];
				$percentuser=$row['percentuser'];
				$owner=$row['owner'];
				
				$result = $mysqli->query("SELECT gold FROM currency WHERE usercur='$owner'") or die($mysqli->error());
				$row = mysqli_fetch_array($result);
				$ownergold=$row['gold'];
			}else{
				$paygold2=0;
			}
			
			$result = $mysqli->query("SELECT gold FROM currency WHERE usercur='$username'") or die($mysqli->error());
			$row = mysqli_fetch_array($result);
			$usergold=$row['gold'];
										
			if($paygold2 > 0){
				$prize=($goldperdamage2*($strength+100))/100;
				$percentowner=$prize*($percentowner/100);
				$percentunit=$prize*($percentunit/100);
				$percentuser=$prize*($percentuser/100);
						
				$paygold2=$paygold2-$prize;
				$sql = "UPDATE diplomacy SET paygold2='$paygold2' WHERE id='$id'";
				mysqli_query($mysqli, $sql);
				
				$usergold=$usergold+$percentuser;
				$sql = "UPDATE currency SET gold='$usergold' WHERE usercur='$username'";
				mysqli_query($mysqli, $sql);
					
				$usergold=$usergold+$percentuser;
				$sql = "UPDATE currency SET gold='$usergold' WHERE usercur='$username'";
				mysqli_query($mysqli, $sql);
						
				$ownergold=$ownergold+$percentowner;
				$sql = "UPDATE currency SET gold='$ownergold' WHERE usercur='$owner'";
				mysqli_query($mysqli, $sql);
									
				$unitgold=$unitgold+$percentunit;
				$sql = "UPDATE militaryunit SET gold='$unitgold' WHERE id='$militaryunit'";
				mysqli_query($mysqli, $sql);		
			}
			
			//chance to get wounded
			$randnumber = rand(0, 100);
			if($randnumber == 1){
				$randnumber2 = rand(0, 100);
				
				if($randnumber2 <= 5){
					$result4 = $mysqli->query("SELECT * FROM traits WHERE name='incapable'") or die($mysqli->error());
					$row4 = mysqli_fetch_array($result4);
					$traitid = $row4['id'];
					
					$result3 = "SELECT * FROM traitscharacters WHERE characterid='$usercharacterid' AND traitid = '$traitid'";
					$rs_result2 = $mysqli->query($result3);
					$count2 = $rs_result2->num_rows;//aantal titles
					
					if($count2 != 0){
						$sql = "INSERT INTO traitscharacters (characterid, traitid, date) " 
						. "VALUES ('$usercharacterid','$traitid',NOW())";
				 		mysqli_query($mysqli, $sql);
			 		}
				}
				
				$result3 = "SELECT * FROM traitscharacters WHERE characterid='$usercharacterid'";
				$rs_result2 = $mysqli->query($result3);
				$count2 = $rs_result2->num_rows;//aantal titles
				$woundedcount = 0;
				while($row2 = $rs_result2->fetch_assoc()) {//ga door traits heen
					$traitid=$row2["traitid"];
					
					$result4 = $mysqli->query("SELECT * FROM traits WHERE id='$traitid'") or die($mysqli->error());
					$row4 = mysqli_fetch_array($result4);
					$traitname = $row4['name'];
					
					if($traitname == "wounded"){
						$woundedcount = $woundedcount + 1;
					}
				}
				
				if($woundedcount == 0){
					$result4 = $mysqli->query("SELECT * FROM traits WHERE name='wounded'") or die($mysqli->error());
					$row4 = mysqli_fetch_array($result4);
					$traitid = $row4['id'];
					
					$sql = "INSERT INTO traitscharacters (characterid, traitid, date) " 
					. "VALUES ('$usercharacterid','$traitid',NOW())";
			 		mysqli_query($mysqli, $sql);
					
					$content= "You got wounded while fighting";
					$sql = "INSERT INTO events (date, content, extrainfo) " 
				     . "VALUES (NOW(),'$content','$username')";
					mysqli_query($mysqli2, $sql);
				}
			}
			//header("Refresh:0");
		}else{
			echo "You don't have enough weapons or energy!";
		}	
	}elseif($type=='weapon q3'){
		$result = $mysqli->query("SELECT * FROM inventory WHERE userinv='$username'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$weapon=$row['weaponq3'];
		
		$weapon=$weapon-1;
		$energy=$energy-10;
		
		if($energy>=0 AND $weapon>=0){
			$country12damage=$country12damage+$strength+150+$militaryunitrankbonus;
			$alltimedamage=$alltimedamage+$strength+150+$militaryunitrankbonus;		
			$sql = "UPDATE diplomacy SET country12damage='$country12damage' WHERE id='$id'";
			mysqli_query($mysqli, $sql);
			
			if($hospital >= 10){
				$hospital=$hospital-10;
				$sql = "UPDATE diplomacy SET hospital2='$hospital' WHERE id='$id'";
				mysqli_query($mysqli, $sql);
				
				$sql = "UPDATE users SET totaldamage='$alltimedamage' WHERE username='$username'";
				mysqli_query($mysqli, $sql);	
			}else{
				$sql = "UPDATE users SET energy='$energy', totaldamage='$alltimedamage' WHERE username='$username'";
				mysqli_query($mysqli, $sql);
			}
			
			$sql = "UPDATE inventory SET weaponq3='$weapon' WHERE userinv='$username'";
			mysqli_query($mysqli, $sql);
			
			$result = $mysqli->query("SELECT * FROM diplomacy WHERE id='$id' ") or die($mysqli->error());
			$row = mysqli_fetch_array($result);
			$paygold2=$row['paygold2'];
			$goldperdamage2=$row['goldperdamage2'];
				
			$result = $mysqli->query("SELECT * FROM users WHERE username='$username'") or die($mysqli->error());
			$row = mysqli_fetch_array($result);
			$militaryunit=$row['militaryunit'];
				
			if($militaryunit > 0){
				$result = $mysqli->query("SELECT * FROM militaryunit WHERE id='$militaryunit'") or die($mysqli->error());
				$row = mysqli_fetch_array($result);
				$unitgold=$row['gold'];
				$percentowner=$row['percentowner'];
				$percentunit=$row['percentunit'];
				$percentuser=$row['percentuser'];
				$owner=$row['owner'];
				
				$result = $mysqli->query("SELECT gold FROM currency WHERE usercur='$owner'") or die($mysqli->error());
				$row = mysqli_fetch_array($result);
				$ownergold=$row['gold'];
			}else{
				$paygold2=0;
			}
			
			$result = $mysqli->query("SELECT gold FROM currency WHERE usercur='$username'") or die($mysqli->error());
			$row = mysqli_fetch_array($result);
			$usergold=$row['gold'];
										
			if($paygold2 > 0){
				$prize=($goldperdamage2*($strength+150))/100;
				$percentowner=$prize*($percentowner/100);
				$percentunit=$prize*($percentunit/100);
				$percentuser=$prize*($percentuser/100);
						
				$paygold2=$paygold2-$prize;
				$sql = "UPDATE diplomacy SET paygold2='$paygold2' WHERE id='$id'";
				mysqli_query($mysqli, $sql);
				
				$usergold=$usergold+$percentuser;
				$sql = "UPDATE currency SET gold='$usergold' WHERE usercur='$username'";
				mysqli_query($mysqli, $sql);
					
				$usergold=$usergold+$percentuser;
				$sql = "UPDATE currency SET gold='$usergold' WHERE usercur='$username'";
				mysqli_query($mysqli, $sql);
						
				$ownergold=$ownergold+$percentowner;
				$sql = "UPDATE currency SET gold='$ownergold' WHERE usercur='$owner'";
				mysqli_query($mysqli, $sql);
									
				$unitgold=$unitgold+$percentunit;
				$sql = "UPDATE militaryunit SET gold='$unitgold' WHERE id='$militaryunit'";
				mysqli_query($mysqli, $sql);		
			}
			
			//chance to get wounded
			$randnumber = rand(0, 100);
			if($randnumber == 1){
				$randnumber2 = rand(0, 100);
				
				if($randnumber2 <= 5){
					$result4 = $mysqli->query("SELECT * FROM traits WHERE name='incapable'") or die($mysqli->error());
					$row4 = mysqli_fetch_array($result4);
					$traitid = $row4['id'];
					
					$result3 = "SELECT * FROM traitscharacters WHERE characterid='$usercharacterid' AND traitid = '$traitid'";
					$rs_result2 = $mysqli->query($result3);
					$count2 = $rs_result2->num_rows;//aantal titles
					
					if($count2 != 0){
						$sql = "INSERT INTO traitscharacters (characterid, traitid, date) " 
						. "VALUES ('$usercharacterid','$traitid',NOW())";
				 		mysqli_query($mysqli, $sql);
			 		}
				}
				
				$result3 = "SELECT * FROM traitscharacters WHERE characterid='$usercharacterid'";
				$rs_result2 = $mysqli->query($result3);
				$count2 = $rs_result2->num_rows;//aantal titles
				$woundedcount = 0;
				while($row2 = $rs_result2->fetch_assoc()) {//ga door traits heen
					$traitid=$row2["traitid"];
					
					$result4 = $mysqli->query("SELECT * FROM traits WHERE id='$traitid'") or die($mysqli->error());
					$row4 = mysqli_fetch_array($result4);
					$traitname = $row4['name'];
					
					if($traitname == "wounded"){
						$woundedcount = $woundedcount + 1;
					}
				}
				
				if($woundedcount == 0){
					$result4 = $mysqli->query("SELECT * FROM traits WHERE name='wounded'") or die($mysqli->error());
					$row4 = mysqli_fetch_array($result4);
					$traitid = $row4['id'];
					
					$sql = "INSERT INTO traitscharacters (characterid, traitid, date) " 
					. "VALUES ('$usercharacterid','$traitid',NOW())";
			 		mysqli_query($mysqli, $sql);
					
					$content= "You got wounded while fighting";
					$sql = "INSERT INTO events (date, content, extrainfo) " 
				     . "VALUES (NOW(),'$content','$username')";
					mysqli_query($mysqli2, $sql);
				}
			}
			//header("Refresh:0");
		}else{
			echo "You don't have enough weapons or energy!";
		}	
	}elseif($type=='weapon q4'){
		$result = $mysqli->query("SELECT * FROM inventory WHERE userinv='$username'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$weapon=$row['weaponq4'];
		
		$weapon=$weapon-1;
		$energy=$energy-10;
		
		if($energy>=0 AND $weapon>=0){
			$country12damage=$country12damage+$strength+200+$militaryunitrankbonus;
			$alltimedamage=$alltimedamage+$strength+200+$militaryunitrankbonus;			
			$sql = "UPDATE diplomacy SET country12damage='$country12damage' WHERE id='$id'";
			mysqli_query($mysqli, $sql);
			
			if($hospital >= 10){
				$hospital=$hospital-10;
				$sql = "UPDATE diplomacy SET hospital2='$hospital' WHERE id='$id'";
				mysqli_query($mysqli, $sql);
				
				$sql = "UPDATE users SET totaldamage='$alltimedamage' WHERE username='$username'";
				mysqli_query($mysqli, $sql);	
			}else{
				$sql = "UPDATE users SET energy='$energy', totaldamage='$alltimedamage' WHERE username='$username'";
				mysqli_query($mysqli, $sql);
			}
			
			$sql = "UPDATE inventory SET weaponq4='$weapon' WHERE userinv='$username'";
			mysqli_query($mysqli, $sql);
			
			$result = $mysqli->query("SELECT * FROM diplomacy WHERE id='$id' ") or die($mysqli->error());
			$row = mysqli_fetch_array($result);
			$paygold2=$row['paygold2'];
			$goldperdamage2=$row['goldperdamage2'];
				
			$result = $mysqli->query("SELECT * FROM users WHERE username='$username'") or die($mysqli->error());
			$row = mysqli_fetch_array($result);
			$militaryunit=$row['militaryunit'];
				
			if($militaryunit > 0){
				$result = $mysqli->query("SELECT * FROM militaryunit WHERE id='$militaryunit'") or die($mysqli->error());
				$row = mysqli_fetch_array($result);
				$unitgold=$row['gold'];
				$percentowner=$row['percentowner'];
				$percentunit=$row['percentunit'];
				$percentuser=$row['percentuser'];
				$owner=$row['owner'];
				
				$result = $mysqli->query("SELECT gold FROM currency WHERE usercur='$owner'") or die($mysqli->error());
				$row = mysqli_fetch_array($result);
				$ownergold=$row['gold'];
			}else{
				$paygold2=0;
			}
			
			$result = $mysqli->query("SELECT gold FROM currency WHERE usercur='$username'") or die($mysqli->error());
			$row = mysqli_fetch_array($result);
			$usergold=$row['gold'];
										
			if($paygold2 > 0){
				$prize=($goldperdamage2*($strength+200))/100;
				$percentowner=$prize*($percentowner/100);
				$percentunit=$prize*($percentunit/100);
				$percentuser=$prize*($percentuser/100);
						
				$paygold2=$paygold2-$prize;
				$sql = "UPDATE diplomacy SET paygold2='$paygold2' WHERE id='$id'";
				mysqli_query($mysqli, $sql);
				
				$usergold=$usergold+$percentuser;
				$sql = "UPDATE currency SET gold='$usergold' WHERE usercur='$username'";
				mysqli_query($mysqli, $sql);
					
				$usergold=$usergold+$percentuser;
				$sql = "UPDATE currency SET gold='$usergold' WHERE usercur='$username'";
				mysqli_query($mysqli, $sql);
						
				$ownergold=$ownergold+$percentowner;
				$sql = "UPDATE currency SET gold='$ownergold' WHERE usercur='$owner'";
				mysqli_query($mysqli, $sql);
									
				$unitgold=$unitgold+$percentunit;
				$sql = "UPDATE militaryunit SET gold='$unitgold' WHERE id='$militaryunit'";
				mysqli_query($mysqli, $sql);		
			}
			
			//chance to get wounded
			$randnumber = rand(0, 100);
			if($randnumber == 1){
				$randnumber2 = rand(0, 100);
				
				if($randnumber2 <= 5){
					$result4 = $mysqli->query("SELECT * FROM traits WHERE name='incapable'") or die($mysqli->error());
					$row4 = mysqli_fetch_array($result4);
					$traitid = $row4['id'];
					
					$result3 = "SELECT * FROM traitscharacters WHERE characterid='$usercharacterid' AND traitid = '$traitid'";
					$rs_result2 = $mysqli->query($result3);
					$count2 = $rs_result2->num_rows;//aantal titles
					
					if($count2 != 0){
						$sql = "INSERT INTO traitscharacters (characterid, traitid, date) " 
						. "VALUES ('$usercharacterid','$traitid',NOW())";
				 		mysqli_query($mysqli, $sql);
			 		}
				}
				
				$result3 = "SELECT * FROM traitscharacters WHERE characterid='$usercharacterid'";
				$rs_result2 = $mysqli->query($result3);
				$count2 = $rs_result2->num_rows;//aantal titles
				$woundedcount = 0;
				while($row2 = $rs_result2->fetch_assoc()) {//ga door traits heen
					$traitid=$row2["traitid"];
					
					$result4 = $mysqli->query("SELECT * FROM traits WHERE id='$traitid'") or die($mysqli->error());
					$row4 = mysqli_fetch_array($result4);
					$traitname = $row4['name'];
					
					if($traitname == "wounded"){
						$woundedcount = $woundedcount + 1;
					}
				}
				
				if($woundedcount == 0){
					$result4 = $mysqli->query("SELECT * FROM traits WHERE name='wounded'") or die($mysqli->error());
					$row4 = mysqli_fetch_array($result4);
					$traitid = $row4['id'];
					
					$sql = "INSERT INTO traitscharacters (characterid, traitid, date) " 
					. "VALUES ('$usercharacterid','$traitid',NOW())";
			 		mysqli_query($mysqli, $sql);
					
					$content= "You got wounded while fighting";
					$sql = "INSERT INTO events (date, content, extrainfo) " 
				     . "VALUES (NOW(),'$content','$username')";
					mysqli_query($mysqli2, $sql);
				}
			}
			//header("Refresh:0");
		}else{
			echo "You don't have enough weapons or energy!";
		}	
	}elseif($type=='weapon q5'){
		$result = $mysqli->query("SELECT * FROM inventory WHERE userinv='$username'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$weapon=$row['weaponq5'];
		
		$weapon=$weapon-1;
		$energy=$energy-10;
		
		if($energy>=0 AND $weapon>=0){
			$country12damage=$country12damage+$strength+250+$militaryunitrankbonus;
			$alltimedamage=$alltimedamage+$strength+250+$militaryunitrankbonus;
			$sql = "UPDATE diplomacy SET country12damage='$country12damage' WHERE id='$id'";
			mysqli_query($mysqli, $sql);
			
			if($hospital >= 10){
				$hospital=$hospital-10;
				$sql = "UPDATE diplomacy SET hospital2='$hospital' WHERE id='$id'";
				mysqli_query($mysqli, $sql);
				
				$sql = "UPDATE users SET totaldamage='$alltimedamage' WHERE username='$username'";
				mysqli_query($mysqli, $sql);	
			}else{
				$sql = "UPDATE users SET energy='$energy', totaldamage='$alltimedamage' WHERE username='$username'";
				mysqli_query($mysqli, $sql);
			}
			
			$sql = "UPDATE inventory SET weaponq5='$weapon' WHERE userinv='$username'";
			mysqli_query($mysqli, $sql);
			
			$result = $mysqli->query("SELECT * FROM diplomacy WHERE id='$id' ") or die($mysqli->error());
			$row = mysqli_fetch_array($result);
			$paygold2=$row['paygold2'];
			$goldperdamage2=$row['goldperdamage2'];
				
			$result = $mysqli->query("SELECT * FROM users WHERE username='$username'") or die($mysqli->error());
			$row = mysqli_fetch_array($result);
			$militaryunit=$row['militaryunit'];
				
			if($militaryunit > 0){
				$result = $mysqli->query("SELECT * FROM militaryunit WHERE id='$militaryunit'") or die($mysqli->error());
				$row = mysqli_fetch_array($result);
				$unitgold=$row['gold'];
				$percentowner=$row['percentowner'];
				$percentunit=$row['percentunit'];
				$percentuser=$row['percentuser'];
				$owner=$row['owner'];
				
				$result = $mysqli->query("SELECT gold FROM currency WHERE usercur='$owner'") or die($mysqli->error());
				$row = mysqli_fetch_array($result);
				$ownergold=$row['gold'];
			}else{
				$paygold2=0;
			}
			
			$result = $mysqli->query("SELECT gold FROM currency WHERE usercur='$username'") or die($mysqli->error());
			$row = mysqli_fetch_array($result);
			$usergold=$row['gold'];
										
			if($paygold2 > 0){
				$prize=($goldperdamage2*($strength+250))/100;
				$percentowner=$prize*($percentowner/100);
				$percentunit=$prize*($percentunit/100);
				$percentuser=$prize*($percentuser/100);
						
				$paygold2=$paygold2-$prize;
				$sql = "UPDATE diplomacy SET paygold2='$paygold2' WHERE id='$id'";
				mysqli_query($mysqli, $sql);
				
				$usergold=$usergold+$percentuser;
				$sql = "UPDATE currency SET gold='$usergold' WHERE usercur='$username'";
				mysqli_query($mysqli, $sql);
					
				$usergold=$usergold+$percentuser;
				$sql = "UPDATE currency SET gold='$usergold' WHERE usercur='$username'";
				mysqli_query($mysqli, $sql);
						
				$ownergold=$ownergold+$percentowner;
				$sql = "UPDATE currency SET gold='$ownergold' WHERE usercur='$owner'";
				mysqli_query($mysqli, $sql);
									
				$unitgold=$unitgold+$percentunit;
				$sql = "UPDATE militaryunit SET gold='$unitgold' WHERE id='$militaryunit'";
				mysqli_query($mysqli, $sql);		
			}
			
			//chance to get wounded
			$randnumber = rand(0, 100);
			if($randnumber == 1){
				$randnumber2 = rand(0, 100);
				
				if($randnumber2 <= 5){
					$result4 = $mysqli->query("SELECT * FROM traits WHERE name='incapable'") or die($mysqli->error());
					$row4 = mysqli_fetch_array($result4);
					$traitid = $row4['id'];
					
					$result3 = "SELECT * FROM traitscharacters WHERE characterid='$usercharacterid' AND traitid = '$traitid'";
					$rs_result2 = $mysqli->query($result3);
					$count2 = $rs_result2->num_rows;//aantal titles
					
					if($count2 != 0){
						$sql = "INSERT INTO traitscharacters (characterid, traitid, date) " 
						. "VALUES ('$usercharacterid','$traitid',NOW())";
				 		mysqli_query($mysqli, $sql);
			 		}
				}
				
				$result3 = "SELECT * FROM traitscharacters WHERE characterid='$usercharacterid'";
				$rs_result2 = $mysqli->query($result3);
				$count2 = $rs_result2->num_rows;//aantal titles
				$woundedcount = 0;
				while($row2 = $rs_result2->fetch_assoc()) {//ga door traits heen
					$traitid=$row2["traitid"];
					
					$result4 = $mysqli->query("SELECT * FROM traits WHERE id='$traitid'") or die($mysqli->error());
					$row4 = mysqli_fetch_array($result4);
					$traitname = $row4['name'];
					
					if($traitname == "wounded"){
						$woundedcount = $woundedcount + 1;
					}
				}
				
				if($woundedcount == 0){
					$result4 = $mysqli->query("SELECT * FROM traits WHERE name='wounded'") or die($mysqli->error());
					$row4 = mysqli_fetch_array($result4);
					$traitid = $row4['id'];
					
					$sql = "INSERT INTO traitscharacters (characterid, traitid, date) " 
					. "VALUES ('$usercharacterid','$traitid',NOW())";
			 		mysqli_query($mysqli, $sql);
					
					$content= "You got wounded while fighting";
					$sql = "INSERT INTO events (date, content, extrainfo) " 
				     . "VALUES (NOW(),'$content','$username')";
					mysqli_query($mysqli2, $sql);
				}
			}
			//header("Refresh:0");
		}else{
			echo "You don't have enough weapons or energy!";
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

?>
</div>
</body>
<footer>
<?php require 'bottombar.php'; ?>
</footer>
</html>
