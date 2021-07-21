<?php 
require 'navigationbar.php';
require 'db.php';
//require 'regionborders.php';
require_once 'purifier/library/HTMLPurifier.auto.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/* Displays user information and some useful messages */
//session_start();

// Check if user is logged in using the session variable
//if (isset($_SESSION['logged_in'])) { $topic  = $_GET["topic"]; } else { $topic=0; };
//$topic=$mysqli->escape_string($topic);

if ( isset($_SESSION['logged_in']) AND $_SESSION['logged_in'] != 1 ) {
  //$_SESSION['message'] = "You must log in before viewing your profile page!";
  //header("location: error.php");  
  $login = 0;
}
elseif(isset($_SESSION['logged_in']) AND $_SESSION['logged_in'] == 1) {
    // Makes it easier to read
    $username = $_SESSION['username'];
    //$last_name = $_SESSION['last_name'];
    $email = $_SESSION['email'];
    $active = $_SESSION['active'];
	$login = 1;
}else{
	$login=0;
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
  <meta name="description" content="Fantasy-Sim is a massively multiplayer online strategy game in which players can join a country community and join the economy, war and religion modules.">
  <meta name="keywords" content="rts,realtime,strategy,game,fantasy,lotr,lord,of,the,rings,economy,politics,religion,countries,soicial,simulation,erepublik,online,mmo,massive,multiplayer,free,to,play,orc,human,elve,dwarf,war,political,economy">
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
  

  
</head>

<body>

<div class="boxedtot">
<?php
//require 'ageing.php';

if (isset($_GET["category"])) { $category  = $_GET["category"]; } else { $category=0; };
$category=$mysqli->escape_string($category);

if (isset($_GET["topic"])) { $topic  = $_GET["topic"]; } else { $topic=0; };
$topic=$mysqli->escape_string($topic);

if (isset($_GET["page"])) { $page  = $_GET["page"]; } else { $page=1; };
$page=$mysqli->escape_string($page);
$results_per_page=20;
$start_from = ($page-1) * $results_per_page;

if($login == 1){
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
	
	 	$_SESSION['message'] = "You have been banned from the forum until $date for the following reason: $reason";
		header("location: error.php");    
	}
}else{
	$moderator = 0;
}

echo nl2br ("<div class=\"h1\">Forum</div>");
?> <hr /> <?php

