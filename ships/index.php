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
if (isset($_GET['delete'])) {
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
if (isset($_GET['new'])) {
    foreach ($_REQUEST as $key => $value) {
        $lore[$key] = strip_tags(stripslashes(str_replace(["'", '"'], '', $value)));
    }
    if (!isset($shipList[$lore['ship']])) {
        $validationErrors[] = 'invalid ship';
    }
    if (!count($validationErrors)) {
      $stmt = $mysqli->prepare('CALL spCreateShipCleaner(?,?,?,?)');
      $stmt->bind_param('siis', $lore['new_ship'], $lore['ship'], $user->data()->id, $lgd_ip);
      $stmt->execute();
      foreach ($stmt->error_list as $error) {
          $validationErrors[] = 'DB: ' . $error['error'];
      }
      $stmt->close();
  header("Location: .");
    }
}
if (isset($_GET['edit'])) {
    foreach ($_REQUEST as $key => $value) {
        $lore[$key] = strip_tags(stripslashes(str_replace(["'", '"'], '', $value)));
    }
    if (!isset($shipList[$lore['ship']])) {
        $validationErrors[] = 'invalid ship';
    }
    if (!count($validationErrors)) {
      $stmt = $mysqli->prepare('CALL spEditShipCleaner(?,?,?,?)');
      $stmt->bind_param('siis', $lore['edt_alias'], $lore['ship'], $lore['numberedt'], $lgd_ip);
      $stmt->execute();
      foreach ($stmt->error_list as $error) {
          $validationErrors[] = 'DB: ' . $error['error'];
      }
      $stmt->close();
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
    $no_rows = 0;
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $db = include '../db.php';
    $mysqli = new mysqli($db['server'], $db['user'], $db['pass'], $db['db'], $db['port']);
    $stmt = $mysqli->prepare("SELECT ID, seal_ID, ship_name, class FROM ships WHERE seal_ID =? AND del_flag <> 1");
    $stmt->bind_param("i", $user->data()->id);
    $stmt->execute();
    $result = $stmt->get_result();
    if($result->num_rows === 0) {
      $no_rows = 1;
    }
    if ($no_rows == 1) {

    }
    else {
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
        <form action="?delete" method="post">
            <input type="hidden" name="numberedt" value="'.$field1name.'" required>
          <button type="submit" class="btn btn-danger">Yes, Remove.</button><button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          </form>
      </div>
    </div>
  </div>
</div>';
echo '<div class="modal fade" id="moE'.$field1name.'" tabindex="-1" aria-hidden="true">
				  <div class="modal-dialog modal-dialog-centered">
				    <div class="modal-content">
				      <div class="modal-header">
				        <h5 class="modal-title" id="exampleModalLabel" style="color:black;">Edit Ship<h5>
				        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
				          <span aria-hidden="true">&times;</span>
				        </button>
				      </div>
				      <div class="modal-body" style="color:black;">
				      <form action="?edit" method="post">
				        <div class="input-group mb-3">
				                  <div class="input-group-prepend">
				                      <span class="input-group-text">Edited Name:</span>
				                  </div>
				                  <input type="text" name="edt_alias" value="';
				                   echo $field2name;
				                   echo '" class="form-control" placeholder="Edited Ship Name" aria-label="Edited Ship Name" required>
				      </div>
              <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Ship Class:</span>
                                        </div>
                                        <select name="ship" class="custom-select" id="inputGroupSelect01" placeholder="Test" required>
                                          <option selected disabled>Choose...</option>';
                                            foreach ($shipList as $shipId => $shipName) {
                                                echo '<option value="' . $shipId . '"' . ($burgerking['ship'] == $shipId ? ' checked' : '') . '>' . $shipName . '</option>';
                                            }
                                        echo '</select>
</div>
				      <div class="modal-footer">
				            <input type="hidden" name="numberedt" value="'.$field3name.'" required>
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
    <button class="btn btn-success btn-lg active" data-target="#moNew" data-toggle="modal" type="button">Register a New Ship</button>
    <div aria-hidden="true" class="modal fade" id="moNew" tabindex="-1">
					<div class="modal-dialog modal-dialog-centered">
						<div class="modal-content">
							<div class="modal-header">
								<h5 class="modal-title" id="exampleModalLabel" style="color:black;">New Ship</h5><button aria-label="Close" class="close" data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
							</div>
							<div class="modal-body" style="color:black;">
								<form action="?new" method="post">
									<div class="input-group mb-3">
										<input type="text" name="new_ship" value="<?= $lore['new_ship'] ?? '' ?>" class="form-control" placeholder="New Ship Name" aria-label="New Ship Name" required>
									</div>
                  <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">Ship Class</span>
                                            </div>
                                            <select name="ship" class="custom-select" id="inputGroupSelect01" placeholder="Test" required>
                                              <option selected disabled>Choose...</option>
                                                <?php
                                                foreach ($shipList as $shipId => $shipName) {
                                                    echo '<option value="' . $shipId . '"' . ($shipclass['ship'] == $shipId ? ' checked' : '') . '>' . $shipName . '</option>';
                                                }
                                                ?>
                                            </select>
</div>
									<div class="modal-footer">
										<button class="btn btn-primary" type="submit">Submit</button><button class="btn btn-secondary" data-dismiss="modal" type="button">Close</button>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>

  </article>
  <div class="clearfix"></div>
</section>
</div>
<?php include '../../assets/includes/footer.php'; ?>
</body>
</html>
