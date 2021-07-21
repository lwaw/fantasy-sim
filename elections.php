<?php 
require 'navigationbar.php';
require 'db.php';
require_once 'purifier/library/HTMLPurifier.auto.php';
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
if (isset($_GET["page"])) { $page  = $_GET["page"]; } else { $page=1; };
$page=$mysqli->escape_string($page);
$results_per_page=20;
$start_from = ($page-1) * $results_per_page;

//set cp election day
$cp = 1;

//get nationality info
$result = $mysqli->query("SELECT * FROM users WHERE username='$username'") or die($mysqli->error());
$row = mysqli_fetch_array($result);
$nationality = $row['nationality'];
$userreligion = $row['userreligion'];
$accountcreated = $row['accountcreated'];

$result = $mysqli->query("SELECT * FROM countryinfo WHERE country='$nationality'") or die($mysqli->error());
$row = mysqli_fetch_array($result);
$government = $row['government'];

//check if new account only vote after 3 days
$date2 = new DateTime($accountcreated);
$date2->add(new DateInterval('P3D')); // P1D means a period of 1 day
$Datenew2 = $date2->format('Y-m-d H:i:s');

//get nationality info
$result = $mysqli->query("SELECT * FROM religion WHERE name='$userreligion'") or die($mysqli->error());
$row = mysqli_fetch_array($result);
$leader = $row['leader'];
$deathdate = $row['deathdate'];

$date = new DateTime($deathdate);
$date->add(new DateInterval('P1D')); // P1D means a period of 1 day
$Datenew1 = $date->format('Y-m-d H:i:s');

date_default_timezone_set('UTC'); //current date
$datecur = date("Y-m-d H:i:s"); 

//get day of the month
date_default_timezone_set('UTC');
$day = date("d");
//echo "$day";

if($Datenew2>$datecur){
	echo'<div class="boxed">You can only vote when your account is at least three days old!</div>';
}

?> <div class="h1"> <?php echo "Elections"; ?> </div> <?php
?> <hr /> <?php

?>
<table id="table1">
	<tr>
		<th><?php echo "Election:"; ?></th>
		<th><?php echo "Country president"; ?></th>
		<th><?php echo "Party president"; ?></th>
		<th><?php echo "Congress"; ?></th>
	</tr>
	<tr>
		<td><?php echo "Day:"; ?></td>
		<td>
			<?php echo "1"; ?>
			<?php if($government==2){ echo'<div class="boxed">This country currently is a monarchy which do not have country president elections!</div>'; } ?>
		</td>
		<td><?php echo "8"; ?></td>
		<td><?php echo "8"; ?></td>
	</tr>
</table>
<?php

