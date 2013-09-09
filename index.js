$(afterThePageLoads);

function afterThePageLoads() {
    var adminTableData = {};
    var takenOrdersData = {};
    refreshTable();
    refreshTakenOrders();

    setInterval(function() {
        refreshTable();
        refreshTakenOrders();
    }, 3000);

    function refreshTakenOrders() {
        $.ajax({
            url: "./takenOrders.php",
            success: function(data) {
                data = JSON.parse(data);
                console.log(data);
                if (!_.isEqual(data,takenOrdersData)) {
                    var source = $("#taken-orders-template").html();
                    var template = Handlebars.compile(source);
                    var html = template(data);
                    $("#taken-orders").html(html);
                    takenOrdersData = data;
                    console.log("updated taken orders!");
                } else {
                    console.log("no updates to be made to taken orders!");
                }
            }
        })
    }

    function refreshTable() {
        console.log("checking for updates...");
        $.ajax({
            url: "./adminTable.php",
            success: function(data) {
                data = JSON.parse(data);
                if (!_.isEqual(data,adminTableData)){
                    var source = $("#admin-table-template").html();
                    var template = Handlebars.compile(source);
                    var html = template(data);
                    $("#admin-table").html(html);
                    adminTableData = data;
                    $(afterTableLoads());
                    console.log("changes loaded!");
                } else {
                    console.log("no changes have been made to admin table!");
                }
            }
        })
    }

    var menuInputFile = $("#menuInputFile");
    var menuInputURL = $("#menuInputURL");
    menuInputFile.change(function() {
        if (menuInputFile.val() == "") {
            menuInputURL.attr('disabled', false);
        } else {
            menuInputURL.attr('disabled', true);
        }
    });
    menuInputURL.change(function() {
        if (menuInputURL.val() == "") {
            menuInputFile.attr('disabled', false);
        } else {
            menuInputFile.attr('disabled', true);
        }
    });

    $('.close-modal').click(function() {
        $(this).closest('.modal').modal('hide');
        $(this).closest('.modal-body .form').trigger('reset');
        menuInputFile.attr('disabled', false);
        menuInputURL.attr('disabled', false);
    });

    $('.logout-button').click(function() {
        $.ajax({
            url: "logout.php",
            success: function() {
                window.location.reload(true);
            }
        })
    });

    $("form#restaurant").submit(function() {
        var formData = new FormData($(this)[0]);
        $.ajax({
            url: "./insertRestaurant.php",
            type: 'POST',
            data: formData,
            async: false,
            success: function(data) {
                $('#add_restaurant').modal('hide');
                $('form#restaurant').trigger('reset');
                menuInputFile.attr('disabled', false);
                menuInputURL.attr('disabled', false);
                console.log(data);
            },
            cache: false,
            contentType: false,
            processData: false
        });
        refreshTable();
        return false;
    });

    function afterTableLoads() {

        $(".close-orders-button").hide();

        $('.delete-button').click(function() {
            var restaurant_id = $(this).data('restaurant-id');
            $("#" + restaurant_id).hide();
            $.ajax({
                type: "POST",
                url: "./deleteRestaurant.php",
                data: {'restaurant_id': restaurant_id},
                success: function (data) {
                    console.log(data);
                }
            })
        });

        $(".restaurant-actions").each(function (index, element) {
            var url = $(this).data('menu-url');
            if (url) {
                $(this).find('.add-menu-button').hide();
            } else {
                $(this).find('.view-menu-button').hide();
            }
            if ($(this).data('taking-orders')) {
                $(this).find('.take-orders-button').hide();
            } else {
                $(this).find('.order-in-progress-button').hide();
            }
            if ($(this).data('auth-close')) {
                $(this).find('.close-orders-button').show();
            }
        });

        $(".view-menu-button").click(function() {
            window.open($(this).data('menu-url'));
        })

        $(".add-menu-button").click(function() {
            var restaurant_id = $(this).data('restaurant-id');
            $("#menuModal").modal('show');
            $("form#menu").submit(function() {
                var formData = new FormData($(this)[0]);
                formData.append('restaurant_id', restaurant_id);
                $.ajax({
                    url: "./addMenu.php",
                    type: 'POST',
                    data: formData,
                    async: false,
                    success: function() {
                        $('#menuModal').modal('hide');
                        refreshTable();
                    },
                    cache: false,
                    contentType: false,
                    processData: false
                });
                return false;
            });
        });

        $(".take-orders-button").click(function() {
            var restaurant_id = $(this).data('restaurant-id');
            $("#" + restaurant_id + " .take-orders-button").hide();
            $("#" + restaurant_id + " .order-in-progress-button").show();
            $("#" + restaurant_id + " .close-orders-button").show();
            $.ajax({
                url: "./orderInProgress.php",
                type: 'POST',
                data: {'action': "take_orders", 'restaurant_id': restaurant_id}
            });
        });

        $(".close-orders-button").click(function() {
            var button = $(this);
            button.closest(".restaurant-actions").find('.take-orders-button').show();
            button.closest(".restaurant-actions").find(".order-in-progress-button").hide();
            button.hide();

            var restaurant_id = $(this).data('restaurant-id');
            $.ajax({
                url: "./orderInProgress.php",
                type: 'POST',
                data: {'action': "deactivate_restaurant", 'restaurant_id': restaurant_id},
                success: function(data) {
                    data = JSON.parse(data);
                    var htmlTakenOrders = data['htmlTakenOrders'];
                    $('#taken_orders').append(htmlTakenOrders);
                }

            })
        });
    };
}

