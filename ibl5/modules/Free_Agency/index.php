<?php

use Player\Player;

/************************************************************************/
/*                     IBL Free Agency Module                           */
/*               (c) July 22, 2005 by Spencer Cooley                    */
/************************************************************************/

if (!mb_eregi("modules.php", $_SERVER['PHP_SELF'])) {
    die("You can't access this file directly...");
}

$module_name = basename(dirname(__FILE__));
get_lang($module_name);

$pagetitle = "- Free Agency System";

function main($user)
{
    global $stop;
    if (!is_user($user)) {
        Nuke\Header::header();
        OpenTable();
        echo "<center><font class=\"title\"><b>" . ($stop ? _LOGININCOR : _USERREGLOGIN) . "</b></font></center>";
        CloseTable();
        echo "<br>";
        if (!is_user($user)) {
            OpenTable();
            loginbox();
            CloseTable();
        }
        Nuke\Footer::footer();
    } elseif (is_user($user)) {
        display();
    }
}

function display()
{
    global $prefix, $db, $cookie;
    $sharedFunctions = new Shared($db);
    $season = new Season($db);

    Nuke\Header::header();
    OpenTable();

    $username = $cookie[1];
    $teamName = $sharedFunctions->getTeamnameFromUsername($username);
    $team = Team::initialize($db, $teamName);

    UI::displaytopmenu($db, $team->teamID);

    $year1TotalSalary = $year2TotalSalary = $year3TotalSalary = $year4TotalSalary = $year5TotalSalary = $year6TotalSalary = 0;
    $rosterspots1 = $rosterspots2 = $rosterspots3 = $rosterspots4 = $rosterspots5 = $rosterspots6 = 15;

    echo "<center><img src=\"images/logo/$team->teamID.jpg\"></center><p>";

    // ==== DISPLAY PLAYERS CURRENTLY UNDER CONTRACT FOR TEAM

    echo "<table border=1 cellspacing=0 class=\"sortable\">
		<caption style=\"background-color: #0000cc\">
			<center><b><font color=white>$team->name Players Under Contract</font></b></center>
		</caption>
		<colgroup>
			<col span=5>
			<col span=6 style=\"background-color: #ddd\">
			<col span=7>
			<col span=8 style=\"background-color: #ddd\">
			<col span=3>
			<col span=6 style=\"background-color: #ddd\">
			<col span=5>
		</colgroup>
		<thead>
			<tr>
				<td><b>Options</b></td>
				<td><b>Pos</b></td>
				<td><b>Player</b></td>
				<td><b>Team</b></td>
				<td><b>Age</b></td>
				<td><b>2ga</b></td>
				<td><b>2g%</b></td>
				<td><b>fta</b></td>
				<td><b>ft%</b></td>
				<td><b>3ga</b></td>
				<td><b>3g%</b></td>
				<td><b>orb</b></td>
				<td><b>drb</b></td>
				<td><b>ast</b></td>
				<td><b>stl</b></td>
				<td><b>to</b></td>
				<td><b>blk</b></td>
				<td><b>foul</b></td>
				<td><b>oo</b></td>
				<td><b>do</b></td>
				<td><b>po</b></td>
				<td><b>to</b></td>
				<td><b>od</b></td>
				<td><b>dd</b></td>
				<td><b>pd</b></td>
				<td><b>td</b></td>
				<td><b>T</b></td>
				<td><b>S</b></td>
				<td><b>I</b></td>
				<td><b>Yr1</b></td>
				<td><b>Yr2</b></td>
				<td><b>Yr3</b></td>
				<td><b>Yr4</b></td>
				<td><b>Yr5</b></td>
				<td><b>Yr6</b></td>
				<td><b>Loy</b></td>
				<td><b>PFW</b></td>
				<td><b>PT</b></td>
				<td><b>Sec</b></td>
				<td><b>Trad</b></td>
			</tr>
		</thead>
		<tbody>";


    foreach ($team->getRosterUnderContractOrderedByOrdinalResult() as $playerRow) {
        $player = Player::withPlrRow($db, $playerRow);

        $yearPlayerIsFreeAgent = $player->draftYear + $player->yearsOfExperience + $player->contractTotalYears - $player->contractCurrentYear;
        if ($yearPlayerIsFreeAgent != $season->endingYear) {
            // === MATCH UP CONTRACT AMOUNTS WITH FUTURE YEARS BASED ON CURRENT YEAR OF CONTRACT

            $year1Salary = $year2Salary = $year3Salary = $year4Salary = $year5Salary = $year6Salary = 0;

            // if player name doesn't start with '|' (pipe symbol), then don't occupy a roster slot
            $firstCharacterOfPlayerName = substr($player->name, 0, 1); 

            if ($player->contractCurrentYear == 0) {
                $year1Salary = $player->contractYear1Salary;
                $year2Salary = $player->contractYear2Salary;
                $year3Salary = $player->contractYear3Salary;
                $year4Salary = $player->contractYear4Salary;
                $year5Salary = $player->contractYear5Salary;
                $year6Salary = $player->contractYear6Salary;

                if ($player->teamName == $team->name AND $firstCharacterOfPlayerName !== '|') {
                    if ($player->contractYear1Salary != 0) $rosterspots1--;
                    if ($player->contractYear2Salary != 0) $rosterspots2--;
                    if ($player->contractYear3Salary != 0) $rosterspots3--;
                    if ($player->contractYear4Salary != 0) $rosterspots4--;
                    if ($player->contractYear5Salary != 0) $rosterspots5--;
                    if ($player->contractYear6Salary != 0) $rosterspots6--;
                }
            }
            if ($player->contractCurrentYear == 1) {
                $year1Salary = $player->contractYear2Salary;
                $year2Salary = $player->contractYear3Salary;
                $year3Salary = $player->contractYear4Salary;
                $year4Salary = $player->contractYear5Salary;
                $year5Salary = $player->contractYear6Salary;

                if ($player->teamName == $team->name AND $firstCharacterOfPlayerName !== '|') {
                    if ($player->contractYear2Salary != 0) $rosterspots1--;
                    if ($player->contractYear3Salary != 0) $rosterspots2--;
                    if ($player->contractYear4Salary != 0) $rosterspots3--;
                    if ($player->contractYear5Salary != 0) $rosterspots4--;
                    if ($player->contractYear6Salary != 0) $rosterspots5--;
                }
            }
            if ($player->contractCurrentYear == 2) {
                $year1Salary = $player->contractYear3Salary;
                $year2Salary = $player->contractYear4Salary;
                $year3Salary = $player->contractYear5Salary;
                $year4Salary = $player->contractYear6Salary;

                if ($player->teamName == $team->name AND $firstCharacterOfPlayerName !== '|') {
                    if ($player->contractYear3Salary != 0) $rosterspots1--;
                    if ($player->contractYear4Salary != 0) $rosterspots2--;
                    if ($player->contractYear5Salary != 0) $rosterspots3--;
                    if ($player->contractYear6Salary != 0) $rosterspots4--;
                }
            }
            if ($player->contractCurrentYear == 3) {
                $year1Salary = $player->contractYear4Salary;
                $year2Salary = $player->contractYear5Salary;
                $year3Salary = $player->contractYear6Salary;

                if ($player->teamName == $team->name AND $firstCharacterOfPlayerName !== '|') {
                    if ($player->contractYear4Salary != 0) $rosterspots1--;
                    if ($player->contractYear5Salary != 0) $rosterspots2--;
                    if ($player->contractYear6Salary != 0) $rosterspots3--;
                }
            }
            if ($player->contractCurrentYear == 4) {
                $year1Salary = $player->contractYear5Salary;
                $year2Salary = $player->contractYear6Salary;

                if ($player->teamName == $team->name AND $firstCharacterOfPlayerName !== '|') {
                    if ($player->contractYear5Salary != 0) $rosterspots1--;
                    if ($player->contractYear6Salary != 0) $rosterspots2--;
                }
            }
            if ($player->contractCurrentYear == 5) {
                $year1Salary = $player->contractYear6Salary;

                if ($player->teamName == $team->name AND $firstCharacterOfPlayerName !== '|') {
                    if ($player->contractYear6Salary != 0) $rosterspots1--;
                }
            }

            echo "<tr>
                <td>";

            // ==== ROOKIE OPTIONS
            if ($player->canRookieOption($season->phase)) {
                echo "<a href=\"modules.php?name=Player&pa=rookieoption&pid=$player->playerID\">Rookie Option</a>";
            }

            if ($player->ordinal > JSB::WAIVERS_ORDINAL) {
                $player->name .= "*";
            }

            echo "</td>
                <td>$player->position</td>
                <td><a href=\"modules.php?name=Player&pa=showpage&pid=$player->playerID\">$player->name</a></td>
                <td><a href=\"modules.php?name=Team&op=team&teamID=$player->teamID\">$player->teamName</a></td>
                <td>$player->age</td>
                <td>$player->ratingFieldGoalAttempts</td>
                <td>$player->ratingFieldGoalPercentage</td>
                <td>$player->ratingFreeThrowAttempts</td>
                <td>$player->ratingFreeThrowPercentage</td>
                <td>$player->ratingThreePointAttempts</td>
                <td>$player->ratingThreePointPercentage</td>
                <td>$player->ratingOffensiveRebounds</td>
                <td>$player->ratingDefensiveRebounds</td>
                <td>$player->ratingAssists</td>
                <td>$player->ratingSteals</td>
                <td>$player->ratingTurnovers</td>
                <td>$player->ratingBlocks</td>
                <td>$player->ratingFouls</td>
                <td>$player->ratingOutsideOffense</td>
                <td>$player->ratingDriveOffense</td>
                <td>$player->ratingPostOffense</td>
                <td>$player->ratingTransitionOffense</td>
                <td>$player->ratingOutsideDefense</td>
                <td>$player->ratingDriveDefense</td>
                <td>$player->ratingPostDefense</td>
                <td>$player->ratingTransitionDefense</td>
                <td>$player->ratingTalent</td>
                <td>$player->ratingSkill</td>
                <td>$player->ratingIntangibles</td>
                <td>$year1Salary</td>
                <td>$year2Salary</td>
                <td>$year3Salary</td>
                <td>$year4Salary</td>
                <td>$year5Salary</td>
                <td>$year6Salary</td>
                <td>$player->freeAgencyLoyalty</td>
                <td>$player->freeAgencyPlayForWinner</td>
                <td>$player->freeAgencyPlayingTime</td>
                <td>$player->freeAgencySecurity</td>
                <td>$player->freeAgencyTradition</td>
            </tr>";

            $year1TotalSalary += $year1Salary;
            $year2TotalSalary += $year2Salary;
            $year3TotalSalary += $year3Salary;
            $year4TotalSalary += $year4Salary;
            $year5TotalSalary += $year5Salary;
            $year6TotalSalary += $year6Salary;
        }
    }

    echo "</tbody>
		<tfoot>
			<tr>
				<td colspan=29 align=right><b><i>$team->name Total Salary</i></b></td>
				<td><b><i>$year1TotalSalary</i></b></td>
				<td><b><i>$year2TotalSalary</i></b></td>
				<td><b><i>$year3TotalSalary</i></b></td>
				<td><b><i>$year4TotalSalary</i></b></td>
				<td><b><i>$year5TotalSalary</i></b></td>
				<td><b><i>$year6TotalSalary</i></b></td>
			</tr>
		</tfoot>
	</table>

	<p>";

    // ==== END LIST OF PLAYERS CURRENTLY UNDER CONTRACT

    // ==== INSERT LIST OF PLAYERS WITH OFFERS

    echo "<table border=1 cellspacing=0 class=\"sortable\">
		<caption style=\"background-color: #0000cc\">
			<center><b><font color=white>$team->name Contract Offers</font></b></center>
		</caption>
		<colgroup>
			<col span=5>
			<col span=6 style=\"background-color: #ddd\">
			<col span=7>
			<col span=8 style=\"background-color: #ddd\">
			<col span=3>
			<col span=6 style=\"background-color: #ddd\">
			<col span=5>
		</colgroup>
		<thead>
			<tr>
				<td><b>Negotiate</b></td>
				<td><b>Pos</b></td>
				<td><b>Player</b></td>
				<td><b>Team</b></td>
				<td><b>Age</b></td>
				<td><b>2ga</b></td>
				<td><b>2g%</b></td>
				<td><b>fta</b></td>
				<td><b>ft%</b></td>
				<td><b>3ga</b></td>
				<td><b>3g%</b></td>
				<td><b>orb</b></td>
				<td><b>drb</b></td>
				<td><b>ast</b></td>
				<td><b>stl</b></td>
				<td><b>to</b></td>
				<td><b>blk</b></td>
				<td><b>foul</b></td>
				<td><b>oo</b></td>
				<td><b>do</b></td>
				<td><b>po</b></td>
				<td><b>to</b></td>
				<td><b>od</b></td>
				<td><b>dd</b></td>
				<td><b>pd</b></td>
				<td><b>td</b></td>
				<td><b>T</b></td>
				<td><b>S</b></td>
				<td><b>I</b></td>
				<td><b>Yr1</b></td>
				<td><b>Yr2</b></td>
				<td><b>Yr3</b></td>
				<td><b>Yr4</b></td>
				<td><b>Yr5</b></td>
				<td><b>Yr6</b></td>
				<td><b>Loy</b></td>
				<td><b>PFW</b></td>
				<td><b>PT</b></td>
				<td><b>Sec</b></td>
				<td><b>Trad</b></td>
			</tr>
		</thead>
		<tbody>";

    foreach ($team->getFreeAgencyOffersResult() as $offerRow) {
        $playerID = $sharedFunctions->getPlayerIDFromPlayerName($offerRow['name']);
        $player = Player::withPlayerID($db, $playerID);

        $offer1 = $offerRow['offer1'];
        $offer2 = $offerRow['offer2'];
        $offer3 = $offerRow['offer3'];
        $offer4 = $offerRow['offer4'];
        $offer5 = $offerRow['offer5'];
        $offer6 = $offerRow['offer6'];

        echo "<tr>
            <td><a href=\"modules.php?name=Free_Agency&pa=negotiate&pid=$player->playerID\">Negotiate</a></td>
            <td>$player->position</td>
            <td><a href=\"modules.php?name=Player&pa=showpage&pid=$player->playerID\">$player->name</a></td>
            <td><a href=\"modules.php?name=Team&op=team&teamID=$player->teamID\">$player->teamName</a></td>
            <td>$player->age</td>
            <td>$player->ratingFieldGoalAttempts</td>
            <td>$player->ratingFieldGoalPercentage</td>
            <td>$player->ratingFreeThrowAttempts</td>
            <td>$player->ratingFreeThrowPercentage</td>
            <td>$player->ratingThreePointAttempts</td>
            <td>$player->ratingThreePointPercentage</td>
            <td>$player->ratingOffensiveRebounds</td>
            <td>$player->ratingDefensiveRebounds</td>
            <td>$player->ratingAssists</td>
            <td>$player->ratingSteals</td>
            <td>$player->ratingTurnovers</td>
            <td>$player->ratingBlocks</td>
            <td>$player->ratingFouls</td>
            <td>$player->ratingOutsideOffense</td>
            <td>$player->ratingDriveOffense</td>
            <td>$player->ratingPostOffense</td>
            <td>$player->ratingTransitionOffense</td>
            <td>$player->ratingOutsideDefense</td>
            <td>$player->ratingDriveDefense</td>
            <td>$player->ratingPostDefense</td>
            <td>$player->ratingTransitionDefense</td>
            <td>$player->ratingTalent</td>
            <td>$player->ratingSkill</td>
            <td>$player->ratingIntangibles</td>
            <td>$offer1</td>
            <td>$offer2</td>
            <td>$offer3</td>
            <td>$offer4</td>
            <td>$offer5</td>
            <td>$offer6</td>
            <td>$player->freeAgencyLoyalty</td>
            <td>$player->freeAgencyPlayForWinner</td>
            <td>$player->freeAgencyPlayingTime</td>
            <td>$player->freeAgencySecurity</td>
            <td>$player->freeAgencyTradition</td>
        </tr>";

        $year1TotalSalary += $offer1;
        $year2TotalSalary += $offer2;
        $year3TotalSalary += $offer3;
        $year4TotalSalary += $offer4;
        $year5TotalSalary += $offer5;
        $year6TotalSalary += $offer6;

        if ($offer1 != 0) $rosterspots1--;
        if ($offer2 != 0) $rosterspots2--;
        if ($offer3 != 0) $rosterspots3--;
        if ($offer4 != 0) $rosterspots4--;
        if ($offer5 != 0) $rosterspots5--;
        if ($offer6 != 0) $rosterspots6--;
    }

    echo "</tbody>
		<tfoot>
			<tr>
				<td colspan=29 align=right><b><i>$team->name Total Salary Plus Contract Offers</i></b></td>
				<td><b><i>$year1TotalSalary</i></b></td>
				<td><b><i>$year2TotalSalary</i></b></td>
				<td><b><i>$year3TotalSalary</i></b></td>
				<td><b><i>$year4TotalSalary</i></b></td>
				<td><b><i>$year5TotalSalary</i></b></td>
				<td><b><i>$year6TotalSalary</i></b></td>
			</tr>";

    // ==== END INSERT OF PLAYERS WITH OFFERS

    $year1AvailableSoftCap = League::SOFT_CAP_MAX - $year1TotalSalary;
    $year1AvailableHardCap = League::HARD_CAP_MAX - $year1TotalSalary;
    $year2AvailableSoftCap = League::SOFT_CAP_MAX - $year2TotalSalary;
    $year2AvailableHardCap = League::HARD_CAP_MAX - $year2TotalSalary;
    $year3AvailableSoftCap = League::SOFT_CAP_MAX - $year3TotalSalary;
    $year3AvailableHardCap = League::HARD_CAP_MAX - $year3TotalSalary;
    $year4AvailableSoftCap = League::SOFT_CAP_MAX - $year4TotalSalary;
    $year4AvailableHardCap = League::HARD_CAP_MAX - $year4TotalSalary;
    $year5AvailableSoftCap = League::SOFT_CAP_MAX - $year5TotalSalary;
    $year5AvailableHardCap = League::HARD_CAP_MAX - $year5TotalSalary;
    $year6AvailableSoftCap = League::SOFT_CAP_MAX - $year6TotalSalary;
    $year6AvailableHardCap = League::HARD_CAP_MAX - $year6TotalSalary;

    // ===== CAP AND ROSTER SLOT INFO =====

    $MLEicon = ($team->hasMLE == "1") ? "\u{2705}" : "\u{274C}";
    $LLEicon = ($team->hasLLE == "1") ? "\u{2705}" : "\u{274C}";

    echo "<tr bgcolor=#cc0000>
        <td align=right><font color=white><b>MLE:</b></font></td>
        <td align=center>$MLEicon</td>
		<td colspan=19 bgcolor=#eeeeee></td>
		<td colspan=8 align=right><font color=white><b>Soft Cap Space</b></font></td>
		<td>$year1AvailableSoftCap</td>
		<td>$year2AvailableSoftCap</td>
		<td>$year3AvailableSoftCap</td>
		<td>$year4AvailableSoftCap</td>
		<td>$year5AvailableSoftCap</td>
		<td>$year6AvailableSoftCap</td>
	</tr>";
    echo "<tr bgcolor=#cc0000>
        <td align=right><font color=white><b>LLE:</b></font></td>
        <td align=center>$LLEicon</td>
        <td colspan=19 bgcolor=#eeeeee></td>
		<td colspan=8 align=right><font color=white><b>Hard Cap Space</b></font></td>
		<td>$year1AvailableHardCap</td>
		<td>$year2AvailableHardCap</td>
		<td>$year3AvailableHardCap</td>
		<td>$year4AvailableHardCap</td>
		<td>$year5AvailableHardCap</td>
		<td>$year6AvailableHardCap</td>
	</tr>";
    echo "<tr bgcolor=#cc0000>
		<td colspan=21 bgcolor=#eeeeee></td>
		<td colspan=8 align=right><font color=white><b>Empty Roster Slots</b></font></td>
		<td>$rosterspots1</td>
		<td>$rosterspots2</td>
		<td>$rosterspots3</td>
		<td>$rosterspots4</td>
		<td>$rosterspots5</td>
		<td>$rosterspots6</td>
	</tr>";

    echo "</tfoot></table>
	<p>
	<hr>
	<p>";

    // ==== INSERT LIST OF FREE AGENTS FROM TEAM

    echo "<table border=1 cellspacing=0 class=\"sortable\">
		<caption style=\"background-color: #0000cc\">
			<center><b><font color=white>$team->name Unsigned Free Agents</b><br>
			(Note: * and <i>italicized</i> indicates player has Bird Rights)</font></b></center>
		</caption>
		<colgroup>
			<col span=5>
			<col span=6 style=\"background-color: #ddd\">
			<col span=7>
			<col span=8 style=\"background-color: #ddd\">
			<col span=3>
			<col span=6 style=\"background-color: #ddd\">
			<col span=5>
		</colgroup>
		<thead>
			<tr>
				<td><b>Negotiate</b></td>
				<td><b>Pos</b></td>
				<td><b>Player</b></td>
				<td><b>Team</b></td>
				<td><b>Age</b></td>
				<td><b>2ga</b></td>
				<td><b>2g%</b></td>
				<td><b>fta</b></td>
				<td><b>ft%</b></td>
				<td><b>3ga</b></td>
				<td><b>3g%</b></td>
				<td><b>orb</b></td>
				<td><b>drb</b></td>
				<td><b>ast</b></td>
				<td><b>stl</b></td>
				<td><b>to</b></td>
				<td><b>blk</b></td>
				<td><b>foul</b></td>
				<td><b>oo</b></td>
				<td><b>do</b></td>
				<td><b>po</b></td>
				<td><b>to</b></td>
				<td><b>od</b></td>
				<td><b>dd</b></td>
				<td><b>pd</b></td>
				<td><b>td</b></td>
				<td><b>T</b></td>
				<td><b>S</b></td>
				<td><b>I</b></td>
				<td><b>Yr1</b></td>
				<td><b>Yr2</b></td>
				<td><b>Yr3</b></td>
				<td><b>Yr4</b></td>
				<td><b>Yr5</b></td>
				<td><b>Yr6</b></td>
				<td><b>Loy</b></td>
				<td><b>PFW</b></td>
				<td><b>PT</b></td>
				<td><b>Sec</b></td>
				<td><b>Trad</b></td>
			</tr>
		</thead>
		<tbody>";

    foreach ($team->getRosterUnderContractOrderedByOrdinalResult() as $playerRow) {
        $player = Player::withPlrRow($db, $playerRow);

        $yearPlayerIsFreeAgent = $player->draftYear + $player->yearsOfExperience + $player->contractTotalYears - $player->contractCurrentYear;
        if ($yearPlayerIsFreeAgent == $season->endingYear) {
            $playerDemands = $db->sql_fetchrow($player->getFreeAgencyDemands());
            $year1PlayerDemands = $playerDemands['dem1'];
            $year2PlayerDemands = $playerDemands['dem2'];
            $year3PlayerDemands = $playerDemands['dem3'];
            $year4PlayerDemands = $playerDemands['dem4'];
            $year5PlayerDemands = $playerDemands['dem5'];
            $year6PlayerDemands = $playerDemands['dem6'];

            echo "<tr>
				<td>";

            if ($rosterspots1 > 0) {
                echo "<a href=\"modules.php?name=Free_Agency&pa=negotiate&pid=$player->playerID\">Negotiate</a>";
            }

            echo "</td>
                <td>$player->position</td>
                <td><a href=\"modules.php?name=Player&pa=showpage&pid=$player->playerID\">";

            if ($player->birdYears >= 3) {
                echo "*<i>$player->name</i>*";
            } else {
                echo "$player->name";
            }

            echo "</a></td>
				<td><a href=\"modules.php?name=Team&op=team&teamID=$player->teamID\">$player->teamName</a></td>
                <td>$player->age</td>
                <td>$player->ratingFieldGoalAttempts</td>
                <td>$player->ratingFieldGoalPercentage</td>
                <td>$player->ratingFreeThrowAttempts</td>
                <td>$player->ratingFreeThrowPercentage</td>
                <td>$player->ratingThreePointAttempts</td>
                <td>$player->ratingThreePointPercentage</td>
                <td>$player->ratingOffensiveRebounds</td>
                <td>$player->ratingDefensiveRebounds</td>
                <td>$player->ratingAssists</td>
                <td>$player->ratingSteals</td>
                <td>$player->ratingTurnovers</td>
                <td>$player->ratingBlocks</td>
                <td>$player->ratingFouls</td>
                <td>$player->ratingOutsideOffense</td>
                <td>$player->ratingDriveOffense</td>
                <td>$player->ratingPostOffense</td>
                <td>$player->ratingTransitionOffense</td>
                <td>$player->ratingOutsideDefense</td>
                <td>$player->ratingDriveDefense</td>
                <td>$player->ratingPostDefense</td>
                <td>$player->ratingTransitionDefense</td>
                <td>$player->ratingTalent</td>
                <td>$player->ratingSkill</td>
                <td>$player->ratingIntangibles</td>
				<td>$year1PlayerDemands</td>
				<td>$year2PlayerDemands</td>
				<td>$year3PlayerDemands</td>
				<td>$year4PlayerDemands</td>
				<td>$year5PlayerDemands</td>
				<td>$year6PlayerDemands</td>
                <td>$player->freeAgencyLoyalty</td>
                <td>$player->freeAgencyPlayForWinner</td>
                <td>$player->freeAgencyPlayingTime</td>
                <td>$player->freeAgencySecurity</td>
                <td>$player->freeAgencyTradition</td>
			</tr>";
        }
    }

    echo "</table><p>";

    // ==== END INSERT OF LIST OF FREE AGENTS FROM TEAM

    // ==== INSERT ALL OTHER FREE AGENTS

    echo "<table border=1 cellspacing=0 class=\"sortable\">
		<caption style=\"background-color: #0000cc\">
			<center><b><font color=white>All Other Free Agents</font></b></center>
		</caption>
		<colgroup>
			<col span=5>
			<col span=6 style=\"background-color: #ddd\">
			<col span=7>
			<col span=8 style=\"background-color: #ddd\">
			<col span=3>
			<col span=6 style=\"background-color: #ddd\">
			<col span=5>
		</colgroup>
		<thead>
			<tr>
				<td><b>Negotiate</b></td>
				<td><b>Pos</b></td>
				<td><b>Player</b></td>
				<td><b>Team</b></td>
				<td><b>Age</b></td>
				<td><b>2ga</b></td>
				<td><b>2g%</b></td>
				<td><b>fta</b></td>
				<td><b>ft%</b></td>
				<td><b>3ga</b></td>
				<td><b>3g%</b></td>
				<td><b>orb</b></td>
				<td><b>drb</b></td>
				<td><b>ast</b></td>
				<td><b>stl</b></td>
				<td><b>to</b></td>
				<td><b>blk</b></td>
				<td><b>foul</b></td>
				<td><b>oo</b></td>
				<td><b>do</b></td>
				<td><b>po</b></td>
				<td><b>to</b></td>
				<td><b>od</b></td>
				<td><b>dd</b></td>
				<td><b>pd</b></td>
				<td><b>td</b></td>
				<td><b>T</b></td>
				<td><b>S</b></td>
				<td><b>I</b></td>
				<td><b>Yr1</b></td>
				<td><b>Yr2</b></td>
				<td><b>Yr3</b></td>
				<td><b>Yr4</b></td>
				<td><b>Yr5</b></td>
				<td><b>Yr6</b></td>
				<td><b>Loy</b></td>
				<td><b>PFW</b></td>
				<td><b>PT</b></td>
				<td><b>Sec</b></td>
				<td><b>Trad</b></td>
			</tr>
		</thead>
		<tbody>";

    $resultFreeAgentsNotOnUserTeam = $db->sql_query("SELECT * FROM ibl_plr WHERE teamname!='$team->name' AND retired='0' ORDER BY ordinal ASC");
    foreach ($resultFreeAgentsNotOnUserTeam as $playerRow) {
        $player = Player::withPlrRow($db, $playerRow);

        $yearPlayerIsFreeAgent = $player->draftYear + $player->yearsOfExperience + $player->contractTotalYears - $player->contractCurrentYear;
        if ($yearPlayerIsFreeAgent == $season->endingYear) {
            $playerDemands = $db->sql_fetchrow($player->getFreeAgencyDemands());
            $year1PlayerDemands = $playerDemands['dem1'];
            $year2PlayerDemands = $playerDemands['dem2'];
            $year3PlayerDemands = $playerDemands['dem3'];
            $year4PlayerDemands = $playerDemands['dem4'];
            $year5PlayerDemands = $playerDemands['dem5'];
            $year6PlayerDemands = $playerDemands['dem6'];

            echo "<tr>
                <td>";

            if ($rosterspots1 > 0) {
                echo "<a href=\"modules.php?name=Free_Agency&pa=negotiate&pid=$player->playerID\">Negotiate</a>";
            }

            echo "</td>
				<td>$player->position</td>
				<td><a href=\"modules.php?name=Player&pa=showpage&pid=$player->playerID\">$player->name</a></td>
				<td><a href=\"modules.php?name=Team&op=team&teamID=$player->teamID\">$player->teamName</a></td>
                <td>$player->age</td>
                <td>$player->ratingFieldGoalAttempts</td>
                <td>$player->ratingFieldGoalPercentage</td>
                <td>$player->ratingFreeThrowAttempts</td>
                <td>$player->ratingFreeThrowPercentage</td>
                <td>$player->ratingThreePointAttempts</td>
                <td>$player->ratingThreePointPercentage</td>
                <td>$player->ratingOffensiveRebounds</td>
                <td>$player->ratingDefensiveRebounds</td>
                <td>$player->ratingAssists</td>
                <td>$player->ratingSteals</td>
                <td>$player->ratingTurnovers</td>
                <td>$player->ratingBlocks</td>
                <td>$player->ratingFouls</td>
                <td>$player->ratingOutsideOffense</td>
                <td>$player->ratingDriveOffense</td>
                <td>$player->ratingPostOffense</td>
                <td>$player->ratingTransitionOffense</td>
                <td>$player->ratingOutsideDefense</td>
                <td>$player->ratingDriveDefense</td>
                <td>$player->ratingPostDefense</td>
                <td>$player->ratingTransitionDefense</td>
                <td>$player->ratingTalent</td>
                <td>$player->ratingSkill</td>
                <td>$player->ratingIntangibles</td>";

            if ($player->yearsOfExperience > 0) {
                echo "
                <td>$year1PlayerDemands</td>
				<td>$year2PlayerDemands</td>
				<td>$year3PlayerDemands</td>
				<td>$year4PlayerDemands</td>
				<td>$year5PlayerDemands</td>
				<td>$year6PlayerDemands</td>";
            } else {
                // Limit undrafted rookie FA contracts to two years by only displaying their demands for years 3 and 4
                // this is hacky and assumes that the demands table always contains demands for years 3 and 4 instead of recalculating demands appropriately
                echo "
                <td>$year3PlayerDemands</td>
				<td>$year4PlayerDemands</td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>";
            }

            echo "
                <td>$player->freeAgencyLoyalty</td>
                <td>$player->freeAgencyPlayForWinner</td>
                <td>$player->freeAgencyPlayingTime</td>
                <td>$player->freeAgencySecurity</td>
                <td>$player->freeAgencyTradition</td>
			</tr>";
        }
    }

    // ==== END INSERT OF ALL OTHER FREE AGENTS

    echo "</table>";

    CloseTable();
    Nuke\Footer::footer();
}

