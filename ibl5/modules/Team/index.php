<?php

use Player\Player;

if (!mb_eregi("modules.php", $_SERVER['PHP_SELF'])) {
    die("You can't access this file directly...");
}

$module_name = basename(dirname(__FILE__));
get_lang($module_name);

$pagetitle = "- Team Pages";

function team($teamID)
{
    global $db;
    $teamID = (int) $teamID; // Ensure teamID is an integer
    if ($teamID > 0) {
        $team = Team::initialize($db, $teamID);
    }
    $sharedFunctions = new Shared($db);
    $season = new Season($db);

    $yr = $_REQUEST['yr'];

    $display = $_REQUEST['display'];
    if ($display == null) {
        $display = "ratings";
    }

    Nuke\Header::header();
    OpenTable();


    $isFreeAgencyModuleActive = $sharedFunctions->isFreeAgencyModuleActive();

    if ($teamID == 0) { // Team 0 is the Free Agents; we want a query that will pick up all of their players.
        if ($isFreeAgencyModuleActive == 0) {
            $query = "SELECT * FROM ibl_plr WHERE ordinal > '959' AND retired = 0 ORDER BY ordinal ASC";
        } else {
            $query = "SELECT * FROM ibl_plr WHERE ordinal > '959' AND retired = 0 AND cyt != cy ORDER BY ordinal ASC";
        }
        $result = $db->sql_query($query);
    } else if ($teamID == "-1") { // SHOW ENTIRE LEAGUE
        $query = "SELECT * FROM ibl_plr WHERE retired = 0 AND name NOT LIKE '%Buyouts' ORDER BY ordinal ASC";
        $result = $db->sql_query($query);
    } else { // If not Free Agents, use the code below instead.
        if ($yr != "") {
            $query = "SELECT * FROM ibl_hist WHERE teamid = '$teamID' AND year = '$yr' ORDER BY name ASC";
            $result = $db->sql_query($query);
        } else if ($isFreeAgencyModuleActive == 1) {
            $result = $team->getFreeAgencyRosterOrderedByNameResult();
        } else {
            $result = $team->getRosterUnderContractOrderedByNameResult();
        }
    }

    echo "<table>
        <tr>
            <td align=center valign=top>";
            
    UI::displaytopmenu($db, $team->teamID);
            
    echo "<img src=\"./images/logo/$teamID.jpg\">";
            
    if ($yr != "") {
        echo "<center><h1>$yr $team->name</h1></center>";
        $insertyear = "&yr=$yr";
    } else {
        $insertyear = "";
    }

    $tabs = "";
    if ($display == "ratings") {
        $showing = "Player Ratings";
        $table_ratings = UI::ratings($db, $result, $team, $yr, $season);
        $table_output = $table_ratings;
        $tabs .= "<td bgcolor=#BBBBBB style=\"font-weight:bold\">";
    } else {
        $tabs .= "<td>";
    }
    $tabs .= "<a href=\"modules.php?name=Team&op=team&teamID=$teamID&display=ratings$insertyear\">Ratings</a></td>";

    if ($display == "total_s") {
        $showing = "Season Totals";
        $table_totals = UI::seasonTotals($db, $result, $team, $yr);
        $table_output = $table_totals;
        $tabs .= "<td bgcolor=#BBBBBB style=\"font-weight:bold\">";
    } else {
        $tabs .= "<td>";
    }
    $tabs .= "<a href=\"modules.php?name=Team&op=team&teamID=$teamID&display=total_s$insertyear\">Season Totals</a></td>";

    if ($display == "avg_s") {
        $showing = "Season Averages";
        $table_averages = UI::seasonAverages($db, $result, $team, $yr);
        $table_output = $table_averages;
        $tabs .= "<td bgcolor=#BBBBBB style=\"font-weight:bold\">";
    } else {
        $tabs .= "<td>";
    }
    $tabs .= "<a href=\"modules.php?name=Team&op=team&teamID=$teamID&display=avg_s$insertyear\">Season Averages</a></td>";

    if ($display == "per36mins") {
        $showing = "Per 36 Minutes";
        $table_per36Minutes = UI::per36Minutes($db, $result, $team, $yr);
        $table_output = $table_per36Minutes;
        $tabs .= "<td bgcolor=#BBBBBB style=\"font-weight:bold\">";
    } else {
        $tabs .= "<td>";
    }
    $tabs .= "<a href=\"modules.php?name=Team&op=team&teamID=$teamID&display=per36mins$insertyear\">Per 36 Minutes</a></td>";

    if ($display == "chunk") {
        $showing = "Chunk Averages";
        $table_periodAverages = UI::periodAverages($db, $team, $season);
        $table_output = $table_periodAverages;
        $tabs .= "<td bgcolor=#BBBBBB style=\"font-weight:bold\">";
    } else {
        $tabs .= "<td>";
    }
    $tabs .= "<a href=\"modules.php?name=Team&op=team&teamID=$teamID&display=chunk$insertyear\">Sim Averages</a></td>";

    if (
        $season->phase == "Playoffs"
        OR $season->phase == "Draft"
        OR $season->phase == "Free Agency"
    ) {
        if ($display == "playoffs") {
            $showing = "Playoff Averages";
            $table_periodAverages = UI::periodAverages($db, $team, $season, $season->playoffsStartDate, $season->playoffsEndDate);
            $table_output = $table_periodAverages;
            $tabs .= "<td bgcolor=#BBBBBB style=\"font-weight:bold\">";
        } else {
            $tabs .= "<td>";
        }
        $tabs .= "<a href=\"modules.php?name=Team&op=team&teamID=$teamID&display=playoffs$insertyear\">Playoffs Averages</a></td>";
    }

    if ($display == "contracts") {
        $showing = "Contracts";
        $table_contracts = UI::contracts($db, $result, $team, $sharedFunctions);
        $table_output = $table_contracts;
        $tabs .= "<td bgcolor=#BBBBBB style=\"font-weight:bold\">";
    } else {
        $tabs .= "<td>";
    }
    $tabs .= "<a href=\"modules.php?name=Team&op=team&teamID=$teamID&display=contracts$insertyear\">Contracts</a></td>";

    if ($teamID > 0 AND $yr == "") {
        $starters_table = lastSimsStarters($db, $result, $team);
    }

    $tableDraftPicks = $team ? UI\Modules\Team::draftPicks($db, $team) : "";

    $inforight = team_info_right($team);
    $team_info_right = $inforight[0];
    $rafters = $inforight[1];

    echo "
    <table align=center>
        <tr bgcolor=$team->color1>
            <td><font color=$team->color2><b><center>$showing (Sortable by clicking on Column Heading)</center></b></font></td>
        </tr>
		<tr>
            <td align=center><table><tr>$tabs</tr></table></td>
        </tr>
		<tr>
            <td align=center>$table_output</td>
        </tr>
		<tr>
            <td align=center>$starters_table</td>
        </tr>
		<tr bgcolor=$team->color1>
            <td><font color=$team->color2><b><center>Draft Picks</center></b></font></td>
        </tr>
		<tr>
            <td>$tableDraftPicks</td>
        </tr>
		<tr>
            <td>$rafters</td>
        </tr>
    </table>";

    // TRANSITIONS TO NEXT SIDE OF PAGE
    echo "</td><td valign=top>$team_info_right</td></tr></table>";

    CloseTable();
    Nuke\Footer::footer();
}

