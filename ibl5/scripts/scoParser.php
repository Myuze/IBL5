<?php

require $_SERVER['DOCUMENT_ROOT'] . '/ibl5/mainfile.php';

function scoParser($uploadedFilePath, $operatingSeasonEndingYear, $operatingSeasonPhase)
{
    global $db, $mysqli_db;
    $season = new Season($db);

    $scoFilePath = ($uploadedFilePath) ? $uploadedFilePath : $_SERVER['DOCUMENT_ROOT'] . "/ibl5/IBL5.sco";
    $operatingSeasonEndingYear = ($operatingSeasonEndingYear) ? $operatingSeasonEndingYear : $season->endingYear;
    $operatingSeasonStartingYear = $operatingSeasonEndingYear - 1;
    $operatingSeasonPhase = ($operatingSeasonPhase) ? $operatingSeasonPhase : $season->phase;

    echo "<h2>Parse Log</h2>
        <b>Parsing .sco file for the $operatingSeasonStartingYear-$operatingSeasonEndingYear $operatingSeasonPhase...</b><p>";

    $scoFile = fopen("$scoFilePath", "rb");
    fseek($scoFile, 1000000);

    if ($operatingSeasonPhase == "Preseason") {
        if (Boxscore::deletePreseasonBoxScores($db, $operatingSeasonStartingYear)) {
            echo "Deleted any existing Preseason box scores." . "<p>";
        } else {
            echo "<b><font color=#F00>Failed to delete existing Preseason box scores!</font></b>";
        }
    } elseif ($operatingSeasonPhase == "HEAT") {
        if (Boxscore::deleteHEATBoxScores($db, $operatingSeasonStartingYear)) {
            echo "Deleted any existing HEAT box scores." . "<p>";
        } else {
            echo "<b><font color=#F00>Failed to delete existing HEAT box scores!</font></b>";
        }
    } else {
        if (Boxscore::deleteRegularSeasonAndPlayoffsBoxScores($db, $operatingSeasonStartingYear)) {
            echo "Deleted any existing regular season and playoffs box scores." . "<p>";
        } else {
            echo "<b><font color=#F00>Failed to delete existing regular season and playoffs box scores!</font></b>";
        }
    }

    echo "<i>[scoParser works silently now]</i><br>";

    $teamStatement = $mysqli_db->prepare(Boxscore::TEAMSTATEMENT_PREPARE);
    $teamStatement->bind_param("ssiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiii",
        $gameDate,
        $name,
        $gameOfThatDay,
        $visitorTeamID,
        $homeTeamID,
        $attendance,
        $capacity,
        $visitorWins,
        $visitorLosses,
        $homeWins,
        $homeLosses,
        $visitorQ1points,
        $visitorQ2points,
        $visitorQ3points,
        $visitorQ4points,
        $visitorOTpoints,
        $homeQ1points,
        $homeQ2points,
        $homeQ3points,
        $homeQ4points,
        $homeOTpoints,
        $gameFieldGoalsMade,
        $gameFieldGoalsAttempted,
        $gameFreeThrowsMade,
        $gameFreeThrowsAttempted,
        $gameThreePointersMade,
        $gameThreePointersAttempted,
        $gameOffensiveRebounds,
        $gameDefensiveRebounds,
        $gameAssists,
        $gameSteals,
        $gameTurnovers,
        $gameBlocks,
        $gamePersonalFouls
    );

    $playerStatement = $mysqli_db->prepare(Boxscore::PLAYERSTATEMENT_PREPARE);
    $playerStatement->bind_param("sssiiiiiiiiiiiiiiiii",
        $gameDate,
        $name,
        $position,
        $playerID,
        $visitorTeamID,
        $homeTeamID,
        $gameMinutesPlayed,
        $gameFieldGoalsMade,
        $gameFieldGoalsAttempted,
        $gameFreeThrowsMade,
        $gameFreeThrowsAttempted,
        $gameThreePointersMade,
        $gameThreePointersAttempted,
        $gameOffensiveRebounds,
        $gameDefensiveRebounds,
        $gameAssists,
        $gameSteals,
        $gameTurnovers,
        $gameBlocks,
        $gamePersonalFouls
    );

    $numberOfLinesProcessed = 0;
    while (!feof($scoFile)) {
        $line = fgets($scoFile, 2001);

        $gameInfoLine = substr($line, 0, 58);
        $boxscoreGameInfo = Boxscore::withGameInfoLine($gameInfoLine, $operatingSeasonEndingYear, $operatingSeasonPhase);

        $gameDate = $boxscoreGameInfo->gameDate;
        $gameOfThatDay = $boxscoreGameInfo->gameOfThatDay;
        $visitorTeamID = $boxscoreGameInfo->visitorTeamID;
        $homeTeamID = $boxscoreGameInfo->homeTeamID;
        $attendance = $boxscoreGameInfo->attendance;
        $capacity = $boxscoreGameInfo->capacity;
        $visitorWins = $boxscoreGameInfo->visitorWins;
        $visitorLosses = $boxscoreGameInfo->visitorLosses;
        $homeWins = $boxscoreGameInfo->homeWins;
        $homeLosses = $boxscoreGameInfo->homeLosses;
        $visitorQ1points = $boxscoreGameInfo->visitorQ1points;
        $visitorQ2points = $boxscoreGameInfo->visitorQ2points;
        $visitorQ3points = $boxscoreGameInfo->visitorQ3points;
        $visitorQ4points = $boxscoreGameInfo->visitorQ4points;
        $visitorOTpoints = $boxscoreGameInfo->visitorOTpoints;
        $homeQ1points = $boxscoreGameInfo->homeQ1points;
        $homeQ2points = $boxscoreGameInfo->homeQ2points;
        $homeQ3points = $boxscoreGameInfo->homeQ3points;
        $homeQ4points = $boxscoreGameInfo->homeQ4points;
        $homeOTpoints = $boxscoreGameInfo->homeOTpoints;

        for ($i = 0; $i < 30; $i++) {
            $x = $i * 53; // 53 = amount of characters to skip to get to the next player's/team's data line
            $playerInfoLine = substr($line, 58 + $x, 53);
            $playerStats = PlayerStats::withBoxscoreInfoLine($db, $playerInfoLine);

            $name = $playerStats->name;
            $position = $playerStats->position;
            $playerID = $playerStats->playerID;
            $gameMinutesPlayed = $playerStats->gameMinutesPlayed;
            $gameFieldGoalsMade = $playerStats->gameFieldGoalsMade;
            $gameFieldGoalsAttempted = $playerStats->gameFieldGoalsAttempted;
            $gameFreeThrowsMade = $playerStats->gameFreeThrowsMade;
            $gameFreeThrowsAttempted = $playerStats->gameFreeThrowsAttempted;
            $gameThreePointersMade = $playerStats->gameThreePointersMade;
            $gameThreePointersAttempted = $playerStats->gameThreePointersAttempted;
            $gameOffensiveRebounds = $playerStats->gameOffensiveRebounds;
            $gameDefensiveRebounds = $playerStats->gameDefensiveRebounds;
            $gameAssists = $playerStats->gameAssists;
            $gameSteals = $playerStats->gameSteals;
            $gameTurnovers = $playerStats->gameTurnovers;
            $gameBlocks = $playerStats->gameBlocks;
            $gamePersonalFouls = $playerStats->gamePersonalFouls;

            if ($playerStats->name != null || $playerStats->name != '') {
                if ($playerID == 0) {
                    if ($teamStatement->execute()) {
                        $numberOfLinesProcessed++;
                    }
                } else {
                    if ($playerStatement->execute()) {
                        $numberOfLinesProcessed++;
                    }
                }
            }
        }
    }

    echo "<p>Number of .sco lines processed: $numberOfLinesProcessed";

    $newSimEndDate = $season->getLastBoxScoreDate();

    if ($season->lastSimEndDate) {
        if ($season->lastSimEndDate != $newSimEndDate) {
            $dateObjectForNewSimEndDate = date_create($season->lastSimEndDate);
            date_modify($dateObjectForNewSimEndDate, '+1 day');
            $newSimStartDate = date_format($dateObjectForNewSimEndDate, 'Y-m-d');

            $newSimNumber = $season->lastSimNumber + 1;

            $insertNewSimDates = $season->setLastSimDatesArray($newSimNumber, $newSimStartDate, $newSimEndDate);
        } else {
            echo "<p>Looks like new box scores haven't been added.
            <br>Sim Start/End Dates will stay set to $season->lastSimStartDate and $season->lastSimEndDate.";
            die();
        }
    } else {
        $newSimStartDate = $season->getFirstBoxScoreDate();
        $insertNewSimDates = $season->setLastSimDatesArray(1, $newSimStartDate, $newSimEndDate);
    }

    if ($insertNewSimDates) {
        echo "<p>Added box scores from $newSimStartDate through $newSimEndDate.";
    } else {
        die('Invalid query: ' . $db->sql_error());
    }
}

