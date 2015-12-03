<!DOCTYPE html>
<!--
To the extent possible under law, the person who associated CC0 with
this work has waived all copyright and related or neighboring rights
to this work.
-->
<?php
////////////////////////////////////////////////////////////
// Utility code
////////////////////////////////////////////////////////////

// These are in license number order
$LICENSE_NAMES =
     ['All Rights Reserved', 'Attribution-NonCommercial-ShareAlike',
      'Creative Commons Attribution-NonCommercial',
      'Creative Commons Attribution-NonCommercial-NoDerivatives',
      'Creative Commons Attribution',
      'Creative Commons Attribution-ShareAlike',
      'Creative Commons Attribution-NoDerivatives',
      'Creative Commons Zero'];

function lic_name ($license_number) {
    global $LICENSE_NAMES;
    return $LICENSE_NAMES[$license_number];
 }

function lic_abbrv ($license_number) {
    // These are in license number order
    return ['All Rights Reserved', 'by-nc-sa', 'by-nc', 'by-nc-nd', 'by',
            'by-sa', 'by-nd', 'zero'][$license_number];
}

function render_options($from, $to, $selected, $any) {
    if ($any) {
        echo '<option '
            . (($selected == '*') ? 'selected ' : '')
            . 'value="*">Any</option>';
    }
    // '*' == '0'
    if ($selected == '*') {
        $selected = -1;
    }
    for ($i = $from; $i <= $to; $i++) {
        echo '<option '
            . (($i == $selected) ? 'selected ' : '')
            . 'value="' . $i . '">'
            . lic_name($i)
            . '</option>';
    }
}

////////////////////////////////////////////////////////////
// Pre-UI rendering environment setup and request processing
////////////////////////////////////////////////////////////

session_start();

global $dbh;

$dbh = new PDO('sqlite:/tmp/foo.db');

$foo = $dbh->exec("SELECT COUNT(*) FROM sqlite_master WHERE type='table' AND name='users'");

if ($foo != 1) {
  setupdb();
}

$action = $_GET["action"];

// Handle page logic here that needs to be resolved before drawing the UI.
// There's another $action switch below in the body to render the correct view.

