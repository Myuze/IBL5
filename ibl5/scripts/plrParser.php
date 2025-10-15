<?php
if (!isset($_POST['confirmed'])) {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <style>
            .modal-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.5);
                display: flex;
                justify-content: center;
                align-items: center;
                z-index: 1000;
            }
            .modal-content {
                background: white;
                padding: 20px;
                border-radius: 5px;
                text-align: center;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            }
            .modal-buttons {
                margin-top: 20px;
            }
            .btn-run {
                background: #dc3545;
                color: white;
                border: none;
                padding: 10px 20px;
                margin: 0 10px;
                border-radius: 5px;
                cursor: pointer;
            }
            .btn-cancel {
                background: white;
                color: black;
                border: 1px solid #ccc;
                padding: 10px 20px;
                margin: 0 10px;
                border-radius: 5px;
                cursor: pointer;
            }
        </style>
    </head>
    <body>
        <div class="modal-overlay">
            <div class="modal-content">
                <h2>WARNING</h2>
                <p>Are you sure you want to run the PLR Parser script?</p>
                This will <u><b>erase all player changes</b></u> that aren't in JSB.<br>
                (i.e. trades, add/drops, extensions, etc.)
                <p>Make sure you have uploaded a new .plr file with<br>
                all the current player changes before proceeding.</p>
                <div class="modal-buttons">
                    <form method="POST">
                        <input type="hidden" name="confirmed" value="1">
                        <button type="submit" class="btn-run">Run script</button>
                        <button type="button" class="btn-cancel" onclick="window.history.back()">Cancel</button>
                    </form>
                </div>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit();
}

require $_SERVER['DOCUMENT_ROOT'] . '/ibl5/mainfile.php';

$sharedFunctions = new Shared($db);
$season = new Season($db);

$tidOffenseStats = $tidDefenseStats = 0;

$queryTeamIDsNames = "SELECT teamid, team_name FROM ibl_team_info ORDER BY teamid ASC;";
$resultTeamIDsNames = $db->sql_query($queryTeamIDsNames);
$numRowsTeamIDsNames = $db->sql_numrows($resultTeamIDsNames);

echo "Calculating foul baseline...<br>";

$plrFile = fopen($_SERVER['DOCUMENT_ROOT'] . "/ibl5/IBL5.plr", "rb");
$foulRatioArray = [];
while (!feof($plrFile)) {
    $line = fgets($plrFile);

    $realLifeMIN = intval(substr($line, 56, 4));
    $realLifePF = intval(substr($line, 108, 4));

    $personalFoulsPerMinute = ($realLifePF != 0) ? round($realLifePF / $realLifeMIN, 6) : 0;
    $foulRatioArray[] = $personalFoulsPerMinute;
}
fclose($plrFile);

if (!empty($foulRatioArray) && max($foulRatioArray) > 0) {
    echo "Foul baseline calculated!<br><br>";
}

echo "Updating ibl_plr and ibl_hist...<br>";

