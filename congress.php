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
$congressmember=$row['congressmember'];

$result = $mysqli->query("SELECT * FROM countryinfo WHERE country='$nationality'") or die($mysqli->error());
$row = mysqli_fetch_array($result);
$countrypresident=$row['countrypresident'];
$finance=$row['finance'];
$foreignaffairs=$row['foreignaffairs'];
$immigration=$row['immigration'];

if($congressmember == $nationality || $countrypresident == $username || $finance == $username || $foreignaffairs == $username || $immigration == $username){
	echo nl2br ("<div class=\"h1\">Congress</div>");
	?> <hr /> <?php
	
	$sql = "SELECT * FROM congress WHERE country='$nationality' AND type != 'vote'";
	$rs_result = $mysqli->query($sql);
	?> 
	<table id="table1">
		<tr>
	    <th> Issue</th>
	    <th> Date</th>
	    <th> For</th>
	    <th> Against</th>
	</tr>
	<?php
	while($row = $rs_result->fetch_assoc()) {
		$date1=$row["start"];
		$date = new DateTime($date1);
		$date->add(new DateInterval('P1D'));
		$Datenew1 = $date->format('Y-m-d H:i:s');
		
		date_default_timezone_set('UTC'); //current date
		$datecur = date("Y-m-d H:i:s"); 
		
		if($Datenew1>$datecur){
			?>
	       <tr>
	           <td>
	           	<?php
	           	if($row["type"]=="immigrationtax"){
	           		$extraint=$row["extraint"];
					$id=$row["id"];
	           		echo nl2br ("<div class=\"t1\">Change the immigration tax to $extraint</div>");
	           	}elseif($row["type"]=="financemin"){
	           		$extratext=$row["extratext"];
					$id=$row["id"];
	           		echo nl2br ("<div class=\"t1\">Change the minister of finance to $extratext</div>");
	           	}elseif($row["type"]=="foreignmin"){
	           		$extratext=$row["extratext"];
					$id=$row["id"];
	           		echo nl2br ("<div class=\"t1\">Change the minister of foreign affairs to $extratext</div>");
	           	}elseif($row["type"]=="immigrationmin"){
	           		$extratext=$row["extratext"];
					$id=$row["id"];
	           		echo nl2br ("<div class=\"t1\">Change the minister of immigration to $extratext</div>");
	           	}elseif($row["type"]=="statereligion"){
	           		$extratext=$row["extratext"];
					$id=$row["id"];
	           		echo nl2br ("<div class=\"t1\">Change the state religion to $extratext</div>");
	           	}elseif($row["type"]=="government"){
	           		$extratext=$row["extratext"];
					$id=$row["id"];
	           		echo nl2br ("<div class=\"t1\">Change the government type to $extratext</div>");
	           	}elseif($row["type"]=="waroffer"){
	           		$extratext=$row["extratext"];
					$id=$row["id"];
	           		echo nl2br ("<div class=\"t1\">Declare war to $extratext</div>");
	           	}elseif($row["type"]=="napoffer"){
	           		$extratext=$row["extratext"];
					$extraint=$row["extraint"];
					$id=$row["id"];
					
					if($extraint==0){
	           			echo nl2br ("<div class=\"t1\">Offer nap to $extratext</div>");
					}elseif($extraint==1){
						echo nl2br ("<div class=\"t1\">$extratext offers a nap</div>");
					}
	           	}elseif($row["type"]=="vat"){
	           		$extraint=$row["extraint"];
					$id=$row["id"];
	           		echo nl2br ("<div class=\"t1\">Change vat to $extraint</div>");
	           	}elseif($row["type"]=="worktax"){
	           		$extraint=$row["extraint"];
					$id=$row["id"];
	           		echo nl2br ("<div class=\"t1\">Change worktax to $extraint</div>");
	           	}elseif($row["type"]=="inflation"){
	           		$extraint=$row["extraint"];
					$extratext=$row["extratext"];
					$id=$row["id"];
	           		echo nl2br ("<div class=\"t1\">$extratext $extraint currency</div>");
	           	}elseif($row["type"]=="boycot"){
					$extratext=$row["extratext"];
					$id=$row["id"];
	           		echo nl2br ("<div class=\"t1\">Ennact boycot against $extratext</div>");
	           	}elseif($row["type"]=="stopboycot"){
					$extraint=$row["extraint"];
					$extratext=$row["extratext"];
					$id=$row["id"];
	           		echo nl2br ("<div class=\"t1\">End boycot against $extratext</div>");
	           	}elseif($row["type"]=="addtreasury"){
					$extraint=$row["extraint"];
					$extratext=$row["extratext"];
					$id=$row["id"];
	           		echo nl2br ("<div class=\"t1\">Add $extraint $extratext to treasury</div>");
	           	}elseif($row["type"]=="retracttreasury"){
					$extraint=$row["extraint"];
					$extratext=$row["extratext"];
					$id=$row["id"];
	           		echo nl2br ("<div class=\"t1\">Retract $extraint $extratext to treasury</div>");
	           	}elseif($row["type"]=="bioweapon"){
					$extratext=$row["extratext"];
					$id=$row["id"];
	           		echo nl2br ("<div class=\"t1\">Use a bioweapon on $extratext</div>");
	           	}elseif($row["type"]=="transfergold"){
	           		$extraint=$row["extraint"];
					$extratext=$row["extratext"];
					$id=$row["id"];
	           		echo nl2br ("<div class=\"t1\">Transfer $extraint gold to $extratext</div>");
	           	}elseif($row["type"]=="peaceoffer"){
	           		$extraint=$row["extraint"];
					$extratext=$row["extratext"];
					$id=$row["id"];
					
					$result2 = $mysqli->query("SELECT * FROM diplomacy WHERE id='$extraint'") or die($mysqli->error());
					$row2 = mysqli_fetch_array($result2);
					$peace=$row2['peace'];
					
					if($peace==1){
						echo nl2br ("<div class=\"t1\">Offer peace to $extratext</div>");
					}elseif($peace==2){
						echo nl2br ("<div class=\"t1\">$extratext made a peace offer</div>");
					}
	           	}
	           	?>
	           	</td>
	           	<td><?php echo "$Datenew1"; ?></td>
	           <td>
				     <form method="post" action="">  
						<input type="hidden" name="id" value="<?php echo $row["id"]; ?>" />
						<input type="hidden" name="voteval" value="<?php echo "1";; ?>" />
				     	<button type="submit" name="vote" />Vote for</button>
				     </form>
	           </td>
	           <td>
				     <form method="post" action="">  
						<input type="hidden" name="id" value="<?php echo $row["id"]; ?>" />
						<input type="hidden" name="voteval" value="<?php echo "0"; ?>" />
				     	<button type="submit" name="vote" />Vote against</button>
				     </form>
	           </td>
	       </tr>
			<?php	
		}else{
			//count votes
			$votesfor=$row["votesfor"];
			$votesagainst=$row["votesagainst"];
			$id=$row["id"];
			$type=$row["type"];
			$extraint=$row["extraint"];
			$extratext=$row["extratext"];
			$country=$row["country"];
			
			if($votesfor<=$votesagainst){
				$sql = "DELETE FROM congress WHERE id='$id' OR (type='vote' AND votedfor='$id')";
				mysqli_query($mysqli, $sql); 
			}
			
			if($type=="immigrationtax"){
				if($votesfor>$votesagainst){
					$sql = "UPDATE countryinfo SET immigrationtax='$extraint' WHERE country='$country'";
					mysqli_query($mysqli, $sql);
					
					$content= "$country has changed its immigration tax to $extraint";
					$sql = "INSERT INTO events (date, content) " 
				     . "VALUES (NOW(),'$content')";
					mysqli_query($mysqli2, $sql);
					
					$sql = "DELETE FROM congress WHERE id='$id' OR (type='vote' AND votedfor='$id')";
					mysqli_query($mysqli, $sql); 
				}
			}elseif($type=="financemin"){
				if($votesfor>$votesagainst){
					$sql = "UPDATE countryinfo SET finance='$extratext' WHERE country='$country'";
					mysqli_query($mysqli, $sql);
					
					$content= "$country has appointed $extratext as new minister of finance";
					$sql = "INSERT INTO events (date, content) " 
				     . "VALUES (NOW(),'$content')";
					mysqli_query($mysqli2, $sql);
					
					$sql = "DELETE FROM congress WHERE id='$id' OR (type='vote' AND votedfor='$id')";
					mysqli_query($mysqli, $sql); 
				}
			}elseif($type=="foreignmin"){
				if($votesfor>$votesagainst){
					$sql = "UPDATE countryinfo SET foreignaffairs='$extratext' WHERE country='$country'";
					mysqli_query($mysqli, $sql);
					
					$content= "$country has appointed $extratext as new minister of foreign affairs";
					$sql = "INSERT INTO events (date, content) " 
				     . "VALUES (NOW(),'$content')";
					mysqli_query($mysqli2, $sql);
					
					$sql = "DELETE FROM congress WHERE id='$id' OR (type='vote' AND votedfor='$id')";
					mysqli_query($mysqli, $sql); 
				}
			}elseif($type=="immigrationmin"){
				if($votesfor>$votesagainst){
					$sql = "UPDATE countryinfo SET immigration='$extratext' WHERE country='$country'";
					mysqli_query($mysqli, $sql);
					
					$content= "$country has appointed $extratext as new minister of immigration";
					$sql = "INSERT INTO events (date, content) " 
				     . "VALUES (NOW(),'$content')";
					mysqli_query($mysqli2, $sql);
					
					$sql = "DELETE FROM congress WHERE id='$id' OR (type='vote' AND votedfor='$id')";
					mysqli_query($mysqli, $sql); 
				}
			}elseif($type=="statereligion"){
				if($votesfor>$votesagainst){
					$result = $mysqli->query("SELECT * FROM countryinfo WHERE country='$country'") or die($mysqli->error());
					$row = mysqli_fetch_array($result);
					$gold=$row['gold'];
					
					$result = $mysqli->query("SELECT * FROM religion WHERE name='$extratext'") or die($mysqli->error());
					$row = mysqli_fetch_array($result);
					$religiontax=$row['religiontax'];
					$gold=$gold-$religiontax;
					$religiongold=$row['gold'];
					$religiongold=$religiongold+$religiontax;
					
					if($gold>=0){					
						$sql = "UPDATE countryinfo SET statereligion ='$extratext', gold='$gold' WHERE country='$country'";
						mysqli_query($mysqli, $sql);
						
						$sql = "UPDATE countryinfo SET immigration='$extratext' WHERE country='$country'";
						mysqli_query($mysqli, $sql);
						
						$sql = "UPDATE religion SET gold ='$religiongold' WHERE name='$extratext'";
						mysqli_query($mysqli, $sql);
						
						$content= "The new statereligion of $country is now $extratext";
						$sql = "INSERT INTO events (date, content) " 
					     . "VALUES (NOW(),'$content')";
						mysqli_query($mysqli2, $sql);
						
						$sql = "DELETE FROM congress WHERE id='$id' OR (type='vote' AND votedfor='$id')";
						mysqli_query($mysqli, $sql); 
					}
					$sql = "DELETE FROM congress WHERE id='$id' OR (type='vote' AND votedfor='$id')";
					mysqli_query($mysqli, $sql); 
				}
			}elseif($type=="government"){
				if($votesfor>$votesagainst){						
					if($extratext=="democracy"){
						$sql = "UPDATE countryinfo SET government ='1' WHERE country='$country'";
						mysqli_query($mysqli, $sql);
					}elseif($extratext=="kingdom"){
						$sql = "UPDATE countryinfo SET government ='2' WHERE country='$country'";
						mysqli_query($mysqli, $sql);
					}
					
					$content= "The new government type of $country is now $extratext";
					$sql = "INSERT INTO events (date, content) " 
				     . "VALUES (NOW(),'$content')";
					mysqli_query($mysqli2, $sql);
					
					$sql = "DELETE FROM congress WHERE id='$id' OR (type='vote' AND votedfor='$id')";
					mysqli_query($mysqli, $sql); 
				}
			}elseif($type=="waroffer"){
				if($votesfor>$votesagainst){
											
					$result = $mysqli->query("SELECT * FROM countryinfo WHERE country='$nationality'") or die($mysqli->error());
					$row = mysqli_fetch_array($result);
					$money=$row['gold'];
					$warcost=50;
					$money=$money-$warcost;
					
					if($money>=0){
						$sql = "INSERT INTO diplomacy (type, date, country1, country2) " 
				   			. "VALUES ('war',NOW(),'$country', '$extratext')";
						mysqli_query($mysqli, $sql);
						
						$sql = "UPDATE countryinfo SET gold ='$money' WHERE country='$country'";
						mysqli_query($mysqli, $sql);
						
						$content= "$country declared war on $extratext";
						$sql = "INSERT INTO events (date, content) " 
					     . "VALUES (NOW(),'$content')";
						mysqli_query($mysqli2, $sql);
					}
					
					$sql = "DELETE FROM congress WHERE id='$id' OR (type='vote' AND votedfor='$id')";
					mysqli_query($mysqli, $sql); 
				}
			}elseif($type=="napoffer"){
				$napcost=50;
				if($votesfor>$votesagainst){
					if($extraint==0){
						//offer to congress of other country
						$sql = "INSERT INTO congress (type, country, start, extratext, extraint) " 
						. "VALUES ('napoffer','$extratext',NOW(),'$country', '1')";
				 		mysqli_query($mysqli, $sql);
						
						$sql = "DELETE FROM congress WHERE id='$id' OR (type='vote' AND votedfor='$id')";
						mysqli_query($mysqli, $sql); 
					}elseif($extraint==1){
						$result = $mysqli->query("SELECT * FROM countryinfo WHERE country='$country'") or die($mysqli->error());
						$row = mysqli_fetch_array($result);
						$money1=$row['gold'];
						$money1=$money1-$napcost;
						
						$result = $mysqli->query("SELECT * FROM countryinfo WHERE country='$extratext'") or die($mysqli->error());
						$row = mysqli_fetch_array($result);
						$money2=$row['gold'];
						$money2=$money2-$napcost;
						
						if($money1>=0){
							if($money2>=0){
								$sql = "UPDATE countryinfo SET gold ='$money1' WHERE country='$country'";
								mysqli_query($mysqli, $sql);
								
								$sql = "UPDATE countryinfo SET gold ='$money2' WHERE country='$extratext'";
								mysqli_query($mysqli, $sql);
								
								$sql = "INSERT INTO diplomacy (type, date, country1, country2, acceptnap) " 
							   		. "VALUES ('$diplomacy',NOW(),'$nationality', '$country2', '1')";
								mysqli_query($mysqli, $sql);
								
								$content= "$country has signed a NAP with $extratext";
								$sql = "INSERT INTO events (date, content) " 
							     . "VALUES (NOW(),'$content')";
								mysqli_query($mysqli2, $sql);
								
								$sql = "DELETE FROM congress WHERE id='$id' OR (type='vote' AND votedfor='$id')";
								mysqli_query($mysqli, $sql); 
							}else{
								
							}
						}else{
							
						}
						$sql = "DELETE FROM congress WHERE id='$id' OR (type='vote' AND votedfor='$id')";
						mysqli_query($mysqli, $sql); 
					}
				}
			}elseif($type=="vat"){
				if($votesfor>$votesagainst){
					$content= "$country has changed its vat to $extraint";
					$sql = "INSERT INTO events (date, content) " 
				     . "VALUES (NOW(),'$content')";
					mysqli_query($mysqli2, $sql);
					
					$sql = "UPDATE countryinfo SET vat ='$extraint' WHERE country='$country'";
					mysqli_query($mysqli, $sql);
					
					$sql = "DELETE FROM congress WHERE id='$id' OR (type='vote' AND votedfor='$id')";
					mysqli_query($mysqli, $sql); 
				}
			}elseif($type=="worktax"){
				if($votesfor>$votesagainst){
					$content= "$country has changed its worktax to $extraint";
					$sql = "INSERT INTO events (date, content) " 
				     . "VALUES (NOW(),'$content')";
					mysqli_query($mysqli2, $sql);
					
					$sql = "UPDATE countryinfo SET worktax ='$extraint' WHERE country='$country'";
					mysqli_query($mysqli, $sql);
					
					$sql = "DELETE FROM congress WHERE id='$id' OR (type='vote' AND votedfor='$id')";
					mysqli_query($mysqli, $sql); 
				}
			}elseif($type=="inflation"){
				$maxmoneycreation=1000;
				if($votesfor>$votesagainst){
					$result = $mysqli->query("SELECT * FROM countryinfo WHERE country='$country'") or die($mysqli->error());
					$row = mysqli_fetch_array($result);
					$money=$row['money'];
					$moneycreation=$row['moneycreation'];
					
					$moneycreation=$moneycreation+$extraint;
					
					if($moneycreation<$maxmoneycreation){
						if($extratext=="create"){
							$money=$money+$extraint;
							if($money>=0){
								$sql = "UPDATE countryinfo SET moneycreation ='$moneycreation', money='$money' WHERE country='$country'";
								mysqli_query($mysqli, $sql);
							}
						}else{
							$money=$money-$extraint;
							if($money>=0){
								$sql = "UPDATE countryinfo SET moneycreation ='$moneycreation', money='$money' WHERE country='$country'";
								mysqli_query($mysqli, $sql);
							}
						}
						$content= "$country has $extratext $extraint currency";
						$sql = "INSERT INTO events (date, content) " 
					     . "VALUES (NOW(),'$content')";
						mysqli_query($mysqli2, $sql);
					}
					$sql = "DELETE FROM congress WHERE id='$id' OR (type='vote' AND votedfor='$id')";
					mysqli_query($mysqli, $sql); 
				}
			}elseif($type=="boycot"){
				$boycotcost=50;
				if($votesfor>$votesagainst){
					$result = $mysqli->query("SELECT * FROM countryinfo WHERE country='$country'") or die($mysqli->error());
					$row = mysqli_fetch_array($result);
					$money=$row['gold'];
					
					$money=$money-$boycotcost;
					
					if($money>=0){
						$sql = "UPDATE countryinfo SET gold ='$money' WHERE country='$nationality'";
						mysqli_query($mysqli, $sql);
						$country2 = $mysqli->escape_string($_POST['country2']);
				
						$sql = "INSERT INTO diplomacy (type, date, country1, country2) " 
					   		. "VALUES ('boycot',NOW(),'$country', '$extratext')";
						mysqli_query($mysqli, $sql);
						
						$content= "$country has started a boycot against $extratext";
						$sql = "INSERT INTO events (date, content) " 
					     . "VALUES (NOW(),'$content')";
						mysqli_query($mysqli2, $sql);
					}
					$sql = "DELETE FROM congress WHERE id='$id' OR (type='vote' AND votedfor='$id')";
					mysqli_query($mysqli, $sql); 
				}
			}elseif($type=="stopboycot"){
				if($votesfor>$votesagainst){
					$sql = "DELETE FROM diplomacy WHERE id='$extraint'";
					mysqli_query($mysqli, $sql);
												
					$content= "$country has started ended a boycot against $extratext";
					$sql = "INSERT INTO events (date, content) " 
				     . "VALUES (NOW(),'$content')";
					mysqli_query($mysqli2, $sql);
					
					$sql = "DELETE FROM congress WHERE id='$id' OR (type='vote' AND votedfor='$id')";
					mysqli_query($mysqli, $sql); 
				}
			}elseif($type=="addtreasury"){
				if($votesfor>$votesagainst){
					$result = $mysqli->query("SELECT * FROM countryinfo WHERE country='$country'") or die($mysqli->error());
					$row = mysqli_fetch_array($result);
					$gold=$row['gold'];
					$money=$row['money'];
					$treasurymoney=$row['treasurymoney'];
					$treasurygold=$row['treasurygold'];
					$nodecisions=$row['nodecisions'];
					echo "test";
					$positive=0;
					if($extratext=="gold"){
						$gold=$gold-$extraint;
						$treasurygold=$treasurygold+$extraint;
						if($gold>=0){
							$sql = "UPDATE countryinfo SET gold ='$gold', treasurygold='$treasurygold' WHERE country='$country'";
							mysqli_query($mysqli, $sql);
						}
					}elseif($extratext=="currency"){
						$money=$money-$extraint;
						$treasurymoney=$treasurymoney+$extraint;
						if($money>=0){
							$sql = "UPDATE countryinfo SET money ='$money', treasurymoney='$treasurymoney' WHERE country='$country'";
							mysqli_query($mysqli, $sql);
						}
					}	
					$content= "$country added $extraint $extratext to treasury";
					$sql = "INSERT INTO events (date, content) " 
				     . "VALUES (NOW(),'$content')";
					mysqli_query($mysqli2, $sql);
					
					$sql = "DELETE FROM congress WHERE id='$id' OR (type='vote' AND votedfor='$id')";
					mysqli_query($mysqli, $sql); 
				}
			}elseif($type=="retracttreasury"){
				if($votesfor>$votesagainst){
					$result = $mysqli->query("SELECT * FROM countryinfo WHERE country='$country'") or die($mysqli->error());
					$row = mysqli_fetch_array($result);
					$gold=$row['gold'];
					$money=$row['money'];
					$treasurymoney=$row['treasurymoney'];
					$treasurygold=$row['treasurygold'];
					$nodecisions=$row['nodecisions'];
					
					$positive=0;
					if($extratext=="gold"){
						$gold=$gold+$extraint;
						$treasurygold=$treasurygold-$extraint;
						if($treasurygold>=0){
							$sql = "UPDATE countryinfo SET gold ='$gold', treasurygold='$treasurygold' WHERE country='$country'";
							mysqli_query($mysqli, $sql);
						}
					}elseif($extratext=="currency"){
						$money=$money+$extraint;
						$treasurymoney=$treasurymoney-$extraint;
						if($treasurymoney>=0){
							$sql = "UPDATE countryinfo SET money ='$money', treasurymoney='$treasurymoney' WHERE country='$country'";
							mysqli_query($mysqli, $sql);
						}
					}	
					$content= "$country retracted $extraint $extratext to treasury";
					$sql = "INSERT INTO events (date, content) " 
				     . "VALUES (NOW(),'$content')";
					mysqli_query($mysqli2, $sql);
					
					$sql = "DELETE FROM congress WHERE id='$id' OR (type='vote' AND votedfor='$id')";
					mysqli_query($mysqli, $sql); 
				}
			}elseif($type=="bioweapon"){
				if($votesfor>$votesagainst){
					$bioweaponcost=100;
					
					$result = $mysqli->query("SELECT * FROM countryinfo WHERE country='$country'") or die($mysqli->error());
					$row = mysqli_fetch_array($result);
					$gold=$row['gold'];
					
					$gold=$gold-$bioweaponcost;
					
					if($gold>=0){
						$sql = "UPDATE countryinfo SET gold ='$gold' WHERE country='$country'";
						mysqli_query($mysqli, $sql);
						
						$sql = "UPDATE region SET epidemic='1' WHERE name='$extratext'";
						mysqli_query($mysqli, $sql);
						
						$content= "$country launched used a bioweapon on $extratext";
						$sql = "INSERT INTO events (date, content) " 
					     . "VALUES (NOW(),'$content')";
						mysqli_query($mysqli2, $sql);
						
						$sql = "DELETE FROM congress WHERE id='$id' OR (type='vote' AND votedfor='$id')";
						mysqli_query($mysqli, $sql); 
					}else{
						$sql = "DELETE FROM congress WHERE id='$id' OR (type='vote' AND votedfor='$id')";
						mysqli_query($mysqli, $sql); 
					}
				}
			}elseif($type=="transfergold"){
				if($votesfor>$votesagainst){
					$result = $mysqli->query("SELECT * FROM countryinfo WHERE country='$country'") or die($mysqli->error());
					$row = mysqli_fetch_array($result);
					$gold=$row['gold'];
					
					$gold=$gold-$extraint;
					
					if($gold>=0){
						$result = $mysqli->query("SELECT * FROM countryinfo WHERE country='$extratext'") or die($mysqli->error());
						$row = mysqli_fetch_array($result);
						$gold2=$row['gold'];
						
						$gold2=$gold2+$extraint;
						
						$sql = "UPDATE countryinfo SET gold ='$gold' WHERE country='$country'";
						mysqli_query($mysqli, $sql);
						
						$sql = "UPDATE countryinfo SET gold ='$gold2' WHERE country='$extratext'";
						mysqli_query($mysqli, $sql);
						
						$content= "$country transfered $extraint gold to $extratext";
						$sql = "INSERT INTO events (date, content) " 
					     . "VALUES (NOW(),'$content')";
						mysqli_query($mysqli2, $sql);
						
						$sql = "DELETE FROM congress WHERE id='$id' OR (type='vote' AND votedfor='$id')";
						mysqli_query($mysqli, $sql); 
					}else{
						$sql = "DELETE FROM congress WHERE id='$id' OR (type='vote' AND votedfor='$id')";
						mysqli_query($mysqli, $sql); 
					}
				}
			}elseif($type=="peaceoffer"){
				if($votesfor>$votesagainst){
					$result = $mysqli->query("SELECT * FROM diplomacy WHERE id='$extraint'") or die($mysqli->error());
					$row = mysqli_fetch_array($result);
					$peace=$row['peace'];
					$country1=$row['country1'];
					$country2=$row['country2'];
					
					if($peace==1){
						if($country1==$country){
							$country=$country2;
							$extratext=$country1;
						}elseif($country2==$country){
							$country=$country1;
							$extratext=$country2;
						}
						
						$sql = "INSERT INTO congress (type, country, start, extraint, extratext) " 
						. "VALUES ('peaceoffer','$country',NOW(), '$id', '$extratext')";
						mysqli_query($mysqli, $sql);
						
						$sql = "UPDATE diplomacy SET peace ='2' WHERE id='$extraint'";
						mysqli_query($mysqli, $sql);
						
						$sql = "DELETE FROM congress WHERE id='$id' OR (type='vote' AND votedfor='$id')";
						mysqli_query($mysqli, $sql); 
					}elseif($peace==2){
						$sql = "DELETE FROM diplomacy WHERE id='$extraint'";
						mysqli_query($mysqli, $sql); 
						
						$content= "The war between $country1 and $country2 has ended";
						$sql = "INSERT INTO events (date, content) " 
					     . "VALUES (NOW(),'$content')";
						
						$sql = "DELETE FROM congress WHERE id='$id' OR (type='vote' AND votedfor='$id')";
						mysqli_query($mysqli, $sql); 
					}
				}else{
					$sql = "UPDATE diplomacy SET peace ='0' WHERE id='$extraint'";
					mysqli_query($mysqli, $sql);
					
					$content= "congress of $country declined a peaceoffer with $extratext";
					$sql = "INSERT INTO events (date, content) " 
				     . "VALUES (NOW(),'$content')";
					mysqli_query($mysqli2, $sql);
				}
			}
		}
	}; 
	?>
	</table>
	<?php
	
	if(isset($_POST['vote'])){
		$id = $mysqli->escape_string($_POST['id']);
		$id = (int) $id;
		$voteval = $mysqli->escape_string($_POST['voteval']);
		$voteval = (int) $voteval;
		
		$result = $mysqli->query("SELECT * FROM congress WHERE id='$id' AND country='$nationality'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$votesfor=$row['votesfor'];
		$votesagainst=$row['votesagainst'];

		$result = $mysqli->query("SELECT * FROM congress WHERE votedfor='$id' AND user='$username'") or die($mysqli->error());
		
		if($result->num_rows == 0 ){
			if($voteval == 1){
				$votesfor=$votesfor+1;
				
				$sql = "UPDATE congress SET votesfor='$votesfor' WHERE id='$id'";
				mysqli_query($mysqli, $sql);
				
				$sql = "INSERT INTO congress (type, user, votedfor) " 
		            . "VALUES ('vote', '$username', '$id')";
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
				$votesagainst=$votesagainst+1;
				
				$sql = "UPDATE congress SET votesagainst='$votesagainst' WHERE id='$id'";
				mysqli_query($mysqli, $sql);
				
				$sql = "INSERT INTO congress (type, user, votedfor) " 
		            . "VALUES ('vote', '$username', '$id')";
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
		}else{
			echo'<div class="boxed">You already voted for this issue!</div>';
			
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
	echo'<div class="boxed">You are not a congress member!</div>';
}

?>
</div>
</body>
<footer>
<?php require 'bottombar.php'; ?>
</footer>
</html>
