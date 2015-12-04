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
$LICENSE_NAMES = [
    'All Rights Reserved', 'Attribution-NonCommercial-ShareAlike',
    'Creative Commons Attribution-NonCommercial',
    'Creative Commons Attribution-NonCommercial-NoDerivatives',
    'Creative Commons Attribution',
    'Creative Commons Attribution-ShareAlike',
    'Creative Commons Attribution-NoDerivatives',
    'Creative Commons Zero'
    ];

function lic_name ($license_number) {
    global $LICENSE_NAMES;
    return $LICENSE_NAMES[$license_number];
 }

function lic_abbrv ($license_number) {
    // These are in license number order
    return ['All Rights Reserved', 'by-nc-sa', 'by-nc', 'by-nc-nd', 'by',
            'by-sa', 'by-nd', 'zero'][$license_number];
}

// int for sql insertion

function lic_nc ($license) {
    return (int)(($license == 1) || ($license == 2) || ($license == 3));
}

// int for sql insertion

function lic_nd ($license) {
    return (int)(($license == 3) || ($license == 6));
}

function license_block ($dbh, $work) {
    $user = user_for_id($dbh, $work['user_id']);
    $license_name = lic_name($work['license']);
    $license_abbrv = lic_abbrv($work['license']);
    if ($work['license'] == 0) {
        $block = '<a href="?action=display&work_id=' . $work['work_id']
               . '">' . $work['title'] . '</a> by <a href="?who&user_id='
               . $user['user_id'] .'">' . $user['username'] . '</a>.';
    } elseif ($work['license'] == 7) {
        $block = '<p xmlns:dct="http://purl.org/dc/terms/">
      <a rel="license"
        href="http://creativecommons.org/publicdomain/zero/1.0/">
        <img src="http://i.creativecommons.org/p/zero/1.0/88x31.png"
          style="border-style: none;" alt="CC0">
      </a>
      <br>
      To the extent possible under law,
      <a rel="dct:publisher"
        href="?who&user_id=' . $user['user_id'] . '">
        <span property="dct:title">' . $user['username'] . '</span>
      </a>
      has waived all copyright and related or neighboring rights to
      <a href="?action=display&work_id=' . $work['work_id']
               . '" property="dct:title">' . $work['title']
               . '</a>.</p>';
    } else  {
        $block = '<a rel="license" href="http://creativecommons.org/licenses/'
               . $license_abbrv . '/4.0/"><img alt="Creative Commons License" style="border-width:0" src="https://i.creativecommons.org/l/' . $license_abbrv
               . '/4.0/88x31.png" /></a><br /><a href="?action=display&work_id='
               . $work['work_id'] . '" xmlns:dct="http://purl.org/dc/terms/" href="http://purl.org/dc/dcmitype/StillImage" property="dct:title" rel="dct:type">'
               . $work['title'] .'</a> by <a xmlns:cc="http://creativecommons.org/ns#" href="?who&user_id='
               . $user['user_id'] . '" property="cc:attributionName" rel="cc:attributionURL">'
               . $user['username'] . '</a> is licensed under a <a rel="license" href="http://creativecommons.org/licenses/'
               . $license_abbrv . '/4.0/">' . $license_name
               . ' 4.0 International License</a>.';
    }
    return $block;
}

// Print the options for a license select, optionally with an "All" entry

