<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//wayoflife.php account.php
function seteventuser($content, $username){
	require 'db.php';
	
	$content=$mysqli->escape_string($content);
	$sql = "INSERT INTO events (date, content, extrainfo) " 
     . "VALUES (NOW(),'$content','$username')";
	mysqli_query($mysqli2, $sql);
}

//account.php wayoflife.php
function selectwayoflifecharacter($characterid){
	require 'db.php';
	
	$characterwayoflife = "none";
	
	$result3 = "SELECT * FROM traits WHERE type = 'way of life'";
	$rs_result = $mysqli->query($result3);
	$count = $rs_result->num_rows;
	
	if($count != 0){
		while($row = $rs_result->fetch_assoc()) {//go through all traits
			$traitid=$row["id"];
			$traitname=$row["name"];
			
			$result4 = "SELECT * FROM traitscharacters WHERE traitid = '$traitid' AND characterid='$characterid'";
			$rs_result2 = $mysqli->query($result4);
			$count2 = $rs_result2->num_rows;
			
			if($count2 != 0){
				$characterwayoflife = $traitname;
			}
		}
	}

	return $characterwayoflife;
}

//wayoflife.php
function selectwayoflifeid($characterid){
	require 'db.php';
	
	$characterwayoflife = "none";
	
	$result3 = "SELECT * FROM traits WHERE type = 'way of life'";
	$rs_result = $mysqli->query($result3);
	$count = $rs_result->num_rows;
	
	if($count != 0){
		while($row = $rs_result->fetch_assoc()) {//go through all traits
			$traitid=$row["id"];
			$traitname=$row["name"];
			
			$result4 = "SELECT * FROM traitscharacters WHERE traitid = '$traitid' AND characterid='$characterid'";
			$rs_result2 = $mysqli->query($result4);
			$count2 = $rs_result2->num_rows;
			
			if($count2 != 0){
				$wayoflifeid = $traitid;
			}
		}
	}

	return $wayoflifeid;
}

function selectrandomneighbouringregion($regionname){
	require 'db.php';
	require 'regionborders.php';
	
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
	
	return $newregionname;
}

//wayoflife.php
function selecthighesttitle($characterid){
	require 'db.php';
	
	$result3 = "SELECT * FROM titles WHERE holderid = '$characterid'";
	$rs_result = $mysqli->query($result3);
	$count = $rs_result->num_rows;
	
	$highestitle=0;
	if($count != 0){
		while($row = $rs_result->fetch_assoc()) {
			$titleid=$row["id"];
			$titletype=$row["holdingtype"];
			if($titletype=="kingdom"){
				if($highestitle < 10){
					$highestitle = 10;
				}
			}elseif($titletype=="duchy"){
				if($highestitle < 9){
					$highestitle = 9;
				}
			}
		}
	}else{
		$highestitle = 0;
	}
	
	$result4 = $mysqli->query("SELECT * FROM traits WHERE name='religious leader'") or die($mysqli->error());
	$row4 = mysqli_fetch_array($result4);
	$relleadertraitid = $row4['id'];
	
	$result4 = $mysqli->query("SELECT * FROM traits WHERE name='archprelate'") or die($mysqli->error());
	$row4 = mysqli_fetch_array($result4);
	$archprelatetraitid = $row4['id'];
	
	$result3 = "SELECT * FROM traitscharacters WHERE characterid = '$characterid' AND traitid = '$relleadertraitid'";
	$rs_result = $mysqli->query($result3);
	$count = $rs_result->num_rows;
	
	$result3 = "SELECT * FROM traitscharacters WHERE characterid = '$characterid' AND traitid = '$archprelatetraitid'";
	$rs_result = $mysqli->query($result3);
	$count2 = $rs_result->num_rows;
	
	if($count != 0){//religious leader
		if($highestitle < 10){
			$highestitle = 10;
		}
	}elseif($count2 != 0){//archprelate
		if($highestitle < 9){
			$highestitle = 9;
		}
	}
	
	return $highestitle;
}

