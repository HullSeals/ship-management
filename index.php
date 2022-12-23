<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//Declare Title, Content, Author
$pgAuthor = "David Sangrey";
$pgContent = "Update your Registered Seal Ships";
$useIP = 1; //1 if Yes, 0 if No.

//UserSpice Required
require_once '../users/init.php';  //make sure this path is correct!
require_once $abs_us_root . $us_url_root . 'users/includes/template/prep.php';
if (!securePage($_SERVER['PHP_SELF'])) {
  die();
}

$counter = 0;
if (isset($_SESSION['2ndrun'])) {
  unset($_SESSION['2ndrun']);
}
$db = include 'db.php';
$mysqli = new mysqli($db['server'], $db['user'], $db['pass'], $db['db'], $db['port']);
$shipList = [];
$res = $mysqli->query('SELECT * FROM lookups.ships_lu ORDER BY ship_id');
while ($shipclass = $res->fetch_assoc()) {
  $shipList[$shipclass['ship_id']] = $shipclass['ship_name'];
}
$validationErrors = 0;
$lore = [];
if (isset($_GET['delete'])) {
  foreach ($_REQUEST as $key => $value) {
    $lore[$key] = strip_tags(stripslashes(str_replace(["'", '"'], '', $value)));
  }
  if ($validationErrors == 0) {
    $stmt = $mysqli->prepare('CALL spRemShip(?,?)');
    $stmt->bind_param('is', $lore['numberedt'], $lgd_ip);
    $stmt->execute();
    $stmt->close();
    unset($_SESSION['2ndrun']);
    header("Location: .");
  }
}
if (isset($_GET['new'])) {
  foreach ($_REQUEST as $key => $value) {
    $lore[$key] = strip_tags(stripslashes(str_replace(["'", '"'], '', $value)));
  }
  if (!isset($shipList[$lore['ship']])) {
    sessionValMessages("Invalid ship type! Please try again.");
    $validationErrors += 1;
  }
  if (strlen($lore['new_ship']) > 40) {
    sessionValMessages("Name too long! Please try again.");
    $validationErrors += 1;
  }
  if ($validationErrors == 0) {
    $stmt = $mysqli->prepare('CALL spCreateShip(?,?,?,?,?)');
    $stmt->bind_param('sisis', $lore['new_ship'], $lore['ship'], $lore['link'], $user->data()->id, $lgd_ip);
    $stmt->execute();
    $stmt->close();
    header("Location: .");
  }
}
if (isset($_GET['edit'])) {
  foreach ($_REQUEST as $key => $value) {
    $lore[$key] = strip_tags(stripslashes(str_replace(["'", '"'], '', $value)));
  }
  if (!isset($shipList[$lore['ship']])) {
    sessionValMessages("Invalid ship type! Please try again.");
    $validationErrors += 1;
  }
  if (strlen($lore['new_ship']) > 40) {
    sessionValMessages("Name too long! Please try again.");
    $validationErrors += 1;
  }
  if ($validationErrors == 0) {
    $stmt = $mysqli->prepare('CALL spEditShip(?,?,?,?,?)');
    $stmt->bind_param('sisis', $lore['edt_alias'], $lore['ship'], $lore['link'], $lore['numberedt'], $lgd_ip);
    $stmt->execute();
    $stmt->close();
    header("Location: .");
  }
}
?>
<h1>My Ships</h1>
<p>Here you can view your registered Seal Ships as well as register a new one. Registration is completely optional, but you can lay claim to your own unique Seal Fleet Registry Number</p>
<p>
  <em>You may not use Seal registered ships for PvP combat operations.</em> All ships registered here must conform at all times to all Seal rules of use.
