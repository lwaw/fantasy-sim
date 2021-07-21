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
$userprep=$_GET["user"];
$user=$mysqli->escape_string($userprep);

if (isset($_GET["charid"])) { $characterid  = $_GET["charid"]; } else { $characterid=0; };
$characterid=$mysqli->escape_string($characterid);

$result = $mysqli->query("SELECT * FROM users WHERE username='$user'") or die($mysqli->error());
$row = mysqli_fetch_array($result);
$email = $row['email'];
$accountcreated = $row['accountcreated'];
$strength = $row['strength'];
$nationality = $row['nationality'];
$location = $row['location'];
$location2 = $row['location2'];
$race = $row['race'];
$energy = $row['energy'];
$dominance = $row['dominance'];
$age = $row['age'];
$housepos = $row['housepos'];
$userreligion = $row['userreligion'];
$workid = $row['workid'];
$salary = $row['salary'];

//select player character gegevens
$result2 = $mysqli->query("SELECT * FROM characters WHERE id='$usercharacterid'") or die($mysqli->error());
$row2 = mysqli_fetch_array($result2);
$usercharactername = $row2['name'];
$usercharacterid = $row2['id'];
$usercharacterage = $row2['age'];
$usercharacterrace = $row2['race'];
$usercharactersex = $row2['sex'];
$usercharacterfamily = $row2['familyid'];
$usercharacteruser = $row2['user'];
$usercharacteralive = $row2['alive'];
$usercharacterliege = $row2['liege'];
$usercharactermother = $row2['mother'];
$usercharacterfather = $row2['father'];
$usercharacterlocation = $row2['location'];
$usercharacterlocation2 = $row2['location2'];
$usercharacterwayoflife = selectwayoflifecharacter($usercharacterid);

//select user highest title
$userhighestitle = selecthighesttitle($usercharacterid);

//count player titles
$result3 = "SELECT * FROM titles WHERE holderid = '$usercharacterid'";
$rs_result = $mysqli->query($result3);
$countplayertitles = $rs_result->num_rows;

if($characterid==0){//als geencharacterid in url dan eerste character pakken
	$result2 = $mysqli->query("SELECT * FROM characters WHERE user='$user' AND alive='1' LIMIT 1") or die($mysqli->error());
	$row2 = mysqli_fetch_array($result2);
	$countnumbercharacters = $result2->num_rows;
	$charactername = $row2['name'];
	$characterid = $row2['id'];
	$characterage = $row2['age'];
	$characterrace = $row2['race'];
	$charactersex = $row2['sex'];
	$characterfamily = $row2['familyid'];
	$characteruser = $row2['user'];
	$characteralive = $row2['alive'];
	$characterliege = $row2['liege'];
	$charactermother = $row2['mother'];
	$characterfather = $row2['father'];
	$characterlocation = $row2['location'];
	$characterlocation2 = $row2['location2'];
	$characternationality = $row2['nationality'];
	$characterwayoflife = selectwayoflifecharacter($characterid);
}else{
	$result2 = $mysqli->query("SELECT * FROM characters WHERE id='$characterid' LIMIT 1") or die($mysqli->error());
	$row2 = mysqli_fetch_array($result2);
	$countnumbercharacters = $result2->num_rows;
	$charactername = $row2['name'];
	$characterid = $row2['id'];
	$characterage = $row2['age'];
	$characterrace = $row2['race'];
	$charactersex = $row2['sex'];
	$characterfamily = $row2['familyid'];
	$characteruser = $row2['user'];
	$characteralive = $row2['alive'];
	$characterliege = $row2['liege'];
	$charactermother = $row2['mother'];
	$characterfather = $row2['father'];
	$characterlocation = $row2['location'];
	$characterlocation2 = $row2['location2'];
	$characternationality = $row2['nationality'];
	$characterwayoflife = selectwayoflifecharacter($characterid);
}

//select character highest title
$characterhighestitle = selecthighesttitle($usercharacterid);

//select family
$result2 = $mysqli->query("SELECT * FROM family WHERE id='$characterfamily'") or die($mysqli->error());
$row2 = mysqli_fetch_array($result2);
$familyname = $row2['name'];

//select liege
$result2 = $mysqli->query("SELECT * FROM characters WHERE id='$characterliege'") or die($mysqli->error());
$row2 = mysqli_fetch_array($result2);
$characterliegename = $row2['name'];
$characterliegefamily = $row2['familyid'];
$characterliegeuser = $row2['user'];

//select liege family
$result2 = $mysqli->query("SELECT * FROM family WHERE id='$characterliegefamily'") or die($mysqli->error());
$row2 = mysqli_fetch_array($result2);
$characterliegefamilyname = $row2['name'];

//select spouse
$result2 = $mysqli->query("SELECT * FROM characters WHERE married='$characterid'") or die($mysqli->error());
$row2 = mysqli_fetch_array($result2);
$countmarried = $result2->num_rows;
if($countmarried != 0){
	$marriedname = $row2['name'];
	$marriedfamily = $row2['familyid'];
	$marriedid = $row2['id'];
	$marrieduser = $row2['user'];
	
	$result2 = $mysqli->query("SELECT * FROM family WHERE id='$marriedfamily'") or die($mysqli->error());
	$row2 = mysqli_fetch_array($result2);
	$marriedfamilyname = $row2['name'];
}

//count number of kingdoms in posession
$countkingdomtitles = 0;
if($characterid == $usercharacterid){
	$result2 = $mysqli->query("SELECT * FROM titles WHERE holdingtype='kingdom' AND holderid='$usercharacterid'") or die($mysqli->error());
	$row2 = mysqli_fetch_array($result2);
	$countkingdomtitles = $result2->num_rows;
}

//select father mother
$result2 = $mysqli->query("SELECT * FROM characters WHERE id='$charactermother'") or die($mysqli->error());
$row2 = mysqli_fetch_array($result2);
$charactermothername = $row2['name'];
$charactermotherfamily = $row2['familyid'];
$charactermotheruser = $row2['user'];

$result2 = $mysqli->query("SELECT * FROM characters WHERE id='$characterfather'") or die($mysqli->error());
$row2 = mysqli_fetch_array($result2);
$characterfathername = $row2['name'];
$characterfatherfamily = $row2['familyid'];
$characterfatheruser = $row2['user'];

$result2 = $mysqli->query("SELECT * FROM family WHERE id='$charactermotherfamily'") or die($mysqli->error());
$row2 = mysqli_fetch_array($result2);
$charactermotherfamilyname = $row2['name'];

$result2 = $mysqli->query("SELECT * FROM family WHERE id='$characterfatherfamily'") or die($mysqli->error());
$row2 = mysqli_fetch_array($result2);
$characterfatherfamilyname = $row2['name'];

//check if character is in hiding
$result4 = $mysqli->query("SELECT * FROM traits WHERE name='in hiding'") or die($mysqli->error());
$row4 = mysqli_fetch_array($result4);
$inhidingtraitid = $row4['id'];

$result3 = "SELECT * FROM traitscharacters WHERE characterid='$characterid' AND traitid='$inhidingtraitid'";
$rs_result2 = $mysqli->query($result3);
$countinhiding = $rs_result2->num_rows;//aantal titles

?> <div class="everythingOnOneLine2"> <?php
	?> <div class="flexcontainer"> <?php
		?> <div class="h1"> <?php echo "Userpage of $charactername $familyname"; if($characteralive==0){echo " [+]";} ?> </div> <?php
			?>
			<div class="notificationbox2">
				<div class="notificationbox3">
					<a href="rankings.php?type=characters&country=<?php echo "$nationality"; ?>&sort=name&order=asc">
					<img src="img/membersicon.png">
					</a>
				</div>
			</div>
			<?php
		 
	?> </div> <?php
?> </div> <?php
?> <hr /> <?php

