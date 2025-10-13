<?php

if (!mb_eregi("modules.php", $_SERVER['PHP_SELF'])) {
    die("You can't access this file directly...");
}

$module_name = basename(dirname(__FILE__));
get_lang($module_name);

$pagetitle = "- Player Archives";

function showpage($playerID, $pageView)
{
    global $db, $cookie;
    $sharedFunctions = new Shared($db);
    $season = new Season($db);
    
    $player = Player::withPlayerID($db, $playerID);
    $playerStats = PlayerStats::withPlayerID($db, $playerID);
    $pageView = ($pageView !== null) ? intval($pageView) : null;

    // DISPLAY PAGE

    Nuke\Header::header();
    OpenTable();
    UI::playerMenu();

    echo "<table>
        <tr>
            <td valign=top><font class=\"title\">$player->position $player->name ";

    if ($player->nickname != NULL) {
        echo "- Nickname: \"$player->nickname\" ";
    }

    echo "(<a href=\"modules.php?name=Team&op=team&teamID=$player->teamID\">$player->teamName</a>)</font>
        <hr>
        <table>
            <tr>
                <td valign=center><img src=\"images/player/$playerID.jpg\" height=\"90\" width=\"65\"></td>
                <td>";

    // RENEGOTIATION BUTTON START

    $userTeamName = $sharedFunctions->getTeamnameFromUsername($cookie[1]);
    $userTeam = Team::initialize($db, $userTeamName);

    if ($player->wasRookieOptioned()) {
        echo "<table align=right bgcolor=#ff0000>
                <tr>
                    <td align=center>ROOKIE OPTION<br>USED; RENEGOTIATION<br>IMPOSSIBLE</td>
                </tr>
            </table>";
    } elseif (
        $userTeam->name != "Free Agents"
        AND $userTeam->hasUsedExtensionThisSeason == 0
        AND $player->canRenegotiateContract()
        AND $player->teamName == $userTeam->name
        AND $season->phase != 'Draft'
        AND $season->phase != 'Free Agency'
    ) {
        echo "<table align=right bgcolor=#ff0000>
                <tr>
                    <td align=center><a href=\"modules.php?name=Player&pa=negotiate&pid=$playerID\">RENEGOTIATE<BR>CONTRACT</a></td>
                </tr>
            </table>";
    }

    // RENEGOTIATION BUTTON END

    if (
        $userTeam->name != "Free Agents"
        AND $player->canRookieOption($season->phase)
        AND $player->teamName == $userTeam->name
        ) {
            echo "<table align=right bgcolor=#ffbb00>
                <tr>
                    <td align=center><a href=\"modules.php?name=Player&pa=rookieoption&pid=$playerID\">ROOKIE<BR>OPTION</a></td>
                </tr>
            </table>";
    }

    $contract_display = implode("/", $player->getRemainingContractArray());

    echo "<font class=\"content\">Age: $player->age | Height: $player->heightFeet-$player->heightInches | Weight: $player->weightPounds | College: $player->collegeName<br>
        <i>Drafted by the $player->draftTeamOriginalName with the # $player->draftPickNumber pick of round $player->draftRound in the <a href=\"draft.php?year=$player->draftYear\">$player->draftYear Draft</a></i><br>
        <center><table>
            <tr>
                <td align=center><b>2ga</b></td>
                <td align=center><b>2gp</b></td>
                <td align=center><b>fta</b></td>
                <td align=center><b>ftp</b></td>
                <td align=center><b>3ga</b></td>
                <td align=center><b>3gp</b></td>
                <td align=center><b>orb</b></td>
                <td align=center><b>drb</b></td>
                <td align=center><b>ast</b></td>
                <td align=center><b>stl</b></td>
                <td align=center><b>tvr</b></td>
                <td align=center><b>blk</b></td>
                <td align=center><b>foul</b></td>
                <td align=center><b>oo</b></td>
                <td align=center><b>do</b></td>
                <td align=center><b>po</b></td>
                <td align=center><b>to</b></td>
                <td align=center><b>od</b></td>
                <td align=center><b>dd</b></td>
                <td align=center><b>pd</b></td>
                <td align=center><b>td</b></td>
            </tr>
            <tr>
                <td align=center>$player->ratingFieldGoalAttempts</td>
                <td align=center>$player->ratingFieldGoalPercentage</td>
                <td align=center>$player->ratingFreeThrowAttempts</td>
                <td align=center>$player->ratingFreeThrowPercentage</td>
                <td align=center>$player->ratingThreePointAttempts</td>
                <td align=center>$player->ratingThreePointPercentage</td>
                <td align=center>$player->ratingOffensiveRebounds</td>
                <td align=center>$player->ratingDefensiveRebounds</td>
                <td align=center>$player->ratingAssists</td>
                <td align=center>$player->ratingSteals</td>
                <td align=center>$player->ratingTurnovers</td>
                <td align=center>$player->ratingBlocks</td>
                <td align=center>$player->ratingFouls</td>
                <td align=center>$player->ratingOutsideOffense</td>
                <td align=center>$player->ratingDriveOffense</td>
                <td align=center>$player->ratingPostOffense</td>
                <td align=center>$player->ratingTransitionOffense</td>
                <td align=center>$player->ratingOutsideDefense</td>
                <td align=center>$player->ratingDriveDefense</td>
                <td align=center>$player->ratingPostDefense</td>
                <td align=center>$player->ratingTransitionDefense</td>
            </tr>
        </table></center>
    <b>BIRD YEARS:</b> $player->birdYears | <b>Remaining Contract:</b> $contract_display </td>";

    echo "<td rowspan=3 valign=top>
        <table border=1 cellspacing=0 cellpadding=0>
            <tr bgcolor=#0000cc>
                <td align=center colspan=3><font color=#ffffff><b>PLAYER HIGHS</b></font></td>
            </tr>
            <tr bgcolor=#0000cc>
                <td align=center colspan=3><font color=#ffffff><b>Regular-Season</b></font></td>
            </tr>
            <tr bgcolor=#0000cc>
                <td></td>
                <td><font color=#ffffff>Ssn</font></td>
                <td><font color=#ffffff>Car</td>
            </tr>
            <tr>
                <td><b>Points</b></td>
                <td>$playerStats->seasonHighPoints</td>
                <td>$playerStats->careerSeasonHighPoints</td>
            </tr>
            <tr>
                <td><b>Rebounds</b></td>
                <td>$playerStats->seasonHighRebounds</td>
                <td>$playerStats->careerSeasonHighRebounds</td>
            </tr>
            <tr>
                <td><b>Assists</b></td>
                <td>$playerStats->seasonHighAssists</td>
                <td>$playerStats->careerSeasonHighAssists</td>
            </tr>
            <tr>
                <td><b>Steals</b></td>
                <td>$playerStats->seasonHighSteals</td>
                <td>$playerStats->careerSeasonHighSteals</td>
            </tr>
            <tr>
                <td><b>Blocks</b></td>
                <td>$playerStats->seasonHighBlocks</td>
                <td>$playerStats->careerSeasonHighBlocks</td>
            </tr>
            <tr>
                <td>Double-Doubles</td>
                <td>$playerStats->seasonDoubleDoubles</td>
                <td>$playerStats->careerDoubleDoubles</td>
            </tr>
            <tr>
                <td>Triple-Doubles</td>
                <td>$playerStats->seasonTripleDoubles</td>
                <td>$playerStats->careerTripleDoubles</td>
            </tr>
            <tr bgcolor=#0000cc>
                <td align=center colspan=3><font color=#ffffff><b>Playoffs</b></font></td>
            </tr>
            <tr bgcolor=#0000cc>
                <td></td>
                <td><font color=#ffffff>Ssn</font></td>
                <td><font color=#ffffff>Car</td>
            </tr>
            <tr>
                <td><b>Points</b></td>
                <td>$playerStats->seasonPlayoffHighPoints</td>
                <td>$playerStats->careerPlayoffHighPoints</td>
            </tr>
            <tr>
                <td><b>Rebounds</b></td>
                <td>$playerStats->seasonPlayoffHighRebounds</td>
                <td>$playerStats->careerPlayoffHighRebounds</td>
            </tr>
            <tr>
                <td><b>Assists</b></td>
                <td>$playerStats->seasonPlayoffHighAssists</td>
                <td>$playerStats->careerPlayoffHighAssists</td>
            </tr>
            <tr>
                <td><b>Steals</b></td>
                <td>$playerStats->seasonPlayoffHighSteals</td>
                <td>$playerStats->careerPlayoffHighSteals</td>
            </tr>
            <tr>
                <td><b>Blocks</b></td>
                <td>$playerStats->seasonPlayoffHighBlocks</td>
                <td>$playerStats->careerPlayoffHighBlocks</td>
            </tr>
        </table></td>";

    echo "<tr>
        <td colspan=2><hr></td>
    </tr>
    <tr>
        <td colspan=2><b><center>PLAYER MENU</center></b><br>
            <center>" .
            "<a href=\"" . PlayerPageType::getUrl($playerID, PlayerPageType::OVERVIEW) . "\">" . PlayerPageType::getDescription(PlayerPageType::OVERVIEW) . "</a> | " .
            "<a href=\"" . PlayerPageType::getUrl($playerID, PlayerPageType::AWARDS_AND_NEWS) . "\">" . PlayerPageType::getDescription(PlayerPageType::AWARDS_AND_NEWS) . "</a><br>" .
            "<a href=\"" . PlayerPageType::getUrl($playerID, PlayerPageType::ONE_ON_ONE) . "\">" . PlayerPageType::getDescription(PlayerPageType::ONE_ON_ONE) . "</a> | " .
            "<a href=\"" . PlayerPageType::getUrl($playerID, PlayerPageType::SIM_STATS) . "\">" . PlayerPageType::getDescription(PlayerPageType::SIM_STATS) . "</a><br>" .
            "<a href=\"" . PlayerPageType::getUrl($playerID, PlayerPageType::REGULAR_SEASON_TOTALS) . "\">" . PlayerPageType::getDescription(PlayerPageType::REGULAR_SEASON_TOTALS) . "</a> | " .
            "<a href=\"" . PlayerPageType::getUrl($playerID, PlayerPageType::REGULAR_SEASON_AVERAGES) . "\">" . PlayerPageType::getDescription(PlayerPageType::REGULAR_SEASON_AVERAGES) . "</a><br>" .
            "<a href=\"" . PlayerPageType::getUrl($playerID, PlayerPageType::PLAYOFF_TOTALS) . "\">" . PlayerPageType::getDescription(PlayerPageType::PLAYOFF_TOTALS) . "</a> | " .
            "<a href=\"" . PlayerPageType::getUrl($playerID, PlayerPageType::PLAYOFF_AVERAGES) . "\">" . PlayerPageType::getDescription(PlayerPageType::PLAYOFF_AVERAGES) . "</a><br>" .
            "<a href=\"" . PlayerPageType::getUrl($playerID, PlayerPageType::HEAT_TOTALS) . "\">" . PlayerPageType::getDescription(PlayerPageType::HEAT_TOTALS) . "</a> | " .
            "<a href=\"" . PlayerPageType::getUrl($playerID, PlayerPageType::HEAT_AVERAGES) . "\">" . PlayerPageType::getDescription(PlayerPageType::HEAT_AVERAGES) . "</a><br>" .
            "<a href=\"" . PlayerPageType::getUrl($playerID, PlayerPageType::OLYMPIC_TOTALS) . "\">" . PlayerPageType::getDescription(PlayerPageType::OLYMPIC_TOTALS) . "</a> | " .
            "<a href=\"" . PlayerPageType::getUrl($playerID, PlayerPageType::OLYMPIC_AVERAGES) . "\">" . PlayerPageType::getDescription(PlayerPageType::OLYMPIC_AVERAGES) . "</a><br>" .
            "<a href=\"" . PlayerPageType::getUrl($playerID, PlayerPageType::RATINGS_AND_SALARY) . "\">" . PlayerPageType::getDescription(PlayerPageType::RATINGS_AND_SALARY) . "</a>
            </center>
        </td>
    </tr>
    <tr>
        <td colspan=3><hr></td>
    </tr>";

    if ($pageView == PlayerPageType::OVERVIEW) {
        require_once __DIR__ . '/views/OverviewView.php';
        $view = new OverviewView($db, $player, $playerStats, $season, $sharedFunctions);
        $view->render();
    } elseif ($pageView == PlayerPageType::SIM_STATS) {
        require_once __DIR__ . '/views/SimStatsView.php';
        $view = new SimStatsView($db, $player, $playerStats);
        $view->render();
    } elseif ($pageView == PlayerPageType::REGULAR_SEASON_TOTALS) {
        require_once __DIR__ . '/views/RegularSeasonTotalsView.php';
        $view = new RegularSeasonTotalsView($db, $player, $playerStats);
        $view->render();
    } elseif ($pageView == PlayerPageType::REGULAR_SEASON_AVERAGES) {
        require_once __DIR__ . '/views/RegularSeasonAveragesView.php';
        $view = new RegularSeasonAveragesView($db, $player, $playerStats);
        $view->render();
    } elseif ($pageView == PlayerPageType::PLAYOFF_TOTALS) {
        require_once __DIR__ . '/views/PlayoffTotalsView.php';
        $view = new PlayoffTotalsView($db, $player, $playerStats);
        $view->render();
    } elseif ($pageView == PlayerPageType::PLAYOFF_AVERAGES) {
        require_once __DIR__ . '/views/PlayoffAveragesView.php';
        $view = new PlayoffAveragesView($db, $player, $playerStats);
        $view->render();
    } elseif ($pageView == PlayerPageType::HEAT_TOTALS) {
        require_once __DIR__ . '/views/HeatTotalsView.php';
        $view = new HeatTotalsView($db, $player, $playerStats);
        $view->render();
    } elseif ($pageView == PlayerPageType::HEAT_AVERAGES) {
        require_once __DIR__ . '/views/HeatAveragesView.php';
        $view = new HeatAveragesView($db, $player, $playerStats);
        $view->render();
    } elseif ($pageView == PlayerPageType::OLYMPIC_TOTALS) {
        require_once __DIR__ . '/views/OlympicTotalsView.php';
        $view = new OlympicTotalsView($db, $player, $playerStats);
        $view->render();
    } elseif ($pageView == PlayerPageType::OLYMPIC_AVERAGES) {
        require_once __DIR__ . '/views/OlympicAveragesView.php';
        $view = new OlympicAveragesView($db, $player, $playerStats);
        $view->render();
    } elseif ($pageView == PlayerPageType::RATINGS_AND_SALARY) {
        require_once __DIR__ . '/views/RatingsAndSalaryView.php';
        $view = new RatingsAndSalaryView($db, $player, $playerStats);
        $view->render();
    } elseif ($pageView == PlayerPageType::AWARDS_AND_NEWS) {
        require_once __DIR__ . '/views/AwardsAndNewsView.php';
        $view = new AwardsAndNewsView($db, $player, $playerStats);
        $view->render();
    } elseif ($pageView == PlayerPageType::ONE_ON_ONE) {
        require_once __DIR__ . '/views/OneOnOneView.php';
        $view = new OneOnOneView($db, $player, $playerStats);
        $view->render();
    }

    echo "</table>";

    CloseTable();
    Nuke\Footer::footer();

    // END OF DISPLAY PAGE
}

