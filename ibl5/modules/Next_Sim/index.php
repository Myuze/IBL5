<?php

use Player\Player;

global $db, $cookie;
$sharedFunctions = new Shared($db);
$season = new Season($db);

if (!defined('MODULE_FILE')) {
    die("You can't access this file directly...");
}

$module_name = basename(dirname(__FILE__));
get_lang($module_name);
$pagetitle = "- $module_name";

$username = $cookie[1];
$userTeam = Team::initialize($db, $sharedFunctions->getTeamnameFromUsername($username));
$userStartingPG = Player::withPlayerID($db, $userTeam->getCurrentlySetStarterPlayerIDForPosition('PG') ?? 4040404);
$userStartingSG = Player::withPlayerID($db, $userTeam->getCurrentlySetStarterPlayerIDForPosition('SG') ?? 4040404);
$userStartingSF = Player::withPlayerID($db, $userTeam->getCurrentlySetStarterPlayerIDForPosition('SF') ?? 4040404);
$userStartingPF = Player::withPlayerID($db, $userTeam->getCurrentlySetStarterPlayerIDForPosition('PF') ?? 4040404);
$userStartingC = Player::withPlayerID($db, $userTeam->getCurrentlySetStarterPlayerIDForPosition('C') ?? 4040404);

$resultUserTeamProjectedGamesNextSim = Schedule\TeamSchedule::getProjectedGamesNextSimResult($db, $userTeam->teamID, $season->lastSimEndDate);
$lastSimEndDateObject = new DateTime($season->lastSimEndDate);

$i = 0;
foreach ($resultUserTeamProjectedGamesNextSim as $gameRow) {
    $rows[$i]['game'] = new Game($gameRow);
    $rows[$i]['date'] = new DateTime($rows[$i]['game']->date);
    $rows[$i]['day'] = $rows[$i]['date']->diff($lastSimEndDateObject)->format("%a");
    $rows[$i]['opposingTeam'] = Team::initialize($db, $rows[$i]['game']->getOpposingTeamID($userTeam->teamID));
    $rows[$i]['opposingStartingPG'] = Player::withPlayerID($db, $rows[$i]['opposingTeam']->getLastSimStarterPlayerIDForPosition('PG') ?? 4040404);
    $rows[$i]['userStartingPG'] = $userStartingPG ?? 4040404;
    $rows[$i]['opposingStartingSG'] = Player::withPlayerID($db, $rows[$i]['opposingTeam']->getLastSimStarterPlayerIDForPosition('SG') ?? 4040404);
    $rows[$i]['userStartingSG'] = $userStartingSG ?? 4040404;
    $rows[$i]['opposingStartingSF'] = Player::withPlayerID($db, $rows[$i]['opposingTeam']->getLastSimStarterPlayerIDForPosition('SF') ?? 4040404);
    $rows[$i]['userStartingSF'] = $userStartingSF ?? 4040404;
    $rows[$i]['opposingStartingPF'] = Player::withPlayerID($db, $rows[$i]['opposingTeam']->getLastSimStarterPlayerIDForPosition('PF') ?? 4040404);
    $rows[$i]['userStartingPF'] = $userStartingPF ?? 4040404;
    $rows[$i]['opposingStartingC'] = Player::withPlayerID($db, $rows[$i]['opposingTeam']->getLastSimStarterPlayerIDForPosition('C') ?? 4040404);
    $rows[$i]['userStartingC'] = $userStartingC ?? 4040404;

    $i++;
}

?>

<?php
    Nuke\Header::header();
    OpenTable();
?>

<center>
    <h1>Next Sim</h1>
<?php if (mysqli_num_rows($resultUserTeamProjectedGamesNextSim) == 0) : ?>
    No games projected next sim!
<?php else : ?>
    <table width=100% align=center>
        <?php for ($i = 0; $i <= League::getSimLengthInDays($db) - 1; $i++) : ?>
            <?php if (isset($rows[$i]['game']) && $rows[$i]['game'] != NULL) : ?>
                <tr>
                    <td>
                        <table align=center>
                            <tr>
                                <td style="text-align: right;" width=150>
                                    <h2 title="<?= $rows[$i]['game']->date ?>"><?= "Day " . $rows[$i]['day'] . " " . $rows[$i]['game']->getUserTeamLocationPrefix($userTeam->teamID) ?></h2>
                                </td>
                                <td style="text-align: center; padding-left: 4px; padding-right: 4px">
                                    <a href="modules.php?name=Team&op=team&teamID=<?= $rows[$i]['opposingTeam']->teamID ?>">
                                        <img src="./images/logo/<?= $rows[$i]['opposingTeam']->teamID ?>.jpg">
                                    </a>
                                </td>
                                <td style="text-align: left;" width=150>
                                    <h2><?= $rows[$i]['opposingTeam']->seasonRecord ?></h2>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?= UI::ratings($db, $rows[$i], $rows[$i]['opposingTeam'], "", $season, $module_name) ?>
                    </td>
                </tr>
            <?php endif; ?>
            <tr style="height: 15px"></tr>
        <?php endfor; ?>
    </table>
<?php endif ?>
<?php
    CloseTable();
    Nuke\Footer::footer();