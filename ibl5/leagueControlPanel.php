<?php

require $_SERVER['DOCUMENT_ROOT'] . '/ibl5/mainfile.php';
$season = new Season($db);

$queryString = "";
$successText = "";

if (isset($_POST['query'])) {
    switch ($_POST['query']) {
        case 'Activate Player and Season Leaders modules after Trivia':
            $queryString = "UPDATE nuke_modules SET active = 1 WHERE title = 'Player' OR title = 'Season_Leaders';";
            $successText = "Player and Season Leaders modules have been activated.";
            break;
        case 'Deactivate Player and Season Leaders modules for Trivia':
            $queryString = "UPDATE nuke_modules SET active = 0 WHERE title = 'Player' OR title = 'Season_Leaders';";
            $successText = "Player and Season Leaders modules have been deactivated.";
            break;
        case 'Insert new `ibl_heat_win_loss` database entries':
            $currentSeasonHEATYear = $season->beginningYear;
            $queryHEATEntriesAlreadyExist = "SELECT currentname FROM ibl_heat_win_loss WHERE year = $currentSeasonHEATYear;";
            $resultHEATEntriesAlreadyExist = $db->sql_query($queryHEATEntriesAlreadyExist);

            if ($db->sql_numrows($resultHEATEntriesAlreadyExist) == 0) {
                $queryTeamNames = "SELECT team_name FROM ibl_team_info WHERE teamid != " . League::FREE_AGENTS_TEAMID . " ORDER BY teamid ASC;";
                $resultTeamNames = $db->sql_query($queryTeamNames);
                $numTeamNames = $db->sql_numrows($resultTeamNames);
    
                $i = 0;
                while ($i < $numTeamNames) {
                    $values .= "($currentSeasonHEATYear, '" . $db->sql_result($resultTeamNames, $i, 'team_name') . "', '" . $db->sql_result($resultTeamNames, $i, 'team_name') . "', 0, 0)";
                    if ($i < $numTeamNames - 1) {
                        $values .= ", ";
                    }
                    $i++;
                }
    
                $queryString = "INSERT INTO ibl_heat_win_loss (`year`, `currentname`, `namethatyear`, `wins`, `losses`) VALUES $values;";
                $successText = "New `ibl_heat_win_loss` database entries were inserted for each team for the $currentSeasonHEATYear season.";
            } else {
                $failureText = "`ibl_heat_win_loss` database entries already exist for the $currentSeasonHEATYear season! New entries were NOT inserted.";
            }
            break;
        case 'Reset All Contract Extensions':
            $queryString = "UPDATE ibl_team_info SET Used_Extension_This_Season = 0;";
            $successText = "All teams' contract extensions have been reset.";
            break;
        case 'Reset All MLEs/LLEs':
            $queryString = "UPDATE ibl_team_info SET HasMLE = 1, HasLLE = 1;";
            $successText = "All teams' MLEs and LLEs have been reset.";
            break;
        case 'Reset All-Star Voting':
            $multiQueryString = "UPDATE ibl_votes_ASG 
                SET East_F1 = NULL, East_F2 = NULL, East_F3 = NULL, East_F4 = NULL,
                    West_F1 = NULL, West_F2 = NULL, West_F3 = NULL, West_F4 = NULL,
                    East_B1 = NULL, East_B2 = NULL, East_B3 = NULL, East_B4 = NULL,
                    West_B1 = NULL, West_B2 = NULL, West_B3 = NULL, West_B4 = NULL;
                UPDATE ibl_settings SET value = 'Yes' where name = 'ASG Voting';
                UPDATE ibl_team_history SET asg_vote = 'No Vote';";
            $successText = "ASG Voting has been reset!";
            break;
        case 'Reset End of the Year Voting':
            $multiQueryString = "UPDATE ibl_votes_EOY
                SET MVP_1 = NULL, MVP_2 = NULL, MVP_3 = NULL,
                    Six_1 = NULL, Six_2 = NULL, Six_3 = NULL,
                    ROY_1 = NULL, ROY_2 = NULL, ROY_3 = NULL,
                    GM_1 = NULL, GM_2 = NULL, GM_3 = NULL;
                UPDATE ibl_settings SET value = 'Yes' where name = 'EOY Voting';
                UPDATE ibl_team_history SET eoy_vote = 'No Vote';";
            $successText = "EOY Voting has been reset!";
            break;
        case 'Set all players on waivers to Free Agents and reset their Bird years':
            $queryString = "UPDATE ibl_plr SET teamname = 'Free Agents', bird = 0 WHERE retired != 1 AND ordinal > " . JSB::WAIVERS_ORDINAL . ";";
            $successText = "All players currently on waivers have their teamname set to Free Agents and 0 Bird years.";
            break;
        case 'Set Allow Trades Status':
            if (isset($_POST['Trades'])) {
                $queryString = "UPDATE ibl_settings SET value = '{$_POST['Trades']}' WHERE name = 'Allow Trades';";
            }
            $successText = "Allow Trades Status has been set to {$_POST['Trades']}.";
            break;
        case 'Set Free Agency factors for PFW':
            if ($season->phase == 'Draft' or $season->phase == 'Free Agency') {
                $queryString = "UPDATE ibl_team_info info, ibl_power power
                    SET Contract_Wins = power.win,
                        Contract_Losses = power.loss
                    WHERE power.TeamID = info.teamid;";
                $successText = "The columns that affect each team's Play For Winner demand factor have been updated to match this past season's ($season->endingYear) win/loss records.";
            } else {
                $failureText = "Sorry, that button can only be used during the Draft or Free Agency.<br>
                    The FA demands formula requires the current season to be finished before calculating factors.";
            }
            break;
        case 'Set Season Phase':
            if (isset($_POST['SeasonPhase'])) {
                $queryString = "UPDATE ibl_settings SET value = '{$_POST['SeasonPhase']}' WHERE name = 'Current Season Phase';";
            }
            $successText = "Season Phase has been set to {$_POST['SeasonPhase']}.";
            break;
        case 'Set Sim Length in Days':
            if (isset($_POST['SimLengthInDays'])) {
                $queryString = "UPDATE ibl_settings SET value = '{$_POST['SimLengthInDays']}' WHERE name = 'Sim Length in Days';";
            }
            $successText = "Sim Length in Days has been set to {$_POST['SimLengthInDays']}.";
            break;
        case 'Set Waiver Wire Status':
            if (isset($_POST['Waivers'])) {
                $queryString = "UPDATE ibl_settings SET value = '{$_POST['Waivers']}' WHERE name = 'Allow Waiver Moves';";
            }
            $successText = "Waiver Wire Status has been set to {$_POST['Waivers']}.";
            break;
        case 'Toggle Free Agency Notifications':
            if (isset($_POST['FANotifs'])) {
                $queryString = "UPDATE ibl_settings SET value = '{$_POST['FANotifs']}' WHERE name = 'Free Agency Notifications';";
            }
            $successText = "Free Agency Notifications are now {$_POST['FANotifs']}.";
            break;
    }

    if ($queryString != NULL) {
        if ($db->sql_query($queryString)) {
            $querySuccessful = true;
            if (isset($_POST['SeasonPhase'])) {
                $season->phase = $_POST['SeasonPhase'];
            }
            if (isset($_POST['Waivers'])) {
                $season->allowWaivers = $_POST['Waivers'];
            }
            if (isset($_POST['Trades'])) {
                $season->allowTrades = $_POST['Trades'];
            }
            if (isset($_POST['FANotifs'])) {
                $season->freeAgencyNotificationsState = $_POST['FANotifs'];
                Discord::postToChannel('#free-agency', $successText);
            }
        } else {
            $querySuccessful = false;
        }
    } elseif ($multiQueryString != NULL) {
        if (mysqli_multi_query($db->db_connect_id, $multiQueryString)) {
            $querySuccessful = true;
        } else {
            $querySuccessful = false;
        }
    }
}

