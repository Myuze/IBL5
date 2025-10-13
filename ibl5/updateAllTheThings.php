<?php
error_reporting(E_ALL);
libxml_use_internal_errors(true);

require $_SERVER['DOCUMENT_ROOT'] . '/ibl5/mainfile.php';

$sharedFunctions = new Shared($db);
$season = new Season($db);

// Initialize components
$scheduleUpdater = new Updater\ScheduleUpdater($db, $sharedFunctions, $season);
$standingsUpdater = new Updater\StandingsUpdater($db, $sharedFunctions);
$powerRankingsUpdater = new Updater\PowerRankingsUpdater($db, $season);
$standingsHTMLGenerator = new Updater\StandingsHTMLGenerator($db);

// Update schedule
$scheduleUpdater->update();

// Update standings
$standingsUpdater->update();

// Update power rankings
$powerRankingsUpdater->update();

// Generate standings HTML
$standingsHTMLGenerator->generateStandingsPage();

// Reset extension attempts
$sharedFunctions->resetSimContractExtensionAttempts();

echo '<p><b>All the things have been updated!</br><p>';

echo '<a href="index.php">Return to the IBL homepage</a>';
