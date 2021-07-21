<?php 
require 'navigationbar.php';
require 'db.php';
require 'functions.php';
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

$typeprep=$_GET["type"];
$type=$mysqli->escape_string($typeprep);

if (isset($_GET["country"])) { $country  = $_GET["country"]; } else { $country="None"; };
$country=$mysqli->escape_string($country);

$sortprep=$_GET["sort"];
$sort=$mysqli->escape_string($sortprep);

$orderprep=$_GET["order"];
$order=$mysqli->escape_string($orderprep);

if (isset($_GET["page"])) { $page  = $_GET["page"]; } else { $page=1; };
$page=$mysqli->escape_string($page);
$results_per_page=20;
$start_from = ($page-1) * $results_per_page;

//order can be ascending or descending
echo "Sort: <a href='rankings.php?type=$type&country=$country&sort=$sort&order=asc'>ascending</a> | <a href='rankings.php?type=$type&country=$country&sort=$sort&order=desc'>descending</a>";
echo nl2br(" \n");

if($type=="users"){
	echo "Sort on: <a href='rankings.php?type=$type&country=$country&sort=username&order=$order'>username</a> |
	 <a href='rankings.php?type=$type&country=$country&sort=lastonline&order=$order'>last online</a> |
	 <a href='rankings.php?type=$type&country=$country&sort=nationality&order=$order'>nationality</a> |
	 <a href='rankings.php?type=$type&country=$country&sort=race&order=$order'>race</a> |
	 <a href='rankings.php?type=$type&country=$country&sort=age&order=$order'>age</a> |
	 <a href='rankings.php?type=$type&country=$country&sort=strength&order=$order'>strength</a> |
	 <a href='rankings.php?type=$type&country=$country&sort=dominance&order=$order'>dominance</a>";
	echo nl2br(" \n");
}

if($type=="characters"){
	echo "Sort on: <a href='rankings.php?type=$type&country=$country&sort=name&order=$order'>name</a> |
	 <a href='rankings.php?type=$type&country=$country&sort=familyid&order=$order'>family</a> |
	 <a href='rankings.php?type=$type&country=$country&sort=age&order=$order'>age</a> |
	 <a href='rankings.php?type=$type&country=$country&sort=race&order=$order'>race</a> |
	 <a href='rankings.php?type=$type&country=$country&sort=liege&order=$order'>liege</a> |
	 <a href='rankings.php?type=$type&country=$country&sort=married&order=$order'>married</a>";
	echo nl2br(" \n");
}

if($type=="country"){
	echo "Sort on: <a href='rankings.php?type=$type&sort=country&order=$order'>name</a> |
	 <a href='rankings.php?type=$type&sort=government&order=$order'>government</a> |
	 <a href='rankings.php?type=$type&sort=countrypresident&order=$order'>president</a> |
	 <a href='rankings.php?type=$type&sort=citizens&order=$order'>citizens</a>";
	echo nl2br(" \n");
}

if($type=="region"){
	echo "Sort on: <a href='rankings.php?type=$type&sort=name&order=$order'>name</a> |
	 <a href='rankings.php?type=$type&sort=curowner&order=$order'>current owner</a> |
	 <a href='rankings.php?type=$type&sort=originalowner&order=$order'>original owner</a> |
	 <a href='rankings.php?type=$type&sort=climate&order=$order'>climate</a> |
	 <a href='rankings.php?type=$type&sort=resource&order=$order'>resource</a> |
	 <a href='rankings.php?type=$type&sort=taxminoneday&order=$order'>Tax income</a> |
	 <a href='rankings.php?type=$type&sort=epidemic&order=$order'>epidemic</a> |
	 <a href='rankings.php?type=$type&sort=biggestrel&order=$order'>biggest religion</a>";
	echo nl2br(" \n");
}

if($type=="relics"){
	echo "Sort on: <a href='rankings.php?type=$type&sort=name&order=$order'>name</a> |
	 <a href='rankings.php?type=$type&sort=owner&order=$order'>current owner</a> |
	 <a href='rankings.php?type=$type&sort=location&order=$order'>location</a> |
	 <a href='rankings.php?type=$type&sort=spreadpower&order=$order'>Spread power</a>";
	echo nl2br(" \n");
}

if($type=="militaryunitmembers"){
	echo "Sort on: <a href='rankings.php?type=$type&country=$country&sort=username&order=$order'>name</a> |
	<a href='rankings.php?type=$type&country=$country&sort=strength&order=$order'>strength</a> |
	<a href='rankings.php?type=$type&country=$country&sort=militaryunitrank&order=$order'>rank</a>";
	echo nl2br(" \n");
}

if($type=="politicalpartymembers"){
	echo "Sort on: 
	<a href='rankings.php?type=$type&country=$country&sort=username&order=$order'>name</a>";
	echo nl2br(" \n");
}

if($type=="religionmembers"){
	echo "Sort on: 
	<a href='rankings.php?type=$type&country=$country&sort=username&order=$order'>name</a>";
	echo nl2br(" \n");
}

if($type=="vieworder"){
	echo "Sort on: 
	<a href='rankings.php?type=$type&country=$country&sort=name&order=$order'>name</a> |
	<a href='rankings.php?type=$type&country=$country&sort=leader&order=$order'>leader</a> |
	<a href='rankings.php?type=$type&country=$country&sort=nominee&order=$order'>nominee</a>";
	echo nl2br(" \n");
}

if($type=="runcongress"){
	echo "Sort on: 
	<a href='rankings.php?type=$type&country=$country&sort=electorder&order=$order'>order</a>";
	echo nl2br(" \n");
}

if($type=="claim"){
	echo "Sort on: 
	<a href='rankings.php?type=$type&country=$country&sort=type&order=$order'>type</a> |
	<a href='rankings.php?type=$type&country=$country&sort=inheritable&order=$order'>inheritable</a>";
	echo nl2br(" \n");
}

if($type=="traits"){
	echo "Sort on:
	<a href='rankings.php?type=$type&country=$country&sort=type&order=$order'>type</a> |
	<a href='rankings.php?type=$type&country=$country&sort=name&order=$order'>name</a>";
	echo nl2br(" \n");
}
/*
if($type=="orphanage"){
	echo "Sort on:
	<a href='rankings.php?type=$type&country=$country&sort=type&order=$order'>name</a> |
	<a href='rankings.php?type=$type&country=$country&sort=state&order=$order'>state</a>";
	echo nl2br(" \n");
}
*/
/*
if($type=="battles"){
	echo "Sort on:
	<a href='rankings.php?type=$type&country=$country&sort=location&order=$order'>location</a> |
	<a href='rankings.php?type=$type&country=$country&sort=state&order=$order'>state</a>";
	echo nl2br(" \n");
}
*/

