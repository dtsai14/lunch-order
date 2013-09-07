function afterThePageLoads() {
    $('.logout-button').click(function() {
        $.ajax({
            url: "./logout.php",
            success: function() {
                window.location.reload(true);
            }
        })
    });
}

$(afterThePageLoads);