//buttons for pages
if($characteruser == $username){
	?>
	<div class="textbox">
		<form method="post" action="">
			<?php if($characterwayoflife == "none" AND $characterage>=18){?><button type="submit" name="wayoflifeform" />Select a way of life</button><?php } ?>
			<button type="submit" name="foodform" />Consume food</button>
			<button type="submit" name="sleepform" />Sleep</button>
			<button type="submit" name="houseform" />Built house</button>
			<button type="submit" name="locationform" />Change your location</button>
			<!-- <button type="submit" name="nationalityform" />Change your nationality</button> -->
			<button type="submit" name="lotteryform" />Join the lottery</button>
			<button type="submit" name="duelform" />Duel player</button>
			<button type="submit" name="searchform" />Search user</button>
			<button type="submit" name="descriptionform" />Change user description</button>
			<button type="submit" name="debutant" />Present debutant to your court</button>
			<?php if($countinhiding == 0){ ?> <button type="submit" name="hiding" />Go into hiding</button> <?php }else{ ?> <button type="submit" name="hiding" />Go out of hiding</button> <?php } ?>
			
		</form>
	</div>
	<?php
}else{
	?> <div class="everythingOnOneLine"> <?php
	if($characteralive == 1 AND $countnumbercharacters >= 1){//grant landed title
		?>
		<div class="textbox">
			<form method="post" action="">
				<?php
				if($countplayertitles > 1){//je kan niet alles weggeven ?>
				<button type="submit" name="grantlandedtitle" />Grant landed title</button>
				<?php } ?>
				<button type="submit" name="donategold" />Donate gold to this character</button>
			</form>
		</div>
		<?php
	}
	
	if($usercharacterwayoflife == "assassin"){
		$result4 = $mysqli->query("SELECT * FROM traits WHERE name='assassin'") or die($mysqli->error());
		$row4 = mysqli_fetch_array($result4);
		$traitid = $row4['id'];
		
		$result4 = $mysqli->query("SELECT * FROM traitscharacters WHERE characterid='$usercharacterid' AND traitid='$traitid'") or die($mysqli->error());
		$row4 = mysqli_fetch_array($result4);
		$extrainfo = $row4['extrainfo'];
		
		if($extrainfo == NULL OR $extrainfo == "NULL"){
			?>
			<div class="textbox">
				<form method="post" action="">
					<button type="submit" name="assassinate" />Mark for assassination</button>
				</form>
			</div>
			<?php
		}
	}
	
	if($usercharacterid == $usercharacterliege AND $userhighestitle < $characterhighestitle){//swear fealty alleen als userliege gelijk aan zichzelf en user highes title lager dan van nieuwe liege
		$result3 = "SELECT * FROM titles WHERE holderid = '$usercharacterid'";
		$rs_result = $mysqli->query($result3);
		$count = $rs_result->num_rows;
		
		$swearfealty=0;
		if($count != 0){
			while($row = $rs_result->fetch_assoc()) {//ga door titles van user
				$titleid=$row["id"];
				$titletype=$row["holdingtype"];
				$titleholdingid=$row["holdingid"];
				
				if($titletype == "kingdom"){
					
				}elseif($titletype == "duchy"){
					$result4 = $mysqli->query("SELECT * FROM region WHERE id='$titleholdingid'") or die($mysqli->error());
					$row4 = mysqli_fetch_array($result4);
					$titlecurowner = $row4['curowner'];
					
					$result4 = $mysqli->query("SELECT * FROM countryinfo WHERE country='$titlecurowner'") or die($mysqli->error());
					$row4 = mysqli_fetch_array($result4);
					$titlecharacterowner = $row4['characterowner'];
					
					if($titlecharacterowner == $characterid){//if character owns title in region of user than swear fealty
						$swearfealty = 1;
					}
				}
			}
		}
		
		if($swearfealty == 1){
			?>
			<div class="textbox">
				<form method="post" action="">
					<button type="submit" name="swearfealty" />Swear fealty</button>
				</form>
			</div>
			<?php
		}
	}
	?> </div> <?php
}

if(isset($_POST['assassinate'])){
	$result4 = $mysqli->query("SELECT * FROM traits WHERE name='assassin'") or die($mysqli->error());
	$row4 = mysqli_fetch_array($result4);
	$traitid = $row4['id'];
	
	$result4 = $mysqli->query("SELECT * FROM traitscharacters WHERE characterid='$usercharacterid' AND traitid='$traitid'") or die($mysqli->error());
	$row4 = mysqli_fetch_array($result4);
	$extrainfo = $row4['extrainfo'];
	$traitscharactersid = $row4['id'];
	
	if($extrainfo == NULL OR $extrainfo == "NULL"){
		$sql = "UPDATE traitscharacters SET extrainfo='$characterid', extrainfo2='0' WHERE id='$traitscharactersid'";
		mysqli_query($mysqli, $sql);
		
		$content = "You marked <a href='account.php?user=$characteruser&charid=$characterid'>$charactername $familyname</a> for assassination.";
		seteventuser($content,$username);
		
		echo'<div class="boxed">Done!</div>';
	}
	?>
	<script>
	    if ( window.history.replaceState ) {
	        window.history.replaceState( null, null, window.location.href );
	    }
	</script>
	<?php
}