function render_license_options($from, $to, $selected, $any) {
    if ($any) {
        echo '<option '
            . (($selected == '*') ? 'selected ' : '')
            . 'value="*">Any</option>';
    }
    // '*' == '0', so change it to a value that doesn't
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

// World's worst and slowest full-text search

function browse_sql ($keywords_string, $license) {
    $keywords = explode(' ', $keywords_string);
    $queries = [];
    foreach ($keywords as $keyword) {
        if (strlen($keyword) >= 3) {
            $queries[] = "'%" . $keyword . "%'";
        }
    }
    $license_constraint = '';
    // * == any, so don't constrain the search in that case
    if ($license != '*') {
        $license_constraint = 'AND license = ' . intval($license);
    }
    return 'SELECT * FROM works WHERE title LIKE '
        . implode(' OR title LIKE ', $queries)
        . $license_constraint;
}

function user_for_id ($dbh, $user_id) {
    $user_row = false;
    $select_user = $dbh->prepare("SELECT * FROM users where user_id = "
                                 . $user_id);
    $ok = $select_user->execute();
    if ($ok) {
        $user_row = $select_user->fetch();
    }
    return $user_row;
}

function work_for_id ($dbh, $work_id) {
    $work_row = false;
    $select_work = $dbh->prepare("SELECT * FROM works where work_id = "
                               . $work_id);
    $ok = $select_work->execute();
    if ($ok) {
        $work_row = $select_work->fetch();
    }
    return $work_row;
}

function works_for_user ($dbh, $user_id) {
    $user_works = $dbh->prepare("SELECT * FROM works where user_id = "
                                . $user_id);
    $ok = $user_works->execute();
    if (! $ok) {
        $user_works = false;
    }
    return $user_works;
}

function user_name_for_work ($dbh, $work) {
    $user_name = false;
    $user_row = user_for_id($dbh, $work['user_id']);
    if ($user_row) {
        $user_name = $user_row['username'];
    }
    return $user_name;
}

function update_work_license($dbh, $work, $license) {
    $update_lic = 'UPDATE works SET license=' . $license
                . ', nc=' . lic_nc($license) . ', nd=' . lic_nd($license)
                . ' WHERE work_id=' . $work['work_id'];
    $ok = $dbh->exec($update_lic);
    return $ok;
}

function thumbnail ($work) {
    return '<a alt="' . $work['title']
         . '" href="?action=display&work_id=' . $work['work_id']
         .'"><img width="64" src="'
         . $work['filename'] . '"></a></div>';
}

////////////////////////////////////////////////////////////
// Pre-UI rendering environment setup and request processing
////////////////////////////////////////////////////////////

session_start();

$action = strip_tags($_GET["action"]);

if (($action == '') && isset($_SESSION['user_id'])) {
    header('Location:?action=browse');
    exit;
}

global $dbh;

$dbh = new PDO('sqlite:' . dirname(__FILE__) . '/foo.db');

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
        $username = strip_tags(trim($_POST['username']));
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
                //$login_status = 'ok';
                header('Location:?action=browse');
                exit;
            } else {
                $action = 'loginfailed';
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
            $title = strip_tags(trim($_POST['title']));
            $license = intval($_POST['license']);
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
                    . lic_nc($license) . ","
                    . lic_nd($license)
                    . ")";
                $dbh->exec($file_insert_sql);
                $upload_status = 'uploaded';
                $work_id = $dbh->lastInsertId();
                header('Location:?action=display&work_id=' . $work_id);
                exit;
            }
        }
    }
    break;

case 'browse':
    $browse_status = 'get';
    if (isset($_POST['keywords']) && isset($_POST['keywords'])) {
        $browse_status = 'err';
        $keywords = strip_tags($_POST['keywords']);
        $license = intval($_POST['license']);
        $keywords_query = browse_sql($keywords, $license);
        $keywords_matches_statement = $dbh->prepare($keywords_query);
        if ($keywords_matches_statement) {
            $ok = $keywords_matches_statement->execute();
            if ($ok) {
                // Get all the results in an array so we can count them
                $keywords_matches = $keywords_matches_statement->fetchAll();
                $browse_status = 'ok';
            }
        }
    }
    break;

case 'display':
    $display_status = 'err';
    if (isset($_REQUEST['work_id'])){
        //FIXME: validate
        $work_id = intval($_REQUEST['work_id']);
        $work_row = work_for_id($dbh, $work_id);
        if ($work_row) {
            $user_row = user_for_id($dbh, $work_row['user_id']);
            if ($user_row) {
                $display_status = 'ok';
            }
        }
    }
    break;

