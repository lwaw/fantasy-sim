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
	$usercharacterid = $_SESSION['usercharacterid'];
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

if (isset($_GET["show"])) { $show  = $_GET["show"]; } else { $show=0; };
$show=$mysqli->escape_string($show);

$result = $mysqli->query("SELECT militaryunit, location FROM users WHERE username='$username'") or die($mysqli->error());
$row = mysqli_fetch_array($result);
$militaryunit=$row['militaryunit'];
$location=$row['location'];

$result = $mysqli->query("SELECT userreligion, location2, religionorder FROM users WHERE username='$username'") or die($mysqli->error());
$row = mysqli_fetch_array($result);
$userreligion=$row['userreligion'];
$userorder=$row['religionorder'];
$location2=$mysqli->escape_string($row['location2']);

$result = $mysqli->query("SELECT * FROM religion WHERE name='$userreligion'") or die($mysqli->error());
$row = mysqli_fetch_array($result);
$leader=$row['leader'];
$id=$row['id'];

?> <div class="everythingOnOneLine2"> <?php
	?> <div class="flexcontainer"> <?php
		if($userreligion != NULL AND $userreligion != "NULL"){ ?> <div class="h1"> <?php echo "$userreligion"; ?> </div> <?php }else{ ?> <div class="h1"> <?php echo "Religion"; ?> </div> <?php }
			?>
			<div class="notificationbox2">
				<div class="notificationbox3">
					<a href="rankings.php?type=religionmembers&country=<?php echo "$id"; ?>&sort=username&order=asc">
					<img src="img/membersicon.png">
					</a>
				</div>
			</div>
			<?php
		 
	?> </div> <?php
?> </div> <?php
?> <hr /> <?php