//if not voting day: run for president
if ($day != $cp) {
    ?> 
     <?php if($government != 2){ ?>
	     <form method="post" action=""> 
	     <button type="submit" name="runfcpform" />Run for country presidency</button>
	     </form>
     <?php } ?>
     
	 <?php if(isset($_POST['runfcpform'])){ ?>
	 	<?php echo nl2br("Running for country president costs 1 gold \n"); ?>
	     <form method="post" action=""> 
	     <textarea rows="4" cols="50" name="message" maxlength="500">Enter program here...</textarea> 
	     <button type="submit" name="runcp" />Run for country presidency</button>
	     </form>
	     
		<script>
		    if ( window.history.replaceState ) {
		        window.history.replaceState( null, null, window.location.href );
		    }
		</script>
	 <?php } ?>
    
    <?php
	
}
if($day==$cp){
	echo nl2br ("<div class=\"h1\">Country president elections</div>");
	?> <hr /> <?php
	
	$sql = "SELECT * FROM elections WHERE countryel='$nationality' AND type='country' ORDER BY candidate ASC LIMIT $start_from, ".$results_per_page;
	$rs_result = $mysqli->query($sql);
	
	?> 
	<table id="table1">
		<tr>
	    <th> Candidate</th>
	    <th> Message</th>
        <th> Political party</th>	
	    <th> Vote</th>
	</tr>
	<?php
	while($row = $rs_result->fetch_assoc()) {
		$message = $row["message"];
		$message = $purifier->purify($message);
		
		//get party info
		$user=$row["candidate"];
		$result2 = $mysqli->query("SELECT politicalparty FROM users WHERE username='$user'") or die($mysqli->error());
		$row2 = mysqli_fetch_array($result2);
		$politicalparty = $row2['politicalparty'];
		if($politicalparty != 0){
			$result3 = $mysqli->query("SELECT name FROM politicalparty WHERE id='$politicalparty'") or die($mysqli->error());
			$row3 = mysqli_fetch_array($result3);
			$politicalpartyname = $row3['name'];
		}
	
		?> 
       <tr>
           <td><?php echo $row["candidate"]; ?></td>
           <td><?php echo "$message"; ?></td>
           <?php if($politicalparty != 0){ ?>
           		<td><?php echo $politicalpartyname; ?></td>	
           <?php }else{ ?>
           		<td><?php echo "None";; ?></td>	
           <?php } ?>
           <td>
			     <form method="post" action="">  
			     	<input type="hidden" name="id" value="<?php echo $row["id"]; ?>" />
			     	<input type="hidden" name="type" value="<?php echo "country"; ?>" />
					<input type="hidden" name="candidatevote" value="<?php echo $row["candidate"]; ?>" />
			     	<?php if($Datenew2<$datecur){ ?><button type="submit" name="vote" />Vote for candidate</button> <?php } ?>
			     </form>
           </td>
       </tr>
		<?php		
	}; 
	?>
	</table>
	<?php
	$sql = "SELECT COUNT(candidate) AS total FROM elections WHERE countryel='$nationality'";
	$result = $mysqli->query($sql);
	$row = $result->fetch_assoc();
	$total_pages = ceil($row["total"] / $results_per_page); // calculate total pages with results
	
	for ($i=1; $i<=$total_pages; $i++) {  // print links for all pages
		echo "<a href='elections.php?show=1&page=".$i."'";
	    if ($i==$page)  echo " class='curPage'";
	    echo ">".$i."</a> "; 
	};	
}elseif($day==15){ //congress elections
	echo nl2br ("<div class=\"h1\">Congress elections</div>");
	?> <hr /> <?php

	$sql = "SELECT * FROM politicalparty WHERE country='$nationality' AND runcongress!='0' ORDER BY id ASC LIMIT $start_from, ".$results_per_page;
	$rs_result = $mysqli->query($sql);

	?> 
	<table id="table1">
	<tr>
	    <th> Party</th>
	    <th> Vote</th>
	</tr>
	<?php
	while($row = $rs_result->fetch_assoc()) {
		$party=$row["name"];
		$id=$row["id"];

		?> 
       <tr>
           <td><?php echo $row["name"]; ?></td>
           <td>
			     <form method="post" action="">  
			     	<input type="hidden" name="id" value="<?php echo "$id"; ?>" />
			     	<?php if($Datenew2<$datecur){ ?><button type="submit" name="votecongress" />Vote for party</button> <?php } ?>
			     </form>
           </td>
       </tr>
		<?php		
	}; 
	?>
	</table>
	<?php
	$sql = "SELECT COUNT(id) AS total FROM politicalparty WHERE country='$nationality' AND runcongress!='0'";
	$result = $mysqli->query($sql);
	$row = $result->fetch_assoc();
	$total_pages = ceil($row["total"] / $results_per_page); // calculate total pages with results
	
	for ($i=1; $i<=$total_pages; $i++) {  // print links for all pages
		echo "<a href='elections.php?show=1&page=".$i."'";
	    if ($i==$page)  echo " class='curPage'";
	    echo ">".$i."</a> "; 
	};	
}elseif($day == 8){ //party president elections
	echo nl2br ("<div class=\"h1\">Party president elections</div>");
	?> <hr /> <?php

	$result = $mysqli->query("SELECT politicalparty FROM users WHERE username='$username'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$politicalparty = $row['politicalparty'];
	$politicalparty = (int) $politicalparty;
	
	if($politicalparty != 0){
		$sql = "SELECT * FROM elections WHERE type='partypresident' AND party='$politicalparty' ORDER BY candidate ASC LIMIT $start_from, ".$results_per_page;
		$rs_result = $mysqli->query($sql);
		
		?> 
		<table id="table1">
			<tr>
		    <th> Candidate</th>
		    <th> Vote</th>
		</tr>
		<?php
		while($row = $rs_result->fetch_assoc()) {
			?> 
	       <tr>
	           <td><?php echo $row["candidate"]; ?></td>
	           <td>
				     <form method="post" action="">  
				     	<input type="hidden" name="id" value="<?php echo $row["id"]; ?>" />
				     	<input type="hidden" name="type" value="<?php echo "partypres"; ?>" />
						<input type="hidden" name="candidatevote" value="<?php echo $row["candidate"]; ?>" />
				     	<?php if($Datenew2<$datecur){ ?><button type="submit" name="vote" />Vote for candidate</button> <?php } ?>
				     </form>
	           </td>
	       </tr>
			<?php		
		}; 
		?>
		</table>
		<?php
		$sql = "SELECT COUNT(candidate) AS total FROM elections WHERE type='partypresident' AND party='$politicalparty'";
		$result = $mysqli->query($sql);
		$row = $result->fetch_assoc();
		$total_pages = ceil($row["total"] / $results_per_page); // calculate total pages with results
		
		for ($i=1; $i<=$total_pages; $i++) {  // print links for all pages
			echo "<a href='elections.php?show=1&page=".$i."'";
		    if ($i==$page)  echo " class='curPage'";
		    echo ">".$i."</a> "; 
		};	
	}
}

// We know user email exists if the rows returned are more than 0
$alreadycandidated = $mysqli->query("SELECT * FROM elections WHERE candidate='$username' AND type='country'") or die($mysqli->error());

//run for country presidency
if(isset($_POST['runcp'])){
	$message = $mysqli->escape_string($_POST['message']);
		
	//get gold info
	$result = $mysqli->query("SELECT gold FROM currency WHERE usercur='$username'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$gold = $row['gold'];
	
	$result = $mysqli->query("SELECT * FROM users WHERE username='$username'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$nationality = $row['nationality'];
	
	$result = $mysqli->query("SELECT * FROM countryinfo WHERE country='$nationality'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$government = $row['government'];

    if ($alreadycandidated->num_rows == 0 ){
    	if($government != 2){
         //echo "gelukt";
		 //set new gold
		 	$gold = $gold-1;
		 		if($gold<0){
		 			echo "Not enough gold!";
				}else{
					$sql = "UPDATE currency SET gold='$gold' WHERE usercur='$username'";
					mysqli_query($mysqli, $sql);
					
					//set candidate & country
    				$sql = "INSERT INTO elections (type, candidate, countryel, message) " 
            		. "VALUES ('country','$username','$nationality','$message')";
			 		mysqli_query($mysqli, $sql);
					
				}
		}else{
			echo nl2br ("<div class=\"boxed\">The current government type of this country is kingdom which does not allow for country president elections!</div>");
		}
	}else{
		echo nl2br ("<div class=\"boxed\">You have already signed up for the upcoming country president elections!</div>");
	}
	?>
	<script>
	    if ( window.history.replaceState ) {
	        window.history.replaceState( null, null, window.location.href );
	    }
	</script>
	<?php
}

//vote for country presidency
if(isset($_POST['vote'])){
	$type = $mysqli->escape_string($_POST['type']); //country partypres 
	$id = $mysqli->escape_string($_POST['id']);
	$id = (int) $id;
	
	date_default_timezone_set('UTC');
	$day = date("d");
	
	//get voted info
	$result = $mysqli->query("SELECT * FROM users WHERE username='$username'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$voted = $row['voted'];
	$userreligion = $row['userreligion'];
	$nationality = $row['nationality'];
	$politicalparty = $row['politicalparty'];

	//check if already voted
	if($voted==0){
		//get votes info
		if($day == 1 AND $type=="country"){
			$select=1;
		}elseif($day == 8 AND $type=="partypres"){
			$select=2;
		}else{
			?>
			<script>
				var val = "<?php echo $religionorder ?>"
			    window.location = 'elections.php';
			</script>
			<?php
		}
		
		if($select==1){ //countrypresident en religeous leader
			$result = $mysqli->query("SELECT * FROM elections WHERE id='$id' AND countryel='$nationality'") or die($mysqli->error());
			$row = mysqli_fetch_array($result);
			$id = $row['id'];	
			$votes = $row['votes'];	
			$candidate = $row['candidate'];	
		}elseif($select==2){ //partypresident
			$result = $mysqli->query("SELECT * FROM elections WHERE id='$id' AND party='$politicalparty'") or die($mysqli->error());
			$row = mysqli_fetch_array($result);
			$id = $row['id'];	
			$votes = $row['votes'];	
			$candidate = $row['candidate'];	
		}

		//votes+1
		$votes = $votes + 1;
	
		$sql = "UPDATE elections SET votes='$votes' WHERE id='$id'";
		mysqli_query($mysqli, $sql);
		
		//set voted: yes
		$sql = "UPDATE users SET voted='1' WHERE username='$username'";
		mysqli_query($mysqli, $sql);
		
		echo'<div class="boxed">Voted!</div>';
		
		?>
		<script>
		    if ( window.history.replaceState ) {
		        window.history.replaceState( null, null, window.location.href );
		    }
		</script>
		<?php
	
	}else{
		echo'<div class="boxed">You have already voted!</div>';
		
		?>
		<script>
		    if ( window.history.replaceState ) {
		        window.history.replaceState( null, null, window.location.href );
		    }
		</script>
		<?php
	}
}

if(isset($_POST['votecongress'])){
	$id = $mysqli->escape_string($_POST['id']);
	
	if($day != 15){
		?>
		<script>
			var val = "<?php echo $religionorder ?>"
		    window.location = 'elections.php';
		</script>
		<?php
	}
	
	//get voted info
	$result = $mysqli->query("SELECT * FROM users WHERE username='$username'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$voted = $row['voted'];
	$nationality = $row['nationality'];
	
	$result = $mysqli->query("SELECT * FROM countryinfo WHERE country='$nationality'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$totalcongressel = $row['totalcongressel'];
	
	//check if already voted
	if($voted==0){
		//get votes info
		$result = $mysqli->query("SELECT * FROM politicalparty WHERE id='$id'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$congressvotes = $row['congressvotes'];	
		$country = $row['country'];	
		
		if($nationality == $country){
			//votes+1
			$congressvotes = $congressvotes + 1;
			$totalcongressel = $totalcongressel + 1;
			
			$sql = "UPDATE countryinfo SET totalcongressel='$totalcongressel' WHERE country='$nationality'";
			mysqli_query($mysqli, $sql);
		
			$sql = "UPDATE politicalparty SET congressvotes='$congressvotes' WHERE id='$id'";
			mysqli_query($mysqli, $sql);
			
			//set voted: yes
			$sql = "UPDATE users SET voted='1' WHERE username='$username'";
			mysqli_query($mysqli, $sql);
			
			echo'<div class="boxed">Voted!</div>';
			
			?>
			<script>
			    if ( window.history.replaceState ) {
			        window.history.replaceState( null, null, window.location.href );
			    }
			</script>
			<?php
		}	
	}else{
		echo'<div class="boxed">You have already voted!</div>';
		
		?>
		<script>
		    if ( window.history.replaceState ) {
		        window.history.replaceState( null, null, window.location.href );
		    }
		</script>
		<?php
	}
}

?>
</div>
</body>
<footer>
<?php require 'bottombar.php'; ?>
</footer>
</html>
