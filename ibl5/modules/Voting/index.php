
<?php

use Player\Player;

/************************************************************************/
/* PHP-NUKE: Web Portal System                                          */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2002 by Francisco Burzi                                */
/* http://phpnuke.org                                                   */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/*                                                                      */
/* ibl College Scout Module added by Spencer Cooley                    */
/* 2/2/2005                                                             */
/*                                                                      */
/************************************************************************/

if (!mb_eregi("modules.php", $_SERVER['PHP_SELF'])) {
    die("You can't access this file directly...");
}

$module_name = basename(dirname(__FILE__));
get_lang($module_name);

if (!function_exists('str_contains')) {
    function str_contains($haystack, $needle)
    {
        return $needle !== '' && mb_strpos($haystack, $needle) !== false;
    }
}

function userinfo($username)
{
    global $prefix, $db;
    $sharedFunctions = new Shared($db);
    $season = new Season($db);

    $sql2 = "SELECT * FROM " . $prefix . "_users WHERE username = '$username'";
    $result2 = $db->sql_query($sql2);
    $userinfo = $db->sql_fetchrow($result2);

    Nuke\Header::header();

// === CODE TO INSERT IBL DEPTH CHART ===

    OpenTable();

    function getCandidates($votingCategory, $voterTeamName)
    {
        global $db;
        $league = new League($db);
        $season = new Season($db);

        if ($season->phase == "Regular Season") {
            $result = $league->getAllStarCandidatesResult($votingCategory);
        } else {
            if (strpos($votingCategory, 'MVP') !== false) {
                $result = $league->getMVPCandidatesResult();
            } elseif (strpos($votingCategory, 'Six') !== false) {
                $result = $league->getSixthPersonOfTheYearCandidatesResult();
            } elseif (strpos($votingCategory, 'ROY') !== false) {
                $result = $league->getRookieOfTheYearCandidatesResult();
            } elseif (strpos($votingCategory, 'GM') !== false) {
                $result = $league->getGMOfTheYearCandidatesResult();
            }
        }

        echo "<SCRIPT>
			function ShowAndHide$votingCategory() {
				var x = document.getElementById('$votingCategory');
				if (x.style.display == 'none') {
					x.style.display = 'block';
				} else {
					x.style.display = 'none';
				}
			}
		</SCRIPT>";

        $output = "<table id=\"$votingCategory\" style=\"display:none\" class=\"sortable\">
			<tbody>
				<tr>";

        if ($season->phase == "Regular Season") {
            $output .= "<th>Vote</th>";
        } else {
            $output .= "<th>1st</th>
						<th>2nd</th>
						<th>3rd</th>";
        }

        if ($votingCategory != "GM") {
            $output .= "<th>Name</th>
						<th>gm</th>
						<th>gs</th>
						<th>min</th>
						<th>fgm</th>
						<th>fga</th>
						<th>fgp</th>
						<th>ftm</th>
						<th>fta</th>
						<th>ftp</th>
						<th>3gm</th>
						<th>3ga</th>
						<th>3gp</th>
						<th>orb</th>
						<th>drb</th>
						<th>reb</th>
						<th>ast</th>
						<th>stl</th>
						<th>to</th>
						<th>blk</th>
						<th>pf</th>
						<th>pts</th>
						<th>2x2</th>
						<th>3x3</th>
					</tr>";
        } else {
            $output .= "<th>Name</th>
						<th>Team</th>
					</tr>";
        }

        $i = 0;
        while ($row = $db->sql_fetch_assoc($result)) {
            if ($votingCategory != "GM") {
                $player = Player::withPlrRow($db, $row);
                $playerStats = PlayerStats::withPlrRow($db, $row);
                $teamname = $player->teamName;
            } else {
                $name = $row['owner_name'];
                $teamname = $row['team_city'] . " " . $row['team_name'];
            }

            (($i % 2) == 0) ? $bgcolor = "FFFFFF" : $bgcolor = "EEEEEE";

            $output .= "<tr bgcolor=$bgcolor>";

            if (!str_contains($teamname, $voterTeamName)) {
                if ($season->phase == "Regular Season") {
                    $output .= "<td><center><input type=\"checkbox\" name=\"" . $votingCategory . "[]\" value=\"$player->name, $player->teamName\"></center></td>";
                } else {
                    if ($votingCategory == "GM") {
                        $output .= "<td><center><input type=\"radio\" name=\"" . $votingCategory . "[1]\" value=\"$name, $teamname\"></center></td>
                                    <td><center><input type=\"radio\" name=\"" . $votingCategory . "[2]\" value=\"$name, $teamname\"></center></td>
                                    <td><center><input type=\"radio\" name=\"" . $votingCategory . "[3]\" value=\"$name, $teamname\"></center></td>";
                    } else {
                        $output .= "<td><center><input type=\"radio\" name=\"" . $votingCategory . "[1]\" value=\"$player->name, $player->teamName\"></center></td>
                                    <td><center><input type=\"radio\" name=\"" . $votingCategory . "[2]\" value=\"$player->name, $player->teamName\"></center></td>
                                    <td><center><input type=\"radio\" name=\"" . $votingCategory . "[3]\" value=\"$player->name, $player->teamName\"></center></td>";
                    }
                }
            } else {
                if ($season->phase == "Regular Season") {
                    $output .= "<td></td>";
                } else {
                    $output .= "<td></td>
								<td></td>
								<td></td>";
                }
            }

            if ($votingCategory != "GM") {
                $output .= "<td>$player->name, $player->teamName</td>
							<td>$playerStats->seasonGamesPlayed</td>
							<td>$playerStats->seasonGamesStarted</td>
							<td>$playerStats->seasonMinutesPerGame</td>
							<td>$playerStats->seasonFieldGoalsMadePerGame</td>
							<td>$playerStats->seasonFieldGoalsAttemptedPerGame</td>
							<td>$playerStats->seasonFieldGoalPercentage</td>
							<td>$playerStats->seasonFreeThrowsMadePerGame</td>
							<td>$playerStats->seasonFreeThrowsAttemptedPerGame</td>
							<td>$playerStats->seasonFreeThrowPercentage</td>
							<td>$playerStats->seasonThreePointersMadePerGame</td>
							<td>$playerStats->seasonThreePointersAttemptedPerGame</td>
							<td>$playerStats->seasonThreePointPercentage</td>
							<td>$playerStats->seasonOffensiveReboundsPerGame</td>
							<td>$playerStats->seasonDefensiveReboundsPerGame</td>
							<td>$playerStats->seasonTotalReboundsPerGame</td>
							<td>$playerStats->seasonAssistsPerGame</td>
							<td>$playerStats->seasonStealsPerGame</td>
							<td>$playerStats->seasonTurnoversPerGame</td>
							<td>$playerStats->seasonBlocksPerGame</td>
							<td>$playerStats->seasonPersonalFoulsPerGame</td>
							<td>$playerStats->seasonPointsPerGame</td>
							<td>$playerStats->seasonDoubleDoubles</td>
							<td>$playerStats->seasonTripleDoubles</td>
						</tr>";
            } else {
                $output .= "<td>$name</td>
							<td>$teamname</td>";
            }

            $i++;
        }

        $output .= "</tbody>
			</table><br><br>";

        return $output;
    }

    $formName = ($season->phase == "Regular Season") ? "ASGVote" : "EOYVote";
    $voterTeamName = $userinfo['user_ibl_team'];
    $tid = $sharedFunctions->getTidFromTeamname($voterTeamName);

    $formAction = ($season->phase == "Regular Season") ? "modules/Voting/ASGVote.php" : "modules/Voting/EOYVote.php";
    echo "<form name=\"$formName\" method=\"post\" action=\"$formAction\">
		<center>
			<img src=\"images/logo/$tid.jpg\"><br><br>";

    echo "<input type=\"submit\" value=\"Submit Votes!\">";

    $easternConferenceFrontcourt = $easternConferenceBackcourt = "";
    $westernConferenceFrontcourt = $westernConferenceBackcourt = "";
    $mostValuablePlayers = $sixthPersons = $rookiesOfTheYear = $generalManagers = "";

    if ($season->phase == "Regular Season") {
        $easternConferenceFrontcourt .= getCandidates('ECF', $voterTeamName);
        $easternConferenceBackcourt .= getCandidates('ECB', $voterTeamName);

        $westernConferenceFrontcourt .= getCandidates('WCF', $voterTeamName);
        $westernConferenceBackcourt .= getCandidates('WCB', $voterTeamName);

        echo "<div onclick=\"ShowAndHideECF()\">
				<h2>Select FOUR Eastern Conference Frontcourt Players:</h2>
				<i>Tap/click here to reveal/hide nominees</i>
			</div>
			$easternConferenceFrontcourt

			<div onclick=\"ShowAndHideECB()\">
				<h2>Select FOUR Eastern Conference Backcourt Players:</h2>
				<i>Tap/click here to reveal/hide nominees</i>
			</div>
			$easternConferenceBackcourt

			<div onclick=\"ShowAndHideWCF()\">
				<h2>Select FOUR Western Conference Frontcourt Players:</h2>
				<i>Tap/click here to reveal/hide nominees</i>
			</div>
			$westernConferenceFrontcourt

			<div onclick=\"ShowAndHideWCB()\">
				<h2>Select FOUR Western Conference Backcourt Players:</h2>
				<i>Tap/click here to reveal/hide nominees</i>
			</div>
			$westernConferenceBackcourt";
    } else {
        $mostValuablePlayers .= getCandidates('MVP', $voterTeamName);
        $sixthPersons .= getCandidates('Six', $voterTeamName);
        $rookiesOfTheYear .= getCandidates('ROY', $voterTeamName);
        $generalManagers .= getCandidates('GM', $voterTeamName);

        echo "<div onclick=\"ShowAndHideMVP()\">
				<h2>Select your top THREE choices for Most Valuable Player:</h2>
				<i>Tap/click here to reveal/hide nominees</i>
			</div>
			$mostValuablePlayers

			<div onclick=\"ShowAndHideSix()\">
				<h2>Select your top THREE choices for Sixth-Person of the Year:</h2>
				<i>Tap/click here to reveal/hide nominees</i>
			</div>
			$sixthPersons

			<div onclick=\"ShowAndHideROY()\">
				<h2>Select your top THREE choices for Rookie of the Year:</h2>
				<i>Tap/click here to reveal/hide nominees</i>
			</div>
			$rookiesOfTheYear

			<div onclick=\"ShowAndHideGM()\">
				<h2>Select your top THREE choices for General Manager of the Year:</h2>
				<i>Tap/click here to reveal/hide nominees</i>
			</div>
			$generalManagers";
    }

    echo "<input type=\"hidden\" name=\"teamname\" value=\"$voterTeamName\">

		<input type=\"submit\" value=\"Submit Votes!\">
	</center>
	</form>";

    CloseTable();

    Nuke\Footer::footer();
}

function main($user)
{
    global $stop;
    if (!is_user($user)) {
        Nuke\Header::header();
        if ($stop) {
            OpenTable();
            echo "<center><font class=\"title\"><b>" . _LOGININCOR . "</b></font></center>\n";
            CloseTable();
            echo "<br>\n";
        } else {
            OpenTable();
            echo "<center><font class=\"title\"><b>" . _USERREGLOGIN . "</b></font></center>\n";
            CloseTable();
            echo "<br>\n";
        }
        if (!is_user($user)) {
            loginbox();
        }
        Nuke\Footer::footer();
    } elseif (is_user($user)) {
        global $cookie;
        userinfo($cookie[1]);
    }
}

switch ($op) {
    default:
        main($user);
        break;
}

?>
