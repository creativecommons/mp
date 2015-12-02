<!DOCTYPE html>
<!--
To the extent possible under law, the person who associated CC0 with
this work has waived all copyright and related or neighboring rights
to this work.
-->
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
            <li class="active"><a href=".">Home</a></li>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </nav>

    <div class="container">
      <div class="main-content">
<?php

global $dbh;

$action = $_GET["action"];

$dbh = new PDO('sqlite:/tmp/foo.db');

$foo = $dbh->exec("SELECT COUNT(*) FROM sqlite_master WHERE type='table' AND name='users'");

if ($foo != 1) {
  setupdb();
}

switch ($action) {

default:
?>

    <h1>Hello World!</h1>

    <p>You can <a href="?action=login">Login/Register</a> or <a href="?action=browse">browse exists works</a></p>

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

    $username = $_POST['username'];
    echo $username;

    $sql = "SELECT * FROM users where username = '" . $username . "'";

    $foo = $dbh->exec($sql);

    echo $foo;

    break;

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

    $sql = "CREATE TABLE IF NOT EXISTS `users` (
	`user_ID` INT AUTO_INCREMENT NOT NULL,
	`username` varchar(200) NOT NULL,
        `nickname` varchar(200) NOT NULL
      ) CHARACTER SET utf8 COLLATE utf8_general_ci;";

    $dbh->exec($sql);

    $sql = "CREATE TABLE IF NOT EXISTS `works` (
	`work_ID` INT AUTO_INCREMENT NOT NULL,
	`user_ID` INT NOT NULL,
        `title` varchar(200) NOT NULL,
        `filename` varchar(200) NOT NULL,
        `license` INT NOT NULL
      ) CHARACTER SET utf8 COLLATE utf8_general_ci;";

    $dbh->exec($sql);

}

?>

      </div>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
  </body>
</html>