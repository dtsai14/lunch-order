function afterThePageLoads() {
    $('.alert').hide();
    updateGreeting();
    alertQuote();

    $('.logout-button').click(function() {
        $.ajax({
            url: "./logout.php",
            success: function() {
                window.location.reload(true);
            }
        })
    });

    setInterval(function () {
        updateGreeting();
    }, 3000);

    function alertQuote() {
        $.ajax({
            url: "./greeting.php",
            success: function(data) {
                data = JSON.parse(data);
                if (data['displayQuote']) {
                    $('#quote').html(data['quote']);
                    $('#person').html(data['person']);
                    $('.alert').show();
                }
            }
        })
    }
    function updateGreeting() {
        $.ajax({
            url: "./greeting.php",
            success: function(data) {
                data = JSON.parse(data);
                $('#greeting').html(data['greeting']);
            }
        })
    }

    $('#quote-alert').bind('close.bs.alert', function() {
        $.ajax({
            url: "./closeAlert.php",
            success: function() {
                console.log("won't display quote anymore!");
            }
        })
    })
}

$(afterThePageLoads);