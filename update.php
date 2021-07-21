<?php 
//require 'navigationbar.php';
//require 'db.php';
require '../db.php';
require '../regionborders.php';
require '../functions.php';
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
/*
var_dump($_SERVER['SERVER_ADDR']);
echo "\n";
var_dump($_SERVER['REMOTE_ADDR']); 
if($_SERVER['REMOTE_ADDR'] != $_SERVER['SERVER_ADDR']){
	die('Acces is not permitted');
} 
if($_SERVER['REMOTE_ADDR'] != "192.168.0.87"){
	die('Acces is not permitted');
}
*/
///../update.php
//echo $_SERVER['DOCUMENT_ROOT'];

$pass=$_GET["pass"];
//if($pass != $update_pass){
if($pass != $update_pass){
	die('Acces is not permitted');
}

//set trained to 0
$sql = "UPDATE users SET trained = '0'";
mysqli_query($mysqli, $sql);

//set workedlastday to worked and worked to 0
$sql = "UPDATE users SET workedlastday=worked";
mysqli_query($mysqli, $sql);

$sql = "UPDATE users SET worked = '0'";
mysqli_query($mysqli, $sql);

//set dueled to 0
$sql = "UPDATE users SET dueled = '0'";
mysqli_query($mysqli, $sql);

//set ageup to 0
$sql = "UPDATE users SET ageup = '0'";
mysqli_query($mysqli, $sql);

//set spread to 0
$sql = "UPDATE users SET spread = '0'";
mysqli_query($mysqli, $sql);

//set expedition to 0
$sql = "UPDATE users SET expedition = '0'";
mysqli_query($mysqli, $sql);

//set tavernup to 0
$sql = "UPDATE users SET tavernup = '0'";
mysqli_query($mysqli, $sql);

//set locationup to 0
$sql = "UPDATE users SET locationup = '0'";
mysqli_query($mysqli, $sql);

//set epidemicup to 0
$sql = "UPDATE region SET epidemicup = '0'";
mysqli_query($mysqli, $sql);

$sql = "UPDATE characters SET wayoflifeaction='0'";
mysqli_query($mysqli, $sql);

//get day of the month
date_default_timezone_set('UTC');
$day = date("d");

//set tax income for this day to last day and tax statistics for region
$result = $mysqli->query("SELECT * FROM region") or die($mysqli->error());
while($row=mysqli_fetch_array($result)) {
	$regionid=$row["id"];
	$regioname=$row["name"];
	$taxtoday=$row["taxtoday"];
	$currtemp=$row["currtemp"];

	$sql = "INSERT INTO statistics (type, datestat, waarde, name) " 
     . "VALUES ('regiontax',NOW() - INTERVAL 1 DAY,'$taxtoday','$regioname')";
	mysqli_query($mysqli2, $sql);
	
	$sql = "INSERT INTO statistics (type, datestat, waarde, name) " 
     . "VALUES ('regiontemperature',NOW(),'$currtemp','$regionid')";
	mysqli_query($mysqli2, $sql);
}

