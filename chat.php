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
  

	
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.0/jquery.min.js"></script>

    <script type="text/javascript">
         var auto_refresh = setInterval(
         	function() {
         		$('#chatbox').load('chat.php?type=global&typeid=0' + ' #chatbox').fadeIn("slow");
         	}, 1500); // refreshing after every 15000 milliseconds
	</script> 
  
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

if (isset($_GET["type"])) { $type  = $_GET["type"]; } else { $type="none"; };
$type=$mysqli->escape_string($type);

if (isset($_GET["report"])) { $report  = $_GET["report"]; } else { $report="0"; };
$report=$mysqli->escape_string($report);

if (isset($_GET["id"])) { $id  = $_GET["id"]; } else { $id="0"; };
$id=$mysqli->escape_string($id);

$result = $mysqli->query("SELECT * FROM users WHERE username='$username'") or die($mysqli->error());
$row = mysqli_fetch_array($result);
$moderator = $row['moderator'];

//banned
$result2 = $mysqli->query("SELECT * FROM ban WHERE user='$username'") or die($mysqli->error());
$count = $result2->num_rows;

if ( $count != 0 ) {
	$result = $mysqli->query("SELECT * FROM ban WHERE user='$username'") or die($mysqli->error());
	$row = mysqli_fetch_array($result);
	$reason = $row['reason'];
	$date = $row['date'];

 	$_SESSION['message'] = "You have been banned from the chat until $date for the following reason: $reason";
	header("location: error.php");    
}

if ( $report != 0 ){
	$sql = "UPDATE chat SET report='1', reporter='$username' WHERE id='$id'";
	mysqli_query($mysqli2, $sql);
	
	echo "<script>window.location.replace = 'chat.php?type=$type'</script>";
}

if($type=="politicalparty"){
	echo nl2br ("<div class=\"h1\">Political party chat</div>");
}elseif($type=="militaryunit"){
	echo nl2br ("<div class=\"h1\">Military unit chat</div>");
}elseif($type=="country"){
	echo nl2br ("<div class=\"h1\">Country chat</div>");
}elseif($type=="global"){
	echo nl2br ("<div class=\"h1\">Global chat</div>");
}elseif($type=="religionorder"){
	echo nl2br ("<div class=\"h1\">Order chat</div>");
}

$result = $mysqli->query("SELECT politicalparty, militaryunit, religionorder FROM users WHERE username='$username'") or die($mysqli->error());
$row = mysqli_fetch_array($result);
$politicalparty = $row['politicalparty'];
$militaryunit = $row['militaryunit'];
$religionorder = $row['religionorder'];

?> <br /><?php

echo " <a href='chat.php?type=global'>global</a> | <a href='chat.php?type=country'>country</a>";
if($politicalparty != 0){
	echo " | <a href='chat.php?type=politicalparty'>political party</a>";
}
if($militaryunit != 0){
	echo " | <a href='chat.php?type=militaryunit'>military unit</a>";
}
if($religionorder != 0){
	echo " | <a href='chat.php?type=religionorder'>order</a>";
}
?> <hr /> <?php

