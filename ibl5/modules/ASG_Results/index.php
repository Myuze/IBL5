<?php

declare(strict_types=1);

use Voting\VotingResultsController;
use Voting\VotingResultsService;
use Voting\VotingResultsTableRenderer;

/************************************************************************/
/* PHP-NUKE: Web Portal System                                          */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2006 by Francisco Burzi                                */
/* http://phpnuke.org                                                   */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/

if (!mb_eregi("modules.php", $_SERVER['PHP_SELF'])) {
    die("You can't access this file directly...");
}

$module_name = basename(dirname(__FILE__));
get_lang($module_name);

Nuke\Header::header();

global $db;
$season = new Season($db);
$service = new VotingResultsService($db);
$renderer = new VotingResultsTableRenderer();
$controller = new VotingResultsController($service, $renderer, $season);

OpenTable();
echo $controller->renderAllStarView();
CloseTable();

Nuke\Footer::footer();