//functions.php update.php
function checkifcharacteralreadyhastrait($characterid, $traitid){
  $result3 = "SELECT * FROM traitscharacters WHERE characterid = '$characterid' AND traitid = '$traitid'";
	$rs_result = $mysqli->query($result3);
	$count = $rs_result->num_rows;
  
  if($count > 0){
      return 1;
  }
  
  return 0;
}

//functions.php update.php
function removedoubleemotiontrait($characterid, $keeptraitid){
	$result3 = "SELECT * FROM traits WHERE type = 'emotion' AND id != '$keeptraitid'";
	$rs_result = $mysqli->query($result3);
	$count = $rs_result->num_rows;
  
	if($count != 0){
		while($row = $rs_result->fetch_assoc()) {//go through all traits
			$traitid=$row["id"];
			$traitname=$row["name"];
			
      $sql = "DELETE FROM traitscharacters WHERE traitid='$traitid' AND characterid='$characterid' ";
      mysqli_query($mysqli, $sql);
		}
	}
}

//functions.php update.php
function checkantitrait($characterid, $traitid){
	$result2 = $mysqli->query("SELECT * FROM traits WHERE id='$traitid'") or die($mysqli->error());
	$row2 = mysqli_fetch_array($result2);
	$antitraitname = $row2['antitrait'];
  
	$result2 = $mysqli->query("SELECT * FROM traits WHERE name='$antitraitname'") or die($mysqli->error());
	$row2 = mysqli_fetch_array($result2);
	$antitraitid = $row2['id'];
  
	$result3 = "SELECT * FROM traitscharacters WHERE traitid = '$antitraitid' AND characterid = '$characterid'";
	$rs_result = $mysqli->query($result3);
	$count = $rs_result->num_rows;
  
  if($count > 0){//character has antitrait so new trait cant be added
      return 1;
  }
  
  return 0;
}

//account.php ageing.php update.php
function addtraitstochild($characterid, $child = 0){
    $result3 = "SELECT * FROM traits";
    $rs_result2 = $mysqli->query($result3);
    $count2 = $rs_result2->num_rows;//aantal titles
    while($row2 = $rs_result2->fetch_assoc()) {//ga door traits heen
      $traitid=$row2["id"];
      $traitbirthchance=$row2["birthchance"];
      $traitname=$row2["name"];
      
      if($traitname == "homosexual" && $child == 1){
          $invissible = 1;
      }else{
          $invissible = 0;
      }
      
      if($traitbirthchance != 0){
        $randnumber = rand(0, 100);
        if($randnumber <= $traitbirthchance){
          $sql = "INSERT INTO traitscharacters (characterid, traitid, date, invissible) " 
          . "VALUES ('$characterid','$traitid',NOW(), '$invissible')";
          mysqli_query($mysqli, $sql);
        }
      }
    }
    
    if($child == 1){
      //add child trait
      $result4 = $mysqli->query("SELECT * FROM traits WHERE name='child'") or die($mysqli->error());
      $row4 = mysqli_fetch_array($result4);
      $childtraitid = $row4['id'];

      $sql = "INSERT INTO traitscharacters (characterid, traitid, date) " 
      . "VALUES ('$characterid','$childtraitid',NOW())";
      mysqli_query($mysqli, $sql);
    }
}