$plrFile = fopen($_SERVER['DOCUMENT_ROOT'] . "/ibl5/IBL5.plr", "rb");
while (!feof($plrFile)) {
    $line = fgets($plrFile);

    // TODO: Refactor the parser to load all the data into a Player object.
    $ordinal = intval(substr($line, 0, 4));
    $name = trim(addslashes(substr($line, 4, 32)));
    $age = intval(substr($line, 36, 2));
    $pid = intval(substr($line, 38, 6));
    $tid = intval(substr($line, 44, 2));
    $peak = intval(substr($line, 46, 4));
    $pos = trim(substr($line, 50, 2));
    $realLifeGP = intval(substr($line, 52, 4));
    $realLifeMIN = intval(substr($line, 56, 4));
    $realLifeFGM = intval(substr($line, 60, 4));
    $realLifeFGA = intval(substr($line, 64, 4));
    $realLifeFTM = intval(substr($line, 68, 4));
    $realLifeFTA = intval(substr($line, 72, 4));
    $realLife3GM = intval(substr($line, 76, 4));
    $realLife3GA = intval(substr($line, 80, 4));
    $realLifeORB = intval(substr($line, 84, 4));
    $realLifeDRB = intval(substr($line, 88, 4));
    $realLifeAST = intval(substr($line, 92, 4));
    $realLifeSTL = intval(substr($line, 96, 4));
    $realLifeTVR = intval(substr($line, 100, 4));
    $realLifeBLK = intval(substr($line, 104, 4));
    $realLifePF = intval(substr($line, 108, 4));
    $oo = intval(substr($line, 112, 2));
    $od = intval(substr($line, 114, 2));
    $do = intval(substr($line, 116, 2));
    $dd = intval(substr($line, 118, 2));
    $po = intval(substr($line, 120, 2));
    $pd = intval(substr($line, 122, 2));
    $to = intval(substr($line, 124, 2));
    $td = intval(substr($line, 126, 2));
    $clutch = intval(substr($line, 128, 2));
    $consistency = intval(substr($line, 130, 2));
    $PGDepth = intval(substr($line, 132, 1));
    $SGDepth = intval(substr($line, 133, 1));
    $SFDepth = intval(substr($line, 134, 1));
    $PFDepth = intval(substr($line, 135, 1));
    $CDepth = intval(substr($line, 136, 1));
    $active = intval(substr($line, 137, 1));
    // 138,2 = ?
    $injuryDaysLeft = intval(substr($line, 140, 4));
    $seasonGamesStarted = intval(substr($line, 144, 4));
    $seasonGamesPlayed = intval(substr($line, 148, 4));
    $seasonMIN = intval(substr($line, 152, 4));
    $season2GM = intval(substr($line, 156, 4));
    $season2GA = intval(substr($line, 160, 4));
    $seasonFTM = intval(substr($line, 164, 4));
    $seasonFTA = intval(substr($line, 168, 4));
    $season3GM = intval(substr($line, 172, 4));
    $season3GA = intval(substr($line, 176, 4));
    $seasonORB = intval(substr($line, 180, 4));
    $seasonDRB = intval(substr($line, 184, 4));
    $seasonAST = intval(substr($line, 188, 4));
    $seasonSTL = intval(substr($line, 192, 4));
    $seasonTVR = intval(substr($line, 196, 4));
    $seasonBLK = intval(substr($line, 200, 4));
    $seasonPF = intval(substr($line, 204, 4));
    $playoffGS = intval(substr($line, 208, 4));
    $playoffMIN = intval(substr($line, 212, 4));
    $playoff2GM = intval(substr($line, 216, 4));
    $playoff2GA = intval(substr($line, 220, 4));
    $playoffFTM = intval(substr($line, 224, 4));
    $playoffFTA = intval(substr($line, 228, 4));
    $playoff3GM = intval(substr($line, 232, 4));
    $playoff3GA = intval(substr($line, 236, 4));
    $playoffORB = intval(substr($line, 240, 4));
    $playoffDRB = intval(substr($line, 244, 4));
    $playoffAST = intval(substr($line, 248, 4));
    $playoffSTL = intval(substr($line, 252, 4));
    $playoffTVR = intval(substr($line, 256, 4));
    $playoffBLK = intval(substr($line, 260, 4));
    $playoffPF = intval(substr($line, 264, 4));
    $talent = intval(substr($line, 268, 2));
    $skill = intval(substr($line, 270, 2));
    $intangibles = intval(substr($line, 272, 2));
    $coach = intval(substr($line, 274, 2));
    $loyalty = intval(substr($line, 276, 2));
    $playingTime = intval(substr($line, 278, 2));
    $playForWinner = intval(substr($line, 280, 2));
    $tradition = intval(substr($line, 282, 2));
    $security = intval(substr($line, 284, 2));
    $exp = intval(substr($line, 286, 2));
    $bird = intval(substr($line, 288, 2));
    $currentContractYear = intval(substr($line, 290, 2));
    $totalContractYears = intval(substr($line, 292, 2));
    // 294,4 = ?
    $contractYear1 = intval(substr($line, 298, 4));
    $contractYear2 = intval(substr($line, 302, 4));
    $contractYear3 = intval(substr($line, 306, 4));
    $contractYear4 = intval(substr($line, 310, 4));
    $contractYear5 = intval(substr($line, 314, 4));
    $contractYear6 = intval(substr($line, 318, 4));
    // 322,4 = ? (always 1111)
    $draftRound = intval(substr($line, 326, 2));
    $draftPickNumber = intval(substr($line, 328, 2));
    // 330 = ?
    $contractOwnedBy = intval(substr($line, 331, 2));
    // 333-340 = ?
    $seasonHighPTS = intval(substr($line, 341, 2));
    $seasonHighREB = intval(substr($line, 343, 2));
    $seasonHighAST = intval(substr($line, 345, 2));
    $seasonHighSTL = intval(substr($line, 347, 2));
    $seasonHighBLK = intval(substr($line, 349, 2));
    $seasonHighDoubleDoubles = intval(substr($line, 351, 2));
    $seasonHighTripleDoubles = intval(substr($line, 353, 2));
    $seasonPlayoffHighPTS = intval(substr($line, 355, 2));
    $seasonPlayoffHighREB = intval(substr($line, 357, 2));
    $seasonPlayoffHighAST = intval(substr($line, 359, 2));
    $seasonPlayoffHighSTL = intval(substr($line, 361, 2));
    $seasonPlayoffHighBLK = intval(substr($line, 363, 2));
    $careerSeasonHighPTS = intval(substr($line, 365, 6));
    $careerSeasonHighREB = intval(substr($line, 371, 6));
    $careerSeasonHighAST = intval(substr($line, 377, 6));
    $careerSeasonHighSTL = intval(substr($line, 383, 6));
    $careerSeasonHighBLK = intval(substr($line, 389, 6));
    $careerSeasonHighDoubleDoubles = intval(substr($line, 395, 6));
    $careerSeasonHighTripleDoubles = intval(substr($line, 401, 6));
    $careerPlayoffHighPTS = intval(substr($line, 407, 6));
    $careerPlayoffHighREB = intval(substr($line, 413, 6));
    $careerPlayoffHighAST = intval(substr($line, 419, 6));
    $careerPlayoffHighSTL = intval(substr($line, 425, 6));
    $careerPlayoffHighBLK = intval(substr($line, 431, 6));
    $careerGP = intval(substr($line, 437, 5));
    $careerMIN = intval(substr($line, 442, 5));
    $career2GM = intval(substr($line, 447, 5));
    $career2GA = intval(substr($line, 452, 5));
    $careerFTM = intval(substr($line, 457, 5));
    $careerFTA = intval(substr($line, 462, 5));
    $career3GM = intval(substr($line, 467, 5));
    $career3GA = intval(substr($line, 472, 5));
    $careerORB = intval(substr($line, 477, 5));
    $careerDRB = intval(substr($line, 482, 5));
    $careerAST = intval(substr($line, 487, 5));
    $careerSTL = intval(substr($line, 492, 5));
    $careerTVR = intval(substr($line, 497, 5));
    $careerBLK = intval(substr($line, 502, 5));
    $careerPF = intval(substr($line, 507, 5));
    // 512-543 = blank
    // 544-549 = ?
    $heightInches = intval(substr($line, 550, 2));
    $weight = intval(substr($line, 552, 3));
    $rating2GA = intval(substr($line, 555, 3));
    $rating2GP = intval(substr($line, 558, 3));
    $ratingFTA = intval(substr($line, 561, 3));
    $ratingFTP = intval(substr($line, 564, 3));
    $rating3GA = intval(substr($line, 567, 3));
    $rating3GP = intval(substr($line, 570, 3));
    $ratingORB = intval(substr($line, 573, 3));
    $ratingDRB = intval(substr($line, 576, 3));
    $ratingAST = intval(substr($line, 579, 3));
    $ratingSTL = intval(substr($line, 582, 3));
    $ratingTVR = intval(substr($line, 585, 3));
    $ratingBLK = intval(substr($line, 588, 3));
    $ratingOO = intval(substr($line, 591, 2));
    $ratingDO = intval(substr($line, 593, 2));
    $ratingPO = intval(substr($line, 595, 2));
    $ratingTO = intval(substr($line, 597, 2));
    $ratingOD = intval(substr($line, 599, 2));
    $ratingDD = intval(substr($line, 601, 2));
    $ratingPD = intval(substr($line, 603, 2));
    $ratingTD = intval(substr($line, 605, 2));

    $seasonFGM = $season2GM + $season3GM;
    $seasonFGA = $season2GA + $season3GA;
    $seasonREB = $seasonORB + $seasonDRB;
    $seasonPTS = $season2GM * 2 + $seasonFTM + $season3GM * 3;

    $careerFGM = $career2GM + $career3GM;
    $careerFGA = $career2GA + $career3GA;
    $careerREB = $careerORB + $careerDRB;
    $careerPTS = $career2GM * 2 + $careerFTM + $career3GM * 3;

    // HACK: The following is adapted from Player::getCurrentSeasonSalary().
    if ("contractYear" . $currentContractYear . "Salary" == "contractYear0Salary") {
        $currentSeasonSalary = $contractYear1;
    } elseif ("contractYear" . $currentContractYear . "Salary" == "contractYear7Salary") {
        $currentSeasonSalary = 0;
    } else {
        $currentSeasonSalary = ${"contractYear" . $currentContractYear};
    }

    $heightFT = floor($heightInches / 12);
    $heightIN = $heightInches % 12;
    $draftYear = $season->endingYear - $exp;

    $personalFoulsPerMinute = ($realLifePF != 0) ? round($realLifePF / $realLifeMIN, 6) : 0;
    $ratingFOUL = intval(100-round($personalFoulsPerMinute / max($foulRatioArray) * 100, 0));

    if ($ordinal <= 1440 AND $pid != 0) {
        $playerUpdateQuery = "INSERT INTO ibl_plr
            (   `ordinal`,
                `name`,
                `age`,
                `pid`,
                `tid`,
                `peak`,
                `pos`,
                `oo`,
                `od`,
                `do`,
                `dd`,
                `po`,
                `pd`,
                `to`,
                `td`,
                `Clutch`,
                `Consistency`,
                `PGDepth`,
                `SGDepth`,
                `SFDepth`,
                `PFDepth`,
                `CDepth`,
                `active`,
                `stats_gs`,
                `stats_gm`,
                `stats_min`,
                `stats_fgm`,
                `stats_fga`,
                `stats_ftm`,
                `stats_fta`,
                `stats_3gm`,
                `stats_3ga`,
                `stats_orb`,
                `stats_drb`,
                `stats_ast`,
                `stats_stl`,
                `stats_to`,
                `stats_blk`,
                `stats_pf`,
                `talent`,
                `skill`,
                `intangibles`,
                `coach`,
                `loyalty`,
                `playingTime`,
                `winner`,
                `tradition`,
                `security`,
                `exp`,
                `bird`,
                `cy`,
                `cyt`,
                `cy1`,
                `cy2`,
                `cy3`,
                `cy4`,
                `cy5`,
                `cy6`,
                `sh_pts`,
                `sh_reb`,
                `sh_ast`,
                `sh_stl`,
                `sh_blk`,
                `s_dd`,
                `s_td`,
                `sp_pts`,
                `sp_reb`,
                `sp_ast`,
                `sp_stl`,
                `sp_blk`,
                `ch_pts`,
                `ch_reb`,
                `ch_ast`,
                `ch_stl`,
                `ch_blk`,
                `c_dd`,
                `c_td`,
                `cp_pts`,
                `cp_reb`,
                `cp_ast`,
                `cp_stl`,
                `cp_blk`,
                `car_gm`,
                `car_min`,
                `car_fgm`,
                `car_fga`,
                `car_ftm`,
                `car_fta`,
                `car_tgm`,
                `car_tga`,
                `car_orb`,
                `car_drb`,
                `car_reb`,
                `car_ast`,
                `car_stl`,
                `car_to`,
                `car_blk`,
                `car_pf`,
                `car_pts`,
                `r_fga`,
                `r_fgp`,
                `r_fta`,
                `r_ftp`,
                `r_tga`,
                `r_tgp`,
                `r_orb`,
                `r_drb`,
                `r_ast`,
                `r_stl`,
                `r_to`,
                `r_blk`,
                `draftround`,
                `draftpickno`,
                `injured`,
                `htft`,
                `htin`,
                `wt`,
                `draftyear`,
                `retired`,
                `r_foul`
            )
        VALUES
            ($ordinal,
            '$name',
            $age,
            $pid,
            $tid,
            $peak,
            '$pos',
            $ratingOO,
            $ratingOD,
            $ratingDO,
            $ratingDD,
            $ratingPO,
            $ratingPD,
            $ratingTO,
            $ratingTD,
            $clutch,
            $consistency,
            $PGDepth,
            $SGDepth,
            $SFDepth,
            $PFDepth,
            $CDepth,
            $active,
            $seasonGamesStarted,
            $seasonGamesPlayed,
            $seasonMIN,
            $seasonFGM,
            $seasonFGA,
            $seasonFTM,
            $seasonFTA,
            $season3GM,
            $season3GA,
            $seasonORB,
            $seasonDRB,
            $seasonAST,
            $seasonSTL,
            $seasonTVR,
            $seasonBLK,
            $seasonPF,
            $talent,
            $skill,
            $intangibles,
            $coach,
            $loyalty,
            $playingTime,
            $playForWinner,
            $tradition,
            $security,
            $exp,
            $bird,
            $currentContractYear,
            $totalContractYears,
            $contractYear1,
            $contractYear2,
            $contractYear3,
            $contractYear4,
            $contractYear5,
            $contractYear6,
            $seasonHighPTS,
            $seasonHighREB,
            $seasonHighAST,
            $seasonHighSTL,
            $seasonHighBLK,
            $seasonHighDoubleDoubles,
            $seasonHighTripleDoubles,
            $seasonPlayoffHighPTS,
            $seasonPlayoffHighREB,
            $seasonPlayoffHighAST,
            $seasonPlayoffHighSTL,
            $seasonPlayoffHighBLK,
            $careerSeasonHighPTS,
            $careerSeasonHighREB,
            $careerSeasonHighAST,
            $careerSeasonHighSTL,
            $careerSeasonHighBLK,
            $careerSeasonHighDoubleDoubles,
            $careerSeasonHighTripleDoubles,
            $careerPlayoffHighPTS,
            $careerPlayoffHighREB,
            $careerPlayoffHighAST,
            $careerPlayoffHighSTL,
            $careerPlayoffHighBLK,
            $careerGP,
            $careerMIN,
            $careerFGM,
            $careerFGA,
            $careerFTM,
            $careerFTA,
            $career3GM,
            $career3GA,
            $careerORB,
            $careerDRB,
            $careerREB,
            $careerAST,
            $careerSTL,
            $careerTVR,
            $careerBLK,
            $careerPF,
            $careerPTS,
            $rating2GA,
            $rating2GP,
            $ratingFTA,
            $ratingFTP,
            $rating3GA,
            $rating3GP,
            $ratingORB,
            $ratingDRB,
            $ratingAST,
            $ratingSTL,
            $ratingTVR,
            $ratingBLK,
            $draftRound,
            $draftPickNumber,
            $injuryDaysLeft,
            $heightFT,
            $heightIN,
            $weight,
            $draftYear,
            0,
            $ratingFOUL)
        ON DUPLICATE KEY UPDATE
            `ordinal` = $ordinal,
            `name` = '$name',
            `age` = $age,
            `pid` = $pid,
            `tid` = $tid,
            `peak` = $peak,
            `pos` = '$pos',
            `oo` = $ratingOO,
            `od` = $ratingOD,
            `do` = $ratingDO,
            `dd` = $ratingDD,
            `po` = $ratingPO,
            `pd` = $ratingPD,
            `to` = $ratingTO,
            `td` = $ratingTD,
            `Clutch` = $clutch,
            `Consistency` = $consistency,
            `PGDepth` = $PGDepth,
            `SGDepth` = $SGDepth,
            `SFDepth` = $SFDepth,
            `PFDepth` = $PFDepth,
            `CDepth` = $CDepth,
            `active` = $active,
            `stats_gs` = $seasonGamesStarted,
            `stats_gm` = $seasonGamesPlayed,
            `stats_min` = $seasonMIN,
            `stats_fgm` = $seasonFGM,
            `stats_fga` = $seasonFGA,
            `stats_ftm` = $seasonFTM,
            `stats_fta` = $seasonFTA,
            `stats_3gm` = $season3GM,
            `stats_3ga` = $season3GA,
            `stats_orb` = $seasonORB,
            `stats_drb` = $seasonDRB,
            `stats_ast` = $seasonAST,
            `stats_stl` = $seasonSTL,
            `stats_to` = $seasonTVR,
            `stats_blk` = $seasonBLK,
            `stats_pf` = $seasonPF,
            `talent` = $talent,
            `skill` = $skill,
            `intangibles` = $intangibles,
            `coach` = $coach,
            `loyalty` = $loyalty,
            `playingTime` = $playingTime,
            `winner` = $playForWinner,
            `tradition` = $tradition,
            `security` = $security,
            `exp` = $exp,
            `bird` = $bird,
            `cy` = $currentContractYear,
            `cyt` = $totalContractYears,
            `cy1` = $contractYear1,
            `cy2` = $contractYear2,
            `cy3` = $contractYear3,
            `cy4` = $contractYear4,
            `cy5` = $contractYear5,
            `cy6` = $contractYear6,
            `sh_pts` = $seasonHighPTS,
            `sh_reb` = $seasonHighREB,
            `sh_ast` = $seasonHighAST,
            `sh_stl` = $seasonHighSTL,
            `sh_blk` = $seasonHighBLK,
            `s_dd` = $seasonHighDoubleDoubles,
            `s_td` = $seasonHighTripleDoubles,
            `sp_pts` = $seasonPlayoffHighPTS,
            `sp_reb` = $seasonPlayoffHighREB,
            `sp_ast` = $seasonPlayoffHighAST,
            `sp_stl` = $seasonPlayoffHighSTL,
            `sp_blk` = $seasonPlayoffHighBLK,
            `ch_pts` = $careerSeasonHighPTS,
            `ch_reb` = $careerSeasonHighREB,
            `ch_ast` = $careerSeasonHighAST,
            `ch_stl` = $careerSeasonHighSTL,
            `ch_blk` = $careerSeasonHighBLK,
            `c_dd` = $careerSeasonHighDoubleDoubles,
            `c_td` = $careerSeasonHighTripleDoubles,
            `cp_pts` = $careerPlayoffHighPTS,
            `cp_reb` = $careerPlayoffHighREB,
            `cp_ast` = $careerPlayoffHighAST,
            `cp_stl` = $careerPlayoffHighSTL,
            `cp_blk` = $careerPlayoffHighBLK,
            `car_gm` = $careerGP,
            `car_min` = $careerMIN,
            `car_fgm` = $careerFGM,
            `car_fga` = $careerFGA,
            `car_ftm` = $careerFTM,
            `car_fta` = $careerFTA,
            `car_tgm` = $career3GM,
            `car_tga` = $career3GA,
            `car_orb` = $careerORB,
            `car_drb` = $careerDRB,
            `car_reb` = $careerREB,
            `car_ast` = $careerAST,
            `car_stl` = $careerSTL,
            `car_to` = $careerTVR,
            `car_blk` = $careerBLK,
            `car_pf` = $careerPF,
            `car_pts` = $careerPTS,
            `r_fga` = $rating2GA,
            `r_fgp` = $rating2GP,
            `r_fta` = $ratingFTA,
            `r_ftp` = $ratingFTP,
            `r_tga` = $rating3GA,
            `r_tgp` = $rating3GP,
            `r_orb` = $ratingORB,
            `r_drb` = $ratingDRB,
            `r_ast` = $ratingAST,
            `r_stl` = $ratingSTL,
            `r_to` = $ratingTVR,
            `r_blk` = $ratingBLK,
            `draftround` = $draftRound,
            `draftpickno` = $draftPickNumber,
            `injured` = $injuryDaysLeft,
            `htft` = $heightFT,
            `htin` = $heightIN,
            `wt` = $weight,
            `draftyear` = $draftYear,
            `retired` = 0,
            `r_foul` = $ratingFOUL;";

        $historicalStatsUpdateQuery = "INSERT INTO ibl_hist
            (`pid`,
            `name`,
            `year`,
            `team`,
            `teamid`,
            `games`,
            `minutes`,
            `fgm`,
            `fga`,
            `ftm`,
            `fta`,
            `tgm`,
            `tga`,
            `orb`,
            `reb`,
            `ast`,
            `stl`,
            `blk`,
            `tvr`,
            `pf`,
            `pts`,
            `r_2ga`,
            `r_2gp`,
            `r_fta`,
            `r_ftp`,
            `r_3ga`,
            `r_3gp`,
            `r_orb`,
            `r_drb`,
            `r_ast`,
            `r_stl`,
            `r_blk`,
            `r_tvr`,
            `r_oo`,
            `r_od`,
            `r_do`,
            `r_dd`,
            `r_po`,
            `r_pd`,
            `r_to`,
            `r_td`,
            `salary`)
        VALUES
            ($pid,
            '$name',
            $season->endingYear,
            '" . $sharedFunctions->getTeamnameFromTeamID($tid) . "',
            $tid,
            $seasonGamesPlayed,
            $seasonMIN,
            $seasonFGM,
            $seasonFGA,
            $seasonFTM,
            $seasonFTA,
            $season3GM,
            $season3GA,
            $seasonORB,
            $seasonREB,
            $seasonAST,
            $seasonSTL,
            $seasonBLK,
            $seasonTVR,
            $seasonPF,
            $seasonPTS,
            $rating2GA,
            $rating2GP,
            $ratingFTA,
            $ratingFTP,
            $rating3GA,
            $rating3GP,
            $ratingORB,
            $ratingDRB,
            $ratingAST,
            $ratingSTL,
            $ratingBLK,
            $ratingTVR,
            $ratingOO,
            $ratingOD,
            $ratingDO,
            $ratingDD,
            $ratingPO,
            $ratingPD,
            $ratingTO,
            $ratingTD,
            $currentSeasonSalary)
        ON DUPLICATE KEY UPDATE
            `team` = '" . $sharedFunctions->getTeamnameFromTeamID($tid) . "',
            `teamid` = $tid,
            `games` = $seasonGamesPlayed,
            `minutes` = $seasonMIN,
            `fgm` = $seasonFGM,
            `fga` = $seasonFGA,
            `ftm` = $seasonFTM,
            `fta` = $seasonFTA,
            `tgm` = $season3GM,
            `tga` = $season3GA,
            `orb` = $seasonORB,
            `reb` = $seasonREB,
            `ast` = $seasonAST,
            `stl` = $seasonSTL,
            `blk` = $seasonBLK,
            `tvr` = $seasonTVR,
            `pf` = $seasonPF,
            `pts` = $seasonPTS,
            `r_2ga` = $rating2GA,
            `r_2gp` = $rating2GP,
            `r_fta` = $ratingFTA,
            `r_ftp` = $ratingFTP,
            `r_3ga` = $rating3GA,
            `r_3gp` = $rating3GP,
            `r_orb` = $ratingORB,
            `r_drb` = $ratingDRB,
            `r_ast` = $ratingAST,
            `r_stl` = $ratingSTL,
            `r_blk` = $ratingBLK,
            `r_tvr` = $ratingTVR,
            `r_oo` = $ratingOO,
            `r_od` = $ratingOD,
            `r_do` = $ratingDO,
            `r_dd` = $ratingDD,
            `r_po` = $ratingPO,
            `r_pd` = $ratingPD,
            `r_to` = $ratingTO,
            `r_td` = $ratingTD,
            `salary` = $currentSeasonSalary;";
        if ($pid != 0) {
            if (!$db->sql_query($playerUpdateQuery)) {
                die('Invalid query: ' . $db->sql_error());
            }
            if (!$db->sql_query($historicalStatsUpdateQuery)) {
                die('Invalid query: ' . $db->sql_error());
            }
        }
    } elseif ($ordinal >= 1441 && $ordinal <= 1504) {
        if ($ordinal >= 1441 && $ordinal <= 1472) {
            if ($ordinal == 1441) {
                echo "ibl_plr and ibl_hist updated!<br><br>";
                echo "Updating ibl_team_offense_stats...<br>";
            }
            $tidOffenseStats++;
            $sideOfTheBall = 'offense';
            $teamName = $sharedFunctions->getTeamnameFromTeamID($tidOffenseStats);
        } elseif ($ordinal >= 1473 && $ordinal <= 1504) {
            if ($ordinal == 1473) {
                echo "ibl_team_offense_stats updated!<br><br>";
                echo "Updating ibl_team_defense_stats...<br>";
            }
            $tidDefenseStats++;
            $sideOfTheBall = 'defense';
            $teamName = $sharedFunctions->getTeamnameFromTeamID($tidDefenseStats);
        }

        $teamUpdateQuery = 'UPDATE `ibl_team_' . $sideOfTheBall . '_stats`
            SET
            `teamID` = ' . ${'tid' . ucfirst($sideOfTheBall) . 'Stats'} . ',
            `games` = ' . $seasonGamesPlayed . ',
            `fgm` = ' . ($season2GM + $season3GM) . ',
            `fga` = ' . ($season2GA + $season3GA) . ',
            `ftm` = ' . $seasonFTM . ',
            `fta` = ' . $seasonFTA . ',
            `tgm` = ' . $season3GM . ',
            `tga` = ' . $season3GA . ',
            `orb` = ' . $seasonORB . ',
            `reb` = ' . ($seasonORB + $seasonDRB) . ',
            `ast` = ' . $seasonAST . ',
            `stl` = ' . $seasonSTL . ',
            `tvr` = ' . $seasonTVR . ',
            `blk` = ' . $seasonBLK . ',
            `pf` = ' . $seasonPF . '
            WHERE
            `name` = \'' . $teamName . '\';';
        if (!$db->sql_query($teamUpdateQuery)) {
            die('Invalid query: ' . $db->sql_error());
        }
    }
}
fclose($plrFile);

echo "ibl_team_defense_stats updated!<br><br>";

echo "Assigning team names to players...<br>";

$i = 0;
while ($i < $numRowsTeamIDsNames) {
    $teamname = $db->sql_result($resultTeamIDsNames, $i, 'team_name');
    $teamID = $db->sql_result($resultTeamIDsNames, $i, 'teamid');
    $teamnameUpdateQuery = "UPDATE `ibl_plr` SET `teamname` = '$teamname' WHERE `tid` = $teamID;";
    if (!$db->sql_query($teamnameUpdateQuery)) {
        die('Invalid query: ' . $db->sql_error());
    }

    $i++;
}

echo "Team names successfully assigned to players!<br><br>";

echo "<b>plrParser complete!</b>";
