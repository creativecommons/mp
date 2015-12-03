<!DOCTYPE html>
<!--
To the extent possible under law, the person who associated CC0 with
this work has waived all copyright and related or neighboring rights
to this work.
-->
<?php

session_start();

global $dbh;

$action = $_GET["action"];

$dbh = new PDO('sqlite:/tmp/foo.db');

$foo = $dbh->exec("SELECT COUNT(*) FROM sqlite_master WHERE type='table' AND name='users'");

if ($foo != 1) {
  setupdb();
}

// Handle page logic here that needs to be resolved before drawing the UI.
// There's another $action switch below in the body to render the correct view.

switch ($action) {

case 'loginprocess':
    $login_status = 'err';
    // Make sure we've been passed a non-empty username
    if ((isset($_POST['username'])) && trim($_POST['username']) != ''){
        $username = trim($_POST['username']);
        $usernameq = $dbh->quote($username);
        $select_user = "SELECT * FROM users where username = " . $usernameq;
        $user_exists = $dbh->query($select_user);
        // Does the user already exist or do we have to insert them?
        if ($user_exists) {
            // The user is already in the db so just use the name
            $_SESSION['username'] = $username;
            $login_status = 'exists';
        } else {
            // The user is not already in the db, try to insert them
            $insert_user = "INSERT INTO users (username) VALUES("
                . $usernameq . ")";
            $user_inserted = $dbh->exec($insert_user);
            if ($user_inserted) {
                // The user is now inserted in the db, use their name
                $_SESSION['username'] = $username;
                $login_status = 'created';
            }
        }
    }
    break;

case 'logoutprocess':
    // Tear down the session
    $_SESSION = array();
    session_destroy();
    // Set the action to the default index page
    $action = '';
    break;
}

// Flag to tell the UI whether the user is logged in or not
$logged_in = isset($_SESSION['username']);
?>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Model Platform</title>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">

    <link href="style.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
    <nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="#">Model Platform</a>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
            <li<?php if ($action == '') { echo ' class="active"'; } ?>>
              <a href=".">Home</a></li>
<?php
if ($logged_in) {
    echo '<li><a href="?action=logoutprocess">Log out</a></li>';
    echo '<li' . (($action == 'profile') ? ' class="active"' : '')
           . '><a href="?action=profile">' . $_SESSION['username']
           . '</a></li>';
}
?>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </nav>

    <div class="container">
      <div class="main-content">
<?php

switch ($action) {

default:
?>
    <h1>Hello World!</h1>
    <p>You can
<?php
    if (! $logged_in) {
?>
<a href="?action=login">Login/Register</a> or
<?php } ?>
<a href="?action=browse">browse existing works</a></p>
<?php
    break;

case "login":
?>
    <form action="?action=loginprocess" method="post">
      <div class="form-group">
        <label for="username">Username</label>
        <input name="username" id="username" class="form-control" type="text"
          maxlength="15" size="15">
      </div>
      <input type="submit" class="btn btn-default"></button>
    </form>
<?php
    break;

case "loginprocess":
    if ($loginstatus == 'err') {
?>
    <h1>Something went wrong</h1>
    <p>We're sorry about that! <a href="?action=login">Please try again</a>.</p>
<?php
    } else {
?>
    <h1>Success!</h1>
    <p>You are now logged in.</p>
    <p>Please choose an option from the main navigation to see what you can
       do.</p>
<?php
    }
    break;

// Just fall through to the default and render the welcome page
//case "logoutprocess":
//
//    // Log the user out of the system
//
//    break;

case "new":

    // add a new work (optional license)

    break;

case "browse":

    // browse works

    break;

case "who":

    // user profile

    break;

case "license":

    // license a work

    break;

case "batch":

    // batch license two or more works

    break;

}

function setupdb() {

    global $dbh;

    $sql = "PRAGMA encoding = UTF-8;";

    $dbh->exec($sql);

    $sql = "CREATE TABLE IF NOT EXISTS `users` (
	    `user_id` INTEGER PRIMARY KEY AUTOINCREMENT,
	    `username` varchar(200) NOT NULL
      );";

    $dbh->exec($sql);

    $sql = "CREATE TABLE IF NOT EXISTS `works` (
	    `work_id`  INTEGER PRIMARY KEY AUTOINCREMENT,
	    `user_id` INTEGER NOT NULL,
        `title` varchar(200) NOT NULL,
        `filename` varchar(200) NOT NULL,
        `license` INT NOT NULL
      );";

    $dbh->exec($sql);

}

?>

      </div>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
  </body>
</html>