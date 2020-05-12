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

////////////////////////////////////////////////////////////
// Licenses
////////////////////////////////////////////////////////////


// The license numbers are arbitrary within this codebase.

// Everything that isn't all rights reserved

$LICENSE_RANGE = range(1, 7);

// In order of "restriction".

$LICENSE_RANGE_FREEDOM = [7, 4, 5, 2, 1, 6, 3];

// Get the genimg code for the license number
// FIXME: USE CC BUTTON URL FORMAT

function lic_genimg_code ($index) {
    // Codes are for historical reasons (placement of icons in font)
    // "-" will never be used, it's to make this a simple index lookup
    $LIC_GENIMG_CODES = ['-', 'bna', 'bn', 'bnd', 'b', 'ba', 'bd', '0'];
    return $LIC_GENIMG_CODES[$index];
}

// Look up the license name for the license number

function lic_name ($license_number) {
    // These are in license number order.
    $LICENSE_NAMES = [
    'All Rights Reserved',
    'Creative Commons Attribution-NonCommercial-ShareAlike',
    'Creative Commons Attribution-NonCommercial',
    'Creative Commons Attribution-NonCommercial-NoDerivatives',
    'Creative Commons Attribution',
    'Creative Commons Attribution-ShareAlike',
    'Creative Commons Attribution-NoDerivatives',
    'Creative Commons Zero'
    ];
    return $LICENSE_NAMES[$license_number];
 }

// Look up the license abbreviation for the license number

function lic_abbrv ($license_number) {
    // These are in license number order
    return ['All Rights Reserved', 'by-nc-sa', 'by-nc', 'by-nc-nd', 'by',
            'by-sa', 'by-nd', 'zero'][$license_number];
}

// Get the button image url (or span) for the license

function lic_button($license) {
    if ($license == 0) {
        $logo = '<span class="copyright-logo">&copy</span>';
    } elseif ($license == 7) {
        $logo = '<img src="http://i.creativecommons.org/p/zero/1.0/88x31.png"
                   style="border-style: none;" alt="CC0">';
    } else {
        $abbrev = lic_abbrv($license);
        $logo = '<img alt="Creative Commons License" style="border-width:0"
                   src="https://i.creativecommons.org/l/$abbrev/4.0/88x31.png"
                    style="border-width:0">';
    }
    return $logo;
}

// The icons image (or span) for the license. Plural as BY-SA has 2 icons etc.

function lic_icons ($base_url, $license) {
    if ($license == 0) {
        $icons = '<span class="copyright-logo">&copy</span>';
    } else {
        $code = lic_genimg_code($license);
        return "<img src=\"$base_url/genimg/genimg.php?b=ffffff&l=$code\"
                  style=\"border-width:0\">";
    }
    return $icons;
}

// Return a link to the license for use in a (vertically compact) table row

function license_for_table ($work_license) {
    $lic = "All rights reserved";
    if ($work_license > 0) {
        if ($work_license == 7) {
            $license_url = 'http://creativecommons.org/publicdomain/zero/1.0/';
            $license_name = 'Creative Commons Zero';
        } else {
            $abbrev = lic_abbrv($work_license);
            $name = lic_name($work_license);
            $license_url = "http://creativecommons.org/licenses/$abbrev/4.0/";
            $license_naame = "$name 4.0 International";
        }
        $lic = "<a href=\"$license_url\">$license_name</a>";
    }
    return $lic;
}

// Generate the license metadata, after:
//  http://creativecommons.org/choose/
// and
//  https://wiki.creativecommons.org/wiki/Best_practices_for_attribution