//update age and death
$result2 = "SELECT * FROM characters WHERE alive='1'";
$rs_result = $mysqli->query($result2);
$count = $rs_result->num_rows;
while($rowimportant = $rs_result->fetch_assoc()) {
	$characterid=$rowimportant["id"];
	$characterage=$rowimportant["age"];
	$characteruser=$rowimportant["user"];
	$charactername=$rowimportant["name"];
	$characterfamily=$rowimportant["familyid"];
	$charactermarried=$rowimportant["married"];
	$characterliege=$rowimportant["liege"];
	$characterrace=$rowimportant["race"];
	$charactermother=$rowimportant["mother"];
	$characterfather=$rowimportant["father"];
	$characterfertile=$rowimportant["fertile"];
	$charactersex=$rowimportant["sex"];
	$charactermatrilineal=$rowimportant["matrilineal"];
	$characterlocation=$rowimportant["location"];
	$characterlocation2=$rowimportant["location2"];
	$characternationality=$rowimportant["nationality"];
	
	$characterage=$characterage+1;
	if($charactersex == "female"){
		$c=(0.00001*exp(0.085*$characterage))*100;//*100 omdat anders tussen 0 en 1
	}else{
		$c=(0.0001*exp(0.085*$characterage))*100;
	}
	//$c=15*(0.005*($characterage-0))^2+0;  //a(b(x-h))^n+k     Gompertz law of human mortality 0.0001*e^(0.85*x) -> gebruik 0.0001 voor 50% kans bij 100 jaar, 0.00001 voor 50% bij 130 jaar
	
	//go through all traits & update health traits to calculate chance to die
	$fertiletraits = 0;
	$result3 = "SELECT * FROM traitscharacters WHERE characterid='$characterid'";
	$rs_result2 = $mysqli->query($result3);
	$count2 = $rs_result2->num_rows;//aantal titles
	while($row2 = $rs_result2->fetch_assoc()) {//ga door traits heen
		$traitid=$row2["traitid"];
    $traitcharacterid=$row2["id"];
    $traitextrainfo=$row2["extrainfo"];
		
		$result4 = $mysqli->query("SELECT * FROM traits WHERE id='$traitid'") or die($mysqli->error());
		$row4 = mysqli_fetch_array($result4);
		$traittype = $row4['type'];
		$traitamount = $row4['amount'];
		$traitremovechance = $row4['removechance'];
		$traitname = $row4['name'];
		
		$addage =  ($c / 100) * $traitamount;//traitamount is given as percentage of chance to die; 50% to die + 10% = 60%
		if($traittype == "health"){
			$c = $c + (-1 * $addage);
		}elseif($traittype == "fertility"){
			$fertiletraits = $fertiletraits + $traitamount;//will be added or retracted to 10
		}
		
    if($traitname == "sad"){
      $result4 = $mysqli->query("SELECT * FROM traits WHERE name='depressed'") or die($mysqli->error());
      $row4 = mysqli_fetch_array($result4);
      $depressedtraitid = $row4['id'];
      
      if(checkifcharacteralreadyhastrait($characterid, $depressedtraitid) == 0){
        $randdepressed = rand(0, 100);
        if($randdepressed <= 15){
          if(checkantitrait($characterid, $depressedtraitid) == 0){
            $sql = "INSERT INTO traitscharacters (characterid, traitid, date, extrainfo) " 
            . "VALUES ('$characterid','$depressedtraitid',NOW(), '0')";
            mysqli_query($mysqli, $sql);

            $content= "You got depressed";
            $sql = "INSERT INTO events (date, content, extrainfo) " 
               . "VALUES (NOW(),'$content','$characteruser')";
            mysqli_query($mysqli2, $sql);
          }
        }
      }
    }elseif($traitname == "depressed"){
      $traitextrainfo = $traitextrainfo + 1;

      $sql = "UPDATE traitscharacters SET extrainfo='$traitextrainfo' WHERE id='$traitcharacterid'";
      mysqli_query($mysqli, $sql);
    }elseif($traitname == "pessimistic"){
        $result4 = $mysqli->query("SELECT * FROM traits WHERE name='sad'") or die($mysqli->error());
        $row4 = mysqli_fetch_array($result4);
        $sadtraitid = $row4['id'];

        if(checkifcharacteralreadyhastrait($characterid, $sadtraitid) == 0){
          $randdepressed = rand(0, 100);
          if($randdepressed <= 5){
            if(checkantitrait($characterid, $sadtraitid) == 0){
              $sql = "INSERT INTO traitscharacters (characterid, traitid, date, extrainfo) " 
              . "VALUES ('$characterid','$sadtraitid',NOW(), '0')";
              mysqli_query($mysqli, $sql);
            }
          }
        }
    }elseif($traitname == "optimistic"){
        $result4 = $mysqli->query("SELECT * FROM traits WHERE name='happy'") or die($mysqli->error());
        $row4 = mysqli_fetch_array($result4);
        $happytraitid = $row4['id'];

        if(checkifcharacteralreadyhastrait($characterid, $happytraitid) == 0){
          $randdepressed = rand(0, 100);
          if($randdepressed <= 5){
            if(checkantitrait($characterid, $happytraitid) == 0){
              $sql = "INSERT INTO traitscharacters (characterid, traitid, date, extrainfo) " 
              . "VALUES ('$characterid','$happytraitid',NOW(), '0')";
              mysqli_query($mysqli, $sql);
            }
          }
        }
    }elseif($traitname == "fiery"){
        $result4 = $mysqli->query("SELECT * FROM traits WHERE name='angry'") or die($mysqli->error());
        $row4 = mysqli_fetch_array($result4);
        $angrytraitid = $row4['id'];

        if(checkifcharacteralreadyhastrait($characterid, $angrytraitid) == 0){
          $randdepressed = rand(0, 100);
          if($randdepressed <= 5){
            if(checkantitrait($characterid, $angrytraitid) == 0){
              $sql = "INSERT INTO traitscharacters (characterid, traitid, date, extrainfo) " 
              . "VALUES ('$characterid','$angrytraitid',NOW(), '0')";
              mysqli_query($mysqli, $sql);
            }
          }
        }
    }
    
		$randnumber = rand(0, 100);//chance to remove trait
		if($randnumber <= $traitremovechance){
			if($traitname == "epidemic"){
				$result4 = $mysqli->query("SELECT * FROM traits WHERE name='immune'") or die($mysqli->error());
				$row4 = mysqli_fetch_array($result4);
				$immuneid = $row4['id'];
				
				$randnumber2 = rand(0, 100);
				if($randnumber2 <= 70){
					$sql = "INSERT INTO traitscharacters (characterid, traitid, date) " 
					. "VALUES ('$characterid','$immuneid',NOW())";
			 		mysqli_query($mysqli, $sql);
					
					$content= "You recovered from the epidemic and gained immunity";
					$sql = "INSERT INTO events (date, content, extrainfo) " 
				     . "VALUES (NOW(),'$content','$characteruser')";
					mysqli_query($mysqli2, $sql);
				}else{
					$content= "You recovered from the epidemic";
					$sql = "INSERT INTO events (date, content, extrainfo) " 
				     . "VALUES (NOW(),'$content','$characteruser')";
					mysqli_query($mysqli2, $sql);
				}
			}elseif($traitname == "wounded"){
				$traitarray = array("one eyed","one legged","one handed","scarred");
				
				$randindex = array_rand($traitarray);
				$randomvalue = $traitarray[$randindex];
				
				$result4 = $mysqli->query("SELECT * FROM traits WHERE name='$randomvalue'") or die($mysqli->error());
				$row4 = mysqli_fetch_array($result4);
				$valueid = $row4['id'];
				$valuebirthchance = $row4['birthchance'];
				
				$randnumber2 = rand(0, 100);
				
				if($randnumber2 <= 30){
					$sql = "INSERT INTO traitscharacters (characterid, traitid, date) " 
					. "VALUES ('$characterid','$valueid',NOW())";
			 		mysqli_query($mysqli, $sql);
			 		
					$content= "You recovered from the wounded trait and gained the $randomvalue trait";
					$sql = "INSERT INTO events (date, content, extrainfo) " 
				     . "VALUES (NOW(),'$content','$characteruser')";
					mysqli_query($mysqli2, $sql);
		 		}else{
					$content= "You recovered from the wounded trait";
					$sql = "INSERT INTO events (date, content, extrainfo) " 
				     . "VALUES (NOW(),'$content','$characteruser')";
					mysqli_query($mysqli2, $sql);
		 		}
			}
			
			$sql = "DELETE FROM traitscharacters WHERE id='$traitid' ";
			mysqli_query($mysqli, $sql);
		}
	}
	
	//check npc user location
	if($characteruser == "npc"){
		if($characterlocation2 == NULL){
			$result4 = $mysqli->query("SELECT * FROM region WHERE name='cahaurin'") or die($mysqli->error());
			$row4 = mysqli_fetch_array($result4);
			$regioncurrowner= $row4['curowner'];
			
			$sql = "UPDATE characters SET location='$regioncurrowner', location2='cahaurin' WHERE id='$characterid'";
			mysqli_query($mysqli, $sql);
		}else{
			$randnumber = rand(1, 10);
			
			$result4 = $mysqli->query("SELECT * FROM traits WHERE name='suspicious'") or die($mysqli->error());
			$row4 = mysqli_fetch_array($result4);
			$susptraitid = $row4['id'];
			
			$result3 = "SELECT * FROM traitscharacters WHERE characterid='$characterid' AND traitid='$susptraitid'";
			$rs_result2 = $mysqli->query($result3);
			$count2 = $rs_result2->num_rows;//aantal titles
			if($randnumber <= 4 OR $count2 != 0){//40% chance to move; when suspicious 100% chance
				$newregionlocation = selectrandomneighbouringregion($characterlocation2);
				$newregionlocation=$mysqli->escape_string($newregionlocation);
				
				$result4 = $mysqli->query("SELECT * FROM region WHERE name='$newregionlocation'") or die($mysqli->error());
				$row4 = mysqli_fetch_array($result4);
				$regioncurrowner= $row4['curowner'];
				
				$sql = "UPDATE characters SET location='$regioncurrowner', location2='$newregionlocation' WHERE id='$characterid'";
				mysqli_query($mysqli, $sql);
			}
		}
	}
	
	//check for epidemics
	$result4 = $mysqli->query("SELECT * FROM users WHERE username='$characteruser'") or die($mysqli->error());
	$row4 = mysqli_fetch_array($result4);
	$characterlocation2 = $row4['location2'];
	$characterlocation2=$mysqli->escape_string($characterlocation2);
	
	$result4 = $mysqli->query("SELECT * FROM region WHERE name='$characterlocation2'") or die($mysqli->error());
	$row4 = mysqli_fetch_array($result4);
	$regionepidemic = $row4['epidemic'];
	
	if($regionepidemic == 1){
		$result3 = "SELECT * FROM traitscharacters WHERE characterid='$characterid'";
		$rs_result2 = $mysqli->query($result3);
		$count2 = $rs_result2->num_rows;//aantal titles
		
		$sickimmune = 0;
		if($count2 != 0){
			while($row2 = $rs_result2->fetch_assoc()) {//ga door titles heen
				$traitid=$row2["traitid"];
				
				$result4 = $mysqli->query("SELECT * FROM traits WHERE id='$traitid'") or die($mysqli->error());
				$row4 = mysqli_fetch_array($result4);
				$traittype = $row4['type'];
				$traitamount = $row4['amount'];
				$traitname = $row4['name'];
				
				if($traitname == "epidemic" OR $traitname == "immune"){
					$sickimmune = $sickimmune + 1;
				}
			}
		}
		
		if($sickimmune == 0){
			$randnumber = rand(0, 100);
			
			if($randnumber <= 10){
				$result4 = $mysqli->query("SELECT * FROM traits WHERE name='epidemic'") or die($mysqli->error());
				$row4 = mysqli_fetch_array($result4);
				$traitid = $row4['id'];
				
				$sql = "INSERT INTO traitscharacters (characterid, traitid, date) " 
				. "VALUES ('$characterid','$traitid',NOW())";
		 		mysqli_query($mysqli, $sql);
				
				$content= "You contracted the epidemic that is spreading";
				$sql = "INSERT INTO events (date, content, extrainfo) " 
			     . "VALUES (NOW(),'$content','$characteruser')";
				mysqli_query($mysqli2, $sql);
			}
		}
	}
	
	
	//update fertility
	if($characterage >= 18 AND $charactermarried != 0){
		$result = $mysqli->query("SELECT * FROM characters WHERE id='$charactermarried'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$marriedid=$row['id'];
		$marriedsex=$row['sex'];
		$marriedrace=$row['race'];
		$marriedage=$row['age'];
		
		$randfertile=rand(0, 100);//1x in tien dagen fertile
		$fertilechance = 70 + $fertiletraits;
		if($marriedsex != $charactersex AND $marriedrace == $characterrace AND $randfertile<=$fertilechance){
			$sql = "UPDATE characters SET fertile='1' WHERE id='$characterid'";
			mysqli_query($mysqli, $sql);
		}else{
			$sql = "UPDATE characters SET fertile='0' WHERE id='$characterid'";
			mysqli_query($mysqli, $sql);
		}
	}else{
		$sql = "UPDATE characters SET fertile='0' WHERE id='$characterid'";
		mysqli_query($mysqli, $sql);
	}
	/*
	if($charactermarried != 0){
		if($characterfertile == 0 AND $charactersex == "female"){
			$result = $mysqli->query("SELECT * FROM characters WHERE id='$charactermarried'") or die($mysqli->error());
			$row = mysqli_fetch_array($result);
			$marriedid=$row['id'];
			$marriedsex=$row['sex'];
			$marriedrace=$row['race'];
			$marriedage=$row['age'];
			
			$randfertile=rand(0, 100);//1x in tien dagen fertile
			$fertilechance = 10 + $fertiletraits;
			if($marriedsex != $charactersex AND $characterage >= 18 AND $marriedrace == $characterrace AND $randfertile<=$fertilechance){
				$sql = "UPDATE characters SET fertile='1' WHERE id='$characterid'";
				mysqli_query($mysqli, $sql);
			}
		}
	}
	*/
	
	//random child
	//also update ageing.php
	if($charactermarried != 0){
		$result = $mysqli->query("SELECT * FROM characters WHERE id='$charactermarried'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$marriedid=$row['id'];
		$marriedsex=$row['sex'];
		$marriedrace=$row['race'];
		$marriedage=$row['age'];
		$marriedfertile=$row['fertile'];
		$marriedfamily = $row['familyid'];
		$marrieduser = $row['user'];
		
		if($characterfertile == 1 AND $marriedfertile == 1){
			$rchild = rand(1, 30);
			if($rchild == 1){
				$rnumber=rand(0, 1);
				if($rnumber==0){
					$nsex="male";
				}else{
					$nsex="female";
				}
				
				if($charactermatrilineal==0){
					if($charactersex=="male"){
						$liege=$characterid;
						$father=$characterid;
						$mother=$charactermarried;
						$familyid=$characterfamily;
					}else{
						$liege=$charactermarried;
						$father=$charactermarried;
						$mother=$characterid;
						$familyid=$marriedfamily;
					}
				}else{
					if($charactersex=="male"){
						$liege=$charactermarried;
						$father=$characterid;
						$mother=$charactermarried;
						$familyid=$marriedfamily;
					}else{
						$liege=$characterid;
						$father=$charactermarried;
						$mother=$characterid;
						$familyid=$characterfamily;
					}
				}
				
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
				
				$result4 = $mysqli->query("SELECT * FROM characters WHERE id='$liege'") or die($mysqli->error());
				$row4 = mysqli_fetch_array($result4);
				$liegename = $row4['name'];
				$liegebirthplace = $row4['birthplace'];
				$liegenationality = $row4['nationality'];
				$liegebirthplace=$mysqli->escape_string($liegebirthplace);
				
				$result4 = $mysqli->query("SELECT * FROM region WHERE name='$liegebirthplace'") or die($mysqli->error());
				$row4 = mysqli_fetch_array($result4);
				$currowner = $row4['currowner'];
				
				$location2escaped=$mysqli->escape_string(location2);
				$sql = "INSERT INTO characters (alive, type, sex, race, user,mother,father,liege,familyid,lastonline,birthplace,location,$location2escaped,nationality) " 
				. "VALUES ('1','npc','$nsex','$characterrace','npc','$mother','$father','$liege','$familyid',NOW(),'$liegebirthplace','$currowner','$liegebirthplace','$liegenationality')";
		 		mysqli_query($mysqli, $sql);
				$lastid = $mysqli->insert_id;
				//$_SESSION['usercharacterid'] = $lastid;
				
				//add traits to child
				addtraitstochild($lastid, 1);
				
				//add child trait
				$result4 = $mysqli->query("SELECT * FROM traits WHERE name='child'") or die($mysqli->error());
				$row4 = mysqli_fetch_array($result4);
				$childtraitid = $row4['id'];
				
				$sql = "INSERT INTO traitscharacters (characterid, traitid, date) " 
				. "VALUES ('$lastid','$childtraitid',NOW())";
		 		mysqli_query($mysqli, $sql);
				
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
				
				$content= "You gave birth to a $nsex child";
				$sql = "INSERT INTO events (date, content, extrainfo) " 
			     . "VALUES (NOW(),'$content','$motheruser')";
				mysqli_query($mysqli2, $sql);
				
				$content= "Your spouse gave birth to a $nsex child";
				$sql = "INSERT INTO events (date, content, extrainfo) " 
			     . "VALUES (NOW(),'$content','$fatheruser')";
				mysqli_query($mysqli2, $sql);
				
			}
		}
	}
	
	//chance to survive
	$life = rand(1, 100) > $c; 
	if($life==1){
		$sql = "UPDATE users SET inactive='0', age='$characterage', ageup='1' WHERE username='$characteruser'";
		mysqli_query($mysqli, $sql);
		
		if($characterage == 18){
			//delete child trait
			$result4 = $mysqli->query("SELECT * FROM traits WHERE name='child'") or die($mysqli->error());
			$row4 = mysqli_fetch_array($result4);
			$childtraitid = $row4['id'];
			
			$sql = "DELETE FROM traitscharacters WHERE characterid='$characterid' AND traitid = '$childtraitid'";
			mysqli_query($mysqli, $sql);
			
			$result4 = $mysqli->query("SELECT * FROM traits WHERE name='orphan'") or die($mysqli->error());
			$row4 = mysqli_fetch_array($result4);
			$orphantraitid = $row4['id'];
			
			$sql = "UPDATE traitscharacters SET extrainfo= NULL WHERE characterid='$characterid' AND traitid = '$orphantraitid'";
			mysqli_query($mysqli, $sql);
      
			$result4 = $mysqli->query("SELECT * FROM traits WHERE name='homosexual'") or die($mysqli->error());
			$row4 = mysqli_fetch_array($result4);
			$homotraitid = $row4['id'];
      
			$sql = "UPDATE traitscharacters SET invissible= '0' WHERE characterid='$characterid' AND traitid = '$homotraitid'";
			mysqli_query($mysqli, $sql);
			
			//set name if not named
			if($charactername == NULL){//update name
				//count lines in txt file
				$file="../names/first-names.txt";
				$linecount = 0;
				$handle = fopen($file, "r");
				while(!feof($handle)){
				  $line = fgets($handle);
				  $linecount++;
				}
				fclose($handle);
				$rname=rand(1, $linecount);
				
				//slect firstname
				$file="../names/first-names.txt";
				$linecount = 0;
				$handle = fopen($file, "r");
				while(!feof($handle)){
				  $line = fgets($handle);
				  if($linecount == $rname){
				  	$firstname=$line;
				  }
				  $linecount++;
				}
				fclose($handle);
				
				$sql = "UPDATE characters SET name='$firstname' WHERE id='$characterid'";
				mysqli_query($mysqli, $sql);
			}
		}
		
		$sql = "UPDATE characters SET age='$characterage' WHERE id='$characterid'";
		mysqli_query($mysqli, $sql);
	}else{
		characterdies($characterid);
	}
}

//update marriageproposal
date_default_timezone_set('UTC'); //current date
$datecur = date("Y-m-d H:i:s"); 

$result2 = "SELECT * FROM marriageproposal";
$rs_result = $mysqli->query($result2);
$count = $rs_result->num_rows;
while($row = $rs_result->fetch_assoc()) {
	$proposalid=$row["id"];
	$proposaldate=$row["date"];
	$candidate1id=$row["candidate1"];
	$candidate1liege=$row["candidate1liege"];
	$candidate1accept=$row["candidate1accept"];
	$candidate2id=$row["candidate2"];
	$candidate2liege=$row["candidate2liege"];
	$candidate2accept=$row["candidate2accept"];
	$matrilineal=$row["matrilineal"];

	$date1=$proposaldate; //date voor country1
	//echo date('Y-m-d H:i:s', strtotime($date1. ' + 1 days'));
	$date = new DateTime($date1);
	$date->add(new DateInterval('P5D')); // P1D means a period of 1 day
	$Datenew1 = $date->format('Y-m-d H:i:s');
	if($datecur > $Datenew1 AND ($candidate1accept != 2 OR $candidate2accept != 2)){//accept marriage na 5 dagen als er geen decline is gezegd
		$result4 = "SELECT * FROM marriageproposal WHERE id = '$proposalid'";//check if proposal is deleted during this loop
		$rs_result4 = $mysqli->query($result4);
		$count4 = $rs_result4->num_rows;
		
		if($count4 != 0){
			if($matrilineal == 1){
				$sql = "UPDATE characters SET married='$candidate2id', matrilineal='1' WHERE id='$candidate1id'";
				mysqli_query($mysqli, $sql);
				
				$sql = "UPDATE characters SET married='$candidate1id', matrilineal='1' WHERE id='$candidate2id'";
				mysqli_query($mysqli, $sql);
			}elseif($matrilineal == 0){
				$sql = "UPDATE characters SET married='$candidate2id', matrilineal='0' WHERE id='$candidate1id'";
				mysqli_query($mysqli, $sql);
				
				$sql = "UPDATE characters SET married='$candidate1id', matrilineal='0' WHERE id='$candidate2id'";
				mysqli_query($mysqli, $sql);
			}
	
			$sql = "DELETE FROM marriageproposal WHERE id='$proposalid' OR candidate1='$candidate1id' OR candidate1='$candidate2id' OR candidate2='$candidate1id' OR candidate2='$candidate2id'";
			mysqli_query($mysqli, $sql);
			
			$result5 = $mysqli->query("SELECT * FROM characters WHERE id='$candidate1id'") or die($mysqli->error());
			$row5 = mysqli_fetch_array($result5);
			$candidate1user = $row5['user'];
			$candidate1name = $row5['name'];
			
			$result5 = $mysqli->query("SELECT * FROM characters WHERE id='$candidate2id'") or die($mysqli->error());
			$row5 = mysqli_fetch_array($result5);
			$candidate2user = $row5['user'];
			$candidate2name = $row5['name'];
			
			$content= "<a href='account.php?user=$candidate1user&charid=$candidate1id'>$candidate1name</a> and <a href='account.php?user=$candidate2user&charid=$candidate2id'>$candidate2name</a> are now married";
			$content=$mysqli->escape_string($content);
			$sql = "INSERT INTO events (date, content, extrainfo) " 
		     . "VALUES (NOW(),'$content','$candidate1user')";
			mysqli_query($mysqli2, $sql);
			
			$content= "<a href='account.php?user=$candidate1user&charid=$candidate1id'>$candidate1name</a> and <a href='account.php?user=$candidate2user&charid=$candidate2id'>$candidate2name</a> are now married";
			$content=$mysqli->escape_string($content);
			$sql = "INSERT INTO events (date, content, extrainfo) " 
		     . "VALUES (NOW(),'$content','$candidate2user')";
			mysqli_query($mysqli2, $sql);
		}
	}
}

//statistics for country tax en citizens
$result = $mysqli->query("SELECT country, citizens FROM countryinfo") or die($mysqli->error());
//voor elk land
while($row=mysqli_fetch_array($result)) {
	$countryname=$row["country"];
	$citizens=$row["citizens"];
	
	$sql = "INSERT INTO statistics (type, datestat, waarde, name) " 
     . "VALUES ('countrycitizens',NOW() - INTERVAL 1 DAY,'$citizens','$countryname')";
	mysqli_query($mysqli2, $sql);
	
	$countrytaxtoday=0;
	
	//tell elke regio bij elkaar op
	$result2 = $mysqli->query("SELECT name, taxtoday FROM region WHERE curowner='$countryname'") or die($mysqli->error());
	while($row2=mysqli_fetch_array($result2)) {
		$regioname=$row2["name"];
		$taxtoday=$row2["taxtoday"];
		
		$countrytaxtoday = $countrytaxtoday + $taxtoday;
	}
	
	$sql = "INSERT INTO statistics (type, datestat, waarde, name) " 
     . "VALUES ('countrytax',NOW(),'$countrytaxtoday','$countryname')";
	mysqli_query($mysqli2, $sql);
}

//religionstats
$result = $mysqli->query("SELECT name, gold FROM religion WHERE type='religion'") or die($mysqli->error());
//voor elk land
while($row=mysqli_fetch_array($result)) {
	$religionname=$row["name"];
	$religiongold=$row["gold"];
	$result2 = $mysqli->query("SELECT id FROM users WHERE userreligion='$religionname'") or die($mysqli->error());
	$count = $result2->num_rows;
	
	$sql = "INSERT INTO statistics (type, datestat, waarde, name) " 
     . "VALUES ('religionfollowers',NOW(),'$count','$religionname')";
	mysqli_query($mysqli2, $sql);
	
	$sql = "INSERT INTO statistics (type, datestat, waarde, name) " 
     . "VALUES ('religiongold',NOW(),'$religiongold','$religionname')";
	mysqli_query($mysqli2, $sql);
}

//verwijder statistics na 7 dagen
$sql = "DELETE FROM statistics WHERE NOW() > DATE_ADD(datestat, INTERVAL 30 DAY)";
mysqli_query($mysqli2, $sql);

//zet tax minoneday
$sql = "UPDATE region SET taxminoneday=taxtoday";
mysqli_query($mysqli, $sql);

$sql = "UPDATE region SET taxtoday='0'";
mysqli_query($mysqli, $sql);

//op tweede dag stemmen tellen
if($day==2){
	//calculate election results
	$result = mysqli_query($mysqli,"SELECT country FROM countryinfo");
	$columnValues = Array();
	while ( $row = mysqli_fetch_assoc($result) ) {
		$columnValues[] = $row['country'];
	}
	
	//run for every country
	foreach($columnValues as $item){
		$b=0;
		$candfinal= NULL;
		$result = $mysqli->query("SELECT candidate, countryel, votes FROM elections WHERE type='country'") or die($mysqli->error());
		
		//for every row in elections table check if country matches and then count most votes
		while($row=mysqli_fetch_array($result)) {
			//echo "id: " . $row["candidate"]. " - Name: " . $row["countryel"]. " " . $row["votes"]. "<br>";
	
			$coun=$row["countryel"];
			$coun2=$item;
			$cand=$row["candidate"];
				
			if($coun==$coun2){
				$a=$row["votes"];
				//echo "testi";
					if($a>$b){
						$b=$a;
						$candfinal=$cand;
					}
			}
		}
		
		$result2 = $mysqli->query("SELECT * FROM countryinfo WHERE country='$coun2'") or die($mysqli->error());
		$row2 = mysqli_fetch_array($result2);
		$government = $row2['government'];
		$countryid = $row2['id'];
		echo "$government";
		
		//only if democracy update president
		if($government==1){
			echo "$candfinal";
			
			//update characterowner
			$result4 = $mysqli->query("SELECT * FROM characters WHERE user='$candfinal' AND alive='1' LIMIT 1") or die($mysqli->error());
			$row4 = mysqli_fetch_array($result4);
			$countnumbercharacters = $result4->num_rows;
			$charactercandfinalid = $row4['id'];
			
			$sql = "UPDATE countryinfo SET countrypresident='$candfinal', characterowner='$charactercandfinalid' WHERE country='$coun2'";
			mysqli_query($mysqli, $sql);
			
			$sql = "UPDATE titles SET holderid='$charactercandfinalid' WHERE holdingid='$countryid'";
			mysqli_query($mysqli, $sql);
			
			$content= "The presidential election of $coun2 has been won by $candfinal";
			$sql = "INSERT INTO events (date, content) " 
		     . "VALUES (NOW(),'$content')";
			mysqli_query($mysqli2, $sql);
			
			$content= "You have won the country president elections of $coun2 and earned 5 gold";
			$sql = "INSERT INTO events (date, content, extrainfo) " 
		     . "VALUES (NOW(),'$content','$candfinal')";
			mysqli_query($mysqli2, $sql);
			
			$result = $mysqli->query("SELECT gold FROM currency WHERE usercur='$candfinal'") or die($mysqli->error());
			$row = mysqli_fetch_array($result);
			
			$gold=$row['gold'];
			$gold=$gold+5;
			
			$sql = "UPDATE currency SET gold='$gold' WHERE usercur='$candfinal'";
			mysqli_query($mysqli, $sql);
			
			$result = $mysqli->query("SELECT politicalparty FROM users WHERE username='$candfinal'") or die($mysqli->error());
			$row = mysqli_fetch_array($result);
			$politicalparty=$row['politicalparty'];
			
			if($politicalparty != 0){
				$result = $mysqli->query("SELECT gold FROM politicalparty WHERE id='$politicalparty'") or die($mysqli->error());
				$row = mysqli_fetch_array($result);
				$partygold=$row['gold'];
				
				$partygold=$partygold+5;
				$sql = "UPDATE politicalparty SET gold = '$partygold' WHERE id='$politicalparty'";
				mysqli_query($mysqli, $sql);								
			}
		}
	}
	//clear table
	$sql = "DELETE FROM elections WHERE type='country'";
	mysqli_query($mysqli, $sql);
		
	//countrypresident decisions
	$sql = "UPDATE countryinfo SET nodecisions = '0', moneycreation='0', changedgov='0', finance='NULL', foreignaffairs='NULL', immigration='NULL', changedrel='0'";
	mysqli_query($mysqli, $sql);
	
	//voted=0
	$sql = "UPDATE users SET voted = '0'";
	mysqli_query($mysqli, $sql);
	
	//set advertisement to 0
	$sql4 = "UPDATE politicalparty SET ad = '0'";
	mysqli_query($mysqli, $sql4);
}

//16de dag congress tellen
if($day==16){
	$congresssize=12;
	
	$sql4 = "UPDATE users SET congressmember = '0'";
	mysqli_query($mysqli, $sql4);
	
	$sql4 = "UPDATE politicalparty SET ad = '0'";
	mysqli_query($mysqli, $sql4);
	
	$sql = "SELECT country, totalcongressel FROM countryinfo";
	$rs_result = $mysqli->query($sql);
	
	while($row = $rs_result->fetch_assoc()) {//go through all countries
		$country=$row["country"];
		$totalcongressel=$row["totalcongressel"];
		
		$votesperseat=$totalcongressel/$congresssize;//aantal stemmen per zetel
		$freeseats=$congresssize;
		
		$sql2 = "SELECT * FROM politicalparty WHERE country='$country' ORDER BY congressvotes DESC";
		$rs_result2 = $mysqli->query($sql2);
		
		while($row2 = $rs_result2->fetch_assoc()) { //go through all parties
			$id=$row2["id"];
			$congressvotes=$row2["congressvotes"];
			$partygold=$row2["gold"];
			
			$seats=intval($congressvotes/$votesperseat);//aantal zetels pert partij
			
			$sql3 = "SELECT candidate FROM elections WHERE type='congress' AND party='$id' ORDER BY electorder ASC LIMIT $seats";
			$rs_result3 = $mysqli->query($sql3);
			
			while($row3 = $rs_result3->fetch_assoc()) { //go through all winners of seats
				$candidate=$row3["candidate"];
			
				$result4 = $mysqli->query("SELECT * FROM users WHERE username='$candidate'") or die($mysqli->error());
				$row4 = mysqli_fetch_array($result4);
				$nationality = $row4['nationality'];
				
				if($country==$nationality AND $freeseats > 0){
					$freeseats=$freeseats-1;
					
					$sql4 = "UPDATE users SET congressmember = '$country' WHERE username = '$candidate'";
					mysqli_query($mysqli, $sql4);
					
					$content= "You have become a congressmember of $country and earned 3 gold";
					$sql = "INSERT INTO events (date, content, extrainfo) " 
				     . "VALUES (NOW(),'$content','$candfinal')";
					mysqli_query($mysqli2, $sql);
					
					$result4 = $mysqli->query("SELECT * FROM currency WHERE usercur='$candidate'") or die($mysqli->error());
					$row4 = mysqli_fetch_array($result4);
					$gold = $row4['gold'];					
					$gold=$gold+3;
					$sql4 = "UPDATE currency SET gold = '$gold' WHERE usercur = '$candidate'";
					mysqli_query($mysqli, $sql4);
					
					$partygold=$partygold+2;
				}
			}
			$sql4 = "UPDATE politicalparty SET gold = '$partygold' WHERE id = '$id'";
			mysqli_query($mysqli, $sql4);
		}
	}
	$sql4 = "UPDATE politicalparty SET runcongress = '0', congressvotes = '0'";
	mysqli_query($mysqli, $sql4);
	
	$sql4 = "UPDATE countryinfo SET totalcongressel = '0'";
	mysqli_query($mysqli, $sql4);
	
	$sql = "DELETE FROM elections WHERE type = 'congress'";
	mysqli_query($mysqli, $sql);
}

if($day==9){
	$sql4 = "UPDATE politicalparty SET partypresidentel = '0' WHERE structure='1'";
	mysqli_query($mysqli, $sql4);
}

//lottery
if($day==15){
	$result = mysqli_query($mysqli,"SELECT username FROM users WHERE lottery='1'");
	
	for ($set=array(); $row=$result->fetch_assoc(); $set[]=$row);
	//print_r($set);
	$r=array_rand($set,1);
	//print_r($set[$r]);
	$win=$set[$r];
	
	foreach($win as $item){
		$winner=$item;	
	}
	
	$l=0;
	foreach ($set as $item) {
		$l=$l+1;
	}
	$result = $mysqli->query("SELECT gold FROM currency WHERE usercur='$winner'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$gold = $row['gold'];
	
	$price=$l*0.9;
	$gold=$gold+$price;
	$sql = "UPDATE currency SET gold='$gold' WHERE usercur='$winner'";
	mysqli_query($mysqli, $sql);
	
	$sql = "UPDATE users SET lottery = '0'";
	mysqli_query($mysqli, $sql);
	
	$content= "The lottery has been won by $winner. $winner has won $price gold.";
	$sql = "INSERT INTO events (date, content) " 
     . "VALUES (NOW(),'$content')";
	mysqli_query($mysqli2, $sql);
}

//update nap
$result = $mysqli->query("SELECT id FROM diplomacy WHERE type='nap'") or die($mysqli->error());
for ($set=array(); $row=$result->fetch_assoc(); $set[]=$row);
foreach ($set as $key => $value) {
	$id[$key] = $value['id'];
	
	$result = $mysqli->query("SELECT * FROM diplomacy WHERE id='$id[$key]'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$date1 = $row['date'];
	$country1 = $row['country1'];
	$country2 = $row['country2'];
	
	//check dates
	//echo date('Y-m-d H:i:s', strtotime($date1. ' + 1 days'));
	$date = new DateTime($date1);
	$date->add(new DateInterval('P30D')); // P1D means a period of 1 day
	$Datenew1 = $date->format('Y-m-d H:i:s');
	
	date_default_timezone_set('UTC'); //current date
	$datecur = date("Y-m-d H:i:s"); 
	
	if($datecur>$Datenew1){
		$sql = "DELETE FROM diplomacy WHERE id='$id[$key]'";
		mysqli_query($mysqli, $sql);
		
		$content= "The NAP between $country1 and $country2 has ended";
		$sql = "INSERT INTO events (date, content) " 
	     . "VALUES (NOW(),'$content')";
		mysqli_query($mysqli2, $sql);
	}
}

//bans updaten
$sql = "DELETE FROM ban WHERE date<NOW()";
mysqli_query($mysqli, $sql);

//update events
//select id from ban where NOW() > DATE_ADD(date, INTERVAL 2 DAY)
$sql = "DELETE FROM events WHERE NOW() > DATE_ADD(date, INTERVAL 14 DAY)";
mysqli_query($mysqli2, $sql);

//update chat
//select id from ban where NOW() > DATE_ADD(date, INTERVAL 2 DAY)
$sql = "DELETE FROM chat WHERE NOW() > DATE_ADD(date, INTERVAL 7 DAY)";
mysqli_query($mysqli2, $sql);

//update messages
//select id from ban where NOW() > DATE_ADD(date, INTERVAL 2 DAY)
$sql = "DELETE FROM messages WHERE NOW() > DATE_ADD(date, INTERVAL 60 DAY)";
mysqli_query($mysqli2, $sql);

//update users inactive
$result = $mysqli->query("SELECT username FROM users WHERE NOW() > DATE_ADD(lastonline, INTERVAL 30 DAY) AND inactive='0'") or die($mysqli->error());
//voor elke citizen
while($row=mysqli_fetch_array($result)) {
	$username=$row["username"];
	
	$sql = "UPDATE users SET inactive='1', militaryunit='0', politicalparty='0', userreligion='NULL', religionorder=NULL WHERE username='$username'";
	mysqli_query($mysqli, $sql);
	
	$sql = "DELETE FROM newsextra WHERE user='$username' AND type='subscription'";
	mysqli_query($mysqli2, $sql);
}

//update religion leader
$result = $mysqli->query("SELECT * FROM religion WHERE type='religion'") or die($mysqli->error());
for ($set=array(); $row=$result->fetch_assoc(); $set[]=$row);
foreach ($set as $key => $value) {
	$leader[$key] = $value['leader'];
	$name[$key] = $value['name'];
	$id[$key] = $value['id'];
	$deathdate[$key] = $value['deathdate'];

	$date = new DateTime($deathdate[$key]);
	$date->add(new DateInterval('P1D')); // P1D means a period of 1 day
	$Datenew1 = $date->format('Y-m-d H:i:s');
	
	date_default_timezone_set('UTC'); //current date
	$datecur = date("Y-m-d H:i:s"); 
	
	if($leader[$key]==NULL OR $leader[$key]=="NULL"){
		$b=0;
		$candfinal= NULL;

		$results_per_page2=10;
		$start_from2=0;
		$sql = "SELECT * FROM religion WHERE (type='order' OR type='secretorder') AND religionid='$id[$key]' ORDER BY donatedgold DESC LIMIT $start_from2, ".$results_per_page2;
		$rs_result = $mysqli->query($sql);
		
		//voor elke order
		$totaldonatedgold=0;
		$donatedgoldarr=array();
		$candidatesarr=array();
		while($row = $rs_result->fetch_assoc()) {
			$donatedgold=$row["donatedgold"];
			$totaldonatedgold=$totaldonatedgold+$donatedgold;
			
			$candidate=$row["nominee"];
			
			if($candidate != NULL OR $candidate != "NULL"){
				array_push($candidatesarr,$candidate);
				array_push($donatedgoldarr,$donatedgold);
			}
		}
		//voor elke candidaat
		$i=0;
		$candidatesarr2=array();
		foreach ($candidatesarr as $cand) {		
			//eerste keer zorgen dat array niet leeg is
			if($i==0){
				array_push($candidatesarr2,$cand);
			}
			//creeer array met unieke kandidaten
			$count=0;
			foreach ($candidatesarr2 as $cand2) {
				if($cand==$cand2){
					$count=$count+1;
				}
			}
			if($count==0){
				array_push($candidatesarr2,$cand);
			}
			$i=$i+1;
		}
		
		//ga door unieke kandidaten lijst en kijk of deze voorkomen in lijst met alle kandidaten; dan geld optellen en in array stoppen
		$donatedgoldarr2=array();
		foreach ($candidatesarr2 as $cand) {
			$donatedgold=0;
			$i=0;
			foreach ($candidatesarr as $cand2) {
				if($cand==$cand2){
					$donatedgold=$donatedgold+$donatedgoldarr[$i];
				}
				$i=$i+1;
			}
			array_push($donatedgoldarr2,$donatedgold);
		}
		
		//kies winnaar
		$i=0;
		$highest=0;
		$winner=0;
		foreach ($donatedgoldarr2 as $donation) {
			if($donation>$highest){
				$highest=$donation;
				$winner=$i;
			}
			$i=$i+1;
		}
		
		$finalwinner=$candidatesarr2[$winner]; //winnaar
		
		if($finalwinner != NULL){
			$result = $mysqli->query("SELECT gold FROM currency WHERE usercur='$finalwinner'") or die($mysqli->error());
			$row = mysqli_fetch_array($result);
			$gold=$row['gold'];
			$gold=$gold+5;
			
			$sql = "UPDATE currency SET gold='$gold' WHERE usercur='$finalwinner'";
			mysqli_query($mysqli, $sql);
			
			$sql = "UPDATE religion SET leader='$finalwinner', deathdate='2099-01-01 00:00:00', changedtax='0' WHERE name='$name[$key]' AND type='religion'";
			mysqli_query($mysqli, $sql);
			
			$sql = "UPDATE religion SET nominee=NULL, donatedgold='0' WHERE religionid='$id[$key]' AND (type='order' OR type='secretorder')";
			mysqli_query($mysqli, $sql);
			
			//add trait
			$result4 = $mysqli->query("SELECT * FROM characters WHERE user='$finalwinner' AND alive='1'") or die($mysqli->error());
			$row4 = mysqli_fetch_array($result4);
			$relleadercharacterid = $row4['id'];
		
			$result4 = $mysqli->query("SELECT * FROM traits WHERE name='religious leader'") or die($mysqli->error());
			$row4 = mysqli_fetch_array($result4);
			$relleadertraitid = $row4['id'];
			
			$sql = "INSERT INTO traitscharacters (characterid, traitid, date, extrainfo) " 
			. "VALUES ('$relleadercharacterid','$relleadertraitid',NOW(),'$name[$key]')";
	 		mysqli_query($mysqli, $sql);
			
			//add messages
			$content= "The elections for new religious leader of $name[$key] has been won by $finalwinner";
			$sql = "INSERT INTO events (date, content) " 
		     . "VALUES (NOW(),'$content')";
			mysqli_query($mysqli2, $sql);
			
			$content= "You have become religious leader of $name[$key] and earned 5 gold";
			$sql = "INSERT INTO events (date, content, extrainfo) " 
		     . "VALUES (NOW(),'$content','$finalwinner')";
			mysqli_query($mysqli2, $sql);
		}
		
	}elseif($leader[$key] != "NULL"){
		//niet meer nodig gaat automatisch nu
		/*
		$result = $mysqli->query("SELECT lastonline FROM users WHERE username='$leader[$key]'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$lastonline=$row['lastonline'];
		
		$date = new DateTime($lastonline);
		$date->add(new DateInterval('P7D')); // P1D means a period of 1 day
		$Datenew1 = $date->format('Y-m-d H:i:s');
		
		date_default_timezone_set('UTC'); //current date
		$datecur = date("Y-m-d H:i:s");
		
		if($datecur > $Datenew1){
			$sql = "UPDATE religion SET leader =NULL, deathdate='$datecur', changedtax='0', crusadeup='0', crusade='NULL' WHERE name='$name[$key]'";
			mysqli_query($mysqli, $sql);
		}
		*/
	}
}


//update relics owner gaat automatisch nu
/*
$result = $mysqli->query("SELECT id, owner FROM relics") or die($mysqli->error());
for ($set=array(); $row=$result->fetch_assoc(); $set[]=$row);
foreach ($set as $key => $value) {
	$id[$key] = $value['id'];
	$owner[$key] = $value['owner'];
	
	$result2 = $mysqli->query("SELECT lastonline FROM users WHERE username='$owner[$key]'") or die($mysqli->error());
	$row2 = mysqli_fetch_array($result2);
	$lastonline=$row2['lastonline'];
	
	$date = new DateTime($lastonline);
	$date->add(new DateInterval('P5D')); // P1D means a period of 1 day
	$Datenew1 = $date->format('Y-m-d H:i:s');
	
	date_default_timezone_set('UTC'); //current date
	$datecur = date("Y-m-d H:i:s");
	
	if($datecur > $Datenew1){
		$sql = "UPDATE relics SET owner='NULL', location='NULL' WHERE owner='$owner[$key]'";
		mysqli_query($mysqli, $sql);
	}
}
*/

//update region religion
$result = $mysqli->query("SELECT name FROM religion WHERE id='1'") or die($mysqli->error());
$row = mysqli_fetch_array($result);
$name1=$row['name'];

$result = $mysqli->query("SELECT name FROM religion WHERE id='2'") or die($mysqli->error());
$row = mysqli_fetch_array($result);
$name2=$row['name'];

$result = $mysqli->query("SELECT name FROM religion WHERE id='3'") or die($mysqli->error());
$row = mysqli_fetch_array($result);
$name3=$row['name'];

$result = $mysqli->query("SELECT `1`, `2`, `3`, name FROM region") or die($mysqli->error());
for ($set=array(); $row=$result->fetch_assoc(); $set[]=$row);
foreach ($set as $key => $value) {
	$r1[$key] = $value['1'];
	$r2[$key] = $value['2'];
	$r3[$key] = $value['3'];
	$name[$key] = $value['name'];
	$name[$key]=$mysqli->escape_string($name[$key]);
	
	if($r1[$key]>$r2[$key] && $r1[$key]>$r3[$key]){
		$sql = "UPDATE region SET biggestrel='$name1' WHERE name='$name[$key]'";
		mysqli_query($mysqli, $sql);
	}elseif($r2[$key]>$r1[$key] && $r2[$key]>$r3[$key]){
		$sql = "UPDATE region SET biggestrel='$name2' WHERE name='$name[$key]'";
		mysqli_query($mysqli, $sql);
	}elseif($r3[$key]>$r1[$key] && $r3[$key]>$r2[$key]){
		$sql = "UPDATE region SET biggestrel='$name3' WHERE name='$name[$key]'";
		mysqli_query($mysqli, $sql);		
	}
}

//update relic location
$result = $mysqli->query("SELECT location, id FROM relics WHERE location='NULL'") or die($mysqli->error());
for ($set=array(); $row=$result->fetch_assoc(); $set[]=$row);

//create list of regionnames
$result2 = mysqli_query($mysqli,"SELECT name FROM region");
for ($set2=array(); $row2=$result2->fetch_assoc(); $set2[]=$row2);

foreach ($set as $key => $value) {
	$reliclocation[$key] = $value['location'];
	$id[$key] = $value['id'];
	
	if($reliclocation[$key]=='NULL'){
		$r=array_rand($set2,1); //select random position in list
		$win=$set2[$r]; //select winner
		
		//get winner from list
		foreach($win as $item){
			$winner=$mysqli->escape_string($item);
		}
		
		$sql = "UPDATE relics SET location='$winner' WHERE id='$id[$key]'";
		mysqli_query($mysqli, $sql);	
	}
}

//number of citizens
$result = mysqli_query($mysqli,"SELECT country FROM countryinfo");
$columnValues = Array();
while ( $row = mysqli_fetch_assoc($result) ) {
	$columnValues[] = $row['country'];
}
	
//run for every country
foreach($columnValues as $item){
	$country=$item;
	
	$sql = "SELECT COUNT(id) AS total FROM characters WHERE nationality='$country' AND alive='1'";
	$result = $mysqli->query($sql);
	$row = $result->fetch_assoc();
	$total_citizens = $row["total"];
	
	$sql = "UPDATE countryinfo SET citizens='$total_citizens' WHERE country='$country'";
	mysqli_query($mysqli, $sql);	
	
}

//epidemic spread
$result4 = "SELECT * FROM region WHERE epidemic ='1'";
$rs_result3 = $mysqli->query($result4);
$count3 = $rs_result3->num_rows;//aantal epidemic regions

if($count3 != 0){//als er epidemics zijn
	while($row3 = $rs_result3->fetch_assoc()) {//ga door kinderen heen
		$regionid=$row3["id"];
		$regionname=$row3["name"];
		
		$spreadchance=rand(1, 7);
		$curechance=rand(1, 14);
		echo "curechance: $curechance , spreadchance: $spreadchance";
		if($spreadchance == 1){
			$borderregions=array();
			foreach ($borders as $key => $value) {
				$name[$key] = $value['name'];
				$border1[$key]=$value['border1'];
				$border2[$key]=$value['border2'];
				$border3[$key]=$value['border3'];
				$border4[$key]=$value['border4'];
				$border5[$key]=$value['border5'];
				
				//select all borders of region
				if($name[$key]==$regionname){
					if($border1[$key] != "NULL"){array_push($borderregions,$border1[$key]);}
					if($border2[$key] != "NULL"){array_push($borderregions,$border2[$key]);}
					if($border3[$key] != "NULL"){array_push($borderregions,$border3[$key]);}
					if($border4[$key] != "NULL"){array_push($borderregions,$border4[$key]);}
					if($border5[$key] != "NULL"){array_push($borderregions,$border5[$key]);}
				}			
			}
			
			$newregionselect=array_rand($borderregions);
			$newregionname=$borderregions[$newregionselect];
			$newregionname=$mysqli->escape_string($newregionname);
			
			$result2 = $mysqli->query("SELECT * FROM region WHERE name='$newregionname'") or die($mysqli->error());
			$row2 = mysqli_fetch_array($result2);
			$alreadyepidemic = $row2['epidemic'];
			$newregionepidemiccured=$row2["epidemiccured"];
			
			if($alreadyepidemic == 0){
				if($newregionepidemiccured == 0){//check if already had epidemic
					$sql = "UPDATE region SET epidemic='1' WHERE name='$newregionname'";
					mysqli_query($mysqli, $sql);
					
					$regionname=$mysqli->escape_string($regionname);
					$content= "The ongoing epidemic has spread from $regionname to $newregionname";
					$sql = "INSERT INTO events (date, content) " 
				     . "VALUES (NOW(),'$content')";
					mysqli_query($mysqli2, $sql);
				}else{
					//echo "test niet";
				}
			}
		}
		if($curechance == 1){
			$sql = "UPDATE region SET epidemic='0', epidemiccured='1' WHERE id='$regionid'";
			mysqli_query($mysqli, $sql);
			
			$regionname=$mysqli->escape_string($regionname);
			$content= "The ongoing epidemic has dissolved from $regionname";
			$sql = "INSERT INTO events (date, content) " 
		     . "VALUES (NOW(),'$content')";
			mysqli_query($mysqli2, $sql);
		}
	}
}

//epidemic in new region
$result3 = "SELECT * FROM region WHERE epidemic='1'";
$rs_result2 = $mysqli->query($result3);
$epidemiccount = $rs_result2->num_rows;//aantal titles

if($epidemiccount == 0){//if no epidemic in world reset all epidemiccured regions so new epidemic can spread
	$sql = "UPDATE region SET epidemiccured='0'";
	mysqli_query($mysqli, $sql);
}

$epidemicchance = rand(1, 365);
if($epidemicchance <= 5 AND $epidemiccount == 0){
	$result2 = mysqli_query($mysqli,"SELECT name FROM region");
	for ($set2=array(); $row2=$result2->fetch_assoc(); $set2[]=$row2);
		
	$r=array_rand($set2,1); //select random position in list
	$win=$set2[$r]; //select winner
	
	//get winner from list
	foreach($win as $item){
		$winner=$item;
	}
	
	$winner=$mysqli->escape_string($winner);
	$sql = "UPDATE region SET epidemic='1' WHERE name='$winner'";
	mysqli_query($mysqli, $sql);
	
	$content= "An epidemic has emerged in $winner";
	$content=$mysqli->escape_string($content);
	$sql = "INSERT INTO events (date, content) " 
     . "VALUES (NOW(),'$content')";
	mysqli_query($mysqli2, $sql);
		
	
}

?>

</body>
</html>
