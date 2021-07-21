<?php 
require 'navigationbar.php';
require 'db.php';
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
$countryprep=$_GET["country"];
$country=$mysqli->escape_string($countryprep);
if (isset($_GET["show"])) { $show  = $_GET["show"]; } else { $show=0; };
$show=$mysqli->escape_string($show);

?> <div class="everythingOnOneLine"> <?php
	?> <div class="flexcontainer"> <?php
		?> <div class="h1"> <?php echo "$country"; ?> </div> <?php
			?>
			<div class="notificationbox2">
				<div class="notificationbox3">											
					<a href="chat.php?type=country">
					<img src="img/chaticon.png">
					</a>
				</div>
			</div>
			<?php
		 
	?> </div> <?php

//echo ("<div class=\"h1\">$country</div>");

//search country
?>
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
	<select required name="countrysearch" type="text">
		<?php	        
		// Iterating through the product array
		foreach($columnValues as $item){
			?>
		 	<option value="<?php echo strtolower($item); ?>"><?php echo $item; ?></option>
		    <?php
	    }
	    ?>
	</select> 
	<button type="submit" name="search" />Search country</button>
</form>
</div>
<?php

//choose which information to show
?> 
<div class="everythingOnOneLine">
<form method="post" action="">
	<button type="submit" name="general" />General info</button>
	<button type="submit" name="political" />Political view</button>
	<button type="submit" name="economic" />Economic view</button>
</form>
</div> 
<?php

if(isset($_POST['general'])){
	?>
	<script>
		var val = "<?php echo $country ?>"
	    window.location = 'country.php?country='+val+'&show=0';
	</script>
	<?php
}
if(isset($_POST['political'])){
	?>
	<script>
		var val = "<?php echo $country ?>"
	    window.location = 'country.php?country='+val+'&show=1';
	</script>
	<?php
}
if(isset($_POST['economic'])){
	?>
	<script>
		var val = "<?php echo $country ?>"
	    window.location = 'country.php?country='+val+'&show=2';
	</script>
	<?php
}
?> <hr /> <?php

//show country president link
$result = $mysqli->query("SELECT * FROM countryinfo WHERE country='$country'") or die($mysqli->error());
$row = mysqli_fetch_array($result);
$countrypresident=$row['countrypresident'];
$finance=$row['finance'];
$foreignaffairs=$row['foreignaffairs'];
$immigration=$row['immigration'];
$gold=$row['gold'];
$vat=$row['vat'];
$worktax=$row['worktax'];
$immigrationtax=$row['immigrationtax'];
$money=$row['money'];
$currency=$row['currency'];
$government=$row['government'];
$statereligion = $row['statereligion'];
$citizens = $row['citizens'];
$treasurygold = $row['treasurygold'];
$treasurymoney = $row['treasurymoney'];
$characterowner = $row['characterowner'];