if($type=="users"){
	if($order=="asc"){		
		$sql = "SELECT username, strength, dominance, age, race, nationality, lastonline FROM users WHERE active='1' ORDER BY $sort ASC LIMIT $start_from, ".$results_per_page;
		$rs_result = $mysqli->query($sql);
	}else{
		$sql = "SELECT username, strength, dominance, age, race, nationality, lastonline FROM users WHERE active='1' ORDER BY $sort DESC LIMIT $start_from, ".$results_per_page;
		$rs_result = $mysqli->query($sql);
	}

	?> 
	<div class="scroll">
	<table id="table1">
	<tr>
	    <th> Username</th>
	    <th> Race</th>
	    <th> Nationality</th>
	    <th> Strength</th>
	    <th> Dominance</th>
	    <th> Age</th>
	</tr>
	<?php 
	while($row = $rs_result->fetch_assoc()) {
		$user=$row["username"];
		$link="<a href='account.php?user=$user'>$user</a>";
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
	           <td><?php echo $row["race"]; ?></td>
	           <td><?php echo $row["nationality"]; ?></td>
	           <td><?php echo $row["strength"]; ?></td>
	           <td><?php echo $row["dominance"]; ?></td>
	           <td><?php echo $row["age"]; ?></td>
	           </tr>
		<?php 
		
	}; 
	?> 
	</table>
	</div>
	<?php 

	$sql = "SELECT COUNT(username) AS total FROM users";
	$result = $mysqli->query($sql);
	$row = $result->fetch_assoc();
	$total_pages = ceil($row["total"] / $results_per_page); // calculate total pages with results
	
}elseif($type=="characters"){
	//check if character is owned by user
	$usercharacterid=$country;
	if($usercharacterid != "None"){
		$result2 = $mysqli->query("SELECT * FROM characters WHERE id='$usercharacterid'") or die($mysqli->error());
		$row2 = mysqli_fetch_array($result2);
		$usercharactername = $row2['name'];
		$usercharacterfamily = $row2['familyid'];
		$user=$row2["user"];
	}
	
	if($order=="asc"){
		$sql = "SELECT * FROM characters WHERE alive='1' ORDER BY $sort ASC LIMIT $start_from, ".$results_per_page;
		$rs_result = $mysqli->query($sql);
	}else{
		$sql = "SELECT * FROM characters WHERE alive='1' ORDER BY $sort DESC LIMIT $start_from, ".$results_per_page;
		$rs_result = $mysqli->query($sql);
	}

	?> 
	<div class="scroll">
	<table id="table1">
	<tr>
	    <th> Name</th>
	    <th> Dynasty</th>
	    <th> Age</th>
	    <th> Race</th>
	    <th> Sex</th>
	    <th> Married</th>
	    <th> Liege</th>
	</tr>
	<?php 
	while($row = $rs_result->fetch_assoc()) {
		$charactername=$row["name"];
		$characterid=$row["id"];
		$characteruser=$row["user"];
		$link="<a href='account.php?user=$characteruser&charid=$characterid'>$charactername</a>";
		$characterage=$row["age"];
		$characterrace=$row["race"];
		$charactersex=$row["sex"];
		$charactermarried=$row["married"];
		$characterliege=$row["liege"];
		$characterfamilyid=$row["familyid"];
		$characterlastonline=$row["lastonline"];
		
		//family
		$result2 = $mysqli->query("SELECT * FROM family WHERE id='$characterfamilyid'") or die($mysqli->error());
		$row2 = mysqli_fetch_array($result2);
		$familyname = $row2['name'];
		$link2="<a href='familypage.php?charid=$characterid&type=1'>$familyname</a>";
		
		//liege
		$result2 = $mysqli->query("SELECT * FROM characters WHERE id='$characterliege'") or die($mysqli->error());
		$row2 = mysqli_fetch_array($result2);
		$liegename = $row2['name'];
		$liegeid = $row2['id'];
		$liegefamilyid = $row2['familyid'];
		$liegeuser=$row2["user"];
		
		$result2 = $mysqli->query("SELECT * FROM family WHERE id='$liegefamilyid'") or die($mysqli->error());
		$row2 = mysqli_fetch_array($result2);
		$liegefamilyname = $row2['name'];
		$link3="<a href='account.php?user=$liegeuser&charid=$characterliege'>$liegename $liegefamilyname</a>";
		
		if($charactermarried != 0){
			$result2 = $mysqli->query("SELECT * FROM characters WHERE id='$charactermarried'") or die($mysqli->error());
			$row2 = mysqli_fetch_array($result2);
			$marriedname = $row2['name'];
			$marriedid = $row2['id'];
			$marrieduser=$row["user"];
			$marriedfamilyid = $row2['familyid'];
			
			$result2 = $mysqli->query("SELECT * FROM family WHERE id='$marriedfamilyid'") or die($mysqli->error());
			$row2 = mysqli_fetch_array($result2);
			$marriedfamilyname = $row2['name'];
			$link4="<a href='account.php?user=$marrieduser&charid=$marriedid'>$marriedname $marriedfamilyname</a>";
		}else{
			$link4="Not married";
		}
	
		?> 
	           <tr>
	           <td>
	           	<?php
	           	$lastonline=$characterlastonline;
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
	           <td><?php echo $link2; ?></td>
	           <td><?php echo $characterage; ?></td>
	           <td><?php echo $characterrace; ?></td>
	           <td><?php echo $charactersex; ?></td>
	           <td><?php echo $link4; ?></td>
	           <td><?php echo $link3; ?></td>
	           </tr>
		<?php 
		
	}; 
	?> 
	</table>
	</div>
	<?php 
	
	$sql = "SELECT COUNT(id) AS total FROM characters";
	$result = $mysqli->query($sql);
	$row = $result->fetch_assoc();
	$total_pages = ceil($row["total"] / $results_per_page); // calculate total pages with results

}elseif($type=="country"){
		
	if($order=="asc"){
		$sql = "SELECT * FROM countryinfo ORDER BY $sort ASC LIMIT $start_from, ".$results_per_page;
		$rs_result = $mysqli->query($sql);
	}else{
		$sql = "SELECT * FROM countryinfo ORDER BY $sort DESC LIMIT $start_from, ".$results_per_page;
		$rs_result = $mysqli->query($sql);
	}

	?> 
	<div class="scroll">
	<table id="table1">
	<tr>
	    <th> countryname</th>
	    <th> government type</th>
	    <th> Monarch</th>
	    <th> citizens</th>
	</tr>
	<?php 
	while($row = $rs_result->fetch_assoc()) {
		$countryname=$row["country"];
		$countryid=$row["id"];
		$link="<a href='country.php?country=$countryname'>$countryname</a>";
		if($row["government"]==1){
			$government="Elective monarchy";
		}elseif($row["government"]==2){
			$government="Absolute monarchy";
		}
		
		$result4 = $mysqli->query("SELECT holderid FROM titles WHERE holdingid='$countryid'") or die($mysqli->error());
		$row4 = mysqli_fetch_array($result4);
		$holderid = $row4['holderid'];
		
		$result4 = $mysqli->query("SELECT * FROM characters WHERE id='$holderid'") or die($mysqli->error());
		$row4 = mysqli_fetch_array($result4);
		$holderid = $row4['id'];
		$holderuser = $row4['user'];
		$holdername = $row4['name'];
		$holderfamily = $row4['familyid'];
		
		$result4 = $mysqli->query("SELECT * FROM family WHERE id='$holderfamily'") or die($mysqli->error());
		$row4 = mysqli_fetch_array($result4);
		$holderfamilyname = $row4['name'];
		
		$link2="<a href='account.php?user=$holderuser&charid=$holderid'>$holdername $holderfamilyname</a>";
	
		?> 
	           <tr>
	           <td><?php echo $link; ?></td>
	           <td><?php echo $government; ?></td>
	           <td><?php echo $link2; ?></td>
	           <td><?php echo $row["citizens"]; ?></td>
	           </tr>
		<?php 
		
	}; 
	?> 
	</table>
	</div>
	<?php 
	
	$sql = "SELECT COUNT(country) AS total FROM countryinfo";
	$result = $mysqli->query($sql);
	$row = $result->fetch_assoc();
	$total_pages = ceil($row["total"] / $results_per_page); // calculate total pages with results

}elseif($type=="region"){
		
	if($order=="asc"){
		$sql = "SELECT * FROM region ORDER BY $sort ASC LIMIT $start_from, ".$results_per_page;
		$rs_result = $mysqli->query($sql);
	}else{
		$sql = "SELECT * FROM region ORDER BY $sort DESC LIMIT $start_from, ".$results_per_page;
		$rs_result = $mysqli->query($sql);
	}

	?> 
	<div class="scroll">
	<table id="table1">
	<tr>
	    <th> Region name</th>
	    <th> Current owner</th>
	    <th> Original owner</th>
	    <th> Title holder</th>
	    <th> Climate</th>
	    <th> Resource</th>
	    <th> Last days tax income</th>
	    <th> Epidemic</th>
	    <th> Biggest religion</th>
	</tr>
	<?php 
	while($row = $rs_result->fetch_assoc()) {
		$regionname=$row["name"];
		$regionid=$row["id"];
		$link="<a href='region.php?region=$regionid'>$regionname</a>";
		
		$result4 = $mysqli->query("SELECT holderid FROM titles WHERE holdingid='$regionid'") or die($mysqli->error());
		$row4 = mysqli_fetch_array($result4);
		$holderid = $row4['holderid'];
		
		$result4 = $mysqli->query("SELECT * FROM characters WHERE id='$holderid'") or die($mysqli->error());
		$row4 = mysqli_fetch_array($result4);
		$holderid = $row4['id'];
		$holderuser = $row4['user'];
		$holdername = $row4['name'];
		$holderfamily = $row4['familyid'];
		
		$result4 = $mysqli->query("SELECT * FROM family WHERE id='$holderfamily'") or die($mysqli->error());
		$row4 = mysqli_fetch_array($result4);
		$holderfamilyname = $row4['name'];
		
		$link2="<a href='account.php?user=$holderuser&charid=$holderid'>$holdername $holderfamilyname</a>";
		
		if($row["epidemic"]==0){
			$epidemic="No";
		}elseif($row["epidemic"]==1){
			$epidemic="Yes";
		}
	
		?> 
	           <tr>
	           <td><?php echo $link; ?></td>
	           <td><?php echo $row["curowner"]; ?></td>
	           <td><?php echo $row["originalowner"]; ?></td>
	           <td><?php echo $link2; ?></td>
	           <td><?php echo $row["climate"]; ?></td>
	           <td><?php echo $row["resource"]; ?></td>
	           <td><?php echo $row["taxminoneday"]; ?></td>
	           <td><?php echo $epidemic; ?></td>
	           <td><?php echo $row["biggestrel"]; ?></td>
	           </tr>
		<?php 
		
	}; 
	?> 
	</table>
	</div>
	<?php 
	
	$sql = "SELECT COUNT(name) AS total FROM region";
	$result = $mysqli->query($sql);
	$row = $result->fetch_assoc();
	$total_pages = ceil($row["total"] / $results_per_page); // calculate total pages with results

}elseif($type=="relics"){
		
	if($order=="asc"){
		$sql = "SELECT * FROM relics ORDER BY $sort ASC LIMIT $start_from, ".$results_per_page;
		$rs_result = $mysqli->query($sql);
	}else{
		$sql = "SELECT * FROM relics ORDER BY $sort DESC LIMIT $start_from, ".$results_per_page;
		$rs_result = $mysqli->query($sql);
	}

	?> 
	<div class="scroll">
	<table id="table1">
	<tr>
	    <th> Name</th>
	    <th> Current owner</th>
	    <th> Location</th>
	    <th> Spread power</th>
	</tr>
	<?php 
	while($row = $rs_result->fetch_assoc()) {
		if($row["owner"]=="NULL"){
			$location=$row["location"];
			$owner="None";
		}else{
			$location="None";
			$owner=$row["owner"];
		}
	
		?> 
	           <tr>
	           <td><?php echo $row["name"]; ?></td>
	           <td><?php echo $owner ?></td>
	           <td><?php echo $location; ?></td>
	           <td><?php echo $row["spreadpower"]; ?></td>
	           </tr>
		<?php 
		
	}; 
	?> 
	</table>
	</div>
	<?php 
	
	$sql = "SELECT COUNT(name) AS total FROM relics";
	$result = $mysqli->query($sql);
	$row = $result->fetch_assoc();
	$total_pages = ceil($row["total"] / $results_per_page); // calculate total pages with results

}elseif($type=="militaryunitmembers"){
	$id=$country;
	
	if($order=="asc"){
		$sql = "SELECT * FROM users WHERE militaryunit='$id' ORDER BY $sort ASC LIMIT $start_from, ".$results_per_page;
		$rs_result = $mysqli->query($sql);
	}else{
		$sql = "SELECT * FROM users WHERE militaryunit='$id' ORDER BY $sort DESC LIMIT $start_from, ".$results_per_page;
		$rs_result = $mysqli->query($sql);
	}
	
	$result = $mysqli->query("SELECT * FROM militaryunit WHERE id='$id'") or die($mysqli->error());
	$row2 = mysqli_fetch_array($result);
	$owner=$row2['owner'];
	$gold=$row2['gold'];

	?> 
	<div class="scroll">
	<table id="table1">
	<tr>
	    <th> Name</th>
	    <th> strength</th>
	    <th> rank</th>
	    <?php
	    if($username==$owner){
	    	?> <th> Promote (costs 5 gold and gives 5 strength per rank)</th> <?php
	    }
		?>
	</tr>
	<?php 
	while($row = $rs_result->fetch_assoc()) {
		?> 
	           <tr>
	           <td><?php echo $row["username"]; ?></td>
	           <td><?php echo $row["strength"]; ?></td>
	           <td><?php echo $row["militaryunitrank"]; ?></td>
	           <?php
	           if($row["militaryunitrank"]<5){
	          	if($username==$owner){
	           		?>
	           		<td>
					<form method="post" action="">
						<input type="hidden" name="id" value="<?php echo $id; ?>" />
						<input type="hidden" name="name" value="<?php echo $row["username"]; ?>" />
						<input type="hidden" name="militaryunitrank" value="<?php echo $row["militaryunitrank"]; ?>" />
						<button type="submit" name="promote" /><?php echo "promote member"; ?></button>
					</form>
					</td>
					<?php
	           	}
	           }
	           ?>
	           </tr>
		<?php 
		
	}; 
	?> 
	</table>
	</div>
	<?php 
	
	$sql = "SELECT COUNT(username) AS total FROM users WHERE militaryunit='$id'";
	$result = $mysqli->query($sql);
	$row = $result->fetch_assoc();
	$total_pages = ceil($row["total"] / $results_per_page); // calculate total pages with results

}elseif($type=="politicalpartymembers"){
	$id=$country;
	
	if($order=="asc"){
		$sql = "SELECT * FROM users WHERE politicalparty='$id' ORDER BY $sort ASC LIMIT $start_from, ".$results_per_page;
		$rs_result = $mysqli->query($sql);
	}else{
		$sql = "SELECT * FROM users WHERE politicalparty='$id' ORDER BY $sort DESC LIMIT $start_from, ".$results_per_page;
		$rs_result = $mysqli->query($sql);
	}
	
	$result = $mysqli->query("SELECT * FROM politicalparty WHERE id='$id'") or die($mysqli->error());
	$row2 = mysqli_fetch_array($result);
	$owner=$row2['owner'];

	?> 
	<div class="scroll">
	<table id="table1">
	<tr>
	    <th> Name</th>
	</tr>
	<?php 
	while($row = $rs_result->fetch_assoc()) {
		?> 
       <tr>
       <td><?php echo $row["username"]; ?></td>
       </tr>
		<?php 
		
	}; 
	?> 
	</table>
	</div>
	<?php 
	
	$sql = "SELECT COUNT(username) AS total FROM users WHERE politicalparty='$id'";
	$result = $mysqli->query($sql);
	$row = $result->fetch_assoc();
	$total_pages = ceil($row["total"] / $results_per_page); // calculate total pages with results

}elseif($type=="religionmembers"){
	$id=$country;
	
	$result = $mysqli->query("SELECT * FROM religion WHERE id='$id'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$religiontype=$row['type'];
	$religionname=$row['name'];
	$religionleader=$row['leader'];
	
	if($religiontype=="religion"){
		if($order=="asc"){
			$sql = "SELECT * FROM users WHERE userreligion='$religionname' ORDER BY $sort ASC LIMIT $start_from, ".$results_per_page;
			$rs_result = $mysqli->query($sql);
		}else{
			$sql = "SELECT * FROM users WHERE userreligion='$religionname' ORDER BY $sort DESC LIMIT $start_from, ".$results_per_page;
			$rs_result = $mysqli->query($sql);
		}
	}else{
		$result = $mysqli->query("SELECT * FROM users WHERE username='$username'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$userorder=$row['religionorder'];
		
		if($religiontype=="order"){
			if($order=="asc"){
				$sql = "SELECT * FROM users WHERE religionorder='$id' ORDER BY $sort ASC LIMIT $start_from, ".$results_per_page;
				$rs_result = $mysqli->query($sql);
			}else{
				$sql = "SELECT * FROM users WHERE religionorder='$id' ORDER BY $sort DESC LIMIT $start_from, ".$results_per_page;
				$rs_result = $mysqli->query($sql);
			}
		}else{
			//alleen secretorder laten zien als je lid bent
			if($userorder==$id){
				if($order=="asc"){
					$sql = "SELECT * FROM users WHERE religionorder='$id' ORDER BY $sort ASC LIMIT $start_from, ".$results_per_page;
					$rs_result = $mysqli->query($sql);
				}else{
					$sql = "SELECT * FROM users WHERE religionorder='$id' ORDER BY $sort DESC LIMIT $start_from, ".$results_per_page;
					$rs_result = $mysqli->query($sql);
				}
			}
		}
	}

	if($religionleader==$username AND ($religiontype=="order" OR $religiontype=="secretorder")){
		echo nl2br ("<div class=\"t1\">Promoting a user to a higher rank increases his ability to remove faith in other religions in regions by 2. Promoting someone costs 5 gold which is removed from the orders treassury. </div>");
	}
	?> 
	<div class="scroll">
	<table id="table1">
	<tr>
	    <th> Character</th>
	    <th> User</th>
	    <?php if($religiontype=="order" OR $religiontype=="secretorder"){ ?> <th> Rank</th> <?php } ?>
	</tr>
	<?php 
	while($row = $rs_result->fetch_assoc()) {
		$user = $row["username"];
		
		$result = $mysqli->query("SELECT * FROM characters WHERE user='$user' AND alive='1'") or die($mysqli->error());
		$row2 = mysqli_fetch_array($result);
		$count = $result->num_rows;
		if($count != 0){
			$usercharacterid=$row2['id'];
			$usercharactername=$row2['name'];
			$usercharacterfamilyid=$row2['familyid'];
			
			$result = $mysqli->query("SELECT * FROM family WHERE id='$usercharacterfamilyid'") or die($mysqli->error());
			$row2 = mysqli_fetch_array($result);
			$usercharacterfamilyname=$row2['name'];
			
			$link="<a href='account.php?user=$user&charid=$usercharacterid'>$usercharactername $usercharacterfamilyname</a>";
			
			?> 
	       <tr>
	       	<td><?php echo $link; ?></td>
	       	<td><?php echo $user; ?></td>
	       	<?php if($religiontype=="order" OR $religiontype=="secretorder"){ ?>
		       	<td>
		       		<?php $orderrank= $row["orderrank"]; 
		       		if($orderrank==0){
		       			echo "Novice";
		       		}elseif($orderrank==1){
		       			echo "priest";
		       		}elseif($orderrank==2){
		       			echo "High priest";
		       		}
					if($religionleader==$username){
						?>
						<form method="post" action="">
							<input type="hidden" name="id" value="<?php echo $id; ?>" />
							<input type="hidden" name="name" value="<?php echo $row["username"]; ?>" />
							<input type="hidden" name="orderrank" value="<?php echo $row["orderrank"]; ?>" />
							<button type="submit" name="promotereligion" /><?php echo "promote follower"; ?></button>
						</form>
						<?php
					}
		       		?>
		       	</td>
	       	<?php } ?>
	       </tr>
			<?php 
		}
		
	}; 
	?> 
	</table>
	</div>
	<?php 
	
	if($religiontype=="religion"){
		$sql = "SELECT COUNT(username) AS total FROM users WHERE userreligion='$religionname'";
		$result = $mysqli->query($sql);
		$row = $result->fetch_assoc();
		$total_pages = ceil($row["total"] / $results_per_page); // calculate total pages with results
	}else{
		$sql = "SELECT COUNT(username) AS total FROM users WHERE religionorder='$id'";
		$result = $mysqli->query($sql);
		$row = $result->fetch_assoc();
		$total_pages = ceil($row["total"] / $results_per_page); // calculate total pages with results
	}

}elseif($type=="vieworder"){
	$id=$country;
	
	if($order=="asc"){
		$sql = "SELECT * FROM religion WHERE religionid='$id' AND type='order' ORDER BY $sort ASC LIMIT $start_from, ".$results_per_page;
		$rs_result = $mysqli->query($sql);
	}else{
		$sql = "SELECT * FROM religion WHERE religionid='$id' AND type='order' ORDER BY $sort DESC LIMIT $start_from, ".$results_per_page;
		$rs_result = $mysqli->query($sql);
	}

	?> 
	<div class="scroll">
	<table id="table1">
	<tr>
	    <th> Name</th>
	    <th> Leader</th>
	    <th> Nominee</th>
	</tr>
	<?php 
	while($row = $rs_result->fetch_assoc()) {
		$owner=$row['owner'];
		$name=$row['name'];
		$orderid=$row['id'];
		?> 
       <tr>
       <td><?php echo "<a href='order.php?order=$orderid'>$name </a>";?></td>
       <td><?php echo $row["leader"]; ?></td>
       <td><?php echo $row["nominee"]; ?></td>
       </tr>
		<?php 
		
	}; 
	?> 
	</table>
	</div>
	<?php 
	
	$sql = "SELECT COUNT(id) AS total FROM religion WHERE religionid='$id' AND type='order'";
	$result = $mysqli->query($sql);
	$row = $result->fetch_assoc();
	$total_pages = ceil($row["total"] / $results_per_page); // calculate total pages with results

}elseif($type=="runcongress"){
	if($order=="asc"){
		$sql = "SELECT * FROM elections WHERE party='$country' ORDER BY $sort ASC LIMIT $start_from, ".$results_per_page;
		$rs_result = $mysqli->query($sql);
	}else{
		$sql = "SELECT * FROM elections WHERE party='$country' ORDER BY $sort DESC LIMIT $start_from, ".$results_per_page;
		$rs_result = $mysqli->query($sql);
	}
	
	$result = $mysqli->query("SELECT * FROM politicalparty WHERE id='$country'") or die($mysqli->error());
	$row2 = mysqli_fetch_array($result);
	$partypresident=$row2['partypresident'];
	
	date_default_timezone_set('UTC');
	$day = date("d");

	?> 
	<div class="scroll">
	<table id="table1">
	<tr>
	    <th> Usernamer</th>
	    <th> Order</th>
	</tr>
	<?php 
	while($row = $rs_result->fetch_assoc()) {
		?> 
	           <tr>
	           <td><?php echo $row["candidate"]; ?></td>
	           <td>
	           	<?php 		
				if($partypresident==$username && $day != 15){
					$candidate=$row["candidate"];
					$curorder=$row["electorder"];
	           		?>
					<form method="post" action="">
						<input type="hidden" name="candidate" value="<?php echo "$candidate "; ?>" />
						<input type="number" name="neworder" min="1" step="1" placeholder="<?php echo "$curorder" ?>;">
						<button type="submit" name="changeorder" /><?php echo "Change order"; ?></button>
					</form>
	           		<?php
				}else{
	           		echo $row["electorder"]; 
				}
	           	?></td>
	           </tr>
		<?php 
		
	}; 
	?> 
	</table>
	</div>
	<?php 
	
	$sql = "SELECT COUNT(candidate) AS total FROM elections WHERE party='$country'";
	$result = $mysqli->query($sql);
	$row = $result->fetch_assoc();
	$total_pages = ceil($row["total"] / $results_per_page); // calculate total pages with results

}elseif($type=="claim"){
	$id=$country;
	
	if($order=="asc"){
		$sql = "SELECT * FROM claim WHERE charowner='$id' ORDER BY $sort ASC LIMIT $start_from, ".$results_per_page;
		$rs_result = $mysqli->query($sql);
	}else{
		$sql = "SELECT * FROM claim WHERE charowner='$id' ORDER BY $sort DESC LIMIT $start_from, ".$results_per_page;
		$rs_result = $mysqli->query($sql);
	}

	?> 
	<div class="scroll">
	<table id="table1">
	<tr>
	    <th> Claim</th>
	    <th> Current owner</th>
	    <th> Inheritable</th>
	    <th> Type</th>
	</tr>
	<?php 
	while($row = $rs_result->fetch_assoc()) {
		$claimtitleid=$row['title'];
		$claimtype=$row['type'];
		$claiminheritable=$row['inheritable'];
		$claimcharowner=$row['charowner'];
		$claimid=$row['id'];
		
		if($claimtype == "retract title" OR $claimtype == "resistanceclaim" OR $claimtype == "religionclaim"){
			$result = $mysqli->query("SELECT * FROM titles WHERE id='$claimtitleid'") or die($mysqli->error());
			$row2 = mysqli_fetch_array($result);
			$titleholdingtype=$row2['holdingtype'];
			$titleholderid=$row2['holderid'];
			$titleholdingid=$row2['holdingid'];
			
	   		if($titleholdingtype == "kingdom"){
				$result = $mysqli->query("SELECT * FROM countryinfo WHERE id='$titleholdingid'") or die($mysqli->error());
				$row2 = mysqli_fetch_array($result);
				$titlename=$row2['country'];
	   		}elseif($titleholdingtype == "duchy"){
	  			$result = $mysqli->query("SELECT * FROM region WHERE id='$titleholdingid'") or die($mysqli->error());
				$row2 = mysqli_fetch_array($result);
				$titlename=$row2['name'];
	   		}
		}elseif($claimtype == "imprison"){
			//titleholderid = the one to be imprisoned
			$titleholderid = $claimtitleid;
			$titleholdingtype = "imprison";
		}
		
		$result = $mysqli->query("SELECT * FROM characters WHERE id='$titleholderid'") or die($mysqli->error());
		$row2 = mysqli_fetch_array($result);
		$holdername=$row2['name'];
		$holderfamilyid=$row2['familyid'];
		$holderuser=$row2['user'];
		
		$result = $mysqli->query("SELECT * FROM family WHERE id='$holderfamilyid'") or die($mysqli->error());
		$row2 = mysqli_fetch_array($result);
		$holderfamilyname=$row2['name'];
		?> 
       <tr>
       <td>
       		<?php 
       		if($titleholdingtype == "kingdom"){
       			echo "Kingdom of <a href='region.php?region=$titleholdingid'>$titlename</a>";
       		}elseif($titleholdingtype == "duchy"){
       			echo "Duchy of <a href='region.php?region=$titleholdingid'>$titlename</a>";
       		}elseif($titleholdingtype == "imprison"){
       			echo "Claim to imprison <a href='account.php?user=$holderuser&charid=$titleholderid'>$holdername $holderfamilyname</a>";
       		}
       		?>
	   </td>
       <td>
       		<?php 
			echo "<a href='account.php?user=$holderuser&charid=$titleholderid'>$holdername $holderfamilyname</a>";
       		?>
	   </td>
       <td>
       		<?php 
			if($claiminheritable > 0){
				echo "Yes";
			}elseif($claiminheritable == 0){
				echo "No";
			}
       		?>
	   </td>
       <td>
       		<?php 
       		if($claimtype == "retract title"){
       			echo "Usurp title";
       		}elseif($claimtype == "resistanceclaim"){
       			echo "Resistance claim";
       		}elseif($claimtype == "religionclaim"){
       			echo "Religion claim";
       		}elseif($claimtype == "imprison"){
       			echo "Imprison claim";
       		}
			if($usercharacterid == $claimcharowner){
				?>
				<form method="post" action="">
					<input type="hidden" name="claimid" value="<?php echo $claimid; ?>" />
					<button type="submit" name="useclaim" /><?php echo "Use claim"; ?></button>
				</form>
				<?php
			}
       		?>
	   </td>
       </tr>
		<?php 
		
	}; 
	?> 
	</table>
	</div>
	<?php 
	
	$sql = "SELECT COUNT(id) AS total FROM claim WHERE charowner='$id' AND type='order'";
	$result = $mysqli->query($sql);
	$row = $result->fetch_assoc();
	$total_pages = ceil($row["total"] / $results_per_page); // calculate total pages with results

}elseif($type=="traits"){
	if($order=="asc"){
		$sql = "SELECT * FROM traits ORDER BY $sort ASC LIMIT $start_from, ".$results_per_page;
		$rs_result = $mysqli->query($sql);
	}else{
		$sql = "SELECT * FROM traits ORDER BY $sort DESC LIMIT $start_from, ".$results_per_page;
		$rs_result = $mysqli->query($sql);
	}
	
	?> 
	<div class="scroll">
	<table id="table1">
	<tr>
	    <th> Name</th>
	    <th> Type</th>
	    <th> Amount</th>
	    <th> Chance to cease</th>
	</tr>
	<?php 
	 
	while($row = $rs_result->fetch_assoc()) {
		$name=$row['name'];
		$type=$row['type'];
		$amount=$row['amount'];
		$dissolve=$row['removechance'];
		
		?>
		<tr>
		<td>
			<?php echo "$name"; ?>
		</td>	
		<td>
			<?php echo "$type"; ?>
		</td>
		<td>
			<?php echo "$amount"; ?>
		</td>
		<td>
			<?php echo "$dissolve"; ?>
		</td>
		</tr>
		<?php
	}
	?>
	</table>
	</div>
	<?php
	$sql = "SELECT COUNT(id) AS total FROM traits";
	$result = $mysqli->query($sql);
	$row = $result->fetch_assoc();
	$total_pages = ceil($row["total"] / $results_per_page); // calculate total pages with results
}elseif($type=="orphanage"){
	$result4 = $mysqli->query("SELECT * FROM traits WHERE name='orphan'") or die($mysqli->error());
	$row4 = mysqli_fetch_array($result4);
	$traitid = $row4['id'];
	
	$result4 = $mysqli->query("SELECT * FROM traits WHERE name='eunuch'") or die($mysqli->error());
	$row4 = mysqli_fetch_array($result4);
	$traiteunuchid = $row4['id'];
	//echo "$traitid , $country";
	if($order=="asc"){
		$sql = "SELECT * FROM traitscharacters WHERE traitid = '$traitid' AND extrainfo = '$country' ORDER BY characterid ASC LIMIT $start_from, ".$results_per_page;
		$rs_result = $mysqli->query($sql);
	}else{
		$sql = "SELECT * FROM traitscharacters WHERE traitid = '$traitid' AND extrainfo = '$country' ORDER BY characterid DESC LIMIT $start_from, ".$results_per_page;
		$rs_result = $mysqli->query($sql);
	}
	
	?> 
	<div class="scroll">
	<table id="table1">
	<tr>
	    <th> Name</th>
	    <th> State</th>
	</tr>
	<?php 
	 
	while($row = $rs_result->fetch_assoc()) {
		$characterid=$row['characterid'];
		
		$result4 = $mysqli->query("SELECT * FROM characters WHERE id='$characterid'") or die($mysqli->error());
		$row4 = mysqli_fetch_array($result4);
		$charactername = $row4['name'];
		$characterfamilyid = $row4['familyid'];
		$characteruser = $row4['user'];
		
		$result4 = $mysqli->query("SELECT * FROM family WHERE id='$characterfamilyid'") or die($mysqli->error());
		$row4 = mysqli_fetch_array($result4);
		$characterfamilyname = $row4['name'];
		
		
		$result3 = "SELECT * FROM traitscharacters WHERE characterid='$characterid' AND traitid = '$traiteunuchid'";
		$rs_result2 = $mysqli->query($result3);
		$count2 = $rs_result2->num_rows;//aantal titles
		
		$result4 = $mysqli->query("SELECT * FROM religion WHERE name='$country'") or die($mysqli->error());
		$row4 = mysqli_fetch_array($result4);
		$religionleader = $row4['leader'];
		
		?>
		<tr>
		<td>
			<?php echo "<a href='account.php?user=$characteruser&charid=$characterid'>$charactername $characterfamilyname</a>"; ?>
		</td>	
		<td>
			<?php 
			if($count2 == 0){
				echo "Normal";
				if($religionleader == $username){
					?>
					<form method="post" action="">
						<input type="hidden" name="characterid" value="<?php echo $characterid; ?>" />
						<button type="submit" name="createeunuch" /><?php echo "Create eunuch"; ?></button>
					</form>
					<?php
				}
			}else{
				echo "Eunuch";
			}
			?>
		</td>
		</tr>
		<?php
	}
	?>
	</table>
	</div>
	<?php
	$sql = "SELECT COUNT(id) AS total FROM traitscharacters WHERE traitid = '$traitid' AND extrainfo = '$country'";
	$result = $mysqli->query($sql);
	$row = $result->fetch_assoc();
	$total_pages = ceil($row["total"] / $results_per_page); // calculate total pages with results
}elseif($type=="battles"){
	if($order=="asc"){
		$sql = "SELECT * FROM diplomacy WHERE (type = 'war' OR type = 'resistance') AND ((attackcountry1 IS NOT NULL AND attackcountry1 != 'NULL') OR (attackcountry2 IS NOT NULL AND attackcountry2 != 'NULL')) ORDER BY type ASC LIMIT $start_from, ".$results_per_page;
		$rs_result = $mysqli->query($sql);
	}else{
		$sql = "SELECT * FROM diplomacy WHERE (type = 'war' OR type = 'resistance') AND ((attackcountry1 IS NOT NULL AND attackcountry1 != 'NULL') OR (attackcountry2 IS NOT NULL AND attackcountry2 != 'NULL')) ORDER BY type DESC LIMIT $start_from, ".$results_per_page;
		$rs_result = $mysqli->query($sql);
	}
	
	date_default_timezone_set('UTC'); //current date
	$datecur = date("Y-m-d H:i:s");
	
	?> 
	<div class="scroll">
	<table id="table1">
	<tr>
		<th> War between</th>
	    <th> Region</th>
	    <th> Time</th>
	</tr>
	<?php 
	
	while($row = $rs_result->fetch_assoc()) {
		$diplomacyid=$row['id'];
		$diplomacytype=$row['type'];
		
		$country1=$row['country1'];
		$country2=$row['country2'];
		
		$attackcountry1=$row['attackcountry1'];
		$attackcountry1start=$row['attackcountry1start'];
		
		$attackcountry2=$row['attackcountry2'];
		$attackcountry2start=$row['attackcountry2start'];
		
		if($attackcountry1 != NULL AND $attackcountry1 != "NULL"){
			?>
			<tr>
			<td>
				<?php
				if($diplomacytype == "war"){
					echo "$country1 and $country2";
				}elseif($diplomacytype == "resistance"){
					$result4 = $mysqli->query("SELECT * FROM characters WHERE id='$country1'") or die($mysqli->error());
					$row4 = mysqli_fetch_array($result4);
					$fighter1 = $row4['name'];
					$fighter1user = $row4['user'];
					$fighter1familyid = $row4['familyid'];
					
					$result4 = $mysqli->query("SELECT * FROM family WHERE id='$fighter1familyid'") or die($mysqli->error());
					$row4 = mysqli_fetch_array($result4);
					$fighter1family = $row4['name'];
					
					$result4 = $mysqli->query("SELECT * FROM characters WHERE id='$country2'") or die($mysqli->error());
					$row4 = mysqli_fetch_array($result4);
					$fighter2 = $row4['name'];
					$fighter2user = $row4['user'];
					$fighter2familyid = $row4['familyid'];
					
					$result4 = $mysqli->query("SELECT * FROM family WHERE id='$fighter2familyid'") or die($mysqli->error());
					$row4 = mysqli_fetch_array($result4);
					$fighter2family = $row4['name'];
					
					echo "<a href='account.php?user=$fighter1user&charid=$country1'>$fighter1 $fighter1family </a> AND <a href='account.php?user=$fighter2user&charid=$country2'>$fighter2 $fighter2family </a>";
				}
				?>
			</td>
			<td>
				<?php echo "$attackcountry1"; ?>
			</td>
			<td>
				<?php
				$date1=$attackcountry1start; //date voor country1
				//echo date('Y-m-d H:i:s', strtotime($date1. ' + 1 days'));
				$date = new DateTime($date1);
				$date->add(new DateInterval('P5D')); // P1D means a period of 1 day
				$Datenew1 = $date->format('Y-m-d H:i:s');
				//echo "$Datenew1";
				
				if($datecur<$Datenew1){
					
					?>
					<p id="demo"></p>
		
					<script>
					// Set the date we're counting down to 2018-04-12 15:37:25
					var countDownDate = new Date("<?php echo $Datenew1 ?>").getTime();
					
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
					    document.getElementById("demo").innerHTML = days + "d " + hours + "h "
					    + minutes + "m " + seconds + "s ";
					    
					    // If the count down is over, write some text 
					    if (distance < 0) {
					        clearInterval(x);
					        document.getElementById("demo").innerHTML = "EXPIRED";
					        window.location.reload(false);
					    }
					}, 1000);
					</script>
					<?php
				}
				?>
			</td>
			</tr>
			<?php
		}
		if($attackcountry2 != NULL AND $attackcountry2 != "NULL"){
			?>
			<tr>
			<td>
				<?php
				if($diplomacytype == "war"){
					echo "$country1 and $country2";
				}elseif($diplomacytype == "resistance"){
					$result4 = $mysqli->query("SELECT * FROM characters WHERE id='$country1'") or die($mysqli->error());
					$row4 = mysqli_fetch_array($result4);
					$fighter1 = $row4['name'];
					$fighter1user = $row4['user'];
					$fighter1familyid = $row4['familyid'];
					
					$result4 = $mysqli->query("SELECT * FROM family WHERE id='$fighter1familyid'") or die($mysqli->error());
					$row4 = mysqli_fetch_array($result4);
					$fighter1family = $row4['name'];
					
					$result4 = $mysqli->query("SELECT * FROM characters WHERE id='$country2'") or die($mysqli->error());
					$row4 = mysqli_fetch_array($result4);
					$fighter2 = $row4['name'];
					$fighter2user = $row4['user'];
					$fighter2familyid = $row4['familyid'];
					
					$result4 = $mysqli->query("SELECT * FROM family WHERE id='$fighter2familyid'") or die($mysqli->error());
					$row4 = mysqli_fetch_array($result4);
					$fighter2family = $row4['name'];
					
					echo "<a href='account.php?user=$fighter1user&charid=$country1'>$fighter1 $fighter1family </a> AND <a href='account.php?user=$fighter2user&charid=$country2'>$fighter2 $fighter2family </a>";
				}
				?>
			</td>
			<td>
				<?php echo "$attackcountry2"; ?>
			</td>
			<td>
				<?php
				$date1=$attackcountry2start; //date voor country1
				//echo date('Y-m-d H:i:s', strtotime($date1. ' + 1 days'));
				$date = new DateTime($date1);
				$date->add(new DateInterval('P5D')); // P1D means a period of 1 day
				$Datenew1 = $date->format('Y-m-d H:i:s');
				//echo "$Datenew1";
				
				if($datecur<$Datenew1){
					
					?>
					<p id="demo"></p>
		
					<script>
					// Set the date we're counting down to 2018-04-12 15:37:25
					var countDownDate = new Date("<?php echo $Datenew1 ?>").getTime();
					
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
					    document.getElementById("demo").innerHTML = days + "d " + hours + "h "
					    + minutes + "m " + seconds + "s ";
					    
					    // If the count down is over, write some text 
					    if (distance < 0) {
					        clearInterval(x);
					        document.getElementById("demo").innerHTML = "EXPIRED";
					        window.location.reload(false);
					    }
					}, 1000);
					</script>
					<?php
				}
				?>
			</td>
			</tr>
			<?php
		}
	}
	?>
	</table>
	</div>
	<?php
	$sql = "SELECT COUNT(id) AS total FROM diplomacy WHERE (type = 'war' OR type = 'resistance') AND ((attackcountry1 IS NOT NULL AND attackcountry1 != 'NULL') OR (attackcountry2 IS NOT NULL AND attackcountry2 != 'NULL'))";
	$result = $mysqli->query($sql);
	$row = $result->fetch_assoc();
	$total_pages = ceil($row["total"] / $results_per_page); // calculate total pages with results
}elseif($type=="prison"){
	$result4 = $mysqli->query("SELECT * FROM traits WHERE name='imprisoned'") or die($mysqli->error());
	$row4 = mysqli_fetch_array($result4);
	$traitid = $row4['id'];
	
	//$result4 = $mysqli->query("SELECT * FROM traits WHERE name='eunuch'") or die($mysqli->error());
	//$row4 = mysqli_fetch_array($result4);
	//$traiteunuchid = $row4['id'];
	//echo "$traitid , $country";
	if($order=="asc"){
		$sql = "SELECT * FROM traitscharacters WHERE traitid = '$traitid' AND extrainfo = '$country' ORDER BY characterid ASC LIMIT $start_from, ".$results_per_page;
		$rs_result = $mysqli->query($sql);
	}else{
		$sql = "SELECT * FROM traitscharacters WHERE traitid = '$traitid' AND extrainfo = '$country' ORDER BY characterid DESC LIMIT $start_from, ".$results_per_page;
		$rs_result = $mysqli->query($sql);
	}
	
	?> 
	<div class="scroll">
	<table id="table1">
	<tr>
	    <th> Prisoner </th>
	    <th> Years imprisoned </th>
	    <th> Action </th>
	    <th> Release </th>
	</tr>
	<?php 
	 
	while($row = $rs_result->fetch_assoc()) {
		$characterid=$row['characterid'];
		$imprisondate=$row['date'];
		
		$date = date("d", strtotime($imprisondate));
		
		date_default_timezone_set('UTC'); //current date
		$datecur = date("d"); 
		
		$datediffdays = $datecur - $date;
		
		$result4 = $mysqli->query("SELECT * FROM characters WHERE id='$characterid'") or die($mysqli->error());
		$row4 = mysqli_fetch_array($result4);
		$charactername = $row4['name'];
		$characterfamilyid = $row4['familyid'];
		$characteruser = $row4['user'];
		
		$result4 = $mysqli->query("SELECT * FROM family WHERE id='$characterfamilyid'") or die($mysqli->error());
		$row4 = mysqli_fetch_array($result4);
		$characterfamilyname = $row4['name'];
		
		$result4 = $mysqli->query("SELECT * FROM traits WHERE name='tortured'") or die($mysqli->error());
		$row4 = mysqli_fetch_array($result4);
		$torturedtraitid = $row4['id'];
		
		$result3 = "SELECT * FROM traitscharacters WHERE traitid='$torturedtraitid' AND characterid='$characterid'";
		$rs_result2 = $mysqli->query($result3);
		$count3 = $rs_result2->num_rows;//aantal titles
		
		?>
		<tr>
		<td>
			<?php echo "<a href='account.php?user=$characteruser&charid=$characterid'>$charactername $characterfamilyname</a>"; ?>
		</td>
		<td>
			<?php echo "$datediffdays"; ?>
		</td>	
		<td>
			<div class="textbox">
				<form method="post" action="">
					<input type="hidden" name="prisonerid" value="<?php echo "$characterid"; ?>" />
					<?php if($count3 == 0){ ?> <button type="submit" value="torture" name="actionprisoner" />Torture</button> <?php } ?>
				</form>
			</div>
		</td>	
		<td>
			<?php 
			?>
			<div class="textbox">
				<form method="post" action="">
					<input type="hidden" name="prisonerid" value="<?php echo "$characterid"; ?>" />
					<button type="submit" value="release" name="actionprisoner" />Release</button>
					<button type="submit" value="mutilate" name="actionprisoner" />Mutilate</button>
					<button type="submit" value="castrate" name="actionprisoner" />Castrate</button>
				</form>
			</div>
			<?php
			?>
		</td>
		</tr>
		<?php
	}
	?>
	</table>
	</div>
	<?php
	$sql = "SELECT COUNT(id) AS total FROM traitscharacters WHERE traitid = '$traitid' AND extrainfo = '$country'";
	$result = $mysqli->query($sql);
	$row = $result->fetch_assoc();
	$total_pages = ceil($row["total"] / $results_per_page); // calculate total pages with results
}

