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
	$usercharacterid = $_SESSION['usercharacter'];
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

if (isset($_GET["page"])) { $page  = $_GET["page"]; } else { $page=1; };
$page=$mysqli->escape_string($page);
$results_per_page=20;
$start_from = ($page-1) * $results_per_page;

if (isset($_GET["accept"])) { $accept  = $_GET["accept"]; } else { $accept=0; };
$accept=$mysqli->escape_string($accept);

if($characterid != 0){	
	$result = $mysqli->query("SELECT * FROM users WHERE username='$username'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	
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
	$userid = $row['id'];
	
	$result2 = $mysqli->query("SELECT * FROM characters WHERE id='$characterid' AND alive = '1' LIMIT 1") or die($mysqli->error());
	$row2 = mysqli_fetch_array($result2);
	$charactername = $row2['name'];
	$characterid = $row2['id'];
	$characterage = $row2['age'];
	$characterrace = $row2['race'];
	$charactersex = $row2['sex'];
	$characterfamily = $row2['familyid'];
	$characteruser = $row2['user'];
	$characteralive = $row2['alive'];
	$charactermarried = $row2['married'];
	$characterliege = $row2['liege'];
	
	//select family
	$result2 = $mysqli->query("SELECT * FROM family WHERE id='$characterfamily'") or die($mysqli->error());
	$row2 = mysqli_fetch_array($result2);
	$familyname = $row2['name'];
	
	?> <div class="everythingOnOneLine2"> <?php
		?> <div class="flexcontainer"> <?php
			?> <div class="h1"> <?php echo "Marry to $charactername $familyname"; if($characteralive==0){echo " [+]";} ?> </div> <?php
				?>
				<div class="notificationbox2">
					<div class="notificationbox3">
						<a href="rankings.php?type=users&country=<?php echo "$nationality"; ?>&sort=username&order=asc">
						<img src="img/membersicon.png">
						</a>
					</div>
				</div>
				<?php
			 
		?> </div> <?php
	?> </div> <?php
	?> <hr /> <?php
	
	if($charactermarried == 0){
		if($characterid != $usercharacterid){
			$result3 = "SELECT * FROM characters WHERE (liege = '$usercharacterid' OR (id = '$usercharacterid' AND age >= '18')) AND married = '0' AND alive = '1'";
			$rs_result = $mysqli->query($result3);
			$count = $rs_result->num_rows;
			
			$result = $mysqli->query("SELECT * FROM characters WHERE (liege = '$usercharacterid' OR (id = '$usercharacterid' AND age >= '18')) AND married = '0' AND alive = '1'") or die($mysqli->error());
			$columnValues = Array();
			
			echo nl2br ("<div class=\"t1\">It is possible to propose to a character when you are 18 years old. Your liege can always propose yo to another character. After proposing to someone lieges of both character have to approve the marriage within 7 days. After 7 days the marriage will automatically continue. 
			Future offspring of matrilineal marriages will continue the mothers blood line and will thus bear the mothers last name. Character from different races are able to marry but are not capable to produce offspring.</div>");
			if($characteralive == 1){
				?>
				<form method="post" action="">
					<input type="hidden" name="matrilineal" value="false" />
				    <select required name="candidate" type="text">
				    <option value="" disabled selected hidden>Who do you want to marry?</option> 
				    <?php       
				    // Iterating through the product array
					while ( $row = mysqli_fetch_assoc($result) ) {
						$candidatefamily = $row['familyid'];
						$result2 = $mysqli->query("SELECT * FROM family WHERE id='$candidatefamily'") or die($mysqli->error());
						$row2 = mysqli_fetch_array($result2);
						$candidatefamilyname = $row2['name'];
					    ?>
					    <option value="<?php echo strtolower($row['id']); ?>"><?php echo $row['name']; ?></option>
					    <?php
					}
				    ?>
				    </select> 
				    <input type="checkbox" name="matrilineal" value="true"> Matrilineal<br>
				    <button type="submit" name="propose" />Propose</button>
				</form>
				<?php
			}else{
				echo'<div class="boxed">This character is deceased!</div>';
			}
		}else{
			echo'<div class="boxed">It is not possible to marry to yourself!</div>';
		}
	}else{
		echo'<div class="boxed">This character is already married!</div>';
	}
}else{
	echo'<div class="boxed">Please select a character to arrange a marriage from the account page!</div>';
}

if(isset($_POST['propose'])){
	$candidateid = $mysqli->escape_string($_POST['candidate']);
	$matrilineal = $mysqli->escape_string($_POST['matrilineal']);
	
	$result2 = $mysqli->query("SELECT * FROM characters WHERE id='$candidateid' AND alive='1' AND married='0' LIMIT 1") or die($mysqli->error());
	$row2 = mysqli_fetch_array($result2);
	$candidatecharactername = $row2['name'];
	$candidatecharacterrace = $row2['race'];
	$candidatecharacterid = $row2['id'];
	$candidatecharacterliege = $row2['liege'];
	$candidatecharacteralive = $row2['alive'];
	$candidatecharactermarried = $row2['married'];
	
	if($matrilineal == "true"){
		$matrilineal = 1;
		$matrilineal2 = "matrilineal";
	}else{
		$matrilineal = 0;
		$matrilineal2 = " ";
	}
	
	$result2 = $mysqli->query("SELECT * FROM characters WHERE id='$candidatecharacterid' AND alive='1'") or die($mysqli->error());
	$row2 = mysqli_fetch_array($result2);
	$candidatecharacteruser = $row2['user'];
	
	$result2 = $mysqli->query("SELECT * FROM characters WHERE id='$characterid' AND alive='1'") or die($mysqli->error());
	$row2 = mysqli_fetch_array($result2);
	$characteruser = $row2['user'];
	$characteralive = $row2['alive'];
	$charactermarried = $row2['married'];
	
	$result2 = $mysqli->query("SELECT * FROM characters WHERE id='$candidatecharacterliege' AND alive='1'") or die($mysqli->error());
	$row2 = mysqli_fetch_array($result2);
	$candidatecharacterliegeuser = $row2['user'];
	
	$result2 = $mysqli->query("SELECT * FROM characters WHERE id='$characterliege' AND alive='1'") or die($mysqli->error());
	$row2 = mysqli_fetch_array($result2);
	$characterliegeuser = $row2['user'];
	
	if($candidatecharacterid == $candidateid AND $characterid != $candidatecharacterid AND $candidatecharacteralive == 1 AND $characteralive == 1 AND $candidatecharactermarried == 0 AND $charactermarried == 0){
			$sql = "INSERT INTO marriageproposal (candidate1, candidate1liege, candidate2, candidate2liege, matrilineal, date) " 
			. "VALUES ('$candidatecharacterid','$candidatecharacterliege','$characterid','$characterliege','$matrilineal',NOW())";
	 		mysqli_query($mysqli, $sql);
					
			$content= "$candidatecharactername proposed to $charactername $matrilineal2. Click <a href='marriage.php?accept=1'>here</a> to accept this proposal. After 5 days the proposal will be accepted automatically.";
			$content=$mysqli->escape_string($content);
			$sql = "INSERT INTO events (date, content, extrainfo) " 
		     . "VALUES (NOW(),'$content','$candidatecharacterliegeuser')";
			mysqli_query($mysqli2, $sql);
			
			$content= "$candidatecharactername proposed to $charactername $matrilineal2. Click <a href='marriage.php?accept=1'>here</a> to accept this proposal. After 5 days the proposal will be accepted automatically.";
			$content=$mysqli->escape_string($content);
			$sql = "INSERT INTO events (date, content, extrainfo) " 
		     . "VALUES (NOW(),'$content','$characterliegeuser')";
			mysqli_query($mysqli2, $sql);
			
			$content= "$candidatecharactername proposed to $charactername $matrilineal2. Your liege will have to accept this proposal. After 5 days the proposal will be accepted automatically.";
			$content=$mysqli->escape_string($content);
			$sql = "INSERT INTO events (date, content, extrainfo) " 
		     . "VALUES (NOW(),'$content','$characteruser')";
			mysqli_query($mysqli2, $sql);
			
			$content= "$candidatecharactername proposed to $charactername $matrilineal2. Your liege will have to accept this proposal. After 5 days the proposal will be accepted automatically.";
			$content=$mysqli->escape_string($content);
			$sql = "INSERT INTO events (date, content, extrainfo) " 
		     . "VALUES (NOW(),'$content','$candidatecharacteruser')";
			mysqli_query($mysqli2, $sql);
			
			echo'<div class="boxed">Done!</div>';
	}else{
		echo'<div class="boxed">It is not possible to marry a character to itself!</div>';
	}
	?>
	<script>
	    if ( window.history.replaceState ) {
	        window.history.replaceState( null, null, window.location.href );
	    }
	</script>
	<?php
}

if($accept == 1){
	$sql = "SELECT * FROM marriageproposal WHERE (candidate1liege='$usercharacterid' AND candidate1accept='0') OR (candidate2liege='$usercharacterid' AND candidate2accept='0') ORDER BY id ASC LIMIT $start_from, ".$results_per_page;
	$rs_result = $mysqli->query($sql);	
	
	?> 
	<table id="table1">
		<tr>
	    <th> Character</th>
	    <th> Character</th>
	    <th> Matrilineal</th>
	    <th> Accept</th>
		</tr>
		<?php
		while($row = $rs_result->fetch_assoc()) {
			//count number of members
			$proposalid=$row["id"];
			$candidate1id=$row["candidate1"];
			$candidate1accept=$row["candidate1accept"];
			$candidate2id=$row["candidate2"];
			$candidate2accept=$row["candidate2accept"];
			$matrilineal=$row["matrilineal"];
			
			$result2 = $mysqli->query("SELECT * FROM characters WHERE id='$candidate1id' AND alive = '1' LIMIT 1") or die($mysqli->error());
			$row2 = mysqli_fetch_array($result2);
			$candidate1name = $row2['name'];
			$candidate1family = $row2['familyid'];
			$candidate1user = $row2['user'];
			$candidate1type = $row2['type'];
			
			$result2 = $mysqli->query("SELECT * FROM family WHERE id='$candidate1family'") or die($mysqli->error());
			$row2 = mysqli_fetch_array($result2);
			$candidate1familyname = $row2['name'];
			
			$link1="<a href='account.php?user=$candidate1user&charid=$candidate1id'>$candidate1name $candidate1familyname</a>";
			
			$result2 = $mysqli->query("SELECT * FROM characters WHERE id='$candidate2id' AND alive = '1' LIMIT 1") or die($mysqli->error());
			$row2 = mysqli_fetch_array($result2);
			$candidate2name = $row2['name'];
			$candidate2family = $row2['familyid'];
			$candidate2user = $row2['user'];
			$candidate2type = $row2['type'];
			
			$result2 = $mysqli->query("SELECT * FROM family WHERE id='$candidate2family'") or die($mysqli->error());
			$row2 = mysqli_fetch_array($result2);
			$candidate2familyname = $row2['name'];
			
			$link2="<a href='account.php?user=$candidate2user&charid=$candidate2id'>$candidate2name $candidate2familyname</a>";
			?> 
		           <tr>
			           <td><?php echo "$link1"; ?></td>
			           <td><?php echo "$link2"; ?></td>
			           <td><?php echo $count; ?></td>
			           <td>
							<form method="post" action="">
								<input type="hidden" name="proposalid" value="<?php echo "$proposalid"; ?>" />
								<button type="submit" name="process" value="accept" /><?php echo "Accept"; ?></button>
								<button type="submit" name="process" value="decline" /><?php echo "Decline"; ?></button>
							</form>
			           </td>
		           </tr>
			<?php		
		}; 
	?>
	</table>
	<?php
	$sql = "SELECT COUNT(id) AS total FROM marriageproposal WHERE candidate1liege='$usercharacterid' OR candidate2liege='$usercharacterid'";
	$result = $mysqli->query($sql);
	$row = $result->fetch_assoc();
	$total_pages = ceil($row["total"] / $results_per_page); // calculate total pages with results
	
	for ($i=1; $i<=$total_pages; $i++) {  // print links for all pages
    	echo "<a href='marriage.php?accept=1&page=".$i."'";
        if ($i==$page)  echo " class='curPage'";
        echo ">".$i."</a> "; 
	};
}

if(isset($_POST['process'])){
	$proposalid = $mysqli->escape_string($_POST['proposalid']);
	$value = $mysqli->escape_string($_POST['process']);
	
	$result2 = $mysqli->query("SELECT * FROM marriageproposal WHERE id='$proposalid' AND ((candidate1liege='$usercharacterid' AND candidate1accept='0') OR (candidate2liege='$usercharacterid' AND candidate2accept='0'))") or die($mysqli->error());
	$row2 = mysqli_fetch_array($result2);
	$proposalid=$row2["id"];
	$candidate1id=$row2["candidate1"];
	$candidate1liege=$row2["candidate1liege"];
	$candidate1accept=$row2["candidate1accept"];
	$candidate2id=$row2["candidate2"];
	$candidate2liege=$row2["candidate2liege"];
	$candidate2accept=$row2["candidate2accept"];
	$matrilineal=$row2["matrilineal"];
	
	$result2 = $mysqli->query("SELECT * FROM characters WHERE id='$candidate1id' AND alive = '1' LIMIT 1") or die($mysqli->error());
	$row2 = mysqli_fetch_array($result2);
	$candidate1user = $row2['user'];
	$candidate1name = $row2['name'];
	$candidate1sex = $row2['sex'];
	$candidate1race = $row2['race'];
	
	$result2 = $mysqli->query("SELECT * FROM characters WHERE id='$candidate2id' AND alive = '1' LIMIT 1") or die($mysqli->error());
	$row2 = mysqli_fetch_array($result2);
	$candidate2user = $row2['user'];
	$candidate2name = $row2['name'];
	$candidate2sex = $row2['sex'];
	$candidate2race = $row2['race'];
	
	$result2 = $mysqli->query("SELECT * FROM characters WHERE id='$candidate1liege' AND alive = '1' LIMIT 1") or die($mysqli->error());
	$row2 = mysqli_fetch_array($result2);
	$candidate1liegeuser = $row2['user'];
	$candidate1liegename = $row2['name'];
	
	$result2 = $mysqli->query("SELECT * FROM characters WHERE id='$candidate2liege' AND alive = '1' LIMIT 1") or die($mysqli->error());
	$row2 = mysqli_fetch_array($result2);
	$candidate2liegeuser = $row2['user'];
	$candidate2liegename = $row2['name'];
	
	if($value=="accept"){	
		if($usercharacterid == $candidate1liege){
			$sql = "UPDATE marriageproposal SET candidate1accept='1' WHERE id='$proposalid'";
			mysqli_query($mysqli, $sql);
			
			$content= "<a href='account.php?user=$candidate1liegeuser&charid=$candidate1liege'>$candidate1liegename</a> accepted the proposal between <a href='account.php?user=$candidate1user&charid=$candidate1id'>$candidate1name</a> and <a href='account.php?user=$candidate2user&charid=$candidate2id'>$candidate2name</a>";
			$content=$mysqli->escape_string($content);
			$sql = "INSERT INTO events (date, content, extrainfo) " 
		     . "VALUES (NOW(),'$content','$candidate2liegeuser')";
			mysqli_query($mysqli2, $sql);
			
			$content= "<a href='account.php?user=$candidate1liegeuser&charid=$candidate1liege'>$candidate1liegename</a> accepted the proposal between <a href='account.php?user=$candidate1user&charid=$candidate1id'>$candidate1name</a> and <a href='account.php?user=$candidate2user&charid=$candidate2id'>$candidate2name</a>";
			$content=$mysqli->escape_string($content);
			$sql = "INSERT INTO events (date, content, extrainfo) " 
		     . "VALUES (NOW(),'$content','$candidate1user')";
			mysqli_query($mysqli2, $sql);
			
			$content= "<a href='account.php?user=$candidate1liegeuser&charid=$candidate1liege'>$candidate1liegename</a> accepted the proposal between <a href='account.php?user=$candidate1user&charid=$candidate1id'>$candidate1name</a> and <a href='account.php?user=$candidate2user&charid=$candidate2id'>$candidate2name</a>";
			$content=$mysqli->escape_string($content);
			$sql = "INSERT INTO events (date, content, extrainfo) " 
		     . "VALUES (NOW(),'$content','$candidate2user')";
			mysqli_query($mysqli2, $sql);
			
		}elseif($usercharacterid == $candidate2liege){
			$sql = "UPDATE marriageproposal SET candidate2accept='1' WHERE id='$proposalid'";
			mysqli_query($mysqli, $sql);
			
			$content= "<a href='account.php?user=$candidate2liegeuser&charid=$candidate2liege'>$candidate2liegename</a> accepted the proposal between <a href='account.php?user=$candidate1user&charid=$candidate1id'>$candidate1name</a> and <a href='account.php?user=$candidate2user&charid=$candidate2id'>$candidate2name</a>";
			$content=$mysqli->escape_string($content);
			$sql = "INSERT INTO events (date, content, extrainfo) " 
		     . "VALUES (NOW(),'$content','$candidate1liegeuser')";
			mysqli_query($mysqli2, $sql);
			
			$content= "<a href='account.php?user=$candidate1liegeuser&charid=$candidate1liege'>$candidate1liegename</a> accepted the proposal between <a href='account.php?user=$candidate1user&charid=$candidate1id'>$candidate1name</a> and <a href='account.php?user=$candidate2user&charid=$candidate2id'>$candidate2name</a>";
			$content=$mysqli->escape_string($content);
			$sql = "INSERT INTO events (date, content, extrainfo) " 
		     . "VALUES (NOW(),'$content','$candidate1user')";
			mysqli_query($mysqli2, $sql);
			
			$content= "<a href='account.php?user=$candidate1liegeuser&charid=$candidate1liege'>$candidate1liegename</a> accepted the proposal between <a href='account.php?user=$candidate1user&charid=$candidate1id'>$candidate1name</a> and <a href='account.php?user=$candidate2user&charid=$candidate2id'>$candidate2name</a>";
			$content=$mysqli->escape_string($content);
			$sql = "INSERT INTO events (date, content, extrainfo) " 
		     . "VALUES (NOW(),'$content','$candidate2user')";
			mysqli_query($mysqli2, $sql);
		}

		if($candidate1liege == $candidate2liege){//als liege dezelfde is voor beide characters dan gelijk accepteren
			$sql = "UPDATE marriageproposal SET candidate2accept='1', candidate1accept='1' WHERE id='$proposalid'";
			mysqli_query($mysqli, $sql);
		}

	}elseif($value=="decline"){
		if($usercharacterid == $candidate1liege){
			$sql = "UPDATE marriageproposal SET candidate1accept='2' WHERE id='$proposalid'";
			mysqli_query($mysqli, $sql);
			
			$content= "<a href='account.php?user=$candidate2liegeuser&charid=$candidate2liege'>$candidate2liegename</a> declined the proposal between <a href='account.php?user=$candidate1user&charid=$candidate1id'>$candidate1name</a> and <a href='account.php?user=$candidate2user&charid=$candidate2id'>$candidate2name</a>";
			$content=$mysqli->escape_string($content);
			$sql = "INSERT INTO events (date, content, extrainfo) " 
		     . "VALUES (NOW(),'$content','$candidate2liegeuser')";
			mysqli_query($mysqli2, $sql);
			
			$content= "<a href='account.php?user=$candidate1liegeuser&charid=$candidate1liege'>$candidate1liegename</a> declined the proposal between <a href='account.php?user=$candidate1user&charid=$candidate1id'>$candidate1name</a> and <a href='account.php?user=$candidate2user&charid=$candidate2id'>$candidate2name</a>";
			$content=$mysqli->escape_string($content);
			$sql = "INSERT INTO events (date, content, extrainfo) " 
		     . "VALUES (NOW(),'$content','$candidate1user')";
			mysqli_query($mysqli2, $sql);
			
			$content= "<a href='account.php?user=$candidate1liegeuser&charid=$candidate1liege'>$candidate1liegename</a> declined the proposal between <a href='account.php?user=$candidate1user&charid=$candidate1id'>$candidate1name</a> and <a href='account.php?user=$candidate2user&charid=$candidate2id'>$candidate2name</a>";
			$content=$mysqli->escape_string($content);
			$sql = "INSERT INTO events (date, content, extrainfo) " 
		     . "VALUES (NOW(),'$content','$candidate2user')";
			mysqli_query($mysqli2, $sql);
			
		}elseif($usercharacterid == $candidate2liege){
			$sql = "UPDATE marriageproposal SET candidate2accept='2' WHERE id='$proposalid'";
			mysqli_query($mysqli, $sql);
			
			$content= "<a href='account.php?user=$candidate2liegeuser&charid=$candidate2liege'>$candidate2liegename</a> declined the proposal between <a href='account.php?user=$candidate1user&charid=$candidate1id'>$candidate1name</a> and <a href='account.php?user=$candidate2user&charid=$candidate2id'>$candidate2name</a>";
			$content=$mysqli->escape_string($content);
			$sql = "INSERT INTO events (date, content, extrainfo) " 
		     . "VALUES (NOW(),'$content','$candidate1liegeuser')";
			mysqli_query($mysqli2, $sql);
			
			$content= "<a href='account.php?user=$candidate1liegeuser&charid=$candidate1liege'>$candidate1liegename</a> declined the proposal between <a href='account.php?user=$candidate1user&charid=$candidate1id'>$candidate1name</a> and <a href='account.php?user=$candidate2user&charid=$candidate2id'>$candidate2name</a>";
			$content=$mysqli->escape_string($content);
			$sql = "INSERT INTO events (date, content, extrainfo) " 
		     . "VALUES (NOW(),'$content','$candidate1user')";
			mysqli_query($mysqli2, $sql);
			
			$content= "<a href='account.php?user=$candidate1liegeuser&charid=$candidate1liege'>$candidate1liegename</a> declined the proposal between <a href='account.php?user=$candidate1user&charid=$candidate1id'>$candidate1name</a> and <a href='account.php?user=$candidate2user&charid=$candidate2id'>$candidate2name</a>";
			$content=$mysqli->escape_string($content);
			$sql = "INSERT INTO events (date, content, extrainfo) " 
		     . "VALUES (NOW(),'$content','$candidate2user')";
			mysqli_query($mysqli2, $sql);
		}
	}
	
	//check if accepted by both parties
	$result2 = $mysqli->query("SELECT * FROM marriageproposal WHERE id='$proposalid' AND (candidate1liege='$usercharacterid' OR candidate2liege='$usercharacterid')") or die($mysqli->error());
	$row2 = mysqli_fetch_array($result2);
	$candidate1accept2=$row2["candidate1accept"];
	$candidate2accept2=$row2["candidate2accept"];
	
	if($candidate1accept2 == 1 AND $candidate2accept2 == 1){
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
		
		if($candidate1race != $candidate2race OR $candidate1sex == $candidate2sex){//als verschillend ras of sex geen kinderen
			$sql = "UPDATE characters SET fertile='0' WHERE id='$candidate1id'";
			mysqli_query($mysqli, $sql);
			
			$sql = "UPDATE characters SET fertile='0' WHERE id='$candidate2id'";
			mysqli_query($mysqli, $sql);
		}

		$sql = "DELETE FROM marriageproposal WHERE id='$proposalid' OR candidate1='$candidate1id' OR candidate1='$candidate2id' OR candidate2='$candidate1id' OR candidate2='$candidate2id'";
		mysqli_query($mysqli, $sql);
		
		$content= "<a href='account.php?user=$candidate1liegeuser&charid=$candidate1id'>$candidate1name</a> and <a href='account.php?user=$candidate2user&charid=$candidate2id'>$candidate2name</a> are now married";
		$content=$mysqli->escape_string($content);
		$sql = "INSERT INTO events (date, content, extrainfo) " 
	     . "VALUES (NOW(),'$content','$candidate1user')";
		mysqli_query($mysqli2, $sql);
		
		$content= "<a href='account.php?user=$candidate1liegeuser&charid=$candidate1id'>$candidate1name</a> and <a href='account.php?user=$candidate2user&charid=$candidate2id'>$candidate2name</a> are now married";
		$content=$mysqli->escape_string($content);
		$sql = "INSERT INTO events (date, content, extrainfo) " 
	     . "VALUES (NOW(),'$content','$candidate2user')";
		mysqli_query($mysqli2, $sql);
	}
	
	?>
	<script>
	    window.location = 'marriage.php?accept=1';
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
