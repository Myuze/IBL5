<?php

if (!mb_eregi("modules.php", $_SERVER['PHP_SELF'])) {
    die("You can't access this file directly...");
}

$module_name = basename(dirname(__FILE__));
get_lang($module_name);

$pagetitle = "- Team Pages";

function menu()
{
    global $db;

    Nuke\Header::header();
    OpenTable();

    UI::displaytopmenu($db, 0);

    CloseTable();
    Nuke\Footer::footer();
}

function buildTeamFutureSalary($resultTeamPlayers, $k)
{
    global $db;
    $uiHelper = new Trading_UIHelper($db);
    return $uiHelper->buildTeamFutureSalary($resultTeamPlayers, $k);
}

function buildTeamFuturePicks($resultTeamPicks, $future_salary_array)
{
    global $db;
    $uiHelper = new Trading_UIHelper($db);
    return $uiHelper->buildTeamFuturePicks($resultTeamPicks, $future_salary_array);
}

function tradeoffer($username, $bypass = 0, $hid = 0, $url = 0)
{
    global $user, $prefix, $user_prefix, $db, $partner;
    $sharedFunctions = new Shared($db);
    $season = new Season($db);

    $sql = "SELECT * FROM " . $prefix . "_bbconfig";
    $result = $db->sql_query($sql);
    while ($row = $db->sql_fetchrow($result)) {
        $board_config[$row['config_name']] = $row['config_value'];
    }

    $sql2 = "SELECT * FROM " . $user_prefix . "_users WHERE username = '$username'";
    $result2 = $db->sql_query($sql2);
    $userinfo = $db->sql_fetchrow($result2);
    if (!$bypass) {
        cookiedecode($user);
    }

    Nuke\Header::header();

    $currentSeasonEndingYear = $season->endingYear; // we use this as an incrementer

    OpenTable();

    $teamlogo = $userinfo['user_ibl_team'];
    $teamID = $sharedFunctions->getTidFromTeamname($teamlogo); // This function now returns an integer
    UI::displaytopmenu($db, $teamID);

    $queryUserTeamPlayers = "SELECT pos, name, pid, ordinal, cy, cy1, cy2, cy3, cy4, cy5, cy6
		FROM ibl_plr
		WHERE tid = $teamID
		AND retired = '0'
		ORDER BY ordinal ASC ";
    $resultUserTeamPlayers = $db->sql_query($queryUserTeamPlayers);

    $queryUserTeamDraftPicks = "SELECT *
		FROM ibl_draft_picks
		WHERE ownerofpick = '$teamlogo'
		ORDER BY year, round ASC ";
    $resultUserTeamDraftPicks = $db->sql_query($queryUserTeamDraftPicks);

    echo "<form name=\"Trade_Offer\" method=\"post\" action=\"/ibl5/modules/Trading/maketradeoffer.php\">
		<input type=\"hidden\" name=\"offeringTeam\" value=\"$teamlogo\">
		<center>
			<img src=\"images/logo/$teamID.jpg\"><br>
			<table border=1 cellspacing=0 cellpadding=5>
				<tr>
					<th colspan=4><center>TRADING MENU</center></th>
				</tr>
				<tr>
					<td valign=top>
						<table cellspacing=3>
							<tr>
								<td valign=top colspan=4>
									<center><b><u>$teamlogo</u></b></center>
								</td>
							</tr>
							<tr>
								<td valign=top><b>Select</b></td>
								<td valign=top><b>Pos</b></td>
								<td valign=top><b>Name</b></td>
								<td valign=top><b>Salary</b></td>";

    $future_salary_array = buildTeamFutureSalary($resultUserTeamPlayers, 0);
    $future_salary_array = buildTeamFuturePicks($resultUserTeamDraftPicks, $future_salary_array);
    $k = $future_salary_array['k']; // pull $k value out to populate $Fields_Counter in maketradeoffer.php

    echo "</table>
		</td>
		<td valign=top>
			<table cellspacing=3>
				<tr>
					<td valign=top align=center colspan=4>
						<input type=\"hidden\" name=\"switchCounter\" value=\"$k\">
						<input type=\"hidden\" name=\"listeningTeam\" value=\"$partner\">
						<b><u>$partner</u></b>
					</td>
				</tr>
				<tr>
					<td valign=top><b>Select</b></td>
					<td valign=top><b>Pos</b></td>
					<td valign=top><b>Name</b></td>
					<td valign=top><b>Salary</b></td>
				</tr>";

    $partnerTeamID = $sharedFunctions->getTidFromTeamname($partner); // This function now returns an integer
    $queryPartnerTeamPlayers = "SELECT pos, name, pid, ordinal, cy, cy1, cy2, cy3, cy4, cy5, cy6
		FROM ibl_plr
		WHERE tid = $partnerTeamID
		AND retired = '0'
		ORDER BY ordinal ASC ";
    $resultPartnerTeamPlayers = $db->sql_query($queryPartnerTeamPlayers);

    $queryPartnerTeamDraftPicks = "SELECT *
		FROM ibl_draft_picks
		WHERE ownerofpick = '$partner'
		ORDER BY year, round ASC ";
    $resultPartnerTeamDraftPicks = $db->sql_query($queryPartnerTeamDraftPicks);

    $future_salary_arrayb = buildTeamFutureSalary($resultPartnerTeamPlayers, $k);
    $future_salary_arrayb = buildTeamFuturePicks($resultPartnerTeamDraftPicks, $future_salary_arrayb);
    $k = $future_salary_arrayb['k']; // pull $k value out to populate $Fields_Counter in maketradeoffer.php

    $k--;
    echo "</table>
		</td>
		<td valign=top>
			<table>
				<tr>
					<td valign=top><center><b><u>Make Trade Offer To...</u></b></center>";

    $uiHelper = new Trading_UIHelper($db);
    $teams = $uiHelper->getAllTeamsForTrading();
    echo $uiHelper->renderTeamSelectionLinks($teams);

    echo "</td></tr></table>";
    $z = 0;
    $seasonsToDisplay = 6;
    if (
        $season->phase == "Playoffs"
        OR $season->phase == "Draft"
        OR $season->phase == "Free Agency"
    ) {
        $currentSeasonEndingYear++;
        $seasonsToDisplay--;
    }
    while ($z < $seasonsToDisplay) {
        echo "<tr>
            <td>
                <b>$teamlogo Cap Total in " . ($currentSeasonEndingYear + $z - 1) . "-" . ($currentSeasonEndingYear + $z) . ":</b> " . $future_salary_array['player'][$z] . "
            </td>
            <td align=right>
                <b>$partner Cap Total in " . ($currentSeasonEndingYear + $z - 1) . "-" . ($currentSeasonEndingYear + $z) . ":</b> " . $future_salary_arrayb['player'][$z] . "
            </td>";
        $z++;
    }

    $currentSeasonEndingYear = $season->endingYear; // This resets the incrementation from the last block.
    $i = 1; // We need to start at 1 because of the "xSendsCash" value names.
    if (
        $season->phase == "Playoffs"
        OR $season->phase == "Draft"
        OR $season->phase == "Free Agency"
    ) {
        $i++;
    }
    while ($i <= 6) {
        // Because we start $i = 1, the math to derive the years to display increases by 1 too.
        echo "<tr>
            <td>
                <b>$teamlogo send
                <input type=\"number\" name=\"userSendsCash$i\" value =\"0\" min=\"0\" max =\"2000\">
                for " . ($currentSeasonEndingYear - 2 + $i) . "-" . ($currentSeasonEndingYear - 1 + $i) . "</b>
            </td>
            <td align=right>
                <b>$partner send
                <input type=\"number\" name=\"partnerSendsCash$i\" value =\"0\" min=\"0\" max =\"2000\">
                for " . ($currentSeasonEndingYear - 2 + $i) . "-" . ($currentSeasonEndingYear - 1 + $i) . "</b>
            </td>
        </tr>";
        $i++;
    }

    echo "<tr>
            <td colspan=3 align=center>
                <input type=\"hidden\" name=\"fieldsCounter\" value=\"$k\">
                <input type=\"submit\" value=\"Make Trade Offer\">
            </td>
        </tr>
    </form></center></table>";

    CloseTable();

    Nuke\Footer::footer();
}