//create eunuch
if(isset($_POST['createeunuch'])){
	$characterid = $mysqli->escape_string($_POST['characterid']);
	
	echo nl2br ("<div class=\"t1\">This action will turn this character into an eunuch. This action is irreversible and will cost the religion 5 gold.</div>");
	
	?>
	<form method="post" action="">
		<input type="hidden" name="characterid" autofocus value="<?php echo $characterid; ?>" />
		<button type="submit" name="createeunuch2" />Accept</button>
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

if(isset($_POST['createeunuch2'])){
	$characterid = $mysqli->escape_string($_POST['characterid']);
	
	$result4 = $mysqli->query("SELECT * FROM characters WHERE id='$characterid'") or die($mysqli->error());
	$row4 = mysqli_fetch_array($result4);
	$characteruser = $row4['user'];
	
	$result4 = $mysqli->query("SELECT * FROM traits WHERE name='orphan'") or die($mysqli->error());
	$row4 = mysqli_fetch_array($result4);
	$traitorphanid = $row4['id'];
	
	$result4 = $mysqli->query("SELECT * FROM traits WHERE name='eunuch'") or die($mysqli->error());
	$row4 = mysqli_fetch_array($result4);
	$traiteunuchid = $row4['id'];
	
	$result4 = $mysqli->query("SELECT * FROM traits WHERE name='terrible'") or die($mysqli->error());
	$row4 = mysqli_fetch_array($result4);
	$traitterribleid = $row4['id'];
	
	$result3 = "SELECT * FROM traitscharacters WHERE characterid='$usercharacterid' AND traitid = '$traitterribleid'";
	$rs_result2 = $mysqli->query($result3);
	$countterrible = $rs_result2->num_rows;//aantal titles

	$result3 = "SELECT * FROM traitscharacters WHERE characterid='$characterid' AND traitid = '$traitorphanid' AND extrainfo = '$country'";
	$rs_result2 = $mysqli->query($result3);
	$count2 = $rs_result2->num_rows;//aantal titles

	$result4 = $mysqli->query("SELECT * FROM religion WHERE name='$country'") or die($mysqli->error());
	$row4 = mysqli_fetch_array($result4);
	$religionleader = $row4['leader'];
	$religiongold = $row4['gold'];
	
	if($religionleader == $username){
		if($count2 != 0){
			$religiongold = $religiongold - 5;
			if($religiongold >= 0){
				$sql = "UPDATE religion SET gold='$religiongold' WHERE name='$country'";
				mysqli_query($mysqli, $sql);
				
				$sql = "UPDATE characters SET liege='$usercharacterid' WHERE id='$characterid'";
				mysqli_query($mysqli, $sql);
				
				$sql = "INSERT INTO traitscharacters (characterid, traitid, date, extrainfo) " 
				. "VALUES ('$characterid','$traiteunuchid',NOW(),'$country')";
		 		mysqli_query($mysqli, $sql);
				
				$content= "You have been castrated by your religious leader.";
				$sql = "INSERT INTO events (date, content, extrainfo) " 
			     . "VALUES (NOW(),'$content','$characteruser')";
				mysqli_query($mysqli2, $sql);
				
				if($countterrible == 0){
					$randterrible = rand(0, 100);
					if($randterrible <= 5){
						$sql = "INSERT INTO traitscharacters (characterid, traitid, date) " 
						. "VALUES ('$usercharacterid','$traitterribleid',NOW())";
				 		mysqli_query($mysqli, $sql);
						
						$content= "Castrating orphans gained you the terrible trait.";
						$sql = "INSERT INTO events (date, content, extrainfo) " 
					     . "VALUES (NOW(),'$content','$username')";
						mysqli_query($mysqli2, $sql);
					}
				}
				
				echo'<div class="boxed">Done!</div>';
			}else{
				echo'<div class="boxed">Not enough gold available!</div>';
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

if(isset($_POST['actionprisoner'])){
	$prisonerid = $mysqli->escape_string($_POST['prisonerid']);
	$action = $mysqli->escape_string($_POST['actionprisoner']);
	
	$result4 = $mysqli->query("SELECT * FROM traits WHERE name='imprisoned'") or die($mysqli->error());
	$row4 = mysqli_fetch_array($result4);
	$traitid = $row4['id'];
	
	$result3 = "SELECT * FROM traitscharacters WHERE traitid='$traitid' AND characterid='$prisonerid' AND extrainfo='$usercharacterid'";
	$rs_result2 = $mysqli->query($result3);
	$count2 = $rs_result2->num_rows;//aantal titles
	
	$result4 = $mysqli->query("SELECT * FROM characters WHERE id='$prisonerid'") or die($mysqli->error());
	$row4 = mysqli_fetch_array($result4);
	$prisonername = $row4['name'];
	$prisonerfamilyid = $row4['familyid'];
	$prisoneruser = $row4['user'];
	
	$result4 = $mysqli->query("SELECT * FROM family WHERE id='$prisonerfamilyid'") or die($mysqli->error());
	$row4 = mysqli_fetch_array($result4);
	$prisoneridfamilyname = $row4['name'];
	
	$result4 = $mysqli->query("SELECT * FROM characters WHERE id='$usercharacterid'") or die($mysqli->error());
	$row4 = mysqli_fetch_array($result4);
	$usercharactername = $row4['name'];
	$usercharacterfamilyid = $row4['familyid'];
	$usercharacteruser = $row4['user'];
	
	$result4 = $mysqli->query("SELECT * FROM family WHERE id='$usercharacterfamilyid'") or die($mysqli->error());
	$row4 = mysqli_fetch_array($result4);
	$usercharacterfamilyname = $row4['name'];
	
	if($count2 != 0){
		$result4 = $mysqli->query("SELECT * FROM traitscharacters WHERE traitid='$traitid' AND characterid='$prisonerid' AND extrainfo='$usercharacterid'") or die($mysqli->error());
		$row4 = mysqli_fetch_array($result4);
		$traitscharactersid = $row4['id'];
		
		if($action == "release"){
			$sql = "DELETE FROM traitscharacters WHERE id = '$traitscharactersid'";
			mysqli_query($mysqli, $sql);
			
			$content = "You have been released from prison by <a href='account.php?user=$usercharacteruser&charid=$usercharacterid'>$usercharactername $usercharacterfamilyname</a>";
			seteventuser($content,$prisoneruser);
			
			$randnumber = rand(1, 100);
			if($randnumber <= 30){
				$result4 = $mysqli->query("SELECT * FROM traits WHERE name='forgiving'") or die($mysqli->error());
				$row4 = mysqli_fetch_array($result4);
				$forgivingtraitid = $row4['id'];
				
				$result3 = "SELECT * FROM traitscharacters WHERE traitid='$forgivingtraitid' AND characterid='$usercharacterid'";
				$rs_result2 = $mysqli->query($result3);
				$count3 = $rs_result2->num_rows;//aantal titles
				
				if($count3 == 0){
					$sql = "INSERT INTO traitscharacters (characterid, traitid, date) " 
					. "VALUES ('$usercharacterid','$forgivingtraitid',NOW())";
			 		mysqli_query($mysqli, $sql);
					
					$content = "By forgiving your prisoners you gained the forgiving trait.";
					seteventuser($content,$usercharacteruser);
				}
			}
			echo'<div class="boxed">Done!</div>';
		}elseif($action == "mutilate"){
			$sql = "DELETE FROM traitscharacters WHERE id = '$traitscharactersid'";
			mysqli_query($mysqli, $sql);
			
			$content = "As a punishment for your crimes you have been mutilated by <a href='account.php?user=$usercharacteruser&charid=$usercharacterid'>$usercharactername $usercharacterfamilyname</a>";
			seteventuser($content,$prisoneruser);
			
			$result4 = $mysqli->query("SELECT * FROM traits WHERE name='mutilated'") or die($mysqli->error());
			$row4 = mysqli_fetch_array($result4);
			$mutilatedtraitid = $row4['id'];
			
			$result4 = $mysqli->query("SELECT * FROM traits WHERE name='wounded'") or die($mysqli->error());
			$row4 = mysqli_fetch_array($result4);
			$woundedtraitid = $row4['id'];
			
			$result3 = "SELECT * FROM traitscharacters WHERE traitid='$mutilatedtraitid' AND characterid='$prisonerid'";
			$rs_result2 = $mysqli->query($result3);
			$count3 = $rs_result2->num_rows;//aantal titles
			
			
			if($count3 == 0){
				$sql = "INSERT INTO traitscharacters (characterid, traitid, date) " 
				. "VALUES ('$prisonerid','$mutilatedtraitid',NOW())";
		 		mysqli_query($mysqli, $sql);
		 		
				$sql = "INSERT INTO traitscharacters (characterid, traitid, date) " 
				. "VALUES ('$prisonerid','$woundedtraitid',NOW())";
		 		mysqli_query($mysqli, $sql);
			}
			
			$randnumber = rand(1, 100);
			if($randnumber <= 30){
				$result4 = $mysqli->query("SELECT * FROM traits WHERE name='cruel'") or die($mysqli->error());
				$row4 = mysqli_fetch_array($result4);
				$crueltraitid = $row4['id'];
				
				$result3 = "SELECT * FROM traitscharacters WHERE traitid='$crueltraitid' AND characterid='$usercharacterid'";
				$rs_result2 = $mysqli->query($result3);
				$count3 = $rs_result2->num_rows;//aantal titles
				
				if($count3 == 0){
					$sql = "INSERT INTO traitscharacters (characterid, traitid, date) " 
					. "VALUES ('$usercharacterid','$crueltraitid',NOW())";
			 		mysqli_query($mysqli, $sql);
					
					$content = "Due to your cruel punishment of your prisoners you gained the cruel trait.";
					seteventuser($content,$usercharacteruser);
				}
			}
			echo'<div class="boxed">Done!</div>';
		}elseif($action == "castrate"){
			$sql = "DELETE FROM traitscharacters WHERE id = '$traitscharactersid'";
			mysqli_query($mysqli, $sql);
			
			$content = "As a punishment for your crimes you have been castrated by <a href='account.php?user=$usercharacteruser&charid=$usercharacterid'>$usercharactername $usercharacterfamilyname</a>";
			seteventuser($content,$prisoneruser);
			
			$result4 = $mysqli->query("SELECT * FROM traits WHERE name='eunuch'") or die($mysqli->error());
			$row4 = mysqli_fetch_array($result4);
			$eunuchtraitid = $row4['id'];
			
			$result4 = $mysqli->query("SELECT * FROM traits WHERE name='wounded'") or die($mysqli->error());
			$row4 = mysqli_fetch_array($result4);
			$woundedtraitid = $row4['id'];
			
			$result3 = "SELECT * FROM traitscharacters WHERE traitid='$mutilatedtraitid' AND characterid='$prisonerid'";
			$rs_result2 = $mysqli->query($result3);
			$count3 = $rs_result2->num_rows;//aantal titles
			
			
			if($count3 == 0){
				$sql = "INSERT INTO traitscharacters (characterid, traitid, date) " 
				. "VALUES ('$prisonerid','$eunuchtraitid',NOW())";
		 		mysqli_query($mysqli, $sql);
		 		
				$sql = "INSERT INTO traitscharacters (characterid, traitid, date) " 
				. "VALUES ('$prisonerid','$woundedtraitid',NOW())";
		 		mysqli_query($mysqli, $sql);
			}
			
			$randnumber = rand(1, 100);
			if($randnumber <= 30){
				$result4 = $mysqli->query("SELECT * FROM traits WHERE name='cruel'") or die($mysqli->error());
				$row4 = mysqli_fetch_array($result4);
				$crueltraitid = $row4['id'];
				
				$result3 = "SELECT * FROM traitscharacters WHERE traitid='$crueltraitid' AND characterid='$usercharacterid'";
				$rs_result2 = $mysqli->query($result3);
				$count3 = $rs_result2->num_rows;//aantal titles
				
				if($count3 == 0){
					$sql = "INSERT INTO traitscharacters (characterid, traitid, date) " 
					. "VALUES ('$usercharacterid','$crueltraitid',NOW())";
			 		mysqli_query($mysqli, $sql);
					
					$content = "Due to your cruel punishment of your prisoners you gained the cruel trait.";
					seteventuser($content,$usercharacteruser);
				}
			}
			echo'<div class="boxed">Done!</div>';
		}elseif($action == "torture"){
			$result4 = $mysqli->query("SELECT * FROM traits WHERE name='tortured'") or die($mysqli->error());
			$row4 = mysqli_fetch_array($result4);
			$torturedtraitid = $row4['id'];
			
			$result3 = "SELECT * FROM traitscharacters WHERE traitid='$torturedtraitid' AND characterid='$prisonerid'";
			$rs_result2 = $mysqli->query($result3);
			$count3 = $rs_result2->num_rows;//aantal titles
			
			if($count3 == 0){
				$sql = "INSERT INTO traitscharacters (characterid, traitid, date) " 
				. "VALUES ('$prisonerid','$torturedtraitid',NOW())";
		 		mysqli_query($mysqli, $sql);
				
				$randnumber = rand(1, 100);
				if($randnumber <= 10){
					$result4 = $mysqli->query("SELECT * FROM traits WHERE name='cruel'") or die($mysqli->error());
					$row4 = mysqli_fetch_array($result4);
					$crueltraitid = $row4['id'];
					
					$result3 = "SELECT * FROM traitscharacters WHERE traitid='$crueltraitid' AND characterid='$usercharacterid'";
					$rs_result2 = $mysqli->query($result3);
					$count3 = $rs_result2->num_rows;//aantal titles
					
					if($count3 == 0){
						$sql = "INSERT INTO traitscharacters (characterid, traitid, date) " 
						. "VALUES ('$usercharacterid','$crueltraitid',NOW())";
				 		mysqli_query($mysqli, $sql);
						
						$content = "Due to your cruel punishment of your prisoners you gained the cruel trait.";
						seteventuser($content,$usercharacteruser);
					}
				}
				echo'<div class="boxed">Done!</div>';
			}else{
				echo'<div class="boxed">This character has already been tortured!</div>';
			}
		}
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

//use claim
if(isset($_POST['useclaim'])){
	$claimid = $mysqli->escape_string($_POST['claimid']);
	
	$result = $mysqli->query("SELECT * FROM claim WHERE id='$claimid'") or die($mysqli->error());
	$row2 = mysqli_fetch_array($result);
	$claimtitleid=$row2['title'];
	$claimtype=$row2['type'];
	$claiminheritable=$row2['inheritable'];
	$claimcharowner=$row2['charowner'];
	$claimid=$row2['id'];
	
	if($usercharacterid == $claimcharowner){
		if($claimtype == "retractclaim" OR $claimtype == "resistanceclaim" OR $claimtype == "religionclaim"){
			$result = $mysqli->query("SELECT * FROM titles WHERE id='$claimtitleid'") or die($mysqli->error());
			$row2 = mysqli_fetch_array($result);
			$titleholdingtype=$row2['holdingtype'];
			$titleholderid=$row2['holderid'];
			$titleholdingid=$row2['holdingid'];
			
			if($titleholdingtype == "kingdom"){
				$result = $mysqli->query("SELECT * FROM countryinfo WHERE id='$titleholdingid'") or die($mysqli->error());
				$row2 = mysqli_fetch_array($result);
				$titlename=$row2['country'];
			}elseif($titleholdingtype == "duchy"){
				$result = $mysqli->query("SELECT * FROM region WHERE id='$titleholdingid'") or die($mysqli->error());
				$row2 = mysqli_fetch_array($result);
				$titlename=$row2['name'];
			}
		
			$result = $mysqli->query("SELECT * FROM characters WHERE id='$titleholderid'") or die($mysqli->error());
			$row2 = mysqli_fetch_array($result);
			$holdername=$row2['name'];
			$holderfamilyid=$row2['familyid'];
			$holderuser=$row2['user'];
			
			$result = $mysqli->query("SELECT * FROM family WHERE id='$holderfamilyid'") or die($mysqli->error());
			$row2 = mysqli_fetch_array($result);
			$holderfamilyname=$row2['name'];
		}
		
		if($claimtype == "retractclaim"){
			echo nl2br ("<div class=\"t1\">With this claim you are the rightfull owner of this title. If you use it you you will become the holderof this title. This action may offend the current holder of this title. The current holder will also gain a claim to start a resistance within the realm of this title.</div>");
		}elseif($claimtype == "resistanceclaim" OR $claimtype == "religionclaim"){
			echo nl2br ("<div class=\"t1\">With this claim you will start a resistance war in this holding. To do this you will need to be part or own a military unit with a camp in this region.</div>");
		}elseif($claimtype == "imprison"){
			echo nl2br ("<div class=\"t1\">With this claim you will imprison this character. To use this claim you will need to be in the same region.</div>");
		}
		?>
		<form method="post" action="">
			<input type="hidden" name="claimid" autofocus value="<?php echo $claimid; ?>" />
			<button type="submit" name="useclaim2" />Accept</button>
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

if(isset($_POST['useclaim2'])){
	$claimid = $mysqli->escape_string($_POST['claimid']);
	
	$result = $mysqli->query("SELECT * FROM claim WHERE id='$claimid'") or die($mysqli->error());
	$row2 = mysqli_fetch_array($result);
	$claimtitleid=$row2['title'];
	$claimtype=$row2['type'];
	$claiminheritable=$row2['inheritable'];
	$claimcharowner=$row2['charowner'];
	$claimid=$row2['id'];
	$claimcountryowner=$row2['countryowner'];
	
	if($usercharacterid == $claimcharowner){
		if($claimtype == "retractclaim" OR $claimtype == "resistanceclaim" OR $claimtype == "religionclaim"){
			$result = $mysqli->query("SELECT * FROM titles WHERE id='$claimtitleid'") or die($mysqli->error());
			$row2 = mysqli_fetch_array($result);
			$titleholdingtype=$row2['holdingtype'];
			$titleholderid=$row2['holderid'];
			$titleholdingid=$row2['holdingid'];
			$titleid=$row2['id'];
			
			if($titleholdingtype == "kingdom"){
				$result = $mysqli->query("SELECT * FROM countryinfo WHERE id='$titleholdingid'") or die($mysqli->error());
				$row2 = mysqli_fetch_array($result);
				$titlename=$row2['country'];
				$titlename2=$mysqli->escape_string($titlename);
			}elseif($titleholdingtype == "duchy"){
				$result = $mysqli->query("SELECT * FROM region WHERE id='$titleholdingid'") or die($mysqli->error());
				$row2 = mysqli_fetch_array($result);
				$titlename=$row2['name'];
				$titlename2=$mysqli->escape_string($titlename);
				$curowner=$row2['curowner'];
			}
			
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
		}
		
		$result = $mysqli->query("SELECT * FROM characters WHERE id='$usercharacterid'") or die($mysqli->error());
		$row2 = mysqli_fetch_array($result);
		$charactername=$row2['name'];
		$characterfamilyid=$row2['familyid'];
		$characterlocation=$row2['location'];
		$characterlocation2=$row2['location2'];
		
		$result = $mysqli->query("SELECT * FROM family WHERE id='$characterfamilyid'") or die($mysqli->error());
		$row2 = mysqli_fetch_array($result);
		$characterfamilyname=$row2['name'];
		
		$result = $mysqli->query("SELECT * FROM users WHERE username='$username'") or die($mysqli->error());
		$row2 = mysqli_fetch_array($result);
		$characternationality=$row2['nationality'];
		$charactermilitaryunit=$row2['militaryunit'];
		
		if($claimtype == "retractclaim"){
			if($holderid != $usercharacterid){
				if($titleholdingtype == "kingdom"){
					$sql = "UPDATE countryinfo SET countrypresident='$usercharacterid', characterowner='$usercharacterid' WHERE id='$titleholdingid'";
					mysqli_query($mysqli, $sql);
				}elseif($titleholdingtype == "duchy"){
					$sql = "UPDATE region SET characterowner='$usercharacterid', curowner='$claimcountryowner' WHERE id='$titleholdingid'";
					mysqli_query($mysqli, $sql);
					
					$sql = "UPDATE users SET location='$claimcountryowner' WHERE location2='$titlename'"; //update country of companies
					mysqli_query($mysqli, $sql);
						
					$sql = "UPDATE characters SET location='$claimcountryowner' WHERE location2='$titlename'"; //update country of companies
					mysqli_query($mysqli, $sql);
					
					$sql = "UPDATE companies SET countryco='$claimcountryowner' WHERE region='$titlename'"; //update country of companies
					mysqli_query($mysqli, $sql);
					
					//if no duchies left give claim to kingdom
					$result8 = "SELECT * FROM region WHERE curowner='$curowner'";
					$rs_result8 = $mysqli->query($result8);
					$count8 = $rs_result8->num_rows;
					
					if($count8 == 0){
						$sql = "INSERT INTO claim (type, inheritable, charowner, title, date, countryowner) " 
						. "VALUES ('retractclaim','0','$usercharacterid','$titleid',NOW(),'$nationality')";
				 		mysqli_query($mysqli, $sql);
						$lastid = $mysqli->insert_id;
					}
				}
				$sql = "UPDATE titles SET holderid='$usercharacterid' WHERE id='$titleid'";
				mysqli_query($mysqli, $sql);
				
				if($titleholdingtype == "duchy"){//alleen bij duchy resistanceclaim
					$sql = "INSERT INTO claim (type, inheritable, charowner, title, date, countryowner) " 
					. "VALUES ('resistanceclaim','1','$titleholderid','$titleid',NOW(),'$nationality')";
			 		mysqli_query($mysqli, $sql);
					$lastid = $mysqli->insert_id;
					
					$content= "<a href='account.php?user=$username&charid=$usercharacterid'>$charactername $characterfamilyname</a> usurped the $titleholdingtype of $titlename from you. You gained a resistance claim on this region.";
					$content=$mysqli->escape_string($content);
					$sql = "INSERT INTO events (date, content, extrainfo) " 
				     . "VALUES (NOW(),'$content','$holderuser')";
					mysqli_query($mysqli2, $sql);
				}elseif($titleholdingtype == "kingdom"){
					$sql = "INSERT INTO claim (type, inheritable, charowner, title, date, countryowner) " 
					. "VALUES ('resistanceclaim','1','$titleholderid','$titleid',NOW(),'$nationality')";
			 		mysqli_query($mysqli, $sql);
					$lastid = $mysqli->insert_id;
					
					$content= "<a href='account.php?user=$username&charid=$usercharacterid'>$charactername $characterfamilyname</a> usurped the $titleholdingtype of $titlename from you";
					$content=$mysqli->escape_string($content);
					$sql = "INSERT INTO events (date, content, extrainfo) " 
				     . "VALUES (NOW(),'$content','$holderuser')";
					mysqli_query($mysqli2, $sql);
				}else{
					$content= "<a href='account.php?user=$username&charid=$usercharacterid'>$charactername $characterfamilyname</a> usurped the $titleholdingtype of $titlename from you";
					$content=$mysqli->escape_string($content);
					$sql = "INSERT INTO events (date, content, extrainfo) " 
				     . "VALUES (NOW(),'$content','$holderuser')";
					mysqli_query($mysqli2, $sql);
				}
				
				$content= "You usurped the $titleholdingtype of $titlename from <a href='account.php?user=$holderuser&charid=$holderid'>$holdername $holderfamilyname</a>";
				$content=$mysqli->escape_string($content);
				$sql = "INSERT INTO events (date, content, extrainfo) " 
			     . "VALUES (NOW(),'$content','$username')";
				mysqli_query($mysqli2, $sql);
				
				$sql = "DELETE FROM claim WHERE id='$claimid'";
				mysqli_query($mysqli, $sql);
			}else{
				echo'<div class="boxed">You already own this holding!</div>';
			}
		}elseif($claimtype == "resistanceclaim" OR $claimtype == "religionclaim"){
			if($titleholdingtype == "duchy"){
				$result4 = $mysqli->query("SELECT camp FROM militaryunit WHERE (owner='$username' OR id='$charactermilitaryunit') AND camp='$titlename2'") or die($mysqli->error());
				$row4 = mysqli_fetch_array($result4);
				$countcamp = $result4->num_rows;
				//$camplocation=$row['camp'];
			}elseif($titleholdingtype == "kingdom"){
				$result8 = "SELECT * FROM region WHERE curowner='$titlename2'";
				$rs_result8 = $mysqli->query($result8);
				$count8 = $rs_result8->num_rows;
				
				if($count8 != 0){
					while($row3 = $rs_result3->fetch_assoc()) {//check if camp is set up in one of regions of kingdom
						if($i==1){
							$regionname=$row2["name"];
							$regionnamecampcheck=$mysqli->escape_string($regionname);
							
							$result4 = $mysqli->query("SELECT camp FROM militaryunit WHERE (owner='$username' OR id='$charactermilitaryunit') AND camp='$regionnamecampcheck'") or die($mysqli->error());
							$row4 = mysqli_fetch_array($result4);
							$count6 = $result4->num_rows;
							
							if($count6 != 0){
								$countcamp = $countcamp + 1;
							}
						}
					}
				}else{
					$countcamp = 0;
				}
			}
			
			if($countcamp >= 1){
				if($titleholdingtype == "duchy"){
					if($holderliege != $usercharacterid AND $holderid != $usercharacterid){
						$result4 = $mysqli->query("SELECT * FROM diplomacy WHERE attackcountry1='$titlename2' OR attackcountry2='$titlename2'") or die($mysqli->error());
						$row4 = mysqli_fetch_array($result4);
						$countwars = $result4->num_rows;
						
						if($countwars == 0){
							date_default_timezone_set('UTC'); //current date
							$datecur = date("Y-m-d H:i:s"); 
							
							$sql = "INSERT INTO diplomacy (type, country1, country2, attackcountry1, attackcountry1start,acceptnap) " 
								. "VALUES ('resistance','$usercharacterid', '$holderid', '$titlename2', NOW(),'1')";
							mysqli_query($mysqli, $sql);
							
							$sql = "DELETE FROM claim WHERE id='$claimid'";
							mysqli_query($mysqli, $sql);
							
							$content= "<a href='account.php?user=$username&charid=$usercharacterid'>$charactername $characterfamilyname</a> started a resistance war in the duchy of $titlename";
							$content=$mysqli->escape_string($content);
							$sql = "INSERT INTO events (date, content) " 
						     . "VALUES (NOW(),'$content')";
							mysqli_query($mysqli2, $sql);
						}else{
							echo'<div class="boxed">You can not press this claim while a war is in progress in this duchy!</div>';
						}
					}elseif($titleholdingtype == "kingdom"){
						$result = $mysqli->query("SELECT camp FROM militaryunit WHERE id='$charactermilitaryunit'") or die($mysqli->error());
						$row2 = mysqli_fetch_array($result);
						$militaryunitcamp=$row2['camp'];
						$militaryunitcamp=$mysqli->escape_string($militaryunitcamp);
						
						$result4 = $mysqli->query("SELECT * FROM diplomacy WHERE attackcountry1='$militaryunitcamp' OR attackcountry2='$militaryunitcamp'") or die($mysqli->error());
						$row4 = mysqli_fetch_array($result4);
						$countwars = $result4->num_rows;
						
						if($countwars == 0){
							date_default_timezone_set('UTC'); //current date
							$datecur = date("Y-m-d H:i:s"); 
							
							$sql = "INSERT INTO diplomacy (type, country1, country2, attackcountry1, attackcountry1start,acceptnap) " 
								. "VALUES ('resistance','$usercharacterid', '$holderid', '$militaryunitcamp', NOW(),'2')";
							mysqli_query($mysqli, $sql);
							
							$sql = "DELETE FROM claim WHERE id='$claimid'";
							mysqli_query($mysqli, $sql);
							
							$content= "<a href='account.php?user=$username&charid=$usercharacterid'>$charactername $characterfamilyname</a> started a resistance war in the duchy of $titlename";
							$content=$mysqli->escape_string($content);
							$sql = "INSERT INTO events (date, content) " 
						     . "VALUES (NOW(),'$content')";
							mysqli_query($mysqli2, $sql);	
						}else{
							echo'<div class="boxed">You can not press this claim while a war is in progress in this duchy!</div>';
						}
							
					}else{
						echo'<div class="boxed">You can not press this claim while the owner of this duchy is your vassal!</div>';
					}
				}
			}else{
				echo'<div class="boxed">You need a military unit to set up a camp in the region to press this claim!</div>';
			}
		}elseif($claimtype == "imprison"){
			$result = $mysqli->query("SELECT * FROM characters WHERE id='$claimtitleid'") or die($mysqli->error());
			$row2 = mysqli_fetch_array($result);
			$targetid = $row2['id'];
			$targetname = $row2['name'];
			$targetfamilyid = $row2['familyid'];
			$targetuser = $row2['user'];
			$targetlocation = $row2['location'];
			$targetlocation2 = $row2['location2'];
			
			$result4 = $mysqli->query("SELECT * FROM traits WHERE name='imprisoned'") or die($mysqli->error());
			$row4 = mysqli_fetch_array($result4);
			$imprisonedtraitid = $row4['id'];
			
			$result3 = "SELECT * FROM traitscharacters WHERE characterid='$claimtitleid' AND traitid='$imprisonedtraitid'";
			$rs_result2 = $mysqli->query($result3);
			$count2 = $rs_result2->num_rows;//aantal titles
			
			if($count2 == 0){
				if($characterlocation == $targetlocation AND $characterlocation2 == $targetlocation2){
					$sql = "INSERT INTO traitscharacters (characterid, traitid, date, extrainfo) " 
					. "VALUES ('$targetid','$imprisonedtraitid',NOW(),'$usercharacterid')";
			 		mysqli_query($mysqli, $sql);
					
					$content= "Your character has been imprisoned by <a href='account.php?user=$username&charid=$usercharacterid'>$charactername $characterfamilyname</a>.";
					seteventuser($content,$targetuser);
					
					$sql = "DELETE FROM claim WHERE id='$claimid'";
					mysqli_query($mysqli, $sql);
					
					echo'<div class="boxed">Done!</div>';
				}else{
					echo'<div class="boxed">You need to be in the same location to imprison someone!</div>';
				}
			}else{
				echo'<div class="boxed">This character is already imprisoned!</div>';
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

//promote member military unit
if(isset($_POST['promote'])){
	$id = $mysqli->escape_string($_POST['id']);
	$name = $mysqli->escape_string($_POST['name']);
	
	$result2 = $mysqli->query("SELECT id FROM militaryunit WHERE id='$id' AND owner='$username'") or die($mysqli->error());
	$count = $result2->num_rows;
	if($count != 0){
		$result = $mysqli->query("SELECT gold FROM militaryunit WHERE id='$id' AND owner='$username'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$gold=$row['gold'];
		
		$result = $mysqli->query("SELECT * FROM users WHERE username='$name' AND militaryunit='$id'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$militaryunitrank=$row['militaryunitrank'];
		
		$result2 = $mysqli->query("SELECT * FROM users WHERE username='$name' AND militaryunit='$id'") or die($mysqli->error());
		$count = $result2->num_rows;
		if($count != 0){
			$gold=$gold-5;
			if($gold>=0){
				$militaryunitrank=$militaryunitrank+1;
				
				$sql = "UPDATE militaryunit SET gold ='$gold' WHERE id='$id'";
				mysqli_query($mysqli, $sql);
				
				$sql = "UPDATE users SET militaryunitrank ='$militaryunitrank' WHERE username='$name'";
				mysqli_query($mysqli, $sql);
				
				echo nl2br ("<div class=\"boxed\">Done!</div>");
			}else{
				echo nl2br ("<div class=\"boxed\">The military unit doesn't have enough gold!</div>");
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

//promote member religion
if(isset($_POST['promotereligion'])){
	$name = $mysqli->escape_string($_POST['name']);
	
	$result = $mysqli->query("SELECT * FROM users WHERE username='$name'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$promoteuserorder=$row['religionorder'];
	$promoterank=$row['orderrank'];
	
	$result = $mysqli->query("SELECT * FROM users WHERE username='$username'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$userorder=$row['religionorder'];
	
	if($promoteuserorder==$userorder){
		$result = $mysqli->query("SELECT * FROM religion WHERE id='$userorder'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$ordergold=$row['gold'];
		$orderleader=$row['leader'];
		
		if($orderleader==$username){
			if($promoterank<2){
				$ordergold=$ordergold-5;
				$promoterank=$promoterank+1;
				
				if($ordergold >=0){
					$sql = "UPDATE religion SET gold ='$ordergold' WHERE id='$userorder'";
					mysqli_query($mysqli, $sql);
					
					$sql = "UPDATE users SET orderrank ='$promoterank' WHERE username='$name'";
					mysqli_query($mysqli, $sql);
					
					echo'<div class="boxed">Done!</div>';
				}else{
					echo'<div class="boxed">The order does not have enough gold!</div>';
				}
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

if(isset($_POST['changeorder'])){
	$candidate = $mysqli->escape_string($_POST['candidate']);
	$neworder = $mysqli->escape_string($_POST['neworder']);
	$neworder = (int) $neworder;
	if($neworder <= 0){
		$neworder = 1;
	}
	
	$result = $mysqli->query("SELECT * FROM users WHERE username='$username'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$politicalparty=$row['politicalparty'];
	
	$result = $mysqli->query("SELECT * FROM users WHERE username='$candidate'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$politicalpartycand=$row['politicalparty'];
	if($politicalparty == $politicalpartycand){
		$sql = "UPDATE elections SET electorder='$neworder' WHERE candidate='$candidate' AND type='congress'";
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

//show numbers of page
$i=1; if($i < 1){$i=1;}
echo "<a href='rankings.php?type=$type&country=$country&sort=$sort&order=$order&page=$i'><< </a>";

$i=$page-1; if($i < 1){$i=1;}
echo "<a href='rankings.php?type=$type&country=$country&sort=$sort&order=$order&page=$i'>< </a>";

for ($i=1; $i<=$total_pages; $i++) {  // print links for all pages
		    $showpages=5;
			if($i > ($page-5) AND $i < ($page+5)){
	            echo "<a href='rankings.php?type=$type&country=$country&sort=$sort&order=$order&page=".$i."'";
	            if ($i==$page)  echo " class='curPage'";
	            echo ">".$i."</a> "; 
			}
};

$i=$page+1; if($i < 1){$i=1;}
echo "<a href='rankings.php?type=$type&country=$country&sort=$sort&order=$order&page=$i'>> </a>";

$i=$total_pages; if($i < 1){$i=1;}
echo "<a href='rankings.php?type=$type&country=$country&sort=$sort&order=$order&page=$i'>>> </a>";

?>
</div>
</body>
<footer>
<?php require 'bottombar.php'; ?>
</footer>
</html>