switch ($action) {

case 'loginprocess':
    $login_status = 'err';
    // Make sure we've been passed a non-empty username
    if ((isset($_POST['username'])) && trim($_POST['username']) != ''){
        $username = trim($_POST['username']);
        $usernameq = $dbh->quote($username);
        // Try to insert the user. We don't care if this fails when they exist.
        $insert_user = "INSERT INTO users (username) VALUES("
            . $usernameq . ")";
        $user_inserted = $dbh->exec($insert_user);
        // Get the user's details now they're definitely inserted
        $select_user = $dbh->prepare("SELECT * FROM users where username = "
                                     . $usernameq);
        $ok = $select_user->execute();
        if ($ok) {
            $user_row = $select_user->fetch();
            // Does the user already exist or do we have to insert them?
            if ($user_row) {
                // The user is already in the db so just use the name
                $_SESSION['username'] = $user_row['username'];
                $_SESSION['user_id'] = $user_row['user_id'];
                $login_status = 'ok';
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

case 'newprocess':
    $upload_status = 'err';
    // Only do this if user is logged in (our handy var for this isn't set yet)
    if (isset($_SESSION['user_id'])) {
        if (isset($_FILES['file'])
           && isset($_POST['title'])
           && isset($_POST['license'])) {
            $filename = "uploads/" . $_FILES["file"]["name"];
            $title = trim($_POST['title']);
            $license = intval($_POST['license']);
            $nc = (int)(($license == 1) || ($license == 2) || ($license == 3));
            $nd = (int)(($license == 3) || ($license == 6));
            //FIXME: Validate things
            if (move_uploaded_file($_FILES["file"]["tmp_name"], $filename)) {
                $file_insert_sql = "INSERT INTO works (user_id, title,
                                                       filename, license,
                                                       nc, nd)
                    VALUES("
                    . $_SESSION['user_id'] . ","
                    . $dbh->quote($title) . ","
                    . $dbh->quote($filename) . ","
                    . $license . ","
                    . $nc . ","
                    . $nd
                    . ")";
                $dbh->exec($file_insert_sql);
                //TODO:GET INSERTED ID AND DISPLAY
                $upload_status = 'uploaded';
            }
        }
    }
    break;

case 'display':
    $display_status = 'err';
    if (isset($_REQUEST['work_id'])){
        //FIXME: validate
        $work_id = intval($_REQUEST['work_id']);
        $select_work = $dbh->prepare("SELECT * FROM works where work_id = "
                                     . $work_id);
        $ok = $select_work->execute();
        if ($ok) {
            $work_row = $select_work->fetch();
            if ($work_row) {
                $select_user = $dbh->prepare("SELECT * FROM users where user_id = " . $work_row['user_id']);
                $ok = $select_user->execute();
                if ($ok) {
                    $user_row = $select_user->fetch();
                    if ($user_row) {
                        $display_status = 'ok';
                    }
                }
            }
        }
    }
    break;

case 'who':
    $who_state = 'err';
    if (isset($_REQUEST['user_id'])) {
        $who_id = $_REQUEST['user_id'];
        $select_user = $dbh->prepare("SELECT * FROM users where user_id = "
                                     . $dbh->quote($who_id));
        $ok = $select_user->execute();
        if ($ok) {
            $user_row = $select_user->fetch();
            if ($user_row) {
                $who_name = $user_row['username'];
                $who_state= 'ok';
            }
        }
    } elseif (isset($_SESSION['user_id'])) {
        $who_id = $_SESSION['user_id'];
        $who_name = $_SESSION['username'];
        $who_state= 'ok';
    }
    if ($who_state == 'ok') {
        $who_works = $dbh->prepare("SELECT * FROM works where user_id = "
                                     . $who_id);
        $ok = $who_works->execute();
        if (! $ok) {
            $who_state = 'err';
        }
    }
    break;
}

// Flag to tell the UI whether the user is logged in or not
$logged_in = isset($_SESSION['user_id']);
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
            <li<?php if ($action == 'browse') { echo ' class="active"'; } ?>>
              <a href="/?action=browse">Browse</a></li>
<?php
if ($logged_in) {
    echo '<li><a href="?action=logoutprocess">Log out</a></li>';
    echo '<li' . (($action == 'profile') ? ' class="active"' : '')
           . '><a href="?action=profile">' . $_SESSION['username']
           . '</a></li>';
} else {
    echo '<li'. (($action == 'login') ? ' class="active"' : '')
        . '><a href="/?action=login">Log In</a></li>';
}
?>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </nav>

    <div class="container">
      <div class="main-content">
<?php
////////////////////////////////////////////////////////////
// View rendering using the data from higher up
////////////////////////////////////////////////////////////

switch ($action) {

default:
?>
    <h1>Welcome To Model Platform!</h1>
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
    if ($login_status == 'err') {
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
?>
    <form action="?action=newprocess" method="post"
      enctype="multipart/form-data">
      <div class="form-group">
        <label for="file">File</label>
        <input name="file" id="file" class="form-control" type="file">
      </div>
      <div class="form-group">
        <label for="title">Title</label>
        <input name="title" id="title" class="form-control" type="text"
          maxlength="200" size="32">
      </div>
      <div class="form-group">
        <label for="license">License</label>
        <select name="license" id="license" class="form-control">
           <?php render_options(0, 7, 4, false); ?>
        </select>
      </div>
      <input type="submit" class="btn btn-default" value="Upload">
    </form>
<?php
    break;

case "browse":
$cl = isset($_POST['license']) ? $_POST['license'] : '*';
?>
    <form action="?action=browse" method="post">
      <div class="form-group">
        <label for="keywords">Keywords</label>
        <input name="keywords" id="keywords" class="form-control" type="text"
          maxlength="200" size="32">
      </div>
      <div class="form-group">
        <label for="license">License</label>
        <select name="license" id="license" class="form-control">
          <?php render_options(0, 7, $cl, true); ?>
        </select>
      </div>
      <input type="submit" class="btn btn-default" value="Search">
    </form>
<?php
    if(isset($browse_results)) {
        echo '<h2>Results</h2>';
        echo '<table class="table table-striped"><thead>';
        echo '<tr><th>Title</th><th>License</th><th>Link</th></tr>';
        echo '</thead><tbody>';
        foreach ($browse_results as $item) {
            echo '<tr><td>' . $item['title'] . '</td>'
                . '<td>' . $item['license_name'] . '</td>'
                .'<td><a href="' . $item['resource_locator']
                . '">' . $item['resource_locator']
                . '</a></td></tr>';
        }
        echo '</tbody></table><p>';
    }
    break;

case "display":
    if ($display_status == 'ok') {
        $license_name = lic_name($work_row['license']);
        $license_abbrv = lic_abbrv($work_row['license']);
?>
    <a href="?action=display&work_id=<?php echo $work_row['work_id']; ?>">
      <h2 class="display-title"><?php echo $work_row['title']; ?></a> by
    <a href="?who&user_id=<?php echo $user_row['user_id']; ?>">
      <?php echo $user_row['username'] ?></a></h2>
    <img src="<?php echo $work_row['filename'] ?>">
    <?php if ($work_row['license'] == 0) {
?>
    <a href="?action=display&work_id=<?php echo $work_row['work_id']; ?>"><?php echo $work_row['title']; ?></a> by <a href="?who&user_id=<?php echo $user_row['user_id']; ?>"><?php echo $user_row['username']; ?></a>.
<?php } elseif ($work_row['license'] == 7) { ?>
    <p xmlns:dct="http://purl.org/dc/terms/">
      <a rel="license"
        href="http://creativecommons.org/publicdomain/zero/1.0/">
        <img src="http://i.creativecommons.org/p/zero/1.0/88x31.png"
          style="border-style: none;" alt="CC0">
      </a>
      <br>
      To the extent possible under law,
      <a rel="dct:publisher"
        href="?who&user_id=<?php echo $user_row['user_id']; ?>">
        <span property="dct:title"><?php echo $user_row['username'] ?></span>
      </a>
      has waived all copyright and related or neighboring rights to
      <a href= property="dct:title"><?php echo $work_row['title']; ?></a>.
    </p>
<?php } else  { ?>
    <a rel="license" href="http://creativecommons.org/licenses/<?php echo $license_abbrv; ?>/4.0/"><img alt="Creative Commons License" style="border-width:0" src="https://i.creativecommons.org/l/<?php echo $license_abbrv; ?>/4.0/88x31.png" /></a><br /><a href="?action=display&work_id=<?php echo $work_row['work_id']; ?>" xmlns:dct="http://purl.org/dc/terms/" href="http://purl.org/dc/dcmitype/StillImage" property="dct:title" rel="dct:type"><?php echo $work_row['title']; ?></a> by <a xmlns:cc="http://creativecommons.org/ns#" href="?who&user_id=<?php echo $user_row['user_id']; ?>" property="cc:attributionName" rel="cc:attributionURL"><?php echo $user_row['username']; ?></a> is licensed under a <a rel="license" href="http://creativecommons.org/licenses/<?php echo $license_abbrv; ?>/4.0/">Creative Commons <?php echo $license_name; ?> 4.0 International License</a>.
    <div class="show-button-section">
      <a class="btn btn-primary"
        href="#">Copy Attribution</a>
      <a class="btn btn-primary"
        href="<?php echo $work_row['filename'] ?>">Download Image &#x25BC;</a>
    </div>
<?php
        }
    } else {
?>
    <h1>No Work Specified</h1>
<?php
    }
    break;

case "who":
    if ($who_state == 'ok') {
?>
    <h1><a href="?action=who&user_id=<?php echo $who_id; ?>">
          <?php echo $who_name; ?></a></h1>
    <h3>Works by <?php echo $who_name; ?></h3>
      <table class="table table-striped"><thead>
        <tr><th>Title</th><th>License</th></tr>
        </thead><tbody>
<?php
        foreach ($who_works as $work) {
            $lic = "All rights reserved";
            if ($work['license'] == 7) {
                $lic = '<a href="http://creativecommons.org/publicdomain/zero/1.0/">Creative Commons Zero</a>';
            } else {
                $lic = '<a href="http://creativecommons.org/licenses/'
                    . lic_abbrv($work['license'])
                    . '/4.0/">Creative Commons '
                    . lic_name($work['license'])
                    . ' 4.0 International License</a>';
            }
            echo '<tr><td><a href="?action=display&work_id='
                 . $work['work_id'] . '">' . $work['title'] . '</a></td><td>'
                 . $lic . '</td></tr>';
        }
?>
    </tbody></table>
<?php
    } else {
?>
    <h2>No user specified or logged in.</h2>
<?php
    }
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
	    `work_id` INTEGER PRIMARY KEY AUTOINCREMENT,
	    `user_id` INTEGER NOT NULL,
        `title` varchar(200) NOT NULL,
        `filename` varchar(200) NOT NULL,
        `license` INT NOT NULL,
        `nc` BOOL NOT NULL,
        `nd` BOOL NOT NULL
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