function license_block ($dbh, $base_url, $work) {
    $user = user_for_id($dbh, $work['user_id']);
    $user_id = $user['user_id'];
    $user_name = $user['username'];
    $user_url = $base_url . '?who&user_id=' . $user_id;
    $work_title = $work['title'];
    $work_id = $work['work_id'];
    $work_url = $base_url . '?action=display&work_id=' . $work_id;
    if ($work['license'] == 0) {
        $lic = "<a href=\"$work_url\">$work_title</a>
                by <a href=\"$user_url\">$user_name</a>.";
    } elseif ($work['license'] == 7) {
        $lic = "<p xmlns:dct=\"http://purl.org/dc/terms/\">
                  <a rel=\"license\"
                    href=\"http://creativecommons.org/publicdomain/zero/1.0/\">
                    <img src=\"$base_url/genimg/genimg.php?b=ffffff&l=0\"
                      style=\"border-style: none;\" alt=\"CC0\">
                  </a>
                  <br>
                  To the extent possible under law,
                  <a rel=\"dct:publisher\"
                    href=\"$user_url\">
                    <span property=\"dct:title\">$user_name</span>
                  </a>
                  has waived all copyright and related or neighboring rights to
                  <a href=\"$work_url\"
                    property=\"dct:title\">$work_title</a>.</p>";
    } else  {
        $license_name = lic_name($work['license']) . ' 4.0 International License';
        $license_abbrv = lic_abbrv($work['license']);
        $mediatype_url = work_mediatype($work);
        $gencode = lic_genimg_code($work['license']);
        $icon_url = "$base_url/genimg/genimg.php?b=ffffff&l=$gencode";
        $license_url = "http://creativecommons.org/licenses/$license_abbrv/4.0/";
        $lic = "<a rel=\"license\" href=\"$license_url\">
                  <img alt=\"Creative Commons License\"
                    style=\"border-width:0\" src=\"$icon_url\" /></a>
                <br />
                <a href=\"$work_url\">
                  <span xmlns:dct=\"http://purl.org/dc/terms/\"
                    href=\"$mediatype_url\" property=\"dct:title\"
                    rel=\"dct:type\">$work_title</span></a>
                by <a xmlns:cc=\"http://creativecommons.org/ns#\"
                     href=\"$user_url\" property=\"cc:attributionName\"
                     rel=\"cc:attributionURL\">$user_name</a>
                is licensed under a <a rel=\"license\" href=\"$license_url\">
                $license_name</a>.";
    }
    return $lic;
}

// Print the options for a license select, optionally with an "All" entry

function license_option ($index, $selection_index) {
    $selected = ($index == $selection_index) ? 'selected ' : '';
    $name = lic_name($index);
    return "<option value=\"$index\" $selected>$name</option>";
}

function license_options ($selected, $any) {
    $options = '';
    if ($any) {
        $options = //.= '<optgroup label="Any License">' .
                   '<option ' . (($selected == '*') ? 'selected ' : '')
                  . 'value="*">Any Terms</option>'
                  //. '</optgroup>'
        ;
    }
    // '*' == '0', so change it to a value that doesn't
    if ($selected == '*') {
        $selected = -1;
    }
    $options .= '<optgroup label="Public Domain">';
    $options .= license_option(7, $selected);
    $options .= '</optgroup>';
    $options .= '<optgroup label="Creative Commons Licenses">';
    $options .= license_option(4, $selected);
    $options .= license_option(5, $selected);
    $options .= license_option(2, $selected);
    $options .= license_option(1, $selected);
    $options .= license_option(6, $selected);
    $options .= license_option(3, $selected);
    $options .= '</optgroup>';
    $options .= '<optgroup label="Default Copyright">';
    $options .= license_option(0, $selected);
    $options .= '</optgroup>';
    return $options;
}

////////////////////////////////////////////////////////////////////////////////
// Presenting works and information
////////////////////////////////////////////////////////////////////////////////

// Print a table listing works, for example from a search or for a user.
//FIXME: This is inefficient: ifs in loop and fetch user name each time
//FIXME: Generalise to handle the relicensing table
//FIXME: Just pass a list of columns and look up how to print their th/td

function print_works_table ($dbh, $works, $show_user, $show_license) {
    echo '<table class="table table-striped"><thead>';
    echo '<tr><th></th><th>Title</th>';
    if ($show_user) {
        echo '<th>User</th>';
    }
    if ($show_license) {
        echo '<th>License</th>';
    }
    echo '</tr>';
    echo '</thead><tbody>';
    foreach ($works as $work) {
        echo '<tr><td class="preview-td">' . work_thumbnail($work)
           . '</td><td><a href="?action=display&work_id='
           . $work['work_id'] . '">' . $work['title'] . '</a></td>';
        if ($show_user) {
            echo '<td class="user-td">'
               . '<a href="?action=who&user_id='
               . $work['user_id'] . '">'
               . user_name_for_work($dbh, $work) . '</a></td>';
        }
        if($show_license) {
            $lic = license_for_table ($work['license']);
            echo '<td class="license-td">' . $lic . '</td>';
        }
        echo '</tr>';
    }
    echo '</tbody></table><p>';
}

// Print the list of changes to the works's license (if any)

