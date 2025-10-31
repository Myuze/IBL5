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

$teams = League::getAllTeamsResult($db);

$i = 0;
foreach ($teams as $team) {
    $rows['team'][$i] = Team::initialize($db, $team);
    $rows['startingPG'][$i] = Player::withPlayerID($db, $rows['team'][$i]->getLastSimStarterPlayerIDForPosition('PG') ?? 4040404);
    $rows['startingSG'][$i] = Player::withPlayerID($db, $rows['team'][$i]->getLastSimStarterPlayerIDForPosition('SG') ?? 4040404);
    $rows['startingSF'][$i] = Player::withPlayerID($db, $rows['team'][$i]->getLastSimStarterPlayerIDForPosition('SF') ?? 4040404);
    $rows['startingPF'][$i] = Player::withPlayerID($db, $rows['team'][$i]->getLastSimStarterPlayerIDForPosition('PF') ?? 4040404);
    $rows['startingC'][$i] = Player::withPlayerID($db, $rows['team'][$i]->getLastSimStarterPlayerIDForPosition('C') ?? 4040404);
    
    $rows['startingPG'][$i]->teamName = $rows['startingSG'][$i]->teamName = $rows['startingSF'][$i]->teamName = 
        $rows['startingPF'][$i]->teamName = $rows['startingC'][$i]->teamName = $rows['team'][$i]->name;
        
    $i++;
}

?>

<?php
    Nuke\Header::header();
    OpenTable();
?>

<center>
    <h1>League Starters</h1>
    <table width=100% align=center>
        <tr>
            <td>
            <h2 style="text-align:center">Point Guards</h2>
                <?= UI::ratings($db, $rows['startingPG'], $userTeam, "", $season, $module_name) ?>
            </td>
        </tr>
        <tr style="height: 15px"></tr>
        <tr>
            <td>
            <h2 style="text-align:center">Shooting Guards</h2>
                <?= UI::ratings($db, $rows['startingSG'], $userTeam, "", $season, $module_name) ?>
            </td>
        </tr>
        <tr style="height: 15px"></tr>
        <tr>
            <td>
            <h2 style="text-align:center">Small Forwards</h2>
                <?= UI::ratings($db, $rows['startingSF'], $userTeam, "", $season, $module_name) ?>
            </td>
        </tr>
        <tr style="height: 15px"></tr>
        <tr>
            <td>
            <h2 style="text-align:center">Power Forwards</h2>
                <?= UI::ratings($db, $rows['startingPF'], $userTeam, "", $season, $module_name) ?>
            </td>
        </tr>
        <tr style="height: 15px"></tr>
        <tr>
            <td>
            <h2 style="text-align:center">Centers</h2>
                <?= UI::ratings($db, $rows['startingC'], $userTeam, "", $season, $module_name) ?>
            </td>
        </tr>
        <tr style="height: 15px"></tr>
    </table>
<?php
    CloseTable();
    Nuke\Footer::footer();