function negotiate($playerID)
{
    global $prefix, $db, $user, $cookie;

    $playerID = intval($playerID);
    $playerinfo = $db->sql_fetchrow($db->sql_query("SELECT * FROM ibl_plr WHERE pid = '$playerID'"));
    $player_name = stripslashes(check_html($playerinfo['name'], "nohtml"));
    $player_pos = stripslashes(check_html($playerinfo['pos'], "nohtml"));
    $player_team_name = stripslashes(check_html($playerinfo['teamname'], "nohtml"));

    $player_loyalty = stripslashes(check_html($playerinfo['loyalty'], "nohtml"));
    $player_winner = stripslashes(check_html($playerinfo['winner'], "nohtml"));
    $player_playingtime = stripslashes(check_html($playerinfo['playingTime'], "nohtml"));
    $player_tradition = stripslashes(check_html($playerinfo['tradition'], "nohtml"));

    Nuke\Header::header();
    OpenTable();
    UI::playerMenu();

    // RENEGOTIATION STUFF

    cookiedecode($user);

    $sql2 = "SELECT * FROM " . $prefix . "_users WHERE username = '$cookie[1]'";
    $result2 = $db->sql_query($sql2);
    $userinfo = $db->sql_fetchrow($result2);

    $userteam = stripslashes(check_html($userinfo['user_ibl_team'], "nohtml"));

    $player_exp = stripslashes(check_html($playerinfo['exp'], "nohtml"));
    $player_bird = stripslashes(check_html($playerinfo['bird'], "nohtml"));
    $yearOfCurrentContract = stripslashes(check_html($playerinfo['cy'], "nohtml"));
    $salaryIn2ndYearOfCurrentContract = stripslashes(check_html($playerinfo['cy2'], "nohtml"));
    $salaryIn3rdYearOfCurrentContract = stripslashes(check_html($playerinfo['cy3'], "nohtml"));
    $salaryIn4thYearOfCurrentContract = stripslashes(check_html($playerinfo['cy4'], "nohtml"));
    $salaryIn5thYearOfCurrentContract = stripslashes(check_html($playerinfo['cy5'], "nohtml"));
    $salaryIn6thYearOfCurrentContract = stripslashes(check_html($playerinfo['cy6'], "nohtml"));

    // CONTRACT CHECKER

    $can_renegotiate = 0;

    if (
        ($yearOfCurrentContract == 0 AND $salaryIn2ndYearOfCurrentContract == 0)
        OR ($yearOfCurrentContract == 1 AND $salaryIn2ndYearOfCurrentContract == 0)
        OR ($yearOfCurrentContract == 2 AND $salaryIn3rdYearOfCurrentContract == 0)
        OR ($yearOfCurrentContract == 3 AND $salaryIn4thYearOfCurrentContract == 0)
        OR ($yearOfCurrentContract == 4 AND $salaryIn5thYearOfCurrentContract == 0)
        OR ($yearOfCurrentContract == 5 AND $salaryIn6thYearOfCurrentContract == 0)
        OR ($yearOfCurrentContract == 6)
    ) {
        $can_renegotiate = 1;
    }

    // END CONTRACT CHECKER

    echo "<b>$player_pos $player_name</b> - Contract Demands:
    <br>";

    if ($can_renegotiate == 1) {
        if ($player_team_name == $userteam) {
            // Assign player stats to variables
            $negotiatingPlayerFGA = intval($playerinfo['r_fga']);
            $negotiatingPlayerFGP = intval($playerinfo['r_fgp']);
            $negotiatingPlayerFTA = intval($playerinfo['r_fta']);
            $negotiatingPlayerFTP = intval($playerinfo['r_ftp']);
            $negotiatingPlayerTGA = intval($playerinfo['r_tga']);
            $negotiatingPlayerTGP = intval($playerinfo['r_tgp']);
            $negotiatingPlayerORB = intval($playerinfo['r_orb']);
            $negotiatingPlayerDRB = intval($playerinfo['r_drb']);
            $negotiatingPlayerAST = intval($playerinfo['r_ast']);
            $negotiatingPlayerSTL = intval($playerinfo['r_stl']);
            $negotiatingPlayerTOV = intval($playerinfo['r_to']);
            $negotiatingPlayerBLK = intval($playerinfo['r_blk']);
            $negotiatingPlayerFOUL = intval($playerinfo['r_foul']);
            $negotiatingPlayerOO = intval($playerinfo['oo']);
            $negotiatingPlayerOD = intval($playerinfo['od']);
            $negotiatingPlayerDO = intval($playerinfo['do']);
            $negotiatingPlayerDD = intval($playerinfo['dd']);
            $negotiatingPlayerPO = intval($playerinfo['po']);
            $negotiatingPlayerPD = intval($playerinfo['pd']);
            $negotiatingPlayerTO = intval($playerinfo['to']);
            $negotiatingPlayerTD = intval($playerinfo['td']);

            // Pull max values of each stat category
            $marketMaxFGA = $db->sql_fetchrow($db->sql_query("SELECT MAX(`r_fga`) FROM ibl_plr"));
            $marketMaxFGP = $db->sql_fetchrow($db->sql_query("SELECT MAX(`r_fgp`) FROM ibl_plr"));
            $marketMaxFTA = $db->sql_fetchrow($db->sql_query("SELECT MAX(`r_fta`) FROM ibl_plr"));
            $marketMaxFTP = $db->sql_fetchrow($db->sql_query("SELECT MAX(`r_ftp`) FROM ibl_plr"));
            $marketMaxTGA = $db->sql_fetchrow($db->sql_query("SELECT MAX(`r_tga`) FROM ibl_plr"));
            $marketMaxTGP = $db->sql_fetchrow($db->sql_query("SELECT MAX(`r_tgp`) FROM ibl_plr"));
            $marketMaxORB = $db->sql_fetchrow($db->sql_query("SELECT MAX(`r_orb`) FROM ibl_plr"));
            $marketMaxDRB = $db->sql_fetchrow($db->sql_query("SELECT MAX(`r_drb`) FROM ibl_plr"));
            $marketMaxAST = $db->sql_fetchrow($db->sql_query("SELECT MAX(`r_ast`) FROM ibl_plr"));
            $marketMaxSTL = $db->sql_fetchrow($db->sql_query("SELECT MAX(`r_stl`) FROM ibl_plr"));
            $marketMaxTOV = $db->sql_fetchrow($db->sql_query("SELECT MAX(`r_to`) FROM ibl_plr"));
            $marketMaxBLK = $db->sql_fetchrow($db->sql_query("SELECT MAX(`r_blk`) FROM ibl_plr"));
            $marketMaxFOUL = $db->sql_fetchrow($db->sql_query("SELECT MAX(`r_foul`) FROM ibl_plr"));
            $marketMaxOO = $db->sql_fetchrow($db->sql_query("SELECT MAX(`oo`) FROM ibl_plr"));
            $marketMaxOD = $db->sql_fetchrow($db->sql_query("SELECT MAX(`od`) FROM ibl_plr"));
            $marketMaxDO = $db->sql_fetchrow($db->sql_query("SELECT MAX(`do`) FROM ibl_plr"));
            $marketMaxDD = $db->sql_fetchrow($db->sql_query("SELECT MAX(`dd`) FROM ibl_plr"));
            $marketMaxPO = $db->sql_fetchrow($db->sql_query("SELECT MAX(`po`) FROM ibl_plr"));
            $marketMaxPD = $db->sql_fetchrow($db->sql_query("SELECT MAX(`pd`) FROM ibl_plr"));
            $marketMaxTO = $db->sql_fetchrow($db->sql_query("SELECT MAX(`to`) FROM ibl_plr"));
            $marketMaxTD = $db->sql_fetchrow($db->sql_query("SELECT MAX(`td`) FROM ibl_plr"));

            // Determine raw score for each stat
            $rawFGA = intval(round($negotiatingPlayerFGA / intval($marketMaxFGA[0]) * 100));
            $rawFGP = intval(round($negotiatingPlayerFGP / intval($marketMaxFGP[0]) * 100));
            $rawFTA = intval(round($negotiatingPlayerFTA / intval($marketMaxFTA[0]) * 100));
            $rawFTP = intval(round($negotiatingPlayerFTP / intval($marketMaxFTP[0]) * 100));
            $rawTGA = intval(round($negotiatingPlayerTGA / intval($marketMaxTGA[0]) * 100));
            $rawTGP = intval(round($negotiatingPlayerTGP / intval($marketMaxTGP[0]) * 100));
            $rawORB = intval(round($negotiatingPlayerORB / intval($marketMaxORB[0]) * 100));
            $rawDRB = intval(round($negotiatingPlayerDRB / intval($marketMaxDRB[0]) * 100));
            $rawAST = intval(round($negotiatingPlayerAST / intval($marketMaxAST[0]) * 100));
            $rawSTL = intval(round($negotiatingPlayerSTL / intval($marketMaxSTL[0]) * 100));
            $rawTOV = intval(round($negotiatingPlayerTOV / intval($marketMaxTOV[0]) * 100));
            $rawBLK = intval(round($negotiatingPlayerBLK / intval($marketMaxBLK[0]) * 100));
            $rawFOUL = intval(round($negotiatingPlayerFOUL / intval($marketMaxFOUL[0]) * 100));
            $rawOO = intval(round($negotiatingPlayerOO / intval($marketMaxOO[0]) * 100));
            $rawOD = intval(round($negotiatingPlayerOD / intval($marketMaxOD[0]) * 100));
            $rawDO = intval(round($negotiatingPlayerDO / intval($marketMaxDO[0]) * 100));
            $rawDD = intval(round($negotiatingPlayerDD / intval($marketMaxDD[0]) * 100));
            $rawPO = intval(round($negotiatingPlayerPO / intval($marketMaxPO[0]) * 100));
            $rawPD = intval(round($negotiatingPlayerPD / intval($marketMaxPD[0]) * 100));
            $rawTO = intval(round($negotiatingPlayerTO / intval($marketMaxTO[0]) * 100));
            $rawTD = intval(round($negotiatingPlayerTD / intval($marketMaxTD[0]) * 100));
            $totalRawScore = $rawFGA + $rawFGP + $rawFTA + $rawFTP + $rawTGA + $rawTGP + $rawORB + $rawDRB + $rawAST + $rawSTL + $rawTOV + $rawBLK + $rawFOUL +
                $rawOO + $rawOD + $rawDO + $rawDD + $rawPO + $rawPD + $rawTO + $rawTD;
            //    var_dump($totalRawScore);
            $adjustedScore = $totalRawScore - 700; // MJ's 87-88 season numbers = 1414 raw score! Sam Mack's was 702. So I cut the score down by 700.
            $demandsFactor = 3; // I got this number by trial-and-error until the first-round picks of the dispersal draft demanded around a max.
            $avgDemands = $adjustedScore * $demandsFactor;
            $totalDemands = $avgDemands * 5;
            $baseDemands = $totalDemands / 6;
            $maxRaise = floor($baseDemands * 0.1);

            $dem1 = $baseDemands;
            $dem2 = $baseDemands + $maxRaise;
            $dem3 = $baseDemands + $maxRaise * 2;
            $dem4 = $baseDemands + $maxRaise * 3;
            $dem5 = $baseDemands + $maxRaise * 4;
            $dem6 = 0;
            
            $teamfactors = $db->sql_fetchrow($db->sql_query("SELECT * FROM ibl_team_info WHERE team_name = '$userteam'"));
            $tf_wins = stripslashes(check_html($teamfactors['Contract_Wins'], "nohtml"));
            $tf_loss = stripslashes(check_html($teamfactors['Contract_Losses'], "nohtml"));
            $tf_trdw = stripslashes(check_html($teamfactors['Contract_AvgW'], "nohtml"));
            $tf_trdl = stripslashes(check_html($teamfactors['Contract_AvgL'], "nohtml"));

            $millionsatposition = $db->sql_query("SELECT * FROM ibl_plr WHERE teamname = '$userteam' AND pos = '$player_pos' AND name != '$player_name'");
            // LOOP TO GET MILLIONS COMMITTED AT POSITION

            $tf_millions = 0;

            while ($millionscounter = $db->sql_fetchrow($millionsatposition)) {
                $millionscy = stripslashes(check_html($millionscounter['cy'], "nohtml"));
                $millionscy2 = stripslashes(check_html($millionscounter['cy2'], "nohtml"));
                $millionscy3 = stripslashes(check_html($millionscounter['cy3'], "nohtml"));
                $millionscy4 = stripslashes(check_html($millionscounter['cy4'], "nohtml"));
                $millionscy5 = stripslashes(check_html($millionscounter['cy5'], "nohtml"));
                $millionscy6 = stripslashes(check_html($millionscounter['cy6'], "nohtml"));

                // FOR AN EXTENSION, LOOK AT SALARY COMMITTED NEXT YEAR, NOT THIS YEAR

                if ($millionscy == 1) {
                    $tf_millions = $tf_millions + $millionscy2;
                }
                if ($millionscy == 2) {
                    $tf_millions = $tf_millions + $millionscy3;
                }
                if ($millionscy == 3) {
                    $tf_millions = $tf_millions + $millionscy4;
                }
                if ($millionscy == 4) {
                    $tf_millions = $tf_millions + $millionscy5;
                }
                if ($millionscy == 5) {
                    $tf_millions = $tf_millions + $millionscy6;
                }
            }

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

            //$modfactor1 = (0.0005*($tf_wins-$tf_losses)*($player_winner-1));
            $PFWFactor = (0.025 * ($tf_wins - $tf_loss) / ($tf_wins + $tf_loss) * ($player_winner - 1));
            //$modfactor2 = (0.00125*($tf_trdw-$tf_trdl)*($player_tradition-1));
            $traditionFactor = (0.025 * ($tf_trdw - $tf_trdl) / ($tf_trdw + $tf_trdl) * ($player_tradition - 1));
            //$modfactor3 = (.01*($tf_coach)*($player_coach=1));
            //$modfactor4 = (.025*($player_loyalty-1));
            $loyaltyFactor = (0.025 * ($player_loyalty - 1));
            $PTFactor = (($tf_millions * -0.00005) + 0.025) * ($player_playingtime - 1);

            $modifier = 1 + $PFWFactor + $traditionFactor + $loyaltyFactor + $PTFactor;
            //echo "Wins: $tf_wins<br>Loses: $tf_loss<br>Tradition Wins: $tf_trdw<br> Tradition Loses: $tf_trdl<br>Coach: $tf_coach<br>Loyalty: $player_loyalty<br>Play Time: $tf_millions<br>ModW: $modfactor1<br>ModT: $modfactor2<br>ModC: $modfactor3<br>ModL: $modfactor4<br>ModS: $modfactor5<br>ModP: $modfactor6<br>Mod: $modifier<br>Demand 1: $dem1<br>Demand 2: $dem2<br>Demand 3: $dem3<br>Demand 4: $dem4<br>Demand 5: $dem5<br>";
            
            $dem1 = round($dem1 / $modifier);
            $dem2 = round($dem2 / $modifier);
            $dem3 = round($dem3 / $modifier);
            $dem4 = round($dem4 / $modifier);
            $dem5 = round($dem5 / $modifier);
            // The sixth year is zero for extensions only; remove the line below and uncomment the regular line in the FA module.
            $dem6 = 0;
            // $dem6 = round($dem6/$modifier);

            $demtot = round($dem1 + $dem2 + $dem3 + $dem4 + $dem5 + $dem6);

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

            // LOOP TO GET HARD CAP SPACE

            $capnumber = League::HARD_CAP_MAX;

            $capquery = "SELECT * FROM ibl_plr WHERE teamname='$userteam' AND retired = '0'";
            $capresult = $db->sql_query($capquery);
            while ($capdecrementer = $db->sql_fetchrow($capresult)) {

                $capcy = stripslashes(check_html($capdecrementer['cy'], "nohtml"));
                $capcy2 = stripslashes(check_html($capdecrementer['cy2'], "nohtml"));
                $capcy3 = stripslashes(check_html($capdecrementer['cy3'], "nohtml"));
                $capcy4 = stripslashes(check_html($capdecrementer['cy4'], "nohtml"));
                $capcy5 = stripslashes(check_html($capdecrementer['cy5'], "nohtml"));
                $capcy6 = stripslashes(check_html($capdecrementer['cy6'], "nohtml"));

                // LOOK AT SALARY COMMITTED NEXT YEAR, NOT THIS YEAR

                if ($capcy == 1) {
                    $capnumber = $capnumber - $capcy2;
                }
                if ($capcy == 2) {
                    $capnumber = $capnumber - $capcy3;
                }
                if ($capcy == 3) {
                    $capnumber = $capnumber - $capcy4;
                }
                if ($capcy == 4) {
                    $capnumber = $capnumber - $capcy5;
                }
                if ($capcy == 5) {
                    $capnumber = $capnumber - $capcy6;
                }
            }

            // ======= BEGIN HTML OUTPUT FOR RENEGOTIATION FUNCTION ======

            $fa_activecheck = $db->sql_fetchrow($db->sql_query("SELECT * FROM " . $prefix . "_modules WHERE title = 'Free_Agency'"));
            $fa_active = stripslashes(check_html($fa_activecheck['active'], "nohtml"));

            if ($fa_active == 1) {
                echo "Sorry, the contract extension feature is not available during free agency.";
            } else {
                echo "<form name=\"ExtensionOffer\" method=\"post\" action=\"modules/Player/extension.php\">";

                $maxyr1 = 1063;
                if ($player_exp > 6) {
                    $maxyr1 = 1275;
                }
                if ($player_exp > 9) {
                    $maxyr1 = 1451;
                }

                echo "Note that if you offer the max and I refuse, it means I am opting for Free Agency at the end of the season):
                    <table cellspacing=0 border=1>
                        <tr>
                            <td>My demands are:</td><td>$demand_display</td>
                        </tr>
                        <tr>
                            <td>Please enter your offer in this row:</td>
                        <td>";

                if ($dem1 < $maxyr1) {
                    echo "<INPUT TYPE=\"number\" style=\"width: 4em\" NAME=\"offerYear1\" SIZE=\"4\" VALUE=\"$dem1\"></td>
                        <td><INPUT TYPE=\"number\" style=\"width: 4em\" NAME=\"offerYear2\" SIZE=\"4\" VALUE=\"$dem2\"></td>
                        <td><INPUT TYPE=\"number\" style=\"width: 4em\" NAME=\"offerYear3\" SIZE=\"4\" VALUE=\"$dem3\"></td>
                        <td><INPUT TYPE=\"number\" style=\"width: 4em\" NAME=\"offerYear4\" SIZE=\"4\" VALUE=\"$dem4\"></td>
                        <td><INPUT TYPE=\"number\" style=\"width: 4em\" NAME=\"offerYear5\" SIZE=\"4\" VALUE=\"$dem5\"></td>
                    </tr>";
                } else {
                    if ($player_bird >= 3) {
                        $maxraise = round($maxyr1 * 0.125);
                    } else {
                        $maxraise = round($maxyr1 * 0.1);
                    }

                    $maxyr2 = 0;
                    $maxyr3 = 0;
                    $maxyr4 = 0;
                    $maxyr5 = 0;

                    if ($dem2 != 0) {
                        $maxyr2 = $maxyr1 + $maxraise;
                    }
                    if ($dem3 != 0) {
                        $maxyr3 = $maxyr2 + $maxraise;
                    }
                    if ($dem4 != 0) {
                        $maxyr4 = $maxyr3 + $maxraise;
                    }
                    if ($dem5 != 0) {
                        $maxyr5 = $maxyr4 + $maxraise;
                    }

                    echo "<INPUT TYPE=\"text\" NAME=\"offerYear1\" SIZE=\"4\" VALUE=\"$maxyr1\"></td>
                        <td><INPUT TYPE=\"text\" NAME=\"offerYear2\" SIZE=\"4\" VALUE=\"$maxyr2\"></td>
                        <td><INPUT TYPE=\"text\" NAME=\"offerYear3\" SIZE=\"4\" VALUE=\"$maxyr3\"></td>
                        <td><INPUT TYPE=\"text\" NAME=\"offerYear4\" SIZE=\"4\" VALUE=\"$maxyr4\"></td>
                        <td><INPUT TYPE=\"text\" NAME=\"offerYear5\" SIZE=\"4\" VALUE=\"$maxyr5\"></td>
                    </tr>";
                }

                echo "<tr>
                    <td colspan=6><b>Notes/Reminders:</b>
                        <ul>
                            <li>You have $capnumber in cap space available; the amount you offer in year 1 cannot exceed this.</li>
                            <li>Based on my years of service, the maximum amount you can offer me in year 1 is $maxyr1.</li>
                            <li>Enter \"0\" for years you do not want to offer a contract.</li>
                            <li>Contract extensions must be at least three years in length.</li>
                            <li>The amounts offered each year must equal or exceed the previous year.</li>";

                if ($player_bird >= 3) {
                    echo "<li>Because this player has Bird Rights, you may add no more than 12.5% of your the amount you offer in the first year as a raise between years (for instance, if you offer 500 in Year 1, you cannot offer a raise of more than 75 between any two subsequent years.)</li>";
                } else {
                    echo "<li>Because this player does not have Bird Rights, you may add no more than 10% of your the amount you offer in the first year as a raise between years (for instance, if you offer 500 in Year 1, you cannot offer a raise of more than 50 between any two subsequent years.)</li>";
                }

                echo "<li>When re-signing your own players, you can go over the soft cap and up to the hard cap (" . League::HARD_CAP_MAX . ").</li>
                    </ul></td></tr>
                    <input type=\"hidden\" name=\"maxyr1\" value=\"$maxyr1\">
                    <input type=\"hidden\" name=\"demandsTotal\" value=\"$demtot\">
                    <input type=\"hidden\" name=\"demandsYears\" value=\"$demyrs\">
                    <input type=\"hidden\" name=\"teamName\" value=\"$userteam\">
                    <input type=\"hidden\" name=\"playerName\" value=\"$player_name\">
                    <input type=\"hidden\" name=\"playerID\" value=\"$playerID\">
                </table>";

                echo "<input type=\"submit\" value=\"Offer Extension!\"></form>";
            }

        } else {
            echo "Sorry, this player is not on your team.";
        }
    } else {
        echo "Sorry, this player is not eligible for a contract extension at this time.";
    }

    // RENEGOTIATION STUFF END

    CloseTable();
    Nuke\Footer::footer();
}

