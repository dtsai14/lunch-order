function afterThePageLoads() {
    $('#invalid-alert').hide(); // hide invalid username/password alert initially

    /* when login form is submitted, either logs in user and redirects to
     * homepage, or displays invalid username/password alert */
    $('form#login').submit(function() {
        $.ajax({
            url: "./loginApi.php",
            type: 'POST',
            data: {'cmd': 'loginUser', 'username': $('#username').val(),
            'password': $('#password').val()},
            success: function(data) {
                data = JSON.parse(data);
                if (data['loggedIn']) {
                    window.location.href="../index.php";
                } else {
                    $('#invalid-alert').show();
                }
            }
        });
        return false;
    })
}

$(afterThePageLoads);