<?php 
//require 'navigationbar.php';
//require 'db.php';
require '../db.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/* Displays user information and some useful messages */
session_start();

?>

<!DOCTYPE html>

<html>
	
<head>
  <title>Update</title>
  <?php include '../css/css.html'; ?>
</head>

<body>

<?php

$pass=$_GET["pass"];
if($pass != $weather_update_pass){
//if($pass != $update_pass){
	die('Acces is not permitted');
}

//get day of the month
date_default_timezone_set('UTC');
$day = date("d");

$climatearray=array("dessert", "tropical", "savanna", "mediterranean", "sea", "temperate", "mountain");
foreach ($climatearray as $climate) {
	$result = $mysqli->query("SELECT * FROM region WHERE climate='$climate'") or die($mysqli->error());
	$temp=99; //alleen de temperatuur van 1 regio bepalen in elk klimaat
	
	while($row=mysqli_fetch_array($result)) {
		$id=$row["id"];
		$climate=$row["climate"];
		$currtemp=$row["currtemp"];
		$currweather=$row["currweather"];
		$weatherstreak=$row["weatherstreak"];
		$weatherevent=$row["weatherevent"];
		
		if($temp==99){
			//weather
			if($day > 8 OR $day < 22){
				$state="dryseason";
			}else{
				$state="wetseason";
			}
			
			if($climate=="dessert"){ //bereken temperatuur die het op dit moment zou moeten zijn en regenkans
				$temp=-0.067*($day**2)+(2*$day)+30;
				
				$rainchance=5; //1%
			}elseif($climate=="tropical"){
				$temp=-0.022*($day**2)+0.67*$day+25;
				
				$rainchance=80; //80% 
			}elseif($climate=="savanna"){
				$temp=-0.022*($day**2)+0.67*$day+25;
				
				if($state=="wetseason"){
					$rainchance=60;
				}else{
					$rainchance=15;
				}
			}elseif($climate=="mediterranean"){
				$temp=-0.089*($day**2)+2.67*$day+10;
				
				if($state=="wetseason"){
					$rainchance=40;
				}else{
					$rainchance=15;
				}
			}elseif($climate=="sea"){
				$temp=-0.067*($day**2)+2*$day+5;
				
				if($state=="wetseason"){
					$rainchance=70;
				}else{
					$rainchance=40;
				}
			}elseif($climate=="temperate"){
				$temp=-0.156*($day**2)+4.67*$day-10;
				
				if($state=="wetseason"){
					$rainchance=80;
				}else{
					$rainchance=20;
				}
			}elseif($climate=="mountain"){
				$temp=-0.13*($day**2)+4*$day-20;
				
				if($state=="wetseason"){
					$rainchance=30;
				}else{
					$rainchance=10;
				}
			}
			
			$difftemp=$temp-$currtemp; //verschil tussen temperatuur die het zou moeten zijn en actuele temperatuur
			$noise=rand(1, 15); // noise temperatuur tussen 0.1 en 1.5 graden
			$noisetemp=$noise/10;
			
			if($difftemp <= 0){ //te warm dus moet kouder worden
				$win=rand(1, 10);
				if($win <= 7){ // 70 procent kans op -
					$temp=$currtemp-$noisetemp;
				}else{
					$temp=$currtemp+$noisetemp;
				}
			}else{ //te koud dus moet warmer worden
				$win=rand(1, 10);
				if($win <= 7){ // 70 procent kans op +
					$temp=$currtemp+$noisetemp;
				}else{
					$temp=$currtemp-$noisetemp;
				}
			}
			
			//weather update
			$weathernoise=rand(1, 20);
			$psameweather=70+$weathernoise;
			
			$weatherchange=rand(1, 100);
			if($weatherchange <= $psameweather){ //houd hetzelfde weer
				$curregionweather=$currweather;
			
				$weatherstreak2=$weatherstreak+1;
				if($weatherstreak2 >= 24){ //kans van 0.0797
					if($currweather == "rain" OR $currweather == "thunderstorm" OR $currweather == "drizzle"){
						$weatherevent="flood";
					}elseif($currweather == "sunny" OR $currweather == "clouded" OR $currweather == "windy"){
						$weatherevent="drought";
					}
				}
			}else{
				$prain=rand(1, 100);
				
				if($prain <= $rainchance){ //regen
					$weatherarray=array("rain", "thunderstorm", "drizzle");
					
					foreach ($weatherarray as $weather) { //als nieuw weer in zelfde categorie dan verdergaan met tellen
						if($currweather == $weather){
							$weatherstreak2 = $weatherstreak2 + 1;
						}else{
							$weatherstreak2 = 0;
							$weatherevent = "none";
						}
					}
					
					$randindex=array_rand($weatherarray);
					$curregionweather=$weatherarray[$randindex];
				}else{
					$weatherarray=array("sunny", "clouded", "windy");
					
					foreach ($weatherarray as $weather) { //als nieuw weer in zelfde categorie dan verdergaan met tellen
						if($currweather == $weather){
							$weatherstreak2 = $weatherstreak2 + 1;
						}else{
							$weatherstreak2 = 0;
							$weatherevent = "none";
						}
					}
					
					$randindex=array_rand($weatherarray);
					$curregionweather=$weatherarray[$randindex];
				}
			}
		}

		$noise2=rand(-5, 5); // verschil per regio temperatuur tussen -0.5 en 0.5 graden
		$noisetemp2=$noise2/10;
		
		$currregiontemp=$temp+$noisetemp2;
		$currregiontemp=round($currregiontemp, 1);
		
		$noise3=rand(1, 100); // 10% kans op ander weer dan andere regios
		if($noise3 <= 10){
			$currweather=$curregionweather;
		}else{
			$currweather==$currweather;
		}
		
		if($currregiontemp <= 0 AND ($currweather == "rain" OR $currweather == "thunderstorm" OR $currweather == "drizzle")){ //sneeuw
			$currweather = "snowfall";
		}
		
		$sql = "UPDATE region SET currtemp='$currregiontemp', currweather='$currweather', weatherstreak='$weatherstreak2' WHERE id='$id'";
		mysqli_query($mysqli, $sql);
	}
}


?>

</body>
</html>
