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
  header("nationality: error.php");    
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
  <link rel="stylesheet" href="css/styletot.css">
   <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
   <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
   <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
   <link rel="manifest" href="/site.webmanifest">
   <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#add8e6">
   <meta name="msapplication-TileColor" content="#add8e6">
   <meta name="theme-color" content="#ffffff">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  

  
  <script type="text/javascript" src="tinymce/tinymce.js"></script>
  <script>
  tinymce.init({
    selector: '#mytextarea',
	plugins: ["emoticons autolink help preview textcolor"],
   toolbar: [
     'undo redo | styleselect | forecolor backcolor | bold italic | emoticons | alignleft aligncenter alignright | help | preview'
   ]
  });
  </script>
  

</head>

<body>
<div class="boxedtot">
<?php
require 'ageing.php';

if (isset($_GET["page"])) { $page  = $_GET["page"]; } else { $page=1; };
$page=$mysqli->escape_string($page);
$results_per_page=20;
$start_from = ($page-1) * $results_per_page;

$result = $mysqli->query("SELECT politicalparty, nationality FROM users WHERE username='$username'") or die($mysqli->error());
$row = mysqli_fetch_array($result);
$politicalparty=$row['politicalparty'];
$nationality=$row['nationality'];

if($politicalparty != 0){
	$result = $mysqli->query("SELECT name FROM politicalparty WHERE id='$politicalparty'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$politicalpartyname=$row['name'];
}

?> <div class="everythingOnOneLine2"> <?php
	?> <div class="flexcontainer"> <?php
		if($politicalparty != 0){ ?> <div class="h1"> <?php echo "$politicalpartyname"; ?> </div> <?php }else{ ?> <div class="h1"> <?php echo "Political party"; ?> </div> <?php }
			?>
			<div class="notificationbox2">
				<div class="notificationbox3">
					<a href="rankings.php?type=politicalpartymembers&country=<?php echo "$politicalparty"; ?>&sort=username&order=asc">
					<img src="img/membersicon.png">
					</a>
				</div>
				
				<div class="notificationbox3">											
					<a href="chat.php?type=politicalparty">
					<img src="img/chaticon.png">
					</a>
				</div>
			</div>
			<?php
		 
	?> </div> <?php
?> </div> <?php
?> <hr /> <?php

//join political party
if($politicalparty==0){
	$sql = "SELECT id, name, owner FROM politicalparty WHERE country='$nationality' ORDER BY id ASC LIMIT $start_from, ".$results_per_page;
	$rs_result = $mysqli->query($sql);	

	//print_r($set);
	
	?> 
	<table id="table1">
		<tr>
	    <th> Name</th>
	    <th> Owner</th>
	    <th> Members</th>
	    <th> Join</th>
	</tr>
	<?php
	while($row = $rs_result->fetch_assoc()) {
		//count number of members
		$id=$row["id"];
		$result2 = $mysqli->query("SELECT username FROM users WHERE politicalparty='$id'") or die($mysqli->error());
		$count = $result2->num_rows;
		?> 
	           <tr>
		           <td><?php echo $row["name"]; ?></td>
		           <td><?php echo $row["owner"]; ?></td>
		           <td><?php echo $count; ?></td>
		           <td>
						<form method="post" action="">
							<input type="hidden" name="id" value="<?php echo $row["id"]; ?>" />
							<input type="hidden" name="name" value="<?php echo $row["name"]; ?>" />
							<input type="hidden" name="owner" value="<?php echo $row["owner"]; ?>" />
							<button type="submit" name="join" /><?php echo "Join political party"; ?></button>
						</form>
		           </td>
	           </tr>
		<?php
	}; 
	?>
	</table>
	<?php

	$sql = "SELECT COUNT(id) AS total FROM politicalparty WHERE country='$nationality'";
	$result = $mysqli->query($sql);
	$row = $result->fetch_assoc();
	$total_pages = ceil($row["total"] / $results_per_page); // calculate total pages with results

	for ($i=1; $i<=$total_pages; $i++) {  // print links for all pages
    	echo "<a href='politicalparty.php?page=".$i."'";
        if ($i==$page)  echo " class='curPage'";
        echo ">".$i."</a> "; 
	};
}

if(isset($_POST['join'])){
	$id = $mysqli->escape_string($_POST['id']);
	$id = (int) $id;
	
	$result = $mysqli->query("SELECT country FROM politicalparty WHERE id='$id'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$country = $row['country'];
	
	if($country==$nationality){
		$sql = "UPDATE users SET politicalparty ='$id' WHERE username='$username'";
		mysqli_query($mysqli, $sql);
		
		echo'<div class="boxed">Joined!</div>';
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
//show you political party info
if($politicalparty!=0){
	$result = $mysqli->query("SELECT * FROM politicalparty WHERE id='$politicalparty'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$id=$row['id'];
	$name=$row['name'];
	$owner=$row['owner'];
	$message=$row['message'];
	$message = $purifier->purify($message);
	$country=$row['country'];
	$partypresident=$row['partypresident'];
	$structure=$row['structure'];
	$partypresidentel=$row['partypresidentel'];
	$gold=$row['gold'];
	
	//update partypresident elections
	if($partypresidentel==0){
		$result2 = $mysqli->query("SELECT * FROM elections WHERE party='$id' ORDER BY votes DESC LIMIT 1") or die($mysqli->error());
		$count = $result2->num_rows;
		
		if($count==0){
			$sql = "UPDATE politicalparty SET partypresidentel='1' WHERE id='$id'";
			mysqli_query($mysqli, $sql);
		}else{
			$result = $mysqli->query("SELECT * FROM elections WHERE party='$id' ORDER BY votes DESC LIMIT 1") or die($mysqli->error());
			$row = mysqli_fetch_array($result);
			$idel = $row['id'];
			$candidate = $row['candidate'];	
			
			$sql = "UPDATE politicalparty SET partypresident = '$candidate', partypresidentel='1' WHERE id='$id'";
			mysqli_query($mysqli, $sql);
			
			$sql = "DELETE FROM elections WHERE type = 'partypresident' AND party='$id'";
			mysqli_query($mysqli, $sql);
		}
	}
	
	$result2 = $mysqli->query("SELECT username FROM users WHERE politicalparty='$politicalparty'") or die($mysqli->error());
	$count = $result2->num_rows;
	
	?>
	<div class="scroll">
	<table id="table1">
		<tr>
		    <th> Name</th>
		    <th> Owner</th>
		    <th> Party president</th>
		    <th> Country</th>
		    <th> Members</th>
		    <th> Gold</th>
		</tr>

		<tr>
	       <td><?php echo $name; ?></td>
	       <td><?php echo $owner; ?></td>
	       <td><?php echo $partypresident; ?></td>
	       <td><?php echo $country; ?></td>
	       <td><?php echo $count; ?></td>
	       <td><?php echo $gold; ?></td>
		</tr>	
	</table>
	
	<table id="table1">
		<tr>
		    <th> Message</th>
		</tr>

		<tr>
	       <td><?php echo $message; ?></td>
		</tr>	
	</table>
	</div>
	<?php
	echo nl2br("<div class=\"t1\"><a href='rankings.php?type=runcongress&country=$id&sort=electorder&order=asc'>view candidate congress members</a></div>");

	if($partypresident == "$username"){
		//set message
		?>
		<form method="post" action="">
			<input type="hidden" name="id" value="<?php echo "$id "; ?>" />
			<textarea rows="4" cols="50" id='mytextarea' name="message" maxlength="5000" placeholder="Enter new message here"></textarea>
			<button type="submit" name="newmessage" /><?php echo "Set new message"; ?></button>
		</form>
		<form method="post" action="">
			<input type="hidden" name="id" value="<?php echo "$id "; ?>" />
			<button type="submit" name="addadvertisement" /><?php echo "Buy advertisement"; ?></button>
		</form>
		<?php
	}
}

if($politicalparty!=0){
	date_default_timezone_set('UTC');
	$day = date("d");
	
	?>
	<form method="post" action="">  
		<button type="submit" name="leave" />Leave political party</button>
		<button type="submit" name="donateform" />Donate gold to party</button>
		<button type="submit" name="selectform" />View one of your political party's</button>
		<button type="submit" name="createform" />Create a political party</button>
		<?php if($day != 15){?>
		<button type="submit" name="runcongress" />Run for congress</button>
		<?php } if($day != 8 && $structure == 1){ ?>
			<button type="submit" name="runpartypres" />Run for party president</button>
		<?php } ?>
	</form>
	<?php
}else{
	?>
	<form method="post" action="">  
		<button type="submit" name="selectform" />View one of your political party's</button>
		<button type="submit" name="createform" />Create a political party</button>
	</form>
	<?php
}

?> <hr /> <?php

if(isset($_POST['leave'])){
	$sql = "UPDATE users SET politicalparty ='0' WHERE username='$username'";
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

if(isset($_POST['runcongress'])){
	echo nl2br("<div class=\"t1\">Running for congress costs 1 gold. Are you sure?</div>");
	?>
	<form method="post" action="">  
		<button type="submit" name="runcongress2" />Yes</button>
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

if(isset($_POST['runcongress2'])){
	$alreadycandidated = $mysqli->query("SELECT * FROM elections WHERE candidate='$username' AND type='congress'") or die($mysqli->error());
		
	$result = $mysqli->query("SELECT * FROM politicalparty WHERE id='$politicalparty'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$runcongress=$row['runcongress'];
	
	$result = $mysqli->query("SELECT * FROM currency WHERE usercur='$username'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$gold=$row['gold'];
	
	if($alreadycandidated->num_rows == 0){
		$gold = $gold - 1;
		$runcongress = $runcongress + 1;
		if($gold >= 0){
			$sql = "INSERT INTO elections (type, candidate, countryel, party) " 
    		. "VALUES ('congress','$username','$nationality', '$politicalparty')";
	 		mysqli_query($mysqli, $sql);
			
			$sql = "UPDATE currency SET gold ='$gold' WHERE usercur='$username'";
			mysqli_query($mysqli, $sql);
			
			$sql = "UPDATE politicalparty SET runcongress ='$runcongress' WHERE id='$politicalparty'";
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
		echo'<div class="boxed">You already run for congress!</div>';
		
		?>
		<script>
		    if ( window.history.replaceState ) {
		        window.history.replaceState( null, null, window.location.href );
		    }
		</script>
		<?php
	}

}

if(isset($_POST['donateform'])){
	echo nl2br("<div class=\"t1\">To donate gold to this party enter a amount below.</div>");
	?>
	<form method="post" action=""> 
		<input type="number" size="25" required autocomplete="off" id="amount" name="amount" min="0.01" step="0.01" /> 
		<button type="submit" name="donateform2" />Donate</button>
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
		
if(isset($_POST['donateform2'])){
	$amount = $mysqli->escape_string($_POST['amount']);
	$amount = (double) $amount;
	if($amount > 0){
		$result = $mysqli->query("SELECT * FROM currency WHERE usercur='$username'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$gold=$row['gold'];
		
		$result = $mysqli->query("SELECT gold FROM politicalparty WHERE id='$politicalparty'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$partygold=$row['gold'];
		
		$gold=$gold-$amount;
		$partygold=$partygold+$amount;
		if($gold >= 0){
			$sql = "UPDATE politicalparty SET gold ='$partygold' WHERE id='$politicalparty'";
			mysqli_query($mysqli, $sql);
			
			$sql = "UPDATE currency SET gold ='$gold' WHERE usercur='$username'";
			mysqli_query($mysqli, $sql);
			
			echo nl2br ("<div class=\"boxed\">Done!</div>");
		}else{
			echo nl2br ("<div class=\"boxed\">You don\'t have enough gold!</div>");
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

if(isset($_POST['runpartypres'])){
	echo nl2br("<div class=\"t1\">Running for party president costs 1 gold. Are you sure?</div>");
	?>
	<form method="post" action="">  
		<button type="submit" name="runpartypres2" />Yes</button>
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

if(isset($_POST['runpartypres2'])){
	$alreadycandidated = $mysqli->query("SELECT * FROM elections WHERE candidate='$username' AND type='partypresident'") or die($mysqli->error());
	
	if($alreadycandidated->num_rows == 0){
		$result = $mysqli->query("SELECT * FROM currency WHERE usercur='$username'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$gold=$row['gold'];
		$gold=$gold-1;
		if($gold >= 0){
			$sql = "INSERT INTO elections (type, candidate, countryel, party) " 
    		. "VALUES ('partypresident','$username','$nationality', '$politicalparty')";
	 		mysqli_query($mysqli, $sql);
			
			$sql = "UPDATE currency SET gold ='$gold' WHERE usercur='$username'";
			mysqli_query($mysqli, $sql);
		}else{
			echo'<div class="boxed">You do not have enough gold!!</div>';
		}
	}else{
		echo'<div class="boxed">You already run for party president!</div>';
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
if(isset($_POST['selectform'])){
	//owner settings
	$result = $mysqli->query("SELECT * FROM politicalparty WHERE owner='$username'") or die($mysqli->error());
	$columnValues = Array();
	$count = $result->num_rows;
	while ( $row = mysqli_fetch_assoc($result) ) {
		$columnValues[] = $row['id'];
	}
	
	if($count != 0){
		?>
		<form method="post" action="">
		    <select name="selectparty" type="text">
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
		    <button type="submit" name="selectpartyform" />Select political party</button>
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
if(isset($_POST['selectpartyform'])){
	$id = $mysqli->escape_string($_POST['selectparty']);
	$id = (int) $id;
	
	$result = $mysqli->query("SELECT * FROM politicalparty WHERE id='$id' AND owner='$username'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$id=$row["id"];
	$country=$row['country'];
	$name=$row['name'];
	$owner=$row['owner'];
	$message=$row['message'];
    $message = $purifier->purify($message);
	$partypresident=$row['partypresident'];
	$structure=$row['structure'];
	
	$result2 = $mysqli->query("SELECT username FROM users WHERE politicalparty='$id'") or die($mysqli->error());
	$count = $result2->num_rows;
	
	echo nl2br ("<div class=\"h1\">Owner settings of $name</div>");
	?> <hr /> <?php
		
	?> 
	<div class="scroll">
	<table id="table1">
		<tr>
		    <th> Name</th>
		    <th> Owner</th>
		    <th> Party president</th>
		    <th> Country</th>
		    <th> Members</th>
		</tr>

		<tr>
	       <td><?php echo $name; ?></td>
	       <td><?php echo $owner; ?></td>
	       <td><?php echo $partypresident; ?></td>
	       <td><?php echo $country; ?></td>
	       <td><?php echo $count; ?></td>
		</tr>	
	</table>
	
	<table id="table1">
		<tr>
		    <th> Message</th>
		</tr>

		<tr>
	       <td><?php echo $message; ?></td>
		</tr>	
	</table>
	</div>
	<?php
	
	//change structure
	if($structure == 0){
		echo nl2br("<div class=\"t1\">Change the party structure to allow party president elections. Warning: this change can't be undone.</div>");
		?>
		<form method="post" action="">
			<input type="hidden" name="id" value="<?php echo "$id "; ?>" />
			<button type="submit" name="changestruct" /><?php echo "Change"; ?></button>
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

if(isset($_POST['newmessage'])){
	$id = $mysqli->escape_string($_POST['id']);
	$id = (int) $id;
	$message = $mysqli->escape_string($_POST['message']);
	
	$result2 = mysqli_query($mysqli,"SELECT * FROM politicalparty WHERE id='$id'");
	$row2=mysqli_fetch_array($result2);
	$partypresident=$row2["partypresident"];
	
	if($partypresident==$username){
		if(strlen($message) <= 5050){
			$sql = "UPDATE politicalparty SET message ='$message' WHERE id='$id'";
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
}

if(isset($_POST['addadvertisement'])){
	$id = $mysqli->escape_string($_POST['id']);
	$id = (int) $id;
	?> <div class="textbox2"> <?php
	echo nl2br ("<div class=\"t1\">Buy advertisement space for the next elections. This costs 5 gold.</div>");
	
	?>
	<br><br>
	<form method="post" action="">
		<input type="hidden" name="id" value="<?php echo "$id "; ?>" />
		<textarea rows="4" cols="50" id='mytextarea' name="content" maxlength="1000" autofocus placeholder="Enter content here"></textarea>
		<button type="submit" name="addadvertisement2" /><?php echo "Submit"; ?></button>
	</form>
	<?php
	?> </div> <?php
	?>
	<script>
	    if ( window.history.replaceState ) {
	        window.history.replaceState( null, null, window.location.href );
	    }
	</script>
	<?php
}

if(isset($_POST['addadvertisement2'])){
	$id = $mysqli->escape_string($_POST['id']);
	$id = (int) $id;
	$content = $mysqli->escape_string($_POST['content']);
	
	$result2 = mysqli_query($mysqli,"SELECT * FROM politicalparty WHERE id='$id'");
	$row2=mysqli_fetch_array($result2);
	$partypresident=$row2["partypresident"];
	$gold=$row2["gold"];
	
	if($partypresident==$username){
		$gold=$gold-5;
		if($gold>=0){
			if(strlen($content)<=1050){
				$sql = "UPDATE politicalparty SET gold ='$gold', ad='1', adtext='$content' WHERE id='$id' AND owner='$username'";
				mysqli_query($mysqli, $sql);
				
				echo'<div class="boxed">Done!</div>';
			}
		}else{
			echo'<div class="boxed">The party does not have enough gold!</div>';
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

if(isset($_POST['changestruct'])){
	$id = $mysqli->escape_string($_POST['id']);
	$id = (int) $id;
	
	$sql = "UPDATE politicalparty SET structure ='1' WHERE id='$id' AND owner='$username'";
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

//create political party
if(isset($_POST['createform'])){
	echo nl2br ("<div class=\"t1\">Creating a political party cots 15 gold.</div>");
	?>
	<form onsubmit="return confirm('Are you sure?');" method="post" action="">
		<input type="text" required placeholder="Enter name here" maxlength="15" pattern="[a-zA-Z0-9-]+" size="25" required autocomplete="off" id="name" name="name"/>
		<button type="submit" name="create" /><?php echo "Create party"; ?></button>
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

if(isset($_POST['create'])){
	$name = $mysqli->escape_string($_POST['name']);
	
	if(ctype_alnum($name) AND strlen($name) <= 15 AND strlen($name) >= 1){
		$result = $mysqli->query("SELECT nationality FROM users WHERE username='$username'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$nationality=$row['nationality'];
		
		$result = $mysqli->query("SELECT gold FROM currency WHERE usercur='$username'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$gold=$row['gold'];
		$gold=$gold-15;
		
		if($gold>=0){
			// Check if user with that username already exists and create username in currency table
			$result = $mysqli->query("SELECT * FROM politicalparty WHERE name='$name'") or die($mysqli->error());
			// We know table for inventory & currency exists if the rows returned are more than 0
			if ( $result->num_rows > 0 ) {
				echo "This name already exists!";
			}else{
				$sql = "INSERT INTO politicalparty (name, owner, partypresident, country) " 
	     		 . "VALUES ('$name','$username','$username','$nationality')";
				mysqli_query($mysqli, $sql);
				
				$result = $mysqli->query("SELECT id FROM politicalparty WHERE name='$name'") or die($mysqli->error());
				$row = mysqli_fetch_array($result);
				$politicalpartyid=$row['id'];
				
				$sql = "UPDATE currency SET gold ='$gold' WHERE usercur='$username'";
				mysqli_query($mysqli, $sql);
				
				echo "Done!";
				
				?>
				<script>
				    if ( window.history.replaceState ) {
				        window.history.replaceState( null, null, window.location.href );
				    }
				</script>
				<?php
			}
		}else{
			echo "You don't have enough gold!";
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

?>
</div>
</body>
<footer>
<?php require 'bottombar.php'; ?>
</footer>
</html>