if(isset($_POST['wayoflifeform'])){
	$result = $mysqli->query("SELECT * FROM traits WHERE type='way of life'") or die($mysqli->error());
	$columnValues = Array();
	
	echo nl2br ("<div class=\"t1\">Select a way of life.</div>");
	?>
	<form method="post" action="">
	    <select required name="wayoflifetype" type="text">
	    <option value="" disabled selected hidden>Select one</option> 
	    <?php       
	    // Iterating through the product array
		while ( $row = mysqli_fetch_assoc($result) ) {
			$traitid = $row['id'];
			$traitname = $row['name'];
		    ?>
		    <option value="<?php echo "$traitid"; ?>"><?php echo "$traitname"; ?></option>
		    <?php
		}
	    ?>
	    </select> 
	    <button type="submit" name="wayoflifeform2" />Select</button>
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

if(isset($_POST['wayoflifeform2'])){
	$traitid = $mysqli->escape_string($_POST['wayoflifetype']);
	
	$result2 = $mysqli->query("SELECT * FROM traits WHERE id='$traitid'") or die($mysqli->error());
	$row2 = mysqli_fetch_array($result2);
	$traitname = $row2['name'];
	$traittype = $row2['type'];
	
	if($traittype == "way of life"){
		$sql = "INSERT INTO traitscharacters (characterid, traitid, date) " 
		. "VALUES ('$characterid','$traitid',NOW())";
 		mysqli_query($mysqli, $sql);
		
		$content = "You adopted the way of the $traitname";
		seteventuser($content,$username);
		
		echo'<div class="boxed">Done!</div>';
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

if(isset($_POST['swearfealty'])){
	echo nl2br ("<div class=\"t1\">Swear fealty to this character. This option is available because you are in your own liege and your highest title is lower
	than the highest title of this character and one of the titles of this character owns the land of one of your titles. This action will put you in the liege of this character.</div>");
	?>
	<div class="textbox">
		<form method="post" action="">
			<button type="submit" name="swearfealty2" />Are you sure?</button>
		</form>
	</div>
	<?php
	?>
	<script>
	    if ( window.history.replaceState ) {
	        window.history.replaceState( null, null, window.location.href );
	    }
	</script>
	<?php
}
			
if(isset($_POST['swearfealty2'])){
	$sql = "UPDATE characters SET liege='$characterid' WHERE id='$usercharacterid'";
	mysqli_query($mysqli, $sql);
	
	//update regionowner en companies en location
	$result = $mysqli->query("SELECT * FROM characters WHERE id='$characterid'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$nliegecharactername=$row['name'];
	$nliegecharacterfamilyid=$row['familyid'];
	$nliegecharacteruser=$row['user'];
	$nliegecharacterid=$row['id'];
	
	$result = $mysqli->query("SELECT * FROM users WHERE username='$nliegecharacteruser'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$nliegecharacterreligion=$row['userreligion'];
	$nliegecharacternationality=$row['nationality'];
	
	$sql = "UPDATE region SET curowner='$nliegecharacternationality' WHERE characterowner='$usercharacterid'";
	mysqli_query($mysqli, $sql);
	
	$result2 = "SELECT * FROM region WHERE characterowner='$usercharacterid'";
	$rs_result = $mysqli->query($result2);
	$count = $rs_result->num_rows;
	
	if($count != 0){
		while($row = $rs_result->fetch_assoc()) {
			$regionname=$row["name"];
			$regionname=$mysqli->escape_string($regionname);
			
			$sql = "UPDATE users SET location='$nliegecharacternationality' WHERE location2='$regionname'"; //update country of users in region
			mysqli_query($mysqli, $sql);
			
			$sql = "UPDATE characters SET location='$nliegecharacternationality' WHERE location2='$regionname'"; //update country of characters in region
			mysqli_query($mysqli, $sql);
			
			$sql = "UPDATE companies SET countryco='$nliegecharacternationality' WHERE region='$regionname'"; //update country of companies
			mysqli_query($mysqli, $sql);
		}
	}
	
	echo'<div class="boxed">Done!</div>';
	
	$content= "$usercharactername swore fealty to you and is now in your liege.";
	$sql = "INSERT INTO events (date, content, extrainfo) " 
     . "VALUES (NOW(),'$content','$characteruser')";
	mysqli_query($mysqli2, $sql);
	?>
	<script>
	    if ( window.history.replaceState ) {
	        window.history.replaceState( null, null, window.location.href );
	    }
	</script>
	<?php
}
		
if(isset($_POST['grantlandedtitle'])){
	$result = $mysqli->query("SELECT * FROM titles WHERE holderid='$usercharacterid'") or die($mysqli->error());
	$columnValues = Array();
	
	echo nl2br ("<div class=\"t1\">Grant one of your titles to this character.</div>");
	?>
	<form method="post" action="">
	    <select required name="granttitleid" type="text">
	    <option value="" disabled selected hidden>Select a holding?</option> 
	    <?php       
	    // Iterating through the product array
		while ( $row = mysqli_fetch_assoc($result) ) {
			$holdingtype = $row['holdingtype'];
			$holdingid = $row['holdingid'];
			$titleid = $row['id'];
			if($holdingtype=="duchy"){
				$result2 = $mysqli->query("SELECT * FROM region WHERE id='$holdingid'") or die($mysqli->error());
				$row2 = mysqli_fetch_array($result2);
				$holdingname = $row2['name'];
			}elseif($holdingtype=="kingdom"){
				$result2 = $mysqli->query("SELECT * FROM countryinfo WHERE id='$holdingid'") or die($mysqli->error());
				$row2 = mysqli_fetch_array($result2);
				$holdingname = $row2['country'];
			}
		    ?>
		    <option value="<?php echo "$titleid"; ?>"><?php echo "$holdingname"; ?></option>
		    <?php
		}
	    ?>
	    </select> 
	    <button type="submit" name="grantlandedtitle2" />Grant title</button>
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

if(isset($_POST['grantlandedtitle2'])){
	$granttitleid = $mysqli->escape_string($_POST['granttitleid']);
	
	$result2 = $mysqli->query("SELECT * FROM titles WHERE id='$granttitleid'") or die($mysqli->error());
	$row2 = mysqli_fetch_array($result2);
	$granttitleholderid = $row2['holderid'];
	$granttitletholdingid = $row2['holdingid'];
	$granttitletype = $row2['holdingtype'];
	
	//update regionowner en companies en location
	$result = $mysqli->query("SELECT * FROM characters WHERE id='$characterid'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$nliegecharactername=$row['name'];
	$nliegecharacterfamilyid=$row['familyid'];
	$nliegecharacteruser=$row['user'];
	$nliegecharacterid=$row['id'];
	
	$result = $mysqli->query("SELECT * FROM users WHERE username='$nliegecharacteruser'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$nliegecharacterreligion=$row['userreligion'];
	$nliegecharacternationality=$row['nationality'];
	
	if($granttitleholderid == $usercharacterid){
		$sql = "UPDATE titles SET holderid='$characterid' WHERE id='$granttitleid'";
		mysqli_query($mysqli, $sql);
		
		if($granttitletype=="kingdom"){
			$sql = "UPDATE countryinfo SET countrypresident='$characteruser', characterowner='$characterid' WHERE id='$granttitletholdingid'";
			mysqli_query($mysqli, $sql);
			
			$result = $mysqli->query("SELECT country FROM countryinfo WHERE id='$granttitletholdingid'") or die($mysqli->error());
			$row = mysqli_fetch_array($result);
			$granttitletname=$row['country'];
			$content= "$usercharactername granted the the kingdom of $granttitletname to you";
		}elseif($granttitletype=="duchy"){
			$sql = "UPDATE region SET characterowner='$characterid', curowner='$nliegecharacternationality' WHERE id='$granttitletholdingid'";
			mysqli_query($mysqli, $sql);
			
			$result = $mysqli->query("SELECT name FROM region WHERE id='$granttitletholdingid'") or die($mysqli->error());
			$row = mysqli_fetch_array($result);
			$granttitletname=$row['name'];
			$granttitletname=$mysqli->escape_string($granttitletname);
			
			$sql = "UPDATE users SET location='$nliegecharacternationality' WHERE location2='$granttitletname'"; //update country of companies
			mysqli_query($mysqli, $sql);
			
			$sql = "UPDATE companies SET countryco='$nliegecharacternationality' WHERE region='$granttitletname'"; //update country of companies
			mysqli_query($mysqli, $sql);
			
			$content= "$usercharactername granted the the kingdom of $granttitletname to you";
		}
		$sql = "INSERT INTO events (date, content, extrainfo) " 
	     . "VALUES (NOW(),'$content','$characteruser')";
		mysqli_query($mysqli2, $sql);
		echo'<div class="boxed">Done!</div>';
	}
	?>
	<script>
	    if ( window.history.replaceState ) {
	        window.history.replaceState( null, null, window.location.href );
	    }
	</script>
	<?php
}

if(isset($_POST['donategold'])){
	?>
	<form method="post" action="">
		<input type="number" size="25" required autocomplete="off" id="donateamount" name="donateamount" min="0.01" max="100" step="0.01" />
		<button type="submit" name="donategold2"  /><?php echo "Accept"; ?></button>
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
		
if(isset($_POST['donategold2'])){
	$donateamount = $mysqli->escape_string($_POST['donateamount']);
	
	if($donateamount > 0 AND $donateamount <= 100){
		$result2 = $mysqli->query("SELECT * FROM currency WHERE usercur='$user'") or die($mysqli->error());
		$row2 = mysqli_fetch_array($result2);
		$usergold = $row2['gold'];
		
		$result2 = $mysqli->query("SELECT * FROM currency WHERE usercur='$username'") or die($mysqli->error());
		$row2 = mysqli_fetch_array($result2);
		$usernamegold = $row2['gold'];
		
		$usergold = $usergold + $donateamount;
		$usernamegold = $usernamegold - $donateamount;
		
		if($usernamegold >= 0){
			$sql = "UPDATE currency SET gold='$usergold' WHERE usercur='$user'";
			mysqli_query($mysqli, $sql);
			
			$sql = "UPDATE currency SET gold='$usernamegold' WHERE usercur='$username'";
			mysqli_query($mysqli, $sql);
			
			$content= "<a href='account.php?user=$username&charid=$usercharacterid'>$usercharactername</a> donated $donateamount gold to you.";
			seteventuser($content,$user);
			
			$content= "You donated $donateamount gold to <a href='account.php?user=$user&charid=$characterid'>$charactername</a>";
			seteventuser($content,$username);
			
			echo'<div class="boxed">Done!</div>';
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

if(isset($_POST['hiding'])){
	//check if character is in hiding
	$result4 = $mysqli->query("SELECT * FROM traits WHERE name='in hiding'") or die($mysqli->error());
	$row4 = mysqli_fetch_array($result4);
	$inhidingtraitid = $row4['id'];
	
	$result3 = "SELECT * FROM traitscharacters WHERE characterid='$usercharacterid' AND traitid='$inhidingtraitid'";
	$rs_result2 = $mysqli->query($result3);
	$countinhiding = $rs_result2->num_rows;//aantal titles
	
	if($countinhiding == 0){
		echo nl2br ("<div class=\"t1\">Hiding will remove the location from your account page but will disable your ability to change your location. This action will cost 1 gold.</div>");
			
		?>
		<div class="textbox">
			<form method="post" action="">
				<button type="submit" name="hiding2" />Accept</button>
			</form>
		</div>
		<?php
	}else{
		?>
		<div class="textbox">
			<form method="post" action="">
				<button type="submit" name="hiding2" />Accept</button>
			</form>
		</div>
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

if(isset($_POST['hiding2'])){
	//check if character is in hiding
	$result4 = $mysqli->query("SELECT * FROM traits WHERE name='in hiding'") or die($mysqli->error());
	$row4 = mysqli_fetch_array($result4);
	$inhidingtraitid = $row4['id'];
	
	$result3 = "SELECT * FROM traitscharacters WHERE characterid='$usercharacterid' AND traitid='$inhidingtraitid'";
	$rs_result2 = $mysqli->query($result3);
	$countinhiding = $rs_result2->num_rows;//aantal titles
	
	if($countinhiding == 0){
		$result4 = $mysqli->query("SELECT * FROM currency WHERE usercur='$username'") or die($mysqli->error());
		$row4 = mysqli_fetch_array($result4);
		$usergold = $row4['gold'];
		
		$result4 = $mysqli->query("SELECT * FROM region WHERE name='$usercharacterlocation2'") or die($mysqli->error());
		$row4 = mysqli_fetch_array($result4);
		$regiontaxtoday = $row4['taxtoday'];
		$regionowner = $row4['curowner'];
		
		$result4 = $mysqli->query("SELECT * FROM countryinfo WHERE country='$regionowner'") or die($mysqli->error());
		$row4 = mysqli_fetch_array($result4);
		$countrygold = $row4['gold'];
		
		$usergold = $usergold - 1;
		$regiontaxtoday = $regiontaxtoday + 1;
		$countrygold = $countrygold + 1;
		if($usergold >= 0){
			$sql = "INSERT INTO traitscharacters (characterid, traitid, date) " 
			. "VALUES ('$usercharacterid','$inhidingtraitid',NOW())";
	 		mysqli_query($mysqli, $sql);
			
			$sql = "UPDATE currency SET gold = '$usergold' WHERE usercur = '$username'";
			mysqli_query($mysqli, $sql);
			
			$sql = "UPDATE region SET taxtoday = '$regiontaxtoday' WHERE name = '$usercharacterlocation2'";
			mysqli_query($mysqli, $sql);
			
			$sql = "UPDATE countryinfo SET gold = '$countrygold' WHERE country = '$regionowner'";
			mysqli_query($mysqli, $sql);
			
			echo'<div class="boxed">Done!</div>';
		}else{
			echo'<div class="boxed">You do not have enough gold!</div>';
		}
	}else{
		$sql = "DELETE FROM traitscharacters WHERE characterid='$usercharacterid' AND traitid='$inhidingtraitid'";
		mysqli_query($mysqli, $sql);
		
		echo'<div class="boxed">Done!</div>';
	}
	
	?>
	<script>
	    if ( window.history.replaceState ) {
	        window.history.replaceState( null, null, window.location.href );
	    }
	</script>
	<?php
}

if(isset($_POST['debutant'])){
	echo nl2br ("<div class=\"t1\">Presenting a debutant to your court will invite a debutant to your court that will live there and will be of your liege. This action will cost 3 gold.</div>");
		
	?>
	<div class="textbox">
		<form method="post" action="">
			<button type="submit" name="debutant2" />Present debutant to your court</button>
		</form>
	</div>
	<?php
}

if(isset($_POST['debutant2'])){
	$result4 = $mysqli->query("SELECT * FROM currency WHERE usercur='$username'") or die($mysqli->error());
	$row4 = mysqli_fetch_array($result4);
	$gold = $row4['gold'];
	$gold=$gold-3;
	
	if($gold >=0){
		//count lines in txt file
		$file="names/first-names.txt";
		$linecount = 0;
		$handle = fopen($file, "r");
		while(!feof($handle)){
		  $line = fgets($handle);
		  $linecount++;
		}
		fclose($handle);
		$rname=rand(1, $linecount);
		
		//slect firstname
		$file="names/first-names.txt";
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
		
		//count lines in txt file
		$file="names/names.txt";
		$linecount = 0;
		$handle = fopen($file, "r");
		while(!feof($handle)){
		  $line = fgets($handle);
		  $linecount++;
		}
		fclose($handle);
		$rname=rand(1, $linecount);
		
		//slect lastname
		$file="names/names.txt";
		$linecount = 0;
		$handle = fopen($file, "r");
		while(!feof($handle)){
		  $line = fgets($handle);
		  if($linecount == $rname){
		  	$lastname=$line;
		  }
		  $linecount++;
		}
		fclose($handle);
		
		//sex
		$nsex=rand(0, 1);
		if($usercharactersex=="male"){
			$nsex="female";
		}else{
			$nsex="male";
		}
		
		//select birthplace
		$result4 = $mysqli->query("SELECT * FROM characters WHERE id='$usercharacterid'") or die($mysqli->error());
		$row4 = mysqli_fetch_array($result4);
		$usercharacterbirthplace = $row4['birthplace'];
		$liegenationality = $row4['nationality'];
		$liegelocation = $row4['location'];
		$liegelocation2 = $row4['location2'];
		$liegelocation2=$mysqli->escape_string($liegelocation2);
		
		$sql = "INSERT INTO characters (alive, age, type, sex, race, user, fertile, name, liege, lastonline,birthplace,location,location2,nationality) " 
		. "VALUES ('1','20','npc','$nsex','$characterrace','npc','1','$firstname','$characterid',NOW(),'$usercharacterbirthplace','$liegelocation','$liegelocation2','$liegenationality')";
		mysqli_query($mysqli, $sql);
		
		$lastid = $mysqli->insert_id;
		$insertcharid = $lastid; //character id
		$sql = "INSERT INTO family (name, heritagelaw, dynast) " 
		. "VALUES ('$lastname','1','$lastid')";
		mysqli_query($mysqli, $sql);
		
		$lastid = $mysqli->insert_id; //family id
		$sql = "UPDATE characters SET familyid='$lastid' WHERE id='$insertcharid'";
		mysqli_query($mysqli, $sql);
		
		$sql = "UPDATE currency SET gold='$gold' WHERE usercur='$username'";
		mysqli_query($mysqli, $sql);
		
		//add traits to debutant
		addtraitstochild($lastid, 0);
		
		$content= "A $nsex debutant named $firstname $lastname has arrived at your court";
		$sql = "INSERT INTO events (date, content, extrainfo) " 
	     . "VALUES (NOW(),'$content','$username')";
		mysqli_query($mysqli2, $sql);
		
		echo'<div class="boxed">Done!</div>';
		
		?>
		<script>
		    if ( window.history.replaceState ) {
		        window.history.replaceState( null, null, window.location.href );
		    }
		</script>
		<?php
	}else{
		echo'<div class="boxed">You do not have enough gold!</div>';
		
		?>
		<script>
		    if ( window.history.replaceState ) {
		        window.history.replaceState( null, null, window.location.href );
		    }
		</script>
		<?php
	}
}

?> <div class="textbox"> <?php
?> <div class="accountleft"> <?php

?> 
<table id="table1">
    <tr>
       <td><?php echo "Dynasty"; ?></td>
       <td><?php echo "<a href='familypage.php?charid=$characterid&type=1'>$familyname</a>"; ?></td>
    </tr>	
    <tr>
       <td><?php echo "User"; ?></td>
       <td><?php echo "$characteruser"; ?></td>
    </tr>
    <tr>
       <td><?php echo "Sex"; ?></td>
       <td><?php echo "$charactersex"; ?></td>
    </tr>	
    <tr>
       <td><?php echo "Race"; ?></td>
       <td><?php echo "$characterrace"; ?></td>
    </tr>
    <tr>
       <td><?php echo "Age"; ?></td>
       <td><?php echo "$characterage"; ?></td>
    </tr>
    <tr>
       <td><?php echo "Liege"; ?></td>
       <td><?php echo "<a href='account.php?user=$characterliegeuser&charid=$characterliege'>$characterliegename $characterliegefamilyname </a>"; ?></td>
    </tr>
    <tr>
       <td><?php echo "Father"; ?></td>
       <td><?php echo "<a href='account.php?user=$characterfatheruser&charid=$characterfather'>$characterfathername $characterfatherfamilyname</a>"; ?></td>
    </tr>
    <tr>
       <td><?php echo "Mother"; ?></td>
       <td><?php echo "<a href='account.php?user=$charactermotheruser&charid=$charactermother'>$charactermothername $charactermotherfamilyname</a>"; ?></td>
    </tr>
    <tr>
       <td><?php echo "Strength"; ?></td>
       <td><?php echo "$strength"; ?></td>
    </tr>
    <tr>
       <td><?php echo "Energy"; ?></td>
       <td><?php echo "$energy"; ?></td>
    </tr>
    <tr>
       <td><?php echo "Dominance"; ?></td>
       <td><?php echo "$dominance"; ?></td>
    </tr>  
    <tr>
       <td><?php echo "Nationality"; ?></td>
       <td>
       	<?php 
       echo "$nationality"; 
       if($countkingdomtitles > 1){
			?>
			<form method="post" action="">
				<button type="submit" name="changenationalityking" /><?php echo "Change nationality"; ?></button>
			</form>
			<?php
       }
       	?>
       	
       </td>
    </tr>
    <tr>
       <td><?php echo "Location"; ?></td>
       <td>
       	<?php 
       if($countinhiding == 0 OR $username == $characteruser){
	       if($characteruser == "npc"){
	       		echo "$characterlocation, $characterlocation2"; 
		   }else{
		   		echo "$location, $location2"; 
		   }
	   }else{
			echo "In hiding";
	   }
       ?>
       </td>
    </tr> 
    <tr>
       <td><?php echo "Location of house"; ?></td>
       <td><?php echo "$housepos"; ?></td>
    </tr>
    <tr>
       <td><?php echo "Spouse"; ?></td>
       <td>
       	<?php 
       	if($countmarried != 0){
			echo "<a href='account.php?user=$marrieduser&charid=$marriedid'>$marriedname $marriedfamilyname </a>";
       	}else{
       		//echo "<a href='rankings.php?type=characters&sort=married&order=asc&country=$characterid'>Get married</a>";
       		//get married
       		if($characteralive == 1){
				?>
				<div class="textbox">
					<form method="post" action="">
						<button type="submit" name="marriageform" />Arrange marriage</button>
					</form>
				</div>
				<?php
			}
       	}
       	?>
       	</td>
    </tr>
    <tr>
       <td><?php echo "Children"; ?></td>
       <td>
       	<?php 
			$result3 = "SELECT * FROM characters WHERE mother = '$characterid' OR father= '$characterid' ORDER BY age DESC";
			$rs_result = $mysqli->query($result3);
			$count = $rs_result->num_rows;
       	if($count != 0){			
			while($row = $rs_result->fetch_assoc()) {
				$childid=$row["id"];
				$childname=$row["name"];
				$childuser=$row["user"];
				
				//check if heir
				$result2 = $mysqli->query("SELECT * FROM family WHERE heir='$childid'") or die($mysqli->error());
				$row2 = mysqli_fetch_array($result2);
				$countheir = $result2->num_rows;
				if($countheir != 0){
					echo "<a href='account.php?user=$childuser&charid=$childid' style='color: gold;'>$childname </a>";
				}else{
					echo "<a href='account.php?user=$childuser&charid=$childid'>$childname </a>";
				}
				echo ", ";
			}
       	}
       	?>
       	</td>
    </tr>
    <tr>
       <td><?php echo "Vassals"; ?></td>
       <td><?php echo "<a href='familypage.php?charid=$characterid&type=2'>View vassals</a>"; ?></td>
    </tr>
    <?php
	if($workid != 0){
		$result2 = $mysqli->query("SELECT companyname, owner, countryco FROM companies WHERE id='$workid'") or die($mysqli->error());
		$row2 = mysqli_fetch_array($result2);
		$companyname = $row2['companyname'];
		$countryco = $row2['countryco'];
		$owner = $row2['owner'];
		
		$result2 = $mysqli->query("SELECT currency FROM countryinfo WHERE country='$countryco'") or die($mysqli->error());
		$row2 = mysqli_fetch_array($result2);
		$currency = $row2['currency'];
		
		?>
	    <tr>
	       <td><?php echo "Work"; ?></td>
	       <td><?php echo "Working for $owner at $companyname for $salary $currency"; ?></td>
	    </tr>
		<?php
	}
    ?>
    <?php
	if($userreligion != "NULL" AND $userreligion != NULL){
		?>
	    <tr>
	       <td><?php echo "Religion"; ?></td>
	       <td><?php echo "$userreligion"; ?></td>
	    </tr>
		<?php
	}
    ?>
    <?php
	$politicalparty = $row['politicalparty'];
	if($politicalparty != 0){
		$result2 = $mysqli->query("SELECT name FROM politicalparty WHERE id='$politicalparty'") or die($mysqli->error());
		$row2 = mysqli_fetch_array($result2);
		$politicalpartyname = $row2['name'];
		?>
	    <tr>
	       <td><?php echo "Political party"; ?></td>
	       <td><?php echo "$politicalpartyname"; ?></td>
	    </tr>
		<?php
	}
    ?>
    <?php
	$militaryunit = $row['militaryunit'];
	if($militaryunit != 0){
		$result2 = $mysqli->query("SELECT name FROM militaryunit WHERE id='$militaryunit'") or die($mysqli->error());
		$row2 = mysqli_fetch_array($result2);
		$militaryunitname = $row2['name'];
		?>
	    <tr>
	       <td><?php echo "Military unit"; ?></td>
	       <td><?php echo "$militaryunitname"; ?></td>
	    </tr>
		<?php
	}
    ?>
    <tr>
       <td><?php echo "Traits <a href='rankings.php?type=traits&country=$characterid&sort=name&order=asc'>*</a>"; ?> </td>
       <td>
       	<?php 
       	$result3 = "SELECT * FROM traitscharacters WHERE characterid='$characterid'";
		$rs_result2 = $mysqli->query($result3);
		$count2 = $rs_result2->num_rows;//aantal titles
		if($count2 != 0){
			while($row2 = $rs_result2->fetch_assoc()) {//ga door traits heen
				$traitid=$row2["traitid"];
        $traitinvissible=$row2["invissible"];
			
				$result4 = $mysqli->query("SELECT * FROM traits WHERE id='$traitid'") or die($mysqli->error());
				$row4 = mysqli_fetch_array($result4);
				$traitname = $row4['name'];
        
        if($traitinvissible == 0){
          echo "$traitname, ";
        }
			}
		}
       ?>
       </td>
    </tr>
    <tr>
       <td><?php echo "Dungeon <a href='rankings.php?type=prison&country=$characterid&sort=name&order=asc'>*</a>"; ?> </td>
       <td>
       	<?php 
		$result4 = $mysqli->query("SELECT * FROM traits WHERE name='imprisoned'") or die($mysqli->error());
		$row4 = mysqli_fetch_array($result4);
		$prisontraitid = $row4['id'];
       	
       	$result3 = "SELECT * FROM traitscharacters WHERE traitid='$prisontraitid' AND extrainfo='$characterid'";
		$rs_result2 = $mysqli->query($result3);
		$count2 = $rs_result2->num_rows;//aantal titles
		
		echo "$count2";		
       ?>
       </td>
    </tr>
</table>

<?php

if(isset($_POST['changenationalityking'])){
	?><hr class="side"><?php
	echo nl2br ("<div class=\"t1\">Change your nationality here. This option is available because you own more than one kingdom and is necessary to acces the country king page of each country. Changing your nationality is free of cost and can be done unlimited.</div>");
	
	$result = $mysqli->query("SELECT * FROM titles WHERE holdingtype='kingdom' AND holderid='$usercharacterid'") or die($mysqli->error());
	$columnValues = Array();
	?>
	<form method="post" action="">
	    <select required name="nationality" type="text" autofocus="">
	    <option value="" disabled selected hidden>Select a nationality?</option> 
	    <?php       
	    // Iterating through the product array
		while ( $row = mysqli_fetch_assoc($result) ) {
			$titleid = $row['id'];
			$holdingid = $row['holdingid'];
			echo "$titleid";
			$result4 = $mysqli->query("SELECT * FROM countryinfo WHERE id='$holdingid'") or die($mysqli->error());
			$row4 = mysqli_fetch_array($result4);
			$countryname = $row4['country'];
		    ?>
		    <option value="<?php echo $countryname; ?>"><?php echo $countryname; ?></option>
		    <?php
		}
	    ?>
	    </select> 
	    <button type="submit" name="changenationalityking2" />Change nationality</button>
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

if(isset($_POST['changenationalityking2'])){
	$newnationality = $mysqli->escape_string($_POST['nationality']);
	
	$result4 = $mysqli->query("SELECT * FROM countryinfo WHERE country='$newnationality'") or die($mysqli->error());
	$row4 = mysqli_fetch_array($result4);
	$countryid = $row4['id'];
	
	$result4 = $mysqli->query("SELECT * FROM titles WHERE holdingid='$countryid'") or die($mysqli->error());
	$row4 = mysqli_fetch_array($result4);
	$titleholderid = $row4['holderid'];
	
	if($titleholderid == $usercharacterid){
		$sql = "UPDATE users SET nationality='$newnationality' WHERE username='$username'";
		mysqli_query($mysqli, $sql);
		
		echo'<div class="boxed">Done!</div>';
	}
	?>
	<script>
	    if ( window.history.replaceState ) {
	        window.history.replaceState( null, null, window.location.href );
	    }
	</script>
	<?php
}

if(isset($_POST['marriageform'])){
	$link="marriage.php?charid=$characterid";
	?>
	<script>
	    window.location = '<?php echo "$link"; ?>';
	</script>
	<?php
}

?> </div> <?php

?> <div class="accountright"> <?php
$result3 = "SELECT * FROM titles WHERE holderid = '$characterid'";
$rs_result = $mysqli->query($result3);
$count = $rs_result->num_rows;

?> 
<table id="table1">
    <tr>
       <td><?php echo "Holdings <a href='rankings.php?type=claim&country=$characterid&sort=type&order=asc'>*</a>"; ?></td>
       <td>
       	<?php
       		if($count != 0){
				while($row = $rs_result->fetch_assoc()) {
					$titleid=$row["id"];
					$holdingtype=$row["holdingtype"];
					$holdingid=$row["holdingid"];
					
					if($holdingtype=="kingdom"){
						$result4 = $mysqli->query("SELECT * FROM countryinfo WHERE id='$holdingid'") or die($mysqli->error());
						$row4 = mysqli_fetch_array($result4);
						$countryname = $row4['country'];
						
						echo "Kingdom of $countryname";
					}elseif($holdingtype=="duchy"){
						$result4 = $mysqli->query("SELECT name FROM region WHERE id='$holdingid' LIMIT 1") or die($mysqli->error());
						$row4 = mysqli_fetch_array($result4);
						$regionname = $row4['name'];
						
						echo "Duchy of $regionname";
					}
					echo ", ";
				}
			}else{
				echo "None";
			}
       ?>	
       </td>
    </tr>	
</table>
<?php

echo nl2br ("<div class=\"bold\">About</div>");

$about=$row['about'];
$about = $purifier->purify($about);
echo nl2br ("<div class=\"t1\">$about</div>");

?> </div> <?php
?> </div> <?php

if($characteruser==$username){
	
	?> <div class="textbox"> <?php
	//echo inventory
	echo nl2br ("<div class=\"bold\">Items in your inventory</div>");
	?><hr class="side"><?php
	
	$result = $mysqli->query("SELECT * FROM inventory WHERE userinv='$username'") or die($mysqli->error());
	$row = mysqli_fetch_assoc($result);
	?> 
	<div class="scroll">
	<table id="table1">
		<tr>
		<?php
			$i=0;
			foreach ($row as $key => $value) {
				if($i>0 AND $i<11){
					?><th><?php echo $key; ?></th><?php
				}
				$i=$i+1;
			}
		?> </tr> <?php
			$i=0;
			foreach ($row as $key => $value) {
				if($i>0 AND $i<11){
					?><td><?php echo $value; ?></td><?php
				}
				$i=$i+1;
			}
			?>
	</table>
	<?php 
	?> 
	<table id="table1">
		<tr>
		<?php
			$i=0;
			foreach ($row as $key => $value) {
				if($i>10){
					?><th><?php echo $key; ?></th><?php
				}
				$i=$i+1;
			}
		?> </tr> <?php
			$i=0;
			foreach ($row as $key => $value) {
				if($i>10){
					?><td><?php echo $value; ?></td><?php
				}
				$i=$i+1;
			}
			?>
	</table>
	</div>
	<?php 
	?> </div> <?php
	
	?> <div class="textbox"> <?php
	//echo currency
	echo nl2br("<div class=\"bold\">Currency</div>");
	?><hr class="side"><?php
	
	$result = $mysqli->query("SELECT * FROM currency WHERE usercur='$username'") or die($mysqli->error());
	$row = mysqli_fetch_assoc($result);
	?> 
	<div class="scroll">
	<table id="table1">
		<tr>
		<?php
			foreach ($row as $key => $value) {
				?><th><?php echo $key; ?></th><?php
			}
		?> </tr> <?php
			foreach ($row as $key => $value) {
				?><td><?php echo $value; ?></td><?php
			}
			?>
	</table>
	</div>
	<?php 
	?> </div> <?php
	
	?> <hr /> <?php
	
	//eat form
	if(isset($_POST['foodform'])){
		?> <div class="textbox"> <?php
		echo nl2br ("<div class=\"t1\">Consume food to regain energy. The energy the food resupplies per consumption is q*2.</div>");
		?>
		
		<br><br>
		<form method="post" action=""> 
			 <select name="type" type="text" autofocus>
		  		<option value="foodq1">food q1</option>
		  		<option value="foodq2">food q2</option>
		  		<option value="foodq3">food q3</option>
		  		<option value="foodq4">food q4</option>
		  		<option value="foodq5">food q5</option>
		   	</select>
		   	<label for="noeats">Number of food to eat:</label>
		   	<input type="number" size="25" required autocomplete="off" id="noeats" name='noeats' min="1" />
			<button type="submit" name="eat" />Consume food</button>
		</form>
		</div>
		<?php
		
		?>
		<script>
		    if ( window.history.replaceState ) {
		        window.history.replaceState( null, null, window.location.href );
		    }
		</script>
		<?php
	}
	
	//if eat
	if(isset($_POST['eat'])){
		?> <div class="textbox"> <?php
		$eattype = $mysqli->escape_string($_POST['type']);
		$noeats = $mysqli->escape_string($_POST['noeats']);
		$noeats = (int) $noeats;
		if($noeats <= 0){
			$noeats = 1;
		}

		if($eattype != "foodq1" && $eattype != "foodq2" && $eattype != "foodq3" && $eattype != "foodq4" && $eattype && "foodq5"){
			echo'<div class="boxed">Action is not permitted!</div>';
		}elseif($sleepstate=="asleep" || $sleepstate=="neither"){
			echo'<div class="boxed">You can not consume food while asleep!</div>';
		}else{
			$result = $mysqli->query("SELECT $eattype FROM inventory WHERE userinv='$username'") or die($mysqli->error());
			$row = mysqli_fetch_assoc($result);
			$noitems=$row[$eattype];
			
			$result = $mysqli->query("SELECT * FROM users WHERE username='$username'") or die($mysqli->error());
			$row = mysqli_fetch_array($result);
			$energy=$row['energy'];
			
			$noitems=$noitems-$noeats;
		
			if($noitems>=0){
				if($eattype=='foodq1'){
					$energy=$energy+2*$noeats;
				}elseif($eattype=='foodq2'){
					$energy=$energy+4*$noeats;
				}elseif($eattype=='foodq3'){
					$energy=$energy+6*$noeats;
				}elseif($eattype=='foodq4'){
					$energy=$energy+8*$noeats;
				}elseif($eattype=='foodq5'){
					$energy=$energy+10*$noeats;
				}
			}else{
				echo "You don't have enough food!";
				echo'<div class="boxed">You don not have enough food in your inventory!</div>';
				
			}
			if(($energy<=100) && ($noitems>=0)){
				$sql = "UPDATE inventory SET $eattype='$noitems' WHERE userinv='$username'";
				mysqli_query($mysqli, $sql);
				$sql = "UPDATE users SET energy='$energy' WHERE username='$username'";
				mysqli_query($mysqli, $sql);
			}else{
				echo'<div class="boxed">The maximum amount of energy is 100!</div>';
			}
		}
		?> </div> <?php
		
		?>
		<script>
		    if ( window.history.replaceState ) {
		        window.history.replaceState( null, null, window.location.href );
		    }
		</script>
		<?php
	}

	//sleep form
	if(isset($_POST['sleepform'])){
		?> <div class="textbox"> <?php		
		$result = $mysqli->query("SELECT state, statetime FROM users WHERE username='$username'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$state = $mysqli->escape_string($row['state']);
		$statetime = $mysqli->escape_string($row['statetime']);
		
		echo nl2br ("<div class=\"t1\">Status: $state</div>");
		
		if($state=="awake" || $state=="asleep"){
			?>
			<p id="demo"></p>
			<script>
			// Set the date we're counting down to 2018-04-12 15:37:25
			var countDownDate = new Date("<?php echo $statetime ?>").getTime();
			
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
			    document.getElementById("demo").innerHTML = "Duration of current status: " + days + "d " + hours + "h "
			    + minutes + "m " + seconds + "s ";
			    
			    // If the count down is over, write some text 
			    if (distance < 0) {
			        clearInterval(x);
			        document.getElementById("demo").innerHTML = "EXPIRED";
			        //window.location.reload(true);
			    }
			}, 1000);
			</script>
			<?php
		}
		
		if($state=="awake" || $state=="neither" || $state==NULL){
			echo nl2br ("<div class=\"t1\">You can only sleep when you are in the same region as your house or if you used a tavern. After sleeping your status will turn into awake which allows you to perform the daily activities.</div>");
			
			?>
			<br><br>
			<form method="post" action=""> 
				 <select name="sleeptime" type="text" autofocus>
			  		<option value="1h">1 hour</option>
			  		<option value="2h">2 hours</option>
			  		<option value="3h">3 hours</option>
			  		<option value="4h">4 hours</option>
			  		<option value="5h">5 hours</option>
			  		<option value="6h">6 hours</option>
			  		<option value="7h">7 hours</option>
			  		<option value="8h">8 hours</option>
			   	</select>
				<button type="submit" name="sleep" />Sleep</button>
			</form>
			<?php
		}
		?>
		<script>
		    if ( window.history.replaceState ) {
		        window.history.replaceState( null, null, window.location.href );
		    }
		</script>
		</div>
		<?php
	}
	
	if(isset($_POST['sleep'])){
		$sleeptime = $mysqli->escape_string($_POST['sleeptime']);
		
		$result = $mysqli->query("SELECT state, tavernup FROM users WHERE username='$username'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$state = $mysqli->escape_string($row['state']);
		$tavernup = $mysqli->escape_string($row['tavernup']);
		
		//check if imprisoned
		$result4 = $mysqli->query("SELECT * FROM traits WHERE name='imprisoned'") or die($mysqli->error());
		$row4 = mysqli_fetch_array($result4);
		$imprisonedtraitid = $row4['id'];
		
		$result3 = "SELECT * FROM traitscharacters WHERE characterid='$usercharacterid' AND traitid='$imprisonedtraitid'";
		$rs_result2 = $mysqli->query($result3);
		$count2 = $rs_result2->num_rows;//aantal titles
		if($count2 != 0){
			$characterimprisoned = 1;
		}else{
			$characterimprisoned = 0;
		}
		
		date_default_timezone_set('UTC'); //current date
		$datecur = date("Y-m-d H:i:s"); 
		//echo "$datecur";
		$date = new DateTime($datecur);
		
		if($sleeptime=="1h"){
			$date->add(new DateInterval('PT1H')); // P1D means a period of 1 day
			$sleephours=1;
		}elseif($sleeptime=="2h"){
			$date->add(new DateInterval('PT2H')); // P1D means a period of 1 day
			$sleephours=2;
		}elseif($sleeptime=="3h"){
			$date->add(new DateInterval('PT3H')); // P1D means a period of 1 day
			$sleephours=3;
		}elseif($sleeptime=="4h"){
			$date->add(new DateInterval('PT4H')); // P1D means a period of 1 day
			$sleephours=4;
		}elseif($sleeptime=="5h"){
			$date->add(new DateInterval('PT5H')); // P1D means a period of 1 day
			$sleephours=5;
		}elseif($sleeptime=="6h"){
			$date->add(new DateInterval('PT6H')); // P1D means a period of 1 day
			$sleephours=6;
		}elseif($sleeptime=="7h"){
			$date->add(new DateInterval('PT7H')); // P1D means a period of 1 day
			$sleephours=7;
		}elseif($sleeptime=="8h"){
			$date->add(new DateInterval('PT8H')); // P1D means a period of 1 day
			$sleephours=8;
		}		
		$Datenew1 = $date->format('Y-m-d H:i:s');
		
		if($tavernup==1 OR $characterimprisoned == 1){
			if($state=="awake" || $state=="neither" || $state==NULL){
				$sql = "UPDATE users SET state='asleep', sleephours='$sleephours', statetime='$Datenew1' WHERE username='$username'";
				mysqli_query($mysqli, $sql);
				
				echo'<div class="boxed">Done!</div>';
			}else{
				echo'<div class="boxed">You are already asleep!</div>';
			}
		}else{
			echo'<div class="boxed">To be able to sleep you should have your house in your current region or use a tavern!</div>';
		}
		
		?>
		<script>
		    if ( window.history.replaceState ) {
		        window.history.replaceState( null, null, window.location.href );
		    }
		</script>
		<?php
	}

	?> <div class="textbox"> <?php	
	//deploy house
	if(isset($_POST['houseform'])){
		echo nl2br ("<div class=\"t1\">Built your house in a region. In this region you can now spent the night without having to rent a room in a tavern. You can only built a house once in your lifetime.</div>");
		?>
		<form onsubmit="return confirm('Do you really want to built here?');" method="post" action="">
	    	<button type="submit" name="built" autofocus/>built house in current region</button>
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
	
	if(isset($_POST['built'])){
		$result = $mysqli->query("SELECT housebuilt, location2 FROM users WHERE username='$username'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$housebuilt = $row['housebuilt'];
		$location2 = $row['location2'];
		$location2=$mysqli->escape_string($location2);
		
		if($housebuilt == 0){
			$result = $mysqli->query("SELECT house FROM inventory WHERE userinv='$username'") or die($mysqli->error());
			$row = mysqli_fetch_array($result);
			$house = $row['house'];
			$house=$house-1;
			if($house >= 0){
				$sql = "UPDATE inventory SET house='$house' WHERE userinv='$username'";
				mysqli_query($mysqli, $sql);
				
				$sql = "UPDATE users SET housebuilt='1', housepos='$location2' WHERE username='$username'";
				mysqli_query($mysqli, $sql);
				
				echo'<div class="boxed">done!</div>';
			}else{
				echo'<div class="boxed">You do\'t have enough houses in your inventory!</div>';
			}
		}else{
			echo'<div class="boxed">You already built a house this live!</div>';
		}
		
		?>
		<script>
		    if ( window.history.replaceState ) {
		        window.history.replaceState( null, null, window.location.href );
		    }
		</script>
		<?php
	}
	
	//location
	if(isset($_POST['locationform'])){
		echo nl2br ("<div class=\"t1\">Per day you can move one region. Notice: if you are in a different region than that of where your house is built you should sleep in a tavern.</div>");
		?>
		<br><br>
		<form method="post" action=""> 
			<label for="chlocation">You can change your location 1 time a day</label>
			<?php
			$result = $mysqli->query("SELECT location2 FROM users WHERE username='$username'") or die($mysqli->error());
			$row = mysqli_fetch_array($result);
			$location2 = $row['location2'];
			
			$borderregions=array();
			foreach ($borders as $key => $value) {
				$name[$key] = $value['name'];
				$border1[$key]=$value['border1'];
				$border2[$key]=$value['border2'];
				$border3[$key]=$value['border3'];
				$border4[$key]=$value['border4'];
				$border5[$key]=$value['border5'];
	
				//select all borders of region
				if($name[$key]==$location2){
					if($border1[$key] != "NULL"){array_push($borderregions,$border1[$key]);}
					if($border2[$key] != "NULL"){array_push($borderregions,$border2[$key]);}
					if($border3[$key] != "NULL"){array_push($borderregions,$border3[$key]);}
					if($border4[$key] != "NULL"){array_push($borderregions,$border4[$key]);}
					if($border5[$key] != "NULL"){array_push($borderregions,$border5[$key]);}
				}			
			}
			
			// Sort the data with wek ascending order, add $mar as the last parameter, to sort by the common key
			asort($borderregions);
			?>
			<select required name="moveto" type="text" autofocus>
				<?php	        
				// Iterating through the product array
				foreach($borderregions as $item){
					?>
				 	<option value="<?php echo strtolower($item); ?>"><?php echo $item; ?></option>
				    <?php
			    }
			    ?>
			</select> 
			<button type="submit" name="chlocation" />Change location</button>
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
	
	if(isset($_POST['chlocation'])){
		$moveto = $mysqli->escape_string($_POST['moveto']);
		
		//check validity of region
		$result = $mysqli->query("SELECT location2 FROM users WHERE username='$username'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$location2 = $row['location2'];
		
		$borderregions=array();
		foreach ($borders as $key => $value) {
			$name[$key] = $value['name'];
			$border1[$key]=$value['border1'];
			$border2[$key]=$value['border2'];
			$border3[$key]=$value['border3'];
			$border4[$key]=$value['border4'];
			$border5[$key]=$value['border5'];

			//select all borders of region
			if($name[$key]==$location2){
				if($border1[$key] != "NULL"){array_push($borderregions,$border1[$key]);}
				if($border2[$key] != "NULL"){array_push($borderregions,$border2[$key]);}
				if($border3[$key] != "NULL"){array_push($borderregions,$border3[$key]);}
				if($border4[$key] != "NULL"){array_push($borderregions,$border4[$key]);}
				if($border5[$key] != "NULL"){array_push($borderregions,$border5[$key]);}
			}			
		}

		asort($borderregions);
		
		foreach($borderregions as $item){
			$item2 = $mysqli->escape_string($item);
			if($item2==$moveto){
				$result = $mysqli->query("SELECT curowner FROM region WHERE name='$moveto'") or die($mysqli->error());
				$row = mysqli_fetch_array($result);
				$location1 = $row['curowner'];
				
				$result = $mysqli->query("SELECT locationup FROM users WHERE username='$username'") or die($mysqli->error());
				$row = mysqli_fetch_array($result);
				$locationup = $row['locationup'];
				
				$result4 = $mysqli->query("SELECT * FROM traits WHERE name='imprisoned'") or die($mysqli->error());
				$row4 = mysqli_fetch_array($result4);
				$imprisonedtraitid = $row4['id'];
				
				$result3 = "SELECT * FROM traitscharacters WHERE characterid='$usercharacterid' AND traitid='$imprisonedtraitid'";
				$rs_result2 = $mysqli->query($result3);
				$count2 = $rs_result2->num_rows;//aantal titles
				
				if($count2 == 0){
					$result4 = $mysqli->query("SELECT * FROM traits WHERE name='in hiding'") or die($mysqli->error());
					$row4 = mysqli_fetch_array($result4);
					$inhidingtraitid = $row4['id'];
					
					$result3 = "SELECT * FROM traitscharacters WHERE characterid='$usercharacterid' AND traitid='$inhidingtraitid'";
					$rs_result2 = $mysqli->query($result3);
					$count2 = $rs_result2->num_rows;//aantal titles
					
					if($count2 == 0){
						if($locationup==0){
							$sql = "UPDATE users SET location2='$moveto', location='$location1', locationup='1' WHERE username='$username'";
							mysqli_query($mysqli, $sql);
							
							$sql = "UPDATE characters SET location2='$moveto', location='$location1' WHERE id='$usercharacterid'";
							mysqli_query($mysqli, $sql);
							
							echo'<div class="boxed">Done!</div>';
						}else{
							echo'<div class="boxed">You already changed your location today!</div>';
						}
					}else{
						echo'<div class="boxed">You can not change your location while you are in hiding!</div>';
					}
				}else{
					echo'<div class="boxed">You can not change your location while you are imprisoned!</div>';
				}
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
	/*
	if(isset($_POST['chlocation2'])){
		$moveto = $mysqli->escape_string($_POST['moveto']);
		$movetoregion = $mysqli->escape_string($_POST['movetoregion']);
		
		$result = $mysqli->query("SELECT ticket FROM inventory WHERE userinv='$username'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$ticket = $row['ticket'];
		
		//$ticket=$ticket-1; 
		if($ticket>=0){
			$sql = "UPDATE inventory SET ticket='$ticket' WHERE userinv='$username'";
			mysqli_query($mysqli, $sql);
			
			$sql = "UPDATE users SET location='$moveto', location2='$movetoregion' WHERE username='$username'";
			mysqli_query($mysqli, $sql);
			
			echo "Done!";
		}else{
			echo "You don't have enough moving tickets!";
		}
		
		?>
		<script>
		    if ( window.history.replaceState ) {
		        window.history.replaceState( null, null, window.location.href );
		    }
		</script>
		<?php
	}
	*/
	?> </div> <?php
	?> <div class="textbox"> <?php
	//change nationality
	if(isset($_POST['nationalityform'])){
		echo nl2br ("<div class=\"t1\">You can change your nationality by applying to another country. This will cost you the immigration tax of that country in gold. The minister of immigration of the country has to accept your application, if not the immigration tax is paid back.</div>");
		?>
		<br><br>
		<form onsubmit="return confirm('Make sure you have enough gold in your inventory to pay the immigration tax.');" method="post" action=""> 
			<?php
			$result = mysqli_query($mysqli,"SELECT country FROM countryinfo");
			$columnValues = Array();
			
			while ( $row = mysqli_fetch_assoc($result) ) {
			  $columnValues[] = $row['country'];
			}
			// Sort the data with wek ascending order, add $mar as the last parameter, to sort by the common key
			asort($columnValues);
			?>
			<select required name="immigrateto" type="text" autofocus>
				<?php	        
				// Iterating through the product array
				foreach($columnValues as $item){
					?>
				 	<option value="<?php echo strtolower($item); ?>"><?php echo $item; ?></option>
				    <?php
			    }
			    ?>
			</select> 
			<textarea rows="4" cols="50" name="message" maxlength="2000">Enter text here...</textarea>
			<button type="submit" name="chnationality" />Change nationality</button>
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
	
	if(isset($_POST['chnationality'])){
		$immigrateto = $mysqli->escape_string($_POST['immigrateto']);
		$message = $mysqli->escape_string($_POST['message']);
		$message = htmlspecialchars($message);
		
		$sql = "SELECT COUNT(country) AS total FROM countryinfo WHERE country='$immigrateto'";
		$result = $mysqli->query($sql);
		$row = $result->fetch_assoc();
		$total = ceil($row["total"]);
		
		if($total != 0){
			$result = $mysqli->query("SELECT id FROM immigration WHERE immigrant='$username'") or die($mysqli->error());
			if($immigrateto != $nationality){
				if(strlen($message) <= 2050){
					if ( $result->num_rows == 0 ) {
						$sql = "INSERT INTO immigration (immigrant, tocountry, message) " 
					     . "VALUES ('$username','$immigrateto','$message')";
						mysqli_query($mysqli, $sql);
					}else{
						echo "You already applied for a country!";
					}
				}
			}else{
				echo "You already are an inhabitant of that country!";
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
	
	?> </div> <?php
	?> <div class="textbox"> <?php
	
	//lottery
	if(isset($_POST['lotteryform'])){
		echo nl2br ("<div class=\"t1\">Every 15th of the month the winner will be choosen randomly. Participation costs 1 gold.</div>");
		?>
		<br><br>
		<form method="post" action=""> 
			<button type="submit" name="lottery" autofocus/>Joining the lottery costs 1 gold!</button>
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
	
	if(isset($_POST['lottery'])){
		$result = $mysqli->query("SELECT * FROM users WHERE username='$username'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$lottery = $row['lottery'];
		
		$result = $mysqli->query("SELECT * FROM currency WHERE usercur='$username'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$gold = $row['gold'];
		
		$gold=$gold-1;
		
		if($lottery==0 && $gold>=0){
			$sql = "UPDATE users SET lottery='1' WHERE username='$username'";
			mysqli_query($mysqli, $sql);
			
			$sql = "UPDATE currency SET gold='$gold' WHERE usercur='$username'";
			mysqli_query($mysqli, $sql);
			echo "Done!";
		}else{
			echo "You already participate or don't have enough gold!";
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
	
	//duel form
	if(isset($_POST['duelform'])){
		echo nl2br ("<div class=\"t1\">Dueling will increase or decrease your dominance and can be done once a day. Dueling against players with a much lower dominance than you will increase your dominace very little and opposite dueling a strong player will increase your dominance more.</div>");
		?>
		<br><br>
		<form method="post" action=""> 
			<input type="question" size="25" required autocomplete="off" autofocus placeholder="Enter username here" value="" name='duelname'/>
			<button type="submit" name="duel" />Duel player</button>
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
	
	if(isset($_POST['duel'])){
		$duelname = $mysqli->escape_string($_POST['duelname']);
		
		$sql = "SELECT COUNT(username) AS total FROM users WHERE username='$duelname'";
		$result = $mysqli->query($sql);
		$row = $result->fetch_assoc();
		$total = ceil($row["total"]);
		
		if($sleepstate=="awake" || $sleepstate=="neither"){
			if($total != 0){
				$result = $mysqli->query("SELECT * FROM users WHERE username='$duelname'") or die($mysqli->error());
				$row = mysqli_fetch_array($result);
				$dudominance = $row['dominance'];
				
				$result = $mysqli->query("SELECT * FROM users WHERE username='$username'") or die($mysqli->error());
				$row = mysqli_fetch_array($result);
				$dominance = $row['dominance'];
				$dueled = $row['dueled'];
				$energy = $row['energy'];
				$nationality = $row['nationality'];
				
				$result = $mysqli->query("SELECT * FROM countryinfo WHERE country='$nationality'") or die($mysqli->error());
				$row = mysqli_fetch_array($result);
				$countrypresident = $row['countrypresident'];
				$government = $row['government'];
				
				$energy=$energy-0;
				
				if($energy>=0 && $username !== $duelname && $dudominance != NULL){
					if($dueled==0){
					
						$diff=$dominance-$dudominance;
						$diff2=$diff*-1; //Voor verlies tegenovergestelde prijs; groot veel meer dominance dan vijand -> meer verliezen of minder winnen
						
						//fight
						$c=(0.9)*$diff +64;
						$Awon = rand(0, 99) < $c;
						//$c = (($dominance +1)/($dudominance+$dominance));
						//$Awon = rand(0, 9999) < ($c * 1000);
						//UPDATE users SET dueled='0', dominance='0' WHERE username='admin'
						//echo "$c";
						//echo "$Awon";
						//add prize
						if($Awon==1){
							$prize=20/(1+exp(0.08*($diff-0))); //logistic growth
						
							$dominance=$dominance+$prize;
							$dudominance=$dudominance-$prize;	
						
							if($dominance<0){
								$dominance=0;
							}
							if($dudominance<0){
								$dudominance=0;
							}
						
							$sql = "UPDATE users SET dominance='$dominance', dueled='1' WHERE username='$username'";
							mysqli_query($mysqli, $sql);
						
							$sql = "UPDATE users SET dominance='$dudominance' WHERE username='$duelname'";
							mysqli_query($mysqli, $sql);
							echo "You won";
							
							$content= "$username dueled against you and won, your dominance decreased by $prize";
							$sql = "INSERT INTO events (date, content, extrainfo) " 
						     . "VALUES (NOW(),'$content','$duelname')";
							mysqli_query($mysqli2, $sql);
							/*
							if($countrypresident==$duelname && $government==2){
								$sql = "UPDATE countryinfo SET countrypresident='$username' WHERE country='$nationality'";
								mysqli_query($mysqli, $sql);
								echo nl2br(" \n");
								echo "You are now king!";
							}*/
						}else{
							$prize=20/(1+exp(0.08*($diff2-0))); //logistic growth
						
							$dominance=$dominance-$prize;
							$dudominance=$dudominance+$prize;	
							
							if($dominance<0){
								$dominance=0;
							}
							if($dudominance<0){
								$dudominance=0;
							}
						
							$sql = "UPDATE users SET dominance='$dominance', dueled='1' WHERE username='$username'";
							mysqli_query($mysqli, $sql);
						
							$sql = "UPDATE users SET dominance='$dudominance' WHERE username='$duelname'";
							mysqli_query($mysqli, $sql);
							echo "You lost";
							
							$content= "$username dueled against you and lost, your dominance increased by $prize";
							$sql = "INSERT INTO events (date, content, extrainfo) " 
						     . "VALUES (NOW(),'$content','$duelname')";
							mysqli_query($mysqli2, $sql);
						}
					}else{
						echo "You have already dueled today!";
					}
				}else{
					echo "You don't have enough energy or entered a wrong name!";
				}
			}else{
				echo "User doesnt exist";
			}
		}else{
			echo'<div class="boxed">You can\'t duel another player while asleep!</div>';
		}
		
		?>
		<script>
		    if ( window.history.replaceState ) {
		        window.history.replaceState( null, null, window.location.href );
		    }
		</script>
		<?php
	}

	//change description
	if(isset($_POST['descriptionform'])){
		$result = $mysqli->query("SELECT userinfo FROM shop WHERE username='$user'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$userinfo = $row['userinfo'];
		?>
		<br><br>
		<form method="post" action="">
			<?php if($userinfo == 1){ ?>
				<textarea rows="4" cols="50" id='mytextarea' name="content" maxlength="2000" autofocus placeholder="Enter content here..."><?php echo $about; ?></textarea>
				<?php }else{ ?>
				<textarea rows="4" cols="50" name="content" maxlength="200" onkeyup="this.value = this.value.replace(/[&*<>]/g, '')" autofocus placeholder="Enter content here..."><?php echo $about; ?></textarea>
			<?php } ?>
			<button type="submit" name="changedescription" />Change description</button>
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
	
	if(isset($_POST['changedescription'])){
		$content = $mysqli->escape_string($_POST['content']);
		
		$result = $mysqli->query("SELECT userinfo FROM shop WHERE username='$user'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$userinfo = $row['userinfo'];
		
		if($userinfo == 1){
			$maxlength = 2050;
		}else{
			$maxlength = 250;
		}
		$check=0;
		if($userinfo==0){
			if(ctype_alnum($content)){
				$check=1;
			}else{
				$check=0;
			}
		}else{
			$check=1;
		}
		
		if($check==1){
			if(strlen($content) <= $maxlength){
				$sql = "UPDATE users SET about='$content' WHERE username='$username'";
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
		}
	}
}
?> </div> <?php
?> <div class="textbox"> <?php
//serach player form
if(isset($_POST['searchform'])){
	echo nl2br ("<div class=\"t1\">To search the page of another player type in the name of that player below.</div>");
	?>
	<br><br>
	<form method="post" action=""> 
		<input type="question" autofocus size="25" required autocomplete="off" placeholder="Enter username here" value="" name='inputname'/>
		<button type="submit" name="searchplayer" />Search player</button>
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

//if search player
if(isset($_POST['searchplayer'])){
	$inputname = $mysqli->escape_string($_POST['inputname']);
	
	$sql = "SELECT COUNT(username) AS total FROM users WHERE username='$inputname'";
	$result = $mysqli->query($sql);
	$row = $result->fetch_assoc();
	$total = ceil($row["total"]);
	
	if($total != 0){
		$result = $mysqli->query("SELECT * FROM users WHERE username='$inputname'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$strength = $row['strength'];
		echo nl2br("$inputname 's strength: $strength \n");
	
		//$result = $mysqli->query("SELECT * FROM users WHERE username='$inputname'") or die($mysqli->error());
		//$row = mysqli_fetch_array($result);
		$nationality = $row['nationality'];
		echo nl2br("$inputname 's nationality: $nationality \n");
		
		//$result = $mysqli->query("SELECT * FROM users WHERE username='$inputname'") or die($mysqli->error());
		//$row = mysqli_fetch_array($result);
		$location = $row['location'];
		echo nl2br("$inputname 's location: $location \n");
		
		$dominance = $row['dominance'];
		echo nl2br("$inputname 's dominance: $dominance \n");
		
		$age = $row['age'];
		echo nl2br("$inputname 's age: $age \n");
		
		?>
		<script>
			var val = "<?php echo $inputname ?>"
		    window.location = 'account.php?user='+val;
		</script>
		<?php
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
