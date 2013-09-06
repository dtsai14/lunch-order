$(afterThePageLoads);

function afterThePageLoads() {
    $('#voting_container').hide();
    $('#ordering_container').hide();
    var takingOrders;
    var voteTableData = {};
    var intervalId;
    var activeRestaurantsData = {};
    var ordersData = {};
    checkSwap();

    setInterval(function () {
        checkSwap();
    }, 10000);

    $("#voting_form").submit(function () {
        $.ajax({
            url: "sendVote.php",
            data: {'restaurant_id': $('input[name=vote]:checked').val()},
            type: "POST",
            success: function (data) {
                console.log(data['data_added']);
                data = JSON.parse(data);
                if (data['data_added']) {
                    $('#vote_alert').html("<div class='alert alert-success alert-dismissable'>" +
                        "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>" +
                        "Your vote has been recorded!</div>");
                } else {
                    $('#vote_alert').html("<div class='alert alert-warning alert-dismissable'>" +
                        "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>" +
                        "You've already voted once today! Please vote again tomorrow.</div>");
                }
                $('input[name=vote]:checked').attr('checked', false);
                refreshVotes();
            },
            error: function () {
                console.log("ERROR", arguments);
            }
        });
        return false;
    });

    function refreshVotes() {
        $.ajax({
            url: "voteTable.php",
            success: function(data) {
                data = JSON.parse(data);
                console.log(data);
                if (!_.isEqual(data,voteTableData)) {
                    var source = $("#vote-table-template").html();
                    var template = Handlebars.compile(source);
                    var html = template(data);
                    voteTableData = data;

                    var selected = $('input[name=vote]:checked').val();
                    $("#vote-table").html(html);
                    $('.radio #' + selected).prop('checked', true);
                    console.log("votes refreshed!");
                } else {
                    console.log("no new votes to update");
                }
            }
        })
    }

    // adds the order to the database and refreshes the div to include new orders
    $("#order_form").submit(function () {
        var order = $('#order').val();
        $.ajax({
                url: "sendOrder.php",
                type: "POST",
                data: {'restaurant_id': $('#restaurant-dropdown').val(),
                    'order': order},
                success: function (data) {
                    $('#order').val("");
                    refreshOrders();
                }
            });
        return false;
    });

    function refreshActiveRestaurants() {
        var restaurantChoice = $('#restaurant-dropdown').val();
        $.ajax({
            url: "/lunchorder/activeRestaurants.php",
            success: function (data) {
                data = JSON.parse(data);
                if (!_.isEqual(data,activeRestaurantsData)) {
                    var dropdownSource = $("#restaurant-dropdown-template").html();
                    var dropdownTemplate = Handlebars.compile(dropdownSource);
                    var dropdownHtml = dropdownTemplate(data);
                    $("#restaurant-dropdown").html(dropdownHtml);
                    $("#restaurant-dropdown").val(restaurantChoice);

                    var alertsSource = $('#restaurant-alerts-template').html();
                    var alertsTemplate = Handlebars.compile(alertsSource);
                    var alertsHtml = alertsTemplate(data);
                    $("#restaurant-alerts").html(alertsHtml);
                    activeRestaurantsData = data;
                    console.log("active restaurants updated!")
                } else {
                    console.log("no changes in active restaurants!")
                }
            }
        })
    }

    // accesses database to update div with all current orders
    function refreshOrders() {
        $.ajax({
            url: "/lunchorder/orderList.php",
            success: function (data) {
                data = JSON.parse(data);
                console.log(data);
                if (!_.isEqual(data,ordersData)) {
                    var source = $('#order-list-template').html();
                    var template = Handlebars.compile(source);
                    var html = template(data);
                    $("#order-list").html(html);
                    ordersData = data;
                    console.log("orders refreshed!");
                } else {
                    console.log("no new orders since last update!");
                }
            },
            error: function () {
                console.log("ERROR", arguments);
            }
        });
    }

    function checkSwap() {
        $.ajax({
            url: "/lunchorder/orderInProgress.php",
            type: 'POST',
            data: {'action': "get_active_restaurants"},
            success: function(data) {
                data = JSON.parse(data);
                var activeRestaurants = data['activeRestaurants'];
                var takingOrdersCheck = activeRestaurants.length > 0;
                if (takingOrdersCheck != takingOrders){
                    clearInterval(intervalId);
                    takingOrders = takingOrdersCheck;
                    if (takingOrders) {
                        console.log("changed to taking orders!");
                        refreshActiveRestaurants();
                        refreshOrders();
                        $('#ordering_container').show();
                        $('#voting_container').hide();

                        intervalId = setInterval(function () {
                            refreshActiveRestaurants();
                            refreshOrders();
                        }, 5000);
                    } else {
                        console.log("changed to voting!");
                        refreshVotes();
                        $('#voting_container').show();
                        $('#ordering_container').hide();

                        intervalId = setInterval(function () {
                            refreshVotes();
                        }, 5000);
                    }
                }
            },
            error: function() {
                console.log("ERROR", arguments);
            }
        });
    }
}










