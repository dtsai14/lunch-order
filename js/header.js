function afterThePageLoads() {
    $('.alert').hide();
    updateGreeting();
    alertQuote();
    displayPic();

    /* update greeting every 3 seconds */
    setInterval(function () {
        updateGreeting();
    }, 3000);

    var interval = setInterval(function() {
        displayPic();
        console.log("will run every 3 seconds until user has voted and pic is displayed!!");
    }, 3000);
    /* logs user out and refreshes to login page*/
    $('.logout-button').click(function () {
        $.ajax({
            url: "./header/headerApi.php",
            type: 'POST',
            data: {'cmd': 'logout'},
            success: function() {
                window.location.reload(true);
            }
        })
    });

    /* checks to see if picture should be displayed beside greeting, inserting*/
    function displayPic() {
        $.ajax({
            url: "./header/headerApi.php",
            type: 'POST',
            data: {'cmd': 'getPicUrl'},
            success: function (data) {
                data = JSON.parse(data);
                if (data['display_pic']) {
                    var source = $('#pic-opt-template').html();
                    var template = Handlebars.compile(source);
                    var html = template(data);
                    $('#pic-opt').html(html);
                    clearInterval(interval);
                    console.log("no longer checking for pic!");
                }
            }
        })
    }

    /* updates greeting based on time */
    function updateGreeting() {
        $.ajax({
            url: "./header/headerApi.php",
            type: 'POST',
            data: {'cmd': 'getGreeting'},
            success: function (data) {
                data = JSON.parse(data);
                $('#greeting').html(data['greeting']);
            },
            error: function(args) {
                console.log(args);
            }
        })
    }

    /* displays quote of day, unless user has closed quote of day alert during
    * this session*/
    function alertQuote() {
        $.ajax({
            url: "./header/headerApi.php",
            type: 'POST',
            data: {'cmd': 'getQuote'},
            success: function(data) {
                data = JSON.parse(data);
                if (data['display_quote']) {
                    $('#quote').html(data['quote']);
                    $('#person').html(data['person']);
                    $('.alert').show();
                }
            }
        })
    }

    /* when quote of day alert is closed, keeps user from seeing alert again
     * this session */
    $('#quote-alert').bind('close.bs.alert', function() {
        $.ajax({
            url: "./header/headerApi.php",
            type: 'POST',
            data: {'cmd': 'closeAlert'},
            success: function(data) {
                console.log("display quote =");
                console.log(data);
            }
        })
    })
}

$(afterThePageLoads);