//update.php wayoflife.php
function characterdies($characterid){
	require 'db.php';
	require 'regionborders.php';
	
	$result = $mysqli->query("SELECT * FROM characters WHERE id='$characterid'") or die($mysqli->error());
	$rowimportant = mysqli_fetch_array($result);
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
	
	$sql = "UPDATE users SET inactive='0', age='0', ageup='1', strength='0', dominance='0', energy='100', lastonline=NOW(), housebuilt='0', userreligion='NULL', religionorder='NULL', orderrank='NULL', removereligion='0' WHERE username='$characteruser'";
	mysqli_query($mysqli, $sql);
	
	$sql = "UPDATE traitscharacters SET extrainfo='NULL', extrainfo2='0' WHERE extrainfo='$characteruser'";
	mysqli_query($mysqli, $sql);
	
	//$sql = "UPDATE relics SET owner='NULL', location='NULL' WHERE owner='$characteruser'";
	//mysqli_query($mysqli, $sql);
	
	$sql = "UPDATE characters SET alive='0', age='$characterage', married='0' WHERE id='$characterid'";
	mysqli_query($mysqli, $sql);
	
	$sql = "UPDATE religion SET nominee='NULL' WHERE nominee='$characteruser'";
	mysqli_query($mysqli, $sql);
	
	$sql = "UPDATE religion SET leader='NULL', changedtax='0' WHERE leader='$characteruser' AND type='religion'";
	mysqli_query($mysqli, $sql);
	
	//reset archprelate
	$sql = "UPDATE region SET archprelate=0 WHERE archprelate='$characterid'";
	mysqli_query($mysqli, $sql);
	
	//remove married
	if($charactermarried != 0){
		$sql = "UPDATE characters SET married='0' WHERE id='$charactermarried'";
		mysqli_query($mysqli, $sql);
		
		//check chance to get depressed and sad for married
    $result4 = $mysqli->query("SELECT * FROM traits WHERE name='depressed'") or die($mysqli->error());
    $row4 = mysqli_fetch_array($result4);
    $traitid = $row4['id'];

    if(checkifcharacteralreadyhastrait($charactermarried, $traitid) == 0){
      if(checkantitrait($charactermarried, $traitid) == 0){
        $randdepressed = rand(0, 100);
        if($randdepressed <= 30){
          $sql = "INSERT INTO traitscharacters (characterid, traitid, date, extrainfo) " 
          . "VALUES ('$charactermarried','$traitid',NOW(), '0')";
          mysqli_query($mysqli, $sql);

          $content= "You got depressed";
          $sql = "INSERT INTO events (date, content, extrainfo) " 
             . "VALUES (NOW(),'$content','$marrieduser')";
          mysqli_query($mysqli2, $sql);
        }
      }
    }
    
    $result4 = $mysqli->query("SELECT * FROM traits WHERE name='sad'") or die($mysqli->error());
    $row4 = mysqli_fetch_array($result4);
    $traitid = $row4['id'];
    
    if(checkifcharacteralreadyhastrait($charactermarried, $traitid) == 0){
      $randsad = rand(0, 100);
      if($randsad <= 70){
        $sql = "INSERT INTO traitscharacters (characterid, traitid, date) " 
        . "VALUES ('$charactermarried','$traitid',NOW())";
        mysqli_query($mysqli, $sql);
        
        removedoubleemotiontrait($charactermarried, $traitid);
        
        $content= "You got sad";
        $sql = "INSERT INTO events (date, content, extrainfo) " 
           . "VALUES (NOW(),'$content','$marrieduser')";
        mysqli_query($mysqli2, $sql);
      }
    }
	}
	
	//delete from marriageproposal
	$sql = "DELETE FROM marriageproposal WHERE candidate1='$characterid' OR candidate2='$characterid'";
	mysqli_query($mysqli, $sql);
	
	//delete traits
	$sql = "DELETE FROM traitscharacters WHERE characterid='$characterid'";
	mysqli_query($mysqli, $sql);
	
	$result2 = $mysqli->query("SELECT * FROM users WHERE id='$characteruser'") or die($mysqli->error());
	$row2 = mysqli_fetch_array($result2);
	$userreligion = $row2['userreligion'];
	
	$result = $mysqli->query("SELECT * FROM religion WHERE name='$userreligion'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$leader=$row['leader'];
	
	if($leader==$characteruser){
		date_default_timezone_set('UTC'); //current date
		$datecur = date("Y-m-d H:i:s"); 

		$sql = "UPDATE religion SET leader =NULL, deathdate='$datecur', changedtax='0', crusadeup='0', crusade='NULL' WHERE name='$userreligion'";
		mysqli_query($mysqli, $sql);
	}
	
	//check for titles & update liege & update order owner
	$result = $mysqli->query("SELECT * FROM family WHERE id='$characterfamily'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$heritagelaw=$row['heritagelaw'];
	$familyheir=$row['heir'];
	$familydynast=$row['dynast'];
	
	$result = $mysqli->query("SELECT * FROM characters WHERE id='$familydynast'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$dynastmother=$row['mother'];
	$dynastfather=$row['father'];
	
	$result = $mysqli->query("SELECT * FROM characters WHERE id='$familyheir'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$familyheiruser=$row['user'];
	
	//update religionorder owner en leader naar heir
	$result3 = "SELECT * FROM religion WHERE (type='order' OR type='secretorder') AND leader='$characteruser'";
	$rs_result2 = $mysqli->query($result3);
	$count2 = $rs_result2->num_rows;//aantal titles
	
	if($count2 != 0){
		while($row2 = $rs_result2->fetch_assoc()) {//ga door traits heen
			$order=$row2["id"];
			
			$result4 = $mysqli->query("SELECT * FROM users WHERE religionorder='$order' ORDER BY orderrank DESC LIMIT 1") or die($mysqli->error());
			$row4 = mysqli_fetch_array($result4);
			$newleader=$row4['username'];
			
			$sql = "UPDATE religion SET leader='$newleader' WHERE id = '$order'";
			mysqli_query($mysqli, $sql);
			
			$content= "The previous leader of your order $charactername passes away. Since you were the highest in rank you are now the new leader.";
			$sql = "INSERT INTO events (date, content, extrainfo) " 
		     . "VALUES (NOW(),'$content','$newleader')";
			mysqli_query($mysqli2, $sql);
		}
	}
	
	/*
	if($familyheir != NULL OR $familyheir != "NULL" OR $familyheir != 0){
		$sql = "UPDATE religion SET leader='$familyheiruser', owner='$familyheiruser' WHERE (type='order' OR type='secretorder') AND leader='$characteruser'";
		mysqli_query($mysqli, $sql);
		
		$content= "You inherited a religion order from $charactername";
		$sql = "INSERT INTO events (date, content, extrainfo) " 
	     . "VALUES (NOW(),'$content','$familyheiruser')";
		mysqli_query($mysqli2, $sql);
	}else{
		$sql = "UPDATE religion SET leader='NULL', owner='NULL' WHERE (type='order' OR type='secretorder') AND leader='$characteruser'";
		mysqli_query($mysqli, $sql);
	}
	*/
	
	//update relics owner heir
	if($familyheir != NULL OR $familyheir != "NULL" OR $familyheir != 0){
		$sql = "UPDATE relics SET owner='$familyheiruser' WHERE owner='$characteruser'";
		mysqli_query($mysqli, $sql);
		
		$content= "You inherited a relic from $charactername";
		$sql = "INSERT INTO events (date, content, extrainfo) " 
	     . "VALUES (NOW(),'$content','$familyheiruser')";
		mysqli_query($mysqli2, $sql);
	}else{
		$sql = "UPDATE relics SET owner='NULL', location='NULL' WHERE owner='$characteruser'";
		mysqli_query($mysqli, $sql);
	}
	
	//deleteclaims en update claims naar heir
	if($familyheir != NULL OR $familyheir != "NULL" OR $familyheir != 0){
		$result3 = "SELECT * FROM claim WHERE charowner='$characterid' AND inheritable != '0'";
		$rs_result2 = $mysqli->query($result3);
		$count2 = $rs_result2->num_rows;//aantal titles
		while($row2 = $rs_result2->fetch_assoc()) {//ga door titles heen
			$claimnhertiable=$row2["inheritable"];
			$claimid=$row2["id"];
			$claimnhertiable = $claimnhertiable - 1;
			
			$sql = "UPDATE claim SET charowner='$familyheir', inheritable='$claimnhertiable' WHERE id='$claimid'";
			mysqli_query($mysqli, $sql);
		}
	}
	$sql = "DELETE FROM claim WHERE charowner='$characterid'";
	mysqli_query($mysqli, $sql);
	
	//update liege als character == dynast naar heir
	if(($familyheir != NULL AND $familyheir != 0) AND $familydynast == $characterid){ 
		$sql = "UPDATE characters SET liege='$familyheir' WHERE liege='$characterid'";
		mysqli_query($mysqli, $sql);
		
		if($characterliege != $characterid){//als character niet eigen liege verander liege of heir naar liege van character
			$sql = "UPDATE characters SET liege='$characterliege' WHERE id='$familyheir'";
			mysqli_query($mysqli, $sql);
		}
		
		$result3 = "SELECT * FROM characters WHERE (mother='$familyheir' OR father='$familyheir') AND alive='1'";
		$rs_result2 = $mysqli->query($result3);
		$count2 = $rs_result2->num_rows;//aantal kinderen van nieuwe dynast/oude heir
		
		if($count2 != 0){
			if($heritagelaw==1){//selecteer oudste kind
				$i=1;
				while($row3 = $rs_result3->fetch_assoc()) {//ga door kinderen heen en selecteer oudste kind
					if($i==1){
						$childid=$row2["id"];
					}
				}
			}
		$sql = "UPDATE family SET heir='$childid', dynast='$familyheir' WHERE id='$characterfamily'";
		mysqli_query($mysqli, $sql);
		}else{//geen heir
			$sql = "UPDATE family SET heir='0', dynast='$familyheir' WHERE id='$characterfamily'";
			mysqli_query($mysqli, $sql);
		}
		

	}elseif($familydynast == $characterid){//no heir but dynast == character; everyone is his own liege
		$result3 = "SELECT * FROM characters WHERE liege='$characterid'";
		$rs_result2 = $mysqli->query($result3);
		$count3 = $rs_result2->num_rows;//aantal titles
		
		
		if($count3 != 0){
			if($characterliege == $characterid){//character has no liege
				while($row2 = $rs_result2->fetch_assoc()) {//ga door titles heen
					$characterliegeid=$row2["id"];
				
					$sql = "UPDATE characters SET liege='$characterliegeid' WHERE id='$characterliegeid'";
					mysqli_query($mysqli, $sql);
				}
			}else{//character has liege; update liege of followers to liege of character
				$sql = "UPDATE characters SET liege='$characterliege' WHERE liege='$characterid'";
				mysqli_query($mysqli, $sql);
			}
		}
	}elseif($characterliege != $characterid){//change liege to the liege of character; character is not dynast
		$sql = "UPDATE characters SET liege='$characterliege' WHERE liege='$characterid'";
		mysqli_query($mysqli, $sql);
	}else{//if everything fails free everyone
		$result3 = "SELECT * FROM characters WHERE liege='$characterid'";
		$rs_result2 = $mysqli->query($result3);
		$count3 = $rs_result2->num_rows;//aantal titles
		
		if($count3 != 0){
			while($row2 = $rs_result2->fetch_assoc()) {//ga door titles heen
				$characterliegeid=$row2["id"];
			
				$sql = "UPDATE characters SET liege='$characterliegeid' WHERE id='$characterliegeid'";
				mysqli_query($mysqli, $sql);
			}
		}
	}

	if($familyheir == $characterid){//als familyheir is dood select new familyheir
		if($heritagelaw == 1){
			$result4 = $mysqli->query("SELECT * FROM characters WHERE (father='$familydynast' OR mother='$familydynast') AND alive='1' LIMIT 1") or die($mysqli->error());
			$row4 = mysqli_fetch_array($result4);
			$countnumbercharacters = $result4->num_rows;
			$newheir = $row4['id'];
			
			if($countnumbercharacters != 0){
				$sql = "UPDATE family SET heir='$newheir' WHERE id='$characterfamily'";
				mysqli_query($mysqli, $sql);
			}else{//geen heir
				$sql = "UPDATE family SET heir='0' WHERE id='$characterfamily'";
				mysqli_query($mysqli, $sql);
			}
		}
	}
	
	$result3 = "SELECT * FROM titles WHERE holderid='$characterid'";
	$rs_result2 = $mysqli->query($result3);
	$count2 = $rs_result2->num_rows;//aantal titles
	
	if($count2 != 0){//als er titles zijn
		if($heritagelaw==1){//iedereen erft even veel
			$result4 = "SELECT * FROM characters WHERE mother='$characterid' OR father='$characterid' AND alive='1'";
			$rs_result3 = $mysqli->query($result4);
			$count3 = $rs_result3->num_rows;//aantal kinderen
			
			if($count3 != 0){//als er kinderen zijn
				$childarray=array();
				while($row3 = $rs_result3->fetch_assoc()) {//ga door kinderen heen
					$childid=$row3["id"];
					array_push($childarray,$childid);
				}
				
				$childnumber=0;
				$maxchildnumber=count($childarray)-1;
				
				while($row2 = $rs_result2->fetch_assoc()) {//ga door titles heen
					$titleid=$row2["id"];
					$holdingtype=$row2["holdingtype"];
					$holdingid=$row2["holdingid"];
				
					$childid=$childarray[$childnumber];
					
					if($childnumber==$maxchildnumber){
						$childnumber=0;
					}else{
						$childnumber=$childnumber+1;
					}
					$sql = "UPDATE titles SET holderid='$childid' WHERE id='$titleid'";
					mysqli_query($mysqli, $sql);
					
					$result = $mysqli->query("SELECT user FROM characters WHERE id='$childid'") or die($mysqli->error());
					$row = mysqli_fetch_array($result);
					$childuser=$row['user'];
					
					if($holdingtype == "kingdom"){
						$sql = "UPDATE countryinfo SET countrypresident='$childuser', characterowner='$childid' WHERE id='$holdingid'";
						mysqli_query($mysqli, $sql);
						
						$result = $mysqli->query("SELECT country FROM countryinfo WHERE id='$holdingid'") or die($mysqli->error());
						$row = mysqli_fetch_array($result);
						$holdingname=$row['country'];
						$content= "You inherited the kingdom of $holdingname from one of your parents";
					}elseif($holdingtype == "duchy"){
						$sql = "UPDATE region SET characterowner='$childid' WHERE id='$holdingid'";
						mysqli_query($mysqli, $sql);
						
						$result = $mysqli->query("SELECT name FROM region WHERE id='$holdingid'") or die($mysqli->error());
						$row = mysqli_fetch_array($result);
						$holdingname=$row['name'];
						$content= "You inherited the duchy of $holdingname from one of your parents";
					}
					$sql = "INSERT INTO events (date, content, extrainfo) " 
				     . "VALUES (NOW(),'$content','$childuser')";
					mysqli_query($mysqli2, $sql);
				}
			
			}else{//geen kinderen
				$result4 = "SELECT * FROM characters WHERE mother='$dynastmother' AND father='$dynastfather' AND alive='1'";
				$rs_result3 = $mysqli->query($result4);
				$count3 = $rs_result3->num_rows;//aantal broers of zussen			
				
				if($count3 != 0){//als er broers of zussen zijn
					if($heritagelaw == 1){
						$i=1;
						while($row3 = $rs_result3->fetch_assoc()) {//ga door borers en zussen heen en selecteer oudste
							if($i==1){
								$newtitleholder=$row3["id"];
							}
						}
						while($row2 = $rs_result2->fetch_assoc()) {//ga door titles heen
							$titleid=$row2["id"];
							$holdingtype=$row2["holdingtype"];
							$holdingid=$row2["holdingid"];
						
							$sql = "UPDATE titles SET holderid='$newtitleholder' WHERE id='$titleid'";
							mysqli_query($mysqli, $sql);
							
							$result = $mysqli->query("SELECT user FROM characters WHERE id='$newtitleholder'") or die($mysqli->error());
							$row = mysqli_fetch_array($result);
							$newtitleholderuser=$row['user'];
							
							if($holdingtype == "kingdom"){
								$sql = "UPDATE countryinfo SET countrypresident='$newtitleholderuser', characterowner='$newtitleholder' WHERE id='$holdingid'";
								mysqli_query($mysqli, $sql);
								
								$result = $mysqli->query("SELECT country FROM countryinfo WHERE id='$holdingid'") or die($mysqli->error());
								$row = mysqli_fetch_array($result);
								$holdingname=$row['country'];
								$content= "You inherited the kingdom of $holdingname from one of your family members";
							}elseif($holdingtype == "duchy"){
								$sql = "UPDATE region SET characterowner='$newtitleholder' WHERE id='$holdingid'";
								mysqli_query($mysqli, $sql);
								
								$result = $mysqli->query("SELECT name FROM region WHERE id='$holdingid'") or die($mysqli->error());
								$row = mysqli_fetch_array($result);
								$holdingname=$row['name'];
								$content= "You inherited the duchy of $holdingname from one of your family members";
							}
							$sql = "INSERT INTO events (date, content, extrainfo) " 
						     . "VALUES (NOW(),'$content','$newtitleholderuser')";
							mysqli_query($mysqli2, $sql);
						}
					}
				}else{//geef titles aan liege
					if($characterliege != $characterid){//niet aan zelf erven
						while($row2 = $rs_result2->fetch_assoc()) {//ga door titles heen
							$titleid=$row2["id"];
							$holdingtype=$row2["holdingtype"];
							$holdingid=$row2["holdingid"];
							
							$result = $mysqli->query("SELECT user, alive FROM characters WHERE id='$characterliege'") or die($mysqli->error());
							$row = mysqli_fetch_array($result);
							$newtitleholderuser=$row['user'];
							$liegealive=$row['alive'];
						
							$sql = "UPDATE titles SET holderid='$characterliege' WHERE id='$titleid'";
							mysqli_query($mysqli, $sql);
							
							if($holdingtype == "kingdom"){
								$sql = "UPDATE countryinfo SET countrypresident='$newtitleholderuser', characterowner='$characterliege' WHERE id='$holdingid'";
								mysqli_query($mysqli, $sql);
								
								$result = $mysqli->query("SELECT country FROM countryinfo WHERE id='$holdingid'") or die($mysqli->error());
								$row = mysqli_fetch_array($result);
								$holdingname=$row['country'];
								$content= "You inherited the kingdom of $holdingname from one of your vassals";
							}elseif($holdingtype == "duchy"){
								$sql = "UPDATE region SET characterowner='$characterliege' WHERE id='$holdingid'";
								mysqli_query($mysqli, $sql);
								
								$result = $mysqli->query("SELECT name FROM region WHERE id='$holdingid'") or die($mysqli->error());
								$row = mysqli_fetch_array($result);
								$holdingname=$row['name'];
								$content= "You inherited the duchy of $holdingname from one of your vassals";
							}
							$sql = "INSERT INTO events (date, content, extrainfo) " 
						     . "VALUES (NOW(),'$content','$newtitleholderuser')";
							mysqli_query($mysqli2, $sql);
						}
					}else{
						while($row2 = $rs_result2->fetch_assoc()) {//ga door titles heen
							$titleid=$row2["id"];
							$holdingtype=$row2["holdingtype"];
							$holdingid=$row2["holdingid"];
						
							$sql = "UPDATE titles SET holderid='0' WHERE id='$titleid'";
							mysqli_query($mysqli, $sql);
						}
					}
				}
			}
		}
	}
	
	$content= "Your character $charactername died of old age";
	$sql = "INSERT INTO events (date, content, extrainfo) " 
     . "VALUES (NOW(),'$content','$characteruser')";
	mysqli_query($mysqli2, $sql);
}
?>