echo "
<HTML>
<HEAD>
    <TITLE>IBLv5 Control Panel</TITLE>
</HEAD>
<BODY>";

echo "<FORM action=\"leagueControlPanel.php\" method=\"POST\">
    <select name=\"SeasonPhase\">
        <option value = \"Preseason\"" . ($season->phase == "Preseason" ? " SELECTED" : "") . ">Preseason</option>
        <option value = \"HEAT\"" . ($season->phase == "HEAT" ? " SELECTED" : "") . ">HEAT</option>
        <option value = \"Regular Season\"" . ($season->phase == "Regular Season" ? " SELECTED" : "") . ">Regular Season</option>
        <option value = \"Playoffs\"" . ($season->phase == "Playoffs" ? " SELECTED" : "") . ">Playoffs</option>
        <option value = \"Draft\"" . ($season->phase == "Draft" ? " SELECTED" : "") . ">Draft</option>
        <option value = \"Free Agency\"" . ($season->phase == "Free Agency" ? " SELECTED" : "") . ">Free Agency</option>
    </select>
    <INPUT type='submit' name='query' value='Set Season Phase'><p>
    <A HREF=\"/ibl5/pages/seasonHighs.php\">Season Highs</A><p>";

switch ($season->phase) {
    case 'Preseason':
        echo "<A HREF=\"updateAllTheThings.php\">Update All The Things</A><p>
            <A HREF=\"scoParser.php\">Run scoParser.php</A><p>
            <select name=\"Waivers\">
                <option value = \"Yes\"" . ($season->allowWaivers == "Yes" ? " SELECTED" : "") . ">Yes</option>
                <option value = \"No\"" . ($season->allowWaivers == "No" ? " SELECTED" : "") . ">No</option>
            </select>
            <INPUT type='submit' name='query' value='Set Waiver Wire Status'><p>
            <INPUT type='submit' name='query' value='Set all players on waivers to Free Agents and reset their Bird years'><p>";
        break;
    case 'HEAT':
        echo "<A HREF=\"updateAllTheThings.php\">Update All The Things</A><p>
            <A HREF=\"scoParser.php\">Run scoParser.php</A><p>
            <A HREF=\"heatupdateboth.php\">Update HEAT Leaderboards</A><p>
            <A HREF=\"history_update.php\">IBL History Update</A><p>
            <INPUT type='submit' name='query' value='Insert new `ibl_heat_win_loss` database entries'><p>";
        break;
    case 'Regular Season':
        echo "<A HREF=\"plrParser.php\">Run plrParser.php</A>
                <br><b>(but make sure you've uploaded the updated PLR file before you run this!)</b><p>
            <A HREF=\"updateAllTheThings.php\">Update All The Things</A><p>
            <A HREF=\"scoParser.php\">Run scoParser.php</A><p>
            <INPUT type='number' name='SimLengthInDays' min=1 max=180 size=3 value='" . League::getSimLengthInDays($db) . "'>
            <INPUT type='submit' name='query' value='Set Sim Length in Days'> <i>
                <br>(you HAVE to CLICK to set the days – you unfortunately can't just hit Return/Enter)<p>
            <INPUT type='submit' name='query' value='Reset All-Star Voting'><p>
            <INPUT type='submit' name='query' value='Reset End of the Year Voting'><p>
            <select name=\"Trades\">
                <option value = \"Yes\"" . ($season->allowTrades == "Yes" ? " SELECTED" : "") . ">Yes</option>
                <option value = \"No\"" . ($season->allowTrades == "No" ? " SELECTED" : "") . ">No</option>
            </select>
            <INPUT type='submit' name='query' value='Set Allow Trades Status'><p>";
        break;
    case 'Playoffs':
        echo "<A HREF=\"updateAllTheThings.php\">Update All The Things</A><p>
            <A HREF=\"scoParser.php\">Run scoParser.php</A><p>
            <INPUT type='submit' name='query' value='Reset End of the Year Voting'><p>
            <select name=\"Trades\">
                <option value = \"Yes\"" . ($season->allowTrades == "Yes" ? " SELECTED" : "") . ">Yes</option>
                <option value = \"No\"" . ($season->allowTrades == "No" ? " SELECTED" : "") . ">No</option>
            </select>
            <INPUT type='submit' name='query' value='Set Allow Trades Status'><p>";
        break;
    case 'Draft':
        echo "<A HREF=\"playoffupdate.php\">Playoff Leaderboard Update #1</A><p>
            <A HREF=\"playofflbupdate.php\">Playoff Leaderboard Update #2</A><p>
            <A HREF=\"seasonlbupdate.php\">Season Leaderboard Update</A><p>
            <A HREF=\"history_update.php\">IBL History Update</A><p>";
        break;
    case 'Free Agency':
        echo "<INPUT type='submit' name='query' value='Reset All Contract Extensions'><p>
            <INPUT type='submit' name='query' value='Reset All MLEs/LLEs'><p>
            <INPUT type='submit' name='query' value='Set Free Agency factors for PFW'><p>
            <A HREF=\"tradition.php\">Set Free Agency factors for Tradition</A><p>
            <select name=\"FANotifs\">
                <option value = \"On\"" . ($season->freeAgencyNotificationsState == "On" ? " SELECTED" : "") . ">On</option>
                <option value = \"Off\"" . ($season->freeAgencyNotificationsState == "Off" ? " SELECTED" : "") . ">Off</option>
            </select>
            <INPUT type='submit' name='query' value='Toggle Free Agency Notifications'><p>
            <select name=\"Waivers\">
                <option value = \"Yes\"" . ($season->allowWaivers == "Yes" ? " SELECTED" : "") . ">Yes</option>
                <option value = \"No\"" . ($season->allowWaivers == "No" ? " SELECTED" : "") . ">No</option>
            </select>
            <INPUT type='submit' name='query' value='Set Waiver Wire Status'><p>
            <INPUT type='submit' name='query' value='Set all players on waivers to Free Agents and reset their Bird years'><p>";
        break;
}

echo "<INPUT type='submit' name='query' value='Deactivate Player and Season Leaders modules for Trivia'><p>
    <INPUT type='submit' name='query' value='Activate Player and Season Leaders modules after Trivia'><p>
    </FORM><p><hr><p>";

if ($querySuccessful == true) {
    $queryString = $queryString ?: $multiQueryString;
    echo "<code>" . $queryString . "</code>";
    echo "<p>";
    echo "<b>" . $successText . "</b>";
} elseif (!isset($_POST['query'])) {
    // Do/display nothing on an initial page load
} else {
    echo "Oops, something went wrong:<p>
    <FONT color=red>$failureText</FONT><p>
    Let A-Jay know what you were trying to do and he'll look into it.";
}

echo "
</BODY>
</HTML>";
