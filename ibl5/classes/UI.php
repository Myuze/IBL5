<?php

use Player\Player;
use Services\DatabaseService;

class UI
{
    public static function displayDebugOutput($content, $title = 'Debug Output') 
    {
        static $debugId = 0;
        $debugId++;
        
        echo "<div style='margin: 10px 0; border: 1px solid #ccc; border-radius: 4px;'>
            <div style='padding: 8px; background-color: #f5f5f5; border-bottom: 1px solid #ccc; cursor: pointer;'
                 onclick='toggleDebug$debugId()'>
                <span id='debugIcon$debugId'>▶</span> $title
            </div>
            <pre id='debugContent$debugId' style='display: none; margin: 0; padding: 8px; background-color: #fff; overflow: auto;'>$content</pre>
        </div>
        <script>
            function toggleDebug$debugId() {
                var content = document.getElementById('debugContent$debugId');
                var icon = document.getElementById('debugIcon$debugId');
                if (content.style.display === 'none') {
                    content.style.display = 'block';
                    icon.textContent = '▼';
                } else {
                    content.style.display = 'none';
                    icon.textContent = '▶';
                }
            }
        </script>";
    }

    public static function displaytopmenu($db, $teamID = League::FREE_AGENTS_TEAMID)
    {
        $team = Team::initialize($db, $teamID);

        echo "<center><table width=400 border=0><tr>";

        $teamCityQuery = "SELECT `team_city`,`team_name`,`teamid` FROM `ibl_team_info` ORDER BY `team_city` ASC";
        $teamCityResult = $db->sql_query($teamCityQuery);
        $teamNameQuery = "SELECT `team_city`,`team_name`,`teamid` FROM `ibl_team_info` ORDER BY `team_name` ASC";
        $teamNameResult = $db->sql_query($teamNameQuery);
        $teamIDQuery = "SELECT `team_city`,`team_name`,`teamid` FROM `ibl_team_info` ORDER BY `teamid` ASC";
        $teamIDResult = $db->sql_query($teamIDQuery);

        echo '<p>';
        echo '<b> Team Pages: </b>';
        echo '<select name="teamSelectCity" onchange="location = this.options[this.selectedIndex].value;">';
        echo '<option value="">Location</option>';
        while ($row = $db->sql_fetch_assoc($teamCityResult)) {
            echo '<option value="./modules.php?name=Team&op=team&teamID=' . $row["teamid"] . '">' . $row["team_city"] . '	' . $row["team_name"] . '</option>';
        }
        echo '</select>';

        echo '<select name="teamSelectName" onchange="location = this.options[this.selectedIndex].value;">';
        echo '<option value="">Namesake</option>';
        while ($row = $db->sql_fetch_assoc($teamNameResult)) {
            echo '<option value="./modules.php?name=Team&op=team&teamID=' . $row["teamid"] . '">' . $row["team_name"] . '</option>';
        }
        echo '</select>';

        echo '<select name="teamSelectID" onchange="location = this.options[this.selectedIndex].value;">';
        echo '<option value="">ID#</option>';
        while ($row = $db->sql_fetch_assoc($teamIDResult)) {
            echo '<option value="./modules.php?name=Team&op=team&teamID=' . $row["teamid"] . '">' . $row["teamid"] . '	' . $row["team_city"] . '	' . $row["team_name"] . '</option>';
        }
        echo '</select>';

        echo "<td nowrap=\"nowrap\"><a style=\"font:bold 11px Helvetica;text-decoration: none;background-color: #$team->color2;color: #$team->color1;padding: 2px 6px 2px 6px;border-top: 1px solid #000000;border-right: 1px solid #000000;border-bottom: 1px solid #000000;border-left: 1px solid #000000;\" href=\"modules.php?name=Team&op=team&teamID=$teamID\">Team Page</a></td>";
        echo "<td nowrap=\"nowrap\"><a style=\"font:bold 11px Helvetica;text-decoration: none;background-color: #$team->color2;color: #$team->color1;padding: 2px 6px 2px 6px;border-top: 1px solid #000000;border-right: 1px solid #000000;border-bottom: 1px solid #000000;border-left: 1px solid #000000;\" href=\"modules.php?name=Team_Schedule&teamID=$teamID\">Team Schedule</a></td>";
        echo "<td nowrap=\"nowrap\"><a style=\"font:bold 11px Helvetica;text-decoration: none;background-color: #$team->color2;color: #$team->color1;padding: 2px 6px 2px 6px;border-top: 1px solid #000000;border-right: 1px solid #000000;border-bottom: 1px solid #000000;border-left: 1px solid #000000;\" href=\"modules.php?name=Team&op=drafthistory&teamID=$teamID\">Draft History</a></td>";
        echo "<td nowrap=\"nowrap\" valign=center><font style=\"font:bold 14px Helvetica;text-decoration: none;\"> | </td>";
        echo "<td nowrap=\"nowrap\"><a style=\"font:bold 11px Helvetica;text-decoration: none;background-color: #$team->color2;color: #$team->color1;padding: 2px 6px 2px 6px;border-top: 1px solid #000000;border-right: 1px solid #000000;border-bottom: 1px solid #000000;border-left: 1px solid #000000;\" href=\"modules.php?name=Depth_Chart_Entry\">Depth Chart Entry</a></td>";
        echo "<td nowrap=\"nowrap\"><a style=\"font:bold 11px Helvetica;text-decoration: none;background-color: #$team->color2;color: #$team->color1;padding: 2px 6px 2px 6px;border-top: 1px solid #000000;border-right: 1px solid #000000;border-bottom: 1px solid #000000;border-left: 1px solid #000000;\" href=\"modules.php?name=Trading&op=reviewtrade\">Trades/Waivers</a></td>";
        //echo "<td nowrap=\"nowrap\"><a style=\"font:bold 11px Helvetica;text-decoration: none;background-color: #$color2;color: #$color1;padding: 2px 6px 2px 6px;border-top: 1px solid #000000;border-right: 1px solid #000000;border-bottom: 1px solid #000000;border-left: 1px solid #000000;\" href=\"modules.php?name=Team&op=injuries&teamID=$tid\">Injuries</a></td></tr>";
        echo "</tr></table></center>";
        echo "<hr>";
    }

    public static function playerMenu()
    {
        echo "<center><b>
            <a href=\"modules.php?name=Player_Search\">Player Search</a>  |
            <a href=\"modules.php?name=Player_Awards\">Awards Search</a> |
            <a href=\"modules.php?name=One-on-One\">One-on-One Game</a> |
            <a href=\"modules.php?name=Leaderboards\">Career Leaderboards</a> (All Types)
        </b><center>
        <hr>";
    }

