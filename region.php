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
$regionrep=$_GET["region"];
$region=$mysqli->escape_string($regionrep);
if (isset($_GET["show"])) { $show  = $_GET["show"]; } else { $show=0; };
$show=$mysqli->escape_string($show);

$integer = is_numeric($regionrep);
if($integer == "true"){
	$result = $mysqli->query("SELECT name FROM region WHERE id = '$region'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$regionrep=$row['name'];
	$region=$mysqli->escape_string($regionrep);
}

?> <div class="everythingOnOneLine"> <?php
	?> <div class="flexcontainer"> <?php
		?> <div class="h1"> <?php echo "$regionrep"; ?> </div> <?php	 
	?> </div> <?php

//echo ("<div class=\"h1\">$country</div>");

//search country
?>
<form method="post" action=""> 
	<?php
	$result = mysqli_query($mysqli,"SELECT name FROM region");
	$columnValues = Array();
	
	while ( $row = mysqli_fetch_assoc($result) ) {
	  $columnValues[] = $row['name'];
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
	<button type="submit" name="search" />Search region</button>
</form>
</div>
<?php

//choose which information to show
?> 
<div class="everythingOnOneLine">
<form method="post" action="">
	<button type="submit" name="general" />General info</button>
	<!--<button type="submit" name="political" />Political view</button>
	<button type="submit" name="economic" />Economic view</button>-->
</form>
</div> 
<?php

if(isset($_POST['general'])){
	?>
	<script>
		var val = "<?php echo $region ?>"
	    window.location = 'region.php?region='+val+'&show=0';
	</script>
	<?php
}
/*if(isset($_POST['political'])){
	?>
	<script>
		var val = "<?php echo $region ?>"
	    window.location = 'region.php?region='+val+'&show=1';
	</script>
	<?php
}
if(isset($_POST['economic'])){
	?>
	<script>
		var val = "<?php echo $region ?>"
	    window.location = 'region.php?region='+val+'&show=2';
	</script>
	<?php
}*/
?> <hr /> <?php

//show country president link
$result = $mysqli->query("SELECT * FROM region WHERE name='$region'") or die($mysqli->error());
$row = mysqli_fetch_array($result);
$regionid=$row['id'];
$currowner=$row['curowner'];
$biggestrel=$row['biggestrel'];
$climate=$row['climate'];
$currtemp=$row['currtemp'];
$currweather=$row['currweather'];

if($show==0){ //general info
	?>
	<?php echo nl2br ("<div class=\"bold\">General info</div>"); ?>
	<table id="table1">
	    <tr>
	       <td><?php echo "Region"; ?></td>
	       <td><?php echo "$region"; ?></td>
	    </tr>	
	    <tr>
	       <td><?php echo "Owner"; ?></td>
	       <td><?php echo "$currowner"; ?></td>
	    </tr>	
	    <tr>
	       <td><?php echo "Climate"; ?></td>
			<td><?php echo "$climate"; ?></td>
	    </tr>	
	    <tr>
	       <td><?php echo "Current temperature"; ?></td>
	       <td><?php echo "$currtemp"; ?></td>
	    </tr>	
	    <tr>
	       <td><?php echo "Current weather"; ?></td>
	       <td><?php echo "$currweather"; ?></td>
	    </tr>	
	</table>
	
	<?php
	//print grafiek met population
	$datearray=array();
	$waardearray=array();
	
	$result2 = $mysqli2->query("SELECT * FROM statistics WHERE type='regiontemperature' AND name='$regionid'") or die($mysqli->error());
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
		          label: "Temperature",
		          borderColor: "#3e95cd",
		          data: <?php echo $waardearray2 ?>
		        }
		      ]
		    },
		    options: {
		      legend: { display: false },
		      title: {
		        display: true,
		        text: 'Temperature'
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
	
}/*elseif($show==1){ //politics
	echo nl2br ("<div class=\"bold\">Political view</div>");
	?>
	<table id="table1">
	    <tr>
	    	<th>
	    	<div id="block_container">
				<?php if($government == 1){
					echo "Country president: <a href='account.php?user=$countrypresident'>$countrypresident</a>";
				}elseif($government == 2){
					echo "King: <a href='account.php?user=$countrypresident'>$countrypresident</a>";
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
}*/

?> <hr /> <?php

if(isset($_POST['search'])){
	$search = $mysqli->escape_string($_POST['countrysearch']);
	
	?>
	<script>
		var val = "<?php echo $search ?>"
	    window.location = 'region.php?region='+val;
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
