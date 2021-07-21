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
  <script type="text/javascript" src="tinymce/tinymce.js"></script>
  <script>
  tinymce.init({
    selector: '#mytextarea',
    plugins: ["emoticons autolink help preview textcolor"],
   toolbar: [
     'undo redo | styleselect | alignleft aligncenter alignright | forecolor backcolor | bold italic | emoticons | help | preview'
   ]
  });
  </script>
  

  
</head>

<body>

<div class="boxedtot">
<?php
require 'ageing.php';

if (isset($_GET["id"])) { $id  = $_GET["id"]; } else { $id=0; };
$id=$mysqli->escape_string($id);

if (isset($_GET["show"])) { $show  = $_GET["show"]; } else { $show=0; };
$show=$mysqli->escape_string($show);

if (isset($_GET["page"])) { $page  = $_GET["page"]; } else { $page=1; };
$page=$mysqli->escape_string($page);
$results_per_page=20;
$start_from = ($page-1) * $results_per_page;

echo nl2br ("<div class=\"h1\">Messages</div>");
?> <hr /> <?php

if($id == 0){
	?>
	<form method="post" action="">
		<button type="submit" name="sendmessage" />Send new message</button>
		<button type="submit" name="viewmessage" />View received messages</button>
	</form>
	<?php
	//send message
	if(isset($_POST['sendmessage'])){
		?>
		<form method="post" action="">
			<input type="text" name="recipient" placeholder="Receiver">
			<input type="text" required name="subject" maxlength="30" minlength="1" placeholder="Subject">
			<textarea rows="4" cols="50" id='mytextarea' name="content" placeholder="Enter message here..." maxlength="5000"></textarea>
			<button type="submit" name="sendmessage2" />Send</button>
		</form>
		<?php
	}
	
	if(isset($_POST['sendmessage2'])){
		$recipient = $mysqli->escape_string($_POST['recipient']);
		$subject= $mysqli->escape_string($_POST['subject']);
		$content = $mysqli->escape_string($_POST['content']);
		if(strlen($subject) <= 30 AND strlen($subject) >= 1 AND strlen($content) <= 5500){
			$result = $mysqli->query("SELECT username FROM users WHERE username='$recipient'") or die($mysqli->error());
			if($result->num_rows > 0){	
				$sql = "INSERT INTO messages (sender, recipient, date, subject, content) " 
			     . "VALUES ('$username','$recipient',NOW(),'$subject','$content')";
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
				echo'<div class="boxed">User does not exist!</div>';
			}
		}
	}
	
	if(isset($_POST['viewmessage']) || $show == 1){
		$sql = "SELECT * FROM messages WHERE recipient='$username' ORDER BY date DESC LIMIT $start_from, ".$results_per_page;
		$rs_result = $mysqli2->query($sql);
		
		//check for messages
		if ( $rs_result->num_rows > 0 ){	
			?> 
			<div class="scroll">
			<table id="table1">
			<tr>
			    <th> </th>
			    <th> </th>
			</tr>
			<?php 
			while($row = $rs_result->fetch_assoc()) {
				$id=$row["id"];
				$sender=$row["sender"];
				$date=$row["date"];
				$subject=$row["subject"];
				$read=$row["read"];
				?> 
		           <tr>
		           <td class="leftpart">
		           		<?php echo "<a href='messages.php?id=$id'>$subject </a>";?>
		           </td>
		           <td class="rightpart">
			           	<?php echo $row["sender"]; ?>
			           	<br />
			           	<?php echo $row["date"]; ?>
			           	<br />
						<form method="post" action="">
							<input type="hidden" name="messageid" value="<?php echo $id; ?>" />
							<button type="submit" name="delete" />Delete message</button>
						</form>
			           	<?php
			           	if($read==0){
							echo'<div class="boxed">Unread message</div>';
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
		
			$sql = "SELECT COUNT(subject) AS total FROM topics";
			$result = $mysqli2->query($sql);
			$row = $result->fetch_assoc();
			$total_pages = ceil($row["total"] / $results_per_page); // calculate total pages with results	
			
			for ($i=1; $i<=$total_pages; $i++) {  // print links for all pages
			            echo "<a href='messages.php?show=1&page=".$i."'";
			            if ($i==$page)  echo " class='curPage'";
			            echo ">".$i."</a> "; 
			};
		}else{
			echo'<div class="boxed">You have no messages in your inbox</div>';
		}
	}

	if(isset($_POST['delete'])){
		$id = $mysqli->escape_string($_POST['messageid']);
		$id = (int) $id;
		
		$sql = "DELETE FROM messages WHERE id='$id' AND recipient='$username'";
		mysqli_query($mysqli2, $sql);
		
		echo'<div class="boxed">Done!</div>';
		
		?>
		<script>
		    if ( window.history.replaceState ) {
		        window.history.replaceState( null, null, window.location.href );
		    }
		    document.location = "messages.php?show=1";
		</script>
		<?php
	}

}else{
	$result2 = $mysqli2->query("SELECT * FROM messages WHERE id='$id'") or die($mysqli->error());
	$row2 = mysqli_fetch_array($result2);
	$id = $row2['id'];
	$recipient = $row2['recipient'];
	$content = $row2['content'];
	$subject = $row2['subject'];
	$sender = $row2['sender'];
	$date = $row2['date'];
	$read = $row2['read'];
	$replyto = $row2['replyto'];
	$inviteid=$row2["inviteid"];
	$invitetype=$row2["invitetype"];
	
	if($recipient == $username){
		?> 
		<div class="scroll">
		<table id="table1">
			<tr>
			    <th> <?php echo "$subject"; ?> </th>
			    <th> </th>
			</tr>
	
	       <tr>
	       <td class="leftpart">
           		<?php
            	$clean_html = $purifier->purify($content);
				echo "$clean_html";
				if($invitetype != NULL){
					?>
					<form method="post" action="">
						<input type="hidden" name="id" value="<?php echo $id; ?>" />
						<input type="hidden" name="inviteid" value="<?php echo $inviteid; ?>" />
						<input type="hidden" name="invitetype" value="<?php echo $invitetype; ?>" />
						<button type="submit" name="accept" />Accept invitation</button>
					</form>
					<?php
				}
           		?>
	       		<?php
	           	if(isset($_POST['reply'])){
	           		$replyto = $mysqli->escape_string($_POST['messageid']);
					$recipient = $mysqli->escape_string($_POST['sender']);
					$subject = $mysqli->escape_string($_POST['subject']);
	           		?>
		           	<div class="textbox">
						<form method="post" action="">
							<input type="hidden" name="replyto" value="<?php echo $replyto; ?>" />
							<input type="hidden" name="recipient" value="<?php echo $recipient; ?>" />
							<input type="hidden" name="subject" value="<?php echo $subject; ?>" />
							<textarea rows="4" cols="50" id='mytextarea' name="content" maxlength="5000" placeholder="Enter post here..." autofocus></textarea>
							<button type="submit" name="reply2" />Submit reply</button>
						</form>
					</div>
	           		<?php
	           	}
	       		?>
	       </td>
	       <td class="rightpart">
	           	<?php echo $sender; ?>
	           	<br />
	           	<?php echo $date; ?>
	           	<br />
				<form method="post" action="">
					<input type="hidden" name="messageid" value="<?php echo $id; ?>" />
					<button type="submit" name="delete" />Delete message</button>
				</form>
				<br />
				<form method="post" action="">
					<input type="hidden" name="messageid" value="<?php echo $id; ?>" />
					<input type="hidden" name="sender" value="<?php echo $sender; ?>" />
					<input type="hidden" name="subject" value="<?php echo $subject; ?>" />
					<button type="submit" name="reply" />Write a reply</button>
				</form>
	       </td>
	       </tr>
		</table>
		</div>
		<?php 
		
		if($read == 0){
			$sql = "UPDATE messages SET `read`='1' WHERE id='$id'";
			mysqli_query($mysqli2, $sql);
		}
		
		if(isset($_POST['delete'])){
			$id = $mysqli->escape_string($_POST['messageid']);
			
			$sql = "DELETE FROM messages WHERE id='$id' AND recipient='$username'";
			mysqli_query($mysqli2, $sql);
			
			echo'<div class="boxed">Done!</div>';
			
			?>
			<script>
			    if ( window.history.replaceState ) {
			        window.history.replaceState( null, null, window.location.href );
			        document.location = "messages.php?show=1";
			    }
			</script>
			<?php
		}
		
		if(isset($_POST['accept'])){
			$messageid = $mysqli->escape_string($_POST['id']);
			$inviteid = $mysqli->escape_string($_POST['inviteid']);
			$invitetype = $mysqli->escape_string($_POST['invitetype']);
			
			$result2 = $mysqli2->query("SELECT id FROM messages WHERE recipient='$username' AND inviteid='$inviteid'") or die($mysqli2->error());
			$count = $result2->num_rows;
			
			if($inviteid != 0){
				if($count != 0){
					if($invitetype=="order"){
						$result = $mysqli->query("SELECT userreligion, religionorder FROM users WHERE username='$username'") or die($mysqli->error());
						$row = mysqli_fetch_array($result);
						$userreligion=$row['userreligion'];
						$religionorder=$row['religionorder'];
						
						if($religionorder == NULL OR $religionorder == "NULL"){
							$result = $mysqli->query("SELECT religionid  FROM religion WHERE id='$inviteid'") or die($mysqli->error());
							$row = mysqli_fetch_array($result);
							$orderreligionid=$row['religionid'];
							
							$result = $mysqli->query("SELECT name  FROM religion WHERE id='$orderreligionid'") or die($mysqli->error());
							$row = mysqli_fetch_array($result);
							$religionname=$row['name'];
							
							if($religionname == $userreligion){
								$sql = "UPDATE users SET religionorder ='$inviteid' WHERE username='$username'";
								mysqli_query($mysqli, $sql);
								
								$sql = "UPDATE messages SET inviteid ='0' WHERE id='$messageid' AND recipient='$username'";
								mysqli_query($mysqli2, $sql);
								
								echo'<div class="boxed">Done!</div>';
							}else{
								echo'<div class="boxed">The order belong to a different religion than the one you currently folloe!</div>';
							}
						}else{
							echo'<div class="boxed">First leave your current order to accept the invitation!</div>';
						}
					}
				}
			}else{
				echo'<div class="boxed">This invitation is no longer valid!</div>';
			}
			?>
			<script>
			    if ( window.history.replaceState ) {
			        window.history.replaceState( null, null, window.location.href );
			    }
			</script>
			<?php
		}
		
		if(isset($_POST['reply2'])){
			$replyto = $mysqli->escape_string($_POST['replyto']);
			$recipient = $mysqli->escape_string($_POST['recipient']);
			$content = $mysqli->escape_string($_POST['content']);
			$subject = $mysqli->escape_string($_POST['subject']);
			$subject = $purifier->purify($subject);
			
			if(strlen($content) <= 5500){
				$result2 = $mysqli2->query("SELECT * FROM messages WHERE id='$replyto' AND recipient='$username'") or die($mysqli->error());
				$row2 = mysqli_fetch_array($result2);
				$oldcontent = $row2['content'];
				$oldcontent=$mysqli->escape_string($oldcontent);
				
				$final = '<div class="replybox">' . '<div class="bold">' . $recipient . ' wrote:' . '</div>' . '<br />' . $oldcontent . '</div>' . $content;
				
				$sql = "INSERT INTO messages (sender, recipient, date, subject, content, replyto) " 
			     . "VALUES ('$username','$recipient',NOW(),'$subject','$final','$replyto')";
				mysqli_query($mysqli2, $sql);
				
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
	}else{
		echo'<div class="boxed">You are not allowed to see that message!</div>';
	}
}


?>
</div>
</body>
<footer>
<?php require 'bottombar.php'; ?>
</footer>
</html>