    public static function contracts($db, $result, $team, $sharedFunctions)
    {
        $season = new Season($db);

        if ($sharedFunctions->isFreeAgencyModuleActive() == 1) {
            $season->endingYear++;
        }
        
        $table_contracts = "<table align=\"center\" class=\"sortable\">
            <thead>
                <tr bgcolor=$team->color1>
                    <th><font color=$team->color2>Pos</font></th>
                    <th colspan=2><font color=$team->color2>Player</font></th>
                    <th><font color=$team->color2>Age</font></th>
                    <th><font color=$team->color2>Exp</font></th>
                    <th><font color=$team->color2>Bird</font></th>
                    <td bgcolor=$team->color1 width=0></td>
                    <th><font color=$team->color2>" . ($season->endingYear + -1) . "-<br>" . ($season->endingYear + 0) . "</font></th>
                    <th><font color=$team->color2>" . ($season->endingYear + 0) . "-<br>" . ($season->endingYear + 1) . "</font></th>
                    <th><font color=$team->color2>" . ($season->endingYear + 1) . "-<br>" . ($season->endingYear + 2) . "</font></th>
                    <th><font color=$team->color2>" . ($season->endingYear + 2) . "-<br>" . ($season->endingYear + 3) . "</font></th>
                    <th><font color=$team->color2>" . ($season->endingYear + 3) . "-<br>" . ($season->endingYear + 4) . "</font></th>
                    <th><font color=$team->color2>" . ($season->endingYear + 4) . "-<br>" . ($season->endingYear + 5) . "</font></th>
                    <td bgcolor=$team->color1 width=0></td>
                    <th><font color=$team->color2>Tal</font></th>
                    <th><font color=$team->color2>Skl</font></th>
                    <th><font color=$team->color2>Int</font></th>
                    <td bgcolor=$team->color1 width=0></td>
                    <th><font color=$team->color2>Loy</font></th>
                    <th><font color=$team->color2>PFW</font></th>
                    <th><font color=$team->color2>PT</font></th>
                    <th><font color=$team->color2>Sec</font></th>
                    <th><font color=$team->color2>Trad</font></th>
                </tr>
            </thead>
        <tbody>";
    
        $cap1 = $cap2 = $cap3 = $cap4 = $cap5 = $cap6 = 0;
    
        $i = 0;
        foreach ($result as $plrRow) {
            $player = Player::withPlrRow($db, $plrRow);
    
            if ($sharedFunctions->isFreeAgencyModuleActive() == 0) {
                $year1 = $player->contractCurrentYear;
                $year2 = $player->contractCurrentYear + 1;
                $year3 = $player->contractCurrentYear + 2;
                $year4 = $player->contractCurrentYear + 3;
                $year5 = $player->contractCurrentYear + 4;
                $year6 = $player->contractCurrentYear + 5;
            } else {
                $year1 = $player->contractCurrentYear + 1;
                $year2 = $player->contractCurrentYear + 2;
                $year3 = $player->contractCurrentYear + 3;
                $year4 = $player->contractCurrentYear + 4;
                $year5 = $player->contractCurrentYear + 5;
                $year6 = $player->contractCurrentYear + 6;
            }
            if ($player->contractCurrentYear == 0) {
                $year1 < 7 ? $con1 = $player->contractYear1Salary : $con1 = 0;
                $year2 < 7 ? $con2 = $player->contractYear2Salary : $con2 = 0;
                $year3 < 7 ? $con3 = $player->contractYear3Salary : $con3 = 0;
                $year4 < 7 ? $con4 = $player->contractYear4Salary : $con4 = 0;
                $year5 < 7 ? $con5 = $player->contractYear5Salary : $con5 = 0;
                $year6 < 7 ? $con6 = $player->contractYear6Salary : $con6 = 0;
            } else {
                $year1 < 7 ? $con1 = $player->{'contractYear' . $year1 . 'Salary'} : $con1 = 0;
                $year2 < 7 ? $con2 = $player->{'contractYear' . $year2 . 'Salary'} : $con2 = 0;
                $year3 < 7 ? $con3 = $player->{'contractYear' . $year3 . 'Salary'} : $con3 = 0;
                $year4 < 7 ? $con4 = $player->{'contractYear' . $year4 . 'Salary'} : $con4 = 0;
                $year5 < 7 ? $con5 = $player->{'contractYear' . $year5 . 'Salary'} : $con5 = 0;
                $year6 < 7 ? $con6 = $player->{'contractYear' . $year6 . 'Salary'} : $con6 = 0;
            }
    
            (($i % 2) == 0) ? $bgcolor = "FFFFFF" : $bgcolor = "EEEEEE";
    
            $table_contracts .= "
                <tr bgcolor=$bgcolor>
                <td align=center>$player->position</td>
                <td colspan=2><a href=\"./modules.php?name=Player&pa=showpage&pid=$player->playerID\">$player->decoratedName</a></td>
                <td align=center>$player->age</td>
                <td align=center>$player->yearsOfExperience</td>
                <td align=center>$player->birdYears</td>
                <td bgcolor=$team->color1></td>
                <td>$con1</td>
                <td>$con2</td>
                <td>$con3</td>
                <td>$con4</td>
                <td>$con5</td>
                <td>$con6</td>
                <td bgcolor=$team->color1></td>
                <td align=center>$player->ratingTalent</td>
                <td align=center>$player->ratingSkill</td>
                <td align=center>$player->ratingIntangibles</td>
                <td bgcolor=$team->color1></td>
                <td align=center>$player->freeAgencyLoyalty</td>
                <td align=center>$player->freeAgencyPlayForWinner</td>
                <td align=center>$player->freeAgencyPlayingTime</td>
                <td align=center>$player->freeAgencySecurity</td>
                <td align=center>$player->freeAgencyTradition</td>
            </tr>";
    
            $cap1 += $con1;
            $cap2 += $con2;
            $cap3 += $con3;
            $cap4 += $con4;
            $cap5 += $con5;
            $cap6 += $con6;
            $i++;
        }
    
        $table_contracts .= "</tbody>
            <tfoot>
                <tr>
                    <td></td>
                    <td colspan=2><b>Cap Totals</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td bgcolor=$team->color1></td>
                    <td><b>$cap1</td>
                    <td><b>$cap2</td>
                    <td><b>$cap3</td>
                    <td><b>$cap4</td>
                    <td><b>$cap5</td>
                    <td><b>$cap6</td>
                    <td bgcolor=$team->color1></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td colspan=19><i>Note:</i> Players whose names appear in parenthesis and with a trailing asterisk are waived players that still count against the salary cap.</td>
                </tr>
            </tfoot>
        </table>";
    
        return $table_contracts;
    }