function rookieoption($pid)
{
    global $prefix, $db, $cookie;
    $sharedFunctions = new Shared($db);
    $season = new Season($db);
    $player = Player::withPlayerID($db, $pid);

    $userteam = $sharedFunctions->getTeamnameFromUsername($cookie[1]);
    $userTeamID = $sharedFunctions->getTidFromTeamname($userteam);

    if ($userTeamID != $player->teamID) {
        echo "$player->position $player->name is not on your team.<br>
            <a href=\"javascript:history.back()\">Go Back</a>";
        return;
    }

    if ($player->draftRound == 1 AND $player->canRookieOption($season->phase)) {
        $finalYearOfRookieContract = $player->contractYear3Salary;
    } elseif ($player->draftRound == 2 AND $player->canRookieOption($season->phase)) {
        $finalYearOfRookieContract = $player->contractYear2Salary;
    } else {
        echo "Sorry, $player->position $player->name is not eligible for a rookie option.<p>
            Only draft picks are eligible for rookie options, and the option must be exercised
            before the final season of their rookie contract is underway.<p>
    		<a href=\"javascript:history.back()\">Go Back</a>";
        return;
    }

    $rookieOptionValue = 2 * $finalYearOfRookieContract;

    echo "<img align=left src=\"images/player/$pid.jpg\">
    	You may exercise the rookie extension option on <b>$player->position $player->name</b>.<br>
    	Their contract value the season after this one will be <b>$rookieOptionValue</b>.<br>
    	However, by exercising this option, <b>you can't use an in-season contract extension on them next season</b>.<br>
    	<b>They will become a free agent</b>.<br>
    	<form name=\"RookieExtend\" method=\"post\" action=\"/ibl5/modules/Player/rookieoption.php\">
            <input type=\"hidden\" name=\"teamname\" value=\"$userteam\">
            <input type=\"hidden\" name=\"playerID\" value=\"$player->playerID\">
            <input type=\"hidden\" name=\"rookieOptionValue\" value=\"$rookieOptionValue\">
            <input type=\"submit\" value=\"Activate Rookie Extension\">
        </form>";
}

switch ($pa) {

    case "negotiate":
        negotiate($pid);
        break;

    case "rookieoption":
        rookieoption($pid);
        break;

    case "showpage":
        showpage($pid, $pageView);
        break;
}
