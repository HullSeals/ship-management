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
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$db = include '../db.php';
$mysqli = new mysqli($db['server'], $db['user'], $db['pass'], $db['db'], $db['port']);
$stmt = $mysqli->prepare("SELECT * FROM ships WHERE seal_ID = ? AND ship_name = ? AND del_flag <> 1");
    $stmt->bind_param("is", $user->data()->id, $_GET['cne']);
    $stmt->execute();
    $result = $stmt->get_result();
    if (!isset($_SESSION['2ndrun'])){
    if($result->num_rows === 0) {
      Redirect::to('index.php');
    }
  }
  $_SESSION['2ndrun'] = true;
$chickennugget = $result->fetch_assoc();
$fluffernutter = $chickennugget['ship_name'];
$blizzard = $chickennugget['link'];
$salsa = $chickennugget['ID'];
$stmt->close();
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
    if (!isset($shipList[$lore['ship']])) {
        $validationErrors[] = 'invalid ship';
    }
    if (!count($validationErrors)) {
      $stmt = $mysqli->prepare('CALL spEditShip(?,?,?,?,?)');
      $stmt->bind_param('sisis', $lore['edt_alias'], $lore['ship'], $lore['link'], $lore['numberedt'], $lgd_ip);
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
<title>Edit Alias | The Hull Seals</title>
<?php include '../../assets/includes/headerCenter.php'; ?>

</head>
<body>
<div id="home">
  <?php include '../../assets/includes/menuCode.php';?>
    <section class="introduction container">
  <article id="intro3">
      <h1>Edit Ship</h1>
      <br />
      <h5>Please edit your ship information.</h5>
<hr />
      <?php
      if (count($validationErrors)) {
          foreach ($validationErrors as $error) {
              echo '<div class="alert alert-danger">' . $error . '</div>';
          }
          echo '<br>';
      }
      ?>
      <form action="?send" method="post">
        <div class="input-group mb-3">
                  <div class="input-group-prepend">
                      <span class="input-group-text">Edited Name:</span>
                  </div>
                  <input type="text" name="edt_alias" value="<?php echo $fluffernutter; ?>" class="form-control" placeholder="Edited Alias Name" aria-label="Edited Alias Name" required>
                  <input type="hidden" name="numberedt" value="<?php echo $salsa; ?>" required>
                  </div>
                  <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">Ship Class:</span>
                                            </div>
                                            <select name="ship" class="custom-select" id="inputGroupSelect01" placeholder="Test" required>
                                                <?php
                                                foreach ($shipList as $shipId => $shipName) {
                                                    echo '<option value="' . $shipId . '"' . ($burgerking['ship'] == $shipId ? ' checked' : '') . '>' . $shipName . '</option>';
                                                }
                                                ?>
                                            </select>
</div>
<div class="input-group mb-3">
  <input type="url" name="link" id="link" class="form-control" value="<?php echo $blizzard; ?>"
  placeholder="Coriolis Shortlink (Optional) https://s.orbis.zone/"
  pattern="(https?:\/\/(.+?\.)?orbis\.zone(\/[A-Za-z0-9\-\._~:\/\?#\[\]@!$&'\(\)\*\+,;\=]*)?)" size="30">
</div>

                  <button type="submit" class="btn btn-primary">Submit</button> <a href="." class="btn btn-warning">Go Back</a>
                  </form>
                </article>
                <div class="clearfix"></div>
            </section>
          </div>
          <?php include '../../assets/includes/footer.php'; ?>
      </body>
      </html>