function lastSimsStarters($db, $result, $team)
{
    $num = $db->sql_numrows($result);
    $i = 0;
    while ($i < $num) {
        if ($db->sql_result($result, $i, "PGDepth") == 1) {
            $startingPG = $db->sql_result($result, $i, "name");
            $startingPGpid = $db->sql_result($result, $i, "pid");
        }
        if ($db->sql_result($result, $i, "SGDepth") == 1) {
            $startingSG = $db->sql_result($result, $i, "name");
            $startingSGpid = $db->sql_result($result, $i, "pid");
        }
        if ($db->sql_result($result, $i, "SFDepth") == 1) {
            $startingSF = $db->sql_result($result, $i, "name");
            $startingSFpid = $db->sql_result($result, $i, "pid");
        }
        if ($db->sql_result($result, $i, "PFDepth") == 1) {
            $startingPF = $db->sql_result($result, $i, "name");
            $startingPFpid = $db->sql_result($result, $i, "pid");
        }
        if ($db->sql_result($result, $i, "CDepth") == 1) {
            $startingC = $db->sql_result($result, $i, "name");
            $startingCpid = $db->sql_result($result, $i, "pid");
        }
        $i++;
    }

    $starters_table = "<table align=\"center\" border=1 cellpadding=1 cellspacing=1>
        <tr bgcolor=$team->color1>
            <td colspan=5><font color=$team->color2><center><b>Last Sim's Starters</b></center></font></td>
        </tr>
        <tr>
            <td><center><b>PG</b><br><img src=\"./images/player/$startingPGpid.jpg\" height=\"90\" width=\"65\"><br><a href=\"./modules.php?name=Player&pa=showpage&pid=$startingPGpid\">$startingPG</a></td>
            <td><center><b>SG</b><br><img src=\"./images/player/$startingSGpid.jpg\" height=\"90\" width=\"65\"><br><a href=\"./modules.php?name=Player&pa=showpage&pid=$startingSGpid\">$startingSG</a></td>
            <td><center><b>SF</b><br><img src=\"./images/player/$startingSFpid.jpg\" height=\"90\" width=\"65\"><br><a href=\"./modules.php?name=Player&pa=showpage&pid=$startingSFpid\">$startingSF</a></td>
            <td><center><b>PF</b><br><img src=\"./images/player/$startingPFpid.jpg\" height=\"90\" width=\"65\"><br><a href=\"./modules.php?name=Player&pa=showpage&pid=$startingPFpid\">$startingPF</a></td>
            <td><center><b>C</b><br><img src=\"./images/player/$startingCpid.jpg\" height=\"90\" width=\"65\"><br><a href=\"./modules.php?name=Player&pa=showpage&pid=$startingCpid\">$startingC</a></td>
        </tr>
    </table>";

    return $starters_table;
}

