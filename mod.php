<?php 
require 'navigationbar.php';
require 'db.php';
require 'regionborders.php';
require_once 'purifier/library/HTMLPurifier.auto.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/* Displays user information and some useful messages */
//session_start();

// Check if user is logged in using the session variable
if ($_SESSION['logged_in'] != 1 ) {
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
  <link rel="stylesheet" href="css/styletot.css">
  

</head>
<body>
<div class="boxedtot">
<?php
if (isset($_GET["show"])) { $show  = $_GET["show"]; } else { $show=0; };
$show=$mysqli->escape_string($show);

if (isset($_GET["page"])) { $page  = $_GET["page"]; } else { $page=1; };
$page=$mysqli->escape_string($page);
$results_per_page=20;
$start_from = ($page-1) * $results_per_page;

$result = $mysqli->query("SELECT * FROM users WHERE username='$username'") or die($mysqli->error());
$row = mysqli_fetch_array($result);
$moderator=$row['moderator'];

if($moderator==1 || $moderator==2 || $moderator==5 || $moderator==4){
	?> <div class="textbox"> <?php
	echo nl2br ("<div class=\"h1\">Moderator page</div>");
	?> <hr /> <?php
	
	?>
	<form method="post" action="">
		<button type="submit" name="showreport" />Show forum reports</button>
		<button type="submit" name="showchatreport" />Show chat reports</button>
	</form>
	<?php
	
	if(isset($_POST['showreport']) || $show==1){
		$sql = "SELECT * FROM report ORDER BY date DESC LIMIT $start_from, ".$results_per_page;
		$rs_result = $mysqli2->query($sql);
		
		?> 
		<div class="scroll">
		<table id="table1">
		<tr>
		    <th> Date</th>
		    <th> link</th>
		    <th> topic</th>
		    <th> post</th>
		    <th> Reporter</th>
		    <th> Reason</th>
		    <th> Solve</th>
		</tr>
		<?php 
		while($row = $rs_result->fetch_assoc()) {
			$id=$row["id"];
			$date=$row["date"];
			$topic=$row["topic"];
			$post=$row["post"];
			$user=$row["user"];
			$reason=$row["reason"];
			$reason = $purifier->purify($reason);
			$page2=$row["page"];
			?> 
	           <tr>
	           <td >
	           		<?php echo "$date"; ?>
	           </td>
	           <td >
	           		<?php echo "<a href='forum.php?topic=$topic&page=$page2&category=1'>View </a>";?>
	           </td>
	           <td >
	           		<?php echo "$topic"; ?>
	           </td>
	           <td >
	           		<?php echo "$post"; ?>
	           </td>
	           <td >
	           		<?php echo "$user"; ?>
	           </td>
	           <td >
	           		<?php echo "$reason"; ?>
	           </td>
	           <td >
				<form method="post" action="">
					<input type="hidden" name="id" value="<?php echo $id; ?>" />
					<button type="submit" name="solve" />Solve</button>
				</form>
	           </td>
	           </tr>
			<?php 
			
		}; 
		?> 
		</table>
		</div>
		<?php 
		
		$sql = "SELECT COUNT(id) AS total FROM report";
		$result = $mysqli2->query($sql);
		$row = $result->fetch_assoc();
		$total_pages = ceil($row["total"] / $results_per_page); // calculate total pages with results	
		
		for ($i=1; $i<=$total_pages; $i++) {  // print links for all pages
		            echo "<a href='mod.php?show=1&page=".$i."'";
		            if ($i==$page)  echo " class='curPage'";
		            echo ">".$i."</a> "; 
		};
		?>
		<script>
		    if ( window.history.replaceState ) {
		        window.history.replaceState( null, null, window.location.href );
		    }
		</script>
		<?php
	}

	if(isset($_POST['solve'])){
		$id = $mysqli->escape_string($_POST['id']);
		
		$sql = "DELETE FROM report WHERE id='$id'";
		mysqli_query($mysqli2, $sql);
		
		echo'<div class="boxed">Done!</div>';
		
		?>
		<script>
		    if ( window.history.replaceState ) {
		        window.history.replaceState( null, null, window.location.href );
		    }
		    document.location = "mod.php?show=1";
		</script>
		<?php
	}
	
	if(isset($_POST['showchatreport'])){
		$results_per_page2=10;
		$start_from2=0;
		$sql = "SELECT * FROM chat WHERE report='1' ORDER BY date DESC LIMIT $start_from2, ".$results_per_page2;
		$rs_result = $mysqli2->query($sql);
		
		?> 
		<div class="scroll">
		<table id="table1">
		<tr>
		    <th> Date</th>
		    <th> user</th>
		    <th> post</th>
		    <th> Reporter</th>
		    <th> Delete</th>
		    <th> Solve</th>
		</tr>
		<?php 
		while($row = $rs_result->fetch_assoc()) {
			$id=$row["id"];
			$date=$row["date"];
			$content=$row["content"];
			$user=$row["user"];
			$reporter=$row["reporter"];
			$content = $purifier->purify($content);
			?> 
	           <tr>
	           <td >
	           		<?php echo "$date"; ?>
	           </td>
	           <td >
	           		<?php echo "$user"; ?>
	           </td>
	           <td >
	           		<?php echo "$content"; ?>
	           </td>
	           <td >
	           		<?php echo "$reporter"; ?>
	           </td>
	           <td >
				<form method="post" action="">
					<input type="hidden" name="id" value="<?php echo $id; ?>" />
					<button type="submit" name="deletechat" />Delete</button>
				</form>
	           </td>
	           <td >
				<form method="post" action="">
					<input type="hidden" name="id" value="<?php echo $id; ?>" />
					<button type="submit" name="solvechat" />Solve</button>
				</form>
	           </td>
	           </tr>
	          <?php } ?>
		</table>
		</div>
		
		<script>
		    if ( window.history.replaceState ) {
		        window.history.replaceState( null, null, window.location.href );
		    }
		</script>
		<?php
	}

	if(isset($_POST['deletechat'])){
		$id = $mysqli->escape_string($_POST['id']);
		
		$sql = "DELETE FROM chat WHERE id='$id'";
		mysqli_query($mysqli2, $sql);
		
		echo'<div class="boxed">Done!</div>';
		
		?>
		<script>
		    if ( window.history.replaceState ) {
		        window.history.replaceState( null, null, window.location.href );
		    }
		    document.location = "mod.php?show=0";
		</script>
		<?php
	}
	
	if(isset($_POST['solvechat'])){
		$id = $mysqli->escape_string($_POST['id']);
		
		$sql = "UPDATE chat SET report='0' WHERE id='$id'";
		mysqli_query($mysqli2, $sql);
		
		echo'<div class="boxed">Done!</div>';
		
		?>
		<script>
		    if ( window.history.replaceState ) {
		        window.history.replaceState( null, null, window.location.href );
		    }
		    document.location = "mod.php?show=0";
		</script>
		<?php
	}
	
	
	?> </div> <?php
}else{
	echo "You are not a moderator!";

}

if($moderator==1 || $moderator==2 || $moderator==5){
	?>
	<form method="post" action="">
		<button type="ban" name="ban" />Ban user</button>
		<button type="banad" name="banad" />Remove advertisement</button>
	</form>
	<?php
	
	if(isset($_POST['ban'])){
		?>
		<form method="post" action="">
		  <input type="text" name="user" placeholder="user">
		  <select required name="time" type="text">  
		  		<option value="" disabled selected hidden>Choose A time</option>   
				<option value="week">1 week</option>
				<option value="month">1 month</option>
				<option value="year">1 year</option>
				<option value="game">gameban</option>
				<?php if($moderator==2 || $moderator==5){ ?>
					<option value="perma">perma</option>
				<?php } ?>
		  </select>
		  <textarea rows="4" cols="50" name="reason" maxlength="500">Enter reason here...</textarea>
			<button type="submit" name="ban2" />Ban user</button>
		</form>
		<?php
	}
	
	if(isset($_POST['ban2'])){
		$user = $mysqli->escape_string($_POST['user']);
		$time = $mysqli->escape_string($_POST['time']);
		$reason = $mysqli->escape_string($_POST['reason']);
		
		$game=0;
				
		$result2 = $mysqli->query("SELECT id FROM users WHERE username='$user'") or die($mysqli->error());
		$count = $result2->num_rows;
		if($count != 0){
			$result = $mysqli->query("SELECT * FROM users WHERE username='$user'") or die($mysqli->error());
			$row = mysqli_fetch_array($result);
			$moderator = $row['moderator'];
			
			if($moderator == 0){
				$result2 = $mysqli->query("SELECT * FROM ban WHERE user='$user'") or die($mysqli->error());
				$count = $result2->num_rows;
				if($count != 0){
					$result = $mysqli->query("SELECT * FROM ban WHERE user='$user'") or die($mysqli->error());
					$row = mysqli_fetch_array($result);
					$id = $row['id'];
					$date1 = $row['date'];
					
					if($time=="week"){
						//echo date('Y-m-d H:i:s', strtotime($date1. ' + 1 days'));
						$date = new DateTime($date1);
						$date->add(new DateInterval('P7D')); // P1D means a period of 1 day
						$Datenew1 = $date->format('Y-m-d H:i:s');
					}elseif($time=="month"){
						//echo date('Y-m-d H:i:s', strtotime($date1. ' + 1 days'));
						$date = new DateTime($date1);
						$date->add(new DateInterval('P1M')); // P1D means a period of 1 day
						$Datenew1 = $date->format('Y-m-d H:i:s');
					}elseif($time=="year"){
						//echo date('Y-m-d H:i:s', strtotime($date1. ' + 1 days'));
						$date = new DateTime($date1);
						$date->add(new DateInterval('P1Y')); // P1D means a period of 1 day
						$Datenew1 = $date->format('Y-m-d H:i:s');
					}elseif($time=="perma"){
						//echo date('Y-m-d H:i:s', strtotime($date1. ' + 1 days'));
						$date = new DateTime($date1);
						$date->add(new DateInterval('P10Y')); // P1D means a period of 1 day
						$Datenew1 = $date->format('Y-m-d H:i:s');
						
						$game=1;
					}elseif($time=="game"){
						//echo date('Y-m-d H:i:s', strtotime($date1. ' + 1 days'));
						$date = new DateTime($date1);
						$date->add(new DateInterval('P2D')); // P1D means a period of 1 day
						$Datenew1 = $date->format('Y-m-d H:i:s');
						
						$game=1;
					}
					
					$sql = "UPDATE ban SET date='$Datenew1', game='$game' WHERE id='$id'";
					mysqli_query($mysqli, $sql);
					
			 		echo'<div class="boxed">Done!</div>';
			 		
					?>
					<script>
					    if ( window.history.replaceState ) {
					        window.history.replaceState( null, null, window.location.href );
					    }
					</script>
					<?php
						
				}else{
					if($time=="week"){
						date_default_timezone_set('UTC'); //current date
						$datecur = date("Y-m-d H:i:s"); 
						//echo "$datecur";
						$date = new DateTime($datecur);
						$date->add(new DateInterval('P7D')); // P1D means a period of 1 day
						$Datenew1 = $date->format('Y-m-d H:i:s');
					}elseif($time=="month"){
						date_default_timezone_set('UTC'); //current date
						$datecur = date("Y-m-d H:i:s"); 
						//echo "$datecur";
						$date = new DateTime($datecur);
						$date->add(new DateInterval('P1M')); // P1D means a period of 1 day
						$Datenew1 = $date->format('Y-m-d H:i:s');
					}elseif($time=="year"){
						date_default_timezone_set('UTC'); //current date
						$datecur = date("Y-m-d H:i:s"); 
						//echo "$datecur";
						$date = new DateTime($datecur);
						$date->add(new DateInterval('P1Y')); // P1D means a period of 1 day
						$Datenew1 = $date->format('Y-m-d H:i:s');
					}elseif($time=="perma"){
						date_default_timezone_set('UTC'); //current date
						$datecur = date("Y-m-d H:i:s"); 
						//echo "$datecur";
						$date = new DateTime($datecur);
						$date->add(new DateInterval('P10Y')); // P1D means a period of 1 day
						$Datenew1 = $date->format('Y-m-d H:i:s');
						
						$game=1;
					}elseif($time=="game"){
						//echo date('Y-m-d H:i:s', strtotime($date1. ' + 1 days'));
						$date = new DateTime($date1);
						$date->add(new DateInterval('P2D')); // P1D means a period of 1 day
						$Datenew1 = $date->format('Y-m-d H:i:s');
						
						$game=1;
					}
					
    				$sql = "INSERT INTO ban (date, user, reason, banmod, game) " 
            		. "VALUES ('$Datenew1','$user','$reason','$username','$game')";
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
				echo'<div class="boxed">This user is a moderator!</div>';
			}
		}else{
			echo'<div class="boxed">User does not exist!</div>';
		}
	}

	if(isset($_POST['banad'])){
		?>
		<form method="post" action="">
		  <input type="text" name="id" placeholder="id">
		  <button type="submit" name="banad2" />Ban ad</button>
		</form>
		<?php
	}
	
	if(isset($_POST['banad2'])){
		$id = $mysqli->escape_string($_POST['id']);
		$id = (int) $id;
		
		$result = $mysqli->query("SELECT * FROM politicalparty WHERE id='$id'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$adtext = $row['adtext'];
		$partypresident = $row['partypresident'];
		$country = $row['country'];
		$name = $row['name'];
		
		$sql = "UPDATE politicalparty SET ad='0' WHERE id='$id'";
		mysqli_query($mysqli, $sql);
		
		echo nl2br ("<div class=\"boxed\">Done!  Partypresident: $partypresident | Country: $country | Partyname: $name</div>");
	}
}

if($moderator==2 || $moderator==5){
	?>
	<form method="post" action="">
		<button type="appointmod" name="appointmod" />Appoint moderator</button>
	</form>
	<?php
	
	if(isset($_POST['appointmod'])){
		?>
		<form method="post" action="">
		  <input type="text" name="user" placeholder="user">
		  <select required name="option" type="text">  
		  		<option value="" disabled selected hidden>Choose</option>   
				<option value="grant">Grant</option>
				<option value="retract">Retract</option>
		  </select>
		  <button type="submit" name="appointmod2" />Appoint moderator</button>
		</form>
		<?php
	}
	
	if(isset($_POST['appointmod2'])){
		$user = $mysqli->escape_string($_POST['user']);
		$option = $mysqli->escape_string($_POST['option']);
		
		$result2 = $mysqli->query("SELECT id FROM users WHERE username='$user'") or die($mysqli->error());
		$count = $result2->num_rows;
		if($count != 0){
			if($option=="grant"){
				$result = $mysqli->query("SELECT * FROM users WHERE username='$user'") or die($mysqli->error());
				$row = mysqli_fetch_array($result);
				$moderator2 = $row['moderator'];
				if($moderator2==0){
					$sql = "UPDATE users SET moderator='1' WHERE username='$user'";
					mysqli_query($mysqli, $sql);
					
					echo'<div class="boxed">Done!</div>';
				}else{
					echo'<div class="boxed">User is already a moderator!</div>';
				}
			}elseif($option=="retract"){
				$result = $mysqli->query("SELECT * FROM users WHERE username='$user'") or die($mysqli->error());
				$row = mysqli_fetch_array($result);
				$moderator2 = $row['moderator'];
				if($moderator2==1){
					$sql = "UPDATE users SET moderator='0' WHERE username='$user'";
					mysqli_query($mysqli, $sql);
					
					echo'<div class="boxed">Done!</div>';
				}else{
					echo'<div class="boxed">User has a higher rank than you or is not a moderator!</div>';
				}
			}
		}else{
			echo'<div class="boxed">User does not exist!</div>';
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

if($moderator==5){
	?>
	<form method="post" action="">
		<button type="appointheadmod" name="appointheadmod" />Appoint head moderator</button>
	</form>
	<?php
	
	if(isset($_POST['appointheadmod'])){
		?>
		<form method="post" action="">
		  <input type="text" name="user" placeholder="user">
		  <select required name="option" type="text">  
		  		<option value="" disabled selected hidden>Choose</option>   
				<option value="grant">Grant</option>
				<option value="retract">Retract</option>
		  </select>
		  <button type="submit" name="appointheadmod2" />Appoint head moderator</button>
		</form>
		<?php
	}
			
	if(isset($_POST['appointheadmod2'])){
		$user = $mysqli->escape_string($_POST['user']);
		$option = $mysqli->escape_string($_POST['option']);
		
		$result2 = $mysqli->query("SELECT id FROM users WHERE username='$user'") or die($mysqli->error());
		$count = $result2->num_rows;
		if($count != 0){
			if($option=="grant"){
				$result = $mysqli->query("SELECT * FROM users WHERE username='$user'") or die($mysqli->error());
				$row = mysqli_fetch_array($result);
				$moderator2 = $row['moderator'];
				if($moderator2==1){
					$sql = "UPDATE users SET moderator='2' WHERE username='$user'";
					mysqli_query($mysqli, $sql);
					
					echo'<div class="boxed">Done!</div>';
				}else{
					echo'<div class="boxed">User not yet a moderator!</div>';
				}
			}elseif($option=="retract"){
				$result = $mysqli->query("SELECT * FROM users WHERE username='$user'") or die($mysqli->error());
				$row = mysqli_fetch_array($result);
				$moderator2 = $row['moderator'];
				if($moderator2==2){
					$sql = "UPDATE users SET moderator='1' WHERE username='$user'";
					mysqli_query($mysqli, $sql);
					
					echo'<div class="boxed">Done!</div>';
				}else{
					echo'<div class="boxed">User is not a head moderator!</div>';
				}
			}
		}else{
			echo'<div class="boxed">User does not exist!</div>';
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

if($moderator==4 || $moderator==5){
	?>
	<form method="post" action="">
		<button type="submit" name="showstory" />Show submitted articles</button>
	</form>
	<?php
	
	if(isset($_POST['showstory'])){
		$results_per_page=1;
		$sql = "SELECT * FROM storytopics WHERE verified='0' ORDER BY versiondate DESC LIMIT $start_from, ".$results_per_page;
		$rs_result = $mysqli2->query($sql);
		
		?> 
		<div class="scroll">
		<table id="table1">
		<tr>
		    <th> Date</th>
		    <th> Original article</th>
		    <th> Article</th>
		    <th> id</th>
		    <th> writer</th>
		    <th> Content</th>
		    <th> Solve</th>
		</tr>
		<?php 
		while($row = $rs_result->fetch_assoc()) {
			$id=$row["id"];
			$versiondate=$row["versiondate"];
			$version1id=$row["version1id"];
			$creator=$row["creator"];
			$name=$row["name"];
			$content=$row["content"];
			$category=$row["category"];
			$content = $purifier->purify($content);
			?> 
	           <tr>
	           <td >
	           		<?php echo "$versiondate"; ?>
	           </td>
	           <td >
	           		<?php echo "<a href='story.php?category=$category&topic=$version1id'>View </a>";?>
	           </td>
	           <td >
	           		<?php echo "$name"; ?>
	           </td>
	           <td >
	           		<?php echo "$id"; ?>
	           </td>
	           <td >
	           		<?php echo "$creator"; ?>
	           </td>
	           <td >
	           		<?php echo "$content"; ?>
	           </td>
	           <td >
				<form method="post" action="">
					<input type="hidden" name="id" value="<?php echo $id; ?>" />
					<button type="submit" name="approve" />Approve</button>
					<button type="submit" name="decline" />Decline</button>
				</form>
	           </td>
	           </tr>
			<?php 
			
		}; 
		?> 
		</table>
		</div>
		
		<script>
		    if ( window.history.replaceState ) {
		        window.history.replaceState( null, null, window.location.href );
		    }
		</script>
		<?php
	}

	if(isset($_POST['approve'])){
		$id= $mysqli->escape_string($_POST['id']);
		
		$result2 = $mysqli2->query("SELECT * FROM storytopics WHERE id='$id'") or die($mysqli->error());
		$row2 = mysqli_fetch_array($result2);
		$name = $row2['name'];
		$version = $row2['version'];
		$versiondate = $row2['versiondate'];
		$version1id = $row2['version1id'];
		$creator = $row2['creator'];
		
		$sql = "UPDATE storytopics SET verified='1' WHERE id='$id'";
		mysqli_query($mysqli2, $sql);
		
		$messagecontent="Hello $creator, <br> your submitted content with id: $version1id and name: $name has been approved by $username. <br> Regards,";
		$sql = "INSERT INTO messages (sender, recipient, date, subject, content) " 
	     . "VALUES ('$username','$creator',NOW(),'Approval of article with id $version1id','$messagecontent')";
		mysqli_query($mysqli2, $sql);
		
		echo'<div class="boxed">Done!</div>';
		
		?>
		<script>
		    if ( window.history.replaceState ) {
		        window.history.replaceState( null, null, window.location.href );
		    }
		</script>
		<?php
	}
	if(isset($_POST['decline'])){
		$id= $mysqli->escape_string($_POST['id']);
		
		$result2 = $mysqli2->query("SELECT * FROM storytopics WHERE id='$id'") or die($mysqli->error());
		$row2 = mysqli_fetch_array($result2);
		$name = $row2['name'];
		$version = $row2['version'];
		$versiondate = $row2['versiondate'];
		$version1id = $row2['version1id'];
		$creator = $row2['creator'];
		
   		?>
       	<div class="textbox">
			<form method="post" action="">
				<input type="hidden" name="id" value="<?php echo $id; ?>" />
				<textarea rows="4" cols="50" name="reason" maxlength="200" autofocus placeholder="Enter reason here..."></textarea>
				<button type="submit" name="decline2" />Decline</button>
			</form>
		</div>
   		<?php

		?>
		<script>
		    if ( window.history.replaceState ) {
		        window.history.replaceState( null, null, window.location.href );
		    }
		</script>
		<?php
	}
	
	if(isset($_POST['decline2'])){
		$id= $mysqli->escape_string($_POST['id']);
		$reason= $mysqli->escape_string($_POST['reason']);
		
		$result2 = $mysqli2->query("SELECT * FROM storytopics WHERE id='$id'") or die($mysqli->error());
		$row2 = mysqli_fetch_array($result2);
		$name = $row2['name'];
		$version = $row2['version'];
		$versiondate = $row2['versiondate'];
		$version1id = $row2['version1id'];
		$creator = $row2['creator'];
		$category = $row2['category'];
		
		$sql = "UPDATE storytopics SET verified='2' WHERE id='$id'";
		mysqli_query($mysqli2, $sql);
		
		$link="<a href=story.php?category=$category&topic=$id>here.</a>";
		$messagecontent="Hello $creator, <br> your submitted content with id: $version1id and name: $name has been refused by $username for the following reasons: $reason. You can view your admission $link <br> Regards,";
		$sql = "INSERT INTO messages (sender, recipient, date, subject, content) " 
	     . "VALUES ('$username','$creator',NOW(),'Refusal of article with id $version1id','$messagecontent')";
		mysqli_query($mysqli2, $sql);
		
		echo'<div class="boxed">Done!</div>';
	}
}
?>
</div>
</body>
<footer>
<?php require 'bottombar.php'; ?>
</footer>
</html>
