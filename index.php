<?php

global $dbh;

$action = $_GET["action"];

$dbh = new PDO('sqlite:/tmp/foo.db');

$foo = $dbh->exec("SELECT COUNT(*) FROM sqlite_master WHERE type='table' AND name='users'");

if ($foo != 1) {
  setupdb();
}

switch ($action) {

case "":

    echo "<h1>Hello World!</h1>";

    ?>You can <a href="?action=login">Login/Register</a> or <a href="?action=browse">browse exists works</a><?php

    
    break;
case "login":
    ?>

    <form action="?action=loginprocess" method="post">
    <label>Username</label>
    <input name="username" type="text" maxlength="15" size="15" />
    <input type="submit" />
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

    
    break;
case "new":
    break;
case "browse":
    break;
case "who":
    break;
case "license":
    break;
case "batch":
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
      
     
      //break;

    }
    
?>
