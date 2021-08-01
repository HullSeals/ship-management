<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//UserSpice Required
require_once '../users/init.php';  //make sure this path is correct!
if (!securePage($_SERVER['PHP_SELF'])){die();}

//
$db = include 'db.php';
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
<meta content="The Seal Fleets" name="description">
<title>The Seal Fleets | The Hull Seals</title>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
<?php include '../assets/includes/headerCenter.php'; ?>
</head>
<body>
    <div id="home">
      <?php include '../assets/includes/menuCode.php';?>
        <section class="introduction container">
	    <article id="intro3">
    <h1>Our Fleet</h1>
    <p>Here you can view all registered repair ships within the Seal fleet. This is not a complete list, however, and registration is optional.</p>
    <?php
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $db = include 'db.php';
    $mysqli = new mysqli($db['server'], $db['user'], $db['pass'], $db['db'], $db['port']);
    $stmt = $mysqli->prepare("SELECT ID, seal_ID, ship_name, class FROM ships WHERE del_flag <> 1");
    $stmt->execute();
    $result = $stmt->get_result();
    echo "<h3>Returning all Registered Ships: ";
    echo nl2br ("</h3>");
    echo '<table class="table table-dark table-striped table-bordered table-hover table-responsive-md">
          <tr>
              <td>Registry Number</td>
              <td>Ship Name Name</td>
              <td>Class</td>
             <td>Owner</td>
          </tr>';
        while ($row = $result->fetch_assoc()) {
            $field1name = $row["ID"];
            $field2name = $row["ship_name"];
            $field3name = $row["class"];
            $field4name = $row["seal_ID"];
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
                      echo '</td> <td>';
                        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
                        $stmt2 = $mysqli->prepare("SELECT seal_name FROM sealsudb.staff WHERE seal_ID = ? LIMIT 1");
                        $stmt2->bind_param("i", $field4name);
                        $stmt2->execute();
                        $result2 = $stmt2->get_result();
                        $result2 = mysqli_fetch_assoc($result2);
                        $stmt2->close();
                        if (!isset($result2)) {
                          echo 'CMDR Not Set</td></tr>';
			}
			else {
                          echo $result2['seal_name'];'</td></tr>';
			}
        }
        echo '</table>';
        $result->free();
    ?>
    <p><small><sub>* HS00-HS19 are reserved for future use.</sub></small></p>
    <br />
    <a href="." class="btn btn-success btn-lg active" >Manage Your Ships</a>
  </article>
  <div class="clearfix"></div>
</section>
</div>
<?php include '../assets/includes/footer.php'; ?>
</body>
</html>
