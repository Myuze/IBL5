<?php

if (!defined('MODULE_FILE')) {
    die("You can't access this file directly...");
}

$module_name = basename(dirname(__FILE__));
get_lang($module_name);

$pagetitle = "- Team Pages";

/**
 * Main entry point for waivers module
 * 
 * @param mixed $user Current user
 */
function waivers($user)
{
    global $db, $action;
    
    $controller = new Waivers\WaiversController($db);
    $controller->handleWaiverRequest($user, $action);
}

waivers($user);
