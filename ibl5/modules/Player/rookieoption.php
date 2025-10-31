<?php

use Player\Player;

require $_SERVER['DOCUMENT_ROOT'] . '/ibl5/mainfile.php';
$sharedFunctions = new Shared($db);
$season = new Season($db);

$Team_Name = $_POST['teamname'];
$player = Player::withPlayerID($db, $_POST['playerID']);
$ExtensionAmount = $_POST['rookieOptionValue'];

$teamID = $sharedFunctions->getTidFromTeamname($Team_Name); // This function now returns an integer

$recipient = 'ibldepthcharts@gmail.com';
$emailsubject = "Rookie Extension Option - " . $player->name;
$filetext = $Team_Name . " exercise the rookie extension option on " . $player->name . " in the amount of " . $ExtensionAmount . ".";

if ($player->draftRound == 1 AND $player->canRookieOption($season->phase)) {
    $queryrookieoption = "UPDATE ibl_plr SET cy4 = '$ExtensionAmount' WHERE name = '$player->name'";
} elseif ($player->draftRound == 2 AND $player->canRookieOption($season->phase)) {
    $queryrookieoption = "UPDATE ibl_plr SET cy3 = '$ExtensionAmount' WHERE name = '$player->name'";
} else {
    die("This player's experience doesn't match their rookie status; please let the commish know about this error.");
}

$resultrookieoption = $db->sql_query($queryrookieoption);

echo "<html><head><title>Rookie Option Page</title></head><body>

Your rookie option has been updated in the database and should reflect on your team pages immediately.<br>";

if ($season->phase == "Free Agency") {
    echo "Please <a href=\"/ibl5/modules.php?name=Free_Agency\">click here to return to the Free Agency Screen</a>.";
} else {
    echo "Please <a href=\"/ibl5/modules.php?name=Team&op=team&teamID=$teamID\">click here to return to your team page</a>.";
}

Discord::postToChannel('#rookie-options', $filetext);

if (mail($recipient, $emailsubject, $filetext, "From: rookieoption@iblhoops.net")) {
    $rookieOptionInMillions = $ExtensionAmount / 100;
    $timestamp = date('Y-m-d H:i:s', time());
    $storytitle = $player->name . " extends their contract with the " . $Team_Name;
    $hometext = $Team_Name . " exercise the rookie extension option on " . $player->name . " in the amount of " . $rookieOptionInMillions . " million dollars.";

    $querytopic = "SELECT * FROM nuke_topics WHERE topicname = '$Team_Name'";
    $resulttopic = $db->sql_query($querytopic);
    $topicid = $db->sql_result($resulttopic, 0, "topicid");

    $querycat = "SELECT * FROM nuke_stories_cat WHERE title = 'Rookie Extension'";
    $resultcat = $db->sql_query($querycat);
    $RookieExtensions = $db->sql_result($resultcat, 0, "counter");
    $catid = $db->sql_result($resultcat, 0, "catid");

    $querycat2 = "UPDATE nuke_stories_cat SET counter = $RookieExtensions WHERE title = 'Rookie Extension'";
    $resultcat2 = $db->sql_query($querycat2);

    $querystor = "INSERT INTO nuke_stories
            (catid,
             aid,
             title,
             time,
             hometext,
             topic,
             informant,
             counter,
             alanguage)
VALUES      ('$catid',
             'Associated Press',
             '$storytitle',
             '$timestamp',
             '$hometext',
             '$topicid',
             'Associated Press',
             '0',
             'english')";
    $resultstor = $db->sql_query($querystor);

    echo "<center>An e-mail regarding this extension has been successfully sent to the commissioner's office. Thank you.</center>";
} else {
    echo "Message failed to e-mail properly; please notify the commissioner of the error.</center>";
}