function tradereview($username, $bypass = 0, $hid = 0, $url = 0)
{
    global $user, $prefix, $user_prefix, $db;
    $sharedFunctions = new Shared($db);

    $sql = "SELECT * FROM " . $prefix . "_bbconfig";
    $result = $db->sql_query($sql);
    while ($row = $db->sql_fetchrow($result)) {
        $board_config[$row['config_name']] = $row['config_value'];
    }

    // ==== PICKUP LOGGED-IN USER INFO

    $sql2 = "SELECT * FROM " . $user_prefix . "_users WHERE username = '$username'";
    $result2 = $db->sql_query($sql2);
    $userinfo = $db->sql_fetchrow($result2);
    if (!$bypass) {
        cookiedecode($user);
    }

    // ===== END OF INFO PICKUP

    Nuke\Header::header();

    OpenTable();

    $teamlogo = $userinfo['user_ibl_team'];
    $teamID = $sharedFunctions->getTidFromTeamname($teamlogo); // This function now returns an integer
    UI::displaytopmenu($db, $teamID);

    echo "<center><img src=\"images/logo/$teamID.jpg\"><br>";

    $sql3 = "SELECT * FROM ibl_trade_info ORDER BY tradeofferid ASC";
    $result3 = $db->sql_query($sql3);

    $tradeworkingonnow = 0;

    echo "<table>
		<th>
			<tr>
				<td valign=top>REVIEW TRADE OFFERS";

    while ($row3 = $db->sql_fetchrow($result3)) {
        $isinvolvedintrade = 0;
        $hashammer = 0;
        $offerid = $row3['tradeofferid'];
        $itemid = $row3['itemid'];

        // For itemtype (1 = player, 0 = pick, cash = cash)
        $itemtype = $row3['itemtype'];
        $from = $row3['from'];
        $to = $row3['to'];
        $approval = $row3['approval'];

        if ($from == $teamlogo) {
            $isinvolvedintrade = 1;
            $oppositeTeam = $to;
        }
        if ($to == $teamlogo) {
            $isinvolvedintrade = 1;
            $oppositeTeam = $from;
        }
        if ($approval == $teamlogo) {
            $hashammer = 1;
        }

        if ($isinvolvedintrade == 1) {
            if ($offerid == $tradeworkingonnow) {
            } else {
                echo "				</td>
							</tr>
						</th>
					</table>
					<table border=1 valign=top align=center>
						<tr>
							<td>
								<b><u>TRADE OFFER</u></b><br>
								<table align=right border=1 cellspacing=0 cellpadding=0>
									<tr>
										<td valign=center>";
                if ($hashammer == 1) {
                    echo "<form name=\"tradeaccept\" method=\"post\" action=\"/ibl5/modules/Trading/accepttradeoffer.php\">
						<input type=\"hidden\" name=\"offer\" value=\"$offerid\">
						<input type=\"submit\" value=\"Accept\" onclick=\"this.disabled=true;this.value='Submitting...'; this.form.submit();\">
					</form>";
                } else {
                    echo "(Awaiting Approval)";
                }
                echo "</td>
						<td valign=center>
							<form name=\"tradereject\" method=\"post\" action=\"/ibl5/modules/Trading/rejecttradeoffer.php\">
								<input type=\"hidden\" name=\"offer\" value=\"$offerid\">
                                <input type=\"hidden\" name=\"teamRejecting\" value=\"$teamlogo\">
                                <input type=\"hidden\" name=\"teamReceiving\" value=\"$oppositeTeam\">
								<input type=\"submit\" value=\"Reject\">
							</form>
						</td>
					</tr>
				</table>";
            }

            if ($itemtype == 'cash') {
                $queryCashDetails = "SELECT * FROM ibl_trade_cash WHERE tradeOfferID = $offerid AND sendingTeam = '$from';";
                $cashDetails = $db->sql_fetchrow($db->sql_query($queryCashDetails));

                $cashYear[1] = $cashDetails['cy1'];
                $cashYear[2] = $cashDetails['cy2'];
                $cashYear[3] = $cashDetails['cy3'];
                $cashYear[4] = $cashDetails['cy4'];
                $cashYear[5] = $cashDetails['cy5'];
                $cashYear[6] = $cashDetails['cy6'];

                echo "The $from send 
                $cashYear[1] $cashYear[2] $cashYear[3] $cashYear[4] $cashYear[5] $cashYear[6]
                in cash to the $to.<br>";
            } elseif ($itemtype == 0) {
                $sqlgetpick = "SELECT * FROM ibl_draft_picks WHERE pickid = '$itemid'";
                $resultgetpick = $db->sql_query($sqlgetpick);
                $rowsgetpick = $db->sql_fetchrow($resultgetpick);

                $pickteam = $rowsgetpick['teampick'];
                $pickyear = $rowsgetpick['year'];
                $pickround = $rowsgetpick['round'];
                $picknotes = $rowsgetpick['notes'];

                echo "The $from send the $pickteam $pickyear Round $pickround draft pick to the $to.<br>";
                if ($picknotes != NULL) {
                    echo "<i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . $picknotes . "</i><br>";
                }
            } elseif ($itemtype == 1) {
                $sqlgetplyr = "SELECT * FROM ibl_plr WHERE pid = '$itemid'";
                $resultgetplyr = $db->sql_query($sqlgetplyr);
                $rowsgetplyr = $db->sql_fetchrow($resultgetplyr);

                $plyrname = $rowsgetplyr['name'];
                $plyrpos = $rowsgetplyr['pos'];

                echo "The $from send $plyrpos $plyrname to the $to.<br>";
            }

            $tradeworkingonnow = $offerid;
        }
    }

    echo "</td>
		<td valign=top><center><b><u>Make Trade Offer To...</u></b></center>";

    $uiHelper = new Trading_UIHelper($db);
    $teams = $uiHelper->getAllTeamsForTrading();
    echo $uiHelper->renderTeamSelectionLinks($teams);

    echo "</td>
		</tr>
		<tr>
			<td colspan=2 align=center>
				<a href=\"modules.php?name=Waivers&action=drop\">Drop a player to Waivers</a><br>
				<a href=\"modules.php?name=Waivers&action=add\">Add a player from Waivers</a><br>
			</td>
		</tr>
	</table>";

    CloseTable();
    Nuke\Footer::footer();
}