function print_work_license_changes ($changes) {
    if ($changes) {
        echo '<h2>License Changes</h2>
              <table class="table table-striped"><thead>
                  <tr><th>License</th><th>Date Applied</th></tr>
                </thead><tbody>';
        foreach ($changes as $change) {
            $license = license_for_table ($change['license']);
            $datetime = strtotime($change['when']);
            $timestamp = date('l jS \of F Y h:i:s A', $datetime);
            echo "<tr><td>$license</td><td>$timestamp</td></tr>";
        }
        echo '</tbody></table>';
    }
}

////////////////////////////////////////////////////////////
// Database querying
////////////////////////////////////////////////////////////

// World's worst and slowest full-text search

function keywords_for_search ($keywords_string) {
    return array_filter(explode(' ', $keywords_string),
                             function ($keyword) {
                                 return strlen($keyword) >= 3;
                             });
}

function search_sql ($keywords, $license) {
    $queries = [];
    foreach ($keywords as $keyword) {
        $queries[] = "'%" . $keyword . "%'";
    }
    $license_constraint = '';
    // * == any, so don't constrain the search in that case
    if ($license != '*') {
        $license_constraint = 'AND license = ' . intval($license);
    }
    return 'SELECT * FROM works WHERE title LIKE '
        . implode(' OR title LIKE ', $queries)
        . $license_constraint . ' ORDER BY work_id DESC';
}

// Select the $count most recent works with $license

function list_works_with_license ($dbh, $license, $count) {
    $sql = 'SELECT * FROM works WHERE license=' . $license
         . ' ORDER BY work_id DESC LIMIT ' . $count;
    $statement = $dbh->prepare($sql);
    $statement->execute();
    return $statement;
}