echo "<h1>JSB .sco File Parser</h1>
<h2>Uploader</h2>
<form enctype=\"multipart/form-data\" action=\"ibl5/scripts/scoParser.php\" method=\"POST\">
    <input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"14000000\" />
    <label for=\"scoFile\">Upload Old Season's .sco: </label><input name=\"scoFile\" type=\"file\" /><p>
    <label for=\"seasonPhase\">Season Phase for Uploaded .sco: </label><select name=\"seasonPhase\">
        <option value = \"Preseason\">Preseason</option>
        <option value = \"HEAT\">HEAT</option>
        <option value = \"Regular Season/Playoffs\">Regular Season</option>
    </select><p>
    <label for=\"seasonEndingYear\">Season <b><u>Ending</u></b> Year for Uploaded .sco: </label><input type=\"text\" name=\"seasonEndingYear\" maxlength=4 minlength=4 size=4 /><br>
    <i>e.g. HEAT before the 1990-1991 season</i> = <code>1991</code><br>
    <i>e.g. 1984-1985 Preseason or Regular Season</i> = <code>1985</code><p>
    <input type=\"submit\" value=\"Parse Uploaded .sco File\" />
</form>
<hr>
<br>";

if ($_FILES['scoFile']['error']) {
    echo $_FILES['scoFile']['error'] . "<p>";
};

if ($_FILES['scoFile']['tmp_name']) {
    $uploadedFilePath = $_FILES['scoFile']['tmp_name'];
    $operatingSeasonEndingYear = $_POST['seasonEndingYear'];
    $operatingSeasonPhase = $_POST['seasonPhase'];
}

scoParser($uploadedFilePath, $operatingSeasonEndingYear, $operatingSeasonPhase);