if($category==0){
	if($moderator == 5){
   		?>
		<form method="post" action="">
			<input type="text" name="categoryname" placeholder="Category">
			<input type="checkbox" name="protected" value="true"> Protected against creating topics<br>
			<textarea rows="4" cols="50" name="description" maxlength="100">Enter first post here...</textarea>
			<button type="submit" name="newcategory" /><?php echo "Create new category"; ?></button>
		</form>
   		<?php
	}
	
	if(isset($_POST['newcategory'])){
		$categoryname = $mysqli->escape_string($_POST['categoryname']);
		$protected = $mysqli->escape_string($_POST['protected']);
		$description = $mysqli->escape_string($_POST['description']);
		
		if($protected == "true"){
			$protected = 1;
		}else{
			$protected = 0;
		}
		
		$sql = "INSERT INTO categories (name, description, protected) " 
	     . "VALUES ('$categoryname','$description','$protected')";
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
	
	echo "<a href='forum.php?category=0&topic=0'>index</a>";
		
	$sql = "SELECT * FROM categories ORDER BY catorder ASC LIMIT $start_from, ".$results_per_page;
	$rs_result = $mysqli2->query($sql);
	
	?> 
	<div class="scroll">
	<table id="table1">
	<tr>
	    <th> Category</th>
	    <th> Newest reply</th>
	</tr>
	<?php 
	while($row = $rs_result->fetch_assoc()) {
		$catname=$row["name"];
		$catid=$row["id"];
		$catorder=$row["catorder"];
		?> 
           <tr>
           <td class="leftpart">
           		<?php echo "<a href='forum.php?category=$catid'>$catname </a>";?>
           		<br />
           		<?php  echo $row["description"]; ?>
           </td>
           <td class="rightpart">
           	<?php
           	if($moderator == 5){
           		//echo nl2br("Order of categories: $catorder");
           		?>
				<form method="post" action="">
					<input type="hidden" name="catid" value="<?php echo "$catid "; ?>" />
					<input type="number" name="newcatorder" min="1" step="1" placeholder="<?php echo "$catorder" ?>;">
					<button type="submit" name="changeorder" /><?php echo "Change category order"; ?></button>
				</form>
				<form method="post" action="">
					<input type="hidden" name="catid" value="<?php echo "$catid "; ?>" />
					<button type="submit" name="deletecat" /><?php echo "Delete category"; ?></button>
				</form>
           		<?php
           	}
           	//echo "test"; 
           	?>
           	</td>
           </tr>
		<?php 
		
	}; 
	?> 
	</table>
	</div>
	<?php 
	
	if(isset($_POST['changeorder'])){
		$catid = $mysqli->escape_string($_POST['catid']);
		$newcatorder = $mysqli->escape_string($_POST['newcatorder']);
		
		$sql = "UPDATE categories SET catorder='$newcatorder' WHERE id='$catid'";
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

if(isset($_POST['deletecat'])){
	$catid = $mysqli->escape_string($_POST['catid']);
	
	$sql = "DELETE FROM categories WHERE id = '$catid'";
	mysqli_query($mysqli2, $sql);
	
	$sql = "SELECT id FROM topics WHERE category='$catid'";
	$rs_result = $mysqli2->query($sql);
	while($row = $rs_result->fetch_assoc()) {
		$topid=$row["id"];
		
		$sql = "DELETE FROM posts WHERE topic = '$topid'";
		mysqli_query($mysqli2, $sql);
	}
	
	$sql = "DELETE FROM topics WHERE category = '$catid'";
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

	$sql = "SELECT COUNT(name) AS total FROM categories";
	$result = $mysqli2->query($sql);
	$row = $result->fetch_assoc();
	$total_pages = ceil($row["total"] / $results_per_page); // calculate total pages with results	
	
	for ($i=1; $i<=$total_pages; $i++) {  // print links for all pages
	            echo "<a href='forum.php?category=0&page=".$i."'";
	            if ($i==$page)  echo " class='curPage'";
	            echo ">".$i."</a> "; 
	};
	
}elseif($category != 0 AND $topic == 0){
	$result2 = $mysqli2->query("SELECT * FROM categories WHERE id='$category'") or die($mysqli->error());
	$row2 = mysqli_fetch_array($result2);
	$catid = $row2['id'];
	$catname = $row2['name'];
	$protected = $row2['protected'];
	
	//add topic if protected==0
	if($protected==0 OR $moderator==5){
		?>
		<div class="textbox">
			<form method="post" action="">
				<input type="hidden" name="catid" value="<?php echo $catid; ?>" />
				<?php if($login==1){ ?> <button type="submit" name="addtopic" />Create new topic</button> <?php } ?>
			</form>
		</div>
		<?php
	}else{
		echo'<div class="boxed">It is not possible to create a topic in this category!</div>';
	}

	//create topic
	if(isset($_POST['addtopic'])){
		$catid = $mysqli->escape_string($_POST['catid']);
		?>
		<form method="post" action="">
			<input type="hidden" name="catid" value="<?php echo "$catid "; ?>" />
			<input type="text" required name="topic" maxlength="100" placeholder="Subject">
			<textarea rows="4" cols="50" id='mytextarea' name="firstpost" maxlength="4000">Enter first post here...</textarea>
			<button type="submit" name="addtopic2" /><?php echo "Create topic"; ?></button>
		</form>
		<?php
	}
	
	if(isset($_POST['addtopic2'])){
		$catid = $mysqli->escape_string($_POST['catid']);
		$catid = (int) $catid;
		$topic = $mysqli->escape_string($_POST['topic']);
		$topic = $purifier->purify($topic);
		$firstpost = $mysqli->escape_string($_POST['firstpost']);
		
		$result2 = $mysqli2->query("SELECT * FROM categories WHERE id='$catid'") or die($mysqli->error());
		$row2 = mysqli_fetch_array($result2);
		$protected = $row2['protected'];
		if(strlen($topic) <= 100 AND strlen($topic) >= 1){
			if($protected == 0 OR $moderator==5){
				$sql = "INSERT INTO topics (subject, date, category, creator, lastreply) " 
			     . "VALUES ('$topic',NOW(),'$catid','$username',NOW())";
				mysqli_query($mysqli2, $sql);
				
				$result2 = $mysqli2->query("SELECT * FROM topics WHERE creator='$username' AND subject='$topic' AND category='$catid'") or die($mysqli->error());
				$row2 = mysqli_fetch_array($result2);
				$topicid = $row2['id'];
				
				$sql = "INSERT INTO posts (content, date, topic, creator) " 
			     . "VALUES ('$firstpost',NOW(),'$topicid','$username')";
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
		}
	}
	
	echo "<a href='forum.php?category=0&topic=0'>index</a> |
	 <a href='forum.php?category=$catid&topic=0'>$catname</a>";
	 	
	$sql = "SELECT * FROM topics WHERE category='$catid' ORDER BY lastreply DESC LIMIT $start_from, ".$results_per_page;
	$rs_result = $mysqli2->query($sql);
	
	?> 
	<div class="scroll">
	<table id="table1">
	<tr>
	    <th> <?php echo nl2br("<div class=\"t1\">$catname</div>"); ?> </th>
	    <th> </th>
	</tr>
	<?php 
	while($row = $rs_result->fetch_assoc()) {
		$topicname=$row["subject"];
		$topicid=$row["id"];
		?> 
           <tr>
           <td class="leftpart">
           		<?php echo "<a href='forum.php?category=$catid&topic=$topicid'>$topicname </a>";?>
           </td>
           <td class="rightpart">
	           	<?php echo $row["creator"]; ?>
	           	<br />
	           	<?php echo $row["lastreply"]; ?>
	           	<br />
	           	<?php if($row["archived"]==1){
	           		echo "Closed topic";
				} ?>
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
	            echo "<a href='forum.php?category=$catid&topic=0&page=".$i."'";
	            if ($i==$page)  echo " class='curPage'";
	            echo ">".$i."</a> "; 
	};	
}elseif($topic != 0){
	$result2 = $mysqli2->query("SELECT * FROM categories WHERE id='$category'") or die($mysqli->error());
	$row2 = mysqli_fetch_array($result2);
	$catid = $row2['id'];
	$catname = $row2['name'];
	
	$result2 = $mysqli2->query("SELECT * FROM topics WHERE id='$topic'") or die($mysqli->error());
	$row2 = mysqli_fetch_array($result2);
	$topid = $row2['id'];
	$topname = $row2['subject'];
	$archived = $row2['archived'];
	$views = $row2['views'];
	
	$views=$views+1;
	$sql = "UPDATE topics SET views='$views' WHERE id='$topid'";
	mysqli_query($mysqli2, $sql);
	
	echo "<a href='forum.php?category=0&topic=0'>index</a> |
	 <a href='forum.php?category=$catid&topic=0'>$catname</a> |
	 <a href='forum.php?category=$catid&topic=$topid'>$topname</a>";
	 
	 //archivate topic
	 if($archived == 0 AND ($moderator==1 || $moderator==2 || $moderator==5)){
	 	?>
		<form method="post" action="">
			<input type="hidden" name="topid" value="<?php echo $topid; ?>" />
			<button type="submit" name="closetopic" />Close topic</button>
		</form>
		<?php
	 }	 
	if(isset($_POST['closetopic'])){
   		$topid = $mysqli->escape_string($_POST['topid']);
		$topid = (int) $topid;
		
		$sql = "UPDATE topics SET archived='1' WHERE id='$topid'";
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
	 	
	$sql = "SELECT * FROM posts WHERE topic='$topid' ORDER BY date ASC LIMIT $start_from, ".$results_per_page;
	$rs_result = $mysqli2->query($sql);
	
	?> 
	<div class="scroll">
	<table id="table1">
	<tr>
	    <th> 
	    	<?php echo nl2br("<div class=\"t1\">Views: $views</div>"); ?>
	    </th>
	    <th>
	    	<?php echo nl2br("<div class=\"t1\">$topname</div>"); ?>
	    </th>
	</tr>
	<?php 
	while($row = $rs_result->fetch_assoc()) {
		$postid=$row["id"];
		?> 
           <tr>
           <td class="rightpart">
	           	<?php $creator = $row["creator"]; ?>
	           	<?php echo nl2br("<div class=\"t1\"><a href='account.php?user=$creator'>$creator</a></div>"); ?>
	           	<br />
	           	<?php echo $row["date"]; ?>
	           	<br />
	           	<?php if($archived==0){ ?>
				<div class="textbox">
					<form method="post" action="">
						<input type="hidden" name="topid" value="<?php echo $row["topic"]; ?>" />
						<input type="hidden" name="postid" value="<?php echo $postid; ?>" />
						<?php if($login==1){ ?> <button type="submit" name="reply" />Write a reply</button> <?php } ?>
					</form>
				</div>
				<div class="textbox">
					<form method="post" action="">
						<input type="hidden" name="topid" value="<?php echo $row["topic"]; ?>" />
						<input type="hidden" name="postid" value="<?php echo $postid; ?>" />
						<?php if($login==1){ ?> <button type="submit" name="report" />Report</button> <?php } ?>
					</form>
				</div>
				<?php } ?>
	           	<br />
	           	<?php if($moderator==1 || $moderator==2 || $moderator==5){ ?>
				<div class="textbox">
					<form method="post" action="">
						<input type="hidden" name="topid" value="<?php echo $row["topic"]; ?>" />
						<input type="hidden" name="postid" value="<?php echo $postid; ?>" />
						<button type="submit" name="moderate" />Moderate post</button>
					</form>
				</div>
				<?php
				echo "post id: $postid";
				?>
				<?php } ?>
           </td>
           <td class="leftpart">
           		<?php
           		$clean_html = $purifier->purify($row["content"]);
				echo "$clean_html";
           		?>
	           	<?php
	           	// write reply
	           	if(isset($_POST['reply'])){
		           		$topid = $mysqli->escape_string($_POST['topid']);
						$postid = $mysqli->escape_string($_POST['postid']);
						$creator = $row["creator"];
						$content = $row["content"];
						if($postid == $row["id"]){
		           		?>
			           	<div class="textbox">
							<form method="post" action="">
								<input type="hidden" name="topid" value="<?php echo $topid; ?>" />
								<input type="hidden" name="postid" value="<?php echo $postid; ?>" />
								<input type="hidden" name="creator" value="<?php echo $creator; ?>" />
								<textarea rows="4" cols="50" id='mytextarea' name="content" maxlength="4000" autofocus placeholder="Enter reply here..."></textarea>
								<button type="submit" name="reply2" />Submit reply</button>
							</form>
						</div>
		           		<?php
					}
	           	}
				
				//report
	           	if(isset($_POST['report'])){
		           		$topid = $mysqli->escape_string($_POST['topid']);
						$postid = $mysqli->escape_string($_POST['postid']);
						$creator = $row["creator"];
						$content = $row["content"];
						if($postid == $row["id"]){
		           		?>
			           	<div class="textbox">
							<form method="post" action="">
								<input type="hidden" name="topid" value="<?php echo $topid; ?>" />
								<input type="hidden" name="postid" value="<?php echo $postid; ?>" />
								<input type="hidden" name="creator" value="<?php echo $creator; ?>" />
								<textarea rows="4" cols="50" name="content" maxlength="200" autofocus placeholder="Enter reason here..."></textarea>
								<button type="submit" name="report2" />Submit</button>
							</form>
						</div>
		           		<?php
					}
	           	}
				//moderate post
	           	if(isset($_POST['moderate'])){
		           		$topid = $mysqli->escape_string($_POST['topid']);
						$postid = $mysqli->escape_string($_POST['postid']);
						
						$result2 = $mysqli2->query("SELECT * FROM posts WHERE id='$postid'") or die($mysqli->error());
						$row2 = mysqli_fetch_array($result2);
		           		$clean_html = $purifier->purify($row2["content"]);
						
						if($postid == $row["id"]){
		           		?>
			           	<div class="textbox">
							<form method="post" action="">
								<input type="hidden" name="topid" value="<?php echo $topid; ?>" />
								<input type="hidden" name="postid" value="<?php echo $postid; ?>" />
								<textarea rows="4" cols="50" id='mytextarea' name="content" maxlength="4000" autofocus><?php echo $clean_html; ?></textarea>
								<button type="submit" name="moderate2" />Moderate</button>
							</form>
						</div>
		           		<?php
					}
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
	
	//add post
	if($archived==0 AND $login==1){
		?>
		<div class="textbox">
			<form method="post" action="">
				<input type="hidden" name="topid" value="<?php echo $topid; ?>" />
				<textarea rows="4" cols="50" id='mytextarea' name="content" maxlength="4000" placeholder="Enter post here..."></textarea>
				<button type="submit" name="addpost" />Submit new post</button>
			</form>
		</div>
		<?php
	}
	
	if(isset($_POST['addpost'])){
		$topid = $mysqli->escape_string($_POST['topid']);
		$topid = (int) $topid;
		$content = $mysqli->escape_string($_POST['content']);
		
		$result2 = $mysqli2->query("SELECT * FROM topics WHERE id='$topid'") or die($mysqli->error());
		$row2 = mysqli_fetch_array($result2);
		$archived = $row2['archived'];
		if($archived == 0){
			$sql = "INSERT INTO posts (content, date, topic, creator) " 
		     . "VALUES ('$content',NOW(),'$topid','$username')";
			mysqli_query($mysqli2, $sql);
			
			$sql = "UPDATE topics SET lastreply=NOW() WHERE id='$topid'";
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
	}
	
	if(isset($_POST['reply2'])){
   		$topid = $mysqli->escape_string($_POST['topid']);
		$topid = (int) $topid;
		$postid = $mysqli->escape_string($_POST['postid']);
		$postid = (int) $postid;
		$content = $mysqli->escape_string($_POST['content']);
		$creator = $mysqli->escape_string($_POST['creator']);
		
		if(strlen($content) <= 4100){
			$result2 = $mysqli2->query("SELECT * FROM topics WHERE id='$topid'") or die($mysqli->error());
			$row2 = mysqli_fetch_array($result2);
			$archived = $row2['archived'];
			if($archived == 0){
				$result2 = $mysqli2->query("SELECT * FROM posts WHERE id='$postid'") or die($mysqli->error());
				$row2 = mysqli_fetch_array($result2);
				$replyto = $row2['content'];
				$replyto=$mysqli->escape_string($replyto);
				
				$final = '<div class="replybox">' . '<div class="bold">' . $creator . ' wrote:' . '</div>' . '<br />' . $replyto . '</div>' . $content;
				
				$sql = "INSERT INTO posts (content, date, topic, creator, replyto) " 
			     . "VALUES ('$final',NOW(),'$topid','$username','$postid')";
				mysqli_query($mysqli2, $sql);
				
				$sql = "UPDATE topics SET lastreply=NOW() WHERE id='$topid'";
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
		}
	}

	if(isset($_POST['report2'])){
   		$topid = $mysqli->escape_string($_POST['topid']);
		$topid = (int) $topid;
		$postid = $mysqli->escape_string($_POST['postid']);
		$postid = (int) $postid;
		$content = $mysqli->escape_string($_POST['content']);
		$creator = $mysqli->escape_string($_POST['creator']);
		
		if(strlen($content) <= 250){
			$result2 = $mysqli2->query("SELECT * FROM posts WHERE id='$postid'") or die($mysqli->error());
			$row2 = mysqli_fetch_array($result2);
			$replyto = $row2['content'];	
	
			$sql = "INSERT INTO report (date, topic, post, user, reason, page) " 
		     . "VALUES (NOW(),'$topid','$postid','$username','$content', '$page')";
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
	}
	
	if(isset($_POST['moderate2'])){
   		$topid = $mysqli->escape_string($_POST['topid']);
		$topid = (int) $topid;
		$postid = $mysqli->escape_string($_POST['postid']);
		$postid = (int) $postid;
		$content = $mysqli->escape_string($_POST['content']);
		
		$sql = "UPDATE posts SET content='$content' WHERE id='$postid'";
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

	$sql = "SELECT COUNT(id) AS total FROM posts WHERE topic='$topid'";
	$result = $mysqli2->query($sql);
	$row = $result->fetch_assoc();
	$total_pages = ceil($row["total"] / $results_per_page); // calculate total pages with results	
	
	for ($i=1; $i<=$total_pages; $i++) {  // print links for all pages
	            echo "<a href='forum.php?category=$catid&topic=$topid&page=".$i."'";
	            if ($i==$page)  echo " class='curPage'";
	            echo ">".$i."</a> "; 
	};	
}
?>
</div>
</body>
<footer>
<?php require 'bottombar.php'; ?>
</footer>
</html>
