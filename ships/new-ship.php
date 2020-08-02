<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../../users/init.php';  //make sure this path is correct!
if (!securePage($_SERVER['PHP_SELF'])){die();}

$db = include '../db.php';
$mysqli = new mysqli($db['server'], $db['user'], $db['pass'], $db['db'], $db['port']);
$shipList = [];
$res = $mysqli->query('SELECT * FROM lookups.ships_lu ORDER BY ship_id');
while ($shipclass = $res->fetch_assoc()) {
    $shipList[$shipclass['ship_id']] = $shipclass['ship_name'];
}
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$mysqli = new mysqli($db['server'], $db['user'], $db['pass'], $db['db'], $db['port']);
$stmt = $mysqli->prepare("SELECT seal_ID FROM ships WHERE seal_ID =?");
$stmt->bind_param("i", $user->data()->id);
$stmt->execute();
$numAlias = $stmt->get_result();
$stmt->close();
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
      $stmt = $mysqli->prepare('CALL spCreateShipCleaner(?,?,?)');
      $stmt->bind_param('sii', $lore['new_ship'], $lore['ship'], $user->data()->id);
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
        <title>New Ship | The Hull Seals</title>
        <?php include '../../assets/includes/headerCenter.php'; ?>
</head>
<body>
    <div id="home">
      <?php include '../../assets/includes/menuCode.php';?>
        <section class="introduction container">
	    <article id="intro3">
                    <h1>Register a New Ship</h1>
                    <br />
					<h5>Please enter the new Ship and details.</h5>
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
                        <button type="submit" class="btn btn-primary">Submit</button> <a href="." class="btn btn-warning">Go Back</a>
                    </form>
                    <?php
    if($numAlias->num_rows === 0)
    exit('</article>
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
    ?>
  </article>
  <div class="clearfix"></div>
</section>
</div>
<?php include '../../assets/includes/footer.php'; ?>
</body>
</html>
