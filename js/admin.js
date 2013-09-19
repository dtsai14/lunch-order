$(afterThePageLoads);

function afterThePageLoads() {
    var adminTableData = {};
    var takenOrdersData = {};
    refreshTable();
    refreshTakenOrders();

    /* check for changes to table and taken orders every 3 seconds */
    setInterval(function () {
        refreshTable();
        refreshTakenOrders();
    }, 3000);

    /* checks if user has taken and closed orders today and displays it on page */
    function refreshTakenOrders() {
        $.ajax({
            url: './api.php',
            type: 'POST',
            data: {'cmd': 'getTakenOrders'},
            success: function(data) {
                data = JSON.parse(data);
                if (!_.isEqual(data,takenOrdersData)) {
                    var source = $('#taken-orders-template').html();
                    var template = Handlebars.compile(source);
                    var html = template(data);
                    $('#taken-orders').html(html);
                    takenOrdersData = data;
                    console.log('updated taken orders!');
                } else {
                    console.log('no updates to be made to taken orders!');
                }
            }
        })
    }

    /* checks for and updates changes to admin table */
    function refreshTable() {
        $.ajax({
            url: './api.php',
            type: 'POST',
            data: {'cmd': 'getTable'},
            success: function(data) {
                data = JSON.parse(data);
                if (!_.isEqual(data,adminTableData)){
                    var source = $('#admin-table-template').html();
                    var template = Handlebars.compile(source);
                    var html = template(data);
                    $('#admin-table').html(html);
                    adminTableData = data;
                    $(afterTableLoads());
                    console.log('changes loaded!');
                } else {
                    console.log('no changes have been made to admin table!');
                }
            }
        })
    }

    /* sets behavior that adding input to one of the menu input fields will
    * disable the other input field */
    var menuInputFile = $('#menuInputFile');
    var menuInputURL = $('#menuInputURL');
    menuInputFile.change(function () {
        if (menuInputFile.val() == '') {
            menuInputURL.attr('disabled', false);
        } else {
            menuInputURL.attr('disabled', true);
        }
    });
    menuInputURL.change(function () {
        if (menuInputURL.val() == '') {
            menuInputFile.attr('disabled', false);
        } else {
            menuInputFile.attr('disabled', true);
        }
    });

    /* closes and resets modal when its close button is clicked */
    $('.close-modal').click(function () {
        $(this).closest('.modal').modal('hide');
        $(this).closest('.modal-body .form').trigger('reset');
        menuInputFile.attr('disabled', false);
        menuInputURL.attr('disabled', false);
    });

    /* adds restaurant to database, closes and resets modal when its form is
    submitted */
    $('form#restaurant').submit(function () {
        var formData = new FormData($(this)[0]);
        $.ajax({
            url: './admin/addRestaurant.php',
            type: 'POST',
            data: formData,
            async: false,
            success: function () {
                $('#add_restaurant').modal('hide');
                $('form#restaurant').trigger('reset');
                menuInputFile.attr('disabled', false);
                menuInputURL.attr('disabled', false);
            },
            cache: false,
            contentType: false,
            processData: false
        });
        refreshTable();
        return false;
    });

    function afterTableLoads() {
        /* hide all close orders buttons at first*/
        $('.close-orders-button').hide();

        /* deletes given restaurant when its delete button is clicked */
        $('.delete-button').click(function () {
            var restaurantId = $(this).data('restaurant-id');
            $('#' + restaurantId).hide();
            $.ajax({
                url: './api.php',
                type: 'POST',
                data: {'restaurant_id': restaurantId, 'cmd': 'deleteRestaurant'},
                success: function () {
                    console.log('restaurant deleted!');
                }
            })
        });

        /* goes through each restaurant in table to hide/show (depending),
        * add menu/view menu buttons, taking orders/order in progress buttons,
        * and close orders buttons */
        $('.restaurant-actions').each(function (index, element) {
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

        $('.restaurant-phone').each(function () {
            var phone_num = $(this).data('phone-num');
            $(this).find('.add-phone-form').hide();
            if (phone_num) {
                $(this).find('.add-phone-button').hide();
            } else {
                $(this).find('.call-link').hide();
            }
        });

        /* opens given restaurant's menu in a separate tab */
        $('.view-menu-button').click(function () {
            window.open($(this).data('menu-url'));
        });

        /* opens add-menu-modal and adds menu to database on submission of form */
        $('.add-menu-button').click(function () {
            var restaurantId = $(this).data('restaurant-id');
            $('#menuModal').modal('show');
            $('form#menu').submit(function () {
                var formData = new FormData($(this)[0]);
                formData.append('restaurant_id', restaurantId);

                $.ajax({
                    url: './admin/addMenu.php',
                    type: 'POST',
                    data: formData,
                    async: false,
                    success: function () {
                        $('#menuModal').modal('hide');
                        $('form#menu').trigger('reset');
                        refreshTable();
                    },
                    error: function(args) {
                        console.log(args);
                    },
                    cache: false,
                    contentType: false,
                    processData: false
                });
                return false;
            });
        });

        $('.add-phone-button').click(function () {
            var restaurantId = $(this).data('restaurant-id');
            $(this).hide();
            $('#' + restaurantId + ' .add-phone-form').show();

            $('.add-phone-form').submit(function () {
                $('#' + restaurantId + ' .add-phone-form').hide();
                var phoneInput = $(this).find('#phone-input').val();
                if (phoneInput) {
                    $('#' + restaurantId + ' .call-link').text(phoneInput).show();
                    $.ajax({
                        url: './api.php',
                        type: 'POST',
                        data: {'cmd': 'addPhone',
                            'restaurant_id': restaurantId,
                            'phone': phoneInput}
                    })
                } else {
                    $('#' + restaurantId + ' .add-phone-button').show();
                }
                return false;
            })
        });



        /* clicking take-orders-button opens the given restaurant for the day */
        $('.take-orders-button').click(function () {
            var restaurantId = $(this).data('restaurant-id');
            $('#' + restaurantId + ' .take-orders-button').hide();
            $('#' + restaurantId + ' .order-in-progress-button').show();
            $('#' + restaurantId + ' .close-orders-button').show();

            $.ajax({
                url: './api.php',
                type: 'POST',
                data: {'cmd': 'takeOrders', 'restaurant_id': restaurantId}
            });
        });

        /* clicking close-orders-button closes the given restaurant for the day */
        $('.close-orders-button').click(function () {
            var button = $(this);
            button.closest('.restaurant-actions').find('.take-orders-button').show();
            button.closest('.restaurant-actions').find('.order-in-progress-button').hide();
            button.hide();

            var restaurantId = $(this).data('restaurant-id');
            $.ajax({
                url: './api.php',
                type: 'POST',
                data: {'cmd': 'closeOrders', 'restaurant_id': restaurantId},
                success: function () {
                    refreshTakenOrders();
                }
            })
        });
    }
}

