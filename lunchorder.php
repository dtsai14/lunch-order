<?php
include 'authenticate.php';
include 'mysql.php';
?>

<!DOCTYPE html>
<html>
<head lang="en">
    <title>Lunch Task Manager</title>
    <?php
    include 'bootstrapSources.php';
    ?>
</head>
<body>
<?php
date_default_timezone_set('America/Los_Angeles');

//store all restaurant names alphabetically in an array $restaurantNames
/*try {
    $pdo = new PDO('mysql:host=localhost;dbname=lunch_master', 'root', '', array(PDO::ATTR_PERSISTENT => false));
    $stmt = $pdo->prepare("SELECT * FROM restaurants ORDER BY name");
    $stmt->execute();

    $restaurantNames = array();
    while ($rs = $stmt->fetch(PDO::FETCH_OBJ)) {
        $restaurantNames [] = $rs->name;
    }
} catch (PDOException $e) {
    print "Error!: " . $e->getMessage() . "<br/>";
    die();
}*/
?>

<div class="container">
    <div class="page-header">
        <h1>Good Morning, <?php echo $_SESSION['firstname'] ?>!</h1>
    </div>

    <nav class="navbar navbar-default" role="navigation">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <a class="navbar-brand" href="#">LunchMaster</a>
        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse navbar-ex1-collapse">
            <ul class="nav navbar-nav">
                <li class="active"><a href="#">Lunch Order</a></li>
                <li><a href="/lunchorder">Admin Page</a></li>
            </ul>

            <ul class="nav navbar-nav navbar-right">
                <a class="btn btn-primary navbar-btn" href="/lunchorder/logout.php">Logout</a>
            </ul>
        </div>
        <!-- /.navbar-collapse -->
    </nav>

    <h3>Which restaurant would you like the office to order from today?</h3>
    <form id="voting_form" role="form">
        <table id="voting" class="table table-bordered table-hover table-striped">
            <thead>
            <tr>
                <th style="width: 5%">Vote</th>
                <th style="width: 20%">Restaurant Name</th>
                <th>Current Votes</th>
            </tr>
            </thead>
            <tbody>

            <?php foreach (getRestaurants() as $restaurant) { ?>
                <tr>
                <td>
                    <div class='radio'>
                        <input type='radio' name='vote' id="<?= $restaurant['id'] ?>" value="<?= $restaurant['id'] ?>" required>
                </td>
                <td>
                    <span onClick='window.open("<?= $restaurant['menu_url'] ?>")'> <?= $restaurant['name'] ?> </span>
                </td>
                <td>
                    <div class='progress progress-striped'>
                        <div class='progress-bar progress-bar-info' role='progressbar' aria-valuenow='10' aria-valuemin='0' aria-valuemax='16' style='width: 10%'></div>
                        <span class='badge'>42</span>
                    </div>
                </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
        <button id="send_vote" type="submit" class="btn btn-default">Vote</button>
        <span id="vote_alert"></span>
    </form>

    <div class="orderForm"></div>
    <div id="restaurantOfDay">
        <h2>Today's menu is from <h2>
    </div>

    <form role="form">
        <div class="form-group">
            <label for="restaurant">Restaurant</label>
            <input type="text" class="form-control" id="restaurant" placeholder="Enter Restaurant">
        </div>
        <div class="form-group">
            <label for="order">Order</label>
            <textarea name="order" class="form-control" id="order" maxlength="1000" cols="20" rows="6" required></textarea>
        </div>
        <button id="send_order" class="btn btn-default">Submit</button>
    </form>

    <h2>Orders <span class="pull-right"><small><?php echo date('l, F d, Y')?></small></span></h2>
    <div id="flash"></div>
<?php
/*$statement = $pdo->prepare("SELECT * FROM orders WHERE date(creation_date) = curdate() ORDER BY creation_date DESC");
$statement->execute();

foreach ($statement->fetchAll() as $row) {
    echo "<div class='panel panel-default'>
      <div class='panel-heading'>

        <h3 class='panel-title'>" . $row['user_id'] . "<span class='badge pull-right'>" . date("g:i a, l, F d, Y", strtotime($row['creation_date'])) . "</span></h3>
      </div>
      <div class='panel-body'>" .
        $row['text']
        ."</div>
    </div>";
}*/
?>

</div>

<script type="text/javascript" src="lunchorder.js"></script>
</body>
</html>