    public static function per36Minutes($db, $result, $team, $yr)
    {
        $table_per36Minutes = "<table align=\"center\" class=\"sortable\">
            <thead>
                <tr bgcolor=$team->color1>
                    <th><font color=$team->color2>Pos</font></th>
                    <th colspan=3><font color=$team->color2>Player</font></th>
                    <th><font color=$team->color2>g</font></th>
                    <th><font color=$team->color2>gs</font></th>
                    <th><font color=$team->color2>mpg</font></th>
                    <th><font color=$team->color2>36min</font></th>
                    <td bgcolor=$team->color1 width=0></td>
                    <th><font color=$team->color2>fgm</font></th>
                    <th><font color=$team->color2>fga</font></th>
                    <th><font color=$team->color2>fgp</font></th>
                    <td bgcolor=#CCCCCC width=0></td>
                    <th><font color=$team->color2>ftm</font></th>
                    <th><font color=$team->color2>fta</font></th>
                    <th><font color=$team->color2>ftp</font></th>
                    <td bgcolor=#CCCCCC width=0></td>
                    <th><font color=$team->color2>3gm</font></th>
                    <th><font color=$team->color2>3ga</font></th>
                    <th><font color=$team->color2>3gp</font></th>
                    <td bgcolor=$team->color1 width=0></td>
                    <th><font color=$team->color2>orb</font></th>
                    <th><font color=$team->color2>reb</font></th>
                    <th><font color=$team->color2>ast</font></th>
                    <th><font color=$team->color2>stl</font></th>
                    <th><font color=$team->color2>to</font></th>
                    <th><font color=$team->color2>blk</font></th>
                    <th><font color=$team->color2>pf</font></th>
                    <th><font color=$team->color2>pts</font></th>
                </tr>
            </thead>
        <tbody>";
    
        $i = 0;
        foreach ($result as $plrRow) {
            if ($yr == "") {
                $player = Player::withPlrRow($db, $plrRow);
                $playerStats = PlayerStats::withPlrRow($db, $plrRow);

                $firstCharacterOfPlayerName = substr($player->name, 0, 1); // if player name starts with '|' (pipe symbol), then skip them
                if ($firstCharacterOfPlayerName == '|') {
                    continue;
                }
            } else {
                $player = Player::withHistoricalPlrRow($db, $plrRow);
                $playerStats = PlayerStats::withHistoricalPlrRow($db, $plrRow);
            }
    
            $stats_fgm = ($playerStats->seasonMinutes != 0) ? number_format((36 / $playerStats->seasonMinutes * $playerStats->seasonFieldGoalsMade), 1) : "0.0";
            $stats_fga = ($playerStats->seasonMinutes != 0) ? number_format((36 / $playerStats->seasonMinutes * $playerStats->seasonFieldGoalsAttempted), 1) : "0.0";
            $stats_fgp = ($stats_fga != 0) ? number_format(($stats_fgm / $stats_fga), 3) : "0.000";
            $stats_ftm = ($playerStats->seasonMinutes != 0) ? number_format((36 / $playerStats->seasonMinutes * $playerStats->seasonFreeThrowsMade), 1) : "0.0";
            $stats_fta = ($playerStats->seasonMinutes != 0) ? number_format((36 / $playerStats->seasonMinutes * $playerStats->seasonFreeThrowsAttempted), 1) : "0.0";
            $stats_ftp = ($stats_fta != 0) ? number_format(($stats_ftm / $stats_fta), 3) : "0.000";
            $stats_tgm = ($playerStats->seasonMinutes != 0) ? number_format((36 / $playerStats->seasonMinutes * $playerStats->seasonThreePointersMade), 1) : "0.0";
            $stats_tga = ($playerStats->seasonMinutes != 0) ? number_format((36 / $playerStats->seasonMinutes * $playerStats->seasonThreePointersAttempted), 1) : "0.0";
            $stats_tgp = ($stats_tga != 0) ? number_format(($stats_tgm / $stats_tga), 3) : "0.000";
            $stats_mpg = ($playerStats->seasonMinutes != 0) ? number_format(($playerStats->seasonMinutes / $playerStats->seasonGamesPlayed), 1) : "0.0";
            $stats_per36Min = ($playerStats->seasonMinutes != 0) ? number_format((36 / $playerStats->seasonMinutes * $playerStats->seasonMinutes), 1) : "0.0";
            $stats_opg = ($playerStats->seasonMinutes != 0) ? number_format((36 / $playerStats->seasonMinutes * $playerStats->seasonOffensiveRebounds), 1) : "0.0";
            $stats_rpg = ($playerStats->seasonMinutes != 0) ? number_format((36 / $playerStats->seasonMinutes * $playerStats->seasonTotalRebounds), 1) : "0.0";
            $stats_apg = ($playerStats->seasonMinutes != 0) ? number_format((36 / $playerStats->seasonMinutes * $playerStats->seasonAssists), 1) : "0.0";
            $stats_spg = ($playerStats->seasonMinutes != 0) ? number_format((36 / $playerStats->seasonMinutes * $playerStats->seasonSteals), 1) : "0.0";
            $stats_tpg = ($playerStats->seasonMinutes != 0) ? number_format((36 / $playerStats->seasonMinutes * $playerStats->seasonTurnovers), 1) : "0.0";
            $stats_bpg = ($playerStats->seasonMinutes != 0) ? number_format((36 / $playerStats->seasonMinutes * $playerStats->seasonBlocks), 1) : "0.0";
            $stats_fpg = ($playerStats->seasonMinutes != 0) ? number_format((36 / $playerStats->seasonMinutes * $playerStats->seasonPersonalFouls), 1) : "0.0";
            $stats_ppg = ($playerStats->seasonMinutes != 0) ? number_format((36 / $playerStats->seasonMinutes * $playerStats->seasonPoints), 1) : "0.0";
        
            (($i % 2) == 0) ? $bgcolor = "FFFFFF" : $bgcolor = "EEEEEE";
        
            $table_per36Minutes .= "<tr bgcolor=$bgcolor>
                <td>$player->position</td>
                <td colspan=3><a href=\"modules.php?name=Player&pa=showpage&pid=$player->playerID\">$player->decoratedName</a></td>
                <td><center>$playerStats->seasonGamesPlayed</center></td>
                <td><center>$playerStats->seasonGamesStarted</center></td>
                <td><center>$stats_mpg</center></td>
                <td><center>$stats_per36Min</center></td>
                <td bgcolor=$team->color1 width=0></td>
                <td><center>$stats_fgm</center></td>
                <td><center>$stats_fga</center></td>
                <td><center>$stats_fgp</center></td>
                <td bgcolor=#CCCCCC width=0></td>
                <td><center>$stats_ftm</center></td>
                <td><center>$stats_fta</center></td>
                <td><center>$stats_ftp</center></td>
                <td bgcolor=#CCCCCC width=0></td>
                <td><center>$stats_tgm</center></td>
                <td><center>$stats_tga</center></td>
                <td><center>$stats_tgp</center></td>
                <td bgcolor=$team->color1 width=0></td>
                <td><center>$stats_opg</center></td>
                <td><center>$stats_rpg</center></td>
                <td><center>$stats_apg</center></td>
                <td><center>$stats_spg</center></td>
                <td><center>$stats_tpg</center></td>
                <td><center>$stats_bpg</center></td>
                <td><center>$stats_fpg</center></td>
                <td><center>$stats_ppg</center></td>
            </tr>";

            $i++;
        }
    
        $table_per36Minutes .= "</tbody>
            </table>";
    
        return $table_per36Minutes;
    }
    
