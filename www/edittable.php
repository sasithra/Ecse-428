<?php

	include "session.php";
	
	$s = new Session();

	$db = $s->link_database() or header_error("database error");
	$user = $s->get_user() or header_error("user timeout error");
	$info = $s->user_info($user) or header_error("user info error");
	is_admin($info) or header_error("not authorised");
	
	/* Save the original table value from the POST of the source page */
	if(isset($_POST['intable'])){
		$pieces = explode(" ", $_POST['intable']);
		$_SESSION["oritable"] = $pieces[0];
		$_SESSION["orimaxsize"] = $pieces[1];
		$_SESSION["oricurrentsize"] = $pieces[2];
		$intablenumber = $pieces[0];
		$inmaxsize = $pieces[1];
		$incurrentsize = $pieces[2];
		$infirstrun = true;
	}else{
		$firstrun = false;
	}
	
	if(isset($_SESSION['submitted'])){
		$submitted = $_SESSION['submitted'];
		unset($_SESSION['submitted']);
	}

			/* if the things are set, get them into vars */
	isset($_REQUEST["intablenumber"])	and $intablenumber 	= strip_tags(stripslashes($_REQUEST["intablenumber"])) 
		and $tablenumber = $intablenumber;
	isset($_REQUEST["inmaxsize"]) 		and $inmaxsize    	= strip_tags(stripslashes($_REQUEST["inmaxsize"])) 
		and $maxsize = $inmaxsize;
	isset($_REQUEST["incurrentsize"])	and $incurrentsize  = strip_tags(stripslashes($_REQUEST["incurrentsize"])) 
		and $currentsize = $incurrentsize;
	isset($_REQUEST["infirstrun"])		and $infirstrun   	= strip_tags(stripslashes($_REQUEST["infirstrun"])) 
		and $firstrun = $infirstrun;

	$is_ready = false;
	if(   isset($tablenumber)
	   || isset($maxsize)
	   || isset($currentsize)){
		$is_ready = true;
		if(   !isset($tablenumber)
		   || !isset($maxsize)
		   || !isset($currentsize)
		   || empty($tablenumber)
		   || empty($maxsize)
		   || (empty($currentsize) && $currentsize != 0)) {
			$is_ready = false;
			echo "You did not enter all the required information.<br/>\n";
		}
		
		if(strlen($tablenumber) > Session::INTEGER_MAX) {
			$is_ready = false;
			echo "Username is maximum ".Session::INTEGER_MAX." characters.<br/>\n";
		}
		if(strlen($maxsize) > Session::INTEGER_MAX) {
			$is_ready = false;
			echo "Password is too long.<br/>\n";
		}
		if(strlen($currentsize) > Session::INTEGER_MAX) {
			$is_ready = false;
			echo "First name is maximum ".Session::INTEGER_MAX." characters.<br/>\n";
		}
		if($currentsize > $maxsize){
			$is_ready = false;
			echo "current size is larger than the maximum size.";
		}else if($currentsize > 0){
			$status = 'occupied';
		}else{
			$status = 'vacant';
		}
		
	}
	if($is_ready) {
		if($s->edit_table($_SESSION["oritable"], $tablenumber, $maxsize, $currentsize, $status)){
			
			/* Clear out temporary Session variables*/
			unset($_SESSION['oritable']);
			unset($_SESSION['orimaxsize']);
			unset($_SESSION['oricurrentsize']);
			$_SESSION['submitted'] = true;
			
			Header('Location: '.$_SERVER['PHP_SELF']);
		} else {
			echo "Table not edited: ".$s->status()."<br/>\n";
		}
	}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
		<meta name = "Author" content = "Team RMS">
		<link rel = "shortcut icon" href = "favicon.ico" type = "image/x-icon">
		<link rel = "stylesheet" type = "text/css" href = "style.css">
        <title>Edit Table</title>
    </head>
	    <body>
        <form method="post" action="edittable.php">
            <h1>Edit Table</h1>
			<label>Currently: <br/></label>
			<?php
			if($firstrun){
				echo "Original table number: 		 &quot;".$_SESSION["oritable"]."&quot;<br/>\n";
				echo "Original table max size: 	 	 &quot;".$_SESSION["orimaxsize"]."&quot;<br/>\n";
				echo "Original table current size: 	 &quot;".$_SESSION["oricurrentsize"]."&quot;<br/>\n";
			}
			?>
            <div>
			<label>New Table ID:</label>
<input type="text" name="intablenumber"
value = "<?php if(isset($_SESSION['oritable'])) echo $_SESSION['oritable'];?>" 	
maxlength = "<?php echo Session::INTEGER_MAX;?>"/><br/>

            <label>New Table Maximum Size:</label>
<input type="text" name="inmaxsize"
value = "<?php if(isset($_SESSION['orimaxsize'])) echo $_SESSION['orimaxsize'];?>" 
maxlength = "<?php echo Session::INTEGER_MAX;?>"/><br/>

            <label>New Table Size:</label>
<input type="text" name="incurrentsize"
value = "<?php if(isset($_SESSION['oricurrentsize'])) echo $_SESSION['oricurrentsize'];?>"  
maxlength = "<?php echo Session::INTEGER_MAX;?>"/><br/>

            <br/>
            <br/>
			<input type = "submit" value = "Edit" <?php if (!isset($_SESSION["oritable"])){ echo "disabled";} ?>/>
			<p><?php if(isset($submitted)) echo "Edit Complete.";?><br/>
			<?php if (!isset($_SESSION["oritable"])){ echo "This page is presently stale.  Please return to mainmenu";} ?></p>
			</div>
        </form>
		
		<?php 
			if($_SESSION['submitted']){
				echo "submitted <br>";
			}
		?>
		
		<form action = "viewtables.php">
			<input type="submit" value="Return to view table">
		</form>
		
	</body>
	
	
</html>