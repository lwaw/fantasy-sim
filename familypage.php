<?php 
session_start();
// Check if user is logged in using the session variable
if ($_SESSION['logged_in'] != 1 ) {
  $_SESSION['message'] = "You must log in before viewing your profile page!";
  header("location: error.php"); 
  //echo session_id();   
}
else {
    // Makes it easier to read
    $username = $_SESSION['username'];
    $last_name = $_SESSION['last_name'];
    $email = $_SESSION['email'];
    $active = $_SESSION['active'];
	$usercharacterid = $_SESSION['usercharacterid'];
}

require 'navigationbar.php';
require 'db.php';
require 'regionborders.php';
require_once 'purifier/library/HTMLPurifier.auto.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/* Displays user information and some useful messages */
//session_start();

//html purifier setup
$config = HTMLPurifier_Config::createDefault();
$purifier = new HTMLPurifier($config);
$config->set('HTML.SafeIframe', true); //iframes
$config->set('URI.SafeIframeRegexp', '%^(https?:)?//(www\.youtube(?:-nocookie)?\.com/embed/|player\.vimeo\.com/video/)%');
?>

<!DOCTYPE html>

<html>
	
<head>
  <title>Fantasy-Sim</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/styletot.css">
  <link rel="stylesheet" href="css/styletot.css">
   <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
   <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
   <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
   <link rel="manifest" href="/site.webmanifest">
   <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#add8e6">
   <meta name="msapplication-TileColor" content="#add8e6">
   <meta name="theme-color" content="#ffffff">
   
  <script type="text/javascript" src="tinymce/tinymce.js"></script>
  <script>
  tinymce.init({
    selector: '#mytextarea',
    plugins: ["emoticons autolink help preview textcolor"],
   toolbar: [
     'undo redo | styleselect | alignleft aligncenter alignright | forecolor backcolor | bold italic | emoticons | help | preview'
   ]
  });
  </script>
  

  
</head>

<body>
<div class="boxedtot">
<?php
require 'ageing.php';
if (isset($_GET["charid"])) { $characterid  = $_GET["charid"]; } else { $characterid=0; };
$characterid=$mysqli->escape_string($characterid);

if (isset($_GET["type"])) { $type  = $_GET["type"]; } else { $characterid=0; };
$type=$mysqli->escape_string($type);

