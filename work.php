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
  <link rel="stylesheet" href="css/styletot.css">
   <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
   <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
   <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
   <link rel="manifest" href="/site.webmanifest">
   <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#add8e6">
   <meta name="msapplication-TileColor" content="#add8e6">
   <meta name="theme-color" content="#ffffff">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  

  
</head>

<body>
<div class="boxedtot">
<?php
require 'ageing.php';
//get location & job info
$result = $mysqli->query("SELECT * FROM users WHERE username='$username'") or die($mysqli->error());
$row = mysqli_fetch_array($result);
$location = $row['location'];
$salary=$row['salary'];
$workid=$row['workid'];

?> <div class="textbox"> <?php
//Work
if($workid>0){
	?>
     <form method="post" action="">      
        <button type="submit" name="work" />Work</button>
        <button type="submit" name="resign" />Resign</button>
        <button type="submit" name="viewcompany" />View owned companies</button>
        <button type="submit" name="newcompany" />Create a new company</button>
     </form>
	<?php
}else{
	?>
     <form method="post" action="">      
        <button type="submit" name="viewcompany" />View owned companies</button>
        <button type="submit" name="newcompany" />Create a new company</button>
     </form>
	<?php
}
?> <hr /> <?php

//process work button
if(isset($_POST['work'])){
	$result = $mysqli->query("SELECT * FROM users WHERE username='$username'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$workid=$row['workid'];
	$worked=$row['worked'];
	$energy=$row['energy'];
	//$energy=$energy-10;
	
	if($sleepstate=="awake"){
		if($worked==0){
			//get company info
			$result = $mysqli->query("SELECT * FROM companies WHERE id='$workid'") or die($mysqli->error());
			$row = mysqli_fetch_array($result);
			$comptype=$row['type'];
			$compowner=$row['owner'];
			$compcountry=$row['countryco'];
			$companyname = $mysqli->escape_string($row['companyname']);
			$region = $mysqli->escape_string($row['region']);
			$cropid = $mysqli->escape_string($row['crop']);
			
			//check country of company		
			$result = $mysqli->query("SELECT curowner FROM region WHERE name='$region'") or die($mysqli->error());
			$row = mysqli_fetch_array($result);
			$curowner = $row['curowner'];
			
			if($compcountry != $curowner){ //verander land bedrijf als eigenaar van regio veranderd
				$sql = "UPDATE companies SET countryco='$curowner' WHERE owner='$compowner'";
				mysqli_query($mysqli, $sql);
				$compcountry = $curowner;
			}
			
			//select currency type of country of company
			$result = $mysqli->query("SELECT * FROM countryinfo WHERE country='$compcountry'") or die($mysqli->error());
			$row = mysqli_fetch_array($result);
			$jobcurrency=$row['currency'];
			
			//select currency of owner
			$result = $mysqli->query("SELECT $jobcurrency FROM currency WHERE usercur='$compowner'") or die($mysqli->error());
			$row = mysqli_fetch_assoc($result);
			$ownercurrency=$row[$jobcurrency];
			
			//check if owner has enough money then proceed
			$ownercurrency=$ownercurrency-$salary;
		
			if($ownercurrency>=0){
				$check=0;		
				if($comptype=='food raw'){
					$result = $mysqli->query("SELECT rawfood FROM inventory WHERE userinv='$compowner'") or die($mysqli->error());
					$row = mysqli_fetch_assoc($result);
					$rawfood=$row['rawfood'];
					
					//crop bonus
					$result = $mysqli->query("SELECT * FROM crops WHERE id='$cropid'") or die($mysqli->error());
					$row = mysqli_fetch_array($result);
					$tempneed=$row['temperatureneed'];
					$waterneed=$row['waterneed'];
					
					$result = $mysqli->query("SELECT * FROM region WHERE name='$region'") or die($mysqli->error());
					$row = mysqli_fetch_array($result);
					$weatherevent=$row['weatherevent'];
					$currtemp=$row['currtemp'];
					
					if($tempneed == "hot"){
						$production=-0.0889*($currtemp**2)+5.333*$currtemp+40;
					}elseif($tempneed == "normal"){
						$production=-0.4*($currtemp**2)+12*$currtemp+30;
					}elseif($tempneed == "cold"){
						$production=-0.05*($currtemp**2)+120;
					}
					
					if($waterneed == "dry"){
						if($weatherevent == "drought"){
							$production = $production+10;
						}elseif( $weatherevent == "flood"){
							$production = $production-10;
						}
					}elseif( $waterneed == "wet"){
						if($weatherevent == "flood"){
							$production = $production+10;
						}elseif( $weatherevent == "drought"){
							$production = $production-10;
						}
					}
					
					if($production < 80){
						$production = 80;
					}
					$rawfood=$rawfood+$production;
					
					$result = mysqli_query($mysqli,"SELECT id FROM region WHERE resource='farmland' AND curowner='$compcountry'");
					for ($set=array(); $row=$result->fetch_assoc(); $set[]=$row);
					$numberofresource=0;
					foreach ($set as $item) {
						$numberofresource=$numberofresource+1;
					}
					
					$rawfood=$rawfood+100*($numberofresource*0.05);
				
					$sql = "UPDATE inventory SET rawfood='$rawfood' WHERE userinv='$compowner'";
					mysqli_query($mysqli, $sql);
					
					//update worked & energy
					$sql = "UPDATE users SET worked='1', energy='$energy' WHERE username='$username'";
					mysqli_query($mysqli, $sql);
					
					$check=1;
				}elseif($comptype=='weapon raw'){
					$result = $mysqli->query("SELECT rawweapon FROM inventory WHERE userinv='$compowner'") or die($mysqli->error());
					$row = mysqli_fetch_assoc($result);
					$rawweapon=$row['rawweapon'];
				
					$rawweapon=$rawweapon+100;
					
					$result = mysqli_query($mysqli,"SELECT id FROM region WHERE resource='iron' AND curowner='$compcountry'");
					for ($set=array(); $row=$result->fetch_assoc(); $set[]=$row);
					$numberofresource=0;
					foreach ($set as $item) {
						$numberofresource=$numberofresource+1;
					}
					
					$rawweapon=$rawweapon+100*($numberofresource*0.05);
				
					$sql = "UPDATE inventory SET rawweapon='$rawweapon' WHERE userinv='$compowner'";
					mysqli_query($mysqli, $sql);
					
					//update worked & energy
					$sql = "UPDATE users SET worked='1', energy='$energy' WHERE username='$username'";
					mysqli_query($mysqli, $sql);
					
					$check=1;
				}elseif($comptype=='house raw'){
					$result = $mysqli->query("SELECT rawhouse FROM inventory WHERE userinv='$compowner'") or die($mysqli->error());
					$row = mysqli_fetch_assoc($result);
					$rawhouse=$row['rawhouse'];
				
					$rawhouse=$rawhouse+100;
					
					$result = mysqli_query($mysqli,"SELECT id FROM region WHERE resource='stone' AND curowner='$compcountry'");
					for ($set=array(); $row=$result->fetch_assoc(); $set[]=$row);
					$numberofresource=0;
					foreach ($set as $item) {
						$numberofresource=$numberofresource+1;
					}
					
					$rawhouse=$rawhouse+100*($numberofresource*0.05);
				
					$sql = "UPDATE inventory SET rawhouse='$rawhouse' WHERE userinv='$compowner'";
					mysqli_query($mysqli, $sql);
					
					//update worked & energy
					$sql = "UPDATE users SET worked='1', energy='$energy' WHERE username='$username'";
					mysqli_query($mysqli, $sql);
					
					$check=1;
				}elseif($comptype=='paper'){
					$result = $mysqli->query("SELECT paper FROM inventory WHERE userinv='$compowner'") or die($mysqli->error());
					$row = mysqli_fetch_assoc($result);
					$paper=$row['paper'];
				
					$paper=$paper+100;
					
					$result = mysqli_query($mysqli,"SELECT id FROM region WHERE resource='papyrus' AND curowner='$compcountry'");
					for ($set=array(); $row=$result->fetch_assoc(); $set[]=$row);
					$numberofresource=0;
					foreach ($set as $item) {
						$numberofresource=$numberofresource+1;
					}
					
					$paper=$paper+100*($numberofresource*0.05);
				
					$sql = "UPDATE inventory SET paper='$paper' WHERE userinv='$compowner'";
					mysqli_query($mysqli, $sql);
					
					//update worked & energy
					$sql = "UPDATE users SET worked='1', energy='$energy' WHERE username='$username'";
					mysqli_query($mysqli, $sql);
					
					$check=1;
				}elseif($comptype=='hospital raw'){
					$result = $mysqli->query("SELECT rawhospital FROM inventory WHERE userinv='$compowner'") or die($mysqli->error());
					$row = mysqli_fetch_assoc($result);
					$rawhospital=$row['rawhospital'];
				
					$rawhospital=$rawhospital+100;
					
					$result = mysqli_query($mysqli,"SELECT id FROM region WHERE resource='primeval forest' AND curowner='$compcountry'");
					for ($set=array(); $row=$result->fetch_assoc(); $set[]=$row);
					$numberofresource=0;
					foreach ($set as $item) {
						$numberofresource=$numberofresource+1;
					}
					
					$rawhospital=$rawhospital+100*($numberofresource*0.05);
				
					$sql = "UPDATE inventory SET rawhospital='$rawhospital' WHERE userinv='$compowner'";
					mysqli_query($mysqli, $sql);
					
					//update worked & energy
					$sql = "UPDATE users SET worked='1', energy='$energy' WHERE username='$username'";
					mysqli_query($mysqli, $sql);
					
					$check=1;
				}elseif($comptype=='weapon q1'){
					
					$result = $mysqli->query("SELECT rawweapon FROM inventory WHERE userinv='$compowner'") or die($mysqli->error());
					$row = mysqli_fetch_assoc($result);
					$rawweapon=$row['rawweapon'];
				
					$rawweapon=$rawweapon-100;
					
					if($rawweapon>=0){
				
						$sql = "UPDATE inventory SET rawweapon='$rawweapon' WHERE userinv='$compowner'";
						mysqli_query($mysqli, $sql);
					
						$result = $mysqli->query("SELECT weaponq1 FROM inventory WHERE userinv='$compowner'") or die($mysqli->error());
						$row = mysqli_fetch_assoc($result);
						$weaponq1=$row['weaponq1'];
				
						$weaponq1=$weaponq1+5;
				
						$sql = "UPDATE inventory SET weaponq1='$weaponq1' WHERE userinv='$compowner'";
						mysqli_query($mysqli, $sql);
						
						//update worked & energy
						$sql = "UPDATE users SET worked='1', energy='$energy' WHERE username='$username'";
						mysqli_query($mysqli, $sql);
						
						$check=1;
					}else{
						echo'<div class="boxed">There are not enough raw materials available to work!</div>';
						
						$content= "$username tried to work in $companyname but there were not enough raw materials available!";
						$sql = "INSERT INTO events (date, content, extrainfo) " 
					     . "VALUES (NOW(),'$content','$compowner')";
						mysqli_query($mysqli2, $sql);
					}
				}elseif($comptype=='weapon q2'){
					
					$result = $mysqli->query("SELECT rawweapon FROM inventory WHERE userinv='$compowner'") or die($mysqli->error());
					$row = mysqli_fetch_assoc($result);
					$rawweapon=$row['rawweapon'];
				
					$rawweapon=$rawweapon-200;
					
					if($rawweapon>=0){
				
						$sql = "UPDATE inventory SET rawweapon='$rawweapon' WHERE userinv='$compowner'";
						mysqli_query($mysqli, $sql);
					
						$result = $mysqli->query("SELECT weaponq2 FROM inventory WHERE userinv='$compowner'") or die($mysqli->error());
						$row = mysqli_fetch_assoc($result);
						$weaponq2=$row['weaponq2'];
				
						$weaponq2=$weaponq2+5;
			
						$sql = "UPDATE inventory SET weaponq2='$weaponq2' WHERE userinv='$compowner'";
						mysqli_query($mysqli, $sql);
						
						//update worked & energy
						$sql = "UPDATE users SET worked='1', energy='$energy' WHERE username='$username'";
						mysqli_query($mysqli, $sql);
						
						$check=1;
					}else{
						echo'<div class="boxed">There are not enough raw materials available to work!</div>';
						
						$content= "$username tried to work in $companyname but there were not enough raw materials available!";
						$sql = "INSERT INTO events (date, content, extrainfo) " 
					     . "VALUES (NOW(),'$content','$compowner')";
						mysqli_query($mysqli2, $sql);
					}
				}elseif($comptype=='weapon q3'){
					
					$result = $mysqli->query("SELECT rawweapon FROM inventory WHERE userinv='$compowner'") or die($mysqli->error());
					$row = mysqli_fetch_assoc($result);
					$rawweapon=$row['rawweapon'];
				
					$rawweapon=$rawweapon-300;
					
					if($rawweapon>=0){
				
						$sql = "UPDATE inventory SET rawweapon='$rawweapon' WHERE userinv='$compowner'";
						mysqli_query($mysqli, $sql);
					
						$result = $mysqli->query("SELECT weaponq3 FROM inventory WHERE userinv='$compowner'") or die($mysqli->error());
						$row = mysqli_fetch_assoc($result);
						$weaponq3=$row['weaponq3'];
				
						$weaponq3=$weaponq3+5;
				
						$sql = "UPDATE inventory SET weaponq3='$weaponq3' WHERE userinv='$compowner'";
						mysqli_query($mysqli, $sql);
						
						//update worked & energy
						$sql = "UPDATE users SET worked='1', energy='$energy' WHERE username='$username'";
						mysqli_query($mysqli, $sql);
						
						$check=1;
					}else{
						echo'<div class="boxed">There are not enough raw materials available to work!</div>';
						
						$content= "$username tried to work in $companyname but there were not enough raw materials available!";
						$sql = "INSERT INTO events (date, content, extrainfo) " 
					     . "VALUES (NOW(),'$content','$compowner')";
						mysqli_query($mysqli2, $sql);
					}
				}elseif($comptype=='weapon q4'){
					
					$result = $mysqli->query("SELECT rawweapon FROM inventory WHERE userinv='$compowner'") or die($mysqli->error());
					$row = mysqli_fetch_assoc($result);
					$rawweapon=$row['rawweapon'];
				
					$rawweapon=$rawweapon-400;
					
					if($rawweapon>=0){
				
						$sql = "UPDATE inventory SET rawweapon='$rawweapon' WHERE userinv='$compowner'";
						mysqli_query($mysqli, $sql);
					
						$result = $mysqli->query("SELECT weaponq4 FROM inventory WHERE userinv='$compowner'") or die($mysqli->error());
						$row = mysqli_fetch_assoc($result);
						$weaponq4=$row['weaponq4'];
				
						$weaponq1=$weaponq1+5;
				
						$sql = "UPDATE inventory SET weaponq4='$weaponq4' WHERE userinv='$compowner'";
						mysqli_query($mysqli, $sql);
						
						//update worked & energy
						$sql = "UPDATE users SET worked='1', energy='$energy' WHERE username='$username'";
						mysqli_query($mysqli, $sql);
						
						$check=1;
					}else{
						echo'<div class="boxed">There are not enough raw materials available to work!</div>';
						
						$content= "$username tried to work in $companyname but there were not enough raw materials available!";
						$sql = "INSERT INTO events (date, content, extrainfo) " 
					     . "VALUES (NOW(),'$content','$compowner')";
						mysqli_query($mysqli2, $sql);
					}
				}elseif($comptype=='weapon q5'){
					
					$result = $mysqli->query("SELECT rawweapon FROM inventory WHERE userinv='$compowner'") or die($mysqli->error());
					$row = mysqli_fetch_assoc($result);
					$rawweapon=$row['rawweapon'];
				
					$rawweapon=$rawweapon-500;
					
					if($rawweapon>=0){
				
						$sql = "UPDATE inventory SET rawweapon='$rawweapon' WHERE userinv='$compowner'";
						mysqli_query($mysqli, $sql);
					
						$result = $mysqli->query("SELECT weaponq5 FROM inventory WHERE userinv='$compowner'") or die($mysqli->error());
						$row = mysqli_fetch_assoc($result);
						$weaponq1=$row['weaponq5'];
				
						$weaponq5=$weaponq1+5;
				
						$sql = "UPDATE inventory SET weaponq5='$weaponq5' WHERE userinv='$compowner'";
						mysqli_query($mysqli, $sql);
						
						//update worked & energy
						$sql = "UPDATE users SET worked='1', energy='$energy' WHERE username='$username'";
						mysqli_query($mysqli, $sql);
						
						$check=1;
					}else{
						echo'<div class="boxed">There are not enough raw materials available to work!</div>';
						
						
						$content= "$username tried to work in $companyname but there were not enough raw materials available!";
						$sql = "INSERT INTO events (date, content, extrainfo) " 
					     . "VALUES (NOW(),'$content','$compowner')";
						mysqli_query($mysqli2, $sql);
					}
				}elseif($comptype=='food q1'){
					
					$result = $mysqli->query("SELECT rawfood FROM inventory WHERE userinv='$compowner'") or die($mysqli->error());
					$row = mysqli_fetch_assoc($result);
					$rawfood=$row['rawfood'];
				
					$rawfood=$rawfood-200;
					
					if($rawfood>=0){
				
						$sql = "UPDATE inventory SET rawfood='$rawfood' WHERE userinv='$compowner'";
						mysqli_query($mysqli, $sql);
					
						$result = $mysqli->query("SELECT foodq1 FROM inventory WHERE userinv='$compowner'") or die($mysqli->error());
						$row = mysqli_fetch_assoc($result);
						$foodq1=$row['foodq1'];
				
						$foodq1=$foodq1+20;
				
						$sql = "UPDATE inventory SET foodq1='$foodq1' WHERE userinv='$compowner'";
						mysqli_query($mysqli, $sql);
						
						//update worked & energy
						$sql = "UPDATE users SET worked='1', energy='$energy' WHERE username='$username'";
						mysqli_query($mysqli, $sql);
						
						$check=1;
					}else{
						echo'<div class="boxed">There are not enough raw materials available to work!</div>';
						
						$content= "$username tried to work in $companyname but there were not enough raw materials available!";
						$sql = "INSERT INTO events (date, content, extrainfo) " 
					     . "VALUES (NOW(),'$content','$compowner')";
						mysqli_query($mysqli2, $sql);
					}
				}elseif($comptype=='food q2'){
					
					$result = $mysqli->query("SELECT rawfood FROM inventory WHERE userinv='$compowner'") or die($mysqli->error());
					$row = mysqli_fetch_assoc($result);
					$rawfood=$row['rawfood'];
				
					$rawfood=$rawfood-400;
					
					if($rawfood>=0){
				
						$sql = "UPDATE inventory SET rawfood='$rawfood' WHERE userinv='$compowner'";
						mysqli_query($mysqli, $sql);
					
						$result = $mysqli->query("SELECT foodq2 FROM inventory WHERE userinv='$compowner'") or die($mysqli->error());
						$row = mysqli_fetch_assoc($result);
						$foodq2=$row['foodq2'];
				
						$foodq2=$foodq2+20;
				
						$sql = "UPDATE inventory SET foodq2='$foodq2' WHERE userinv='$compowner'";
						mysqli_query($mysqli, $sql);
						
						//update worked & energy
						$sql = "UPDATE users SET worked='1', energy='$energy' WHERE username='$username'";
						mysqli_query($mysqli, $sql);
						
						$check=1;
					}else{
						echo'<div class="boxed">There are not enough raw materials available to work!</div>';
						
						$content= "$username tried to work in $companyname but there were not enough raw materials available!";
						$sql = "INSERT INTO events (date, content, extrainfo) " 
					     . "VALUES (NOW(),'$content','$compowner')";
						mysqli_query($mysqli2, $sql);
					}
				}elseif($comptype=='food q3'){
					
					$result = $mysqli->query("SELECT rawfood FROM inventory WHERE userinv='$compowner'") or die($mysqli->error());
					$row = mysqli_fetch_assoc($result);
					$rawfood=$row['rawfood'];
				
					$rawfood=$rawfood-600;
					
					if($rawfood>=0){
				
						$sql = "UPDATE inventory SET rawfood='$rawfood' WHERE userinv='$compowner'";
						mysqli_query($mysqli, $sql);
					
						$result = $mysqli->query("SELECT foodq3 FROM inventory WHERE userinv='$compowner'") or die($mysqli->error());
						$row = mysqli_fetch_assoc($result);
						$foodq3=$row['foodq3'];
				
						$foodq3=$foodq3+20;
				
						$sql = "UPDATE inventory SET foodq3='$foodq3' WHERE userinv='$compowner'";
						mysqli_query($mysqli, $sql);
						
						//update worked & energy
						$sql = "UPDATE users SET worked='1', energy='$energy' WHERE username='$username'";
						mysqli_query($mysqli, $sql);
						
						$check=1;
					}else{
						echo'<div class="boxed">There are not enough raw materials available to work!</div>';
						
						$content= "$username tried to work in $companyname but there were not enough raw materials available!";
						$sql = "INSERT INTO events (date, content, extrainfo) " 
					     . "VALUES (NOW(),'$content','$compowner')";
						mysqli_query($mysqli2, $sql);
					}
				}elseif($comptype=='food q4'){
					
					$result = $mysqli->query("SELECT rawfood FROM inventory WHERE userinv='$compowner'") or die($mysqli->error());
					$row = mysqli_fetch_assoc($result);
					$rawfood=$row['rawfood'];
				
					$rawfood=$rawfood-800;
					
					if($rawfood>=0){
				
						$sql = "UPDATE inventory SET rawfood='$rawfood' WHERE userinv='$compowner'";
						mysqli_query($mysqli, $sql);
					
						$result = $mysqli->query("SELECT foodq4 FROM inventory WHERE userinv='$compowner'") or die($mysqli->error());
						$row = mysqli_fetch_assoc($result);
						$foodq4=$row['foodq4'];
				
						$foodq4=$foodq4+20;
				
						$sql = "UPDATE inventory SET foodq4='$foodq4' WHERE userinv='$compowner'";
						mysqli_query($mysqli, $sql);
						
						//update worked & energy
						$sql = "UPDATE users SET worked='1', energy='$energy' WHERE username='$username'";
						mysqli_query($mysqli, $sql);
						
						$check=1;
					}else{
						echo'<div class="boxed">There are not enough raw materials available to work!</div>';
						
						$content= "$username tried to work in $companyname but there were not enough raw materials available!";
						$sql = "INSERT INTO events (date, content, extrainfo) " 
					     . "VALUES (NOW(),'$content','$compowner')";
						mysqli_query($mysqli2, $sql);
					}
				}elseif($comptype=='food q5'){
					
					$result = $mysqli->query("SELECT rawfood FROM inventory WHERE userinv='$compowner'") or die($mysqli->error());
					$row = mysqli_fetch_assoc($result);
					$rawfood=$row['rawfood'];
				
					$rawfood=$rawfood-1000;
					
					if($rawfood>=0){
				
						$sql = "UPDATE inventory SET rawfood='$rawfood' WHERE userinv='$compowner'";
						mysqli_query($mysqli, $sql);
					
						$result = $mysqli->query("SELECT foodq5 FROM inventory WHERE userinv='$compowner'") or die($mysqli->error());
						$row = mysqli_fetch_assoc($result);
						$foodq5=$row['foodq5'];
				
						$foodq5=$foodq5+20;
				
						$sql = "UPDATE inventory SET foodq5='$foodq5' WHERE userinv='$compowner'";
						mysqli_query($mysqli, $sql);
						
						//update worked & energy
						$sql = "UPDATE users SET worked='1', energy='$energy' WHERE username='$username'";
						mysqli_query($mysqli, $sql);
						
						$check=1;
					}else{
						echo'<div class="boxed">There are not enough raw materials available to work!</div>';
						
						$content= "$username tried to work in $companyname but there were not enough raw materials available!";
						$sql = "INSERT INTO events (date, content, extrainfo) " 
					     . "VALUES (NOW(),'$content','$compowner')";
						mysqli_query($mysqli2, $sql);
					}
				}elseif($comptype=='houses'){
					
					$result = $mysqli->query("SELECT rawhouse FROM inventory WHERE userinv='$compowner'") or die($mysqli->error());
					$row = mysqli_fetch_assoc($result);
					$rawhouse=$row['rawhouse'];
				
					$rawhouse=$rawhouse-1000;
					
					if($rawhouse>=0){
				
						$sql = "UPDATE inventory SET rawhouse='$rawhouse' WHERE userinv='$compowner'";
						mysqli_query($mysqli, $sql);
					
						$result = $mysqli->query("SELECT house FROM inventory WHERE userinv='$compowner'") or die($mysqli->error());
						$row = mysqli_fetch_assoc($result);
						$house=$row['house'];
				
						$house=$house+1;
				
						$sql = "UPDATE inventory SET house='$house' WHERE userinv='$compowner'";
						mysqli_query($mysqli, $sql);
						
						//update worked & energy
						$sql = "UPDATE users SET worked='1', energy='$energy' WHERE username='$username'";
						mysqli_query($mysqli, $sql);
						
						$check=1;
					}else{
						echo'<div class="boxed">There are not enough raw materials available to work!</div>';
						
						$content= "$username tried to work in $companyname but there were not enough raw materials available!";
						$sql = "INSERT INTO events (date, content, extrainfo) " 
					     . "VALUES (NOW(),'$content','$compowner')";
						mysqli_query($mysqli2, $sql);
					}
				}elseif($comptype=='book'){
					
					$result = $mysqli->query("SELECT paper FROM inventory WHERE userinv='$compowner'") or die($mysqli->error());
					$row = mysqli_fetch_assoc($result);
					$paper=$row['paper'];
				
					$paper=$paper-200;
					
					if($paper>=0){
				
						$sql = "UPDATE inventory SET paper='$paper' WHERE userinv='$compowner'";
						mysqli_query($mysqli, $sql);
					
						$result = $mysqli->query("SELECT book FROM inventory WHERE userinv='$compowner'") or die($mysqli->error());
						$row = mysqli_fetch_assoc($result);
						$book=$row['book'];
				
						$book=$book+10;
				
						$sql = "UPDATE inventory SET book='$book' WHERE userinv='$compowner'";
						mysqli_query($mysqli, $sql);
						
						//update worked & energy
						$sql = "UPDATE users SET worked='1', energy='$energy' WHERE username='$username'";
						mysqli_query($mysqli, $sql);
						
						$check=1;
					}else{
						echo'<div class="boxed">There are not enough raw materials available to work!</div>';
						
						$content= "$username tried to work in $companyname but there were not enough raw materials available!";
						$sql = "INSERT INTO events (date, content, extrainfo) " 
					     . "VALUES (NOW(),'$content','$compowner')";
						mysqli_query($mysqli2, $sql);
					}
				}elseif($comptype=='hospital'){
					
					$result = $mysqli->query("SELECT rawhospital FROM inventory WHERE userinv='$compowner'") or die($mysqli->error());
					$row = mysqli_fetch_assoc($result);
					$rawhospital=$row['rawhospital'];
				
					$rawhospital=$rawhospital-5000;
					
					if($rawhospital>=0){
				
						$sql = "UPDATE inventory SET rawhospital='$rawhospital' WHERE userinv='$compowner'";
						mysqli_query($mysqli, $sql);
					
						$result = $mysqli->query("SELECT hospital FROM inventory WHERE userinv='$compowner'") or die($mysqli->error());
						$row = mysqli_fetch_assoc($result);
						$hospital=$row['hospital'];
				
						$hospital=$hospital+1;
				
						$sql = "UPDATE inventory SET hospital='$hospital' WHERE userinv='$compowner'";
						mysqli_query($mysqli, $sql);
						
						//update worked & energy
						$sql = "UPDATE users SET worked='1', energy='$energy' WHERE username='$username'";
						mysqli_query($mysqli, $sql);
						
						$check=1;
					}else{
						echo'<div class="boxed">There are not enough raw materials available to work!</div>';
						
						$content= "$username tried to work in $companyname but there were not enough raw materials available!";
						$sql = "INSERT INTO events (date, content, extrainfo) " 
					     . "VALUES (NOW(),'$content','$compowner')";
						mysqli_query($mysqli2, $sql);
					}
				}elseif($comptype=='tavern'){
					
					$result = $mysqli->query("SELECT rooms FROM companies WHERE id='$workid'") or die($mysqli->error());
					$row = mysqli_fetch_array($result);
					$rooms=$row['rooms'];
					
					$rooms=$rooms+5;
					
					$sql = "UPDATE companies SET rooms='$rooms' WHERE id='$workid'";
					mysqli_query($mysqli, $sql);
					
					//update worked & energy
					$sql = "UPDATE users SET worked='1', energy='$energy' WHERE username='$username'";
					mysqli_query($mysqli, $sql);
					
					$check=1;
				}
				
				if($check==1){
					//update owner
					$sql = "UPDATE currency SET $jobcurrency='$ownercurrency' WHERE usercur='$compowner'";
					mysqli_query($mysqli, $sql);
		
					//select currency of worker
					$result = $mysqli->query("SELECT $jobcurrency FROM currency WHERE usercur='$username'") or die($mysqli->error());
					$row = mysqli_fetch_assoc($result);
					$workercurrency=$row[$jobcurrency];
					
					//get money & tax of country info
					$result = $mysqli->query("SELECT * FROM countryinfo WHERE country='$compcountry'") or die($mysqli->error());
					$row = mysqli_fetch_array($result);
					$tax=$row['worktax'];
					$countrymoney=$row['money'];
		
					$tax=($tax/100)*$salary;
					$countrymoney=$countrymoney+$tax;
					
					//update country money
					$sql = "UPDATE countryinfo SET money='$countrymoney' WHERE country='$compcountry'";
					mysqli_query($mysqli, $sql);
					
					//select region tax statistic
					$result = $mysqli->query("SELECT taxtoday FROM region WHERE name='$region'") or die($mysqli->error());
					$row = mysqli_fetch_array($result);
					$taxtoday=$row['taxtoday'];
					
					$taxtoday=$taxtoday+$tax;
					
					$sql = "UPDATE region SET taxtoday='$taxtoday' WHERE name='$region'";
					mysqli_query($mysqli, $sql);
					
					//calculate workersalary & update
					$workercurrency=$workercurrency+$salary-$tax;
					$sql = "UPDATE currency SET $jobcurrency='$workercurrency' WHERE usercur='$username'";
					mysqli_query($mysqli, $sql);
					
					echo nl2br ("<div class=\"boxed\">You worked and earned $salary!</div>");
				}
				
			}else{
				echo'<div class="boxed">The company owner doesn\'t have enough money to pay you!</div>';
				
				$content= "$username tried to work in $companyname but there was not enough $jobcurrency available!";
				$sql = "INSERT INTO events (date, content, extrainfo) " 
			     . "VALUES (NOW(),'$content','$compowner')";
				mysqli_query($mysqli2, $sql);
			}
			
		}else{
			echo'<div class="boxed">You have already worked today!</div>';
		}
	}else{
		//echo'<div class="boxed">You don\'t have enough energy to work!</div>';
		echo'<div class="boxed">You need to be awake to perform this action!</div>';
	}
	?>
	<script>
	    if ( window.history.replaceState ) {
	        window.history.replaceState( null, null, window.location.href );
	    }
	</script>
	<?php
}

//resign
if(isset($_POST['resign'])){
	$result = $mysqli->query("SELECT * FROM users WHERE username='$username'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$workid=$row['workid'];
	
	$result = $mysqli->query("SELECT * FROM companies WHERE id='$workid'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$pos1=$row['position1'];
	$pos2=$row['position2'];
	$pos3=$row['position3'];
	$pos4=$row['position4'];
	$pos5=$row['position5'];
	
	if($pos1==$username){
		$sql = "UPDATE users SET salary='0', workid='0' WHERE username='$username'";
		mysqli_query($mysqli, $sql);
		
		$sql = "UPDATE companies SET position1='free' WHERE id='$workid'";
		mysqli_query($mysqli, $sql);
		
		echo'<div class="boxed">You have resigned!</div>';
	}elseif($pos2==$username){
		$sql = "UPDATE users SET salary='0', workid='0' WHERE username='$username'";
		mysqli_query($mysqli, $sql);
		
		$sql = "UPDATE companies SET position2='free' WHERE id='$workid'";
		mysqli_query($mysqli, $sql);
		
		echo'<div class="boxed">You have resigned!</div>';
	}elseif($pos3==$username){
		$sql = "UPDATE users SET salary='0', workid='0' WHERE username='$username'";
		mysqli_query($mysqli, $sql);
		
		$sql = "UPDATE companies SET position3='free' WHERE id='$workid'";
		mysqli_query($mysqli, $sql);
		
		echo'<div class="boxed">You have resigned!</div>';
	}elseif($pos4==$username){
		$sql = "UPDATE users SET salary='0', workid='0' WHERE username='$username'";
		mysqli_query($mysqli, $sql);
		
		$sql = "UPDATE companies SET position4='free' WHERE id='$workid'";
		mysqli_query($mysqli, $sql);
		
		echo'<div class="boxed">You have resigned!</div>';
	}elseif($pos5==$username){
		$sql = "UPDATE users SET salary='0', workid='0' WHERE username='$username'";
		mysqli_query($mysqli, $sql);
		
		$sql = "UPDATE companies SET position5='free' WHERE id='$workid'";
		mysqli_query($mysqli, $sql);
		
		echo'<div class="boxed">You have resigned!</div>';
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
//Company information and hire workers
//get company info
$result = $mysqli->query("SELECT * FROM companies WHERE owner='$username'") or die($mysqli->error());
$columnValues = Array();
while ( $row = mysqli_fetch_assoc($result) ) {
  $columnValues[] = $row['id'];
}

//make form for your companies
if(isset($_POST['viewcompany'])){
	?>
	<form method="post" action="">
	    <select name="selectcompany" type="text">
	    <option selected="selected">Choose one</option>
	    <?php       
	    // Iterating through the product array
	    foreach($columnValues as $item){
	    ?>
	    <option value="<?php echo strtolower($item); ?>"><?php echo $item; ?></option>
	    <?php
	    }
	    ?>
	    </select> 
	    <button type="submit" name="selectcompanyform" />Select company</button>
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

//show company info
if(isset($_POST['selectcompanyform'])){
	$selectcompany = $mysqli->escape_string($_POST['selectcompany']);
	//echo "$selectcompany";
	$result2 = mysqli_query($mysqli,"SELECT * FROM companies WHERE id='$selectcompany' AND owner='$username'");
	$row2=mysqli_fetch_array($result2);
	$id=$row2["id"];
	$_SESSION['id'] = $row2["id"];
	$companyname=$row2["companyname"];
	$type=$row2["type"];
	$countryco=$row2["countryco"];
	$position1=$row2["position1"];
	$position2=$row2["position2"];
	$position3=$row2["position3"];
	$position4=$row2["position4"];
	$position5=$row2["position5"];
	$joboffer=$row2["joboffer"];
	$region=$row2["region"];
	$cropid=$row2["crop"];
	
	$result3 = mysqli_query($mysqli,"SELECT name FROM crops WHERE id='$cropid'");
	$row3=mysqli_fetch_array($result3);
	$cropname=$row3["name"];
	
	if($position1 != 'free'){
		$result3 = mysqli_query($mysqli,"SELECT salary, workedlastday FROM users WHERE username='$position1'");
		$row3=mysqli_fetch_array($result3);
		$salary1=$row3["salary"];
		$workedlastday1=$row3["workedlastday"];
	}else{
		$salary1=0;
		$workedlastday1="No";
	}
	if($position2 != 'free'){
		$result3 = mysqli_query($mysqli,"SELECT salary, workedlastday FROM users WHERE username='$position2'");
		$row3=mysqli_fetch_array($result3);
		$salary2=$row3["salary"];
		$workedlastday2=$row3["workedlastday"];
	}else{
		$salary2=0;
		$workedlastday2="No";
	}
	if($position3 != 'free'){
		$result3 = mysqli_query($mysqli,"SELECT salary, workedlastday FROM users WHERE username='$position3'");
		$row3=mysqli_fetch_array($result3);
		$salary3=$row3["salary"];
		$workedlastday3=$row3["workedlastday"];
	}else{
		$salary3=0;
		$workedlastday3="No";
	}
	if($position4 != 'free'){
		$result3 = mysqli_query($mysqli,"SELECT salary, workedlastday FROM users WHERE username='$position4'");
		$row3=mysqli_fetch_array($result3);
		$salary4=$row3["salary"];
		$workedlastday4=$row3["workedlastday"];
	}else{
		$salary4=0;
		$workedlastday4="No";
	}
	if($position5 != 'free'){
		$result3 = mysqli_query($mysqli,"SELECT salary, workedlastday FROM users WHERE username='$position5'");
		$row3=mysqli_fetch_array($result3);
		$salary5=$row3["salary"];
		$workedlastday5=$row3["workedlastday"];
	}else{
		$salary5=0;
		$workedlastday5="No";
	}
	
	if($type == "tavern"){
		$rooms=$row2["rooms"];
		$price=$row2["price"];
		
	}
	
	?> 
	<div class="scroll">
	<table id="table1">
		<tr>
		    <th> Company name</th>
		    <th> Type</th>
		    <th> Country</th>
		    <th> Region</th>
		    <th> Position 1</th>
		    <th> Salary</th>
		    <th> Worked last day</th>
		    <th> Position 2</th>
		    <th> Salary</th>
		    <th> Worked last day</th>
		    <th> Position 3</th>
		    <th> Salary</th>
		</tr>
	    <tr>
	       <td><?php echo $companyname; ?></td>
	       <td><?php echo $type; ?></td>
	       <td><?php echo $countryco; ?></td>
	       <td><?php echo $region; ?></td>
	       <td><?php echo $position1; ?></td>
	       <td><?php echo $salary1; ?></td>
	       <td><?php echo $workedlastday1; ?></td>
	       <td><?php echo $position2; ?></td>
	       <td><?php echo $salary2; ?></td>
	       <td><?php echo $workedlastday2; ?></td>
	       <td><?php echo $position3; ?></td>
	       <td><?php echo $salary3; ?></td>
	   </tr>		
	</table>
	<table id="table1">
		<tr>
			<th> Worked last day</th>
		    <th> Position 4</th>
		    <th> Salary</th>
		    <th> Worked last day</th>
		    <th> Position 5</th>
		    <th> Salary</th>
		    <th> Worked last day</th>
		    <th> Offered salary</th>
		    
		    <?php if($type=="tavern"){ ?>
			    <th> Rooms available</th>
			    <th> Price per room</th>
		    <?php } ?>
		    <?php if($type=="food raw"){ ?>
			    <th> Crop</th>
		    <?php } ?>
		</tr>
	    <tr>
	    	<td><?php echo $workedlastday3; ?></td>
	       <td><?php echo $position4; ?></td>
	       <td><?php echo $salary4; ?></td>
	       <td><?php echo $workedlastday4; ?></td>
	       <td><?php echo $position5; ?></td>
	       <td><?php echo $salary5; ?></td>
	       <td><?php echo $workedlastday5; ?></td>
	       <td><?php echo $joboffer; ?></td>
	       
		    <?php if($type=="tavern"){ ?>
			    <td><?php echo $rooms; ?></td>
			    <td><?php echo $price; ?></td>
		    <?php } ?>
		    <?php if($type=="food raw"){ ?>
			    <td><?php echo $cropname; ?></td>
		    <?php } ?>
	   </tr>		
	</table>
	</div>
	<?php

	
	//company forms
	if($type == "tavern"){
		?>
	     <form method="post" action=""> 
	     	<input type="hidden" name="position1" value="<?php echo "$position1"; ?>" /> 
	     	<input type="hidden" name="position2" value="<?php echo "$position2"; ?>" /> 
	     	<input type="hidden" name="position3" value="<?php echo "$position3"; ?>" /> 
	     	<input type="hidden" name="position4" value="<?php echo "$position4"; ?>" /> 
	     	<input type="hidden" name="position5" value="<?php echo "$position5"; ?>" />
	     	<input type="hidden" name="id" value="<?php echo "$id"; ?>" /> 
	     	<input type="hidden" name="type" value="<?php echo "$type"; ?>" />  
	     	    
	        <button type="submit" name="hireform" />Post offer on job market</button>
	        <button type="submit" name="fireform" />Fire employees</button>
	        <button type="submit" name="upgradeform" />Upgrade company</button>
	        <button type="submit" name="dissolveform" />Dissolve company</button>
	        <button type="submit" name="roompriceform" />Set room price</button>
	     </form>
		<?php
	}elseif($type == "food raw"){
		?>
	     <form method="post" action=""> 
	     	<input type="hidden" name="position1" value="<?php echo "$position1"; ?>" /> 
	     	<input type="hidden" name="position2" value="<?php echo "$position2"; ?>" /> 
	     	<input type="hidden" name="position3" value="<?php echo "$position3"; ?>" /> 
	     	<input type="hidden" name="position4" value="<?php echo "$position4"; ?>" /> 
	     	<input type="hidden" name="position5" value="<?php echo "$position5"; ?>" />
	     	<input type="hidden" name="id" value="<?php echo "$id"; ?>" /> 
	     	<input type="hidden" name="type" value="<?php echo "$type"; ?>" />  
	     	    
	        <button type="submit" name="hireform" />Post offer on job market</button>
	        <button type="submit" name="fireform" />Fire employees</button>
	        <button type="submit" name="upgradeform" />Upgrade company</button>
	        <button type="submit" name="dissolveform" />Dissolve company</button>
	        <button type="submit" name="cropform" />Set crop</button>
	     </form>
		<?php
	}else{
		?>
	     <form method="post" action=""> 
	     	<input type="hidden" name="position1" value="<?php echo "$position1"; ?>" /> 
	     	<input type="hidden" name="position2" value="<?php echo "$position2"; ?>" /> 
	     	<input type="hidden" name="position3" value="<?php echo "$position3"; ?>" /> 
	     	<input type="hidden" name="position4" value="<?php echo "$position4"; ?>" /> 
	     	<input type="hidden" name="position5" value="<?php echo "$position5"; ?>" />
	     	<input type="hidden" name="id" value="<?php echo "$id"; ?>" /> 
	     	<input type="hidden" name="type" value="<?php echo "$type"; ?>" /> 
	     	     
	        <button type="submit" name="hireform" />Post offer on job market</button>
	        <button type="submit" name="fireform" />Fire employees</button>
	        <button type="submit" name="upgradeform" />Upgrade company</button>
	        <button type="submit" name="dissolveform" />Dissolve company</button>
	     </form>
		<?php
	}
	?>
	<script>
	    if ( window.history.replaceState ) {
	        window.history.replaceState( null, null, window.location.href );
	    }
	</script>
	<?php
}
	
//if there is work; show form to set job offer
if(isset($_POST['hireform'])){
	$id=$mysqli->escape_string($_POST['id']);
	
	$result2 = mysqli_query($mysqli,"SELECT * FROM companies WHERE id='$id' AND owner='$username'");
	$row2=mysqli_fetch_array($result2);
	$id=$row2["id"];
	$type=$row2["type"];
	$position1=$row2["position1"];
	$position2=$row2["position2"];
	$position3=$row2["position3"];
	$position4=$row2["position4"];
	$position5=$row2["position5"];
	
	if ($position1=='free' || $position2=='free' || $position3=='free' || $position4=='free' || $position5=='free') {
		echo nl2br("<div class=\"t1\">Offer a salary to the job market to let users become a employee. You can fire employees after three days.</div>");		
		?>
		<form method="post" action="">
			<input type="number" size="25" required autocomplete="off" value="Enter salary here" name='offersalary' step="0.01" min="0.01" />
    		<button type="submit" name="postjoboffer" />Post job offer</button>
    	</form>
		<?php
	}else{
		echo'<div class="boxed">There are no work spots available!</div>';
	}
	?>
	<script>
	    if ( window.history.replaceState ) {
	        window.history.replaceState( null, null, window.location.href );
	    }
	</script>
	<?php
}

if(isset($_POST['fireform'])){
	$id=$mysqli->escape_string($_POST['id']);
	
	$result2 = mysqli_query($mysqli,"SELECT * FROM companies WHERE id='$id' AND owner='$username'");
	$row2=mysqli_fetch_array($result2);
	$id=$row2["id"];
	$type=$row2["type"];
	$position1=$row2["position1"];
	$position2=$row2["position2"];
	$position3=$row2["position3"];
	$position4=$row2["position4"];
	$position5=$row2["position5"];
	
	?>
	<form onsubmit="return confirm('Do you really want to fire?');" method="post" action="">
 	<select name="positionfire" type="text">
		<option value="position1">Position 1</option>
 		<option value="position2">Position 2</option>
 		<option value="position3">Position 3</option>
  		<option value="position4">Position 4</option>
  		<option value="position5">Position 5</option>
   	</select>
   	<input type="hidden" name="id" value="<?php echo "$id"; ?>" />
    	<button type="submit" name="fire" />Fire employee</button>
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

//upgrade company
if(isset($_POST['upgradeform'])){
	$id=$mysqli->escape_string($_POST['id']);
	
	$result2 = mysqli_query($mysqli,"SELECT * FROM companies WHERE id='$id' AND owner='$username'");
	$row2=mysqli_fetch_array($result2);
	$id=$row2["id"];
	$type=$row2["type"];
	$position1=$row2["position1"];
	$position2=$row2["position2"];
	$position3=$row2["position3"];
	$position4=$row2["position4"];
	$position5=$row2["position5"];
	
	if($type=='food q1' OR $type=='food q2' OR $type=='food q3' OR $type=='food q4' OR $type=='weapon q1' OR $type=='weapon q2' OR $type=='weapon q3' OR $type=='weapon q4'){
		echo nl2br("<div class=\"t1\">Upgrading your company gives you a higher q company. Upgrading costs 15 gold for q2, 20 gold for q3, 25 gold for q4 and 30 gold for q5.</div>");
		?>
		<form onsubmit="return confirm('Do you really want to upgrade?');" method="post" action="">
			<input type="hidden" name="type" value="<?php echo "$type"; ?>" />
			<input type="hidden" name="id" value="<?php echo "$id"; ?>" />
			<button type="submit" name="upgrade" />Upgrade company</button>
		</form>
		<?php
	}else{
		echo'<div class="boxed">This company can not be upgraded!</div>';
	}
	?>
	<script>
	    if ( window.history.replaceState ) {
	        window.history.replaceState( null, null, window.location.href );
	    }
	</script>
	<?php
}
//set room price
if(isset($_POST['roompriceform'])){
	$id=$mysqli->escape_string($_POST['id']);
	
	$result2 = mysqli_query($mysqli,"SELECT * FROM companies WHERE id='$id' AND owner='$username'");
	$row2=mysqli_fetch_array($result2);
	$id=$row2["id"];
	$type=$row2["type"];
	$position1=$row2["position1"];
	$position2=$row2["position2"];
	$position3=$row2["position3"];
	$position4=$row2["position4"];
	$position5=$row2["position5"];
	
	if($type == "tavern"){
		?>
		<form onsubmit="return confirm('Do you really want to change price?');" method="post" action="">
			<input type="hidden" name="id" value="<?php echo "$id"; ?>" />
			<label for="newprice:">Price per room:</label>
			<input type="number" size="25" required autocomplete="off" step="1" id="newprice" name='newprice' min="0" />
    		<button type="submit" name="setprice" />set room price</button>
    	</form>
		<?php
	}else{
		echo'<div class="boxed">Cannot do that!</div>';
	}
	?>
	<script>
	    if ( window.history.replaceState ) {
	        window.history.replaceState( null, null, window.location.href );
	    }
	</script>
	<?php
}

//set crop
if(isset($_POST['cropform'])){
	$id=$mysqli->escape_string($_POST['id']);
	
	$result = $mysqli->query("SELECT * FROM crops") or die($mysqli->error());
	$columnValues = Array();
	while ( $row = mysqli_fetch_assoc($result) ) {
	  $columnValues[] = $row['name'];
	}
	
	echo "Click <a href='story.php?category=1&topic=0'>here</a> for more information on the different crops.";
	?>
	<form method="post" action="">
		<input type="hidden" name="id" value="<?php echo "$id"; ?>" />
	    <select name="selectcrop" type="text">
	    <option selected="selected">Choose one</option>
	    <?php       
	    // Iterating through the product array
	    foreach($columnValues as $item){
	    ?>
	    <option value="<?php echo strtolower($item); ?>"><?php echo $item; ?></option>
	    <?php
	    }
	    ?>
	    </select> 
	    <button type="submit" name="selectcropform" />Select crop</button>
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

if(isset($_POST['selectcropform'])){
	$id=$mysqli->escape_string($_POST['id']);
	$selectcrop = $mysqli->escape_string($_POST['selectcrop']);
	//echo "$selectcompany";
	$result2 = mysqli_query($mysqli,"SELECT * FROM companies WHERE id='$id' AND owner='$username'");
	$row2=mysqli_fetch_array($result2);
	$id=$row2["id"];
	
	$result2 = mysqli_query($mysqli,"SELECT * FROM crops WHERE name='$selectcrop'");
	$row2=mysqli_fetch_array($result2);
	$cropid=$row2["id"];
	
	$sql = "UPDATE companies SET crop='$cropid' WHERE id='$id'";
	mysqli_query($mysqli, $sql);
	echo "$id";
	echo "Done!";
	
	?>
	<script>
	    if ( window.history.replaceState ) {
	        window.history.replaceState( null, null, window.location.href );
	    }
	</script>
	<?php
}

//dissolveform
if(isset($_POST['dissolveform'])){
	$id=$mysqli->escape_string($_POST['id']);
	
	$result2 = mysqli_query($mysqli,"SELECT * FROM companies WHERE id='$id' AND owner='$username'");
	$row2=mysqli_fetch_array($result2);
	$id=$row2["id"];
	$type=$row2["type"];
	$position1=$row2["position1"];
	$position2=$row2["position2"];
	$position3=$row2["position3"];
	$position4=$row2["position4"];
	$position5=$row2["position5"];
	
	?>
	<form onsubmit="return confirm('Do you really want to dissolve company?');" method="post" action="">
		<input type="hidden" name="id" value="<?php echo "$id"; ?>" />
    	<button type="submit" name="dissolve" />Dissolve company</button>
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
echo nl2br(" \n");

//post joboffer
if(isset($_POST['postjoboffer'])){
	$id = $_SESSION['id'];
	$offersalary=$mysqli->escape_string($_POST['offersalary']);
	$offersalary= (double) $offersalary;
	if($offersalary >= 0){
		$sql = "UPDATE companies SET joboffer='$offersalary' WHERE id='$id'";
		mysqli_query($mysqli, $sql);
		echo nl2br ("Created new joboffer \n");
	}

	?>
	<script>
	    if ( window.history.replaceState ) {
	        window.history.replaceState( null, null, window.location.href );
	    }
	</script>
	<?php
}

if(isset($_POST['fire'])){
	$positionfire=$mysqli->escape_string($_POST['positionfire']);
	$id = $_SESSION['id'];
	
	$result = mysqli_query($mysqli,"SELECT * FROM companies WHERE id='$id'");
	$row=mysqli_fetch_array($result);
	$employee=$row["$positionfire"];
	
	$result = $mysqli->query("SELECT workstart FROM users WHERE username='$employee'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$workstart=$row["workstart"];
	
	$date = new DateTime($workstart);
	$date->add(new DateInterval('P3D')); // P1D means a period of 1 day
	$Datenew1 = $date->format('Y-m-d H:i:s');
	
	date_default_timezone_set('UTC'); //current date
	$datecur = date("Y-m-d H:i:s"); 
	
	if($datecur>$Datenew1){
		$sql = "UPDATE users SET salary='0',workid='0',workstart='2099-01-01 00:00:00' WHERE username='$employee'";
		mysqli_query($mysqli, $sql);
		
		$sql = "UPDATE companies SET $positionfire='free' WHERE id='$id'";
		mysqli_query($mysqli, $sql);
		
		echo "Done!";
	}else{
		echo "Employee is working les than three days for you!";
	}
	?>
	<script>
	    if ( window.history.replaceState ) {
	        window.history.replaceState( null, null, window.location.href );
	    }
	</script>
	<?php
}

if(isset($_POST['upgrade'])){
	$id=$mysqli->escape_string($_POST['id']);
	
	$result2 = mysqli_query($mysqli,"SELECT * FROM companies WHERE id='$id' AND owner='$username'");
	$row2=mysqli_fetch_array($result2);
	$id=$row2["id"];
	$type=$row2["type"];
	$position1=$row2["position1"];
	$position2=$row2["position2"];
	$position3=$row2["position3"];
	$position4=$row2["position4"];
	$position5=$row2["position5"];
	
	if($type=='food q1'){
		$result = $mysqli->query("SELECT * FROM currency WHERE usercur='$username'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$gold = $row['gold'];		
		$gold=$gold-15;

		if($gold>=0){
			$sql = "UPDATE currency SET gold='$gold' WHERE usercur='$username'";
			mysqli_query($mysqli, $sql);
			
			$sql = "UPDATE companies SET type='food q2' WHERE id='$id'";
			mysqli_query($mysqli, $sql);
		}else{
			echo'<div class="boxed">You don\'t have enough gold!</div>';
		}
	}elseif($type=='food q2'){
		$result = $mysqli->query("SELECT * FROM currency WHERE usercur='$username'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$gold = $row['gold'];		
		$gold=$gold-20;

		if($gold>=0){
			$sql = "UPDATE currency SET gold='$gold' WHERE usercur='$username'";
			mysqli_query($mysqli, $sql);
			
			$sql = "UPDATE companies SET type='food q3' WHERE id='$id'";
			mysqli_query($mysqli, $sql);
		}else{
			echo'<div class="boxed">You don\'t have enough gold!</div>';
		}
	}elseif($type=='food q3'){
		$result = $mysqli->query("SELECT * FROM currency WHERE usercur='$username'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$gold = $row['gold'];		
		$gold=$gold-25;

		if($gold>=0){
			$sql = "UPDATE currency SET gold='$gold' WHERE usercur='$username'";
			mysqli_query($mysqli, $sql);
			
			$sql = "UPDATE companies SET type='food q4' WHERE id='$id'";
			mysqli_query($mysqli, $sql);
		}else{
			echo'<div class="boxed">You don\'t have enough gold!</div>';
		}
	}elseif($type=='food q4'){
		$result = $mysqli->query("SELECT * FROM currency WHERE usercur='$username'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$gold = $row['gold'];		
		$gold=$gold-30;

		if($gold>=0){
			$sql = "UPDATE currency SET gold='$gold' WHERE usercur='$username'";
			mysqli_query($mysqli, $sql);
			
			$sql = "UPDATE companies SET type='food q5' WHERE id='$id'";
			mysqli_query($mysqli, $sql);
		}else{
			echo'<div class="boxed">You don\'t have enough gold!</div>';
		}
	}elseif($type=='weapon q1'){
		$result = $mysqli->query("SELECT * FROM currency WHERE usercur='$username'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$gold = $row['gold'];		
		$gold=$gold-15;

		if($gold>=0){
			$sql = "UPDATE currency SET gold='$gold' WHERE usercur='$username'";
			mysqli_query($mysqli, $sql);
			
			$sql = "UPDATE companies SET type='weapon q2' WHERE id='$id'";
			mysqli_query($mysqli, $sql);
		}else{
			echo'<div class="boxed">You don\'t have enough gold!</div>';
		}
	}elseif($type=='weapon q2'){
		$result = $mysqli->query("SELECT * FROM currency WHERE usercur='$username'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$gold = $row['gold'];		
		$gold=$gold-20;

		if($gold>=0){
			$sql = "UPDATE currency SET gold='$gold' WHERE usercur='$username'";
			mysqli_query($mysqli, $sql);
			
			$sql = "UPDATE companies SET type='weapon q3' WHERE id='$id'";
			mysqli_query($mysqli, $sql);
		}else{
			echo'<div class="boxed">You don\'t have enough gold!</div>';
		}
	}elseif($type=='weapon q3'){
		$result = $mysqli->query("SELECT * FROM currency WHERE usercur='$username'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$gold = $row['gold'];		
		$gold=$gold-25;

		if($gold>=0){
			$sql = "UPDATE currency SET gold='$gold' WHERE usercur='$username'";
			mysqli_query($mysqli, $sql);
			
			$sql = "UPDATE companies SET type='weapon q4' WHERE id='$id'";
			mysqli_query($mysqli, $sql);
		}else{
			echo'<div class="boxed">You don\'t have enough gold!</div>';
		}
	}elseif($type=='weapon q4'){
		$result = $mysqli->query("SELECT * FROM currency WHERE usercur='$username'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$gold = $row['gold'];		
		$gold=$gold-30;

		if($gold>=0){
			$sql = "UPDATE currency SET gold='$gold' WHERE usercur='$username'";
			mysqli_query($mysqli, $sql);
			
			$sql = "UPDATE companies SET type='weapon q5' WHERE id='$id'";
			mysqli_query($mysqli, $sql);
		}else{
			echo'<div class="boxed">You don\'t have enough gold!</div>';
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

if(isset($_POST['setprice'])){
	$id=$mysqli->escape_string($_POST['id']);
	
	$result2 = mysqli_query($mysqli,"SELECT * FROM companies WHERE id='$id' AND owner='$username'");
	$row2=mysqli_fetch_array($result2);
	$id=$row2["id"];

	$newprice=$mysqli->escape_string($_POST['newprice']);
	$newprice= (double) $newprice;
	
	$sql = "UPDATE companies SET price='$newprice' WHERE id='$id'";
	mysqli_query($mysqli, $sql);
	?>
	<script>
	    if ( window.history.replaceState ) {
	        window.history.replaceState( null, null, window.location.href );
	    }
	</script>
	<?php
}

if(isset($_POST['dissolve'])){
	$id=$mysqli->escape_string($_POST['id']);
	
	$result2 = mysqli_query($mysqli,"SELECT * FROM companies WHERE id='$id' AND owner='$username'");
	$row2=mysqli_fetch_array($result2);
	$id=$row2["id"];
	$type=$row2["type"];
	$position1=$row2["position1"];
	$position2=$row2["position2"];
	$position3=$row2["position3"];
	$position4=$row2["position4"];
	$position5=$row2["position5"];
	
	if($position1 != "free"){
		$sql = "UPDATE users SET salary='0',workid='0',workstart='2099-01-01 00:00:00' WHERE username='$position1'";
		mysqli_query($mysqli, $sql);
	}elseif($position2 != "free"){
		$sql = "UPDATE users SET salary='0',workid='0',workstart='2099-01-01 00:00:00' WHERE username='$position2'";
		mysqli_query($mysqli, $sql);		
	}elseif($position3 != "free"){
		$sql = "UPDATE users SET salary='0',workid='0',workstart='2099-01-01 00:00:00' WHERE username='$position3'";
		mysqli_query($mysqli, $sql);		
	}elseif($position4 != "free"){
		$sql = "UPDATE users SET salary='0',workid='0',workstart='2099-01-01 00:00:00' WHERE username='$position4'";
		mysqli_query($mysqli, $sql);		
	}elseif($position5 != "free"){
		$sql = "UPDATE users SET salary='0',workid='0',workstart='2099-01-01 00:00:00' WHERE username='$position5'";
		mysqli_query($mysqli, $sql);		
	}
		
	$sql = "DELETE FROM companies WHERE id='$id'";
	mysqli_query($mysqli, $sql); 
	
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
//create new company form
if(isset($_POST['newcompany'])){
	echo nl2br("<div class=\"t1\">Creating a new company costs 10 gold.</div>");
	?>	
	<form onsubmit="return confirm('Do you really want to buy a company?');" method="post" action="">  
	 	<select name="type" type="text">
			<option value="weapon raw">weapon raw</option>
	 		<option value="food raw">food raw</option>
	 		<option value="house raw">house raw</option>
	 		<option value="paper">paper factory</option>
	 		<option value="hospital raw">hospital raw</option>
	  		<option value="weapon q1">weapon q1</option>
	  		<option value="food q1">food q1</option>
	  		<option value="houses">houses</option>
	  		<option value="book">book factory</option>
	  		<option value="hospital">hospital</option>
	  		<option value="tavern">tavern</option>
	   	</select>
	   	<input type="text" pattern="[a-zA-Z0-9]+[a-zA-Z0-9 ]+" size="25" required autocomplete="off" placeholder="Enter company name here" maxlength="15" name='companyname'/>
	    <button type="submit" name="create" />Create company</button>
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

//create new company
if(isset($_POST['create'])){
	$companyname = $mysqli->escape_string($_POST['companyname']);
	$type = $mysqli->escape_string($_POST['type']);
	$companysafe = str_replace(' ', '', $companyname);
	if($type=="weapon raw" || $type=="food raw" || $type=="house raw" || $type=="paper" || $type=="hospital raw" || $type=="weapon q1" || $type=="food q1" || $type=="houses" || $type=="book" || $type=="hospital" || $type=="tavern"){
		if(strlen($companyname) <= 15 AND strlen($companyname) >= 1 AND ctype_alnum($companysafe)){
			//get gold info
			$result = $mysqli->query("SELECT * FROM currency WHERE usercur='$username'") or die($mysqli->error());
			$row = mysqli_fetch_array($result);
			$gold = $row['gold'];
			
			$result = $mysqli->query("SELECT location, location2 FROM users WHERE username='$username'") or die($mysqli->error());
			$row = mysqli_fetch_array($result);
			$location = $row['location'];
			$location2 = $mysqli->escape_string($row['location2']);
			
			//set new gold
			$gold=$gold-10;
			if($gold>=0){
				$sql = "UPDATE currency SET gold='$gold' WHERE usercur='$username'";
				mysqli_query($mysqli, $sql);
				$sql = "INSERT INTO companies (owner, countryco, region, type, companyname, position1, position2, position3, position4, position5) " 
		            . "VALUES ('$username','$location', '$location2', '$type', '$companyname', 'free', 'free', 'free', 'free', 'free')";
				mysqli_query($mysqli, $sql);
				
				echo'<div class="boxed">Done!</div>';
			}else{
				echo'<div class="boxed">You don\'t have enough gold!</div>';
			}
		}
	}else{
		
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
