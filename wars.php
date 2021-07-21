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
  //header("location: error.php");    
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

//set bonus percentage damage when religion in region is same as region
$relbonus=0.01;

$result = $mysqli->query("SELECT * FROM users WHERE username='$username'") or die($mysqli->error());
$row = mysqli_fetch_array($result);
$location=$row['location'];
$location2=$row['location2'];
$location2 = $mysqli->escape_string($location2);

$result = $mysqli->query("SELECT * FROM countryinfo WHERE country='$location'") or die($mysqli->error());
$row = mysqli_fetch_array($result);
$countrypresident=$row['countrypresident'];

$result = $mysqli->query("SELECT * FROM diplomacy WHERE type='war' AND ( attackcountry1='$location2' OR attackcountry2='$location2' ) ") or die($mysqli->error());
for ($set=array(); $row=$result->fetch_assoc(); $set[]=$row);
//print_r($set);
		
echo nl2br(" \n");
		
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
	$attackcountry2[$key]=$value['attackcountry2'];
	$attackcountry2[$key]=$mysqli->escape_string($attackcountry2[$key]);
	$attackcountry2start[$key]=$value['attackcountry2start'];
	$country21damage[$key]=$value['country21damage'];
	$country22damage[$key]=$value['country22damage'];
	$paygold1[$key]=$value['paygold1'];
	$goldperdamage1[$key]=$value['goldperdamage1'];
	$goldperdamage2[$key]=$value['goldperdamage2'];
	$hospital1[$key]=$value['hospital1'];
	$paygold2[$key]=$value['paygold2'];
	$hospital2[$key]=$value['hospital2'];
	
	//check dates
	$date1=$attackcountry1start[$key]; //date voor country1
	//echo date('Y-m-d H:i:s', strtotime($date1. ' + 1 days'));
	$date = new DateTime($date1);
	$date->add(new DateInterval('P5D')); // P1D means a period of 1 day
	$Datenew1 = $date->format('Y-m-d H:i:s');
	//echo "$Datenew1";
	
	$date2=$attackcountry2start[$key]; //date voor country2
	//echo date('Y-m-d H:i:s', strtotime($date1. ' + 1 days'));
	$date = new DateTime($date2);
	$date->add(new DateInterval('P5D')); // P1D means a period of 1 day
	$Datenew2 = $date->format('Y-m-d H:i:s');
	//echo "$Datenew2";
	
	date_default_timezone_set('UTC'); //current date
	$datecur = date("Y-m-d H:i:s"); 
	
	if($datecur>$Datenew1){
		$attackcountry1[$key]=$mysqli->escape_string($attackcountry1[$key]);
		
		$result = $mysqli->query("SELECT biggestrel FROM region WHERE name='$attackcountry1[$key]'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$biggestrel=$row['biggestrel'];
		
		$result = $mysqli->query("SELECT statereligion FROM countryinfo WHERE country='$country1[$key]'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$statereligion1=$row['statereligion'];
		
		$result = $mysqli->query("SELECT statereligion FROM countryinfo WHERE country='$country2[$key]'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$statereligion2=$row['statereligion'];
		
		if($statereligion1=$biggestrel){
			$country11damage[$key]=$country11damage[$key]+$relbonus*$country11damage[$key];
		}elseif($statereligion2=$biggestrel){
			$country12damage[$key]=$country12damage[$key]+$relbonus*$country12damage[$key];
		}
		if($country11damage[$key]>$country12damage[$key]){ //country 1 gewonnen
			//$sql = "UPDATE region SET curowner='$country1[$key]' WHERE name='$attackcountry1[$key]'";
			//mysqli_query($mysqli, $sql);
			
			//insert claim if liege of region is not countrypresident
			$result = $mysqli->query("SELECT * FROM countryinfo WHERE country='$country1[$key]'") or die($mysqli->error());
			$row = mysqli_fetch_array($result);
			$characterownercountry1=$row['characterowner'];
			$countrypresidentcountry1=$row['characterowner'];
			
			$result = $mysqli->query("SELECT * FROM region WHERE name='$attackcountry1[$key]'") or die($mysqli->error());
			$row = mysqli_fetch_array($result);
			$idregion1=$row['id'];
			$characterownerregion1=$row['characterowner'];
			$nameregion1=$row['name'];
			$nameregion1=$mysqli->escape_string($nameregion1);
			
			$result = $mysqli->query("SELECT * FROM characters WHERE id='$characterownerregion1[$key]'") or die($mysqli->error());
			$row = mysqli_fetch_array($result);
			$characterliegeregion1=$row['liege'];
			
			$result = $mysqli->query("SELECT * FROM titles WHERE holdingid='$idregion1'") or die($mysqli->error());
			$row = mysqli_fetch_array($result);
			$titleid=$row['id'];
			
			if($characterliegeregion1 != $characterownercountry1){
				$sql = "INSERT INTO claim (type, inheritable, charowner, title, date, countryowner) " 
				. "VALUES ('retractclaim','0','$characterownercountry1','$titleid',NOW(),'$country1[$key]')";
		 		mysqli_query($mysqli, $sql);
				$lastid = $mysqli->insert_id;
				
				$content= "By conquering the duchy of $nameregion1 you obtained a claim on this duchy";
				$content=$mysqli->escape_string($content);
				$sql = "INSERT INTO events (date, content, extrainfo) " 
			     . "VALUES (NOW(),'$content','$countrypresidentcountry1')";
				mysqli_query($mysqli2, $sql);
			}
			
			//$sql = "UPDATE users SET location='$country1[$key]' WHERE location2='$attackcountry1[$key]'"; //update country of companies
			//mysqli_query($mysqli, $sql);
			
			//$sql = "UPDATE companies SET countryco='$country1[$key]' WHERE region='$attackcountry1[$key]'"; //update country of companies
			//mysqli_query($mysqli, $sql);
		}
		$sql = "UPDATE diplomacy SET attackcountry1='NULL', attackcountry1start='2099-01-01 00:00:00' WHERE id='$id[$key]'";
		mysqli_query($mysqli, $sql);
	}

	if($datecur>$Datenew2){
		$attackcountry2[$key]=$mysqli->escape_string($attackcountry2[$key]);
		
		$result = $mysqli->query("SELECT biggestrel FROM region WHERE name='$attackcountry2[$key]'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$biggestrel=$row['biggestrel'];
		
		$result = $mysqli->query("SELECT statereligion FROM countryinfo WHERE country='$country1[$key]'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$statereligion1=$row['statereligion'];
		
		$result = $mysqli->query("SELECT statereligion FROM countryinfo WHERE country='$country2[$key]'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$statereligion2=$row['statereligion'];
		
		if($statereligion2=$biggestrel){
			$country21damage[$key]=$country21damage[$key]+$relbonus*$country21damage[$key];
		}elseif($statereligion1=$biggestrel){
			$country22damage[$key]=$country22damage[$key]+$relbonus*$country22damage[$key];
		}
		
		if($country21damage[$key]>$country22damage[$key]){ //country 2 gewonnen
			//$sql = "UPDATE region SET curowner='$country2[$key]' WHERE name='$attackcountry2[$key]'";
			//mysqli_query($mysqli, $sql);
			
			//insert claim if liege of region is not countrypresident
			$result = $mysqli->query("SELECT * FROM countryinfo WHERE country='$country2[$key]'") or die($mysqli->error());
			$row = mysqli_fetch_array($result);
			$characterownercountry2=$row['characterowner'];
			$countrypresidentcountry2=$row['characterowner'];
			
			$result = $mysqli->query("SELECT * FROM region WHERE name='$attackcountry2[$key]'") or die($mysqli->error());
			$row = mysqli_fetch_array($result);
			$idregion2=$row['id'];
			$characterownerregion2=$row['characterowner'];
			$nameregion2=$row['name'];
			$nameregion2=$mysqli->escape_string($nameregion2);
			
			$result = $mysqli->query("SELECT * FROM characters WHERE id='$characterownerregion2[$key]'") or die($mysqli->error());
			$row = mysqli_fetch_array($result);
			$characterliegeregion2=$row['liege'];
			
			$result = $mysqli->query("SELECT * FROM titles WHERE holdingid='$idregion2'") or die($mysqli->error());
			$row = mysqli_fetch_array($result);
			$titleid=$row['id'];
			
			if($characterliegeregion1 != $characterownercountry1){
				$sql = "INSERT INTO claim (type, inheritable, charowner, title, date, countryowner) " 
				. "VALUES ('retractclaim','0','$characterownercountry2','$titleid',NOW(),'$country1[$key]')";
		 		mysqli_query($mysqli, $sql);
				$lastid = $mysqli->insert_id;
				
				$content= "By conquering the duchy of $nameregion2 you obtained a claim on this duchy";
				$content=$mysqli->escape_string($content);
				$sql = "INSERT INTO events (date, content, extrainfo) " 
			     . "VALUES (NOW(),'$content','$countrypresidentcountry2')";
				mysqli_query($mysqli2, $sql);
			}
			
			//$sql = "UPDATE users SET location='$country2[$key]' WHERE location2='$attackcountry2[$key]'"; //update country of companies
			//mysqli_query($mysqli, $sql);
			
			//$sql = "UPDATE companies SET countryco='$country2[$key]' WHERE region='$attackcountry2[$key]'"; //update country of companies
			//mysqli_query($mysqli, $sql);
		}
		$sql = "UPDATE diplomacy SET attackcountry2='NULL', attackcountry2start='2099-01-01 00:00:00' WHERE id='$id[$key]'";
		mysqli_query($mysqli, $sql);
	}
	
	echo nl2br ("<div class=\"h1\">War between $country1[$key] and $country2[$key]</div>");
		
		//doe dingen in country2 aanvallen in nederland, verdedigen nederland // attacker eerst dan defender
		if($attackcountry2[$key] != 'NULL' AND $attackcountry2[$key] != NULL){
			echo "Attack on $attackcountry2[$key] | $country2[$key] damage: $country21damage[$key] | $country1[$key] damage: $country22damage[$key] | $country1[$key] pays: total gold $paygold1[$key], payment per 100 damage $goldperdamage1[$key] | $country2[$key] pays: total gold $paygold2[$key], payment per 100 damage $goldperdamage2[$key], Hospital energy $country1[$key] $hospital1[$key], Hospital energy $country2[$key] $hospital2[$key]";
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
			    document.getElementById("demo").innerHTML = days + "d " + hours + "h "
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
				        label: '<?php echo $country2[$key] ?>',
				        data: [<?php echo $country21damage[$key] ?>],
				        backgroundColor: '#FF0000',
				      },
				      {
				        label: '<?php echo $country1[$key] ?>',
				        data: [<?php echo $country22damage[$key] ?>],
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
				<input type="hidden" name="attackcountry2" value="<?php echo "$attackcountry2[$key]"; ?>" />
				<input type="hidden" name="attackcountry1" value="<?php echo "$attackcountry1[$key]"; ?>" />
				<input type="hidden" name="attackcountry2start" value="<?php echo "$attackcountry2start[$key]"; ?>" />
				<input type="hidden" name="country21damage" value="<?php echo "$attackcountry21damage[$key]"; ?>" />
				<input type="hidden" name="country22damage" value="<?php echo "$attackcountry22damage[$key]"; ?>" />
				<select name="type" type="text">
					<option value="no weapon">no weapon</option>
  					<option value="weapon q1">weapon q1</option>
  					<option value="weapon q2">weapon q2</option>
  					<option value="weapon q3">weapon q3</option>
  					<option value="weapon q4">weapon q4</option>
  					<option value="weapon q5">weapon q5</option>
   				</select>
				<button type="submit" name="attack2" value="<?php echo "1"; ?>" /><?php echo "Fight for $country2[$key]";//attack ?></button>
				<button type="submit" name="defend2" value="<?php echo "1"; ?>" /><?php echo "Fight for $country1[$key]";//defend ?></button>
			</form>
			<?php
		}
	
	echo nl2br(" \n");
		//doe dingen in country1 aanvallen in nederland verdedigen belgie
		if($attackcountry1[$key] != 'NULL' AND $attackcountry1[$key] != NULL){
			echo "Attack on $attackcountry1[$key] | $country1[$key] damage: $country11damage[$key] | $country2[$key] damage: $country12damage[$key] | $country1[$key] pays: total gold $paygold1[$key], payment per 100 damage $goldperdamage1[$key] | $country2[$key] pays: total gold $paygold2[$key], payment per 100 damage $goldperdamage2[$key], Hospital energy $country1[$key] $hospital1[$key], Hospital energy $country2[$key] $hospital2[$key]";
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
			    document.getElementById("demo").innerHTML = days + "d " + hours + "h "
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
				        label: '<?php echo $country1[$key] ?>',
				        data: [<?php echo $country11damage[$key] ?>],
				        backgroundColor: '#FF0000',
				      },
				      {
				        label: '<?php echo $country2[$key] ?>',
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
				<button type="submit" name="attack1" value="<?php echo "1"; ?>" /><?php echo "Fight for $country1[$key]";//attack ?></button>
				<button type="submit" name="defend1" value="<?php echo "1"; ?>" /><?php echo "Fight for $country2[$key]";//defend ?></button>
			</form>
			<?php
		}
	echo nl2br(" \n");
	?> </div> <?php
}

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
			}else{
				$paygold1=0;
			}
			
			$result = $mysqli->query("SELECT gold FROM currency WHERE usercur='$username'") or die($mysqli->error());
			$row = mysqli_fetch_array($result);
			$usergold=$row['gold'];
													
			if($paygold1 > 0){
				$result = $mysqli->query("SELECT gold FROM currency WHERE usercur='$owner'") or die($mysqli->error());
				$row = mysqli_fetch_array($result);
				$ownergold=$row['gold'];
				
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
			}else{
				$paygold1=0;
			}
				
			$result = $mysqli->query("SELECT gold FROM currency WHERE usercur='$username'") or die($mysqli->error());
			$row = mysqli_fetch_array($result);
			$usergold=$row['gold'];
										
			if($paygold1 > 0){
				$result = $mysqli->query("SELECT gold FROM currency WHERE usercur='$owner'") or die($mysqli->error());
				$row = mysqli_fetch_array($result);
				$ownergold=$row['gold'];
				
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
			}else{
				$paygold1=0;
			}
			
			$result = $mysqli->query("SELECT gold FROM currency WHERE usercur='$username'") or die($mysqli->error());
			$row = mysqli_fetch_array($result);
			$usergold=$row['gold'];
						
			if($paygold1 > 0){
				$result = $mysqli->query("SELECT gold FROM currency WHERE usercur='$owner'") or die($mysqli->error());
				$row = mysqli_fetch_array($result);
				$ownergold=$row['gold'];
				
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
			}else{
				$paygold1=0;
			}
			
			$result = $mysqli->query("SELECT gold FROM currency WHERE usercur='$username'") or die($mysqli->error());
			$row = mysqli_fetch_array($result);
			$usergold=$row['gold'];
										
			if($paygold1 > 0){
				$result = $mysqli->query("SELECT gold FROM currency WHERE usercur='$owner'") or die($mysqli->error());
				$row = mysqli_fetch_array($result);
				$ownergold=$row['gold'];
				
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
			}else{
				$paygold1=0;
			}
			
			$result = $mysqli->query("SELECT gold FROM currency WHERE usercur='$username'") or die($mysqli->error());
			$row = mysqli_fetch_array($result);
			$usergold=$row['gold'];
										
			if($paygold1 > 0){
				$result = $mysqli->query("SELECT gold FROM currency WHERE usercur='$owner'") or die($mysqli->error());
				$row = mysqli_fetch_array($result);
				$ownergold=$row['gold'];
				
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
			}else{
				$paygold1=0;
			}
			
			$result = $mysqli->query("SELECT gold FROM currency WHERE usercur='$username'") or die($mysqli->error());
			$row = mysqli_fetch_array($result);
			$usergold=$row['gold'];
										
			if($paygold1 > 0){
				$result = $mysqli->query("SELECT gold FROM currency WHERE usercur='$owner'") or die($mysqli->error());
				$row = mysqli_fetch_array($result);
				$ownergold=$row['gold'];
				
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
			}else{
				$paygold2=0;
			}
			
			$result = $mysqli->query("SELECT gold FROM currency WHERE usercur='$username'") or die($mysqli->error());
			$row = mysqli_fetch_array($result);
			$usergold=$row['gold'];
										
			if($paygold2 > 0){
				$result = $mysqli->query("SELECT gold FROM currency WHERE usercur='$owner'") or die($mysqli->error());
				$row = mysqli_fetch_array($result);
				$ownergold=$row['gold'];
				
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
			}else{
				$paygold2=0;
			}
			
			$result = $mysqli->query("SELECT gold FROM currency WHERE usercur='$username'") or die($mysqli->error());
			$row = mysqli_fetch_array($result);
			$usergold=$row['gold'];
										
			if($paygold2 > 0){
				$result = $mysqli->query("SELECT gold FROM currency WHERE usercur='$owner'") or die($mysqli->error());
				$row = mysqli_fetch_array($result);
				$ownergold=$row['gold'];
				
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
			}else{
				$paygold2=0;
			}
			
			$result = $mysqli->query("SELECT gold FROM currency WHERE usercur='$username'") or die($mysqli->error());
			$row = mysqli_fetch_array($result);
			$usergold=$row['gold'];
										
			if($paygold2 > 0){
				$result = $mysqli->query("SELECT gold FROM currency WHERE usercur='$owner'") or die($mysqli->error());
				$row = mysqli_fetch_array($result);
				$ownergold=$row['gold'];
				
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
			}else{
				$paygold2=0;
			}
			
			$result = $mysqli->query("SELECT gold FROM currency WHERE usercur='$username'") or die($mysqli->error());
			$row = mysqli_fetch_array($result);
			$usergold=$row['gold'];
										
			if($paygold2 > 0){
				$result = $mysqli->query("SELECT gold FROM currency WHERE usercur='$owner'") or die($mysqli->error());
				$row = mysqli_fetch_array($result);
				$ownergold=$row['gold'];
				
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
			}else{
				$paygold2=0;
			}
			
			$result = $mysqli->query("SELECT gold FROM currency WHERE usercur='$username'") or die($mysqli->error());
			$row = mysqli_fetch_array($result);
			$usergold=$row['gold'];
										
			if($paygold2 > 0){
				$result = $mysqli->query("SELECT gold FROM currency WHERE usercur='$owner'") or die($mysqli->error());
				$row = mysqli_fetch_array($result);
				$ownergold=$row['gold'];
				
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
			}else{
				$paygold2=0;
			}
			
			$result = $mysqli->query("SELECT gold FROM currency WHERE usercur='$username'") or die($mysqli->error());
			$row = mysqli_fetch_array($result);
			$usergold=$row['gold'];
										
			if($paygold2 > 0){
				$result = $mysqli->query("SELECT gold FROM currency WHERE usercur='$owner'") or die($mysqli->error());
				$row = mysqli_fetch_array($result);
				$ownergold=$row['gold'];
				
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

if(isset($_POST['attack2'])){
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
	$country21damage=$row['country21damage'];
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
			$country21damage=$country21damage+$strength+$militaryunitrankbonus;
			$alltimedamage=$alltimedamage+$strength+$militaryunitrankbonus;			
			$sql = "UPDATE diplomacy SET country21damage='$country21damage' WHERE id='$id'";
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
			}else{
				$paygold2=0;
			}
			
			$result = $mysqli->query("SELECT gold FROM currency WHERE usercur='$username'") or die($mysqli->error());
			$row = mysqli_fetch_array($result);
			$usergold=$row['gold'];
										
			if($paygold2 > 0){
				$result = $mysqli->query("SELECT gold FROM currency WHERE usercur='$owner'") or die($mysqli->error());
				$row = mysqli_fetch_array($result);
				$ownergold=$row['gold'];
				
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
			$country21damage=$country21damage+$strength+50+$militaryunitrankbonus;
			$alltimedamage=$alltimedamage+$strength+50+$militaryunitrankbonus;				
			$sql = "UPDATE diplomacy SET country21damage='$country21damage' WHERE id='$id'";
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
			}else{
				$paygold2=0;
			}
			
			$result = $mysqli->query("SELECT gold FROM currency WHERE usercur='$username'") or die($mysqli->error());
			$row = mysqli_fetch_array($result);
			$usergold=$row['gold'];
										
			if($paygold2 > 0){
				$result = $mysqli->query("SELECT gold FROM currency WHERE usercur='$owner'") or die($mysqli->error());
				$row = mysqli_fetch_array($result);
				$ownergold=$row['gold'];
				
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
			$country21damage=$country21damage+$strength+100+$militaryunitrankbonus;
			$alltimedamage=$alltimedamage+$strength+100+$militaryunitrankbonus;				
			$sql = "UPDATE diplomacy SET country21damage='$country21damage' WHERE id='$id'";
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
			}else{
				$paygold2=0;
			}
			
			$result = $mysqli->query("SELECT gold FROM currency WHERE usercur='$username'") or die($mysqli->error());
			$row = mysqli_fetch_array($result);
			$usergold=$row['gold'];
										
			if($paygold2 > 0){
				$result = $mysqli->query("SELECT gold FROM currency WHERE usercur='$owner'") or die($mysqli->error());
				$row = mysqli_fetch_array($result);
				$ownergold=$row['gold'];
				
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
			$country21damage=$country21damage+$strength+150+$militaryunitrankbonus;
			$alltimedamage=$alltimedamage+$strength+150+$militaryunitrankbonus;					
			$sql = "UPDATE diplomacy SET country21damage='$country21damage' WHERE id='$id'";
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
			}else{
				$paygold2=0;
			}
			
			$result = $mysqli->query("SELECT gold FROM currency WHERE usercur='$username'") or die($mysqli->error());
			$row = mysqli_fetch_array($result);
			$usergold=$row['gold'];
										
			if($paygold2 > 0){
				$result = $mysqli->query("SELECT gold FROM currency WHERE usercur='$owner'") or die($mysqli->error());
				$row = mysqli_fetch_array($result);
				$ownergold=$row['gold'];
				
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
			$country21damage=$country21damage+$strength+200+$militaryunitrankbonus;
			$alltimedamage=$alltimedamage+$strength+200+$militaryunitrankbonus;				
			$sql = "UPDATE diplomacy SET country21damage='$country21damage' WHERE id='$id'";
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
			}else{
				$paygold2=0;
			}
			
			$result = $mysqli->query("SELECT gold FROM currency WHERE usercur='$username'") or die($mysqli->error());
			$row = mysqli_fetch_array($result);
			$usergold=$row['gold'];
										
			if($paygold2 > 0){
				$result = $mysqli->query("SELECT gold FROM currency WHERE usercur='$owner'") or die($mysqli->error());
				$row = mysqli_fetch_array($result);
				$ownergold=$row['gold'];
				
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
			$country21damage=$country21damage+$strength+250+$militaryunitrankbonus;
			$alltimedamage=$alltimedamage+$strength+250+$militaryunitrankbonus;				
			$sql = "UPDATE diplomacy SET country21damage='$country21damage' WHERE id='$id'";
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
			}else{
				$paygold2=0;
			}
			
			$result = $mysqli->query("SELECT gold FROM currency WHERE usercur='$username'") or die($mysqli->error());
			$row = mysqli_fetch_array($result);
			$usergold=$row['gold'];
										
			if($paygold2 > 0){
				$result = $mysqli->query("SELECT gold FROM currency WHERE usercur='$owner'") or die($mysqli->error());
				$row = mysqli_fetch_array($result);
				$ownergold=$row['gold'];
				
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

if(isset($_POST['defend2'])){
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
	$country22damage=$row['country22damage'];
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
			$country22damage=$country22damage+$strength+$militaryunitrankbonus;
			$alltimedamage=$alltimedamage+$strength+$militaryunitrankbonus;				
			$sql = "UPDATE diplomacy SET country22damage='$country22damage' WHERE id='$id'";
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
			}else{
				$paygold1=0;
			}
			
			$result = $mysqli->query("SELECT gold FROM currency WHERE usercur='$username'") or die($mysqli->error());
			$row = mysqli_fetch_array($result);
			$usergold=$row['gold'];
										
			if($paygold1 > 0){
				$result = $mysqli->query("SELECT gold FROM currency WHERE usercur='$owner'") or die($mysqli->error());
				$row = mysqli_fetch_array($result);
				$ownergold=$row['gold'];
				
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
		}
	}elseif($type=='weapon q1'){
		$result = $mysqli->query("SELECT * FROM inventory WHERE userinv='$username'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$weapon=$row['weaponq1'];
		
		$weapon=$weapon-1;
		$energy=$energy-10;
		
		if($energy>=0 AND $weapon>=0){
			$country22damage=$country22damage+$strength+50+$militaryunitrankbonus;	
			$alltimedamage=$alltimedamage+$strength+50+$militaryunitrankbonus;				
			$sql = "UPDATE diplomacy SET country22damage='$country22damage' WHERE id='$id'";
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
			}else{
				$paygold1=0;
			}
			
			$result = $mysqli->query("SELECT gold FROM currency WHERE usercur='$username'") or die($mysqli->error());
			$row = mysqli_fetch_array($result);
			$usergold=$row['gold'];
										
			if($paygold1 > 0){
				$result = $mysqli->query("SELECT gold FROM currency WHERE usercur='$owner'") or die($mysqli->error());
				$row = mysqli_fetch_array($result);
				$ownergold=$row['gold'];
				
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
			$country22damage=$country22damage+$strength+100+$militaryunitrankbonus;
			$alltimedamage=$alltimedamage+$strength+100+$militaryunitrankbonus;			
			$sql = "UPDATE diplomacy SET country22damage='$country22damage' WHERE id='$id'";
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
			}else{
				$paygold1=0;
			}
			
			$result = $mysqli->query("SELECT gold FROM currency WHERE usercur='$username'") or die($mysqli->error());
			$row = mysqli_fetch_array($result);
			$usergold=$row['gold'];
										
			if($paygold1 > 0){
				$result = $mysqli->query("SELECT gold FROM currency WHERE usercur='$owner'") or die($mysqli->error());
				$row = mysqli_fetch_array($result);
				$ownergold=$row['gold'];
				
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
			$country22damage=$country22damage+$strength+150+$militaryunitrankbonus;
			$alltimedamage=$alltimedamage+$strength+150+$militaryunitrankbonus;			
			$sql = "UPDATE diplomacy SET country22damage='$country22damage' WHERE id='$id'";
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
			}else{
				$paygold1=0;
			}
			
			$result = $mysqli->query("SELECT gold FROM currency WHERE usercur='$username'") or die($mysqli->error());
			$row = mysqli_fetch_array($result);
			$usergold=$row['gold'];
										
			if($paygold1 > 0){
				$result = $mysqli->query("SELECT gold FROM currency WHERE usercur='$owner'") or die($mysqli->error());
				$row = mysqli_fetch_array($result);
				$ownergold=$row['gold'];
				
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
			$country22damage=$country22damage+$strength+200+$militaryunitrankbonus;
			$alltimedamage=$alltimedamage+$strength+200+$militaryunitrankbonus;			
			$sql = "UPDATE diplomacy SET country22damage='$country22damage' WHERE id='$id'";
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
			}else{
				$paygold1=0;
			}
			
			$result = $mysqli->query("SELECT gold FROM currency WHERE usercur='$username'") or die($mysqli->error());
			$row = mysqli_fetch_array($result);
			$usergold=$row['gold'];
										
			if($paygold1 > 0){
				$result = $mysqli->query("SELECT gold FROM currency WHERE usercur='$owner'") or die($mysqli->error());
				$row = mysqli_fetch_array($result);
				$ownergold=$row['gold'];
				
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
			$country22damage=$country22damage+$strength+250+$militaryunitrankbonus;
			$alltimedamage=$alltimedamage+$strength+250+$militaryunitrankbonus;	
			$sql = "UPDATE diplomacy SET country22damage='$country22damage' WHERE id='$id'";
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
			}else{
				$paygold1=0;
			}
			
			$result = $mysqli->query("SELECT gold FROM currency WHERE usercur='$username'") or die($mysqli->error());
			$row = mysqli_fetch_array($result);
			$usergold=$row['gold'];
										
			if($paygold1 > 0){
				$result = $mysqli->query("SELECT gold FROM currency WHERE usercur='$owner'") or die($mysqli->error());
				$row = mysqli_fetch_array($result);
				$ownergold=$row['gold'];
				
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

?>
</div>
</body>
<footer>
<?php require 'bottombar.php'; ?>
</footer>
</html>