    public static function ratings($db, $data, $team, $yr, $season, $moduleName = "")
    {
        $table_ratings = "<table align=\"center\" class=\"sortable\">
            <colgroup span=2><colgroup span=2><colgroup span=6><colgroup span=6><colgroup span=4><colgroup span=4><colgroup span=1>
            <thead bgcolor=$team->color1>
                <tr bgcolor=$team->color1>";

        if ($moduleName == "League_Starters") {
            $table_ratings .= "<th><font color=$team->color2>Team</font></th>";
        }

        $table_ratings .= "
                    <th><font color=$team->color2>Pos</font></th>
                    <th><font color=$team->color2>Player</font></th>
                    <th><font color=$team->color2>Age</font></th>
                    <td bgcolor=$team->color1 width=0></td>
                    <th><font color=$team->color2>2ga</font></th>
                    <th><font color=$team->color2>2g%</font></th>
                    <td bgcolor=#CCCCCC width=0></td>
                    <th><font color=$team->color2>fta</font></th>
                    <th><font color=$team->color2>ft%</font></th>
                    <td bgcolor=#CCCCCC width=0></td>
                    <th><font color=$team->color2>3ga</font></th>
                    <th><font color=$team->color2>3g%</font></th>
                    <td bgcolor=$team->color1 width=0></td>
                    <th><font color=$team->color2>orb</font></th>
                    <th><font color=$team->color2>drb</font></th>
                    <th><font color=$team->color2>ast</font></th>
                    <th><font color=$team->color2>stl</font></th>
                    <th><font color=$team->color2>tvr</font></th>
                    <th><font color=$team->color2>blk</font></th>
                    <th><font color=$team->color2>foul</font></th>
                    <td bgcolor=$team->color1 width=0></td>
                    <th><font color=$team->color2>oo</font></th>
                    <th><font color=$team->color2>do</font></th>
                    <th><font color=$team->color2>po</font></th>
                    <th><font color=$team->color2>to</font></th>
                    <td bgcolor=#CCCCCC width=0></td>
                    <th><font color=$team->color2>od</font></th>
                    <th><font color=$team->color2>dd</font></th>
                    <th><font color=$team->color2>pd</font></th>
                    <th><font color=$team->color2>td</font></th>
                    <td bgcolor=$team->color1 width=0></td>
                    <th><font color=$team->color2>Clu</font></th>
                    <th><font color=$team->color2>Con</font></th>
                    <td bgcolor=$team->color1 width=0></td>
                    <th><font color=$team->color2>Injury Return<br>Date (Days)</font></th>
                </tr>
            </thead>
        <tbody>";

        $i = 0;
        foreach ($data as $plrRow) {
            if ($yr == "") {
                if (is_object($data)) {
                    $player = Player::withPlrRow($db, $plrRow);
                    (($i % 2) == 0) ? $bgcolor = "FFFFFF" : $bgcolor = "EEEEEE";
                } elseif ($plrRow instanceof Player) {
                    $player = $plrRow;
                    if ($moduleName == "Next_Sim") {
                        (($i % 2) == 0) ? $bgcolor = "FFFFFF" : $bgcolor = "FFFFAA";
                    } elseif ($moduleName == "League_Starters") {
                        ($player->teamID == $team->teamID) ? $bgcolor = "FFFFAA" : $bgcolor = "FFFFFF";
                    } else {
                        (($i % 2) == 0) ? $bgcolor = "FFFFFF" : $bgcolor = "EEEEEE";
                    }
                } else {
                    continue;
                }

                $firstCharacterOfPlayerName = substr($player->name, 0, 1); // if player name starts with '|' (pipe symbol), then skip them
                if ($firstCharacterOfPlayerName == '|') {
                    continue;
                }
            } else {
                $player = Player::withHistoricalPlrRow($db, $plrRow);
            }
    
            $injuryInfo = $player->getInjuryReturnDate($season->lastSimEndDate);
            if ($injuryInfo != "") {
                $injuryInfo .= " ($player->daysRemainingForInjury)";
            }

            if (($i % 2) == 0 AND $moduleName == "Next_Sim") {
                $table_ratings .= "<tr>
                <td colspan=55 bgcolor=$team->color1>
                </td>
                </tr>";
            }

            $table_ratings .= "<tr bgcolor=$bgcolor>";

            if ($moduleName == "League_Starters") {
                $table_ratings .= "<td>$player->teamName</td>";
            }

            $table_ratings .= "
                <td align=center>$player->position</td>
                <td><a href=\"./modules.php?name=Player&pa=showpage&pid=$player->playerID\">$player->decoratedName</a></td>
                <td align=center>$player->age</td>
                <td bgcolor=$team->color1></td>
                <td align=center>$player->ratingFieldGoalAttempts</td>
                <td align=center>$player->ratingFieldGoalPercentage</td>
                <td bgcolor=#CCCCCC width=0></td>
                <td align=center>$player->ratingFreeThrowAttempts</td>
                <td align=center>$player->ratingFreeThrowPercentage</td>
                <td bgcolor=#CCCCCC width=0></td>
                <td align=center>$player->ratingThreePointAttempts</td>
                <td align=center>$player->ratingThreePointPercentage</td>
                <td bgcolor=$team->color1></td>
                <td align=center>$player->ratingOffensiveRebounds</td>
                <td align=center>$player->ratingDefensiveRebounds</td>
                <td align=center>$player->ratingAssists</td>
                <td align=center>$player->ratingSteals</td>
                <td align=center>$player->ratingTurnovers</td>
                <td align=center>$player->ratingBlocks</td>
                <td align=center>$player->ratingFouls</td>
                <td bgcolor=$team->color1></td>
                <td align=center>$player->ratingOutsideOffense</td>
                <td align=center>$player->ratingDriveOffense</td>
                <td align=center>$player->ratingPostOffense</td>
                <td align=center>$player->ratingTransitionOffense</td>
                <td bgcolor=#CCCCCC width=0></td>
                <td align=center>$player->ratingOutsideDefense</td>
                <td align=center>$player->ratingDriveDefense</td>
                <td align=center>$player->ratingPostDefense</td>
                <td align=center>$player->ratingTransitionDefense</td>
                <td bgcolor=$team->color1></td>
                <td align=center>$player->ratingClutch</td>
                <td align=center>$player->ratingConsistency</td>
                <td bgcolor=$team->color1></td>
                <td align=center>$injuryInfo</td>
            </tr>";

            $i++;
        }
    
        $table_ratings .= "</tbody></table>";
    
        return $table_ratings;
    }

