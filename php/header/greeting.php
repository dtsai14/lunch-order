<?php include_once '../authenticate.php';

date_default_timezone_set('America/Los_Angeles');
$hour = date("G");
$minute = date("i");
$name = $_SESSION['first_name'];
$displayQuote = $_SESSION['displayQuote'];

if ($hour < 5) {
    $greeting = "It's too early to be up... =_=";
} elseif ($hour < 12) {
    $greeting = "Good Morning, $name!";
} elseif ($hour == 12) {
    if ($minute == 0) {
        $greeting = "Good Noon, $name! ~(^_^)/ (￣ー￣) ( ^_^）o自自o（^_^ ） ";
    } else {
        $greeting = "Time for lunch!";
    }
} elseif ($hour < 19) {
    $greeting = "Good Afternoon, $name!";
} elseif ($hour < 22) {
    $greeting = "Good Night, $name!";
} else {
    $greeting = "Time to go to sleep...ZzzZzzZzz";
}

$quotes = array(
    "Gandhi"=>"Strength does not come from physical capacity. It comes from an indomitable will.",
    "Amelia Earhart"=>"Everyone has oceans to fly, if they have the heart to do it. Is it reckless? Maybe. But what do dreams know of boundaries?",
    "Baruch Spinoza"=>"I have made a ceaseless effort not to ridicule, not to bewail, not to scorn human actions, but to understand them.",
    "Beverly Sills"=>"You may be disappointed if you fail, but you are doomed if you don't try.",
    "Amelia Earhart"=>"The most difficult thing is the decision to act, the rest is merely tenacity. The fears are paper tigers. You can do anything you decide to do. You can act to change and control your life; and the procedure, the process is its own reward.",
    "Albert Einstein"=>"Try not to become a man of success but rather try to become a man of value.",
    "Buddha"=>"In compassion lies the world's true strength",
    "Helen Keller"=>"When one door closes another opens. But often we look so long so regretfully upon the closed door that we fail to see the one that has opened for us.",
    "Mohandas Gandhi"=>"Always aim at complete harmony of thought and word and deed. Always aim at purifying your thoughts and everything will be well.",
    "Mark Twain"=>"Twenty years from now you will be more disappointed by the things that you didn't do than by the ones you did do, so throw off the bowlines, sail away from the safe harbor, catch the trade winds in your sails. Explore, Dream, Discover.",
    "Chinese Proverb"=>"The best time to plant a tree was 20 years ago. The second best time is now.",
    "Zig Ziglar"=>"People often say that motivation doesn't last. Well, neither does bathing. That's why we recommend it daily.",
    "Ancient Indian Proverb"=>"Certain things catch your eye, but pursue only those that capture the heart."
);

$people = array_keys($quotes);
$chosenOne = $people[(date("d") % sizeof($people))];


echo json_encode(array("greeting" => $greeting, "quote" => $quotes[$chosenOne], "person" => $chosenOne, "displayQuote" => $displayQuote));
