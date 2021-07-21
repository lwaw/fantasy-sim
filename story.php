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

if (isset($_GET["history"])) { $history  = $_GET["history"]; } else { $history=0; };
$history=$mysqli->escape_string($history);

if (isset($_GET["page"])) { $page  = $_GET["page"]; } else { $page=1; };
$page=$mysqli->escape_string($page);
$results_per_page=20;
$start_from = ($page-1) * $results_per_page;

if($login == 1){
$result = $mysqli->query("SELECT * FROM users WHERE username='$username'") or die($mysqli->error());
$row = mysqli_fetch_array($result);
$moderator = $row['moderator'];
}else{
	$moderator = 0;
}

echo nl2br ("<div class=\"h1\">Story</div>");
?> <hr /> <?php

if($category==0){
	if($moderator == 5){
   		?>
		<form method="post" action="">
			<input type="text" name="categoryname" placeholder="Category">
			<input type="text" name="tablename" placeholder="Table name">
			<button type="submit" name="newcategory" /><?php echo "Create new category"; ?></button>
		</form>
   		<?php
	}
	
	if(isset($_POST['newcategory'])){
		$categoryname = $mysqli->escape_string($_POST['categoryname']);
		$tablename = $mysqli->escape_string($_POST['tablename']);
				
		$sql = "INSERT INTO storycategories (name, tablename) " 
	     . "VALUES ('$categoryname','$tablename')";
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
	
	echo "<a href='story.php?category=0&topic=0'>index</a>";
		
	$sql = "SELECT * FROM storycategories ORDER BY name ASC LIMIT $start_from, ".$results_per_page;
	$rs_result = $mysqli2->query($sql);
	
	?> 
	<div class="scroll">
	<table id="table1">
	<tr>
	    <th> Category</th>
	</tr>
	<?php 
	while($row = $rs_result->fetch_assoc()) {
		$catname=$row["name"];
		$catid=$row["id"];
		?> 
           <tr>
           <td>
           		<?php echo "<a href='story.php?category=$catid'>$catname </a>";?>
           		
	           	<?php
	           	if($moderator == 5){
	           		//echo nl2br("Order of categories: $catorder");
	           		?>
					<form method="post" action="">
						<input type="hidden" name="catid" value="<?php echo "$catid "; ?>" />
						<button type="submit" name="deletecat" /><?php echo "Delete category"; ?></button>
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

if(isset($_POST['deletecat'])){
	$catid = $mysqli->escape_string($_POST['catid']);
	
	$sql = "DELETE FROM storycategories WHERE id = '$catid'";
	mysqli_query($mysqli2, $sql);
	
	$sql = "DELETE FROM storytopics WHERE category = '$catid'";
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

	$sql = "SELECT COUNT(name) AS total FROM storycategories";
	$result = $mysqli2->query($sql);
	$row = $result->fetch_assoc();
	$total_pages = ceil($row["total"] / $results_per_page); // calculate total pages with results	
	
	for ($i=1; $i<=$total_pages; $i++) {  // print links for all pages
	            echo "<a href='story.php?category=0&page=".$i."'";
	            if ($i==$page)  echo " class='curPage'";
	            echo ">".$i."</a> "; 
	};
	
}elseif($category != 0 AND $topic == 0){
	$result2 = $mysqli2->query("SELECT * FROM storycategories WHERE id='$category'") or die($mysqli->error());
	$row2 = mysqli_fetch_array($result2);
	$catid = $row2['id'];
	$catname = $row2['name'];
	
	//add topic if protected==0
	if($moderator==5){
		?>
		<div class="textbox">
			<form method="post" action="">
				<input type="hidden" name="catid" value="<?php echo $catid; ?>" />
				<?php if($login==1){ ?> <button type="submit" name="addtopic" />Create new topic</button> <?php } ?>
			</form>
		</div>
		<?php
	}else{
		
	}

	//create topic
	if(isset($_POST['addtopic'])){
		$catid = $mysqli->escape_string($_POST['catid']);
		?>
		<form method="post" action="">
			<input type="hidden" name="catid" value="<?php echo "$catid "; ?>" />
			<input type="text" required name="topic" maxlength="100" placeholder="name">
			<input type="text" required name="gameid" maxlength="100" placeholder="gameid">
			<textarea rows="4" cols="50" id='mytextarea' name="content" maxlength="40000" autofocus placeholder="Enter reply here..."></textarea>
			<button type="submit" name="addtopic2" /><?php echo "Create topic"; ?></button>
		</form>
		<?php
	}
	
	if(isset($_POST['addtopic2'])){
		$catid = $mysqli->escape_string($_POST['catid']);
		$catid = (int) $catid;
		$topic = $mysqli->escape_string($_POST['topic']); //$topic = name
		$topic = $purifier->purify($topic);
		$gameid = $mysqli->escape_string($_POST['gameid']);
		$gameid = $purifier->purify($gameid);
		$content = $mysqli->escape_string($_POST['content']);
		
		$result2 = $mysqli2->query("SELECT * FROM storycategories WHERE id='$catid'") or die($mysqli->error());
		$row2 = mysqli_fetch_array($result2);
		if(strlen($topic) <= 100 AND strlen($topic) >= 1){
			if($moderator==5){
				$sql = "INSERT INTO storytopics (category, gameid, name, version, versiondate, creator, content) " 
			     . "VALUES ('$catid','$gameid','$topic','1', NOW(),'$username','$content')";
				mysqli_query($mysqli2, $sql);
				$lastid = $mysqli2->insert_id;
				
				$sql = "UPDATE storytopics SET version1id='$lastid' WHERE id='$lastid'";
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
	
	echo "<a href='story.php?category=0&topic=0'>index</a> |
	 <a href='story.php?category=$catid&topic=0'>$catname</a>";
	 	
	$sql = "SELECT * FROM storytopics WHERE category='$catid' AND version='1' ORDER BY name ASC LIMIT $start_from, ".$results_per_page;
	$rs_result = $mysqli2->query($sql);
	
	?> 
	<div class="scroll">
	<table id="table1">
	<tr>
	    <th> <?php echo nl2br("<div class=\"t1\">$catname</div>"); ?> </th>
	</tr>
	<?php 
	while($row = $rs_result->fetch_assoc()) {
		$topicname=$row["name"];
		$topicid=$row["id"];
		?> 
           <tr>
           <td>
           		<?php echo "<a href='story.php?category=$catid&topic=$topicid'>$topicname </a>";?>
           </td>
           </tr>
		<?php 
		
	}; 
	?> 
	</table>
	</div>
	<?php 

	$sql = "SELECT COUNT(id) AS total FROM storytopics";
	$result = $mysqli2->query($sql);
	$row = $result->fetch_assoc();
	$total_pages = ceil($row["total"] / $results_per_page); // calculate total pages with results	
	
	for ($i=1; $i<=$total_pages; $i++) {  // print links for all pages
	            echo "<a href='story.php?category=$catid&topic=0&page=".$i."'";
	            if ($i==$page)  echo " class='curPage'";
	            echo ">".$i."</a> "; 
	};	
}elseif($topic != 0){
	$result2 = $mysqli2->query("SELECT * FROM storycategories WHERE id='$category'") or die($mysqli->error());
	$row2 = mysqli_fetch_array($result2);
	$catid = $row2['id'];
	$catname = $row2['name'];
	
	if($history==0){
		//select nieuwste versie; versie 1 heeft version1id van 0; dus als er geen nieuwe versies zijn de orginele weergeven
		$result2 = $mysqli2->query("SELECT * FROM storytopics WHERE version1id='$topic'") or die($mysqli->error());
		$count = $result2->num_rows;
		
		if($count != 0){
			$limit=1;
			$result2 = $mysqli2->query("SELECT * FROM storytopics WHERE version1id='$topic' AND verified='1' ORDER BY version DESC LIMIT $limit") or die($mysqli->error());
			$row2 = mysqli_fetch_array($result2);
			$topid = $row2['id'];
			$topname = $row2['name'];
			$content = $row2['content'];
			$version = $row2['version'];
			$versiondate = $row2['versiondate'];
			$version1id = $row2['version1id'];
		}else{
			$result2 = $mysqli2->query("SELECT * FROM storytopics WHERE id='$topic'") or die($mysqli->error());
			$row2 = mysqli_fetch_array($result2);
			$topid = $row2['id'];
			$topname = $row2['name'];
			$content = $row2['content'];
			$version = $row2['version'];
			$versiondate = $row2['versiondate'];
			$version1id = $topid; //dit is de eerste versie
		}
	}elseif($history==1){
			$limit=1;
			if($moderator==4 OR $moderator==5){
				$result2 = $mysqli2->query("SELECT * FROM storytopics WHERE id='$topic' AND verified='1' ORDER BY version DESC LIMIT $limit") or die($mysqli->error());
			}else{
				$result2 = $mysqli2->query("SELECT * FROM storytopics WHERE id='$topic' AND verified='2' ORDER BY version DESC LIMIT $limit") or die($mysqli->error());
			}
			$row2 = mysqli_fetch_array($result2);
			$topid = $row2['id'];
			$topname = $row2['name'];
			$content = $row2['content'];
			$version = $row2['version'];
			$versiondate = $row2['versiondate'];
			$version1id = $row2['version1id'];
	}
	
	echo "<a href='story.php?category=0&topic=0'>index</a> |
	 <a href='story.php?category=$catid&topic=0'>$catname</a> |
	 <a href='story.php?category=$catid&topic=$topid'>$topname</a>";
	
	?> 
	<div class="scroll">
	<table id="table1">
	<tr>
	    <th>
	    	<?php echo nl2br("<div class=\"t1\">$topname</div>"); ?>
	    </th>
	</tr>
   <tr>
   <td>
   		<?php
   		$clean_html = $purifier->purify($content);
		echo "$clean_html";
   		?>
   </td>
   </tr>
	<?php 
	?> 
	</table>
	</div>
	
	<?php
	if($login==1){
		//alleen edit toestaan als er niet al een nieuwe versie beoordeelt wordt en ook geen oud artikel editen
		$result2 = $mysqli2->query("SELECT * FROM storytopics WHERE version1id='$version1id' AND verified='0'") or die($mysqli->error());
		$count = $result2->num_rows;
		if($count==0 AND $history==0){
		?>
		<div class="textbox">
			<form method="post" action="">
				<input type="hidden" name="topid" value="<?php echo $topid; ?>" />
				<button type="submit" name="edit" />Suggest edit</button>
			</form>
		</div>
		<?php 
		}
		
		if($moderator==4 OR $moderator==5){
			//historic versions
			$result2 = $mysqli2->query("SELECT * FROM storytopics WHERE version1id='$version1id' AND verified='1'") or die($mysqli->error());
			$count = $result2->num_rows;
			
			if($count != 0){
				$result = $mysqli2->query("SELECT * FROM storytopics WHERE version1id='$version1id' AND verified='1'") or die($mysqli->error());
				$columnValues = Array();
				while ( $row = mysqli_fetch_assoc($result) ) {
				  $columnValues[] = $row['id'];
				}
				?>
				<form method="post" action="">
				    <select name="selectstory" type="text">
				    <option selected="selected">View version</option>
				    <?php       
				    // Iterating through the product array
				    foreach($columnValues as $item){
				    ?>
				    <option value="<?php echo strtolower($item); ?>"><?php echo $item; ?></option>
				    <?php
				    }
				    ?>
				    </select> 
				    <button type="submit" name="selecthistoric" />View historic version</button>
				</form>
				<?php
			}
			
			//restore version
			if($history==1){
				?>
				<div class="textbox">
					<form method="post" action="">
						<input type="hidden" name="topid" value="<?php echo $topid; ?>" />
						<button type="submit" name="restore" />Restore this version</button>
					</form>
				</div>
				<?php 
			}
		}
	}

	if(isset($_POST['restore'])){
		$topid = $mysqli->escape_string($_POST['topid']);
		
		$result2 = $mysqli2->query("SELECT * FROM storytopics WHERE id='$topic'") or die($mysqli->error());
		$row2 = mysqli_fetch_array($result2);
		$topid = $row2['id'];
		$topname = $row2['name'];
		$category = $row2['category'];
		$name = $row2['name'];
		$content = $row2['content'];
		$version = $row2['version'];
		$versiondate = $row2['versiondate'];
		$version1id = $row2['version1id'];
		$creator = $row2['creator'];
		$gameid = $row2['gameid'];
		
		$limit=1;
		$result2 = $mysqli2->query("SELECT * FROM storytopics WHERE version1id='$version1id' ORDER BY version DESC LIMIT $limit") or die($mysqli->error());
		$row2 = mysqli_fetch_array($result2);
		$version = $row2['version'];
		
		$newversion=$version+1;
	
		$sql = "INSERT INTO storytopics (category, gameid, name, version, versiondate, creator, content, version1id, verified) " 
	     . "VALUES ('$category','$gameid','$name','$newversion', NOW(),'$username','$content','$version1id','1')";
		mysqli_query($mysqli2, $sql);
		
		echo'<div class="boxed">Done!</div>';
	}

	if(isset($_POST['selecthistoric'])){
		$id = $mysqli->escape_string($_POST['selectstory']);
		
		$result2 = $mysqli2->query("SELECT * FROM storytopics WHERE id='$id'") or die($mysqli->error());
		$row2 = mysqli_fetch_array($result2);
		$category = $row2['category'];	
		
		?>
		<script>
			var val = "<?php echo $id ?>"
			var val2 = "<?php echo $category ?>"
		    window.location = 'story.php?history=1&category='+val2+'&topic='+val;
		</script>
		<?php
	}
	
   	if(isset($_POST['edit'])){
       		$topid = $mysqli->escape_string($_POST['topid']);
			
			$result2 = $mysqli2->query("SELECT * FROM storytopics WHERE id='$topid'") or die($mysqli->error());
			$row2 = mysqli_fetch_array($result2);
       		$clean_html = $purifier->purify($row2["content"]);
			$version = $row2['version'];
			$version1id = $row2['version1id'];
			
			//banned
			$result2 = $mysqli->query("SELECT * FROM ban WHERE user='$username'") or die($mysqli->error());
			$count = $result2->num_rows;
			
			if ( $count != 0 ) {
				$result = $mysqli->query("SELECT * FROM ban WHERE user='$username'") or die($mysqli->error());
				$row = mysqli_fetch_array($result);
				$reason = $row['reason'];
				$date = $row['date'];
			
			 	$_SESSION['message'] = "You have been banned from the edditing articles until $date for the following reason: $reason";
				header("location: error.php");    
			}else{
	       		?>
	           	<div class="textbox">
					<form method="post" action="">
						<input type="hidden" name="topid" value="<?php echo $topid; ?>" />
						<textarea rows="4" cols="50" id='mytextarea' name="content" maxlength="4000" autofocus><?php echo $clean_html; ?></textarea>
						<button type="submit" name="edit2" />Submit</button>
					</form>
				</div>
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
	
	if(isset($_POST['edit2'])){
		$topid = $mysqli->escape_string($_POST['topid']);
		$content= $mysqli->escape_string($_POST['content']);
		
		$result2 = $mysqli2->query("SELECT * FROM storytopics WHERE id='$topid'") or die($mysqli->error());
		$row2 = mysqli_fetch_array($result2);
		$version = $row2['version'];
		$version1id = $row2['version1id'];
		$catid = $row2['category'];
		$gameid = $row2['gameid'];
		$name = $row2['name'];
		
		if($version1id==0){ // eerste bewerking
			$version1id=$topid;
		}
		
		$version=$version+1;
		if(strlen($content) <= 5000){
			$sql = "INSERT INTO storytopics (category, gameid, name, version, versiondate, creator, content, version1id) " 
		     . "VALUES ('$catid','$gameid','$name','$version', NOW(),'$username','$content','$version1id')";
			mysqli_query($mysqli2, $sql);
			
			echo'<div class="boxed">Thanks for submitting, your admission will be reviewed soon.</div>';
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
?>
</div>
</body>
<footer>
<?php require 'bottombar.php'; ?>
</footer>
</html>
