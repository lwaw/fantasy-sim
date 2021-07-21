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



<body>
<div class="boxedtot">
<?php
require 'ageing.php';

if (isset($_GET["page"])) { $page  = $_GET["page"]; } else { $page=1; };
$page=$mysqli->escape_string($page);
$results_per_page=20;
$start_from = ($page-1) * $results_per_page;

$resistancewarprice=50;
$revolutionprice=50;

$result = $mysqli->query("SELECT militaryunit, location, location2 FROM users WHERE username='$username'") or die($mysqli->error());
$row = mysqli_fetch_array($result);
$militaryunit=$row['militaryunit'];
$location=$row['location'];
$location2=$row['location2'];

if($militaryunit != 0){
	$result = $mysqli->query("SELECT name FROM militaryunit WHERE id='$militaryunit'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$militaryunitname=$row['name'];
}

?> <div class="everythingOnOneLine2"> <?php
	?> <div class="flexcontainer"> <?php
		if($militaryunit != 0){ ?> <div class="h1"> <?php echo "$militaryunitname"; ?> </div> <?php }else{ ?> <div class="h1"> <?php echo "Military unit"; ?> </div> <?php }
			?>
			<div class="notificationbox2">
				<div class="notificationbox3">
					<a href="rankings.php?type=militaryunitmembers&country=<?php echo "$militaryunit"; ?>&sort=username&order=asc">
					<img src="img/membersicon.png">
					</a>
				</div>
				
				<div class="notificationbox3">											
					<a href="chat.php?type=militaryunit">
					<img src="img/chaticon.png">
					</a>
				</div>
			</div>
			<?php
		 
	?> </div> <?php
?> </div> <?php
?> <hr /> <?php

//join military unit
if($militaryunit==0){
	
	$sql = "SELECT id, name, owner, percentowner, percentuser, percentunit FROM militaryunit WHERE country='$location' ORDER BY id ASC LIMIT $start_from, ".$results_per_page;
	$rs_result = $mysqli->query($sql);	
	//print_r($set);
	
	?> 
	<table id="table1">
		<tr>
	    <th> Name</th>
	    <th> Owner</th>
	    <th> Members</th>
	    <th> Percentage owner</th>
	    <th> Percentage user</th>
	    <th> Percentage military unit</th>
	    <th> Join</th>
	</tr>
	<?php
	while($row = $rs_result->fetch_assoc()) {
		//count number of members
		$id=$row["id"];
		$result2 = $mysqli->query("SELECT username FROM users WHERE militaryunit='$id'") or die($mysqli->error());
		$count = $result2->num_rows;
		?> 
	           <tr>
		           <td><?php echo $row["name"]; ?></td>
		           <td><?php echo $row["owner"]; ?></td>
		           <td><?php echo $count; ?></td>
		           <td><?php echo $row["percentowner"]; ?></td>
		           <td><?php echo $row["percentuser"]; ?></td>
		           <td><?php echo $row["percentunit"]; ?></td>
		           <td>
						<form method="post" action="">
							<input type="hidden" name="id" value="<?php echo $row["id"]; ?>" />
							<input type="hidden" name="name" value="<?php echo $row["name"]; ?>" />
							<input type="hidden" name="owner" value="<?php echo $row["owner"]; ?>" />
							<button type="submit" name="join" /><?php echo "Join military unit"; ?></button>
						</form>
		           </td>
	           </tr>
		<?php		
	}; 
	?>
	</table>
	<?php
	$sql = "SELECT COUNT(id) AS total FROM militaryunit WHERE country='$location'";
	$result = $mysqli->query($sql);
	$row = $result->fetch_assoc();
	$total_pages = ceil($row["total"] / $results_per_page); // calculate total pages with results
	
	for ($i=1; $i<=$total_pages; $i++) {  // print links for all pages
    	echo "<a href='militaryunit.php?page=".$i."'";
        if ($i==$page)  echo " class='curPage'";
        echo ">".$i."</a> "; 
	};
}

if(isset($_POST['join'])){
	$id = $mysqli->escape_string($_POST['id']);
	
	$sql = "UPDATE users SET militaryunit='$id' WHERE username='$username' AND location='$location'";
	mysqli_query($mysqli, $sql);
	
	echo'<div class="boxed">Joined!</div>';
	
	?>
	<script>
	    if ( window.history.replaceState ) {
	        window.history.replaceState( null, null, window.location.href );
	    }
	</script>
	<?php
}

//show you militry unit info
if($militaryunit!=0){
	$result = $mysqli->query("SELECT * FROM militaryunit WHERE id='$militaryunit'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$id=$row['id'];
	$name=$row['name'];
	$owner=$row['owner'];
	$message=$row['message'];
	$message = $purifier->purify($message);
	$country=$row['country'];
	$percentowner=$row['percentowner'];
	$percentuser=$row['percentuser'];
	$percentunit=$row['percentunit'];
	$camp=$row['camp'];
	$gold=$row['gold'];
	
	$result2 = $mysqli->query("SELECT username FROM users WHERE militaryunit='$militaryunit'") or die($mysqli->error());
	$count = $result2->num_rows;
	
	?>
	<div class="scroll">
	<table id="table1">
		<tr>
		    <th> Name</th>
		    <th> Owner</th>
		    <th> Country</th>
		    <th> Members</th>
		    <th> Percentage owner</th>
		    <th> Percentage unit</th>
		    <th> Percentage user</th>
		    <th> Camp</th>
		    <th> Gold</th>
		</tr>

		<tr>
	       <td><?php echo $name; ?></td>
	       <td><?php echo $owner; ?></td>
	       <td><?php echo $country; ?></td>
	       <td><?php echo $count; ?></td>
	       <td><?php echo $percentowner; ?></td>
	       <td><?php echo $percentuser; ?></td>
	       <td><?php echo $percentunit; ?></td>
	       <td><?php echo $camp; ?></td>
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
}

if($militaryunit != 0){
	?>
	<form method="post" action="">
		<button type="submit" name="leave" />Leave military unit</button>
		<button type="submit" name="donateform" />Donate gold to military unit</button>
		<button type="submit" name="selectform" />View one of your military units</button>
		<button type="submit" name="createform" />Create a military unit</button>
	</form>
	<?php
}else{
	?>
	<form method="post" action="">
		<button type="submit" name="selectform" />View one of your military units</button>
		<button type="submit" name="createform" />Create a military unit</button>
	</form>
	<?php
}

?> <hr /> <?php

if(isset($_POST['leave'])){
	$sql = "UPDATE users SET militaryunit ='0', militaryunitrank='0' WHERE username='$username'";
	mysqli_query($mysqli, $sql);
	
	echo nl2br ("<div class=\"boxed\">Done!</div>");
	
	?>
	<script>
	    if ( window.history.replaceState ) {
	        window.history.replaceState( null, null, window.location.href );
	    }
	</script>
	<?php
}

if(isset($_POST['donateform'])){
	echo nl2br("<div class=\"t1\">To donate gold to this military unit enter a amount below.</div>");
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
		
		$result = $mysqli->query("SELECT gold FROM militaryunit WHERE id='$militaryunit'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$unitgold=$row['gold'];
		
		$gold=$gold-$amount;
		$unitgold=$unitgold+$amount;
		if($gold >= 0){
			$sql = "UPDATE militaryunit SET gold ='$unitgold' WHERE id='$militaryunit'";
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

//owner settings
if(isset($_POST['selectform'])){
	$result = $mysqli->query("SELECT * FROM militaryunit WHERE owner='$username'") or die($mysqli->error());
	$columnValues = Array();
	while ( $row = mysqli_fetch_assoc($result) ) {
		$columnValues[] = $row['id'];
	}
	
	?>
	<form method="post" action="">
	    <select name="selectunit" type="text">
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
	    <button type="submit" name="selectunitform" />Select military unit</button>
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

if(isset($_POST['selectunitform'])){
	$id = $mysqli->escape_string($_POST['selectunit']);
	
	$result = $mysqli->query("SELECT * FROM militaryunit WHERE id='$id' AND owner='$username'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$country=$row['country'];
	$gold=$row['gold'];
	$name=$row['name'];
	$owner=$row['owner'];
	$message=$row['message'];
	$message = $purifier->purify($message);
	$percentowner=$row['percentowner'];
	$percentuser=$row['percentuser'];
	$percentunit=$row['percentunit'];
	$camp=$row['camp'];
	
	$result2 = $mysqli->query("SELECT username FROM users WHERE militaryunit='$id'") or die($mysqli->error());
	$count = $result2->num_rows;
	
	echo nl2br ("<div class=\"h1\">Owner settings of $name</div>");
	?> <hr /> <?php
		
	?>
	<div class="scroll"> 
	<table id="table1">
		<tr>
		    <th> Name</th>
		    <th> Owner</th>
		    <th> Country</th>
		    <th> Members</th>
		    <th> Gold</th>
		    <th> Percentage owner</th>
		    <th> Percentage unit</th>
		    <th> Percentage user</th>
		    <th> Camp</th>
		</tr>

		<tr>
	       <td><?php echo $name; ?></td>
	       <td><?php echo $owner; ?></td>
	       <td><?php echo $country; ?></td>
	       <td><?php echo $count; ?></td>
	       <td><?php echo $gold; ?></td>
	       <td><?php echo $percentowner; ?></td>
	       <td><?php echo $percentuser; ?></td>
	       <td><?php echo $percentunit; ?></td>
	       <td><?php echo $camp; ?></td>
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
	
	echo nl2br("<div class=\"t1\"><a href='rankings.php?type=militaryunitmembers&country=$id&sort=username&order=asc'>view members</a></div>");
	
	//change percentages
	echo nl2br ("<div class=\"t1\">With this setting you can change what percentage of the payment that is earned while fighting for a country will go to who.</div>");
	?>
	<form method="post" action="">
		<input type="hidden" name="id" value="<?php echo "$id "; ?>" />
		<label for="percentowner">Set percentage for owner:</label>
		<input type="number" size="25" required autocomplete="off" id="percentowner" name="percentowner" min="1" max="99" step="1" />
		<label for="percentunit">Set percentage for unit:</label>
		<input type="number" size="25" required autocomplete="off" id="percentunit" name="percentunit" min="1" max="99" step="1" />
		<label for="percentuser">Set percentage for user:</label>
		<input type="number" size="25" required autocomplete="off" id="percentuser" name="percentuser" min="1" max="99" step="1" />
		<button type="submit" name="changepercent" /><?php echo "Total should be 100"; ?></button>
		</form>
	<?php
	
	//set new message
	?>
	<form method="post" action="">
		<input type="hidden" name="id" value="<?php echo "$id "; ?>" />
		<textarea rows="4" cols="50" id='mytextarea' name="message" maxlength="500">Enter text here...</textarea>
		<button type="submit" name="newmessage" /><?php echo "Set new message"; ?></button>
	</form>
	<?php
	
	//set camp in current region
	echo nl2br ("<div class=\"t1\">If you set up a camp in a region you can fund a resistance war from this region with your military unit. Members of this military unit will also get bonus damage when fighting in this region. It costs 5 gold to set up a camp.</div>");
	?>
	<form method="post" action="">
		<input type="hidden" name="id" value="<?php echo "$id "; ?>" />
		<button type="submit" name="setcamp" /><?php echo "Set up camp"; ?></button>
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

if(isset($_POST['changepercent'])){
	$id = $mysqli->escape_string($_POST['id']);
	$percentowner = $mysqli->escape_string($_POST['percentowner']);
	$percentowner = (int) $percentowner;
	$percentunit = $mysqli->escape_string($_POST['percentunit']);
	$percentunit = (int) $percentunit;
	$percentuser = $mysqli->escape_string($_POST['percentuser']);
	$percentuser = (int) $percentuser;
	
	$total=$percentowner+$percentunit+$percentuser;
	if($total==100){
		$sql = "UPDATE militaryunit SET percentowner ='$percentowner', percentunit='$percentunit', percentuser='$percentuser' WHERE id='$id' AND owner='$username'";
		mysqli_query($mysqli, $sql);
		echo nl2br ("<div class=\"boxed\">Done!</div>");
	}else{
		echo nl2br ("<div class=\"boxed\">Numbers don't add up to 100!</div>");
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
	$message = $mysqli->escape_string($_POST['message']);
	if(strlen($message) <= 5050){
		$sql = "UPDATE militaryunit SET message ='$message' WHERE id='$id' AND owner='$username'";
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

if(isset($_POST['setcamp'])){
	$id = $mysqli->escape_string($_POST['id']);
	
	$result = $mysqli->query("SELECT location2 FROM users WHERE username='$username'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$location2=$row['location2'];
	$location2 = $mysqli->escape_string($location2);
	
	$result = $mysqli->query("SELECT gold, camp FROM militaryunit WHERE id='$id' AND owner='$username'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$gold=$row['gold'];
	$camp=$row['camp'];
	$camp = $mysqli->escape_string($camp);
	
	if($camp != $location2){
		$location2=$mysqli->escape_string($location2);
		$gold=$gold-5;
		if($gold>=0){
			$sql = "UPDATE militaryunit SET gold ='$gold', camp='$location2' WHERE id='$id' AND owner='$username'";
			mysqli_query($mysqli, $sql);
		}else{
			echo nl2br ("<div class=\"boxed\">The military unit doesn\'t have enough gold!</div>");
		}
	}else{
		echo nl2br ("<div class=\"boxed\">The military unit already set camp in this region!</div>");
	}
	?>
	<script>
	    if ( window.history.replaceState ) {
	        window.history.replaceState( null, null, window.location.href );
	    }
	</script>
	<?php
}

//create military unit
if(isset($_POST['createform'])){
	echo nl2br ("<div class=\"t1\">Creating a military unit cots 15 gold.</div>");
	?>
	<form onsubmit="return confirm('Are you sure?');" method="post" action="">
		<input type="text" required placeholder="Enter name here" maxlength="15" pattern="[a-zA-Z0-9-]+" size="25" required autocomplete="off" id="name" name="name"/>
		<button type="submit" name="create" /><?php echo "Create unit"; ?></button>
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
	$result = $mysqli->query("SELECT location FROM users WHERE username='$username'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$location=$row['location'];
	
	$result = $mysqli->query("SELECT gold FROM currency WHERE usercur='$username'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$gold=$row['gold'];
	$gold=$gold-15;
	
	$name = $mysqli->escape_string($_POST['name']);
	
	if(ctype_alnum($name) AND strlen($name) <= 15 AND strlen($name) >= 1){
		if($gold>=0){
			// Check if user with that username already exists and create username in currency table
			$result = $mysqli->query("SELECT * FROM militaryunit WHERE name='$name'") or die($mysqli->error());
			// We know table for inventory & currency exists if the rows returned are more than 0
			if ( $result->num_rows > 0 ) {
				echo nl2br ("<div class=\"boxed\">This name already exists!</div>");
			}else{
				$sql = "INSERT INTO militaryunit (name, owner, country) " 
	     		 . "VALUES ('$name','$username','$location')";
				mysqli_query($mysqli, $sql);
				
				$result = $mysqli->query("SELECT id FROM militaryunit WHERE name='$name'") or die($mysqli->error());
				$row = mysqli_fetch_array($result);
				$militaryunitid=$row['id'];
				
				$sql = "UPDATE currency SET gold ='$gold' WHERE usercur='$username'";
				mysqli_query($mysqli, $sql);
				
				echo nl2br ("<div class=\"boxed\">Done!</div>");
			}
		}else{
			echo nl2br ("<div class=\"boxed\">You don't have enough gold!</div>");
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
