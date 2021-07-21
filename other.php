<?php 
/* Main page with two forms: sign up and log in */
//require 'navigationbar.php';
require 'db.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

?>

<!DOCTYPE html>
<html>
<head>
<div class="flowercastle-image">
  <title>Fantasy-Sim</title>
  <meta name="description" content="Other projects">
  <meta name="keywords" content="">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/styletot.css">
   <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
   <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
   <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
   <link rel="manifest" href="/site.webmanifest">
   <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#add8e6">
   <meta name="msapplication-TileColor" content="#add8e6">
   <meta name="theme-color" content="#ffffff">
  <script src='https://www.google.com/recaptcha/api.js'></script> 
	

  
	</head>
	
	<?php 
	
	?>
	<body>
	<div class="boxedtot">
	
	<div class="everythingOnOneLine2"> <?php
		?> <div class="flexcontainer"> <?php
			?> <div class="h1"> <?php echo "Portfolio"; ?> </div> <?php
				?>
				<div class="notificationbox2">
					<div class="notificationbox3">
						<a href="https://www.fantasy-sim.com">
						<img src="img/chaticon.png">
						</a>
					</div>
				</div>
				<?php
			 
		?> </div> <?php
	?> </div> <?php
	?> <hr />

	<table id="table1">
		<tr>
		    <th> Project name</th>
		    <th> Requirements</th>
		    <th> Description</th>
        <th> Github</th>
		</tr>
		
		<?php $description = "CAS is a tool that allows to place different cellular automata on a single grid"; ?>
		<?php $description2 = "Dckingdoms is a discord bot written in python to transform your discord server into a kingdoms server. "; ?>
    
		<tr>
		    <td> <a href="https://top.gg/bot/790973205799632906" target="_blank">Cellular Automata Simulator</a></th>
		    <td> Java 8.0 or higher</th>
		    <td> <?php echo "$description"; ?></td>
        <td> <a href="https://github.com/lwaw/Cellular_Automata_Simulator" target="_blank">Github repository</a></td>
		</tr>
		<tr>
		    <td> <a href="https://top.gg/bot/790973205799632906" target="_blank">Dckingdoms</a></th>
		    <td> Discord server</th>
		    <td> <?php echo "$description2"; ?></td>
        <td> <a href="https://github.com/lwaw/dckingdoms" target="_blank">Github repository</a></td>
		</tr>
                
	</table>
	
	</body>
</div>
<footer>
</footer>
</html>
