<?php
include 'authenticate.php';
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

            <?php
            $pdo = new PDO('mysql:host=localhost;dbname=lunch_master', 'root', '', array(PDO::ATTR_PERSISTENT => false));
            $stmt = $pdo->prepare("SELECT * FROM restaurants ORDER BY name");
            $stmt->execute();
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
                echo "<tr>";
                echo "<td>" . "<div class='radio'> <label> <input type='radio' name='vote' id=\"" . $row['id'] . "\" value=\"" . $row['id'] . "\" required>" . "</td>";
                echo "<td>" . "<span onClick=\"window.open('" . $row['menu_url'] . "')\">" . $row['name'] . "</span>" . "</td>";
                echo "<td>" .
                    "<div class='progress progress-striped'>
                    <div class='progress-bar progress-bar-info' role='progressbar' aria-valuenow='10' aria-valuemin='0' aria-valuemax='16' style='width: 10%'>
                    </div><span class='badge'>42</span>

                    </div>"
                    . "</td>";
                echo "</tr>";
            }
            ?>

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

<script type="text/javascript">
    $("#voting_form").submit(function() {
        $.ajax('sendVote.php', {
                data: {'restaurant_id': $('input[name=vote]:checked').val()},
            type: "POST",
            success: function(data) {
                console.log(data['data_added']);
                data = JSON.parse(data);
                if (data['data_added']) {
                    $('#vote_alert').html("<div class='alert alert-success alert-dismissable'>" +
                        "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>" +
                        "Your vote has been recorded!</div>");
                } else {
                    $('#vote_alert').html("<div class='alert alert-warning alert-dismissable'>" +
                        "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>" +
                        "You've already voted once today!</div>");
                }
                $('input[name=vote]:checked').attr('checked', false);
            },
            error: function() {
                console.log("ERROR", arguments);
            }
        });
        return false;
    });

    // adds the order to the database and refreshes the div to include new orders
    $("#send_order").click(function() {
        $.ajax('sendOrder.php',
            {data: {'restaurant' : $( '#restaurant').val(),
                'order' : $( '#order').val()},
            type: "POST",
            success: function(data) {
                console.log(data);
            }}
        ).done(function() {
                $('#order').val("");
                refresh();
            }
        );
        return false;
    });

    // causes div to be refreshed as soon as page is loaded
    $(function(){
        refresh();
    });

    // accesses database to update div with all current orders
    function refresh(){
        console.log("Running Ajax");
        $.ajax('/lunchorder/orderList.php', {
            success: function(data) {
                data = JSON.parse(data);
                console.log(data);
                var orders = data['orders'];
                console.log(orders);
                var htmlOrder = "";
                for (i = 0; i < orders.length ; ++i) {
                    order = orders[i];
                    htmlOrder += "<div class='panel panel-default'><div class='panel-heading'><h3 class='panel-title'>"
                        + order['username'] + "'s order from " + "<span class='badge pull-right'>" + order['creation_time']
                        + "</span></h3></div><div class='panel-body'>" + order['text'] + "</div></div>";
                }
                $('#flash').html(htmlOrder);
            },
            error: function(){
                console.log("ERROR", arguments);
            }
        });
    }
    // refreshes div at a regular interval
    setInterval(function(){
        refresh();
    }, 10000);
</script>
</body>
</html>