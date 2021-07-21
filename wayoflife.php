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
require 'functions.php';
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

$result4 = $mysqli->query("SELECT * FROM characters WHERE id='$usercharacterid'") or die($mysqli->error());
$row4 = mysqli_fetch_array($result4);
$userid = $row4['id'];
$charusername = $row4['name'];
$userfamilyid = $row4['familyid'];
$useruser = $row4['user'];
$userlocation = $row4['location'];
$userlocation2 = $row4['location2'];
$userwayoflifeskill = $row4['wayoflifeskill'];
$userwayoflifeaction = $row4['wayoflifeaction'];

$result4 = $mysqli->query("SELECT * FROM family WHERE id='$userfamilyid'") or die($mysqli->error());
$row4 = mysqli_fetch_array($result4);
$userfamilyname = $row4['name'];

$wayoflife = selectwayoflifecharacter($usercharacterid);

if($wayoflife == "assassin"){
	$wayoflifeid = selectwayoflifeid($usercharacterid);
	
	$result4 = $mysqli->query("SELECT * FROM traitscharacters WHERE traitid='$wayoflifeid' AND characterid='$usercharacterid'") or die($mysqli->error());
	$row4 = mysqli_fetch_array($result4);
	$wayoflifecharacterid = $row4['id'];
	$extrainfo = $row4['extrainfo'];
	$extrainfo2 = $row4['extrainfo2'];
	
	if($extrainfo != NULL AND $extrainfo != "NULL"){
		$result4 = $mysqli->query("SELECT * FROM characters WHERE id='$extrainfo'") or die($mysqli->error());
		$row4 = mysqli_fetch_array($result4);
		$targetid = $row4['id'];
		$targetname = $row4['name'];
		$targetfamilyid = $row4['familyid'];
		$targetuser = $row4['user'];
		$targetlocation = $row4['location'];
		$targetlocation2 = $row4['location2'];
		
		$result4 = $mysqli->query("SELECT * FROM family WHERE id='$targetfamilyid'") or die($mysqli->error());
		$row4 = mysqli_fetch_array($result4);
		$targetfamilyname = $row4['name'];
		
		$result4 = $mysqli->query("SELECT * FROM traits WHERE name='suspicious'") or die($mysqli->error());
		$row4 = mysqli_fetch_array($result4);
		$susptraitid = $row4['id'];
		$susptraitamount = $row4['amount'];
		
		$result3 = "SELECT * FROM traitscharacters WHERE characterid='$targetid' AND traitid='$susptraitid'";
		$rs_result2 = $mysqli->query($result3);
		$count2 = $rs_result2->num_rows;//aantal titles
		
		if($count2 != 0){
			$susp = "true";
		}else{
			$susp = "false";
		}
		
		$targethighesttitle = selecthighesttitle($targetid);
		if($targethighesttitle == 0){
			$userpower = 5;
		}elseif($targethighesttitle == 9){
			$userpower = 2;
		}elseif($targethighesttitle == 10){
			$userpower = 1;
		}
	}else{
		$targetid = 0;
	}
	
	?>
	<table id="table1">
	    <tr>
	    	<th>
	    	<div id="block_container">
				<?php
				echo "Way of the $wayoflife";
				?>
			</div>
			</th>
	    </tr>
	</table>
	<table id="table1">	
	    <tr>
	    	<th><?php echo "Target"; ?></th>
	    	<th><?php echo "Skill"; ?></th>
	    	<th><?php echo "Succes chance"; ?></th>
	    	<th><?php echo "Prepare assassination"; ?></th>
	    	<th><?php echo "Assassinate"; ?></th>
	    	<th><?php echo "Unmark target"; ?></th>
	    </tr>
	    <tr>
	    	<td>
	    	<?php 
	    		if($targetid != 0){
	    			echo "<a href='account.php?user=$targetuser&charid=$targetid'>$targetname $targetfamilyname</a>";
	    		}else{
	    			echo "No target";
	    		}
	    	?>
	    	</td>
	    	<td>
	    	<?php 
	    		if($targetid != 0){
	    			echo "$extrainfo2";
	    		}else{
	    			
	    		}
	    	?>
	    	</td>
	    	<td>
	    	<?php 
	    		echo "$userwayoflifeskill";
	    	?>
	    	</td>
	    	<td>
		    	<?php 
		    		if($targetid != 0 AND $userwayoflifeaction == 0){
		    			?>
						<div class="everythingOnOneLine">
						<form method="post" action="">
							<button type="submit" name="prepareassassination" />Prepare</button>
						</form>
						</div> 
						<?php
		    		}else{
		    			
		    		}
		    	?>
				<?php
				if(isset($_POST['prepareassassination'])){
					if($userlocation == $targetlocation AND $userlocation2 == $targetlocation2){
						$extrainfo2 = $extrainfo2 + ($userwayoflifeskill + 1) + $userpower;
						
						$sql = "UPDATE traitscharacters SET extrainfo2='$extrainfo2' WHERE id='$wayoflifecharacterid'";
						mysqli_query($mysqli, $sql);
						
						$sql = "UPDATE characters SET wayoflifeaction='1' WHERE id='$usercharacterid'";
						mysqli_query($mysqli, $sql);
						
						if($susp == "false"){
							$randnumber = rand(1, 100);
							if($randnumber <= 5){
								$sql = "INSERT INTO traitscharacters (characterid, traitid, date) " 
								. "VALUES ('$targetid','$susptraitid',NOW())";
						 		mysqli_query($mysqli, $sql);
								
								$content= "Your character gained the suspicious trait";
								seteventuser($content,$targetuser);
							}
						}
						
						echo'<div class="boxed">Done!</div>';
					}else{
						echo'<div class="boxed">You need to be in the same location as your target!</div>';
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
				
	    	</td>
	    	<td>
		    	<?php 
		    		if($targetid != 0){
		    			?>
						<div class="everythingOnOneLine">
						<form method="post" action="">
							<button type="submit" name="assassinate" />Assassinate target</button>
						</form>
						</div> 
						<?php
		    		}else{
		    			
		    		}
		    	?>
		    	
				<?php
				if(isset($_POST['assassinate'])){
					if($userlocation == $targetlocation AND $userlocation2 == $targetlocation2){
						$randnumber = rand(1, 100);
						if($randnumber <= $extrainfo2){//succes
							characterdies($targetid);
							
							$userwayoflifeskill = $userwayoflifeskill + 1;
							
							$sql = "UPDATE characters SET wayoflifeskill='$userwayoflifeskill' WHERE id='$usercharacterid'";
							mysqli_query($mysqli, $sql);
							
							$sql = "UPDATE traitscharacters SET extrainfo='NULL', extrainfo2='0' WHERE id='$wayoflifecharacterid'";
							mysqli_query($mysqli, $sql);
							
							$content= "Your character has been assassinated by <a href='account.php?user=$username&charid=$usercharacterid'>$charusername $userfamilyname</a>";
							seteventuser($content,$targetuser);
							
							echo'<div class="boxed">You succesfully assassinated your target!</div>';
						}else{//failure
							$sql = "UPDATE traitscharacters SET extrainfo2='0' WHERE id='$wayoflifecharacterid'";
							mysqli_query($mysqli, $sql);
							
							//check for double claims
							$result3 = "SELECT * FROM claim WHERE charowner='$targetid' AND title='$usercharacterid'";
							$rs_result2 = $mysqli->query($result3);
							$count5 = $rs_result2->num_rows;//aantal titles
							
							if($count5 ==0){
								$sql = "INSERT INTO claim (type, inheritable, charowner, title, date) " 
								. "VALUES ('imprison','0','$targetid','$usercharacterid',NOW())";
						 		mysqli_query($mysqli, $sql);
								$lastid = $mysqli->insert_id;
							}
							
							$content= "Your character survived a assassination attempt by <a href='account.php?user=$username&charid=$usercharacterid'>$charusername $userfamilyname</a>. You gained a claim to imprison this character.";
							seteventuser($content,$targetuser);
							
							echo'<div class="boxed">Your assassination attempt failed!</div>';
						}
					}else{
						echo'<div class="boxed">You need to be in the same location as your target!</div>';
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
	    	</td>
	    	<td>
	    		<?php
	    		if($targetid != 0){
		    		?>
					<div class="everythingOnOneLine">
					<form method="post" action="">
						<button type="submit" name="unmark" />Unmark target</button>
					</form>
					</div> 
					<?php
				}
				?>
				
				<?php
				if(isset($_POST['unmark'])){
					$sql = "UPDATE traitscharacters SET extrainfo='NULL', extrainfo2='0' WHERE id='$wayoflifecharacterid'";
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
				?>
	    	</td>
	    </tr>			
	</table>
	<?php
	
}elseif($wayoflife == "warrior"){
	$wayoflifeid = selectwayoflifeid($usercharacterid);
	
	$result4 = $mysqli->query("SELECT * FROM traitscharacters WHERE traitid='$wayoflifeid' AND characterid='$usercharacterid'") or die($mysqli->error());
	$row4 = mysqli_fetch_array($result4);
	$wayoflifecharacterid = $row4['id'];
	$extrainfo = $row4['extrainfo'];
	$extrainfo2 = $row4['extrainfo2'];
	
	?>
	<table id="table1">
	    <tr>
	    	<th>
	    	<div id="block_container">
				<?php
				echo "Way of the $wayoflife";
				?>
			</div>
			</th>
	    </tr>
	</table>
	<table id="table1">	
	    <tr>
	    	<th><?php echo "Skill"; ?></th>
	    	<th><?php echo "Train"; ?></th>
	    </tr>
	    <tr>
	    	<td>
	    	<?php 
	    		echo "$userwayoflifeskill";
	    	?>
	    	</td>
	    	<td>
		    	<?php 
		    		if($userwayoflifeaction == 0){
		    			?>
						<div class="everythingOnOneLine">
						<form method="post" action="">
							<button type="submit" name="warriortrain" />Train</button>
						</form>
						</div> 
						<?php
		    		}else{
              
		    		}
		    	?>
				<?php
				if(isset($_POST['warriortrain'])){
					$result4 = $mysqli->query("SELECT * FROM characters WHERE id='$usercharacterid'") or die($mysqli->error());
					$row4 = mysqli_fetch_array($result4);
					$userid = $row4['id'];
					$charusername = $row4['name'];
					$userfamilyid = $row4['familyid'];
					$useruser = $row4['user'];
					$userlocation = $row4['location'];
					$userlocation2 = $row4['location2'];
					$userwayoflifeskill = $row4['wayoflifeskill'];
					$userwayoflifeaction = $row4['wayoflifeaction'];
					
					$result4 = $mysqli->query("SELECT * FROM users WHERE username='$username'") or die($mysqli->error());
					$row4 = mysqli_fetch_array($result4);
					$userstrength = $row4['strength'];
					
					if($userwayoflifeaction == 0){
						$userwayoflifeskill = $userwayoflifeskill + 1;
						$userstrength = $userstrength + 1;
						
						$sql = "UPDATE characters SET wayoflifeskill='$userwayoflifeskill', wayoflifeaction='1' WHERE id='$userid'";
						mysqli_query($mysqli, $sql);
						
						$sql = "UPDATE users SET strength='$userstrength' WHERE username='$username'";
						mysqli_query($mysqli, $sql);
						
						echo'<div class="boxed">Done!</div>';
					}else{
						echo'<div class="boxed">You can only train once a day!</div>';
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
				
	    	</td>
	    </tr>
	   </table>
	   <?php
}elseif($wayoflife == "none"){
	echo'<div class="boxed">You have not selected a way of life yet!</div>';
}


?> </div> <?php	

?>
</div>
</body>
<footer>
<?php require 'bottombar.php'; ?>
</footer>
</html>