</p>
<?php
$no_rows = 0;
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$db = include 'db.php';
$mysqli = new mysqli($db['server'], $db['user'], $db['pass'], $db['db'], $db['port']);
$stmt = $mysqli->prepare("SELECT ID, seal_ID, ship_name, class, link FROM ships WHERE seal_ID =? AND del_flag <> 1");
$stmt->bind_param("i", $user->data()->id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
  $no_rows = 1;
}
if ($no_rows == 1) {
} else {
  echo "<h3>Returning all Registered Ships for: ";
  echo echousername($user->data()->id);
  echo nl2br("</h3>");
  echo '<table class="table table-dark table-striped table-bordered table-hover table-responsive-md">
          <tr>
            <td>Registry Number</td>
            <td>Ship Name Name</td>
            <td>Ship Link</td>
            <td>Class</td>
            <td colspan="2">Options</td>
          </tr>';
  while ($row = $result->fetch_assoc()) {
    $field1name = $row["ID"];
    $field2name = $row["ship_name"];
    $field3name = $row["class"];
    $field4name = $row["link"];
    echo '<tr>
            <td>HS' . $field1name . '</td>
            <td>' . $field2name . '</td>
            <td>' . $field4name . '</td>
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
            <td><button type="button" class="btn btn-warning active" data-toggle="modal" data-target="#me' . $field1name . '">Edit</button>
            <td><button type="button" class="btn btn-danger active" data-toggle="modal" data-target="#mo' . $field1name . '">Delete</button>
            </td>
          </tr>
          <div class="modal fade" id="mo' . $field1name . '" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel" style="color:black;">Delete Ship?</h5>
              <button type="button" class="close" data-dismiss="modal">
                <span >&times;</span>
              </button>
            </div>
          <div class="modal-body" style="color:black;">
            Are you sure you want to delete the Ship "' . $field2name . '"?
          </div>
          <div class="modal-footer">
            <form action="?delete" method="post">
                <input type="hidden" name="numberedt" value="' . $field1name . '" required>
              <button type="submit" class="btn btn-danger">Yes, Remove.</button><button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              </form>
            </div>
          </div>
        </div>
      </div>';
    $stmtship = $mysqli->prepare("SELECT * FROM ships WHERE seal_ID = ? AND ID = ? AND del_flag <> 1");
    $stmtship->bind_param("is", $user->data()->id, $field1name);
    $stmtship->execute();
    $resultship = $stmtship->get_result();
    $resultsArray = $resultship->fetch_assoc();
    $shipName = $resultsArray['ship_name'];
    $shipLink = $resultsArray['link'];
    $shipID1 = $resultsArray['ID'];
    $stmtship->close();
    echo '<div class="modal fade" id="me' . $field1name . '" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel" style="color:black;">Edit Ship?</h5>
            <button type="button" class="close" data-dismiss="modal" >
              <span >&times;</span>
            </button>
          </div>
          <div class="modal-body" style="color:black;">
        Please edit your ship information.
        </div>
        <div class="modal-footer">
          <form action="?edit" method="post">
            <div class="input-group mb-3">
              <div class="input-group-prepend">
                <span class="input-group-text">Edited Name:</span>
              </div>
              <input type="text" pattern="[\x20-\x7A]+" minlength="3" name="edt_alias" value="' . $shipName . '" class="form-control" placeholder="Edited Alias Name" required>
              <input type="hidden" name="numberedt" value="' . $shipID1 . '" required>
            </div>
          <div class="input-group mb-3">
            <div class="input-group-prepend">
              <span class="input-group-text">Ship Class:</span>
            </div>
            <select name="ship" class="custom-select" id="inputGroupSelect01" placeholder="Ship Type" required>';
    foreach ($shipList as $shipId => $shipName) {
      if ($shipId == $resultsArray['class']) {
        $selected = "selected";
      } else {
        $selected = "";
      }
      echo '<option value="' . $shipId . '"' . $selected . '>' . $shipName . '</option>';
    }
    echo '</select>
          </div>
          <div class="input-group mb-3">
            <input type="url" name="link" id="link" class="form-control" value="' . $shipLink . '"
            placeholder="Coriolis Shortlink (Optional) https://s.orbis.zone/"
            pattern="(https?:\/\/(.+?\.)?orbis\.zone(\/[A-Za-z0-9\-\._~:\/\?#\[\]@!$&\'\(\)\*\+,;\=]*)?)" size="30">
          </div>
            <button type="submit" class="btn btn-primary">Submit</button><button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          </form>
        </div>
      </div>
    </div>
  </div>';
    $counter++;
  }
  echo '</table>';
  $result->free();
}
?>
<br />
<button class="btn btn-success btn-lg active" data-target="#moNew" data-toggle="modal" type="button">Register a New Ship</button> or <a href="seal-fleet.php" class="btn btn-lg btn-info">View All Registered Ships</a>
<div class="modal fade" id="moNew" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel" style="color:black;">New Ship</h5><button class="close" data-dismiss="modal" type="button"><span>&times;</span></button>
      </div>
      <div class="modal-body" style="color:black;">
        <form action="?new" method="post">
          <div class="input-group mb-3">
            <input type="text" name="new_ship" pattern="[\x20-\x7A]+" minlength="3" value="<?= $lore['new_ship'] ?? '' ?>" class="form-control" placeholder="New Ship Name" required>
          </div>
          <div class="input-group mb-3">
            <div class="input-group-prepend">
              <span class="input-group-text">Ship Class</span>
            </div>
            <select name="ship" class="custom-select" id="inputGroupSelect01" placeholder="Ship Type" required>
              <option selected disabled>Choose...</option>
              <?php
              foreach ($shipList as $shipId => $shipName) {
                echo '<option value="' . $shipId . '">' . $shipName . '</option>';
              }
              ?>
            </select>
          </div>
          <div class="input-group mb-3">
            <input type="url" name="link" id="link" class="form-control" placeholder="Coriolis Shortlink (Optional) https://s.orbis.zone/" pattern="(https?:\/\/(.+?\.)?orbis\.zone(\/[A-Za-z0-9\-\._~:\/\?#\[\]@!$&'\(\)\*\+,;\=]*)?)" size="30">
          </div>
          <div class="modal-footer">
            <button class="btn btn-primary" type="submit">Submit</button><button class="btn btn-secondary" data-dismiss="modal" type="button">Close</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<?php require_once $abs_us_root . $us_url_root . 'users/includes/html_footer.php'; ?>