    public static function seasonAverages($db, $result, $team, $yr)
    {
        $table_averages = "<table align=\"center\" class=\"sortable\">
                <thead>
                    <tr bgcolor=$team->color1>
                        <th><font color=$team->color2>Pos</font></th>
                        <th colspan=3><font color=$team->color2>Player</font></th>
                        <th><font color=$team->color2>g</font></th>
                        <th><font color=$team->color2>gs</font></th>
                        <th><font color=$team->color2>min</font></th>
                        <td bgcolor=$team->color1 width=0></td>
                        <th><font color=$team->color2>fgm</font></th>
                        <th><font color=$team->color2>fga</font></th>
                        <th><font color=$team->color2>fgp</font></th>
                        <td bgcolor=#CCCCCC width=0></td>
                        <th><font color=$team->color2>ftm</font></th>
                        <th><font color=$team->color2>fta</font></th>
                        <th><font color=$team->color2>ftp</font></th>
                        <td bgcolor=#CCCCCC width=0></td>
                        <th><font color=$team->color2>3gm</font></th>
                        <th><font color=$team->color2>3ga</font></th>
                        <th><font color=$team->color2>3gp</font></th>
                        <td bgcolor=$team->color1 width=0></td>
                        <th><font color=$team->color2>orb</font></th>
                        <th><font color=$team->color2>reb</font></th>
                        <th><font color=$team->color2>ast</font></th>
                        <th><font color=$team->color2>stl</font></th>
                        <th><font color=$team->color2>to</font></th>
                        <th><font color=$team->color2>blk</font></th>
                        <th><font color=$team->color2>pf</font></th>
                        <th><font color=$team->color2>pts</font></th>
                    </tr>
                </thead>
            <tbody>";
    
        /* =======================AVERAGES */
    
        $i = 0;
        foreach ($result as $plrRow) {
            if ($yr == "") {
                $player = Player::withPlrRow($db, $plrRow);
                $playerStats = PlayerStats::withPlrRow($db, $plrRow);

                $firstCharacterOfPlayerName = substr($player->name, 0, 1); // if player name starts with '|' (pipe symbol), then skip them
                if ($firstCharacterOfPlayerName == '|') {
                    continue;
                }
            } else {
                $player = Player::withHistoricalPlrRow($db, $plrRow);
                $playerStats = PlayerStats::withHistoricalPlrRow($db, $plrRow);
            }
        
            (($i % 2) == 0) ? $bgcolor = "FFFFFF" : $bgcolor = "EEEEEE";
        
            $table_averages .= "<tr bgcolor=$bgcolor>
                <td>$player->position</td>
                <td colspan=3><a href=\"modules.php?name=Player&pa=showpage&pid=$player->playerID\">$player->decoratedName</a></td>
                <td><center>$playerStats->seasonGamesPlayed</center></td>
                <td><center>$playerStats->seasonGamesStarted</center></td>
                <td><center>$playerStats->seasonMinutesPerGame</center></td>
                <td bgcolor=$team->color1 width=0></td>
                <td><center>$playerStats->seasonFieldGoalsMadePerGame</center></td>
                <td><center>$playerStats->seasonFieldGoalsAttemptedPerGame</center></td>
                <td><center>$playerStats->seasonFieldGoalPercentage</center></td>
                <td bgcolor=#CCCCCC width=0></td>
                <td><center>$playerStats->seasonFreeThrowsMadePerGame</center></td>
                <td><center>$playerStats->seasonFreeThrowsAttemptedPerGame</center></td>
                <td><center>$playerStats->seasonFreeThrowPercentage</center></td>
                <td bgcolor=#CCCCCC width=0></td>
                <td><center>$playerStats->seasonThreePointersMadePerGame</center></td>
                <td><center>$playerStats->seasonThreePointersAttemptedPerGame</center></td>
                <td><center>$playerStats->seasonThreePointPercentage</center></td>
                <td bgcolor=$team->color1 width=0></td>
                <td><center>$playerStats->seasonOffensiveReboundsPerGame</center></td>
                <td><center>$playerStats->seasonTotalReboundsPerGame</center></td>
                <td><center>$playerStats->seasonAssistsPerGame</center></td>
                <td><center>$playerStats->seasonStealsPerGame</center></td>
                <td><center>$playerStats->seasonTurnoversPerGame</center></td>
                <td><center>$playerStats->seasonBlocksPerGame</center></td>
                <td><center>$playerStats->seasonPersonalFoulsPerGame</center></td>
                <td><center>$playerStats->seasonPointsPerGame</center></td>
            </tr>";

            $i++;
        }

        // ========= TEAM AVERAGES DISPLAY
    
        $table_averages .= "</tbody>
            <tfoot>";
    
        $teamStats = TeamStats::withTeamName($db, $team->name);
    
        if ($yr == "") {
            $table_averages .= "<tr>
                <td colspan=4><b>$team->name Offense</td>
                <td><b><center>$teamStats->seasonOffenseGamesPlayed</center></td>
                <td><b><center>$teamStats->seasonOffenseGamesPlayed</center></td>
                <td></td>
                <td bgcolor=$team->color1 width=0></td>
                <td><center><b>$teamStats->seasonOffenseFieldGoalsMadePerGame</center></td>
                <td><center><b>$teamStats->seasonOffenseFieldGoalsAttemptedPerGame</center></td>
                <td><center><b>$teamStats->seasonOffenseFieldGoalPercentage</center></td>
                <td bgcolor=#CCCCCC width=0></td>
                <td><center><b>$teamStats->seasonOffenseFreeThrowsMadePerGame</center></td>
                <td><center><b>$teamStats->seasonOffenseFreeThrowsAttemptedPerGame</center></td>
                <td><center><b>$teamStats->seasonOffenseFreeThrowPercentage</center></td>
                <td bgcolor=#CCCCCC width=0></td>
                <td><center><b>$teamStats->seasonOffenseThreePointersMadePerGame</center></td>
                <td><center><b>$teamStats->seasonOffenseThreePointersAttemptedPerGame</center></td>
                <td><center><b>$teamStats->seasonOffenseThreePointPercentage</center></td>
                <td bgcolor=$team->color1 width=0></td>
                <td><center><b>$teamStats->seasonOffenseOffensiveReboundsPerGame</center></td>
                <td><center><b>$teamStats->seasonOffenseTotalReboundsPerGame</center></td>
                <td><center><b>$teamStats->seasonOffenseAssistsPerGame</center></td>
                <td><center><b>$teamStats->seasonOffenseStealsPerGame</center></td>
                <td><center><b>$teamStats->seasonOffenseTurnoversPerGame</center></td>
                <td><center><b>$teamStats->seasonOffenseBlocksPerGame</center></td>
                <td><center><b>$teamStats->seasonOffensePersonalFoulsPerGame</center></td>
                <td><center><b>$teamStats->seasonOffensePointsPerGame</center></td>
            </tr>
            <tr>
                <td colspan=4><b>$team->name Defense</td>
                <td><center><b>$teamStats->seasonDefenseGamesPlayed</center></td>
                <td><b>$teamStats->seasonDefenseGamesPlayed</td>
                <td></td>
                <td bgcolor=$team->color1 width=0></td>
                <td><center><b>$teamStats->seasonDefenseFieldGoalsMadePerGame</center></td>
                <td><center><b>$teamStats->seasonDefenseFieldGoalsAttemptedPerGame</center></td>
                <td><center><b>$teamStats->seasonDefenseFieldGoalPercentage</center></td>
                <td bgcolor=#CCCCCC width=0></td>
                <td><center><b>$teamStats->seasonDefenseFreeThrowsMadePerGame</center></td>
                <td><center><b>$teamStats->seasonDefenseFreeThrowsAttemptedPerGame</center></td>
                <td><center><b>$teamStats->seasonDefenseFreeThrowPercentage</center></td>
                <td bgcolor=#CCCCCC width=0></td>
                <td><center><b>$teamStats->seasonDefenseThreePointersMadePerGame</center></td>
                <td><center><b>$teamStats->seasonDefenseThreePointersAttemptedPerGame</center></td>
                <td><center><b>$teamStats->seasonDefenseThreePointPercentage</center></td>
                <td bgcolor=$team->color1 width=0></td>
                <td><center><b>$teamStats->seasonDefenseOffensiveReboundsPerGame</center></td>
                <td><center><b>$teamStats->seasonDefenseTotalReboundsPerGame</center></td>
                <td><center><b>$teamStats->seasonDefenseAssistsPerGame</center></td>
                <td><center><b>$teamStats->seasonDefenseStealsPerGame</center></td>
                <td><center><b>$teamStats->seasonDefenseTurnoversPerGame</center></td>
                <td><center><b>$teamStats->seasonDefenseBlocksPerGame</center></td>
                <td><center><b>$teamStats->seasonDefensePersonalFoulsPerGame</center></td>
                <td><center><b>$teamStats->seasonDefensePointsPerGame</center></td>
            </tr>";
        }
    
        $table_averages .= "</tfoot>
            </table>";
    
        return $table_averages;
    }