// === START NEGOTIATE FUNCTION ===
function negotiate($pid)
{
    global $prefix, $db, $cookie;
    $sharedFunctions = new Shared($db);

    $pid = intval($pid);

    $sql2 = "SELECT * FROM " . $prefix . "_users WHERE username='$cookie[1]'";
    $result2 = $db->sql_query($sql2);
    $userinfo = $db->sql_fetchrow($result2);

    $userteam = $userinfo['user_ibl_team'];
    $teamID = $sharedFunctions->getTidFromTeamname($userteam); // This function now returns an integer

    $exceptioninfo = $db->sql_fetchrow($db->sql_query("SELECT * FROM ibl_team_info WHERE team_name='$userteam'"));

    $HasMLE = $exceptioninfo['HasMLE'];
    $HasLLE = $exceptioninfo['HasLLE'];

    $playerinfo = $db->sql_fetchrow($db->sql_query("SELECT * FROM ibl_plr WHERE pid='$pid'"));

    $player_name = $playerinfo['name'];
    $player_pos = $playerinfo['pos'];
    $player_team_name = $playerinfo['teamname'];

    Nuke\Header::header();
    OpenTable();

    $player_exp = $playerinfo['exp'];
    $player_bird = $playerinfo['bird'];

    $offer1 = 0;
    $offer2 = 0;
    $offer3 = 0;
    $offer4 = 0;
    $offer5 = 0;
    $offer6 = 0;

    echo "<b>$player_pos $player_name</b> - Contract Demands:
	<br>";

    $demands = $db->sql_fetchrow($db->sql_query("SELECT * FROM ibl_demands WHERE name='$player_name'"));
    $dem1 = $demands['dem1'];
    $dem2 = $demands['dem2'];
    $dem3 = $demands['dem3'];
    $dem4 = $demands['dem4'];
    $dem5 = $demands['dem5'];
    $dem6 = $demands['dem6'];

    $millionsatposition = $db->sql_query("SELECT * FROM ibl_plr WHERE teamname='$userteam' AND pos='$player_pos' AND name!='$player_name'");

    // LOOP TO GET MILLIONS COMMITTED AT POSITION

    $tf_millions = 0;

    while ($millionscounter = $db->sql_fetchrow($millionsatposition)) {
        $millionscy = $millionscounter['cy'];
        $millionscy1 = $millionscounter['cy1'];
        $millionscy2 = $millionscounter['cy2'];
        $millionscy3 = $millionscounter['cy3'];
        $millionscy4 = $millionscounter['cy4'];
        $millionscy5 = $millionscounter['cy5'];
        $millionscy6 = $millionscounter['cy6'];

        // LOOK AT SALARY COMMITTED IN PROPER YEAR

        if ($millionscy == 0) {
            $tf_millions += $millionscy1;
        }

        if ($millionscy == 1) {
            $tf_millions += $millionscy2;
        }

        if ($millionscy == 2) {
            $tf_millions += $millionscy3;
        }

        if ($millionscy == 3) {
            $tf_millions += $millionscy4;
        }

        if ($millionscy == 4) {
            $tf_millions += $millionscy5;
        }

        if ($millionscy == 5) {
            $tf_millions += $millionscy6;
        }

    }

    // END LOOP

    $demyrs = 6;
    if ($dem6 == 0) {
        $demyrs = 5;
        if ($dem5 == 0) {
            $demyrs = 4;
            if ($dem4 == 0) {
                $demyrs = 3;
                if ($dem3 == 0) {
                    $demyrs = 2;
                    if ($dem2 == 0) {
                        $demyrs = 1;
                    }
                }
            }
        }
    }

    $demtot = round(($dem1 + $dem2 + $dem3 + $dem4 + $dem5 + $dem6) / 100, 2);

    if ($player_exp > 0) {
        $demand_display = $dem1;
        if ($dem2 != 0) {
            $demand_display = $demand_display . "</td><td>" . $dem2;
        }

        if ($dem3 != 0) {
            $demand_display = $demand_display . "</td><td>" . $dem3;
        }

        if ($dem4 != 0) {
            $demand_display = $demand_display . "</td><td>" . $dem4;
        }

        if ($dem5 != 0) {
            $demand_display = $demand_display . "</td><td>" . $dem5;
        }

        if ($dem6 != 0) {
            $demand_display = $demand_display . "</td><td>" . $dem6;
        }

        $demand_display .= "</td><td></td>";
    } else {
        // Limit undrafted rookie FA contracts to two years by only displaying their demands for years 3 and 4
        // this is hacky and assumes that the demands table always contains demands for years 3 and 4 instead of recalculating demands appropriately
        $demand_display = $dem3;
        if ($dem4 != 0) {
            $demand_display = $demand_display . "</td><td>" . $dem4;
        }

        $demand_display .= "</td><td></td>";
    }

    // LOOP TO GET SOFT CAP SPACE

    $capnumber = League::SOFT_CAP_MAX;
    $capnumber2 = League::SOFT_CAP_MAX;
    $capnumber3 = League::SOFT_CAP_MAX;
    $capnumber4 = League::SOFT_CAP_MAX;
    $capnumber5 = League::SOFT_CAP_MAX;
    $capnumber6 = League::SOFT_CAP_MAX;

    $rosterspots = 15;

    $capquery = "SELECT * FROM ibl_plr WHERE (tid=$teamID AND retired='0') ORDER BY ordinal ASC;";
    $capresult = $db->sql_query($capquery);

    while ($capdecrementer = $db->sql_fetchrow($capresult)) {
        $ordinal = $capdecrementer['ordinal'];
        $capcy = $capdecrementer['cy'];
        $capcyt = $capdecrementer['cyt'];
        $capcy1 = $capdecrementer['cy1'];
        $capcy2 = $capdecrementer['cy2'];
        $capcy3 = $capdecrementer['cy3'];
        $capcy4 = $capdecrementer['cy4'];
        $capcy5 = $capdecrementer['cy5'];
        $capcy6 = $capdecrementer['cy6'];

        // LOOK AT SALARY COMMITTED IN PROPER YEAR

        if ($capcy == 0) {
            $capnumber -= $capcy1;
            $capnumber2 -= $capcy2;
            $capnumber3 -= $capcy3;
            $capnumber4 -= $capcy4;
            $capnumber5 -= $capcy5;
            $capnumber6 -= $capcy6;
        }
        if ($capcy == 1) {
            $capnumber -= $capcy2;
            $capnumber2 -= $capcy3;
            $capnumber3 -= $capcy4;
            $capnumber4 -= $capcy5;
            $capnumber5 -= $capcy6;
        }
        if ($capcy == 2) {
            $capnumber -= $capcy3;
            $capnumber2 -= $capcy4;
            $capnumber3 -= $capcy5;
            $capnumber4 -= $capcy6;
        }
        if ($capcy == 3) {
            $capnumber -= $capcy4;
            $capnumber2 -= $capcy5;
            $capnumber3 -= $capcy6;
        }
        if ($capcy == 4) {
            $capnumber -= $capcy5;
            $capnumber2 -= $capcy6;
        }
        if ($capcy == 5) {
            $capnumber -= $capcy6;
        }

        if ($capcy != $capcyt && $ordinal <= JSB::WAIVERS_ORDINAL) {
            $rosterspots -= 1;
        }

    }

    $capquery2 = "SELECT * FROM ibl_fa_offers WHERE team='$userteam'";
    $capresult2 = $db->sql_query($capquery2);

    while ($capdecrementer2 = $db->sql_fetchrow($capresult2)) {
        $offer1 = $capdecrementer2['offer1'];
        $offer2 = $capdecrementer2['offer2'];
        $offer3 = $capdecrementer2['offer3'];
        $offer4 = $capdecrementer2['offer4'];
        $offer5 = $capdecrementer2['offer5'];
        $offer6 = $capdecrementer2['offer6'];
        $capnumber -= $offer1;
        $capnumber2 -= $offer2;
        $capnumber3 -= $offer3;
        $capnumber4 -= $offer4;
        $capnumber5 -= $offer5;
        $capnumber6 -= $offer6;
        $offer1 = 0;

        $rosterspots = $rosterspots - 1;
    }

    $hardcapnumber = $capnumber + 2000;
    $hardcapnumber2 = $capnumber2 + 2000;
    $hardcapnumber3 = $capnumber3 + 2000;
    $hardcapnumber4 = $capnumber4 + 2000;
    $hardcapnumber5 = $capnumber5 + 2000;
    $hardcapnumber6 = $capnumber6 + 2000;

    // END LOOP

    $offergrabber = $db->sql_fetchrow($db->sql_query("SELECT * FROM ibl_fa_offers WHERE team='$userteam' AND name='$player_name'"));

    $offer1 = $offergrabber['offer1'];
    $offer2 = $offergrabber['offer2'];
    $offer3 = $offergrabber['offer3'];
    $offer4 = $offergrabber['offer4'];
    $offer5 = $offergrabber['offer5'];
    $offer6 = $offergrabber['offer6'];

    if ($offer1 == 0) {
        $prefill1 = "";
        $prefill2 = "";
        $prefill3 = "";
        $prefill4 = "";
        $prefill5 = "";
        $prefill6 = "";
    } else {
        $prefill1 = $offer1;
        $prefill2 = $offer2;
        $prefill3 = $offer3;
        $prefill4 = $offer4;
        $prefill5 = $offer5;
        $prefill6 = $offer6;
    }

    if ($player_exp > 9) {
        $vetmin = 103;
        $maxstartsat = 1451;
    } elseif ($player_exp > 8) {
        $vetmin = 100;
        $maxstartsat = 1275;
    } elseif ($player_exp > 7) {
        $vetmin = 89;
        $maxstartsat = 1275;
    } elseif ($player_exp > 6) {
        $vetmin = 82;
        $maxstartsat = 1275;
    } elseif ($player_exp > 5) {
        $vetmin = 76;
        $maxstartsat = 1063;
    } elseif ($player_exp > 4) {
        $vetmin = 70;
        $maxstartsat = 1063;
    } elseif ($player_exp > 3) {
        $vetmin = 64;
        $maxstartsat = 1063;
    } elseif ($player_exp > 2) {
        $vetmin = 61;
        $maxstartsat = 1063;
    } elseif ($player_exp > 1) {
        $vetmin = 51;
        $maxstartsat = 1063;
    } else {
        $vetmin = 35;
        $maxstartsat = 1063;
    }

    // ==== CALCULATE MAX OFFER ====
    $Offer_max_increase = round($maxstartsat * 0.1, 0);
    $Offer_max_increase_bird = round($maxstartsat * 0.125, 0);

    $maxstartsat2 = $maxstartsat + $Offer_max_increase;
    $maxstartsat3 = $maxstartsat2 + $Offer_max_increase;
    $maxstartsat4 = $maxstartsat3 + $Offer_max_increase;
    $maxstartsat5 = $maxstartsat4 + $Offer_max_increase;
    $maxstartsat6 = $maxstartsat5 + $Offer_max_increase;

    $maxstartsatbird2 = $maxstartsat + $Offer_max_increase_bird;
    $maxstartsatbird3 = $maxstartsatbird2 + $Offer_max_increase_bird;
    $maxstartsatbird4 = $maxstartsatbird3 + $Offer_max_increase_bird;
    $maxstartsatbird5 = $maxstartsatbird4 + $Offer_max_increase_bird;
    $maxstartsatbird6 = $maxstartsatbird5 + $Offer_max_increase_bird;

    echo "<img align=left src=\"images/player/$pid.jpg\">";

    echo "Here are my demands (note these are not adjusted for your team's attributes; I will adjust the offer you make to me accordingly):";

    if ($rosterspots < 1 and $offer1 == 0) {
        echo "<table cellspacing=0 border=1><tr><td colspan=8>Sorry, you have no roster spots remaining and cannot offer me a contract!</td>";
    } else {
        echo "<table cellspacing=0 border=1><tr><td>My demands are:</td><td>$demand_display</td></tr>

		<form name=\"FAOffer\" method=\"post\" action=\"/ibl5/modules/Free_Agency/freeagentoffer.php\">
		<tr><td>Please enter your offer in this row:</td><td>";
        if ($player_exp > 0) {
            echo "<INPUT TYPE=\"number\" style=\"width: 4em\" NAME=\"offeryear1\" SIZE=\"4\" VALUE=\"$prefill1\"></td><td>
                  <INPUT TYPE=\"number\" style=\"width: 4em\" NAME=\"offeryear2\" SIZE=\"4\" VALUE=\"$prefill2\"></td><td>
                  <INPUT TYPE=\"number\" style=\"width: 4em\" NAME=\"offeryear3\" SIZE=\"4\" VALUE=\"$prefill3\"></td><td>
                  <INPUT TYPE=\"number\" style=\"width: 4em\" NAME=\"offeryear4\" SIZE=\"4\" VALUE=\"$prefill4\"></td><td>
                  <INPUT TYPE=\"number\" style=\"width: 4em\" NAME=\"offeryear5\" SIZE=\"4\" VALUE=\"$prefill5\"></td><td>
                  <INPUT TYPE=\"number\" style=\"width: 4em\" NAME=\"offeryear6\" SIZE=\"4\" VALUE=\"$prefill6\"></td>";
        } else { // Limit undrafted rookie FA contracts to two years
            echo "<INPUT TYPE=\"number\" style=\"width: 4em\" NAME=\"offeryear1\" SIZE=\"4\" VALUE=\"$prefill3\"></td><td>
			      <INPUT TYPE=\"number\" style=\"width: 4em\" NAME=\"offeryear2\" SIZE=\"4\" VALUE=\"$prefill4\"></td>";
        }
        $amendedCapSpaceYear1 = $capnumber + $offer1;
        echo "<input type=\"hidden\" name=\"amendedCapSpaceYear1\" value=\"$amendedCapSpaceYear1\">
              <input type=\"hidden\" name=\"capnumber\" value=\"$capnumber\">
              <input type=\"hidden\" name=\"capnumber2\" value=\"$capnumber2\">
              <input type=\"hidden\" name=\"capnumber3\" value=\"$capnumber3\">
              <input type=\"hidden\" name=\"capnumber4\" value=\"$capnumber4\">
              <input type=\"hidden\" name=\"capnumber5\" value=\"$capnumber5\">
              <input type=\"hidden\" name=\"capnumber6\" value=\"$capnumber6\">
              <input type=\"hidden\" name=\"demtot\" value=\"$demtot\">
              <input type=\"hidden\" name=\"demyrs\" value=\"$demyrs\">
              <input type=\"hidden\" name=\"max\" value=\"$maxstartsat\">
              <input type=\"hidden\" name=\"teamname\" value=\"$userteam\">
              <input type=\"hidden\" name=\"player_teamname\" value=\"$player_team_name\">
              <input type=\"hidden\" name=\"playername\" value=\"$player_name\">
              <input type=\"hidden\" name=\"bird\" value=\"$player_bird\">
              <input type=\"hidden\" name=\"vetmin\" value=\"$vetmin\">
              <input type=\"hidden\" name=\"MLEyrs\" value=\"0\">
	    <td>  <input type=\"submit\" value=\"Offer/Amend Free Agent Contract!\"></form></td></tr>

		<tr><td colspan=8><center><b>MAX SALARY OFFERS:</b></center></td></tr>

		<td>Max Level Contract 10%(click the button that corresponds to the final year you wish to offer):</td>

		<td>
            <form name=\"FAOffer\" method=\"post\" action=\"/ibl5/modules/Free_Agency/freeagentoffer.php\">
                <input type=\"hidden\" name=\"offeryear1\" value=\"$maxstartsat\">
                <input type=\"hidden\" name=\"amendedCapSpaceYear1\" value=\"$amendedCapSpaceYear1\">
                <input type=\"hidden\" name=\"capnumber\" value=\"$capnumber\">
                <input type=\"hidden\" name=\"demtot\" value=\"$demtot\">
                <input type=\"hidden\" name=\"demyrs\" value=\"$demyrs\">
                <input type=\"hidden\" name=\"max\" value=\"$maxstartsat\">
                <input type=\"hidden\" name=\"teamname\" value=\"$userteam\">
                <input type=\"hidden\" name=\"player_teamname\" value=\"$player_team_name\">
                <input type=\"hidden\" name=\"playername\" value=\"$player_name\">
                <input type=\"hidden\" name=\"bird\" value=\"$player_bird\">
                <input type=\"hidden\" name=\"vetmin\" value=\"$vetmin\">
                <input type=\"hidden\" name=\"MLEyrs\" value=\"0\">
                <input type=\"submit\" value=\"$maxstartsat\">
            </form>
        </td>

		<td>
            <form name=\"FAOffer\" method=\"post\" action=\"/ibl5/modules/Free_Agency/freeagentoffer.php\">
                <input type=\"hidden\" name=\"offeryear1\" value=\"$maxstartsat\">
                <input type=\"hidden\" name=\"offeryear2\" value=\"$maxstartsat2\">
                <input type=\"hidden\" name=\"amendedCapSpaceYear1\" value=\"$amendedCapSpaceYear1\">
                <input type=\"hidden\" name=\"capnumber\" value=\"$capnumber\">
                <input type=\"hidden\" name=\"capnumber2\" value=\"$capnumber2\">
                <input type=\"hidden\" name=\"demtot\" value=\"$demtot\">
                <input type=\"hidden\" name=\"demyrs\" value=\"$demyrs\">
                <input type=\"hidden\" name=\"max\" value=\"$maxstartsat\">
                <input type=\"hidden\" name=\"teamname\" value=\"$userteam\">
                <input type=\"hidden\" name=\"player_teamname\" value=\"$player_team_name\">
                <input type=\"hidden\" name=\"playername\" value=\"$player_name\">
                <input type=\"hidden\" name=\"bird\" value=\"$player_bird\">
                <input type=\"hidden\" name=\"vetmin\" value=\"$vetmin\">
                <input type=\"hidden\" name=\"MLEyrs\" value=\"0\">
                <input type=\"submit\" value=\"$maxstartsat2\">
            </form>
        </td>";

        if ($player_exp > 0) {
            echo "<td>
                <form name=\"FAOffer\" method=\"post\" action=\"/ibl5/modules/Free_Agency/freeagentoffer.php\">
                    <input type=\"hidden\" name=\"offeryear1\" value=\"$maxstartsat\">
                    <input type=\"hidden\" name=\"offeryear2\" value=\"$maxstartsat2\">
                    <input type=\"hidden\" name=\"offeryear3\" value=\"$maxstartsat3\">
                    <input type=\"hidden\" name=\"amendedCapSpaceYear1\" value=\"$amendedCapSpaceYear1\">
                    <input type=\"hidden\" name=\"capnumber\" value=\"$capnumber\">
                    <input type=\"hidden\" name=\"capnumber2\" value=\"$capnumber2\">
                    <input type=\"hidden\" name=\"capnumber3\" value=\"$capnumber3\">
                    <input type=\"hidden\" name=\"demtot\" value=\"$demtot\">
                    <input type=\"hidden\" name=\"demyrs\" value=\"$demyrs\">
                    <input type=\"hidden\" name=\"max\" value=\"$maxstartsat\">
                    <input type=\"hidden\" name=\"teamname\" value=\"$userteam\">
                    <input type=\"hidden\" name=\"player_teamname\" value=\"$player_team_name\">
                    <input type=\"hidden\" name=\"playername\" value=\"$player_name\">
                    <input type=\"hidden\" name=\"bird\" value=\"$player_bird\">
                    <input type=\"hidden\" name=\"vetmin\" value=\"$vetmin\">
                    <input type=\"hidden\" name=\"MLEyrs\" value=\"0\">
                    <input type=\"submit\" value=\"$maxstartsat3\">
                </form>
            </td>

			<td>
                <form name=\"FAOffer\" method=\"post\" action=\"/ibl5/modules/Free_Agency/freeagentoffer.php\">
                    <input type=\"hidden\" name=\"offeryear1\" value=\"$maxstartsat\">
                    <input type=\"hidden\" name=\"offeryear2\" value=\"$maxstartsat2\">
                    <input type=\"hidden\" name=\"offeryear3\" value=\"$maxstartsat3\">
                    <input type=\"hidden\" name=\"offeryear4\" value=\"$maxstartsat4\">
                    <input type=\"hidden\" name=\"amendedCapSpaceYear1\" value=\"$amendedCapSpaceYear1\">
                    <input type=\"hidden\" name=\"capnumber\" value=\"$capnumber\">
                    <input type=\"hidden\" name=\"capnumber2\" value=\"$capnumber2\">
                    <input type=\"hidden\" name=\"capnumber3\" value=\"$capnumber3\">
                    <input type=\"hidden\" name=\"capnumber4\" value=\"$capnumber4\">
                    <input type=\"hidden\" name=\"demtot\" value=\"$demtot\">
                    <input type=\"hidden\" name=\"demyrs\" value=\"$demyrs\">
                    <input type=\"hidden\" name=\"max\" value=\"$maxstartsat\">
                    <input type=\"hidden\" name=\"teamname\" value=\"$userteam\">
                    <input type=\"hidden\" name=\"player_teamname\" value=\"$player_team_name\">
                    <input type=\"hidden\" name=\"playername\" value=\"$player_name\">
                    <input type=\"hidden\" name=\"bird\" value=\"$player_bird\">
                    <input type=\"hidden\" name=\"vetmin\" value=\"$vetmin\">
                    <input type=\"hidden\" name=\"MLEyrs\" value=\"0\">
                    <input type=\"submit\" value=\"$maxstartsat4\">
                </form>
            </td>

			<td>
                <form name=\"FAOffer\" method=\"post\" action=\"/ibl5/modules/Free_Agency/freeagentoffer.php\">
                    <input type=\"hidden\" name=\"offeryear1\" value=\"$maxstartsat\">
                    <input type=\"hidden\" name=\"offeryear2\" value=\"$maxstartsat2\">
                    <input type=\"hidden\" name=\"offeryear3\" value=\"$maxstartsat3\">
                    <input type=\"hidden\" name=\"offeryear4\" value=\"$maxstartsat4\">
                    <input type=\"hidden\" name=\"offeryear5\" value=\"$maxstartsat5\">
                    <input type=\"hidden\" name=\"amendedCapSpaceYear1\" value=\"$amendedCapSpaceYear1\">
                    <input type=\"hidden\" name=\"capnumber\" value=\"$capnumber\">
                    <input type=\"hidden\" name=\"capnumber2\" value=\"$capnumber2\">
                    <input type=\"hidden\" name=\"capnumber3\" value=\"$capnumber3\">
                    <input type=\"hidden\" name=\"capnumber4\" value=\"$capnumber4\">
                    <input type=\"hidden\" name=\"capnumber5\" value=\"$capnumber5\">
                    <input type=\"hidden\" name=\"demtot\" value=\"$demtot\">
                    <input type=\"hidden\" name=\"demyrs\" value=\"$demyrs\">
                    <input type=\"hidden\" name=\"max\" value=\"$maxstartsat\">
                    <input type=\"hidden\" name=\"teamname\" value=\"$userteam\">
                    <input type=\"hidden\" name=\"player_teamname\" value=\"$player_team_name\">
                    <input type=\"hidden\" name=\"playername\" value=\"$player_name\">
                    <input type=\"hidden\" name=\"bird\" value=\"$player_bird\">
                    <input type=\"hidden\" name=\"vetmin\" value=\"$vetmin\">
                    <input type=\"hidden\" name=\"MLEyrs\" value=\"0\">
                    <input type=\"submit\" value=\"$maxstartsat5\">
                </form>
            </td>

			<td>
                <form name=\"FAOffer\" method=\"post\" action=\"/ibl5/modules/Free_Agency/freeagentoffer.php\">
                    <input type=\"hidden\" name=\"offeryear1\" value=\"$maxstartsat\">
                    <input type=\"hidden\" name=\"offeryear2\" value=\"$maxstartsat2\">
                    <input type=\"hidden\" name=\"offeryear3\" value=\"$maxstartsat3\">
                    <input type=\"hidden\" name=\"offeryear4\" value=\"$maxstartsat4\">
                    <input type=\"hidden\" name=\"offeryear5\" value=\"$maxstartsat5\">
                    <input type=\"hidden\" name=\"offeryear6\" value=\"$maxstartsat6\">
                    <input type=\"hidden\" name=\"amendedCapSpaceYear1\" value=\"$amendedCapSpaceYear1\">
                    <input type=\"hidden\" name=\"capnumber\" value=\"$capnumber\">
                    <input type=\"hidden\" name=\"capnumber2\" value=\"$capnumber2\">
                    <input type=\"hidden\" name=\"capnumber3\" value=\"$capnumber3\">
                    <input type=\"hidden\" name=\"capnumber4\" value=\"$capnumber4\">
                    <input type=\"hidden\" name=\"capnumber5\" value=\"$capnumber5\">
                    <input type=\"hidden\" name=\"capnumber6\" value=\"$capnumber6\">
                    <input type=\"hidden\" name=\"demtot\" value=\"$demtot\">
                    <input type=\"hidden\" name=\"demyrs\" value=\"$demyrs\">
                    <input type=\"hidden\" name=\"max\" value=\"$maxstartsat\">
                    <input type=\"hidden\" name=\"teamname\" value=\"$userteam\">
                    <input type=\"hidden\" name=\"player_teamname\" value=\"$player_team_name\">
                    <input type=\"hidden\" name=\"playername\" value=\"$player_name\">
                    <input type=\"hidden\" name=\"bird\" value=\"$player_bird\">
                    <input type=\"hidden\" name=\"vetmin\" value=\"$vetmin\">
                    <input type=\"hidden\" name=\"MLEyrs\" value=\"0\">
                    <input type=\"submit\" value=\"$maxstartsat6\">
                </form>
            </td>";
        } else { // Limit undrafted rookie FA contracts to two years
            echo "";
        }

        echo "<td></td></tr>";

        // ===== CHECK TO SEE IF MAX BIRD RIGHTS IS AVAILABLE =====

        if ($player_bird > 2 && $player_team_name == $userteam) {
            echo "<tr><td><b>Max Bird Level Contract 12.5%(click the button that corresponds to the final year you wish to offer):</b></td>
			<td>
                <form name=\"FAOffer\" method=\"post\" action=\"/ibl5/modules/Free_Agency/freeagentoffer.php\">
                    <input type=\"hidden\" name=\"offeryear1\" value=\"$maxstartsat\">
                    <input type=\"hidden\" name=\"amendedCapSpaceYear1\" value=\"$amendedCapSpaceYear1\">
                    <input type=\"hidden\" name=\"capnumber\" value=\"$capnumber\">
                    <input type=\"hidden\" name=\"demtot\" value=\"$demtot\">
                    <input type=\"hidden\" name=\"demyrs\" value=\"$demyrs\">
                    <input type=\"hidden\" name=\"max\" value=\"$maxstartsat\">
                    <input type=\"hidden\" name=\"teamname\" value=\"$userteam\">
                    <input type=\"hidden\" name=\"player_teamname\" value=\"$player_team_name\">
                    <input type=\"hidden\" name=\"playername\" value=\"$player_name\">
                    <input type=\"hidden\" name=\"bird\" value=\"$player_bird\">
                    <input type=\"hidden\" name=\"vetmin\" value=\"$vetmin\">
                    <input type=\"hidden\" name=\"MLEyrs\" value=\"0\">
                    <input type=\"submit\" value=\"$maxstartsat\">
                </form>
            </td>

			<td>
                <form name=\"FAOffer\" method=\"post\" action=\"/ibl5/modules/Free_Agency/freeagentoffer.php\">
                    <input type=\"hidden\" name=\"offeryear1\" value=\"$maxstartsat\">
                    <input type=\"hidden\" name=\"offeryear2\" value=\"$maxstartsatbird2\">
                    <input type=\"hidden\" name=\"amendedCapSpaceYear1\" value=\"$amendedCapSpaceYear1\">
                    <input type=\"hidden\" name=\"capnumber\" value=\"$capnumber\">
                    <input type=\"hidden\" name=\"capnumber2\" value=\"$capnumber2\">
                    <input type=\"hidden\" name=\"demtot\" value=\"$demtot\">
                    <input type=\"hidden\" name=\"demyrs\" value=\"$demyrs\">
                    <input type=\"hidden\" name=\"max\" value=\"$maxstartsat\">
                    <input type=\"hidden\" name=\"teamname\" value=\"$userteam\">
                    <input type=\"hidden\" name=\"player_teamname\" value=\"$player_team_name\">
                    <input type=\"hidden\" name=\"playername\" value=\"$player_name\">
                    <input type=\"hidden\" name=\"bird\" value=\"$player_bird\">
                    <input type=\"hidden\" name=\"vetmin\" value=\"$vetmin\">
                    <input type=\"hidden\" name=\"MLEyrs\" value=\"0\">
                    <input type=\"submit\" value=\"$maxstartsatbird2\">
                </form>
            </td>

			<td>
                <form name=\"FAOffer\" method=\"post\" action=\"/ibl5/modules/Free_Agency/freeagentoffer.php\">
                    <input type=\"hidden\" name=\"offeryear1\" value=\"$maxstartsat\">
                    <input type=\"hidden\" name=\"offeryear2\" value=\"$maxstartsatbird2\">
                    <input type=\"hidden\" name=\"offeryear3\" value=\"$maxstartsatbird3\">
                    <input type=\"hidden\" name=\"amendedCapSpaceYear1\" value=\"$amendedCapSpaceYear1\">
                    <input type=\"hidden\" name=\"capnumber\" value=\"$capnumber\">
                    <input type=\"hidden\" name=\"capnumber2\" value=\"$capnumber2\">
                    <input type=\"hidden\" name=\"capnumber3\" value=\"$capnumber3\">
                    <input type=\"hidden\" name=\"demtot\" value=\"$demtot\">
                    <input type=\"hidden\" name=\"demyrs\" value=\"$demyrs\">
                    <input type=\"hidden\" name=\"max\" value=\"$maxstartsat\">
                    <input type=\"hidden\" name=\"teamname\" value=\"$userteam\">
                    <input type=\"hidden\" name=\"player_teamname\" value=\"$player_team_name\">
                    <input type=\"hidden\" name=\"playername\" value=\"$player_name\">
                    <input type=\"hidden\" name=\"bird\" value=\"$player_bird\">
                    <input type=\"hidden\" name=\"vetmin\" value=\"$vetmin\">
                    <input type=\"hidden\" name=\"MLEyrs\" value=\"0\">
                    <input type=\"submit\" value=\"$maxstartsatbird3\">
                </form>
            </td>

			<td>
                <form name=\"FAOffer\" method=\"post\" action=\"/ibl5/modules/Free_Agency/freeagentoffer.php\">
                    <input type=\"hidden\" name=\"offeryear1\" value=\"$maxstartsat\">
                    <input type=\"hidden\" name=\"offeryear2\" value=\"$maxstartsatbird2\">
                    <input type=\"hidden\" name=\"offeryear3\" value=\"$maxstartsatbird3\">
                    <input type=\"hidden\" name=\"offeryear4\" value=\"$maxstartsatbird4\">
                    <input type=\"hidden\" name=\"amendedCapSpaceYear1\" value=\"$amendedCapSpaceYear1\">
                    <input type=\"hidden\" name=\"capnumber\" value=\"$capnumber\">
                    <input type=\"hidden\" name=\"capnumber2\" value=\"$capnumber2\">
                    <input type=\"hidden\" name=\"capnumber3\" value=\"$capnumber3\">
                    <input type=\"hidden\" name=\"capnumber4\" value=\"$capnumber4\">
                    <input type=\"hidden\" name=\"demtot\" value=\"$demtot\">
                    <input type=\"hidden\" name=\"demyrs\" value=\"$demyrs\">
                    <input type=\"hidden\" name=\"max\" value=\"$maxstartsat\">
                    <input type=\"hidden\" name=\"teamname\" value=\"$userteam\">
                    <input type=\"hidden\" name=\"player_teamname\" value=\"$player_team_name\">
                    <input type=\"hidden\" name=\"playername\" value=\"$player_name\">
                    <input type=\"hidden\" name=\"bird\" value=\"$player_bird\">
                    <input type=\"hidden\" name=\"vetmin\" value=\"$vetmin\">
                    <input type=\"hidden\" name=\"MLEyrs\" value=\"0\">
                    <input type=\"submit\" value=\"$maxstartsatbird4\">
                </form>
            </td>

			<td>
                <form name=\"FAOffer\" method=\"post\" action=\"/ibl5/modules/Free_Agency/freeagentoffer.php\">
                    <input type=\"hidden\" name=\"offeryear1\" value=\"$maxstartsat\">
                    <input type=\"hidden\" name=\"offeryear2\" value=\"$maxstartsatbird2\">
                    <input type=\"hidden\" name=\"offeryear3\" value=\"$maxstartsatbird3\">
                    <input type=\"hidden\" name=\"offeryear4\" value=\"$maxstartsatbird4\">
                    <input type=\"hidden\" name=\"offeryear5\" value=\"$maxstartsatbird5\">
                    <input type=\"hidden\" name=\"amendedCapSpaceYear1\" value=\"$amendedCapSpaceYear1\">
                    <input type=\"hidden\" name=\"capnumber\" value=\"$capnumber\">
                    <input type=\"hidden\" name=\"capnumber2\" value=\"$capnumber2\">
                    <input type=\"hidden\" name=\"capnumber3\" value=\"$capnumber3\">
                    <input type=\"hidden\" name=\"capnumber4\" value=\"$capnumber4\">
                    <input type=\"hidden\" name=\"capnumber5\" value=\"$capnumber5\">
                    <input type=\"hidden\" name=\"demtot\" value=\"$demtot\">
                    <input type=\"hidden\" name=\"demyrs\" value=\"$demyrs\">
                    <input type=\"hidden\" name=\"max\" value=\"$maxstartsat\">
                    <input type=\"hidden\" name=\"teamname\" value=\"$userteam\">
                    <input type=\"hidden\" name=\"player_teamname\" value=\"$player_team_name\">
                    <input type=\"hidden\" name=\"playername\" value=\"$player_name\">
                    <input type=\"hidden\" name=\"bird\" value=\"$player_bird\">
                    <input type=\"hidden\" name=\"vetmin\" value=\"$vetmin\">
                    <input type=\"hidden\" name=\"MLEyrs\" value=\"0\">
                    <input type=\"submit\" value=\"$maxstartsatbird5\">
                </form>
            </td>

			<td>
                <form name=\"FAOffer\" method=\"post\" action=\"/ibl5/modules/Free_Agency/freeagentoffer.php\">
                    <input type=\"hidden\" name=\"offeryear1\" value=\"$maxstartsat\">
                    <input type=\"hidden\" name=\"offeryear2\" value=\"$maxstartsatbird2\">
                    <input type=\"hidden\" name=\"offeryear3\" value=\"$maxstartsatbird3\">
                    <input type=\"hidden\" name=\"offeryear4\" value=\"$maxstartsatbird4\">
                    <input type=\"hidden\" name=\"offeryear5\" value=\"$maxstartsatbird5\">
                    <input type=\"hidden\" name=\"offeryear6\" value=\"$maxstartsatbird6\">
                    <input type=\"hidden\" name=\"amendedCapSpaceYear1\" value=\"$amendedCapSpaceYear1\">
                    <input type=\"hidden\" name=\"capnumber\" value=\"$capnumber\">
                    <input type=\"hidden\" name=\"capnumber2\" value=\"$capnumber2\">
                    <input type=\"hidden\" name=\"capnumber3\" value=\"$capnumber3\">
                    <input type=\"hidden\" name=\"capnumber4\" value=\"$capnumber4\">
                    <input type=\"hidden\" name=\"capnumber5\" value=\"$capnumber5\">
                    <input type=\"hidden\" name=\"capnumber6\" value=\"$capnumber6\">
                    <input type=\"hidden\" name=\"demtot\" value=\"$demtot\">
                    <input type=\"hidden\" name=\"demyrs\" value=\"$demyrs\">
                    <input type=\"hidden\" name=\"max\" value=\"$maxstartsat\">
                    <input type=\"hidden\" name=\"teamname\" value=\"$userteam\">
                    <input type=\"hidden\" name=\"player_teamname\" value=\"$player_team_name\">
                    <input type=\"hidden\" name=\"playername\" value=\"$player_name\">
                    <input type=\"hidden\" name=\"bird\" value=\"$player_bird\">
                    <input type=\"hidden\" name=\"vetmin\" value=\"$vetmin\">
                    <input type=\"hidden\" name=\"MLEyrs\" value=\"0\">
                    <input type=\"submit\" value=\"$maxstartsatbird6\">
                </form>
            </td>";
        }

        echo "<tr><td colspan=8><center><b>SALARY CAP EXCEPTIONS:</b></center></td></tr>";

        // ===== CHECK TO SEE IF MLE IS AVAILABLE =====

        if ($HasMLE == 1) {
            $MLEoffers = $db->sql_numrows($db->sql_query("SELECT * FROM ibl_fa_offers WHERE MLE='1' AND team='$userteam'"));
            if ($MLEoffers == 0) {
                echo "<tr><td>Mid-Level Exception (click the button that corresponds to the final year you wish to offer):</td>

				<td>
                    <form name=\"FAOffer\" method=\"post\" action=\"/ibl5/modules/Free_Agency/freeagentoffer.php\">
                        <input type=\"hidden\" name=\"amendedCapSpaceYear1\" value=\"$amendedCapSpaceYear1\">
                        <input type=\"hidden\" name=\"capnumber\" value=\"$capnumber\">
                        <input type=\"hidden\" name=\"demtot\" value=\"$demtot\">
                        <input type=\"hidden\" name=\"demyrs\" value=\"$demyrs\">
                        <input type=\"hidden\" name=\"max\" value=\"$maxstartsat\">
                        <input type=\"hidden\" name=\"teamname\" value=\"$userteam\">
                        <input type=\"hidden\" name=\"player_teamname\" value=\"$player_team_name\">
                        <input type=\"hidden\" name=\"playername\" value=\"$player_name\">
                        <input type=\"hidden\" name=\"vetmin\" value=\"$vetmin\">
                        <input type=\"hidden\" name=\"MLEyrs\" value=\"1\">
                        <input type=\"submit\" value=\"450\">
                    </form>
                </td>

				<td>
                    <form name=\"FAOffer\" method=\"post\" action=\"/ibl5/modules/Free_Agency/freeagentoffer.php\">
                        <input type=\"hidden\" name=\"amendedCapSpaceYear1\" value=\"$amendedCapSpaceYear1\">
                        <input type=\"hidden\" name=\"capnumber\" value=\"$capnumber\">
                        <input type=\"hidden\" name=\"capnumber2\" value=\"$capnumber2\">
                        <input type=\"hidden\" name=\"demtot\" value=\"$demtot\">
                        <input type=\"hidden\" name=\"demyrs\" value=\"$demyrs\">
                        <input type=\"hidden\" name=\"max\" value=\"$maxstartsat\">
                        <input type=\"hidden\" name=\"teamname\" value=\"$userteam\">
                        <input type=\"hidden\" name=\"player_teamname\" value=\"$player_team_name\">
                        <input type=\"hidden\" name=\"playername\" value=\"$player_name\">
                        <input type=\"hidden\" name=\"MLEyrs\" value=\"2\">
                        <input type=\"submit\" value=\"495\">
                    </form>
                </td>";

                if ($player_exp > 0) {
                    echo "<td>
                        <form name=\"FAOffer\" method=\"post\" action=\"/ibl5/modules/Free_Agency/freeagentoffer.php\">
                            <input type=\"hidden\" name=\"amendedCapSpaceYear1\" value=\"$amendedCapSpaceYear1\">
                            <input type=\"hidden\" name=\"capnumber\" value=\"$capnumber\">
                            <input type=\"hidden\" name=\"capnumber2\" value=\"$capnumber2\">
                            <input type=\"hidden\" name=\"capnumber3\" value=\"$capnumber3\">
                            <input type=\"hidden\" name=\"demtot\" value=\"$demtot\">
                            <input type=\"hidden\" name=\"demyrs\" value=\"$demyrs\">
                            <input type=\"hidden\" name=\"max\" value=\"$maxstartsat\">
                            <input type=\"hidden\" name=\"teamname\" value=\"$userteam\">
                            <input type=\"hidden\" name=\"player_teamname\" value=\"$player_team_name\">
                            <input type=\"hidden\" name=\"playername\" value=\"$player_name\">
                            <input type=\"hidden\" name=\"vetmin\" value=\"$vetmin\">
                            <input type=\"hidden\" name=\"MLEyrs\" value=\"3\">
                            <input type=\"submit\" value=\"540\">
                        </form>
                    </td>

					<td>
                        <form name=\"FAOffer\" method=\"post\" action=\"/ibl5/modules/Free_Agency/freeagentoffer.php\">
                            <input type=\"hidden\" name=\"amendedCapSpaceYear1\" value=\"$amendedCapSpaceYear1\">
                            <input type=\"hidden\" name=\"capnumber\" value=\"$capnumber\">
                            <input type=\"hidden\" name=\"capnumber2\" value=\"$capnumber2\">
                            <input type=\"hidden\" name=\"capnumber3\" value=\"$capnumber3\">
                            <input type=\"hidden\" name=\"capnumber4\" value=\"$capnumber4\">
                            <input type=\"hidden\" name=\"demtot\" value=\"$demtot\">
                            <input type=\"hidden\" name=\"demyrs\" value=\"$demyrs\">
                            <input type=\"hidden\" name=\"max\" value=\"$maxstartsat\">
                            <input type=\"hidden\" name=\"teamname\" value=\"$userteam\">
                            <input type=\"hidden\" name=\"player_teamname\" value=\"$player_team_name\">
                            <input type=\"hidden\" name=\"playername\" value=\"$player_name\">
                            <input type=\"hidden\" name=\"vetmin\" value=\"$vetmin\">
                            <input type=\"hidden\" name=\"MLEyrs\" value=\"4\">
                            <input type=\"submit\" value=\"585\">
                        </form>
                    </td>

					<td>
                        <form name=\"FAOffer\" method=\"post\" action=\"/ibl5/modules/Free_Agency/freeagentoffer.php\">
                            <input type=\"hidden\" name=\"amendedCapSpaceYear1\" value=\"$amendedCapSpaceYear1\">
                            <input type=\"hidden\" name=\"capnumber\" value=\"$capnumber\">
                            <input type=\"hidden\" name=\"capnumber2\" value=\"$capnumber2\">
                            <input type=\"hidden\" name=\"capnumber3\" value=\"$capnumber3\">
                            <input type=\"hidden\" name=\"capnumber4\" value=\"$capnumber4\">
                            <input type=\"hidden\" name=\"capnumber5\" value=\"$capnumber5\">
                            <input type=\"hidden\" name=\"demtot\" value=\"$demtot\">
                            <input type=\"hidden\" name=\"demyrs\" value=\"$demyrs\">
                            <input type=\"hidden\" name=\"max\" value=\"$maxstartsat\">
                            <input type=\"hidden\" name=\"teamname\" value=\"$userteam\">
                            <input type=\"hidden\" name=\"player_teamname\" value=\"$player_team_name\">
                            <input type=\"hidden\" name=\"playername\" value=\"$player_name\">
                            <input type=\"hidden\" name=\"vetmin\" value=\"$vetmin\">
                            <input type=\"hidden\" name=\"MLEyrs\" value=\"5\">
                            <input type=\"submit\" value=\"630\">
                        </form>
                    </td>

					<td>
                        <form name=\"FAOffer\" method=\"post\" action=\"/ibl5/modules/Free_Agency/freeagentoffer.php\">
                            <input type=\"hidden\" name=\"amendedCapSpaceYear1\" value=\"$amendedCapSpaceYear1\">
                            <input type=\"hidden\" name=\"capnumber\" value=\"$capnumber\">
                            <input type=\"hidden\" name=\"capnumber2\" value=\"$capnumber2\">
                            <input type=\"hidden\" name=\"capnumber3\" value=\"$capnumber3\">
                            <input type=\"hidden\" name=\"capnumber4\" value=\"$capnumber4\">
                            <input type=\"hidden\" name=\"capnumber5\" value=\"$capnumber5\">
                            <input type=\"hidden\" name=\"capnumber6\" value=\"$capnumber6\">
                            <input type=\"hidden\" name=\"demtot\" value=\"$demtot\">
                            <input type=\"hidden\" name=\"demyrs\" value=\"$demyrs\">
                            <input type=\"hidden\" name=\"max\" value=\"$maxstartsat\">
                            <input type=\"hidden\" name=\"teamname\" value=\"$userteam\">
                            <input type=\"hidden\" name=\"player_teamname\" value=\"$player_team_name\">
                            <input type=\"hidden\" name=\"playername\" value=\"$player_name\">
                            <input type=\"hidden\" name=\"vetmin\" value=\"$vetmin\">
                            <input type=\"hidden\" name=\"MLEyrs\" value=\"6\">
                            <input type=\"submit\" value=\"675\">
                        </form>
                    </td>";
                } else { // Limit undrafted rookie FA contracts to two years
                    echo "";
                }

                echo "<td></td></tr>";
            }
        }
        // ===== CHECK TO SEE IF LLE IS AVAILABLE =====

        if ($HasLLE == 1) {
            $LLEoffers = $db->sql_numrows($db->sql_query("SELECT * FROM ibl_fa_offers WHERE LLE='1' AND team='$userteam'"));
            if ($LLEoffers == 0) {
                echo "<tr><td>Lower-Level Exception:</td>
				<td>
                    <form name=\"FAOffer\" method=\"post\" action=\"/ibl5/modules/Free_Agency/freeagentoffer.php\">
                        <input type=\"hidden\" name=\"amendedCapSpaceYear1\" value=\"$amendedCapSpaceYear1\">
                        <input type=\"hidden\" name=\"capnumber\" value=\"$capnumber\">
                        <input type=\"hidden\" name=\"demtot\" value=\"$demtot\">
                        <input type=\"hidden\" name=\"demyrs\" value=\"$demyrs\">
                        <input type=\"hidden\" name=\"max\" value=\"$maxstartsat\">
                        <input type=\"hidden\" name=\"teamname\" value=\"$userteam\">
                        <input type=\"hidden\" name=\"player_teamname\" value=\"$player_team_name\">
                        <input type=\"hidden\" name=\"playername\" value=\"$player_name\">
                        <input type=\"hidden\" name=\"MLEyrs\" value=\"7\">
                        <input type=\"hidden\" name=\"vetmin\" value=\"$vetmin\">
                        <input type=\"submit\" value=\"145\">
                    </form>
                </td>
				<td colspan=6></td></tr>";
            }
        }

        // ===== VETERANS EXCEPTION (ALWAYS AVAILABLE) =====

        echo "<tr><td>Veterans Exception:</td>
		<td>
            <form name=\"FAOffer\" method=\"post\" action=\"/ibl5/modules/Free_Agency/freeagentoffer.php\">
                <input type=\"hidden\" name=\"amendedCapSpaceYear1\" value=\"$amendedCapSpaceYear1\">
                <input type=\"hidden\" name=\"capnumber\" value=\"$capnumber\">
                <input type=\"hidden\" name=\"demtot\" value=\"$demtot\">
                <input type=\"hidden\" name=\"demyrs\" value=\"$demyrs\">
                <input type=\"hidden\" name=\"max\" value=\"$maxstartsat\">
                <input type=\"hidden\" name=\"teamname\" value=\"$userteam\">
                <input type=\"hidden\" name=\"player_teamname\" value=\"$player_team_name\">
                <input type=\"hidden\" name=\"playername\" value=\"$player_name\">
                <input type=\"hidden\" name=\"MLEyrs\" value=\"8\">
                <input type=\"hidden\" name=\"vetmin\" value=\"$vetmin\">
                <input type=\"submit\" value=\"$vetmin\">
            </form>
        </td>
		<td colspan=6></td></tr>";
    }

    echo "
		<tr><td colspan=8><b>Notes/Reminders:</b> <ul>
		<li>The maximum contract permitted for me (based on my years of service) starts at $maxstartsat in Year 1.
		<li>You have <b>$amendedCapSpaceYear1</b> in <b>soft cap</b> space available; the amount you offer in year 1 cannot exceed this unless you are using one of the exceptions.</li>
		<li>You have <b>$capnumber2</b> in <b>soft cap</b> space available; the amount you offer in year 2 cannot exceed this unless you are using one of the exceptions.</li>
		<li>You have <b>$capnumber3</b> in <b>soft cap</b> space available; the amount you offer in year 3 cannot exceed this unless you are using one of the exceptions.</li>
		<li>You have <b>$capnumber4</b> in <b>soft cap</b> space available; the amount you offer in year 4 cannot exceed this unless you are using one of the exceptions.</li>
		<li>You have <b>$capnumber5</b> in <b>soft cap</b> space available; the amount you offer in year 5 cannot exceed this unless you are using one of the exceptions.</li>
		<li>You have <b>$capnumber6</b> in <b>soft cap</b> space available; the amount you offer in year 6 cannot exceed this unless you are using one of the exceptions.</li>
		<li>You have <b>$hardcapnumber</b> in <b>hard cap</b> space available; the amount you offer in year 1 cannot exceed this, period.</li>
		<li>You have <b>$hardcapnumber2</b> in <b>hard cap</b> space available; the amount you offer in year 2 cannot exceed this, period.</li>
		<li>You have <b>$hardcapnumber3</b> in <b>hard cap</b> space available; the amount you offer in year 3 cannot exceed this, period.</li>
		<li>You have <b>$hardcapnumber4</b> in <b>hard cap</b> space available; the amount you offer in year 4 cannot exceed this, period.</li>
		<li>You have <b>$hardcapnumber5</b> in <b>hard cap</b> space available; the amount you offer in year 5 cannot exceed this, period.</li>
		<li>You have <b>$hardcapnumber6</b> in <b>hard cap</b> space available; the amount you offer in year 6 cannot exceed this, period.</li>
		<li>Enter \"0\" for years you do not want to offer a contract.</li>
		<li>The amounts offered each year must equal or exceed the previous year.</li>
		<li>The first year of the contract must be at least the veteran's minimum ($vetmin for this player).</li>
		<li><b>For Players who do not have Bird Rights with your team:</b> You may add no more than 10% of your the amount you offer in the first year as a raise between years (for instance, if you offer 500 in Year 1, you cannot offer a raise of more than 50 between any two subsequent years.)</li>
		<li><b>Bird Rights Player on Your Team:</b> You may add no more than 12.5% of your the amount you offer in the first year as a raise between years (for instance, if you offer 500 in Year 1, you cannot offer a raise of more than 62 between any two subsequent years.)</li>
		</ul></td></tr>
		</table>

		</form>
	";

    echo "<form name=\"FAOfferDelete\" method=\"post\" action=\"/ibl5/modules/Free_Agency/freeagentofferdelete.php\">
		<input type=\"submit\" value=\"Retract All Offers to this Player!\">
		<input type=\"hidden\" name=\"teamname\" value=\"$userteam\">
        <input type=\"hidden\" name=\"player_teamname\" value=\"$player_team_name\">
		<input type=\"hidden\" name=\"playername\" value=\"$player_name\">
		</form>";

    CloseTable();
    Nuke\Footer::footer();
}

switch ($pa) {
    case "display":
        display(1);
        break;

    case "negotiate":
        negotiate($pid);
        break;

    default:
        main($user);
        break;
}