function team_info_right($team)
{
    global $db;

    $output = "<table bgcolor=#eeeeee width=220>";

    $output .= UI\Modules\Team::currentSeason($db, $team);
    $output .= UI\Modules\Team::gmHistory($db, $team);
    $ultimate_output[1] = UI\Modules\Team::championshipBanners($db, $team);
    $output .= UI\Modules\Team::teamAccomplishments($db, $team);
    $output .= UI\Modules\Team::resultsRegularSeason($db, $team);
    $output .= UI\Modules\Team::resultsHEAT($db, $team);
    $output .= UI\Modules\Team::resultsPlayoffs($db, $team);

    $output .= "</table>";

    $ultimate_output[0] = $output;

    return $ultimate_output;
}

function viewinjuries($teamID)
{
    global $db;
    $teamID = (int) $teamID; // Ensure teamID is an integer
    $league = new League($db);

    Nuke\Header::header();
    OpenTable();

    UI::displaytopmenu($db, $teamID);

    echo "<center><h2>INJURED PLAYERS</h2></center>
		<table>
            <tr>
                <td valign=top>
		            <table class=\"sortable\">
		                <tr>
                            <th>Pos</th>
                            <th>Player</th>
                            <th>Team</th>
                            <th>Days Injured</th>
                        </tr>";

    $i = 0;
    foreach ($league->getInjuredPlayersResult() as $injuredPlayer) {
        $player = Player::withPlrRow($db, $injuredPlayer);
        $team = Team::initialize($db, $player->teamID);

        (($i % 2) == 0) ? $bgcolor = "FFFFFF" : $bgcolor = "DDDDDD";

        echo "<tr bgcolor=$bgcolor>
            <td>$player->position</td>
            <td><a href=\"./modules.php?name=Player&pa=showpage&pid=$player->playerID\">$player->name</a></td>
            <td bgcolor=\"#$team->color1\">
                <font color=\"#$team->color2\"><a href=\"./modules.php?name=Team&op=team&teamID=$player->teamID\">$team->city $player->teamName</a></font>
            </td>
            <td>$player->daysRemainingForInjury</td>
        </tr>";

        $i++;
    }

    echo "</table></table>";

    CloseTable();
    Nuke\Footer::footer();
}

function drafthistory($teamID)
{
    global $db;
    $teamID = (int) $teamID; // Ensure teamID is an integer

    Nuke\Header::header();
    OpenTable();
    UI::displaytopmenu($db, $teamID);

    $team = Team::initialize($db, $teamID);

    echo "$team->name Draft History
        <table class=\"sortable\">
            <tr>
                <th>Player</th>
                <th>Pos</th>
                <th>Year</th>
                <th>Round</th>
                <th>Pick</th>
            </tr>";

    foreach ($team->getDraftHistoryResult() as $playerRow) {
        $player = Player::withPlrRow($db, $playerRow);

        echo "<tr>";

        if ($player->isRetired) {
            echo "<td><a href=\"./modules.php?name=Player&pa=showpage&pid=$player->playerID\">$player->name</a> (retired)</td>";
        } else {
            echo "<td><a href=\"./modules.php?name=Player&pa=showpage&pid=$player->playerID\">$player->name</a></td>";
        }

        echo "
            <td>$player->position</td>
            <td>$player->draftYear</td>
            <td>$player->draftRound</td>
            <td>$player->draftPickNumber</td>
        </tr>";
    }

    echo "</table>";

    CloseTable();
    Nuke\Footer::footer();
}

function menu()
{
    global $db;

    Nuke\Header::header();
    OpenTable();

    UI::displaytopmenu($db, 0);

    CloseTable();
    Nuke\Footer::footer();
}

$teamID = isset($teamID) ? (int) $teamID : 0;

switch ($op) {
    case "team":
        team($teamID);
        break;

    case "injuries":
        viewinjuries($teamID);
        break;

    case "drafthistory":
        drafthistory($teamID);
        break;

    default:
        menu();
        break;
}