    public static function seasonTotals($db, $result, $team, $yr)
    {
        $table_totals = "<table align=\"center\" class=\"sortable\">
            <thead>
                <tr bgcolor=$team->color1>
                    <th><font color=$team->color2>Pos</font></th>
                    <th colspan=3><font color=$team->color2>Player</font></th>
                    <th><font color=$team->color2>g</font></th>
                    <th><font color=$team->color2>gs</font></th>
                    <th><font color=$team->color2>min</font></th>
                    <td bgcolor=$team->color1 width=0></td>
                    <th><font color=$team->color2>fgm</font></th>
                    <th><font color=$team->color2>fga</font></th>
                    <td bgcolor=#CCCCCC width=0></td>
                    <th><font color=$team->color2>ftm</font></th>
                    <th><font color=$team->color2>fta</font></th>
                    <td bgcolor=#CCCCCC width=0></td>
                    <th><font color=$team->color2>3gm</font></th>
                    <th><font color=$team->color2>3ga</font></th>
                    <td bgcolor=$team->color1 width=0></td>
                    <th><font color=$team->color2>orb</font></th>
                    <th><font color=$team->color2>reb</font></th>
                    <th><font color=$team->color2>ast</font></th>
                    <th><font color=$team->color2>stl</font></th>
                    <th><font color=$team->color2>to</font></th>
                    <th><font color=$team->color2>blk</font></th>
                    <th><font color=$team->color2>pf</font></th>
                    <th><font color=$team->color2>pts</font></th>
                </tr>
            </thead>
        <tbody>";

        $i = 0;
        foreach ($result as $plrRow) {
            if ($yr == "") {
                $player = Player::withPlrRow($db, $plrRow);
                $playerStats = PlayerStats::withPlrRow($db, $plrRow);

                $firstCharacterOfPlayerName = substr($player->name, 0, 1); // if player name starts with '|' (pipe symbol), then skip them
                if ($firstCharacterOfPlayerName == '|') {
                    continue;
                }
            } else {
                $player = Player::withHistoricalPlrRow($db, $plrRow);
                $playerStats = PlayerStats::withHistoricalPlrRow($db, $plrRow);
            }
        
            (($i % 2) == 0) ? $bgcolor = "FFFFFF" : $bgcolor = "EEEEEE";
    
            $table_totals .= "<tr bgcolor=$bgcolor>
                <td>$player->position</td>
                <td colspan=3><a href=\"./modules.php?name=Player&pa=showpage&pid=$player->playerID\">$player->decoratedName</a></td>
                <td><center>$playerStats->seasonGamesPlayed</center></td>
                <td><center>$playerStats->seasonGamesStarted</center></td>
                <td><center>$playerStats->seasonMinutes</center></td>
                <td bgcolor=$team->color1 width=0></td>
                <td><center>$playerStats->seasonFieldGoalsMade</center></td>
                <td><center>$playerStats->seasonFieldGoalsAttempted</center></td>
                    <td bgcolor=#CCCCCC width=0></td>
                <td><center>$playerStats->seasonFreeThrowsMade</center></td>
                <td><center>$playerStats->seasonFreeThrowsAttempted</center></td>
                    <td bgcolor=#CCCCCC width=0></td>
                <td><center>$playerStats->seasonThreePointersMade</center></td>
                <td><center>$playerStats->seasonThreePointersAttempted</center></td>
                <td bgcolor=$team->color1 width=0></td>
                <td><center>$playerStats->seasonOffensiveRebounds</center></td>
                <td><center>$playerStats->seasonTotalRebounds</center></td>
                <td><center>$playerStats->seasonAssists</center></td>
                <td><center>$playerStats->seasonSteals</center></td>
                <td><center>$playerStats->seasonTurnovers</center></td>
                <td><center>$playerStats->seasonBlocks</center></td>
                <td><center>$playerStats->seasonPersonalFouls</center></td>
                <td><center>$playerStats->seasonPoints</center></td>
                </tr>";    

            $i++;
        }

        $table_totals .= "</tbody>
            <tfoot>";

        // ==== INSERT TEAM OFFENSE AND DEFENSE TOTALS ====

        $teamStats = TeamStats::withTeamName($db, $team->name);

        if ($yr == "") {
            $table_totals .= "<tr>
                <td colspan=4><b>$team->name Offense</td>
                <td><center><b>$teamStats->seasonOffenseGamesPlayed</center></td>
                <td><center><b>$teamStats->seasonOffenseGamesPlayed</center></td>
                <td></td>
                <td bgcolor=$team->color1 width=0></td>
                <td><center><b>$teamStats->seasonOffenseTotalFieldGoalsMade</center></td>
                <td><center><b>$teamStats->seasonOffenseTotalFieldGoalsAttempted</b></center></td>
                <td bgcolor=#CCCCCC width=0></td>
                <td><center><b>$teamStats->seasonOffenseTotalFreeThrowsMade</center></td>
                <td><center><b>$teamStats->seasonOffenseTotalFreeThrowsAttempted</b></center></td>
                <td bgcolor=#CCCCCC width=0></td>
                <td><center><b>$teamStats->seasonOffenseTotalThreePointersMade</center></td>
                <td><center><b>$teamStats->seasonOffenseTotalThreePointersAttempted</b></center></td>
                <td bgcolor=$team->color1 width=0></td>
                <td><center><b>$teamStats->seasonOffenseTotalOffensiveRebounds</center></td>
                <td><center><b>$teamStats->seasonOffenseTotalRebounds</center></td>
                <td><center><b>$teamStats->seasonOffenseTotalAssists</center></td>
                <td><center><b>$teamStats->seasonOffenseTotalSteals</center></td>
                <td><center><b>$teamStats->seasonOffenseTotalTurnovers</center></td>
                <td><center><b>$teamStats->seasonOffenseTotalBlocks</center></td>
                <td><center><b>$teamStats->seasonOffenseTotalPersonalFouls</center></td>
                <td><center><b>$teamStats->seasonOffenseTotalPoints</center></td>
            </tr>";
            
            $table_totals .= "<tr>
                <td colspan=4><b>$team->name Defense</td>
                <td><center><b>$teamStats->seasonDefenseGamesPlayed</center></td>
                <td><center><b>$teamStats->seasonDefenseGamesPlayed</center></td>
                <td></td>
                <td bgcolor=$team->color1 width=0></td>
                <td><center><b>$teamStats->seasonDefenseTotalFieldGoalsMade</center></td>
                <td><center><b>$teamStats->seasonDefenseTotalFieldGoalsAttempted</b></center></td>
                <td bgcolor=#CCCCCC width=0></td>
                <td><center><b>$teamStats->seasonDefenseTotalFreeThrowsMade</center></td>
                <td><center><b>$teamStats->seasonDefenseTotalFreeThrowsAttempted</b></center></td>
                <td bgcolor=#CCCCCC width=0></td>
                <td><center><b>$teamStats->seasonDefenseTotalThreePointersMade</b></center></td>
                <td><center><b>$teamStats->seasonDefenseTotalThreePointersAttempted</b></center></td>
                <td bgcolor=$team->color1 width=0></td>
                <td><center><b>$teamStats->seasonDefenseTotalOffensiveRebounds</center></td>
                <td><center><b>$teamStats->seasonDefenseTotalRebounds</center></td>
                <td><center><b>$teamStats->seasonDefenseTotalAssists</center></td>
                <td><center><b>$teamStats->seasonDefenseTotalSteals</center></td>
                <td><center><b>$teamStats->seasonDefenseTotalTurnovers</center></td>
                <td><center><b>$teamStats->seasonDefenseTotalBlocks</center></td>
                <td><center><b>$teamStats->seasonDefenseTotalPersonalFouls</center></td>
                <td><center><b>$teamStats->seasonDefenseTotalPoints</center></td>
            </tr>";
        }

        $table_totals .= "</tfoot>
            </table>";

        return $table_totals;
    }

