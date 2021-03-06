<?php

	include "session.php";

	$s = new Session();

	$db = $s->link_database() or header_error("database error");

	/* must be here or else caching says "user error" w/ logoff */
	if(isset($_REQUEST["logout"]) && $s->logoff()) {
		header("Location: loginpage.php");
		exit();
	}

	$user = $s->get_user() or header_error("user timeout error");
	$info = $s->user_info($user) or header_error("user info error");

?>
<!DOCTYPE html>

<html>
    <head>
        <meta charset="UTF-8">
		<meta name = "Author" content = "Team RMS">
		<link rel = "shortcut icon" href = "favicon.ico" type = "image/x-icon">
		<link rel = "stylesheet" type = "text/css" href = "style.css">
        <title>Main Menu</title>
    </head>
    <body>

<h1>Main Menu</h1>

<?php
	/* request check in/out */
	$is_checkedin = is_checkedin($info);
	if(isset($_REQUEST["checkout"]) && $is_checkedin) {
		if($s->checkout($info)) {
			echo "<p>You have been checked out.</p>\n\n";
		} else {
			echo "<p>There was an error and you may still be checked in; ".$s->status()."</p>\n\n";
		}
		/* refesh user info; assert true */
		$info = $s->user_info($user);
	} else if(isset($_REQUEST["checkin"]) && !$is_checkedin) {
		if($s->checkin($info)) {
			echo "<p>You have been checked in.</p>\n\n";
		} else {
			echo "<p>There was an error and you may not be checked in; ".$s->status()."</p>\n\n";
		}
		/* refesh user info; assert true */
		$info = $s->user_info($user);
	}
?>

<!-- this is where you don't need to be an admin and you don't need to be
checked in -->

<?php
	echo "<p>You are currently logged in as ".$info["FirstName"]." "
	     .$info["LastName"]." (".$info["username"].".)</p>\n\n";
?>


<?php
	if(is_checkedin($info)) {
?>

<!-- all the functions that depend on check in -->

<h2>User tasks</h2>

<p>Create <a href = "createorder.php">a new Order</a>.</p>

<p>View/Change <a href = "vieworders.php">an Order</a>.</p>

<p><a href = "bill.php">Bill</a> a table.</p>

<p><a href = "viewtables.php">View Tables</a></p>

<p>View <a href = "viewitems.php">Menu Items</a>.</p>

<h2>User Configuration</h2>

<p>View <a href = "viewpersonal.php">Account Information</a>.</p>

<p> <a href = "changepass.php">Change Existing Password</a>.</p>

<?php
		if(is_admin($info)) {
?>

<!-- admin and checked in -->

<h2>Admin Menu</h2>

<p><a href = "createtable.php">Create Tables</a></p>

<h3>User administration</h3>

<p><a href = "addaccount.php">Add account</a>.</p>

<p><a href = "viewusers.php">Edit Users</a>.</p>

<p><a href = "shifts.php">View and edit shifts</a>.</p>

<h3>Statistics</h3>

<p><a href = "viewrevenues.php">View revenues</a>.</p>

<p><a href = "mostpopular.php">View most popular items</a>.</p>

<?php
		}

		/* just checked in */
		echo "<p><form><input type=\"submit\" name=\"checkout\" value=\"Check Out\"/></form></p>\n\n";

	} else {

		/* checked out */
		echo "<p><form><input type=\"submit\" name=\"checkin\" value=\"Check In\"/></form></p>\n\n";

	}
?>

<!-- also you show this to all -->

<p><form><input type="submit" name="logout" value="Logout"></form></p>

    </body>
</html>