?> <div id="chatbox"> <?php
if($type != "none"){
	if($type == "politicalparty"){
		$result = $mysqli->query("SELECT politicalparty FROM users WHERE username='$username'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$politicalparty = $row['politicalparty'];
		
		$results_per_page2=50;
		$start_from2=0;
		$result2 = $mysqli2->query("SELECT * FROM chat WHERE typeid='$politicalparty' AND type='$type'") or die($mysqli->error());
		$count = $result2->num_rows;
		
		if($count != 0){
			$sql = "SELECT * FROM chat WHERE typeid='$politicalparty' AND type='$type' ORDER BY date ASC LIMIT $start_from2, ".$results_per_page2;
			$rs_result = $mysqli2->query($sql);
			
			while($row = $rs_result->fetch_assoc()) {
				$id=$row["id"];
				$date=$row["date"];
				$user=$row["user"];
				$content=$row["content"];
				$content = $purifier->purify($content);
				
				$date = new DateTime($date); //convert to datetime
				
				date_default_timezone_set('UTC'); //current date
				$datecur = date("Y-m-d H:i:s"); 
				$datecur = new DateTime($datecur);
				
				$diff = $datecur->diff($date); //calculate difference in hours
				$hours = $diff->h;
				$hours = $hours + ($diff->days*24);
				
				echo nl2br ("<div class=\"bold\"><a href='account.php?user=$user'>$user</a> wrote $hours hours ago:</div>");
				echo "$content";
				echo nl2br("<div class=\"t1\"><a href='chat.php?type=$type&report=1&id=$id'>report</a></div>");
			}
		}else{
			echo nl2br ("<div class=\"t1\">No one has sent a message yet!</div>");
		}
	}elseif($type == "militaryunit"){
		$result = $mysqli->query("SELECT militaryunit FROM users WHERE username='$username'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$militaryunit = $row['militaryunit'];
		
		$results_per_page2=50;
		$start_from2=0;
		$result2 = $mysqli2->query("SELECT * FROM chat WHERE typeid='$militaryunit' AND type='$type'") or die($mysqli->error());
		$count = $result2->num_rows;
		
		if($count != 0){
			$sql = "SELECT * FROM chat WHERE typeid='$militaryunit' AND type='$type' ORDER BY date ASC LIMIT $start_from2, ".$results_per_page2;
			$rs_result = $mysqli2->query($sql);
			
			while($row = $rs_result->fetch_assoc()) {
				$id=$row["id"];
				$date=$row["date"];
				$user=$row["user"];
				$content=$row["content"];
				$content = $purifier->purify($content);
				
				$date = new DateTime($date); //convert to datetime
				
				date_default_timezone_set('UTC'); //current date
				$datecur = date("Y-m-d H:i:s"); 
				$datecur = new DateTime($datecur);
				
				$diff = $datecur->diff($date); //calculate difference in hours
				$hours = $diff->h;
				$hours = $hours + ($diff->days*24);
				
				echo nl2br ("<div class=\"bold\"><a href='account.php?user=$user'>$user</a> wrote $hours hours ago:</div>");
				echo "$content";
				echo nl2br("<div class=\"t1\"><a href='chat.php?type=$type&report=1&id=$id'>report</a></div>");
			}
		}else{
			echo nl2br ("<div class=\"t1\">No one has sent a message yet!</div>");
		}
	}elseif($type == "religionorder"){
		$result = $mysqli->query("SELECT religionorder FROM users WHERE username='$username'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$religionorder = $row['religionorder'];
		
		$results_per_page2=50;
		$start_from2=0;
		$result2 = $mysqli2->query("SELECT * FROM chat WHERE typeid='$religionorder' AND type='$type'") or die($mysqli->error());
		$count = $result2->num_rows;
		
		if($count != 0){
			$sql = "SELECT * FROM chat WHERE typeid='$religionorder' AND type='$type' ORDER BY date ASC LIMIT $start_from2, ".$results_per_page2;
			$rs_result = $mysqli2->query($sql);
			
			while($row = $rs_result->fetch_assoc()) {
				$id=$row["id"];
				$date=$row["date"];
				$user=$row["user"];
				$content=$row["content"];
				$content = $purifier->purify($content);
				
				$date = new DateTime($date); //convert to datetime
				
				date_default_timezone_set('UTC'); //current date
				$datecur = date("Y-m-d H:i:s"); 
				$datecur = new DateTime($datecur);
				
				$diff = $datecur->diff($date); //calculate difference in hours
				$hours = $diff->h;
				$hours = $hours + ($diff->days*24);
				
				echo nl2br ("<div class=\"bold\"><a href='account.php?user=$user'>$user</a> wrote $hours hours ago:</div>");
				echo "$content";
				echo nl2br("<div class=\"t1\"><a href='chat.php?type=$type&report=1&id=$id'>report</a></div>");
			}
		}else{
			echo nl2br ("<div class=\"t1\">No one has sent a message yet!</div>");
		}
	}elseif($type == "country"){
		$result = $mysqli->query("SELECT nationality FROM users WHERE username='$username'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$nationality = $row['nationality'];
		
		$result = $mysqli->query("SELECT id FROM countryinfo WHERE country='$nationality'") or die($mysqli->error());
		$row = mysqli_fetch_array($result);
		$typeid = $row['id'];
		
		$results_per_page2=50;
		$start_from2=0;
		$result2 = $mysqli2->query("SELECT * FROM chat WHERE typeid='$typeid' AND type='$type'") or die($mysqli->error());
		$count = $result2->num_rows;
		
		if($count != 0){
			$sql = "SELECT * FROM chat WHERE typeid='$typeid' AND type='$type' ORDER BY date ASC LIMIT $start_from2, ".$results_per_page2;
			$rs_result = $mysqli2->query($sql);
			
			while($row = $rs_result->fetch_assoc()) {
				$id=$row["id"];
				$date=$row["date"];
				$user=$row["user"];
				$content=$row["content"];
				$content = $purifier->purify($content);
				
				$date = new DateTime($date); //convert to datetime
				
				date_default_timezone_set('UTC'); //current date
				$datecur = date("Y-m-d H:i:s"); 
				$datecur = new DateTime($datecur);
				
				$diff = $datecur->diff($date); //calculate difference in hours
				$hours = $diff->h;
				$hours = $hours + ($diff->days*24);
				
				echo nl2br ("<div class=\"bold\"><a href='account.php?user=$user'>$user</a> wrote $hours hours ago:</div>");
				echo "$content";
				echo nl2br("<div class=\"t1\"><a href='chat.php?type=$type&report=1&id=$id'>report</a></div>");
			}
		}else{
			echo nl2br ("<div class=\"t1\">No one has sent a message yet!</div>");
		}
	}elseif($type == "global"){
		$results_per_page2=50;
		$start_from2=0;
		$result2 = $mysqli2->query("SELECT * FROM chat WHERE type='$type'") or die($mysqli->error());
		$count = $result2->num_rows;
		
		if($count != 0){
			$sql = "SELECT * FROM chat WHERE type='$type' ORDER BY date ASC LIMIT $start_from2, ".$results_per_page2;
			$rs_result = $mysqli2->query($sql);
			
			while($row = $rs_result->fetch_assoc()) {
				$id=$row["id"];
				$date=$row["date"];
				$user=$row["user"];
				$content=$row["content"];
				$content = $purifier->purify($content);
				
				$date = new DateTime($date); //convert to datetime
				
				date_default_timezone_set('UTC'); //current date
				$datecur = date("Y-m-d H:i:s"); 
				$datecur = new DateTime($datecur);
				
				$diff = $datecur->diff($date); //calculate difference in hours
				$hours = $diff->h;
				$hours = $hours + ($diff->days*24);
				
				echo nl2br ("<div class=\"bold\"><a href='chat.php?type=$type&report=1&id=$id'>report</a> | <a href='account.php?user=$user'>$user</a> wrote $hours hours ago:</div>");
				echo nl2br ("<div class=\"chatmessage\">$content</div>");
			}
		}else{
			echo nl2br ("<div class=\"t1\">No one has sent a message yet!</div>");
		}
	}
	
	?> </div> <?php
	?>
   	<div class="textbox">
		<form method="post" action="">
			<input type="hidden" name="type" value="<?php echo $type; ?>" />
			<textarea rows="4" cols="50" id='mytextarea' name="content" maxlength="200" placeholder="Enter message here..." autofocus></textarea>
			<button type="submit" name="reply" />Submit reply</button>
		</form>
	</div>
	<?php
	if(isset($_POST['reply'])){
		$type = $mysqli->escape_string($_POST['type']);
		$content = $mysqli->escape_string($_POST['content']);
		
		$typeid=0;
		if($type == "politicalparty"){
			$result2 = $mysqli->query("SELECT politicalparty FROM users WHERE username='$username'") or die($mysqli->error());
			$count = $result2->num_rows;
			
			if($count != 0){
				$result = $mysqli->query("SELECT politicalparty FROM users WHERE username='$username'") or die($mysqli->error());
				$row = mysqli_fetch_array($result);
				$typeid = $row['politicalparty'];
			}
		}elseif($type == "militaryunit"){
			$result2 = $mysqli->query("SELECT militaryunit FROM users WHERE username='$username'") or die($mysqli->error());
			$count = $result2->num_rows;
			
			if($count != 0){
				$result = $mysqli->query("SELECT militaryunit FROM users WHERE username='$username'") or die($mysqli->error());
				$row = mysqli_fetch_array($result);
				$typeid = $row['militaryunit'];
			}
		}elseif($type == "religionorder"){
			$result2 = $mysqli->query("SELECT religionorder FROM users WHERE username='$username'") or die($mysqli->error());
			$count = $result2->num_rows;
			
			if($count != 0){
				$result = $mysqli->query("SELECT religionorder FROM users WHERE username='$username'") or die($mysqli->error());
				$row = mysqli_fetch_array($result);
				$typeid = $row['religionorder'];
			}
		}elseif($type == "country"){
			$result = $mysqli->query("SELECT nationality FROM users WHERE username='$username'") or die($mysqli->error());
			$row = mysqli_fetch_array($result);
			$nationality = $row['nationality'];
			
			$result = $mysqli->query("SELECT id FROM countryinfo WHERE country='$nationality'") or die($mysqli->error());
			$row = mysqli_fetch_array($result);
			$typeid = $row['id'];
		}
		
		if(iconv_strlen($content,'UTF-8') <=600){
			if($typeid != 0 OR $type=="global"){
				$sql = "INSERT INTO chat (type, typeid, date, user, content) " 
			     . "VALUES ('$type','$typeid',NOW(),'$username','$content')";
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
		}else{
			echo'<div class="boxed">The maximum length of chat messages is 500 characters!</div>';
			?>
			<script>
			    if ( window.history.replaceState ) {
			        window.history.replaceState( null, null, window.location.href );
			    }
			</script>
			<?php
		}
	}
}else{
	
}


?>
</div>
</body>
<footer>
<?php require 'bottombar.php'; ?>
</footer>
</html>