function count_works_with_license ($dbh, $license) {
    $sql = 'SELECT COUNT(*) FROM works WHERE license=' . $license;
    $result = $dbh->query($sql);
    return $result->fetch(PDO::FETCH_NUM)[0];
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
                                . $user_id . ' ORDER BY work_id DESC');
    $ok = $user_works->execute();
    if ($ok) {
        // So we can count them
        $user_works = $user_works->fetchAll();
    } else {
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

// Record that the license of the work changed (including initial creation)

function license_changed_for_work ($dbh, $work_id, $license) {
    $sql = 'INSERT INTO works_license_changes (work_id, license) VALUES ('
         . $work_id . ', ' . $license . ')';
    $ok = $dbh->exec($sql);
    return $ok;
}

// List the license changes for the work, excluding the current one
// (we show that separately)

function license_changes_for_work ($dbh, $work_id) {
    // Select all previous states, which may be none for a new work
    $sql = "SELECT * FROM works_license_changes
                              where work_id=" . $work_id
                           . " AND change_id
                                   < (SELECT MAX(change_id)
                                       FROM works_license_changes)
                               ORDER BY change_id DESC";
    $changes = $dbh->prepare($sql);
    $ok = $changes->execute();
    if ($ok) {
        // Get all the results in an array so we can count them
        $changes = $changes->fetchAll();
    } else {
        $changes = false;
    }
    return $changes;
}

// The user changed the work's license, so set this on the work and insert
// a record of the change into the changes table.

function update_work_license ($dbh, $work_id, $license) {
    $update_lic = 'UPDATE works SET license=' . $license
                . ' WHERE work_id=' . $work_id;
    $ok = $dbh->exec($update_lic);
    if ($ok) {
        $ok = license_changed_for_work($dbh, $work_id, $license);
    }
    return $ok;
}

// Users have a default license for their work.

function update_user_default_license ($dbh, $user_id, $license) {
    $update_lic = 'UPDATE users SET default_license=' . $license
                . ' WHERE user_id=' . $user_id ;
    $ok = $dbh->exec($update_lic);
    return $ok;
}

function user_default_license ($dbh, $user_id) {
    $license = 0;
    $select_user = $dbh->prepare("SELECT * FROM users where user_id = "
                               . $user_id);
    $ok = $select_user->execute();
    if ($ok) {
        $user_row = $select_user->fetch();
        if ($user_row) {
            $license = $user_row['default_license'];
        }
    }
    return $license;
}

////////////////////////////////////////////////////////////
// Work upload/doanload/identification
////////////////////////////////////////////////////////////

// Send the work to the client with its original filename

function download_work ($work) {
    header('Content-Disposition: attachment; filename="'
         . $work['filename'] . '"');
    readfile(dirname(__FILE__) . '/uploads/' . $work['uuidfilename']);
}

// Check whether we accept this kind of file or not.
// Remember to check the list in the upload file accept field
// Also update table & comments about 8 char extension max if you add longer one

function file_valid ($filename) {
    $extension = pathinfo($filename, PATHINFO_EXTENSION);
    return in_array($extension, ['gif', 'jpg', 'jpeg',
                                'markdown', 'md', 'mp3', 'mp4',
                                'ogg', 'ogv', 'png', 'txt']);
}

// We display works differently depending on whether they're image/audio/etc.

function work_kind ($work) {
    $extension = pathinfo($work['filename'], PATHINFO_EXTENSION);
    $kind = 'img';
    if (in_array($extension, ['ogg', 'ogv', 'mp4'])) {
        $kind = 'vid';
    } elseif (in_array($extension, ['oga', 'mp3'])) {
        $kind = 'aud';
    } elseif (in_array($extension, ['txt', 'md', 'markdown'])) {
        $kind = 'txt';
    }
    return $kind;
}

////////////////////////////////////////////////////////////
// Displaying works in html
////////////////////////////////////////////////////////////

// The Dublin Core media types for our work categories.

function work_mediatype ($work) {
    $kind = work_kind($work);
    switch ($kind) {
        case 'img':
            $type = 'http://purl.org/dc/dcmitype/StillImage';
            break;
        case 'vid':
            $type = 'http://purl.org/dc/dcmitype/MovingImage';
            break;
        case 'aud':
            $type = 'http://purl.org/dc/dcmitype/Sound';
            break;
       case 'txt':
            $type = 'http://purl.org/dc/dcmitype/Text';
            break;
    }
    return $type;
}

// The thumbnail for the work in a table listing.

function work_thumbnail ($work) {
    $thumb = '<a alt="' . $work['title']
           . '" href="?action=display&work_id=' . $work['work_id'] . '">';
    $kind = work_kind($work);
    switch ($kind) {
        case 'img':
            $thumb .= '<img height="32" src="uploads/'
                   .  $work['uuidfilename'] . '">';
            break;
        case 'vid':
            $thumb .= '<span class="glyphicon glyphicon-film"></span>';
            break;
        case 'aud':
            $thumb .= '<span class="glyphicon glyphicon-music"></span>';
            break;
        case 'txt':
            $thumb .= '<span class="glyphicon glyphicon-text-background"></span>';
            break;
    }
    $thumb .= '</a></div>';
    return $thumb;
}

// The full-sized work (or work with play controls) for display on its own page

function work_display ($work) {
    $display = '<div class="display-work">';
    $kind = work_kind($work);
    $uploadname = 'uploads/' . $work['uuidfilename'];
    $downloadname = '?action=download&work_id=' . $work['work_id'];
    switch ($kind) {
        case 'img':
            $display .= '<img alt="' . $work['title'] . '" src="'
                      . $uploadname . '">';
            break;
        case 'vid':
            $display .= '<video controls>
                           Sorry, your browser does not support the
                           <code>video</code> element, but you can
                           <a href="' . $downloadname
                      . '">download this file</a> and listen to it with your
                             favourite media player!
                            <source src="' . $uploadname . '">
                         </video>';
            break;
        case 'aud':
            $display .= '<audio controls>
                           Your browser does not support the
                           <code>audio</code> element, but you can
                           <a href="' . $downloadname
                      . '">download this file</a> and listen to it with your
                             favourite media player!
                           <source src="' . $uploadname .'">
                         </audio>';
            break;
        case 'txt':
            $markdown .= htmlentities(file_get_contents($filename));
            $display = '<div id="markdown-src">'
                     . $markdown . '</div>
              <script src="https://cdnjs.cloudflare.com/ajax/libs/showdown/1.3.0/showdown.min.js"></script>
              <script>
                var converter  = new showdown.Converter();
                var md_element = document.getElementById("markdown-src");
                var text       = md_element.textContent;
                var html       = converter.makeHtml(text);
                md_element.innerHTML = html;
              </script>';
            break;
    }
    $display .= '</div>';
    return $display;
}

////////////////////////////////////////////////////////////
// Pre-UI rendering environment setup and request processing
////////////////////////////////////////////////////////////

session_start();

$BASE_URL = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);

$action = '';
if (isset($_REQUEST["action"])) {
    $action = strip_tags($_REQUEST["action"]);
}

if (($action == '') && isset($_SESSION['user_id'])) {
    header('Location:?action=browse');
    exit;
}

global $dbh;

$dbh = new PDO('sqlite:' . dirname(__FILE__) . '/foo.db');

