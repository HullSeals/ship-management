<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//UserSpice Required
require_once '../../users/init.php';  //make sure this path is correct!
if (!securePage($_SERVER['PHP_SELF'])){die();}

//IP Tracking Stuff
require '../../assets/includes/ipinfo.php';

//
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
$validationErrors = [];
$lore = [];
if (isset($_GET['send'])) {
    foreach ($_REQUEST as $key => $value) {
        $lore[$key] = strip_tags(stripslashes(str_replace(["'", '"'], '', $value)));
    }
    if (!count($validationErrors)) {
        $stmt = $mysqli->prepare('CALL spRemShip(?,?)');
        $stmt->bind_param('is',$lore['numberedt'], $lgd_ip);
        $stmt->execute();
        foreach ($stmt->error_list as $error) {
            $validationErrors[] = 'DB: ' . $error['error'];
        }
        $stmt->close();
        unset($_SESSION['2ndrun']);
        header("Location: .");
  }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
<meta content="Hull Seals Ship Registration Portal" name="description">
<title>My Fleet | The Hull Seals</title>
<?php include '../../assets/includes/headerCenter.php'; ?>

</head>
<body>
<div id="home">
  <?php include '../../assets/includes/menuCode.php';?>
    <section class="introduction container">
  <article id="intro3">
    <h1>My Ships</h1>
    <p>Here you can view your registered Seal Ships as well as register a new one. Registration is completely optional, but you can lay claim to your own unique Seal Fleet Registry Number</p>
    <p>
      <em>You may not use Seal registered ships for PvP combat operations.</em> All ships registered here must conform at all times to all Seal rules of use.
    </p>
    <?php
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $db = include '../db.php';
    $mysqli = new mysqli($db['server'], $db['user'], $db['pass'], $db['db'], $db['port']);
    $stmt = $mysqli->prepare("SELECT ID, seal_ID, ship_name, class FROM ships WHERE seal_ID =? AND del_flag <> 1");
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
                    <li><a href="https://twitter.com/HullSeals" target="_blank"><img alt="Twitter" height="20" src="https://hullseals.space/images/twitter_loss.png" width="20"></a> <a href="https://reddit.com/r/HullSeals" target="_blank"><img alt="Reddit" height="20" src="https://hullseals.space/images/reddit.png" width="20"></a> <a href="https://www.youtube.com/channel/UCwKysCkGU_C6V8F2inD8wGQ" target="_blank"><img alt="Youtube" height="20" src="https://hullseals.space/images/youtube.png" width="20"></a> <a href="https://www.twitch.tv/hullseals" target="_blank"><img alt="Twitch" height="20" src="https://hullseals.space/images/twitch.png" width="20"></a> <a href="https://gitlab.com/hull-seals" target="_blank"><img alt="GitLab" height="20" src="https://hullseals.space/images/gitlab.png" width="20"></a></li>
        <li><a href="https://hullseals.space/donate">Donate</a></li>
                    <li><a href="https://hullseals.space/knowledge/books/important-information/page/privacy-policy">Privacy & Cookies Policy</a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="footer-copyright">
        Site content copyright © 2020, The Hull Seals. All Rights Reserved. Elite Dangerous and all related marks are trademarks of Frontier Developments Inc.
    </div>
</footer>
</body>
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
                      mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
                      $stmt3 = $mysqli->prepare("SELECT ship_name FROM lookups.ships_lu WHERE ship_id = ?");
                      $stmt3->bind_param("i", $field3name);
                      $stmt3->execute();
                      $result3 = $stmt3->get_result();
                      $result3 = mysqli_fetch_assoc($result3);
                      $stmt3->close();
                      echo $result3['ship_name'];
                      echo '</td>
                      <td><a href="edit-ship.php?cne='.$field2name.'" class="btn btn-warning active">Edit</a></td>
                      <td><button type="button" class="btn btn-danger active" data-toggle="modal" data-target="#mo'.$field1name.'">Delete</button>
                      </td>
                  </tr>';
                  echo '<div class="modal fade" id="mo'.$field1name.'" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel" style="color:black;">Delete Ship?</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" style="color:black;">
        Are you sure you want to delete the Ship "'.$field2name.'"?
      </div>
      <div class="modal-footer">
        <form action="?send" method="post">
            <input type="hidden" name="numberedt" value="'.$field1name.'" required>
          <button type="submit" class="btn btn-danger">Yes, Remove.</button><button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          </form>
      </div>
    </div>
  </div>
</div>';
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
<?php include '../../assets/includes/footer.php'; ?>
</body>
</html>