    public static function periodAverages($db, $team, $season, $startDate = NULL, $endDate = NULL)
    {
        if ($startDate == NULL AND $endDate == NULL) {
            // default to last simulated period
            $startDate = $season->lastSimStartDate;
            $endDate = $season->lastSimEndDate;
        }
        
        // convert to Y-m-d format if DateTime object
        if ($startDate instanceof DateTime) {
            $startDate = $startDate->format('Y-m-d');
        }
        if ($endDate instanceof DateTime) {
            $endDate = $endDate->format('Y-m-d');
        }

        $table_periodAverages = "<table align=\"center\" class=\"sortable\">
            <thead>
                <tr bgcolor=$team->color1>
                    <th><font color=$team->color2>Pos</font></th>
                    <th colspan=3><font color=$team->color2>Player</font></th>
                    <th><font color=$team->color2>g</font></th>
                    <th><font color=$team->color2>min</font></th>
                    <td bgcolor=$team->color1 width=0></td>
                    <th><font color=$team->color2>fgm</font></th>
                    <th><font color=$team->color2>fga</font></th>
                    <th><font color=$team->color2>fgp</font></th>
                    <td bgcolor=#CCCCCC width=0></td>
                    <th><font color=$team->color2>ftm</font></th>
                    <th><font color=$team->color2>fta</font></th>
                    <th><font color=$team->color2>ftp</font></th>
                    <td bgcolor=#CCCCCC width=0></td>
                    <th><font color=$team->color2>3gm</font></th>
                    <th><font color=$team->color2>3ga</font></th>
                    <th><font color=$team->color2>3gp</font></th>
                    <td bgcolor=$team->color1 width=0></td>
                    <th><font color=$team->color2>orb</font></th>
                    <th><font color=$team->color2>reb</font></th>
                    <th><font color=$team->color2>ast</font></th>
                    <th><font color=$team->color2>stl</font></th>
                    <th><font color=$team->color2>to</font></th>
                    <th><font color=$team->color2>blk</font></th>
                    <th><font color=$team->color2>pf</font></th>
                    <th><font color=$team->color2>pts</font></th>
                </tr>
            </thead>
        <tbody>";

        $resultPlayerSimBoxScores = $db->sql_query("SELECT name,
            pos,
            pid,
            COUNT(DISTINCT `Date`) as games,
            ROUND(SUM(gameMIN)/COUNT(DISTINCT `Date`), 1) as gameMINavg,
            ROUND(SUM(game2GM + game3GM)/COUNT(DISTINCT `Date`), 2) as gameFGMavg,
            ROUND(SUM(game2GA + game3GA)/COUNT(DISTINCT `Date`), 2) as gameFGAavg,
            ROUND((SUM(game2GM) + SUM(game3GM)) / (SUM(game2GA) + SUM(game3GA)), 3) as gameFGPavg,
            ROUND(SUM(gameFTM)/COUNT(DISTINCT `Date`), 2) as gameFTMavg,
            ROUND(SUM(gameFTA)/COUNT(DISTINCT `Date`), 2) as gameFTAavg,
            ROUND((SUM(gameFTM)) / (SUM(gameFTA)), 3) as gameFTPavg,
            ROUND(SUM(game3GM)/COUNT(DISTINCT `Date`), 2) as game3GMavg,
            ROUND(SUM(game3GA)/COUNT(DISTINCT `Date`), 2) as game3GAavg,
            ROUND((SUM(game3GM)) / (SUM(game3GA)), 3) as game3GPavg,
            ROUND(SUM(gameORB)/COUNT(DISTINCT `Date`), 1) as gameORBavg,
            ROUND((SUM(gameORB) + SUM(gameDRB))/COUNT(DISTINCT `Date`), 1) as gameREBavg,
            ROUND(SUM(gameAST)/COUNT(DISTINCT `Date`), 1) as gameASTavg,
            ROUND(SUM(gameSTL)/COUNT(DISTINCT `Date`), 1) as gameSTLavg,
            ROUND(SUM(gameTOV)/COUNT(DISTINCT `Date`), 1) as gameTOVavg,
            ROUND(SUM(gameBLK)/COUNT(DISTINCT `Date`), 1) as gameBLKavg,
            ROUND(SUM(gamePF)/COUNT(DISTINCT `Date`) , 1) as gamePFavg,
            ROUND(((2 * SUM(game2GM)) + SUM(gameFTM) + (3 * SUM(game3GM)))/COUNT(DISTINCT `Date`) , 1) as gamePTSavg
        FROM   ibl_box_scores
        WHERE  date BETWEEN '$startDate' AND '$endDate'
            AND ( hometid = $team->teamID
                OR visitortid = $team->teamID )
            AND gameMIN > 0
            AND pid IN (SELECT pid
                        FROM   ibl_plr
                        WHERE  tid = $team->teamID
                            AND retired = 0
                            AND `name` NOT LIKE '%|%')
        GROUP  BY name, pos, pid
        ORDER  BY name ASC;");

        $periodAverageMIN = $periodAverageFGM = $periodAverageFGA = $periodAverageFGP = $periodAverageFTM = $periodAverageFTA = $periodAverageFTP = 0;
        $periodAverage3GM = $periodAverage3GA = $periodAverage3GP = $periodAverageORB = $periodAverageREB = $periodAverageAST = $periodAverageSTL = 0;
        $periodAverageTOV = $periodAverageBLK = $periodAveragePF = $periodAveragePTS = $i = 0;

        while ($row = $db->sql_fetch_assoc($resultPlayerSimBoxScores)) {
            $name = DatabaseService::safeHtmlOutput($row['name']); // Safely escape for HTML output
            $pos = $row['pos'];
            $pid = $row['pid'];
            $numberOfGamesPlayedInSim = $row['games'];
            $periodAverageMIN = $row['gameMINavg'];
            $periodAverageFGM = $row['gameFGMavg'];
            $periodAverageFGA = $row['gameFGAavg'];
            $periodAverageFGP = $row['gameFGPavg'] ?? '0.000';
            $periodAverageFTM = $row['gameFTMavg'];
            $periodAverageFTA = $row['gameFTAavg'];
            $periodAverageFTP = $row['gameFTPavg'] ?? '0.000';
            $periodAverage3GM = $row['game3GMavg'];
            $periodAverage3GA = $row['game3GAavg'];
            $periodAverage3GP = $row['game3GPavg'] ?? '0.000';
            $periodAverageORB = $row['gameORBavg'];
            $periodAverageREB = $row['gameREBavg'];
            $periodAverageAST = $row['gameASTavg'];
            $periodAverageSTL = $row['gameSTLavg'];
            $periodAverageTOV = $row['gameTOVavg'];
            $periodAverageBLK = $row['gameBLKavg'];
            $periodAveragePF = $row['gamePFavg'];
            $periodAveragePTS = $row['gamePTSavg'];

            (($i % 2) == 0) ? $bgcolor = "FFFFFF" : $bgcolor = "EEEEEE";

            $table_periodAverages .= "<tr bgcolor=$bgcolor>
                <td>$pos</td>
                <td colspan=3><a href=\"./modules.php?name=Player&pa=showpage&pid=$pid\">$name</a></td>
                <td><center>$numberOfGamesPlayedInSim</center></td>
                <td><center>$periodAverageMIN</center></td>
                <td bgcolor=$team->color1 width=0></td>
                <td><center>$periodAverageFGM</center></td>
                <td><center>$periodAverageFGA</center></td>
                <td><center>$periodAverageFGP</center></td>
                <td bgcolor=#CCCCCC width=0></td>
                <td><center>$periodAverageFTM</center></td>
                <td><center>$periodAverageFTA</center></td>
                <td><center>$periodAverageFTP</center></td>
                <td bgcolor=#CCCCCC width=0></td>
                <td><center>$periodAverage3GM</center></td>
                <td><center>$periodAverage3GA</center></td>
                <td><center>$periodAverage3GP</center></td>
                <td bgcolor=$team->color1 width=0></td>
                <td><center>$periodAverageORB</center></td>
                <td><center>$periodAverageREB</center></td>
                <td><center>$periodAverageAST</center></td>
                <td><center>$periodAverageSTL</center></td>
                <td><center>$periodAverageTOV</center></td>
                <td><center>$periodAverageBLK</center></td>
                <td><center>$periodAveragePF</center></td>
                <td><center>$periodAveragePTS</center></td>
            </tr>";

            $i++;
        }
    
        $table_periodAverages .= "</tbody>
            </table>";
    
        return $table_periodAverages;
    }
}