if($characterid != 0){
	$result2 = $mysqli->query("SELECT * FROM characters WHERE id='$characterid' LIMIT 1") or die($mysqli->error());
	$row2 = mysqli_fetch_array($result2);
	$charactername = $row2['name'];
	$characterid = $row2['id'];
	$characterage = $row2['age'];
	$characterrace = $row2['race'];
	$charactersex = $row2['sex'];
	$characterfamily = $row2['familyid'];
	$characteruser = $row2['user'];
	$characteralive = $row2['alive'];
	$characterliege = $row2['liege'];
	
	//select family
	$result2 = $mysqli->query("SELECT * FROM family WHERE id='$characterfamily'") or die($mysqli->error());
	$row2 = mysqli_fetch_array($result2);
	$familyname = $row2['name'];
	$familydynast = $row2['dynast'];
	$familyheir = $row2['heir'];
	
	if($type == 1){//show dynasty
		//count your children within your liege
		$result3 = "SELECT * FROM characters WHERE (father = '$usercharacterid' OR mother = '$usercharacterid') AND liege = '$usercharacterid' AND age < '18' AND alive = '1'";
		$rs_result2 = $mysqli->query($result3);
		$count2 = $rs_result2->num_rows;//aantal titles
		
		if($count2 != 0){
			$result4 = $mysqli->query("SELECT * FROM users WHERE username = '$username'") or die($mysqli->error());
			$row4 = mysqli_fetch_array($result4);
			$userreligion=$row4["userreligion"];
			
			if($userreligion != "NULL" AND $userreligion != NULL){
				?>
				<div class="textbox">
					<form method="post" action="">
						<button type="submit" name="disinheritform" />Disinherit one of your children</button>
					</form>
				</div>
				<?php
			}
		}
		
		if(isset($_POST['disinheritform'])){
			$result4 = $mysqli->query("SELECT * FROM users WHERE username = '$username'") or die($mysqli->error());
			$row4 = mysqli_fetch_array($result4);
			$userreligion=$row4["userreligion"];
			
			$result4 = $mysqli->query("SELECT * FROM religion WHERE name = '$userreligion'") or die($mysqli->error());
			$row4 = mysqli_fetch_array($result4);
			$religiontax=$row4["religiontax"];
			
			echo nl2br ("<div class=\"t1\">This will remove one of your children from your dynasty and will put them under the supervision of your religion.
			Disinherited children will not inherit any holding but will get claims on all holdings. This will cost $religiontax gold.</div>");
			
			$result = $mysqli->query("SELECT * FROM characters WHERE (father = '$usercharacterid' OR mother = '$usercharacterid') AND liege = '$usercharacterid' AND age < '18' AND alive = '1'") or die($mysqli->error());
			$columnValues = Array();
			?>
			<form method="post" action="">
			    <select required name="disinheritcharacterid" type="text">
			    <option value="" disabled selected hidden>Which child do you want to disinherit?</option> 
			    <?php       
			    // Iterating through the product array
				while ( $row = mysqli_fetch_assoc($result) ) {
				    ?>
				    <option value="<?php echo strtolower($row['id']); ?>"><?php echo $row['name']; ?></option>
				    <?php
				}
			    ?>
			    </select> 
			    <button type="submit" name="disinheritform2" />Buy claim</button>
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

		if(isset($_POST['disinheritform2'])){
			$disinheritcharacterid = $mysqli->escape_string($_POST['disinheritcharacterid']);
			
			$result3 = "SELECT * FROM characters WHERE id = '$disinheritcharacterid' AND (father = '$usercharacterid' OR mother = '$usercharacterid') AND liege = '$usercharacterid' AND age < '18' AND alive = '1'";
			$rs_result2 = $mysqli->query($result3);
			$count2 = $rs_result2->num_rows;//aantal titles
			
			if($count2 != 0){
				$result4 = $mysqli->query("SELECT * FROM characters WHERE id = '$disinheritcharacterid'") or die($mysqli->error());
				$row4 = mysqli_fetch_array($result4);
				$disinheritedfamilyid=$row4["familyid"];
				$disinheriteduser=$row4["user"];
				$disinheritedmother=$row4["mother"];
				$disinheritedfather=$row4["father"];
				//echo "$disinheritcharacterid";
				$result4 = $mysqli->query("SELECT * FROM family WHERE id = '$disinheritedfamilyid'") or die($mysqli->error());
				$row4 = mysqli_fetch_array($result4);
				$disinheritedfamilyheir=$row4["heir"];
				
				$result4 = $mysqli->query("SELECT * FROM users WHERE username = '$username'") or die($mysqli->error());
				$row4 = mysqli_fetch_array($result4);
				$userreligion=$row4["userreligion"];
				
				$result4 = $mysqli->query("SELECT * FROM religion WHERE name = '$userreligion'") or die($mysqli->error());
				$row4 = mysqli_fetch_array($result4);
				$religiontax=$row4["religiontax"];
				$religiongold=$row4["gold"];
				
				$result4 = $mysqli->query("SELECT * FROM currency WHERE usercur = '$username'") or die($mysqli->error());
				$row4 = mysqli_fetch_array($result4);
				$usergold=$row4["gold"];
				//echo "$usergold";
				
				$usergold = $usergold - $religiontax;
				$religiongold = $religiongold + $religiontax;
				//echo "$usergold";
				if($usergold >= 0){
					//update heir
					if($disinheritedfamilyheir == $disinheritcharacterid){
						$result3 = "SELECT * FROM characters WHERE (mother='$usercharacterid' OR father='$usercharacterid') AND alive='1' AND id != '$disinheritcharacterid'";
						$rs_result2 = $mysqli->query($result3);
						$count3 = $rs_result2->num_rows;//aantal kinderen van nieuwe dynast/oude heir
						
						if($count3 != 0){
							$i=1;
							while($row3 = $rs_result3->fetch_assoc()) {//ga door kinderen heen en selecteer oudste kind
								if($i==1){
									$childid=$row2["id"];
								}
							}
							$sql = "UPDATE family SET heir='$childid' WHERE id='$disinheritedfamilyid'";
							mysqli_query($mysqli, $sql);
						}else{//geen heir
							$sql = "UPDATE family SET heir='0' WHERE id='$disinheritedfamilyid'";
							mysqli_query($mysqli, $sql);
						}
					}
					
					$sql = "UPDATE religion SET gold='$religiongold' WHERE name='$userreligion'";
					mysqli_query($mysqli, $sql);
					
					$sql = "UPDATE currency SET gold='$usergold' WHERE usercur='$username'";
					mysqli_query($mysqli, $sql);
					
					$sql = "UPDATE characters SET liege='$disinheritcharacterid', familyid = NULL, mother = NULL, father = NULL WHERE id='$disinheritcharacterid'";
					mysqli_query($mysqli, $sql);
					
					$sql = "UPDATE users SET userreligion = '$userreligion' WHERE username='$disinheriteduser'";
					mysqli_query($mysqli, $sql);
					
					//add trait
					$result4 = $mysqli->query("SELECT * FROM traits WHERE name='orphan'") or die($mysqli->error());
					$row4 = mysqli_fetch_array($result4);
					$orphantraitid = $row4['id'];
					
					$sql = "INSERT INTO traitscharacters (characterid, traitid, date, extrainfo) " 
					. "VALUES ('$disinheritcharacterid','$orphantraitid',NOW(),'$userreligion')";
			 		mysqli_query($mysqli, $sql);
					
					//check for titles and claims
					$result3 = "SELECT * FROM titles WHERE holderid='$usercharacterid'";
					$rs_result2 = $mysqli->query($result3);
					$count2 = $rs_result2->num_rows;//aantal titles
					
					if($count2 != 0){
						while($row2 = $rs_result2->fetch_assoc()) {//ga door titles heen
							$titleid=$row2["id"];
							$holdingtype=$row2["holdingtype"];
							$holderid=$row2["holderid"];
							$holdingid=$row2["holdingid"];
							
							if($disinheriteduser != "npc"){
								$result4 = $mysqli->query("SELECT * FROM users WHERE username = '$disinheriteduser'") or die($mysqli->error());
								$row4 = mysqli_fetch_array($result4);
								$disinheritednationality=$row4["nationality"];
							}else{
								$result4 = $mysqli->query("SELECT * FROM region WHERE id = '$holdingid'") or die($mysqli->error());
								$row4 = mysqli_fetch_array($result4);
								$disinheritednationality=$row4["curowner"];
							}
							
							if($holdingtype == "duchy"){								
								$sql = "INSERT INTO claim (type, inheritable, charowner, title, date, countryowner) " 
								. "VALUES ('resistanceclaim','1','$holderid','$titleid',NOW(),'$disinheritednationality')";
						 		mysqli_query($mysqli, $sql);
								$lastid = $mysqli->insert_id;
							}elseif($holdingtype == "kingdom"){
								$sql = "INSERT INTO claim (type, inheritable, charowner, title, date, countryowner) " 
								. "VALUES ('resistanceclaim','1','$holderid','$titleid',NOW(),'$disinheritednationality')";
						 		mysqli_query($mysqli, $sql);
								$lastid = $mysqli->insert_id;
							}
						}
					}
								
					if($usercharacterid == $disinheritedfather){
						$content= "You have been disinherited by your father";
					}else{
						$content= "You have been disinherited by your mother";
					}
					$sql = "INSERT INTO events (date, content, extrainfo) " 
				     . "VALUES (NOW(),'$content','$disinheriteduser')";
					mysqli_query($mysqli2, $sql);
					
					echo'<div class="boxed">Done!</div>';
				}else{
					echo'<div class="boxed">You do not have enough gold!</div>';
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
	
	
		$sql = "SELECT * FROM characters WHERE familyid='$characterfamily' ORDER BY name ASC";
		$rs_result = $mysqli->query($sql);
		
		?> 
		<div class="scroll">
		<table id="table1">
		<tr>
		    <th> Dynasty members</th>
		    <th> Age</th>
		</tr>
		<?php 
		while($row = $rs_result->fetch_assoc()) {
			$selectid=$row["id"];
			$selectname=$row["name"];
			$selectuser=$row["user"];
			$selectalive=$row["alive"];
			$lastonline=$row["lastonline"];
			if($selectalive==1){
				$link="<a href='account.php?user=$selectuser&charid=$selectid'>$selectname $familyname</a>";
			}elseif($selectalive==0){
				$link="<a href='account.php?user=$selectuser&charid=$selectid'>$selectname $familyname [+]</a>";
			}
			?> 
		           <tr>
		           <td>
		           	<?php
		           	$lastonline=$row["lastonline"];
					$date = new DateTime($lastonline);
					$date->add(new DateInterval('PT' . 15 . 'M'));
					$Datenew1 = $date->format('Y-m-d H:i:s');
					
					date_default_timezone_set('UTC'); //current date
					$datecur = date("Y-m-d H:i:s"); 
		           	?>
		           	<div class="everythingOnOneLine2">
		           		<?php echo $link; ?>
		           		<?php if($Datenew1>$datecur){ ?>
						<div class="notificationbox">
							<img src="img/notificationgreencircle.png">
						</div>
						<?php } ?>
					</div>
		           </td>
		           <td><?php echo $row["age"]; ?></td>
		           </tr>
			<?php 
			
		}; 
		?> 
		</table>
		</div>
		<?php
	}elseif($type==2){//show vassals
		$sql = "SELECT * FROM characters WHERE liege='$characterid' ORDER BY name ASC";
		$rs_result = $mysqli->query($sql);
		
		?> 
		<div class="scroll">
		<table id="table1">
		<tr>
		    <th> Vassals</th>
		    <th> Age</th>
		</tr>
		<?php 
		while($row = $rs_result->fetch_assoc()) {
			$selectid=$row["id"];
			$selectname=$row["name"];
			$selectuser=$row["user"];
			$selectalive=$row["alive"];
			$lastonline=$row["lastonline"];
			$selectfamilyid=$row["familyid"];
			
			$result2 = $mysqli->query("SELECT * FROM family WHERE id='$selectfamilyid'") or die($mysqli->error());
			$row2 = mysqli_fetch_array($result2);
			$selectfamilyname = $row2['name'];
			if($selectalive==1){
				$link="<a href='account.php?user=$selectuser&charid=$selectid'>$selectname $selectfamilyname</a>";
			}elseif($selectalive==0){
				$link="<a href='account.php?user=$selectuser&charid=$selectid'>$selectname $selectfamilyname [+]</a>";
			}
			?> 
		           <tr>
		           <td>
		           	<?php
		           	$lastonline=$row["lastonline"];
					$date = new DateTime($lastonline);
					$date->add(new DateInterval('PT' . 15 . 'M'));
					$Datenew1 = $date->format('Y-m-d H:i:s');
					
					date_default_timezone_set('UTC'); //current date
					$datecur = date("Y-m-d H:i:s"); 
		           	?>
		           	<div class="everythingOnOneLine2">
		           		<?php echo $link; ?>
		           		<?php if($Datenew1>$datecur){ ?>
						<div class="notificationbox">
							<img src="img/notificationgreencircle.png">
						</div>
						<?php } ?>
					</div>
		           </td>
		           <td><?php echo $row["age"]; ?></td>
		           </tr>
			<?php 
			
		}; 
		?> 
		</table>
		</div>
		<?php
	}
	
	if($familyheir == $characterid AND $characteruser == $username){
		
	}
}

?> </div> <?php	

?>
</div>
</body>
<footer>
<?php require 'bottombar.php'; ?>
</footer>
</html>