case "license":
    //$license_state = 'err';
    if (isset($_SESSION['user_id']) && isset($_REQUEST['work_id'])) {
        $user_id = intval($_SESSION['user_id']);
        $work_id = intval($_REQUEST['work_id']);
        $license_work = work_for_id($dbh, $work_id);
        if ($license_work && $license_work['user_id'] == $user_id) {
            if (isset($_POST['license'])) {
                $license = intval($_POST['license']);
                update_work_license($dbh, $license_work, $license);
                // Get the updated details to display
                $license_work = work_for_id($dbh, $work_id);
                //$license_state = 'ok';
                header('Location:?action=display&work_id=' . $work_id);
                exit;
            }
        } else {
            $license_work = false;
        }
     }
    break;

case "batch":
    $batch_state = 'err';
        if (isset($_SESSION['user_id'])) {
        if (isset($_POST['license']) && isset($_POST['apply'])
            && is_array($_POST['apply'])) {
            $license = intval($_POST['license']);
            foreach($_POST['apply'] as $apply) {
                $work_id = intval($apply);
                $work = work_for_id($dbh, $work_id);
                // We can only update our own images
                if ($work && $work['user_id'] == $_SESSION['user_id']) {
                    update_work_license($dbh, $work, $license);
                }
            }
        }
        $batch_works = works_for_user($dbh, $_SESSION['user_id']);
        $batch_state = 'ok';
    }
    break;

