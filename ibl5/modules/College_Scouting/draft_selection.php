<?php

require $_SERVER['DOCUMENT_ROOT'] . '/ibl5/mainfile.php';

global $db;

$teamname = $_POST['teamname'];
$playerToBeDrafted = $_POST['player'];
$draft_round = $_POST['draft_round'];
$draft_pick = $_POST['draft_pick'];
$date = date('Y-m-d h:i:s');

$sharedFunctions = new Shared($db);
$season = new Season($db);

$queryCurrentDraftSelection = "SELECT `player`
    FROM ibl_draft
    WHERE `round` = '$draft_round' 
       AND `pick` = '$draft_pick';";
$resultCurrentDraftSelection = $db->sql_query($queryCurrentDraftSelection);
$currentDraftSelection = $db->sql_result($resultCurrentDraftSelection, 0, 'player');

if (($currentDraftSelection == NULL OR $currentDraftSelection == "") AND $playerToBeDrafted != NULL) {
    // NOTE: $queryUpdateDraftTable and $queryUpdateRookieTable are formatted with single quotes to allow for apostrophes in player names.
    $queryUpdateDraftTable = 'UPDATE ibl_draft 
         SET `player` = "' . $playerToBeDrafted . '", 
               `date` = "' . $date . '" 
        WHERE `round` = "' . $draft_round . '" 
           AND `pick` = "' . $draft_pick . '"';
    $resultUpdateDraftTable = $db->sql_query($queryUpdateDraftTable);
    
    $queryUpdateRookieTable = 'UPDATE `ibl_draft_class`
          SET `team` = "' . $teamname . '", 
           `drafted` = "1"
        WHERE `name` = "' . $playerToBeDrafted . '"';
    $resultUpdateRookieTable = $db->sql_query($queryUpdateRookieTable);

    if ($resultUpdateDraftTable AND $resultUpdateRookieTable) {
        $message = "With pick #$draft_pick in round $draft_round of the $season->endingYear IBL Draft, the **" . $teamname . "** select **" . $playerToBeDrafted . "!**";
        echo "$message<p>
        <a href=\"/ibl5/modules.php?name=College_Scouting\">Go back to the Draft module</a>";
    
        $queryNextTeamDraftPick = "SELECT team from ibl_draft WHERE player = '' ORDER BY round ASC, pick ASC LIMIT 1";
        $resultNextTeamDraftPick = $db->sql_query($queryNextTeamDraftPick);
        $nextTeamDraftPick = $db->sql_result($resultNextTeamDraftPick, 0, 'team');

        $teamOnTheClock = $sharedFunctions->getCurrentOwnerOfDraftPick($season->endingYear, $draft_round, $nextTeamDraftPick);

        if ($teamOnTheClock != NULL) {
            $queryDiscordIDOfTeamOnTheClock = "SELECT discordID from ibl_team_info WHERE team_name = '$teamOnTheClock' LIMIT 1;";
            $resultDiscordIDOfTeamOnTheClock = $db->sql_query($queryDiscordIDOfTeamOnTheClock);
            $discordIDOfTeamOnTheClock = $db->sql_result($resultDiscordIDOfTeamOnTheClock, 0, 'discordID');

            Discord::postToChannel('#general-chat', $message);
            $message .= '
    **<@!' . $discordIDOfTeamOnTheClock . '>** is on the clock!
https://www.iblhoops.net/ibl5/modules.php?name=College_Scouting';
        } else {
            $message .= "
    **🏁 __The $season->endingYear IBL Draft has officially concluded!__ 🏁**";
            Discord::postToChannel('#general-chat', $message);
        }

        Discord::postToChannel('#draft-picks', $message);
    } else {
        echo "Oops, something went wrong, and at least one of the draft database tables wasn't updated.<p>
            Let A-Jay know what happened and he'll look into it.<p>
            
            <a href=\"/ibl5/modules.php?name=College_Scouting\">Go back to the Draft module</a>";
    }
} elseif ($playerToBeDrafted == NULL) {
    echo "Oops, you didn't select a player.<p>
        <a href=\"/ibl5/modules.php?name=College_Scouting\">Click here to return to the Draft module</a> and please select a player before hitting the Draft button.";
} elseif ($currentDraftSelection != NULL) {
    echo "Oops, it looks like you've already drafted a player with this draft pick.<p>
        <a href=\"/ibl5/modules.php?name=College_Scouting\">Click here to return to the Draft module</a> and if it's your turn, try drafting again.";
}