//join military unit
if($userreligion=="NULL" OR $userreligion==NULL){
	echo nl2br ("<div class=\"t1\">Currently you aren't in any religion. Join one to help it spread and help your religion spread out over the world.</div>");
	$sql = "SELECT id, name, leader FROM religion WHERE type='religion' ORDER BY 'id' ASC LIMIT $start_from, ".$results_per_page;
	$rs_result = $mysqli->query($sql);	
	//print_r($set);
	
	?> 
	<table id="table1">
		<tr>
	    <th> Name</th>
	    <th> Leader</th>
	    <th> Followers</th>
	    <th> Join</th>
	</tr>
	<?php
	while($row = $rs_result->fetch_assoc()) {
		//count number of members
		$name=$row["name"];
		$result2 = $mysqli->query("SELECT username FROM users WHERE userreligion='$name'") or die($mysqli->error());
		$count = $result2->num_rows;
		
		$leader = $row["leader"];
		
		$result = $mysqli->query("SELECT * FROM characters WHERE user='$leader' AND alive='1'") or die($mysqli->error());
		$row2 = mysqli_fetch_array($result);
		$leadercharacterid=$row2['id'];
		$leadercharactername=$row2['name'];
		$leadercharacterfamilyid=$row2['familyid'];
		
		$result = $mysqli->query("SELECT * FROM family WHERE id='$leadercharacterfamilyid'") or die($mysqli->error());
		$row2 = mysqli_fetch_array($result);
		$leadercharacterfamilyname=$row2['name'];
		
		if($leader != NULL AND $leader != "NULL"){
			$link="<a href='account.php?user=$leader&charid=$leadercharacterid'>$leadercharactername $leadercharacterfamilyname</a>";
		}else{
			$link="No leader";
		}
		?> 
	           <tr>
		           <td><?php echo $name; ?></td>
		           <td><?php echo $link; ?></td>
		           <td><?php echo $count; ?></td>
		           <td>
						<form method="post" action="">
							<input type="hidden" name="id" value="<?php echo $row["id"]; ?>" />
							<input type="hidden" name="name" value="<?php echo $row["name"]; ?>" />
							<input type="hidden" name="leader" value="<?php echo $row["owner"]; ?>" />
							<button type="submit" name="join" /><?php echo "Join religion"; ?></button>
						</form>
		           </td>
	           </tr>
		<?php		
	}; 
	?>
	</table>
	<?php
	$sql = "SELECT COUNT(id) AS total FROM religion";
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
	$id = (int) $id;
	
	$result = $mysqli->query("SELECT name FROM religion WHERE id='$id'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$name=$row['name'];
	
	$sql = "UPDATE users SET userreligion ='$name' WHERE username='$username'";
	mysqli_query($mysqli, $sql);
			
	echo'<div class="boxed">Joined!</div>';
	
	?>
	<script>
	    if ( window.history.replaceState ) {
	        window.history.replaceState( null, null, window.location.href );
	    }
	</script>
	<?php
	
	?>
	<script>
	    window.location = 'religion.php';
	</script>
	<?php
}

?> <div class="textbox"> <?php
//show you militry unit info
if($userreligion != "NULL" AND $userreligion != NULL){
	$result = $mysqli->query("SELECT * FROM religion WHERE name='$userreligion'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$name=$row['name'];
	$leader=$row['leader'];
	$message=$row['message'];
	$message = $purifier->purify($message);
	$religiontax=$row['religiontax'];
	$gold=$row['gold'];
	$crusade=$row['crusade'];
	
	//select leader
	$result2 = $mysqli->query("SELECT username FROM users WHERE userreligion='$name'") or die($mysqli->error());
	$count = $result2->num_rows;
	
	$leader = $row["leader"];
	
	$result = $mysqli->query("SELECT * FROM characters WHERE user='$leader' AND alive='1'") or die($mysqli->error());
	$row2 = mysqli_fetch_array($result);
	$leadercharacterid=$row2['id'];
	$leadercharactername=$row2['name'];
	$leadercharacterfamilyid=$row2['familyid'];
	
	$result = $mysqli->query("SELECT * FROM family WHERE id='$leadercharacterfamilyid'") or die($mysqli->error());
	$row2 = mysqli_fetch_array($result);
	$leadercharacterfamilyname=$row2['name'];
	
	$link="<a href='account.php?user=$leader&charid=$leadercharacterid'>$leadercharactername $leadercharacterfamilyname</a>";
	
	//select liegereligion
	$result = $mysqli->query("SELECT * FROM characters WHERE id='$usercharacterid'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$characterliegeid=$row['liege'];
	
	$result = $mysqli->query("SELECT * FROM characters WHERE id='$characterliegeid'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$characterliegeuser=$row['user'];
	
	$result = $mysqli->query("SELECT * FROM users WHERE username='$characterliegeuser'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$characterliegereligion=$row['userreligion'];
	
	$result2 = $mysqli->query("SELECT username FROM users WHERE userreligion='$userreligion'") or die($mysqli->error());
	$count = $result2->num_rows;

	if($leader != "NULL"){
		?>
		<form method="post" action="">
			<input type="hidden" name="name" value="<?php echo "$name"; ?>" />
			<input type="hidden" name="leader" value="<?php echo "$leader"; ?>" />
			<input type="hidden" name="id" value="<?php echo "$id"; ?>" />
			<?php if($userreligion != $characterliegereligion AND ($characterliegereligion != "NULL" AND $characterliegereligion != NULL)){ ?> <button type="submit" name="changereligionliege" >Change your religion</button> <?php } ?>
			<button type="submit" name="spreadform" />Spread religion in region</button>
			<button type="submit" name="expeditionform" />Launch expedition</button>
			<button type="submit" name="buyclaimform" />Buy a religious claim</button>
			<button type="submit" name="viewrelordersform" />View orders of this religion</button>
			<button type="submit" name="createorderform" />Found new order</button>
			<button type="submit" name="joinownorderform" />Join one of your orders</button>
			<?php if($userorder != "NULL" AND $userorder != NULL){ ?> <button type="submit" name="vieworderform" >View order</button> <?php } ?>
		</form>
		<?php
	}else{
		?>
		<form method="post" action="">
			<input type="hidden" name="name" value="<?php echo "$name"; ?>" />
			<input type="hidden" name="leader" value="<?php echo "$leader"; ?>" />
			<input type="hidden" name="id" value="<?php echo "$id"; ?>" />
			<?php if($userreligion != $characterliegereligion AND ($characterliegereligion != "NULL" AND $characterliegereligion != NULL)){ ?> <button type="submit" name="changereligionliege" >Change your religion</button> <?php } ?>
			<button type="submit" name="spreadform" />Spread religion in region</button>
			<button type="submit" name="expeditionform" />Launch expedition</button>
			<button type="submit" name="buyclaimform" />Buy a religious claim</button>
			<button type="submit" name="viewrelordersform" />View orders of this religion</button>
			<button type="submit" name="createorderform" />Found new order</button>
			<button type="submit" name="joinownorderform" />Join one of your orders</button>
			<?php if($userorder != "NULL" AND $userorder != NULL){ ?> <button type="submit" name="vieworderform" >View order</button> <?php } ?>
		</form>
		<?php
	}
	
	?> 
	<table id="table1">
		<tr>
		    <th> Name</th>
		    <th> Leader</th>
		    <th> Gold</th>
		    <th> Religion tax</th>
		    <th> Followers</th>
		    <?php if($crusade != "NULL"){?> <th> Crusade against </th> <?php }; ?>
		    <th> Orphanage</th>
		</tr>

		<tr>
	       <td><?php echo $name; ?></td>
	       <td><?php echo $link; ?></td>
	       <td><?php echo $gold; ?></td>
	       <td><?php echo $religiontax; ?></td>
	       <td><?php echo $count; ?></td>
	       <?php if($crusade != "NULL"){?> <td> <?php echo "$crusade"; ?> </td> <?php }; ?>
	       <?php
			$result4 = $mysqli->query("SELECT * FROM traits WHERE name='orphan'") or die($mysqli->error());
			$row4 = mysqli_fetch_array($result4);
			$traitid = $row4['id'];
	       
			$result3 = "SELECT * FROM traitscharacters WHERE traitid='$traitid' AND extrainfo = '$name'";
			$rs_result2 = $mysqli->query($result3);
			$count2 = $rs_result2->num_rows;//aantal titles
	        ?> <td><?php echo "<a href='rankings.php?type=orphanage&country=$name&sort=name&order=asc'>$count2</a>";?></td><?php
	        ?>
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
	<?php
}
?> </div> <?php

if($userreligion!="NULL" AND $userreligion!=NULL){
	
	?> <div class="textbox"> <?php
	
	//spread religion
	$result = $mysqli->query("SELECT curowner, archprelate, `1`, `2`, `3` FROM region WHERE name='$location2'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$curowner=$row['curowner'];
	$archprelate=$row['archprelate'];
	
	$r1=$row['1'];
	$r2=$row['2'];
	$r3=$row['3'];
	
	$archprelatecount = 0;
	if($archprelate != NULL OR $archprelate != 0){
		$result = $mysqli->query("SELECT * FROM characters WHERE id='$archprelate'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$archprelatename=$row['name'];
		$archprelatefamilyid=$row['familyid'];
		$archprelateuser=$row['user'];
		
		$result = $mysqli->query("SELECT * FROM family WHERE id='$archprelatefamilyid'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$archprelatefamilyname=$row['name'];
		
		$archprelatecount = 1;
	}
	
	$result = $mysqli->query("SELECT name FROM religion WHERE id='1'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$name1=$row['name'];
	
	$result = $mysqli->query("SELECT name FROM religion WHERE id='2'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$name2=$row['name'];
	
	$result = $mysqli->query("SELECT name FROM religion WHERE id='3'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$name3=$row['name'];
	
	?> <br /> <?php
	echo nl2br ("<div class=\"bold\">Faith of religions in $location2</div>");
	?><hr class="side"><?php
	?> 
	<table id="table1">
		<tr>
		    <th> <?php echo $name1; ?></th>
		    <th> <?php echo $name2; ?></th>
		    <th> <?php echo $name3; ?></th>
		    <th> <?php echo "archprelate"; ?></th>
		</tr>

		<tr>
	       <td><?php echo $r1; ?></td>
	       <td><?php echo $r2; ?></td>
	       <td><?php echo $r3; ?></td>
	       <td>
	       <?php 
	       if($archprelatecount == 1){
	       	echo "<a href='account.php?user=$archprelateuser&charid=$archprelate'>$archprelatename $archprelatefamilyname</a>";
	       }
	       ?>
	       </td>
		</tr>	
	</table>
	<?php
	
	?> <br /> <?php
	?><hr class="side"><?php
	
	if(isset($_POST['spreadform'])){
		echo'<div class="t1">Spread religion in your current region. Your base spread power is 1. This can be increased by using a book which adds 2 spread power. Your spread power can also be increased by owning artifacts, which will each grant bonus spread power. Additionally if your religion is equal to the national religion of the country that owns the region in which you are positioned, your spread power increases by 2.</div>';
		echo'<div class="t1">Spreading your religion can be done once a day and after 20 times of spreading your religion you will be awarded 5 gold minus the percentage religion tax which will go the the treasury of your religon.</div>';
		?>
		<form method="post" action="">
			<button type="submit" name="spread" /><?php echo "Spread religion"; ?></button>
			<select name="usebook" type="text" autofocus>
				<option value="no">don't use book</option>
		 		<option value="yes">use book</option>
		   	</select>
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
	
	if(isset($_POST['spread'])){
		$usebook = $mysqli->escape_string($_POST['usebook']);
		
		$result = $mysqli->query("SELECT curowner, `1`, `2`, `3` FROM region WHERE name='$location2'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$curowner=$row['curowner'];
		$r1=$row['1'];
		$r2=$row['2'];
		$r3=$row['3'];
		
		$result = $mysqli->query("SELECT statereligion FROM countryinfo WHERE country='$curowner'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$statereligion=$row['statereligion'];
		
		$result = $mysqli->query("SELECT energy, spread, spreadbonus FROM users WHERE username='$username'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$energy=$row['energy'];
		$spread=$row['spread'];
		$spreadbonus=$row['spreadbonus'];
		
		$result = $mysqli->query("SELECT spreadpower FROM relics WHERE owner='$username'") or die($mysqli->error());
		for ($set=array(); $row=$result->fetch_assoc(); $set[]=$row);
		
		$dorelicspread=0;
		foreach ($set as $key => $value) {
			$relicspread[$key] = $value['spreadpower'];
			$dorelicspread=$dorelicspread+$relicspread[$key];
		}
		//echo "$userreligion";
		//echo "$statereligion";
		if($spread==0){
			//$energy=$energy-5;
			if($sleepstate=="awake"){		
				if($leader==$username){
					$dospread=2;
				}else{
					$dospread=1;
				}
				
				if($usebook=='yes'){
					$result = $mysqli->query("SELECT book FROM inventory WHERE userinv='$username'") or die($mysqli->error());
					$row = mysqli_fetch_array($result);
					$book=$row['book'];
					
					$book=$book-1;
					
					if($book>=0){
						$dospread=$dospread+2;
						
						$sql = "UPDATE inventory SET book ='$book' WHERE userinv='$username'";
						mysqli_query($mysqli, $sql);
					}
				}
				
				if($statereligion==$userreligion){
					$dospread=$dospread+2;
				}
				
				//check traits
				$result3 = "SELECT * FROM traitscharacters WHERE characterid='$usercharacterid'";
				$rs_result2 = $mysqli->query($result3);
				$count2 = $rs_result2->num_rows;//aantal titles
				
				if($count2 != 0){
					while($row2 = $rs_result2->fetch_assoc()) {//ga door traits heen
						$traitid=$row2["traitid"];
						
						$result4 = $mysqli->query("SELECT * FROM traits WHERE id='$traitid'") or die($mysqli->error());
						$row4 = mysqli_fetch_array($result4);
						$traittype = $row4['type'];
						$traitamount = $row4['amount'];
						
						if($traittype == "religion"){
							$dospread = $dospread + $traitamount;
						}
					}
				}
				
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
					
					//add trait chance
					$result4 = $mysqli->query("SELECT * FROM traits WHERE name='pious'") or die($mysqli->error());
					$row4 = mysqli_fetch_array($result4);
					$traitid = $row4['id'];
					
					$result3 = "SELECT * FROM traitscharacters WHERE characterid='$usercharacterid' AND traitid = '$traitid'";
					$rs_result2 = $mysqli->query($result3);
					$count2 = $rs_result2->num_rows;//aantal titles
					
					if($count2 == 0){
						$randnumber = rand(0, 100);
						if($randnumber <= 1){
							$sql = "INSERT INTO traitscharacters (characterid, traitid, date) " 
							. "VALUES ('$usercharacterid','$traitid',NOW())";
					 		mysqli_query($mysqli, $sql);
							
							$content= "You gained the pious trait";
							$sql = "INSERT INTO events (date, content, extrainfo) " 
						     . "VALUES (NOW(),'$content','$username')";
							mysqli_query($mysqli2, $sql);
						}
					}
					
				}
				$result = $mysqli->query("SELECT id FROM religion WHERE name='$userreligion'") or die($mysqli->error());
				$row = mysqli_fetch_array($result);
				$id=$row['id'];
				
				$result = $mysqli->query("SELECT `$id` FROM region WHERE name='$location2'") or die($mysqli->error());
				$row = mysqli_fetch_array($result);
				$currspread=$row["$id"];
				
				$currspread=$currspread+$dospread+$dorelicspread;
				$totspread=$dospread+$dorelicspread;
				//echo'<div class="boxed">You spread $totspread religion!</div>';
				echo nl2br ("<div class=\"boxed\">You spread $totspread religion!</div>");
				
				//calculate alltime spread
				$result = $mysqli->query("SELECT totalspread FROM users WHERE username='$username'") or die($mysqli->error());
				$row = mysqli_fetch_array($result);
				$alltimespread=$row['totalspread'];
				$alltimespread=$alltimespread+$totspread;
	
				$sql = "UPDATE users SET spreadbonus ='$spreadbonus', energy='$energy', spread='1', totalspread='$alltimespread' WHERE username='$username'";
				mysqli_query($mysqli, $sql);
				
				$sql = "UPDATE region SET `$id` ='$currspread' WHERE name='$location2'";
				mysqli_query($mysqli, $sql);
			}else{
				//echo'<div class="boxed">Not enough energy!</div>';
				echo'<div class="boxed">You need to be awake to perform this action!</div>';
			}
		}else{
			echo'<div class="boxed">Already spread religion today!</div>';
		} 
		
		?>
		<script>
		    if ( window.history.replaceState ) {
		        window.history.replaceState( null, null, window.location.href );
		    }
		</script>
		<?php
	}
	
	//launch expedition
	if(isset($_POST['expeditionform'])){
		echo nl2br ("<div class=\"t1\">Launching a expedition in a region gives you a chance of finding a available artifact that is hidden in that region. Launching an expedition costs 1 gold.</div>");
		?>
		<form method="post" action="">
			<button type="submit" name="expedition" /><?php echo "Launch expedition"; ?></button>
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
	
	if(isset($_POST['expedition'])){
		$result = $mysqli->query("SELECT expedition, energy FROM users WHERE username='$username'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$expedition=$row['expedition'];
		$energy=$row['energy'];
		
		if($expedition==0){
			$result = $mysqli->query("SELECT gold FROM currency WHERE usercur='$username'") or die($mysqli->error());
			$row = mysqli_fetch_array($result);
			$gold=$row['gold'];
			
			$gold=$gold-1;
			
			if($gold>=0){
				$result = $mysqli->query("SELECT id FROM relics WHERE owner='NULL'") or die($mysqli->error());
				$row = mysqli_fetch_array($result);
				$freerelics=$row['id'];
				
				$energy=$energy-10;
				if($energy>=0){
				
					if($result->num_rows > 0){
						$win = rand(1, 5);
						if($win==1){
							$sql = "UPDATE currency SET gold ='$gold' WHERE usercur='$username'";
							mysqli_query($mysqli, $sql);
							
							$sql = "UPDATE relics SET owner ='$username' WHERE id='$freerelics'";
							mysqli_query($mysqli, $sql);
							
							$sql = "UPDATE users SET expedition ='1', energy='$energy' WHERE username='$username'";
							mysqli_query($mysqli, $sql);
						}else{
							echo'<div class="boxed">You didn\'t find a relic!!</div>';
							
							$sql = "UPDATE users SET expedition ='1', energy='$energy' WHERE username='$username'";
							mysqli_query($mysqli, $sql);
						}
		
					}else{
					echo'<div class="boxed">You didn\'t find a relic!!</div>';	
						
					$sql = "UPDATE users SET expedition ='1', energy='$energy' WHERE username='$username'";
					mysqli_query($mysqli, $sql);
					}
				}else{
					echo'<div class="boxed">You don\'t have enough energy!</div>';	
				}
			}else{
				echo'<div class="boxed">You don\'t have enough gold!</div>';	
			}
		}else{
			echo'<div class="boxed">You already went on expedition today!</div>';	
		}
		
		?>
		<script>
		    if ( window.history.replaceState ) {
		        window.history.replaceState( null, null, window.location.href );
		    }
		</script>
		<?php
	}
	
	if(isset($_POST['buyclaimform'])){
		echo nl2br ("<div class=\"t1\">You can buy a claim on a title that does not follow the same religion. This will you the cost you the religious tax in gold. Note that you need to own or be a member of a military unit that has a camp in this holding to press the claim.</div>");
		
		$result = $mysqli->query("SELECT * FROM region WHERE biggestrel!='$userreligion' AND characterowner!='$usercharacterid' AND curowner='$crusade'") or die($mysqli->error());
		$columnValues = Array();
		?>
		<form method="post" action="">
		    <select required name="claimregionid" type="text">
		    <option value="" disabled selected hidden>Which title do you want to claim?</option> 
		    <?php       
		    // Iterating through the product array
			while ( $row = mysqli_fetch_assoc($result) ) {
			    ?>
			    <option value="<?php echo strtolower($row['id']); ?>"><?php echo $row['name']; ?></option>
			    <?php
			}
		    ?>
		    </select> 
		    <button type="submit" name="buyclaimform2" />Buy claim</button>
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

	if(isset($_POST['buyclaimform2'])){
		$claimregionid = $mysqli->escape_string($_POST['claimregionid']);
		
		$result = $mysqli->query("SELECT * FROM region WHERE id='$claimregionid'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$regionname=$row['name'];
		$regionname2=$mysqli->escape_string($regionname);
		$curowner=$row['curowner'];
		
		$result = $mysqli->query("SELECT * FROM titles WHERE holdingid='$claimregionid' AND holdingtype='duchy'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$titleid=$row['id'];
		$titleholderid=$row['holderid'];
		
		$result = $mysqli->query("SELECT * FROM characters WHERE id='$titleholderid'") or die($mysqli->error());
		$row2 = mysqli_fetch_array($result);
		$holdername=$row2['name'];
		$holderfamilyid=$row2['familyid'];
		$holderuser=$row2['user'];
		$holderid=$row2['id'];
		$holderliege=$row2['liege'];
		
		$result = $mysqli->query("SELECT * FROM family WHERE id='$holderfamilyid'") or die($mysqli->error());
		$row2 = mysqli_fetch_array($result);
		$holderfamilyname=$row2['name'];
		
		$result = $mysqli->query("SELECT * FROM users WHERE username='$holderuser'") or die($mysqli->error());
		$row2 = mysqli_fetch_array($result);
		$holderreligion=$row2['userreligion'];
		
		$result = $mysqli->query("SELECT * FROM characters WHERE id='$usercharacterid'") or die($mysqli->error());
		$row2 = mysqli_fetch_array($result);
		$charactername=$row2['name'];
		$characterfamilyid=$row2['familyid'];
		
		$result = $mysqli->query("SELECT * FROM family WHERE id='$characterfamilyid'") or die($mysqli->error());
		$row2 = mysqli_fetch_array($result);
		$characterfamilyname=$row2['name'];
		
		$result = $mysqli->query("SELECT * FROM users WHERE username='$username'") or die($mysqli->error());
		$row2 = mysqli_fetch_array($result);
		$characternationality=$row2['nationality'];
		
		$result = $mysqli->query("SELECT * FROM religion WHERE name='$userreligion'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$religiontax=$row['religiontax'];
		$religiongold=$row['gold'];
		
		$result = $mysqli->query("SELECT * FROM currency WHERE usercur='$username'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$usergold=$row['gold'];
		
		$usergold = $usergold - $religiontax;
		$religiongold = $religiongold + $religiontax;
		if($holderid != $usercharacterid AND $crusade == strtolower($curowner)){
			if($userreligion != $holderreligion){
				if($usergold >= 0){
					
					$sql = "INSERT INTO claim (type, inheritable, charowner, title, date, countryowner) " 
					. "VALUES ('religionclaim','0','$usercharacterid','$titleid',NOW(),'$characternationality')";
			 		mysqli_query($mysqli, $sql);
					$lastid = $mysqli->insert_id;
					
					$sql = "UPDATE currency SET gold='$usergold' WHERE usercur='$username'";
					mysqli_query($mysqli, $sql);
					
					$sql = "UPDATE religion SET gold='$religiongold' WHERE name='$userreligion'";
					mysqli_query($mysqli, $sql);
					
					$content= "You gained a religious claim on the duchy of $regionname which is owned by <a href='account.php?user=$holderuser&charid=$holderid'>$holdername $holderfamilyname</a>";
					$content=$mysqli->escape_string($content);
					$sql = "INSERT INTO events (date, content, extrainfo) " 
				     . "VALUES (NOW(),'$content','$username')";
					mysqli_query($mysqli2, $sql);
					
					$content= "<a href='account.php?user=$username&charid=$usercharacterid'>$charactername $characterfamilyname</a> gained a religious claim on the duchy of $regionname";
					$content=$mysqli->escape_string($content);
					$sql = "INSERT INTO events (date, content, extrainfo) " 
				     . "VALUES (NOW(),'$content','$holderuser')";
					mysqli_query($mysqli2, $sql);
					
					echo'<div class="boxed">Done!</div>';
				}else{
					echo'<div class="boxed">You do not have enough gold to buy this claim!</div>';
				}
			}else{
					echo'<div class="boxed">The holder of this title has the same religion as you!</div>';
			}
		}else{
			echo'<div class="boxed">You own this duchy!</div>';
		}
		?>
		<script>
		    if ( window.history.replaceState ) {
		        window.history.replaceState( null, null, window.location.href );
		    }
		</script>
		<?php
	}
	
	if(isset($_POST['changereligionliege'])){
		echo nl2br ("<div class=\"t1\">Your liege follows a different religion than you. This makes it possible to change your religion
		to the same religion as your liege.</div>");
		?>
		<form method="post" action="" autofocus>
			<button type="submit" name="changereligionliege2" /><?php echo "Change religion"; ?></button>
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
	
	if(isset($_POST['changereligionliege2'])){
		//select liegereligion
		$result = $mysqli->query("SELECT * FROM characters WHERE id='$usercharacterid'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$characterliegeid=$row['liege'];
		
		$result = $mysqli->query("SELECT * FROM characters WHERE id='$characterliegeid'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$characterliegeuser=$row['user'];
		
		$result = $mysqli->query("SELECT * FROM users WHERE username='$characterliegeuser'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$characterliegereligion=$row['userreligion'];

		$sql = "UPDATE users SET userreligion='$characterliegereligion' WHERE username='$username'";
		mysqli_query($mysqli, $sql);
		
		?>
		<script>
		    window.location = 'religion.php';
		</script>
		<?php
	}
	
	if(isset($_POST['createorderform'])){
		echo nl2br ("<div class=\"t1\">Orders operate within religions and can propose candidates for the next religious leader. Creating an order costs 5 gold.</div>");
		?>
		<form method="post" action="">
			<input type="hidden" name="secretorder" value="false" />
			<input type="checkbox" name="secretorder" value="true"> Create secret order<br>
			<input type="text" pattern="[a-zA-Z0-9]+[a-zA-Z0-9 ]+" size="25" required autocomplete="off" placeholder="Enter order name here" maxlength="30" name='name'/>
			<button type="submit" name="createorder" /><?php echo "Create order"; ?></button>
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
	
	if(isset($_POST['createorder'])){
		$name = $mysqli->escape_string($_POST['name']);
		$namesafe = str_replace(' ', '', $name);
		$namelowercase = strtolower($name);
		$namelowercase = trim($namelowercase, " ");
		
		$secretorder = $mysqli->escape_string($_POST['secretorder']);
		
		//check if name is unique
		$result2 = $mysqli->query("SELECT id FROM religion WHERE name='$namelowercase'") or die($mysqli->error());
		$count = $result2->num_rows;
		
		if(strlen($name) <= 30 AND strlen($name) >= 1 AND ctype_alnum($namesafe)){
			if($count == 0){
				$result = $mysqli->query("SELECT userreligion FROM users WHERE username='$username'") or die($mysqli->error());
				$row = mysqli_fetch_array($result);
				$userreligion=$row['userreligion'];
				
				$result = $mysqli->query("SELECT gold FROM currency WHERE usercur='$username'") or die($mysqli->error());
				$row = mysqli_fetch_array($result);
				$gold=$row['gold'];
				
				$result = $mysqli->query("SELECT id FROM religion WHERE type='religion' AND name='$userreligion'") or die($mysqli->error());
				$row = mysqli_fetch_array($result);
				$religionid=$row['id'];
				
				$gold = $gold-5;
				if($gold >= 0){				
					if($secretorder == "true"){
						$type = "secretorder";
					}else{
						$type = "order";
					}
					
					$sql = "UPDATE currency SET gold='$gold' WHERE usercur='$username'";
					mysqli_query($mysqli, $sql);
					
					$sql = "INSERT INTO religion (name, type, religionid, leader, owner) " 
			            . "VALUES ('$name','$type', '$religionid', '$username', '$username')";
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
				echo'<div class="boxed">This name is not unique!</div>';
				
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

	if(isset($_POST['joinownorderform'])){
		$result = $mysqli->query("SELECT id, leader FROM religion WHERE name='$userreligion'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$id=$row['id'];
		
		$result = $mysqli->query("SELECT id FROM religion WHERE (type='order' OR type='secretorder') AND owner='$username' AND religionid='$id'") or die($mysqli->error());
		$columnValues = Array();
		while ( $row = mysqli_fetch_assoc($result) ) {
		  $columnValues[] = $row['id'];
		}
		
		?>
		<form method="post" action="">
		    <select name="ownorder" type="text">
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
		    <button type="submit" name="joinownorder" />Join order</button>
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
	
	if(isset($_POST['joinownorder'])){
		$ownorder = $mysqli->escape_string($_POST['ownorder']);
		
		$result = $mysqli->query("SELECT id, leader FROM religion WHERE name='$userreligion'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$id=$row['id'];
		
		$result = $mysqli->query("SELECT * FROM religion WHERE id='$ownorder'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$owner=$row['owner'];
		$religionid=$row['religionid'];
		$type=$row['type'];
		
		if($id==$religionid){
			if($owner==$username){
				$sql = "UPDATE users SET religionorder='$ownorder' WHERE username='$username'";
				mysqli_query($mysqli, $sql);
				
				echo'<div class="boxed">Done!</div>';
			}
		}else{
			echo'<div class="boxed">Your must have the same religion as the religion your order belongs to!</div>';
		}
		?>
		<script>
		    if ( window.history.replaceState ) {
		        window.history.replaceState( null, null, window.location.href );
		    }
		</script>
		<?php
	}

	if(isset($_POST['viewrelordersform'])){
		$result = $mysqli->query("SELECT id FROM religion WHERE name='$userreligion'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$religionid=$row['id'];
		
		?>
		<script>
			var val = "<?php echo $religionid ?>"
		    window.location = 'rankings.php?type=vieworder&sort=name&order=asc&country='+val;
		</script>
		<?php
	}
	
	//view order
	if(isset($_POST['vieworderform'])){
		$result = $mysqli->query("SELECT religionorder FROM users WHERE username='$username'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$religionorder=$row['religionorder'];
		
		?>
		<script>
			var val = "<?php echo $religionorder ?>"
		    window.location = 'order.php?order='+val;
		</script>
		<?php
	}
	
	echo nl2br("<div class=\"t1\"><a href='rankings.php?type=relics&sort=name&order=asc'>View available relics</a></div>");
	
	?> <br /> <?php
	?>
	<form method="post" action="">
		<button type="submit" name="followers" />Followers</button>
		<button type="submit" name="treasury" />Treasury</button>
	</form>
	<?php
	
	//view followers en treasury
	if(isset($_POST['followers'])){	
		?>
		<script>
			var val = "<?php echo $religionorder ?>"
		    window.location = 'religion.php?show=1';
		</script>
		<?php
	}
	if(isset($_POST['treasury'])){	
		?>
		<script>
			var val = "<?php echo $religionorder ?>"
		    window.location = 'religion.php?show=2';
		</script>
		<?php
	}
	
	if($show==1){
		//print grafiek met followers statisitcs
		$datearray=array();
		$waardearray=array();
		
		$result2 = $mysqli2->query("SELECT * FROM statistics WHERE type='religionfollowers' AND name='$userreligion'") or die($mysqli->error());
		while($row2=mysqli_fetch_array($result2)) {
			$datestat=$row2["datestat"];
			$waarde=$row2["waarde"];
			
			$datearray[]=$datestat;
			$waardearray[]=$waarde;
		}
		
		//print json_encode($datearray);
		$datearray2=json_encode($datearray);
		$waardearray2=json_encode($waardearray);
		?>
		
		<canvas id="bar-chart" width="800" height="450"></canvas>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.3.0/Chart.min.js"></script>
		
		<script>
			new Chart(document.getElementById("bar-chart"), {
			    type: 'line',
			    data: {
			      labels: <?php echo $datearray2 ?>,
			      datasets: [
			        {
			          label: "Followers",
			          borderColor: "#3e95cd",
			          data: <?php echo $waardearray2 ?>
			        }
			      ]
			    },
			    options: {
			      legend: { display: false },
			      title: {
			        display: true,
			        text: 'Followers'
			      },
			      scales: {
			        yAxes: [{
			        	ticks: {
			            	beginAtZero: true
			          }
			        }]
			      }
			    }
			});
		</script>
		<?php
	}elseif($show==2){
		//print grafiek met treasury
		$datearray=array();
		$waardearray=array();
		
		$result2 = $mysqli2->query("SELECT * FROM statistics WHERE type='religiongold' AND name='$userreligion'") or die($mysqli->error());
		while($row2=mysqli_fetch_array($result2)) {
			$datestat=$row2["datestat"];
			$waarde=$row2["waarde"];
			
			$datearray[]=$datestat;
			$waardearray[]=$waarde;
		}
		
		//print json_encode($datearray);
		$datearray2=json_encode($datearray);
		$waardearray2=json_encode($waardearray);
		?>
		
		<canvas id="bar-chart" width="800" height="450"></canvas>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.3.0/Chart.min.js"></script>
		
		<script>
			new Chart(document.getElementById("bar-chart"), {
			    type: 'bar',
			    data: {
			      labels: <?php echo $datearray2 ?>,
			      datasets: [
			        {
			          label: "Gold",
			          backgroundColor: "#3e95cd",
			          data: <?php echo $waardearray2 ?>
			        }
			      ]
			    },
			    options: {
			      legend: { display: false },
			      title: {
			        display: true,
			        text: 'Gold in treasury'
			      },
			      scales: {
			        yAxes: [{
			        	ticks: {
			            	beginAtZero: true
			          }
			        }]
			      }
			    }
			});
		</script>
		<?php
	}
	?> </div> <?php
	/*
	if($religionorder != 0){
		$result2 = $mysqli->query("SELECT id FROM religion WHERE owner='$username'") or die($mysqli->error());
		$count = $result2->num_rows;
		
		$result = $mysqli->query("SELECT * FROM religion WHERE id='$userorder'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$orderid=$row['id'];
		$ordername=$row['name'];
		$orderleader=$row['leader'];
		$orderreligionid=$row['religionid'];
		
		$result = $mysqli->query("SELECT * FROM religion WHERE id='$orderreligionid'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$orderreligionname=$row['name'];
		
		if($religionorder==$userorder){
			?> <div class="h1"> <?php echo "$ordername"; ?> </div> <?php
			?>
			<form method="post" action="">
				<button type="submit" name="messageform" />Set new message</button>
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
		
	}*/

}

?> <div class="textbox"> <?php
if($leader==$username){
	echo nl2br ("<div class=\"h1\">Leader settings</div>");
	?> <hr /> <?php

	?>
	<form method="post" action="">
		<button type="submit" name="messageform" />Set new message</button>
		<button type="submit" name="taxform" />Set new religion tax</button>
		<button type="submit" name="heresyform" />Combat heresy</button>
		<button type="submit" name="crusadeform" />View crusade</button>
		<button type="submit" name="archprelateform" />Appoint archprelate</button>
	</form>
	<?php
	
	//new archprelate
	if(isset($_POST['archprelateform'])){
		$result3 = "SELECT * FROM region WHERE (archprelate IS NULL OR archprelate=NULL OR archprelate='0') AND biggestrel = '$name'";
		$rs_result2 = $mysqli->query($result3);
		$count2 = $rs_result2->num_rows;//aantal titles
		
		if($count2 != 0){
			echo nl2br ("<div class=\"t1\">This action will appoint an archprelate to a region.</div>");
			
			$result4 = $mysqli->query("SELECT * FROM traits WHERE name='eunuch'") or die($mysqli->error());
			$row4 = mysqli_fetch_array($result4);
			$traiteunuchid = $row4['id'];
			
			$result4 = $mysqli->query("SELECT * FROM traits WHERE name='archprelate'") or die($mysqli->error());
			$row4 = mysqli_fetch_array($result4);
			$traitarchprelateid = $row4['id'];
			
			//check for eunuch
			$result3 = "SELECT * FROM traitscharacters WHERE traitid = '$traiteunuchid' AND extrainfo = '$name'";
			$rs_result2 = $mysqli->query($result3);
			$count2 = $rs_result2->num_rows;//aantal titles
			
			$candidatelist = array();
			if($count2 != 0){
			
				while($row2 = $rs_result2->fetch_assoc()) {//ga door traits heen
					$characterid=$row2["characterid"];
					
					//check if already archprelate
					$result4 = "SELECT * FROM traitscharacters WHERE traitid = '$traitarchprelateid' AND characterid = '$characterid'";
					$rs_result3 = $mysqli->query($result4);
					$count3 = $rs_result3->num_rows;//aantal titles
					
					if($count3 == 0){
						//check for liege
						$result4 = $mysqli->query("SELECT * FROM characters WHERE id='$characterid'") or die($mysqli->error());
						$row4 = mysqli_fetch_array($result4);
						$characterliege = $row4['liege'];
						//echo "test2 , $characterid";
						if($characterliege == $usercharacterid){
							array_push($candidatelist, $characterid);
						}
					}
				}
			}
			
			
			$result = $mysqli->query("SELECT * FROM region WHERE (archprelate IS NULL OR archprelate=NULL OR archprelate='0') AND biggestrel = '$name'") or die($mysqli->error());
			$columnValues = Array();
			?>
			<form method="post" action="">
				<select required name="regionid" type="text">
			    <option value="" disabled selected hidden>Select a region?</option> 
			    <?php       
			    // Iterating through the product array
				while ( $row = mysqli_fetch_assoc($result) ) {
				    ?>
				    <option value="<?php echo strtolower($row['id']); ?>"><?php echo $row['name']; ?></option>
				    <?php
				}
			    ?>
			    </select> 
			    <select required name="characterid" type="text">
			    <option value="" disabled selected hidden>Which eunuch do you want to appoint?</option> 
			    <?php       
			    // Iterating through the product array
				foreach ($candidatelist as $key) {
					$result4 = $mysqli->query("SELECT * FROM characters WHERE id='$key'") or die($mysqli->error());
					$row4 = mysqli_fetch_array($result4);
					$charactername = $row4['name'];
				    ?>
				    <option value="<?php echo strtolower($key); ?>"><?php echo $charactername; ?></option>
				    <?php
				}
			    ?>
			    </select> 
			    <button type="submit" name="archprelateform2" />Appoint</button>
			</form>
			<?php
			
		}else{
			echo'<div class="boxed">All regions in which your religion is the biggest have an active archprelate!</div>';
		}
		
		?>
		<script>
		    if ( window.history.replaceState ) {
		        window.history.replaceState( null, null, window.location.href );
		    }
		</script>
		<?php
	}

	if(isset($_POST['archprelateform2'])){
		$regionselectid= $mysqli->escape_string($_POST['regionid']);
		$characterid= $mysqli->escape_string($_POST['characterid']);
		
		$result4 = $mysqli->query("SELECT * FROM characters WHERE id='$characterid'") or die($mysqli->error());
		$row4 = mysqli_fetch_array($result4);
		$characterliege = $row4['liege'];
		$characteruser = $row4['user'];
		
		$result4 = $mysqli->query("SELECT * FROM traits WHERE name='eunuch'") or die($mysqli->error());
		$row4 = mysqli_fetch_array($result4);
		$traiteunuchid = $row4['id'];
		
		$result4 = $mysqli->query("SELECT * FROM traits WHERE name='archprelate'") or die($mysqli->error());
		$row4 = mysqli_fetch_array($result4);
		$traitarchprelateid = $row4['id'];
		
		$result4 = "SELECT * FROM region WHERE id = $regionselectid AND (archprelate IS NULL OR archprelate=NULL OR archprelate='0') AND biggestrel = '$name'";
		$rs_result3 = $mysqli->query($result4);
		$count3 = $rs_result3->num_rows;//aantal titles
		
		if($count3 != 0){
			$result4 = "SELECT * FROM traitscharacters WHERE characterid = $characterid AND traitid = '$traiteunuchid' AND extrainfo = '$name'";
			$rs_result3 = $mysqli->query($result4);
			$count3 = $rs_result3->num_rows;//aantal titles
			
			if($count3 != 0){//wel eunuch en geen archprelate
				$result4 = "SELECT * FROM traitscharacters WHERE characterid = $characterid AND traitid = '$traitarchprelateid'";
				$rs_result3 = $mysqli->query($result4);
				$count3 = $rs_result3->num_rows;//aantal titles
				
				if($count3 == 0){//wel eunuch en geen archprelate
					if($characterliege == $usercharacterid){//check if in your liege
						$sql = "UPDATE region SET archprelate='$characterid' WHERE id='$regionselectid'";
						mysqli_query($mysqli, $sql);
					
						$sql = "INSERT INTO traitscharacters (characterid, traitid, date, extrainfo) " 
						. "VALUES ('$characterid','$traitarchprelateid',NOW(),'$name')";
				 		mysqli_query($mysqli, $sql);
						
						$content= "You have been appointed as archprelate by your religious leader.";
						$sql = "INSERT INTO events (date, content, extrainfo) " 
					     . "VALUES (NOW(),'$content','$characteruser')";
						mysqli_query($mysqli2, $sql);
						
						echo'<div class="boxed">Done!</div>';
					}
				}
			}
		}
	}
	
	//new message
	if(isset($_POST['messageform'])){
		?>
		<form method="post" action="">
			<input type="hidden" name="id" value="<?php echo "$id "; ?>" />
			<textarea rows="4" cols="50" name="message" maxlength="500" placeholder="Enter text here..."></textarea>
			<button type="submit" name="newmessage" /><?php echo "Set new message"; ?></button>
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
	
	//change religeous tax max 100
	if(isset($_POST['taxform'])){
		echo nl2br ("<div class=\"t1\">Religion tax is paid when a country sets a national religion in gold and as apercentage of the bonus users get from spreading religion. The maximal tax is 100%.</div>");
		?>
		<form method="post" action="">
			<input type="number" size="25" required autocomplete="off" id="changetax" name="changetax" min="1" max="100" step="1" />
			<button type="submit" name="tax"  /><?php echo "Change religion tax"; ?></button>
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
	
	if(isset($_POST['tax'])){
		$changetax = $mysqli->escape_string($_POST['changetax']);
		$changetax = (int) $changetax;
		$result = $mysqli->query("SELECT changedtax FROM religion WHERE id='$id'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$changedtax=$row['changedtax'];
		
		if($changedtax==0 && $changetax>=1 && $changetax<=100){
			$sql = "UPDATE religion SET religiontax ='$changetax', changedtax='1' WHERE id='$id'";
			mysqli_query($mysqli, $sql);			
		}else{
			echo "You already changed the tax during your term!";
		}
		
		?>
		<script>
		    if ( window.history.replaceState ) {
		        window.history.replaceState( null, null, window.location.href );
		    }
		</script>
		<?php
	}
	
	//combat heresy
	if(isset($_POST['heresyform'])){
		echo nl2br ("<div class=\"t1\">Combat heresy from a region. This will remove all other faith out of the region and costs 50 gold.</div>");
		?>
		<br><br>
		<form method="post" action=""> 
			<?php
			$result = mysqli_query($mysqli,"SELECT country FROM countryinfo");
			$columnValues = Array();
			
			while ( $row = mysqli_fetch_assoc($result) ) {
			  $columnValues[] = $row['country'];
			}
			// Sort the data with wek ascending order, add $mar as the last parameter, to sort by the common key
			asort($columnValues);
			?>
			<select required name="searchcoun" type="text">
				<?php	        
				// Iterating through the product array
				foreach($columnValues as $item){
					?>
				 	<option value="<?php echo strtolower($item); ?>"><?php echo $item; ?></option>
				    <?php
			    }
			    ?>
			</select> 
			<button type="submit" name="countryselect" />Select country</button>
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
	
	if(isset($_POST['countryselect'])){
		$searchcoun = $mysqli->escape_string($_POST['searchcoun']);
		
		$result2 = $mysqli->query("SELECT name FROM region WHERE curowner='$searchcoun'") or die($mysqli->error());
		$count = $result2->num_rows;
		
		if($count>0){
			?>
			<br><br>
			<form method="post" action=""> 
				<input type="hidden" name="searchcoun" value="<?php echo "$searchcoun"; ?>" />
				<label for="regionsearch">Select region to combat heresy:</label>
				<?php
				$result = mysqli_query($mysqli,"SELECT name FROM region WHERE curowner='$searchcoun'");
				$columnValues = Array();
				
				while ( $row = mysqli_fetch_assoc($result) ) {
				  $columnValues[] = $row['name'];
				}
				// Sort the data with wek ascending order, add $mar as the last parameter, to sort by the common key
				asort($columnValues);
				?>
				<select required name="regionsearch" type="text">
					<?php	        
					// Iterating through the product array
					foreach($columnValues as $item){
						?>
					 	<option value="<?php echo strtolower($item); ?>"><?php echo $item; ?></option>
					    <?php
				    }
				    ?>
				</select> 
				<button type="submit" name="regionselect" />Select region</button>
			</form>
			<?php
		}else{
			echo'<div class="boxed">This country doesn\'t control have any regions!</div>';
		}
		
		?>
		<script>
		    if ( window.history.replaceState ) {
		        window.history.replaceState( null, null, window.location.href );
		    }
		</script>
		<?php
	}

	if(isset($_POST['regionselect'])){
		$regionsearch = $mysqli->escape_string($_POST['regionsearch']);
		
		$result = $mysqli->query("SELECT id, gold FROM religion WHERE name='$userreligion' AND leader='$username'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$id=$row['id'];
		$gold=$row['gold'];
		
		$gold=$gold-50;
		if($gold >= 0){
			if($id==1){
				$sql = "UPDATE region SET `2` ='0',`3` ='0' WHERE name='$regionsearch'";
				mysqli_query($mysqli, $sql);
			}elseif($id==2){
				$sql = "UPDATE region SET `1` ='0',`3` ='0' WHERE name='$regionsearch'";
				mysqli_query($mysqli, $sql);				
			}elseif($id==3){
				$sql = "UPDATE region SET `1` ='0',`2` ='0' WHERE name='$regionsearch'";
				mysqli_query($mysqli, $sql);				
			}
		$sql = "UPDATE religion SET gold ='$gold' WHERE id='$id'";
		mysqli_query($mysqli, $sql);
		
		echo'<div class="boxed">Done!</div>';
		}else{
			echo'<div class="boxed">Religion doesn\'t have enough gold!</div>';
		}
		
		?>
		<script>
		    if ( window.history.replaceState ) {
		        window.history.replaceState( null, null, window.location.href );
		    }
		</script>
		<?php
	}
	
	
	//start crusade
	if(isset($_POST['crusadeform'])){
		if($crusade=="NULL" || $crusade==NULL){
			echo nl2br ("<div class=\"t1\">Crusades will let a country that follows this religion attack enemies of the faith wether they have a NAP.</div>");
			?>
			<br><br>
			<form method="post" action=""> 
				<label for="searchcoun">Start crusade against:</label>
				<?php
				$result = mysqli_query($mysqli,"SELECT country FROM countryinfo");
				$columnValues = Array();
				
				while ( $row = mysqli_fetch_assoc($result) ) {
				  $columnValues[] = $row['country'];
				}
				// Sort the data with wek ascending order, add $mar as the last parameter, to sort by the common key
				asort($columnValues);
				?>
				<select required name="searchcoun" type="text">
					<?php	        
					// Iterating through the product array
					foreach($columnValues as $item){
						?>
					 	<option value="<?php echo strtolower($item); ?>"><?php echo $item; ?></option>
					    <?php
				    }
				    ?>
				</select> 
				<button type="submit" name="startcrusade" />Select country</button>
			</form>
			<?php
		}else{
			?>
			<form>
				<button type="submit" name="endcrusade" />End crusade</button>
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
	
	if(isset($_POST['startcrusade'])){
		$crusadecountry = $mysqli->escape_string($_POST['searchcoun']);
		
		$result2 = $mysqli->query("SELECT country FROM countryinfo WHERE country='$crusadecountry'") or die($mysqli->error());
		$count = $result2->num_rows;
				
		$result = $mysqli->query("SELECT crusadeup, id FROM religion WHERE name='$userreligion' AND leader='$username'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$crusadeup=$row['crusadeup'];
		$id=$row['id'];
		
		if($crusadeup==0){
			if($count>0){
				$sql = "UPDATE religion SET crusade ='$crusadecountry', crusadeup='1' WHERE id='$id'";
				mysqli_query($mysqli, $sql);
				
				$content= "$userreligion proclaimed a crusade on $crusadecountry";
				$sql = "INSERT INTO events (date, content) " 
			     . "VALUES (NOW(),'$content')";
				mysqli_query($mysqli2, $sql);
				
				echo nl2br ("<div class=\"boxed\">Started crusade against $crusadecountry!</div>");
			}
		}else{
			echo'<div class="boxed">Religion has already started a crusade this month!</div>';
			
		}
		
		?>
		<script>
		    if ( window.history.replaceState ) {
		        window.history.replaceState( null, null, window.location.href );
		    }
		</script>
		<?php
	} 
	
	if(isset($_POST['endcrusade'])){
		$result = $mysqli->query("SELECT crusadeup, id FROM religion WHERE name='$userreligion' AND leader='$username'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$crusadeup=$row['crusadeup'];
		$id=$row['id'];
		
		$sql = "UPDATE religion SET crusade ='NULL' WHERE id='$id'";
		mysqli_query($mysqli, $sql);
		
		$content= "$userreligion ended the crusade on $crusadecountry";
		$sql = "INSERT INTO events (date, content) " 
	     . "VALUES (NOW(),'$content')";
		mysqli_query($mysqli2, $sql);
		
		echo'<div class="boxed">Ended crusade!</div>';	
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
	$message = $mysqli->escape_string($_POST['message']);
	$length = strlen($message);
	
	if($length<=500){
		$result = $mysqli->query("SELECT id FROM religion WHERE name='$userreligion' AND leader='$username'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$id=$row['id'];
		
		$sql = "UPDATE religion SET message ='$message' WHERE id='$id'";
		mysqli_query($mysqli, $sql);
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

?>
</div>
</body>
<footer>
<?php require 'bottombar.php'; ?>
</footer>
</html>
