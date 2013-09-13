function afterThePageLoads() {
    /* when registration form is submitted, displays either a warning alert if
    * username is already taken, or registers user and displays a welcome alert
    * providing link to LunchMaster homepage */
    $('#registration-form').submit(function() {
        $.ajax({
            url: "./loginApi.php",
            type: 'POST',
            data: {'cmd': 'registerUser', "first_name": $("#first-name").val(),
            "last_name": $("#last-name").val(), "username": $("#username").val(),
            "password": $("#password").val(), "email": $("#email").val()},
            success: function(data) {
                data = JSON.parse(data);
                var source = $('#register-alert-template').html();
                var template = Handlebars.compile(source);
                var html = template(data);
                $('#register-alert').html(html);
            }
        });
        return false;
    })
}

$(afterThePageLoads);