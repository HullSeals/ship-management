<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once '../../users/init.php';  //make sure this path is correct!
if (!securePage($_SERVER['PHP_SELF'])){die();}
$logged_in = $user->data();
$counter = 0;
if (isset($_SESSION['2ndrun'])) {
  unset($_SESSION['2ndrun']);
}
$db = include '../db.php';
$mysqli = new mysqli($db['server'], $db['user'], $db['pass'], $db['db'], $db['port']);
$shipList = [];
$res = $mysqli->query('SELECT * FROM lookups.ships_lu ORDER BY ship_id');
while ($shipclass = $res->fetch_assoc()) {
    $shipList[$shipclass['ship_id']] = $shipclass['ship_name'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<link href="../../favicon.ico" rel="icon" type="image/x-icon">
<link href="../../favicon.ico" rel="shortcut icon" type="image/x-icon">
<meta charset="UTF-8">
<meta content="Wolfii Namakura" name="author">
<meta content="hull seals, elite dangerous, distant worlds, seal team fix, mechanics, dw2" name="keywords">
<meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0" name="viewport">
<meta content="About Hull Seals Ship Registration Portal" name="description">
<title>My Fleet | The Hull Seals</title>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
<link href="../../styles.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" type="text/css" href="//cdnpub.websitepolicies.com/lib/cookieconsent/1.0.2/cookieconsent.min.css" />
<script src="https://cdnpub.websitepolicies.com/lib/cookieconsent/1.0.2/cookieconsent.min.js" integrity="sha384-gNaqAsLHf4qf+H76HtN+K++WIcDxMT8yQ3VSiYcRjmkwUKZeHXAqppXDBUtja174" crossorigin="anonymous"></script>
<script>
        window.addEventListener("load", function() {
            window.wpcc.init({
                "colors": {
                    "popup": {
                        "background": "#222222",
                        "text": "#ffffff",
                        "border": "#bd9851"
                    },
                    "button": {
                        "background": "#bd9851",
                        "text": "#000000"
                    }
                },
                "border": "thin",
                "corners": "small",
                "padding": "small",
                "margin": "small",
                "transparency": "25",
                "fontsize": "small",
                "content": {
                    "href": "https://hullseals.space/knowledge/books/important-information/page/cookie-policy"
                }
            })
        });
    </script>
  </head>
  <body>
  <div id="home">
    <header>
        <nav class="navbar container navbar-expand-lg navbar-expand-md navbar-dark" role="navigation">
            <a class="navbar-brand" href="../../"><img src="../../images/emblem_scaled.png" height="30" class="d-inline-block align-top" alt="Logo"> Hull Seals</a>

            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="../../">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../../knowledge">Knowledge Base</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../../journal">Journal Reader</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../../about">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../../contact">Contact</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="https://hullseals.space/users/">Login/Register</a>
                    </li>
                </ul>
            </div>
        </nav>
    </header>
  <section class="introduction">
  <article>
    <h1>My Ships</h1>
    <p>Here you can view your registered Seal Ships as well as register a new one. Registration is completely optional, but you can lay claim to your own unique Seal Fleet Registry Number</p>
    <p>
      <em>You may not use Seal registered ships for PvP combat operations.</em> All ships registered here must conform at all times to all Seal rules of use.
    </p>
    <?php
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $db = include '../db.php';
    $mysqli = new mysqli($db['server'], $db['user'], $db['pass'], $db['db'], $db['port']);
    $stmt = $mysqli->prepare("SELECT ID, seal_ID, ship_name, class FROM ships WHERE seal_ID =?");
    $stmt->bind_param("i", $user->data()->id);
    $stmt->execute();
    $result = $stmt->get_result();
    if($result->num_rows === 0) exit('<a href="new-ship.php" class="btn btn-success btn-lg active" >Register a New Ship</a>
    </article>
    <div class="clearfix"></div>
    </section>
    </div>
    <footer class="page-footer font-small">
    <div class="container">
    <div class="row">
    <div class="col-md-9 mt-md-0 mt-3">
    <h5 class="text-uppercase">Hull Seals</h5>
    <p><em>The Hull Seals</em> were established in January of 3305, and have begun plans to roll out galaxy-wide!</p>
    <a href="https://fuelrats.com/i-need-fuel" class="btn btn-sm btn-secondary">Need Fuel? Call the Rats!</a>
    </div>
    <hr class="clearfix w-100 d-md-none pb-3">
    <div class="col-md-3 mb-md-0 mb-3">
    <h5 class="text-uppercase">Links</h5>
    <ul class="list-unstyled">
    <li><a href="https://twitter.com/HullSeals" target="_blank"><img alt="Twitter" height="20" src="../../images/twitter_loss.png" width="20"></a> <a href="https://reddit.com/r/HullSeals" target="_blank"><img alt="Reddit" height="20" src="../../images/reddit.png" width="20"></a> <a href="https://www.youtube.com/channel/UCwKysCkGU_C6V8F2inD8wGQ" target="_blank"><img alt="Youtube" height="20" src="../../images/youtube.png" width="20"></a> <a href="https://www.twitch.tv/hullseals" target="_blank"><img alt="Twitch" height="20" src="../../images/twitch.png" width="20"></a> <a href="https://gitlab.com/hull-seals-cyberseals" target="_blank"><img alt="GitLab" height="20" src="../../images/gitlab.png" width="20"></a></li>
    <li><a href="/donate">Donate</a></li>
    <li><a href="https://hullseals.space/knowledge/books/important-information/page/privacy-policy">Privacy & Cookies Policy</a></li>
    </ul>
    </div>
    </div>
    </div>
    <div class="footer-copyright">
    Site content copyright © 2019, The Hull Seals. All Rights Reserved. Elite Dangerous and all related marks are trademarks of Frontier Developments Inc.
    </div>
    </footer></body>
    </html>
');
    echo "<h3>Returning all Registered Ships for: ";
    echo echousername($user->data()->id);
    echo nl2br ("</h3>");
    echo '<table class="table table-dark table-striped table-bordered table-hover table-responsive-md">
          <tr>
              <td>Registry Number</td>
              <td>Ship Name Name</td>
              <td>Class</td>
              <td colspan="2">Options</td>
          </tr>';
        while ($row = $result->fetch_assoc()) {
            $field1name = $row["ID"];
            $field2name = $row["ship_name"];
            $field3name = $row["class"];
            echo '<tr>
                      <td>HS'.$field1name.'</td>
                      <td>'.$field2name.'</td>
                      <td>';
                      //There has got to be a better way to do this, but I'm lazy and it just works.
                      //TODO. Fix this shit. ~ Rix
                      if ($field3name == "1") {
                        echo "Adder";
                      } elseif ($field3name=="2") {
                        echo "Alliance Challenger";
                      } elseif ($field3name=="3") {
                        echo "Alliance Chieftain";
                      }elseif ($field3name=="4") {
                        echo "Alliance Crusader";
                      }elseif ($field3name=="5") {
                        echo "Anaconda";
                      }elseif ($field3name=="6") {
                        echo "Asp Explorer";
                      }elseif ($field3name=="7") {
                        echo "Asp Scout";
                      }elseif ($field3name=="8") {
                        echo "Beluga Liner";
                      }elseif ($field3name=="9") {
                        echo "Cobra Mk III";
                      }elseif ($field3name=="10") {
                        echo "Cobra Mk IV";
                      }elseif ($field3name=="11") {
                        echo "Diamondback Explorer";
                      }elseif ($field3name=="12") {
                        echo "Diamondback Scout";
                      }elseif ($field3name=="13") {
                        echo "Dolphin";
                      }elseif ($field3name=="14") {
                        echo "Eagle";
                      }elseif ($field3name=="15") {
                        echo "Federal Assault Ship";
                      }elseif ($field3name=="16") {
                        echo "Federal Corvette";
                      }elseif ($field3name=="17") {
                        echo "Federal Dropship";
                      }elseif ($field3name=="18") {
                        echo "Federal Gunship";
                      }elseif ($field3name=="19") {
                        echo "Fer-de-Lance";
                      }elseif ($field3name=="20") {
                        echo "Hauler";
                      }elseif ($field3name=="21") {
                        echo "Imperial Clipper";
                      }elseif ($field3name=="22") {
                        echo "Imperial Courier";
                      }elseif ($field3name=="23") {
                        echo "Imperial Cutter";
                      }elseif ($field3name=="24") {
                        echo "Imperial Eagle";
                      }elseif ($field3name=="25") {
                        echo "Keelback";
                      }elseif ($field3name=="26") {
                        echo "Krait Mk II";
                      }elseif ($field3name=="27") {
                        echo "Krait Phantom";
                      }elseif ($field3name=="28") {
                        echo "Mamba";
                      }elseif ($field3name=="29") {
                        echo "Orca";
                      }elseif ($field3name=="30") {
                        echo "Python";
                      }elseif ($field3name=="31") {
                        echo "Sidewinder";
                      }elseif ($field3name=="32") {
                        echo "Type-10 Defender";
                      }elseif ($field3name=="33") {
                        echo "Type-6 Transporter";
                      }elseif ($field3name=="34") {
                        echo "Type-7 Transporter";
                      }elseif ($field3name=="35") {
                        echo "Type-9 Heavy";
                      }elseif ($field3name=="36") {
                        echo "Viper";
                      }elseif ($field3name=="37") {
                        echo "Viper Mk IV";
                      }elseif ($field3name=="38") {
                        echo "Vulture";
                      }
                      echo '</td>
                      <td><a href="edit-ship.php?cne='.$field2name.'" class="btn btn-warning active">Edit</a></td>
                      <td><a href="rem-ship.php?cne='.$field2name.'" class="btn btn-danger active">Delete</a></td>
                  </tr>';
              $counter++;
        }
        echo '</table>';
        $result->free();
    ?>
    <br />
    <a href="new-ship.php" class="btn btn-success btn-lg active" >Register a New Ship</a>
  </article>
  <div class="clearfix"></div>
  </section>
  </div>
  <footer class="page-footer font-small">
  <div class="container">
  <div class="row">
  <div class="col-md-9 mt-md-0 mt-3">
  <h5 class="text-uppercase">Hull Seals</h5>
  <p><em>The Hull Seals</em> were established in January of 3305, and have begun plans to roll out galaxy-wide!</p>
  <a href="https://fuelrats.com/i-need-fuel" class="btn btn-sm btn-secondary">Need Fuel? Call the Rats!</a>
  </div>
  <hr class="clearfix w-100 d-md-none pb-3">
  <div class="col-md-3 mb-md-0 mb-3">
  <h5 class="text-uppercase">Links</h5>
  <ul class="list-unstyled">
  <li><a href="https://twitter.com/HullSeals" target="_blank"><img alt="Twitter" height="20" src="../../images/twitter_loss.png" width="20"></a> <a href="https://reddit.com/r/HullSeals" target="_blank"><img alt="Reddit" height="20" src="../../images/reddit.png" width="20"></a> <a href="https://www.youtube.com/channel/UCwKysCkGU_C6V8F2inD8wGQ" target="_blank"><img alt="Youtube" height="20" src="../../images/youtube.png" width="20"></a> <a href="https://www.twitch.tv/hullseals" target="_blank"><img alt="Twitch" height="20" src="../../images/twitch.png" width="20"></a> <a href="https://gitlab.com/hull-seals-cyberseals" target="_blank"><img alt="GitLab" height="20" src="../../images/gitlab.png" width="20"></a></li>
  <li><a href="/donate">Donate</a></li>
  <li><a href="https://hullseals.space/knowledge/books/important-information/page/privacy-policy">Privacy & Cookies Policy</a></li>
  </ul>
  </div>
  </div>
  </div>
  <div class="footer-copyright">
  Site content copyright © 2019, The Hull Seals. All Rights Reserved. Elite Dangerous and all related marks are trademarks of Frontier Developments Inc.
  </div>
  </footer></body>
  </html>