case 'who':
    $who_state = 'err';
    // The user is requesting to look at someone's profile
    if (isset($_REQUEST['user_id'])) {
        $who_id = intval($_REQUEST['user_id']);
        $select_user = $dbh->prepare("SELECT * FROM users where user_id = "
                                     . $who_id);
        $ok = $select_user->execute();
        if ($ok) {
            $user_row = $select_user->fetch();
            if ($user_row) {
                $who_name = $user_row['username'];
                $who_state= 'ok';
            }
        }
    // The user is requesting to look at their own profile
    } elseif (isset($_SESSION['user_id'])) {
        $who_id = $_SESSION['user_id'];
        $who_name = $_SESSION['username'];
        $who_state= 'ok';
    }
    if ($who_state == 'ok') {
        $who_works = works_for_user ($dbh, $who_id);
        if (! $who_works) {
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
          <button type="button" class="navbar-toggle collapsed"
                  data-toggle="collapse" data-target="#navbar"
                  aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href=".">Model Platform</a>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
            <li<?php if ($action == 'browse') { echo ' class="active"'; } ?>>
              <a href="?action=browse">Browse</a></li>
<?php
if ($logged_in) {
    echo '<li' . (($action == 'new') ? ' class="active"' : '')
       . '><a href="?action=new">Upload</a></li>';
    echo '</ul>
          <ul class="nav navbar-nav navbar-right">
            <li'  . (($action == 'who') ? ' class="active"' : '')
       . '      class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown"
                  role="button" aria-haspopup="true" aria-expanded="false">'
       . $_SESSION['username']
       . '    <span class="caret"></span></a>
              <ul class="dropdown-menu">
                <li'  . (($action == 'who') ? ' class="active"' : '') . '>'
       . '        <a href="?action=who">My Profile</a></li>
                <li><a href="?action=logoutprocess">Log Out</a></li>
              </ul>
          </li>';
} else {
    echo '</ul>
          <ul class="nav navbar-nav navbar-right">';
    echo '<li' . (($action == 'login') ? ' class="active"' : '')
       . '><a href="?action=login"><strong>Log In</strong></a></li>';
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
    <h1>Welcome to Model Platform!</h1>
    <p>You can <a href="?action=login">Login/Register</a> or
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

// A successful login just falls through to the default
// and goes to the welcome page

case "loginfailed":
?>
    <h1>Something went wrong</h1>
    <p>We're sorry about that! <a href="?action=login">Please try again</a>.</p>
<?php
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
           <?php render_license_options(0, 7, 4, false); ?>
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
           maxlength="200" size="32"
          <?php
          if (isset($_POST['keywords'])) {
              echo 'value="' . strip_tags($_POST['keywords']) . '"';
          }
          ?>>
      </div>
      <div class="form-group">
        <label for="license">License</label>
        <select name="license" id="license" class="form-control">
          <?php render_license_options(0, 7, $cl, true); ?>
        </select>
      </div>
      <input type="submit" id="search" class="btn btn-primary"
        value="Search" disabled>
    </form>
    <script>
     var keywords_field = document.getElementById('keywords');
     var search_field = document.getElementById('search');
     keywords_field.onkeyup = keywords_changed;
     keywords_field.onchange = keywords_changed;
     function keywords_changed() {
         if (keywords_field.value.length > 0) {
             console.log(1);
             search_field.disabled = false
         } else {
             search_field.disabled = true;
         }
     }
    </script>
<?php
    if($browse_status == 'ok') {
        if (count($keywords_matches) > 0) {
            echo '<h2>Results</h2>';
            echo '<table class="table table-striped"><thead>';
            echo '<tr><th>Image</th><th>Title</th><th>User</th><th>License</th></tr>';
            echo '</thead><tbody>';
            foreach ($keywords_matches as $work) {
                $lic = "All rights reserved";
                if ($work['license'] == 7) {
                    $lic = '<a href="http://creativecommons.org/publicdomain/zero/1.0/">Creative Commons Zero</a>';
                } else {
                    $lic = '<a href="http://creativecommons.org/licenses/'
                         . lic_abbrv($work['license'])
                         . '/4.0/">'
                         . lic_name($work['license'])
                         . ' 4.0 International</a>';
                }
                echo '<tr><td>' . thumbnail($work)
                   . '</td><td><a href="?action=display&work_id='
                   . $work['work_id'] . '">' . $work['title'] . '</a></td><td>'
                   . '<a href="?action=who&user_id='
                   . $work['user_id'] . '">'
                   . user_name_for_work($dbh, $work) . '</a></td><td>'
                   . $lic . '</td></tr>';
            }
            echo '</tbody></table><p>';
        } else {
            echo '<h2>Results</h2>';
            echo '<div class="alert alert-info" role="alert"><strong>None found.</strong> Please try again with different (maybe fewer or simpler) keywords.</div>';
        }
    }
    break;

case "display":
    if ($display_status == 'ok') {
?>
    <h2 class="display-title"><?php echo $work_row['title']; ?> by
    <a href="?action=who&user_id=<?php echo $user_row['user_id']; ?>">
      <?php echo $user_row['username'] ?></a></h2>
    <div class="display-image"><img alt="<?php echo $work_row['title']; ?>"
       src="<?php echo $work_row['filename'] ?>"></div>
    <div id="license-block">
      <?php echo license_block($dbh, $work_row); ?>
    </div>
    <div class="display-buttons">
      <a class="btn btn-info" id="copy-attribution-button"
          href="#">Copy Attribution</a>
      <a class="btn btn-success" download
          href="<?php echo $work_row['filename'] ?>">Download Image
        <span class="glyphicon glyphicon-download-alt"
            aria-hidden="true"></span></a>
<?php
    if ($work_row['user_id'] == $_SESSION['user_id']) {
?>
        <a class="btn btn-danger"
        href="?action=license&work_id=<?php echo $work_row['work_id'] ?>">
         Change license</a>
            <?php } ?>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/1.5.3/clipboard.min.js"></script>
    <script>
     new Clipboard('#copy-attribution-button', {
         text: function(trigger) {
             return document.getElementById("license-block").innerHTML.trim()
                            .replace(/&lt;/g,'<').replace(/&gt;/g,'>')
                            .replace(/&amp;/g,'&') + "\n";
         }
     });
    </script>
<?php
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
        <tr><th>Image</th><th>Title</th><th>License</th></tr>
        </thead><tbody>
<?php
        foreach ($who_works as $work) {
            $lic = "All rights reserved";
            if ($work['license'] == 7) {
                $lic = '<a href="http://creativecommons.org/publicdomain/zero/1.0/">Creative Commons Zero</a>';
            } elseif ($work['license'] > 0) {
                $lic = '<a href="http://creativecommons.org/licenses/'
                    . lic_abbrv($work['license'])
                    . '/4.0/">'
                    . lic_name($work['license'])
                    . ' 4.0 International</a>';
            }
            echo '<tr><td>' . thumbnail($work)
                .'</td><td><a href="?action=display&work_id='
                . $work['work_id'] . '">' . $work['title'] . '</a></td><td>'
                . $lic . '</td></tr>';
        }
?>
    </tbody></table>
    <div class="who-buttons"><a class="btn btn-primary"
        href="?action=batch">Change licenses</a></div>
<?php
    } else {
?>
    <h2>No user specified or logged in.</h2>
<?php
    }
    break;

case "license":
    if (! isset($license_work)) {
?>
        <h2>No work specified.</h2>
<?php
    } elseif ($logged_in) {
?>
    <h1>(Re)License This Work</h1>
    <p>To apply a new license this work, choose the new license below.</p>
    <h2 class="display-title">
       <a href="?action=display&work_id=<?php
         echo $license_work['work_id']; ?>">
         <?php echo $license_work['title']; ?></a></h2>
    <div class="display-image"><img alt="<?php echo $license_work['title']; ?>"
       src="<?php echo $license_work['filename'] ?>"></div>
    <?php echo license_block($dbh, $license_work); ?>
    <form action="?action=license" method="post">
      <input type="hidden" name="work_id"
         value="<?php echo $license_work['work_id']; ?>">
      <div class="form-group">
        <label for="license">License</label>
        <select name="license" id="license" class="form-control">
           <?php render_license_options(0, 7, $license_work['license'],
                                        false); ?>
        </select>
      </div>
      <input type="submit" class="btn btn-default" value="Change">
    </form>
<?php
    } else {
?>
    <h2>Not logged in.</h2>
<?php
    }
    break;

case "batch":
    if ($logged_in) {
?>
    <h1>Batch (Re)License Works</h1>
    <p>To apply a new license to works, select the check box next to the image
      and then choose the license below.</p>
    <form action="?action=batch" method="post">
      <table class="table table-striped"><thead>
        <tr><th>Apply</th><th>Image</th><th>Title</th><th>Current License</th></tr>
<?php
    foreach ($batch_works as $work) {
        $lic = "All rights reserved";
        if ($work['license'] == 7) {
            $lic = '<a href="http://creativecommons.org/publicdomain/zero/1.0/">Creative Commons Zero</a>';
        } else {
            $lic = '<a href="http://creativecommons.org/licenses/'
                 . lic_abbrv($work['license'])
                 . '/4.0/">'
                 . lic_name($work['license'])
                 . ' 4.0 International</a>';
        }
        echo '<tr><td><input type="checkbox" name="apply[]" value="'
             . $work['work_id'] . '"></td><td>' . thumbnail($work)
             . '</td><td><a href="?action=display&work_id='
             . $work['work_id'] . '">' . $work['title'] . '</a></td><td>'
             . $lic . '</td></tr>';
    }
?>
    </tbody></table>
    <div class="form-group">
        <label for="license">License</label>
        <select name="license" id="license" class="form-control">
           <?php render_license_options(0, 7, 4, false); ?>
        </select>
      </div>
      <input type="submit" class="btn btn-default" value="Change">
    </form>
<?php
    } else {
?>
    <h2>Not logged in.</h2>
<?php
    }
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
