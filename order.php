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
if (isset($_GET["order"])) { $religionorder  = $_GET["order"]; } else { $religionorder=0; };
$religionorder=$mysqli->escape_string($religionorder);

if (isset($_GET["page"])) { $page  = $_GET["page"]; } else { $page=1; };
$page=$mysqli->escape_string($page);
$results_per_page=20;
$start_from = ($page-1) * $results_per_page;

$result = $mysqli->query("SELECT * FROM users WHERE username='$username'") or die($mysqli->error());
$row = mysqli_fetch_array($result);
$location=$row['location'];
$location2=$row['location2'];
$orderrank=$row['orderrank'];

$result = $mysqli->query("SELECT userreligion, location2, religionorder FROM users WHERE username='$username'") or die($mysqli->error());
$row = mysqli_fetch_array($result);
$userreligion=$row['userreligion'];
$userorder=$row['religionorder'];
$location2=$mysqli->escape_string($row['location2']);

$result = $mysqli->query("SELECT * FROM religion WHERE name='$userreligion'") or die($mysqli->error());
$row = mysqli_fetch_array($result);
$leader=$row['leader'];
$id=$row['id'];
	
if($religionorder != 0){
	$result2 = $mysqli->query("SELECT id FROM religion WHERE owner='$username'") or die($mysqli->error());
	$count = $result2->num_rows;
	
	$result = $mysqli->query("SELECT * FROM religion WHERE id='$userorder'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$orderid=$row['id'];
	$ordername=$row['name'];
	$orderleader=$row['leader'];
	$orderreligionid=$row['religionid'];
	$gold=$row['gold'];
	$donatedgold=$row['donatedgold'];
	$type=$row['type'];
	$ordernominee=$row['nominee'];
	
	if($ordernominee != NULL AND $ordernominee != "NULL"){
		$nominee = $ordernominee;
	}else{
		$nominee = "None";
	}
	
	$result = $mysqli->query("SELECT * FROM religion WHERE id='$orderreligionid'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$orderreligionname=$row['name'];
	
	$result = $mysqli->query("SELECT * FROM characters WHERE user='$orderleader' AND alive='1'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$leadercharacterid=$row['id'];
	$leadercharactername=$row['name'];
	$leadercharacterfamilyid=$row['familyid'];
	
	$result = $mysqli->query("SELECT * FROM family WHERE id='$leadercharacterfamilyid'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$leadercharacterfamilyname=$row['name'];
	
	$link="<a href='account.php?user=$orderleader&charid=$leadercharacterid'>$leadercharactername $leadercharacterfamilyname</a>";
	
	if($religionorder==$userorder){
		?> <div class="everythingOnOneLine2"> <?php
			?> <div class="flexcontainer"> <?php
				?> <div class="h1"> <?php echo "$ordername"; ?> </div> <?php
					?>
					<div class="notificationbox2">
						<div class="notificationbox3">
							<a href="rankings.php?type=religionmembers&country=<?php echo "$userorder"; ?>&sort=username&order=asc">
							<img src="img/membersicon.png">
							</a>
						</div>
					
						<div class="notificationbox3">											
							<a href="chat.php?type=religionorder">
							<img src="img/chaticon.png">
							</a>
						</div>
					</div>
					<?php
				 
			?> </div> <?php
		?> </div> <?php
		?> <hr /> <?php
		
		echo nl2br ("<div class=\"bold\">Order overview</div>");
		?>
		<table id="table1">
		    <tr>
		    	<th>
		    	<div id="block_container">
					<?php echo "Leader: $link"; ?>
				</div>
				</th>
		    </tr>
		</table>
		<table id="table1">	
		    <tr>
		       <td><?php echo "gold: $gold"; ?></td>
		       <td><?php echo "Gold donated to religion: $donatedgold"; ?></td>
		       <td><?php echo "Nominee: $nominee"; ?></td>
		       <td><?php echo "Type: $type"; ?></td>
		    </tr>			
		</table>
		<?php
		
		$result = $mysqli->query("SELECT curowner, `1`, `2`, `3` FROM region WHERE name='$location2'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$curowner=$row['curowner'];
		
		$r1=$row['1'];
		$r2=$row['2'];
		$r3=$row['3'];
		
		$result = $mysqli->query("SELECT name FROM religion WHERE id='1'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$name1=$row['name'];
		
		$result = $mysqli->query("SELECT name FROM religion WHERE id='2'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$name2=$row['name'];
		
		$result = $mysqli->query("SELECT name FROM religion WHERE id='3'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$name3=$row['name'];
		
		?><hr class="side"><?php
		echo nl2br ("<div class=\"bold\">Faith of religions in $location2</div>");
		?> 
		<table id="table1">
			<tr>
			    <th> <?php echo $name1; ?></th>
			    <th> <?php echo $name2; ?></th>
			    <th> <?php echo $name3; ?></th>
			</tr>
	
			<tr>
		       <td><?php echo $r1; ?></td>
		       <td><?php echo $r2; ?></td>
		       <td><?php echo $r3; ?></td>
			</tr>	
		</table>
		<?php
			
		?>
		<form method="post" action="">
			<button type="submit" name="donatetoorderdorm" />Donate gold to order</button>
			<button type="submit" name="leaveorderform" />Leave order</button>
			<?php if($orderrank > 0){
				?> <button type="submit" name="removereligionform" />Remove religion from region</button> <?php
			} ?>
		<?php
		if($orderleader==$username){
			?>
			<button type="submit" name="orderdonateform" />Donate gold to religion</button>
			<button type="submit" name="nominateleaderform" />Nominate next religeous leader</button>
			<button type="submit" name="inviteform" />Invite user to order</button>
			<?php
		}
		?>
		</form>
		<?php
	}else{
		$result = $mysqli->query("SELECT * FROM religion WHERE id='$religionorder'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$ordername=$row['name'];
		$type=$row['type'];
		$leader=$row['leader'];
		$gold=$row['gold'];
		$donatedgold=$row['donatedgold'];
		
		$result = $mysqli->query("SELECT * FROM characters WHERE user='$leader' AND alive='1'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$leadercharacterid=$row['id'];
		$leadercharactername=$row['name'];
		$leadercharacterfamilyid=$row['familyid'];
		
		$result = $mysqli->query("SELECT * FROM family WHERE id='$leadercharacterfamilyid'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$leadercharacterfamilyname=$row['name'];
		
		$link="<a href='account.php?user=$leader&charid=$leadercharacterid'>$leadercharactername $leadercharacterfamilyname</a>";
		
		if($type=="order"){
			?> <div class="everythingOnOneLine2"> <?php
				?> <div class="flexcontainer"> <?php
					?> <div class="h1"> <?php echo "$ordername"; ?> </div> <?php
						?>
						<div class="notificationbox2">
							<div class="notificationbox3">
								<a href="rankings.php?type=religionmembers&country=<?php echo "$religionorder"; ?>&sort=username&order=asc">
								<img src="img/membersicon.png">
								</a>
							</div>
						</div>
						<?php
					 
				?> </div> <?php
			?> </div> <?php
			
			?> <hr /> <?php
			echo nl2br ("<div class=\"bold\">Order overview</div>");
			?>
			<table id="table1">
			    <tr>
			    	<th>
			    	<div id="block_container">
						<?php echo "Leader: $link"; ?>
					</div>
					</th>
			    </tr>
			</table>
			<table id="table1">	
			    <tr>
			       <td><?php echo "gold: $gold"; ?></td>
			       <td><?php echo "Gold donated to religion: $donatedgold"; ?></td>
			       <td><?php echo "Type: $type"; ?></td>
			    </tr>			
			</table>
			<?php
		}	
	}
	
	if(isset($_POST['leaveorderform'])){
		echo nl2br ("<div class=\"t1\">Are you sure you want to leave your order? This will also remove your rank within the order..</div>");
		?>
		<form method="post" action="">
			<button type="submit" name="leaveorder" />Accept</button>
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
	
	if(isset($_POST['leaveorder'])){
		$sql = "UPDATE users SET religionorder=NULL, orderrank='0' WHERE username='$username'";
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
	
	if(isset($_POST['inviteform'])){
		?>
		<form method="post" action="">
			<input type="text" maxlength="50" name="recipient" placeholder="Receiver">
			<textarea rows="4" cols="50" id='mytextarea' name="content" placeholder="Enter message here..." maxlength="5000"></textarea>
			<button type="submit" name="invite2" />Send</button>
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
	
	if(isset($_POST['donatetoorderdorm'])){
		?>
		<form method="post" action="">
			<input type="number" size="25" required autocomplete="off" id="amount" name="amount" min="0.01" step="0.01" />
			<button type="submit" name="donatetoorder" />Donate gold to order</button>
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
	
	if(isset($_POST['donatetoorder'])){
		$amount = $mysqli->escape_string($_POST['amount']);
		
		$result = $mysqli->query("SELECT * FROM religion WHERE id='$userorder'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$ordergold=$row['gold'];
		$donatedgold=$row['donatedgold'];
		$orderreligionid=$row['religionid'];
		
		$result = $mysqli->query("SELECT * FROM currency WHERE usercur='$username'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$usergold=$row['gold'];
		
		$usergold=$usergold - $amount;
		$ordergold=$ordergold + $amount;
		
		if($usergold >= 0){
			$sql = "UPDATE religion SET gold ='$ordergold' WHERE id='$userorder'";
			mysqli_query($mysqli, $sql);
			
			$sql = "UPDATE currency SET gold ='$usergold' WHERE usercur='$username'";
			mysqli_query($mysqli, $sql);
			
			echo'<div class="boxed">Done!</div>';
		}else{
			echo'<div class="boxed">You do not have enough gold!</div>';
		}
		?>
		<script>
		    if ( window.history.replaceState ) {
		        window.history.replaceState( null, null, window.location.href );
		    }
		</script>
		<?php
	}
	
	if(isset($_POST['orderdonateform'])){
		?>
		<form method="post" action="">
			<input type="number" size="25" required autocomplete="off" id="amount" name="amount" min="0.01" step="0.01" />
			<button type="submit" name="orderdonate" />Donate gold to religion</button>
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
	
	if(isset($_POST['removereligionform'])){
		$result = $mysqli->query("SELECT name FROM religion WHERE type='religion'") or die($mysqli->error());
		$columnValues = Array();
		while ( $row = mysqli_fetch_assoc($result) ) {
		  $columnValues[] = $row['name'];
		}
		
		?>
		<form method="post" action="">
		    <select name="selectreligion" type="text">
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
		    <button type="submit" name="removereligion" />Remove religion from region</button>
		</form>
		<?php		
	}
	
	if(isset($_POST['removereligion'])){
		$selectreligion = $mysqli->escape_string($_POST['selectreligion']);
		
		$result = $mysqli->query("SELECT * FROM users WHERE username='$username'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$orderrank=$row['orderrank'];
		$removedreligion=$row['removedreligion'];
		$orderreligion=$row['religionorder'];
		$location2=$row['location2'];
		$spreadbonus=$row['spreadbonus'];
		$userreligion=$row['userreligion'];
		$sleepstate=$row['state'];
		
		$result = $mysqli->query("SELECT curowner, `1`, `2`, `3` FROM region WHERE name='$location2'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$curowner=$row['curowner'];
		
		$r1=$row['1'];
		$r2=$row['2'];
		$r3=$row['3'];
		
		$result = $mysqli->query("SELECT * FROM religion WHERE name='$selectreligion'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$religionid=$row['id'];
		
		$removestrength=$orderrank*3;
		if($removedreligion==0){
			if($id==1){
				$currspread=$r1-$removestrength;
			}elseif($id==2){
				$currspread=$r2-$removestrength;
			}elseif($id==3){
				$currspread=$r3-$removestrength;
			}
			
			if($currspread < 0){
				$currspread=0;
			}
			
			if($sleepstate=="awake"){
				if($removedreligion==0){
					$spreadbonus=$spreadbonus+1;
					
					if($spreadbonus>=30){
						$result = $mysqli->query("SELECT religiontax, gold FROM religion WHERE name='$userreligion'") or die($mysqli->error());
						$row = mysqli_fetch_array($result);
						$religiontax=$row['religiontax'];
						$religiongold=$row['gold'];
						
						$result = $mysqli->query("SELECT gold FROM currency WHERE usercur='$username'") or die($mysqli->error());
						$row = mysqli_fetch_array($result);
						$gold=$row['gold'];
						
						if($userorder != NULL){
							$result = $mysqli->query("SELECT gold FROM religion WHERE id='$userorder'") or die($mysqli->error());
							$row = mysqli_fetch_array($result);
							$religiongold=$row['gold'];
						}
						
						$tax=(5*$religiontax)/100;
						$gain=5-$tax;
						$gold=$gold+5-$tax;
						$religiongold=$religiongold+$tax;
						
						$sql = "UPDATE currency SET gold ='$gold' WHERE usercur='$username'";
						mysqli_query($mysqli, $sql);
						
						if($userorder == NULL){
							$sql = "UPDATE religion SET gold ='$religiongold' WHERE name='$userreligion'";
							mysqli_query($mysqli, $sql);
						}else{
							$sql = "UPDATE religion SET gold ='$religiongold' WHERE id='$userorder'";
							mysqli_query($mysqli, $sql);
						}
						
						$sql = "UPDATE users SET spreadbonus ='0' WHERE username='$username'";
						mysqli_query($mysqli, $sql);
						
						$spreadbonus=0;
						
						$content= "You have spread religion 30 times and earned $gain and paid $tax to your religion";
						$sql = "INSERT INTO events (date, content, extrainfo) " 
					     . "VALUES (NOW(),'$content','$username')";
						mysqli_query($mysqli2, $sql);
					}
	
					$sql = "UPDATE users SET spreadbonus ='$spreadbonus', removedreligion='1' WHERE username='$username'";
					mysqli_query($mysqli, $sql);
					
					$sql = "UPDATE region SET `$religionid` ='$currspread' WHERE name='$location2'";
					mysqli_query($mysqli, $sql);
					
					echo'<div class="boxed">Done!</div>';
				}else{
					echo'<div class="boxed">Already spread religion today!</div>';
				}
			}else{
				echo'<div class="boxed">You need to be awake to perform this action!</div>';
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
	
	if(isset($_POST['nominateleaderform'])){
		?>
		<form method="post" action="">
			<input type="text" maxlength="50" name="nominee" placeholder="Nominee">
			<button type="submit" name="nominateleader" />Send</button>
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
	
	if(isset($_POST['nominateleader'])){
		$nominee = $mysqli->escape_string($_POST['nominee']);
		
		$result2 = $mysqli->query("SELECT id FROM users WHERE username='$nominee' AND userreligion='$userreligion'") or die($mysqli->error());
		$count = $result2->num_rows;
		
		if($count != 0){
			$sql = "UPDATE religion SET nominee ='$nominee' WHERE id='$userorder'";
			mysqli_query($mysqli, $sql);
			
			echo'<div class="boxed">Done!</div>';
		}else{
			echo'<div class="boxed">This user does not exist or does not follow the same religion as the order!</div>';
		}
		?>
		<script>
		    if ( window.history.replaceState ) {
		        window.history.replaceState( null, null, window.location.href );
		    }
		</script>
		<?php
	}
	
	if(isset($_POST['orderdonate'])){
		$amount = $mysqli->escape_string($_POST['amount']);
		
		$result = $mysqli->query("SELECT * FROM religion WHERE id='$userorder'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$ordergold=$row['gold'];
		$donatedgold=$row['donatedgold'];
		$orderreligionid=$row['religionid'];
		
		$result = $mysqli->query("SELECT * FROM religion WHERE id='$orderreligionid'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$religiongold=$row['gold'];
		
		if($amount >= 0){
			$ordergold = $ordergold - $amount;
			$donatedgold = $donatedgold + $amount;
			$religiongold = $religiongold + $amount;
			
			if($ordergold >=0){
				$sql = "UPDATE religion SET gold ='$ordergold', donatedgold ='$donatedgold' WHERE id='$userorder'";
				mysqli_query($mysqli, $sql);
				
				$sql = "UPDATE religion SET gold ='$religiongold' WHERE id='$orderreligionid'";
				mysqli_query($mysqli, $sql);
				
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
	
	if(isset($_POST['invite2'])){
		$recipient = $mysqli->escape_string($_POST['recipient']);
		$subject = "You have been invited to the order $ordername";
		$content = $mysqli->escape_string($_POST['content']);
		
		if(strlen($content) <= 5500){
			$result = $mysqli->query("SELECT username FROM users WHERE username='$recipient' AND userreligion='$orderreligionname'") or die($mysqli->error());
			if($result->num_rows > 0){
				$sql = "INSERT INTO messages (sender, recipient, date, subject, content, inviteid, invitetype) " 
			     . "VALUES ('$username','$recipient',NOW(),'$subject','$content', '$orderid', 'order')";
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
				echo'<div class="boxed">User does not exist or is not part of the same religion as the order!</div>';
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
	
}
?> </div> <?php

?>
</div>
</body>
<footer>
<?php require 'bottombar.php'; ?>
</footer>
</html>