function reviewtrade($user)
{
    global $db, $stop;
    $season = new Season($db);

    if (!is_user($user)) {
        Nuke\Header::header();
        if ($stop) {
            OpenTable();
            echo "<center><font class=\"title\"><b>" . _LOGININCOR . "</b></font></center>\n";
            CloseTable();
        } else {
            OpenTable();
            echo "<center><font class=\"title\"><b>" . _USERREGLOGIN . "</b></font></center>\n";
            CloseTable();
        }
        if (!is_user($user)) {
            OpenTable();
            UI::displaytopmenu($db, 0);
            loginbox();
            CloseTable();
        }
        Nuke\Footer::footer();
    } elseif (is_user($user)) {
        if ($season->allowTrades == 'Yes') {
            global $cookie;
            cookiedecode($user);
            tradereview($cookie[1]);
        } else {
            Nuke\Header::header();
            OpenTable();
            UI::displaytopmenu($db, 0);
            echo "Sorry, but trades are not allowed right now.";
            if ($season->allowWaivers == 'Yes') {
                echo "<br>
				Players may still be <a href=\"modules.php?name=Waivers&action=add\">Added From Waivers</a> or they may be <a href=\"modules.php?name=Waivers&action=drop\">Dropped to Waivers</a>.";
            } else {
                echo "<br>The waiver wire is also closed.";
            }
            CloseTable();
            Nuke\Footer::footer();
        }
    }
}

function offertrade($user)
{
    global $db, $stop;

    if (!is_user($user)) {
        Nuke\Header::header();
        if ($stop) {
            OpenTable();
            echo "<center><font class=\"title\"><b>" . _LOGININCOR . "</b></font></center>\n";
            CloseTable();
        } else {
            OpenTable();
            echo "<center><font class=\"title\"><b>" . _USERREGLOGIN . "</b></font></center>\n";
            CloseTable();
        }
        if (!is_user($user)) {
            OpenTable();
            UI::displaytopmenu($db, 0); // Default to Free Agents for login screen
            loginbox();
            CloseTable();
        }
        Nuke\Footer::footer();
    } elseif (is_user($user)) {
        global $cookie;
        cookiedecode($user);
        tradeoffer($cookie[1]);
    }
}

switch ($op) {
    case "reviewtrade":
        reviewtrade($user);
        break;

    case "offertrade":
        offertrade($user);
        break;

    default:
        menu();
        break;
}