if($characterowner != 0){
	$result = $mysqli->query("SELECT * FROM characters WHERE id='$characterowner'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$charactername=$row['name'];
	$characterfamilyid=$row['familyid'];
	
	$result = $mysqli->query("SELECT * FROM family WHERE id='$characterfamilyid'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$characterfamilyname=$row['name'];
	
	$monarch = "$charactername $characterfamilyname";
}else{
	$monarch = "None";
}

$result = $mysqli->query("SELECT * FROM users WHERE username='$username'") or die($mysqli->error());
$row = mysqli_fetch_array($result);
$congress=$row['congressmember'];

$result = $mysqli->query("SELECT name FROM region WHERE curowner='$country'") or die($mysqli->error());
for ($set=array(); $row=$result->fetch_assoc(); $set[]=$row);

$result2 = $mysqli->query("SELECT username FROM users WHERE congressmember='$country'") or die($mysqli->error());
for ($set2=array(); $row2=$result2->fetch_assoc(); $set2[]=$row2);


if($show==0){ //general info
	?>
	<?php echo nl2br ("<div class=\"bold\">General info</div>"); ?>
	<table id="table1">
	    <tr>
	       <td><?php echo "Country"; ?></td>
	       <td><?php echo "$country"; ?></td>
	    </tr>	
	    <tr>
	       <td><?php echo "Number of citizens"; ?></td>
	       <td><?php echo "$citizens"; ?></td>
	    </tr>	
	    <tr>
	       <td><?php echo "Regions under control"; ?></td>
	       <td> 
	           <?php
				foreach ($set as $key => $value) {
					$region[$key] = $value['name'];
					echo " $region[$key] |";
				}
	           ?>
	       </td>
	    </tr>	
	    <tr>
	       <td><?php echo "State religion"; ?></td>
	       <td><?php echo "$statereligion"; ?></td>
	    </tr>		
	</table>
	
	<?php
	//print grafiek met population
	$datearray=array();
	$waardearray=array();
	
	$result2 = $mysqli2->query("SELECT * FROM statistics WHERE type='countrycitizens' AND name='$country'") or die($mysqli->error());
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
		          label: "Population",
		          borderColor: "#3e95cd",
		          data: <?php echo $waardearray2 ?>
		        }
		      ]
		    },
		    options: {
		      legend: { display: false },
		      title: {
		        display: true,
		        text: 'Population'
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
	
}elseif($show==1){ //politics
	echo nl2br ("<div class=\"bold\">Political view</div>");
	?>
	<table id="table1">
	    <tr>
	    	<th>
	    	<div id="block_container">
				<?php if($government == 1){
					echo "Monarch: <a href='account.php?user=$countrypresident&charid=$characterowner'>$monarch</a>";
				}elseif($government == 2){
					echo "Monarch: <a href='account.php?user=$countrypresident&charid=$characterowner'>$monarch</a>";
				}?>
			</div>
			</th>
	    </tr>
	</table>
	<table id="table1">	
	    <tr>
	       <td><?php if($finance!="NULL"){ echo "Minister of finance: <a href='account.php?user=$finance'>$finance</a>"; }else{ echo "Minister of finance: None";} ?></td>
	       <td><?php if($foreignaffairs!="NULL"){ echo "Minister of foreign affairs: <a href='account.php?user=$foreignaffairs'>$foreignaffairs</a>"; }else{ echo "Minister of foreign affairs: None";} ?></td>
	       <td><?php if($immigration!="NULL"){ echo "Minister of immigration: <a href='account.php?user=$immigration'>$immigration</a>"; }else{ echo "Minister of immigration: None";} ?></td>
	    </tr>			
	</table>
	<table id="table1">	
       <?php
       $i=0;
	   ?> <tr> <?php
			foreach ($set2 as $key => $value2) {
				$congressmember[$key] = $value2['username'];
				//echo " $congressmember[$key] |";
				
					if($i<=5){
						?>
						<td><?php echo "Congress member: <a href='account.php?user=$congressmember[$key]'>$congressmember[$key]</a>"; ?></td>
						<?php
					}
				$i=$i+1;
			 }
		?> </tr> <?php
		$i=0;
		?> <tr> <?php
			foreach ($set2 as $key => $value2) {
				$congressmember[$key] = $value2['username'];
				//echo " $congressmember[$key] |";
				?> <tr> <?php
					if($i>=6){
						?>
						<td><?php echo "Congress member: <a href='account.php?user=$congressmember[$key]'>$congressmember[$key]</a>"; ?></td>
						<?php
					}
				?> </tr> <?php
				$i=$i+1;
			}
		?> </tr> <?php
        ?>
	</table>
	<?php
}elseif($show==2){
	echo nl2br ("<div class=\"bold\">Economic view</div>");
	
	?>
	<table id="table1">	
	    <tr>
	       <th><?php echo "National currency: $currency"; ?></th>
	    </tr>			
	</table>
	<table id="table1">	
	    <tr>
	    	<th><?php echo "Currency"; ?></th>
	       <th><?php echo "gold"; ?></th>
	       <th><?php echo "$currency"; ?></th>
	    </tr>
	    <tr>
	    	<td><?php echo "Disposable"; ?></td>
	       <td><?php echo "$gold"; ?></td>
	       <td><?php echo "$money"; ?></td>
	    </tr>	
	    <tr>
	    	<td><?php echo "Treasury"; ?></td>
	       <td><?php echo "$treasurygold"; ?></td>
	       <td><?php echo "$treasurymoney"; ?></td>
	    </tr>			
	</table>
	<table id="table1">	
	    <tr>
	    	<th><?php echo "Taxes"; ?></th>
	       <th><?php echo "%"; ?></th>
	    </tr>
	    <tr>
	    	<td><?php echo "Work tax"; ?></td>
	       <td><?php echo "$worktax"; ?></td>
	    </tr>	
	    <tr>
	    	<td><?php echo "VAT"; ?></td>
	       <td><?php echo "$vat"; ?></td>
	    </tr>
	    <tr>
	    	<td><?php echo "Immigration tax"; ?></td>
	       <td><?php echo "$immigrationtax"; ?></td>
	    </tr>	
	</table>
	
	<?php
	//print grafiek met tax income
	$datearray=array();
	$waardearray=array();
	
	$result2 = $mysqli2->query("SELECT * FROM statistics WHERE type='countrytax' AND name='$country'") or die($mysqli->error());
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
		          label: "Local currency",
		          backgroundColor: "#3e95cd",
		          data: <?php echo $waardearray2 ?>
		        }
		      ]
		    },
		    options: {
		      legend: { display: false },
		      title: {
		        display: true,
		        text: 'Tax income'
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

?> <hr /> <?php
//donate to country
?>
<form method="post" action="">
	<button type="submit" name="viewcountryrank" />View country rankings</button>
	<button type="submit" name="viewregionrank" />View region rankings</button>
	<button type="submit" name="donate" />Donate gold to country</button>
	<?php if($countrypresident==$username){?> <button type="submit" name="viewcountrypres" />Country president page</button> <?php } ?>
	<?php if($finance==$username){?> <button type="submit" name="viewminfinance" />Country minister of finance page</button> <?php } ?>
	<?php if($foreignaffairs==$username){?> <button type="submit" name="viewminforeignaffairs" />Minister of foreign affairs page</button> <?php } ?>
	<?php if($immigration==$username){?> <button type="submit" name="viewminimmigration" />Minister of immigration page</button> <?php } ?>
	<?php if($congress==$country OR $countrypresident==$username OR $finance==$username OR $foreignaffairs==$username OR $immigration==$username){?> <button type="submit" name="viewcongress" />Congress page</button> <?php } ?>
</form>
<?php

if(isset($_POST['viewcountryrank'])){
	?>
	<script>
	    window.location = 'rankings.php?type=country&sort=country&order=asc';
	</script>
	<?php
}
if(isset($_POST['viewregionrank'])){
	?>
	<script>
	    window.location = 'rankings.php?type=region&sort=name&order=asc';
	</script>
	<?php
}
if(isset($_POST['viewcountrypres'])){
	?>
	<script>
	    window.location = 'countrypresident.php';
	</script>
	<?php
}
if(isset($_POST['viewminfinance'])){
	?>
	<script>
	    window.location = 'ministeroffinance.php?market=false';
	</script>
	<?php
}
if(isset($_POST['viewminforeignaffairs'])){
	?>
	<script>
	    window.location = 'ministerofforeignaffairs.php';
	</script>
	<?php
}
if(isset($_POST['viewminimmigration'])){
	?>
	<script>
	    window.location = 'ministerofimmigration.php';
	</script>
	<?php
}
if(isset($_POST['viewcongress'])){
	?>
	<script>
	    window.location = 'congress.php';
	</script>
	<?php
}

if(isset($_POST['donate'])){
	?>
	<form method="post" action="">
		<input type="hidden" name="country" value="<?php echo "$country "; ?>" />
		<input type="number" size="25" required autocomplete="off" id="percentuser" name="amount" placeholder="Amount of gold" min="0" step="0.01" />
		<button type="submit" name="donate2" /><?php echo "Donate"; ?></button>
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

if(isset($_POST['donate2'])){
	$country = $mysqli->escape_string($_POST['country']);
	$amount = $mysqli->escape_string($_POST['amount']);
	
	$result = $mysqli->query("SELECT * FROM countryinfo WHERE country='$country'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$countrygold=$row['gold'];
	
	$result = $mysqli->query("SELECT * FROM currency WHERE usercur='$username'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$usergold=$row['gold'];
	
	$usergold=$usergold-$amount;
	$countrygold=$countrygold+$amount;
	
	if($usergold >= 0){
		$sql = "UPDATE currency SET gold='$usergold' WHERE usercur='$username'";
		mysqli_query($mysqli, $sql);
		
		$sql = "UPDATE countryinfo SET gold='$countrygold' WHERE country='$country'";
		mysqli_query($mysqli, $sql);
		
		echo nl2br("<div class=\"boxed\">Done!</div>");
		
		?>
		<script>
		    if ( window.history.replaceState ) {
		        window.history.replaceState( null, null, window.location.href );
		    }
		</script>
		<?php
	}else{
		echo nl2br("<div class=\"boxed\">You don't have enough gold!</div>");
		
		?>
		<script>
		    if ( window.history.replaceState ) {
		        window.history.replaceState( null, null, window.location.href );
		    }
		</script>
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

if(isset($_POST['search'])){
	$search = $mysqli->escape_string($_POST['countrysearch']);
	
	$result = $mysqli->query("SELECT * FROM countryinfo WHERE country='$search'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$countrypresident=$row['countrypresident'];
	$finance=$row['finance'];
	$foreignaffairs=$row['foreignaffairs'];
	$immigration=$row['immigration'];
	$gold=$row['gold'];
	$vat=$row['vat'];
	$worktax=$row['worktax'];
	$immigrationtax=$row['immigrationtax'];
	$money=$row['money'];
	$currency=$row['currency'];
	$government=$row['government'];
	$statereligion = $row['statereligion'];
	$citizens = $row['citizens'];
	
	$result = $mysqli->query("SELECT username FROM users WHERE nationality='$search'") or die($mysqli->error());
	$count = $result->num_rows;
	
	$result = $mysqli->query("SELECT name FROM region WHERE curowner='$search'") or die($mysqli->error());
	for ($set=array(); $row=$result->fetch_assoc(); $set[]=$row);
	
	if($countrypresident==$username){
		?> <p><a href="countrypresident.php">Country president</a><br> <?php
	}
	
	echo nl2br("Country: $search \n");
	echo nl2br("Number of citizens: $citizens \n");
	echo nl2br("Regions under control of $search:");
	foreach ($set as $key => $value) {
		$region[$key] = $value['name'];
		echo " $region[$key] |";
	}
	echo nl2br("\n");
	if($government==1){
		echo nl2br("Government type: democracy \n");
		echo nl2br("President: $countrypresident | Minister of finance: $finance | Minister of foreign affairs: $foreignaffairs | Minister of immigration: $immigration \n");
	}elseif($government==2){
		echo nl2br("Government type: monarchy \n");
		echo nl2br("King: $countrypresident \n");
	}
	echo nl2br("State religion: $statereligion \n");
	echo nl2br("Treasury: $currency, $money | gold, $gold \n");
	echo nl2br("Taxes: vat, $vat | worktax, $worktax | immigrationtax, $immigrationtax\n");
	
	?>
	<script>
		var val = "<?php echo $search ?>"
	    window.location = 'country.php?country='+val;
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
