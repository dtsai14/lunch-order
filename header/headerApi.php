<?php
include_once '../authenticate.php';
include_once '../mysql.php';

date_default_timezone_set('America/Los_Angeles');

/* logs user out */
function logout() {
    $_SESSION = array();
    if (session_id() != "" || isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time()-2592000, '/');
    }
    session_destroy();
}

/* depending on time of day, returns JSON array containing a greeting */
function getGreeting() {
    $hour = date("G");
    $minute = date("i");
    $name = $_SESSION['first_name'];

    if ($hour < 5) {
        $greeting = "It's too early to be up... =_=";
    } elseif ($hour < 12) {
        $greeting = "Good Morning, $name!";
    } elseif ($hour == 12) {
        if ($minute == 0) {
            $greeting = "Good Noon, $name! ~(^_^)/ ( ^_^）o自自o（^_^ ） ";
        } else {
            $greeting = "$name, it's time for lunch!";
        }
    } elseif ($hour < 19) {
        $greeting = "Good Afternoon, $name!";
    } elseif ($hour < 22) {
        $greeting = "Good Night, $name!";
    } else {
        $greeting = "Time to go to sleep...ZzzZzzZzz";
    }

    return json_encode(array("greeting" => $greeting));
}

/* depending on day, returns JSON array containing quote, author of quote, and
whether or not quote is to be displayed */
function getQuote() {
    $quotes = array(
        "Gandhi"=>"Strength does not come from physical capacity. It comes from
        an indomitable will.",
        "Amelia Earhart"=>"Everyone has oceans to fly, if they have the heart
        to do it. Is it reckless? Maybe. But what do dreams know of boundaries?",
        "Baruch Spinoza"=>"I have made a ceaseless effort not to ridicule, not
        to bewail, not to scorn human actions, but to understand them.",
        "Albert Einstein"=>"Try not to become a man of success but rather try
        to become a man of value.",
        "Buddha"=>"In compassion lies the world's true strength",
        "Helen Keller"=>"When one door closes another opens. But often we look
        so long so regretfully upon the closed door that we fail to see the one
        that has opened for us.",
        "Mohandas Gandhi"=>"Always aim at complete harmony of thought and word
        and deed. Always aim at purifying your thoughts and everything will be
        well.",
        "Mark Twain"=>"Twenty years from now you will be more disappointed by
        the things that you didn't do than by the ones you did do, so throw off
        the bowlines, sail away from the safe harbor, catch the trade winds in
        your sails. Explore, Dream, Discover.",
        "Chinese Proverb"=>"The best time to plant a tree was 20 years ago. The
        second best time is now.",
        "Zig Ziglar"=>"People often say that motivation doesn't last. Well,
        neither does bathing. That's why we recommend it daily.",
        "Ancient Indian Proverb"=>"Certain things catch your eye, but pursue
        only those that capture the heart.",
        "FACT"=>"There used to be a street named after Chuck Norris, but it was
        changed because nobody crosses Chuck Norris and lives.",
        "Montesquieu"=>"I have always observed that to succeed in the world one
        should seem a fool, but be wise.",
        "Eleanor Roosevelt"=>"Life was meant to be lived, and curiosity must be
        kept alive. One must never, for whatever reason, turn his back on life.",
        "Henry David Thoreau"=>"As a single footstep will not make a path on the
        earth, so a single thought will not make a pathway in the mind. To make
        a deep physical path, we walk again and again. To make a deep mental
        path, we must think over and over the kind of thoughts we wish to
        dominate our lives."

    );

    $people = array_keys($quotes);
    $chosenOne = $people[(date("d") % sizeof($people))];
    $display_quote = $_SESSION['display_quote'];

    return json_encode(array("quote" => $quotes[$chosenOne],
        "person" => $chosenOne, "display_quote" => $display_quote));
}


/* sets it so that user will not see the quote again during this session */
function closeAlert() {
    $_SESSION['display_quote'] = false;
}

/* returns JSON array containing the url of the display picture for this session
(if user has voted) or false (if user has not voted) */
function getPicUrl() {
    if ($_SESSION['display_pic']) {
        $display_pic = $_SESSION['display_pic'];
    } else {
        $display_pic = false;
    }
    return json_encode(array("display_pic" => $display_pic));
}

$cmd = $_POST['cmd'];
if ($cmd == 'logout') {
    logout();
} else if ($cmd == 'getGreeting') {
    echo getGreeting();
} else if ($cmd == 'getQuote') {
    echo getQuote();
} else if ($cmd == 'closeAlert') {
    closeAlert();
} else if ($cmd == 'getPicUrl') {
    echo getPicUrl();
}