<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//UserSpice Required
require_once '../users/init.php';  //make sure this path is correct!
if (!securePage($_SERVER['PHP_SELF'])){die();}


$counter = 0;
if (isset($_SESSION['2ndrun'])) {
  unset($_SESSION['2ndrun']);
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta content="Hull Seals Ship Registration Portal" name="description">
    <title>My Fleet | The Hull Seals</title>
    <?php include '../assets/includes/headerCenter.php'; ?>
    <script>
    $(function () {
  $('[data-toggle="tooltip"]').tooltip()
})
</script>
</head>
<body>
    <div id="home">
      <?php include '../assets/includes/menuCode.php';?>
        <section class="introduction container">
	    <article id="intro3">
    <h1>My Fleet</h1>
    <p>Please select which type of roster you would like to see.</p>
    <p>
      <a href="ships" class="btn btn-primary btn-lg">Manage My Ships</a>
      <button type="button" class="btn btn-secondary btn-lg" data-toggle="tooltip" data-placement="top" title="Coming Soon!">
        Manage My Carriers
      </button>
    </p>
  </article>
  <div class="clearfix"></div>
</section>
</div>
<?php include '../assets/includes/footer.php'; ?>
</body>
</html>