$foo = $dbh->exec("SELECT COUNT(*) FROM sqlite_master WHERE type='table' AND name='users' LIMIT 1");

// Lazily create the database
if ($foo != 1) {
  setupdb();
}

// Handle page logic here that needs to be resolved before drawing the UI.
// There's another $action switch below in the body to render the correct view.

switch ($action) {

    // The user has submitted the login form, process their login request
    case 'loginprocess':
        $login_status = 'err';
        // Make sure we've been passed a non-empty username
        if ((isset($_POST['username'])) && trim($_POST['username']) != ''){
            $username = strip_tags(trim($_POST['username']));
            $usernameq = $dbh->quote($username);
            // Insert the user. We don't care if this fails when they exist.
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

    // The user has pressed the logout button, log them out
    case 'logoutprocess':
        // Tear down the session
        $_SESSION = array();
        session_destroy();
        // Set the action to the default index page
        $action = '';
        break;

    // The user has submitted the new work form, process the uploaded work
    case 'newprocess':
        $upload_status = 'err';
        // Only do this if user is logged in (our flag for this isn't set yet)
        if (isset($_SESSION['user_id'])) {
            if (isset($_FILES['file'])
                && isset($_POST['title'])
                    && isset($_POST['license'])) {
                $supplied_filename = $_FILES["file"]["name"];
                if (! file_valid($supplied_filename)) {
                    $upload_status = 'unsupported';
                } else {
                    // Assumes max. 8 char extension, see table creation
                    $extension = pathinfo($supplied_filename,
                                          PATHINFO_EXTENSION);
                    $uuid = uniqid($more_entropy=true);
                    $uuid_filename =  $uuid . "." . $extension;
                    if (move_uploaded_file($_FILES["file"]["tmp_name"],
                                           dirname(__FILE__)
                                         . '/uploads/' . $uuid_filename)) {
                        $title = strip_tags(trim($_POST['title']));
                        $license = intval($_POST['license']);
                        $file_insert_sql = "INSERT INTO works (user_id, title,
                                                filename, uuidfilename, license)
                                                VALUES("
                        . $_SESSION['user_id'] . ","
                                         . $dbh->quote($title) . ","
                                         . $dbh->quote($supplied_filename) . ","
                                         . "'" . $uuid_filename . "', "
                                         . $license
                                         . ")";
                        $dbh->exec($file_insert_sql);
                        $upload_status = 'uploaded';
                        $work_id = $dbh->lastInsertId();
                        // Start tracking license changes
                        update_work_license($dbh, $work_id, $license);
                        header('Location:?action=display&work_id=' . $work_id);
                        exit;
                    }
                }
            }
        }
        break;

    // The user has chosen the search page,
    // or has submitted the search form, process their search ready for display.
    case 'search':
        $search_status = 'get';
        if (isset($_POST['keywords']) && isset($_POST['license'])) {
            $search_status = 'err';
            $keywords_string = strip_tags($_POST['keywords']);
            $license = intval($_POST['license']);
            $keywords = keywords_for_search ($keywords_string);
            if (count($keywords) == 0) {
                $search_status = 'ok';
                $keywords_matches = [];
            } else {
                $keywords_query = search_sql($keywords, $license);
                $search_statement = $dbh->prepare($keywords_query);
                if ($search_statement) {
                    $ok = $search_statement->execute();
                    if ($ok) {
                        // So we can count them
                        $keywords_matches = $search_statement->fetchAll();
                        $search_status = 'ok';
                    }
                }
            }
        }
        break;

    // The user is viewing a work, get the work's details ready to display it.
    case 'display':
        $display_status = 'err';
        if (isset($_REQUEST['work_id'])){
            //FIXME: validate
            $work_id = intval($_REQUEST['work_id']);
            $work_row = work_for_id($dbh, $work_id);
            if ($work_row) {
                $user_row = user_for_id($dbh, $work_row['user_id']);
                if ($user_row) {
                    $work_license_changes = license_changes_for_work($dbh,
                                                                     $work_id);
                    $display_status = 'ok';
                }
            }
        }
        break;

    // The user is viewing a work to relicense it,
    // or has submitted the change license form for the work, process this.
    case "license":
        if (isset($_SESSION['user_id']) && isset($_REQUEST['work_id'])) {
            $user_id = intval($_SESSION['user_id']);
            $work_id = intval($_REQUEST['work_id']);
            $license_work = work_for_id($dbh, $work_id);
            if ($license_work && $license_work['user_id'] == $user_id) {
                if (isset($_POST['license'])) {
                    $license = intval($_POST['license']);
                    // Don't record an update if the license hasn't changed
                    if($license != $license_work['license']) {
                        update_work_license($dbh, $work_id, $license);
                    }
                    header('Location:?action=display&work_id=' . $work_id);
                    exit;
                }
                $work_license_changes = license_changes_for_work($dbh,
                                                                 $work_id);
            } else {
                $license_work = false;
            }
        }
        break;

    // The user is viewing a list of works to relicense,
    // or has selected some and submitted the relicense form, process this.
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
                        update_work_license($dbh, $work_id, $license);
                    }
                }
            }
            $batch_works = works_for_user($dbh, $_SESSION['user_id']);
            $batch_state = 'ok';
        }
        break;

    // The user is viewing a user profile, either theirs or someone else's.
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
            $who_default_license = user_default_license ($dbh, $who_id);
            $who_state= 'ok';
        }
        if ($who_state == 'ok') {
            $who_works = works_for_user ($dbh, $who_id);
            if ($who_works === false) {
                $who_state = 'err';
            }
        }
        break;

    // The user has submitted the form to change their default license,
    // update it.
    case 'whodefaultlicenseprocess':
        if (isset($_SESSION['user_id'])) {
            if(isset($_POST['default_license'])) {
                $default_license = intval($_POST['default_license']);
                //FIXME: check in range
                update_user_default_license ($dbh, $_SESSION['user_id'],
                                             $default_license);
            }
            // Should tell the user about success. Add flashes/toasts?
            header('Location:?action=who');
            exit;
        } else {
            $action = 'who';
            $who_state = 'err';
        }
        break;

    // The user is browsing the latest works under all or one of the licenses,
    // get ready to display a list of them.
    case 'browse':
        $browse_license_ids = $LICENSE_RANGE_FREEDOM;
        $browse_license_ids[] = 0;
        $browse_count = 5;
        $browse_initially_selected = '*';
        $browse_all = true;
        if (isset($_REQUEST['license'])) {
            $license_string = trim($_REQUEST['license']);
            if ($license_string != '*') {
                $license = intval($_REQUEST['license']);
                if (in_array($license, $LICENSE_RANGE)) {
                    $browse_license_ids = [$license];
                    $browse_count = 20;
                    $browse_all = false;
                    $browse_initially_selected = $license;
                }
            }
        }
        $browse_results = [];
        foreach ($browse_license_ids as $lic) {
            $browse_results[$lic] = list_works_with_license($dbh,
                                                            $lic,
                                                            $browse_count);
        }
        break;

    // The user has requested to download a file. We vend it under its original
    // filename.
    // No log-in is required for this.
    case 'download':
        if (isset($_GET['work_id']) ) {
            $work_id = intval($_GET['work_id']);
            $work = work_for_id ($dbh, $work_id);
            if($work) {
                download_work($work);
            } else {
                //FIXME: inform user
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

    <link href="css/style.css" rel="stylesheet">

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
            <li<?php if ($action == 'search') { echo ' class="active"'; } ?>>
              <a href="?action=search">Search</a></li>
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
    <p>You can <a href="?action=login">login/register</a> or
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
        <input name="file" id="file" class="form-control" type="file"
          accept=".gif,.jpg,.jpeg,.markdown,.md,.mp3,.mp4,.ogg,.ogv,.png,.txt">
      </div>
      <div class="form-group">
        <label for="title">Title</label>
        <input name="title" id="title" class="form-control" type="text"
          maxlength="200" size="32">
      </div>
      <div class="form-group">
        <label for="license">License
          <small><em>( <a href="html/cc.html">About the licenses</a> )</em>
          </small</label>
        <select name="license" id="license" class="form-control">
          <?php echo license_options(user_default_license ($dbh,
                                                           $_SESSION['user_id']),
                                                           false); ?>
        </select>
      </div>
      <input type="submit" class="btn btn-default" value="Upload">
    </form>
<?php
    break;

case "newprocess":
if ($upload_status == 'unsupported') {
        echo "<h1>Unsupported format</h1><p>";
        echo "Try ogg audio or video, mp3 or mp4, markdown or plain text.</p>";
    }
    break;

case "search":
    $cl = isset($_POST['license']) ? $_POST['license'] : '*';
?>
    <form action="?action=search" method="post">
      <div class="form-group">
        <label for="keywords">Keywords</label>
        <input name="keywords" id="keywords" class="form-control" type="text"
           maxlength="200" size="32"
          <?php
          if (isset($_POST['keywords'])) {
              echo 'value="' . htmlspecialchars($_POST['keywords']) . '"';
          }
          ?>>
      </div>
      <div class="form-group">
        <label for="license">License
          <small><em>( <a href="html/cc.html">About the licenses</a> )</em>
          </small</label>
        <select name="license" id="license" class="form-control">
          <?php echo license_options($cl, true); ?>
        </select>
      </div>
      <input type="submit" id="search" class="btn btn-primary"
          value="Search"
          <?php if (! isset($_POST['keywords'])) { echo ' disabled'; } ?>>
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
    if($search_status == 'ok') {
        if (count($keywords_matches) > 0) {
            echo '<h2>Results</h2>';
            print_works_table($dbh, $keywords_matches, true, true);
        } else {
            echo '<br><div class="alert alert-info" role="alert"><strong>No matches found.</strong> Please try again with different keywords. Note that we don\'t search for words shorter than two letters.</div>';
        }
    }
    break;

case "display":
    if ($display_status == 'ok') {
        $viewing_own_work = ($logged_in
                             && ($work_row['user_id'] == $_SESSION['user_id']));
?>
    <h2 class="display-title"><?php echo $work_row['title']; ?> by
    <a href="?action=who&user_id=<?php echo $user_row['user_id']; ?>">
      <?php echo $user_row['username'] ?></a></h2>
    <?php echo work_display($work_row); ?>
    <div id="license-block" >
      <?php echo license_block($dbh, $BASE_URL, $work_row); ?>
    </div>
    <div class="display-buttons">
      <a class="btn btn-info" id="copy-attribution-button"
         href="#">Copy Attribution</a>
      <a class="btn btn-success" download
         href="?action=download&work_id=<?php echo $work_row['work_id'];
           ?>">Download File
        <span class="glyphicon glyphicon-download-alt"
            aria-hidden="true"></span></a>
<?php
        if ($viewing_own_work) {
?>
        <a class="btn btn-danger"
        href="?action=license&work_id=<?php echo $work_row['work_id'] ?>">
          Change license</a>
<?php } ?>
    </div>
    <div id="attribution-alert" class="alert alert-success alert-dismissible fade in" role="alert" hidden>
      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
  <strong>OK!</strong> The attribution has been copied to the clipboard.
    </div>
    <div id="attribution-popup" class="well" hidden>
        <textarea id="attribution-popup-text">textarea</textarea><br>
        <button onclick="$('#attribution-popup').hide();">Hide</button>
        &nbsp;&nbsp;&nbsp;&nbsp;Please copy the above text.
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/1.5.3/clipboard.min.js"></script>
    <script>
     var license_metadata = document.getElementById("license-block")
                                    .innerHTML.trim()
                                    .replace(/&lt;/g,'<').replace(/&gt;/g,'>')
                                    .replace(/&amp;/g,'&') + "\n";
     var clipboard = new Clipboard('#copy-attribution-button', {
         text: function(trigger) {
             $('#attribution-alert').show(400);
             setTimeout(function() {
                 $('#attribution-alert').hide(400);
                 }, 5000);
             return license_metadata;
         }
     });
     // For browsers that don't yet support the clipboard, allow the user to
     // copy the text manually
     clipboard.on('failure', function() {
         $("#attribution-popup-text").val(license_metadata);
         $("#attribution-popup").show();
         $("#attribution-popup-text").select();
     });
    </script>
<?php
        if ($viewing_own_work) {
            print_work_license_changes($work_license_changes);
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
<?php
        if (count($who_works) > 0) {
?>
    <h3>Works by <?php echo $who_name; ?></h3>
<?php
        print_works_table($dbh, $who_works, false, true);
?>
    <div class="who-buttons"><a class="btn btn-primary"
                                href="?action=batch">Change licenses</a></div>
<?php
        }
        if ($_SESSION['user_id'] && ($who_id == $_SESSION['user_id'])) {
?>
    <h3>Default License</h3>
    <form action="?action=whodefaultlicenseprocess" method="post">
      <div class="form-group">
        <label for="default_license">License
          <small><em>( <a href="html/cc.html">About the licenses</a> )</em>
          </small></label>
        <select name="default_license" id="default_license"
           class="form-control">
           <?php echo license_options(user_default_license($dbh, $who_id),
                                      false); ?>
        </select>
      </div>
      <input type="submit" class="btn btn-default"
         value="Change Default License">
    </form>
<?php
        }
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
     <?php echo work_display($license_work); ?>
     <div id="license-block" class="well">
       <?php echo license_block($dbh, $BASE_URL, $license_work); ?>
     </div>
    <form action="?action=license" method="post">
      <input type="hidden" name="work_id"
         value="<?php echo $license_work['work_id']; ?>">
      <div class="form-group">
        <label for="license">License
          <small><em>( <a href="html/cc.html">About the licenses</a> )</em>
          </small></label>
        <select name="license" id="license" class="form-control">
           <?php echo license_options($license_work['license'],
                                      false); ?>
        </select>
      </div>
      <input type="submit" class="btn btn-default" value="Change">
    </form>
    <?php
        print_work_license_changes($work_license_changes);
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
        <tr><th>Apply</th><th></th><th>Title</th><th>Current License</th></tr>
<?php
    foreach ($batch_works as $work) {
        $lic = license_for_table ($work['license']);
        echo '<tr><td><input type="checkbox" name="apply[]" value="'
             . $work['work_id'] . '"></td><td>' . work_thumbnail($work)
             . '</td><td><a href="?action=display&work_id='
             . $work['work_id'] . '">' . $work['title'] . '</a></td><td>'
             . $lic . '</td></tr>';
    }
?>
    </tbody></table>
    <div class="form-group">
        <label for="license">License
          <small><em>( <a href="html/cc.html">About the licenses</a> )</em>
          </small></label>
        <select name="license" id="license" class="form-control">
          <?php echo license_options(user_default_license($dbh,
                                                          $_SESSION['user_id']),
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

case 'browse':
?>
    <h1>Browse Recent Uploads</h1>
    <hr>
<?php
    foreach ($browse_license_ids as $license) {
        $works_count = count_works_with_license($dbh, $license);
        if ($works_count > 0) {
            echo '<h3>' .lic_icons($BASE_URL, $license)
               . ' ' . lic_name($license) . '</h3>';
            print_works_table($dbh, $browse_results[$license], true, false);
            if ($browse_all) {
                echo '<div class="browse-see-more">&#8594;'
                   . count_works_with_license($dbh, $license)
                   . ' works (<a href="?action=browse&license='
                   . $license . '">See more)</a></div><hr>';
            }
        }
    }
?>
    <form action="?action=browse" method="post">
      <div class="form-group">
        <label for="license">License
          <small><em>( <a href="html/cc.html">About the licenses</a> )</em>
          </small></label>
        <select name="license" id="license" class="form-control">
           <?php echo license_options($browse_initially_selected, true); ?>
        </select>
      </div>
      <input type="submit" class="btn btn-default" value="Browse">
    </form>
<?php
    break;
}

function setupdb() {

    global $dbh;

    $sql = "PRAGMA encoding = UTF-8;";

    $dbh->exec($sql);

    $sql = "CREATE TABLE IF NOT EXISTS `users` (
	    `user_id` INTEGER PRIMARY KEY AUTOINCREMENT,
	    `username` varchar(200) NOT NULL UNIQUE,
        `default_license` INT NOT NULL DEFAULT 0
      );";

    $dbh->exec($sql);

    // Assumes max. 8 char extension in uuidfilename (for markdown)
    $sql = "CREATE TABLE IF NOT EXISTS `works` (
	    `work_id` INTEGER PRIMARY KEY AUTOINCREMENT,
	    `user_id` INTEGER NOT NULL,
        `title` varchar(200) NOT NULL,
        `filename` varchar(200) NOT NULL,
        `uuidfilename` char(32) NOT NULL,
        `license` INT NOT NULL
      );
      CREATE INDEX works_fileuuid_index ON works";

    $dbh->exec($sql);

    $sql = "CREATE TABLE IF NOT EXISTS `works_license_changes` (
        `change_id` INTEGER PRIMARY KEY AUTOINCREMENT,
        `work_id` INTEGER NOT NULL,
        `license` INT NOT NULL,
        `when` DATETIME DEFAULT CURRENT_TIMESTAMP
      );";

    $dbh->exec($sql);
}

?>
        <hr>
        <ul class="list-unstyled list-inline">
          <li><a href="html/tos.html">Terms of Service</a></li>
          <li><a href="html/faq.html">FAQ</a></li>
          <li><a href="html/cc.html">About CC</a></li>
          <li><a href="html/attribution.html">Attribution Guide</a></li>
          <li><a href="html/community.html">Community Guidelines</a></li>
        </ul>
      </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"
            integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS"
            crossorigin="anonymous"></script>
  </body>
</html>
